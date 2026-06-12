<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BMI Pusat Asset & GA</title>
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    
    <!-- External Icon & Typography libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Game CSS -->
    <link rel="stylesheet" href="{{ asset('css/about-game.css') }}">
</head>
<body>

    <!-- Main Game Container -->
    <div id="game-container">
        
        <!-- Environment Layer -->
        <div id="office-environment" style="background-image: url('{{ asset('img/meeting-2.jpg') }}');">
            <!-- Neon Sign Title -->
            <div class="office-title-neon">Asset & General Affair</div>
            
            <!-- Characters will be injected here by JS -->
            
            <!-- Floating click instruction -->
            <div id="click-instruction">
                <i class="fas fa-hand-pointer mr-2"></i>Klik anggota tim untuk berkenalan
            </div>
        </div>

        <!-- UI Overlay Layer -->
        <div id="ui-layer">
            <!-- Back to Home -->
            <a href="{{ url('/menu-awal') }}" id="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <!-- Progress HUD -->
            <div id="progress-hud">
                <i class="fas fa-users"></i>
                <div class="progress-bar-container">
                    <div id="progress-bar-fill"></div>
                </div>
                <span id="progress-count">0/5</span>
                <button id="btn-reset-progress" class="btn-game" style="display: none; padding: 4px 12px; font-size: 11px; margin-left: 12px; align-items: center; gap: 5px;">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>

            <!-- Dialog Box -->
            <div id="dialog-box">
                <img id="dialog-avatar" src="" alt="Avatar">
                <div class="dialog-content">
                    <div id="dialog-name">Name</div>
                    <div id="dialog-role">Role</div>
                    <div id="dialog-text" class="typewriter-cursor">...</div>
                    
                    <div class="dialog-controls">
                        <button id="btn-next-dialog" class="btn-game">Next <i class="fas fa-caret-right ml-1"></i></button>
                        <button id="btn-close-dialog" class="btn-game" style="display: none;">Selesai</button>
                    </div>
                </div>
            </div>
            
            <!-- Achievement Popup -->
            <div id="achievement-popup">
                <div class="achievement-icon"><i class="fas fa-star"></i></div>
                <div>
                    <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Achievement Unlocked</div>
                    <div>Kenal Seluruh Tim!</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Intro Overlay Screen -->
    <div id="intro-overlay">
        <img src="{{ asset('img/habiislami.jpeg') }}" alt="Habi Islami" class="intro-mascot">
        <div style="font-size: 22px; font-weight: bold; color: #10b981; margin-bottom: 15px; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">Habi Islami</div>
        <div class="intro-box">
            <div id="intro-text" class="typewriter-cursor"></div>
            <div style="margin-top: 20px; display: flex; gap: 15px; justify-content: center;">
                <button id="btn-next-intro" class="btn-game">Next</button>
                <button id="btn-skip-intro" class="btn-game" style="background: transparent; border-color: rgba(255,255,255,0.3);">Skip</button>
            </div>
        </div>
    </div>

    <!-- Custom Game Logic JS -->
    <script src="{{ asset('js/about-game.js') }}"></script>

</body>
</html>