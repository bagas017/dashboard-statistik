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

// Fungsi untuk memotong teks
function limitWords($text, $limit) {
    $words = explode(' ', $text);
    if (count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Galeri - Admin Dashboard</title>
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
            font-size: 1.5rem;
        }
        
        /* Button Styling */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: left;
        }
        
        .btn-add {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-add:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-action {
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #e67700;
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(247, 127, 0, 0.3);
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #e51779;
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(247, 37, 133, 0.3);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        /* Filter Styling */
        .filter-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .filter-label {
            font-weight: 500;
            color: #495057;
            margin-right: 10px;
        }
        
        .filter-select {
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            background-color: white;
            transition: all 0.3s;
        }
        
        .filter-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        /* Gallery Grid Styling */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .gallery-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .gallery-media {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .gallery-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .gallery-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-size: 1rem;
            line-height: 1.3;
        }
        
        .gallery-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 10px;
            display: block;
        }
        
        .gallery-desc {
            color: #495057;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }
        
        .gallery-actions {
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
            margin-top: auto;
        }
        
        .gallery-actions .btn-action {
            flex: 1;
            text-align: center;
            margin: 0 5px;
        }
        
        .gallery-actions .btn-action:first-child {
            margin-left: 0;
        }
        
        .gallery-actions .btn-action:last-child {
            margin-right: 0;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            font-weight: 500;
            margin-bottom: 10px;
            color: #6c757d;
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            border: none;
        }
        
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
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
                padding: 20px;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="bi bi-images me-2"></i>Manajemen Galeri</h2>
        <a href="tambah.php" class="btn btn-add">
            <i class="bi bi-plus-circle"></i> Tambah Galeri
        </a>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>Galeri berhasil dihapus.
        </div>
    <?php endif; ?>

    <div class="filter-container">
        <form method="GET" class="d-flex align-items-center">
            <label class="filter-label">Filter Jenis:</label>
            <select name="jenis" class="filter-select" onchange="this.form.submit()">
                <option value="semua" <?= $filter === 'semua' ? 'selected' : '' ?>>Semua</option>
                <option value="foto" <?= $filter === 'foto' ? 'selected' : '' ?>>Foto</option>
                <option value="video" <?= $filter === 'video' ? 'selected' : '' ?>>Video</option>
            </select>
        </form>
    </div>

    <div class="gallery-grid">
        <?php if (count($galeriList) === 0): ?>
            <div class="empty-state">
                <i class="bi bi-image-alt"></i>
                <h4>Tidak Ada Konten Galeri</h4>
                <p>Belum ada konten galeri yang ditambahkan. Klik tombol "Tambah Galeri" untuk memulai.</p>
            </div>
        <?php else: ?>
            <?php foreach ($galeriList as $g): ?>
                <div class="gallery-card">
                    <?php if ($g['jenis'] === 'foto'): ?>
                        <img src="../../../uploads/foto/<?= htmlspecialchars($g['file_path']) ?>" class="gallery-media" alt="<?= htmlspecialchars($g['judul']) ?>">
                    <?php elseif (strpos($g['file_path'], 'http') === 0): ?>
                        <iframe src="<?= htmlspecialchars($g['file_path']) ?>" class="gallery-media" frameborder="0" allowfullscreen></iframe>
                    <?php else: ?>
                        <video class="gallery-media" controls>
                            <source src="../../../uploads/video/<?= htmlspecialchars($g['file_path']) ?>">
                            Browser Anda tidak mendukung video tag.
                        </video>
                    <?php endif; ?>
                    
                    <div class="gallery-body">
                        <h3 class="gallery-title"><?= htmlspecialchars($g['judul']) ?></h3>
                        <span class="gallery-date">
                            <i class="bi bi-calendar me-1"></i><?= date('d-m-Y H:i', strtotime($g['tanggal_upload'])) ?>
                        </span>
                        <p class="gallery-desc" title="<?= htmlspecialchars($g['deskripsi']) ?>">
                            <?= nl2br(htmlspecialchars(limitWords($g['deskripsi'], 30))) ?>
                        </p>
                        
                        <div class="gallery-actions">
                            <a href="edit.php?id=<?= $g['id'] ?>" class="btn-action btn-edit">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="?hapus=<?= $g['id'] ?>" onclick="return confirm('Yakin ingin menghapus konten ini?')" class="btn-action btn-delete">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>