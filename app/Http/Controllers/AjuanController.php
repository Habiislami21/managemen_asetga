<?php

namespace App\Http\Controllers;

use App\Models\Ajuan;
use App\Models\Divisi;
use App\Services\FonteeWhatsAppService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AjuanController extends Controller
{
    protected $whatsAppService;

    public function __construct(FonteeWhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    public function index()
    {
        $divisis = Divisi::all();
        $satuan = Ajuan::SATUAN;
        return view('form.pendataan-stok', compact('divisis', 'satuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        // Hapus format rupiah dan konversi ke integer sebelum validasi
        $harga = (int) filter_var($request->harga, FILTER_SANITIZE_NUMBER_INT);
        $total = (int) filter_var($request->total, FILTER_SANITIZE_NUMBER_INT);

        $request->merge([
            'harga' => $harga,
            'total' => $total
        ]);

        $validated = $request->validate([
            'nama_spa' => 'required|string|max:255',
            'divisi_id' => 'required|exists:divisis,id',
            'barang_ajuan' => 'required|string|max:255',
            'kategori_barang' => 'required|in:RTK,ATK',
            'banyak_barang' => 'required|integer',
            'satuan' => 'required|string',
            'harga' => 'required|integer',
            'total' => 'required|integer',
            'nomor_telp' => 'required|regex:/^[0-9]{10,15}$/'
        ]);

        try {
            $ajuan = Ajuan::create($validated);
            
            // Kirim notifikasi WhatsApp ke Grup
            $this->sendWhatsAppGroupNotification($ajuan);
            
            // Check the submit action
            $submitAction = $request->input('submit_action');
            
            if ($submitAction === 'multiple') {
                // If "Kirim & Isi Lagi" is clicked, redirect back with success message
                return redirect()->back()
                    ->with('success', 'Pengajuan berhasil dikirim! Silakan isi form lagi.')
                    ->withInput(['divisi_id']); // Optionally keep divisi selected
            } else {
                // Default behavior for single submission
                return redirect()->route('display.after-submit')
                    ->with('success', 'Pengajuan berhasil dikirim!');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim pengajuan.')
                ->withInput();
        }
    }

    private function sendWhatsAppGroupNotification(Ajuan $ajuan)
    {
        try {
            // ID Grup WhatsApp (pastikan sudah dikonfigurasi di .env)
            $groupId = env('FONTEE_WHATSAPP_GROUP_ID');

            // Format pesan notifikasi untuk Grup
            $message = "*PENGAJUAN BARANG BARU*\n\n"
                . "*Nama SPA:* {$ajuan->nama_spa}\n"
                . "*Divisi:* {$ajuan->divisi->divisi}\n"
                . "*Barang Ajuan:* {$ajuan->barang_ajuan}\n"
                . "*Kategori:* {$ajuan->kategori_barang}\n"
                . "*Jumlah:* {$ajuan->banyak_barang} {$ajuan->satuan}\n"
                . "*Harga Satuan:* Rp " . number_format($ajuan->harga, 0, ',', '.') . "\n"
                . "*Total Harga:* Rp " . number_format($ajuan->total, 0, ',', '.') . "\n"
                . "*Kontak:* {$ajuan->nomor_telp}\n\n"
                . "Mohon segera ditindaklanjuti.";

            // Kirim pesan WhatsApp ke Grup
            $this->whatsAppService->sendMessageCurl($groupId, $message);

            // Log keberhasilan pengiriman
            \Log::info('Notifikasi Pengajuan Barang berhasil dikirim ke Grup WhatsApp');
        } catch (\Exception $e) {
            // Log error pengiriman WhatsApp
            \Log::error('Gagal mengirim notifikasi WhatsApp ke Grup: ' . $e->getMessage());
            
            // Opsional: Tambahkan logika tambahan jika pengiriman WhatsApp gagal
        }
    }

    public function exportExcel()
    {
        $aduan = Ajuan::with('divisi')->latest()->get();


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'A1' => 'Nama SPA', 'B1' => 'Divisi', 'C1'=> 'Barang Ajuan',
            'D1' => 'Kategori Barang', 'E1' => 'Banyak Barang', 'F1' => 'Satuan',
            'G1' => 'Harga', 'H1' => 'Total Harga', 'I1' => 'Nomor Telp', 'J1' => 'Tanggal Masuk'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Menjadikan header bold
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // Data dari database
        $row = 2;
        foreach ($aduan as $item) {
            $sheet->setCellValue('A' . $row, $item->nama_spa);
            $sheet->setCellValue('B' . $row, $item->divisi->divisi ?? 'Tidak Ada Divisi');
            $sheet->setCellValue('C' . $row, $item->barang_ajuan);
            $sheet->setCellValue('D' . $row, $item->kategori_barang);
            $sheet->setCellValue('E' . $row, $item->banyak_barang);
            $sheet->setCellValue('F' . $row, $item->satuan);
            $sheet->setCellValue('G' . $row, $item->harga);
            $sheet->setCellValue('H' . $row, $item->total);
            $sheet->setCellValue('I' . $row, $item->nomor_telp);
            $sheet->setCellValue('J' . $row, $item->created_at->format('Y-m-d H:i:s'));
            $row++;
        }

        // Menambahkan border untuk semua data
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle("A1:J$lastRow")->applyFromArray($styleArray);

        // Simpan dan kirimkan file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan Ajuan.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}