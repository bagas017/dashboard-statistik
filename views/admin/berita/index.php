<?php
require_once '../../../controllers/berita.php';

// Proses hapus jika ada parameter GET 'hapus'
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    hapusBerita($id);
    header("Location: index.php");
    exit;
}

$berita = getAllBerita();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --danger-color: #f72585;
            --warning-color: #f77f00;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 280px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 250px;
            z-index: 1000;
            background-color: #f8f9fa;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        /* Main Content Styling */
        .content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-header h2 {
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .btn-add {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-add:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-add i {
            margin-right: 8px;
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            font-weight: 600;
        }
        
        /* Table Styling */
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 12px 15px;
            border-bottom: 2px solid #e9ecef;
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: all 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }
        
        /* Button Styling */
        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-right: 5px;
        }
        
        .btn-edit {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #e67700;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(247, 127, 0, 0.3);
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #e51779;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(247, 37, 133, 0.3);
        }
        
        .btn-action i {
            margin-right: 5px;
            font-size: 12px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="bi bi-newspaper me-2"></i>Daftar Berita</h2>
        <a href="tambah.php" class="btn btn-add">
            <i class="bi bi-plus-lg"></i> Tambah Berita
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            Daftar Berita
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="60%" >Judul</th>
                            <th width="20%">Tanggal & Jam Upload</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($berita) > 0): ?>
                            <?php foreach ($berita as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['judul']) ?></td>
                                    <td><?= htmlspecialchars($b['tanggal']) ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $b['id'] ?>" class="btn-action btn-edit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="index.php?hapus=<?= $b['id'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Hapus berita ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-newspaper"></i>
                                        <h4>Belum Ada Berita</h4>
                                        <p>Silakan tambahkan berita baru untuk mengisi daftar ini</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>