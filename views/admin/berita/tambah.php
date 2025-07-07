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
    <title>Tambah Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .form-container {
            max-width: 800px;
        }
        textarea {
            min-height: 200px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Tambah Berita Baru</h2>

    <div class="card mx-auto form-container">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Berita</label>
                    <input type="text" class="form-control" name="judul" id="judul" required>
                </div>

                <div class="mb-3">
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

                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal & Waktu</label>
                    <input type="datetime-local" class="form-control" name="tanggal" id="tanggal" required>
                </div>

                <div class="mb-3">
                    <label for="gambar" class="form-label">Gambar Berita</label>
                    <input type="file" class="form-control" name="gambar" id="gambar" required>
                    <div class="form-text">Format gambar: JPG, PNG, JPEG. Maksimal 2MB.</div>
                </div>

                <div class="mb-4">
                    <label for="isi" class="form-label">Isi Berita</label>
                    <textarea class="form-control" name="isi" id="isi" required></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Berita</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>