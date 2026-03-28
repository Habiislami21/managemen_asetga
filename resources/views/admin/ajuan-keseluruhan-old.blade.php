@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('header')
    <h3>Stok Opname - Keseluruhan</h3>
@endsection

@section('content')
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Daftar Stok</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                + Tambah Barang
            </button>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="stokTable" class="table table-striped display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Sisa Stok</th>
                            <th>Stok Ideal</th>
                            <th>Satuan</th>
                            <th>Kekurangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stoks as $stok)
                        <tr data-id="{{ $stok->id }}">
                            <td>{{ $stok->kode_barang }}</td>
                            <td>{{ $stok->nama_barang }}</td>
                            <td>{{ $stok->sisa_stok }}</td>
                            <td>{{ $stok->stok_ideal }}</td>
                            <td>{{ $stok->satuan }}</td>
                            <td>{{ max(0, $stok->stok_ideal - $stok->sisa_stok) }}</td>
                            <td>
                                <button class="btn btn-success btn-sm stok-btn btn-icon" data-id="{{ $stok->id }}" data-bs-toggle="modal" data-bs-target="#modalStok" data-tipe="masuk" data-bs-toggle="tooltip" title="Barang Masuk">
                                    <i class="bi bi-box-arrow-in-down"></i>
                                </button>
                                <button class="btn btn-warning btn-sm stok-btn btn-icon" data-id="{{ $stok->id }}" data-bs-toggle="modal" data-bs-target="#modalStok" data-tipe="keluar" data-bs-toggle="tooltip" title="Barang Keluar">
                                    <i class="bi bi-box-arrow-up"></i>
                                </button>
                                <button class="btn btn-sm btn-primary stok-ideal-btn" data-id="{{ $stok->id }}" data-bs-toggle="modal" data-bs-target="#modalStokIdeal" data-stok-ideal="{{ $stok->stok_ideal }}" data-bs-toggle="tooltip" title="Edit Stok Ideal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $stok->id }}" data-bs-toggle="tooltip" title="Hapus Stok">
                                    <i class="bi bi-trash"></i> 
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-labelledby="modalTambahBarangLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBarangLabel">Tambah Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div id="success_message" class="alert alert-success d-none"></div> 
                <form id="formTambahBarang">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang</label>
                            <input type="number" class="form-control" id="kode_barang" name="kode_barang" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="divisi_id" class="form-label">Divisi</label>
                            <input type="text" class="form-control" value="Asset & GA (Pusat/Gudang)" readonly>
                            <input type="hidden" name="divisi_id" value="{{ $assetGADivisi->id }}">
                            <small class="form-text text-muted">Barang baru selalu ditambahkan ke Asset & GA sebagai pusat/gudang</small>
                        </div>
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang" required>
                        </div>
                        <div class="mb-3">
                            <label for="sisa_stok" class="form-label">Jumlah Stok</label>
                            <input type="number" class="form-control" name="sisa_stok" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="stok_ideal" class="form-label">Stok Ideal</label>
                            <input type="number" class="form-control" name="stok_ideal" required min="0">
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <select name="satuan" class="form-control" required>
                                @foreach(App\Models\Stok::SATUAN as $satuan)
                                    <option value="{{ $satuan }}">{{ ucfirst($satuan) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalStok" tabindex="-1" aria-labelledby="modalStokLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalStokLabel">Update Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUpdateStok">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="stok_id" name="stok_id">
                    <input type="hidden" id="tipe" name="tipe">
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalStokIdeal" tabindex="-1" aria-labelledby="modalStokIdealLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalStokIdealLabel">Update Stok Ideal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUpdateStokIdeal">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="stok_id_ideal" name="stok_id_ideal">
                    <div class="mb-3">
                        <label for="stok_ideal" class="form-label">Stok Ideal</label>
                        <input type="number" class="form-control" name="stok_ideal" id="stok_ideal_input" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function () {
            const table = $('#stokTable').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                deferRender: true
            });

            $('#formTambahBarang').submit(function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('stok.store') }}",
                    method: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.data) {
                            const kekurangan = Math.max(0, response.data.stok_ideal - response.data.sisa_stok);
                            const id = response.data.id; // Pastikan ID dikembalikan dari controller
                            
                            // Buat tombol aksi dengan ID yang benar
                            const aksiButtons = `
                                <button class="btn btn-success btn-sm stok-btn btn-icon" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalStok" data-tipe="masuk" data-bs-toggle="tooltip" title="Barang Masuk">
                                    <i class="bi bi-box-arrow-in-down"></i>
                                </button>
                                <button class="btn btn-warning btn-sm stok-btn btn-icon" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalStok" data-tipe="keluar" data-bs-toggle="tooltip" title="Barang Keluar">
                                    <i class="bi bi-box-arrow-up"></i>
                                </button>
                                <button class="btn btn-sm btn-primary stok-ideal-btn btn-icon" data-id="${id}" data-bs-toggle="modal" data-bs-target="#modalStokIdeal" data-stok-ideal="${response.data.stok_ideal}" data-bs-toggle="tooltip" title="Edit Stok Ideal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn btn-icon" data-id="${id}" data-bs-toggle="tooltip" title="Hapus Stok">
                                    <i class="bi bi-trash"></i> 
                                </button>
                            `;
                            
                            // Tambahkan row ke DataTable dengan semua data termasuk tombol aksi
                            const newRow = table.row.add([
                                response.data.kode_barang,
                                response.data.nama_barang,
                                response.data.sisa_stok,
                                response.data.stok_ideal,
                                response.data.satuan,
                                kekurangan,
                                aksiButtons
                            ]).draw(false).node();
                            
                            // Tambahkan atribut data-id ke row
                            $(newRow).attr('data-id', id);

                            $('#formTambahBarang')[0].reset();
                            $('#modalTambahBarang').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data barang berhasil ditambahkan',
                                timer: 1500,
                                showConfirmButton: false,
                                position: 'top-end',
                                toast: true,
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan: ' + xhr.responseText,
                            position: 'top-end',
                            toast: true,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });

            $('#modalTambahBarang').on('show.bs.modal', function () {
                $.ajax({
                    url: "{{ route('generateKodeBarang') }}",
                    type: "GET",
                    success: function (response) {
                        $('#kode_barang').val(response.kode_barang);
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Gagal mengambil kode barang otomatis',
                            position: 'top-end',
                            toast: true,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });

            $('#kode_barang').on('change', function () {
                let kodeBarang = $(this).val();

                $.ajax({
                    url: "{{ route('cekKodeBarang') }}", 
                    type: "GET",
                    data: { kode_barang: kodeBarang },
                    success: function (response) {
                        if (response.exists) {
                            $('#kode_barang_error').text("Kode barang sudah digunakan!");
                            $('#kode_barang').addClass('is-invalid');
                        } else {
                            $('#kode_barang_error').text("");
                            $('#kode_barang').removeClass('is-invalid');
                        }
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data barang akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/stok/${id}`,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                table.row(row).remove().draw();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data barang berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    position: 'top-end',
                                    toast: true
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat menghapus data',
                                    position: 'top-end',
                                    toast: true,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            // Handle stok button click
            $(document).on('click', '.stok-btn', function() {
                const id = $(this).data('id');
                const tipe = $(this).data('tipe');
                
                $('#stok_id').val(id);
                $('#tipe').val(tipe);
                $('#modalStokLabel').text(tipe === 'masuk' ? 'Barang Masuk' : 'Barang Keluar');
                $('#jumlah').val(''); // Reset jumlah field
            });

            // Handle form update stok
            $('#formUpdateStok').submit(function(e) {
                e.preventDefault();
                const id = $('#stok_id').val();
                const tipe = $('#tipe').val();
                const jumlah = $('#jumlah').val();
                
                $.ajax({
                    url: `/stok/${id}/update-stok`,
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "jumlah": jumlah,
                        "tipe": tipe
                    },
                    success: function(response) {
                        if(response.data) {
                            // Get the row
                            const $row = $(`tr[data-id="${id}"]`);
                            
                            // Get the row index in the DataTable
                            const rowIndex = table.row($row).index();
                            
                            // Update the sisa_stok cell using DataTables API
                            table.cell(rowIndex, 2).data(response.data.sisa_stok);
                            
                            // Recalculate and update kekurangan
                            const kekurangan = Math.max(0, response.data.stok_ideal - response.data.sisa_stok);
                            table.cell(rowIndex, 5).data(kekurangan);
                            
                            // Redraw only the updated cells
                            table.draw(false);
                            
                            // Reset form and close modal
                            $('#modalStok').modal('hide');
                            $('#formUpdateStok')[0].reset();
                            
                            // Show success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: tipe === 'masuk' ? 'Stok berhasil ditambahkan!' : 'Stok berhasil dikurangi!',
                                timer: 1500,
                                showConfirmButton: false,
                                position: 'top-end',
                                toast: true
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui stok';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                            position: 'top-end',
                            toast: true,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Handle stok ideal button click
            $(document).on('click', '.stok-ideal-btn', function() {
                const id = $(this).data('id');
                const stokIdeal = $(this).data('stok-ideal');
                
                $('#stok_id_ideal').val(id);
                $('#stok_ideal_input').val(stokIdeal);
            });

            // Handle form update stok ideal
            $('#formUpdateStokIdeal').submit(function(e) {
                e.preventDefault();
                const id = $('#stok_id_ideal').val();
                const stokIdeal = $('#stok_ideal_input').val();
                
                $.ajax({
                    url: `/stok/${id}/update-stok-ideal`,
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "stok_ideal": stokIdeal
                    },
                    success: function(response) {
                        if(response.data) {
                            // Get the row
                            const $row = $(`tr[data-id="${id}"]`);
                            
                            // Get the row index in the DataTable
                            const rowIndex = table.row($row).index();
                            
                            // Update the stok_ideal cell using DataTables API
                            table.cell(rowIndex, 3).data(response.data.stok_ideal);
                            
                            // Recalculate and update kekurangan
                            const kekurangan = Math.max(0, response.data.stok_ideal - response.data.sisa_stok);
                            table.cell(rowIndex, 5).data(kekurangan);
                            
                            // Redraw only the updated cells
                            table.draw(false);
                            
                            // Update data-stok-ideal pada tombol
                            $(`.stok-ideal-btn[data-id="${id}"]`).data('stok-ideal', response.data.stok_ideal);
                            
                            // Reset form and close modal
                            $('#modalStokIdeal').modal('hide');
                            $('#formUpdateStokIdeal')[0].reset();
                            
                            // Show success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Stok ideal berhasil diperbarui!',
                                timer: 1500,
                                showConfirmButton: false,
                                position: 'top-end',
                                toast: true
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui stok ideal';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                            position: 'top-end',
                            toast: true,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
@endpush
@endsection
