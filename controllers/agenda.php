<?php
require_once __DIR__ . '/../config/database.php';

// Ambil semua data agenda
function getAllAgenda() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM agenda ORDER BY tanggal DESC, jam_mulai");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Tambah agenda baru
function tambahAgenda($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO agenda (nama_agenda, tanggal, jam_mulai, jam_selesai, lokasi) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['nama_agenda'],
        $data['tanggal'],
        $data['jam_mulai'],
        $data['jam_selesai'],
        $data['lokasi']
    ]);
}

// Ambil satu agenda berdasarkan ID
function getAgendaById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update agenda
function updateAgenda($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE agenda SET nama_agenda = ?, tanggal = ?, jam_mulai = ?, jam_selesai = ?, lokasi = ? WHERE id = ?");
    return $stmt->execute([
        $data['nama_agenda'],
        $data['tanggal'],
        $data['jam_mulai'],
        $data['jam_selesai'],
        $data['lokasi'],
        $id
    ]);
}

// Hapus agenda berdasarkan ID
function hapusAgenda($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM agenda WHERE id = ?");
    return $stmt->execute([$id]);
}

// Format tanggal Indonesia
function formatTanggalIndonesia($tanggal, $jamMulai, $jamSelesai) {
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $tgl = date('j', strtotime($tanggal));
    $bulan = $bulanIndo[(int)date('n', strtotime($tanggal))];
    $tahun = date('Y', strtotime($tanggal));

    $jam1 = date('H.i', strtotime($jamMulai));
    $jam2 = date('H.i', strtotime($jamSelesai));

    return "$tgl $bulan $tahun, $jam1 - $jam2 WIB";
}

////////////////////////////
// Logika hapus langsung //
////////////////////////////
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if (hapusAgenda($id)) {
        header("Location: ../views/admin/agenda/index.php?deleted=1");
        exit;
    } else {
        header("Location: ../views/admin/agenda/index.php?error=1");
        exit;
    }
}
