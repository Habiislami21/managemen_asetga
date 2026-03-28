@extends('layouts.master')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
        
        .item-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .item-card.checked-sesuai {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        .item-card.checked-tidak-sesuai {
            border-color: #dc3545;
            background-color: #fff8f8;
        }
        
        .floating-action {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .badge-lg {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        .select2-container--default .select2-selection--single {
            height: 38px;
            line-height: 36px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            .item-card {
                margin-bottom: 1rem;
            }
        }

        .updating {
            animation: pulse 0.8s ease-in-out;
            color: #007bff !important;
            font-weight: bold;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
                color: #28a745;
            }
            100% {
                transform: scale(1);
            }
        }

        .item-card.updating {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            border-color: #007bff;
            transition: all 0.3s ease;
        }

        .item-card.just-updated {
            animation: successGlow 2s ease-in-out;
        }

        @keyframes successGlow {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0.3);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        #progressCircle {
            transition: stroke-dashoffset 0.8s ease-in-out;
        }
    </style>
@endpush

@section('header')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark mb-0"><i class="bi bi-clipboard-check me-2"></i>Cek Bulanan Stok</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="exportData()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
                <button class="btn btn-primary" onclick="batchCheck()">
                    <i class="bi bi-check-all me-1"></i>Batch Check
                </button>
            </div>
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

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter & Pencarian</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Divisi</label>
                        <select class="form-select" id="filterDivisi">
                            <option value="">Semua Divisi</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Cek</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="belum_dicek">Belum Dicek</option>
                            <option value="sesuai">Sesuai</option>
                            <option value="tidak_sesuai">Tidak Sesuai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="filterBulan">
                            <option value="1" {{ date('m') == 1 ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ date('m') == 2 ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ date('m') == 3 ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ date('m') == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ date('m') == 5 ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ date('m') == 6 ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ date('m') == 7 ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ date('m') == 8 ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ date('m') == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ date('m') == 10 ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ date('m') == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ date('m') == 12 ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <select class="form-select" id="filterTahun">
                            <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                            <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Pencarian</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari kode/nama barang...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" onclick="applyFiltersAjax()">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <button class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Grid -->
        <div class="row" id="itemsContainer">
            @forelse($stokDivisis as $stok)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card item-card {{ $stok->status_cek_bulanan === 'sesuai' ? 'checked-sesuai' : ($stok->status_cek_bulanan === 'tidak_sesuai' ? 'checked-tidak-sesuai' : '') }}" 
                         data-item-id="{{ $stok->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">{{ $stok->stokPusat->nama_barang }}</h6>
                                    <p class="text-muted small mb-0">{{ $stok->stokPusat->kode_barang }}</p>
                                </div>
                                <span class="badge {{ $stok->status_cek_badge_class }}">{{ $stok->status_cek_label }}</span>
                            </div>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="border-end">
                                        <h5 class="mb-0 text-primary">{{ $stok->sisa_stok }}</h5>
                                        <small class="text-muted">Sistem</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-end">
                                        <h5 class="mb-0 {{ $stok->stok_fisik_cek !== null ? 'text-success' : 'text-muted' }}">
                                            {{ $stok->stok_fisik_cek ?? '-' }}
                                        </h5>
                                        <small class="text-muted">Fisik</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <h5 class="mb-0 {{ $stok->selisih === null ? 'text-muted' : ($stok->selisih == 0 ? 'text-success' : 'text-danger') }}">
                                        {{ $stok->selisih === null ? '-' : ($stok->selisih > 0 ? '+' . $stok->selisih : $stok->selisih) }}
                                    </h5>
                                    <small class="text-muted">Selisih</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-building me-1"></i>{{ $stok->divisi->divisi }}
                                </small>
                                @if(!$stok->status_cek_bulanan)
                                    <button class="btn btn-primary btn-sm" onclick="openCheckModal({{ $stok->id }})">
                                        <i class="bi bi-check me-1"></i>Cek
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" onclick="openCheckModal({{ $stok->id }})">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                @endif
                            </div>
                            
                            @if($stok->tgl_cek_bulanan)
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>{{ $stok->tgl_cek_formatted }} - {{ $stok->dicek_oleh }}
                                    </small>
                                    @if($stok->keterangan_cek)
                                        <div class="mt-1">
                                            <small class="text-muted"><strong>Keterangan:</strong> {{ $stok->keterangan_cek }}</small>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h5 class="text-muted mt-3">Tidak ada data ditemukan</h5>
                        <p class="text-muted">Coba ubah filter atau pencarian Anda</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($stokDivisis->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $stokDivisis->appends(request()->query())->links() }}
            </div>
        @endif

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data...</p>
        </div>
    </div>

    <!-- Modal for Stock Check -->
    <div class="modal fade" id="checkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cek Stok Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="checkForm">
                        <input type="hidden" id="checkItemId">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="checkItemName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="checkItemCode" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Divisi</label>
                            <input type="text" class="form-control" id="checkItemDivisi" readonly>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Stok Sistem</label>
                                <input type="text" class="form-control" id="checkStokSistem" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Stok Fisik <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="checkStokFisik" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="checkKeterangan" rows="3" placeholder="Masukkan keterangan jika diperlukan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitCheck()">
                        <i class="bi bi-check me-1"></i>Simpan Cek
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action">
        <div class="dropup">
            <button class="btn btn-primary rounded-circle" style="width: 56px; height: 56px;" data-bs-toggle="dropdown">
                <i class="bi bi-plus-lg fs-5"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="quickCheckAll()">
                    <i class="bi bi-check-all me-2"></i>Cek Semua Sesuai
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="refreshData()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh Data
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="showHelp()">
                    <i class="bi bi-question-circle me-2"></i>Bantuan
                </a></li>
            </ul>
        </div>
    </div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global variables
        let currentData = [];
        let selectedItems = [];
        const baseUrl = '{{ url('/admin/cek-bulanan') }}';
        const csrfToken = '{{ csrf_token() }}';
        let updateInterval;
        let isUpdating = false;

        // Initialize page
        $(document).ready(function() {
            initializeSelects();
            startRealTimeUpdates();
            
            // Event listeners
            $('#searchInput').on('input', debounce(applyFiltersAjax, 500));
            $('#filterDivisi, #filterStatus, #filterBulan, #filterTahun').on('change', applyFiltersAjax);

        });

        function initializeSelects() {
            $('#filterDivisi').select2({
                placeholder: 'Pilih Divisi',
                allowClear: true
            });
        }

        function startRealTimeUpdates() {
            updateInterval = setInterval(() => {
                if (!isUpdating) {
                    updateStatisticsOnly();
                }
            }, 30000);
        }

        function updateStatisticsOnly() {
            if (isUpdating) return;
            
            isUpdating = true;
            
            // Menggunakan method getRealtimeStats yang sudah ada
            $.ajax({
                url: baseUrl + '/get-realtime-stats',
                method: 'GET',
                data: {
                    bulan: $('#filterBulan').val(),
                    tahun: $('#filterTahun').val(),
                    divisi_id: $('#filterDivisi').val()
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Update dengan animasi smooth
                        updateStatCardWithAnimation('#totalItems', data.total || 0);
                        updateStatCardWithAnimation('#checkedItems', data.sudah_dicek || 0);
                        updateStatCardWithAnimation('#uncheckedItems', data.belum_dicek || 0);
                        updateStatCardWithAnimation('#matchItems', data.sesuai || 0);
                        updateStatCardWithAnimation('#mismatchItems', data.tidak_sesuai || 0);
                        
                        // Update progress circle
                        updateProgressCircle(data.progress_percentage || 0);
                        $('#progressText').text((data.progress_percentage || 0) + '%');
                    }
                },
                error: function(xhr) {
                    console.error('Failed to update statistics:', xhr);
                },
                complete: function() {
                    isUpdating = false;
                }
            });
        }

        function updateStatCardWithAnimation(selector, newValue) {
            const $element = $(selector);
            const currentValue = parseInt($element.text()) || 0;
            
            if (currentValue !== newValue) {
                $element.addClass('updating');
                
                $({ count: currentValue }).animate({ count: newValue }, {
                    duration: 800,
                    step: function() {
                        $element.text(Math.floor(this.count));
                    },
                    complete: function() {
                        $element.text(newValue);
                        $element.removeClass('updating');
                    }
                });
            }
        }

        function updateProgressCircle(percentage) {
            const circumference = 2 * Math.PI * 25;
            const offset = circumference - ((percentage || 0) / 100) * circumference;
            
            $('#progressCircle').css({
                'stroke-dashoffset': offset,
                'transition': 'stroke-dashoffset 0.8s ease-in-out'
            });
        }

        function applyFilters() {
            showLoading(true);
            
            const filters = {
                divisi_id: $('#filterDivisi').val(),
                status_cek: $('#filterStatus').val(),
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                search: $('#searchInput').val()
            };
            
            // Build query string
            const queryParams = new URLSearchParams();
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    queryParams.append(key, filters[key]);
                }
            });
            
            // Redirect with filters
            window.location.href = window.location.pathname + '?' + queryParams.toString();
        }

        function applyFiltersAjax() {
            showLoading(true);
            
            const filters = {
                divisi_id: $('#filterDivisi').val(),
                status_cek: $('#filterStatus').val(),
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                search: $('#searchInput').val()
            };
            
            // Update URL tanpa reload
            const queryParams = new URLSearchParams();
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    queryParams.append(key, filters[key]);
                }
            });
            const newUrl = window.location.pathname + (queryParams.toString() ? '?' + queryParams.toString() : '');
            window.history.pushState({}, '', newUrl);
            
            // Fetch filtered data
            $.ajax({
                url: window.location.pathname,
                method: 'GET',
                data: filters,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Extract new content dari response
                    const $tempDiv = $('<div>').html(response);
                    const newItemsHtml = $tempDiv.find('#itemsContainer').html();
                    
                    // Update container dengan fade effect
                    $('#itemsContainer').fadeOut(200, function() {
                        $(this).html(newItemsHtml).fadeIn(200);
                    });
                    
                    // Update statistics
                    updateStatisticsOnly();
                    
                    showLoading(false);
                },
                error: function(xhr) {
                    console.error('Filter Error:', xhr);
                    showLoading(false);
                    // Fallback ke reload jika AJAX gagal
                    window.location.href = newUrl;
                }
            });
        }

        function resetFilters() {
            $('#filterDivisi').val('').trigger('change');
            $('#filterStatus').val('');
            $('#filterBulan').val('{{ date("m") }}');
            $('#filterTahun').val('{{ date("Y") }}');
            $('#searchInput').val('');
            
            // Redirect to base URL
            window.location.href = window.location.pathname;
        }

        function openCheckModal(itemId) {
            $.ajax({
                url: baseUrl + '/get-stok-detail/' + itemId,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        const item = response.data;
                        
                        $('#checkItemId').val(item.id);
                        $('#checkItemName').val(item.stok_pusat.nama_barang);
                        $('#checkItemCode').val(item.stok_pusat.kode_barang);
                        $('#checkItemDivisi').val(item.divisi.divisi);
                        $('#checkStokSistem').val(item.sisa_stok + ' ' + item.stok_pusat.satuan);
                        $('#checkStokFisik').val(item.stok_fisik_cek || '');
                        $('#checkKeterangan').val(item.keterangan_cek || '');
                        
                        new bootstrap.Modal($('#checkModal')[0]).show();
                    } else {
                        Swal.fire('Error', response.message || 'Gagal mengambil detail item', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    let message = 'Terjadi kesalahan saat mengambil data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', message, 'error');
                }
            });
        }

        function submitCheck() {
            const formData = {
                id: $('#checkItemId').val(),
                stok_fisik: $('#checkStokFisik').val(),
                keterangan: $('#checkKeterangan').val()
            };
            
            if (!formData.stok_fisik) {
                Swal.fire('Error', 'Stok fisik harus diisi', 'error');
                return;
            }
            
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang memproses data cek stok',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
            
            $.ajax({
                url: '{{ route("cek.bulanan.update") }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        bootstrap.Modal.getInstance($('#checkModal')[0]).hide();
                        
                        // Update card secara real-time
                        updateItemCardRealtime(response.data);
                        
                        // Update statistik
                        updateStatisticsOnly();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Gagal menyimpan data', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Submit Error:', xhr);
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                }
            });
        }

        function updateItemCardRealtime(data) {
            const $card = $(`.item-card[data-item-id="${data.id}"]`);
            if ($card.length === 0) return;
            
            // Add updating class for animation
            $card.addClass('updating');
            
            setTimeout(() => {
                // Remove old status classes
                $card.removeClass('checked-sesuai checked-tidak-sesuai');
                
                // Add new status class
                if (data.status_cek_bulanan === 'sesuai') {
                    $card.addClass('checked-sesuai');
                } else if (data.status_cek_bulanan === 'tidak_sesuai') {
                    $card.addClass('checked-tidak-sesuai');
                }
                
                // Update badge
                const $badge = $card.find('.badge');
                $badge.attr('class', `badge ${data.status_cek_badge_class}`);
                $badge.text(data.status_cek_label);
                
                // Update stok fisik
                const $stokFisik = $card.find('.col-4 h5').eq(1);
                $stokFisik.removeClass('text-muted').addClass('text-success');
                $stokFisik.text(data.stok_fisik_cek);
                
                // Update selisih
                const $selisih = $card.find('.col-4 h5').eq(2);
                if (data.selisih === 0) {
                    $selisih.removeClass('text-muted text-danger').addClass('text-success').text('0');
                } else {
                    $selisih.removeClass('text-muted text-success').addClass('text-danger');
                    $selisih.text(data.selisih > 0 ? `+${data.selisih}` : data.selisih);
                }
                
                // Update button
                const $button = $card.find('.btn-primary, .btn-outline-secondary');
                $button.removeClass('btn-primary').addClass('btn-outline-secondary');
                $button.html('<i class="bi bi-pencil me-1"></i>Edit');
                
                // Update/add footer info
                let footerHtml = `<small class="text-muted">
                    <i class="bi bi-calendar me-1"></i>${data.tgl_cek_bulanan} - ${data.dicek_oleh}
                </small>`;
                
                if (data.keterangan_cek) {
                    footerHtml += `<div class="mt-1">
                        <small class="text-muted"><strong>Keterangan:</strong> ${data.keterangan_cek}</small>
                    </div>`;
                }
                
                let $footer = $card.find('.border-top');
                if ($footer.length === 0) {
                    $card.find('.card-body').append(`<div class="mt-2 pt-2 border-top">${footerHtml}</div>`);
                } else {
                    $footer.html(footerHtml);
                }
                
                $card.removeClass('updating');
                
                // Success animation
                $card.addClass('just-updated');
                setTimeout(() => $card.removeClass('just-updated'), 2000);
                
            }, 300);
        }

        function batchCheck() {
            Swal.fire({
                title: 'Batch Check',
                html: `
                    <div class="text-start">
                        <p>Pilih aksi untuk item yang belum dicek:</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="batchAction" id="markAllMatch" value="match">
                            <label class="form-check-label" for="markAllMatch">
                                Tandai semua sebagai SESUAI (stok fisik = stok sistem)
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="batchAction" id="customBatch" value="custom">
                            <label class="form-check-label" for="customBatch">
                                Atur manual per item
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const selectedAction = document.querySelector('input[name="batchAction"]:checked');
                    if (!selectedAction) {
                        Swal.showValidationMessage('Pilih salah satu aksi');
                        return false;
                    }
                    return selectedAction.value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value === 'match') {
                        executeBatchMatch();
                    } else {
                        showCustomBatchModal();
                    }
                }
            });
        }

        function executeBatchMatch() {
            Swal.fire({
                title: 'Konfirmasi Batch Check',
                text: 'Akan menandai semua item yang belum dicek sebagai SESUAI. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memproses batch check',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    $.ajax({
                        url: baseUrl + '/batch-mark-match',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Gagal memproses batch', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Terjadi kesalahan saat memproses batch', 'error');
                        }
                    });
                }
            });
        }

        function showCustomBatchModal() {
            // Get unchecked items from current page
            const uncheckedItems = [];
            $('.item-card').each(function() {
                const card = $(this);
                if (!card.hasClass('checked-sesuai') && !card.hasClass('checked-tidak-sesuai')) {
                    const itemId = card.data('item-id');
                    const itemName = card.find('.card-title').text().trim();
                    const itemCode = card.find('.text-muted.small').text().trim();
                    const divisi = card.find('.text-muted i.bi-building').parent().text().trim();
                    const stokSistem = card.find('.text-primary').text().trim();
                    
                    uncheckedItems.push({
                        id: itemId,
                        nama: itemName,
                        kode: itemCode,
                        divisi: divisi,
                        stok_sistem: stokSistem
                    });
                }
            });
            
            if (uncheckedItems.length === 0) {
                Swal.fire('Info', 'Tidak ada item yang belum dicek di halaman ini', 'info');
                return;
            }
            
            let html = `
                <div class="text-start">
                    <p>Atur stok fisik untuk setiap item:</p>
                    <div style="max-height: 400px; overflow-y: auto;">
            `;
            
            uncheckedItems.forEach(item => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            <h6 class="mb-1">${item.nama}</h6>
                            <small class="text-muted">${item.kode} - ${item.divisi}</small>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <label class="form-label small">Stok Sistem</label>
                                    <input type="text" class="form-control form-control-sm" value="${item.stok_sistem}" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Stok Fisik</label>
                                    <input type="number" class="form-control form-control-sm batch-stok-fisik" 
                                        data-item-id="${item.id}" min="0" value="${item.stok_sistem}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Keterangan Umum (Opsional)</label>
                        <textarea class="form-control" id="batchKeterangan" rows="2" placeholder="Keterangan untuk semua item..."></textarea>
                    </div>
                </div>
            `;
            
            Swal.fire({
                title: 'Custom Batch Check',
                html: html,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: 'Simpan Semua',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const batchData = [];
                    const keteranganUmum = document.getElementById('batchKeterangan').value;
                    
                    document.querySelectorAll('.batch-stok-fisik').forEach(input => {
                        if (!input.value) {
                            Swal.showValidationMessage('Semua stok fisik harus diisi');
                            return false;
                        }
                        
                        batchData.push({
                            id: input.dataset.itemId,
                            stok_fisik: parseInt(input.value),
                            keterangan: keteranganUmum
                        });
                    });
                    
                    return batchData;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeCustomBatch(result.value);
                }
            });
        }

        function executeCustomBatch(batchData) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menyimpan data batch',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });
            
            $.ajax({
                url: baseUrl + '/batch-update-cek-bulanan',
                method: 'POST',
                data: {
                    batch_data: JSON.stringify(batchData)
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Update setiap card secara real-time
                        response.data.forEach(item => {
                            updateItemCardRealtime(item);
                        });
                        
                        // Update statistics
                        setTimeout(() => updateStatisticsOnly(), 1000);
                        
                        const stats = response.statistics || {};
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `
                                <p>Batch check berhasil diproses:</p>
                                <ul class="list-unstyled">
                                    <li><i class="bi bi-check-circle text-success"></i> ${stats.sesuai || 0} item sesuai</li>
                                    <li><i class="bi bi-exclamation-triangle text-warning"></i> ${stats.tidak_sesuai || 0} item tidak sesuai</li>
                                </ul>
                            `,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Gagal memproses batch', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat memproses batch', 'error');
                }
            });
        }

        function exportData() {
            const currentUrl = new URL(window.location.href);
            const exportUrl = baseUrl + '/export-cek-bulanan?' + currentUrl.searchParams.toString();
            
            Swal.fire({
                title: 'Export Data',
                text: 'Memproses export...',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 2000,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create download link
            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = `cek-bulanan-${new Date().toISOString().split('T')[0]}.csv`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Berhasil!',
                    text: 'File CSV telah didownload',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 2000);
        }

        function quickCheckAll() {
            const uncheckedCount = $('.item-card:not(.checked-sesuai):not(.checked-tidak-sesuai)').length;
            
            if (uncheckedCount === 0) {
                Swal.fire('Info', 'Semua item di halaman ini sudah dicek', 'info');
                return;
            }
            
            Swal.fire({
                title: 'Quick Check All',
                text: `Tandai ${uncheckedCount} item yang belum dicek sebagai SESUAI?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tandai Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeBatchMatch();
                }
            });
        }

        function refreshData() {
            Swal.fire({
                title: 'Refresh Data',
                text: 'Memuat ulang data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                timer: 1000,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        function showHelp() {
            Swal.fire({
                title: 'Bantuan Cek Bulanan',
                html: `
                    <div class="text-start">
                        <h6>Cara Menggunakan:</h6>
                        <ol>
                            <li><strong>Filter Data:</strong> Gunakan filter divisi, status, atau pencarian untuk mempersempit data</li>
                            <li><strong>Cek Individual:</strong> Klik tombol "Cek" pada setiap item untuk input stok fisik</li>
                            <li><strong>Batch Check:</strong> Gunakan tombol "Batch Check" untuk memproses banyak item sekaligus</li>
                            <li><strong>Export:</strong> Download data hasil cek dalam format CSV</li>
                        </ol>
                        
                        <h6 class="mt-3">Status:</h6>
                        <ul>
                            <li><span class="badge bg-warning">Belum Dicek</span> - Item belum diverifikasi</li>
                            <li><span class="badge bg-success">Sesuai</span> - Stok fisik = stok sistem</li>
                            <li><span class="badge bg-danger">Tidak Sesuai</span> - Ada selisih stok</li>
                        </ul>
                    </div>
                `,
                width: '500px',
                confirmButtonText: 'Mengerti'
            });
        }
        

        function showLoading(show) {
            if (show) {
                $('#itemsContainer').hide();
                $('#loadingIndicator').show();
            } else {
                $('#loadingIndicator').hide();
                $('#itemsContainer').show();
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        setInterval(() => {
            updateStatistics();
        }, 300000);

        function updateStatistics() {
            $.ajax({
                url: baseUrl + '/get-realtime-stats',
                method: 'GET',
                data: {
                    bulan: $('#filterBulan').val(),
                    tahun: $('#filterTahun').val(),
                    divisi_id: $('#filterDivisi').val()
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#totalItems').text(data.total || 0);
                        $('#checkedItems').text(data.sudah_dicek || 0);
                        $('#uncheckedItems').text(data.belum_dicek || 0);
                        $('#matchItems').text(data.sesuai || 0);
                        $('#mismatchItems').text(data.tidak_sesuai || 0);
                        $('#progressText').text((data.progress_percentage || 0) + '%');
                        
                        // Update progress circle
                        const circumference = 2 * Math.PI * 25;
                        const offset = circumference - ((data.progress_percentage || 0) / 100) * circumference;
                        $('#progressCircle').css('stroke-dashoffset', offset);
                    }
                },
                error: function(xhr) {
                    console.error('Failed to update statistics:', xhr);
                }
            });
        }
        
        $(window).on('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });

    </script>
@endpush

@endsection