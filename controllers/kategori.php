<?php
require_once '../config/database.php';

// Jika ada request AJAX kategori by submenu
if (isset($_GET['submenu_id'])) {
    $submenu_id = $_GET['submenu_id'];
    $stmt = $pdo->prepare("SELECT id, nama_kategori FROM kategori WHERE submenu_id = ?");
    $stmt->execute([$submenu_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// Tambah kategori
if (isset($_POST['tambah'])) {
    $submenu_id = $_POST['submenu_id'];
    $nama_kategori = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'] ?? null;
    $gambar = $_POST['gambar'] ?? null;

    // Validasi panjang deskripsi (maks 25 kata)
    $kata = str_word_count(strip_tags($deskripsi));
    if ($kata > 25) {
        $deskripsi = implode(' ', array_slice(explode(' ', $deskripsi), 0, 35));
    }

    $stmt = $pdo->prepare("INSERT INTO kategori (submenu_id, nama_kategori, deskripsi, gambar) VALUES (?, ?, ?, ?)");
    $stmt->execute([$submenu_id, $nama_kategori, $deskripsi, $gambar]);

    header("Location: ../views/admin/kategori/index.php");
    exit;
}

// Update kategori
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $submenu_id = $_POST['submenu_id'];
    $nama_kategori = $_POST['nama_kategori'];
    $deskripsi = $_POST['deskripsi'] ?? null;
    $gambar = $_POST['gambar'] ?? null;

    // Validasi panjang deskripsi (maks 25 kata)
    $kata = str_word_count(strip_tags($deskripsi));
    if ($kata > 25) {
        $deskripsi = implode(' ', array_slice(explode(' ', $deskripsi), 0, 35));
    }

    $stmt = $pdo->prepare("UPDATE kategori SET submenu_id = ?, nama_kategori = ?, deskripsi = ?, gambar = ? WHERE id = ?");
    $stmt->execute([$submenu_id, $nama_kategori, $deskripsi, $gambar, $id]);

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
