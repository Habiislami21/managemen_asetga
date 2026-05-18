<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function __construct()
    {
        // Hanya admin/super admin yang bisa mengelola backup
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || ($user->role !== 'admin' && $user->role !== 'superadmin')) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $backupName = config('backup.backup.name', env('APP_NAME', 'laravel-backup'));
        $disk = Storage::disk('local');
        
        $backups = [];
        
        // Cek file backup di disk local
        if ($disk->exists($backupName)) {
            $files = $disk->allFiles($backupName);
            
            foreach ($files as $file) {
                if (substr($file, -4) === '.zip') {
                    $backups[] = [
                        'file_name' => str_replace($backupName . '/', '', $file),
                        'file_path' => $file,
                        'file_size' => $this->formatBytes($disk->size($file)),
                        'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->locale('id')->translatedFormat('d F Y H:i:s'),
                        'raw_modified' => $disk->lastModified($file)
                    ];
                }
            }
            
            // Urutkan berdasarkan waktu modifikasi terbaru
            usort($backups, function ($a, $b) {
                return $b['raw_modified'] <=> $a['raw_modified'];
            });
        }

        // Cek apakah Google Drive terkonfigurasi
        $isGoogleDriveConfigured = !empty(env('GOOGLE_DRIVE_CLIENT_ID')) && 
                                   !empty(env('GOOGLE_DRIVE_CLIENT_SECRET')) && 
                                   !empty(env('GOOGLE_DRIVE_REFRESH_TOKEN'));

        return view('admin.backup', compact('backups', 'isGoogleDriveConfigured'));
    }

    public function create()
    {
        try {
            // Jalankan command spatie backup secara async/synchronous via Artisan
            // --only-db agar backup berjalan sangat cepat (hanya database)
            Artisan::call('backup:run --only-db');
            
            return response()->json([
                'success' => true,
                'message' => 'Proses backup database berhasil dijalankan!'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal melakukan backup database: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($fileName)
    {
        $backupName = config('backup.backup.name', env('APP_NAME', 'laravel-backup'));
        $filePath = $backupName . '/' . $fileName;
        $disk = Storage::disk('local');

        if ($disk->exists($filePath)) {
            $fs = Storage::disk('local')->getDriver();
            $stream = $fs->readStream($filePath);

            return Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $disk->mimeType($filePath),
                "Content-Length" => $disk->size($filePath),
                "Content-disposition" => "attachment; filename=\"" . basename($filePath) . "\"",
            ]);
        }

        abort(404, 'File backup tidak ditemukan.');
    }

    public function delete($fileName)
    {
        try {
            $backupName = config('backup.backup.name', env('APP_NAME', 'laravel-backup'));
            $filePath = $backupName . '/' . $fileName;
            $disk = Storage::disk('local');

            if ($disk->exists($filePath)) {
                $disk->delete($filePath);
                return response()->json([
                    'success' => true,
                    'message' => 'File backup berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus file backup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
