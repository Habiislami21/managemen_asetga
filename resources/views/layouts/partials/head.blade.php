<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BMI-Aset & GA</title>
    <link rel="shortcut icon" href="{{ asset('img/logo2024.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/compiled/css/app.css">
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="{{ asset('dist') }}/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    @stack('css')

    <style>
        .sidebar-item.has-sub .submenu {
            display: none; /* Default submenu disembunyikan */
            padding-left: 20px;
        }

        .sidebar-item.has-sub:hover .submenu {
            display: block; /* Tampilkan submenu saat di-hover */
        }

        .submenu-item a {
            padding: 8px 15px;
            font-size: 14px;
            display: block;
            color: #6c757d; /* Warna teks submenu */
            transition: all 0.3s;
        }

        .submenu-item a:hover {
            color: #0d6efd; /* Warna teks saat di-hover */
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        </style>
    
</head>
