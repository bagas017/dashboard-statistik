<?php
require_once '../../../controllers/berita.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $divisi = $_POST['divisi'];
    $tanggal = $_POST['tanggal']; // format: Y-m-d\TH:i
    $isi = $_POST['isi'];
    $gambar = '';

    if ($_FILES['gambar']['error'] === 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/berita/' . $gambar);
    }

    tambahBerita($judul, $divisi, $tanggal, $isi, $gambar);

    header("Location: index.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita - Admin Dashboard</title>
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
        
        /* Form Card Styling */
        .form-container {
            max-width: 2000px;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
            border: none;
        }
        
        .form-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
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
        
        /* Button Styling */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        /* File Input Styling */
        .form-file {
            position: relative;
        }
        
        .form-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(2.25rem + 2px);
            margin: 0;
            opacity: 0;
        }
        
        .form-file-label {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1;
            height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        
        .form-file-text {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            
            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="bi bi-newspaper me-2"></i>Tambah Berita</h2>
    </div>

    <div class="form-container">
        <div class="form-card">
            <h3 class="form-title">Tambah Berita Baru</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="judul" class="form-label">Judul Berita</label>
                    <input type="text" class="form-control" name="judul" id="judul" required>
                </div>

                <div class="mb-4">
                    <label for="divisi" class="form-label">Divisi</label>
                    <select class="form-select" name="divisi" id="divisi" required>
                        <option value="">-- Pilih Divisi --</option>
                        <option value="UPTB PUSDATINBANGDA">UPTB PUSDATINBANGDA</option>
                        <option value="INFRASTRUKTUR DAN PENGEMBANGAN">INFRASTRUKTUR DAN PENGEMBANGAN</option>
                        <option value="PENGENDALIAN">PENGENDALIAN</option>
                        <option value="PERENCANAAN MAKRO DAN EVALUASI">PERENCANAAN MAKRO DAN EVALUASI</option>
                        <option value="PEMERINTAHAN DAN PEMBANGUNAN MANUSIA">PEMERINTAHAN DAN PEMBANGUNAN MANUSIA</option>
                        <option value="PEREKONOMIAN">PEREKONOMIAN</option>
                        <option value="PERENCANAAN">PERENCANAAN</option>
                        <option value="SOSIAL">SOSIAL</option>
                        <option value="SEKRETARIAT">SEKRETARIAT</option>
                        <option value="PPID">PPID</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label">Tanggal & Waktu</label>
                    <input type="datetime-local" class="form-control" name="tanggal" id="tanggal" required>
                </div>

                <div class="mb-4">
                    <label for="gambar" class="form-label">Gambar Berita</label>
                    <input type="file" class="form-control" name="gambar" id="gambar" required>
                    <div class="form-text">Format gambar: JPG, PNG, JPEG, SVG. Maksimal 50MB.</div>
                </div>

                <div class="mb-4">
                    <label for="isi" class="form-label">Isi Berita</label>
                    <textarea class="form-control" name="isi" id="isi" required></textarea>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Berita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>