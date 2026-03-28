{{-- resources/views/admin/ajuan-final.blade.php --}}
@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <style>
        .modal-lg {
            max-width: 90%;
        }
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }
        .bg-approved {
            background-color: #198754;
            color: white;
        }
        .action-icon {
            cursor: pointer;
            margin: 0 5px;
            font-size: 1.2rem;
        }
        .action-icon.view {
            color: #0dcaf0;
        }
        .action-icon.edit {
            color: #ffc107;
        }
        .action-icon.export {
            color: #28a745;
        }
        .approval-info {
            font-size: 0.8rem;
            margin-top: 5px;
            font-style: italic;
            color: #6c757d;
        }
        .filter-container {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .editable-field {
            border: 1px solid #dee2e6;
            padding: 8px;
            border-radius: 4px;
            background-color: #fff;
            width: 100%;
            font-size: 0.9rem;
        }
        .editable-field:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .save-indicator {
            color: #28a745;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        .error-indicator {
            color: #dc3545;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        .loading-indicator {
            color: #6c757d;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        .statistics-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .statistics-section .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s ease;
        }
        .statistics-section .card:hover {
            transform: translateY(-5px);
        }
        .statistics-section .display-6 {
            font-size: 1.8rem;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
        }
        .updated-row {
            background-color: #d4edda !important;
            transition: background-color 0.5s ease;
        }

        .btn-outline-danger {
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover:not(.disabled) {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .btn-outline-danger.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Animation untuk row yang akan dihapus */
        @keyframes deleteRowFade {
            0% { 
                opacity: 1; 
                transform: scale(1);
                background-color: #fff;
            }
            50% { 
                opacity: 0.5; 
                transform: scale(0.98);
                background-color: #f8d7da;
            }
            100% { 
                opacity: 0; 
                transform: scale(0.95);
                background-color: #f5c6cb;
            }
        }

        .deleting-row {
            animation: deleteRowFade 0.5s ease-in-out;
        }

        /* Highlight untuk item yang baru diupdate */
        .item-updated {
            background-color: #d4edda !important;
            transition: background-color 0.5s ease;
        }

        /* Styling untuk counter di tombol save */
        .save-counter {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 0.8em;
            margin-left: 5px;
        }
    </style>
@endpush

@section('content')
@php
$isAdmin = Auth::user()->role === 'admin' || Auth::user()->is_admin === 1;
$isGA = Auth::user()->role === 'ga' || Auth::user()->is_ga === 1;
@endphp
<div class="container-fluid h-100">
    <div class="card h-100">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                Daftar Ajuan (Disetujui)
                <span class="badge bg-success ms-2">
                    {{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }}
                </span>
            </h3>
            <div class="d-flex align-items-center">
                <form id="monthFilterForm" class="d-flex align-items-center">
                    <label for="monthFilter" class="me-2">Filter Bulan:</label>
                    <select id="monthFilter" name="month" class="form-select form-select-sm me-2" style="width: auto;">
                        @foreach ($months as $key => $label)
                            <option value="{{ substr($key, 4, 2) }}" 
                                    data-year="{{ substr($key, 0, 4) }}" 
                                    {{ $selectedMonth == substr($key, 4, 2) && $selectedYear == substr($key, 0, 4) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="year" id="yearFilter" value="{{ $selectedYear }}">
                    <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                </form>
            </div>
        </div>
        <div class="card-body p-3">
            <div id="alertContainer"></div>
            
            <div class="table-responsive">
                <table id="myTable" class="table table-striped display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama SPA</th>
                            <th>Divisi</th>
                            <th>Tanggal Ajuan</th>
                            <th>Total Ajuan</th>
                            <th>Status</th>
                            <th>Disetujui Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ajuanList as $index => $ajuan)
                        @php 
                            $tanggalAjuan = Carbon\Carbon::parse($ajuan->tanggal_ajuan);
                            $uniqueId = str_replace(' ', '_', $ajuan->nama_spa) . '_' . $tanggalAjuan->format('YmdHis');
                        @endphp
                        <tr id="row-{{ $uniqueId }}" data-nama-spa="{{ $ajuan->nama_spa }}" data-tanggal="{{ $tanggalAjuan->format('Y-m-d') }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ajuan->nama_spa }}</td>
                            <td>{{ $ajuan->nama_divisi ?? 'Divisi tidak tersedia' }}</td>
                            <td>{{ date('d-m-Y', strtotime($ajuan->tanggal_ajuan)) }}</td>
                            <td class="total-ajuan">Rp {{ number_format($ajuan->total_ajuan, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-approved">Disetujui</span>
                            </td>
                            <td>
                                {{ $ajuan->approved_by_name }}
                                @if($ajuan->approved_at)
                                    <div class="approval-info">
                                        {{ date('d-m-Y H:i', strtotime($ajuan->approved_at)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Icon untuk melihat detail -->
                                    <i class="fas fa-eye action-icon view" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Lihat Detail Ajuan"
                                        onclick="openDetailModal('{{ $uniqueId }}', '{{ $ajuan->nama_spa }}', '{{ $ajuan->tanggal_ajuan }}')"></i>
                                    
                                    <!-- Icon untuk edit -->
                                    <i class="fas fa-edit action-icon edit" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-title="Edit Ajuan"
                                       onclick="openEditModal('{{ $uniqueId }}', '{{ $ajuan->nama_spa }}', '{{ $tanggalAjuan->format('Y-m-d') }}')"></i>

                                    <!-- Icon untuk export ajuan -->
                                    <i class="fas fa-file-export action-icon export" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Export Ajuan"
                                        onclick="exportAjuan('{{ $ajuan->nama_spa }}', '{{ date('Y-m-d', strtotime($ajuan->tanggal_ajuan)) }}')">
                                     </i>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-2">Tidak ada data ajuan yang disetujui</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail untuk setiap SPA -->
@foreach ($ajuanList as $ajuan)
@php
    $spaId = str_replace(' ', '_', $ajuan->nama_spa);
    $dateHash = date('YmdHis', strtotime($ajuan->tanggal_ajuan));
    $uniqueId = $spaId . '_' . $dateHash;
@endphp
<div class="modal fade" id="detailModal{{ $uniqueId }}" tabindex="-1" 
    aria-labelledby="detailModalLabel{{ $uniqueId }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $uniqueId }}">
                    Detail Ajuan: {{ $ajuan->nama_spa }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Divisi:</strong> {{ $ajuan->nama_divisi }}<br>
                            <strong>Tanggal Ajuan:</strong> {{ date('d-m-Y', strtotime($ajuan->tanggal_ajuan)) }}<br>
                            <strong>Status:</strong> 
                            <span class="badge bg-approved">Disetujui</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Disetujui Oleh:</strong> {{ $ajuan->approved_by_name }}<br>
                            @if($ajuan->approved_at)
                                <strong>Tanggal Disetujui:</strong> {{ date('d-m-Y H:i', strtotime($ajuan->approved_at)) }}
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered detail-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang Ajuan</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="detail-body">
                            @php
                                $itemsForThisSPA = $detailAjuan->where('nama_spa', $ajuan->nama_spa)
                                                            ->where('created_at', $ajuan->tanggal_ajuan);
                                $grandTotal = 0;
                                $itemNo = 1;
                            @endphp
                            
                            @forelse ($itemsForThisSPA as $item)
                            <tr data-item-id="{{ $item->id }}">
                                <td>{{ $itemNo++ }}</td>
                                <td class="barang-ajuan">{{ $item->barang_ajuan }}</td>
                                <td class="kategori-barang">{{ $item->kategori_barang }}</td>
                                <td class="banyak-barang">{{ $item->banyak_barang }}</td>
                                <td class="satuan">{{ $item->satuan }}</td>
                                <td class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="total">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="keterangan">{{ $item->keterangan }}</td>
                            </tr>
                            @php
                                $grandTotal += $item->total;
                            @endphp
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data barang</td>
                            </tr>
                            @endforelse
                            
                            <!-- Row untuk total -->
                            <tr class="table-active fw-bold">
                                <td colspan="6" class="text-end">TOTAL</td>
                                <td class="grand-total">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" 
                        onclick="openEditModal('{{ $uniqueId }}', '{{ $ajuan->nama_spa }}', '{{ date('Y-m-d', strtotime($ajuan->tanggal_ajuan)) }}')">
                    <i class="fas fa-edit"></i> Edit Ajuan
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    Edit Ajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama SPA:</strong> <span id="edit_display_nama_spa"></span><br>
                            <strong>Divisi:</strong> <span id="edit_display_divisi"></span><br>
                        </div>
                        <div class="col-md-6">
                            <strong>Tanggal Ajuan:</strong> <span id="edit_display_tanggal_ajuan"></span><br>
                            <strong>Status:</strong> <span class="badge bg-approved">Disetujui</span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Petunjuk:</strong> Edit data pada form di bawah ini. Perubahan akan disimpan secara otomatis saat Anda mengklik tombol "Simpan Perubahan".
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="editItemsTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="18%">Barang Ajuan</th>
                                <th width="10%">Kategori</th>
                                <th width="10%">Jumlah</th>
                                <th width="10%">Satuan</th>
                                <th width="13%">Harga Satuan</th>
                                <th width="13%">Total</th>
                                <th width="13%">Keterangan</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="editItemsBody">
                            <!-- Items will be populated by JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr class="table-active fw-bold">
                                <td colspan="7" class="text-end">TOTAL</td>
                                <td id="editGrandTotal">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="saveAllChanges()" id="saveChangesBtn">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <button type="button" class="btn btn-info" onclick="refreshEditData()">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
        const isAdmin = {{ Auth::user()->role === 'admin' || Auth::user()->is_admin === 1 ? 'true' : 'false' }};
        let editItems = [];
        let currentNamaSpa = '';
        let currentTanggalAjuan = '';
        let currentUniqueId = '';
        let pendingChanges = new Map(); // Track pending changes
        
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
            
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            
            // Month filter functionality
            $('#monthFilter').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const year = selectedOption.data('year');
                $('#yearFilter').val(year);
            });
            
            $('#monthFilterForm').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Memuat Data...',
                    text: 'Sedang mengambil data untuk bulan yang dipilih',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    timer: 1000,
                    timerProgressBar: true
                });
                
                this.submit();
            });
        });
        
        // Toast notification function
        function showToast(icon, title, text) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            
            Toast.fire({
                icon: icon,
                title: text
            });
        }
        
        // Function to open detail modal
        function openDetailModal(uniqueId, namaSpa, tanggalAjuan) {
            const modalId = `#detailModal${uniqueId}`;
            const modal = new bootstrap.Modal(document.querySelector(modalId));
            modal.show();
        }
        
        // Function to open edit modal
        function openEditModal(uniqueId, namaSpa, tanggalAjuan) {
            // Close detail modal if open
            const detailModal = bootstrap.Modal.getInstance(document.querySelector(`#detailModal${uniqueId}`));
            if (detailModal) {
                detailModal.hide();
            }
            
            currentNamaSpa = namaSpa;
            currentTanggalAjuan = tanggalAjuan;
            currentUniqueId = uniqueId;
            pendingChanges.clear(); // Clear any pending changes
            
            // Set form data
            $('#edit_display_nama_spa').text(namaSpa);
            $('#edit_display_tanggal_ajuan').text(formatDate(tanggalAjuan));
            
            // Load items for editing
            loadEditItems(namaSpa, tanggalAjuan);
            
            // Show modal
            const modal = new bootstrap.Modal(document.querySelector('#editModal'));
            modal.show();
        }
        
        // Load items for editing via AJAX
        function loadEditItems(namaSpa, tanggalAjuan) {
            $('#editItemsBody').html('<tr><td colspan="8" class="text-center">Memuat data...</td></tr>');
            
            $.ajax({
                url: '{{ route("ajuan-final.get-items") }}',
                type: 'GET',
                data: {
                    nama_spa: namaSpa,
                    tanggal_ajuan: tanggalAjuan
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        editItems = response.items;
                        $('#edit_display_divisi').text(response.divisi);
                        renderEditItems();
                    } else {
                        $('#editItemsBody').html('<tr><td colspan="8" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading edit items:', error);
                    $('#editItemsBody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>');
                }
            });
        }
        
        // Render edit items table with form inputs
        function renderEditItems() {
            if (editItems.length === 0) {
                $('#editItemsBody').html(`
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> Tidak ada item tersisa dalam ajuan ini
                        </td>
                    </tr>
                `);
                $('#editGrandTotal').text('Rp 0');
                return;
            }
            
            let html = '';
            let grandTotal = 0;
            
            editItems.forEach((item, index) => {
                grandTotal += parseFloat(item.total);
                
                // Tentukan apakah tombol delete bisa digunakan (minimal harus ada 2 item)
                const canDelete = editItems.length > 1;
                const deleteButtonClass = canDelete ? 'btn-outline-danger' : 'btn-outline-secondary disabled';
                const deleteButtonTitle = canDelete ? 'Hapus Item' : 'Tidak dapat menghapus item terakhir';
                
                html += `
                    <tr data-item-id="${item.id}" id="edit-row-${item.id}">
                        <td>${index + 1}</td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control editable-field" 
                                    id="barang_ajuan_${item.id}"
                                    value="${item.barang_ajuan}" 
                                    data-item-id="${item.id}"
                                    data-field="barang_ajuan"
                                    onchange="trackChange(${item.id}, 'barang_ajuan', this.value)">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <select class="form-control editable-field" 
                                        id="kategori_barang_${item.id}"
                                        data-item-id="${item.id}"
                                        data-field="kategori_barang"
                                        onchange="trackChange(${item.id}, 'kategori_barang', this.value)">
                                    <option value="RTK" ${item.kategori_barang === 'RTK' ? 'selected' : ''}>RTK</option>
                                    <option value="ATK" ${item.kategori_barang === 'ATK' ? 'selected' : ''}>ATK</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control editable-field" 
                                    id="banyak_barang_${item.id}"
                                    value="${item.banyak_barang}" 
                                    min="1" step="0.01"
                                    data-item-id="${item.id}"
                                    data-field="banyak_barang"
                                    onchange="trackChangeWithRecalc(${item.id}, 'banyak_barang', this.value)">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <select class="form-control editable-field" 
                                        id="satuan_${item.id}"
                                        data-item-id="${item.id}"
                                        data-field="satuan"
                                        onchange="trackChange(${item.id}, 'satuan', this.value)">
                                    ${getSatuanOptions(item.satuan)}
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="number" class="form-control editable-field" 
                                    id="harga_${item.id}"
                                    value="${item.harga}" 
                                    min="0" step="0.01"
                                    data-item-id="${item.id}"
                                    data-field="harga"
                                    onchange="trackChangeWithRecalc(${item.id}, 'harga', this.value)">
                            </div>
                        </td>
                        <td class="total-cell" id="total_${item.id}">Rp ${formatNumber(item.total)}</td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control editable-field" 
                                    id="keterangan_${item.id}"
                                    value="${item.keterangan || ''}" 
                                    data-item-id="${item.id}"
                                    data-field="keterangan"
                                    onchange="trackChange(${item.id}, 'keterangan', this.value)">
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm ${deleteButtonClass}" 
                                    title="${deleteButtonTitle}"
                                    ${canDelete ? '' : 'disabled'}
                                    onclick="deleteItem(${item.id}, '${item.barang_ajuan}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            $('#editItemsBody').html(html);
            $('#editGrandTotal').text('Rp ' + formatNumber(grandTotal));
        }
        
        // Track changes locally
        function trackChange(itemId, field, value) {
            if (!pendingChanges.has(itemId)) {
                pendingChanges.set(itemId, {});
            }
            pendingChanges.get(itemId)[field] = value;
            
            // Update button state
            updateSaveButtonState();
        }
        
        // Track changes with recalculation
        function trackChangeWithRecalc(itemId, field, value) {
            trackChange(itemId, field, value);
            
            // Find the item and recalculate total locally
            const itemIndex = editItems.findIndex(item => item.id == itemId);
            if (itemIndex !== -1) {
                const item = editItems[itemIndex];
                
                if (field === 'banyak_barang') {
                    item.banyak_barang = parseFloat(value) || 0;
                } else if (field === 'harga') {
                    item.harga = parseFloat(value) || 0;
                }
                
                // Update local item data
                item.total = item.banyak_barang * item.harga;
                
                // Update display
                $(`#total_${itemId}`).text('Rp ' + formatNumber(item.total));
                updateGrandTotal();
            }
        }
        
        // Update grand total
        function updateGrandTotal() {
            let total = 0;
            editItems.forEach(item => {
                total += parseFloat(item.total) || 0;
            });
            $('#editGrandTotal').text('Rp ' + formatNumber(total));
        }
        
        // Update save button state
        function updateSaveButtonState() {
            const hasChanges = pendingChanges.size > 0;
            const btn = $('#saveChangesBtn');
            
            if (hasChanges) {
                btn.removeClass('btn-success').addClass('btn-warning');
                btn.html('<i class="fas fa-save"></i> Simpan Perubahan (' + pendingChanges.size + ')');
            } else {
                btn.removeClass('btn-warning').addClass('btn-success');
                btn.html('<i class="fas fa-save"></i> Simpan Perubahan');
            }
        }
        
        // Save all changes
        function saveAllChanges() {
            if (pendingChanges.size === 0) {
                showToast('info', 'Info', 'Tidak ada perubahan untuk disimpan');
                return;
            }
            
            Swal.fire({
                title: 'Konfirmasi Simpan',
                text: `Apakah Anda yakin ingin menyimpan ${pendingChanges.size} perubahan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    processPendingChanges();
                }
            });
        }
        
        // Process all pending changes
        function processPendingChanges() {
            let processedCount = 0;
            const totalChanges = pendingChanges.size;
            
            Swal.fire({
                title: 'Menyimpan...',
                html: `Memproses perubahan 0/${totalChanges}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const changePromises = [];
            
            for (let [itemId, changes] of pendingChanges) {
                for (let [field, value] of Object.entries(changes)) {
                    const promise = $.ajax({
                        url: '{{ route("ajuan-final.update-item") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: itemId,
                            field: field,
                            value: value
                        },
                        dataType: 'json'
                    }).then(response => {
                        processedCount++;
                        Swal.update({
                            html: `Memproses perubahan ${processedCount}/${totalChanges}`
                        });
                        return response;
                    });
                    
                    changePromises.push(promise);
                }
            }
            
            Promise.all(changePromises)
                .then(responses => {
                    Swal.close();
                    
                    // Check for any failures
                    const failures = responses.filter(r => !r.success);
                    
                    if (failures.length === 0) {
                        showToast('success', 'Berhasil!', `Semua ${totalChanges} perubahan berhasil disimpan`);
                        
                        // Clear pending changes
                        pendingChanges.clear();
                        updateSaveButtonState();
                        
                        // Update all displays with real-time data
                        updateAllDisplays();
                        
                    } else {
                        showToast('warning', 'Sebagian Berhasil', 
                                `${responses.length - failures.length}/${totalChanges} perubahan berhasil disimpan`);
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error saving changes:', error);
                    showToast('error', 'Error', 'Terjadi kesalahan saat menyimpan perubahan');
                });
        }
        
        // Update all displays with current data
        function updateAllDisplays() {
            // Update detail modal if open
            updateDetailModal();
            
            // Update main table total
            updateMainTableTotal();
            
            // Reload the items in edit modal
            if (currentNamaSpa && currentTanggalAjuan) {
                loadEditItems(currentNamaSpa, currentTanggalAjuan);
            }
        }
        

        // Update detail modal content
        function updateDetailModalContent(items) {
            const detailBody = document.querySelector(`#detailModal${currentUniqueId} .detail-body`);
            if (!detailBody) return;
            
            let html = '';
            let grandTotal = 0;
            
            items.forEach((item, index) => {
                grandTotal += parseFloat(item.total);
                
                html += `
                    <tr data-item-id="${item.id}">
                        <td>${index + 1}</td>
                        <td class="barang-ajuan">${item.barang_ajuan}</td>
                        <td class="kategori-barang">${item.kategori_barang}</td>
                        <td class="banyak-barang">${item.banyak_barang}</td>
                        <td class="satuan">${item.satuan}</td>
                        <td class="harga">Rp ${formatNumber(item.harga)}</td>
                        <td class="total">Rp ${formatNumber(item.total)}</td>
                        <td class="keterangan">${item.keterangan || ''}</td>
                    </tr>
                `;
            });
            
            // Add total row
            html += `
                <tr class="table-active fw-bold">
                    <td colspan="6" class="text-end">TOTAL</td>
                    <td class="grand-total">Rp ${formatNumber(grandTotal)}</td>
                    <td></td>
                </tr>
            `;
            detailBody.innerHTML = html;
        }

        function updateDetailModal() {
            if (!currentNamaSpa || !currentTanggalAjuan) return;
            
            // Load fresh data from server for detail modal
            $.ajax({
                url: '{{ route("ajuan-final.get-items") }}',
                type: 'GET',
                data: {
                    nama_spa: currentNamaSpa,
                    tanggal_ajuan: currentTanggalAjuan
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateDetailModalContent(response.items);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating detail modal:', error);
                }
            });
        }
        
        // Update main table total
        function updateMainTableTotal() {
            $.ajax({
                url: '{{ route("ajuan-final.get-items") }}',
                type: 'GET',
                data: {
                    nama_spa: currentNamaSpa,
                    tanggal_ajuan: currentTanggalAjuan
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.items && response.items.length > 0) {
                        // Update existing row
                        const row = $(`tr[data-nama-spa="${currentNamaSpa}"][data-tanggal="${currentTanggalAjuan}"]`);
                        if (row.length) {
                            row.find('.total-ajuan').text('Rp ' + formatNumber(response.total_ajuan));
                            
                            // Add visual indication of update
                            row.addClass('updated-row');
                            setTimeout(() => {
                                row.removeClass('updated-row');
                            }, 2000);
                        }
                    } else {
                        // If no items left, remove the row from main table
                        const row = $(`tr[data-nama-spa="${currentNamaSpa}"][data-tanggal="${currentTanggalAjuan}"]`);
                        if (row.length) {
                            row.addClass('deleting-row');
                            setTimeout(() => {
                                row.remove();
                                
                                // Check if table is empty and show message
                                if ($('#myTable tbody tr').length === 0) {
                                    $('#myTable tbody').html('<tr><td colspan="8" class="text-center py-2">Tidak ada data ajuan yang disetujui</td></tr>');
                                }
                                
                                // Redraw DataTable
                                $('#myTable').DataTable().draw();
                            }, 500);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating main table:', error);
                }
            });
        }
        
        // Get satuan options
        function getSatuanOptions(selectedSatuan) {
            const satuanList = getSatuanList();
            return satuanList.map(satuan => 
                `<option value="${satuan}" ${satuan === selectedSatuan ? 'selected' : ''}>${satuan}</option>`
            ).join('');
        }
        
        // Get satuan list
        function getSatuanList() {
            return ['bulan','pcs','pack','kg','rim','kotak','bungkus','botol','dus','lusin','set'];
        }
        
        // Refresh edit data
        function refreshEditData() {
            if (currentNamaSpa && currentTanggalAjuan) {
                // Clear pending changes
                pendingChanges.clear();
                updateSaveButtonState();
                
                loadEditItems(currentNamaSpa, currentTanggalAjuan);
                showToast('info', 'Data Diperbarui', 'Data telah dimuat ulang dari server');
            }
        }
        
        // Export ajuan
        function exportAjuan(namaSpa, tanggalAjuan) {
            Swal.fire({
                title: 'Memproses...',
                html: 'Sedang memproses data ekspor. Mohon tunggu.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request to export endpoint
            $.ajax({
                url: '{{ route("ajuan-final.export") }}',
                type: 'GET',
                data: {
                    nama_spa: namaSpa,
                    tanggal_ajuan: tanggalAjuan
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, xhr) {
                    Swal.close();
                    
                    // Handle file download
                    const url = window.URL.createObjectURL(new Blob([response]));
                    let filename = `ajuan_${namaSpa.replace(/ /g, '_')}_${formatDate(new Date())}.xlsx`;
                    
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }
                    
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    
                    window.URL.revokeObjectURL(url);
                    link.remove();
                    
                    showToast('success', 'Berhasil!', 'File berhasil diunduh');
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    
                    let errorMessage = 'Terjadi kesalahan saat mengekspor data.';
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData && errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // Use default message
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
        
        // Helper functions
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function deleteItem(itemId, barangAjuan) {
            // Check if this would be the last item
            if (editItems.length <= 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Menghapus',
                    text: 'Ajuan harus memiliki minimal satu item. Tidak dapat menghapus item terakhir.',
                    confirmButtonText: 'Mengerti'
                });
                return;
            }
            
            Swal.fire({
                title: 'Konfirmasi Hapus Item',
                html: `Apakah Anda yakin ingin menghapus item:<br><strong>"${barangAjuan}"</strong>?<br><br><small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    performDeleteItem(itemId, barangAjuan);
                }
            });
        }

        function performDeleteItem(itemId, barangAjuan) {
            // Show loading
            Swal.fire({
                title: 'Menghapus Item...',
                html: `Sedang menghapus "${barangAjuan}"`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '{{ route("ajuan-final.delete-item") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: itemId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.close();
                        
                        // Remove item from local array
                        editItems = editItems.filter(item => item.id != itemId);
                        
                        // Remove any pending changes for this item
                        if (pendingChanges.has(itemId)) {
                            pendingChanges.delete(itemId);
                            updateSaveButtonState();
                        }
                        
                        // Check if this was the last item in the submission
                        if (editItems.length === 0) {
                            // Close edit modal
                            const editModal = bootstrap.Modal.getInstance(document.querySelector('#editModal'));
                            if (editModal) {
                                editModal.hide();
                            }
                            
                            // Close detail modal if open
                            const detailModal = bootstrap.Modal.getInstance(document.querySelector(`#detailModal${currentUniqueId}`));
                            if (detailModal) {
                                detailModal.hide();
                            }
                            
                            // Show message and update displays
                            showToast('success', 'Ajuan Dihapus', 'Semua item telah dihapus. Ajuan telah dihapus dari daftar.');
                            
                            // Update main table (will remove the row)
                            updateMainTableTotal();
                            
                        } else {
                            // Re-render the edit table
                            renderEditItems();
                            
                            // Update all displays
                            updateAllDisplays();
                            
                            // Show success message
                            showToast('success', 'Berhasil!', `Item "${barangAjuan}" berhasil dihapus`);
                            
                            // Show summary info
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Item Dihapus',
                                    html: `
                                        <div class="text-start">
                                            <p><strong>Item yang dihapus:</strong> ${barangAjuan}</p>
                                            <p><strong>Sisa item:</strong> ${response.remaining_items_count}</p>
                                            <p><strong>Total ajuan baru:</strong> Rp ${response.formatted_total_ajuan}</p>
                                        </div>
                                    `,
                                    timer: 4000,
                                    showConfirmButton: true,
                                    confirmButtonText: 'OK'
                                });
                            }, 500);
                        }
                        
                    } else {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('Error deleting item:', error);
                    
                    let errorMessage = 'Terjadi kesalahan saat menghapus item';
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData && errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // Use default message
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function reloadPageData() {
            // Reload the current page with same filters
            const currentMonth = $('#monthFilter').val();
            const currentYear = $('#yearFilter').val();
            
            window.location.href = `{{ route('admin.ajuan-final') }}?month=${currentMonth}&year=${currentYear}`;
        }
    </script>
@endpush
@endsection