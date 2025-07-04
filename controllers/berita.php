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

function tambahBerita($judul, $divisi, $tanggal, $isi, $gambar) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO berita (judul, divisi, tanggal, isi, gambar) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$judul, $divisi, $tanggal, $isi, $gambar]);
}

function updateBerita($id, $judul, $divisi, $tanggal, $isi, $gambar) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE berita SET judul = ?, divisi = ?, tanggal = ?, isi = ?, gambar = ? WHERE id = ?");
    return $stmt->execute([$judul, $divisi, $tanggal, $isi, $gambar, $id]);
}

function hapusBerita($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
    return $stmt->execute([$id]);
}
