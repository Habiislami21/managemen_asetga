@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <!-- Pastikan Sweet Alert 2 tersedia -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('header')
    {{-- <h3>Ajukan Pengambilan Stok</h3> --}}
@endsection

@section('content')
@php
$user = Auth::user();
@endphp

<!-- Pastikan CSRF token tersedia -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid h-100">
    <!-- Form Ajuan Baru -->
    <div class="card mb-4">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                Pengambilan Stok
                <span class="badge bg-info ms-2">{{ $user->divisi->divisi }}</span>
            </h3>
        </div>
        <div class="card-body p-3">
            <form id="formAjuanStok">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stok_pusat_id" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="stok_pusat_id" name="stok_pusat_id" required>
                                <option value="">Pilih Barang</option>
                                @foreach($stokPusats as $stok)
                                    <option value="{{ $stok->id }}" 
                                            data-nama="{{ $stok->nama_barang }}"
                                            data-stok="{{ $stok->sisa_stok }}"
                                            data-satuan="{{ $stok->satuan }}">
                                        {{ $stok->kode_barang }} - {{ $stok->nama_barang }} (Tersedia: {{ $stok->sisa_stok }} {{ $stok->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="jumlah_diminta" class="form-label">Jumlah Diminta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_diminta" name="jumlah_diminta" required min="1">
                            <small class="form-text text-muted">Stok tersedia: <span id="stok_tersedia">0</span> <span id="satuan_barang"></span></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fas fa-paper-plane"></i> Ajukan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Ajuan -->
    <div class="card">
        <div class="card-header py-2">
            <h3 class="card-title mb-0">Riwayat Ajuan Saya</h3>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="riwayatTable" class="table table-striped display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No. Ajuan</th>
                            <th>Barang</th>
                            <th>Jumlah Diminta</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatAjuan as $ajuan)
                        <tr id="ajuan-row-{{ $ajuan->id }}">
                            <td>{{ $ajuan->nomor_ajuan }}</td>
                            <td>{{ $ajuan->stokPusat->nama_barang }}</td>
                            <td>{{ $ajuan->jumlah_diminta }} {{ $ajuan->stokPusat->satuan }}</td>
                            <td>
                                @switch($ajuan->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Menunggu Kabag</span>
                                        @break
                                    @case('approved_kabag')
                                        <span class="badge bg-info">Menunggu GA</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($ajuan->status) }}</span>
                                        @break
                            @endswitch
                            </td>
                            <td>{{ $ajuan->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-info btn-detail" data-id="{{ $ajuan->id }}" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($ajuan->status === 'pending')
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="{{ $ajuan->id }}" title="Hapus Ajuan">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $riwayatAjuan->links() }}
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Pilih Barang",
                width: '100%'
            });

            // Initialize DataTable dan simpan referensinya ke variabel GLOBAL
            let riwayatTable = $('#riwayatTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [[4, 'desc']], // Sort by date (kolom tanggal di index 4)
                columnDefs: [
                    { orderable: false, targets: [5] } // Kolom aksi tidak bisa di-sort
                ]
            });

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

            // Helper function untuk format tanggal
            function formatDate(dateString) {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            }

            // Update stok tersedia saat barang dipilih
            $('#stok_pusat_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                
                if (selectedOption.val()) {
                    const stok = selectedOption.data('stok');
                    const satuan = selectedOption.data('satuan');
                    
                    $('#stok_tersedia').text(stok);
                    $('#satuan_barang').text(satuan);
                    $('#jumlah_diminta').attr('max', stok);
                } else {
                    $('#stok_tersedia').text('0');
                    $('#satuan_barang').text('');
                    $('#jumlah_diminta').removeAttr('max');
                }
            });

            

            // Submit form ajuan dengan real-time update
            $('#formAjuanStok').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                
                // Disable button sementara
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
                
                console.log('Mengirim data ajuan...');
                
                $.ajax({
                    url: '/ajuan-stok/submit', // Ganti dengan URL langsung
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Response dari server:', response);
                        
                        // Reset form
                        $('#formAjuanStok')[0].reset();
                        $('#stok_pusat_id').val('').trigger('change');
                        showToast('success', 'Berhasil!', response.message);
                        
                        // Pastikan response memiliki data ajuan
                        if (response.ajuan && response.ajuan.stok_pusat) {
                            const newAjuan = response.ajuan;
                            
                            console.log('Data ajuan baru:', newAjuan);
                            
                            // Buat HTML untuk tombol aksi
                            const actionButtons = `
                                <button class="btn btn-sm btn-info btn-detail" data-id="${newAjuan.id}" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="${newAjuan.id}" title="Hapus Ajuan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                            
                            // Buat status badge yang dinamis
                            const statusBadge = newAjuan.status === 'pending' ? 
                                '<span class="badge bg-warning">Menunggu Kabag</span>' :
                                newAjuan.status === 'approved_kabag' ?
                                '<span class="badge bg-info">Menunggu GA</span>' :
                                newAjuan.status === 'completed' ?
                                '<span class="badge bg-success">Selesai</span>' :
                                newAjuan.status === 'rejected' ?
                                '<span class="badge bg-danger">Ditolak</span>' :
                                '<span class="badge bg-secondary">' + newAjuan.status + '</span>';
                            
                            // Tambahkan baris baru ke DataTable
                            const newRow = riwayatTable.row.add([
                                newAjuan.nomor_ajuan,
                                newAjuan.stok_pusat.nama_barang,
                                `${newAjuan.jumlah_diminta} ${newAjuan.stok_pusat.satuan}`,
                                statusBadge, // Gunakan variabel statusBadge
                                formatDate(newAjuan.created_at),
                                actionButtons
                            ]).node();
                            
                            // Beri ID pada baris baru untuk referensi
                            $(newRow).attr('id', 'ajuan-row-' + newAjuan.id);
                            
                            // Gambar ulang tabel dan pindah ke halaman pertama
                            riwayatTable.draw();
                            riwayatTable.page('first').draw('page');
                            
                            console.log('Baris baru berhasil ditambahkan ke tabel');
                        } else {
                            console.error('Data ajuan tidak lengkap dalam response:', response);
                            showToast('warning', 'Peringatan', 'Data berhasil disimpan tapi tampilan mungkin perlu di-refresh');
                            // Fallback: reload halaman jika data tidak lengkap
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error AJAX:', xhr);
                        
                        let errorMessage = 'Terjadi kesalahan saat mengajukan stok.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Silakan coba lagi.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Data yang dikirim tidak valid.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Ajukan');
                    }
                });
            });

            $(document).on('click', '.btn-detail', function() {
                const ajuanId = $(this).data('id');
                
                console.log('Membuka detail ajuan ID:', ajuanId);

                if (!ajuanId) {
                    alert('ID Ajuan tidak ditemukan!');
                    return;
                }

                $('#loadingDetail').removeClass('d-none');
                $('#detailContent').addClass('d-none').html('');

                $('#modalDetailAjuan').modal('show');

                $.ajax({
                    url: `/ajuan-stok/detail/${ajuanId}`,
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Full response received:', response);
                        
                        $('#loadingDetail').addClass('d-none');
                        $('#detailContent').removeClass('d-none');

                        if (!response.success) {
                            $('#detailContent').html('<p class="text-danger">' + response.message + '</p>');
                            return;
                        }
                        
                        const ajuan = response.ajuan;
                        const timeline = response.timeline;

                        if (!ajuan) {
                            $('#detailContent').html('<p class="text-danger">Data ajuan tidak ditemukan.</p>');
                            return;
                        }
                        
                        let timelineHtml = '';
                        if (timeline && timeline.length > 0) {
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
                                            ${item.date ? `<small class="text-muted">${formatDate(item.date)}</small>` : ''}
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            timelineHtml = '<p class="text-muted">Timeline tidak tersedia.</p>';
                        }
                        
                        const detailHtml = `
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Informasi Ajuan</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>No. Ajuan:</strong></td><td>${ajuan.nomor_ajuan || '-'}</td></tr>
                                        <tr><td><strong>Divisi:</strong></td><td>${ajuan.divisi?.divisi || '-'}</td></tr>
                                        <tr><td><strong>Pengaju:</strong></td><td>${ajuan.pengaju?.name || '-'}</td></tr>
                                        <tr><td><strong>Tanggal Ajuan:</strong></td><td>${formatDate(ajuan.created_at)}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Informasi Barang</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>Kode Barang:</strong></td><td>${ajuan.stok_pusat?.kode_barang || '-'}</td></tr>
                                        <tr><td><strong>Nama Barang:</strong></td><td>${ajuan.stok_pusat?.nama_barang || '-'}</td></tr>
                                        <tr><td><strong>Jumlah Diminta:</strong></td><td>${ajuan.jumlah_diminta || 0} ${ajuan.stok_pusat?.satuan || ''}</td></tr>
                                        <tr><td><strong>Jumlah Diberikan:</strong></td><td>${ajuan.jumlah_diberikan ? ajuan.jumlah_diberikan + ' ' + (ajuan.stok_pusat?.satuan || '') : '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                            ${ajuan.keterangan ? `<div class="mb-4"><h6>Keterangan:</h6><p>${ajuan.keterangan}</p></div>` : ''}
                            <div>
                                <h6>Timeline Approval</h6>
                                ${timelineHtml}
                            </div>
                        `;
                        
                        $('#detailContent').html(detailHtml);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        $('#loadingDetail').addClass('d-none');
                        $('#detailContent').removeClass('d-none');
                        
                        let errorMessage = 'Gagal memuat detail ajuan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        $('#detailContent').html(`<p class="text-danger">${errorMessage}</p>`);
                    }
                });
            });

            function formatDate(dateString) {
                if (!dateString) return '-';
                
                const date = new Date(dateString);
                const options = { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                
                return date.toLocaleDateString('id-ID', options);
            }

            $(document).on('click', '.btn-hapus', function() {
                const ajuanId = $(this).data('id');
                const rowElement = $(this).closest('tr');

                console.log('Mencoba hapus ajuan ID:', ajuanId);

                if (typeof Swal === 'undefined') {
                    if (confirm('Apakah Anda yakin ingin membatalkan ajuan ini?')) {
                        hapusAjuan(ajuanId, rowElement);
                    }
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Ajuan yang dibatalkan tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        hapusAjuan(ajuanId, rowElement);
                    }
                });
            });

            function hapusAjuan(ajuanId, rowElement) {
                console.log('Mengirim request hapus ajuan...');
                
                $.ajax({
                    url: '/ajuan-stok/cancel',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
                        ajuan_id: ajuanId
                    },
                    success: function(response) {
                        console.log('Ajuan berhasil dibatalkan:', response);
                        
                        showToast('success', 'Berhasil!', response.message);

                        riwayatTable.row(rowElement).remove().draw();
                        
                        console.log('Baris berhasil dihapus dari tabel');
                    },
                    error: function(xhr) {
                        console.error('Error saat membatalkan ajuan:', xhr);
                        
                        let errorMessage = 'Terjadi kesalahan saat membatalkan ajuan.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMessage = 'Anda tidak memiliki akses untuk membatalkan ajuan ini.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Ajuan tidak dapat dibatalkan.';
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorMessage
                            });
                        } else {
                            alert(errorMessage);
                        }
                    }
                });
            }
        });
    </script>
@endpush
@endsection