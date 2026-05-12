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


        .primary-color {
            color: #a95199;
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
<body class="flex items-center justify-center">
    <div class="login-container w-11/12 max-w-md p-8 md:p-10">
        
        <div class="flex flex-col items-center justify-center relative z-10">
            <img src="img/logo2024.png" alt="BMI Logo" class="logo w-32 mb-6">
            
            <h2 class="primary-color text-2xl font-bold mb-2 flex flex-col justify-center items-center text-center">
                <!-- <span>Selamat Datang</span> -->
                <span>Sistem BMI</span>
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
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
    </script>
</body>
</html>