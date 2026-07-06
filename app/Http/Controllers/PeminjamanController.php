<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Peminjaman;
use App\Services\FonteeWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PeminjamanController extends Controller
{
    protected $whatsAppService;

    public function __construct(FonteeWhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    private function autoExpirePendingRequests()
    {
        // Cari peminjaman yang masih pending dan umurnya lebih dari 3 jam
        $expiredPeminjamans = Peminjaman::with('kendaraan')
            ->where('status', 'pending')
            ->where('created_at', '<=', \Carbon\Carbon::now()->subHours(3))
            ->get();

        foreach ($expiredPeminjamans as $peminjaman) {
            $peminjaman->update([
                'status' => 'rejected',
                'catatan_admin' => 'Otomatis dibatalkan sistem: Melewati batas waktu approval (3 jam).',
                'approved_at' => \Carbon\Carbon::now(),
            ]);

            // Kirim notifikasi WA ke peminjam atas penolakan otomatis oleh sistem
            try {
                $tanggalIndo = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('d F Y');
                $jamIndo = \Carbon\Carbon::parse($peminjaman->jam_pinjam)->format('H.i') . ' - ' . \Carbon\Carbon::parse($peminjaman->jam_kembali)->format('H.i') . ' WIB';

                $waMessage = "*Notifikasi Pembatalan Otomatis Peminjaman*\n\n";
                $waMessage .= "Mohon maaf {$peminjaman->nama_peminjam},\n\n";
                $waMessage .= "Pengajuan peminjaman kendaraan *{$peminjaman->kendaraan->nama}* untuk tanggal *{$tanggalIndo}* jam *{$jamIndo}* telah *DIBATALKAN OTOMATIS* oleh sistem.\n\n";
                $waMessage .= "*Alasan:* Tidak ada respons dari Admin dalam batas waktu 3 jam.\n\n";
                $waMessage .= "Silakan ajukan kembali atau hubungi Admin GA secara langsung jika Anda masih memerlukan kendaraan tersebut.\n\n";
                $waMessage .= "Terima kasih.";

                $this->whatsAppService->sendMessageCurl($peminjaman->nomor_hp, $waMessage);
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim WhatsApp Auto-Reject: ' . $e->getMessage());
            }
        }
    }

    public function jadwal(Request $request)
    {
        $this->autoExpirePendingRequests();

        // Default ke hari ini jika tidak ada filter tanggal
        $tanggal = $request->input('tanggal', \Carbon\Carbon::today()->format('Y-m-d'));
        
        // Ambil semua kendaraan beserta jadwal peminjamannya pada tanggal yang dipilih
        $kendaraans = Kendaraan::with(['peminjamans' => function($q) use ($tanggal) {
            $q->where('tanggal_pinjam', $tanggal)
              ->whereIn('status', ['pending', 'approved'])
              ->orderBy('jam_pinjam');
        }])->get();

        return view('peminjaman.jadwal', compact('kendaraans', 'tanggal'));
    }

    public function create()
    {
        $this->autoExpirePendingRequests();
        
        $kendaraans = Kendaraan::all();
        return view('peminjaman.create', compact('kendaraans'));
    }

    public function store(Request $request)
    {
        $this->autoExpirePendingRequests();
        
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'nama_driver' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal_pinjam' => 'required|date',
            'jam_pinjam' => 'required|date_format:H:i',
            'jam_kembali' => 'required|date_format:H:i|after:jam_pinjam',
            'keperluan' => 'required|string',
            'alamat_tujuan' => 'required|string',
            'tanggung_jawab' => 'accepted'
        ]);

        $bentrok = Peminjaman::where('kendaraan_id', $validated['kendaraan_id'])
            ->where('tanggal_pinjam', $validated['tanggal_pinjam'])
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_pinjam', [$validated['jam_pinjam'], $validated['jam_kembali']])
                      ->orWhereBetween('jam_kembali', [$validated['jam_pinjam'], $validated['jam_kembali']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('jam_pinjam', '<=', $validated['jam_pinjam'])
                            ->where('jam_kembali', '>=', $validated['jam_kembali']);
                      });
            })->exists();

        if ($bentrok) {
            return back()->withInput()->withErrors(['kendaraan_id' => 'Kendaraan ini sudah dibooking pada rentang waktu tersebut.']);
        }

        // Generate token
        $validated['approval_token'] = Str::random(32);
        
        // Remove tanggung_jawab before insert
        unset($validated['tanggung_jawab']);

        $peminjaman = Peminjaman::create($validated);

        // Kirim WA ke Admin Group
        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $groupId = env('FONTEE_WHATSAPP_GROUP_ID');
        $approvalLink = url('/peminjaman/approval/' . $peminjaman->approval_token);

        // Format tanggal dan jam untuk pesan WA
        $tanggalIndo = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('d F Y');
        $jamIndo = \Carbon\Carbon::parse($peminjaman->jam_pinjam)->format('H.i') . ' - ' . \Carbon\Carbon::parse($peminjaman->jam_kembali)->format('H.i') . ' WIB';

        $waMessage = "*PENGAJUAN PEMINJAMAN KENDARAAN*\n\n";
        $waMessage .= "*Peminjam:* {$peminjaman->nama_peminjam}\n";
        $waMessage .= "*Driver:* {$peminjaman->nama_driver}\n";
        $waMessage .= "*Nomor HP:* {$peminjaman->nomor_hp}\n";
        $waMessage .= "*Kendaraan:* {$kendaraan->nama} ({$kendaraan->kategori})\n";
        $waMessage .= "*Tanggal:* {$tanggalIndo}\n";
        $waMessage .= "*Jam:* {$jamIndo}\n";
        $waMessage .= "*Keperluan:* {$peminjaman->keperluan}\n";
        $waMessage .= "*Alamat Tujuan:* {$peminjaman->alamat_tujuan}\n\n";
        $waMessage .= "*Link Approval:*\n{$approvalLink}\n\n";
        $waMessage .= "Mohon segera ditindaklanjuti.";

        try {
            $this->whatsAppService->sendMessageCurl($groupId, $waMessage);
            
            // Kirim pesan notifikasi ke peminjam
            $waDriver = "*Notifikasi Sistem BMI*\n\n";
            $waDriver .= "Halo {$peminjaman->nama_peminjam},\n\n";
            $waDriver .= "Pengajuan peminjaman kendaraan *{$kendaraan->nama}* untuk tanggal *{$tanggalIndo}* telah kami terima.\n";
            $waDriver .= "Mohon menunggu persetujuan dari Admin. Kami akan mengabari Anda kembali melalui pesan ini.\n\n";
            $waDriver .= "Terima kasih.";
            
            $this->whatsAppService->sendMessageCurl($peminjaman->nomor_hp, $waDriver);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim WhatsApp Peminjaman: ' . $e->getMessage());
        }

        return redirect()->route('peminjaman.success')->with('success', 'Pengajuan berhasil dikirim. Menunggu approval admin.');
    }

    public function success()
    {
        return view('peminjaman.success');
    }

    public function approvalView($token)
    {
        $this->autoExpirePendingRequests();
        
        $peminjaman = Peminjaman::with('kendaraan')->where('approval_token', $token)->firstOrFail();

        return view('peminjaman.approval', compact('peminjaman'));
    }

    public function approve(Request $request, $token)
    {
        $peminjaman = Peminjaman::with('kendaraan')->where('approval_token', $token)->firstOrFail();

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Status pengajuan ini sudah ' . $peminjaman->status);
        }

        $peminjaman->update([
            'status' => 'approved',
            'approved_at' => now(),
            // 'approved_by' => 'admin_name' jika perlu
        ]);
        
        // Kirim notifikasi WA ke peminjam
        try {
            $tanggalIndo = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('d F Y');
            $jamIndo = \Carbon\Carbon::parse($peminjaman->jam_pinjam)->format('H.i') . ' - ' . \Carbon\Carbon::parse($peminjaman->jam_kembali)->format('H.i') . ' WIB';
            
            $waDriver = "*Notifikasi Persetujuan Peminjaman*\n\n";
            $waDriver .= "Halo {$peminjaman->nama_peminjam},\n\n";
            $waDriver .= "Pengajuan peminjaman kendaraan Anda telah *DISETUJUI*.\n\n";
            $waDriver .= "*Kendaraan:* {$peminjaman->kendaraan->nama}\n";
            $waDriver .= "*Tanggal:* {$tanggalIndo}\n";
            $waDriver .= "*Jam:* {$jamIndo}\n\n";
            $waDriver .= "Silakan ambil kunci kendaraan di bagian GA sesuai jadwal yang ditentukan.\n\n";
            $waDriver .= "Terima kasih.";
            
            $this->whatsAppService->sendMessageCurl($peminjaman->nomor_hp, $waDriver);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim WhatsApp Approve: ' . $e->getMessage());
        }

        return redirect()->route('peminjaman.approval', $token)->with('success', 'Peminjaman berhasil di-Approve!');
    }

    public function reject(Request $request, $token)
    {
        $peminjaman = Peminjaman::with('kendaraan')->where('approval_token', $token)->firstOrFail();

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Status pengajuan ini sudah ' . $peminjaman->status);
        }

        $peminjaman->update([
            'status' => 'rejected',
            'catatan_admin' => $request->input('catatan_admin'),
            'approved_at' => now(),
        ]);

        // Kirim notifikasi WA ke peminjam
        try {
            $tanggalIndo = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('d F Y');
            
            $waDriver = "*Notifikasi Penolakan Peminjaman*\n\n";
            $waDriver .= "Mohon maaf {$peminjaman->nama_peminjam},\n\n";
            $waDriver .= "Pengajuan peminjaman kendaraan *{$peminjaman->kendaraan->nama}* untuk tanggal *{$tanggalIndo}* *DITOLAK*.\n\n";
            $waDriver .= "*Alasan penolakan:* " . $request->input('catatan_admin') . "\n\n";
            $waDriver .= "Silakan hubungi Admin GA jika ada pertanyaan.\n\n";
            $waDriver .= "Terima kasih.";
            
            $this->whatsAppService->sendMessageCurl($peminjaman->nomor_hp, $waDriver);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim WhatsApp Reject: ' . $e->getMessage());
        }

        return redirect()->route('peminjaman.approval', $token)->with('success', 'Peminjaman telah di-Reject.');
    }

    public function riwayat(Request $request)
    {
        $this->autoExpirePendingRequests();
        
        $peminjamans = Peminjaman::with('kendaraan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('peminjaman.riwayat', compact('peminjamans'));
    }

    public function statistikAuth(Request $request)
    {
        $password = $request->input('password');
        
        // Cek password statis (bisa diganti ambil dari .env)
        if ($password === 'adminaset123') {
            session(['statistik_unlocked' => true]);
            return redirect()->route('peminjaman.statistik');
        }
        
        return back()->with('error', 'Password salah!');
    }

    public function statistik(Request $request)
    {
        // Cek auth session
        if (!session('statistik_unlocked')) {
            return redirect()->route('peminjaman.jadwal')->with('error', 'Akses ditolak. Memerlukan password admin.');
        }

        $this->autoExpirePendingRequests();

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Data chart penggunaan per kendaraan di bulan tertentu
        $usagePerKendaraan = Peminjaman::selectRaw('kendaraan_id, COUNT(*) as total')
            ->whereMonth('tanggal_pinjam', $bulan)
            ->whereYear('tanggal_pinjam', $tahun)
            ->whereIn('status', ['approved', 'completed'])
            ->groupBy('kendaraan_id')
            ->get();

        $kendaraans = Kendaraan::all();
        $chartLabels = [];
        $chartData = [];

        foreach ($kendaraans as $kendaraan) {
            $chartLabels[] = $kendaraan->nama;
            $usage = $usagePerKendaraan->firstWhere('kendaraan_id', $kendaraan->id);
            $chartData[] = $usage ? $usage->total : 0;
        }

        // Data statistik pengemudi (berdasarkan nama_driver yang disetujui)
        $topDrivers = Peminjaman::selectRaw('nama_driver, COUNT(*) as total_pinjam')
            ->whereMonth('tanggal_pinjam', $bulan)
            ->whereYear('tanggal_pinjam', $tahun)
            ->whereIn('status', ['approved', 'completed'])
            ->groupBy('nama_driver')
            ->orderByDesc('total_pinjam')
            ->limit(10)
            ->get();
            
        // Penggunaan kendaraan teratas
        $topKendaraan = Peminjaman::selectRaw('kendaraan_id, COUNT(*) as total_pinjam')
            ->with('kendaraan')
            ->whereMonth('tanggal_pinjam', $bulan)
            ->whereYear('tanggal_pinjam', $tahun)
            ->whereIn('status', ['approved', 'completed'])
            ->groupBy('kendaraan_id')
            ->orderByDesc('total_pinjam')
            ->limit(5)
            ->get();

        return view('peminjaman.statistik', compact('chartLabels', 'chartData', 'topDrivers', 'topKendaraan', 'bulan', 'tahun'));
    }
}
