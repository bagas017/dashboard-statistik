<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submenu Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-edit {
            background-color: #ffc107;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
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

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <h2>Submenu Beranda</h2>
    <a href="tambah.php" class="btn btn-add">+ Tambah Submenu</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Icon</th>
                <th>Nama Submenu</th>
                <th>Slug</th>
                <th>Tipe Tampilan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($submenus) > 0): ?>
                <?php foreach ($submenus as $i => $sm): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><i class="bi <?= $sm['icon_class'] ?? 'bi-question-circle' ?>" style="font-size: 1.5rem;"></i></td>
                        <td><?= htmlspecialchars($sm['nama_submenu']) ?></td>
                        <td><?= htmlspecialchars($sm['slug']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= $sm['tipe_tampilan'] ?></span></td>
                        <td>
                            <a href="edit.php?id=<?= $sm['id'] ?>" class="btn btn-edit">Edit</a>
                            <a href="../../../controllers/submenu.php?hapus=<?= $sm['id'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus submenu ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Belum ada submenu ditambahkan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
