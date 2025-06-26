<?php
require_once '../config/database.php';

$submenu_id = $_GET['submenu_id'] ?? 0;
$stmt = $pdo->prepare("SELECT id, nama_kategori FROM kategori WHERE submenu_id = ?");
$stmt->execute([$submenu_id]);
echo json_encode($stmt->fetchAll());


// Tambah kategori
if (isset($_POST['tambah'])) {
    $submenu_id = $_POST['submenu_id'];
    $nama_kategori = $_POST['nama_kategori'];

    $stmt = $pdo->prepare("INSERT INTO kategori (submenu_id, nama_kategori) VALUES (?, ?)");
    $stmt->execute([$submenu_id, $nama_kategori]);

    header("Location: ../views/admin/kategori/index.php");
    exit;
}

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ../views/admin/kategori/index.php");
    exit;
}
