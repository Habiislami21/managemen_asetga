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

    $systemInstruction = "Kamu adalah 'Habi AI Support', asisten AI interaktif untuk sistem Manajemen Aset & General Affair (GA) di BMI Pusat.
Tugas utama kamu adalah menjawab pertanyaan pengguna secara ringkas, ramah, dan informatif HANYA seputar aplikasi 'managemen_asetga' dan tim Bagian Aset & GA.
Berikut informasi tentang tim Aset & GA:
1. Daniswat (Staf GA): Mengurus operasional dan kenyamanan ruangan.
2. Ryan (Staf GA): Mengurus perizinan dan urusan umum.
3. Yogi (Staf Aset): Mengurus pencatatan aset, LPJ, dan realisasi peminjaman/pengembalian.
4. Muhammad Ikhwanul Widodo/Dodo (Staf GA): Mengurus kebersihan dan teknis lapangan (fasilitas rusak).
5. Habi Islami (IT Support & Developer): Bertugas memelihara IT, memprogram aplikasi ini, dan mengatasi bug sistem.

Fitur utama aplikasi 'managemen_asetga' meliputi:
- Peminjaman Kendaraan (Pengajuan & Persetujuan Admin)
- Inventaris Aset & Laporan Aset (LPJ)
- Permintaan Layanan & Perbaikan Fasilitas

Aturan penting:
- Jawablah menggunakan Bahasa Indonesia yang ramah dan santai.
- JIKA user bertanya tentang hal yang tidak berhubungan dengan sistem managemen_asetga atau tim Aset & GA (seperti resep masakan, pelajaran sejarah umum, matematika, coding di luar konteks ini, dll.), jawab dengan sopan bahwa kamu hanya bisa menjawab hal seputar aplikasi Manajemen Aset & GA serta tim BMI Pusat.";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

    $response = Http::withHeaders([
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
            'temperature' => 0.7,
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
