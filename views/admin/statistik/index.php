<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Query menyesuaikan: kategori_id bisa NULL
$stmt = $pdo->prepare("
    SELECT s.*, k.nama_kategori, sub.nama_submenu 
    FROM statistik s
    LEFT JOIN kategori k ON s.kategori_id = k.id
    JOIN submenu sub ON s.submenu_id = sub.id
    ORDER BY s.created_at DESC
");
$stmt->execute();
$statistik = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Statistik</title>
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

    <h2>Data Statistik</h2>

    <a href="tambah.php" class="btn btn-add">+ Tambah Statistik</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Submenu</th>
                <th>Kategori</th>
                <th>Tipe Grafik</th>
                <th>Sumber</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($statistik) > 0): ?>
                <?php foreach ($statistik as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($s['judul']) ?></td>
                    <td><?= htmlspecialchars($s['nama_submenu']) ?></td>
                    <td><?= htmlspecialchars($s['nama_kategori'] ?? '-') ?></td>
                    <td><?= ucfirst($s['tipe_grafik']) ?></td>
                    <td><?= ucfirst($s['sumber_data']) ?></td>
                    <td>
                        <a class="btn btn-edit" href="edit.php?id=<?= $s['id'] ?>">Edit</a>
                        <a class="btn btn-delete" href="../../../controllers/statistik.php?hapus=<?= $s['id'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">Tidak ada data statistik tersedia.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
