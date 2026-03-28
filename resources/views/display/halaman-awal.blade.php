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
            background-image: url('img/background-1.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
        }
        .login-container {
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

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .logo {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(2deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .primary-color {
            color: #a95199;
        }

        #typing-text {
            display: inline-block;
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

        .animated-bg {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(45deg, #a95199, #c87fb6);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: morph 8s ease-in-out infinite;
            opacity: 0.1;
            z-index: -1;
        }

        @keyframes morph {
            0% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%; }
            50% { border-radius: 30% 70% 70% 30% / 70% 70% 30% 30%; }
            75% { border-radius: 70% 30% 30% 70% / 30% 30% 70% 70%; }
            100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        }

        .typing-cursor {
            display: inline-block;
            width: 2px;
            height: 1em;
            background-color: #a95199;
            margin-left: 2px;
            animation: blink 1s infinite;
            vertical-align: middle;
            position: relative;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .feature-icon {
            transition: all 0.3s ease;
        }

        .login-container:hover .feature-icon {
            transform: scale(1.1);
        }

        @media (max-width: 360px) {
        .feature-icon {
            padding: 0.5rem !important;
            }
        .feature-icon i {
            font-size: 0.8rem;
            }
        }
        .typing-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="login-container w-11/12 max-w-md p-8 md:p-10">
        <div class="animated-bg top-0 left-0"></div>
        <div class="animated-bg bottom-0 right-0"></div>
        
        <div class="flex flex-col items-center justify-center relative z-10">
            <img src="img/logo2024.png" alt="BMI Logo" class="logo w-32 mb-6">
            
            <h2 class="primary-color text-2xl font-bold mb-2 flex justify-center items-center">
                <div class="typing-container flex items-center justify-center">
                    <span id="typing-text"></span><span class="typing-cursor"></span>
                </div>
            </h2>
            
            <p class="text-gray-600 mb-6 text-center">Asset & GA Management System</p>
            
            <div class="grid grid-cols-3 gap-2 sm:gap-4 w-full mb-8">
                <div class="flex flex-col items-center text-center">
                    <div class="feature-icon bg-purple-100 rounded-full p-3 mb-2">
                        <i class="fas fa-tasks text-purple-600"></i>
                    </div>
                    <span class="text-xs text-gray-600">Asset Tracking</span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="feature-icon bg-purple-100 rounded-full p-3 mb-2">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <span class="text-xs text-gray-600">GA Analytics</span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="feature-icon bg-purple-100 rounded-full p-3 mb-2">
                        <i class="fas fa-sync text-purple-600"></i>
                    </div>
                    <span class="text-xs text-gray-600 whitespace-nowrap">Updates</span>
                </div>
            </div>
            
            <a href="/menu-awal" class="login-btn w-full py-3 px-6 rounded-lg text-white font-medium text-center transition-all flex items-center justify-center gap-2">
                <span>Yuk Masuk</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            
            <div class="mt-6 text-xs text-gray-500 text-center">
                <p id="current-date"></p>
            </div>
        </div>
    </div>

    <script>
        const welcomeMessages = [
            "Assalamualaikum Wr. Wb.",
            "Selamat Datang",
            "BMIPusat-Aset Online"
        ];
        
        const textElement = document.getElementById('typing-text');
        let messageIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let typingSpeed = 100;
        
        function typeEffect() {
            const currentMessage = welcomeMessages[messageIndex];
            
            if (isDeleting) {
                textElement.textContent = currentMessage.substring(0, charIndex - 1);
                charIndex--;
                typingSpeed = 50;
            } else {
                textElement.textContent = currentMessage.substring(0, charIndex + 1);
                charIndex++;
                typingSpeed = 100;
            }

            const cursor = document.querySelector('.typing-cursor');
            if (cursor) {
                cursor.style.display = 'inline-block';
            }
            
            if (!isDeleting && charIndex === currentMessage.length) {
                isDeleting = true;
                typingSpeed = 2000;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                messageIndex = (messageIndex + 1) % welcomeMessages.length;
                typingSpeed = 500;
            }
                       
            setTimeout(typeEffect, typingSpeed);
        }
        typeEffect();
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
        // Parallax mousemove effect dinonaktifkan untuk menghemat CPU browser.
    </script>
</body>
</html>