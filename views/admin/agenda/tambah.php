<?php
require_once '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_agenda = $_POST['nama_agenda'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $lokasi = $_POST['lokasi'];

    $stmt = $pdo->prepare("INSERT INTO agenda (nama_agenda, tanggal, jam_mulai, jam_selesai, lokasi) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nama_agenda, $tanggal, $jam_mulai, $jam_selesai, $lokasi]);

    header("Location: index.php?success=1");
    exit;
}
?>

<h2>Tambah Agenda Baru</h2>
<form method="POST">
  <label>Judul Agenda:<br>
    <input type="text" name="nama_agenda" required>
  </label><br><br>

  <label>Lokasi:<br>
    <input type="text" name="lokasi" required>
  </label><br><br>
  
  <label>Tanggal:<br>
    <input type="date" name="tanggal" required>
  </label><br><br>

  <label>Jam Mulai:<br>
    <input type="time" name="jam_mulai" required>
  </label><br><br>

  <label>Jam Selesai:<br>
    <input type="time" name="jam_selesai" required>
  </label><br><br>

  <button type="submit">Simpan Agenda</button>
</form>

<p><a href="index.php">← Kembali ke daftar agenda</a></p>
