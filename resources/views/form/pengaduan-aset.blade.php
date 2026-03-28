<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIPusat-Aset Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <!-- Tambahan Font Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tambahan Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --secondary: #4a9e5a;
            --secondary-hover: #3d834b;
            --text-primary: #333;
            --bg-light: #f8f9fa;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: linear-gradient(rgba(97, 0, 221, 0.05), rgba(97, 0, 221, 0.1)), url('img/background-2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Mencegah scroll pada body */
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        html {
            height: 100%;
            overflow: hidden;
        }

        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .form-container {
            background: #ffffff;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 650px;
            max-height: calc(100vh - 40px); /* Tinggi maksimum dengan margin */
            height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            transition: all 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) #f0f0f0;
        }
        
        /* Styling untuk scrollbar pada webkit browsers (Chrome, Safari) */
        .form-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .form-container::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }
        
        .form-container::-webkit-scrollbar-thumb {
            background-color: var(--primary);
            border-radius: 10px;
            border: 2px solid #f0f0f0;
        }
        
        .form-container::-webkit-scrollbar-thumb:hover {
            background-color: var(--primary-hover);
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
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control, .form-select {
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            padding: 12px 16px;
            width: 100%;
            font-size: 15px;
            transition: all 0.3s ease;
            color: #333;
            background-color: #fff;
            box-shadow: none;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
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
            transition: all 0.3s ease;
        }

        .input-with-icon .form-control,
        .input-with-icon .form-select {
            padding-left: 45px;
        }

        .input-with-icon .form-control:focus + .input-icon,
        .input-with-icon .form-select:focus + .input-icon {
            color: var(--primary);
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            width: 100%;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(97, 0, 221, 0.2);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(97, 0, 221, 0.25);
        }
        
        .btn-secondary {
            background: var(--secondary);
            box-shadow: 0 4px 6px rgba(74, 158, 90, 0.2);
        }
        
        .btn-secondary:hover {
            background: var(--secondary-hover);
            box-shadow: 0 6px 12px rgba(74, 158, 90, 0.25);
        }
        
        .btn-action-group {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }

        .btn-action-group .btn {
            flex: 1;
        }

        .btn-icon {
            margin-right: 8px;
        }

        .alert {
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-weight: 500;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #e6f7ec;
            color: #2a9d5f;
            border-color: #2a9d5f;
        }

        .alert-danger {
            background-color: #ffeaea;
            color: #e53e3e;
            border-color: #e53e3e;
        }

        .alert ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }

        .scroll-indicator {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-size: 14px;
            color: var(--primary);
            opacity: 0;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.95);
            padding: 8px 16px;
            border-radius: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            pointer-events: none;
            transform: translateX(-50%) translateY(20px);
        }

        .scroll-indicator.visible {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .scroll-indicator i {
            font-size: 18px;
            animation: bounce 2s infinite;
            color: var(--primary);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        /* Field validation styling */
        .is-invalid {
            border-color: #e53e3e !important;
        }
        
        .invalid-feedback {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Form progress indicator */
        .form-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .progress-bar {
            position: absolute;
            top: 15px;
            left: 0;
            height: 4px;
            background-color: #e1e1e1;
            width: 100%;
            z-index: 1;
        }
        
        .progress-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: var(--primary);
            transition: width 0.3s ease;
            z-index: 2;
        }
        
        .progress-step {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #e1e1e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #888;
            position: relative;
            z-index: 3;
            transition: all 0.3s ease;
        }
        
        .progress-step.active {
            border-color: var(--primary);
            background-color: var(--primary);
            color: #fff;
        }
        
        .progress-step.completed {
            border-color: var(--primary);
            background-color: #fff;
            color: var(--primary);
        }
        
        .progress-step i {
            font-size: 16px;
        }

        /* Field animation */
        .form-group {
            transition: all 0.3s ease;
        }

        .form-control:focus ~ label,
        .form-select:focus ~ label {
            color: var(--primary);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 30px;
                border-radius: 16px;
                max-height: calc(100vh - 30px);
            }
            
            h2 {
                font-size: 24px;
            }
            
            .btn-action-group {
                flex-direction: column;
            }
            
            .main-container {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0;
            }
            
            .main-container {
                padding: 10px;
            }
            
            .form-container {
                padding: 24px;
                border-radius: 12px;
                max-height: calc(100vh - 20px);
            }
            
            h2 {
                font-size: 22px;
            }
            
            .form-control, .form-select {
                font-size: 14px;
                padding: 10px 14px;
            }
            
            .btn-submit {
                padding: 12px 16px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-container">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span>Terdapat kesalahan pada pengisian form:</span>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="form-header">
                <h2>Form Pengaduan Kerusakan</h2>
                <p class="form-subtitle">Sarana & Prasarana BMI Pusat</p>
            </div>
            
            <form id="pengaduanForm" action="{{ route('pengaduan.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="namaSpa">Nama SPA</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('nama_spa') is-invalid @enderror" 
                               name="nama_spa" id="namaSpa" placeholder="Masukkan nama SPA"
                               value="{{ old('nama_spa') }}" required>
                        <i class="fas fa-user input-icon"></i>
                        @error('nama_spa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="divisi">Divisi</label>
                    <div class="input-with-icon">
                        <select name="divisi_id" id="divisi" class="form-select @error('divisi_id') is-invalid @enderror" required>
                            <option value="">Pilih Divisi</option>
                            @foreach ($divisis as $divisi)
                                <option value="{{ $divisi->id }}" {{old('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->divisi}}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-building input-icon"></i>
                        @error('divisi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="amanah">Amanah</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('amanah') is-invalid @enderror" 
                               name="amanah" id="amanah" placeholder="Masukkan amanah, contohnya staf aset"
                               value="{{ old('amanah') }}" required>
                        <i class="fas fa-briefcase input-icon"></i>
                        @error('amanah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="lokasi_pengaduan">Lokasi Pengaduan</label>
                    <div class="input-with-icon">
                        <select name="lokasi_pengaduan" id="lokasi_pengaduan" 
                                class="form-select @error('lokasi_pengaduan') is-invalid @enderror" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($lokasi_pengaduan as $lokasi)
                                <option value="{{ $lokasi }}" {{ old('lokasi_pengaduan') == $lokasi ? 'selected' : '' }}>
                                    {{ $lokasi }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        @error('lokasi_pengaduan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="jenis_pengaduan">Jenis Pengaduan</label>
                    <div class="input-with-icon">
                        <select name="jenis_pengaduan" id="jenis_pengaduan" 
                                class="form-select @error('jenis_pengaduan') is-invalid @enderror" required>
                            <option value="">Pilih Jenis Pengaduan</option>
                            @foreach(['Aset', 'GA'] as $jenis)
                                <option value="{{ $jenis }}" {{ old('jenis_pengaduan') == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-tasks input-icon"></i>
                        @error('jenis_pengaduan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="kerusakan">Kerusakan/Kendala</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('kerusakan') is-invalid @enderror" 
                               name="kerusakan" id="kerusakan" 
                               placeholder="Contoh: Laptop, Smartphone, Barang Aset Lainnya..."
                               value="{{ old('kerusakan') }}" required>
                        <i class="fas fa-tools input-icon"></i>
                        @error('kerusakan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="rincian_pengaduan">Rincian Pengaduan</label>
                    <textarea class="form-control @error('rincian_pengaduan') is-invalid @enderror" 
                              name="rincian_pengaduan" id="rincian_pengaduan" rows="4"
                              placeholder="Jelaskan detail pengaduan, misalnya: Laptop tidak bisa menyala sejak kemarin..." 
                              required>{{ old('rincian_pengaduan') }}</textarea>
                    @error('rincian_pengaduan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="nomor_telp">Nomor Telepon</label>
                    <div class="input-with-icon">
                        <input type="tel" class="form-control @error('nomor_telp') is-invalid @enderror" 
                               name="nomor_telp" id="nomor_telp" placeholder="Masukkan Nomor Telepon" 
                               pattern="^\d{10,15}$" title="Nomor telepon harus berisi 10-15 digit angka"
                               value="{{ old('nomor_telp') }}" required>
                        <i class="fas fa-phone input-icon"></i>
                        @error('nomor_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="btn-action-group">
                    <!-- Tombol submit biasa -->
                    <button type="submit" class="btn btn-submit" name="submit_action" value="single" 
                            onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengirim...'; this.form.submit();">
                        <i class="fas fa-paper-plane btn-icon"></i>Kirim Pengaduan
                    </button>
                    
                    <!-- Tombol submit dan isi lagi -->
                    <button type="submit" class="btn btn-submit btn-secondary" name="submit_action" value="multiple">
                        <i class="fas fa-plus btn-icon"></i>Buat Aduan Lain
                    </button>
                </div>
            </form>

            <!-- Indikator Scroll - Lebih Responsif -->
            <div class="scroll-indicator" id="scrollIndicator">
                <i class="fas fa-chevron-down"></i>
                <span>Geser ke bawah</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk validasi form dengan visual feedback
        const formEl = document.getElementById('pengaduanForm');
        const inputs = formEl.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                if (input.value.trim() === '' && input.hasAttribute('required')) {
                    input.classList.add('is-invalid');
                    
                    // Cek jika pesan error belum ada
                    let nextEl = input.nextElementSibling;
                    if (!nextEl || !nextEl.classList.contains('invalid-feedback')) {
                        const errorMsg = document.createElement('div');
                        errorMsg.classList.add('invalid-feedback');
                        errorMsg.textContent = 'Field ini harus diisi';
                        input.parentNode.appendChild(errorMsg);
                    }
                } else {
                    input.classList.remove('is-invalid');
                    
                    // Hapus pesan error jika ada
                    let nextEl = input.nextElementSibling;
                    if (nextEl && nextEl.classList.contains('invalid-feedback')) {
                        nextEl.remove();
                    }
                }
            });
            
            input.addEventListener('focus', () => {
                const label = input.closest('.form-group').querySelector('label');
                if (label) {
                    label.style.color = 'var(--primary)';
                }
            });
            
            input.addEventListener('blur', () => {
                const label = input.closest('.form-group').querySelector('label');
                if (label && !input.value) {
                    label.style.color = '';
                }
            });
        });

        // Script untuk menampilkan/menyembunyikan indikator scroll
        const formContainer = document.querySelector('.form-container');
        const scrollIndicator = document.getElementById('scrollIndicator');
        
        // Fungsi untuk cek scroll dan update indikator
        function updateScrollIndicator() {
            // Ambil nilai scroll saat ini
            const scrollTop = formContainer.scrollTop;
            const viewportHeight = formContainer.clientHeight;
            const totalHeight = formContainer.scrollHeight;
            
            // Menentukan apakah sudah mendekati bagian bawah
            // Nilai 50 adalah threshold (ambang batas) yang dapat disesuaikan
            const isNearBottom = (scrollTop + viewportHeight) >= (totalHeight - 50);
            
            // Update tampilan indikator
            if (isNearBottom) {
                scrollIndicator.classList.remove('visible');
            } else {
                scrollIndicator.classList.add('visible');
            }
        }
        
        // Pasang event listener
        formContainer.addEventListener('scroll', updateScrollIndicator);
        
        // Periksa kondisi scroll saat halaman dimuat
        window.addEventListener('load', () => {
            // Cek apakah form membutuhkan scroll
            const needsScroll = formContainer.scrollHeight > formContainer.clientHeight + 50;
            
            if (needsScroll) {
                // Jalankan pemeriksaan awal
                updateScrollIndicator();
            } else {
                // Jika tidak perlu scroll, jangan tampilkan indikator
                scrollIndicator.classList.remove('visible');
            }
            
            // Tambahan animasi untuk form
            document.querySelectorAll('.form-group').forEach((group, index) => {
                setTimeout(() => {
                    group.style.opacity = '1';
                }, 100 * index);
            });
            
            // Jalankan pemeriksaan tambahan setelah semua konten dimuat
            setTimeout(updateScrollIndicator, 500);
        });
        
        // Jalankan pemeriksaan saat ukuran window berubah
        window.addEventListener('resize', updateScrollIndicator);
    </script>
</body>
</html>