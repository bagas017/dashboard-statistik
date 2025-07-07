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
    $divisi = $_POST['divisi'];
    $tanggal = $_POST['tanggal'];
    $isi = $_POST['isi'];

    $gambar = $berita['gambar'];
    if ($_FILES['gambar']['error'] === 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/berita/' . $gambar);
    }

    updateBerita($id, $judul, $divisi, $tanggal, $isi, $gambar);

    header("Location: index.php");
    exit;
}

// Daftar pilihan divisi
$divisiOptions = [
    'UPTB PUSDATINBANGDA',
    'INFRASTRUKTUR DAN PENGEMBANGAN',
    'PENGENDALIAN',
    'PERENCANAAN MAKRO DAN EVALUASI',
    'PEMERINTAHAN DAN PEMBANGUNAN MANUSIA',
    'PEREKONOMIAN',
    'PERENCANAAN',
    'SOSIAL',
    'SEKRETARIAT',
    'PPID'
];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Berita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>Edit Berita</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul:</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($berita['judul']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Divisi:</label>
                <select name="divisi" class="form-select" required>
                    <option value="">-- Pilih Divisi --</option>
                    <?php foreach ($divisiOptions as $opt): ?>
                        <option value="<?= $opt ?>" <?= ($opt === $berita['divisi']) ? 'selected' : '' ?>>
                            <?= $opt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal & Waktu:</label>
                <input type="datetime-local" name="tanggal" class="form-control"
                    value="<?= date('Y-m-d\TH:i', strtotime($berita['tanggal'])) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="../../../uploads/berita/<?= htmlspecialchars($berita['gambar']) ?>" width="200" class="rounded">
            </div>

            <div class="mb-3">
                <label class="form-label">Ganti Gambar (opsional):</label>
                <input type="file" name="gambar" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Isi Berita:</label>
                <textarea name="isi" class="form-control" rows="7" required><?= htmlspecialchars($berita['isi']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">← Kembali</a>
        </form>
    </div>
</body>
</html>
