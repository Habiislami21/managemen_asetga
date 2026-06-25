<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Barang Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --text-primary: #333;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: linear-gradient(rgba(97, 0, 221, 0.05), rgba(97, 0, 221, 0.1)), url('{{ asset('img/background-2.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
            color: var(--text-primary);
        }

        .main-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            min-height: calc(100vh - 40px);
            padding: 20px;
        }

        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 760px;
        }

        .form-header {
            margin-bottom: 30px;
            text-align: center;
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .form-subtitle {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-control, .form-select {
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            padding: 12px 16px;
            width: 100%;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(97, 0, 221, 0.15);
            outline: none;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 16px;
            color: #aaa;
        }

        .input-with-icon .form-control {
            padding-left: 45px;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: #fff;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(97, 0, 221, 0.2);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-add-row, .btn-remove-row {
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .barang-section {
            border: 2px dashed #e1e1e1;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            background: #fafafa;
        }

        .barang-section h5 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .barang-row {
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .alert {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #e6f7ec;
            color: #2a9d5f;
            border-color: #2a9d5f;
        }

        .alert-danger {
            background: #ffeaea;
            color: #e53e3e;
            border-color: #e53e3e;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 24px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span>Terdapat kesalahan pada pengisian form:</span>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-header">
                <h2>Form Peminjaman Barang</h2>
                <p class="form-subtitle">Sarana & Prasarana BMI Pusat — Event</p>
            </div>

            <form id="peminjamanBarangForm" action="{{ route('peminjaman-event.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nama_peminjam">Nama Peminjam</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                               name="nama_peminjam" id="nama_peminjam" placeholder="Masukkan nama peminjam"
                               value="{{ old('nama_peminjam') }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="divisi">Divisi</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('divisi') is-invalid @enderror"
                               name="divisi" id="divisi" placeholder="Masukkan divisi"
                               value="{{ old('divisi') }}" required>
                        <i class="fas fa-building input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nomor_hp">Nomor HP (WhatsApp)</label>
                    <div class="input-with-icon">
                        <input type="tel" class="form-control @error('nomor_hp') is-invalid @enderror"
                               name="nomor_hp" id="nomor_hp" placeholder="0895xxxxxxx"
                               value="{{ old('nomor_hp') }}" required>
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="tanggal_kegiatan">Tanggal Kegiatan</label>
                        <input type="date" class="form-control @error('tanggal_kegiatan') is-invalid @enderror"
                               id="tanggal_kegiatan" name="tanggal_kegiatan"
                               value="{{ old('tanggal_kegiatan') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="tanggal_kembali">Tanggal Pengembalian</label>
                        <input type="date" class="form-control @error('tanggal_kembali') is-invalid @enderror"
                               id="tanggal_kembali" name="tanggal_kembali"
                               value="{{ old('tanggal_kembali') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tempat">Tempat / Lokasi</label>
                    <input type="text" class="form-control @error('tempat') is-invalid @enderror"
                           name="tempat" id="tempat" placeholder="Lokasi kegiatan"
                           value="{{ old('tempat') }}" required>
                </div>

                <div class="form-group">
                    <label for="nama_kegiatan">Nama Kegiatan</label>
                    <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror"
                           name="nama_kegiatan" id="nama_kegiatan" placeholder="Nama kegiatan"
                           value="{{ old('nama_kegiatan') }}" required>
                </div>

                <div class="barang-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-boxes-stacked me-2"></i>Daftar Barang</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-add-row" id="addBarangRow">
                            <i class="fas fa-plus me-1"></i>Tambah Barang
                        </button>
                    </div>

                    <div id="barangRows">
                        @php
                            $oldBarang = old('barang', [['nama_barang' => '', 'jumlah' => '']]);
                        @endphp
                        @foreach ($oldBarang as $index => $barang)
                            <div class="barang-row" data-row>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-1 col-2">
                                        <label class="form-label">No</label>
                                        <input type="text" class="form-control row-number" value="{{ $index + 1 }}" readonly>
                                    </div>
                                    <div class="col-md-7 col-10">
                                        <label class="form-label">Nama Barang</label>
                                        <input type="text" class="form-control @error('barang.' . $index . '.nama_barang') is-invalid @enderror"
                                               name="barang[{{ $index }}][nama_barang]"
                                               value="{{ $barang['nama_barang'] ?? '' }}" placeholder="Nama barang" required>
                                    </div>
                                    <div class="col-md-3 col-8">
                                        <label class="form-label">Jumlah</label>
                                        <input type="number" min="1" class="form-control @error('barang.' . $index . '.jumlah') is-invalid @enderror"
                                               name="barang[{{ $index }}][jumlah]"
                                               value="{{ $barang['jumlah'] ?? '' }}" placeholder="Qty" required>
                                    </div>
                                    <div class="col-md-1 col-4 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" @if($index === 0) disabled @endif>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <i class="fas fa-file-word me-2"></i>Buat Form & Unduh Dokumen
                </button>

                <div class="text-center mt-3">
                    <a href="{{ url('/menu-awal') }}" class="text-decoration-none" style="color: var(--primary); font-size: 14px;">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Menu Awal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const barangRows = document.getElementById('barangRows');
        const addBarangRowBtn = document.getElementById('addBarangRow');
        const form = document.getElementById('peminjamanBarangForm');
        const submitBtn = document.getElementById('submitBtn');

        function refreshRowNumbers() {
            barangRows.querySelectorAll('[data-row]').forEach((row, index) => {
                row.querySelector('.row-number').value = index + 1;
                const removeBtn = row.querySelector('.btn-remove-row');
                removeBtn.disabled = index === 0;

                row.querySelectorAll('input[name]').forEach(input => {
                    if (input.name.includes('[nama_barang]')) {
                        input.name = `barang[${index}][nama_barang]`;
                    }
                    if (input.name.includes('[jumlah]')) {
                        input.name = `barang[${index}][jumlah]`;
                    }
                });
            });
        }

        addBarangRowBtn.addEventListener('click', () => {
            const index = barangRows.querySelectorAll('[data-row]').length;
            const wrapper = document.createElement('div');
            wrapper.className = 'barang-row';
            wrapper.setAttribute('data-row', '');
            wrapper.innerHTML = `
                <div class="row g-3 align-items-end">
                    <div class="col-md-1 col-2">
                        <label class="form-label">No</label>
                        <input type="text" class="form-control row-number" value="${index + 1}" readonly>
                    </div>
                    <div class="col-md-7 col-10">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="barang[${index}][nama_barang]" placeholder="Nama barang" required>
                    </div>
                    <div class="col-md-3 col-8">
                        <label class="form-label">Jumlah</label>
                        <input type="number" min="1" class="form-control" name="barang[${index}][jumlah]" placeholder="Qty" required>
                    </div>
                    <div class="col-md-1 col-4 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            barangRows.appendChild(wrapper);
            refreshRowNumbers();
        });

        barangRows.addEventListener('click', (event) => {
            const removeBtn = event.target.closest('.btn-remove-row');
            if (!removeBtn || removeBtn.disabled) {
                return;
            }

            removeBtn.closest('[data-row]').remove();
            refreshRowNumbers();
        });

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
        });
    </script>
</body>
</html>
