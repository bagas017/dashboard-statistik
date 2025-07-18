<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --beranda-color: #4361ee;
            --beranda-hover: #3a56d4;
            --agenda-color: #4cc9f0;
            --agenda-hover: #3ab0d4;
            --berita-color: #f72585;
            --berita-hover: #d41a6f;
            --galeri-color: #7209b7;
            --galeri-hover: #5d0795;
            --text-dark: #2b2d42;
            --text-medium: #4a4e69;
            --text-light: #8e9aaf;
            --background-light: #f8f9fa;
        }
        
        body {
            font-family: "Inter", sans-serif;
            background-color: var(--background-light);
        }
        
        .dashboard-header {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 0.8rem 0.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            text-decoration: none !important;
        }
        
        .logo-svg {
            width: 40px;
            height: 40px;
            margin-right: 12px;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }
        
        .logo-subtext {
            font-size: 0.85rem;
            color: var(--text-medium);
            margin: 0;
            font-weight: 400;
        }
        
        .nav-btn {
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }
        
        /* Warna khusus untuk setiap tombol */
        .btn-beranda {
            background-color: var(--beranda-color);
        }
        .btn-beranda:hover {
            background-color: var(--beranda-hover);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
        }
        
        .btn-agenda {
            background-color: var(--agenda-color);
        }
        .btn-agenda:hover {
            background-color: var(--agenda-hover);
            box-shadow: 0 4px 8px rgba(76, 201, 240, 0.2);
        }
        
        .btn-berita {
            background-color: var(--berita-color);
        }
        .btn-berita:hover {
            background-color: var(--berita-hover);
            box-shadow: 0 4px 8px rgba(247, 37, 133, 0.2);
        }
        
        .btn-galeri {
            background-color: var(--galeri-color);
        }
        .btn-galeri:hover {
            background-color: var(--galeri-hover);
            box-shadow: 0 4px 8px rgba(114, 9, 183, 0.2);
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .nav-btn i {
            margin-right: 8px;
            font-size: 1rem;
        }
        
        .current-date {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 400;
        }
        
        @media (max-width: 992px) {
            .datetime-container {
                display: none;
            }
            
            .nav-btn {
                min-width: auto;
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
            
            .nav-btn i {
                margin-right: 5px;
            }
            
            .logo-text {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="dashboard-header">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <a href="#" class="logo-container text-decoration-none">
                <!-- Logo sebagai img -->
                <img src="../../assets/img/logo.svg" alt="Logo BAPPEDA" class="logo-svg">
                <div>
                    <h1 class="logo-text">BAPPEDA PROV LAMPUNG</h1>
                    <p class="logo-subtext">UPTD Pusat Data dan Informasi Pembangunan Daerah Provinsi Lampung</p>
                </div>
            </a>
            
            <div class="d-flex align-items-center">
                <div class="nav me-3">
                    <a href="beranda.php" class="nav-btn btn-beranda me-2">
                        <i class="bi bi-house-door"></i> Beranda
                    </a>
                    <a href="agenda.php" class="nav-btn btn-agenda me-2">
                        <i class="bi bi-calendar-event"></i> Agenda
                    </a>
                    <a href="berita.php" class="nav-btn btn-berita me-2">
                        <i class="bi bi-newspaper"></i> Berita
                    </a>
                    <a href="galeri.php" class="nav-btn btn-galeri">
                        <i class="bi bi-images"></i> Galeri
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>