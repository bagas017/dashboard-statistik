<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
        .sidebar .active {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar border-end">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="../public/beranda.php"><i class="bi bi-house-door"></i> Beranda</a>
    <hr>
    <strong class="d-block px-3">Beranda</strong>
    <a href="submenu/index.php"><i class="bi bi-list-task"></i> Submenu</a>
    <a href="kategori/index.php"><i class="bi bi-tags"></i> Kategori</a>
    <a href="statistik/index.php"><i class="bi bi-bar-chart-line"></i> Statistik</a>
    <hr>
    <a href="galeri/index.php"><i class="bi bi-images"></i> Galeri</a>
    <a href="agenda/index.php"><i class="bi bi-calendar-event"></i> Agenda</a>
    <a href="berita/index.php"><i class="bi bi-newspaper"></i> Berita</a>
    <hr>
    <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Selamat Datang di Dashboard Admin</h2>
    <p>Gunakan menu di sebelah kiri untuk mengelola konten pada halaman publik.</p>
</div>

</body>
</html>
