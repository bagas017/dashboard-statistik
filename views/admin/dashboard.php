<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
require_once '../../config/database.php';

// Fungsi waktu relatif
function waktuRelatif($tanggalJam) {
    $detik = time() - strtotime($tanggalJam);

    if ($detik < 3600) {
        return "1 jam yang lalu";
    } elseif ($detik < 86400) {
        return floor($detik / 3600) . " jam yang lalu";
    } else {
        return floor($detik / 86400) . " hari yang lalu";
    }
}

// Query utama
$jumlahSubmenu = $pdo->query("SELECT COUNT(*) FROM submenu")->fetchColumn();
$jumlahKategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$jumlahStatistik = $pdo->query("SELECT COUNT(*) FROM statistik")->fetchColumn();

$jumlahBerita = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
$beritaTerakhir = $pdo->query("SELECT judul, tanggal FROM berita ORDER BY tanggal DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$jumlahAgenda = $pdo->query("SELECT COUNT(*) FROM agenda")->fetchColumn();
$agendaTerdekat = $pdo->query("SELECT nama_agenda, tanggal FROM agenda WHERE tanggal >= CURDATE() ORDER BY tanggal ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$jumlahFoto = $pdo->query("SELECT COUNT(*) FROM galeri WHERE jenis = 'foto'")->fetchColumn();
$jumlahVideo = $pdo->query("SELECT COUNT(*) FROM galeri WHERE jenis = 'video'")->fetchColumn();

// Grafik
$grafikData = $pdo->query("SELECT tipe_grafik, COUNT(*) as total FROM statistik GROUP BY tipe_grafik")->fetchAll(PDO::FETCH_ASSOC);
$chartLabels = array_column($grafikData, 'tipe_grafik');
$chartCounts = array_column($grafikData, 'total');

// Aktivitas dinamis dari tabel yang sudah ada
$activityData = [];

// 1. Berita
$berita = $pdo->query("SELECT 'Berita ditambahkan' AS aktivitas, tanggal AS waktu, 'purple' AS warna FROM berita ORDER BY tanggal DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($berita) $activityData[] = $berita;

// 2. Agenda
$agenda = $pdo->query("SELECT 'Agenda dijadwalkan' AS aktivitas, tanggal AS waktu, 'green' AS warna FROM agenda ORDER BY tanggal DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($agenda) $activityData[] = $agenda;

// 3. Galeri
$galeri = $pdo->query("SELECT CONCAT('Galeri ', jenis, ' diunggah') AS aktivitas, tanggal_upload AS waktu, IF(jenis = 'foto', 'yellow', 'red') AS warna FROM galeri ORDER BY tanggal_upload DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($galeri) $activityData[] = $galeri;

// 4. Statistik
$statistik = $pdo->query("SELECT 'Statistik baru dibuat' AS aktivitas, created_at AS waktu, 'blue' AS warna FROM statistik ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($statistik) $activityData[] = $statistik;

// Urutkan berdasarkan waktu terbaru
usort($activityData, function($a, $b) {
    return strtotime($b['waktu']) - strtotime($a['waktu']);
});
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            display: flex;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f8f9fa;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
        .card-box {
            margin-bottom: 20px;
        }

        /* Activity Styles */
        .activity-list {
            list-style: none;
            padding-left: 0;
            margin-top: 30px;
        }
        .activity-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .activity-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 6px;
            margin-right: 10px;
        }
        .activity-badge.purple { background-color: #6f42c1; }
        .activity-badge.green { background-color: #198754; }
        .activity-badge.yellow { background-color: #ffc107; }
        .activity-badge.red { background-color: #dc3545; }
        .activity-badge.blue { background-color: #0d6efd; }
        .activity-text {
            font-weight: 500;
        }
        .activity-time {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/partials/sidebar.php'; ?>

<div class="content">
    <h2 class="mb-4">Dashboard Admin</h2>

    <div class="row">
        <div class="col-md-3 card-box">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Submenu</h5>
                    <h3><?= $jumlahSubmenu ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 card-box">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Kategori</h5>
                    <h3><?= $jumlahKategori ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 card-box">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Statistik</h5>
                    <h3><?= $jumlahStatistik ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 card-box">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Berita</h5>
                    <h3><?= $jumlahBerita ?></h3>
                    <?php if ($beritaTerakhir): ?>
                        <small class="text-muted">Terakhir: <?= htmlspecialchars($beritaTerakhir['judul']) ?> (<?= date('d M Y', strtotime($beritaTerakhir['tanggal'])) ?>)</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 card-box">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Agenda</h5>
                    <h3><?= $jumlahAgenda ?></h3>
                    <?php if ($agendaTerdekat): ?>
                        <small class="text-muted">Terdekat: <?= htmlspecialchars($agendaTerdekat['nama_agenda']) ?> (<?= date('d M Y', strtotime($agendaTerdekat['tanggal'])) ?>)</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 card-box">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Galeri</h5>
                    <h3><?= $jumlahFoto + $jumlahVideo ?></h3>
                    <small>Foto: <?= $jumlahFoto ?> | Video: <?= $jumlahVideo ?></small>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-6">
            <h5>Pie Chart: Distribusi Tipe Grafik</h5>
            <canvas id="chartPie"></canvas>
        </div>
        <div class="col-md-6">
            <h5>Line Chart: Penggunaan Tipe Grafik</h5>
            <canvas id="chartLine"></canvas>
        </div>
    </div>

    <div>
        <h5>Aktivitas Terbaru</h5>
        <ul class="activity-list">
            <?php foreach ($activityData as $activity): ?>
                <li class="activity-item">
                    <div class="activity-badge <?= htmlspecialchars($activity['warna']) ?>"></div>
                    <div>
                        <div class="activity-text"><?= htmlspecialchars($activity['aktivitas']) ?></div>
                        <div class="activity-time"><?= waktuRelatif($activity['waktu']) ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    const pieCtx = document.getElementById('chartPie').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartCounts) ?>,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d']
            }]
        }
    });

    const lineCtx = document.getElementById('chartLine').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Jumlah Penggunaan',
                data: <?= json_encode($chartCounts) ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
                tension: 0.4
            }]
        }
    });
</script>

</body>
</html>
