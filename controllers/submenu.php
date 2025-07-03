<?php
require_once '../config/database.php';

// Fungsi generate slug dari nama submenu
function generateSlug($text) {
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return rtrim($slug, '-');
}

// Tambah submenu
if (isset($_POST['tambah'])) {
    $menu = $_POST['nama_menu'] ?? 'beranda';
    $nama = $_POST['nama_submenu'];
    $slug = generateSlug($nama);
    $tipe = $_POST['tipe_tampilan'];
    $icon = $_POST['icon_class'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO submenu (nama_menu, nama_submenu, slug, tipe_tampilan, icon_class) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$menu, $nama, $slug, $tipe, $icon]);

    header("Location: ../views/admin/submenu/index.php");
    exit;
}

// Update submenu
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_submenu'];
    $slug = generateSlug($nama);
    $tipe = $_POST['tipe_tampilan'];
    $icon = $_POST['icon_class'] ?? null;

    $stmt = $pdo->prepare("UPDATE submenu SET nama_submenu = ?, slug = ?, tipe_tampilan = ?, icon_class = ? WHERE id = ?");
    $stmt->execute([$nama, $slug, $tipe, $icon, $id]);

    header("Location: ../views/admin/submenu/index.php");
    exit;
}

// Hapus submenu
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM submenu WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ../views/admin/submenu/index.php");
    exit;
}
