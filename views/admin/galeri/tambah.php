<?php
session_start();
require_once '../../../config/database.php';

// Konversi URL YouTube menjadi format embed
function convertYoutubeToEmbed($url) {
    if (strpos($url, 'youtu.be/') !== false) {
        $id = explode('youtu.be/', $url)[1];
        $id = explode('?', $id)[0];
        return "https://www.youtube.com/embed/" . $id;
    } elseif (strpos($url, 'watch?v=') !== false) {
        $id = explode('watch?v=', $url)[1];
        $id = explode('&', $id)[0];
        return "https://www.youtube.com/embed/" . $id;
    }
    return $url;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $file_path = '';

    if ($jenis === 'foto') {
        if ($_FILES['file']['error'] === 0) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $newName = time() . '.' . $ext;
            $targetDir = '../../../uploads/foto/';
            move_uploaded_file($_FILES['file']['tmp_name'], $targetDir . $newName);
            $file_path = $newName;
        }
    } elseif ($jenis === 'video') {
        if (!empty($_POST['youtube_link'])) {
            $file_path = convertYoutubeToEmbed($_POST['youtube_link']);
        } elseif ($_FILES['file']['error'] === 0) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $newName = time() . '.' . $ext;
            $targetDir = '../../../uploads/video/';
            move_uploaded_file($_FILES['file']['tmp_name'], $targetDir . $newName);
            $file_path = $newName;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO galeri (jenis, judul, deskripsi, file_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$jenis, $judul, $deskripsi, $file_path]);

    header("Location: index.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Galeri - Admin Dashboard</title>
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
        
        /* File Input Custom Styling */
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-label {
            border: 1px dashed #ced4da;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            display: block;
            transition: all 0.3s;
        }
        
        .file-input-label:hover {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .file-input-label i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: block;
        }
        
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #6c757d;
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
        
        /* Alert Styling */
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
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
        <h2><i class="bi bi-images me-2"></i>Tambah Galeri</h2>
    </div>

    <div class="form-container">
        <div class="form-card">
            <h3 class="form-title">Tambah Item Galeri Baru</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" id="jenis" class="form-select" required onchange="toggleJenis()">
                        <option value="foto">Foto</option>
                        <option value="video">Video</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control"></textarea>
                </div>

                <div id="fileUpload" class="mb-4">
                    <label class="form-label">Upload File</label>
                    <div class="file-input-container">
                        <label class="file-input-label" id="fileInputLabel">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span>Klik untuk mengunggah file</span>
                            <span class="file-name" id="fileName">Format: JPG, PNG (Max 10MB)</span>
                            <input type="file" class="file-input" name="file" id="file" onchange="updateFileName(this)">
                        </label>
                    </div>
                </div>

                <div id="youtubeLink" class="mb-4" style="display:none;">
                    <label class="form-label">Link Video YouTube</label>
                    <input type="text" name="youtube_link" class="form-control" placeholder="Contoh: https://youtu.be/abc123 atau https://www.youtube.com/watch?v=abc123">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleJenis() {
    const jenis = document.getElementById('jenis').value;
    const fileDiv = document.getElementById('fileUpload');
    const ytDiv = document.getElementById('youtubeLink');
    const fileNameDisplay = document.getElementById('fileName');

    if (jenis === 'foto') {
        fileDiv.style.display = 'block';
        ytDiv.style.display = 'none';
        fileNameDisplay.textContent = 'Format: JPG, PNG (Max 10MB)';
    } else {
        fileDiv.style.display = 'block';
        ytDiv.style.display = 'block';
        fileNameDisplay.textContent = 'Format: MP4 (Max 50MB)';
    }
}

// Update file name display
function updateFileName(input) {
    const fileNameDisplay = document.getElementById('fileName');
    if (input.files.length > 0) {
        fileNameDisplay.textContent = input.files[0].name;
        document.getElementById('fileInputLabel').style.borderColor = '#4361ee';
    } else {
        const jenis = document.getElementById('jenis').value;
        fileNameDisplay.textContent = jenis === 'foto' ? 'Format: JPG, PNG (Max 10MB)' : 'Format: MP4 (Max 10MB)';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>