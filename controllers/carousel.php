<?php
require_once __DIR__ . '/../config/database.php';

function getAllCarousel() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM carousel ORDER BY urutan ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCarouselById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM carousel WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function tambahCarousel($gambar, $urutan) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO carousel (gambar, urutan) VALUES (?, ?)");
    return $stmt->execute([$gambar, $urutan]);
}

function updateCarousel($id, $gambar, $urutan) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE carousel SET gambar = ?, urutan = ? WHERE id = ?");
    return $stmt->execute([$gambar, $urutan, $id]);
}

function hapusCarousel($id) {
    global $pdo;

    // Ambil data carousel yang akan dihapus
    $stmt = $pdo->prepare("SELECT urutan FROM carousel WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) return false;

    $urutanDihapus = $data['urutan'];

    // Hapus data carousel
    $stmt = $pdo->prepare("DELETE FROM carousel WHERE id = ?");
    $stmt->execute([$id]);

    // Update urutan: semua urutan yang lebih besar dari yang dihapus dikurangi 1
    $stmt = $pdo->prepare("UPDATE carousel SET urutan = urutan - 1 WHERE urutan > ?");
    $stmt->execute([$urutanDihapus]);

    return true;
}

