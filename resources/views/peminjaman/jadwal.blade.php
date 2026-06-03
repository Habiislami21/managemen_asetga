<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Reservasi Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --secondary: #4a9e5a;
            --text-primary: #333;
            --bg-light: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
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

        .date-filter-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .vehicle-card {
            background: white;
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .vehicle-header {
            background-color: rgba(97, 0, 221, 0.05);
            padding: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .vehicle-title {
            font-weight: 600;
            margin: 0;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .vehicle-category {
            font-size: 0.85rem;
            color: #666;
            background: #e2e8f0;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 5px;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-ready { background-color: #2a9d5f; }
        .status-booked { background-color: #e53e3e; }
        
        .booking-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .booking-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-item:last-child {
            border-bottom: none;
        }

        .booking-time {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .booking-details {
            font-size: 0.85rem;
            color: #666;
        }

        .booking-badge {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-approved { background-color: #d4edda; color: #155724; }

        .no-bookings {
            padding: 30px;
            text-align: center;
            color: #888;
        }

        .no-bookings i {
            font-size: 3rem;
            color: #e2e8f0;
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-custom:hover {
            background-color: var(--primary-hover);
            color: white;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/menu-awal') }}">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="bukaStatistik()">
                    <i class="fas fa-chart-bar me-1"></i>Statistik
                </button>
                <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-history me-1"></i>Riwayat
                </a>
                <a href="{{ route('peminjaman.create') }}" class="btn btn-custom">
                    <i class="fas fa-plus me-1"></i>Ajukan
                </a>
            </div>
        </div>
    </nav>

    <div class="page-header text-center">
        <div class="container">
            <h2 class="fw-bold mb-2">Jadwal Reservasi Kendaraan</h2>
            <p class="mb-0 opacity-75">Pantau ketersediaan dan jadwal pemakaian kendaraan operasional</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Date Filter -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="date-filter-card">
                    <form action="{{ route('peminjaman.jadwal') }}" method="GET" class="d-flex align-items-center gap-3">
                        <label for="tanggal" class="fw-bold mb-0 text-nowrap">Pilih Tanggal:</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
                        @if($tanggal != \Carbon\Carbon::today()->format('Y-m-d'))
                            <a href="{{ route('peminjaman.jadwal') }}" class="btn btn-outline-secondary text-nowrap">Hari Ini</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="mb-3 text-center">
            <h5 class="fw-bold text-primary">
                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
            </h5>
            @if($tanggal == \Carbon\Carbon::today()->format('Y-m-d'))
                <span class="badge bg-success">Hari Ini</span>
            @endif
        </div>

        <!-- Vehicles Grid -->
        <div class="row g-4">
            @foreach($kendaraans as $kendaraan)
                @php
                    $isBookedNow = false;
                    $now = \Carbon\Carbon::now()->format('H:i');
                    if($tanggal == \Carbon\Carbon::today()->format('Y-m-d')) {
                        foreach($kendaraan->peminjamans as $p) {
                            $jamPinjam = \Carbon\Carbon::parse($p->jam_pinjam)->format('H:i');
                            $jamKembali = \Carbon\Carbon::parse($p->jam_kembali)->format('H:i');
                            if($p->status == 'approved' && $now >= $jamPinjam && $now <= $jamKembali) {
                                $isBookedNow = true;
                                break;
                            }
                        }
                    }
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="vehicle-card">
                        <div class="vehicle-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="vehicle-title">{{ $kendaraan->nama }}</h3>
                                    <span class="vehicle-category">{{ $kendaraan->kategori }}</span>
                                </div>
                                @if($tanggal == \Carbon\Carbon::today()->format('Y-m-d'))
                                    <div class="d-flex align-items-center" title="{{ $isBookedNow ? 'Sedang Dipakai' : 'Tersedia Saat Ini' }}">
                                        <span class="status-indicator {{ $isBookedNow ? 'status-booked' : 'status-ready' }}"></span>
                                        <span class="small fw-bold {{ $isBookedNow ? 'text-danger' : 'text-success' }}">
                                            {{ $isBookedNow ? 'Sedang Dipakai' : 'Ready' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="vehicle-body">
                            @if($kendaraan->peminjamans->count() > 0)
                                <ul class="booking-list">
                                    @foreach($kendaraan->peminjamans as $peminjaman)
                                        <li class="booking-item">
                                            <div>
                                                <div class="booking-time">
                                                    <i class="far fa-clock me-1 text-primary"></i> 
                                                    {{ \Carbon\Carbon::parse($peminjaman->jam_pinjam)->format('H:i') }} - {{ \Carbon\Carbon::parse($peminjaman->jam_kembali)->format('H:i') }}
                                                </div>
                                                <div class="booking-details mt-1">
                                                    <i class="far fa-user me-1"></i> {{ $peminjaman->nama_peminjam }}
                                                </div>
                                            </div>
                                            <div>
                                                <span class="booking-badge {{ $peminjaman->status == 'approved' ? 'badge-approved' : 'badge-pending' }}">
                                                    {{ ucfirst($peminjaman->status) }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="no-bookings">
                                    <i class="fas fa-calendar-check d-block"></i>
                                    <span class="fw-medium">Kosong seharian</span>
                                    <p class="small mt-1 mb-0">Kendaraan belum ada yang reservasi pada tanggal ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function bukaStatistik() {
            Swal.fire({
                title: 'Otorisasi Admin',
                text: 'Masukkan password admin untuk melihat statistik',
                input: 'password',
                inputPlaceholder: 'Password',
                showCancelButton: true,
                confirmButtonText: 'Masuk',
                cancelButtonText: 'Batal',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Password tidak boleh kosong');
                        return false;
                    }
                    
                    // Create dynamic form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("peminjaman.statistik.auth") }}';
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    
                    const passInput = document.createElement('input');
                    passInput.type = 'hidden';
                    passInput.name = 'password';
                    passInput.value = password;
                    form.appendChild(passInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // Cek error session dari middleware/controller statistik
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: '{{ session('error') }}'
            });
        @endif
    </script>
</body>
</html>
