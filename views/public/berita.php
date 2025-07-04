<?php
require_once '../../config/database.php';

$berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC")->fetchAll();

function waktuRelatif($tanggalJam) {
    $detik = time() - strtotime($tanggalJam);
    if ($detik < 60) return "$detik detik yang lalu";
    elseif ($detik < 3600) return floor($detik / 60) . " menit yang lalu";
    elseif ($detik < 86400) return floor($detik / 3600) . " jam yang lalu";
    else return floor($detik / 86400) . " hari yang lalu";
}
?>

<h2>Berita Terkini</h2>
<div style="display:flex; flex-wrap:wrap; gap:20px;">
<?php foreach ($berita as $b): ?>
    <div style="border:1px solid #ccc; border-radius:8px; width:300px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <img src="../../uploads/berita/<?= $b['gambar'] ?>" style="width:100%; height:180px; object-fit:cover;">
        <div style="padding:15px;">
            <h4><?= htmlspecialchars($b['judul']) ?></h4>
            <small><strong><?= htmlspecialchars($b['divisi']) ?></strong> - <?= date('d M Y H:i', strtotime($b['tanggal'])) ?></small>
            <p><?= mb_substr(strip_tags($b['isi']), 0, 100) ?>...</p>
            <p><em><?= waktuRelatif($b['tanggal']) ?></em></p>
            <a href="detail_berita.php?id=<?= $b['id'] ?>" style="color:#007bff; text-decoration:none;">Selengkapnya →</a>
        </div>
    </div>
<?php endforeach; ?>
</div>
