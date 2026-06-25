<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <!-- Tambahan Font Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tambahan Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr for 24h Time Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
            background-image: linear-gradient(rgba(97, 0, 221, 0.05), rgba(97, 0, 221, 0.1)), url('{{ asset('img/background-2.png') }}');
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

        .form-check-label {
            font-size: 14px;
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
                <h2>Form Peminjaman Kendaraan</h2>
                <p class="form-subtitle">Sarana & Prasarana BMI Pusat</p>
            </div>
            
            <form id="peminjamanForm" action="{{ route('peminjaman.store') }}" method="POST">
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
                    <label for="nama_driver">Nama Driver</label>
                    <div class="input-with-icon">
                        <input type="text" class="form-control @error('nama_driver') is-invalid @enderror" 
                               name="nama_driver" id="nama_driver" placeholder="Masukkan nama driver"
                               value="{{ old('nama_driver') }}" required>
                        <i class="fas fa-user-tie input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nomor_hp">Nomor HP (WhatsApp)</label>
                    <div class="input-with-icon">
                        <input type="tel" class="form-control @error('nomor_hp') is-invalid @enderror" 
                               name="nomor_hp" id="nomor_hp" placeholder="Nomor HP : 0895xxxxxxx" 
                               value="{{ old('nomor_hp') }}" required>
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="kendaraan_id">Pilih Kendaraan</label>
                    <div class="input-with-icon">
                        <select name="kendaraan_id" id="kendaraan_id" class="form-select @error('kendaraan_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach($kendaraans as $kendaraan)
                                <option value="{{ $kendaraan->id }}" {{ old('kendaraan_id') == $kendaraan->id ? 'selected' : '' }}>
                                    {{ $kendaraan->nama }} ({{ $kendaraan->kategori }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-car input-icon"></i>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="tanggal_pinjam">Tanggal Pinjam</label>
                        <div class="input-with-icon">
                            <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                                   id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}" required>
                            <i class="fas fa-calendar-alt input-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="jam_pinjam">Jam Pinjam</label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control timepicker @error('jam_pinjam') is-invalid @enderror" 
                                   id="jam_pinjam" name="jam_pinjam" value="{{ old('jam_pinjam') }}" placeholder="00:00" required>
                            <i class="fas fa-clock input-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="jam_kembali">Jam Kembali</label>
                        <div class="input-with-icon">
                            <input type="text" class="form-control timepicker @error('jam_kembali') is-invalid @enderror" 
                                   id="jam_kembali" name="jam_kembali" value="{{ old('jam_kembali') }}" placeholder="00:00" required>
                            <i class="fas fa-clock input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keperluan">Keperluan</label>
                    <textarea class="form-control @error('keperluan') is-invalid @enderror" 
                              name="keperluan" id="keperluan" rows="3"
                              placeholder="Jelaskan detail keperluan meminjam kendaraan..." 
                              required>{{ old('keperluan') }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="alamat_tujuan">Alamat Tujuan</label>
                    <textarea class="form-control @error('alamat_tujuan') is-invalid @enderror" 
                              name="alamat_tujuan" id="alamat_tujuan" rows="2"
                              placeholder="Alamat yang dituju..." 
                              required>{{ old('alamat_tujuan') }}</textarea>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input @error('tanggung_jawab') is-invalid @enderror" type="checkbox" id="tanggung_jawab" name="tanggung_jawab" required>
                    <label class="form-check-label text-danger fw-bold" for="tanggung_jawab">
                        Saya bertanggung jawab penuh atas kendaraan selama masa peminjaman. Segala kerusakan, kebersihan, dan bahan bakar bansin kendaraan akan menjadi tanggung jawab saya saat peminjaman.
                    </label>
                </div>
                 
                <button type="submit" class="btn btn-submit" 
                        onclick="if(document.getElementById('peminjamanForm').checkValidity()) { this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengirim...'; this.form.submit(); }">
                    <i class="fas fa-paper-plane btn-icon"></i>Kirim Pengajuan
                </button>
                <div class="text-center mt-3">
                    <a href="{{ url('/menu-awal') }}" class="text-decoration-none" style="color: var(--primary); font-size: 14px;">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Menu Awal
                    </a>
                </div>
            </form>

            <!-- Indikator Scroll -->
            <div class="scroll-indicator" id="scrollIndicator">
                <i class="fas fa-chevron-down"></i>
                <span>Geser ke bawah</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk validasi form dengan visual feedback
        const formEl = document.getElementById('peminjamanForm');
        const inputs = formEl.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            if(input.type === 'checkbox') return;
            
            // Validasi saat blur atau saat nilai berubah (penting untuk mobile/picker)
            const validate = () => {
                if (input.value.trim() === '' && input.hasAttribute('required')) {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            };

            input.addEventListener('blur', validate);
            input.addEventListener('change', validate);
            
            input.addEventListener('focus', () => {
                const label = input.closest('.form-group')?.querySelector('label');
                if (label) {
                    label.style.color = 'var(--primary)';
                }
            });
            
            input.addEventListener('blur', () => {
                const label = input.closest('.form-group')?.querySelector('label');
                if (label && !input.value) {
                    label.style.color = '';
                }
            });
        });

        // Script untuk menampilkan/menyembunyikan indikator scroll
        const formContainer = document.querySelector('.form-container');
        const scrollIndicator = document.getElementById('scrollIndicator');
        
        function updateScrollIndicator() {
            const scrollTop = formContainer.scrollTop;
            const viewportHeight = formContainer.clientHeight;
            const totalHeight = formContainer.scrollHeight;
            
            const isNearBottom = (scrollTop + viewportHeight) >= (totalHeight - 50);
            
            if (isNearBottom) {
                scrollIndicator.classList.remove('visible');
            } else {
                scrollIndicator.classList.add('visible');
            }
        }
        
        formContainer.addEventListener('scroll', updateScrollIndicator);
        
        window.addEventListener('load', () => {
            const needsScroll = formContainer.scrollHeight > formContainer.clientHeight + 50;
            
            if (needsScroll) {
                updateScrollIndicator();
            } else {
                scrollIndicator.classList.remove('visible');
            }
            
            setTimeout(updateScrollIndicator, 500);
        });
        
        window.addEventListener('resize', updateScrollIndicator);

        // Initialize Flatpickr for 24h format
        flatpickr(".timepicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            disableMobile: true, // Pastikan boolean true
            allowInput: true,    // Izinkan input manual jika picker susah di hp tertentu
            onClose: function(selectedDates, dateStr, instance) {
                // Trigger change event agar validasi berjalan
                instance.element.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
