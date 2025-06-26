<?php
require_once '../config/database.php';

// Tambah submenu
if (isset($_POST['tambah'])) {
    $menu = $_POST['nama_menu'];
    $nama = $_POST['nama_submenu'];
    function generateSlug($text) {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return rtrim($slug, '-');
    }
    
    $slug = generateSlug($nama);
    
    $tipe = $_POST['tipe_tampilan'];

    $stmt = $pdo->prepare("INSERT INTO submenu (nama_menu, nama_submenu, slug, tipe_tampilan) VALUES (?, ?, ?, ?)");
    $stmt->execute([$menu, $nama, $slug, $tipe]);

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
