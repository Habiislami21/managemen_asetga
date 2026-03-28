{{-- resources/views/admin/ajuan-rutin.blade.php --}}
@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <style>
        .modal-lg {
            max-width: 90%;
        }
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }
        .bg-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .bg-approved, .bg-disetujui {
            background-color: #198754;
            color: white;
        }
        .bg-rejected, .bg-buat-ulang {
            background-color: #dc3545;
            color: white;
        }
        .action-icon {
            cursor: pointer;
            margin: 0 5px;
            font-size: 1.2rem;
        }
        .action-icon.view {
            color: #0dcaf0;
        }
        .action-icon.approve {
            color: #198754;
        }
        .action-icon.reject {
            color: #dc3545;
        }
        .action-icon.pending {
            color: #ffc107;
        }
        .approval-info {
            font-size: 0.8rem;
            margin-top: 5px;
            font-style: italic;
            color: #6c757d;
        }
        .action-icon.export {
            color: #28a745;
        }
        .filter-container {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .month-badge {
            font-size: 0.75em;
            vertical-align: middle;
            background: #6c757d;
        }
        .statistics-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
        }
        
        .statistics-section .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s ease;
        }
        
        .statistics-section .card:hover {
            transform: translateY(-5px);
        }
        
        .statistics-section .display-6 {
            font-size: 1.8rem;
            font-weight: 500;
        }
        
        .statistics-section .card-title {
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }
        
        .statistics-section .card-text {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            opacity: 0.8;
        }

        /* Hover effect for the table rows */
        .statistics-section .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        /* Animation for chart loading */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .statistics-section canvas {
            animation: fadeIn 1s ease-in-out;
        }

        .action-icon.export {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-icon.export:hover:not(.disabled) {
            transform: scale(1.1);
            color: #1e7e34 !important;
        }

        /* Class disabled - pastikan pointer-events diatur dengan jelas */
        .action-icon.export.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
            color: #6c757d !important;
        }

        /* Tombol yang aktif (disetujui) */
        .action-icon.export:not(.disabled) {
            opacity: 1;
            cursor: pointer;
            pointer-events: auto;
            color: #28a745;
        }

        /* Animasi pulse untuk tombol yang baru diaktifkan */
        @keyframes exportPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .action-icon.export.newly-enabled {
            animation: exportPulse 0.6s ease-in-out;
        }

        /* Hover effect untuk tombol aktif */
        .action-icon.export.hover-enabled {
            transform: scale(1.05);
            color: #1e7e34 !important;
        }

        .action-icon.delete {
            color: #dc3545;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-icon.delete:hover {
            transform: scale(1.1);
            color: #bd2130;
        }

        /* Style untuk division info alert */
        .division-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        
        .division-info .alert-heading {
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #f39c12;
            color: #856404;
        }

        .alert-warning .alert-heading {
            border-bottom: 1px solid rgba(133, 100, 4, 0.2);
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
            color: #d68910;
        }

        .alert-warning .fas.fa-exclamation-triangle {
            color: #f39c12;
        }

        .alert-warning .fas.fa-info-circle {
            color: #d68910;
        }
        .division-info-pj {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .division-info-pj .alert-heading {
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Style khusus untuk info box PJ Divisi */
        .pj-info-box {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .pj-info-box .alert-heading {
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Style untuk disabled actions pada PJ Divisi */
        .action-icon.disabled-pj {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Progress tracker styles */
        .progress-tracker {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .progress-step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .progress-step-icon.pending {
            background: #ffc107;
            color: white;
        }

        .progress-step-icon.approved {
            background: #198754;
            color: white;
        }

        .progress-step-icon.rejected {
            background: #dc3545;
            color: white;
        }

        .progress-step-content h6 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .progress-step-content small {
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
@php
$isAdmin = Auth::user()->role === 'admin' || Auth::user()->is_admin === 1;
$isGA = Auth::user()->role === 'ga' || Auth::user()->is_ga === 1;
$isAset = Auth::user()->role === 'aset';
$isKabag = Auth::user()->role === 'kabag';
$isPjDivisi = Auth::user()->role === 'pj_divisi';
$currentUser = Auth::user();
@endphp
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0">
                    @if($isPjDivisi)
                        Progress Ajuan RutinS
                    @else
                        Daftar Ajuan Rutin
                    @endif
                    <span class="badge bg-info ms-2">
                        {{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }}
                    </span>
                </h3>
                @if($isKabag && $currentUser->divisi)
                    <small class="text-muted">
                        <i class="fas fa-building me-1"></i>
                        Menampilkan data untuk divisi: <strong>{{ $currentUser->divisi->divisi }}</strong>
                    </small>
                @elseif($isAdmin || $isGA || $isAset)
                    <small class="text-muted">
                        <i class="fas fa-globe me-1"></i>
                        Menampilkan data dari <strong>semua divisi</strong>
                    </small>
                @elseif($isPjDivisi && $currentUser->divisi)
                    <small class="text-muted">
                        <i class="fas fa-eye me-1"></i>
                        Memantau ajuan divisi: <strong>{{ $currentUser->divisi->divisi }}</strong>
                    </small>
                @endif
            </div>
            <div class="d-flex align-items-center">
                <form id="monthFilterForm" class="d-flex align-items-center">
                    <label for="monthFilter" class="me-2">Filter Bulan:</label>
                    <select id="monthFilter" name="month" class="form-select form-select-sm me-2" style="width: auto;">
                        @foreach ($months as $key => $label)
                            <option value="{{ substr($key, 4, 2) }}" 
                                    data-year="{{ substr($key, 0, 4) }}" 
                                    {{ $selectedMonth == substr($key, 4, 2) && $selectedYear == substr($key, 0, 4) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="year" id="yearFilter" value="{{ $selectedYear }}">
                    <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                </form>
            </div>
        </div>
        <div class="card-body p-3">
            <div id="alertContainer"></div>
            {{-- Info Alert khusus untuk PJ Divisi --}}
            @if($isPjDivisi && $currentUser->divisi)
                <div class="alert pj-info-box alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-chart-line me-2"></i>
                        Mode Pemantauan Ajuan
                    </h6>
                    <p class="mb-1">
                        Selamat datang, <strong>{{ $currentUser->name }}</strong>! Anda dapat memantau progress ajuan rutin untuk divisi <strong>{{ $currentUser->divisi->divisi }}</strong>.
                    </p>
                    <small class="opacity-75">
                        <i class="fas fa-info-circle me-1"></i>
                        Anda dapat melihat detail dan status setiap ajuan, namun tidak dapat mengubah atau menghapus data.
                    </small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{-- Division Info Alert for Kabag --}}
            @if($isKabag && $currentUser->divisi)
                <div class="alert division-info alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Akses Data
                    </h6>
                    <p class="mb-1">
                        Anda sedang melihat data ajuan rutin khusus untuk divisi <strong>{{ $currentUser->divisi->divisi }}</strong>.
                    </p>
                    <small class="opacity-75">
                        <i class="fas fa-shield-alt me-1"></i>
                        Data dari divisi lain tidak akan ditampilkan sesuai kebijakan akses sistem.
                    </small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(($isAdmin || $isKabag) && !$isPjDivisi)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Kebijakan Persetujuan Ajuan
                </h6>
                <p class="mb-1">
                    <strong>Aturan Baru:</strong> Setiap divisi hanya dapat memiliki <strong>satu ajuan yang disetujui</strong> per bulan.
                </p>
                <small class="opacity-75">
                    <i class="fas fa-info-circle me-1"></i>
                    Jika Anda menyetujui ajuan baru, ajuan yang sebelumnya disetujui dari divisi yang sama akan secara otomatis diubah statusnya menjadi <strong>"buat ulang"</strong>.
                </small>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="table-responsive">
                <table id="myTable" class="table table-striped display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama SPA</th>
                            <th>Divisi</th>
                            <th>Tanggal Ajuan</th>
                            <th>Total Ajuan</th>
                            <th>Status</th>
                            <th>Diproses Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ajuanList as $index => $ajuan)
                        @php 
                            $tanggalAjuan = Carbon\Carbon::parse($ajuan->tanggal_ajuan);
                            $uniqueId = isset($ajuan->unique_id) ? $ajuan->unique_id : 
                                        str_replace(' ', '_', $ajuan->nama_spa) . '_' . 
                                        ($ajuan->divisi_id ?? 'unknown') . '_' . 
                                        $tanggalAjuan->format('YmdHis');
                        @endphp
                        <tr id="row-{{ $uniqueId }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ajuan->nama_spa }}</td>
                            <td>{{ $ajuan->nama_divisi ?? 'Divisi tidak tersedia' }}</td>
                            <td>{{ date('d-m-Y', strtotime($ajuan->tanggal_ajuan)) }}</td>
                            <td>Rp {{ number_format($ajuan->total_ajuan, 0, ',', '.') }}</td>
                            <td>
                                <span id="status-badge-{{ $uniqueId }}" 
                                      class="badge {{ $ajuan->status == 'disetujui' ? 'bg-approved' : ($ajuan->status == 'buat ulang' ? 'bg-rejected' : 'bg-pending') }}">
                                    {{ ucfirst($ajuan->status) }}
                                </span>
                            </td>
                            <td id="approved-by-{{ $uniqueId }}">
                                @if($ajuan->status != 'pending' && $ajuan->approved_by_name)
                                    {{ $ajuan->approved_by_name }}
                                    @if($ajuan->approved_at)
                                        <div class="approval-info">
                                            {{ date('d-m-Y H:i', strtotime($ajuan->approved_at)) }}
                                        </div>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Icon untuk melihat detail - PERBAIKAN: Pastikan uniqueId konsisten -->
                                    <i class="fas fa-eye action-icon view" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Lihat Detail Ajuan"
                                        onclick="openDetailModal('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', '{{ $ajuan->tanggal_ajuan }}')"
                                        style="cursor: pointer;"></i>
                                    
                                    @if(!$isPjDivisi)
                                        <!-- Icon untuk mengubah status - Hanya untuk Admin, GA, Kabag -->
                                        <i class="fas fa-check-circle action-icon approve" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Setujui Ajuan"
                                        onclick="updateStatus('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', 'disetujui', '{{ $ajuan->tanggal_ajuan }}', false, {{ $ajuan->divisi_id ?? 'null' }})"
                                        style="cursor: pointer;"></i>
                                        
                                        <i class="fas fa-times-circle action-icon reject" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Ajuan Perlu Dibuat Ulang"
                                        onclick="updateStatus('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', 'buat ulang', '{{ $ajuan->tanggal_ajuan }}', false, {{ $ajuan->divisi_id ?? 'null' }})"
                                        style="cursor: pointer;"></i>
                                        
                                        <i class="fas fa-clock action-icon pending" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Ubah Status Menjadi Pending"
                                        onclick="updateStatus('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', 'pending', '{{ $ajuan->tanggal_ajuan }}', false, {{ $ajuan->divisi_id ?? 'null' }})"
                                        style="cursor: pointer;"></i>

                                        <!-- Icon untuk export ajuan -->
                                        <i class="fas fa-file-export action-icon export{{ $ajuan->status == 'disetujui' ? '' : ' disabled' }}" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-title="{{ $ajuan->status == 'disetujui' ? 'Export Ajuan' : 'Hanya ajuan yang disetujui yang dapat diekspor' }}"
                                            data-nama-spa="{{ $ajuan->nama_spa }}"
                                            data-tanggal-ajuan="{{ date('Y-m-d', strtotime($ajuan->tanggal_ajuan)) }}"
                                            data-divisi-id="{{ $ajuan->divisi_id ?? '' }}"
                                            style="color: {{ $ajuan->status == 'disetujui' ? '#28a745' : '#6c757d' }}; 
                                                opacity: {{ $ajuan->status == 'disetujui' ? '1' : '0.5' }}; 
                                                cursor: {{ $ajuan->status == 'disetujui' ? 'pointer' : 'not-allowed' }};">
                                        </i>

                                        <!-- Icon untuk menghapus ajuan (hanya untuk admin dan GA) -->
                                        @if($isAdmin || $isGA)
                                        <i class="fas fa-trash-alt action-icon delete" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-title="Hapus Ajuan"
                                            style="color: #dc3545; margin-left: 5px; cursor: pointer;"
                                            onclick="deleteAjuan('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', '{{ $ajuan->tanggal_ajuan }}', {{ $ajuan->divisi_id ?? 'null' }})"></i>
                                        @endif
                                    @else
                                        <!-- Untuk PJ Divisi, tampilkan icon disabled -->
                                        <i class="fas fa-check-circle action-icon approve disabled-pj" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Anda tidak memiliki akses untuk mengubah status"></i>
                                        
                                        <i class="fas fa-times-circle action-icon reject disabled-pj" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Anda tidak memiliki akses untuk mengubah status"></i>
                                        
                                        <i class="fas fa-clock action-icon pending disabled-pj" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Anda tidak memiliki akses untuk mengubah status"></i>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                @if($isKabag || $isPjDivisi)
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <h5>Tidak ada data ajuan rutin</h5>
                                        <p>Belum ada ajuan rutin untuk divisi {{ $currentUser->divisi->divisi ?? 'Anda' }} pada bulan ini.</p>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <h5>Tidak ada data ajuan rutin</h5>
                                        <p>Belum ada ajuan rutin yang tersedia untuk bulan ini.</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail untuk setiap SPA -->
@foreach ($ajuanList as $ajuan)
@php
    // PERBAIKAN: Gunakan unique_id yang sama dengan yang ada di tabel
    $uniqueId = isset($ajuan->unique_id) ? $ajuan->unique_id : 
                str_replace(' ', '_', $ajuan->nama_spa) . '_' . 
                ($ajuan->divisi_id ?? 'unknown') . '_' . 
                Carbon\Carbon::parse($ajuan->tanggal_ajuan)->format('YmdHis');
@endphp
<div class="modal fade" id="detailModal{{ $uniqueId }}" tabindex="-1" 
    aria-labelledby="detailModalLabel{{ $uniqueId }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $uniqueId }}">
                    Detail Ajuan: {{ $ajuan->nama_spa }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($isPjDivisi)
                <!-- Progress Tracker untuk PJ Divisi -->
                <div class="progress-tracker mb-4">
                    <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>Progress Status Ajuan</h6>
                    <div class="progress-step">
                        <div class="progress-step-icon {{ $ajuan->status == 'pending' ? 'pending' : '' }}">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="progress-step-content">
                            <h6>Pending</h6>
                            <small>Ajuan sedang menunggu review</small>
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-step-icon {{ $ajuan->status == 'disetujui' ? 'approved' : '' }}">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="progress-step-content">
                            <h6>Disetujui</h6>
                            <small>Ajuan telah disetujui dan dapat diproses</small>
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="progress-step-icon {{ $ajuan->status == 'buat ulang' ? 'rejected' : '' }}">
                            <i class="fas fa-redo"></i>
                        </div>
                        <div class="progress-step-content">
                            <h6>Buat Ulang</h6>
                            <small>Ajuan perlu diperbaiki atau dibuat ulang</small>
                        </div>
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Divisi:</strong> {{ $ajuan->nama_divisi }}<br>
                            <strong>Nomor Telepon:</strong> {{ $ajuan->nomor_telp }}<br>
                            <strong>Tanggal Ajuan:</strong> {{ date('d-m-Y', strtotime($ajuan->tanggal_ajuan)) }}<br>
                            <strong>Status:</strong> 
                            <span id="modal-status-badge-{{ $uniqueId }}" 
                                class="badge {{ $ajuan->status == 'disetujui' ? 'bg-approved' : ($ajuan->status == 'buat ulang' ? 'bg-rejected' : 'bg-pending') }}">
                                {{ ucfirst($ajuan->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div id="modal-approval-info-{{ $uniqueId }}">
                                @if($ajuan->status != 'pending' && $ajuan->approved_by_name)
                                    <strong>Diproses Oleh:</strong> {{ $ajuan->approved_by_name }}<br>
                                    @if($ajuan->approved_at)
                                        <strong>Tanggal Diproses:</strong> {{ date('d-m-Y H:i', strtotime($ajuan->approved_at)) }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang Ajuan</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $itemsForThisSPA = $detailAjuan->where('nama_spa', $ajuan->nama_spa)
                                                            ->where('created_at', $ajuan->tanggal_ajuan);
                                $grandTotal = 0;
                                $itemNo = 1;
                            @endphp
                            
                            @forelse ($itemsForThisSPA as $item)
                            <tr>
                                <td>{{ $itemNo++ }}</td>
                                <td>{{ $item->barang_ajuan }}</td>
                                <td>{{ $item->kategori_barang }}</td>
                                <td>{{ $item->banyak_barang }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>{{ $item->keterangan }}</td>
                            </tr>
                            @php
                                $grandTotal += $item->total;
                            @endphp
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data barang</td>
                            </tr>
                            @endforelse
                            
                            <!-- Row untuk total -->
                            <tr class="table-active fw-bold">
                                <td colspan="6" class="text-end">TOTAL</td>
                                <td>Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @php
                $modalTanggalAjuan = Carbon\Carbon::parse($ajuan->tanggal_ajuan);
            @endphp

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @if(!$isPjDivisi)
                    <!-- Tombol aksi hanya untuk non PJ Divisi -->
                    <div class="d-flex">
                        <button type="button" class="btn btn-success me-2" 
                                onclick="updateStatus('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', 'disetujui', '{{ $modalTanggalAjuan->format('Y-m-d H:i:s') }}', true, {{ $ajuan->divisi_id ?? 'null' }})">
                            <i class="fas fa-check-circle"></i> Setujui
                        </button>
                        <button type="button" class="btn btn-danger me-2" 
                                onclick="updateStatus('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', 'buat ulang', '{{ $modalTanggalAjuan->format('Y-m-d H:i:s') }}', true, {{ $ajuan->divisi_id ?? 'null' }})">
                            <i class="fas fa-times-circle"></i> Buat Ulang
                        </button>
                        <button type="button" class="btn btn-warning" 
                                onclick="updateStatus('{{ $uniqueId }}', '{{ addslashes($ajuan->nama_spa) }}', 'pending', '{{ $modalTanggalAjuan->format('Y-m-d H:i:s') }}', true, {{ $ajuan->divisi_id ?? 'null' }})">
                            <i class="fas fa-clock"></i> Pending
                        </button>
                    </div>
                @endif
            </div>
                    </div>
    </div>
</div>
@endforeach


@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        // Di bagian awal script
        const isAdmin = {{ ($isAdmin || $isGA) ? 'true' : 'false' }};
        const isKabag = {{ $isKabag ? 'true' : 'false' }};
        const userDivision = '{{ $isKabag && $currentUser->divisi ? $currentUser->divisi->divisi : "" }}';
        
        $(document).ready(function () {
            $('#myTable').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                deferRender: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    zeroRecords: "Tidak ditemukan data yang sesuai",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
            
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });

        // Month filter functionality
        $(document).ready(function() {
            // Update year hidden input when month changes
            $('#monthFilter').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const year = selectedOption.data('year');
                $('#yearFilter').val(year);
            });
            
            // Submit form when filter button is clicked
            $('#monthFilterForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading indicator
                Swal.fire({
                    title: 'Memuat Data...',
                    text: 'Sedang mengambil data untuk bulan yang dipilih',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    timer: 1000,
                    timerProgressBar: true
                });
                
                // Submit the form
                this.submit();
            });
        });

        document.querySelectorAll('.action-icon').forEach(function(button) {
            button.addEventListener('click', function() {
                const status = button.classList.contains('approve') ? 'disetujui' :
                            button.classList.contains('reject') ? 'buat ulang' : 'pending';
                const namaSpa = button.getAttribute('data-nama_spa');
                const tanggalAjuan = button.getAttribute('data-tanggal_ajuan');

                // Mengirim data ke server menggunakan AJAX
                fetch('/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        nama_spa: namaSpa,
                        tanggal_ajuan: tanggalAjuan,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);  // Tampilkan pesan sukses
                        // Perbarui tampilan jika diperlukan
                    } else {
                        alert('Terjadi kesalahan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });

        
        // Toast notification function
        function showToast(icon, title, text) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            
            Toast.fire({
                icon: icon,
                title: text
            });
        }
        
        // Function to open modal
        function openDetailModal(uniqueId, namaSpa, tanggalAjuan) {
            console.log('Opening modal for:', uniqueId, namaSpa, tanggalAjuan); // Debug log
            const modalId = `#detailModal${uniqueId}`;
            console.log('Modal ID:', modalId); // Debug log
            
            const modalElement = document.querySelector(modalId);
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal element not found:', modalId);
                // Fallback: coba cari dengan ID yang lebih sederhana
                const fallbackModalId = `detailModal${uniqueId}`;
                const fallbackElement = document.getElementById(fallbackModalId);
                if (fallbackElement) {
                    const modal = new bootstrap.Modal(fallbackElement);
                    modal.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Modal detail tidak ditemukan. Silakan refresh halaman.',
                        showConfirmButton: true
                    });
                }
            }
        }
        
        function updateStatus(uniqueId, namaSpa, status, tanggalAjuan, fromModal = false, divisiId = null) {
            console.log('Update status called with:', {
                uniqueId,
                namaSpa,
                status,
                tanggalAjuan,
                fromModal,
                divisiId
            });
            
            // Validasi divisi_id
            if (!divisiId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Data divisi tidak valid. Silakan refresh halaman dan coba lagi.',
                    showConfirmButton: true
                });
                return;
            }
            
            let processedDate = tanggalAjuan;
            
            console.log('Original tanggal_ajuan:', tanggalAjuan);
            console.log('Processed date:', processedDate);
            
            // Add division check for kabag users
            let confirmText = `Apakah Anda yakin ingin mengubah status ajuan "${namaSpa}" menjadi "${status}"?`;
            if (isKabag && userDivision) {
                confirmText += `\n\nPerubahan ini hanya akan diterapkan pada data divisi ${userDivision}.`;
            }
            
            // PERBAIKAN: Tambahan peringatan khusus untuk status disetujui
            if (status === 'disetujui') {
                confirmText += `\n\nCATATAN: Jika divisi ini sudah memiliki ajuan yang disetujui di bulan ini, ajuan tersebut akan diubah statusnya menjadi "buat ulang".`;
            }
            
            Swal.fire({
                title: 'Konfirmasi Perubahan Status',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah Status!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading dengan progress bar
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang mengubah status ajuan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        timerProgressBar: true
                    });
                    
                    $.ajax({
                        url: '{{ route("ajuan-rutin.update-status-batch") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nama_spa: namaSpa,
                            tanggal_ajuan: processedDate,
                            status: status,
                            divisi_id: divisiId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Close loading
                                Swal.close();
                                
                                // PERBAIKAN: Update UI secara real-time tanpa reload
                                if (response.reverted_previous) {
                                    // Cari dan update ajuan yang dikembalikan ke buat ulang
                                    updateRevertedAjuan(response.reverted_previous, divisiId);
                                    
                                    // Show notification about reverted ajuan
                                    showToast('info', 'Informasi', `Ajuan "${response.reverted_previous}" diubah ke status "buat ulang"`);
                                }
                                
                                // Update ajuan yang diminta
                                updateElementsById(uniqueId, status, response);
                                
                                // Add visual feedback
                                addVisualFeedback(uniqueId, status);
                                
                                // Show success message
                                showToast('success', 'Berhasil!', response.message);
                                
                                if (fromModal) {
                                    setTimeout(() => {
                                        $(`#detailModal${uniqueId}`).modal('hide');
                                    }, 1500);
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr.responseText);
                            Swal.close();
                            
                            let errorMessage = 'Terjadi kesalahan saat mengubah status. Silakan coba lagi.';
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                if (errorData && errorData.message) {
                                    errorMessage = errorData.message;
                                }
                            } catch (e) {
                                // Use default message
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        }

        function updateRevertedAjuan(revertedNamaSpa, divisiId) {
            // Cari row yang nama_spa nya sama dengan yang dikembalikan
            $('table tbody tr').each(function() {
                const row = $(this);
                const namaSpaCell = row.find('td:nth-child(2)').text().trim();
                
                if (namaSpaCell === revertedNamaSpa) {
                    // Ambil unique ID dari row ini
                    const rowId = row.attr('id');
                    if (rowId) {
                        const uniqueId = rowId.replace('row-', '');
                        
                        // Update status badge
                        const statusBadge = row.find(`#status-badge-${uniqueId}`);
                        statusBadge
                            .removeClass('bg-approved bg-pending')
                            .addClass('bg-rejected')
                            .text('Buat ulang')
                            .css('transform', 'scale(1.1)')
                            .animate({ transform: 'scale(1)' }, 300);
                        
                        // Update approval info (kosongkan karena status buat ulang)
                        const approvalElement = row.find(`#approved-by-${uniqueId}`);
                        approvalElement.fadeOut(200, function() {
                            $(this).html('-').fadeIn(200);
                        });
                        
                        // Update modal elements if exists
                        const modalStatusBadge = $(`#modal-status-badge-${uniqueId}`);
                        if (modalStatusBadge.length) {
                            modalStatusBadge
                                .removeClass('bg-approved bg-pending')
                                .addClass('bg-rejected')
                                .text('Buat ulang');
                        }

                        const modalApprovalInfo = $(`#modal-approval-info-${uniqueId}`);
                        if (modalApprovalInfo.length) {
                            modalApprovalInfo.fadeOut(200, function() {
                                $(this).html('').fadeIn(200);
                            });
                        }
                        
                        // Update tombol export (disable karena bukan disetujui)
                        const exportButton = row.find('.action-icon.export');
                        if (exportButton.length > 0) {
                            exportButton
                                .css({
                                    'opacity': '0.5',
                                    'cursor': 'not-allowed',
                                    'color': '#6c757d',
                                    'pointer-events': 'none'
                                })
                                .addClass('disabled')
                                .attr('data-bs-title', 'Hanya ajuan yang disetujui yang dapat diekspor');
                            
                            // Update tooltip
                            updateTooltip(exportButton[0], 'Hanya ajuan yang disetujui yang dapat diekspor');
                        }
                        
                        // Add visual feedback untuk row yang dikembalikan
                        addVisualFeedback(uniqueId, 'buat ulang');
                        
                        // Break loop karena sudah ketemu
                        return false;
                    }
                }
            });
        }

        function deleteAjuan(uniqueId, namaSpa, tanggalAjuan, divisiId = null) {
            if (!isAdmin) {
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak',
                    text: 'Anda tidak memiliki izin untuk menghapus ajuan',
                    showConfirmButton: true
                });
                return;
            }

            // Validasi divisi_id
            if (!divisiId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Data divisi tidak valid. Silakan refresh halaman dan coba lagi.',
                    showConfirmButton: true
                });
                return;
            }

            // PERBAIKAN: Gunakan tanggal_ajuan yang sama seperti updateStatus
            let processedDate = tanggalAjuan;
            
            console.log('Delete ajuan called with:', {
                uniqueId,
                namaSpa,
                tanggalAjuan: processedDate,
                divisiId
            });

            Swal.fire({
                title: 'Konfirmasi Hapus Ajuan',
                text: `Apakah Anda yakin ingin menghapus ajuan "${namaSpa}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang menghapus ajuan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        timerProgressBar: true
                    });
                    
                    $.ajax({
                        url: '{{ route("ajuan-rutin.delete-batch") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            nama_spa: namaSpa,
                            tanggal_ajuan: processedDate,  // PERBAIKAN: Gunakan processedDate
                            divisi_id: divisiId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Close loading
                                Swal.close();
                                
                                // Remove row from table
                                $(`#row-${uniqueId}`).fadeOut(300, function() {
                                    $(this).remove();
                                    
                                    // Check if table is now empty
                                    if ($('#myTable tbody tr').length === 0) {
                                        $('#myTable tbody').append(`
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                        <h5>Tidak ada data ajuan rutin</h5>
                                                        <p>Semua data telah dihapus atau belum ada ajuan untuk bulan ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        `);
                                    }
                                });
                                
                                // Show success message
                                showToast('success', 'Berhasil!', response.message);
                                
                                // Reload halaman setelah 1.5 detik
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr.responseText);
                            Swal.close();
                            
                            let errorMessage = 'Terjadi kesalahan saat menghapus ajuan. Silakan coba lagi.';
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                if (errorData && errorData.message) {
                                    errorMessage = errorData.message;
                                }
                            } catch (e) {
                                // Use default message
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        }

        function updateElementsById(uniqueId, status, response) {
            let statusClassMap = {
                'disetujui': 'bg-approved',
                'buat ulang': 'bg-rejected',
                'pending': 'bg-pending'
            };
            
            const formattedStatus = status.charAt(0).toUpperCase() + status.slice(1);
            
            const statusBadge = $(`#status-badge-${uniqueId}`);
            statusBadge
                .removeClass('bg-approved bg-rejected bg-pending')
                .addClass(statusClassMap[status])
                .text(formattedStatus)
                .css('transform', 'scale(1.1)')
                .animate({ transform: 'scale(1)' }, 300);
            
            // Update approval info
            const approvalElement = $(`#approved-by-${uniqueId}`);
            if (status === 'pending') {
                approvalElement.fadeOut(200, function() {
                    $(this).html('-').fadeIn(200);
                });
            } else {
                approvalElement.fadeOut(200, function() {
                    $(this).html(`
                        ${response.approved_by}
                        <div class="approval-info">
                            ${response.approved_at}
                        </div>
                    `).fadeIn(200);
                });
            }
            
            // Update modal elements if exists
            const modalStatusBadge = $(`#modal-status-badge-${uniqueId}`);
            if (modalStatusBadge.length) {
                modalStatusBadge
                    .removeClass('bg-approved bg-rejected bg-pending')
                    .addClass(statusClassMap[status])
                    .text(formattedStatus);
            }

            const modalApprovalInfo = $(`#modal-approval-info-${uniqueId}`);
            if (modalApprovalInfo.length) {
                if (status === 'pending') {
                    modalApprovalInfo.fadeOut(200, function() {
                        $(this).html('').fadeIn(200);
                    });
                } else {
                    modalApprovalInfo.fadeOut(200, function() {
                        $(this).html(`
                            <strong>Diproses Oleh:</strong> ${response.approved_by}<br>
                            <strong>Tanggal Diproses:</strong> ${response.approved_at}
                        `).fadeIn(200);
                    });
                }
            }

            // Update tombol export
            const exportButton = $(`#row-${uniqueId} .action-icon.export`);
            
            if (exportButton.length > 0) {
                exportButton.removeClass('disabled newly-enabled');
                
                if (status === 'disetujui') {
                    // Aktifkan tombol export
                    exportButton
                        .css({
                            'opacity': '1',
                            'cursor': 'pointer',
                            'color': '#28a745',
                            'pointer-events': 'auto'
                        })
                        .attr('data-bs-title', 'Export Ajuan')
                        .removeClass('disabled');
                    
                    // Update tooltip
                    updateTooltip(exportButton[0], 'Export Ajuan');
                    
                    // Animasi untuk menunjukkan tombol aktif
                    exportButton.addClass('newly-enabled');
                    setTimeout(() => {
                        exportButton.removeClass('newly-enabled');
                    }, 600);
                    
                } else {
                    // Nonaktifkan tombol export
                    exportButton
                        .css({
                            'opacity': '0.5',
                            'cursor': 'not-allowed',
                            'color': '#6c757d',
                            'pointer-events': 'none'
                        })
                        .addClass('disabled')
                        .attr('data-bs-title', 'Hanya ajuan yang disetujui yang dapat diekspor');
                    
                    // Update tooltip
                    updateTooltip(exportButton[0], 'Hanya ajuan yang disetujui yang dapat diekspor');
                }
            }
        }

        function updateTooltip(element, newTitle) {
            if (!element) return;
            
            const tooltip = bootstrap.Tooltip.getInstance(element);
            if (tooltip) {
                tooltip.dispose();
            }
            
            // Set new title dan create new tooltip
            element.setAttribute('data-bs-title', newTitle);
            new bootstrap.Tooltip(element);
        }

        function addVisualFeedback(uniqueId, status) {
            const row = $(`#row-${uniqueId}`);
            
            // Tambahkan highlight effect ke row
            let highlightColor = '#f8f9fa';
            if (status === 'disetujui') {
                highlightColor = '#d4edda'; // Light green
            } else if (status === 'buat ulang') {
                highlightColor = '#f8d7da'; // Light red
            } else if (status === 'pending') {
                highlightColor = '#fff3cd'; // Light yellow
            }
            
            const originalBg = row.css('background-color');
            row.css('background-color', highlightColor)
                .animate({ backgroundColor: originalBg }, 1500);
            
            // Tambahkan icon sesuai status
            let iconClass = '';
            let iconColor = '';
            
            if (status === 'disetujui') {
                iconClass = 'fas fa-check-circle';
                iconColor = 'text-success';
            } else if (status === 'buat ulang') {
                iconClass = 'fas fa-times-circle';
                iconColor = 'text-danger';
            } else if (status === 'pending') {
                iconClass = 'fas fa-clock';
                iconColor = 'text-warning';
            }
            
            if (iconClass) {
                const statusIcon = $(`<i class="${iconClass} ${iconColor} ms-2 temp-icon"></i>`);
                row.find('td:first').append(statusIcon);
                
                setTimeout(() => {
                    statusIcon.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 2500);
            }
        }
       
        function exportSingleSubmission(namaSpa, tanggalAjuan) {
            console.log('Export function called with:', namaSpa, tanggalAjuan);
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang memproses data ekspor. Mohon tunggu.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            let parsedDate = tanggalAjuan;
            if (typeof tanggalAjuan === 'string' && tanggalAjuan.includes('-')) {
                const parts = tanggalAjuan.split('-');
                if (parts.length === 3) {
                    if (parts[0].length === 4) {
                        parsedDate = tanggalAjuan;
                    } else {
                        parsedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }       
            }

            // Send AJAX request to export endpoint
            $.ajax({
                url: '{{ route("ajuan-rutin.export-approved") }}',
                type: 'GET',
                data: {
                    nama_spa: namaSpa,
                    tanggal_ajuan: parsedDate
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, xhr) {
                    const contentType = xhr.getResponseHeader('content-type');
                    if (contentType && contentType.indexOf('application/json') !== -1) {
                        const reader = new FileReader();
                        reader.onload = function() {
                            const responseText = reader.result;
                            const responseJson = JSON.parse(responseText);
                            
                            Swal.close();
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: responseJson.message || 'Terjadi kesalahan saat mengekspor data.'
                            });
                        };
                        reader.readAsText(response);
                        return;
                    }
                    
                    Swal.close();
                    
                    const url = window.URL.createObjectURL(new Blob([response]));
                    
                    let filename = `ajuan_${namaSpa.replace(/ /g, '_')}_${formatDate(new Date())}.xlsx`;
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }
                    
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    
                    window.URL.revokeObjectURL(url);
                    link.remove();
                    
                    showToast('success', 'Berhasil!', 'File berhasil diunduh');
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    
                    let errorMessage = 'Terjadi kesalahan saat mengekspor data.';
                    
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData && errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // If parsing fails, use default message
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }

        // Helper function to format date for filename
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${day}-${month}-${year}_${hours}-${minutes}-${seconds}`;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            // Data untuk chart
            const statusData = {
                labels: [
                    'Disetujui', 
                    'Pending', 
                    'Buat Ulang'
                ],
                datasets: [{
                    label: 'Jumlah Ajuan',
                    data: [
                        {{ isset($statistics['statusStats']['disetujui']) ? $statistics['statusStats']['disetujui']->count : 0 }},
                        {{ isset($statistics['statusStats']['pending']) ? $statistics['statusStats']['pending']->count : 0 }},
                        {{ isset($statistics['statusStats']['buat ulang']) ? $statistics['statusStats']['buat ulang']->count : 0 }}
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1
                }]
            };
            
            const categoryData = {
                labels: ['RTK', 'ATK'],
                datasets: [{
                    label: 'Jumlah Item',
                    data: [
                        {{ isset($statistics['categoryStats']['RTK']) ? $statistics['categoryStats']['RTK']->count : 0 }},
                        {{ isset($statistics['categoryStats']['ATK']) ? $statistics['categoryStats']['ATK']->count : 0 }}
                    ],
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: [
                        'rgb(13, 110, 253)',
                        'rgb(108, 117, 125)'
                    ],
                    borderWidth: 1
                }]
            };
            
            // Data untuk tren harian
            const dailyLabels = [
                @if(isset($statistics['dailyStats']) && count($statistics['dailyStats']) > 0)
                    @foreach($statistics['dailyStats'] as $day)
                        '{{ \Carbon\Carbon::parse($day->date)->format("d/m") }}',
                    @endforeach
                @endif
            ];
            
            const dailySubmissions = [
                @if(isset($statistics['dailyStats']) && count($statistics['dailyStats']) > 0)
                    @foreach($statistics['dailyStats'] as $day)
                        {{ $day->submissions }},
                    @endforeach
                @endif
            ];
            
            const dailyAmounts = [
                @if(isset($statistics['dailyStats']) && count($statistics['dailyStats']) > 0)
                    @foreach($statistics['dailyStats'] as $day)
                        {{ $day->amount }},
                    @endforeach
                @endif
            ];
            
            // Buat chart status
            new Chart(document.getElementById('statusChart'), {
                type: 'pie',
                data: statusData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.formattedValue + ' ajuan';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            
            // Buat chart kategori
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: categoryData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.formattedValue + ' item';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            
            // Hanya buat chart tren harian jika ada data
            if (dailyLabels.length > 0) {
                new Chart(document.getElementById('dailyTrendChart'), {
                    type: 'line',
                    data: {
                        labels: dailyLabels,
                        datasets: [
                            {
                                label: 'Jumlah Ajuan',
                                data: dailySubmissions,
                                borderColor: 'rgb(13, 110, 253)',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Total Nominal (dalam Rp Juta)',
                                data: dailyAmounts.map(amount => amount / 1000000),
                                borderColor: 'rgb(25, 135, 84)',
                                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 1) {
                                            label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw * 1000000);
                                        } else {
                                            label += context.formattedValue;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Jumlah Ajuan'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Total Nominal (Rp Juta)'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                            }
                        }
                    }
                });
            } else {
                document.getElementById('dailyTrendChart').parentNode.innerHTML = '<div class="text-center py-5"><p class="text-muted">Tidak ada data tren harian untuk bulan ini</p></div>';
            }
        });
    </script>
    <script>
      $(document).ready(function() {
        $(document).on('click', '.action-icon.export', function(e) {
            const button = $(this);
            const namaSpa = button.data('nama-spa');
            const tanggalAjuan = button.data('tanggal-ajuan');
            
            console.log('Export button clicked:', {
                button: button,
                namaSpa: namaSpa,
                tanggalAjuan: tanggalAjuan,
                hasDisabledClass: button.hasClass('disabled'),
                opacity: button.css('opacity'),
                pointerEvents: button.css('pointer-events')
            });
            
            if (button.hasClass('disabled') || parseFloat(button.css('opacity')) < 0.9) {
                e.preventDefault();
                e.stopPropagation();
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Mengekspor',
                    text: 'Hanya ajuan yang telah disetujui yang dapat diekspor.',
                    timer: 2000,
                    showConfirmButton: false
                });
                
                return false;
            }
            
            console.log('Proceeding with export for:', namaSpa);
            exportSingleSubmission(namaSpa, tanggalAjuan);
        });
    });
    </script>
@endpush
@endsection