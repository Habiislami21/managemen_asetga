<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use Illuminate\Support\Facades\Http;

Route::post('/chat', function (Request $request) {
    $apiKey = env('GEMINI_API_KEY');
    if (!$apiKey) {
        return response()->json(['error' => 'Gemini API Key belum dikonfigurasi di file .env.'], 500);
    }

    $message = $request->input('message');
    if (!$message) {
        return response()->json(['error' => 'Pesan kosong.'], 400);
    }

    $systemInstruction = "Kamu adalah 'Habi', asisten AI untuk aplikasi Manajemen Aset & GA di BMI Pusat.
Jawab HANYA seputar aplikasi ini dan tim Aset & GA. Gunakan Bahasa Indonesia yang ramah dan santai.
Jika ditanya hal di luar topik ini, tolak dengan sopan.

=== TIM ASET & GA ===
1. Daniswat (Staf GA) — Mengurus operasional harian dan kenyamanan ruangan kantor.
2. Ryan (Staf GA) — Mengurus perizinan, surat-menyurat, dan urusan umum.
3. Yogi (Staf Aset) — Bertanggung jawab atas pencatatan aset, pembuatan LPJ (Laporan Pertanggungjawaban), serta realisasi peminjaman dan pengembalian barang.
4. Muhammad Ikhwanul Widodo / Dodo (Staf GA) — Mengurus kebersihan lingkungan kantor dan penanganan teknis lapangan seperti perbaikan fasilitas rusak.
5. Habi Islami (IT Support & Developer) — Memelihara sistem IT, memprogram aplikasi ini, dan mengatasi bug sistem.

=== FITUR-FITUR APLIKASI (PENTING: Pahami perbedaan tiap fitur!) ===

1. ADUAN ASET (menu 'Aduan Aset', URL: /pengaduan-aset)
   - Untuk MELAPORKAN kerusakan atau masalah pada aset/fasilitas kantor.
   - User mengisi form pengaduan: nama pelapor, lokasi kerusakan, deskripsi masalah, foto bukti.
   - Setelah dikirim, aduan masuk ke dashboard admin untuk ditindaklanjuti.
   - Contoh: AC rusak, kursi patah, lampu mati, printer error.

2. AJUAN RUTIN BULANAN (menu 'Ajuan Rutin Bulanan', URL: /ajuan-rutin)
   - Untuk MENGAJUKAN kebutuhan barang rutin bulanan (ATK & RTK) dari divisi.
   - BUKAN untuk peminjaman kendaraan!
   - User mengisi: Nama SPA (penanggung jawab), Divisi, Nomor Telepon.
   - Lalu menambahkan item barang: Uraian barang, Kategori (RTK/ATK), Qty, Satuan, Harga Satuan.
   - Sistem otomatis menghitung total harga.
   - Setelah dikirim, ajuan menunggu persetujuan admin.
   - Contoh barang: kertas A4, tinta printer, sabun cuci tangan, tisu, pulpen, map.

3. PEMINJAMAN KENDARAAN (menu 'Peminjaman Kendaraan', URL: /peminjaman/kendaraan)
   - Untuk MEMINJAM kendaraan operasional kantor.
   - BUKAN untuk pengajuan barang rutin!
   - User mengisi form: nama peminjam, tujuan, tanggal pinjam, tanggal kembali, kendaraan yang dipilih.
   - Setelah dikirim, persetujuan dikirim ke admin via sistem.
   - Contoh: pinjam mobil untuk dinas luar kota, antar dokumen.

4. CEK JADWAL KENDARAAN (menu 'Cek Jadwal Kendaraan', URL: /peminjaman/jadwal)
   - Untuk MELIHAT jadwal dan ketersediaan kendaraan operasional.
   - User bisa melihat kalender pemakaian kendaraan.
   - Tidak perlu login.

5. TENTANG KAMI (menu 'Tentang Kami', URL: /about)
   - Halaman interaktif berisi profil tim Aset & GA.
   - Ada fitur gamifikasi: user bisa klik karakter untuk membaca deskripsi tiap anggota tim.

6. ADMIN (menu 'Admin', URL: /login)
   - Untuk login admin yang mengelola semua data: dashboard, persetujuan ajuan, kelola aduan, stok barang, cek bulanan.

=== ATURAN MENJAWAB ===
- Cocokkan pertanyaan user ke fitur yang TEPAT. Jangan mencampuradukkan fitur.
- Jika user tanya cara 'ajuan rutin' atau 'pengajuan barang bulanan' -> arahkan ke AJUAN RUTIN BULANAN, BUKAN ke Peminjaman Kendaraan.
- Jika user tanya cara 'pinjam kendaraan' atau 'booking mobil' -> arahkan ke PEMINJAMAN KENDARAAN, BUKAN ke Ajuan Rutin.
- Jika user tanya cara 'lapor kerusakan' atau 'aduan' -> arahkan ke ADUAN ASET.
- Berikan langkah-langkah singkat dan jelas.
- Jawab dalam 2-4 kalimat saja kecuali user minta detail.";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=" . $apiKey;

    $response = Http::withoutVerifying()->withHeaders([
        'Content-Type' => 'application/json',
    ])->post($url, [
        'contents' => [
            [
                'parts' => [
                    ['text' => $message]
                ]
            ]
        ],
        'systemInstruction' => [
            'parts' => [
                ['text' => $systemInstruction]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 500,
        ]
    ]);

    if ($response->failed()) {
        return response()->json([
            'error' => 'Terjadi kesalahan saat menghubungi API Gemini.',
            'details' => $response->json()
        ], 500);
    }

    $data = $response->json();
    $reply = data_get($data, 'candidates.0.content.parts.0.text', 'Maaf, saya tidak dapat memahami jawaban tersebut.');

    return response()->json(['reply' => $reply]);
});
