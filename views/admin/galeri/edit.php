<?php
require_once '../../../config/database.php';

// Fungsi untuk konversi URL YouTube menjadi embed
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

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM galeri WHERE id = ?");
$stmt->execute([$id]);
$galeri = $stmt->fetch();

if (!$galeri) {
    echo "Data galeri tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $jenis = $_POST['jenis'];
    $oldPath = $galeri['file_path'];
    $newPath = $oldPath;

    if ($jenis === 'video' && isset($_POST['tipe_video']) && $_POST['tipe_video'] === 'youtube' && !empty($_POST['youtube_link'])) {
        $newPath = convertYoutubeToEmbed($_POST['youtube_link']);
    } elseif (!empty($_FILES['file_upload']['name'])) {
        $folder = ($jenis === 'foto') ? '../../../uploads/foto/' : '../../../uploads/video/';
        $ext = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $target = $folder . $fileName;

        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $target)) {
            $newPath = $fileName;
        }
    }

    $stmt = $pdo->prepare("UPDATE galeri SET judul = ?, deskripsi = ?, jenis = ?, file_path = ? WHERE id = ?");
    $stmt->execute([$judul, $deskripsi, $jenis, $newPath, $id]);

    header("Location: index.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Galeri - Admin Dashboard</title>
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
        
        .form-control {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
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
        <h2><i class="bi bi-pencil-square me-2"></i>Edit Galeri</h2>
    </div>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="mb-4">
                <label class="form-label">Judul:</label>
                <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($galeri['judul']) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Deskripsi:</label>
                <textarea class="form-control" name="deskripsi" rows="4"><?= htmlspecialchars($galeri['deskripsi']) ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Jenis:</label>
                <select class="form-control" name="jenis" id="jenis" onchange="toggleInput()" required>
                    <option value="foto" <?= $galeri['jenis'] === 'foto' ? 'selected' : '' ?>>Foto</option>
                    <option value="video" <?= $galeri['jenis'] === 'video' ? 'selected' : '' ?>>Video</option>
                </select>
            </div>

            <div class="mb-4" id="uploadSection">
                <label class="form-label">File Baru (opsional):</label>
                <input type="file" class="form-control" name="file_upload">
                <small class="text-muted">File sebelumnya: <?= htmlspecialchars($galeri['file_path']) ?></small>
            </div>

            <div class="mb-4" id="youtubeSection">
                <label class="form-label">Link Video YouTube:</label>
                <input type="url" class="form-control" name="youtube_link" id="youtube_link" value="<?= (strpos($galeri['file_path'], 'http') === 0) ? htmlspecialchars($galeri['file_path']) : '' ?>">
                <input type="hidden" name="tipe_video" id="tipe_video_input" value="<?= (strpos($galeri['file_path'], 'http') === 0 ? 'youtube' : '') ?>">
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleInput() {
    const jenis = document.getElementById('jenis').value;
    const youtubeSection = document.getElementById('youtubeSection');
    const youtubeInput = document.getElementById('youtube_link');
    const tipeVideoInput = document.getElementById('tipe_video_input');

    if (jenis === 'foto') {
        youtubeSection.style.display = 'none';
        youtubeInput.disabled = true;
        tipeVideoInput.value = '';
    } else {
        youtubeSection.style.display = 'block';
        youtubeInput.disabled = false;
        tipeVideoInput.value = 'youtube';
    }
}

// Jalankan saat halaman pertama kali dimuat
document.addEventListener('DOMContentLoaded', toggleInput);
</script>
</body>
</html>