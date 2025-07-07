<?php
session_start();
require_once '../../../controllers/agenda.php';

$agendas = getAllAgenda();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Agenda</title>
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
    <h2>Daftar Agenda</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Agenda berhasil ditambahkan.</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Agenda berhasil diperbarui.</div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Agenda berhasil dihapus.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Terjadi kesalahan. Coba lagi.</div>
    <?php endif; ?>

    <a href="tambah.php" class="btn btn-add">+ Tambah Agenda</a>

    <?php if (count($agendas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Agenda</th>
                    <th>Tanggal & Waktu</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agendas as $i => $agenda): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($agenda['nama_agenda']) ?></td>
                        <td><?= formatTanggalIndonesia($agenda['tanggal'], $agenda['jam_mulai'], $agenda['jam_selesai']) ?></td>
                        <td><?= htmlspecialchars($agenda['lokasi']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $agenda['id'] ?>" class="btn btn-edit">Edit</a>
                            <a href="../../../controllers/agenda.php?hapus=<?= $agenda['id'] ?>" 
                               onclick="return confirm('Yakin ingin menghapus agenda ini?')" 
                               class="btn btn-delete">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><em>Tidak ada agenda yang tersedia.</em></p>
    <?php endif; ?>
</div>

</body>
</html>
