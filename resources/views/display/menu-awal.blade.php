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
        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('img/background-1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .menu-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
            width: 90%;
            max-width: 420px;
            margin: 0 auto;
            z-index: 10;
        }

        /* Floating Clouds Styling */
        .cloud-left {
            position: absolute;
            left: -8%;
            top: 20%;
            width: 380px;
            max-width: 35vw;
            opacity: 0.9;
            pointer-events: none;
            z-index: 2;
            animation: floatLeft 18s ease-in-out infinite alternate;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
        }

        .cloud-right {
            position: absolute;
            right: -8%;
            bottom: 15%;
            width: 420px;
            max-width: 38vw;
            opacity: 0.9;
            pointer-events: none;
            z-index: 2;
            animation: floatRight 22s ease-in-out infinite alternate;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
        }

        @keyframes floatLeft {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            33% {
                transform: translate(25px, -15px) scale(1.03) rotate(0.8deg);
            }
            66% {
                transform: translate(-10px, 20px) scale(0.97) rotate(-0.5deg);
            }
            100% {
                transform: translate(15px, -5px) scale(1.02) rotate(0.3deg);
            }
        }

        @keyframes floatRight {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            33% {
                transform: translate(-30px, 20px) scale(0.96) rotate(-1deg);
            }
            66% {
                transform: translate(15px, -25px) scale(1.04) rotate(0.6deg);
            }
            100% {
                transform: translate(-15px, 10px) scale(0.98) rotate(-0.4deg);
            }
        }

        /* Responsive design for clouds on mobile */
        @media (max-width: 768px) {
            .cloud-left {
                width: 160px;
                max-width: 45vw;
                left: -12%;
                top: 10%;
                opacity: 0.75;
                animation: floatLeftMobile 12s ease-in-out infinite alternate;
            }
            .cloud-right {
                width: 200px;
                max-width: 50vw;
                right: -12%;
                bottom: 8%;
                opacity: 0.75;
                animation: floatRightMobile 14s ease-in-out infinite alternate;
            }
        }

        @keyframes floatLeftMobile {
            0% {
                transform: translate(0, 0) scale(1);
            }
            100% {
                transform: translate(10px, -10px) scale(1.05);
            }
        }

        @keyframes floatRightMobile {
            0% {
                transform: translate(0, 0) scale(1);
            }
            100% {
                transform: translate(-12px, 12px) scale(0.95);
            }
        }

        /* Laptop/Desktop specific width */
        @media (min-width: 1024px) {
            .menu-container {
                max-width: 420px;
            }
        }



        .primary-color {
            color: #6a0dad;
        }

        .menu-btn {
            background-color: #6a0dad;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-btn:hover {
            background-color: #9c4ade;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(106, 13, 173, 0.3);
        }

        .menu-btn:before {
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

        .menu-btn:hover:before {
            left: 100%;
        }

        @media (max-width: 640px) {
            .menu-container {
                width: 95%;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Clouds Background Elements -->
    <img src="img/awan-1.png" alt="Cloud Left" class="cloud-left">
    <img src="img/awan-2.png" alt="Cloud Right" class="cloud-right">

    <div class="menu-container p-4 md:p-6">
        
        <div class="flex flex-col items-center justify-center relative z-10">
            <img src="img/logo2024.png" alt="BMI Logo" class="w-20 mb-2">
            
            <h2 class="primary-color text-xl font-bold mb-1">
                Asset & GA
            </h2>
            
            <p class="text-gray-600 mb-3 text-center text-xs">
                Sistem Pengaduan & Pengajuan Online
            </p>
            
            <div class="flex flex-col w-full gap-2 mb-2">
                <a href="/pengaduan-aset" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-exclamation-circle menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Aduan Aset</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>
                
                <a href="/ajuan-rutin" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-calendar-check menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Ajuan Rutin Bulanan</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>
                
                <!-- <a href="/pendataan-stok" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-exclamation-triangle menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Ajuan Darurat</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a> -->
                
                <a href="{{ route('peminjaman.create') }}" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-car menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Peminjaman Kendaraan</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>

                <a href="{{ route('peminjaman.jadwal') }}" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-calendar-alt menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Cek Jadwal Kendaraan</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>

                <a href="/login" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-user-shield menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Admin</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>

                <a href="/about" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fa-brands fa-angellist menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Tentang Kami</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>
            </div>
            
            <div class="mt-2 text-xs text-gray-500 text-center">
                <p id="current-date"></p>
            </div>
        </div>
    </div>

    <script>
        // Display current date
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
        
        // Parallax mousemove effect dinonaktifkan untuk menghemat CPU browser.
    </script>
</body>
</html>