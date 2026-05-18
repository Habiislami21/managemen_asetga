<?php

namespace App\Http\Controllers;

use App\Models\AjuanRutin;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class AjuanRutinController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();
        $satuan = AjuanRutin::SATUAN;
        return view('form.ajuan-rutin', compact('divisis', 'satuan'));
    }

    public function create()
    {
        // Get all divisions for the dropdown
        $divisis = Divisi::all();
        return view('form.ajuan-rutin', compact('divisis'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'nama_spa' => 'required|string|max:255',
                'divisi_id' => 'required|exists:divisis,id',
                'nomor_telp' => 'required|string|max:15',
                'total_amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.barang_ajuan' => 'required|string|max:255',
                'items.*.kategori_barang' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // PERBAIKAN: Ajuan baru selalu berstatus 'pending', tidak otomatis disetujui
            // Tidak perlu mengubah status ajuan lain saat membuat ajuan baru
            
            // Store the new request items
            foreach ($request->items as $item) {
                AjuanRutin::create([
                    'nama_spa' => $request->nama_spa,
                    'divisi_id' => $request->divisi_id,
                    'nomor_telp' => $request->nomor_telp,
                    'barang_ajuan' => $item['barang_ajuan'],
                    'kategori_barang' => $item['kategori_barang'],
                    'banyak_barang' => $item['banyak_barang'] ?? 1,
                    'satuan' => $item['satuan'] ?? 'pcs',
                    'harga' => $item['harga'] ?? 0,
                    'total' => $item['total'] ?? 0,
                    'keterangan' => $item['keterangan'] ?? '',
                    'status' => 'pending', // Selalu pending untuk ajuan baru
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()->route('login')->with('success', 'Ajuan rutin berhasil disimpan dengan status pending.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing ajuan rutin: '.$e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function editBatch(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'pj_divisi') {
            return redirect()->route('admin.ajuan-rutin')->with('error', 'Akses ditolak.');
        }

        $namaSpa = $request->query('nama_spa');
        $tanggalAjuan = $request->query('tanggal_ajuan');
        $divisiId = $request->query('divisi_id');

        if (!$namaSpa || !$tanggalAjuan || !$divisiId) {
            return redirect()->route('admin.ajuan-rutin')->with('error', 'Parameter tidak lengkap.');
        }

        if (strlen($tanggalAjuan) > 10) {
            $items = AjuanRutin::where('nama_spa', $namaSpa)
                ->where('divisi_id', $divisiId)
                ->where('created_at', $tanggalAjuan)
                ->get();
        } else {
            $items = AjuanRutin::where('nama_spa', $namaSpa)
                ->where('divisi_id', $divisiId)
                ->whereDate('created_at', $tanggalAjuan)
                ->get();
        }

        if ($items->isEmpty()) {
            return redirect()->route('admin.ajuan-rutin')->with('error', 'Data tidak ditemukan.');
        }

        if ($items->first()->status !== 'buat ulang') {
            return redirect()->route('admin.ajuan-rutin')->with('error', 'Hanya ajuan dengan status "buat ulang" yang dapat diedit.');
        }

        $divisis = Divisi::all();
        $satuan = AjuanRutin::SATUAN ?? ['pcs', 'pack', 'kg', 'rim', 'kotak', 'bungkus', 'botol', 'dus', 'lusin', 'set', 'bulan'];

        return view('form.edit-ajuan-rutin', compact('items', 'divisis', 'satuan', 'namaSpa', 'tanggalAjuan', 'divisiId'));
    }

    public function updateBatch(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'pj_divisi') {
            return redirect()->route('admin.ajuan-rutin')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'old_nama_spa' => 'required|string',
            'old_tanggal_ajuan' => 'required|string',
            'old_divisi_id' => 'required|integer',
            'nama_spa' => 'required|string|max:255',
            'divisi_id' => 'required|exists:divisis,id',
            'nomor_telp' => 'required|string|max:15',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.barang_ajuan' => 'required|string|max:255',
            'items.*.kategori_barang' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $oldNamaSpa = $request->old_nama_spa;
            $oldTanggalAjuan = $request->old_tanggal_ajuan;
            $oldDivisiId = $request->old_divisi_id;

            if (strlen($oldTanggalAjuan) > 10) {
                AjuanRutin::where('nama_spa', $oldNamaSpa)
                    ->where('divisi_id', $oldDivisiId)
                    ->where('created_at', $oldTanggalAjuan)
                    ->delete();
            } else {
                AjuanRutin::where('nama_spa', $oldNamaSpa)
                    ->where('divisi_id', $oldDivisiId)
                    ->whereDate('created_at', $oldTanggalAjuan)
                    ->delete();
            }

            $createdAt = strlen($oldTanggalAjuan) > 10 ? Carbon::parse($oldTanggalAjuan) : Carbon::now();

            foreach ($request->items as $item) {
                AjuanRutin::create([
                    'nama_spa' => $request->nama_spa,
                    'divisi_id' => $request->divisi_id,
                    'nomor_telp' => $request->nomor_telp,
                    'barang_ajuan' => $item['barang_ajuan'],
                    'kategori_barang' => $item['kategori_barang'],
                    'banyak_barang' => $item['banyak_barang'] ?? 1,
                    'satuan' => $item['satuan'] ?? 'pcs',
                    'harga' => $item['harga'] ?? 0,
                    'total' => $item['total'] ?? 0,
                    'keterangan' => $item['keterangan'] ?? '',
                    'status' => 'pending',
                    'created_at' => $createdAt,
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.ajuan-rutin')->with('success', 'Ajuan rutin berhasil diperbaiki dan status menjadi pending.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating ajuan rutin: '.$e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    
    public function ajuanRutin(Request $request)
    {
        // Get the selected month/year or default to current month
        $selectedMonth = $request->get('month', Carbon::now()->format('m'));
        $selectedYear = $request->get('year', Carbon::now()->format('Y'));
        
        // Get current user
        $user = Auth::user();
        
        // Ambil data dengan pendekatan yang lebih sederhana
        $baseQuery = AjuanRutin::with(['divisi', 'approvedBy'])
            ->whereRaw('MONTH(created_at) = ?', [$selectedMonth])
            ->whereRaw('YEAR(created_at) = ?', [$selectedYear]);
    
        // Apply role-based filtering
        if (($user->role === 'kabag' || $user->role === 'pj_divisi') && $user->divisi_id) {
            $baseQuery->where('divisi_id', $user->divisi_id);
        }
        
        $allAjuan = $baseQuery->orderBy('created_at', 'desc')->get();
        
        // Group manual di PHP untuk menghindari SQL GROUP BY issues
        $ajuanList = collect();
        
        // PERBAIKAN: Gunakan kombinasi yang lebih unik untuk grouping
        // Tambahkan divisi_id DAN created_at dengan presisi detik untuk memastikan setiap submission unik
        $groupedBySubmission = $allAjuan->groupBy(function($item) {
            // Gunakan kombinasi nama_spa + divisi_id + created_at dengan presisi detik
            return $item->nama_spa . '_' . $item->divisi_id . '_' . $item->created_at->format('Y-m-d H:i:s');
        });
        
        foreach ($groupedBySubmission as $key => $items) {
            $firstItem = $items->first();
            
            // Buat unique ID yang akan digunakan di frontend
            $uniqueId = str_replace(' ', '_', $firstItem->nama_spa) . '_' . $firstItem->divisi_id . '_' . $firstItem->created_at->format('YmdHis');
            
            $ajuanList->push((object)[
                'nama_spa' => $firstItem->nama_spa,
                'nama_divisi' => $firstItem->divisi->divisi ?? 'Divisi tidak tersedia',
                'nomor_telp' => $firstItem->nomor_telp,
                'tanggal_ajuan' => $firstItem->created_at->format('Y-m-d H:i:s'),
                'total_ajuan' => $items->sum('total'),
                'jumlah_item' => $items->count(),
                'status' => $firstItem->status,
                'approved_at' => $firstItem->approved_at,
                'approved_by_name' => $firstItem->approvedBy->name ?? null,
                'divisi_id' => $firstItem->divisi_id,
                'unique_id' => $uniqueId, // Tambahkan unique_id untuk frontend
                // Tambahkan informasi untuk debugging
                'unique_key' => $key
            ]);
        }
        
        // Sort by tanggal_ajuan descending
        $ajuanList = $ajuanList->sortByDesc('tanggal_ajuan')->values();
        
        // Detail ajuan tetap sama
        $detailAjuan = $allAjuan;
        
        // Generate month list for the dropdown (past 12 months)
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months[$date->format('Ym')] = $date->translatedFormat('F Y');
        }
        
        // Get statistics data with same filtering
        $statistics = $this->getStatistics($selectedMonth, $selectedYear, $user);
        
        // Debug: Log jumlah data untuk troubleshooting
        Log::info('Data count debug', [
            'total_raw_data' => $allAjuan->count(),
            'grouped_submissions' => $groupedBySubmission->count(),
            'final_ajuan_list' => $ajuanList->count(),
            'unique_spa_names' => $allAjuan->pluck('nama_spa')->unique()->count(),
            'user_role' => $user->role,
            'user_divisi_id' => $user->divisi_id
        ]);
        
        return view('admin.ajuan-rutin', compact('ajuanList', 'detailAjuan', 'months', 'selectedMonth', 'selectedYear', 'statistics'));
    }
    
    public function updateStatusBatch(Request $request)
    {
        $request->validate([
            'nama_spa' => 'required|string',
            'tanggal_ajuan' => 'required|string',
            'status' => 'required|in:pending,disetujui,buat ulang',
            'divisi_id' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $user = Auth::user();
            $now = Carbon::now();
            $tanggalAjuan = $request->tanggal_ajuan;
            
            Log::info('Received tanggal_ajuan format', [
                'original' => $tanggalAjuan,
                'type' => gettype($tanggalAjuan),
                'requested_status' => $request->status
            ]);

            // Tentukan query untuk mencari ajuan yang akan diupdate
            if (strlen($tanggalAjuan) > 10) {
                $updateQuery = AjuanRutin::where('nama_spa', $request->nama_spa)
                    ->where('divisi_id', $request->divisi_id)
                    ->where('created_at', $tanggalAjuan);
            } else {
                $updateQuery = AjuanRutin::where('nama_spa', $request->nama_spa)
                    ->where('divisi_id', $request->divisi_id)
                    ->whereDate('created_at', $tanggalAjuan);
            }

            // Apply role-based filtering for update operations
            if ($user->role === 'kabag' && $user->divisi_id) {
                $updateQuery->where('divisi_id', $user->divisi_id);
            }

            // Block PJ Divisi dari mengupdate
            if ($user->role === 'pj_divisi') {
                throw new \Exception('Anda tidak memiliki akses untuk mengubah status ajuan');
            }

            $ajuanToUpdate = $updateQuery->first();

            if (!$ajuanToUpdate) {
                throw new \Exception('Ajuan tidak ditemukan atau Anda tidak memiliki akses');
            }

            // PERBAIKAN UTAMA: Implementasi logika satu ajuan disetujui per divisi per bulan
            $revertedPrevious = null;
            if ($request->status === 'disetujui') {
                // Extract month and year from ajuan yang akan disetujui
                $targetDate = Carbon::parse($ajuanToUpdate->created_at);
                $targetMonth = $targetDate->month;
                $targetYear = $targetDate->year;
                
                // Cari ajuan lain yang sudah disetujui dari divisi yang sama di bulan dan tahun yang sama
                $existingApproved = AjuanRutin::where('divisi_id', $request->divisi_id)
                    ->where('status', 'disetujui')
                    ->whereMonth('created_at', $targetMonth)
                    ->whereYear('created_at', $targetYear)
                    ->where(function($query) use ($request, $tanggalAjuan) {
                        // Kecualikan ajuan yang sedang diupdate
                        if (strlen($tanggalAjuan) > 10) {
                            $query->where('nama_spa', '!=', $request->nama_spa)
                                  ->orWhere('created_at', '!=', $tanggalAjuan);
                        } else {
                            $query->where('nama_spa', '!=', $request->nama_spa)
                                  ->orWhereDate('created_at', '!=', $tanggalAjuan);
                        }
                    })
                    ->first();

                if ($existingApproved) {
                    // PERBAIKAN: Ubah ajuan sebelumnya menjadi 'buat ulang', bukan 'pending'
                    AjuanRutin::where('nama_spa', $existingApproved->nama_spa)
                        ->where('divisi_id', $existingApproved->divisi_id)
                        ->whereDate('created_at', $existingApproved->created_at->format('Y-m-d'))
                        ->update([
                            'status' => 'buat ulang', // PERBAIKAN: Ubah ke 'buat ulang' bukan 'pending'
                            'approved_by' => null,
                            'approved_at' => null
                        ]);

                    $revertedPrevious = $existingApproved->nama_spa;
                    
                    Log::info('Previous approved ajuan changed to buat ulang', [
                        'previous_nama_spa' => $existingApproved->nama_spa,
                        'previous_divisi_id' => $existingApproved->divisi_id,
                        'new_nama_spa' => $request->nama_spa,
                        'new_divisi_id' => $request->divisi_id,
                        'month' => $targetMonth,
                        'year' => $targetYear
                    ]);
                }
            }

            // Update status ajuan yang diminta
            if (strlen($tanggalAjuan) > 10) {
                $updateCount = AjuanRutin::where('nama_spa', $request->nama_spa)
                    ->where('divisi_id', $request->divisi_id)
                    ->where('created_at', $tanggalAjuan)
                    ->when($user->role === 'kabag' && $user->divisi_id, function($query) use ($user) {
                        return $query->where('divisi_id', $user->divisi_id);
                    })
                    ->update([
                        'status' => $request->status,
                        'approved_by' => $request->status === 'pending' ? null : $userId,
                        'approved_at' => $request->status === 'pending' ? null : $now
                    ]);
            } else {
                $updateCount = AjuanRutin::where('nama_spa', $request->nama_spa)
                    ->where('divisi_id', $request->divisi_id)
                    ->whereDate('created_at', $tanggalAjuan)
                    ->when($user->role === 'kabag' && $user->divisi_id, function($query) use ($user) {
                        return $query->where('divisi_id', $user->divisi_id);
                    })
                    ->update([
                        'status' => $request->status,
                        'approved_by' => $request->status === 'pending' ? null : $userId,
                        'approved_at' => $request->status === 'pending' ? null : $now
                    ]);
            }

            if ($updateCount === 0) {
                throw new \Exception('Tidak ada data yang diperbarui atau Anda tidak memiliki akses ke data tersebut');
            }

            DB::commit();

            // Menyusun respons setelah pembaruan berhasil
            $userName = Auth::user()->name;
            $message = 'Status berhasil diperbarui menjadi ' . ucfirst($request->status);

            $response = [
                'success' => true,
                'message' => $message,
                'status' => ucfirst($request->status),
                'updated' => $updateCount,
                'approved_by' => $request->status === 'pending' ? null : $userName,
                'approved_at' => $request->status === 'pending' ? null : $now->format('d-m-Y H:i')
            ];

            // Tambahkan informasi revert jika ada
            if ($revertedPrevious) {
                $response['reverted_previous'] = $revertedPrevious;
                $response['reverted_divisi_id'] = $request->divisi_id; // Tambahkan divisi_id untuk membantu pencarian di frontend
                $response['message'] = $message . '. Ajuan sebelumnya "' . $revertedPrevious . '" telah diubah statusnya menjadi "buat ulang".';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating batch status: ' . $e->getMessage(), [
                'nama_spa' => $request->nama_spa,
                'divisi_id' => $request->divisi_id,
                'tanggal_ajuan' => $request->tanggal_ajuan,
                'status' => $request->status,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage()
            ]);
        }
    }
    
    public function getStatusDetail($namaSpa)
    {
        try {
            $user = Auth::user();
            
            // Build the base query
            $query = DB::table('ajuan_rutins')
                ->select(
                    'ajuan_rutins.nama_spa',
                    'divisis.divisi as nama_divisi',
                    'ajuan_rutins.nomor_telp',
                    DB::raw('MAX(ajuan_rutins.created_at) as tanggal_ajuan'),
                    DB::raw('SUM(ajuan_rutins.total) as total_ajuan'),
                    DB::raw('COUNT(ajuan_rutins.id) as jumlah_item'),
                    'ajuan_rutins.status',
                    DB::raw('MAX(ajuan_rutins.approved_at) as approved_at'),
                    'users.name as approved_by_name'
                )
                ->join('divisis', 'ajuan_rutins.divisi_id', '=', 'divisis.id')
                ->leftJoin('users', 'ajuan_rutins.approved_by', '=', 'users.id')
                ->where('ajuan_rutins.nama_spa', $namaSpa);

            // Apply role-based filtering
            if (($user->role === 'kabag' || $user->role === 'pj_divisi') && $user->divisi_id) {
                $query->where('ajuan_rutins.divisi_id', $user->divisi_id);
            }
            
            $ajuan = $query->groupBy('ajuan_rutins.nama_spa', 'divisis.divisi', 'ajuan_rutins.nomor_telp', 'ajuan_rutins.status', 'users.name')
                ->first();
            
            if (!$ajuan) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses'], 404);
            }
            
            $statusClass = $ajuan->status == 'disetujui' ? 'bg-approved' : ($ajuan->status == 'buat ulang' ? 'bg-rejected' : 'bg-pending');
            
            return response()->json([
                'success' => true,
                'status' => ucfirst($ajuan->status),
                'statusClass' => $statusClass,
                'approved_by' => $ajuan->approved_by_name,
                'approved_at' => $ajuan->approved_at ? date('d-m-Y H:i', strtotime($ajuan->approved_at)) : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function exportApproved(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Build query for approved ajuan
            $query = DB::table('ajuan_rutins')
            ->select(
                'ajuan_rutins.nama_spa',
                'divisis.divisi as nama_divisi',
                'ajuan_rutins.barang_ajuan',
                'ajuan_rutins.kategori_barang',
                'ajuan_rutins.banyak_barang',
                'ajuan_rutins.satuan',
                'ajuan_rutins.harga',
                'ajuan_rutins.total',
                'ajuan_rutins.keterangan',
                'ajuan_rutins.nomor_telp',
                'ajuan_rutins.created_at as tanggal_ajuan',
                'ajuan_rutins.approved_at',
                'users.name as approved_by'
            )
            ->join('divisis', 'ajuan_rutins.divisi_id', '=', 'divisis.id')
            ->leftJoin('users', 'ajuan_rutins.approved_by', '=', 'users.id')
            ->where('ajuan_rutins.status', 'disetujui');

            // Apply role-based filtering
            if (($user->role === 'kabag' || $user->role === 'pj_divisi') && $user->divisi_id) {
                $query->where('ajuan_rutins.divisi_id', $user->divisi_id);
            }
            
            // Filter by nama_spa if provided
            if ($request->has('nama_spa')) {
                $query->where('ajuan_rutins.nama_spa', $request->nama_spa);
                
                // Filter by tanggal_ajuan if provided
                if ($request->has('tanggal_ajuan')) {
                    $tanggalAjuan = $request->tanggal_ajuan;
                    $query->whereDate('ajuan_rutins.created_at', $tanggalAjuan);
                }
            }
    
            // Get the data
            $approvedAjuans = $query->get();
            
            // Check if there's any data
            if ($approvedAjuans->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada ajuan yang disetujui untuk diekspor atau Anda tidak memiliki akses'], 404);
            }
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set the spreadsheet title
            $title = $request->has('nama_spa') 
                ? 'Ajuan Rutin: ' . $request->nama_spa
                : 'Daftar Ajuan Rutin Disetujui';
                
            $spreadsheet->getProperties()
                ->setCreator(Auth::user()->name)
                ->setLastModifiedBy(Auth::user()->name)
                ->setTitle($title)
                ->setSubject($title)
                ->setDescription('Daftar Ajuan Rutin yang telah disetujui');
            
            // Setting up the header row style
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
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
            
            // Set header titles
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Nama SPA');
            $sheet->setCellValue('C1', 'Divisi');
            $sheet->setCellValue('D1', 'Nomor Telepon');
            $sheet->setCellValue('E1', 'Barang Ajuan');
            $sheet->setCellValue('F1', 'Kategori');
            $sheet->setCellValue('G1', 'Jumlah');
            $sheet->setCellValue('H1', 'Satuan');
            $sheet->setCellValue('I1', 'Harga Satuan (Rp)');
            $sheet->setCellValue('J1', 'Total (Rp)');
            $sheet->setCellValue('K1', 'Keterangan');
            $sheet->setCellValue('L1', 'Tanggal Ajuan');
            $sheet->setCellValue('M1', 'Disetujui Oleh');
            $sheet->setCellValue('N1', 'Tanggal Disetujui');
            
            // Apply header style
            $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);
            
            // Fill in data rows
            $row = 2;
            $totalSum = 0;
            foreach ($approvedAjuans as $index => $ajuan) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $ajuan->nama_spa);
                $sheet->setCellValue('C' . $row, $ajuan->nama_divisi);
                $sheet->setCellValue('D' . $row, $ajuan->nomor_telp);
                $sheet->setCellValue('E' . $row, $ajuan->barang_ajuan);
                $sheet->setCellValue('F' . $row, $ajuan->kategori_barang);
                $sheet->setCellValue('G' . $row, $ajuan->banyak_barang);
                $sheet->setCellValue('H' . $row, $ajuan->satuan);
                $sheet->setCellValue('I' . $row, $ajuan->harga);
                $sheet->setCellValue('J' . $row, $ajuan->total);
                $sheet->setCellValue('K' . $row, $ajuan->keterangan);
                $sheet->setCellValue('L' . $row, Carbon::parse($ajuan->tanggal_ajuan)->format('d-m-Y'));
                $sheet->setCellValue('M' . $row, $ajuan->approved_by);
                $sheet->setCellValue('N' . $row, Carbon::parse($ajuan->approved_at)->format('d-m-Y H:i'));
                
                $totalSum += $ajuan->total;
                $row++;
            }
            
            // Add total row
            $sheet->setCellValue('I' . $row, 'TOTAL');
            $sheet->setCellValue('J' . $row, $totalSum);
            $sheet->getStyle('I' . $row . ':J' . $row)->getFont()->setBold(true);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Auto-size columns
            foreach (range('A', 'N') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Format the currency columns
            $sheet->getStyle('I2:J' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
            
            // Apply borders to all cells
            $cellRange = 'A1:N' . ($row - 1);
            $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Set different background color for even rows
            for ($i = 2; $i < $row; $i += 2) {
                $sheet->getStyle('A' . $i . ':N' . $i)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F8F8');
            }
            
            // Create filename with date
            $filenamePart = $request->has('nama_spa') 
                ? 'ajuan_' . str_replace(' ', '_', $request->nama_spa)
                : 'ajuan_rutin_disetujui';
                
            $filename = $filenamePart . '_' . date('d-m-Y_H-i-s') . '.xlsx';
            
            // Create Excel file and prepare response
            $writer = new Xlsx($spreadsheet);
            
            // Save to a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);
            
            // Return the file as a download
            return Response::download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error exporting approved ajuan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatistics($selectedMonth, $selectedYear, $user = null)
    {
        // Ambil semua data dulu, lalu hitung di PHP
        $baseQuery = AjuanRutin::with('divisi')
            ->whereRaw('MONTH(created_at) = ?', [$selectedMonth])
            ->whereRaw('YEAR(created_at) = ?', [$selectedYear]);
        
        // Apply role-based filtering
        if ($user && ($user->role === 'kabag' || $user->role === 'pj_divisi') && $user->divisi_id) {
            $baseQuery->where('divisi_id', $user->divisi_id);
        }
        
        $allData = $baseQuery->get();
        
        // Group by submission (nama_spa + tanggal)
        $submissions = $allData->groupBy(function($item) {
            return $item->nama_spa . '_' . $item->created_at->format('Y-m-d');
        })->map(function($items) {
            $first = $items->first();
            return (object)[
                'nama_spa' => $first->nama_spa,
                'submission_date' => $first->created_at->format('Y-m-d'),
                'status' => $first->status,
                'total_amount' => $items->sum('total'),
                'divisi' => $first->divisi->divisi ?? 'Unknown',
                'item_count' => $items->count()
            ];
        });
        
        // Status statistics
        $statusStats = $submissions->groupBy('status')->map(function($items, $status) {
            return (object)[
                'count' => $items->count(),
                'total_amount' => $items->sum('total_amount')
            ];
        });
        
        // Division statistics
        $divisionStats = $submissions->groupBy('divisi')->map(function($items, $divisi) {
            return (object)[
                'nama_divisi' => $divisi,
                'count' => $items->count(),
                'total_amount' => $items->sum('total_amount')
            ];
        })->sortByDesc('total_amount')->values();
        
        // Category statistics (per item, not per submission)
        $categoryStats = $allData->groupBy('kategori_barang')->map(function($items, $category) {
            return (object)[
                'count' => $items->count(),
                'total_amount' => $items->sum('total')
            ];
        });
        
        // Total statistics
        $totalStats = (object)[
            'total_submissions' => $submissions->count(),
            'total_items' => $allData->count(),
            'total_amount' => $allData->sum('total'),
            'avg_item_amount' => $allData->avg('total'),
            'max_item_amount' => $allData->max('total')
        ];
        
        // Top submissions
        $topSubmissions = $submissions->sortByDesc('total_amount')->take(5)->values();
        
        // Daily statistics
        $dailyStats = $submissions->groupBy('submission_date')->map(function($items, $date) {
            return (object)[
                'date' => $date,
                'submissions' => $items->count(),
                'amount' => $items->sum('total_amount')
            ];
        })->sortBy('date')->values();
        
        return [
            'statusStats' => $statusStats,
            'divisionStats' => $divisionStats,
            'categoryStats' => $categoryStats,
            'totalStats' => $totalStats,
            'topSubmissions' => $topSubmissions,
            'dailyStats' => $dailyStats
        ];
    }
    
    public function deleteBatch(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is admin or GA
        if (!($user->role === 'admin' || $user->is_admin === 1 || $user->role === 'ga' || $user->is_ga === 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus ajuan'
            ], 403);
        }
        
        $request->validate([
            'nama_spa' => 'required|string',
            'tanggal_ajuan' => 'required|string', // Ubah dari date ke string
            'divisi_id' => 'required|integer' // Tambahkan validasi divisi_id
        ]);
        
        try {
            DB::beginTransaction();
            
            $tanggalAjuan = $request->tanggal_ajuan;
            
            // Log for debugging
            Log::info('Deleting batch ajuan with precision', [
                'nama_spa' => $request->nama_spa,
                'tanggal_ajuan' => $tanggalAjuan,
                'divisi_id' => $request->divisi_id,
                'user' => $user->name,
                'user_role' => $user->role
            ]);
            
            // PERBAIKAN: Build delete query dengan presisi yang sama
            if (strlen($tanggalAjuan) > 10) {
                // Exact datetime comparison
                $deleteQuery = AjuanRutin::where('nama_spa', $request->nama_spa)
                    ->where('divisi_id', $request->divisi_id)
                    ->where('created_at', $tanggalAjuan);
            } else {
                // Date comparison
                $deleteQuery = AjuanRutin::where('nama_spa', $request->nama_spa)
                    ->where('divisi_id', $request->divisi_id)
                    ->whereDate('created_at', $tanggalAjuan);
            }
            
            $deleted = $deleteQuery->delete();
            
            if ($deleted === 0) {
                throw new \Exception('Tidak ada data yang dihapus atau Anda tidak memiliki akses');
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Ajuan berhasil dihapus',
                'deleted' => $deleted
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting batch ajuan: ' . $e->getMessage(), [
                'nama_spa' => $request->nama_spa,
                'tanggal_ajuan' => $request->tanggal_ajuan,
                'divisi_id' => $request->divisi_id,
                'user' => $user->name,
                'user_role' => $user->role
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ajuan: ' . $e->getMessage()
            ], 500);
        }
    }
}