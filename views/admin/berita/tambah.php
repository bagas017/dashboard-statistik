<?php
require_once '../../../controllers/berita.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $gambar = '';

    if ($_FILES['gambar']['error'] === 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/berita/' . $gambar);
    }

    tambahBerita($judul, $isi, $gambar);

    header("Location: index.php?success=1");
    exit;
}
?>
<!-- HTML sama seperti sebelumnya -->


<h2>Tambah Berita</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Judul:<br>
        <input type="text" name="judul" required>
    </label><br><br>

    <label>Gambar:<br>
        <input type="file" name="gambar" required>
    </label><br><br>

    <label>Isi Berita:<br>
        <textarea name="isi" rows="7" required></textarea>
    </label><br><br>

    <button type="submit">Simpan</button>
</form>
<p><a href="index.php">← Kembali</a></p>
