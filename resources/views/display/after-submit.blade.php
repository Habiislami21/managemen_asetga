<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIPusat-Aset Online</title>
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#c87fb6',
                            DEFAULT: '#a95199',
                            dark: '#8e3f7e',
                        },
                        secondary: {
                            light: '#5ab06a',
                            DEFAULT: '#4a9e5a',
                            dark: '#3d834b',
                        }
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-light': 'bounce-light 1s infinite',
                        'spin-slow': 'spin 8s linear infinite',
                    },
                    keyframes: {
                        'float': {
                            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(-10px) rotate(2deg)' },
                        },
                        'bounce-light': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('img/background-2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        
        .success-card {
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .background-shapes div {
            position: absolute;
            z-index: -1;
            opacity: 0.5;
        }
        
        .check-animation {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: dash 2s ease-in-out forwards;
        }
        
        @keyframes dash {
            to {
                stroke-dashoffset: 0;
            }
        }
        
        .message-appear {
            opacity: 0;
            transform: translateY(20px);
            animation: appear 0.8s ease forwards 0.5s;
        }
        
        .button-appear {
            opacity: 0;
            transform: translateY(20px);
            animation: appear 0.8s ease forwards 1s;
        }
        
        @keyframes appear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 20px;
            opacity: 0;
            animation: fall linear forwards;
        }
        
        @keyframes fall {
            0% {
                opacity: 1;
                top: -20px;
                transform: translateX(0) rotate(0deg);
            }
            100% {
                opacity: 0.3;
                top: 100vh;
                transform: translateX(100px) rotate(360deg);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <!-- Background animated shapes -->
    <div class="background-shapes">
        <div class="top-20 left-20 w-64 h-64 bg-primary-light rounded-full opacity-5 animate-spin-slow"></div>
        <div class="bottom-20 right-20 w-64 h-64 bg-primary rounded-full opacity-5 animate-spin-slow"></div>
        <div class="top-40 right-40 w-32 h-32 bg-secondary-light rounded-full opacity-5 animate-pulse-slow"></div>
    </div>
    
    <!-- Main success card -->
    <div class="success-card relative bg-white/95 rounded-3xl p-8 md:p-10 w-full max-w-md mx-auto transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-1 overflow-hidden">
        
        <!-- Decorative blobs - absolutely positioned -->
        <div class="absolute -top-20 -left-20 w-40 h-40 bg-gradient-to-br from-primary-light/10 to-primary/10 rounded-full blur-xl"></div>
        <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-gradient-to-tr from-secondary-light/10 to-secondary/10 rounded-full blur-xl"></div>
        
        <!-- Content container -->
        <div class="relative z-10 flex flex-col items-center justify-center text-center">
            
            <!-- Success icon/animation -->
            <div class="w-28 h-28 rounded-full bg-primary/10 flex items-center justify-center mb-6 relative overflow-hidden">
                <!-- Ring pulse animation -->
                <div class="absolute inset-0 rounded-full border-4 border-primary/30 animate-pulse"></div>
                
                <!-- Success checkmark SVG -->
                <svg class="w-14 h-14 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="check-animation" d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            
            <!-- Content -->
            <h2 class="text-2xl md:text-3xl font-bold text-primary mb-3">Berhasil Terkirim!</h2>
            
            <div class="message-appear">
                <p class="text-gray-600 mb-8">
                    {{ session('formType') == 'aduan' ? 'Pengaduan' : 'Pengajuan' }} Anda telah diterima dan akan segera diproses. 
                    <span class="block mt-2 font-medium text-primary-dark">Terima kasih atas partisipasi Anda!</span>
                </p>
                
                <!-- Status indicator -->
                <div class="flex items-center justify-center gap-2 mb-8 bg-green-50 py-2 px-4 rounded-full text-green-600">
                    <div class="relative">
                        <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                        <div class="absolute inset-0 h-2 w-2 rounded-full bg-green-500 animate-ping"></div>
                    </div>
                    <span class="text-sm font-medium">Status: Berhasil Terkirim</span>
                </div>
                
                <!-- Action buttons -->
                <div class="button-appear flex flex-col md:flex-row gap-3 w-full">
                    
                    <a href="/" class="flex-1 py-3 px-4 rounded-xl bg-primary text-white font-medium transition-all hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/20 flex items-center justify-center gap-2">
                        <i class="fas fa-home"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div id="confetti-container"></div>
    
    <script>
        // Create confetti pieces
        document.addEventListener('DOMContentLoaded', function() {
            const confettiContainer = document.getElementById('confetti-container');
            const colors = ['#a95199', '#c87fb6', '#4a9e5a', '#5ab06a', '#f9d56e'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.classList.add('confetti');
                    
                    // Random position, color, rotation, and animation duration
                    const posX = Math.random() * window.innerWidth;
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    const duration = 5 + Math.random() * 5;
                    const size = 5 + Math.random() * 10;
                    
                    confetti.style.left = `${posX}px`;
                    confetti.style.backgroundColor = color;
                    confetti.style.width = `${size}px`;
                    confetti.style.height = `${size * 1.5}px`;
                    confetti.style.animationDuration = `${duration}s`;
                    
                    confettiContainer.appendChild(confetti);
                    
                    // Remove confetti after animation
                    setTimeout(() => {
                        confetti.remove();
                    }, duration * 1000);
                }, 100 * i);
            }
            
            // Add hover effect to the buttons
            const buttons = document.querySelectorAll('a');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.classList.add('scale-105');
                });
                button.addEventListener('mouseleave', function() {
                    this.classList.remove('scale-105');
                });
            });
        });
    </script>
</body>
</html>