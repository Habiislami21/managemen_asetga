<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\Ajuan;
use App\Models\Divisi;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

    public function dashboard()
    {
        $aduanAsetTotal = Aduan::where('jenis_pengaduan', 'Aset')->count();
        $aduanGATotal = Aduan::where('jenis_pengaduan', 'GA')->count();
        $aduanTotal = Aduan::count();

        $monthlyAduansAset = Aduan::where('jenis_pengaduan', 'Aset')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyAduansGA = Aduan::where('jenis_pengaduan', 'GA')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $ajuanRTKTotal = Ajuan::where('kategori_barang', 'RTK')->count();
        $ajuanATKTotal = Ajuan::where('kategori_barang', 'ATK')->count();
        $ajuanTotal = Ajuan::count();

        $monthlyAjuanRTK = Ajuan::where('kategori_barang', 'RTK')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyAjuanATK = Ajuan::where('kategori_barang', 'ATK')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartDataAset = array_fill(0, 12, 0);
        $chartDataGA = array_fill(0, 12, 0);
        $chartDataRTK = array_fill(0, 12, 0);
        $chartDataATK = array_fill(0, 12, 0);

        $totalBarangRTK = Ajuan::where('kategori_barang', 'RTK')->sum('banyak_barang');
        $totalBarangATK = Ajuan::where('kategori_barang', 'ATK')->sum('banyak_barang');

        foreach ($monthlyAduansAset as $data) {
            $chartDataAset[$data->month - 1] = $data->total;
        }

        foreach ($monthlyAduansGA as $data) {
            $chartDataGA[$data->month - 1] = $data->total;
        }

        foreach ($monthlyAjuanRTK as $data) {
        $chartDataRTK[$data->month - 1] = $data->total;
        }

        foreach ($monthlyAjuanATK as $data) {
            $chartDataATK[$data->month - 1] = $data->total;
        }

        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

       return view('admin.dashboard', compact(
        'chartDataAset',
        'chartDataGA',
        'chartDataRTK',
        'chartDataATK',
        'months',
        'aduanAsetTotal',
        'aduanGATotal',
        'aduanTotal',
        'ajuanRTKTotal',
        'ajuanATKTotal',
        'ajuanTotal',
        'totalBarangRTK',
        'totalBarangATK'
    ));
    }

    public function ajuanKeseluruhan()
    {
        return view('admin.ajuan-keseluruhan');
    }

    public function ajuanDivisi()
    {
        return view('admin.ajuan-divisi');
    }

    public function listPengajuan(Request $request)
    {
        $divisis = Divisi::orderBy('divisi')->get();
        
        $query = Ajuan::with('divisi');
        
        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }
        
        $ajuans = $query->orderBy('created_at', 'desc')->get();
        
        return view('admin.ajuan-listpengajuan', compact('ajuans', 'divisis'));
    }

    public function listAduan(Request $request)
    {
        $divisis = Divisi::orderBy('divisi')->get();
        
        $query = Aduan::with('divisi');
        
        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }
        
        $aduans = $query->orderBy('created_at', 'desc')->get();
        
        return view('admin.aduan', compact('aduans', 'divisis'));
    }

    public function updateAduanStatus(Request $request, $id) {
        $aduan = Aduan::findOrFail($id);
    
        // Toggle status: Jika 'pending' jadi 'selesai', jika 'selesai' jadi 'pending'
        $aduan->status = $aduan->status === 'pending' ? 'selesai' : 'pending';
        $aduan->save();
    
        return response()->json([
            'message' => 'Status berhasil diperbarui!',
            'new_status' => $aduan->status
        ]);
    }
       
    
    public function editAduan($id)
    {
        $aduan = Aduan::findOrFail($id);
        return view('admin.edit-aduan', compact('aduan'));
    }

    public function deleteAduan($id)
    {
        $aduan = Aduan::findOrFail($id);
        $aduan->delete();

        return redirect()->route('admin.list-aduan')
                        ->with('success', 'Aduan berhasil dihapus');
    }

    public function getDashboardData()
    {
        $monthlyAduansAset = Aduan::where('jenis_pengaduan', 'Aset')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyAduansGA = Aduan::where('jenis_pengaduan', 'GA')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $monthlyAjuanRTK = Ajuan::where('kategori_barang', 'RTK')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyAjuanATK = Ajuan::where('kategori_barang', 'ATK')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartDataAset = array_fill(0, 12, 0);
        $chartDataGA = array_fill(0, 12, 0);
        $chartDataRTK = array_fill(0, 12, 0);
        $chartDataATK = array_fill(0, 12, 0);

        foreach ($monthlyAduansAset as $data) {
            $chartDataAset[$data->month - 1] = $data->total;
        }

        foreach ($monthlyAduansGA as $data) {
            $chartDataGA[$data->month - 1] = $data->total;
        }

        foreach ($monthlyAjuanRTK as $data) {
        $chartDataRTK[$data->month - 1] = $data->total;
        }

        foreach ($monthlyAjuanATK as $data) {
            $chartDataATK[$data->month - 1] = $data->total;
        }

        return response()->json([
        'chartDataAset' => $chartDataAset,
        'chartDataGA' => $chartDataGA,
        'chartDataRTK' => $chartDataRTK,
        'chartDataATK' => $chartDataATK
        ]);
    }
}