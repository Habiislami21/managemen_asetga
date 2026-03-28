
@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('content')
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2">
            <h3 class="card-title mb-0">Daftar Aduan</h3>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('aduan.export') }}" class="btn btn-success btn-sm me-3">
                <i class="fas fa-file-excel"></i> Export ke Excel
            </a>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="myTable" class="table table-striped display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama SPA</th>
                            <th>Divisi</th>
                            <th>Amanah</th>
                            <th>Lokasi</th>
                            <th>Jenis Aduan</th>
                            <th>Kendala</th>
                            <th>Rincian</th>
                            <th>Nomor Telp</th>
                            <th>Tanggal Aduan Dibuat</th>
                            <th>Update Aduan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aduans as $index => $aduan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $aduan->nama_spa }}</td>
                            <td>{{ $aduan->divisi->divisi ?? 'Divisi tidak tersedia' }}</td>
                            <td>{{ $aduan->amanah }}</td>
                            <td>{{ $aduan->lokasi_pengaduan }}</td>
                            <td>{{ $aduan->jenis_pengaduan }}</td>
                            <td>{{ $aduan->kerusakan }}</td>
                            <td>{{ $aduan->rincian_pengaduan }}</td>
                            <td>{{ $aduan->nomor_telp }}</td>
                            <td>{{ $aduan->created_at->format('d-m-Y') }}</td>
                            <td class="status-cell" data-id="{{ $aduan->id }}" data-status="{{ $aduan->status }}">
                                @if($aduan->status == 'selesai')
                                    <button class="btn btn-success btn-sm toggle-status" data-id="{{ $aduan->id }}" data-status="selesai">
                                        Selesai
                                    </button>
                                @else
                                    <button class="btn btn-warning btn-sm toggle-status" data-id="{{ $aduan->id }}" data-status="pending">
                                        Pending
                                    </button>
                                @endif
                            </td>                            
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-2">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $(document).on('click', '.toggle-status', function(e) {
                e.preventDefault();
                
                let button = $(this);
                let aduanId = button.data('id');
                let currentStatus = button.data('status');
                
                button.prop('disabled', true);
    
                $.ajax({
                    url: `/admin/aduan/${aduanId}/update-status`,
                    type: 'PATCH',
                    success: function(response) {
                        // Jika status berhasil diubah
                        if (response.new_status === 'selesai') {
                            // Ubah tombol ke "Tandai Belum Selesai" dengan warna hijau
                            button.removeClass('btn-warning').addClass('btn-success');
                            button.text('Selesai');
                            button.data('status', 'selesai');
                        } else {
                            // Ubah tombol ke "Tandai Selesai" dengan warna kuning
                            button.removeClass('btn-success').addClass('btn-warning');
                            button.text('Pending');
                            button.data('status', 'pending');
                        }
                        button.prop('disabled', false);
                    },
                    error: function(xhr) {
                        button.prop('disabled', false);
                        alert(xhr.responseJSON?.message || 'Gagal memperbarui status!');
                    }
                });
            });
        });
    </script>
@endpush
@endsection