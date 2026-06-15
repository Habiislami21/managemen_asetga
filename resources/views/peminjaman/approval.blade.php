<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Peminjaman Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --text-primary: #333;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: linear-gradient(rgba(97, 0, 221, 0.05), rgba(97, 0, 221, 0.1)), url('{{ asset('img/background-2.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .approval-container {
            background: #ffffff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 700px;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-header-custom {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
        }

        .card-body-custom {
            padding: 30px;
        }

        .table-custom th {
            background-color: #f8f9fa;
            color: var(--primary);
            font-weight: 600;
            width: 35%;
        }

        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-approved { background-color: #2a9d5f; color: #fff; }
        .badge-rejected { background-color: #e53e3e; color: #fff; }

        .btn-approve {
            background-color: #2a9d5f;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            background-color: #238551;
            transform: translateY(-2px);
            color: white;
        }

        .btn-reject {
            background-color: #e53e3e;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reject:hover {
            background-color: #c53030;
            transform: translateY(-2px);
            color: white;
        }

        .alert-info-custom {
            background-color: #eef2ff;
            border-left: 4px solid var(--primary);
            color: #4338ca;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="approval-container">
        <div class="card-header-custom">
            <h4><i class="fas fa-car-side me-2"></i>Approval Peminjaman</h4>
        </div>
        
        <div class="card-body-custom">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-custom mb-4">
                    <tbody>
                        <tr>
                            <th>Status Saat Ini</th>
                            <td>
                                @if($peminjaman->status == 'pending')
                                    <span class="badge badge-pending p-2 px-3 rounded-pill">Pending</span>
                                @elseif($peminjaman->status == 'approved')
                                    <span class="badge badge-approved p-2 px-3 rounded-pill">Approved</span>
                                @elseif($peminjaman->status == 'rejected')
                                    <span class="badge badge-rejected p-2 px-3 rounded-pill">Rejected</span>
                                @elseif($peminjaman->status == 'completed')
                                    <span class="badge bg-info p-2 px-3 rounded-pill">Completed</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Nama Peminjam</th>
                            <td>{{ $peminjaman->nama_peminjam }}</td>
                        </tr>
                        <tr>
                            <th>Nama Driver</th>
                            <td>{{ $peminjaman->nama_driver }}</td>
                        </tr>
                        <tr>
                            <th>Nomor HP</th>
                            <td>{{ $peminjaman->nomor_hp }}</td>
                        </tr>
                        <tr>
                            <th>Kendaraan</th>
                            <td>{{ $peminjaman->kendaraan->nama }} <span class="text-muted small">({{ $peminjaman->kendaraan->kategori }})</span></td>
                        </tr>
                        <tr>
                            <th>Waktu Pinjam</th>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->locale('id')->translatedFormat('d F Y') }}</strong><br>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($peminjaman->jam_pinjam)->format('H.i') }} - {{ \Carbon\Carbon::parse($peminjaman->jam_kembali)->format('H.i') }} WIB</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Keperluan</th>
                            <td>{{ $peminjaman->keperluan }}</td>
                        </tr>
                        <tr>
                            <th>Alamat Tujuan</th>
                            <td>{{ $peminjaman->alamat_tujuan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($peminjaman->status == 'pending')
                <div class="row g-2">
                    <div class="col-md-6">
                        <form action="{{ route('peminjaman.approve', $peminjaman->approval_token) }}" method="POST" id="formApprove">
                            @csrf
                            <button type="button" class="btn btn-approve w-100" id="btnApprove">
                                <i class="fas fa-check-circle me-2"></i>Setujui
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-reject w-100" id="btnReject">
                            <i class="fas fa-times-circle me-2"></i>Tolak
                        </button>
                    </div>
                </div>
            @else
                <div class="alert-info-custom mt-2 text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Pengajuan ini telah diproses (<strong>{{ strtoupper($peminjaman->status) }}</strong>) pada {{ \Carbon\Carbon::parse($peminjaman->approved_at)->locale('id')->translatedFormat('d F Y, H.i') }} WIB.
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('peminjaman.reject', $peminjaman->approval_token) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="rejectModalLabel">Tolak Peminjaman</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="catatan_admin" class="form-label fw-bold">Alasan Penolakan (Opsional)</label>
                            <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3" placeholder="Contoh: Kendaraan sudah dibooking oleh divisi lain."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4">Ya, Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handler untuk tombol Approve
        document.getElementById('btnApprove')?.addEventListener('click', function() {
            Swal.fire({
                title: 'Setujui Peminjaman?',
                text: "Apakah Anda yakin ingin menyetujui pengajuan peminjaman kendaraan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2a9d5f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('formApprove').submit();
                }
            });
        });

        // Handler untuk tombol Reject
        document.getElementById('btnReject')?.addEventListener('click', function() {
            Swal.fire({
                title: 'Tolak Peminjaman?',
                text: "Apakah Anda yakin ingin menolak pengajuan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan modal alasan penolakan
                    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                    rejectModal.show();
                }
            });
        });

        // Handler untuk sukses/error dari session
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#6100dd'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#6100dd'
            });
        @endif
    </script>
</body>
</html>
