<?php
require_once '../../../controllers/berita.php';

$id = $_GET['id'] ?? 0;
$berita = getBeritaById($id);

if (!$berita) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $divisi = $_POST['divisi'];
    $tanggal = $_POST['tanggal'];
    $isi = $_POST['isi'];

    $gambar = $berita['gambar'];
    if ($_FILES['gambar']['error'] === 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/berita/' . $gambar);
    }

    updateBerita($id, $judul, $divisi, $tanggal, $isi, $gambar);

    header("Location: index.php");
    exit;
}

// Daftar pilihan divisi
$divisiOptions = [
    'UPTB PUSDATINBANGDA',
    'INFRASTRUKTUR DAN PENGEMBANGAN',
    'PENGENDALIAN',
    'PERENCANAAN MAKRO DAN EVALUASI',
    'PEMERINTAHAN DAN PEMBANGUNAN MANUSIA',
    'PEREKONOMIAN',
    'PERENCANAAN',
    'SOSIAL',
    'SEKRETARIAT',
    'PPID'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* Form Card Styling */
        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        textarea.form-control {
            min-height: 200px;
        }
        
        /* Image Preview */
        .image-preview {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .image-preview:hover {
            transform: scale(1.02);
        }
        
        /* Button Styling */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn i {
            margin-right: 8px;
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
        <h2><i class="bi bi-newspaper me-2"></i>Edit Berita</h2>
    </div>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="form-label">Judul:</label>
                <input type="text" name="judul" class="form-control" 
                       value="<?= htmlspecialchars($berita['judul']) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Divisi:</label>
                <select name="divisi" class="form-select" required>
                    <option value="">-- Pilih Divisi --</option>
                    <?php foreach ($divisiOptions as $opt): ?>
                        <option value="<?= $opt ?>" <?= ($opt === $berita['divisi']) ? 'selected' : '' ?>>
                            <?= $opt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Tanggal & Waktu:</label>
                <input type="datetime-local" name="tanggal" class="form-control"
                    value="<?= date('Y-m-d\TH:i', strtotime($berita['tanggal'])) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="../../../uploads/berita/<?= htmlspecialchars($berita['gambar']) ?>" 
                     class="image-preview" style="max-width: 400px;">
            </div>

            <div class="mb-4">
                <label class="form-label">Ganti Gambar (opsional):</label>
                <input type="file" name="gambar" class="form-control">
                <small class="text-muted">Format yang didukung: JPG, PNG, JPEG. Ukuran maksimal: 2MB</small>
            </div>

            <div class="mb-4">
                <label class="form-label">Isi Berita:</label>
                <textarea name="isi" class="form-control" rows="7" required><?= htmlspecialchars($berita['isi']) ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>