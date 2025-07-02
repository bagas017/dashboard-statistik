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
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        a.btn {
            padding: 5px 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 5px;
        }
        a.btn-danger {
            background: #dc3545;
        }
    </style>
</head>
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

<a href="tambah.php" class="btn">+ Tambah Statistik</a><br><br>

<table>
    <tr>
        <th>No</th>
        <th>Judul</th>
        <th>Submenu</th>
        <th>Kategori</th>
        <th>Tipe Grafik</th>
        <th>Sumber</th>
        <th>Aksi</th>
    </tr>
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
                <a class="btn" href="edit.php?id=<?= $s['id'] ?>">Edit</a>
                <a class="btn btn-danger" href="../../../controllers/statistik.php?hapus=<?= $s['id'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center;">Tidak ada data statistik tersedia.</td>
        </tr>
    <?php endif; ?>
</table>

</body>
</html>
