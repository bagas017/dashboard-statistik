<?php
require_once '../../config/database.php';
$berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC")->fetchAll();
?>
<?php include 'partials/header.php'; ?>


<h2>Berita Terkini</h2>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
<?php foreach ($berita as $b): ?>
    <div onclick="location.href='detail_berita.php?id=<?= $b['id'] ?>';" style="cursor:pointer; width: 300px; border:1px solid #ccc; border-radius: 5px; padding: 10px;">
        <img src="../../uploads/berita/<?= $b['gambar'] ?>" style="width:100%; height: 180px; object-fit: cover;">
        <h3><?= htmlspecialchars($b['judul']) ?></h3>
        <p><?= substr(strip_tags($b['isi']), 0, 100) ?>...</p>
    </div>
<?php endforeach; ?>
</div>
