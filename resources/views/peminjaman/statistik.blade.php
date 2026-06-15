<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Peminjaman Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-bottom: 24px;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 16px 20px;
            font-weight: 600;
            color: var(--primary);
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(97, 0, 221, 0.02);
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
            <h2 class="fw-bold mb-2">Statistik Penggunaan Kendaraan</h2>
            <p class="mb-0 opacity-75">{{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Form Filter Bulan Tahun -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('peminjaman.statistik') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="bulan" class="form-select">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('m', $m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select">
                            @for($y=date('Y'); $y>=2024; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primary); border-color: var(--primary);">
                            <i class="fas fa-filter me-2"></i> Filter Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Chart Bulanan -->
            <div class="col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i> Jumlah Peminjaman per Kendaraan
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Pengemudi -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-user-tie me-2"></i> Supir Paling Aktif (Top 10)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Nama Supir</th>
                                        <th class="text-center">Total Pinjam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topDrivers as $index => $driver)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td class="fw-medium">{{ $driver->nama_driver }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">{{ $driver->total_pinjam }}x</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">Data belum tersedia</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Kendaraan -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-car me-2"></i> Kendaraan Paling Sering Dipakai
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Kendaraan</th>
                                        <th class="text-center">Total Pinjam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topKendaraan as $index => $kend)
                                        <tr>
                                            <td class="ps-4">{{ $index + 1 }}</td>
                                            <td class="fw-medium">{{ $kend->kendaraan->nama }} <small class="text-muted">({{ $kend->kendaraan->kategori }})</small></td>
                                            <td class="text-center">
                                                <span class="badge bg-success rounded-pill">{{ $kend->total_pinjam }}x</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">Data belum tersedia</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            
            // gradient for bar
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(97, 0, 221, 0.8)');
            gradient.addColorStop(1, 'rgba(97, 0, 221, 0.2)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Total Peminjaman',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: gradient,
                        borderColor: '#6100dd',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
