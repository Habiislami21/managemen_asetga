<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Berhasil - Peminjaman Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --text-primary: #333;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
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
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .success-container {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 500px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-icon {
            font-size: 80px;
            color: #2a9d5f;
            margin-bottom: 25px;
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-action {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 12px;
            display: block;
            text-decoration: none;
        }

        .btn-primary-custom {
            background-color: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 12px rgba(97, 0, 221, 0.2);
        }

        .btn-outline-custom {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-custom:hover {
            background-color: rgba(97, 0, 221, 0.05);
            transform: translateY(-2px);
        }

        @media (max-width: 576px) {
            .success-container {
                margin: 20px;
                padding: 40px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Pengajuan Berhasil!</h2>
        <p>
            Data peminjaman kendaraan Anda telah berhasil dikirim dan sedang menunggu approval dari Admin. 
            Mohon tunggu konfirmasi selanjutnya melalui WhatsApp.
        </p>
        <div class="actions">
            <a href="{{ route('peminjaman.create') }}" class="btn-action btn-primary-custom">
                <i class="fas fa-plus me-2"></i>Buat Pengajuan Baru
            </a>
            <a href="{{ url('/menu-awal') }}" class="btn-action btn-outline-custom">
                <i class="fas fa-home me-2"></i>Kembali ke Menu Utama
            </a>
        </div>
    </div>
</body>
</html>
