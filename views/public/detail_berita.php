<?php
require_once '../../config/database.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->execute([$id]);
$berita = $stmt->fetch();

if (!$berita) {
    echo "Berita tidak ditemukan.";
    exit;
}
?>

<!-- Tombol Kembali -->
<p>
    <a href="berita.php" style="display:inline-block; margin-top:20px; padding:8px 16px; background:#007bff; color:white; text-decoration:none; border-radius:4px;">
        ← Kembali ke daftar berita
    </a>
</p>

<h2><?= htmlspecialchars($berita['judul']) ?></h2>
<p><small>Dipublikasikan oleh <strong>Administrator</strong> pada <?= date('d M Y', strtotime($berita['tanggal'])) ?></small></p>

<img src="../../uploads/berita/<?= $berita['gambar'] ?>"
     style="width:100%; height:auto; border-radius:5px;">
<br><br>

<p><?= nl2br(htmlspecialchars($berita['isi'])) ?></p>


