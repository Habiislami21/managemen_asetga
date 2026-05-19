@extends('layouts.master')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stats-card {
            border-left: 4px solid;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-card.total { border-left-color: #007bff; }
        .stats-card.checked { border-left-color: #28a745; }
        .stats-card.unchecked { border-left-color: #ffc107; }
        .stats-card.match { border-left-color: #17a2b8; }
        .stats-card.mismatch { border-left-color: #dc3545; }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .floating-action {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
        
        thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #f8f9fa !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark mb-0"><i class="bi bi-clipboard-check me-2"></i>Cek Bulanan Stok (Per Divisi)</h2>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4" id="statsContainer">
            <div class="col-md-2 col-6">
                <div class="card stats-card total h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-boxes fs-2 text-primary mb-2"></i>
                        <h3 class="mb-0" id="totalItems">{{ $total_items }}</h3>
                        <p class="mb-0 text-muted">Total Item</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card stats-card checked h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-2 text-success mb-2"></i>
                        <h3 class="mb-0" id="checkedItems">{{ $sudah_dicek }}</h3>
                        <p class="mb-0 text-muted">Sudah Dicek</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card stats-card unchecked h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-2 text-warning mb-2"></i>
                        <h3 class="mb-0" id="uncheckedItems">{{ $belum_dicek }}</h3>
                        <p class="mb-0 text-muted">Belum Dicek</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card stats-card match h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check fs-2 text-info mb-2"></i>
                        <h3 class="mb-0" id="matchItems">{{ $stokDivisi_sesuai }}</h3>
                        <p class="mb-0 text-muted">Sesuai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card stats-card mismatch h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle fs-2 text-danger mb-2"></i>
                        <h3 class="mb-0" id="mismatchItems">{{ $stokDivisi_tidak_sesuai }}</h3>
                        <p class="mb-0 text-muted">Tidak Sesuai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block">
                            <svg width="60" height="60" class="progress-ring">
                                <circle cx="30" cy="30" r="25" stroke="#e9ecef" stroke-width="4" fill="transparent"></circle>
                                <circle id="progressCircle" cx="30" cy="30" r="25" stroke="#28a745" stroke-width="4" 
                                        fill="transparent" stroke-dasharray="157" stroke-dashoffset="{{ 157 - (157 * $progress_percentage / 100) }}"></circle>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <small id="progressText" class="fw-bold">{{ $progress_percentage }}%</small>
                            </div>
                        </div>
                        <p class="mb-0 text-muted mt-2">Progress</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Divisi -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i>Daftar Divisi</h5>
                <div class="w-25">
                    <input type="text" class="form-control form-control-sm" id="searchDivisi" placeholder="Cari divisi...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableDivisiList">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Divisi</th>
                                <th class="text-center">Total Item</th>
                                <th class="text-center">Progress Pemeriksaan</th>
                                <th class="text-center" width="20%">Aksi Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allStokDivisis = \App\Models\StokDivisi::with(['divisi', 'stokPusat'])
                                    ->whereHas('divisi', function($q) {
                                        $q->where('divisi', '!=', 'Asset & GA');
                                    })
                                    ->where('sisa_stok', '>', 0)
                                    ->get()
                                    ->groupBy('divisi_id');
                            @endphp

                            @forelse($divisis as $index => $divisi)
                                @php
                                    $items = $allStokDivisis->get($divisi->id, collect());
                                    if ($items->isEmpty()) continue;

                                    $totalItems = $items->count();
                                    $checkedItems = $items->whereNotNull('status_cek_bulanan')->count();
                                    $progress = $totalItems > 0 ? round(($checkedItems / $totalItems) * 100) : 0;
                                @endphp
                                <tr class="row-divisi">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold nama-divisi">{{ $divisi->divisi }}</td>
                                    <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">{{ $totalItems }} Item</span></td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="me-2 fw-bold text-{{ $progress == 100 ? 'success' : 'warning' }}">{{ $progress }}%</span>
                                            <div class="progress w-50" style="height: 8px;">
                                                <div class="progress-bar {{ $progress == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $progress }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCek_{{ $divisi->id }}">
                                            <i class="bi bi-search me-1"></i> Periksa Stok
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        Belum ada data stok divisi untuk diperiksa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modals for Each Divisi -->
        @foreach($divisis as $divisi)
            @php
                $items = $allStokDivisis->get($divisi->id, collect());
                if ($items->isEmpty()) continue;
            @endphp
            <div class="modal fade" id="modalCek_{{ $divisi->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-building me-2 text-primary"></i>Pemeriksaan Stok: {{ $divisi->divisi }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex align-items-center mb-4">
                                <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
                                <div>
                                    <strong>Panduan Cek Lapangan:</strong><br>
                                    Anda dapat mengunduh data Excel di bawah ini untuk dicocokkan saat ke lapangan. Jika semua barang pada divisi ini sudah benar, klik <b>"Tandai Semua Benar"</b>. Jika terdapat ketidaksesuaian/kesalahan, isikan angka di kolom <b>Stok Fisik</b> dan berikan keterangan.
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <button type="button" class="btn btn-success btn-sm me-2 shadow-sm" onclick="markAllCorrect({{ $divisi->id }})">
                                        <i class="bi bi-check-all me-1"></i>Tandai Semua Benar
                                    </button>
                                </div>
                                <div>
                                    <form action="{{ route('divisi.export') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="divisi_id" value="{{ $divisi->id }}">
                                        <button type="submit" class="btn btn-outline-success btn-sm shadow-sm">
                                            <i class="bi bi-file-earmark-excel me-1"></i>Download Form Excel
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0" id="tableDivisi_{{ $divisi->id }}">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">Kode</th>
                                            <th width="25%">Nama Barang</th>
                                            <th width="10%">Stok Sistem</th>
                                            <th width="15%">Stok Fisik Real <span class="text-danger">*</span></th>
                                            <th width="10%">Status Saat Ini</th>
                                            <th width="20%">Komentar / Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $idx => $item)
                                            <tr class="item-row" data-id="{{ $item->id }}" data-sistem="{{ $item->sisa_stok }}">
                                                <td class="text-center">{{ $idx + 1 }}</td>
                                                <td class="text-center"><span class="badge bg-secondary">{{ $item->stokPusat->kode_barang }}</span></td>
                                                <td class="fw-semibold">{{ $item->stokPusat->nama_barang }}</td>
                                                <td class="text-center fw-bold fs-5 text-primary">{{ $item->sisa_stok }}</td>
                                                <td>
                                                    <input type="number" class="form-control text-center input-fisik" 
                                                           value="{{ $item->stok_fisik_cek !== null ? $item->stok_fisik_cek : '' }}" 
                                                           placeholder="Hasil Fisik" min="0">
                                                </td>
                                                <td class="text-center">
                                                    @if($item->status_cek_bulanan == 'sesuai')
                                                        <span class="badge bg-success status-badge">Sesuai</span>
                                                    @elseif($item->status_cek_bulanan == 'tidak_sesuai')
                                                        <span class="badge bg-danger status-badge">Tidak Sesuai</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark status-badge">Belum Dicek</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control input-keterangan" 
                                                           value="{{ $item->keterangan_cek }}" 
                                                           placeholder="Ada kerusakan/hilang?">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary px-4 shadow-sm" onclick="submitDivisiCheck({{ $divisi->id }})">
                                <i class="bi bi-save me-1"></i> Simpan Hasil Pemeriksaan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';

        $(document).ready(function() {
            // Live Search Divisi Table
            $('#searchDivisi').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $("#tableDivisiList tbody .row-divisi").filter(function() {
                    $(this).toggle($(this).find('.nama-divisi').text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        // Tandai Semua Sesuai / Benar
        function markAllCorrect(divisiId) {
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin semua barang di divisi ini sudah sesuai dengan stok sistem?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Semua Benar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const table = document.getElementById('tableDivisi_' + divisiId);
                    const rows = table.querySelectorAll('.item-row');
                    
                    rows.forEach(row => {
                        const sistemVal = row.getAttribute('data-sistem');
                        const inputFisik = row.querySelector('.input-fisik');
                        inputFisik.value = sistemVal; // Set nilai fisik = sistem otomatis
                    });
                    
                    Swal.fire({
                        title: 'Terkonfirmasi!',
                        text: 'Kolom Stok Fisik telah terisi otomatis sesuai Sistem. Jangan lupa klik "Simpan Hasil Pemeriksaan" di bawah.',
                        icon: 'success',
                        timer: 4000,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Submit Update Cek per Divisi ke server
        function submitDivisiCheck(divisiId) {
            const table = document.getElementById('tableDivisi_' + divisiId);
            const rows = table.querySelectorAll('.item-row');
            
            let batchData = [];
            
            rows.forEach(row => {
                const id = row.getAttribute('data-id');
                const inputFisik = row.querySelector('.input-fisik').value;
                const ketVal = row.querySelector('.input-keterangan').value;
                
                // Hanya memproses baris yang kolom fisiknya sudah diisi
                if (inputFisik !== '') {
                    batchData.push({
                        id: id,
                        stok_fisik: parseInt(inputFisik),
                        keterangan: ketVal
                    });
                }
            });
            
            if (batchData.length === 0) {
                Swal.fire('Perhatian', 'Belum ada data Stok Fisik yang diinputkan.', 'warning');
                return;
            }
            
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mencatat hasil pemeriksaan ke dalam sistem...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            $.ajax({
                url: '{{ route("cek.bulanan.batch-update") }}',
                method: 'POST',
                data: {
                    batch_data: JSON.stringify(batchData)
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        // Tutup Modal
                        var modalEl = document.getElementById('modalCek_' + divisiId);
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();

                        Swal.fire({
                            title: 'Pemeriksaan Selesai!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Refresh halaman agar progress terupdate
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data. Coba lagi.', 'error');
                }
            });
        }
    </script>
@endpush
@endsection