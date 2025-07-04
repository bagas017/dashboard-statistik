<?php
session_start();
require_once '../../../controllers/carousel.php';

// Cek jika belum login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil semua data carousel
$carousel = getAllCarousel();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Carousel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .sidebar .active {
            font-weight: bold;
            color: #0d6efd;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
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
            width: 120px;
            height: auto;
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
        .btn-add {
            background-color: #28a745;
            color: white;
            margin-bottom: 10px;
            display: inline-block;
        }
        .btn-edit {
            background-color: #ffc107;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <h2>Data Carousel</h2>
    <a href="tambah.php" class="btn btn-add">+ Tambah Carousel</a>

    <?php if (count($carousel) === 0): ?>
        <div class="alert alert-info mt-3">Belum ada data carousel.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Urutan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carousel as $index => $c): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <img src="../../../uploads/carousel/<?= htmlspecialchars($c['gambar']) ?>" class="thumbnail" alt="Gambar">
                        </td>
                        <td><?= htmlspecialchars($c['urutan']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-edit">Edit</a>
                            <a href="index.php?hapus=<?= $c['id'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus carousel ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
// Proses hapus jika ada parameter GET 'hapus'
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (hapusCarousel($id)) {
        echo "<script>alert('Data berhasil dihapus'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data');</script>";
    }
}
?>

</body>
</html>
