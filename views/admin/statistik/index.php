<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT s.*, k.nama_kategori, sub.nama_submenu 
    FROM statistik s
    JOIN kategori k ON s.kategori_id = k.id
    JOIN submenu sub ON k.submenu_id = sub.id
    ORDER BY s.created_at DESC
");
$stmt->execute();
$statistik = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Daftar Statistik</title></head>
<body>
<a href="../dashboard.php" style="
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 15px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 5px;
">← Back to Dashboard</a>
<h2>Data Statistik</h2>
<a href="tambah.php">+ Tambah Statistik</a>
<table border="1">
    <tr>
        <th>No</th>
        <th>Judul</th>
        <th>Submenu</th>
        <th>Kategori</th>
        <th>Tipe Grafik</th>
        <th>Sumber</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($statistik as $i => $s): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= $s['judul'] ?></td>
        <td><?= $s['nama_submenu'] ?></td>
        <td><?= $s['nama_kategori'] ?></td>
        <td><?= ucfirst($s['tipe_grafik']) ?></td>
        <td><?= ucfirst($s['sumber_data']) ?></td>
        <td>
            <!-- nanti bisa tambahkan edit.php & delete -->
            <a href="../../../controllers/statistik.php?hapus=<?= $s['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
