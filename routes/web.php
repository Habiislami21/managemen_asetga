<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AduanController;
use App\Http\Controllers\AjuanController;
use App\Http\Controllers\AjuanFinalController;
use App\Http\Controllers\AjuanRutinController;
use App\Http\Controllers\AjuanStokDivisiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CekBulananController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('display.halaman-awal');
});

Auth::routes();
Route::get('/login-admin', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login-admin');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::view('/parallax', 'parallax');
Route::view('/menu-awal', 'display.menu-awal');
Route::view('/about', 'display.about');

Route::resource('pengaduan', AduanController::class);
Route::get('/pengaduan-aset', [AduanController::class, 'index'])->name('aduan');
Route::get('/aduan', [AduanController::class, 'listAduan'])->name('aduan.list');

Route::resource('ajuan', AjuanController::class);
Route::get('/pendataan-stok', [AjuanController::class, 'index'])->name('ajuan');

Route::get('/after-submit', function () {
    if (session('aduan')) {
        return view('display.after-submit');
    } elseif (session('success')) {
        return view('display.after-submit');
    } else {
        return redirect()->route('pengaduan.create');
    }
})->name('display.after-submit');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/data', [AdminController::class, 'getDashboardData'])->name('admin.dashboard.data');
    Route::get('/ajuan', [AdminController::class, 'listPengajuan'])->name('admin.ajuan.listpengajuan');
    Route::get('/aduan', [AdminController::class, 'listAduan'])->name('admin.list-aduan');
    Route::get('/aduan/{id}/edit', [AdminController::class, 'editAduan'])->name('admin.edit-aduan');
    Route::delete('/aduan/{id}', [AdminController::class, 'deleteAduan'])->name('admin.delete-aduan');
    Route::patch('/aduan/{id}/update-status', [AdminController::class, 'updateAduanStatus'])->name('admin.update-aduan-status');
    Route::get('/stok-keseluruhan', [StokController::class, 'index'])->name('stok.ajuan-keseluruhan');
    Route::get('/check-kode-barang', [StokController::class, 'checkKodeBarang']);
    Route::post('/stok/tambah', [StokController::class, 'tambah']);
    Route::post('/stok/update-stok-ideal', [StokController::class, 'updateStokIdeal']);
    Route::post('/stok/update-stok', [StokController::class, 'updateStok']);
    Route::delete('/stok/hapus/{id}', [StokController::class, 'hapus']);
    Route::get('/stok-divisi', [StokController::class, 'ajuanDivisi'])->name('stok.ajuan-divisi');
});

Route::get('/get-asset-ga-barang', [StokController::class, 'getAssetGABarang'])->name('get-asset-ga-barang');
Route::post('/tambah-barang-divisi', [StokController::class, 'tambahBarangDivisi'])->name('tambah-barang-divisi');
Route::get('/get-stok-divisi', [StokController::class, 'getStokDivisi'])->name('get-stok-divisi');
Route::post('/update-stok', [StokController::class, 'updateStok'])->name('update-stok');
Route::get('/get-stok-pusat-info', [StokController::class, 'getStokPusatInfo'])->name('get-stok-pusat-info');
Route::post('/update-stok-divisi', [StokController::class, 'updateStokDivisi'])->name('update-stok-divisi');
Route::post('/update-stok-ideal-divisi', [StokController::class, 'updateStokIdealDivisi'])->name('update-stok-ideal-divisi');
Route::post('/hapus-stok-divisi', [StokController::class, 'hapusStokDivisi'])->name('hapus-stok-divisi');
Route::get('/admin/stok/detail/{kodeBarang}', [StokController::class, 'detailStok']);
Route::post('/admin/stok/edit-barang', [StokController::class, 'editBarang'])->name('stok.edit');

Route::get('/export-excel-stok', [StokController::class, 'exportExcel'])->name('stok.export');
Route::get('/export-excel-aduan', [AduanController::class, 'exportExcel'])->name('aduan.export');
Route::get('/export-excel-ajuan', [AjuanController::class, 'exportExcel'])->name('ajuan.export');
Route::post('/export-excel-divisi', [StokController::class, 'exportExcelDivisi'])->name('divisi.export');

