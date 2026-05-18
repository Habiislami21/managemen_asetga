@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .backup-status-card {
            border-left: 4px solid #0d6efd;
        }
        .gdrive-status-card {
            border-left: 4px solid #198754;
        }
        .text-bold {
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid h-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Backup & Recovery Database</h2>
        <button id="btnBackup" class="btn btn-primary"><i class="bi bi-cloud-arrow-up-fill"></i> Buat Backup Database Baru</button>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card backup-status-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="me-3 fs-1 text-primary">
                        <i class="bi bi-database-fill-gear"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Penyimpanan Lokal (Server)</h6>
                        <h4 class="mb-0 text-bold">{{ count($backups) }} File Backup</h4>
                        <small class="text-muted">Tersimpan di folder /storage/app/backups</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card gdrive-status-card shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="me-3 fs-1 {{ $isGoogleDriveConfigured ? 'text-success' : 'text-warning' }}">
                        <i class="bi bi-google-drive"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-1">Sinkronisasi Google Drive</h6>
                        @if ($isGoogleDriveConfigured)
                            <h4 class="mb-0 text-success text-bold">Terhubung Aktif</h4>
                            <small class="text-muted">Backup otomatis akan langsung dikirim ke Google Drive Anda</small>
                        @else
                            <h4 class="mb-0 text-warning text-bold">Belum Dikonfigurasi</h4>
                            <small class="text-muted"><a href="#setupGuide" data-bs-toggle="collapse" class="text-decoration-none">Klik disini untuk panduan setup</a></small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Setup Guide (Collapse) -->
    <div class="collapse mb-4" id="setupGuide">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-dark py-2">
                <h5 class="mb-0 fs-6"><i class="bi bi-info-circle-fill"></i> Panduan Menghubungkan Google Drive</h5>
            </div>
            <div class="card-body p-3">
                <p class="mb-2">Untuk mengirim backup otomatis ke Google Drive Anda, tambahkan parameter berikut di file <strong>`.env`</strong> server hosting Anda:</p>
                <pre class="bg-light p-3 rounded border fs-7">
GOOGLE_DRIVE_CLIENT_ID="isi_client_id_google_console_anda"
GOOGLE_DRIVE_CLIENT_SECRET="isi_client_secret_google_console_anda"
GOOGLE_DRIVE_REFRESH_TOKEN="isi_refresh_token_oauth_anda"
GOOGLE_DRIVE_FOLDER_ID="isi_id_folder_tujuan_google_drive_anda"</pre>
                <ol class="fs-7 mb-0">
                    <li>Buat Project di <strong>Google Cloud Console</strong>.</li>
                    <li>Aktifkan <strong>Google Drive API</strong>.</li>
                    <li>Buat OAuth Client ID untuk mendapatkan Client ID & Client Secret.</li>
                    <li>Lakukan otorisasi Oauth Playground untuk mendapatkan Refresh Token.</li>
                    <li>Buat folder kosong di Google Drive Anda, lalu copy kode ID yang ada di URL folder tersebut untuk dipasang di <code>GOOGLE_DRIVE_FOLDER_ID</code>.</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Backup List Table -->
    <div class="card shadow-sm">
        <div class="card-header py-2 bg-light">
            <h5 class="mb-0 fs-6"><i class="bi bi-table"></i> Daftar Riwayat Backup Lokal</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-striped table-hover datatable w-100" id="tableBackup">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Tanggal Pembuatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($backups as $backup)
                            <tr>
                                <td>
                                    <span class="text-bold"><i class="bi bi-file-earmark-zip-fill text-warning me-1"></i> {{ $backup['file_name'] }}</span>
                                </td>
                                <td>{{ $backup['file_size'] }}</td>
                                <td>{{ $backup['last_modified'] }}</td>
                                <td>
                                    <a href="{{ route('admin.backup.download', $backup['file_name']) }}" class="btn btn-sm btn-success" title="Download File">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-delete" data-filename="{{ $backup['file_name'] }}" title="Hapus Backup">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted mb-2"><i class="bi bi-hdd-fill fs-1"></i></div>
                                    Belum ada file backup yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
$(document).ready(function() {
    $('#tableBackup').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        order: [[2, 'desc']]
    });

    // Buat Backup Baru
    $('#btnBackup').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses Backup...');

        Swal.fire({
            title: 'Sedang memproses...',
            text: 'Proses dumping database sedang berjalan di latar belakang.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route("admin.backup.create") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan sistem.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-cloud-arrow-up-fill"></i> Buat Backup Database Baru');
            }
        });
    });

    // Hapus Backup
    $(document).on('click', '.btn-delete', function() {
        const filename = $(this).data('filename');
        const row = $(this).closest('tr');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "File backup " + filename + " akan dihapus permanen dari server!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ url("/admin/backup/delete") }}/' + filename,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menghapus file.';
                        if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush
