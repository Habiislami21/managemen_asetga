<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Kendaraan Event</title>
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

        .section-divider {
            border: none;
            border-top: 2px dashed #e1e1e1;
            margin: 28px 0 24px;
        }

        .section-title {
            color: var(--primary);
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 16px;
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
                <h2><i class="fas fa-car me-2"></i>Form Peminjaman Kendaraan</h2>
                <p class="form-subtitle">Sarana & Prasarana BMI Pusat — Event</p>
            </div>

            <form id="peminjamanKendaraanForm" action="{{ route('peminjaman-event.kendaraan.store') }}" method="POST">
                @csrf

                {{-- ── DATA PEMINJAM ── --}}
                <p class="section-title"><i class="fas fa-user-circle me-2"></i>Data Peminjam</p>

                <div class="form-group">
                    <label for="nama_peminjam">Nama Peminjam</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('nama_peminjam') is-invalid @enderror"
                               name="nama_peminjam" id="nama_peminjam" placeholder="Masukkan nama lengkap"
                               value="{{ old('nama_peminjam') }}" required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="divisi">Divisi Amanah</label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control @error('divisi') is-invalid @enderror"
                                   name="divisi" id="divisi" placeholder="Divisi / bagian"
                                   value="{{ old('divisi') }}" required>
                            <i class="fas fa-building input-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="jabatan">Amanah Jabatan</label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                   name="jabatan" id="jabatan" placeholder="Jabatan / amanah"
                                   value="{{ old('jabatan') }}" required>
                            <i class="fas fa-id-badge input-icon"></i>
                        </div>
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

                <hr class="section-divider">

                {{-- ── DATA KENDARAAN ── --}}
                <p class="section-title"><i class="fas fa-car-side me-2"></i>Data Kendaraan</p>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="jenis_kendaraan">Jenis Kendaraan</label>
                        <select class="form-select @error('jenis_kendaraan') is-invalid @enderror"
                                name="jenis_kendaraan" id="jenis_kendaraan" required>
                            <option value="" disabled {{ old('jenis_kendaraan') ? '' : 'selected' }}>-- Pilih Jenis --</option>
                            <option value="Mobil" {{ old('jenis_kendaraan') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                            <option value="Motor" {{ old('jenis_kendaraan') == 'Motor' ? 'selected' : '' }}>Motor</option>
                            <option value="Bus/Minibus" {{ old('jenis_kendaraan') == 'Bus/Minibus' ? 'selected' : '' }}>Bus / Minibus</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="nomor_plat">No. Plat Kendaraan</label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control @error('nomor_plat') is-invalid @enderror"
                                   name="nomor_plat" id="nomor_plat" placeholder="Contoh: B 1234 XYZ"
                                   value="{{ old('nomor_plat') }}" required>
                            <i class="fas fa-hashtag input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nama_kendaraan">Nama / Merk Kendaraan</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('nama_kendaraan') is-invalid @enderror"
                               name="nama_kendaraan" id="nama_kendaraan" placeholder="Contoh: Toyota Avanza"
                               value="{{ old('nama_kendaraan') }}" required>
                        <i class="fas fa-car input-icon"></i>
                    </div>
                </div>

                <hr class="section-divider">

                {{-- ── DATA PEMAKAIAN ── --}}
                <p class="section-title"><i class="fas fa-calendar-alt me-2"></i>Rencana Pemakaian</p>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="tanggal_pemakaian">Tanggal Pemakaian</label>
                        <input type="date" class="form-control @error('tanggal_pemakaian') is-invalid @enderror"
                               id="tanggal_pemakaian" name="tanggal_pemakaian"
                               value="{{ old('tanggal_pemakaian') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="tanggal_kembali">Tanggal Pengembalian</label>
                        <input type="date" class="form-control @error('tanggal_kembali') is-invalid @enderror"
                               id="tanggal_kembali" name="tanggal_kembali"
                               value="{{ old('tanggal_kembali') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="peruntukan">Peruntukan</label>
                    <select class="form-select @error('peruntukan') is-invalid @enderror"
                            name="peruntukan" id="peruntukan" required>
                        <option value="" disabled {{ old('peruntukan') ? '' : 'selected' }}>-- Pilih Peruntukan --</option>
                        <option value="Keperluan Organisasi BMI" {{ old('peruntukan') == 'Keperluan Organisasi BMI' ? 'selected' : '' }}>Keperluan Organisasi BMI</option>
                        <option value="Keperluan Khidmat (divisi non-BMI)" {{ old('peruntukan') == 'Keperluan Khidmat (divisi non-BMI)' ? 'selected' : '' }}>Keperluan Khidmat (divisi non-BMI)</option>
                        <option value="Pribadi" {{ old('peruntukan') == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lokasi_tujuan">Lokasi Tujuan Perjalanan</label>
                    <input type="text" class="form-control @error('lokasi_tujuan') is-invalid @enderror"
                           name="lokasi_tujuan" id="lokasi_tujuan" placeholder="Alamat / kota tujuan"
                           value="{{ old('lokasi_tujuan') }}" required>
                </div>

                <div class="form-group">
                    <label for="nama_kegiatan">Nama Kegiatan</label>
                    <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror"
                           name="nama_kegiatan" id="nama_kegiatan" placeholder="Nama kegiatan / agenda"
                           value="{{ old('nama_kegiatan') }}" required>
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
        const form      = document.getElementById('peminjamanKendaraanForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
        });
    </script>
</body>
</html>
