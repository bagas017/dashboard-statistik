<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil semua kategori yang berada di submenu 'beranda'
$stmt = $pdo->prepare("SELECT kategori.*, submenu.nama_submenu 
    FROM kategori 
    INNER JOIN submenu ON kategori.submenu_id = submenu.id 
    WHERE submenu.nama_menu = 'beranda'");
$stmt->execute();
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kategori</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        img.thumbnail {
            width: 80px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-edit {
            background-color: #007bff;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-add {
            background-color: #28a745;
            color: white;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>

<a href="../dashboard.php" class="btn btn-back">← Kembali ke Dashboard</a>

<h2>Kategori pada Submenu Beranda</h2>
<a href="tambah.php" class="btn btn-add">+ Tambah Kategori</a>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Submenu</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $i => $row): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['nama_submenu']) ?></td>
            <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
            <td>
                <?php if (!empty($row['gambar'])): ?>
                    <img src="../../../assets/kategori/<?= $row['gambar'] ?>" class="thumbnail" alt="Gambar">
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                <a href="../../../controllers/kategori.php?hapus=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