Route::get('/ajuan-rutin', [AjuanRutinController::class, 'index'])->name('ajuan-rutin');
Route::resource('ajuan-rutin', AjuanRutinController::class)->only(['index', 'create', 'store']);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/ajuan-rutin', [AjuanRutinController::class, 'ajuanRutin'])->name('admin.ajuan-rutin');
    Route::post('/ajuan-rutin/update-status-batch', [AjuanRutinController::class, 'updateStatusBatch'])->name('ajuan-rutin.update-status-batch');
    Route::get('/ajuan-rutin/get-status/{namaSpa}', [AjuanRutinController::class, 'getStatusDetail'])->name('ajuan-rutin.get-status');
    Route::get('/admin/ajuan-rutin/export-approved', [AjuanRutinController::class, 'exportApproved'])->name('ajuan-rutin.export-approved');
    Route::post('/ajuan-rutin/delete-batch', [AjuanRutinController::class, 'deleteBatch'])->name('ajuan-rutin.delete-batch');
    Route::get('/ajuan-rutin/edit-batch', [AjuanRutinController::class, 'editBatch'])->name('ajuan-rutin.edit-batch');
    Route::post('/ajuan-rutin/update-batch', [AjuanRutinController::class, 'updateBatch'])->name('ajuan-rutin.update-batch');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/ajuan-final', [App\Http\Controllers\AjuanFinalController::class, 'index'])->name('admin.ajuan-final');
    Route::post('/ajuan-final/create-from-approved', [App\Http\Controllers\AjuanFinalController::class, 'createFinalFromApproved'])->name('ajuan-final.create-from-approved');
    Route::get('/ajuan-final/export', [App\Http\Controllers\AjuanFinalController::class, 'exportAjuan'])->name('ajuan-final.export');
    Route::get('/ajuan-final/get-items', [AjuanFinalController::class, 'getAjuanItems'])->name('ajuan-final.get-items');
    Route::post('/ajuan-final/update-item', [AjuanFinalController::class, 'updateItem'])->name('ajuan-final.update-item');
    Route::post('/ajuan-final/delete-item', [AjuanFinalController::class, 'deleteItem'])->name('ajuan-final.delete-item');
});

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:pj_divisi'])->group(function () {
        Route::get('/ajuan-stok', [AjuanStokDivisiController::class, 'formAjuan'])->name('ajuan.form');
        Route::post('/ajuan-stok/submit', [AjuanStokDivisiController::class, 'submitAjuan'])->name('ajuan.submit');
        Route::post('/ajuan-stok/cancel', [AjuanStokDivisiController::class, 'cancelAjuan'])->name('ajuan.cancel');
    });
    Route::middleware(['role:ga,kabag,admin,aset'])->group(function () {
        Route::get('/approval-ajuan', [AjuanStokDivisiController::class, 'approvalPage'])->name('approval.page');
        Route::post('/approval-ajuan/proses', [AjuanStokDivisiController::class, 'prosesApproval'])->name('approval.proses');
        Route::post('/approval-ajuan/proses-semua', [AjuanStokDivisiController::class, 'prosesApprovalSemua'])->name('approval.proses-semua');
    });
    Route::middleware(['role:admin,aset'])->group(function () {
        Route::get('admin/daftar-ajuan', [AjuanStokDivisiController::class, 'daftarAjuan'])->name('ajuan.daftar');
    });
    
    Route::middleware(['role:admin,ga,aset'])->group(function () {
        Route::get('admin/cek-bulanan', [AjuanStokDivisiController::class, 'cekBulanan'])->name('cek.bulanan');
        Route::post('admin/ajuan/update-cek-bulanan', [AjuanStokDivisiController::class, 'updateCekBulanan'])->name('cek.bulanan.update');
        Route::post('/cek-bulanan/batch-update', [AjuanStokDivisiController::class, 'batchUpdateCekBulanan'])->name('cek.bulanan.batch-update');
        Route::get('/laporan-cek-bulanan', [AjuanStokDivisiController::class, 'laporanCekBulanan'])->name('laporan.cek-bulanan');
        Route::get('/export-cek-bulanan', [AjuanStokDivisiController::class, 'exportCekBulanan'])->name('export.cek-bulanan');
        Route::get('/cek-bulanan/stats', [AjuanStokDivisiController::class, 'getDashboardStatsCekBulanan'])->name('cek.bulanan.stats');
        Route::get('/cek-bulanan/priority-items', [AjuanStokDivisiController::class, 'getPriorityItems'])->name('cek.bulanan.priority');
        Route::post('/cek-bulanan/mark-multiple', [AjuanStokDivisiController::class, 'markMultipleItems'])->name('cek.bulanan.mark-multiple');
    });
    
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/reset-cek-bulanan', [AjuanStokDivisiController::class, 'resetCekBulanan'])->name('reset.cek-bulanan');
        Route::get('/cek-bulanan/analytics', [AjuanStokDivisiController::class, 'getAnalytics'])->name('cek.bulanan.analytics');
        
        // Backup Database Routes
        Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
        Route::post('/backup/create', [BackupController::class, 'create'])->name('admin.backup.create');
        Route::get('/backup/download/{fileName}', [BackupController::class, 'download'])->name('admin.backup.download');
        Route::delete('/backup/delete/{fileName}', [BackupController::class, 'delete'])->name('admin.backup.delete');
    });

    Route::middleware(['auth', 'role:admin,ga,aset'])->prefix('api')->group(function () {
        Route::get('/cek-bulanan/realtime-stats', [AjuanStokDivisiController::class, 'getRealtimeStats'])->name('api.cek.bulanan.realtime-stats');
        Route::get('/cek-bulanan/recent-activities', [AjuanStokDivisiController::class, 'getRecentActivities'])->name('api.cek.bulanan.recent-activities');
        Route::post('/cek-bulanan/quick-update', [AjuanStokDivisiController::class, 'quickUpdate'])->name('api.cek.bulanan.quick-update'); // TAMBAHAN INI
    });

    Route::middleware(['auth'])->prefix('mobile')->group(function () {
        Route::get('/cek-bulanan/items', [AjuanStokDivisiController::class, 'getMobileItems'])->name('mobile.cek.bulanan.items');
        Route::post('/cek-bulanan/scan-update', [AjuanStokDivisiController::class, 'scanUpdate'])->name('mobile.cek.bulanan.scan-update');
    });
    
    Route::get('/ajuan-stok/detail/{id}', [AjuanStokDivisiController::class, 'getDetailAjuan'])->name('ajuan.detail');
    Route::get('/approval/pending-data', [AjuanStokDivisiController::class, 'getPendingData'])->name('approval.get-pending-data');
    Route::get('/approval/history-data', [AjuanStokDivisiController::class, 'getHistoryData'])->name('approval.get-history-data');
    Route::get('/approval/stok-info', [AjuanStokDivisiController::class, 'getStokInfo'])->name('approval.get-stok-info');
    Route::get('/ajuan-stok/realtime-update', [AjuanStokDivisiController::class, 'getRealtimeUpdate'])->name('realtime.updates');

    Route::middleware(['auth', 'role:admin,ga,aset,pj_divisi'])->prefix('mobile')->group(function () {
        Route::get('/cek-bulanan/items', [AjuanStokDivisiController::class, 'getMobileItems'])->name('mobile.cek.bulanan.items');
        Route::post('/cek-bulanan/quick-action', [AjuanStokDivisiController::class, 'quickUpdate'])->name('mobile.cek.bulanan.quick-action'); // TAMBAHAN INI
    });
});

