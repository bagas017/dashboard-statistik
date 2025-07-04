<?php
require_once '../../controllers/agenda.php';
$agendas = getAllAgenda();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Agenda - Bappeda Prov Lampung</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- CSS eksternal -->
  <link rel="stylesheet" href="../../assets/css/public-agenda.css">

  <!-- Gaya tambahan -->
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #f1f1f1;
    }

    .logo {
      display: flex;
      align-items: center;
      font-weight: bold;
      gap: 10px;
    }

    .logo-icon {
      width: 32px;
      height: 32px;
      background-color: #ccc;
      display: inline-block;
    }

    .nav {
      display: flex;
      gap: 0.5rem;
    }

    .nav a {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      font-weight: 600;
      background-color: #5b4bad;
      color: white;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
    }

    .nav a.active {
      background-color: #5b4bad;
      color: white;
    }

    .main {
      padding: 2rem;
    }

    .back {
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 1.2rem;
      margin-bottom: 1.5rem;
    }

    .back::before {
      content: "←";
      margin-right: 0.5rem;
    }

    h4 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
    }

    .agenda-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .card {
      background-color: #fec76f !important;
      padding: 1rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .day {
      font-weight: 600;
      color: #555;
    }

    .card h4 {
      margin: 0.2rem 0;
      font-size: 1.2rem;
      font-weight: 700;
    }

    .info {
      display: flex;
      align-items: center;
      font-size: 14px;
      font-weight: 600;
      color: #444;
      margin-top: 0.2rem;
      margin-bottom: 0.2rem;
    }

    .info-tanggal {
      font-size: 0.875rem;
      font-weight: 300;
    }
  </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="main">
  <div class="back">AGENDA</div>

  <div class="agenda-grid">
    <?php foreach ($agendas as $agenda): ?>
      <div class="card">
        <div class="day"><?= hariIndonesia($agenda['tanggal']) ?></div>
        <h4><?= htmlspecialchars($agenda['nama_agenda']) ?></h4>
        <div class="info">📍 <?= htmlspecialchars($agenda['lokasi']) ?></div>
        <div class="info-tanggal">📅 <?= formatTanggalIndonesia($agenda['tanggal'], $agenda['jam_mulai'], $agenda['jam_selesai']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>
