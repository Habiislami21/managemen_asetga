<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIPusat - Asset & General Affair</title>
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    
    <!-- Tailwind CSS with custom configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- External Icon & Typography libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #020617;
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #090d16;
        }
        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #059669;
        }
        
        /* Elegant Glassmorphism Cards */
        .glass-panel {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }
        
        .glass-card {
            background: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            background: rgba(16, 185, 129, 0.06);
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.08);
            transform: translateY(-8px);
        }
        
        /* Typewriter Cursor Glow */
        .typewriter-cursor::after {
            content: '|';
            color: #10b981;
            animation: blink 0.8s step-end infinite;
        }
        
        @keyframes blink {
            from, to { opacity: 1; }
            50% { opacity: 0; }
        }
        
        /* Scroll Reveal Animation Styling */
        .reveal-item {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.9s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .reveal-item.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        
        .float-slow {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Custom Navigation Dots */
        .side-dot {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .side-dot.active {
            background-color: #10b981;
            box-shadow: 0 0 12px #10b981;
            transform: scale(1.4);
        }
        
        /* Smooth Slide Transition */
        .slide {
            transition: opacity 0.8s ease-in-out;
        }
        .slide-dot {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="text-slate-100 selection:bg-emerald-500 selection:text-white">

    <!-- Loading Screen -->
    <div id="loading-screen" class="fixed inset-0 bg-slate-950 z-[9999] flex flex-col items-center justify-center transition-all duration-1000">
        <div class="relative flex items-center justify-center">
            <!-- Outer Glowing Ring -->
            <div class="animate-spin rounded-full h-24 w-24 border-t-2 border-b-2 border-emerald-500"></div>
            <!-- Inner Ring -->
            <div class="animate-spin rounded-full h-16 w-16 border-r-2 border-l-2 border-teal-400 absolute duration-[1500ms]" style="animation-direction: reverse;"></div>
            <!-- Central Leaf Icon -->
            <i class="fas fa-leaf text-2xl text-emerald-400 absolute animate-pulse"></i>
        </div>
        <h2 class="text-white text-2xl font-outfit font-semibold mt-8 tracking-wider">BMIPusat-Aset</h2>
        <p class="text-emerald-400/80 text-sm font-poppins mt-2 animate-pulse">Mengharmonisasikan Operasional dengan Alam...</p>
    </div>

    <!-- Video Background Container -->
    <div id="video-container" class="fixed inset-0 w-full h-full overflow-hidden z-[-2] pointer-events-none bg-cover bg-center transition-all duration-1000" style="background-image: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=1920&q=80')">
        <!-- Overlay for better text readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/85 via-slate-950/65 to-slate-950/95 z-[1]"></div>
        <!-- Soft green organic glow effects -->
        <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-emerald-500/8 blur-[130px] pointer-events-none z-[1]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-teal-500/8 blur-[130px] pointer-events-none z-[1]"></div>
        
        <!-- Video Element -->
        <video id="bg-video" autoplay loop muted playsinline class="w-full h-full object-cover scale-[1.02] filter brightness-[0.75] contrast-[1.05] transition-all duration-1000">
            <source id="video-source" src="https://assets.mixkit.co/videos/preview/mixkit-forest-stream-in-the-sunlight-529-large.mp4" type="video/mp4">
        </video>
    </div>

    <!-- Transition Overlay for Video Switching -->
    <div id="video-fade-overlay" class="fixed inset-0 bg-slate-950 opacity-0 z-[-1] pointer-events-none transition-opacity duration-500"></div>

    <!-- Floating Atmosphere Controller -->
    <div class="fixed bottom-6 left-6 z-50 bg-slate-950/75 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-2xl transition-all duration-300 hover:border-emerald-500/30 w-[280px] sm:w-[320px] group">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center space-x-2 text-emerald-400">
                <i class="fas fa-sliders-h animate-pulse"></i>
                <span class="text-xs font-semibold tracking-wider uppercase font-outfit">Suasana Alam</span>
            </div>
            <button id="btn-play-pause" class="text-white hover:text-emerald-400 transition-colors text-xs px-2 py-1 rounded bg-white/5 flex items-center space-x-1" title="Play/Pause Background Video">
                <i class="fas fa-pause" id="play-pause-icon"></i>
                <span id="play-pause-text" class="text-[10px]">Pause</span>
            </button>
        </div>
        
        <div class="space-y-1.5">
            <button onclick="changeAtmosphere('stream', this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-white text-xs text-left transition-all hover:bg-emerald-500/30 font-medium atmosphere-btn">
                <span class="flex items-center space-x-2 whitespace-nowrap">
                    <i class="fas fa-water text-emerald-400"></i>
                    <span>Aliran Sungai Rindang</span>
                </span>
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            </button>
            <button onclick="changeAtmosphere('mist', this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl bg-slate-900/60 border border-white/5 text-slate-300 text-xs text-left transition-all hover:bg-slate-800/80 hover:text-white atmosphere-btn">
                <span class="flex items-center space-x-2 whitespace-nowrap">
                    <i class="fas fa-cloud-sun-rain"></i>
                    <span>Lembah Kabut Sunyi</span>
                </span>
                <span class="w-2 h-2 rounded-full bg-transparent"></span>
            </button>
            <button onclick="changeAtmosphere('waterfall', this)" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl bg-slate-900/60 border border-white/5 text-slate-300 text-xs text-left transition-all hover:bg-slate-800/80 hover:text-white atmosphere-btn">
                <span class="flex items-center space-x-2 whitespace-nowrap">
                    <i class="fas fa-mountain"></i>
                    <span>Air Terjun Hijau</span>
                </span>
                <span class="w-2 h-2 rounded-full bg-transparent"></span>
            </button>
        </div>
    </div>

    <!-- Floating Glassmorphic Header -->
    <header class="fixed top-4 left-1/2 -translate-x-1/2 w-[90%] max-w-7xl z-50 bg-slate-950/40 backdrop-blur-xl border border-white/10 rounded-2xl px-6 py-3 shadow-2xl transition-all duration-300 hover:border-emerald-500/20">
        <div class="flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                <img src="{{ asset('img/logo2024.png') }}" alt="BMI Logo" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
                <div>
                    <span class="text-white font-outfit font-bold text-sm tracking-wide block leading-none">BMI PUSAT</span>
                    <span class="text-emerald-400 font-poppins text-xs block mt-1 tracking-wider uppercase font-semibold">Asset & General Affair</span>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center space-x-8">
                <a href="#headline" class="text-white/80 hover:text-emerald-400 transition-colors font-medium text-sm font-poppins relative after:content-[''] after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-emerald-400 after:transition-all hover:after:w-full">Beranda</a>
                <a href="#apa-yang-kami-lakukan" class="text-white/80 hover:text-emerald-400 transition-colors font-medium text-sm font-poppins relative after:content-[''] after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-emerald-400 after:transition-all hover:after:w-full">Layanan</a>
                <a href="#tim-inti" class="text-white/80 hover:text-emerald-400 transition-colors font-medium text-sm font-poppins relative after:content-[''] after:absolute after:bottom-[-4px] after:left-0 after:w-0 after:h-[2px] after:bg-emerald-400 after:transition-all hover:after:w-full">Tim Kami</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <a href="#tim-inti" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-outfit text-xs font-semibold py-2.5 px-5 rounded-xl transition-all hover:scale-105 hover:shadow-[0_0_15px_rgba(16,185,129,0.4)]">
                    Hubungi Tim
                </a>
            </div>
        </div>
    </header>

    <!-- Side Navigation Dots -->
    <div class="fixed right-6 top-1/2 -translate-y-1/2 z-50 hidden md:flex flex-col space-y-4 bg-slate-950/40 backdrop-blur-xl border border-white/10 py-4 px-2.5 rounded-full shadow-2xl">
        <a href="#headline" class="side-dot w-3 h-3 rounded-full bg-white/30 hover:bg-emerald-400 active" data-section="headline" title="Beranda"></a>
        <a href="#apa-yang-kami-lakukan" class="side-dot w-3 h-3 rounded-full bg-white/30 hover:bg-emerald-400" data-section="apa-yang-kami-lakukan" title="Layanan"></a>
        <a href="#tim-inti" class="side-dot w-3 h-3 rounded-full bg-white/30 hover:bg-emerald-400" data-section="tim-inti" title="Tim Inti"></a>
    </div>

    <!-- Hero / Headline Section -->
    <section id="headline" class="min-height-[100vh] pt-32 pb-20 px-4 md:px-8 flex items-center justify-center relative overflow-hidden">
        <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            
            <!-- Left Side - Glassmorphic Gallery Slideshow -->
            <div class="lg:col-span-6 flex justify-center float-slow">
                <div class="relative w-full max-w-lg glass-panel p-4 rounded-3xl overflow-hidden shadow-2xl border border-white/15">
                    
                    <!-- Blur-backdrop glow representing the slide -->
                    <div id="gallery-blur-bg" class="absolute inset-0 bg-cover bg-center rounded-3xl opacity-20 filter blur-2xl scale-110 duration-1000 transition-all"></div>
                    
                    <!-- Custom Slideshow Container -->
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] group shadow-inner border border-white/10">
                        <!-- Progress slider line -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500/20 z-20">
                            <div id="slide-progress" class="h-full bg-emerald-400 w-0"></div>
                        </div>

                        <!-- Slides -->
                        <div class="relative w-full h-full" id="slideshow">
                            <!-- Slide 1 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-100 duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-3.jpeg')}}" alt="Asset Management System" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Kegiatan</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Outbound & Bounding</h3>
                                    <p class="text-slate-300 text-xs mt-1">Mengokohkan kebersamaan dan sinergi tim di alam bebas.</p>
                                </div>
                            </div>
                            <!-- Slide 2 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-2.jpeg')}}" alt="Team Working" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Rapat</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Sesi Briefing</h3>
                                    <p class="text-slate-300 text-xs mt-1">Perencanaan strategis demi alur operasional yang lancar.</p>
                                </div>
                            </div>
                            <!-- Slide 3 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-4.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Momen</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Paskas Day</h3>
                                    <p class="text-slate-300 text-xs mt-1">Berbagi dedikasi dan kontribusi nyata bagi umat.</p>
                                </div>
                            </div>
                            <!-- Slide 4 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-5.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Spritual</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Kajian di Masjid</h3>
                                    <p class="text-slate-300 text-xs mt-1">Mengisi nutrisi rohani agar bekerja senantiasa bernilai ibadah.</p>
                                </div>
                            </div>
                            <!-- Slide 5 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-6.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Koordinasi</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">After Briefing</h3>
                                    <p class="text-slate-300 text-xs mt-1">Evaluasi cepat setelah koordinasi lapangan selesai dilaksanakan.</p>
                                </div>
                            </div>
                            <!-- Slide 6 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-7.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Ruang Kerja</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Ruang Finance 1</h3>
                                    <p class="text-slate-300 text-xs mt-1">Menata kenyamanan ruang keuangan agar fokus tetap terjaga.</p>
                                </div>
                            </div>
                            <!-- Slide 7 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-8.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Fasilitas</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Ruang Finance 2</h3>
                                    <p class="text-slate-300 text-xs mt-1">Dukungan tata ruang yang efisien dan ergonomis.</p>
                                </div>
                            </div>
                            <!-- Slide 8 -->
                            <div class="slide absolute inset-0 w-full h-full opacity-0 hidden duration-1000 transition-opacity">
                                <img src="{{asset('img/galeri-9.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent p-5 pt-12">
                                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Logistik</span>
                                    <h3 class="text-white text-lg font-bold font-outfit">Briefing Gudang</h3>
                                    <p class="text-slate-300 text-xs mt-1">Mengawasi alur keluar-masuk barang dengan teliti.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Controls -->
                        <button id="prev-slide" class="absolute left-3 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-slate-950/60 hover:bg-emerald-500/80 border border-white/10 text-white flex items-center justify-center transition-all hover:scale-110 active:scale-95">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <button id="next-slide" class="absolute right-3 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-slate-950/60 hover:bg-emerald-500/80 border border-white/10 text-white flex items-center justify-center transition-all hover:scale-110 active:scale-95">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>

                    <!-- Slide Dots Indicators -->
                    <div class="flex justify-center items-center space-x-2 mt-4">
                        <span class="slide-dot w-8 h-2.5 rounded-full bg-emerald-400 cursor-pointer" data-index="0"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="1"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="2"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="3"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="4"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="5"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="6"></span>
                        <span class="slide-dot w-3 h-2.5 rounded-full bg-white/40 cursor-pointer hover:bg-white/60" data-index="7"></span>
                    </div>

                </div>
            </div>
            
            <!-- Right Side - Hero Typography & Information -->
            <div class="lg:col-span-6 reveal-item active text-left">
                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-widest mb-6 inline-block">
                    <i class="fas fa-leaf mr-1.5"></i> Baitulmaal Munzalan Indonesia
                </span>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold font-outfit text-white tracking-tight leading-tight mb-6">
                    Departemen <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-500 bg-clip-text text-transparent typewriter-cursor">Asset & GA</span>
                </h1>
                
                <p class="text-slate-300 text-lg leading-relaxed mb-8">
                    Bagian tak terpisahkan dari manajemen Baitulmaal Munzalan Indonesia. Di balik layar, kami berdedikasi mengurus pengelolaan aset, distribusi logistik, penataan ruangan, hingga memastikan seluruh operasional berjalan lancar dan terstruktur.
                </p>
                
                <blockquote class="border-l-4 border-emerald-400 pl-4 py-2 text-slate-400 italic text-base bg-white/5 rounded-r-lg mb-8">
                    "Misi utama kami adalah merancang cara bekerja yang lebih baik, efisien, dan menyenangkan agar tim tidak capek kesana kemari."
                </blockquote>
                
                <div class="flex flex-wrap gap-4">
                    <a href="#apa-yang-kami-lakukan" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold py-4 px-8 rounded-xl transition-all flex items-center group shadow-lg hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:scale-[1.03]">
                        <span>Jelajahi Tugas Kami</span>
                        <i class="fas fa-arrow-down ml-3 group-hover:translate-y-1 transition-transform"></i>
                    </a>
                    <a href="#tim-inti" class="bg-slate-900/60 hover:bg-slate-800/80 text-white border border-white/10 hover:border-emerald-500/30 font-semibold py-4 px-8 rounded-xl transition-all flex items-center hover:scale-[1.03]">
                        <span>Kenali Tim Kami</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- Responsibilities Section -->
    <section id="apa-yang-kami-lakukan" class="py-24 px-4 relative overflow-hidden bg-slate-950/20">
        <div class="max-w-7xl mx-auto relative z-10">
            
            <div class="text-center mb-16 reveal-item">
                <span class="text-emerald-400 font-semibold tracking-wider uppercase text-xs sm:text-sm block mb-2">Apa Yang Kami Lakukan?</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold font-outfit text-white mb-4">Pilar Dukungan Operasional</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 mx-auto rounded-full mb-4"></div>
                <p class="text-slate-400 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                    Kami senantiasa berikhtiar menciptakan harmoni kerja yang efisien demi kemaslahatan bersama lewat pengelolaan logistik yang gesit dan responsif.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="glass-card rounded-2xl p-8 shadow-xl relative overflow-hidden group reveal-item">
                    <div class="absolute -right-4 -bottom-4 text-emerald-500/5 text-8xl group-hover:scale-110 duration-300 transition-transform font-bold"><i class="fas fa-boxes"></i></div>
                    <div class="w-14 h-14 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 text-2xl mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 font-outfit">Manajemen Aset</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mendata, merawat, dan memelihara seluruh inventaris berharga lembaga. Kami memastikan seluruh aset berada pada kesiapan penuh untuk menunjang program kemanusiaan.
                    </p>
                </div>
                
                <!-- Card 2 -->
                <div class="glass-card rounded-2xl p-8 shadow-xl relative overflow-hidden group reveal-item">
                    <div class="absolute -right-4 -bottom-4 text-teal-500/5 text-8xl group-hover:scale-110 duration-300 transition-transform font-bold"><i class="fas fa-truck-loading"></i></div>
                    <div class="w-14 h-14 bg-teal-500/10 border border-teal-500/20 rounded-xl flex items-center justify-center text-teal-400 text-2xl mb-6 group-hover:bg-teal-500 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 font-outfit">General Affair (GA)</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mulai dari mobilisasi logistik harian, penataan ruangan pertemuan yang representatif, hingga kebersihan fasilitas. Kami adalah penjamin kenyamanan kerja setiap divisi.
                    </p>
                </div>
                
                <!-- Card 3 -->
                <div class="glass-card rounded-2xl p-8 shadow-xl relative overflow-hidden group reveal-item">
                    <div class="absolute -right-4 -bottom-4 text-emerald-500/5 text-8xl group-hover:scale-110 duration-300 transition-transform font-bold"><i class="fas fa-bolt"></i></div>
                    <div class="w-14 h-14 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 text-2xl mb-6 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 font-outfit">Optimalisasi & Inovasi</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Kami merancang sistem dan alur kerja yang meminimalkan kerumitan fisik. Dengan proses yang ringkas, kolaborasi tim dapat tercapai tanpa menguras energi yang tidak perlu.
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- Team Section -->
    <section id="tim-inti" class="py-24 px-4 relative overflow-hidden">
        <div class="max-w-7xl mx-auto relative z-10">
            
            <div class="text-center mb-20 reveal-item">
                <span class="text-emerald-400 font-semibold tracking-wider uppercase text-xs sm:text-sm block mb-2">Skuad Operasional</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold font-outfit text-white mb-4">Tim Penggerak Kami</h2>
                <div class="w-20 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 mx-auto rounded-full mb-4"></div>
                <p class="text-slate-400 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                    Sinergi individu berdedikasi tinggi yang berkomitmen memastikan kelancaran aset fisik dan kenyamanan kerja harian.
                </p>
            </div>
            
            <!-- Leader Profile - High Fidelity Portrait Card -->
            <div class="max-w-xl mx-auto mb-20 reveal-item">
                <div class="glass-card p-8 rounded-3xl border border-emerald-500/20 hover:border-emerald-500/40 relative overflow-hidden group shadow-2xl">
                    <!-- Background subtle gradient glow -->
                    <div class="absolute -right-20 -top-20 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-8 relative z-10">
                        <div class="relative">
                            <div class="w-36 h-36 rounded-2xl overflow-hidden border-4 border-emerald-500/30 group-hover:border-emerald-400 shadow-xl duration-300 transition-all">
                                <img src="{{asset('img/chou.jpg')}}" alt="Team Leader" class="w-full h-full object-cover group-hover:scale-110 duration-500 transition-all">
                            </div>
                            <span class="absolute -bottom-3 -right-3 bg-emerald-500 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-slate-900 shadow-md" title="Pimpinan Bagian">
                                <i class="fas fa-crown text-xs"></i>
                            </span>
                        </div>
                        
                        <div class="text-center sm:text-left flex-1">
                            <span class="text-emerald-400 font-semibold uppercase text-xs tracking-widest block mb-1">Kepala Bagian Aset & GA</span>
                            <h3 class="text-2xl font-bold font-outfit text-white mb-2 tracking-tight group-hover:text-emerald-300 transition-colors">Daniswat</h3>
                            <p class="text-slate-400 text-sm italic mb-4">"Memimpin dengan visi, melayani dengan dedikasi penuh demi kesuksesan bersama."</p>
                            <div class="flex justify-center sm:justify-start space-x-3">
                                <span class="bg-white/5 border border-white/10 px-3 py-1 rounded-full text-xs text-slate-300"><i class="fas fa-award text-emerald-400 mr-1.5"></i> Nakhoda</span>
                                <span class="bg-white/5 border border-white/10 px-3 py-1 rounded-full text-xs text-slate-300"><i class="fas fa-shield-alt text-teal-400 mr-1.5"></i> Strategist</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Skuad / Members Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-8">
                <!-- Member 1 -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-lg group flex flex-col reveal-item">
                    <div class="h-48 sm:h-56 overflow-hidden relative border-b border-white/5">
                        <img src="{{asset('img/yz.jpg')}}" alt="Ryan Putra Pratama" class="w-full h-full object-cover group-hover:scale-110 duration-500 transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>
                        <span class="absolute top-3 right-3 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-400 w-8 h-8 rounded-full flex items-center justify-center text-xs" title="Juragan Snack">
                            <i class="fas fa-cookie-bite"></i>
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold font-outfit text-base text-white group-hover:text-emerald-300 transition-colors line-clamp-1">Ryan Putra Pratama</h4>
                            <p class="text-emerald-400 text-xs font-semibold tracking-wider uppercase mt-1">Kepala Unit GA</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 italic">Juragan Snack 🍿</span>
                            <span class="text-xs text-emerald-400/50"><i class="fas fa-user-shield"></i></span>
                        </div>
                    </div>
                </div>
                
                <!-- Member 2 -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-lg group flex flex-col reveal-item">
                    <div class="h-48 sm:h-56 overflow-hidden relative border-b border-white/5">
                        <img src="{{asset('img/moskov.jpg')}}" alt="Randi" class="w-full h-full object-cover group-hover:scale-110 duration-500 transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>
                        <span class="absolute top-3 right-3 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-400 w-8 h-8 rounded-full flex items-center justify-center text-xs" title="Juragan Mobil">
                            <i class="fas fa-car"></i>
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold font-outfit text-base text-white group-hover:text-emerald-300 transition-colors line-clamp-1">Randi</h4>
                            <p class="text-emerald-400 text-xs font-semibold tracking-wider uppercase mt-1">Staf Asset</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 italic">Juragan Mobil 🚗</span>
                            <span class="text-xs text-emerald-400/50"><i class="fas fa-key"></i></span>
                        </div>
                    </div>
                </div>
                
                <!-- Member 3 -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-lg group flex flex-col reveal-item">
                    <div class="h-48 sm:h-56 overflow-hidden relative border-b border-white/5">
                        <img src="{{asset('img/julian.jpg')}}" alt="Yogi Ramadhandi" class="w-full h-full object-cover group-hover:scale-110 duration-500 transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>
                        <span class="absolute top-3 right-3 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-400 w-8 h-8 rounded-full flex items-center justify-center text-xs" title="Si Penadah">
                            <i class="fas fa-box-open"></i>
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold font-outfit text-base text-white group-hover:text-emerald-300 transition-colors line-clamp-1">Yogi Ramadhandi</h4>
                            <p class="text-emerald-400 text-xs font-semibold tracking-wider uppercase mt-1">Staf Asset</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 italic">Si Penadah 📦</span>
                            <span class="text-xs text-emerald-400/50"><i class="fas fa-hand-holding-hand"></i></span>
                        </div>
                    </div>
                </div>
                
                <!-- Member 4 -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-lg group flex flex-col reveal-item">
                    <div class="h-48 sm:h-56 overflow-hidden relative border-b border-white/5">
                        <img src="{{asset('img/gord.jpg')}}" alt="Ardi" class="w-full h-full object-cover group-hover:scale-110 duration-500 transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>
                        <span class="absolute top-3 right-3 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-400 w-8 h-8 rounded-full flex items-center justify-center text-xs" title="Si Pengacau">
                            <i class="fas fa-bolt"></i>
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold font-outfit text-base text-white group-hover:text-emerald-300 transition-colors line-clamp-1">Ardi</h4>
                            <p class="text-emerald-400 text-xs font-semibold tracking-wider uppercase mt-1">Staf GA</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 italic">Si Pengacau ⚡</span>
                            <span class="text-xs text-emerald-400/50"><i class="fas fa-wind"></i></span>
                        </div>
                    </div>
                </div>
                
                <!-- Member 5 -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-lg group flex flex-col reveal-item">
                    <div class="h-48 sm:h-56 overflow-hidden relative border-b border-white/5">
                        <img src="{{asset('img/idk.jpg')}}" alt="Habi Islami" class="w-full h-full object-cover group-hover:scale-110 duration-500 transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>
                        <span class="absolute top-3 right-3 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 text-emerald-400 w-8 h-8 rounded-full flex items-center justify-center text-xs" title="IT Support - Orang Ganteng">
                            <i class="fas fa-code"></i>
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold font-outfit text-base text-white group-hover:text-emerald-300 transition-colors line-clamp-1">Habi Islami</h4>
                            <p class="text-emerald-400 text-xs font-semibold tracking-wider uppercase mt-1">IT Support</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 italic">Orang Ganteng 😎</span>
                            <span class="text-xs text-emerald-400/50"><i class="fas fa-laptop-code"></i></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Custom Footer -->
    <footer class="bg-slate-950/80 backdrop-blur-md border-t border-white/10 text-white py-12">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 items-center text-center md:text-left">
            <div>
                <a href="#" class="flex items-center justify-center md:justify-start space-x-3 mb-4">
                    <img src="{{ asset('img/logo2024.png') }}" alt="BMI Logo" class="h-10 w-auto">
                    <span class="text-white font-outfit font-bold tracking-wide">BMI PUSAT - ASSET & GA</span>
                </a>
                <p class="text-slate-400 text-sm max-w-xs leading-relaxed mx-auto md:mx-0">
                    Berdedikasi untuk kenyamanan dan efisiensi bersama. Mengatur dengan hati, menjaga kelancaran aksi operasional filantropi.
                </p>
            </div>
            <div class="flex flex-col items-center">
                <p class="text-slate-300 font-semibold mb-3 font-outfit text-sm uppercase tracking-widest">Tautan Navigasi</p>
                <div class="flex space-x-4 text-sm">
                    <a href="#headline" class="text-slate-400 hover:text-emerald-400 transition-colors">Beranda</a>
                    <span>&bull;</span>
                    <a href="#apa-yang-kami-lakukan" class="text-slate-400 hover:text-emerald-400 transition-colors">Layanan</a>
                    <span>&bull;</span>
                    <a href="#tim-inti" class="text-slate-400 hover:text-emerald-400 transition-colors">Tim</a>
                </div>
            </div>
            <div class="flex flex-col items-center md:items-end">
                <p class="text-slate-300 font-semibold mb-3 font-outfit text-sm uppercase tracking-widest">Saluran Sosial</p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-slate-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-slate-300">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all text-slate-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
                <p class="text-slate-500 text-xs mt-4">&copy; 2026 All rights reserved. BMIPusat-Aset.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth Loading Screen Fade-out
            const loader = document.getElementById('loading-screen');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 1000);
                }, 1200);
            }
            
            // Background Video & Fallback Image Sources
            const atmospheres = {
                stream: 'https://assets.mixkit.co/videos/preview/mixkit-forest-stream-in-the-sunlight-529-large.mp4',
                mist: 'https://assets.mixkit.co/videos/preview/mixkit-river-surrounded-by-forest-under-misty-sky-4548-large.mp4',
                waterfall: 'https://assets.mixkit.co/videos/preview/mixkit-waterfall-in-forest-2213-large.mp4'
            };

            const fallbackImages = {
                stream: 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=1920&q=80',
                mist: 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=1920&q=80',
                waterfall: 'https://images.unsplash.com/photo-1546182990-dffeafbe841d?w=1920&q=80'
            };
            
            // Background switcher function
            window.changeAtmosphere = function(type, button) {
                const video = document.getElementById('bg-video');
                const videoContainer = document.getElementById('video-container');
                const overlay = document.getElementById('video-fade-overlay');
                const url = atmospheres[type];
                
                if (!video || !url || !overlay) return;
                
                // Update active styles on buttons
                document.querySelectorAll('.atmosphere-btn').forEach(btn => {
                    btn.classList.remove('bg-emerald-500/20', 'border-emerald-500/30', 'text-white');
                    btn.classList.add('bg-slate-900/60', 'border-white/5', 'text-slate-300');
                    
                    // Reset custom indicator
                    const indicator = btn.querySelector('span:last-child');
                    if (indicator) {
                        indicator.className = 'w-2 h-2 rounded-full bg-transparent';
                    }
                });
                
                button.classList.add('bg-emerald-500/20', 'border-emerald-500/30', 'text-white');
                button.classList.remove('bg-slate-900/60', 'border-white/5', 'text-slate-300');
                
                const indicator = button.querySelector('span:last-child');
                if (indicator) {
                    indicator.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-ping';
                }
                
                // Activate theatrical transition
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
                
                setTimeout(() => {
                    // Update fallback image first (so if video fails to play/load, correct image is shown)
                    if (videoContainer) {
                        videoContainer.style.backgroundImage = `url('${fallbackImages[type]}')`;
                    }

                    video.src = url;
                    video.load();
                    video.play().catch(e => console.log('Autoplay blocked or load failed', e));
                    
                    // Ensure the icon changes to play state if the video successfully autoplays
                    const playPauseIcon = document.getElementById('play-pause-icon');
                    const playPauseText = document.getElementById('play-pause-text');
                    if (playPauseIcon && playPauseText) {
                        playPauseIcon.className = 'fas fa-pause';
                        playPauseText.textContent = 'Pause';
                    }

                    setTimeout(() => {
                        overlay.classList.remove('opacity-100');
                        overlay.classList.add('opacity-0');
                    }, 300);
                }, 500);
            }
            
            // Video Background Play/Pause control
            const btnPlayPause = document.getElementById('btn-play-pause');
            const playPauseIcon = document.getElementById('play-pause-icon');
            const playPauseText = document.getElementById('play-pause-text');
            const video = document.getElementById('bg-video');
            
            if (btnPlayPause && video) {
                btnPlayPause.addEventListener('click', () => {
                    if (video.paused) {
                        video.play().catch(e => console.log('Playback blocked', e));
                        if (playPauseIcon) playPauseIcon.className = 'fas fa-pause';
                        if (playPauseText) playPauseText.textContent = 'Pause';
                    } else {
                        video.pause();
                        if (playPauseIcon) playPauseIcon.className = 'fas fa-play';
                        if (playPauseText) playPauseText.textContent = 'Play';
                    }
                });
            }
            
            // Interactive Slideshow/Gallery
            const slides = document.querySelectorAll('.slide');
            const slideDots = document.querySelectorAll('.slide-dot');
            const prevButton = document.getElementById('prev-slide');
            const nextButton = document.getElementById('next-slide');
            const progress = document.getElementById('slide-progress');
            let currentSlide = 0;
            let slideInterval;
            
            function showSlide(index) {
                if (slides.length === 0) return;
                
                slides.forEach(slide => {
                    slide.classList.add('hidden', 'opacity-0');
                    slide.classList.remove('opacity-100');
                });
                
                slideDots.forEach(dot => {
                    dot.classList.remove('bg-emerald-400', 'w-8');
                    dot.classList.add('bg-white/40', 'w-3');
                });
                
                slides[index].classList.remove('hidden');
                // Trigger browser repaint
                void slides[index].offsetWidth;
                slides[index].classList.add('opacity-100');
                
                slideDots[index].classList.remove('bg-white/40', 'w-3');
                slideDots[index].classList.add('bg-emerald-400', 'w-8');
                
                // Animate progress timeline
                if (progress) {
                    progress.style.transition = 'none';
                    progress.style.width = '0%';
                    setTimeout(() => {
                        progress.style.transition = 'width 5000ms linear';
                        progress.style.width = '100%';
                    }, 50);
                }
                
                currentSlide = index;
            }
            
            function nextSlide() {
                const newIndex = (currentSlide + 1) % slides.length;
                showSlide(newIndex);
            }
            
            function prevSlide() {
                const newIndex = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
                showSlide(newIndex);
            }
            
            if (nextButton) nextButton.addEventListener('click', nextSlide);
            if (prevButton) prevButton.addEventListener('click', prevSlide);
            
            slideDots.forEach((dot, index) => {
                dot.addEventListener('click', () => showSlide(index));
            });
            
            function startSlideshow() {
                showSlide(currentSlide);
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            function stopSlideshow() {
                clearInterval(slideInterval);
            }
            
            startSlideshow();
            
            const slideshowContainer = document.getElementById('slideshow');
            if (slideshowContainer) {
                slideshowContainer.addEventListener('mouseenter', stopSlideshow);
                slideshowContainer.addEventListener('mouseleave', startSlideshow);
            }
            
            // Side Dots Navigation & Scroll Reveal
            const sideDots = document.querySelectorAll('.side-dot');
            const sectionElements = document.querySelectorAll('section');
            
            function handleScrollReveal() {
                // Reveal items when they enter the viewport
                document.querySelectorAll('.reveal-item').forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top <= window.innerHeight * 0.88) {
                        el.classList.add('active');
                    }
                });
                
                // Track current active section for navigation dots
                let currentActive = 'headline';
                sectionElements.forEach(section => {
                    const rect = section.getBoundingClientRect();
                    if (rect.top <= window.innerHeight / 3 && rect.bottom >= window.innerHeight / 3) {
                        currentActive = section.getAttribute('id');
                    }
                });
                
                sideDots.forEach(dot => {
                    if (dot.getAttribute('data-section') === currentActive) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
            
            window.addEventListener('scroll', handleScrollReveal);
            // Trigger initially
            setTimeout(handleScrollReveal, 1300);
        });
    </script>
</body>
</html>