<?php
require_once __DIR__ . '../../../../config/url.php';
?>

<div class="sidebar border-end">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="<?= BASE_URL ?>/views/admin/dashboard.php"><i class="bi bi-house-door"></i> Dashboard</a>
    <hr>
    <strong class="d-block px-3">Beranda</strong>
    <a href="<?= BASE_URL ?>/views/admin/submenu/index.php"><i class="bi bi-list-task"></i> Submenu</a>
    <a href="<?= BASE_URL ?>/views/admin/kategori/index.php"><i class="bi bi-tags"></i> Kategori</a>
    <a href="<?= BASE_URL ?>/views/admin/statistik/index.php"><i class="bi bi-bar-chart-line"></i> Statistik</a>
    <hr>
    <a href="<?= BASE_URL ?>/views/admin/galeri/index.php"><i class="bi bi-images"></i> Galeri</a>
    <a href="<?= BASE_URL ?>/views/admin/agenda/index.php"><i class="bi bi-calendar-event"></i> Agenda</a>
    <a href="<?= BASE_URL ?>/views/admin/berita/index.php"><i class="bi bi-newspaper"></i> Berita</a>
    <hr>
    <a href="<?= BASE_URL ?>/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
</div>
