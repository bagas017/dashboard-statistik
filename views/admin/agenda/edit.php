<?php
require_once '../../../config/database.php';

if (!isset($_GET['id'])) {
    echo "ID agenda tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

// Proses submit form edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_agenda = $_POST['nama_agenda'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $lokasi = $_POST['lokasi'];

    $stmt = $pdo->prepare("UPDATE agenda SET nama_agenda = ?, tanggal = ?, jam_mulai = ?, jam_selesai = ?, lokasi = ? WHERE id = ?");
    $stmt->execute([$nama_agenda, $tanggal, $jam_mulai, $jam_selesai, $lokasi, $id]);

    header("Location: index.php?updated=1");
    exit;
}

// Ambil data agenda lama
$stmt = $pdo->prepare("SELECT * FROM agenda WHERE id = ?");
$stmt->execute([$id]);
$agenda = $stmt->fetch();

if (!$agenda) {
    echo "Data agenda tidak ditemukan.";
    exit;
}
?>

<h2>Edit Agenda</h2>
<form method="POST">
  <label>Judul Agenda:<br>
    <input type="text" name="nama_agenda" value="<?= htmlspecialchars($agenda['nama_agenda']) ?>" required>
  </label><br><br>

  <label>Lokasi:<br>
    <input type="text" name="lokasi" value="<?= htmlspecialchars($agenda['lokasi']) ?>" required>
  </label><br><br>
  
  <label>Tanggal:<br>
    <input type="date" name="tanggal" value="<?= $agenda['tanggal'] ?>" required>
  </label><br><br>

  <label>Jam Mulai:<br>
    <input type="time" name="jam_mulai" value="<?= $agenda['jam_mulai'] ?>" required>
  </label><br><br>

  <label>Jam Selesai:<br>
    <input type="time" name="jam_selesai" value="<?= $agenda['jam_selesai'] ?>" required>
  </label><br><br>

  <button type="submit">Simpan Perubahan</button>
</form>

<p><a href="index.php">← Kembali ke daftar agenda</a></p>
