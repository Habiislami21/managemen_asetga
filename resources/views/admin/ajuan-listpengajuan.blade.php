@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('content')
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2">
            <h3 class="card-title mb-0">Daftar Ajuan</h3>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('ajuan.export') }}" class="btn btn-success btn-sm me-3">
                <i class="fas fa-file-excel"></i> Export ke Excel
            </a>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="ajuanTable" class="display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama SPA</th>
                            <th>Divisi</th>
                            <th>Barang Ajuan</th>
                            <th>Kategori Barang</th>
                            <th>Banyak Barang</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>Nomor Telp</th>
                            <th>Tanggal Ajuan Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ajuans as $index => $ajuan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ajuan->nama_spa }}</td>
                            <td>{{ $ajuan->divisi->divisi ?? 'Divisi tidak tersedia' }}</td>
                            <td>{{ $ajuan->barang_ajuan }}</td>
                            <td>
                                <span class="badge {{ $ajuan->kategori_barang == 'RTK' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $ajuan->kategori_barang }}
                                </span>
                            </td>
                            <td>{{ $ajuan->banyak_barang }}</td>
                            <td>{{ $ajuan->satuan }}</td>
                            <td>Rp {{ number_format($ajuan->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($ajuan->total, 0, ',', '.') }}</td>
                            <td>{{ $ajuan->nomor_telp }}</td>
                            <td>{{ $ajuan->created_at->format('d-m-Y') }}</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-2">Tidak ada data</td>
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
            let table = $('#ajuanTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                }
            });
        });
    </script>
@endpush
@endsection
