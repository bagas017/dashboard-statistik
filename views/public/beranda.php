<?php
require_once '../../config/database.php';
require_once '../../controllers/carousel.php';

$jumlahBerita = 0;
$beritaTerakhir = null;
$jumlahAgenda = 0;
$agendaTerdekat = null;

$jumlahBerita = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$beritaTerakhir = $pdo->query("SELECT judul, tanggal FROM berita ORDER BY tanggal DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$jumlahAgenda = $pdo->query("SELECT COUNT(*) FROM agenda")->fetchColumn();
$agendaTerdekat = $pdo->query("
  SELECT nama_agenda, tanggal, lokasi, jam_mulai, jam_selesai 
  FROM agenda 
  WHERE tanggal >= CURDATE() 
  ORDER BY tanggal ASC 
  LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

function hariIndonesia($tanggal) {
    $hari = date('l', strtotime($tanggal));
    $map = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
    ];
    return $map[$hari] ?? $hari;
}

function formatTanggalIndonesia($tanggal, $jamMulai = null, $jamSelesai = null) {
    $tgl = date('d M Y', strtotime($tanggal));
    if ($jamMulai && $jamSelesai) {
        return "$tgl | $jamMulai - $jamSelesai";
    } elseif ($jamMulai) {
        return "$tgl | $jamMulai";
    }
    return $tgl;
}

$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();

$slug = $_GET['submenu'] ?? null;

$current = null;
foreach ($submenus as $sm) {
    if ($sm['slug'] === $slug) {
        $current = $sm;
        break;
    }
}
?>

<?php include 'partials/header.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>Beranda</title>
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
  /* Reset dan dasar */
  body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background-color: #f5f5f5;
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
    min-height: 100vh;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE 10+ */
  }

  body::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
  }

  * {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE 10+ */
  }

  *::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
  }

  /* === LAYOUT UTAMA === */
  .main-container {
    display: flex;
    flex-direction: row;
    flex: 1;
    padding: 20px;
    gap: 20px;
  }

  .content-container {
    flex: 1;
    padding: 20px;
    background-color: #fafbffff;
    border-radius: 8px;
  }

  /* Tambahan styling jika berisi carousel (tanpa submenu) */
  .konten-carousel {
    flex: 8;
    background-color: #fff;
    padding: 16px;
    border-radius: 8px;
  }

  .side-content {
    flex: 2;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    position: relative;
  }

  /* === HEADER === */
  .page-header {
    text-align: center;
    margin-bottom: 16px;
  }

  .page-header h2 {
    color: #333;
    font-weight: 600;
    font-size: 1.3rem;
  }

  /* === KATEGORI === */
  .kategori-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 0px;
  }

  .kategori-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 300px;
    width: 350px;
    display: flex;
    flex-direction: column;
  }

  .kategori-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
  }

  .kategori-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
  }

  .kategori-content {
    padding: 15px;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .kategori-title {
    font-size: 1rem;
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #333;
  }

  .kategori-desc {
    font-size: 0.8rem;
    text-align: justify;
    color: #666;
    line-height: 1.4;
    margin-bottom: 15px;
    flex: 1;
    overflow: hidden;
    display: -webkit-box;
    line-clamp: 3;
    -webkit-box-orient: vertical;
  }

  .kategori-link {
    align-self: start;
    display: inline-block;
    padding: 6px 12px;
    background-color: #0066d6;
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: background-color 0.2s;
  }

  .kategori-link:hover {
    background-color: #0052aa;
  }

  /* === STATISTIK === */
  .statistik-selector-container {
    margin-bottom: 20px;
  }

  .statistik-wrapper {
    margin-top: 20px;
  }

  .statistik-container {
    background-color: #fdfdfd;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
  }

  #statistikSelector {
    padding: 8px 30px;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-family: 'Inter', sans-serif;
    width: 100%;

  }

  /* === CAROUSEL === */
  .carousel-item {
    height: 580px;
    object-fit: fill;
  }

  .carousel-inner img {
    height: 580px;
    object-fit: fill;
    border-radius: 10px;
  }

  /* === TABEL === */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: 'Inter', sans-serif;
  }

  table, th, td {
    border: 1px solid #ccc;
  }

  th, td {
    padding: 8px 10px;
    text-align: left;
  }

  th {
    background-color: #f0f0f0;
    font-weight: 600;
  }

  .content-section h5 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 16px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 8px;
  }

  .content-item {
    margin-bottom: 20px;
  }

  .content-item .title {
    font-size: 0.95rem;
    font-weight: 500;
    color: #555;
    margin-bottom: 4px;
  }

  .side-content .day {
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 4px;
  }

  .side-content .info, .side-content .info-tanggal {
    font-size: 0.9rem;
    color: #444;
    margin-bottom: 4px;
  }

  /* === DIGITAL CLOCK === */
  .digital-clock {
    background-color: #0066d6;
    color: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  .clock-time {
    font-size: 2.5rem;
    font-weight: 600;
    margin-bottom: 5px;
  }

  .clock-date {
    font-size: 1rem;
    opacity: 0.9;
  }

  .header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

  /* === COPYRIGHT === */
  .copyright-text {
    position: absolute;
    bottom: 10px;
    left: 20px;
    font-size: 0.6rem;
    color: #666;
    font-weight: 300;
  }

  /* === RESPONSIVE === */
  @media (max-width: 1200px) {
    .kategori-container {
      grid-template-columns: repeat(3, 1fr);
    }
    .main-container {
      flex-direction: column;
    }
    .side-content {
      max-width: 100%;
      margin-top: 20px;
    }
    .copyright-text {
      position: static;
      margin-top: 20px;
      text-align: center;
    }
  }

  @media (max-width: 900px) {
    .kategori-container {
      grid-template-columns: repeat(2, 1fr);
    }

    .carousel-inner img {
      height: 400px;
    }
  }

  @media (max-width: 600px) {
    .kategori-container {
      grid-template-columns: 1fr;
    }

    .content-container {
      padding: 15px;
    }

    .carousel-inner img {
      height: 300px;
    }

    .digital-clock {
      padding: 10px;
    }

    .clock-time {
      font-size: 2rem;
    }

    .clock-date {
      font-size: 0.9rem;
    }
  }
  </style>
</head>
<body>
<div class="main-container">
    <!-- Kontainer Kiri -->
    <div class="content-container <?= $current ? '' : 'konten-carousel' ?>">

      <?php if ($current): ?>
        <!-- Header Wrapper -->
        <div class="header-wrapper">
          <h2><?= htmlspecialchars($current['nama_submenu']) ?></h2>

          <?php if ($current['tipe_tampilan'] !== 'kategori'): ?>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM statistik WHERE submenu_id = ?");
            $stmt->execute([$current['id']]);
            $stats = $stmt->fetchAll();
            ?>
            <?php if (count($stats) > 0): ?>
              <?php
              $judulList = array_unique(array_map(fn($s) => $s['judul'], $stats));
              ?>
              <div class="statistik-selector-container">
                <select id="statistikSelector" class="form-select">
                  <option value="" disabled selected>-- Pilih Statistik --</option>
                  <?php foreach ($judulList as $judul): ?>
                    <option value="<?= htmlspecialchars($judul) ?>"><?= htmlspecialchars($judul) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($current): ?>
        <!-- === KATEGORI === -->
        <?php if ($current['tipe_tampilan'] === 'kategori'): ?>
          <?php
          $stmt = $pdo->prepare("SELECT * FROM kategori WHERE submenu_id = ?");
          $stmt->execute([$current['id']]);
          $kategoris = $stmt->fetchAll();
          ?>

          <?php if (count($kategoris) > 0): ?>
            <div class="kategori-container">
              <?php foreach ($kategoris as $kat): ?>
                <div class="kategori-card">
                  <?php if (!empty($kat['gambar'])): ?>
                    <img src="../../assets/kategori/<?= htmlspecialchars($kat['gambar']) ?>" class="kategori-image" alt="<?= htmlspecialchars($kat['nama_kategori']) ?>">
                  <?php endif; ?>
                  <div class="kategori-content">
                    <h3 class="kategori-title"><?= htmlspecialchars($kat['nama_kategori']) ?></h3>
                    <p class="kategori-desc">
                      <?= mb_substr(strip_tags($kat['deskripsi']), 0, 120) ?>...
                    </p>
                    <a href="kategori.php?id=<?= $kat['id'] ?>" class="kategori-link">Lihat Grafik</a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p><em>Tidak ada kategori yang tersedia pada submenu ini.</em></p>
          <?php endif; ?>

        <!-- === STATISTIK === -->
        <?php else: ?>

          <?php if (count($stats) > 0): ?>
            <div class="statistik-wrapper">
              <?php foreach ($stats as $i => $stat): ?>
                <?php
                $chartId = "chart$i";
                $containerId = "statistik$i";
                $chartType = $stat['tipe_grafik'] === 'bar' ? 'column' : $stat['tipe_grafik'];
                $categories = [];
                $series = [];

                if ($stat['sumber_data'] === 'manual') {
                  $stmt = $pdo->prepare("SELECT * FROM statistik_data_manual WHERE statistik_id = ?");
                  $stmt->execute([$stat['id']]);
                  $data = $stmt->fetchAll();

                  if ($chartType === 'pie') {
                    foreach ($data as $row) {
                      $series[] = ['name' => $row['label'], 'y' => (float) $row['value']];
                    }
                  } else {
                    $seriesNames = [];
                    $labelMap = [];
                    foreach ($data as $row) {
                      $seriesNames[$row['series_label']] = true;
                      $labelMap[$row['label']] = true;
                    }
                    $categories = array_keys($labelMap);
                    $seriesNames = array_keys($seriesNames);
                    foreach ($seriesNames as $seriesName) {
                      $seriesData = [];
                      foreach ($categories as $label) {
                        $found = false;
                        foreach ($data as $row) {
                          if ($row['series_label'] === $seriesName && $row['label'] === $label) {
                            $seriesData[] = (float) $row['value'];
                            $found = true;
                            break;
                          }
                        }
                        if (!$found) $seriesData[] = null;
                      }
                      $series[] = ['name' => $seriesName, 'data' => $seriesData];
                    }
                  }
                } elseif ($stat['sumber_data'] === 'csv' && $stat['file_csv']) {
                  $csv_path = "../../uploads/csv/" . $stat['file_csv'];
                  if (file_exists($csv_path)) {
                    if (($handle = fopen($csv_path, "r")) !== false) {
                      $headers = fgetcsv($handle, 1000, ",");
                      if ($chartType === 'pie') {
                        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                          $series[] = ['name' => $row[0], 'y' => (float) $row[1]];
                        }
                      } else {
                        $seriesNames = array_slice($headers, 1);
                        $seriesData = [];
                        foreach ($seriesNames as $name) $seriesData[$name] = [];
                        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                          $categories[] = $row[0];
                          for ($j = 1; $j < count($row); $j++) {
                            $seriesData[$seriesNames[$j - 1]][] = floatval($row[$j]);
                          }
                        }
                        foreach ($seriesData as $name => $data) {
                          $series[] = ['name' => $name, 'data' => $data];
                        }
                      }
                      fclose($handle);
                    }
                  }
                }
                ?>

                <div class="statistik-container" id="<?= $containerId ?>" data-judul="<?= htmlspecialchars($stat['judul']) ?>" style="display: <?= $i === 0 ? 'block' : 'none' ?>;">
                  <h3><?= htmlspecialchars($stat['judul']) ?></h3>
                  <div id="<?= $chartId ?>" style="width:100%; height:400px;"></div>
                  <p><?= nl2br(htmlspecialchars($stat['deskripsi'] ?? '')) ?></p>

                  <h4>Data Tabel</h4>
                  <table class="table">
                    <thead>
                      <tr>
                        <?php if ($chartType === 'pie'): ?>
                          <th>Label</th>
                          <th>Value</th>
                        <?php else: ?>
                          <th>Kategori</th>
                          <?php foreach ($series as $s): ?>
                            <th><?= htmlspecialchars($s['name']) ?></th>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($chartType === 'pie'): ?>
                        <?php foreach ($series as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $row['y'] ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <?php foreach ($categories as $rowIndex => $cat): ?>
                          <tr>
                            <td><?= htmlspecialchars($cat) ?></td>
                            <?php foreach ($series as $s): ?>
                              <td><?= $s['data'][$rowIndex] ?? '' ?></td>
                            <?php endforeach; ?>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

                <script>
                Highcharts.chart('<?= $chartId ?>', {
                  chart: { type: '<?= $chartType ?>' },
                  title: { text: '<?= addslashes($stat['judul']) ?>' }
                  <?php if ($chartType !== 'pie'): ?>,
                  xAxis: { categories: <?= json_encode($categories) ?> },
                  yAxis: { title: { text: 'Nilai' } },
                  series: <?= json_encode($series) ?>
                  <?php else: ?>,
                  series: [{
                    name: 'Data',
                    colorByPoint: true,
                    data: <?= json_encode($series) ?>
                  }]
                  <?php endif; ?>,
                });
                </script>
              <?php endforeach; ?>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
              const selector = document.getElementById('statistikSelector');
              const statContainers = document.querySelectorAll('.statistik-container');
              if (selector && selector.options.length > 1) {
                selector.selectedIndex = 1;
                const firstJudul = selector.options[1].value;
                statContainers.forEach(div => {
                  div.style.display = div.dataset.judul === firstJudul ? 'block' : 'none';
                });
              }

              if (selector) {
                selector.addEventListener('change', function() {
                  const selectedJudul = this.value;
                  statContainers.forEach(div => {
                    div.style.display = div.dataset.judul === selectedJudul ? 'block' : 'none';
                  });
                });
              }
            });
            </script>
          <?php else: ?>
            <p><em>Tidak ada data statistik yang tersedia pada submenu ini.</em></p>
          <?php endif; ?>
        <?php endif; ?>

      <!-- === CAROUSEL === -->
      <?php else: ?>
        <?php $carouselList = getAllCarousel(); ?>

        <?php if (count($carouselList) > 0): ?>
          <div id="carouselBeranda" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="15000">
            <div class="carousel-inner">
              <?php foreach ($carouselList as $i => $c): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                  <img src="../../uploads/carousel/<?= htmlspecialchars($c['gambar']) ?>" class="d-block w-100" alt="Carousel <?= $i + 1 ?>">
                </div>
              <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselBeranda" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselBeranda" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
        <?php else: ?>
          <p>Pilih submenu untuk melihat content</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- === SIDE CONTENT (tampil hanya saat carousel) === -->
    <?php if (!$current): ?>
      <div class="side-content">
        <!-- Digital Clock -->
        <div class="digital-clock">
          <div class="clock-time" id="clock-time">00:00:00</div>
          <div class="clock-date" id="clock-date">Hari, DD Bulan YYYY</div>
        </div>

        <div class="content-section">
          <h5><i class="bi bi-newspaper me-2"></i>Konten Terbaru</h5>

          <div class="content-item mb-4">
            <div class="title">Berita Terbaru</div>
            <?php if ($beritaTerakhir): ?>
              <div class="fw-bold"><?= htmlspecialchars($beritaTerakhir['judul']) ?></div>
              <div class="date text-muted"><?= date('d M Y', strtotime($beritaTerakhir['tanggal'])) ?></div>
            <?php else: ?>
              <div class="text-muted">Belum ada berita</div>
            <?php endif; ?>
          </div>

          <div class="content-item">
            <div class="title">Agenda Terdekat</div>
            <?php if ($agendaTerdekat): ?>
              <h4 style="font-size: 1rem; margin-bottom: 6px; font-weight:600;"><?= htmlspecialchars($agendaTerdekat['nama_agenda']) ?></h4>
              <div class="info">📍 <?= htmlspecialchars($agendaTerdekat['lokasi']) ?></div>
              <div class="info-tanggal">📅 <?= formatTanggalIndonesia($agendaTerdekat['tanggal'], $agendaTerdekat['jam_mulai'], $agendaTerdekat['jam_selesai']) ?></div>
            <?php else: ?>
              <div class="text-muted">Belum ada agenda mendatang</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Copyright Text -->
        <div class="copyright-text">
          Dashboard Display Informasi UPTD Badan Perencanaan Pembangunan Daerah Provinsi Lampung @copyright 2025
        </div>
      </div>

    <?php endif; ?>
  </div>


  <?php include 'partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const carouselEl = document.querySelector('#carouselBeranda');
    if (carouselEl) {
      const carousel = new bootstrap.Carousel(carouselEl, {
        interval: 10000,
        ride: 'carousel',
        pause: false,
        wrap: true
      });
    }

    // Digital Clock Function
    function updateClock() {
      const now = new Date();
      const timeElement = document.getElementById('clock-time');
      const dateElement = document.getElementById('clock-date');
      
      if (timeElement && dateElement) {
        // Format time
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        timeElement.textContent = `${hours}:${minutes}:${seconds}`;
        
        // Format date
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();
        
        dateElement.textContent = `${dayName}, ${date} ${monthName} ${year}`;
      }
    }
    
    // Update clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);
  });
  </script>
</body>
</html>