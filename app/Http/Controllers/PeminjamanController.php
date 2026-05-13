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
        // Cari peminjaman yang masih pending dan umurnya lebih dari 1 jam
        $expiredPeminjamans = Peminjaman::where('status', 'pending')
            ->where('created_at', '<=', \Carbon\Carbon::now()->subHour())
            ->get();

        foreach ($expiredPeminjamans as $peminjaman) {
            $peminjaman->update([
                'status' => 'rejected',
                'catatan_admin' => 'Otomatis dibatalkan sistem: Melewati batas waktu approval (1 jam).',
                'approved_at' => \Carbon\Carbon::now(),
            ]);
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
            'nomor_hp' => 'required|string|max:20',
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal_pinjam' => 'required|date',
            'jam_pinjam' => 'required|date_format:H:i',
            'jam_kembali' => 'required|date_format:H:i|after:jam_pinjam',
            'keperluan' => 'required|string',
            'alamat_tujuan' => 'required|string',
            'tanggung_jawab' => 'accepted' // Checkbox validation
        ]);

        // Cek Bentrok Jadwal
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
        $waMessage .= "*Nama:* {$peminjaman->nama_peminjam}\n";
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
        $peminjaman = Peminjaman::where('approval_token', $token)->firstOrFail();

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Status pengajuan ini sudah ' . $peminjaman->status);
        }

        $peminjaman->update([
            'status' => 'approved',
            'approved_at' => now(),
            // 'approved_by' => 'admin_name' jika perlu
        ]);

        return redirect()->route('peminjaman.approval', $token)->with('success', 'Peminjaman berhasil di-Approve!');
    }

    public function reject(Request $request, $token)
    {
        $peminjaman = Peminjaman::where('approval_token', $token)->firstOrFail();

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Status pengajuan ini sudah ' . $peminjaman->status);
        }

        $peminjaman->update([
            'status' => 'rejected',
            'catatan_admin' => $request->input('catatan_admin'),
            'approved_at' => now(),
        ]);

        return redirect()->route('peminjaman.approval', $token)->with('success', 'Peminjaman telah di-Reject.');
    }
}
