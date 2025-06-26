<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil submenu dari menu 'beranda'
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Submenu Beranda</title></head>
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
<h2>Submenu Beranda</h2>
<a href="tambah.php">+ Tambah Submenu</a>
<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Submenu</th>
        <th>Slug</th>
        <th>Tipe</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($submenus as $i => $sm): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= $sm['nama_submenu'] ?></td>
            <td><?= $sm['slug'] ?></td>
            <td><?= $sm['tipe_tampilan'] ?></td>
            <td>
                <a href="edit.php?id=<?= $sm['id'] ?>">Edit</a> | 
                <a href="../../../controllers/submenu.php?hapus=<?= $sm['id'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
