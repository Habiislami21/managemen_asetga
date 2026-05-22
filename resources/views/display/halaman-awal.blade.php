<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIPusat-Aset Online</title>
    <link rel="shortcut icon" href="img/logo2024.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .login-container {
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
            min-width: 280px;
            width: 90%;
            padding: 1rem;
        }

        @media (min-width: 640px) {
            .login-container {
                padding: 2rem;
                width: 85%;
            }
        }

        .primary-color {
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        h2.primary-color {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .login-btn {
            background-color: #a95199;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            background-color: #8e3f7e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(169, 81, 153, 0.4);
        }

        .login-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transition: all 0.6s;
        }

        .login-btn:hover:before {
            left: 100%;
        }


        .feature-icon {
            transition: all 0.3s ease;
        }


        @media (max-width: 360px) {
        .feature-icon {
            padding: 0.5rem !important;
            }
        .feature-icon i {
            font-size: 0.8rem;
            }
        }
    </style>
</head>
<body class="relative flex items-center justify-center min-h-screen overflow-hidden bg-gray-900">
    <!-- Container Background Video & Fallback -->
    <div class="absolute inset-0 w-full h-full overflow-hidden z-0">
        <!-- Fallback Image (Tampil Seketika) -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('img/background-1.jpg') }}');"></div>
        
        <!-- Video Element (Loaded via JS untuk performa maksimal) -->
        <video id="bg-video" autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover hidden md:block opacity-0 transition-opacity duration-1000 z-10"></video>
        
        <!-- Overlay Gelap & Blur Estetik (Sedikit Lebih Gelap agar Teks Pop) -->
        <div class="absolute inset-0 bg-black/55 backdrop-blur-[2px] z-20"></div>
    </div>

    <!-- Login Container (Tanpa Kotak/Card Latar Belakang) -->
    <div class="login-container w-11/12 max-w-md p-8 md:p-10 relative z-30">
        
        <div class="flex flex-col items-center justify-center relative z-10">
            <img src="img/logo2024.png" alt="BMI Logo" class="logo w-32 mb-6 drop-shadow-[0_4px_6px_rgba(0,0,0,0.3)]">
            
            <h2 class="primary-color text-2xl font-bold mb-2 flex flex-col justify-center items-center text-center">
                <!-- <span>Selamat Datang</span> -->
                <span class="text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]">Sistem BMI</span>
            </h2>
            
            <p class="text-gray-200 mb-6 text-center tracking-wide font-light drop-shadow-[0_1px_2px_rgba(0,0,0,0.5)]">Asset & GA Management System</p>
            
            <div class="grid grid-cols-3 gap-2 sm:gap-4 w-full mb-8">
                <div class="flex flex-col items-center text-center">
                    <div class="feature-icon bg-white/10 backdrop-blur-md border border-white/20 rounded-full p-3 mb-2 shadow-lg">
                        <i class="fas fa-chart-bar text-purple-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.3)]"></i>
                    </div>
                    <span class="text-xs text-gray-200 font-medium drop-shadow-[0_1px_2px_rgba(0,0,0,0.5)]">Asset Report</span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="feature-icon bg-white/10 backdrop-blur-md border border-white/20 rounded-full p-3 mb-2 shadow-lg">
                        <i class="fas fa-file-contract text-purple-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.3)]"></i>
                    </div>
                    <span class="text-xs text-gray-200 font-medium drop-shadow-[0_1px_2px_rgba(0,0,0,0.5)]">GA Requirements</span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="feature-icon bg-white/10 backdrop-blur-md border border-white/20 rounded-full p-3 mb-2 shadow-lg">
                        <i class="fas fa-car text-purple-200 drop-shadow-[0_1px_3px_rgba(0,0,0,0.3)]"></i>
                    </div>
                    <span class="text-xs text-gray-200 font-medium whitespace-nowrap drop-shadow-[0_1px_2px_rgba(0,0,0,0.5)]">Vehicle Info</span>
                </div>
            </div>
            
            <a href="/menu-awal" class="login-btn w-full py-3 px-6 rounded-lg text-white font-medium text-center transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-purple-500/30">
                <span>Assalamualaikum</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            
            <div class="mt-6 text-xs text-gray-300 text-center drop-shadow-[0_1px_2px_rgba(0,0,0,0.5)]">
                <p id="current-date"></p>
            </div>
        </div>
    </div>

    <script>
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);

        // Pemuatan Video Latar Belakang yang Dioptimalkan (Zero-Impact Loading)
        document.addEventListener('DOMContentLoaded', () => {
            // Hanya load video di desktop (layar lebar >= 768px) demi hemat kuota & daya mobile
            if (window.innerWidth >= 768) {
                const video = document.getElementById('bg-video');
                if (video) {
                    const source = document.createElement('source');
                    source.src = "{{ asset('video/company profile.mp4') }}";
                    source.type = "video/mp4";
                    video.appendChild(source);
                    
                    // Trigger loading video secara asinkron
                    video.load();
                    
                    // Efek transisi memudar halus saat video siap diputar
                    video.addEventListener('canplaythrough', () => {
                        video.classList.remove('opacity-0');
                        video.classList.add('opacity-100');
                    });
                }
            }
        });
    </script>
</body>
</html>