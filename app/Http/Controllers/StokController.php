<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\StokDivisi;
use App\Models\StokPusat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;


class StokController extends Controller
{
    public function index()
    {
        $stoks = StokPusat::all();
        $assetGADivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        return view('admin.ajuan-keseluruhan', compact('stoks', 'assetGADivisi'));
    }
    
    public function checkKodeBarang()
    {
        // Cari kode barang yang tersedia (1-999)
        $usedCodes = StokPusat::pluck('kode_barang')->toArray();
        
        // Cari kode yang belum digunakan
        $availableCode = null;
        for ($i = 1; $i <= 999; $i++) {
            if (!in_array($i, $usedCodes)) {
                $availableCode = $i;
                break;
            }
        }
        
        // Jika semua kode sudah digunakan, berikan pesan error
        if ($availableCode === null) {
            return response()->json([
                'message' => 'Semua kode barang telah digunakan. Hapus beberapa barang terlebih dahulu.'
            ], 422);
        }
        
        return response()->json([
            'kode_barang' => $availableCode
        ]);
    }
    
    public function tambah(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|numeric|min:1|max:999|unique:stok_pusats',
            'divisi_id' => 'required|exists:divisis,id',
            'nama_barang' => 'required|string|max:255',
            'sisa_stok' => 'required|numeric|min:0',
            'stok_ideal' => 'required|numeric|min:0',
            'satuan' => 'required|string|in:' . implode(',', StokPusat::SATUAN),
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Cek apakah divisi adalah pusat (Asset & GA)
        $divisi = Divisi::find($request->divisi_id);
        if (!$divisi || !$divisi->is_pusat) {
            return response()->json([
                'message' => 'Barang baru hanya boleh ditambahkan ke divisi pusat (Asset & GA).'
            ], 422);
        }
        
        // Buat stok baru
        $stok = new StokPusat();
        $stok->kode_barang = $request->kode_barang;
        $stok->nama_barang = $request->nama_barang;
        $stok->sisa_stok = $request->sisa_stok;
        $stok->stok_ideal = $request->stok_ideal;
        $stok->satuan = $request->satuan;
        $stok->save();
        
        return response()->json([
            'message' => 'Barang berhasil ditambahkan ke stok pusat.',
            'stok' => $stok
        ]);
    }
    
    public function updateStokIdeal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stok_id_ideal' => 'required|exists:stok_pusats,id',
            'stok_ideal' => 'required|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $stok = StokPusat::find($request->stok_id_ideal);
        $stok->stok_ideal = $request->stok_ideal;
        $stok->save();
        
