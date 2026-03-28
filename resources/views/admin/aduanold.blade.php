@extends('layouts.master')
@push('css')
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/compiled/css/table-datatable.css">
@endpush
@section('content')
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2">
            <h3 class="card-title mb-0">Daftar Aduan</h3>
        </div>
        <div class="card-body p-3">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <label class="input-group-text bg-primary text-white" for="per-page">
                            <i class="bi bi-list-ul"></i>
                        </label>
                        <select class="form-select" id="per-page">
                            <option value="10">10 entries</option>
                            <option value="25">25 entries</option>
                            <option value="50">50 entries</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <form action="{{ route('admin.list-aduan') }}" method="GET" id="divisiForm">
                        <div class="input-group">
                            <label class="input-group-text bg-primary text-white" for="filter-divisi">
                                <i class="bi bi-filter"></i>
                            </label>
                            <select name="divisi_id" id="filter-divisi" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Divisi</option>
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                        {{ $divisi->divisi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search" class="form-control" placeholder="Cari..." id="search">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th class="py-2">No</th>
                            <th class="py-2">Nama SPA</th>
                            <th class="py-2">Divisi</th>
                            <th class="py-2">Amanah</th>
                            <th class="py-2">Lokasi</th>
                            <th class="py-2">Jenis Aduan</th>
                            <th class="py-2">Kendala</th>
                            <th class="py-2">Rincian</th>
                            <th class="py-2">Nomor Telp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aduans as $index => $aduan)
                        <tr>
                            <td class="py-2">{{ $loop->iteration }}</td>
                            <td class="py-2">{{ $aduan->nama_spa }}</td>
                            <td class="py-2">{{ $aduan->divisi->divisi ?? 'Divisi tidak tersedia' }}</td>
                            <td class="py-2">{{ $aduan->amanah }}</td>
                            <td class="py-2">{{ $aduan->lokasi_pengaduan }}</td>
                            <td class="py-2">{{ $aduan->jenis_pengaduan }}</td>
                            <td class="py-2">{{ $aduan->kerusakan }}</td>
                            <td class="py-2">{{ $aduan->rincian_pengaduan }}</td>
                            <td class="py-2">{{ $aduan->nomor_telp }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-2">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 small">
                    Menampilkan {{ $aduans->count() }} data
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        white-space: nowrap;
        background-color: #f8f9fa;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    .input-group-text {
        border: none;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 0;
    }

    #main {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .page-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .container-fluid {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .card {
        flex: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('search').addEventListener('keyup', function(e) {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    document.getElementById('per-page').addEventListener('change', function(e) {
        let rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.style.display = index < this.value ? '' : 'none';
        });
    });
</script>
@endpush
@endsection

@push('js')
    <script src="{{ asset('dist') }}/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="{{ asset('dist') }}/assets/static/js/pages/simple-datatables.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@endpush