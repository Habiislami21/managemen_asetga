<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\Divisi;
use App\Services\FonteeWhatsAppService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class AduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisis = Divisi::all();
        $aduans = Aduan::with('divisi')->latest()->get();
        $lokasi_pengaduan = Aduan::LOKASI_PENGADUAN;
        return view('form.pengaduan-aset', compact('divisis', 'aduans', 'lokasi_pengaduan'));
    }

    public function updateStatus($id)
    {
        $aduan = Aduan::findOrFail($id);
        
        // Toggle status antara 'pending' dan 'selesai'
        $new_status = $aduan->status === 'selesai' ? 'pending' : 'selesai';
        
        $aduan->status = $new_status;
        $aduan->save();
        
        return response()->json([
            'success' => true,
            'new_status' => $new_status
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    protected $whatsAppService;

    public function __construct(FonteeWhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_spa' => 'required|string|max:255',
            'divisi_id' => 'required|exists:divisis,id',
            'amanah' => 'required|string|max:255',
            'lokasi_pengaduan' => 'required|string',
            'jenis_pengaduan' => 'required|in:Aset,GA',
            'kerusakan' => 'required|string|max:255',
            'rincian_pengaduan' => 'required|string',
            'nomor_telp' => 'required|string|min:10|max:15'
        ]);
    
        try {
            $aduan = Aduan::create($validated);
            
            // Kirim notifikasi WhatsApp ke Grup
            $this->sendWhatsAppGroupNotification($aduan);
            
            // Cek apakah user ingin mengisi form lagi
            if ($request->submit_action == 'multiple') {
                return redirect()->route('pengaduan.index')
                    ->with('success', 'Pengaduan berhasil dikirim! Silakan isi form lagi untuk pengaduan baru.');
            }
            
            // Jika tidak, arahkan ke halaman after-submit seperti biasa
            return redirect()->route('display.after-submit')->with([
                'success' => 'Pengaduan berhasil dikirim!',
                'aduan' => $aduan
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim pengaduan.')
                ->withInput();
        }
    }

    private function sendWhatsAppGroupNotification(Aduan $aduan)
    {
        try {
            $groupId = env('FONTEE_WHATSAPP_GROUP_ID');
            $message = "*LAPORAN ADUAN BARU*\n\n"
                . "*Nama SPA:* {$aduan->nama_spa}\n"
                . "*Divisi:* {$aduan->divisi->divisi}\n"
                . "*Lokasi:* {$aduan->lokasi_pengaduan}\n"
                . "*Jenis Pengaduan:* {$aduan->jenis_pengaduan}\n"
                . "*Kerusakan:* {$aduan->kerusakan}\n\n"
                . "*Rincian:*\n{$aduan->rincian_pengaduan}\n\n"
                . "*Kontak:* {$aduan->nomor_telp}\n\n"
                . "Mohon segera ditindaklanjuti.";

            // Kirim pesan WhatsApp ke Grup
            $this->whatsAppService->sendMessageCurl($groupId, $message);
            \Log::info('Notifikasi Aduan berhasil dikirim ke Grup WhatsApp');
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim notifikasi WhatsApp ke Grup: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        $aduan = Aduan::with('divisi')->latest()->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = [
            'A1' => 'Nama SPA', 'B1' => 'Divisi', 'C1' => 'Amanah', 
            'D1' => 'Lokasi Pengaduan', 'E1' => 'Jenis Pengaduan', 
            'F1' => 'Kerusakan', 'G1' => 'Rincian Pengaduan', 
            'H1' => 'Nomor Telepon', 'I1' => 'Status', 'J1' => 'Tanggal Masuk'
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
            $sheet->setCellValue('C' . $row, $item->amanah);
            $sheet->setCellValue('D' . $row, $item->lokasi_pengaduan);
            $sheet->setCellValue('E' . $row, $item->jenis_pengaduan);
            $sheet->setCellValue('F' . $row, $item->kerusakan);
            $sheet->setCellValue('G' . $row, $item->rincian_pengaduan);
            $sheet->setCellValue('H' . $row, $item->nomor_telp);
            $sheet->setCellValue('I' . $row, $item->status);
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
        $filename = 'Laporan Aduan.xlsx';

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