Route::get('/admin/ajuan/get-stok-detail/{id}', [AjuanStokDivisiController::class, 'getStokDetail'])
    ->name('ajuan.get-stok-detail');

Route::post('/admin/ajuan/batch-mark-match', [AjuanStokDivisiController::class, 'batchMarkMatch'])
    ->name('ajuan.batch-mark-match');
Route::get('/admin/cek-bulanan/get-stok-detail/{id}', [AjuanStokDivisiController::class, 'getStokDetailCekBulanan'])
    ->name('cek-bulanan.get-stok-detail');
Route::post('/admin/cek-bulanan/batch-mark-match', [AjuanStokDivisiController::class, 'batchMarkMatch']);

// Peminjaman Kendaraan Routes (Public)
use App\Http\Controllers\PeminjamanController;

Route::get('/peminjaman/jadwal', [PeminjamanController::class, 'jadwal'])->name('peminjaman.jadwal');
Route::get('/peminjaman/kendaraan', [PeminjamanController::class, 'create'])->name('peminjaman.create');
Route::post('/peminjaman/kendaraan', [PeminjamanController::class, 'store'])->name('peminjaman.store');
Route::get('/peminjaman/success', [PeminjamanController::class, 'success'])->name('peminjaman.success');
Route::get('/peminjaman/riwayat', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');
Route::post('/peminjaman/statistik/auth', [PeminjamanController::class, 'statistikAuth'])->name('peminjaman.statistik.auth');
Route::get('/peminjaman/statistik', [PeminjamanController::class, 'statistik'])->name('peminjaman.statistik');

// Peminjaman Kendaraan Routes (Admin Approval)
Route::get('/peminjaman/approval/{token}', [PeminjamanController::class, 'approvalView'])->name('peminjaman.approval');
Route::post('/peminjaman/approval/{token}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
Route::post('/peminjaman/approval/{token}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');