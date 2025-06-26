<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil semua submenu beranda
$stmt = $pdo->prepare("SELECT submenu.*, kategori.* 
    FROM kategori 
    INNER JOIN submenu ON kategori.submenu_id = submenu.id 
    WHERE submenu.nama_menu = 'beranda'");
$stmt->execute();
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Kelola Kategori</title></head>
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

<h2>Kategori pada Submenu Beranda</h2>
<a href="tambah.php">+ Tambah Kategori</a>
<table border="1">
    <tr>
        <th>No</th>
        <th>Submenu</th>
        <th>Nama Kategori</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($data as $i => $row): ?>
    <tr>
        <td><?= $i+1 ?></td>
        <td><?= $row['nama_submenu'] ?></td>
        <td><?= $row['nama_kategori'] ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
            <a href="../../../controllers/kategori.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
