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
        <div class="d-flex justify-content-between align-items-center mb-3 py-2 px-2">
            <h3 class="card-title mb-0">Daftar Stok</h3>
            <div>
                <a href="{{ route('stok.export') }}" class="btn btn-success me-2">
                    <i class="fas fa-file-excel"></i> Export ke Excel
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                    + Tambah Barang
                </button>
            </div>
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
                                <button class="btn btn-sm btn-primary edit-barang-btn" 
                                    data-id="{{ $stok->id }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditBarang" 
                                    data-nama-barang="{{ $stok->nama_barang }}"
                                    data-stok-ideal="{{ $stok->stok_ideal }}"
                                    data-satuan="{{ $stok->satuan }}"
                                    data-bs-toggle="tooltip" 
                                    title="Edit Barang">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                 <button class="btn btn-sm btn-info detail-stok-btn" data-id="{{ $stok->id }}" data-kode="{{ $stok->kode_barang }}" data-bs-toggle="modal" data-bs-target="#modalDetailStok" data-bs-toggle="tooltip" title="Detail Stok">
                                    <i class="bi bi-info-circle"></i>
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
<div class="modal fade" id="modalEditBarang" tabindex="-1" aria-labelledby="modalEditBarangLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditBarangLabel">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditBarang">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="edit_stok_id" name="stok_id">
                    <div class="mb-3">
                        <label for="edit_nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" id="edit_nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok_ideal" class="form-label">Stok Ideal</label>
                        <input type="number" class="form-control" name="stok_ideal" id="edit_stok_ideal" required min="0">
                    </div>
                    <div class="mb-3">
                        <label for="edit_satuan" class="form-label">Satuan</label>
                        <select name="satuan" class="form-control" id="edit_satuan" required>
                            @foreach(App\Models\Stok::SATUAN as $satuan)
                                <option value="{{ $satuan }}">{{ ucfirst($satuan) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetailStok" tabindex="-1" aria-labelledby="modalDetailStokLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailStokLabel">Detail Stok Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="spinner-border text-primary" role="status" id="loadingDetail">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="detailContent" class="d-none">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Kode Barang: <span id="detailKodeBarang"></span></h6>
                        </div>
                        <div class="col-md-6">
                            <h6>Nama Barang: <span id="detailNamaBarang"></span></h6>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Total Stok (Semua Divisi): <span id="detailTotalStok"></span> <span id="detailSatuan"></span></h6>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Divisi</th>
                                    <th>Stok</th>
                                    <th>Stok Ideal</th>
                                    <th>Kekurangan</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                                <!-- Data will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
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
            // Inisialisasi DataTable dengan bahasa Indonesia
            $('#stokTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });

            // Inisialisasi tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Fungsi untuk membuat kode barang acak antara 1-999
            function generateRandomCode() {
                return Math.floor(Math.random() * 999) + 1;
            }

            // Toast notification function
            function showToast(icon, title, text) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
                
                Toast.fire({
                    icon: icon,
                    title: text
                });
            }

            // Event saat tombol edit diklik
$(document).on('click', '.edit-barang-btn', function() {
    const id = $(this).data('id');
    const row = $(`tr[data-id="${id}"]`);
    
    // Ambil data dari baris tabel
    const namaBarang = row.find('td:eq(1)').text();
    const stokIdeal = row.find('td:eq(3)').text();
    const satuan = row.find('td:eq(4)').text();
    
    // Isi form edit
    $('#edit_stok_id').val(id);
    $('#edit_nama_barang').val(namaBarang);
    $('#edit_stok_ideal').val(stokIdeal);
    $('#edit_satuan').val(satuan.toLowerCase());
});

// Event submit form edit barang
$('#formEditBarang').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '/admin/stok/edit-barang',
        type: 'POST',
        data: formData,
        success: function(response) {
            // Tutup modal
            $('#modalEditBarang').modal('hide');
            
            // Update data di tabel
            const row = $(`tr[data-id="${response.stok.id}"]`);
            row.find('td:eq(1)').text(response.stok.nama_barang);
            row.find('td:eq(3)').text(response.stok.stok_ideal);
            row.find('td:eq(4)').text(response.stok.satuan);
            row.find('td:eq(5)').text(Math.max(0, response.stok.stok_ideal - response.stok.sisa_stok));
            
            // Update tombol edit barang dengan data baru
            row.find('.edit-barang-btn')
                .attr('data-nama-barang', response.stok.nama_barang)
                .attr('data-stok-ideal', response.stok.stok_ideal)
                .attr('data-satuan', response.stok.satuan);
            
            // Tampilkan toast notification sukses
            showToast('success', 'Berhasil!', response.message);
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat memperbarui barang.';
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            // Tampilkan SweetAlert error
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: errorMessage
            });
        }
    });
});

            // Event saat modal tambah barang dibuka
            $('#modalTambahBarang').on('show.bs.modal', function() {
                // Reset form
                $('#formTambahBarang')[0].reset();
                
                // Generate kode barang unik
                $.ajax({
                    url: '/admin/check-kode-barang',
                    type: 'GET',
                    success: function(response) {
                        $('#kode_barang').val(response.kode_barang);
                    },
                    error: function(error) {
                        console.error('Error generating code:', error);
                        $('#kode_barang').val(generateRandomCode());
                    }
                });
                
                // Sembunyikan pesan sukses jika ada
                $('#success_message').addClass('d-none');
            });

            // Event submit form tambah barang
            $('#formTambahBarang').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: '/admin/stok/tambah',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Tambahkan baris baru ke tabel
                        const newRow = `
                            <tr data-id="${response.stok.id}">
                                <td>${response.stok.kode_barang}</td>
                                <td>${response.stok.nama_barang}</td>
                                <td>${response.stok.sisa_stok}</td>
                                <td>${response.stok.stok_ideal}</td>
                                <td>${response.stok.satuan}</td>
                                <td>${Math.max(0, response.stok.stok_ideal - response.stok.sisa_stok)}</td>
                                <td>
                                    <button class="btn btn-success btn-sm stok-btn btn-icon" data-id="${response.stok.id}" data-bs-toggle="modal" data-bs-target="#modalStok" data-tipe="masuk" data-bs-toggle="tooltip" title="Barang Masuk">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm stok-btn btn-icon" data-id="${response.stok.id}" data-bs-toggle="modal" data-bs-target="#modalStok" data-tipe="keluar" data-bs-toggle="tooltip" title="Barang Keluar">
                                        <i class="bi bi-box-arrow-up"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary stok-ideal-btn" data-id="${response.stok.id}" data-bs-toggle="modal" data-bs-target="#modalStokIdeal" data-stok-ideal="${response.stok.stok_ideal}" data-bs-toggle="tooltip" title="Edit Stok Ideal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info detail-stok-btn" data-id="${response.stok.id}" data-kode="${response.stok.kode_barang}" data-bs-toggle="modal" data-bs-target="#modalDetailStok" data-bs-toggle="tooltip" title="Detail Stok">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${response.stok.id}" data-bs-toggle="tooltip" title="Hapus Stok">
                                        <i class="bi bi-trash"></i> 
                                    </button>
                                </td>
                            </tr>
                        `;
                        
                        // Tambahkan ke DataTable
                        const table = $('#stokTable').DataTable();
                        const rowNode = table.row.add($(newRow)[0]).draw().node();
                        
                        // Refresh tooltip
                        $('[data-bs-toggle="tooltip"]').tooltip();
                        
                        // Close modal immediately
                        $('#modalTambahBarang').modal('hide');
                        
                        // Tampilkan toast notification sukses
                        showToast('success', 'Berhasil!', response.message);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Tampilkan SweetAlert error
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMessage
                        });
                    }
                });
            });

            // Event saat tombol stok masuk/keluar diklik
            $(document).on('click', '.stok-btn', function() {
                const id = $(this).data('id');
                const tipe = $(this).data('tipe');
                
                $('#stok_id').val(id);
                $('#tipe').val(tipe);
                
                // Set judul modal sesuai tipe
                if (tipe === 'masuk') {
                    $('#modalStokLabel').text('Tambah Stok Barang');
                } else {
                    $('#modalStokLabel').text('Kurangi Stok Barang');
                }
                
                // Reset form
                $('#formUpdateStok')[0].reset();
            });

            // Event saat tombol stok ideal diklik
            $(document).on('click', '.stok-ideal-btn', function() {
                const id = $(this).data('id');
                const stokIdeal = $(this).data('stok-ideal');
                
                $('#stok_id_ideal').val(id);
                $('#stok_ideal_input').val(stokIdeal);
            });

            // Event submit form update stok
            $('#formUpdateStok').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: '/admin/stok/update-stok',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Tutup modal immediately
                        $('#modalStok').modal('hide');
                        
                        // Update data di tabel
                        const row = $(`tr[data-id="${response.stok.id}"]`);
                        row.find('td:eq(2)').text(response.stok.sisa_stok);
                        row.find('td:eq(5)').text(Math.max(0, response.stok.stok_ideal - response.stok.sisa_stok));
                        
                        // Tampilkan toast notification sukses
                        showToast('success', 'Berhasil!', response.message);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui stok.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Tampilkan SweetAlert error
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMessage
                        });
                    }
                });
            });

            // Event submit form update stok ideal
            $('#formUpdateStokIdeal').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: '/admin/stok/update-stok-ideal',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Tutup modal immediately
                        $('#modalStokIdeal').modal('hide');
                        
                        // Update data di tabel
                        const row = $(`tr[data-id="${response.stok.id}"]`);
                        row.find('td:eq(3)').text(response.stok.stok_ideal);
                        row.find('td:eq(5)').text(Math.max(0, response.stok.stok_ideal - response.stok.sisa_stok));
                        
                        // Update atribut data-stok-ideal pada tombol
                        row.find('.stok-ideal-btn').data('stok-ideal', response.stok.stok_ideal);
                        
                        // Tampilkan toast notification sukses
                        showToast('success', 'Berhasil!', response.message);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memperbarui stok ideal.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Tampilkan SweetAlert error
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMessage
                        });
                    }
                });
            });

            // Event saat tombol hapus diklik
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                
                // Konfirmasi penghapusan dengan SweetAlert
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data stok ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/stok/hapus/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Hapus baris dari tabel
                                const table = $('#stokTable').DataTable();
                                table.row($(`tr[data-id="${id}"]`)).remove().draw();
                                
                                // Tampilkan toast notification sukses
                                showToast('success', 'Terhapus!', response.message);
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menghapus stok.';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                // Tampilkan SweetAlert error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

        $(document).on('click', '.detail-stok-btn', function() {
            const id = $(this).data('id');
            const kodeBarang = $(this).data('kode');
            
            // Reset dan tampilkan loading
            $('#detailContent').addClass('d-none');
            $('#loadingDetail').removeClass('d-none');
            $('#detailTableBody').empty();
            
            // Ambil data detail stok dari server
            $.ajax({
                url: `/admin/stok/detail/${kodeBarang}`,
                type: 'GET',
                success: function(response) {
                    // Sembunyikan loading
                    $('#loadingDetail').addClass('d-none');
                    $('#detailContent').removeClass('d-none');
                    
                    // Isi data detail
                    $('#detailKodeBarang').text(response.stok_pusat.kode_barang);
                    $('#detailNamaBarang').text(response.stok_pusat.nama_barang);
                    $('#detailTotalStok').text(response.total_stok);
                    $('#detailSatuan').text(response.stok_pusat.satuan);
                    
                    // Isi tabel detail per divisi
                    let tableContent = '';
                    
                    // Tambahkan baris untuk Asset & GA (pusat)
                    tableContent += `
                        <tr class="table-primary">
                            <td><strong>${response.stok_pusat.divisi}</strong></td>
                            <td>${response.stok_pusat.sisa_stok}</td>
                            <td>${response.stok_pusat.stok_ideal}</td>
                            <td>${Math.max(0, response.stok_pusat.stok_ideal - response.stok_pusat.sisa_stok)}</td>
                        </tr>
                    `;
                    
                    // Tambahkan baris untuk setiap divisi
                    response.stok_divisi.forEach(item => {
                        tableContent += `
                            <tr>
                                <td>${item.divisi}</td>
                                <td>${item.sisa_stok}</td>
                                <td>${item.stok_ideal}</td>
                                <td>${Math.max(0, item.stok_ideal - item.sisa_stok)}</td>
                            </tr>
                        `;
                    });
                    
                    $('#detailTableBody').html(tableContent);
                },
                error: function(xhr) {
                    // Sembunyikan loading
                    $('#loadingDetail').addClass('d-none');
                    
                    // Tampilkan SweetAlert error
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat mengambil detail stok.'
                    });
                }
            });
        });
        });
    </script>
@endpush
@endsection
