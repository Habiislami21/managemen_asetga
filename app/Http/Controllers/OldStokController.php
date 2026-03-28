<?php

namespace App\Http\Controllers;

use App\Models\DistribusiStok;
use App\Models\Divisi;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OldStokController extends Controller
{
    public function index()
    {
        $stoks = Stok::with('divisi')->get();
        $divisis = Divisi::all();
        $assetGADivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        // Jika divisi Asset & GA belum ada, buat baru
        if (!$assetGADivisi) {
            $assetGADivisi = Divisi::create(['divisi' => 'Asset & GA']);
        }
        
        return view('admin.ajuan-keseluruhan', compact('stoks', 'divisis', 'assetGADivisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:stoks,kode_barang',
            'nama_barang' => 'required',
            'sisa_stok' => 'required|integer',
            'stok_ideal' => 'required|integer',
            'satuan' => 'required'
        ]);

        // Cari divisi Asset & GA
        $defaultDivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        // Jika tidak ada divisi yang dipilih atau divisi kosong, gunakan default
        if (empty($request->divisi_id)) {
            $request->merge(['divisi_id' => $defaultDivisi ? $defaultDivisi->id : null]);
        }

       
        // DB::beginTransaction();
        // try {
                // Menyimpan data ke table tok
                $stok = Stok::create($request->all());

                // Menyimpan data ke distribusi stok
                $distribusi = DistribusiStok::create([
                    'divisi_id' => $defaultDivisi->id,
                    'stok_id' => $stok->id,
                    'jumlah_stok' => $stok->sisa_stok,
                ]);
                return response()->json([
                    'success' => 'Barang berhasil ditambahkan!',
                    'data' => $stok  // Mengembalikan seluruh objek stok, termasuk ID
                ], 201);

        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
    }

    public function cekKodeBarang(Request $request)
    {
        $exists = Stok::where('kode_barang', $request->kode_barang)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function generateKodeBarang()
    {
        $kodeBarangTersedia = Stok::pluck('kode_barang')->toArray();
        if (empty($kodeBarangTersedia)) {
            return response()->json(['kode_barang' => 1]);
        }

        sort($kodeBarangTersedia);

        $nextKode = 1;
        foreach ($kodeBarangTersedia as $kode) {
            if ($kode == $nextKode) {
                $nextKode++;
            } else {
                break;
            }
        }

        return response()->json(['kode_barang' => $nextKode]);
    }

    public function ajuanDivisi()
    {
        $divisis = Divisi::all();
        return view('admin.ajuan-divisi', compact('divisis'));
    }

    public function destroy($id)
    {
        $stok = Stok::findOrFail($id);
        $stok->delete();
        
        return response()->json([
            'success' => 'Barang berhasil dihapus!'
        ]);
    }

    public function updateStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tipe' => 'required|in:masuk,keluar'
        ]);

        $stok = Stok::findOrFail($id);
        
        if ($request->tipe === 'masuk') {
            $stok->sisa_stok += $request->jumlah;
        } else {
            if ($stok->sisa_stok < $request->jumlah) {
                return response()->json([
                    'error' => 'Stok tidak mencukupi!'
                ], 422);
            }
            $stok->sisa_stok -= $request->jumlah;
        }
        
        $stok->save();
        $stok->load('divisi');
        
        return response()->json([
            'success' => 'Stok berhasil diperbarui!',
            'data' => $stok
        ]);
    }

    public function updateStokIdeal(Request $request, $id)
    {
        $request->validate([
            'stok_ideal' => 'required|integer|min:0'
        ]);

        $stok = Stok::findOrFail($id);
        $stok->stok_ideal = $request->stok_ideal;
        $stok->save();
        
        return response()->json([
            'success' => 'Stok ideal berhasil diperbarui!',
            'data' => $stok
        ]);
    }

    public function getStokByDivisi($divisi_id)
    {
        $stoks = Stok::with('divisi')
                    ->where('divisi_id', $divisi_id)
                    ->get();
                    
        return response()->json([
            'data' => $stoks->map(function($stok) {
                return [
                    'id' => $stok->id,  // Tambahkan ID
                    'kode_barang' => $stok->kode_barang,
                    'nama_barang' => $stok->nama_barang,
                    'sisa_stok' => $stok->sisa_stok,
                    'stok_ideal' => $stok->stok_ideal,
                    'satuan' => $stok->satuan,
                    'kekurangan' => max(0, $stok->stok_ideal - $stok->sisa_stok)
                ];
            })
        ]);
    }
    
    /**
     * Get all stock items from Asset & GA division
     */
    public function getAssetGAStockList()
    {
        $assetGADivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        if (!$assetGADivisi) {
            return response()->json(['error' => 'Divisi Asset & GA tidak ditemukan!'], 404);
        }
        
        $stoks = Stok::where('divisi_id', $assetGADivisi->id)->get();
        
        return response()->json([
            'data' => $stoks->map(function($stok) {
                return [
                    'id' => $stok->id,
                    'kode_barang' => $stok->kode_barang,
                    'nama_barang' => $stok->nama_barang,
                    'stok_ideal' => $stok->stok_ideal,
                    'satuan' => $stok->satuan
                ];
            })
        ]);
    }
    
    /**
     * Add an existing item from Asset & GA to another division
     */
    public function addToDivisi(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|exists:stoks,kode_barang',
            'divisi_id' => 'required|exists:divisis,id'
        ]);
        
        // Find the Asset & GA division
        $assetGADivisi = Divisi::where('divisi', 'Asset & GA')->first();
        
        if (!$assetGADivisi) {
            return response()->json([
                'error' => 'Divisi Asset & GA tidak ditemukan!'
            ], 404);
        }
        
        // Find the source item
        $sourceItem = Stok::where('kode_barang', $request->kode_barang)
                         ->where('divisi_id', $assetGADivisi->id)
                         ->first();
        
        if (!$sourceItem) {
            return response()->json([
                'error' => 'Barang tidak ditemukan di Asset & GA!'
            ], 404);
        }
        
        // Check if item already exists in target division
        $existingItem = Stok::where('kode_barang', $request->kode_barang)
                           ->where('divisi_id', $request->divisi_id)
                           ->first();
        
        if ($existingItem) {
            return response()->json([
                'error' => 'Barang sudah ada di divisi ini!'
            ], 422);
        }
        
        // Create new item for the target division
        $newItem = new Stok();
        $newItem->kode_barang = $sourceItem->kode_barang;
        $newItem->nama_barang = $sourceItem->nama_barang;
        $newItem->sisa_stok = 0; // Default to zero
        $newItem->stok_ideal = $sourceItem->stok_ideal;
        $newItem->satuan = $sourceItem->satuan;
        $newItem->divisi_id = $request->divisi_id;
        $newItem->save();
        
        return response()->json([
            'success' => 'Barang berhasil ditambahkan ke divisi!',
            'data' => $newItem
        ], 201);
    }
}