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

<h2>Edit Galeri</h2>
<form method="POST" enctype="multipart/form-data" novalidate>
    <label>Judul:</label><br>
    <input type="text" name="judul" value="<?= htmlspecialchars($galeri['judul']) ?>" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" rows="4" cols="40"><?= htmlspecialchars($galeri['deskripsi']) ?></textarea><br><br>

    <label>Jenis:</label><br>
    <select name="jenis" id="jenis" onchange="toggleInput()" required>
        <option value="foto" <?= $galeri['jenis'] === 'foto' ? 'selected' : '' ?>>Foto</option>
        <option value="video" <?= $galeri['jenis'] === 'video' ? 'selected' : '' ?>>Video</option>
    </select><br><br>

    <div id="uploadSection">
        <label>File Baru (opsional):</label><br>
        <input type="file" name="file_upload"><br>
        <small>File sebelumnya: <?= htmlspecialchars($galeri['file_path']) ?></small><br><br>
    </div>

    <div id="youtubeSection">
        <label>Link Video YouTube:</label><br>
        <input type="url" name="youtube_link" id="youtube_link" value="<?= (strpos($galeri['file_path'], 'http') === 0) ? htmlspecialchars($galeri['file_path']) : '' ?>"><br><br>
        <input type="hidden" name="tipe_video" id="tipe_video_input" value="<?= (strpos($galeri['file_path'], 'http') === 0 ? 'youtube' : '') ?>">
    </div>

    <button type="submit">Simpan Perubahan</button>
</form>

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

<p><a href="index.php">← Kembali ke daftar galeri</a></p>
