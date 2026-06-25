<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Berhasil Dibuat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6100dd;
            --primary-hover: #7c18fa;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: linear-gradient(rgba(97, 0, 221, 0.05), rgba(97, 0, 221, 0.1)), url('{{ asset('img/background-2.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: #fff;
            padding: 48px 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 520px;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(97, 0, 221, 0.1);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 24px;
        }

        h2 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 12px;
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: #fff;
            padding: 14px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 24px;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            background: var(--primary-hover);
            color: #fff;
            transform: translateY(-2px);
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 16px;
            margin-top: 20px;
            text-align: left;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h2>Form Berhasil Dibuat</h2>
        <p class="text-muted mb-0">Dokumen Word peminjaman barang telah siap diunduh.</p>

        <div class="info-box">
            <div><strong>Nomor Surat:</strong> {{ $peminjaman->nomor_surat }}</div>
            <div><strong>Peminjam:</strong> {{ $peminjaman->nama_peminjam }}</div>
            <div><strong>Kegiatan:</strong> {{ $peminjaman->nama_kegiatan }}</div>
        </div>

        <a href="{{ route('peminjaman-event.download', $peminjaman) }}" class="btn-download">
            <i class="fas fa-download me-2"></i>Unduh Form Word (.docx)
        </a>

        <div class="mt-4">
            <a href="{{ route('peminjaman-event.create') }}" class="text-decoration-none me-3" style="color: var(--primary);">
                Buat Form Baru
            </a>
            <a href="{{ url('/menu-awal') }}" class="text-decoration-none" style="color: var(--primary);">
                Kembali ke Menu
            </a>
        </div>
    </div>
</body>
</html>
