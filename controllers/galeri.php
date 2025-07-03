<?php
require_once __DIR__ . '/../config/database.php';

// Ambil semua data galeri
function getAllGaleri() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_upload DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil satu galeri berdasarkan ID
function getGaleriById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update galeri
function updateGaleri($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE galeri SET judul = ?, deskripsi = ?, jenis = ?, file_path = ? WHERE id = ?");
    return $stmt->execute([
        $data['judul'],
        $data['deskripsi'],
        $data['jenis'],
        $data['file_path'],
        $id
    ]);
}

// Hapus galeri
function hapusGaleri($id) {
    global $pdo;

    // Ambil dulu path file (jika bukan YouTube)
    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE id = ?");
    $stmt->execute([$id]);
    $galeri = $stmt->fetch();

    if ($galeri) {
        $path = $galeri['file_path'];
        if ($galeri['jenis'] === 'foto' && strpos($path, 'http') !== 0) {
            @unlink(__DIR__ . "/../uploads/foto/$path");
        } elseif ($galeri['jenis'] === 'video' && strpos($path, 'http') !== 0) {
            @unlink(__DIR__ . "/../uploads/video/$path");
        }
    }

    $stmt = $pdo->prepare("DELETE FROM galeri WHERE id = ?");
    return $stmt->execute([$id]);
}

////////////////////////////
// Logika hapus langsung //
////////////////////////////
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if (hapusGaleri($id)) {
        header("Location: ../views/admin/galeri/index.php?deleted=1");
        exit;
    } else {
        header("Location: ../views/admin/galeri/index.php?error=1");
        exit;
    }
}
