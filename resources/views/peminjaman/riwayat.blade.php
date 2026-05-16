<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --bg-light: #f8f9fa;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
        }
        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
        }
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, #9034f8 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 20px rgba(97, 0, 221, 0.2);
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-approved { background-color: #198754; color: #fff; }
        .badge-rejected { background-color: #dc3545; color: #fff; }
        .badge-completed { background-color: #0d6efd; color: #fff; }
        
        table th {
            background-color: rgba(97, 0, 221, 0.05) !important;
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('peminjaman.jadwal') }}">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Jadwal
            </a>
        </div>
    </nav>

    <div class="page-header text-center">
        <div class="container">
            <h2 class="fw-bold mb-2">Riwayat Peminjaman Kendaraan</h2>
            <p class="mb-0 opacity-75">Log pengajuan, persetujuan, dan status kendaraan</p>
        </div>
    </div>

    <div class="container pb-5">
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pinjam</th>
                            <th>Nama Supir / Peminjam</th>
                            <th>Kendaraan</th>
                            <th>Jam</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th>Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $index => $peminjaman)
                            <tr>
                                <td>{{ $peminjamans->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                                <td>{{ $peminjaman->nama_peminjam }}</td>
                                <td>{{ $peminjaman->kendaraan->nama }} <small class="text-muted">({{ $peminjaman->kendaraan->kategori }})</small></td>
                                <td>{{ \Carbon\Carbon::parse($peminjaman->jam_pinjam)->format('H:i') }} - {{ \Carbon\Carbon::parse($peminjaman->jam_kembali)->format('H:i') }}</td>
                                <td>
                                    {{ Str::limit($peminjaman->keperluan, 30) }}<br>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($peminjaman->alamat_tujuan, 30) }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $peminjaman->status }}">
                                        {{ ucfirst($peminjaman->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($peminjaman->catatan_admin)
                                        <small class="text-danger">{{ $peminjaman->catatan_admin }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Belum ada riwayat peminjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $peminjamans->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
