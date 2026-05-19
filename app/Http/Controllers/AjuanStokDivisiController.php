<?php

namespace App\Http\Controllers;

use App\Models\AjuanStokDivisi;
use App\Models\StokDivisi;
use App\Models\StokPusat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AjuanStokDivisiController extends Controller
{
    public function formAjuan()
    {
        $user = Auth::user();
        
        // Hanya PJ Divisi yang bisa mengakses
        if ($user->role !== 'pj_divisi' || !$user->divisi_id) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        // Ambil barang yang tersedia di stok pusat
        $stokPusats = StokPusat::where('sisa_stok', '>', 0)
                            ->orderBy('kode_barang', 'asc')
                            ->get();
        
        // Ambil riwayat ajuan user ini
        $riwayatAjuan = AjuanStokDivisi::where('pengaju_id', $user->id)
                                      ->with(['stokPusat', 'approvedByGA', 'approvedByKabag', 'rejectedBy'])
                                      ->orderBy('created_at', 'desc')
                                      ->paginate(10);
        
        return view('admin.ajuan.form-ajuan', compact('stokPusats', 'riwayatAjuan'));
    }

    public function submitAjuan(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'pj_divisi' || !$user->divisi_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'stok_pusat_id' => 'required|exists:stok_pusats,id',
            'jumlah_diminta' => 'required|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Cek apakah stok masih tersedia
        $stokPusat = StokPusat::find($request->stok_pusat_id);
        if ($stokPusat->sisa_stok < $request->jumlah_diminta) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah yang diminta melebihi stok yang tersedia di pusat (' . $stokPusat->sisa_stok . ' ' . $stokPusat->satuan . ').'
            ], 422);
        }
        
        // Cek apakah sudah ada ajuan pending untuk barang yang sama dari divisi yang sama
        $existingAjuan = AjuanStokDivisi::where('divisi_id', $user->divisi_id)
                                       ->where('stok_pusat_id', $request->stok_pusat_id)
                                       ->whereIn('status', ['pending', 'approved_ga'])
                                       ->exists();
        
        if ($existingAjuan) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada ajuan pending untuk barang ini dari divisi Anda.'
            ], 422);
        }
        
        try {
            // Buat ajuan baru
            $ajuan = AjuanStokDivisi::create([
                'divisi_id' => $user->divisi_id,
                'stok_pusat_id' => $request->stok_pusat_id,
                'pengaju_id' => $user->id,
                'jumlah_diminta' => $request->jumlah_diminta,
                'status' => 'pending'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ajuan berhasil disubmit dengan nomor: ' . $ajuan->nomor_ajuan,
                'ajuan' => $ajuan->load(['stokPusat'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error in submitAjuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan ajuan.'
            ], 500);
        }
    }

    public function approvalPage()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['ga', 'kabag'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $ajuansPending = collect();
        $riwayatAjuan = collect();
        
        if ($user->role === 'ga') {
            // GA melihat ajuan yang sudah di-approve Kabag
            $ajuansPending = AjuanStokDivisi::where('status', 'checked_kabag')
                                           ->with(['divisi', 'stokPusat', 'pengaju', 'approvedByKabag'])
                                           ->orderBy('created_at', 'asc')
                                           ->get();
            
            // Riwayat ajuan yang sudah diproses GA (diterima final/ditolak)
            $riwayatAjuan = AjuanStokDivisi::whereIn('status', ['completed', 'rejected'])
                                          ->with(['divisi', 'stokPusat', 'pengaju', 'approvedByKabag'])
                                          ->orderBy('created_at', 'desc')
                                          ->paginate(10);
                                          
        } elseif ($user->role === 'kabag') {
            // Kabag melihat ajuan pending untuk divisinya dari PJ Divisi
            $ajuansPending = AjuanStokDivisi::where('status', 'pending')
                                           ->where('divisi_id', $user->divisi_id)
                                           ->with(['divisi', 'stokPusat', 'pengaju'])
                                           ->orderBy('created_at', 'asc')
                                           ->get();
                                           
            // Riwayat ajuan divisinya
            $riwayatAjuan = AjuanStokDivisi::where('divisi_id', $user->divisi_id)
                                          ->whereIn('status', ['checked_kabag', 'completed', 'rejected'])
                                          ->with(['divisi', 'stokPusat', 'pengaju'])
                                          ->orderBy('created_at', 'desc')
                                          ->paginate(10);
        }
        
        return view('admin.ajuan.approval-page', compact('ajuansPending', 'riwayatAjuan'));
    }

    public function prosesApprovalSemua(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['ga', 'kabag'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            DB::beginTransaction();
            $count = 0;
            
            if ($user->role === 'kabag') {
                $ajuans = AjuanStokDivisi::where('status', 'pending')
                    ->where('divisi_id', $user->divisi_id)
                    ->get();
                    
                foreach ($ajuans as $ajuan) {
                    $ajuan->update([
                        'status' => 'checked_kabag',
                        'approved_by_kabag' => $user->id,
                        'approved_at_kabag' => now(),
                    ]);
                    $count++;
                }
            } elseif ($user->role === 'ga') {
                $ajuans = AjuanStokDivisi::where('status', 'checked_kabag')
                    ->with('stokPusat')
                    ->get();
                    
                foreach ($ajuans as $ajuan) {
                    if ($ajuan->stokPusat->sisa_stok < $ajuan->jumlah_diminta) {
                        continue; // Skip if stock is not enough
                    }
                    
                    $ajuan->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'jumlah_diberikan' => $ajuan->jumlah_diminta,
                        'approved_by_ga' => $user->id,
                        'approved_at_ga' => now(),
                    ]);
                    
                    $ajuan->stokPusat->decrement('sisa_stok', $ajuan->jumlah_diminta);
                    
                    $stokDivisi = StokDivisi::where('divisi_id', $ajuan->divisi_id)
                        ->where('stok_pusat_id', $ajuan->stok_pusat_id)
                        ->first();
                    
                    if ($stokDivisi) {
                        $stokDivisi->increment('sisa_stok', $ajuan->jumlah_diminta);
                    } else {
                        StokDivisi::create([
                            'divisi_id' => $ajuan->divisi_id,
                            'stok_pusat_id' => $ajuan->stok_pusat_id,
                            'sisa_stok' => $ajuan->jumlah_diminta,
                            'stok_ideal' => 0
                        ]);
                    }
                    
                    // Reset progress cek bulanan divisi ke 0% karena ada barang baru masuk
                    StokDivisi::where('divisi_id', $ajuan->divisi_id)->update([
                        'status_cek_bulanan' => null,
                        'stok_fisik_cek' => null,
                        'tgl_cek_bulanan' => null,
                        'dicek_oleh' => null,
                        'keterangan_cek' => null
                    ]);
                    $count++;
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil menyetujui $count ajuan sekaligus."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in prosesApprovalSemua: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses.'
            ], 500);
        }
    }

    public function prosesApproval(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['ga', 'kabag'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'ajuan_id' => 'required|exists:ajuan_stok_divisis,id',
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $ajuan = AjuanStokDivisi::find($request->ajuan_id);
            $action = $request->action;
            $message = '';
            
            if ($action === 'reject') {
                $keteranganField = $user->role === 'kabag' ? 'keterangan_kabag' : 'keterangan_ga';
                
                $ajuan->update([
                    'status' => 'rejected',
                    'rejected_by' => $user->id,
                    'rejected_at' => now(),
                    'alasan_reject' => $request->keterangan,
                    $keteranganField => $request->keterangan
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ajuan berhasil ditolak.',
                    'data' => $ajuan->load(['stokPusat', 'divisi', 'pengaju'])
                ]);
            }
            
            if ($user->role === 'kabag') {
                if ($ajuan->divisi_id !== $user->divisi_id) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak dapat memproses ajuan divisi lain'], 403);
                }
                
                if ($ajuan->status === 'pending') {
                    $ajuan->update([
                        'status' => 'checked_kabag',
                        'approved_by_kabag' => $user->id,
                        'approved_at_kabag' => now(),
                        'keterangan_kabag' => $request->keterangan,
                    ]);
                    $message = 'Ajuan berhasil diverifikasi Kabag. Menunggu Persetujuan GA.';
                } else {
                    return response()->json(['success' => false, 'message' => 'Status ajuan tidak valid untuk diproses Kabag'], 422);
                }
            } elseif ($user->role === 'ga') {
                if ($ajuan->status === 'checked_kabag') {
                    if ($ajuan->stokPusat->sisa_stok < $ajuan->jumlah_diminta) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok pusat tidak mencukupi. Tersedia: ' . $ajuan->stokPusat->sisa_stok . ' ' . $ajuan->stokPusat->satuan
                        ], 422);
                    }
                    
                    DB::transaction(function () use ($ajuan, $user, $request) {
                        $ajuan->update([
                            'status' => 'completed',
                            'approved_by_ga' => $user->id,
                            'approved_at_ga' => now(),
                            'keterangan_ga' => $request->keterangan,
                            'completed_at' => now(),
                            'jumlah_diberikan' => $ajuan->jumlah_diminta
                        ]);
                        
                        $ajuan->stokPusat->decrement('sisa_stok', $ajuan->jumlah_diminta);
                        
                        $stokDivisi = StokDivisi::where('divisi_id', $ajuan->divisi_id)
                                               ->where('stok_pusat_id', $ajuan->stok_pusat_id)
                                               ->first();
                        
                        if ($stokDivisi) {
                            $stokDivisi->increment('sisa_stok', $ajuan->jumlah_diminta);
                        } else {
                            StokDivisi::create([
                                'divisi_id' => $ajuan->divisi_id,
                                'stok_pusat_id' => $ajuan->stok_pusat_id,
                                'sisa_stok' => $ajuan->jumlah_diminta,
                                'stok_ideal' => 0
                            ]);
                        }

                        // Reset progress cek bulanan divisi ke 0% karena ada barang baru masuk
                        StokDivisi::where('divisi_id', $ajuan->divisi_id)->update([
                            'status_cek_bulanan' => null,
                            'stok_fisik_cek' => null,
                            'tgl_cek_bulanan' => null,
                            'dicek_oleh' => null,
                            'keterangan_cek' => null
                        ]);
                    });
                    
                    $message = 'Ajuan disetujui. Stok sebanyak ' . $ajuan->jumlah_diminta . ' ' . $ajuan->stokPusat->satuan . ' telah ditransfer ke divisi.';
                } else {
                    return response()->json(['success' => false, 'message' => 'Status ajuan tidak valid untuk diproses GA'], 422);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $ajuan->load(['stokPusat', 'divisi', 'pengaju'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in prosesApproval: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses approval.'
            ], 500);
        }
    }

    public function getDetailAjuan($id)
    {
        $user = Auth::user();
        
        try {
            $ajuan = AjuanStokDivisi::with([
                'divisi', 
                'stokPusat', 
                'pengaju', 
                'approvedByGA', 
                'approvedByKabag',
                'processedByAdmin',
                'reapprovedByKabag',
                'rejectedBy'
            ])->findOrFail($id);
            
            $hasAccess = in_array($user->role, ['admin', 'aset', 'ga']) ||
                         ($user->role === 'kabag' && $ajuan->divisi_id === $user->divisi_id) ||
                         ($user->role === 'pj_divisi' && $ajuan->pengaju_id === $user->id);
            
            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $timeline = $this->getAjuanTimeline($ajuan);
            
            return response()->json([
                'success' => true,
                'ajuan' => $ajuan,
                'timeline' => $timeline
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDetailAjuan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil detail ajuan'], 500);
        }
    }

    private function getAjuanTimeline($ajuan)
    {
        $timeline = [];
        
        $timeline[] = [
            'title' => 'Pengajuan Dibuat',
            'description' => 'Ajuan stok dibuat oleh ' . ($ajuan->pengaju->name ?? '-'),
            'date' => $ajuan->created_at,
            'status' => 'completed',
            'icon' => 'bi-file-plus'
        ];
        
        if ($ajuan->approved_at_kabag) {
            $timeline[] = [
                'title' => 'Disetujui Kabag',
                'description' => 'Disetujui oleh ' . ($ajuan->approvedByKabag->name ?? 'Kabag'),
                'date' => $ajuan->approved_at_kabag,
                'status' => 'completed',
                'icon' => 'bi-check-circle'
            ];
        } elseif ($ajuan->status === 'rejected' && $ajuan->rejected_by && !$ajuan->approved_at_kabag) {
            $timeline[] = [
                'title' => 'Ditolak oleh Kabag',
                'description' => 'Ajuan ditolak oleh Kabag',
                'date' => $ajuan->rejected_at,
                'status' => 'rejected',
                'icon' => 'bi-x-circle'
            ];
            return $timeline;
        } else {
            $timeline[] = [
                'title' => 'Menunggu Persetujuan Kabag',
                'description' => 'Menunggu persetujuan Kabag',
                'date' => null,
                'status' => 'pending',
                'icon' => 'bi-clock'
            ];
            return $timeline;
        }

        if ($ajuan->status === 'rejected') return $timeline;

        if ($ajuan->completed_at) {
            $timeline[] = [
                'title' => 'Disetujui GA & Selesai',
                'description' => 'Disetujui GA, stok telah ditransfer.',
                'date' => $ajuan->completed_at,
                'status' => 'completed',
                'icon' => 'bi-check-all'
            ];
        } elseif ($ajuan->status === 'rejected' && !$ajuan->approved_at_ga) {
            $timeline[] = [
                'title' => 'Ditolak oleh GA',
                'description' => 'Ajuan ditolak oleh GA',
                'date' => $ajuan->rejected_at,
                'status' => 'rejected',
                'icon' => 'bi-x-circle'
            ];
            return $timeline;
        } else {
             $timeline[] = [
                'title' => 'Menunggu Persetujuan GA',
                'description' => 'Menunggu persetujuan dan eksekusi stok oleh GA',
                'date' => null,
                'status' => 'pending',
                'icon' => 'bi-clock'
            ];
        }

        return $timeline;
    }

    public function daftarAjuan(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'kabag', 'ga'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $query = AjuanStokDivisi::with(['divisi', 'stokPusat', 'pengaju', 'approvedByGA', 'approvedByKabag']);
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('divisi_id') && $request->divisi_id != '') {
            $query->where('divisi_id', $request->divisi_id);
        }
        
        $ajuans = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $divisis = \App\Models\Divisi::where('divisi', '!=', 'Asset & GA')->get();
        
        return view('admin.ajuan.daftar-ajuan', compact('ajuans', 'divisis'));
    }

    public function cancelAjuan(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'pj_divisi') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        try {
            $ajuan = AjuanStokDivisi::findOrFail($request->ajuan_id);
            
            // Hanya pengaju yang bisa cancel ajuannya sendiri
            if ($ajuan->pengaju_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat membatalkan ajuan orang lain'
                ], 403);
            }
            
            // Hanya bisa cancel jika masih pending
            if ($ajuan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ajuan yang sudah diproses tidak dapat dibatalkan'
                ], 422);
            }
            
            $ajuan->update([
                'status' => 'rejected',
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'alasan_reject' => 'Dibatalkan oleh pengaju'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ajuan berhasil dibatalkan',
                'data' => $ajuan
            ]);
        } catch (\Exception $e) {
            Log::error('Error in cancelAjuan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan ajuan.'
            ], 500);
        }
    }

    public function cekBulanan(Request $request)
    {
        $user = Auth::user();
        
        // Hanya admin, GA, dan aset yang bisa mengakses
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $query = StokDivisi::with(['divisi', 'stokPusat']);
        
        // Filter berdasarkan divisi jika ada
        if ($request->has('divisi_id') && $request->divisi_id != '') {
            $query->where('divisi_id', $request->divisi_id);
        }
        
        // Filter berdasarkan status cek jika ada
        if ($request->has('status_cek') && $request->status_cek != '') {
            switch ($request->status_cek) {
                case 'belum_dicek':
                    $query->whereNull('status_cek_bulanan');
                    break;
                case 'sesuai':
                    $query->where('status_cek_bulanan', 'sesuai');
                    break;
                case 'tidak_sesuai':
                    $query->where('status_cek_bulanan', 'tidak_sesuai');
                    break;
            }
        }
        
        // Filter berdasarkan periode bulan/tahun
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        if ($bulan && $tahun) {
            // Untuk item yang sudah dicek, filter berdasarkan tanggal cek
            // Untuk yang belum dicek, tampilkan semua
            $query->where(function($q) use ($bulan, $tahun) {
                $q->whereNull('tgl_cek_bulanan')
                  ->orWhere(function($subQ) use ($bulan, $tahun) {
                      $subQ->whereYear('tgl_cek_bulanan', $tahun)
                           ->whereMonth('tgl_cek_bulanan', $bulan);
                  });
            });
        }
        
        // Hanya tampilkan divisi yang memiliki stok (kecuali Asset & GA)
        $query->whereHas('divisi', function($q) {
            $q->where('divisi', '!=', 'Asset & GA');
        });
        
        // Hanya tampilkan yang ada stoknya
        $query->where('sisa_stok', '>', 0);
        
        $stokDivisis = $query->orderBy('divisi_id', 'asc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(25);
        
        // Hitung statistik untuk periode yang dipilih
        $statsQuery = StokDivisi::whereHas('divisi', function($q) {
            $q->where('divisi', '!=', 'Asset & GA');
        })->where('sisa_stok', '>', 0);
        
        // Apply same filters for stats
        if ($request->has('divisi_id') && $request->divisi_id != '') {
            $statsQuery->where('divisi_id', $request->divisi_id);
        }
        
        if ($bulan && $tahun) {
            $statsQuery->where(function($q) use ($bulan, $tahun) {
                $q->whereNull('tgl_cek_bulanan')
                  ->orWhere(function($subQ) use ($bulan, $tahun) {
                      $subQ->whereYear('tgl_cek_bulanan', $tahun)
                           ->whereMonth('tgl_cek_bulanan', $bulan);
                  });
            });
        }
        
        $allItems = $statsQuery->get();
        
        // Hitung statistik
        $total_items = $allItems->count();
        $belum_dicek = $allItems->whereNull('status_cek_bulanan')->count();
        $stokDivisi_sesuai = $allItems->where('status_cek_bulanan', 'sesuai')->count();
        $stokDivisi_tidak_sesuai = $allItems->where('status_cek_bulanan', 'tidak_sesuai')->count();
        $sudah_dicek = $stokDivisi_sesuai + $stokDivisi_tidak_sesuai;
        
        // Hitung persentase progress
        $progress_percentage = $total_items > 0 ? round(($sudah_dicek / $total_items) * 100) : 0;
        
        // Untuk dropdown filter divisi
        $divisis = \App\Models\Divisi::where('divisi', '!=', 'Asset & GA')->get();
    
        return view('admin.ajuan.cek-bulanan', compact(
            'stokDivisis', 
            'divisis',
            'total_items',
            'belum_dicek',
            'stokDivisi_sesuai',
            'stokDivisi_tidak_sesuai',
            'sudah_dicek',
            'progress_percentage'
        ));
    }

    public function updateCekBulanan(Request $request)
    {

        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
    
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:stok_divisis,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $stokDivisi = StokDivisi::with(['divisi', 'stokPusat'])->findOrFail($request->id);
            
            $stokFisik = (int) $request->stok_fisik;
            $stokSistem = (int) $stokDivisi->sisa_stok;
            
            // Auto-determine status berdasarkan perbandingan
            $status = ($stokFisik === $stokSistem) ? 'sesuai' : 'tidak_sesuai';
            
            // PERBAIKAN: Pastikan semua field ter-update dengan benar
            $updateData = [
                'stok_fisik_cek' => $stokFisik,
                'status_cek_bulanan' => $status,
                'tgl_cek_bulanan' => now(),
                'dicek_oleh' => $user->name, // Gunakan nama user, bukan ID
            ];
            
            // Tambahkan keterangan jika ada
            if ($request->has('keterangan') && !empty($request->keterangan)) {
                $updateData['keterangan_cek'] = $request->keterangan;
            }
            
            // Update database
            $stokDivisi->update($updateData);
            
            // Refresh model untuk mendapatkan data terbaru
            $stokDivisi->refresh();
            
            // Siapkan response data yang konsisten
            $responseData = [
                'id' => $stokDivisi->id,
                'stok_fisik_cek' => $stokDivisi->stok_fisik_cek,
                'status_cek_bulanan' => $stokDivisi->status_cek_bulanan,
                'status_cek_label' => $stokDivisi->status_cek_label,
                'status_cek_badge_class' => $stokDivisi->status_cek_badge_class,
                'selisih' => $stokDivisi->selisih,
                'tgl_cek_bulanan' => $stokDivisi->tgl_cek_bulanan->format('d/m/Y H:i'),
                'dicek_oleh' => $stokDivisi->dicek_oleh,
                'keterangan_cek' => $stokDivisi->keterangan_cek,
                'stok_sistem' => $stokDivisi->sisa_stok
            ];
            
            return response()->json([
                'success' => true,
                'message' => "Data berhasil dicek. Status: {$stokDivisi->status_cek_label}",
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in updateCekBulanan: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate status cek: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batchUpdateCekBulanan(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        // Parse JSON data
        $batchData = json_decode($request->input('batch_data'), true);
        
        if (!$batchData || !is_array($batchData)) {
            return response()->json([
                'success' => false,
                'message' => 'Data batch tidak valid'
            ], 422);
        }
        
        $validator = Validator::make(['batch_data' => $batchData], [
            'batch_data' => 'required|array',
            'batch_data.*.id' => 'required|exists:stok_divisis,id',
            'batch_data.*.stok_fisik' => 'required|integer|min:0',
            'batch_data.*.keterangan' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $updatedData = [];
        $errorCount = 0;
        $successCount = 0;
        
        try {
            DB::transaction(function () use ($batchData, $user, &$updatedData, &$errorCount, &$successCount) {
                foreach ($batchData as $data) {
                    try {
                        $stokDivisi = StokDivisi::with(['divisi', 'stokPusat'])->findOrFail($data['id']);
                        
                        $stokFisik = (int) $data['stok_fisik'];
                        $stokSistem = (int) $stokDivisi->sisa_stok;
                        
                        // Auto-determine status berdasarkan perbandingan
                        $status = ($stokFisik === $stokSistem) ? 'sesuai' : 'tidak_sesuai';
                        
                        Log::info("Batch update item", [
                            'id' => $data['id'],
                            'stok_fisik' => $stokFisik,
                            'stok_sistem' => $stokSistem,
                            'status' => $status,
                            'user' => $user->name
                        ]);
                        
                        $updateData = [
                            'stok_fisik_cek' => $stokFisik,
                            'status_cek_bulanan' => $status,
                            'tgl_cek_bulanan' => now(),
                            'dicek_oleh' => $user->name,
                        ];
                        
                        if (isset($data['keterangan']) && !empty($data['keterangan'])) {
                            $updateData['keterangan_cek'] = $data['keterangan'];
                        }
                        
                        $stokDivisi->update($updateData);
                        $stokDivisi->refresh();
                        
                        // Siapkan data response
                        $responseData = [
                            'id' => $stokDivisi->id,
                            'stok_fisik_cek' => $stokDivisi->stok_fisik_cek,
                            'status_cek_bulanan' => $stokDivisi->status_cek_bulanan,
                            'selisih' => $stokDivisi->stok_fisik_cek - $stokDivisi->sisa_stok,
                            'tgl_cek_bulanan' => $stokDivisi->tgl_cek_bulanan->format('d/m/Y H:i'),
                            'dicek_oleh' => $stokDivisi->dicek_oleh,
                            'status_cek_label' => $stokDivisi->status_cek_label,
                            'status_cek_badge_class' => $stokDivisi->status_cek_badge_class,
                            'stok_sistem' => $stokDivisi->sisa_stok,
                            'keterangan_cek' => $stokDivisi->keterangan_cek
                        ];
                        
                        $updatedData[] = $responseData;
                        $successCount++;
                        
                        // Log untuk monitoring
                        if ($status === 'tidak_sesuai') {
                            Log::info("Batch check - Perbedaan stok ditemukan", [
                                'divisi' => $stokDivisi->divisi->divisi,
                                'barang' => $stokDivisi->stokPusat->nama_barang,
                                'kode_barang' => $stokDivisi->stokPusat->kode_barang,
                                'stok_sistem' => $stokDivisi->sisa_stok,
                                'stok_fisik' => $stokDivisi->stok_fisik_cek,
                                'selisih' => $stokDivisi->stok_fisik_cek - $stokDivisi->sisa_stok,
                                'dicek_oleh' => $user->name,
                                'status' => $status
                            ]);
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error("Error updating item {$data['id']}: " . $e->getMessage());
                    }
                }
            });
            
            $message = "Berhasil memproses {$successCount} item";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} item gagal diproses";
            }
            
            // Hitung statistik hasil batch
            $sesuai = collect($updatedData)->where('status_cek_bulanan', 'sesuai')->count();
            $tidakSesuai = collect($updatedData)->where('status_cek_bulanan', 'tidak_sesuai')->count();
            
            if ($sesuai > 0 && $tidakSesuai > 0) {
                $message .= " ({$sesuai} sesuai, {$tidakSesuai} tidak sesuai)";
            } elseif ($sesuai > 0) {
                $message .= " (semua sesuai)";
            } elseif ($tidakSesuai > 0) {
                $message .= " (semua tidak sesuai)";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $updatedData,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'statistics' => [
                    'sesuai' => $sesuai,
                    'tidak_sesuai' => $tidakSesuai,
                    'total_processed' => $successCount
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in batchUpdateCekBulanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function laporanCekBulanan(Request $request)
    {
        $user = Auth::user();
    
        if (!in_array($user->role, ['admin', 'ga'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $divisi_id = $request->get('divisi_id');
    
        // Query untuk laporan
        $query = StokDivisi::with(['divisi', 'stokPusat'])
                          ->whereHas('divisi', function($q) {
                              $q->where('divisi', '!=', 'Asset & GA');
                          })
                          ->where('sisa_stok', '>', 0);

        // Filter divisi jika ada
        if ($divisi_id) {
            $query->where('divisi_id', $divisi_id);
        }
    
        // Filter berdasarkan bulan dan tahun
        if ($bulan && $tahun) {
            $query->where(function($q) use ($bulan, $tahun) {
                $q->whereNull('tgl_cek_bulanan')
                  ->orWhere(function($subQ) use ($bulan, $tahun) {
                      $subQ->whereYear('tgl_cek_bulanan', $tahun)
                           ->whereMonth('tgl_cek_bulanan', $bulan);
                  });
            });
        }
    
        $stokDivisis = $query->orderBy('divisi_id', 'asc')
                            ->orderBy('created_at', 'desc')
                            ->get();
    
        // Summary data
        $summary = [
            'total_item' => $stokDivisis->count(),
            'sudah_dicek' => $stokDivisis->whereNotNull('status_cek_bulanan')->count(),
            'belum_dicek' => $stokDivisis->whereNull('status_cek_bulanan')->count(),
            'sesuai' => $stokDivisis->where('status_cek_bulanan', 'sesuai')->count(),
            'tidak_sesuai' => $stokDivisis->where('status_cek_bulanan', 'tidak_sesuai')->count(),
        ];

        // Progress percentage
        $summary['progress_percentage'] = $summary['total_item'] > 0 ? 
            round(($summary['sudah_dicek'] / $summary['total_item']) * 100) : 0;
    
        // Group by divisi untuk laporan
        $stokByDivisi = $stokDivisis->groupBy('divisi.divisi');

        // Data untuk dropdown
        $divisis = \App\Models\Divisi::where('divisi', '!=', 'Asset & GA')->get();
    
        return view('admin.laporan.cek-bulanan', compact(
            'stokByDivisi', 
            'summary', 
            'bulan', 
            'tahun', 
            'divisis',
            'divisi_id'
        ));
    }

    public function exportCekBulanan(Request $request)
    {
        $user = Auth::user();
    
        if (!in_array($user->role, ['admin', 'ga'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $divisi_id = $request->get('divisi_id');
        $status_cek = $request->get('status_cek');
        
        try {
            $query = StokDivisi::with(['divisi', 'stokPusat'])
                              ->whereHas('divisi', function($q) {
                                  $q->where('divisi', '!=', 'Asset & GA');
                              })
                              ->where('sisa_stok', '>', 0);

            // Apply filters
            if ($divisi_id) {
                $query->where('divisi_id', $divisi_id);
            }

            if ($status_cek) {
                switch ($status_cek) {
                    case 'belum_dicek':
                        $query->whereNull('status_cek_bulanan');
                        break;
                    case 'sudah_dicek':
                        $query->whereNotNull('status_cek_bulanan');
                        break;
                    case 'tidak_sesuai':
                        $query->where('status_cek_bulanan', 'tidak_sesuai');
                        break;
                    case 'sesuai':
                        $query->where('status_cek_bulanan', 'sesuai');
                        break;
                }
            }

            // Filter berdasarkan periode
            if ($bulan && $tahun) {
                $query->where(function($q) use ($bulan, $tahun) {
                    $q->whereNull('tgl_cek_bulanan')
                      ->orWhere(function($subQ) use ($bulan, $tahun) {
                          $subQ->whereYear('tgl_cek_bulanan', $tahun)
                               ->whereMonth('tgl_cek_bulanan', $bulan);
                      });
                });
            }
    
            $stokDivisis = $query->orderBy('divisi_id', 'asc')
                                ->orderBy('created_at', 'desc')
                                ->get();
    
            // Generate filename
            $bulanName = \Carbon\Carbon::createFromFormat('m', $bulan)->format('F');
            $filename = "cek-bulanan-{$bulanName}-{$tahun}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
    
            $callback = function() use ($stokDivisis) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header CSV
                fputcsv($file, [
                    'Divisi',
                    'Kode Barang', 
                    'Nama Barang',
                    'Satuan',
                    'Stok Ideal',
                    'Stok Sistem',
                    'Stok Fisik',
                    'Selisih',
                    'Status Cek',
                    'Tanggal Cek',
                    'Dicek Oleh',
                    'Keterangan'
                ]);
    
                // Data
                foreach ($stokDivisis as $stok) {
                    $stokFisik = $stok->stok_fisik_cek ?? $stok->sisa_stok;
                    $selisih = $stokFisik - $stok->sisa_stok;
                    
                    fputcsv($file, [
                        $stok->divisi->divisi,
                        $stok->stokPusat->kode_barang,
                        $stok->stokPusat->nama_barang,
                        $stok->stokPusat->satuan,
                        $stok->stok_ideal ?? 0,
                        $stok->sisa_stok,
                        $stokFisik,
                        ($selisih > 0 ? '+' : '') . $selisih,
                        $stok->status_cek_bulanan ? ucfirst($stok->status_cek_bulanan) : 'Belum Dicek',
                        $stok->tgl_cek_bulanan ? $stok->tgl_cek_bulanan->format('d/m/Y H:i') : '-',
                        $stok->dicek_oleh ?? '-',
                        $stok->keterangan_cek ?? '-'
                    ]);
                }
    
                fclose($file);
            };
    
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Error in exportCekBulanan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengexport data: ' . $e->getMessage());
        }
    }

    public function resetCekBulanan(Request $request)
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
    
        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
            'divisi_id' => 'nullable|exists:divisis,id'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $divisi_id = $request->divisi_id;
    
            $query = StokDivisi::whereNotNull('tgl_cek_bulanan')
                             ->where(function($q) use ($bulan, $tahun) {
                                 $q->whereYear('tgl_cek_bulanan', $tahun)
                                   ->whereMonth('tgl_cek_bulanan', $bulan);
                             });

            if ($divisi_id) {
                $query->where('divisi_id', $divisi_id);
            }

            $affected = $query->update([
                'stok_fisik_cek' => null,
                'status_cek_bulanan' => null,
                'tgl_cek_bulanan' => null,
                'dicek_oleh' => null,
                'keterangan_cek' => null
            ]);

            $divisiText = $divisi_id ? 
                ' untuk divisi ' . \App\Models\Divisi::find($divisi_id)->divisi : 
                ' untuk semua divisi';
    
            return response()->json([
                'success' => true,
                'message' => "Berhasil reset {$affected} data cek bulanan{$divisiText} untuk periode {$bulan}/{$tahun}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in resetCekBulanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mereset data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPendingData(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['ga', 'kabag'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        try {
            if ($user->role === 'ga') {
                $ajuans = AjuanStokDivisi::where('status', 'approved_kabag')
                                        ->with(['divisi', 'stokPusat', 'pengaju', 'approvedByKabag'])
                                        ->orderBy('created_at', 'asc')
                                        ->get();
            } else {
                $ajuans = AjuanStokDivisi::where('status', 'pending')
                                        ->where('divisi_id', $user->divisi_id)
                                        ->with(['divisi', 'stokPusat', 'pengaju'])
                                        ->orderBy('created_at', 'asc')
                                        ->get();
            }
            
            // Add approved_by_kabag_name for GA users
            if ($user->role === 'ga') {
                $ajuans->transform(function($ajuan) {
                    $ajuan->approved_by_kabag_name = $ajuan->approvedByKabag ? $ajuan->approvedByKabag->name : null;
                    return $ajuan;
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => $ajuans,
                'count' => $ajuans->count(),
                'pendingCount' => $ajuans->count(),
                'hasUpdates' => true,
                'timestamp' => now()->timestamp,
                'newItems' => 0
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pending data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pending'
            ], 500);
        }
    }

    public function getHistoryData(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['ga', 'kabag'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        try {
            if ($user->role === 'ga') {
                $ajuans = AjuanStokDivisi::whereIn('status', ['completed', 'rejected'])
                                        ->where(function($query) use ($user) {
                                            $query->where('approved_by_ga', $user->id)
                                                  ->orWhere('rejected_by', $user->id);
                                        })
                                        ->with(['divisi', 'stokPusat', 'pengaju'])
                                        ->orderBy('created_at', 'desc')
                                        ->limit(20)
                                        ->get();
            } else {
                $ajuans = AjuanStokDivisi::where('divisi_id', $user->divisi_id)
                                        ->whereIn('status', ['approved_kabag', 'completed', 'rejected'])
                                        ->where(function($query) use ($user) {
                                            $query->where('approved_by_kabag', $user->id)
                                                  ->orWhere('rejected_by', $user->id);
                                        })
                                        ->with(['divisi', 'stokPusat', 'pengaju'])
                                        ->orderBy('created_at', 'desc')
                                        ->limit(20)
                                        ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $ajuans
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching history data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data riwayat'
            ], 500);
        }
    }

    public function getStokInfo(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['ga', 'kabag'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'ajuan_id' => 'required|exists:ajuan_stok_divisis,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $ajuan = AjuanStokDivisi::with('stokPusat')->findOrFail($request->ajuan_id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'sisa_stok' => $ajuan->stokPusat->sisa_stok,
                    'nama_barang' => $ajuan->stokPusat->nama_barang,
                    'kode_barang' => $ajuan->stokPusat->kode_barang,
                    'satuan' => $ajuan->stokPusat->satuan
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching stock info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi stok'
            ], 500);
        }
    }

    public function getRealTimeUpdates(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'kabag', 'aset'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        try {
            $lastUpdate = $request->get('last_update', now()->subMinutes(5)->timestamp);
            $lastUpdateDate = \Carbon\Carbon::createFromTimestamp($lastUpdate);

            $updates = [];

            // Cek update pada cek bulanan
            if (in_array($user->role, ['admin', 'ga', 'aset'])) {
                $stokUpdates = StokDivisi::where('updated_at', '>', $lastUpdateDate)
                                        ->whereNotNull('status_cek_bulanan')
                                        ->with(['divisi', 'stokPusat'])
                                        ->get();

                if ($stokUpdates->count() > 0) {
                    $updates['cek_bulanan'] = $stokUpdates->map(function($stok) {
                        return [
                            'id' => $stok->id,
                            'divisi' => $stok->divisi->divisi,
                            'nama_barang' => $stok->stokPusat->nama_barang,
                            'status_cek_bulanan' => $stok->status_cek_bulanan,
                            'stok_fisik_cek' => $stok->stok_fisik_cek,
                            'tgl_cek_bulanan' => $stok->tgl_cek_bulanan,
                            'dicek_oleh' => $stok->dicek_oleh
                        ];
                    });
                }
            }

            // Cek update pada ajuan
            if (in_array($user->role, ['ga', 'kabag'])) {
                $ajuanQuery = AjuanStokDivisi::where('updated_at', '>', $lastUpdateDate);
                
                if ($user->role === 'kabag') {
                    $ajuanQuery->where('divisi_id', $user->divisi_id);
                }
                
                $ajuanUpdates = $ajuanQuery->with(['divisi', 'stokPusat', 'pengaju'])->get();

                if ($ajuanUpdates->count() > 0) {
                    $updates['ajuan'] = $ajuanUpdates->map(function($ajuan) {
                        return [
                            'id' => $ajuan->id,
                            'nomor_ajuan' => $ajuan->nomor_ajuan,
                            'status' => $ajuan->status,
                            'divisi' => $ajuan->divisi->divisi,
                            'nama_barang' => $ajuan->stokPusat->nama_barang,
                            'pengaju' => $ajuan->pengaju->name,
                            'updated_at' => $ajuan->updated_at
                        ];
                    });
                }
            }

            return response()->json([
                'success' => true,
                'has_updates' => count($updates) > 0,
                'updates' => $updates,
                'timestamp' => now()->timestamp
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting real-time updates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil update real-time'
            ], 500);
        }
    }

    public function getDashboardStatsCekBulanan()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        try {
            $currentMonth = date('m');
            $currentYear = date('Y');

            $query = StokDivisi::whereHas('divisi', function($q) {
                $q->where('divisi', '!=', 'Asset & GA');
            })->where('sisa_stok', '>', 0);

            // Filter untuk bulan ini
            $monthlyQuery = clone $query;
            $monthlyQuery->where(function($q) use ($currentMonth, $currentYear) {
                $q->whereNull('tgl_cek_bulanan')
                  ->orWhere(function($subQ) use ($currentMonth, $currentYear) {
                      $subQ->whereYear('tgl_cek_bulanan', $currentYear)
                           ->whereMonth('tgl_cek_bulanan', $currentMonth);
                  });
            });

            $monthlyData = $monthlyQuery->get();

            $stats = [
                'total_items' => $monthlyData->count(),
                'checked_items' => $monthlyData->whereNotNull('status_cek_bulanan')->count(),
                'unchecked_items' => $monthlyData->whereNull('status_cek_bulanan')->count(),
                'matching_items' => $monthlyData->where('status_cek_bulanan', 'sesuai')->count(),
                'mismatched_items' => $monthlyData->where('status_cek_bulanan', 'tidak_sesuai')->count(),
                'progress_percentage' => 0,
                'current_month' => \Carbon\Carbon::createFromFormat('m', $currentMonth)->format('F'),
                'current_year' => $currentYear
            ];

            if ($stats['total_items'] > 0) {
                $stats['progress_percentage'] = round(($stats['checked_items'] / $stats['total_items']) * 100);
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik dashboard'
            ], 500);
        }
    }

    public function getPriorityItems(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            $limit = $request->get('limit', 10);
            $items = StokDivisi::getItemsPriorityCheck($limit);
            
            return response()->json([
                'success' => true,
                'data' => $items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'divisi' => $item->divisi->divisi,
                        'nama_barang' => $item->stokPusat->nama_barang,
                        'kode_barang' => $item->stokPusat->kode_barang,
                        'sisa_stok' => $item->sisa_stok,
                        'priority' => $item->check_priority,
                        'last_checked' => $item->tgl_cek_formatted,
                        'status' => $item->status_cek_label
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting priority items: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data prioritas'], 500);
        }
    }

    public function markMultipleItems(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:stok_divisis,id',
            'status' => 'required|in:sesuai,tidak_sesuai',
            'keterangan' => 'nullable|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        try {
            $updatedItems = [];
            
            DB::transaction(function () use ($request, $user, &$updatedItems) {
                foreach ($request->item_ids as $itemId) {
                    $stokDivisi = StokDivisi::with(['divisi', 'stokPusat'])->findOrFail($itemId);
                    
                    // Use current stock as physical stock if not specified
                    $stokFisik = $stokDivisi->sisa_stok;
                    if ($request->status === 'tidak_sesuai') {
                        // For mismatched items, you might want to ask for actual physical stock
                        $stokFisik = $request->get('stok_fisik_' . $itemId, $stokDivisi->sisa_stok);
                    }
                    
                    $stokDivisi->update([
                        'stok_fisik_cek' => $stokFisik,
                        'status_cek_bulanan' => $request->status,
                        'tgl_cek_bulanan' => now(),
                        'dicek_oleh' => $user->name,
                        'keterangan_cek' => $request->keterangan
                    ]);
                    
                    $updatedItems[] = $stokDivisi;
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate ' . count($updatedItems) . ' item',
                'data' => $updatedItems
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking multiple items: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate item'], 500);
        }
    }

    public function getRealtimeStats(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            $divisi_id = $request->get('divisi_id');
            
            $stats = StokDivisi::getStatsBulanIni();
            
            if ($divisi_id) {
                $query = StokDivisi::nonAssetGA()->adaStok()->periodeBulan($bulan, $tahun)->byDivisi($divisi_id);
                $items = $query->get();
                
                $stats = [
                    'total' => $items->count(),
                    'sudah_dicek' => $items->whereNotNull('status_cek_bulanan')->count(),
                    'belum_dicek' => $items->whereNull('status_cek_bulanan')->count(),
                    'sesuai' => $items->where('status_cek_bulanan', 'sesuai')->count(),
                    'tidak_sesuai' => $items->where('status_cek_bulanan', 'tidak_sesuai')->count(),
                    'progress_percentage' => $items->count() > 0 ? round(($items->whereNotNull('status_cek_bulanan')->count() / $items->count()) * 100) : 0
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->timestamp
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting realtime stats: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil statistik'], 500);
        }
    }

    public function getRecentActivities(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            $limit = $request->get('limit', 10);
            
            $activities = StokDivisi::with(['divisi', 'stokPusat'])
                                    ->whereNotNull('tgl_cek_bulanan')
                                    ->orderBy('tgl_cek_bulanan', 'desc')
                                    ->limit($limit)
                                    ->get()
                                    ->map(function($item) {
                                        return [
                                            'id' => $item->id,
                                            'divisi' => $item->divisi->divisi,
                                            'nama_barang' => $item->stokPusat->nama_barang,
                                            'status' => $item->status_cek_label,
                                            'dicek_oleh' => $item->dicek_oleh,
                                            'tgl_cek' => $item->tgl_cek_formatted,
                                            'selisih' => $item->selisih,
                                            'icon' => $item->status_cek_icon,
                                            'badge_class' => $item->status_cek_badge_class
                                        ];
                                    });
            
            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting recent activities: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil aktivitas terbaru'], 500);
        }
    }

    public function getStokDetailCekBulanan($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }
        
        try {
            $stokDivisi = StokDivisi::with(['divisi', 'stokPusat'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $stokDivisi->id,
                    'divisi' => [
                        'divisi' => $stokDivisi->divisi->divisi
                    ],
                    'stok_pusat' => [
                        'nama_barang' => $stokDivisi->stokPusat->nama_barang,
                        'kode_barang' => $stokDivisi->stokPusat->kode_barang,
                        'satuan' => $stokDivisi->stokPusat->satuan
                    ],
                    'sisa_stok' => $stokDivisi->sisa_stok,
                    'stok_fisik_cek' => $stokDivisi->stok_fisik_cek,
                    'keterangan_cek' => $stokDivisi->keterangan_cek,
                    'status_cek_bulanan' => $stokDivisi->status_cek_bulanan
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getStokDetailCekBulanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail stok'
            ], 500);
        }
    }

    public function quickUpdate(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:stok_divisis,id',
            'stok_fisik' => 'nullable|integer|min:0',
            'action' => 'nullable|string|in:match,mismatch,reset',
            'keterangan' => 'nullable|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        try {
            $stok = StokDivisi::with(['divisi', 'stokPusat'])->findOrFail($request->id);
            
            // Tentukan stok fisik berdasarkan input atau action
            if ($request->has('stok_fisik') && $request->stok_fisik !== null) {
                $stokFisik = (int) $request->stok_fisik;
            } elseif ($request->action === 'match') {
                $stokFisik = $stok->sisa_stok; // Samakan dengan stok sistem
            } elseif ($request->action === 'reset') {
                // Reset status
                $stok->update([
                    'stok_fisik_cek' => null,
                    'status_cek_bulanan' => null,
                    'tgl_cek_bulanan' => null,
                    'dicek_oleh' => null,
                    'keterangan_cek' => null
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil direset',
                    'data' => [
                        'id' => $stok->id,
                        'status' => 'Belum Dicek',
                        'status_cek_bulanan' => null,
                        'stok_fisik_cek' => null,
                        'selisih' => null,
                        'tgl_cek_bulanan' => null,
                        'dicek_oleh' => null
                    ]
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Data tidak valid'], 422);
            }
            
            // Tentukan status berdasarkan perbandingan
            $status = ($stokFisik === $stok->sisa_stok) ? 'sesuai' : 'tidak_sesuai';
            
            // Update data
            $updateData = [
                'stok_fisik_cek' => $stokFisik,
                'status_cek_bulanan' => $status,
                'tgl_cek_bulanan' => now(),
                'dicek_oleh' => $user->name,
            ];
            
            if ($request->has('keterangan') && !empty($request->keterangan)) {
                $updateData['keterangan_cek'] = $request->keterangan;
            }
            
            $stok->update($updateData);
            $stok->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => [
                    'id' => $stok->id,
                    'status' => $status === 'sesuai' ? 'Sesuai' : 'Tidak Sesuai',
                    'status_cek_bulanan' => $status,
                    'stok_fisik_cek' => $stok->stok_fisik_cek,
                    'selisih' => $stok->selisih,
                    'tgl_cek_bulanan' => $stok->tgl_cek_bulanan->format('d/m/Y H:i'),
                    'dicek_oleh' => $stok->dicek_oleh,
                    'stok_sistem' => $stok->sisa_stok,
                    'keterangan_cek' => $stok->keterangan_cek
                ]
            ]);
    
        } catch (\Exception $e) {
            Log::error('Quick Update Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnalytics(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            $bulan = $request->get('bulan', date('m'));
            $tahun = $request->get('tahun', date('Y'));
            
            // Stats by division
            $statsByDivisi = StokDivisi::getStatsByDivisi($bulan, $tahun);
            
            // Trend data (last 6 months)
            $trends = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStats = StokDivisi::periodeBulan($date->month, $date->year)
                                    ->nonAssetGA()
                                    ->adaStok()
                                    ->get();
                
                $trends[] = [
                    'month' => $date->format('M Y'),
                    'total' => $monthStats->count(),
                    'checked' => $monthStats->whereNotNull('status_cek_bulanan')->count(),
                    'accuracy' => $monthStats->whereNotNull('status_cek_bulanan')->count() > 0 ? 
                            round(($monthStats->where('status_cek_bulanan', 'sesuai')->count() / $monthStats->whereNotNull('status_cek_bulanan')->count()) * 100) : 0
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'by_division' => $statsByDivisi,
                    'trends' => $trends,
                    'summary' => StokDivisi::getStatsBulanIni()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting analytics: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data analytics'], 500);
        }
    }
 
    public function getMobileItems(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset', 'pj_divisi'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            $query = StokDivisi::with(['divisi', 'stokPusat'])->nonAssetGA()->adaStok();
            
            // Filter by user's division if pj_divisi
            if ($user->role === 'pj_divisi' && $user->divisi_id) {
                $query->where('divisi_id', $user->divisi_id);
            }
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('stokPusat', function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
                });
            }
            
            // Pagination for mobile
            $items = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 20));
            
            return response()->json([
                'success' => true,
                'data' => $items->items(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'total' => $items->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting mobile items: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data'], 500);
        }
    }

    public function batchMarkMatch(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'ga', 'aset'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        try {
            $updated = StokDivisi::whereNull('status_cek_bulanan')
                                ->whereHas('divisi', fn($q) => $q->where('divisi', '!=', 'Asset & GA'))
                                ->where('sisa_stok', '>', 0)
                                ->update([
                                    'stok_fisik_cek' => DB::raw('sisa_stok'),
                                    'status_cek_bulanan' => 'sesuai',
                                    'tgl_cek_bulanan' => now(),
                                    'dicek_oleh' => $user->name,
                                    'keterangan_cek' => 'Batch mark as matching'
                                ]);
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menandai {$updated} item sebagai sesuai"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses'], 500);
        }
    }
}