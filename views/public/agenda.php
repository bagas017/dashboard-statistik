<?php
require_once '../../controllers/agenda.php';
$agendas = getAllAgenda();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Agenda - Bappeda Prov Lampung</title>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  
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

    .main {
      padding: 1.5rem;
    }

    .back {
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 1.2rem;
    }

    h2 {
      font-size: 2rem;
      font-weight: 700;
      color: #333;
      margin-bottom: 0.5rem;
    }

    hr {
      border: none;
      height: 3px;
      width: 60px;
      background-color: #007bff;
      margin-bottom: 1.5rem;
    }

    h4 {
      font-size: 1.8rem;
      margin-bottom: 1.2rem;
    }

    .agenda-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.2rem;
    }

    @media (max-width: 900px) {
      .agenda-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .agenda-grid {
        grid-template-columns: 1fr;
      }
    }

    .card {
      padding: 1rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
      transition: transform 0.2s ease;
      border-radius: 6px !important;
      border: none !important;
    }

    .card:hover {
      transform: translateY(-3px);
    }

    .card-1 {
      background-color: #deeafcff !important; /* Biru muda lembut */
    }

    .card-2 {
      background-color: #fff3cd !important; /* Kuning pastel hangat */
    }

    .card-3 {
      background-color: #e2f0cb !important; /* Hijau pastel segar */
    }

    .day {
      font-weight: 600;
      color: #555;
      margin-bottom: 0.2rem;
      font-size: 0.9rem;
    }

    .card h4 {
      margin: 0.4rem 0;
      font-size: 1rem;
      font-weight: 600;
      color: #333;
    }

    .info {
      display: flex;
      align-items: center;
      font-size: 0.85rem;
      font-weight: 600;
      color: #444;
      margin: 0.4rem 0;
      gap: 0.4rem;
    }

    .info-tanggal {
      display: flex;
      align-items: center;
      font-size: 0.8rem;
      font-weight: 400;
      color: #555;
      gap: 0.4rem;
      margin-top: 0.2rem;
    }

    .bi {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="main">
  <h2>AGENDA</h2>
  <hr>

  <div class="agenda-grid">
    <?php 
    $counter = 0;
    foreach ($agendas as $agenda): 
      $cardClass = 'card-' . (($counter % 3) + 1);
      $counter++;
    ?>
      <div class="card <?php echo $cardClass; ?>">
        <div class="day"><?= hariIndonesia($agenda['tanggal']) ?></div>
        <h4><?= htmlspecialchars($agenda['nama_agenda']) ?></h4>
        <div class="info">
          <i class="bi bi-geo-alt-fill"></i>
          <span><?= htmlspecialchars($agenda['lokasi']) ?></span>
        </div>
        <div class="info-tanggal">
          <i class="bi bi-calendar-event"></i>
          <span><?= formatTanggalIndonesia($agenda['tanggal'], $agenda['jam_mulai'], $agenda['jam_selesai']) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>