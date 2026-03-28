@extends('layouts.master')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Mobile-first CSS */
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --secondary-color: #6c757d;
        }

        body {
            background-color: #f8f9fa;
            font-size: 14px;
        }

        .main-container {
            padding: 10px;
            max-width: 100%;
        }

        /* Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .filter-row {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #495057;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .btn-filter {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-filter .btn {
            flex: 1;
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        /* Stats Cards */
        .stats-section {
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .stat-card.total { border-left-color: var(--secondary-color); }
        .stat-card.pending { border-left-color: var(--warning-color); }
        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.danger { border-left-color: var(--danger-color); }

        .stat-card .number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .label {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .progress-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
        }

        /* Action Buttons */
        .action-section {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .action-buttons .btn {
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 10px;
        }

        /* Data Cards (Mobile View) */
        .data-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .item-card {
            position: relative;
            transition: all 0.3s ease;
        }

        .item-card.processing {
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(13, 110, 253, 0.3);
            border-left-color: var(--primary-color) !important;
        }

        .item-card.processing::after {
            content: "⚡";
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 16px;
            animation: pulse 1s infinite;
        }

        @keyframes checkSuccess {
            0% { 
                transform: scale(1); 
                background-color: transparent;
            }
            50% { 
                transform: scale(1.05); 
                background-color: rgba(25, 135, 84, 0.1);
                border-color: #198754;
            }
            100% { 
                transform: scale(1); 
                background-color: transparent;
            }
        }

        .item-card.success-animation {
            animation: checkSuccess 0.6s ease-out;
        }

        .item-card.check-success {
            animation: checkSuccess 0.6s ease-out;
        }

        @keyframes checkError {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { 
                transform: translateX(5px); 
                background-color: rgba(220, 53, 69, 0.1);
                border-color: #dc3545;
            }
            75% { transform: translateX(-5px); }
            100% { 
                transform: translateX(0); 
                background-color: transparent;
            }
        }

        .item-card.error-animation {
            animation: checkError 0.6s ease-out;
        }

        .table th {
            background: linear-gradient(135deg, #495057, #343a40);
            color: white;
            font-weight: 600;
            border: none;
            padding: 12px 8px;
        }

        .table td {
            vertical-align: middle;
            padding: 12px 8px;
        }

        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .item-card.checked { border-left-color: var(--success-color); }
        .item-card.unchecked { border-left-color: var(--warning-color); }
        .item-card.mismatch { border-left-color: var(--danger-color); }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .item-title {
            flex: 1;
        }

        .item-title h6 {
            margin: 0;
            font-size: 1rem;
            color: #212529;
        }

        .item-title small {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .item-checkbox {
            margin-left: 10px;
        }

        .item-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            text-align: center;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        @keyframes highlightChange {
            0% { 
                background-color: #fff3cd; 
                transform: scale(1.05); 
            }
            50% { 
                background-color: #ffeaa7; 
            }
            100% { 
                background-color: transparent; 
                transform: scale(1); 
            }
        }

        .info-item .value {
            font-weight: bold;
            color: var(--primary-color);
            transition: all 0.3s ease;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        .info-item .label {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .item-actions {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            align-items: center;
        }

        .stok-input {
            flex: 1;
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .stok-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            outline: none;
        }

        .table tbody tr.table-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
        }

        .item-card[style*="pointer-events: none"] {
            transform: scale(0.98);
            transition: all 0.3s ease;
        }

        .item-card:active:not([style*="pointer-events: none"]) {
            transform: scale(0.98);
            background-color: rgba(0, 0, 0, 0.05);
        }

        .check-btn:active:not(.loading) {
            transform: translateY(1px);
        }

        .stok-input:invalid {
            border-color: var(--danger-color);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .item-card.changed {
            border-left: 4px solid #ffc107 !important;
            background-color: rgba(255, 193, 7, 0.1);
            position: relative;
        }

        .item-card.changed::before {
            content: "●";
            color: #ffc107;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 12px;
            animation: pulse 1.5s infinite;
        }

        /* Animasi untuk perubahan data */
        .value-updated {
            animation: highlightChange 0.8s ease-out;
        }

        .check-buttons {
            display: flex;
            gap: 5px;
        }

        .check-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            color: white;
            background: var(--primary-color);
        }

        .check-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .check-btn:active {
            transform: translateY(0);
        }

        .reset-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 0.8rem;
        }

        .sistem-value {
            position: relative;
        }

        .sistem-value::after {
            content: "📊";
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 10px;
            opacity: 0.7;
        }

        .check-btn.success { background: var(--success-color); }
        .check-btn.danger { background: var(--danger-color); }
        .check-btn.warning { background: var(--warning-color); }

        .item-status {
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-success {
            background: #d1e7dd;
            color: #0a3622;
        }

        .status-danger {
            background: #f8d7da;
            color: #58151c;
        }

        /* Responsive Design */
        @media (min-width: 576px) {
            .main-container {
                padding: 20px;
            }

            .filter-row {
                flex-direction: row;
                align-items: end;
            }

            .filter-group {
                flex: 1;
            }

            .btn-filter {
                margin-top: 0;
                align-self: end;
            }

            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .action-buttons {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (min-width: 768px) {
            .stok-input:hover {
                border-color: #adb5bd;
            }
            
            .table tbody tr:hover .stok-input {
                border-color: var(--primary-color);
            }
            
            .check-btn:hover:not(.loading) {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
        }

        /* Loading State */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            font-size: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 1000;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .check-actions {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .stok-fisik-display[style*="italic"],
        .selisih-display[style*="italic"] {
            font-style: italic !important;
            opacity: 0.8 !important;
            position: relative;
        }

        .stok-fisik-display[style*="italic"]::before,
        .selisih-display[style*="italic"]::before {
            content: "👁️";
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 8px;
            opacity: 0.6;
        }

        .badge {
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stok-input.is-valid {
            border-color: var(--success-color);
            background-color: rgba(25, 135, 84, 0.05);
        }

        .stok-input.is-invalid {
            border-color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.05);
        }

        .check-btn.loading {
            pointer-events: none;
            opacity: 0.6;
            position: relative;
        }

        .check-btn.loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .stok-input.changed {
            border-color: #ffc107;
            background-color: rgba(255, 193, 7, 0.1);
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        @media (max-width: 576px) {
            .stok-input {
                font-size: 16px; /* Prevent zoom on iOS */
                min-height: 44px; /* Better touch target */
            }
            
            .check-btn {
                min-height: 44px;
                min-width: 44px;
            }
            
            .item-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .check-actions {
                width: 100%;
                justify-content: center;
            }
        }

        .swal2-popup {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .swal2-title {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .swal2-html-container {
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Status comparison table in SweetAlert */
        .status-comparison {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }

        .status-comparison .row {
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .status-comparison .row:last-child {
            border-bottom: none;
            font-weight: 600;
            background: white;
            margin: 5px -15px -15px;
            padding: 10px 15px;
            border-radius: 0 0 8px 8px;
        }
    </style>
@endpush

@section('header')
@endsection

@section('content')
<div class="main-container">
    <!-- Header -->
    <div class="page-header">
        <h1><i class="bi bi-clipboard-check"></i> Cek Stok Bulanan</h1>
        <small>Periode: {{ \Carbon\Carbon::createFromFormat('m', request('bulan', date('m')))->format('F') }} {{ request('tahun', date('Y')) }}</small>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h6 class="mb-3"><i class="bi bi-funnel"></i> Filter Data</h6>
        <form method="GET" action="{{ route('cek.bulanan') }}" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Divisi</label>
                    <select class="form-select" name="divisi_id" id="filterDivisi">
                        <option value="">Semua Divisi</option>
                        @foreach($divisis as $divisi)
                            <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                {{ $divisi->divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select class="form-select" name="status_cek" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="belum_dicek" {{ request('status_cek') == 'belum_dicek' ? 'selected' : '' }}>Belum Dicek</option>
                        <option value="sesuai" {{ request('status_cek') == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                        <option value="tidak_sesuai" {{ request('status_cek') == 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Bulan</label>
                    <select class="form-select" name="bulan" id="filterBulan">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="filter-group">
                    <label>Tahun</label>
                    <select class="form-select" name="tahun" id="filterTahun">
                        @for($year = date('Y'); $year >= date('Y') - 2; $year--)
                            <option value="{{ $year }}" {{ request('tahun', date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="btn-filter">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="number" id="totalItems">{{ $stokDivisis->total() }}</div>
                <div class="label">Total Item</div>
            </div>
            <div class="stat-card pending">
                <div class="number" id="pendingItems">{{ $stokDivisis->where('status_cek_bulanan', null)->count() }}</div>
                <div class="label">Belum Dicek</div>
            </div>
            <div class="stat-card success">
                <div class="number" id="successItems">{{ $stokDivisi_sesuai }}</div>
                <div class="label">Sesuai</div>
            </div>
            <div class="stat-card danger">
                <div class="number" id="errorItems">{{ $stokDivisi_tidak_sesuai }}</div>
                <div class="label">Tidak Sesuai</div>
            </div>
        </div>
        
        <div class="progress-card">
            <div class="progress-info">
                <span><strong>Progress Cek</strong></span>
                <span id="progressText">{{ $progress_percentage }}%</span>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress_percentage }}%" id="progressBar"></div>
            </div>
            <small class="text-muted mt-2">{{ $sudah_dicek }} dari {{ $stokDivisis->total() }} item telah dicek</small>
        </div>
    </div>

    <!-- Action Section -->
    <div class="action-section">
        <h6 class="mb-3"><i class="bi bi-lightning"></i> Aksi Cepat</h6>
        <div class="action-buttons">
            <button class="btn btn-success" onclick="saveAllChanges()">
                <i class="bi bi-check-all"></i> Simpan Semua
            </button>
            <button class="btn btn-warning" onclick="markAllAsCorrect()">
                <i class="bi bi-check-circle"></i> Tandai Sesuai
            </button>
            <button class="btn btn-info" onclick="exportData()">
                <i class="bi bi-download"></i> Export
            </button>
            <button class="btn btn-outline-primary" onclick="refreshData()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Data Section (Mobile Cards) -->
    <div class="data-section d-md-none" id="mobileView">
        @forelse($stokDivisis as $stok)
            <div class="item-card {{ $stok->status_cek_bulanan == 'sesuai' ? 'checked' : ($stok->status_cek_bulanan == 'tidak_sesuai' ? 'mismatch' : 'unchecked') }} fade-in" 
                 data-id="{{ $stok->id }}" 
                 data-stok-sistem="{{ $stok->sisa_stok }}">
                
                <div class="item-header">
                    <div class="item-title">
                        <h6>{{ $stok->stokPusat->nama_barang }}</h6>
                        <small>{{ $stok->stokPusat->kode_barang }} - {{ $stok->divisi->divisi }}</small>
                    </div>
                    <div class="item-checkbox">
                        <input type="checkbox" class="form-check-input row-checkbox" value="{{ $stok->id }}">
                    </div>
                </div>
                
                <div class="item-info">
                    <div class="info-item">
                        <div class="value">{{ $stok->stok_ideal ?? 0 }}</div>
                        <div class="label">Stok Ideal</div>
                    </div>
                    <div class="info-item">
                        <div class="value sistem-value">{{ $stok->sisa_stok }}</div>
                        <div class="label">Stok Sistem</div>
                    </div>
                    <div class="info-item">
                        <div class="value stok-fisik-display" data-id="{{ $stok->id }}">
                            {{ $stok->stok_fisik_cek ?? '-' }}
                        </div>
                        <div class="label">Stok Fisik</div>
                    </div>
                    <div class="info-item">
                        <div class="value selisih-display" data-id="{{ $stok->id }}" 
                             style="color: {{ $stok->selisih == 0 ? 'var(--success-color)' : ($stok->selisih > 0 ? 'var(--info-color)' : 'var(--danger-color)') }};">
                            {{ $stok->selisih !== null ? ($stok->selisih > 0 ? '+' . $stok->selisih : $stok->selisih) : '-' }}
                        </div>
                        <div class="label">Selisih</div>
                    </div>
                </div>
    
                <div class="item-actions">
                    <input type="number" 
                           class="stok-input" 
                           value="{{ $stok->stok_fisik_cek ?? '' }}" 
                           min="0" 
                           step="1"
                           data-id="{{ $stok->id }}" 
                           data-sistem="{{ $stok->sisa_stok }}"
                           placeholder="Input stok fisik">
                           
                    <div class="check-actions">
                        <button class="btn btn-primary check-btn" 
                                onclick="prosesCheck({{ $stok->id }})" 
                                title="Cek Data"
                                data-id="{{ $stok->id }}">
                            <i class="bi bi-check2-square"></i> Cek
                        </button>
                        @if($stok->status_cek_bulanan)
                        <button class="btn btn-warning btn-sm reset-btn" 
                                onclick="resetStatus({{ $stok->id }})" 
                                title="Reset Status">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        @endif
                    </div>
                </div>
    
                <div class="item-status {{ $stok->status_cek_bulanan == 'sesuai' ? 'status-success' : ($stok->status_cek_bulanan == 'tidak_sesuai' ? 'status-danger' : 'status-pending') }}">
                    @if($stok->status_cek_bulanan)
                        <i class="bi {{ $stok->status_cek_bulanan == 'sesuai' ? 'bi-check-circle' : 'bi-exclamation-triangle' }}"></i> 
                        {{ $stok->status_cek_label }} - Dicek oleh {{ $stok->dicek_oleh }} 
                        ({{ $stok->tgl_cek_bulanan ? $stok->tgl_cek_bulanan->format('d/m/Y H:i') : '' }})
                    @else
                        <i class="bi bi-clock"></i> Belum Dicek
                    @endif
                </div>
            </div>
        @empty
            <div class="no-data">
                <i class="bi bi-inbox"></i>
                <h5>Tidak Ada Data</h5>
                <p>Tidak ada stok divisi yang perlu dicek dengan filter yang dipilih.</p>
            </div>
        @endforelse
    </div>

    <!-- Table View (Desktop) -->
    <div class="table-responsive d-none d-md-block" id="desktopView">
        @if($stokDivisis->count() > 0)
            <table class="table table-hover" id="cekBulananTable">
                <thead class="table-dark">
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th>Divisi</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok Ideal</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Status</th>
                        <th>Terakhir Dicek</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stokDivisis as $stok)
                        <tr data-id="{{ $stok->id }}" data-stok-sistem="{{ $stok->sisa_stok }}">
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $stok->id }}">
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $stok->divisi->divisi }}</span>
                            </td>
                            <td>
                                <small>{{ $stok->stokPusat->kode_barang }}</small>
                            </td>
                            <td>
                                <strong>{{ $stok->stokPusat->nama_barang }}</strong><br>
                                <small class="text-muted">{{ $stok->stokPusat->satuan }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $stok->stok_ideal ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary sistem-value">{{ $stok->sisa_stok }}</span>
                            </td>
                            <td>
                                <input type="number" 
                                       class="form-control form-control-sm stok-input" 
                                       value="{{ $stok->stok_fisik_cek ?? '' }}" 
                                       data-id="{{ $stok->id }}" 
                                       data-sistem="{{ $stok->sisa_stok }}"
                                       style="width: 80px;" 
                                       min="0"
                                       step="1"
                                       placeholder="0">
                            </td>
                            <td>
                                <span class="badge selisih-badge-{{ $stok->id }} {{ $stok->selisih === null ? 'bg-secondary' : ($stok->selisih == 0 ? 'bg-success' : ($stok->selisih > 0 ? 'bg-info' : 'bg-danger')) }}">
                                    {{ $stok->selisih !== null ? ($stok->selisih > 0 ? '+' . $stok->selisih : $stok->selisih) : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge status-badge-{{ $stok->id }} {{ $stok->status_cek_badge_class }}">
                                    {{ $stok->status_cek_label }}
                                </span>
                            </td>
                            <td>
                                @if($stok->tgl_cek_bulanan)
                                    <small>{{ $stok->tgl_cek_bulanan->format('d/m/Y H:i') }}<br>{{ $stok->dicek_oleh }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-primary btn-sm check-btn" 
                                            onclick="prosesCheck({{ $stok->id }})" 
                                            title="Cek Data"
                                            data-id="{{ $stok->id }}">
                                        <i class="bi bi-check2-square"></i>
                                    </button>
                                    @if($stok->status_cek_bulanan)
                                    <button class="btn btn-warning btn-sm reset-btn" 
                                            onclick="resetStatus({{ $stok->id }})" 
                                            title="Reset Status">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="bi bi-inbox"></i>
                <h5>Tidak Ada Data</h5>
                <p>Tidak ada stok divisi yang perlu dicek dengan filter yang dipilih.</p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($stokDivisis->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $stokDivisis->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Floating Action Button -->
<button class="fab d-md-none" onclick="scrollToTop()" title="Kembali ke atas">
    <i class="bi bi-arrow-up"></i>
</button>

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let hasChanges = false;
        let changedItems = new Set();
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const routes = {
            batchUpdate: '/cek-bulanan/batch-update',
            singleUpdate: '/cek-bulanan/update', 
            exportData: '/export-cek-bulanan',
            currentPage: '/admin/cek-bulanan'
        };

        document.addEventListener('DOMContentLoaded', function() {
            initializeEventListeners();
            updateStats();
        });

        function initializeEventListeners() {
            // Check all functionality
            const checkAllElement = document.getElementById('checkAll');
            if (checkAllElement) {
                checkAllElement.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.row-checkbox');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }

            // Individual checkbox change
            document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateCheckAllState();
                });
            });

            // PERBAIKAN: Stok input event handling - KONSISTEN UNTUK MOBILE DAN DESKTOP
            document.querySelectorAll('.stok-input').forEach(input => {
                // Hapus event listener lama jika ada
                input.removeEventListener('input', handleStokInputChange);
                input.removeEventListener('change', handleStokInputChange);
                
                // Tambah event listener baru
                input.addEventListener('input', handleStokInputChange);
                input.addEventListener('change', handleStokInputChange);
                
                // PERBAIKAN: Tambahkan event untuk enter key
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        if (id) {
                            prosesCheck(id);
                        }
                    }
                });
            });
        }

        function handleStokInputChange(e) {
            const input = e.target;
            const id = input.getAttribute('data-id');
            const stokSistem = parseInt(input.getAttribute('data-sistem')) || 0;
            const stokFisik = parseInt(input.value) || 0;
            
            // Validasi input
            if (stokFisik < 0) {
                input.value = 0;
                return;
            }
            
            // Update preview selisih secara real-time
            updateSelisihPreview(id, stokFisik, stokSistem);
            
            // PERBAIKAN: Tambahkan visual feedback untuk perubahan
            markItemAsChanged(id);
        }

        function markItemAsChanged(id) {
            const card = document.querySelector(`[data-id="${id}"]`);
            const row = document.querySelector(`tr[data-id="${id}"]`);
            
            if (card) {
                card.classList.add('changed');
            }
            if (row) {
                row.classList.add('table-warning');
            }
            
            changedItems.add(id);
            hasChanges = true;
        }

        function updateSelisihPreview(id, stokFisik, stokSistem) {
            const selisih = stokFisik - stokSistem;
            
            // Update mobile view
            const mobileSelisih = document.querySelector(`.selisih-display[data-id="${id}"]`);
            if (mobileSelisih) {
                mobileSelisih.textContent = selisih > 0 ? '+' + selisih : selisih;
                mobileSelisih.style.color = selisih === 0 ? 'var(--success-color)' : 
                                        selisih > 0 ? 'var(--info-color)' : 'var(--danger-color)';
                mobileSelisih.style.fontStyle = 'italic';
                mobileSelisih.style.opacity = '0.8';
            }

            // Update desktop view
            const desktopSelisih = document.querySelector(`.selisih-badge-${id}`);
            if (desktopSelisih) {
                desktopSelisih.textContent = selisih > 0 ? '+' + selisih : selisih;
                desktopSelisih.className = `badge selisih-badge-${id} ${selisih === 0 ? 'bg-success' : (selisih > 0 ? 'bg-info' : 'bg-danger')}`;
                desktopSelisih.style.opacity = '0.8';
            }

            // Update stok fisik display
            const stokFisikDisplay = document.querySelector(`.stok-fisik-display[data-id="${id}"]`);
            if (stokFisikDisplay) {
                stokFisikDisplay.textContent = stokFisik;
                stokFisikDisplay.style.fontStyle = 'italic';
                stokFisikDisplay.style.opacity = '0.8';
            }
        }

        function prosesCheck(id) {
            const stokInput = document.querySelector(`input[data-id="${id}"]`);
            if (!stokInput) {
                console.error('Stok input not found for id:', id);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Input element tidak ditemukan'
                });
                return;
            }
            
            const stokFisikValue = stokInput.value.trim();
            
            // Validasi input tidak boleh kosong
            if (stokFisikValue === '' || stokFisikValue === null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: 'Silakan masukkan jumlah stok fisik'
                });
                stokInput.focus();
                return;
            }
            
            const stokFisik = parseInt(stokFisikValue);
            
            // Validasi input harus berupa angka valid
            if (isNaN(stokFisik) || stokFisik < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: 'Silakan masukkan angka yang valid (tidak boleh kurang dari 0)'
                });
                stokInput.focus();
                return;
            }
            
            const stokSistem = parseInt(stokInput.getAttribute('data-sistem')) || 0;
            const statusPreview = stokFisik === stokSistem ? 'SESUAI' : 'TIDAK SESUAI';
            const selisih = stokFisik - stokSistem;
            
            // Debug log
            console.log('Processing check:', {
                id: id,
                stokFisik: stokFisik,
                stokSistem: stokSistem,
                selisih: selisih,
                status: statusPreview
            });
            
            // Konfirmasi dengan preview status
            Swal.fire({
                title: 'Konfirmasi Pengecekan',
                html: `
                    <div class="text-start">
                        <div class="status-comparison">
                            <div class="row">
                                <div class="col-6"><strong>Stok Sistem:</strong></div>
                                <div class="col-6">${stokSistem}</div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Stok Fisik:</strong></div>
                                <div class="col-6">${stokFisik}</div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Selisih:</strong></div>
                                <div class="col-6 ${selisih === 0 ? 'text-success' : 'text-danger'}">
                                    ${selisih > 0 ? '+' : ''}${selisih}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Status:</strong></div>
                                <div class="col-6">
                                    <span class="badge ${statusPreview === 'SESUAI' ? 'bg-success' : 'bg-danger'}">${statusPreview}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                icon: statusPreview === 'SESUAI' ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonColor: statusPreview === 'SESUAI' ? '#198754' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Proses Cek',
                cancelButtonText: 'Batal',
                input: 'textarea',
                inputPlaceholder: 'Keterangan tambahan (opsional)',
                inputAttributes: {
                    maxlength: 500
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    saveCheckToServer(id, stokFisik, result.value);
                }
            });
        }

        function saveCheckToServer(id, stokFisik, keterangan) {
            const checkBtn = document.querySelector(`button[data-id="${id}"]`);
            if (checkBtn) {
                checkBtn.disabled = true;
                checkBtn.classList.add('loading');
            }
            
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('id', id);
            formData.append('stok_fisik', stokFisik);
            
            if (keterangan && keterangan.trim() !== '') {
                formData.append('keterangan', keterangan.trim());
            }

            console.log('Sending data:', {
                id: id,
                stok_fisik: stokFisik,
                keterangan: keterangan,
                url: routes.singleUpdate
            });

            fetch(routes.singleUpdate, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                
                if (data.success) {

                    updateItemCheckUI(id, data.data);
                    
                    const card = document.querySelector(`[data-id="${id}"]`);
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    
                    if (card) card.classList.remove('changed');
                    if (row) row.classList.remove('table-warning');
                    
                    changedItems.delete(id);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    updateStats();
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Check error:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: `
                        <div class="text-start">
                            <p><strong>Terjadi kesalahan:</strong></p>
                            <p>${error.message}</p>
                            <hr>
                            <small class="text-muted">
                                Silakan coba lagi atau hubungi administrator jika masalah berlanjut.
                            </small>
                        </div>
                    `
                });
            })
            .finally(() => {
                if (checkBtn) {
                    checkBtn.disabled = false;
                    checkBtn.classList.remove('loading');
                }
            });
        }

        async function quickAction(id, action) {
            try {
                // Disable button untuk mencegah multiple calls
                const card = document.querySelector(`[data-id="${id}"]`);
                if (card) {
                    card.style.pointerEvents = 'none';
                    card.style.opacity = '0.7';
                }
                
                const response = await fetch('/api/cek-bulanan/quick-update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        id: id, 
                        action: action 
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update UI
                    updateCardStatus(id, data.data);
                    
                    // Show feedback
                    showQuickActionFeedback(action);
                    
                    // Update stats
                    updateStats();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Quick action failed:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tindakan cepat gagal dilakukan: ' + error.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            } finally {
                // Re-enable card
                const card = document.querySelector(`[data-id="${id}"]`);
                if (card) {
                    card.style.pointerEvents = '';
                    card.style.opacity = '';
                }
            }
        }

        function updateItemCheckUI(id, data) {
            console.log('Updating UI for item:', id, 'with data:', data);
            
            const status = data.status_cek_bulanan;
            const statusText = data.status_cek_label;
            
            // Update mobile card
            const card = document.querySelector(`[data-id="${id}"]`);
            if (card) {
                let cardClass = 'unchecked';
                let statusClass = 'status-pending';
                let icon = 'bi-clock';
                
                if (status === 'sesuai') {
                    cardClass = 'checked';
                    statusClass = 'status-success';
                    icon = 'bi-check-circle';
                } else if (status === 'tidak_sesuai') {
                    cardClass = 'mismatch';
                    statusClass = 'status-danger';
                    icon = 'bi-exclamation-triangle';
                }

                // Update card class
                card.className = card.className.replace(/\b(checked|unchecked|mismatch)\b/g, cardClass);

                // Update status element
                const statusElement = card.querySelector('.item-status');
                if (statusElement) {
                    statusElement.className = `item-status ${statusClass}`;
                    statusElement.innerHTML = `<i class="bi ${icon}"></i> ${statusText} - Dicek oleh ${data.dicek_oleh} (${data.tgl_cek_bulanan})`;
                }

                // Update displays dengan data final dari server
                const stokFisikDisplay = card.querySelector('.stok-fisik-display');
                if (stokFisikDisplay) {
                    stokFisikDisplay.textContent = data.stok_fisik_cek;
                    stokFisikDisplay.style.fontStyle = 'normal';
                    stokFisikDisplay.style.opacity = '1';
                }

                const selisihDisplay = card.querySelector('.selisih-display');
                if (selisihDisplay) {
                    selisihDisplay.textContent = data.selisih > 0 ? '+' + data.selisih : data.selisih;
                    selisihDisplay.style.color = data.selisih === 0 ? 'var(--success-color)' : 
                                            data.selisih > 0 ? 'var(--info-color)' : 'var(--danger-color)';
                    selisihDisplay.style.fontStyle = 'normal';
                    selisihDisplay.style.opacity = '1';
                }

                // Show reset button
                const checkActions = card.querySelector('.check-actions');
                if (checkActions && !checkActions.querySelector('.reset-btn')) {
                    const resetBtn = document.createElement('button');
                    resetBtn.className = 'btn btn-warning btn-sm reset-btn';
                    resetBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                    resetBtn.setAttribute('onclick', `resetStatus(${id})`);
                    resetBtn.setAttribute('title', 'Reset Status');
                    checkActions.appendChild(resetBtn);
                }
            }

            // Update desktop table
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                // Update status badge
                const statusBadge = row.querySelector(`.status-badge-${id}`);
                if (statusBadge) {
                    let badgeClass = 'bg-warning';
                    if (status === 'sesuai') badgeClass = 'bg-success';
                    else if (status === 'tidak_sesuai') badgeClass = 'bg-danger';
                    
                    statusBadge.className = `badge status-badge-${id} ${badgeClass}`;
                    statusBadge.textContent = statusText;
                }

                // Update selisih badge dengan data dari server
                const selisihBadge = row.querySelector(`.selisih-badge-${id}`);
                if (selisihBadge) {
                    selisihBadge.textContent = data.selisih > 0 ? '+' + data.selisih : data.selisih;
                    selisihBadge.className = `badge selisih-badge-${id} ${data.selisih === 0 ? 'bg-success' : (data.selisih > 0 ? 'bg-info' : 'bg-danger')}`;
                    selisihBadge.style.opacity = '1';
                }

                // Update last checked column
                const lastCheckedCell = row.cells[row.cells.length - 2]; // Kolom sebelum aksi
                if (lastCheckedCell) {
                    lastCheckedCell.innerHTML = `<small>${data.tgl_cek_bulanan}<br>${data.dicek_oleh}</small>`;
                }

                // Show reset button
                const actionCell = row.cells[row.cells.length - 1];
                if (actionCell && !actionCell.querySelector('.reset-btn')) {
                    const btnGroup = actionCell.querySelector('.btn-group');
                    if (btnGroup) {
                        const resetBtn = document.createElement('button');
                        resetBtn.className = 'btn btn-warning btn-sm reset-btn';
                        resetBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                        resetBtn.setAttribute('onclick', `resetStatus(${id})`);
                        resetBtn.setAttribute('title', 'Reset Status');
                        btnGroup.appendChild(resetBtn);
                    }
                }
            }
        }

        function updateCheckAllState() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const checkAll = document.getElementById('checkAll');
            
            if (!checkAll) return;
            
            if (checkedBoxes.length === 0) {
                checkAll.indeterminate = false;
                checkAll.checked = false;
            } else if (checkedBoxes.length === checkboxes.length) {
                checkAll.indeterminate = false;
                checkAll.checked = true;
            } else {
                checkAll.indeterminate = true;
                checkAll.checked = false;
            }
        }

        function markAsChanged(id) {
            changedItems.add(id);
            hasChanges = true;
            
            // Visual feedback
            const card = document.querySelector(`[data-id="${id}"]`);
            if (card) {
                card.style.borderLeft = '4px solid #ffc107';
            }
        }

        function updateSelisih(id, stokFisik, stokSistem) {
            const selisih = stokFisik - stokSistem;
            
            console.log('updateSelisih - ID:', id, 'Fisik:', stokFisik, 'Sistem:', stokSistem, 'Selisih:', selisih);
            
            // Update mobile view
            const mobileSelisih = document.querySelector(`.selisih-display[data-id="${id}"]`);
            if (mobileSelisih) {
                mobileSelisih.textContent = selisih > 0 ? '+' + selisih : selisih;
                mobileSelisih.style.color = selisih === 0 ? 'var(--success-color)' : 
                                        selisih > 0 ? 'var(--info-color)' : 'var(--danger-color)';
                console.log('Updated mobile selisih display');
            }

            // Update desktop view
            const desktopSelisih = document.querySelector(`.selisih-badge-${id}`);
            if (desktopSelisih) {
                desktopSelisih.textContent = selisih > 0 ? '+' + selisih : selisih;
                desktopSelisih.className = `badge selisih-badge-${id} ${selisih === 0 ? 'bg-success' : (selisih > 0 ? 'bg-info' : 'bg-danger')}`;
                console.log('Updated desktop selisih badge');
            }

            // Update stok fisik display
            const stokFisikDisplay = document.querySelector(`.stok-fisik-display[data-id="${id}"]`);
            if (stokFisikDisplay) {
                stokFisikDisplay.textContent = stokFisik;
                console.log('Updated stok fisik display');
            }
        }

        function markAsCorrect(id) {
            console.warn('markAsCorrect is deprecated, use prosesCheck instead');
            prosesCheck(id);
        }

        function markAsIncorrect(id) {
            console.warn('markAsIncorrect is deprecated, use prosesCheck instead');
            prosesCheck(id);
        }

        function resetStatus(id) {
            Swal.fire({
                title: 'Reset Status?',
                text: 'Status cek akan dikembalikan ke "Belum Dicek"',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveResetToServer(id);
                }
            });
        }

        function saveResetToServer(id) {
            Swal.fire({
                title: 'Mereset...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('id', id);
            formData.append('status', ''); // Empty status untuk reset
            formData.append('stok_fisik', ''); // Empty stok fisik

            fetch(routes.singleUpdate, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateItemStatusUI(id, null, 'Belum Dicek', data.data);
                    
                    // Reset input stok fisik ke stok sistem
                    const stokInput = document.querySelector(`input[data-id="${id}"]`);
                    const stokSistem = parseInt(stokInput.getAttribute('data-sistem'));
                    if (stokInput) {
                        stokInput.value = stokSistem;
                        updateSelisih(id, stokSistem, stokSistem);
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status berhasil direset',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    updateStats();
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Reset error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan saat mereset status'
                });
            });
        }

        function updateStatus(id, status, statusText, stokFisik) {
            Swal.fire({
                title: `Tandai sebagai ${statusText}?`,
                text: `Item akan ditandai sebagai ${statusText}`,
                icon: status === 'sesuai' ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: status === 'sesuai' ? '#198754' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Ya, ${statusText}`,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveStatusToServer(id, status, stokFisik, statusText);
                }
            });
        }

        function saveStatusToServer(id, status, stokFisik, statusText) {
            console.log('saveStatusToServer - ID:', id, 'Status:', status, 'StokFisik:', stokFisik);
            
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('id', id);
            formData.append('status', status);
            formData.append('stok_fisik', stokFisik); // PENTING: Kirim nilai yang benar

            fetch(routes.singleUpdate, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                
                if (data.success) {
                    // PENTING: Gunakan data dari server untuk update UI
                    updateItemStatusUI(id, status, statusText, data.data);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `Item berhasil ditandai sebagai ${statusText}`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    updateStats();
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Single update error:', error);
                
                // PERBAIKAN: Jangan rollback jika berhasil save di server
                // Cek apakah error berasal dari server atau network
                if (!error.message.includes('HTTP 200')) {
                    const stokInput = document.querySelector(`input[data-id="${id}"]`);
                    if (stokInput) {
                        const originalValue = stokInput.getAttribute('data-original');
                        if (originalValue) {
                            stokInput.value = originalValue;
                            const stokSistem = parseInt(stokInput.getAttribute('data-sistem'));
                            updateSelisih(id, parseInt(originalValue), stokSistem);
                        }
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan saat menyimpan data'
                });
            });
        }

        function updateItemStatusUI(id, status, statusText, data) {
            console.log('Updating UI for item:', id, 'with data:', data);
            
            // Update mobile card
            const card = document.querySelector(`[data-id="${id}"]`);
            if (card) {
                let cardClass = 'unchecked';
                let statusClass = 'status-pending';
                let icon = 'bi-clock';
                
                if (status === 'sesuai') {
                    cardClass = 'checked';
                    statusClass = 'status-success';
                    icon = 'bi-check-circle';
                } else if (status === 'tidak_sesuai') {
                    cardClass = 'mismatch';
                    statusClass = 'status-danger';
                    icon = 'bi-exclamation-triangle';
                }

                // Update card class
                card.className = card.className.replace(/\b(checked|unchecked|mismatch)\b/g, cardClass);

                // Update status element
                const statusElement = card.querySelector('.item-status');
                if (statusElement) {
                    statusElement.className = `item-status ${statusClass}`;
                    const statusMessage = status ? 
                        `<i class="bi ${icon}"></i> ${statusText} - Dicek oleh ${data.dicek_oleh || '{{ auth()->user()->name }}'} (${data.tgl_cek_bulanan || new Date().toLocaleString('id-ID')})` :
                        `<i class="bi ${icon}"></i> ${statusText}`;
                    statusElement.innerHTML = statusMessage;
                }

                // PERBAIKAN: Update input dan display dengan data dari server
                if (data.stok_fisik_cek !== undefined) {
                    // Update input stok fisik
                    const stokInput = card.querySelector('.stok-input');
                    if (stokInput) {
                        stokInput.value = data.stok_fisik_cek;
                        stokInput.setAttribute('data-original', data.stok_fisik_cek); // Update original value
                        console.log('Updated input to server value:', data.stok_fisik_cek);
                    }
                    
                    // Update display di mobile card
                    const stokFisikDisplay = card.querySelector('.stok-fisik-display');
                    if (stokFisikDisplay) {
                        stokFisikDisplay.textContent = data.stok_fisik_cek;
                        console.log('Updated display to server value:', data.stok_fisik_cek);
                    }
                }

                // Update selisih dengan data dari server
                if (data.selisih !== undefined) {
                    const selisihDisplay = card.querySelector('.selisih-display');
                    if (selisihDisplay) {
                        selisihDisplay.textContent = data.selisih > 0 ? '+' + data.selisih : data.selisih;
                        selisihDisplay.style.color = data.selisih === 0 ? 'var(--success-color)' : 
                                                data.selisih > 0 ? 'var(--info-color)' : 'var(--danger-color)';
                        console.log('Updated selisih to server value:', data.selisih);
                    }
                }

                // Remove changed indicator
                changedItems.delete(id);
                card.style.borderLeft = '';
            }

            // Update desktop table
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                // Update status badge
                const statusBadge = row.querySelector(`.status-badge-${id}`);
                if (statusBadge) {
                    let badgeClass = 'bg-warning';
                    if (status === 'sesuai') badgeClass = 'bg-success';
                    else if (status === 'tidak_sesuai') badgeClass = 'bg-danger';
                    
                    statusBadge.className = `badge status-badge-${id} ${badgeClass}`;
                    statusBadge.textContent = statusText;
                }

                // PERBAIKAN: Update input di table dengan data dari server
                if (data.stok_fisik_cek !== undefined) {
                    const stokInput = row.querySelector('.stok-input');
                    if (stokInput) {
                        stokInput.value = data.stok_fisik_cek;
                        stokInput.setAttribute('data-original', data.stok_fisik_cek);
                        console.log('Updated table input to server value:', data.stok_fisik_cek);
                    }
                }

                // Update selisih badge dengan data dari server
                if (data.selisih !== undefined) {
                    const selisihBadge = row.querySelector(`.selisih-badge-${id}`);
                    if (selisihBadge) {
                        selisihBadge.textContent = data.selisih > 0 ? '+' + data.selisih : data.selisih;
                        selisihBadge.className = `badge selisih-badge-${id} ${data.selisih === 0 ? 'bg-success' : (data.selisih > 0 ? 'bg-info' : 'bg-danger')}`;
                        console.log('Updated table selisih to server value:', data.selisih);
                    }
                }
            }
        }

        function updateStats() {
            // Hitung ulang statistik dari DOM yang sudah di-update
            const allCards = document.querySelectorAll('.item-card, tr[data-id]');
            let totalItems = 0;
            let checkedItems = 0;
            let uncheckedItems = 0;
            let mismatchItems = 0;
            
            allCards.forEach(item => {
                // Skip jika bukan item yang valid
                if (!item.getAttribute('data-id')) return;
                
                totalItems++;
                
                if (item.classList.contains('checked') || 
                    item.querySelector('.status-badge-' + item.getAttribute('data-id'))?.textContent === 'Sesuai') {
                    checkedItems++;
                } else if (item.classList.contains('mismatch') || 
                          item.querySelector('.status-badge-' + item.getAttribute('data-id'))?.textContent === 'Tidak Sesuai') {
                    mismatchItems++;
                } else {
                    uncheckedItems++;
                }
            });
            
            // Update display
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('pendingItems').textContent = uncheckedItems;
            document.getElementById('successItems').textContent = checkedItems;
            document.getElementById('errorItems').textContent = mismatchItems;
            
            const progress = totalItems > 0 ? Math.round(((checkedItems + mismatchItems) / totalItems) * 100) : 0;
            document.getElementById('progressText').textContent = progress + '%';
            document.getElementById('progressBar').style.width = progress + '%';
        }

        function resetFilter() {
            // Reset form ke nilai default
            document.getElementById('filterDivisi').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterBulan').value = '{{ date("m") }}';
            document.getElementById('filterTahun').value = '{{ date("Y") }}';
            
            // Submit form
            document.getElementById('filterForm').submit();
        }

        function saveAllChanges() {
            const checkedItems = document.querySelectorAll('.row-checkbox:checked');
            
            if (checkedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Item Dipilih',
                    text: 'Pilih minimal satu item untuk dicek'
                });
                return;
            }

            // Validasi semua input
            let hasInvalidInput = false;
            const batchData = [];
            
            checkedItems.forEach(cb => {
                const id = cb.value;
                const stokInput = document.querySelector(`input[data-id="${id}"]`);
                if (stokInput) {
                    const stokFisik = parseInt(stokInput.value);
                    
                    if (isNaN(stokFisik) || stokFisik < 0) {
                        hasInvalidInput = true;
                        return;
                    }
                    
                    batchData.push({
                        id: id,
                        stok_fisik: stokFisik,
                        keterangan: `Batch check - ${new Date().toLocaleString('id-ID')}`
                    });
                }
            });

            if (hasInvalidInput) {
                Swal.fire({
                    icon: 'error',
                    title: 'Input Tidak Valid',
                    text: 'Pastikan semua item yang dipilih memiliki nilai stok fisik yang valid'
                });
                return;
            }

            Swal.fire({
                title: 'Proses Cek Batch?',
                html: `
                    <div class="text-start">
                        <p>Akan memproses <strong>${checkedItems.length}</strong> item</p>
                        <small class="text-muted">Status akan ditentukan otomatis berdasarkan perbandingan stok fisik dengan stok sistem</small>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Proses',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveBatchData(batchData);
                }
            });
        }

        function saveBatchData(batchData) {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('batch_data', JSON.stringify(batchData));

            fetch(routes.batchUpdate, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Update UI untuk setiap item
                    if (data.data && Array.isArray(data.data)) {
                        data.data.forEach(item => {
                            const status = item.status_cek_bulanan;
                            const statusText = item.status_cek_label || (status === 'sesuai' ? 'Sesuai' : (status === 'tidak_sesuai' ? 'Tidak Sesuai' : 'Belum Dicek'));
                            updateItemStatusUI(item.id, status, statusText, item);
                        });
                    }
                    
                    // Reset selection
                    document.querySelectorAll('.row-checkbox:checked').forEach(cb => cb.checked = false);
                    const checkAll = document.getElementById('checkAll');
                    if (checkAll) {
                        checkAll.checked = false;
                        checkAll.indeterminate = false;
                    }
                    
                    hasChanges = false;
                    changedItems.clear();
                    updateStats();
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Batch update error:', error);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                
                if (error.message.includes('HTTP 404')) {
                    errorMessage = 'Route tidak ditemukan. Silakan periksa konfigurasi route.';
                } else if (error.message.includes('HTTP 422')) {
                    errorMessage = 'Data tidak valid. Silakan periksa input Anda.';
                } else if (error.message.includes('HTTP 403')) {
                    errorMessage = 'Anda tidak memiliki akses untuk melakukan operasi ini.';
                } else if (error.message.includes('HTTP 500')) {
                    errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    footer: `Detail: ${error.message}`
                });
            });
        }

        function markAllAsCorrect() {
            const checkedItems = document.querySelectorAll('.row-checkbox:checked');
            
            if (checkedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Item Dipilih',
                    text: 'Pilih minimal satu item untuk ditandai sesuai'
                });
                return;
            }

            Swal.fire({
                title: 'Tandai Semua Sesuai?',
                html: `
                    <div class="text-start">
                        <p><strong>${checkedItems.length}</strong> item akan ditandai sebagai <span class="text-success">SESUAI</span></p>
                        <small class="text-warning">⚠️ Stok fisik akan disamakan dengan stok sistem untuk semua item</small>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tandai Sesuai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const batchData = [];
                    checkedItems.forEach(cb => {
                        const id = cb.value;
                        const stokInput = document.querySelector(`input[data-id="${id}"]`);
                        if (stokInput) {
                            // Untuk tandai sesuai, gunakan stok sistem sebagai stok fisik
                            const stokSistem = parseInt(stokInput.getAttribute('data-sistem')) || 0;
                            
                            batchData.push({
                                id: id,
                                stok_fisik: stokSistem, // Samakan dengan stok sistem
                                keterangan: 'Ditandai sesuai secara batch'
                            });
                            
                            // Update input value juga
                            stokInput.value = stokSistem;
                            updateSelisihPreview(id, stokSistem, stokSistem);
                        }
                    });
                    
                    saveBatchData(batchData);
                }
            });
        }


        function exportData() {
            const currentParams = new URLSearchParams(window.location.search);
            const exportUrl = routes.exportData + '?' + currentParams.toString();
            
            Swal.fire({
                title: 'Export Data',
                text: 'Data akan diexport dengan filter yang aktif saat ini',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#0dcaf0',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-download"></i> Export CSV',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Export...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const link = document.createElement('a');
                    link.href = exportUrl;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Berhasil!',
                            text: 'File CSV siap didownload',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 1000);
                }
            });
        }

        function refreshData() {
            Swal.fire({
                title: 'Refresh Data',
                text: 'Mengambil data terbaru...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                window.location.href = routes.currentPage + window.location.search;
            }, 1000);
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Warn user about unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges && changedItems.size > 0) {
                e.preventDefault();
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            }
        });

        // Auto-save functionality (optional)
        setInterval(() => {
            if (hasChanges && changedItems.size > 0) {
                console.log('Ada perubahan yang belum disimpan:', changedItems);
            }
        }, 30000);

        function debugCekBulanan() {
            console.log('=== DEBUG CEK BULANAN ===');
            console.log('Routes:', routes);
            console.log('CSRF Token:', csrfToken);
            console.log('Checked items:', document.querySelectorAll('.row-checkbox:checked').length);
            console.log('Changed items:', changedItems.size);
            console.log('Has changes:', hasChanges);
            console.log('Current URL:', window.location.href);
            console.log('=========================');
        }

        window.debugCekBulanan = debugCekBulanan;

        function debugStokValues(id) {
            const stokInput = document.querySelector(`input[data-id="${id}"]`);
            const stokFisikDisplay = document.querySelector(`.stok-fisik-display[data-id="${id}"]`);
            const selisihDisplay = document.querySelector(`.selisih-display[data-id="${id}"]`);
            
            console.log('=== DEBUG STOK VALUES ===');
            console.log('ID:', id);
            console.log('Input value:', stokInput?.value);
            console.log('Input data-sistem:', stokInput?.getAttribute('data-sistem'));
            console.log('Input data-original:', stokInput?.getAttribute('data-original'));
            console.log('Stok fisik display:', stokFisikDisplay?.textContent);
            console.log('Selisih display:', selisihDisplay?.textContent);
            console.log('========================');
        }

        // Make debug function available globally
        window.debugStokValues = debugStokValues;
    </script>
    <script>
        class CekBulananRealtime {
            constructor() {
                this.updateInterval = null;
                this.isOnline = navigator.onLine;
                this.lastUpdateTime = Date.now();
                this.retryCount = 0;
                this.maxRetries = 3;
                
                this.init();
            }
            
            init() {
                this.startRealtimeUpdates();
                this.setupOfflineHandling();
                this.setupVisibilityChange();
                this.setupServiceWorker();
            }
            
            startRealtimeUpdates() {
                // Update setiap 30 detik
                this.updateInterval = setInterval(() => {
                    if (this.isOnline && document.visibilityState === 'visible') {
                        this.fetchRealtimeStats();
                        this.fetchRecentActivities();
                    }
                }, 30000);
            }
            
            async fetchRealtimeStats() {
                try {
                    const currentParams = new URLSearchParams(window.location.search);
                    const response = await fetch(`/api/cek-bulanan/realtime-stats?${currentParams.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.updateStatsDisplay(data.data);
                            this.retryCount = 0;
                        }
                    }
                } catch (error) {
                    console.warn('Failed to fetch realtime stats:', error);
                    this.handleRetry();
                }
            }
            
            async fetchRecentActivities() {
                try {
                    const response = await fetch('/api/cek-bulanan/recent-activities?limit=5', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.updateRecentActivities(data.data);
                        }
                    }
                } catch (error) {
                    console.warn('Failed to fetch recent activities:', error);
                }
            }
            
            updateStatsDisplay(stats) {
                // Update stat cards
                const elements = {
                    totalItems: document.getElementById('totalItems'),
                    pendingItems: document.getElementById('pendingItems'),
                    successItems: document.getElementById('successItems'),
                    errorItems: document.getElementById('errorItems'),
                    progressText: document.getElementById('progressText'),
                    progressBar: document.getElementById('progressBar')
                };
                
                if (elements.totalItems) elements.totalItems.textContent = stats.total;
                if (elements.pendingItems) elements.pendingItems.textContent = stats.belum_dicek;
                if (elements.successItems) elements.successItems.textContent = stats.sesuai;
                if (elements.errorItems) elements.errorItems.textContent = stats.tidak_sesuai;
                if (elements.progressText) elements.progressText.textContent = stats.progress_percentage + '%';
                if (elements.progressBar) elements.progressBar.style.width = stats.progress_percentage + '%';
                
                // Add animation effect
                Object.values(elements).forEach(el => {
                    if (el) {
                        el.classList.add('animate__animated', 'animate__pulse');
                        setTimeout(() => {
                            el.classList.remove('animate__animated', 'animate__pulse');
                        }, 1000);
                    }
                });
            }
            
            updateRecentActivities(activities) {
                // Create or update recent activities section
                let activitiesContainer = document.getElementById('recentActivities');
                if (!activitiesContainer) {
                    activitiesContainer = this.createRecentActivitiesSection();
                }
                
                const activitiesList = activitiesContainer.querySelector('.activities-list');
                activitiesList.innerHTML = activities.map(activity => `
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="bi ${activity.icon}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">${activity.nama_barang}</div>
                            <div class="activity-meta">
                                <span class="badge ${activity.badge_class}">${activity.status}</span>
                                <small class="text-muted">${activity.dicek_oleh} • ${activity.tgl_cek}</small>
                            </div>
                        </div>
                        ${activity.selisih !== 0 ? `<div class="activity-badge">
                            <span class="badge ${activity.selisih > 0 ? 'bg-info' : 'bg-danger'}">
                                ${activity.selisih > 0 ? '+' : ''}${activity.selisih}
                            </span>
                        </div>` : ''}
                    </div>
                `).join('');
            }
            
            createRecentActivitiesSection() {
                const container = document.createElement('div');
                container.id = 'recentActivities';
                container.className = 'recent-activities-section';
                container.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h6>
                        </div>
                        <div class="card-body">
                            <div class="activities-list"></div>
                        </div>
                    </div>
                `;
                
                // Insert after action section
                const actionSection = document.querySelector('.action-section');
                if (actionSection) {
                    actionSection.parentNode.insertBefore(container, actionSection.nextSibling);
                }
                
                return container;
            }
            
            setupOfflineHandling() {
                window.addEventListener('online', () => {
                    this.isOnline = true;
                    this.showConnectionStatus('online');
                    this.startRealtimeUpdates();
                });
                
                window.addEventListener('offline', () => {
                    this.isOnline = false;
                    this.showConnectionStatus('offline');
                    if (this.updateInterval) {
                        clearInterval(this.updateInterval);
                    }
                });
            }
            
            setupVisibilityChange() {
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible' && this.isOnline) {
                        // Refresh data when tab becomes visible
                        this.fetchRealtimeStats();
                        this.fetchRecentActivities();
                    }
                });
            }
            
            setupServiceWorker() {
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('/sw.js').catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                }
            }
            
            showConnectionStatus(status) {
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${status === 'online' ? 'success' : 'warning'} border-0`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-${status === 'online' ? 'wifi' : 'wifi-off'}"></i>
                            ${status === 'online' ? 'Koneksi pulih' : 'Koneksi terputus'}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                
                document.body.appendChild(toast);
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }
            
            handleRetry() {
                this.retryCount++;
                if (this.retryCount >= this.maxRetries) {
                    console.warn('Max retries reached, pausing updates');
                    if (this.updateInterval) {
                        clearInterval(this.updateInterval);
                    }
                    
                    // Retry after 5 minutes
                    setTimeout(() => {
                        this.retryCount = 0;
                        this.startRealtimeUpdates();
                    }, 300000);
                }
            }
            
            destroy() {
                if (this.updateInterval) {
                    clearInterval(this.updateInterval);
                }
            }
        }

        // Mobile-specific enhancements
        class MobileEnhancements {
            constructor() {
                this.isMobile = window.innerWidth <= 768;
                this.touchStartY = 0;
                this.isRefreshing = false;
                
                this.init();
            }
            
            init() {
                this.setupPullToRefresh();
                this.setupTouchGestures();
                this.setupResponsiveImages();
                this.setupVirtualKeyboard();
            }
            
            setupPullToRefresh() {
                if (!this.isMobile) return;
                
                let startY = 0;
                let currentY = 0;
                let pullDistance = 0;
                const threshold = 60;
                
                const pullIndicator = this.createPullIndicator();
                
                document.addEventListener('touchstart', (e) => {
                    if (window.scrollY === 0) {
                        startY = e.touches[0].pageY;
                        pullIndicator.style.display = 'block';
                    }
                });
                
                document.addEventListener('touchmove', (e) => {
                    if (window.scrollY === 0 && startY > 0) {
                        currentY = e.touches[0].pageY;
                        pullDistance = currentY - startY;
                        
                        if (pullDistance > 0) {
                            e.preventDefault();
                            const progress = Math.min(pullDistance / threshold, 1);
                            pullIndicator.style.transform = `translateY(${pullDistance}px) rotate(${progress * 180}deg)`;
                            pullIndicator.style.opacity = progress;
                        }
                    }
                });
                
                document.addEventListener('touchend', () => {
                    if (pullDistance > threshold && !this.isRefreshing) {
                        this.triggerRefresh();
                    }
                    
                    pullIndicator.style.transform = 'translateY(0) rotate(0deg)';
                    pullIndicator.style.opacity = '0';
                    startY = 0;
                    pullDistance = 0;
                });
            }
            
            createPullIndicator() {
                const indicator = document.createElement('div');
                indicator.className = 'pull-refresh-indicator';
                indicator.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                indicator.style.cssText = `
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    z-index: 9999;
                    background: var(--primary-color);
                    color: white;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    display: none;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;
                
                document.body.appendChild(indicator);
                return indicator;
            }
            
            async triggerRefresh() {
                this.isRefreshing = true;
                
                try {
                    // Show loading
                    const indicator = document.querySelector('.pull-refresh-indicator');
                    indicator.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
                    
                    // Refresh data
                    await Promise.all([
                        realtime.fetchRealtimeStats(),
                        realtime.fetchRecentActivities()
                    ]);
                    
                    // Show success
                    indicator.innerHTML = '<i class="bi bi-check"></i>';
                    
                    setTimeout(() => {
                        indicator.style.opacity = '0';
                        indicator.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                    }, 1000);
                    
                } catch (error) {
                    console.error('Refresh failed:', error);
                    
                    const indicator = document.querySelector('.pull-refresh-indicator');
                    indicator.innerHTML = '<i class="bi bi-exclamation-triangle"></i>';
                    
                    setTimeout(() => {
                        indicator.style.opacity = '0';
                        indicator.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                    }, 2000);
                } finally {
                    this.isRefreshing = false;
                }
            }
            
            setupTouchGestures() {
                // Swipe gestures for cards
                document.querySelectorAll('.item-card').forEach(card => {
                    let startX = 0;
                    let currentX = 0;
                    
                    card.addEventListener('touchstart', (e) => {
                        startX = e.touches[0].pageX;
                    });
                    
                    card.addEventListener('touchmove', (e) => {
                        currentX = e.touches[0].pageX;
                        const diff = currentX - startX;
                        
                        if (Math.abs(diff) > 10) {
                            e.preventDefault();
                            card.style.transform = `translateX(${diff * 0.3}px)`;
                        }
                    });
                    
                    card.addEventListener('touchend', () => {
                        const diff = currentX - startX;
                        
                        if (Math.abs(diff) > 100) {
                            // Trigger quick action
                            const id = card.getAttribute('data-id');
                            if (diff > 0) {
                                this.quickAction(id, 'match');
                            } else {
                                this.quickAction(id, 'mismatch');
                            }
                        }
                        
                        card.style.transform = 'translateX(0)';
                        startX = 0;
                        currentX = 0;
                    });
                });
            }
            
            async quickAction(id, action) {
                try {
                    const response = await fetch('/api/cek-bulanan/quick-update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ id, action })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update UI
                        this.updateCardStatus(id, data.data);
                        
                        // Show feedback
                        this.showQuickActionFeedback(action);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    console.error('Quick action failed:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tindakan cepat gagal dilakukan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
            
            updateCardStatus(id, data) {
                const card = document.querySelector(`[data-id="${id}"]`);
                if (!card) return;
                
                // Update status display
                const statusElement = card.querySelector('.item-status');
                if (statusElement) {
                    let statusClass = 'status-pending';
                    let icon = 'bi-clock';
                    
                    if (data.status === 'Sesuai') {
                        statusClass = 'status-success';
                        icon = 'bi-check-circle';
                        card.className = card.className.replace(/\b(checked|unchecked|mismatch)\b/g, 'checked');
                    } else if (data.status === 'Tidak Sesuai') {
                        statusClass = 'status-danger';
                        icon = 'bi-exclamation-triangle';
                        card.className = card.className.replace(/\b(checked|unchecked|mismatch)\b/g, 'mismatch');
                    } else {
                        card.className = card.className.replace(/\b(checked|unchecked|mismatch)\b/g, 'unchecked');
                    }
                    
                    statusElement.className = `item-status ${statusClass}`;
                    statusElement.innerHTML = `<i class="bi ${icon}"></i> ${data.status} - ${data.tgl_cek || 'Belum Dicek'}`;
                }
                
                // Update selisih
                const selisihElement = card.querySelector('.selisih-display');
                if (selisihElement && data.selisih !== undefined) {
                    selisihElement.textContent = data.selisih > 0 ? '+' + data.selisih : data.selisih;
                    selisihElement.style.color = data.selisih === 0 ? 'var(--success-color)' : 
                                                data.selisih > 0 ? 'var(--info-color)' : 'var(--danger-color)';
                }
            }
            
            showQuickActionFeedback(action) {
                const messages = {
                    match: 'Ditandai sesuai',
                    mismatch: 'Ditandai tidak sesuai',
                    reset: 'Status direset'
                };
                
                const toast = document.createElement('div');
                toast.className = 'toast-feedback';
                toast.textContent = messages[action] || 'Aksi berhasil';
                toast.style.cssText = `
                    position: fixed;
                    bottom: 100px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: var(--success-color);
                    color: white;
                    padding: 10px 20px;
                    border-radius: 20px;
                    z-index: 9999;
                    animation: slideUp 0.3s ease-out;
                `;
                
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.style.animation = 'slideDown 0.3s ease-in';
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            }
            
            setupResponsiveImages() {
                // Lazy loading untuk gambar jika ada
                if ('IntersectionObserver' in window) {
                    const imageObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                img.src = img.dataset.src;
                                img.classList.remove('lazy');
                                imageObserver.unobserve(img);
                            }
                        });
                    });
                    
                    document.querySelectorAll('img[data-src]').forEach(img => {
                        imageObserver.observe(img);
                    });
                }
            }
            
            setupVirtualKeyboard() {
                // Handle virtual keyboard pada mobile
                if (this.isMobile) {
                    const viewport = document.querySelector('meta[name=viewport]');
                    if (viewport) {
                        let originalContent = viewport.content;
                        
                        document.addEventListener('focusin', (e) => {
                            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                                viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                            }
                        });
                        
                        document.addEventListener('focusout', () => {
                            viewport.content = originalContent;
                        });
                    }
                }
            }
        }

        // Progressive Web App features
        class PWAFeatures {
            constructor() {
                this.init();
            }
            
            init() {
                this.setupInstallPrompt();
                this.setupNotifications();
                this.setupOfflineSync();
            }
            
            setupInstallPrompt() {
                let deferredPrompt;
                
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    deferredPrompt = e;
                    
                    // Show install button
                    this.showInstallButton(deferredPrompt);
                });
                
                window.addEventListener('appinstalled', () => {
                    console.log('PWA was installed');
                    this.hideInstallButton();
                });
            }
            
            showInstallButton(deferredPrompt) {
                const installBtn = document.createElement('button');
                installBtn.className = 'btn btn-primary btn-sm install-btn';
                installBtn.innerHTML = '<i class="bi bi-download"></i> Install App';
                installBtn.style.cssText = `
                    position: fixed;
                    top: 10px;
                    right: 10px;
                    z-index: 9998;
                    display: none;
                `;
                
                if (window.innerWidth <= 768) {
                    installBtn.style.display = 'block';
                }
                
                installBtn.addEventListener('click', async () => {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    if (outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    
                    deferredPrompt = null;
                    this.hideInstallButton();
                });
                
                document.body.appendChild(installBtn);
            }
            
            hideInstallButton() {
                const installBtn = document.querySelector('.install-btn');
                if (installBtn) {
                    installBtn.remove();
                }
            }
            
            setupNotifications() {
                if ('Notification' in window && 'serviceWorker' in navigator) {
                    // Request permission for notifications
                    if (Notification.permission === 'default') {
                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                console.log('Notification permission granted');
                            }
                        });
                    }
                }
            }
            
            async setupOfflineSync() {
                if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
                    try {
                        const registration = await navigator.serviceWorker.ready;
                        
                        // Register background sync
                        await registration.sync.register('background-sync');
                        console.log('Background sync registered');
                    } catch (error) {
                        console.log('Background sync not supported:', error);
                    }
                }
            }
            
            showNotification(title, options = {}) {
                if (Notification.permission === 'granted' && 'serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then(registration => {
                        registration.showNotification(title, {
                            badge: '/icons/badge-72x72.png',
                            icon: '/icons/icon-192x192.png',
                            ...options
                        });
                    });
                }
            }
        }

        // Enhanced search functionality
        class SmartSearch {
            constructor() {
                this.searchInput = document.querySelector('#searchInput');
                this.searchResults = [];
                this.currentIndex = -1;
                
                this.init();
            }
            
            init() {
                if (!this.searchInput) {
                    this.createSearchInput();
                }
                
                this.setupSearchHandlers();
                this.setupKeyboardNavigation();
            }
            
            createSearchInput() {
                const searchContainer = document.createElement('div');
                searchContainer.className = 'search-container';
                searchContainer.innerHTML = `
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari barang...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="search-suggestions" id="searchSuggestions"></div>
                `;
                
                // Insert after filter section
                const filterSection = document.querySelector('.filter-section');
                if (filterSection) {
                    filterSection.parentNode.insertBefore(searchContainer, filterSection.nextSibling);
                }
                
                this.searchInput = document.querySelector('#searchInput');
            }
            
            setupSearchHandlers() {
                let searchTimeout;
                
                this.searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    
                    searchTimeout = setTimeout(() => {
                        this.performSearch(e.target.value);
                    }, 300);
                });
                
                document.getElementById('clearSearch').addEventListener('click', () => {
                    this.clearSearch();
                });
            }
            
            setupKeyboardNavigation() {
                this.searchInput.addEventListener('keydown', (e) => {
                    const suggestions = document.querySelectorAll('.search-suggestion');
                    
                    switch (e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            this.currentIndex = Math.min(this.currentIndex + 1, suggestions.length - 1);
                            this.highlightSuggestion();
                            break;
                            
                        case 'ArrowUp':
                            e.preventDefault();
                            this.currentIndex = Math.max(this.currentIndex - 1, -1);
                            this.highlightSuggestion();
                            break;
                            
                        case 'Enter':
                            e.preventDefault();
                            if (this.currentIndex >= 0 && suggestions[this.currentIndex]) {
                                this.selectSuggestion(suggestions[this.currentIndex]);
                            }
                            break;
                            
                        case 'Escape':
                            this.hideSuggestions();
                            break;
                    }
                });
            }
            
            async performSearch(query) {
                if (query.length < 2) {
                    this.hideSuggestions();
                    this.showAllItems();
                    return;
                }
                
                try {
                    // Filter items in real-time
                    const items = document.querySelectorAll('.item-card, table tbody tr');
                    let visibleCount = 0;
                    
                    items.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        const matches = text.includes(query.toLowerCase());
                        
                        if (matches) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Show search results count
                    this.showSearchResults(visibleCount, query);
                    
                    // Show suggestions for autocomplete
                    this.showSuggestions(query);
                    
                } catch (error) {
                    console.error('Search error:', error);
                }
            }
            
            showSuggestions(query) {
                // Get unique suggestions from visible items
                const suggestions = new Set();
                const items = document.querySelectorAll('.item-card:not([style*="display: none"])');
                
                items.forEach(item => {
                    const namaBarang = item.querySelector('.item-title h6');
                    const kodeBarang = item.querySelector('.item-title small');
                    
                    if (namaBarang) suggestions.add(namaBarang.textContent);
                    if (kodeBarang) suggestions.add(kodeBarang.textContent.split(' - ')[0]);
                });
                
                const suggestionsContainer = document.getElementById('searchSuggestions');
                suggestionsContainer.innerHTML = '';
                
                if (suggestions.size > 0 && query.length >= 2) {
                    const suggestionsList = Array.from(suggestions)
                        .filter(s => s.toLowerCase().includes(query.toLowerCase()))
                        .slice(0, 5);
                    
                    suggestionsContainer.innerHTML = suggestionsList
                        .map(suggestion => `
                            <div class="search-suggestion" data-value="${suggestion}">
                                <i class="bi bi-search"></i> ${suggestion}
                            </div>
                        `).join('');
                    
                    suggestionsContainer.style.display = 'block';
                    
                    // Add click handlers
                    suggestionsContainer.querySelectorAll('.search-suggestion').forEach(item => {
                        item.addEventListener('click', () => this.selectSuggestion(item));
                    });
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            }
            
            selectSuggestion(suggestionElement) {
                const value = suggestionElement.getAttribute('data-value');
                this.searchInput.value = value;
                this.performSearch(value);
                this.hideSuggestions();
            }
            
            highlightSuggestion() {
                const suggestions = document.querySelectorAll('.search-suggestion');
                
                suggestions.forEach((item, index) => {
                    if (index === this.currentIndex) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
            
            hideSuggestions() {
                const suggestionsContainer = document.getElementById('searchSuggestions');
                if (suggestionsContainer) {
                    suggestionsContainer.style.display = 'none';
                }
                this.currentIndex = -1;
            }
            
            showSearchResults(count, query) {
                let resultsBadge = document.querySelector('.search-results-badge');
                
                if (!resultsBadge) {
                    resultsBadge = document.createElement('div');
                    resultsBadge.className = 'search-results-badge alert alert-info';
                    
                    const dataSection = document.querySelector('.data-section');
                    if (dataSection) {
                        dataSection.parentNode.insertBefore(resultsBadge, dataSection);
                    }
                }
                
                resultsBadge.innerHTML = `
                    <i class="bi bi-search"></i> 
                    Ditemukan <strong>${count}</strong> item untuk "<em>${query}</em>"
                    <button type="button" class="btn-close" onclick="smartSearch.clearSearch()"></button>
                `;
                resultsBadge.style.display = 'block';
            }
            
            clearSearch() {
                this.searchInput.value = '';
                this.hideSuggestions();
                this.showAllItems();
                
                const resultsBadge = document.querySelector('.search-results-badge');
                if (resultsBadge) {
                    resultsBadge.style.display = 'none';
                }
            }
            
            showAllItems() {
                const items = document.querySelectorAll('.item-card, table tbody tr');
                items.forEach(item => {
                    item.style.display = '';
                });
            }
        }

        // Initialize all features when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize existing functionality first
            initializeEventListeners();
            updateStats();
            
            // Initialize new features
            window.realtime = new CekBulananRealtime();
            window.mobileEnhancements = new MobileEnhancements();
            window.pwaFeatures = new PWAFeatures();
            window.smartSearch = new SmartSearch();
            
            // Add custom CSS for new features
            addCustomStyles();
        });

        // Add custom styles for new features
        function addCustomStyles() {
            const style = document.createElement('style');
            style.textContent = `
                .search-container {
                    background: white;
                    border-radius: 10px;
                    padding: 15px;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    position: relative;
                }
                
                .search-suggestions {
                    position: absolute;
                    top: 100%;
                    left: 15px;
                    right: 15px;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 0 0 8px 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 1000;
                    display: none;
                }
                
                .search-suggestion {
                    padding: 10px 15px;
                    cursor: pointer;
                    border-bottom: 1px solid #f0f0f0;
                    transition: background 0.2s;
                }
                
                .search-suggestion:hover,
                .search-suggestion.active {
                    background: #f8f9fa;
                }
                
                .search-suggestion:last-child {
                    border-bottom: none;
                }
                
                .recent-activities-section {
                    background: white;
                    border-radius: 10px;
                    padding: 15px;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                
                .activity-item {
                    display: flex;
                    align-items: center;
                    padding: 10px 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                
                .activity-item:last-child {
                    border-bottom: none;
                }
                
                .activity-icon {
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    background: var(--primary-color);
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 10px;
                    font-size: 14px;
                }
                
                .activity-content {
                    flex: 1;
                }
                
                .activity-title {
                    font-weight: 600;
                    font-size: 14px;
                    margin-bottom: 2px;
                }
                
                .activity-meta {
                    font-size: 12px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .activity-badge {
                    margin-left: 10px;
                }
                
                @keyframes slideUp {
                    from { transform: translateX(-50%) translateY(20px); opacity: 0; }
                    to { transform: translateX(-50%) translateY(0); opacity: 1; }
                }
                
                @keyframes slideDown {
                    from { transform: translateX(-50%) translateY(0); opacity: 1; }
                    to { transform: translateX(-50%) translateY(20px); opacity: 0; }
                }
                
                .search-results-badge {
                    margin-bottom: 15px;
                }
                
                .install-btn {
                    animation: pulse 2s infinite;
                }
                
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }
                
                /* Touch feedback */
                .item-card {
                    transition: transform 0.2s ease;
                }
                
                .item-card:active {
                    transform: scale(0.98);
                }
                
                /* Loading states */
                .loading-skeleton {
                    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                    background-size: 200% 100%;
                    animation: loading 1.5s infinite;
                }
                
                @keyframes loading {
                    0% { background-position: 200% 0; }
                    100% { background-position: -200% 0; }
                }
            `;
            
            document.head.appendChild(style);
        }

        // Cleanup function
        window.addEventListener('beforeunload', function() {
            if (window.realtime) {
                window.realtime.destroy();
            }
        });
    </script>
@endpush
@endsection