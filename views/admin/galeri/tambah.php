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
    <title>Tambah Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Tambah Galeri</h2>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Jenis</label>
                    <select name="jenis" id="jenis" class="form-select" required onchange="toggleJenis()">
                        <option value="foto">Foto</option>
                        <option value="video">Video</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control"></textarea>
                </div>

                <div id="fileUpload" class="mb-3">
                    <label class="form-label">Upload File (Gambar/Video)</label>
                    <input type="file" name="file" class="form-control">
                </div>

                <div id="youtubeLink" class="mb-3" style="display:none;">
                    <label class="form-label">Link Video YouTube</label>
                    <input type="text" name="youtube_link" class="form-control" placeholder="Contoh: https://youtu.be/abc123 atau https://www.youtube.com/watch?v=abc123">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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

    if (jenis === 'foto') {
        fileDiv.style.display = 'block';
        ytDiv.style.display = 'none';
    } else {
        fileDiv.style.display = 'block';
        ytDiv.style.display = 'block';
    }
}
</script>
</body>
</html>
