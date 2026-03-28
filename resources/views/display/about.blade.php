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
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Enhanced Parallax Effect */
        .parallax-section {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .parallax-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120%;
            background-size: cover;
            background-position: center;
            transition: all 0.5s ease;
            z-index: -1;
        }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Typing Animation */
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes blink {
            50% { border-color: transparent; }
        }
        
        .typewriter {
            border-right: 3px solid white;
            overflow: hidden;
            white-space: nowrap;
            animation: 
                typing 4s steps(40, end),
                blink 0.5s step-end infinite alternate;
        }
        
        /* Pulse Effect */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        /* Hover Effect Enhancement */
        .team-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .team-card:hover::before {
            left: 100%;
        }
        
        .team-card:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        /* Navigation Dots Enhancement */
        .nav-dots {
            position: fixed;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 100;
        }
        
        .nav-dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            margin: 1rem 0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-dot::after {
            content: '';
            position: absolute;
            left: -10px;
            top: -10px;
            width: 35px;
            height: 35px;
            border: 2px solid transparent;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .nav-dot.active {
            background-color: #3b82f6;
            transform: scale(1.5);
        }
        
        .nav-dot.active::after {
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        /* Enhanced Gallery */
        .gallery-container {
            position: relative;
            perspective: 1000px;
        }
        
        .slide {
            transform-style: preserve-3d;
            transition: all 0.8s ease;
        }
        
        .slide.active {
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.8) rotateY(30deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotateY(0deg);
            }
        }
        
        /* Progress Bar Animation */
        .progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background-color: #3b82f6;
            animation: progress 5s linear infinite;
        }
        
        @keyframes progress {
            from { width: 0%; }
            to { width: 100%; }
        }
        
        /* Section Title Enhancement */
        .section-title {
            position: relative;
            display: inline-block;
            overflow: hidden;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            animation: expandWidth 2s ease-out forwards;
        }
        
        @keyframes expandWidth {
            to { width: 100%; }
        }
        
        /* Scroll Reveal Animation */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s ease;
        }
        
        .scroll-reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Loading Screen */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .team-container {
                flex-direction: column;
            }
            .parallax-section {
                min-height: auto;
                padding: 2rem 0;
            }
            .nav-dots {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader"></div>
        <p class="text-white mt-4">Loading...</p>
    </div>
    
    <!-- Navigation Dots -->
    <div class="nav-dots">
        <div class="nav-dot active" data-section="headline" title="Home"></div>
        <div class="nav-dot" data-section="tim-inti" title="Tim Inti"></div>
    </div>
    
    <!-- Headline Section -->
    <section id="headline" class="parallax-section">
        <div id="headline-background" class="parallax-bg"></div>
        <div class="content-container">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-7xl mx-auto">
                <!-- Left Side - Enhanced Gallery -->
                <div class="flex items-center justify-center float-animation">
                    <div class="relative w-full max-w-2xl">
                        <!-- Gallery Background with Enhanced Blur -->
                        <div id="gallery-blur-bg" class="absolute inset-0 bg-cover bg-center rounded-lg" 
                             style="filter: blur(12px); 
                                    opacity: 0.5;
                                    transform: scale(1.1);
                                    transition: all 1s ease-in-out;"></div>
                        
                        <!-- Slideshow Container -->
                        <div class="relative p-8 gallery-container">
                            <div id="slideshow" class="relative aspect-video overflow-hidden rounded-lg shadow-2xl">
                                <div class="progress-bar"></div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-3.jpeg')}}" alt="Asset Management System" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Bounding</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-2.jpeg')}}" alt="Team Working" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Briefing</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-4.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Paskas Day</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-5.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Di Masjid</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-6.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">After Briefing</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-7.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Ruang Finance</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-8.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Ruang Finacne 2</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                <div class="slide hidden relative w-full h-full">
                                    <img src="{{asset('img/galeri-9.jpeg')}}" alt="Modern Equipment" class="w-full h-full object-cover">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-transparent to-transparent p-4">
                                        <h3 class="text-white text-lg font-semibold">Briefing Gudang</h3>
                                        <p class="text-gray-300 text-sm">...</p>
                                    </div>
                                </div>
                                
                                <!-- Enhanced Navigation Arrows -->
                                <button id="prev-slide" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all hover:scale-110">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="next-slide" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all hover:scale-110">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Enhanced Slideshow Indicators -->
                            <div class="flex justify-center mt-4 space-x-3">
                                <span class="slide-dot active w-4 h-4 rounded-full bg-blue-500 cursor-pointer transition-all hover:bg-blue-400" data-index="0"></span>
                                <span class="slide-dot w-4 h-4 rounded-full bg-white bg-opacity-50 cursor-pointer transition-all hover:bg-opacity-75" data-index="1"></span>
                                <span class="slide-dot w-4 h-4 rounded-full bg-white bg-opacity-50 cursor-pointer transition-all hover:bg-opacity-75" data-index="2"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side - Enhanced Text Content -->
                <div class="flex items-center justify-center scroll-reveal">
                    <div class="bg-gradient-to-br from-black to-gray-900 bg-opacity-80 p-10 rounded-2xl backdrop-blur-sm w-full shadow-2xl">
                        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 text-left leading-tight">
                            <span class="text-blue-400 typewriter">Asset</span> dan <span class="text-blue-400">GA</span>
                        </h1>
                        {{-- <p class="text-xl md:text-2xl text-white mb-8 text-left opacity-90">Misi kami adalah merancang cara bekerja yang lebih baik</p> --}}
                        <p class="text-white mb-10 text-left text-lg opacity-80 leading-relaxed">Salah satu bagian dari tim management Baitulmaal Munzalan Indonesia yang tugasnya yah ngurusin aset, ngangkut barang, beres-beres, nyiapin ruangan, dll. Misi Kami adalah merancang cara bekerja yang lebih baik agar tidak capek kesana kemari</p>
                        <div class="flex justify-start">
                            <a href="#tim-inti" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-8 rounded-lg transition-all flex items-center group shadow-lg hover:shadow-xl hover:scale-105">
                                <span class="mr-3">Jelajahi Tim Kami</span>
                                <i class="fas fa-chevron-down group-hover:animate-bounce"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Tim Inti Section - Enhanced -->
    <section id="tim-inti" class="parallax-section bg-gradient-to-b from-gray-900 to-black">
        <div class="parallax-bg opacity-10"></div>
        <div class="content-container">
            <div class="max-w-7xl mx-auto text-white">
                <br>
                <h2 class="text-5xl font-bold mb-12 section-title text-center scroll-reveal">Tim Inti</h2>
                
                <!-- Leader - Enhanced -->
                <div class="mb-16 text-center scroll-reveal">
                    <div class="relative mx-auto w-40 h-40 group">
                        <div class="w-full h-full overflow-hidden rounded-full border-4 border-blue-500 shadow-2xl transition-all group-hover:border-blue-400 group-hover:shadow-blue-500/20">
                            <img src="{{asset('img/chou.jpg')}}" alt="Team Leader" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                        </div>
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full opacity-20 group-hover:opacity-40 transition-opacity"></div>
                    </div>
                    <h3 class="text-2xl font-bold mb-2 mt-6">Daniswat</h3>
                    <p class="text-blue-400 mb-3 text-lg">Kepala Bagian Aset & GA</p>
                    <p class="text-gray-300 text-base italic">"Memimpin dengan visi dan dedikasi"</p>
                </div>
                
                <!-- Team Members - Enhanced Grid -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <!-- Member 1 -->
                    <div class="team-card bg-gradient-to-b from-gray-800 to-gray-900 rounded-xl overflow-hidden text-center shadow-lg scroll-reveal">
                        <div class="h-40 overflow-hidden relative">
                            <img src="{{asset('img/yz.jpg')}}" alt="Team Member 1" class="w-full h-full object-cover transition-transform hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-base mb-1">Ryan Putra Pratama</h4>
                            <p class="text-blue-400 text-sm mb-1">Kepala Unit GA</p>
                            <p class="text-gray-400 text-xs">Juragan Snack</p>
                        </div>
                    </div>
                    
                    <!-- Member 2 -->
                    <div class="team-card bg-gradient-to-b from-gray-800 to-gray-900 rounded-xl overflow-hidden text-center shadow-lg scroll-reveal">
                        <div class="h-40 overflow-hidden relative">
                            <img src="{{asset('img/moskov.jpg')}}" alt="Team Member 2" class="w-full h-full object-cover transition-transform hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-base mb-1">Randi</h4>
                            <p class="text-blue-400 text-sm mb-1">Staf Asset</p>
                            <p class="text-gray-400 text-xs">Juragan Mobil</p>
                        </div>
                    </div>
                    
                    <!-- Member 3 -->
                    <div class="team-card bg-gradient-to-b from-gray-800 to-gray-900 rounded-xl overflow-hidden text-center shadow-lg scroll-reveal">
                        <div class="h-40 overflow-hidden relative">
                            <img src="{{asset('img/julian.jpg')}}" alt="Team Member 3" class="w-full h-full object-cover transition-transform hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-base mb-1">Yogi Ramadhandi</h4>
                            <p class="text-blue-400 text-sm mb-1">Staf Asset</p>
                            <p class="text-gray-400 text-xs">Si Penadah</p>
                        </div>
                    </div>
                    
                    <!-- Member 4 -->
                    <div class="team-card bg-gradient-to-b from-gray-800 to-gray-900 rounded-xl overflow-hidden text-center shadow-lg scroll-reveal">
                        <div class="h-40 overflow-hidden relative">
                            <img src="{{asset('img/gord.jpg')}}" alt="Team Member 4" class="w-full h-full object-cover transition-transform hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-base mb-1">Ardi</h4>
                            <p class="text-blue-400 text-sm mb-1">Staf GA</p>
                            <p class="text-gray-400 text-xs">Si Pengacau</p>
                        </div>
                    </div>
                    
                    <!-- Member 5 -->
                    <div class="team-card bg-gradient-to-b from-gray-800 to-gray-900 rounded-xl overflow-hidden text-center shadow-lg scroll-reveal">
                        <div class="h-40 overflow-hidden relative">
                            <img src="{{asset('img/idk.jpg')}}" alt="Team Member 5" class="w-full h-full object-cover transition-transform hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-base mb-1">Habi Islami</h4>
                            <p class="text-blue-400 text-sm mb-1">IT Support</p>
                            <p class="text-gray-400 text-xs">Orang Ganteng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Enhanced Footer -->
    <footer class="bg-black text-white text-center py-8 border-t border-gray-800">
        <div class="max-w-4xl mx-auto px-4">
            <p class="text-lg font-semibold mb-2">BMIPusat-Aset Online</p>
            <p class="text-gray-400 mb-4">&copy; 2025 All rights reserved.</p>
            <div class="flex justify-center space-x-6">
                <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                    <i class="fab fa-instagram text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                    <i class="fab fa-linkedin text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-blue-400 transition-colors">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
            </div>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen after page loads
            setTimeout(() => {
                document.getElementById('loading-screen').style.display = 'none';
            }, 1500);
            
            // Background images array
            const backgroundImages = [
                'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920',
                'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=1920',
                'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=1920'
            ];
            
            // Initialize backgrounds
            const headlineBackground = document.getElementById('headline-background');
            const galleryBlurBg = document.getElementById('gallery-blur-bg');
            
            // Set initial background
            headlineBackground.style.backgroundImage = `url('${backgroundImages[0]}')`;
            galleryBlurBg.style.backgroundImage = `url('${backgroundImages[0]}')`;
            
            // Enhanced parallax effect
            let ticking = false;
            
            function updateParallax() {
                const parallaxBgs = document.querySelectorAll('.parallax-bg');
                const scrollPosition = window.pageYOffset;
                
                parallaxBgs.forEach((bg, index) => {
                    const speed = index === 0 ? 0.5 : 0.3;
                    bg.style.transform = `translateY(${scrollPosition * speed}px)`;
                });
                
                ticking = false;
            }
            
            function requestParallaxUpdate() {
                if (!ticking) {
                    requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestParallaxUpdate);
            
            // Enhanced navigation dots functionality
            const sections = ['headline', 'tim-inti'];
            const navDots = document.querySelectorAll('.nav-dot');
            
            function updateActiveDot() {
                sections.forEach((sectionId, index) => {
                    const section = document.getElementById(sectionId);
                    if (section) {
                        const rect = section.getBoundingClientRect();
                        
                        if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
                            navDots.forEach(dot => dot.classList.remove('active'));
                            if (navDots[index]) {
                                navDots[index].classList.add('active');
                            }
                        }
                    }
                });
            }
            
            window.addEventListener('scroll', updateActiveDot);
            
            // Smooth scroll for navigation dots
            navDots.forEach(dot => {
                dot.addEventListener('click', function() {
                    const sectionId = this.getAttribute('data-section');
                    const section = document.getElementById(sectionId);
                    
                    if (section) {
                        window.scrollTo({
                            top: section.offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Enhanced slideshow functionality
            const slides = document.querySelectorAll('.slide');
            const slideDots = document.querySelectorAll('.slide-dot');
            const prevButton = document.getElementById('prev-slide');
            const nextButton = document.getElementById('next-slide');
            let currentSlide = 0;
            let slideInterval;
            
            // Show initial slide
            if (slides.length > 0) {
                slides[0].classList.remove('hidden');
                slides[0].classList.add('active');
            }
            
            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => {
                    slide.classList.add('hidden');
                    slide.classList.remove('active');
                });
                
                // Reset all dots
                slideDots.forEach(dot => {
                    dot.classList.remove('active', 'bg-blue-500');
                    dot.classList.add('bg-white', 'bg-opacity-50');
                });
                
                // Show current slide
                if (slides[index]) {
                    slides[index].classList.remove('hidden');
                    slides[index].classList.add('active');
                }
                
                // Update dot
                if (slideDots[index]) {
                    slideDots[index].classList.add('active', 'bg-blue-500');
                    slideDots[index].classList.remove('bg-white', 'bg-opacity-50');
                }
                
                // Update backgrounds
                if (backgroundImages[index]) {
                    headlineBackground.style.backgroundImage = `url('${backgroundImages[index]}')`;
                    galleryBlurBg.style.backgroundImage = `url('${backgroundImages[index]}')`;
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
            
            // Event listeners for slideshow
            if (nextButton) nextButton.addEventListener('click', nextSlide);
            if (prevButton) prevButton.addEventListener('click', prevSlide);
            
            slideDots.forEach((dot, index) => {
                dot.addEventListener('click', () => showSlide(index));
            });
            
            // Auto-advance slideshow
            function startSlideshow() {
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            function stopSlideshow() {
                clearInterval(slideInterval);
            }
            
            startSlideshow();
            
            // Pause slideshow on hover
            const slideshow = document.getElementById('slideshow');
            if (slideshow) {
                slideshow.addEventListener('mouseenter', stopSlideshow);
                slideshow.addEventListener('mouseleave', startSlideshow);
            }
            
            // Scroll reveal animation
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.scroll-reveal').forEach(el => {
                observer.observe(el);
            });
            
            // Smooth reveal for team cards
            const teamCards = document.querySelectorAll('.team-card');
            teamCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>