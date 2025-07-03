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
</head>
<body class="p-4">
    <div class="container">
        <a href="../dashboard.php" class="btn btn-secondary mb-4">
            ← Kembali ke Dashboard
        </a>

        <h2 class="mb-3">Submenu Beranda</h2>
        <a href="tambah.php" class="btn btn-primary mb-3">+ Tambah Submenu</a>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
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
                                    <a href="edit.php?id=<?= $sm['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="../../../controllers/submenu.php?hapus=<?= $sm['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus submenu ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Belum ada submenu ditambahkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
