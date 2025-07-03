<?php
require_once '../../config/database.php';

$filter = $_GET['jenis'] ?? 'semua';

$query = "SELECT * FROM galeri";
if ($filter === 'foto') {
    $query .= " WHERE jenis = 'foto'";
} elseif ($filter === 'video') {
    $query .= " WHERE jenis = 'video'";
}
$query .= " ORDER BY tanggal_upload DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$galeri = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Galeri - Bappeda Prov Lampung</title>
  <style>
    body {
      font-family: sans-serif;
      background-color: #f1f1f1;
      margin: 0;
      padding: 0;
    }

    header {
      display: flex;
      align-items: center;
      padding: 1rem 2rem;
      background-color: white;
      border-bottom: 1px solid #ccc;
    }

    .logo {
      font-weight: bold;
      margin-right: auto;
    }

    .nav a {
        padding: 0.5rem 1rem;
        background-color: #e2e2e2;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
        color: #000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav a.active {
        background-color: #666;
        color: white;
    }

    .container {
      padding: 2rem;
    }

    h2 {
      margin-bottom: 1rem;
    }

    .filter-form {
      margin-bottom: 1.5rem;
    }

    .filter-form select {
      padding: 0.5rem;
      font-size: 1rem;
    }

    .galeri-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1rem;
    }

    .card {
      background-color: white;
      border-radius: 10px;
      padding: 1rem;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    .card h4 {
      margin: 0.25rem 0;
      font-size: 1.1rem;
    }

    .card p {
      font-size: 14px;
      color: #444;
    }

    .media {
      width: 100%;
      max-height: 180px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 0.5rem;
    }

    iframe, video {
      width: 100%;
      height: 180px;
      border-radius: 6px;
      border: none;
    }
  </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="container">
  <h2>Galeri</h2>

  <form method="GET" class="filter-form">
    <label for="jenis">Filter Jenis:</label>
    <select name="jenis" id="jenis" onchange="this.form.submit()">
      <option value="semua" <?= $filter === 'semua' ? 'selected' : '' ?>>Semua</option>
      <option value="foto" <?= $filter === 'foto' ? 'selected' : '' ?>>Foto</option>
      <option value="video" <?= $filter === 'video' ? 'selected' : '' ?>>Video</option>
    </select>
  </form>

  <div class="galeri-grid">
    <?php foreach ($galeri as $item): ?>
      <div class="card">
        <?php if ($item['jenis'] === 'foto'): ?>
          <img src="../../uploads/foto/<?= htmlspecialchars($item['file_path']) ?>" class="media" alt="Foto">
        <?php elseif (strpos($item['file_path'], 'http') === 0): ?>
          <iframe src="<?= htmlspecialchars($item['file_path']) ?>" allowfullscreen></iframe>
        <?php else: ?>
          <video controls>
            <source src="../../uploads/video/<?= htmlspecialchars($item['file_path']) ?>">
            Browser Anda tidak mendukung video.
          </video>
        <?php endif; ?>

        <h4><?= htmlspecialchars($item['judul']) ?></h4>
        <p><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($galeri) === 0): ?>
    <p><em>Tidak ada konten ditemukan.</em></p>
  <?php endif; ?>
</div>

</body>
</html>