        return response()->json([
            'message' => 'Stok ideal berhasil diperbarui.',
            'stok' => $stok
        ]);
    }
    
    public function updateStok(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stok_id' => 'required|exists:stok_pusats,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $stok = StokPusat::find($request->stok_id);
        
        if ($request->tipe == 'masuk') {
            $stok->sisa_stok += $request->jumlah;
        } else {
            if ($stok->sisa_stok < $request->jumlah) {
                return response()->json([
                    'message' => 'Jumlah stok keluar melebihi stok yang tersedia.'
                ], 422);
            }
            $stok->sisa_stok -= $request->jumlah;
        }
        
        $stok->save();
        
        return response()->json([
            'message' => 'Stok berhasil diperbarui.',
            'stok' => $stok
        ]);
    }
    
    public function hapus(Request $request)
    {
        $stok = StokPusat::find($request->id);
        
        if (!$stok) {
            return response()->json([
                'message' => 'Stok tidak ditemukan.'
            ], 404);
        }
        
        $stok->delete();
        
        return response()->json([
            'message' => 'Stok berhasil dihapus.'
        ]);
    }

    //Fungsi Divisi
    public function ajuanDivisi()
    {
        $user = Auth::user();
        
        // Check user role and permissions
        $allowedRoles = ['admin', 'ga', 'aset', 'pj_divisi'];
        
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Get divisions based on user role
        if ($user->role === 'pj_divisi' && $user->divisi_id) {
            // PJ Divisi can only see their own division
            $divisis = Divisi::where('id', $user->divisi_id)
                             ->where('divisi', '!=', 'Asset & GA')
                             ->get();
        } else {
            // Admin, GA, and Aset can see all divisions except Asset & GA
            $divisis = Divisi::where('divisi', '!=', 'Asset & GA')->get();
        }
        
        return view('admin.ajuan-divisi', compact('divisis'));
    }

    public function getAssetGABarang()
    {
        $assetGADivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        if (!$assetGADivisi) {
            return response()->json(['message' => 'Divisi Asset & GA tidak ditemukan'], 404);
        }
        
        $barangs = StokPusat::all();
        
        return response()->json($barangs);
    }

    public function tambahBarangDivisi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'divisi_id' => 'required|exists:divisis,id',
            'kode_barang' => 'required|exists:stok_pusats,kode_barang',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $stokPusat = StokPusat::where('kode_barang', $request->kode_barang)->first();
        
        $existingStokDivisi = StokDivisi::where('divisi_id', $request->divisi_id)
            ->where('stok_pusat_id', $stokPusat->id)
            ->first();
        
        if ($existingStokDivisi) {
            return response()->json([
                'message' => 'Barang sudah ada di divisi ini.'
            ], 422);
        }
        
        $stokDivisi = new StokDivisi();
        $stokDivisi->divisi_id = $request->divisi_id;
        $stokDivisi->stok_pusat_id = $stokPusat->id;
        $stokDivisi->sisa_stok = 0;
        $stokDivisi->stok_ideal = 0;
        $stokDivisi->save(); // Tambahkan save() untuk menyimpan ke database
        
        return response()->json([
            'message' => 'Barang berhasil ditambahkan ke divisi.',
            'stok_divisi' => $stokDivisi
        ]);
    }


    public function getStokDivisi(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'divisi_id' => 'required|exists:divisis,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ], 422);
        }
        
        // Role-based access control
        if ($user->role === 'pj_divisi') {
            // PJ Divisi can only access their own division's data
            if ($user->divisi_id != $request->divisi_id) {
                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'Anda tidak memiliki akses ke data divisi ini.'
                ], 403);
            }
        }
        
        $stokDivisiItems = StokDivisi::with('stokPusat')
            ->where('divisi_id', $request->divisi_id)
            ->get();
        
        $stokDivisi = $stokDivisiItems->map(function($item) {
            return [
                'kode_barang' => $item->stokPusat->kode_barang,
                'nama_barang' => $item->stokPusat->nama_barang,
                'sisa_stok' => $item->sisa_stok,
                'stok_ideal' => $item->stok_ideal,
                'satuan' => $item->stokPusat->satuan,
                'kekurangan' => max(0, $item->stok_ideal - $item->sisa_stok),
                'id' => $item->id
            ];
        });
        
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $stokDivisiItems->count(),
            'recordsFiltered' => $stokDivisiItems->count(),
            'data' => $stokDivisi
        ]);
    }

    public function updateStokDivisi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stok_id' => 'required|exists:stok_divisis,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $stokDivisi = StokDivisi::find($request->stok_id);
        
        if ($request->tipe == 'masuk') {
            // Get the central stock item
            $stokPusat = $stokDivisi->stokPusat;
            
            // Check if central stock has enough items
            if ($stokPusat->sisa_stok < $request->jumlah) {
                return response()->json([
                    'message' => 'Stok di pusat tidak mencukupi untuk diambil.'
                ], 422);
            }
            
            // Update both stocks
            $stokPusat->sisa_stok -= $request->jumlah;
            $stokDivisi->sisa_stok += $request->jumlah;
            
            $stokPusat->save();
            $stokDivisi->save();
            
            // Reset progress cek bulanan divisi ke 0% karena ada barang baru masuk
            StokDivisi::where('divisi_id', $stokDivisi->divisi_id)->update([
                'status_cek_bulanan' => null,
                'stok_fisik_cek' => null,
                'tgl_cek_bulanan' => null,
                'dicek_oleh' => null,
                'keterangan_cek' => null
            ]);
            
            return response()->json([
                'message' => 'Stok berhasil ditambahkan dari pusat.',
                'stok_divisi' => $stokDivisi
            ]);
        } else { // Tipe keluar
            if ($stokDivisi->sisa_stok < $request->jumlah) {
                return response()->json([
                    'message' => 'Jumlah stok keluar melebihi stok yang tersedia di divisi.'
                ], 422);
            }
            
            $stokDivisi->sisa_stok -= $request->jumlah;
            $stokDivisi->save();
            
            return response()->json([
                'message' => 'Stok divisi berhasil dikurangi.',
                'stok_divisi' => $stokDivisi
            ]);
        }
    }

    public function getStokPusatInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|exists:stok_pusats,kode_barang',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Kode barang tidak valid.'
            ], 422);
        }
        
        $stokPusat = StokPusat::where('kode_barang', $request->kode_barang)->first();
        
        return response()->json([
            'sisa_stok' => $stokPusat->sisa_stok,
            'satuan' => $stokPusat->satuan
        ]);
    }

    public function updateStokIdealDivisi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stok_id' => 'required|exists:stok_divisis,id',
            'stok_ideal' => 'required|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $stokDivisi = StokDivisi::find($request->stok_id);
        $stokDivisi->stok_ideal = $request->stok_ideal;
        $stokDivisi->save();
        
        return response()->json([
            'message' => 'Stok ideal berhasil diperbarui.',
            'stok_divisi' => $stokDivisi
        ]);
    }

    public function hapusStokDivisi(Request $request)
    {
        $stokDivisi = StokDivisi::find($request->id);
        
        if (!$stokDivisi) {
            return response()->json([
                'message' => 'Stok divisi tidak ditemukan.'
            ], 404);
        }
        
        $stokDivisi->delete();
        
        return response()->json([
            'message' => 'Barang berhasil dihapus dari divisi.'
        ]);
    }

    public function exportExcel()
    {
        $stok = StokPusat::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul sheet
        $sheet->setTitle('Stok Pusat');

        // Header kolom
        $headers = [
            'A1' => 'Kode Barang', 
            'B1' => 'Nama Barang', 
            'C1' => 'Jumlah Stok', 
            'D1' => 'Stok Ideal', 
            'E1' => 'Satuan', 
            'F1' => 'Kekurangan',
            'G1' => 'Terakhir Diperbarui'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Menjadikan header bold
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Data dari database
        $row = 2;
        foreach ($stok as $item) {
            $kekurangan = max(0, $item->stok_ideal - $item->sisa_stok);
            
            $sheet->setCellValue('A' . $row, $item->kode_barang);
            $sheet->setCellValue('B' . $row, $item->nama_barang);
            $sheet->setCellValue('C' . $row, $item->sisa_stok);
            $sheet->setCellValue('D' . $row, $item->stok_ideal);
            $sheet->setCellValue('E' . $row, $item->satuan);
            $sheet->setCellValue('F' . $row, $kekurangan);
            $sheet->setCellValue('G' . $row, $item->updated_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size kolom untuk memastikan semua data terlihat
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
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
        $sheet->getStyle("A1:G$lastRow")->applyFromArray($styleArray);
        
        // Tambahkan ringkasan data pada bagian bawah
        $row += 2; // Berikan jarak 1 baris kosong
        $sheet->setCellValue('A' . $row, 'Ringkasan:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Item:');
        $sheet->setCellValue('B' . $row, $stok->count());
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Stok Tersedia:');
        $sheet->setCellValue('B' . $row, $stok->sum('sisa_stok'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Kekurangan Stok:');
        $totalKekurangan = $stok->sum(function($item) {
            return max(0, $item->stok_ideal - $item->sisa_stok);
        });
        $sheet->setCellValue('B' . $row, $totalKekurangan);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Stok Ideal:');
        $sheet->setCellValue('B' . $row, $stok->sum('stok_ideal'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Waktu Export:');
        $sheet->setCellValue('B' . $row, now()->format('Y-m-d H:i:s'));

        $writer = new Xlsx($spreadsheet);
        $filename = 'stok_pusat_' . date('Y-m-d') . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }

    public function detailStok($kodeBarang)
    {
        // Ambil data stok pusat
        $stokPusat = StokPusat::where('kode_barang', $kodeBarang)->first();
        
        if (!$stokPusat) {
            return response()->json([
                'message' => 'Stok tidak ditemukan.'
            ], 404);
        }
        
        // Ambil divisi Asset & GA
        $assetGADivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        // Ambil data stok di semua divisi untuk barang ini
        $stokDivisiItems = StokDivisi::where('stok_pusat_id', $stokPusat->id)
            ->with('divisi')
            ->get();
        
        // Format data untuk respons
        $stokDivisi = $stokDivisiItems->map(function($item) {
            return [
                'divisi' => $item->divisi->divisi,
                'sisa_stok' => $item->sisa_stok,
                'stok_ideal' => $item->stok_ideal,
                'kekurangan' => max(0, $item->stok_ideal - $item->sisa_stok)
            ];
        });
        
        // Hitung total stok (pusat + semua divisi)
        $totalStok = $stokPusat->sisa_stok + $stokDivisiItems->sum('sisa_stok');
        
        return response()->json([
            'stok_pusat' => [
                'kode_barang' => $stokPusat->kode_barang,
                'nama_barang' => $stokPusat->nama_barang,
                'sisa_stok' => $stokPusat->sisa_stok,
                'stok_ideal' => $stokPusat->stok_ideal,
                'satuan' => $stokPusat->satuan,
                'divisi' => $assetGADivisi ? $assetGADivisi->divisi : 'Asset & GA'
            ],
            'stok_divisi' => $stokDivisi,
            'total_stok' => $totalStok
        ]);
    }

    public function exportExcelDivisi(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'divisi_id' => 'required|exists:divisis,id'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Divisi tidak valid.');
        }
        
        // Role-based access control
        if ($user->role === 'pj_divisi') {
            // PJ Divisi can only export their own division's data
            if ($user->divisi_id != $request->divisi_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengekspor data divisi ini.');
            }
        }
        
        // Ambil data divisi
        $divisi = Divisi::find($request->divisi_id);
        if (!$divisi) {
            return redirect()->back()->with('error', 'Divisi tidak ditemukan.');
        }
        
        // Rest of the export code remains the same...
        $stokDivisiItems = StokDivisi::with('stokPusat')
            ->where('divisi_id', $request->divisi_id)
            ->get();
        
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul sheet
        $sheet->setTitle('Stok ' . $divisi->divisi);
        
        // Header kolom
        $headers = [
            'A1' => 'Kode Barang', 
            'B1' => 'Nama Barang', 
            'C1' => 'Jumlah Stok', 
            'D1' => 'Stok Ideal', 
            'E1' => 'Satuan', 
            'F1' => 'Kekurangan',
            'G1' => 'Terakhir Diperbarui'
        ];
        
        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }
        
        // Menjadikan header bold
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        
        // Data dari database
        $row = 2;
        foreach ($stokDivisiItems as $item) {
            $kekurangan = max(0, $item->stok_ideal - $item->sisa_stok);
            
            $sheet->setCellValue('A' . $row, $item->stokPusat->kode_barang);
            $sheet->setCellValue('B' . $row, $item->stokPusat->nama_barang);
            $sheet->setCellValue('C' . $row, $item->sisa_stok);
            $sheet->setCellValue('D' . $row, $item->stok_ideal);
            $sheet->setCellValue('E' . $row, $item->stokPusat->satuan);
            $sheet->setCellValue('F' . $row, $kekurangan);
            $sheet->setCellValue('G' . $row, $item->updated_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size kolom untuk memastikan semua data terlihat
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
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
        $sheet->getStyle("A1:G$lastRow")->applyFromArray($styleArray);
        
        // Tambahkan ringkasan data pada bagian bawah
        $row += 2; // Berikan jarak 1 baris kosong
        $sheet->setCellValue('A' . $row, 'Ringkasan:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Item:');
        $sheet->setCellValue('B' . $row, $stokDivisiItems->count());
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Stok Tersedia:');
        $sheet->setCellValue('B' . $row, $stokDivisiItems->sum('sisa_stok'));
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Total Kekurangan Stok:');
        $totalKekurangan = $stokDivisiItems->sum(function($item) {
            return max(0, $item->stok_ideal - $item->sisa_stok);
        });
        $sheet->setCellValue('B' . $row, $totalKekurangan);
        $row++;
        
        $sheet->setCellValue('A' . $row, 'Waktu Export:');
        $sheet->setCellValue('B' . $row, now()->format('Y-m-d H:i:s'));
        
        // Buat writer untuk output excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'stok_' . str_replace(' ', '_', strtolower($divisi->divisi)) . '_' . date('Y-m-d') . '.xlsx';
        
        // Output file sebagai download
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }

    public function editBarang(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stok_id' => 'required|exists:stok_pusats,id',
            'nama_barang' => 'required|string|max:255',
            'stok_ideal' => 'required|numeric|min:0',
            'satuan' => 'required|string|in:' . implode(',', StokPusat::SATUAN),
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Ambil stok yang akan diubah
        $stok = StokPusat::find($request->stok_id);
        
        // Update data
        $stok->nama_barang = $request->nama_barang;
        $stok->stok_ideal = $request->stok_ideal;
        $stok->satuan = $request->satuan;
        $stok->save();
        
        return response()->json([
            'message' => 'Barang berhasil diperbarui.',
            'stok' => $stok
        ]);
    }
}