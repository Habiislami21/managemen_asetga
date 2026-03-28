@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endpush

@section('header')
    <h3>Stok Opname - Divisi</h3>
@endsection

@section('content')
@php
$user = Auth::user();
$isPjDivisi = $user->role === 'pj_divisi';
$isAdmin = $user->role === 'admin';
$isGA = $user->role === 'ga';
$isAset = $user->role === 'aset';
@endphp

<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                Daftar Stok Per Divisi
                @if($isPjDivisi && $user->divisi)
                    <span class="badge bg-info ms-2">{{ $user->divisi->divisi }}</span>
                @endif
            </h3>
            <div class="d-flex">
                @if($isPjDivisi)
                    <input type="hidden" id="divisiFilter" value="{{ $user->divisi_id }}">
                    <span class="form-control-plaintext me-2">
                        <strong>Divisi Anda:</strong> {{ $user->divisi->divisi ?? 'Tidak ada divisi' }}
                    </span>
                @else
                    <select id="divisiFilter" class="form-control w-auto me-2">
                        <option value="">Pilih Divisi</option>
                        @foreach($divisis as $divisi)
                            <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                        @endforeach
                    </select>
                @endif
        
                <button id="btnExportExcel" class="btn btn-success me-2" style="display: none;">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
        
                @if($isAdmin || $isGA || $isAset)
                    <button id="btnTambahBarang" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTambahBarang" style="display: none;">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                @endif
        
                {{-- <a href="{{ route('ajuan.form') }}" class="btn btn-primary me-2">
                    <i class="fas fa-plus"></i> Tambah Ajuan
                </a> --}}
            </div>
        </div>
        
        <form id="exportForm" action="{{ route('divisi.export') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="divisi_id" id="export_divisi_id">
        </form>
        <div class="card-body p-3">
            @if($isPjDivisi && !$user->divisi_id)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Akun Anda belum terkait dengan divisi manapun. 
                    Silakan hubungi administrator untuk mengatur divisi Anda.
                </div>
            @else
                <div id="noDivisiSelected" class="text-center" style="{{ $isPjDivisi ? 'display: none;' : '' }}">
                    <p>Silakan pilih divisi untuk melihat data stok.</p>
                </div>
                <div id="tableContainer" class="table-responsive" style="{{ $isPjDivisi ? '' : 'display: none;' }}">
                    <table id="stokDivisiTable" class="table table-striped display nowrap" width="100%">
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
                            <!-- Data akan diisi melalui AJAX -->
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Stok -->
    <div class="modal fade" id="modalStok" tabindex="-1" aria-labelledby="modalStokLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStokLabel">Kurangi Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUpdateStok">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="stok_id" name="stok_id">
                        <input type="hidden" id="tipe" name="tipe">
                        <div class="mb-3" id="stokPusatInfo" style="display: none;">
                            <p class="text-info">Stok tersedia di pusat: <span id="stokPusatValue">0</span></p>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Yang Dikurangi</label>
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

    @if($isAdmin || $isGA || $isAset)
    <!-- Modal Tambah Barang dari Asset & GA - Hanya untuk Admin, GA, Aset -->
    <div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-labelledby="modalTambahBarangLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahBarangLabel">Tambah Barang dari Asset & GA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formTambahBarang">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="selected_divisi_id" name="divisi_id">
                        <div class="mb-3">
                            <label for="barang_asset_ga" class="form-label">Pilih Barang dari Asset & GA</label>
                            <select class="form-control select2" id="barang_asset_ga" name="kode_barang" required>
                                <option value="">Pilih Barang</option>
                                <!-- Options will be loaded via AJAX -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="preview_nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="preview_nama_barang" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="preview_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="preview_satuan" disabled>
                        </div>
                         <div class="mb-3">
                            <p class="form-text text-muted">
                                Stok awal akan diatur sebagai 0 (nol). Anda dapat menambahkan stok setelah item ditambahkan ke divisi.
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Tambahkan ke Divisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Edit Stok Ideal -->
    <div class="modal fade" id="modalEditStokIdeal" tabindex="-1" aria-labelledby="modalEditStokIdealLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditStokIdealLabel">Edit Stok Ideal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditStokIdeal">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="edit_stok_id" name="stok_id">
                        <div class="mb-3">
                            <label for="stok_ideal" class="form-label">Stok Ideal</label>
                            <input type="number" class="form-control" name="stok_ideal" id="edit_stok_ideal" required min="0">
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
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let selectedDivisiId = null;
            let stokDivisiTable = null;
            const isPjDivisi = {{ $isPjDivisi ? 'true' : 'false' }};
            const userDivisiId = {{ $user->divisi_id ?? 'null' }};

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

            // Initialize Select2 for barang selection (hanya jika modal ada)
            if ($('#barang_asset_ga').length) {
                $('#barang_asset_ga').select2({
                    dropdownParent: $('#modalTambahBarang'),
                    placeholder: "Pilih Barang",
                    width: '100%'
                });
            }

            // Initialize DataTable
            stokDivisiTable = $('#stokDivisiTable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferLoading: 0,
                ajax: {
                    url: '{{ route("get-stok-divisi") }}',
                    method: 'GET',
                    data: function(d) {
                        d.divisi_id = selectedDivisiId;
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 403) {
                            showToast('error', 'Akses Ditolak', 'Anda tidak memiliki akses ke data divisi ini.');
                        }
                    }
                },
                columns: [
                    { data: 'kode_barang' },
                    { data: 'nama_barang' },
                    { data: 'sisa_stok' },
                    { data: 'stok_ideal' },
                    { data: 'satuan' },
                    { data: 'kekurangan' },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            const deleteButton = {{ $isAdmin || $isGA || $isAset ? 'true' : 'false' }} ? 
                                `<button class="btn btn-sm btn-danger btn-hapus-barang btn-icon" 
                                        data-id="${data.id}" 
                                        data-nama="${data.nama_barang}"
                                        title="Hapus Barang">
                                    <i class="bi bi-trash"></i>
                                </button>` : '';
                            
                            return `
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-warning btn-kurang-stok btn-icon" 
                                            data-id="${data.id}" 
                                            data-kode="${data.kode_barang}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalStok" 
                                            data-tipe="keluar"
                                            title="Kurangi Stok">
                                        <i class="bi bi-box-arrow-up"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary btn-edit-stok-ideal btn-icon" 
                                        data-id="${data.id}" 
                                        data-stok-ideal="${data.stok_ideal}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditStokIdeal" 
                                        title="Edit Stok Ideal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                ${deleteButton}
                                </div>
                            `;                          
                        }
                    }
                ],
                language: {
                    emptyTable: "Pilih divisi untuk melihat data stok"
                }
            });

            // Auto-load data for PJ Divisi
            if (isPjDivisi && userDivisiId) {
                selectedDivisiId = userDivisiId;
                $('#selected_divisi_id').val(selectedDivisiId);
                $('#export_divisi_id').val(selectedDivisiId);
                $('#btnExportExcel').show();
                
                // Load table data
                stokDivisiTable.ajax.reload();
                
                // Load barang options from Asset & GA (hanya jika user bisa menambah barang)
                @if($isAdmin || $isGA || $isAset)
                loadAssetGABarang();
                @endif
            }

            // Divisi Filter Change Event (hanya untuk non-PJ Divisi)
            $('#divisiFilter').change(function() {
                selectedDivisiId = $(this).val();
                
                if (selectedDivisiId) {
                    $('#noDivisiSelected').hide();
                    $('#tableContainer').show();
                    $('#btnTambahBarang').show();
                    $('#btnExportExcel').show();
                    $('#selected_divisi_id').val(selectedDivisiId);
                    $('#export_divisi_id').val(selectedDivisiId);

                    // Reload DataTable with selected divisi
                    stokDivisiTable.ajax.reload();

                    // Load barang options from Asset & GA (hanya jika user bisa menambah barang)
                    @if($isAdmin || $isGA || $isAset)
                    loadAssetGABarang();
                    @endif
                } else {
                    $('#noDivisiSelected').show();
                    $('#tableContainer').hide();
                    $('#btnTambahBarang').hide();
                    $('#btnExportExcel').hide();
                }
            });

            // Function to load Asset & GA barang options
            @if($isAdmin || $isGA || $isAset)
            function loadAssetGABarang() {
                $.ajax({
                    url: '{{ route("get-asset-ga-barang") }}',
                    method: 'GET',
                    success: function(response) {
                        let options = '<option value="">Pilih Barang</option>';
                        response.forEach(function(barang) {
                            options += `<option value="${barang.kode_barang}" 
                                data-nama="${barang.nama_barang}" 
                                data-stok-ideal="${barang.stok_ideal}" 
                                data-satuan="${barang.satuan}">
                                ${barang.kode_barang} - ${barang.nama_barang}
                            </option>`;
                        });
                        $('#barang_asset_ga').html(options);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal memuat data barang dari Asset & GA.';
                            
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
            @endif

            // Barang Selection Change Event (hanya jika modal ada)
            $('#barang_asset_ga').change(function() {
                const selectedOption = $(this).find('option:selected');
                
                if (selectedOption.val()) {
                    $('#preview_nama_barang').val(selectedOption.data('nama'));
                    $('#preview_satuan').val(selectedOption.data('satuan'));
                } else {
                    // Reset preview fields jika tidak ada barang yang dipilih
                    $('#preview_nama_barang').val('');
                    $('#preview_stok_ideal').val('');
                    $('#preview_satuan').val('');
                }
            });

            // Form Tambah Barang Submit (hanya jika form ada)
            $('#formTambahBarang').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("tambah-barang-divisi") }}',
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        divisi_id: $('#selected_divisi_id').val(),
                        kode_barang: $('#barang_asset_ga').val(),
                    },
                    success: function(response) {
                        $('#formTambahBarang')[0].reset();
                        $('#barang_asset_ga').val('').trigger('change');
                        
                        // Reset preview fields
                        $('#preview_nama_barang').val('');
                        $('#preview_stok_ideal').val('');
                        $('#preview_satuan').val('');

                        $('#modalTambahBarang').modal('hide');
                        showToast('success', 'Berhasil!', 'Barang berhasil ditambahkan ke divisi');
                        // Reload DataTable
                        stokDivisiTable.ajax.reload();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menambahkan barang.';
                        
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

            // Set up event handlers for the stok modal
            $(document).on('click', '.btn-tambah-stok, .btn-kurang-stok', function() {
                const stokId = $(this).data('id');
                const tipe = $(this).data('tipe');
                const kodeBarang = $(this).data('kode');
                
                $('#stok_id').val(stokId);
                $('#tipe').val(tipe);
                
                // If it's adding stock (taking from central), show available central stock
                if (tipe === 'masuk') {
                    // Get available stock from central storage
                    $.ajax({
                        url: '{{ route("get-stok-pusat-info") }}',
                        method: 'GET',
                        data: { kode_barang: kodeBarang },
                        success: function(response) {
                            $('#stokPusatValue').text(response.sisa_stok + ' ' + response.satuan);
                            $('#stokPusatInfo').show();
                        },
                        error: function() {
                            $('#stokPusatInfo').hide();
                        }
                    });
                } else {
                    $('#stokPusatInfo').hide();
                }
            });

            // Form Update Stok Submit
            $('#formUpdateStok').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("update-stok-divisi") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modalStok').modal('hide');
                        $('#formUpdateStok')[0].reset();
                        
                        // Tampilkan toast notification sukses
                        showToast('success', 'Berhasil!', response.message);
                        
                        // Reload DataTable
                        stokDivisiTable.ajax.reload();
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

            // Tambahkan event listener untuk modal saat dibuka (hanya jika modal ada)
            $('#modalTambahBarang').on('show.bs.modal', function() {
                // Reset form
                $('#formTambahBarang')[0].reset();
                
                // Reset Select2
                $('#barang_asset_ga').val('').trigger('change');
                
                // Reset preview fields
                $('#preview_nama_barang').val('');
                $('#preview_stok_ideal').val('');
                $('#preview_satuan').val('');
            });

            // Set up event handler untuk edit stok ideal
            $(document).on('click', '.btn-edit-stok-ideal', function() {
                const stokId = $(this).data('id');
                const stokIdeal = $(this).data('stok-ideal');
                
                $('#edit_stok_id').val(stokId);
                $('#edit_stok_ideal').val(stokIdeal);
            });

            // Form Edit Stok Ideal Submit
            $('#formEditStokIdeal').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '{{ route("update-stok-ideal-divisi") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modalEditStokIdeal').modal('hide');
                        $('#formEditStokIdeal')[0].reset();
                        
                        // Tampilkan toast notification sukses
                        showToast('success', 'Berhasil!', response.message);
                        
                        // Reload DataTable
                        stokDivisiTable.ajax.reload();
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

            // Set up event handler untuk hapus barang (hanya untuk role yang diizinkan)
            @if($isAdmin || $isGA || $isAset)
            $(document).on('click', '.btn-hapus-barang', function() {
                const stokId = $(this).data('id');
                const namaBrg = $(this).data('nama');
                
                // Konfirmasi sebelum menghapus
                Swal.fire({
                    title: 'Hapus Barang?',
                    text: `Apakah Anda yakin ingin menghapus ${namaBrg} dari divisi ini?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim permintaan hapus ke server
                        $.ajax({
                            url: '{{ route("hapus-stok-divisi") }}',
                            method: 'POST',
                            data: {
                                _token: $('input[name="_token"]').val(),
                                id: stokId
                            },
                            success: function(response) {
                                // Tampilkan notifikasi sukses
                                showToast('success', 'Berhasil!', response.message);
                                
                                // Reload DataTable
                                stokDivisiTable.ajax.reload();
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan saat menghapus barang.';
                                
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
                    }
                });
            });
            @endif

            $('#btnExportExcel').click(function() {
                $('#export_divisi_id').val(selectedDivisiId);
                $('#exportForm').submit();
            });
        });
    </script>
@endpush
@endsection