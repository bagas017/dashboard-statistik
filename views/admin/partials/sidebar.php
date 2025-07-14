<?php
require_once __DIR__ . '../../../../config/url.php';
?>

<div class="sidebar bg-light border-end d-flex flex-column" style="width: 250px; min-height: 100vh;">
    <div class="p-3 mb-4 bg-white border-bottom">
        <h4 class="m-0 text-center text-primary">
            <i class="bi bi-shield-lock me-2"></i>
            Admin Panel
        </h4>
    </div>
    
    <div class="p-3 flex-grow-1">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/dashboard.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-speedometer2 me-2 text-primary"></i>
                    Dashboard
                </a>
            </li>
            
            <!-- Carousel -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/carousel/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-images me-2 text-primary"></i>
                    Carousel
                </a>
            </li>
            
            <li class="my-3 border-top"></li>
            
            <!-- Beranda Section -->
            <li class="px-3 mb-2 small text-uppercase text-muted fw-bold">Beranda</li>
            
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/submenu/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-list-check me-2 text-primary"></i>
                    Submenu
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/kategori/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-tags me-2 text-primary"></i>
                    Kategori
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/statistik/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-graph-up me-2 text-primary"></i>
                    Statistik
                </a>
            </li>
            
            <li class="my-3 border-top"></li>
            
            <!-- Content Section -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/galeri/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-collection me-2 text-primary"></i>
                    Galeri
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/agenda/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-calendar-date me-2 text-primary"></i>
                    Agenda
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/views/admin/berita/index.php" class="nav-link py-2 px-3 rounded mb-1">
                    <i class="bi bi-newspaper me-2 text-primary"></i>
                    Berita
                </a>
            </li>
        </ul>
    </div>
    
    <div class="p-3 border-top">
        <a href="<?= BASE_URL ?>/logout.php" class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
            <i class="bi bi-box-arrow-right me-2"></i>
            Keluar
        </a>
    </div>
</div>

<style>
    .sidebar {
        background-color: #f8f9fa;
        box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
    }
    
    .nav-link {
        color: #495057;
        transition: all 0.2s;
    }
    
    .nav-link:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    
    .nav-link.active {
        background-color: #0d6efd;
        color: white !important;
    }
    
    .nav-link.active i {
        color: white !important;
    }
    
    .nav-link i {
        width: 20px;
        text-align: center;
    }
    
    /* Tombol Logout Merah */
    .btn-danger {
        background-color: #dc3545;
        color: white !important;
        border-color: #dc3545;
        padding: 8px 12px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-danger:hover {
        background-color: #bb2d3b;
        border-color: #b02a37;
        transform: translateY(-1px);
    }
    
    .btn-danger:active {
        transform: translateY(0);
    }
</style>