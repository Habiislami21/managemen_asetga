<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanBarang;
use App\Services\PeminjamanBarangWordService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanEvent extends Controller
{
    public function __construct(
        private PeminjamanBarangWordService $wordService
    ) {
    }

    public function create()
    {
        return view('peminjaman_event.peminjaman_barang');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_kegiatan',
            'tempat' => 'required|string|max:255',
            'nama_kegiatan' => 'required|string|max:255',
            'barang' => 'required|array|min:1',
            'barang.*.nama_barang' => 'required|string|max:255',
            'barang.*.jumlah' => 'required|integer|min:1',
        ]);

        $barangItems = collect($validated['barang'])
            ->filter(fn ($barang) => filled(trim($barang['nama_barang'] ?? '')))
            ->values();

        if ($barangItems->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['barang' => 'Minimal satu barang harus diisi.']);
        }

        $peminjaman = DB::transaction(function () use ($validated, $barangItems) {
            $nomorPeminjaman = $this->generateNomorPeminjaman();

            $peminjaman = PeminjamanBarang::create([
                'nama_peminjam'    => $validated['nama_peminjam'],
                'divisi'           => $validated['divisi'],
                'nomor_hp'         => $validated['nomor_hp'],
                'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
                'tanggal_kembali'  => $validated['tanggal_kembali'],
                'tempat'           => $validated['tempat'],
                'nama_kegiatan'    => $validated['nama_kegiatan'],
                'nomor_peminjaman' => $nomorPeminjaman,
            ]);

            foreach ($barangItems as $barang) {
                $peminjaman->items()->create([
                    'nama_barang' => $barang['nama_barang'],
                    'jumlah'      => $barang['jumlah'],
                ]);
            }

            return $peminjaman->load('items');
        });

        try {
            $filePath = $this->wordService->generate($peminjaman);
            $peminjaman->update([
                'file_docx' => 'peminjaman_barang/' . basename($filePath),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['form' => 'Data tersimpan, tetapi gagal membuat dokumen Word: ' . $e->getMessage()]);
        }

        return redirect()
            ->route('peminjaman-event.success', $peminjaman)
            ->with('success', 'Form peminjaman barang berhasil dibuat.');
    }

    public function success(PeminjamanBarang $peminjaman)
    {
        return view('peminjaman_event.success', compact('peminjaman'));
    }

    public function download(PeminjamanBarang $peminjaman)
    {
        if (! $peminjaman->file_docx) {
            abort(404, 'Dokumen belum tersedia.');
        }

        $path = storage_path('app/' . $peminjaman->file_docx);

        if (! file_exists($path)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        $filename = basename($path);

        return response()->download($path, $filename);
    }

    /**
     * Generate nomor peminjaman otomatis.
     * Format: PB-YYYYMM-XXX (contoh: PB-202606-001)
     */
    private function generateNomorPeminjaman(): string
    {
        $now = Carbon::now();
        $count = PeminjamanBarang::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count() + 1;

        return sprintf(
            'PB-%d%02d-%03d',
            $now->year,
            $now->month,
            $count
        );
    }
}
