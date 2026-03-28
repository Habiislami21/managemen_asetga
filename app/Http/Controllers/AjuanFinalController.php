<?php

namespace App\Http\Controllers;

use App\Models\AjuanRutin;
use App\Models\AjuanFinal;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class AjuanFinalController extends Controller
{
    public function index(Request $request)
    {
        // Get the selected month/year or default to current month
        $selectedMonth = $request->get('month', Carbon::now()->format('m'));
        $selectedYear = $request->get('year', Carbon::now()->format('Y'));
        
        // Build the query untuk ajuan rutin yang disetujui
        $query = DB::table('ajuan_rutins')
            ->select(
                'ajuan_rutins.nama_spa',
                'divisis.divisi as nama_divisi',
                'ajuan_rutins.nomor_telp',
                'ajuan_rutins.created_at as tanggal_ajuan',
                DB::raw('SUM(ajuan_rutins.total) as total_ajuan'),
                DB::raw('COUNT(ajuan_rutins.id) as jumlah_item'),
                'ajuan_rutins.approved_at',
                'users.name as approved_by_name'
            )
            ->join('divisis', 'ajuan_rutins.divisi_id', '=', 'divisis.id')
            ->leftJoin('users', 'ajuan_rutins.approved_by', '=', 'users.id')
            ->where('ajuan_rutins.status', 'disetujui') // Hanya yang disetujui
            ->whereRaw('MONTH(ajuan_rutins.created_at) = ?', [$selectedMonth])
            ->whereRaw('YEAR(ajuan_rutins.created_at) = ?', [$selectedYear])
            ->groupBy([
                'ajuan_rutins.nama_spa',
                'ajuan_rutins.created_at',
                'divisis.divisi',
                'ajuan_rutins.nomor_telp',
                'ajuan_rutins.approved_at',
                'users.name'
            ])
            ->orderBy('tanggal_ajuan', 'desc');
        
        $ajuanList = $query->get();
        
        // Get the detail data dengan filter yang sama
        $detailAjuan = AjuanRutin::with(['divisi', 'approvedBy'])
            ->where('status', 'disetujui')
            ->whereRaw('MONTH(created_at) = ?', [$selectedMonth])
            ->whereRaw('YEAR(created_at) = ?', [$selectedYear])
            ->get();
        
        // Generate month list untuk dropdown (past 12 months)
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months[$date->format('Ym')] = $date->translatedFormat('F Y');
        }
        
        // Get statistics data
        $statistics = $this->getStatistics($selectedMonth, $selectedYear);
        
        $divisis = Divisi::all();
        
        return view('admin.ajuan-final', compact('ajuanList', 'detailAjuan', 'months', 'selectedMonth', 'selectedYear', 'statistics', 'divisis'));
    }

    public function getAjuanItems(Request $request)
    {
        try {
            $namaSpa = $request->input('nama_spa');
            $tanggalAjuan = $request->input('tanggal_ajuan');
            
            $items = AjuanRutin::with(['divisi'])
                ->where('nama_spa', $namaSpa)
                ->where('status', 'disetujui')
                ->whereDate('created_at', $tanggalAjuan)
                ->get();
            
            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada item ajuan ditemukan'
                ], 404);
            }
            
            $formattedItems = $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'barang_ajuan' => $item->barang_ajuan,
                    'kategori_barang' => $item->kategori_barang,
                    'banyak_barang' => $item->banyak_barang,
                    'satuan' => $item->satuan,
                    'harga' => $item->harga,
                    'total' => $item->total,
                    'keterangan' => $item->keterangan ?? ''
                ];
            });
            
            return response()->json([
                'success' => true,
                'items' => $formattedItems,
                'divisi' => $items->first()->divisi->divisi ?? '',
                'total_ajuan' => $items->sum('total')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting ajuan items: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ajuan_rutins,id',
            'field' => 'required|string|in:barang_ajuan,kategori_barang,banyak_barang,satuan,harga,keterangan',
            'value' => 'required'
        ]);

        try {
            $ajuanRutin = AjuanRutin::find($request->id);
            
            if (!$ajuanRutin || $ajuanRutin->status !== 'disetujui') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ajuan tidak valid atau belum disetujui'
                ], 404);
            }

            // Validate specific fields
            $field = $request->field;
            $value = $request->value;
            
            if ($field === 'banyak_barang' || $field === 'harga') {
                if (!is_numeric($value) || $value < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai harus berupa angka positif'
                    ], 400);
                }
                $value = floatval($value);
            }
            
            if ($field === 'kategori_barang' && !in_array($value, ['RTK', 'ATK'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori barang tidak valid'
                ], 400);
            }
            
            $satuanList = ['bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'];
            if ($field === 'satuan' && !in_array($value, $satuanList)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Satuan tidak valid'
                ], 400);
            }

            // Update the field
            $ajuanRutin->$field = $value;
            
            // Recalculate total if quantity or price changed
            if ($field === 'banyak_barang' || $field === 'harga') {
                $ajuanRutin->total = $ajuanRutin->banyak_barang * $ajuanRutin->harga;
            }
            
            $ajuanRutin->save();

            // Get updated items for this SPA to calculate new total
            $updatedItems = AjuanRutin::where('nama_spa', $ajuanRutin->nama_spa)
                ->where('status', 'disetujui')
                ->whereDate('created_at', $ajuanRutin->created_at->format('Y-m-d'))
                ->get();

            $newTotalAjuan = $updatedItems->sum('total');
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'item' => [
                    'id' => $ajuanRutin->id,
                    'barang_ajuan' => $ajuanRutin->barang_ajuan,
                    'kategori_barang' => $ajuanRutin->kategori_barang,
                    'banyak_barang' => $ajuanRutin->banyak_barang,
                    'satuan' => $ajuanRutin->satuan,
                    'harga' => $ajuanRutin->harga,
                    'total' => $ajuanRutin->total,
                    'keterangan' => $ajuanRutin->keterangan,
                    'formatted_harga' => number_format($ajuanRutin->harga, 0, ',', '.'),
                    'formatted_total' => number_format($ajuanRutin->total, 0, ',', '.')
                ],
                'total_ajuan' => $newTotalAjuan,
                'formatted_total_ajuan' => number_format($newTotalAjuan, 0, ',', '.')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }

    public function exportAjuan(Request $request)
    {
        try {
            $namaSpa = $request->input('nama_spa');
            $tanggalAjuan = $request->input('tanggal_ajuan');
            
            // Build query untuk ajuan rutin yang disetujui
            $query = AjuanRutin::with(['divisi', 'approvedBy'])
                ->where('status', 'disetujui');
            
            if ($namaSpa) {
                $query->where('nama_spa', $namaSpa);
                if ($tanggalAjuan) {
                    $query->whereDate('created_at', $tanggalAjuan);
                }
            }

            $ajuans = $query->get();
            
            if ($ajuans->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data untuk diekspor'], 404);
            }
            
            // Create spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $title = $namaSpa ? 'Ajuan Disetujui: ' . $namaSpa : 'Daftar Ajuan Disetujui';
                
            $spreadsheet->getProperties()
                ->setCreator(Auth::user()->name)
                ->setTitle($title)
                ->setSubject($title);
            
            // Header setup
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            
            // Set headers
            $headers = ['No', 'Nama SPA', 'Divisi', 'Nomor Telepon', 'Barang Ajuan', 'Kategori', 
                       'Jumlah', 'Satuan', 'Harga Satuan (Rp)', 'Total (Rp)', 'Keterangan', 
                       'Tanggal Ajuan', 'Disetujui Oleh', 'Tanggal Disetujui'];
            
            foreach ($headers as $index => $header) {
                $column = chr(65 + $index); // A, B, C, etc.
                $sheet->setCellValue($column . '1', $header);
            }
            
            $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);
            
            // Fill data
            $row = 2;
            $totalSum = 0;
            foreach ($ajuans as $index => $ajuan) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $ajuan->nama_spa);
                $sheet->setCellValue('C' . $row, $ajuan->divisi->divisi ?? '');
                $sheet->setCellValue('D' . $row, $ajuan->nomor_telp);
                $sheet->setCellValue('E' . $row, $ajuan->barang_ajuan);
                $sheet->setCellValue('F' . $row, $ajuan->kategori_barang);
                $sheet->setCellValue('G' . $row, $ajuan->banyak_barang);
                $sheet->setCellValue('H' . $row, $ajuan->satuan);
                $sheet->setCellValue('I' . $row, $ajuan->harga);
                $sheet->setCellValue('J' . $row, $ajuan->total);
                $sheet->setCellValue('K' . $row, $ajuan->keterangan);
                $sheet->setCellValue('L' . $row, Carbon::parse($ajuan->created_at)->format('d-m-Y'));
                $sheet->setCellValue('M' . $row, $ajuan->approvedBy->name ?? '');
                $sheet->setCellValue('N' . $row, $ajuan->approved_at ? Carbon::parse($ajuan->approved_at)->format('d-m-Y H:i') : '');
                
                $totalSum += $ajuan->total;
                $row++;
            }
            
            // Add total row
            $sheet->setCellValue('I' . $row, 'TOTAL');
            $sheet->setCellValue('J' . $row, $totalSum);
            $sheet->getStyle('I' . $row . ':J' . $row)->getFont()->setBold(true);
            
            // Auto-size columns
            foreach (range('A', 'N') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Format currency
            $sheet->getStyle('I2:J' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
            
            $filenamePart = $namaSpa ? 'ajuan_' . str_replace(' ', '_', $namaSpa) : 'ajuan_disetujui';
            $filename = $filenamePart . '_' . date('d-m-Y_H-i-s') . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);
            
            return Response::download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error exporting ajuan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatistics($selectedMonth, $selectedYear)
    {
        // Statistik khusus untuk ajuan yang disetujui
        $totalStats = DB::table('ajuan_rutins')
            ->select(
                DB::raw('COUNT(DISTINCT nama_spa) as total_approved_submissions'),
                DB::raw('COUNT(*) as total_approved_items'),
                DB::raw('SUM(total) as total_approved_amount'),
                DB::raw('AVG(total) as avg_approved_item_amount')
            )
            ->where('status', 'disetujui')
            ->whereRaw('MONTH(created_at) = ?', [$selectedMonth])
            ->whereRaw('YEAR(created_at) = ?', [$selectedYear])
            ->first();
        
        return [
            'totalStats' => $totalStats
        ];
    }

    public function deleteItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ajuan_rutins,id'
        ]);
    
        try {
            $ajuanRutin = AjuanRutin::find($request->id);
            
            if (!$ajuanRutin || $ajuanRutin->status !== 'disetujui') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ajuan tidak valid atau belum disetujui'
                ], 404);
            }
    
            // Get information about the SPA submission before deletion
            $namaSpa = $ajuanRutin->nama_spa;
            $createdAt = $ajuanRutin->created_at;
            $divisiId = $ajuanRutin->divisi_id;
    
            // Check if this is the last item in the submission
            $totalItemsInSubmission = AjuanRutin::where('nama_spa', $namaSpa)
                ->where('status', 'disetujui')
                ->whereDate('created_at', $createdAt->format('Y-m-d'))
                ->where('divisi_id', $divisiId)
                ->count();
    
            if ($totalItemsInSubmission <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus item terakhir. Ajuan harus memiliki minimal satu item.'
                ], 400);
            }
    
            // Store item details for response
            $deletedItem = [
                'id' => $ajuanRutin->id,
                'barang_ajuan' => $ajuanRutin->barang_ajuan,
                'total' => $ajuanRutin->total
            ];
    
            // Delete the item
            $ajuanRutin->delete();
    
            // Get remaining items to calculate new total
            $remainingItems = AjuanRutin::where('nama_spa', $namaSpa)
                ->where('status', 'disetujui')
                ->whereDate('created_at', $createdAt->format('Y-m-d'))
                ->where('divisi_id', $divisiId)
                ->get();
    
            $newTotalAjuan = $remainingItems->sum('total');
            
            // Check if this was the last item (after deletion)
            $isLastItemDeleted = $remainingItems->count() === 0;
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus',
                'deleted_item' => $deletedItem,
                'remaining_items_count' => $remainingItems->count(),
                'total_ajuan' => $newTotalAjuan,
                'formatted_total_ajuan' => number_format($newTotalAjuan, 0, ',', '.'),
                'is_last_item_deleted' => $isLastItemDeleted,
                'remaining_items' => $remainingItems->toArray() // Include remaining items for frontend update
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item'
            ], 500);
        }
    }
}