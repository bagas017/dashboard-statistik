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

<h2>Tambah Galeri</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Jenis:</label><br>
    <select name="jenis" id="jenis" required onchange="toggleJenis()">
        <option value="foto">Foto</option>
        <option value="video">Video</option>
    </select><br><br>

    <label>Judul:</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" rows="3"></textarea><br><br>

    <div id="fileUpload">
        <label>Upload File (Gambar/Video):</label><br>
        <input type="file" name="file"><br><br>
    </div>

    <div id="youtubeLink" style="display:none;">
        <label>Link Video YouTube:</label><br>
        <input type="text" name="youtube_link" placeholder="Contoh: https://youtu.be/abc123 atau https://www.youtube.com/watch?v=abc123"><br><br>
    </div>

    <button type="submit">Simpan</button>
</form>

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
