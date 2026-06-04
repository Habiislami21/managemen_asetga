<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-image: url('img/background-2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            position: relative;
        }
        .back-to-menu {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(106, 13, 173, 0.15);
            color: #6a0dad;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(106, 13, 173, 0.08);
            z-index: 1000;
        }
        .back-to-menu:hover {
            background-color: #6a0dad;
            color: #fff;
            transform: translateX(-3px);
            box-shadow: 0 6px 20px rgba(106, 13, 173, 0.25);
        }
        .back-to-menu i {
            transition: transform 0.3s ease;
        }
        .back-to-menu:hover i {
            transform: translateX(-2px);
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <a href="{{ url('/menu-awal') }}" class="back-to-menu">
        <i class="fas fa-arrow-left"></i>
        <span>Menu Awal</span>
    </a>
    <div class="card p-4 shadow-sm" style="width: 350px;">
        <h3 class="text-center mb-3">Login</h3>
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Notifikasi Error dari Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect" value="/admin/dashboard">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
