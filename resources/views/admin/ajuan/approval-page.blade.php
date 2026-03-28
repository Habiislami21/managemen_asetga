@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
@endpush

@section('content')
@php
$user = Auth::user();
$isGA = $user->role === 'ga';
$isKabag = $user->role === 'kabag';
$isAdmin = in_array($user->role, ['admin', 'aset']);
@endphp

<div class="container-fluid h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Verifikasi Ajuan Stok Divisi</h2>
        <button onclick="window.location.reload()" class="btn btn-primary btn-sm"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
    </div>

    @if(!$isAdmin)
    <!-- Tabs for Kabag and GA -->
    <ul class="nav nav-tabs mb-4" id="approvalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tahap1-tab" data-bs-toggle="tab" data-bs-target="#tahap1" type="button" role="tab">
                Tahap 1 
                <span class="badge bg-danger ms-1">{{ $ajuansPending->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tahap2-tab" data-bs-toggle="tab" data-bs-target="#tahap2" type="button" role="tab">
                Tahap 2
                <span class="badge bg-danger ms-1">{{ $ajuansPending2->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">Riwayat</button>
        </li>
    </ul>

    <div class="tab-content" id="approvalTabsContent">
        <!-- Tahap 1 -->
        <div class="tab-pane fade show active" id="tahap1" role="tabpanel">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0 text-white">
                        @if($isKabag) Menunggu Verifikasi Awal Kabag (Dari PJ Divisi) @endif
                        @if($isGA) Menunggu Keputusan Rapat Anggaran GA @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>No. Ajuan</th>
                                    <th>Divisi</th>
                                    <th>Pengaju</th>
                                    <th>Barang</th>
                                    <th>Jml Diminta</th>
                                    <th>Tgl Ajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ajuansPending as $ajuan)
                                <tr>
                                    <td>{{ $ajuan->nomor_ajuan }}</td>
                                    <td>{{ $ajuan->divisi->divisi }}</td>
                                    <td>{{ $ajuan->pengaju->name }}</td>
                                    <td>{{ $ajuan->stokPusat->nama_barang }}</td>
                                    <td>{{ $ajuan->jumlah_diminta }} {{ $ajuan->stokPusat->satuan }}</td>
                                    <td>{{ $ajuan->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-detail" data-id="{{ $ajuan->id }}"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-success btn-approve" data-id="{{ $ajuan->id }}" data-action="approve" data-title="Setujui Tahap 1"><i class="bi bi-check"></i></button>
                                        <button class="btn btn-sm btn-danger btn-reject" data-id="{{ $ajuan->id }}"><i class="bi bi-x"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tahap 2 -->
        <div class="tab-pane fade" id="tahap2" role="tabpanel">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title mb-0 text-white">
                        @if($isKabag) Menunggu Verifikasi Ulang Kabag (Hasil Input Admin) @endif
                        @if($isGA) Menunggu Finalisasi dan Pemotongan Stok (Selesai Kabag 2) @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>No. Ajuan</th>
                                    <th>Divisi</th>
                                    <th>Barang</th>
                                    <th>Inputan Admin/Stok Jatah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ajuansPending2 as $ajuan)
                                <tr>
                                    <td>{{ $ajuan->nomor_ajuan }}</td>
                                    <td>{{ $ajuan->divisi->divisi }}</td>
                                    <td>{{ $ajuan->stokPusat->nama_barang }}</td>
                                    <td>
                                        <strong>{{ $ajuan->jumlah_diberikan }} {{ $ajuan->stokPusat->satuan }}</strong>
                                        <br><small class="text-muted">Stok Pusat Sisa: {{ $ajuan->stokPusat->sisa_stok }}</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-detail" data-id="{{ $ajuan->id }}"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-success btn-approve" data-id="{{ $ajuan->id }}" data-action="approve" data-title="Setujui Tahap 2"><i class="bi bi-check"></i></button>
                                        <button class="btn btn-sm btn-danger btn-reject" data-id="{{ $ajuan->id }}"><i class="bi bi-x"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat -->
        <div class="tab-pane fade" id="riwayat" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Ajuan</h5>
                </div>
                <div class="card-body">
                    <table class="table datatable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatAjuan as $ajuan)
                            <tr>
                                <td>{{ $ajuan->nomor_ajuan }}</td>
                                <td>{{ $ajuan->stokPusat->nama_barang }}</td>
                                <td><span class="{{ $ajuan->status_badge_class }} badge">{{ $ajuan->status_label }}</span></td>
                                <td><button class="btn btn-sm btn-info btn-detail" data-id="{{ $ajuan->id }}"><i class="bi bi-eye"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Admin View -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Menunggu Input Stok (Tahap 3 - Lolos GA)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped datatable" width="100%">
                        <thead>
                            <tr>
                                <th>No. Ajuan</th>
                                <th>Divisi</th>
                                <th>Barang</th>
                                <th>Jml Diminta</th>
                                <th>Stok Pusat Tersedia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ajuansPending as $ajuan)
                            <tr>
                                <td>{{ $ajuan->nomor_ajuan }}</td>
                                <td>{{ $ajuan->divisi->divisi }}</td>
                                <td>{{ $ajuan->stokPusat->nama_barang }}</td>
                                <td>{{ $ajuan->jumlah_diminta }} {{ $ajuan->stokPusat->satuan }}</td>
                                <td>{{ $ajuan->stokPusat->sisa_stok }} {{ $ajuan->stokPusat->satuan }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-id="{{ $ajuan->id }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-primary btn-input" data-id="{{ $ajuan->id }}" data-stok="{{ $ajuan->stokPusat->sisa_stok }}" data-req="{{ $ajuan->jumlah_diminta }}"><i class="bi bi-pencil"></i> Input Realisasi</button>
                                    <button class="btn btn-sm btn-danger btn-reject" data-id="{{ $ajuan->id }}"><i class="bi bi-x"></i> Tolak</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- History Admin -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Proses Admin</h5>
                </div>
                <div class="card-body">
                    <table class="table datatable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Status</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatAjuan as $ajuan)
                            <tr>
                                <td>{{ $ajuan->nomor_ajuan }}</td>
                                <td>{{ $ajuan->stokPusat->nama_barang }}</td>
                                <td><span class="{{ $ajuan->status_badge_class }} badge">{{ $ajuan->status_label }}</span></td>
                                <td><button class="btn btn-sm btn-info btn-detail" data-id="{{ $ajuan->id }}"><i class="bi bi-eye"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Process Generic (Approve & Input) -->
<div class="modal fade" id="modalProcess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProcessTitle">Proses Ajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProcess">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="process_ajuan_id" name="ajuan_id">
                    <input type="hidden" id="process_action" name="action" value="approve">
                    
                    <div id="input_realisasi_group" style="display:none;" class="mb-3">
                        <label>Jumlah Realisasi (Diberikan) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah_diberikan" id="input_jumlah" min="1">
                        <small class="text-muted" id="input_max_info"></small>
                    </div>

                    <div class="mb-3">
                        <label>Keterangan/Catatan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan & Lanjutkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Ajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReject">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="reject_ajuan_id" name="ajuan_id">
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="mb-3">
                        <label>Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Tolak Ajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail & Timeline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">Loading...</div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }
    });

    $('.btn-approve').click(function() {
        $('#process_ajuan_id').val($(this).data('id'));
        $('#process_action').val('approve');
        $('#input_realisasi_group').hide();
        $('#modalProcessTitle').text($(this).data('title'));
        $('#input_jumlah').removeAttr('required');
        $('#modalProcess').modal('show');
    });

    $('.btn-input').click(function() {
        $('#process_ajuan_id').val($(this).data('id'));
        $('#process_action').val('input');
        $('#input_realisasi_group').show();
        $('#modalProcessTitle').text('Input Stok Realisasi');
        
        const req = $(this).data('req');
        const stok = $(this).data('stok');
        const max = Math.min(req, stok);
        $('#input_jumlah').attr('max', max).attr('required', 'required').val(max);
        $('#input_max_info').text('Maksimal disetujui: ' + max);
        
        $('#modalProcess').modal('show');
    });

    $('.btn-reject').click(function() {
        $('#reject_ajuan_id').val($(this).data('id'));
        $('#modalReject').modal('show');
    });

    $('#formProcess, #formReject').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: '{{ route("approval.proses") }}',
            method: 'POST',
            data: form.serialize(),
            success: function(res) {
                if(res.success) {
                    Swal.fire('Berhasil', res.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function(err) {
                let msg = 'Terjadi kesalahan sistem';
                if(err.responseJSON && err.responseJSON.message) msg = err.responseJSON.message;
                Swal.fire('Gagal', msg, 'error');
            }
        });
    });

    $('.btn-detail').click(function() {
        $('#detailContent').html('Loading...');
        $('#modalDetail').modal('show');
        $.get(`{{ url('/ajuan-stok/detail') }}/${$(this).data('id')}`, function(res) {
            if(res.success) {
                let html = '<div class="row"><div class="col-md-6 mb-3"><strong>Pengaju:</strong> ' + (res.ajuan.pengaju?res.ajuan.pengaju.name:'-') + '<br><strong>Barang:</strong> ' + res.ajuan.stok_pusat.nama_barang + '<br><strong>Jml Diminta:</strong> ' + res.ajuan.jumlah_diminta + ' ' + res.ajuan.stok_pusat.satuan + '<br><strong>Jml Diberikan:</strong> ' + (res.ajuan.jumlah_diberikan||'Belum Diinput') + '</div><div class="col-md-6"><h5>Timeline Status</h5><ul class="list-group">';
                
                res.timeline.forEach(t => {
                    let color = t.status == 'completed' ? 'success' : (t.status == 'pending' ? 'warning' : 'danger');
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div><i class="bi ${t.icon} text-${color}"></i> ${t.title}<br><small>${t.description}</small></div>
                                ${t.date ? '<span class="badge bg-secondary">'+new Date(t.date).toLocaleDateString("id-ID")+'</span>' : ''}
                             </li>`;
                });
                html += '</ul></div></div>';
                $('#detailContent').html(html);
            }
        });
    });
});
</script>
@endpush
@endsection