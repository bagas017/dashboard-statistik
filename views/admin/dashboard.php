<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}
require_once '../../config/database.php';

// Initialize all variables with default values
$jumlahSubmenu = 0;
$jumlahKategori = 0;
$jumlahStatistik = 0;
$jumlahBerita = 0;
$beritaTerakhir = null;
$jumlahAgenda = 0;
$agendaTerdekat = null;
$jumlahFoto = 0;
$jumlahVideo = 0;
$grafikData = [];
$chartLabels = [];
$chartCounts = [];
$activityData = [];

function waktuRelatif($tanggalJam) {
    if (empty($tanggalJam)) return "Belum ada aktivitas";
    
    $detik = time() - strtotime($tanggalJam);

    if ($detik < 3600) {
        return "1 jam yang lalu";
    } elseif ($detik < 86400) {
        return floor($detik / 3600) . " jam yang lalu";
    } else {
        return floor($detik / 86400) . " hari yang lalu";
    }
}

try {
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

    // Aktivitas dinamis
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

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
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
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            overflow-x: hidden;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 250px;
            z-index: 1000;
            background-color: #f8f9fa;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        /* Content Styles */
        .content {
            padding: 30px;
            margin-left: 250px;
            transition: all 0.3s;
            width: calc(100% - 250px);
        }
        
        .page-title {
            color: #2d3436;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        /* New Card Styles */
        .stat-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .card-body {
            padding: 25px;
            position: relative;
            z-index: 1;
        }
        
        .stat-card .card-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 2.5rem;
            opacity: 0.6;
            color: #6c5ce7;
        }
        
        .stat-card .card-title {
            font-size: 1rem;
            color: #ffffffff;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .card-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: #ffffffff;
            margin-bottom: 5px;
        }
        
        .stat-card .card-footer {
            background: rgba(255, 255, 255, 0.3);
            border-top: none;
            padding: 10px 25px;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Different card colors */
        .stat-card.card-primary {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
        }
        
        .stat-card.card-primary .card-title,
        .stat-card.card-primary .card-value,
        .stat-card.card-primary .card-footer {
            color: white;
        }
        
        .stat-card.card-success {
            background: linear-gradient(135deg, #00b894 0%, #55efc4 100%);
            color: white;
        }
        
        .stat-card.card-info {
            background: linear-gradient(135deg, #0984e3 0%, #74b9ff 100%);
            color: white;
        }
        
        .stat-card.card-warning {
            background: linear-gradient(135deg, #fdcb6e 0%, #ffeaa7 100%);
            color: #2d3436;
        }
        
        .stat-card.card-danger {
            background: linear-gradient(135deg, #e17055 0%, #fab1a0 100%);
            color: white;
        }
        
        .stat-card.card-secondary {
            background: linear-gradient(135deg, #636e72 0%, #b2bec3 100%);
            color: white;
        }
        
        .stat-card.card-dark {
            background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
            color: white;
        }
        
        .stat-card.card-purple {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
        }
        
        /* Chart Styles */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            height: 300px;
        }
        
        /* Activity Styles */
        .activity-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .activity-badge {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 5px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .activity-badge.purple { background-color: #6f42c1; }
        .activity-badge.green { background-color: #198754; }
        .activity-badge.yellow { background-color: #ffc107; }
        .activity-badge.red { background-color: #dc3545; }
        .activity-badge.blue { background-color: #0d6efd; }
        
        /* Content Section Styles */
        .content-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            height: 100%;
        }
        
        .content-section h5 {
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 600;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 10px;
        }
        
        .content-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .content-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .content-item .title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .content-item .date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

<?php 
$sidebarPath = __DIR__ . '/partials/sidebar.php';
if (file_exists($sidebarPath)) {
    include $sidebarPath;
} else {
    echo "<div class='alert alert-danger'>Error: Sidebar file not found</div>";
}
?>

<div class="content">
    <h2 class="page-title">Dashboard Admin</h2>

    <div class="row">
        <!-- Stat Cards - Row 1 -->
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-primary">
                <div class="card-body">
                    <i class="bi bi-menu-button-wide card-icon"></i>
                    <h6 class="card-title">Submenu</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahSubmenu) ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-success">
                <div class="card-body">
                    <i class="bi bi-tags card-icon"></i>
                    <h6 class="card-title">Kategori</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahKategori) ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-info">
                <div class="card-body">
                    <i class="bi bi-bar-chart-line card-icon"></i>
                    <h6 class="card-title">Statistik</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahStatistik) ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-warning">
                <div class="card-body">
                    <i class="bi bi-newspaper card-icon"></i>
                    <h6 class="card-title">Berita</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahBerita) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Stat Cards - Row 2 -->
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-danger">
                <div class="card-body">
                    <i class="bi bi-calendar-event card-icon"></i>
                    <h6 class="card-title">Agenda</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahAgenda) ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-secondary">
                <div class="card-body">
                    <i class="bi bi-image card-icon"></i>
                    <h6 class="card-title">Foto</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahFoto) ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-dark">
                <div class="card-body">
                    <i class="bi bi-camera-reels card-icon"></i>
                    <h6 class="card-title">Video</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahVideo) ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card stat-card card-purple">
                <div class="card-body">
                    <i class="bi bi-collection card-icon"></i>
                    <h6 class="card-title">Total Konten</h6>
                    <h2 class="card-value"><?= htmlspecialchars($jumlahBerita + $jumlahAgenda + $jumlahFoto + $jumlahVideo) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Terbaru dan Aktivitas Terbaru -->
    <div class="row">
        <!-- Konten Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="content-section">
                <h5><i class="bi bi-newspaper me-2"></i>Konten Terbaru</h5>
                
                <div class="content-item">
                    <div class="title">Berita Terbaru</div>
                    <?php if ($beritaTerakhir): ?>
                        <div class="fw-bold"><?= htmlspecialchars($beritaTerakhir['judul']) ?></div>
                        <div class="date"><?= date('d M Y', strtotime($beritaTerakhir['tanggal'])) ?></div>
                    <?php else: ?>
                        <div class="text-muted">Belum ada berita</div>
                    <?php endif; ?>
                </div>
                
                <div class="content-item">
                    <div class="title">Agenda Terdekat</div>
                    <?php if ($agendaTerdekat): ?>
                        <div class="fw-bold"><?= htmlspecialchars($agendaTerdekat['nama_agenda']) ?></div>
                        <div class="date"><?= date('d M Y', strtotime($agendaTerdekat['tanggal'])) ?></div>
                    <?php else: ?>
                        <div class="text-muted">Belum ada agenda mendatang</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Aktivitas Terbaru -->
        <div class="col-md-6 mb-4">
            <div class="content-section">
                <h5><i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru</h5>
                
                <?php if (!empty($activityData)): ?>
                    <?php foreach ($activityData as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-badge <?= htmlspecialchars($activity['warna']) ?>"></div>
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($activity['aktivitas']) ?></div>
                                <div class="text-muted small"><?= waktuRelatif($activity['waktu']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-3">Belum ada aktivitas</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Grafik Distribusi Tipe Statistik - Full Width Bar Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="content-section">
                <h5><i class="bi bi-bar-chart me-2"></i>Grafik Distribusi Tipe Statistik</h5>
                <div class="chart-container">
                    <canvas id="chartTipeGrafik"></canvas>
                </div>
            </div>
        </div>
    </div>

<script>
    // Grafik Distribusi Tipe Statistik - Bar Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartTipeGrafik').getContext('2d');
        const chartTipeGrafik = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Jumlah Statistik',
                    data: <?= json_encode($chartCounts) ?>,
                    backgroundColor: '#6c5ce7',
                    borderColor: '#6c5ce7',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Jumlah: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>

</body>
</html>