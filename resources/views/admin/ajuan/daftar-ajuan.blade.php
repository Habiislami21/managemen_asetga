@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('header')
    <h3>Daftar Semua Ajuan Stok</h3>
@endsection

@section('content')
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Daftar Ajuan Stok</h3>
            <div class="d-flex gap-2">
                <!-- Filter -->
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved_ga" {{ request('status') === 'approved_ga' ? 'selected' : '' }}>Approved GA</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <select name="divisi_id" class="form-control form-control-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisis as $divisi)
                            <option value="{{ $divisi->id }}" {{ request('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                {{ $divisi->divisi }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('ajuan.daftar') }}" class="btn btn-sm btn-secondary">Reset</a>
                </form>
            </div>
        </div>
        <div class="card-body p-3">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>{{ $ajuans->where('status', 'pending')->count() }}</h5>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>{{ $ajuans->where('status', 'approved_ga')->count() }}</h5>
                            <small>Approved GA</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>{{ $ajuans->where('status', 'completed')->count() }}</h5>
                            <small>Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h5>{{ $ajuans->where('status', 'rejected')->count() }}</h5>
                            <small>Rejected</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="ajuanTable" class="table table-striped display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No. Ajuan</th>
                            <th>Divisi</th>
                            <th>Pengaju</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ajuans as $ajuan)
                        <tr>
                            <td>{{ $ajuan->nomor_ajuan }}</td>
                            <td>{{ $ajuan->divisi->divisi }}</td>
                            <td>{{ $ajuan->pengaju->name }}</td>
                            <td>
                                <div>
                                    <strong>{{ $ajuan->stokPusat->nama_barang }}</strong>
                                    <br>
                                    <small class="text-muted">Kode: {{ $ajuan->stokPusat->kode_barang }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    Diminta: {{ $ajuan->jumlah_diminta }} {{ $ajuan->stokPusat->satuan }}
                                    @if($ajuan->jumlah_diberikan)
                                        <br>Diberikan: {{ $ajuan->jumlah_diberikan }} {{ $ajuan->stokPusat->satuan }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                @switch($ajuan->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Menunggu GA</span>
                                        @break
                                    @case('approved_ga')
                                        <span class="badge bg-info">Menunggu Kabag</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    @php
                                        $progress = 0;
                                        $progressClass = 'bg-secondary';
                                        
                                        switch($ajuan->status) {
                                            case 'pending':
                                                $progress = 25;
                                                $progressClass = 'bg-warning';
                                                break;
                                            case 'approved_ga':
                                                $progress = 50;
                                                $progressClass = 'bg-info';
                                                break;
                                            case 'completed':
                                                $progress = 100;
                                                $progressClass = 'bg-success';
                                                break;
                                            case 'rejected':
                                                $progress = 100;
                                                $progressClass = 'bg-danger';
                                                break;
                                        }
                                    @endphp
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar" 
                                         style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $progress }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    @switch($ajuan->status)
                                        @case('pending')
                                            Ajuan dibuat
                                            @break
                                        @case('approved_ga')
                                            GA approved
                                            @break
                                        @case('completed')
                                            Transfer selesai
                                            @break
                                        @case('rejected')
                                            Ditolak
                                            @break
                                    @endswitch
                                </small>
                            </td>
                            <td>
                                {{ $ajuan->created_at->format('d/m/Y H:i') }}
                                @if($ajuan->completed_at)
                                    <br><small class="text-success">Selesai: {{ $ajuan->completed_at->format('d/m/Y H:i') }}</small>
                                @elseif($ajuan->rejected_at)
                                    <br><small class="text-danger">Ditolak: {{ $ajuan->rejected_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info btn-detail" 
                                        data-id="{{ $ajuan->id }}" 
                                        title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $ajuans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Ajuan -->
<div class="modal fade" id="modalDetailAjuan" tabindex="-1" aria-labelledby="modalDetailAjuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailAjuanLabel">Detail Ajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="loadingDetail">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="detailContent" class="d-none">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#ajuanTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [[7, 'desc']], // Sort by date descending
                columnDefs: [
                    { targets: [8], orderable: false } // Disable sorting for action column
                ]
            });

            // Event handler untuk tombol detail
            $(document).on('click', '.btn-detail', function() {
                const ajuanId = $(this).data('id');
                
                // Reset modal
                $('#loadingDetail').removeClass('d-none');
                $('#detailContent').addClass('d-none').html('');
                
                // Show modal
                $('#modalDetailAjuan').modal('show');
                
                // Load detail via AJAX
                $.ajax({
                    url: `{{ url('/ajuan-stok/detail') }}/${ajuanId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#loadingDetail').addClass('d-none');
                        $('#detailContent').removeClass('d-none');
                        
                        const ajuan = response.ajuan;
                        const timeline = response.timeline;
                        
                        let timelineHtml = '';
                        timeline.forEach(function(item) {
                            const statusClass = item.status === 'completed' ? 'text-success' : 
                                              item.status === 'pending' ? 'text-warning' : 
                                              item.status === 'rejected' ? 'text-danger' : 'text-secondary';
                            
                            timelineHtml += `
                                <div class="d-flex mb-3">
                                    <div class="me-3">
                                        <i class="bi ${item.icon} ${statusClass} fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">${item.title}</h6>
                                        <p class="text-muted mb-1">${item.description}</p>
                                        ${item.date ? `<small class="text-muted">${new Date(item.date).toLocaleString('id-ID')}</small>` : ''}
                                    </div>
                                </div>
                            `;
                        });
                        
                        const detailHtml = `
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Informasi Ajuan</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>No. Ajuan:</strong></td><td>${ajuan.nomor_ajuan}</td></tr>
                                        <tr><td><strong>Divisi:</strong></td><td>${ajuan.divisi.divisi}</td></tr>
                                        <tr><td><strong>Pengaju:</strong></td><td>${ajuan.pengaju.name}</td></tr>
                                        <tr><td><strong>Tanggal Ajuan:</strong></td><td>${new Date(ajuan.created_at).toLocaleString('id-ID')}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Informasi Barang</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>Kode Barang:</strong></td><td>${ajuan.stok_pusat.kode_barang}</td></tr>
                                        <tr><td><strong>Nama Barang:</strong></td><td>${ajuan.stok_pusat.nama_barang}</td></tr>
                                        <tr><td><strong>Jumlah Diminta:</strong></td><td>${ajuan.jumlah_diminta} ${ajuan.stok_pusat.satuan}</td></tr>
                                        <tr><td><strong>Jumlah Diberikan:</strong></td><td>${ajuan.jumlah_diberikan ? ajuan.jumlah_diberikan + ' ' + ajuan.stok_pusat.satuan : '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                            ${ajuan.keterangan ? `<div class="mb-4"><h6>Keterangan:</h6><p>${ajuan.keterangan}</p></div>` : ''}
                            
                            <!-- Approval Details -->
                            ${ajuan.approved_by_ga ? `
                                <div class="mb-4">
                                    <h6>Approval GA</h6>
                                    <p><strong>Disetujui oleh:</strong> ${ajuan.approved_by_ga.name}</p>
                                    <p><strong>Tanggal:</strong> ${new Date(ajuan.approved_at_ga).toLocaleString('id-ID')}</p>
                                    ${ajuan.keterangan_ga ? `<p><strong>Keterangan:</strong> ${ajuan.keterangan_ga}</p>` : ''}
                                </div>
                            ` : ''}
                            
                            ${ajuan.approved_by_kabag ? `
                                <div class="mb-4">
                                    <h6>Approval Kabag</h6>
                                    <p><strong>Disetujui oleh:</strong> ${ajuan.approved_by_kabag.name}</p>
                                    <p><strong>Tanggal:</strong> ${new Date(ajuan.approved_at_kabag).toLocaleString('id-ID')}</p>
                                    ${ajuan.keterangan_kabag ? `<p><strong>Keterangan:</strong> ${ajuan.keterangan_kabag}</p>` : ''}
                                </div>
                            ` : ''}
                            
                            ${ajuan.rejected_by ? `
                                <div class="mb-4">
                                    <h6>Informasi Penolakan</h6>
                                    <p><strong>Ditolak oleh:</strong> ${ajuan.rejected_by.name}</p>
                                    <p><strong>Tanggal:</strong> ${new Date(ajuan.rejected_at).toLocaleString('id-ID')}</p>
                                    ${ajuan.alasan_reject ? `<p><strong>Alasan:</strong> ${ajuan.alasan_reject}</p>` : ''}
                                </div>
                            ` : ''}
                            
                            <div>
                                <h6>Timeline Approval</h6>
                                ${timelineHtml}
                            </div>
                        `;
                        
                        $('#detailContent').html(detailHtml);
                    },
                    error: function(xhr) {
                        $('#loadingDetail').addClass('d-none');
                        $('#detailContent').removeClass('d-none').html('<p class="text-danger">Gagal memuat detail ajuan.</p>');
                    }
                });
            });
        });
    </script>
@endpush
@endsection