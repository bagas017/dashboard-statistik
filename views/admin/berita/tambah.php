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
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Berita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>Tambah Berita</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul:</label>
                <input type="text" class="form-control" name="judul" id="judul" required>
            </div>

            <div class="mb-3">
                <label for="divisi" class="form-label">Divisi:</label>
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

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal & Jam:</label>
                <input type="datetime-local" class="form-control" name="tanggal" id="tanggal" required>
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar:</label>
                <input type="file" class="form-control" name="gambar" id="gambar" required>
            </div>

            <div class="mb-3">
                <label for="isi" class="form-label">Isi Berita:</label>
                <textarea class="form-control" name="isi" id="isi" rows="7" required></textarea>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="index.php" class="btn btn-secondary">← Kembali</a>
        </form>
    </div>
</body>
</html>
