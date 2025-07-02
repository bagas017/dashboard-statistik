<?php
require_once '../../controllers/agenda.php';
$agendas = getAllAgenda();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Agenda - Bappeda Prov Lampung</title>
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


    .nav {
      display: flex;
      gap: 1rem;
    }

    .nav button {
      padding: 0.5rem 1rem;
      background-color: #e2e2e2;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .nav .active {
      background-color: #666;
      color: white;
    }

    .container {
      padding: 2rem;
    }

    .back {
      font-weight: bold;
      margin-bottom: 1rem;
      display: inline-block;
    }

    h2 {
      margin-bottom: 1.5rem;
    }

    .agenda-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
    }

    .card p {
      margin: 0.25rem 0;
      font-size: 14px;
      color: #444;
    }

    .icon {
      margin-right: 0.3rem;
    }

    .info {
      display: flex;
      align-items: center;
      font-size: 14px;
      margin-bottom: 0.3rem;
    }

    .day {
      color: #888;
      font-weight: bold;
    }

  </style>
</head>
<body>
  <header>
    <div class="logo">🟦 BAPPEDA <br> PROV LAMPUNG</div>
    <div class="nav">
        <a href="beranda.php">🏠 Beranda</a>
        <a href="agenda.php" class="active">📅 Agenda</a>
        <a href="berita.php">📰 Berita</a>
        <a href="galeri.php">🖼️ Galeri</a>
    </div>
  </header>

  <div class="container">
    <h2>Agenda Kegiatan</h2>
    <div class="agenda-grid">
      <?php foreach ($agendas as $agenda): ?>
        <div class="card">
          <div class="day"><?= date('l', strtotime($agenda['tanggal'])) ?></div>
          <h4><?= htmlspecialchars($agenda['nama_agenda']) ?></h4>
          <div class="info">📍 <?= htmlspecialchars($agenda['lokasi']) ?></div>
          <div class="info">📅 <?= formatTanggalIndonesia($agenda['tanggal'], $agenda['jam_mulai'], $agenda['jam_selesai']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
