<?php
require_once __DIR__ . '/../config/database.php';

function getAllBerita() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBeritaById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function tambahBerita($judul, $isi, $gambar) {
    global $pdo;
    $tanggal = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT INTO berita (judul, gambar, isi, tanggal) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$judul, $gambar, $isi, $tanggal]);
}

function updateBerita($id, $judul, $isi, $gambar) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE berita SET judul = ?, isi = ?, gambar = ? WHERE id = ?");
    return $stmt->execute([$judul, $isi, $gambar, $id]);
}

function hapusBerita($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
    return $stmt->execute([$id]);
}
