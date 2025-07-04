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
    $isi = $_POST['isi'];

    $gambar = $berita['gambar'];
    if ($_FILES['gambar']['error'] === 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/berita/' . $gambar);
    }

    updateBerita($id, $judul, $isi, $gambar);

    header("Location: index.php");
    exit;
}
?>
<!-- HTML sama seperti sebelumnya -->


<h2>Edit Berita</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Judul:<br>
        <input type="text" name="judul" value="<?= htmlspecialchars($berita['judul']) ?>" required>
    </label><br><br>

    <label>Gambar Saat Ini:<br>
        <img src="../../../uploads/berita/<?= htmlspecialchars($berita['gambar']) ?>" width="150">
    </label><br><br>

    <label>Ganti Gambar (opsional):<br>
        <input type="file" name="gambar">
    </label><br><br>

    <label>Isi:<br>
        <textarea name="isi" rows="7"><?= htmlspecialchars($berita['isi']) ?></textarea>
    </label><br><br>

    <button type="submit">Update</button>
</form>
<p><a href="index.php">← Kembali</a></p>
