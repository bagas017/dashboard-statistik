<?php
session_start();
require_once '../../../config/database.php';

// Handle hapus langsung dari index
if (isset($_GET['hapus'])) {
    $idHapus = $_GET['hapus'];

    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE id = ?");
    $stmt->execute([$idHapus]);
    $galeri = $stmt->fetch();

    if ($galeri) {
        $path = $galeri['file_path'];
        if ($galeri['jenis'] === 'foto' && strpos($path, 'http') !== 0) {
            @unlink("../../../uploads/foto/$path");
        } elseif ($galeri['jenis'] === 'video' && strpos($path, 'http') !== 0) {
            @unlink("../../../uploads/video/$path");
        }

        $stmt = $pdo->prepare("DELETE FROM galeri WHERE id = ?");
        $stmt->execute([$idHapus]);
    }

    header("Location: index.php?deleted=1");
    exit;
}

$filter = $_GET['jenis'] ?? 'semua';

$query = "SELECT * FROM galeri";
$params = [];

if ($filter === 'foto') {
    $query .= " WHERE jenis = 'foto'";
} elseif ($filter === 'video') {
    $query .= " WHERE jenis = 'video'";
}
$query .= " ORDER BY tanggal_upload DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$galeriList = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
            margin-bottom: 15px;
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
        .galeri-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .galeri-item {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 6px;
            width: 300px;
            background-color: #fff;
        }
        iframe, video, img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 4px;
        }
        select {
            padding: 6px;
            margin-left: 10px;
        }
        label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <h2>Daftar Galeri</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Galeri berhasil dihapus.</div>
    <?php endif; ?>

    <a href="tambah.php" class="btn btn-add">+ Tambah Galeri Baru</a>

    <form method="GET" class="mb-3">
        <label>Filter Jenis:</label>
        <select name="jenis" onchange="this.form.submit()">
            <option value="semua" <?= $filter === 'semua' ? 'selected' : '' ?>>Semua</option>
            <option value="foto" <?= $filter === 'foto' ? 'selected' : '' ?>>Foto</option>
            <option value="video" <?= $filter === 'video' ? 'selected' : '' ?>>Video</option>
        </select>
    </form>

    <div class="galeri-container">
        <?php if (count($galeriList) === 0): ?>
            <p><em>Tidak ada galeri ditemukan.</em></p>
        <?php else: ?>
            <?php foreach ($galeriList as $g): ?>
                <div class="galeri-item">
                    <strong><?= htmlspecialchars($g['judul']) ?></strong><br>
                    <small><?= date('d-m-Y H:i', strtotime($g['tanggal_upload'])) ?></small><br><br>

                    <?php if ($g['jenis'] === 'foto'): ?>
                        <img src="../../../uploads/foto/<?= htmlspecialchars($g['file_path']) ?>" alt="Foto">
                    <?php elseif (strpos($g['file_path'], 'http') === 0): ?>
                        <iframe src="<?= htmlspecialchars($g['file_path']) ?>" frameborder="0" allowfullscreen></iframe>
                    <?php else: ?>
                        <video controls>
                            <source src="../../../uploads/video/<?= htmlspecialchars($g['file_path']) ?>">
                            Browser Anda tidak mendukung video tag.
                        </video>
                    <?php endif; ?>

                    <p><?= nl2br(htmlspecialchars($g['deskripsi'])) ?></p>

                    <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-edit">Edit</a>
                    <a href="?hapus=<?= $g['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-delete">Hapus</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
