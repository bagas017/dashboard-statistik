<?php
require_once '../../config/database.php';

// Ambil semua submenu dari menu 'beranda'
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();

$slug = $_GET['submenu'] ?? ($submenus[0]['slug'] ?? null);

// Temukan submenu yang aktif
$current = null;
foreach ($submenus as $sm) {
    if ($sm['slug'] === $slug) {
        $current = $sm;
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Beranda</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <style>
        .submenu-nav a {
            margin-right: 10px;
            text-decoration: none;
            color: #333;
        }
        .submenu-nav a.active {
            font-weight: bold;
            color: darkblue;
        }
        .kategori-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            width: 200px;
        }
    </style>
</head>
<body>
<a href="../admin/dashboard.php" style="
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 15px;
    background-color: blue;
    color: white;
    text-decoration: none;
    border-radius: 5px;
">← Admin Dashboard</a>

<h1>Beranda</h1>

<!-- Navigasi Submenu -->
<div class="submenu-nav">
    <?php foreach ($submenus as $sm): ?>
        <a href="?submenu=<?= $sm['slug'] ?>" class="<?= ($sm['slug'] === $slug ? 'active' : '') ?>">
            <?= htmlspecialchars($sm['nama_submenu']) ?>
        </a>
    <?php endforeach; ?>
</div>

<hr>

<?php if ($current): ?>
    <h2><?= htmlspecialchars($current['nama_submenu']) ?></h2>

    <?php if ($current['tipe_tampilan'] === 'kategori'): ?>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM kategori WHERE submenu_id = ?");
        $stmt->execute([$current['id']]);
        $kategoris = $stmt->fetchAll();
        ?>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($kategoris as $kat): ?>
                <div class="kategori-box">
                    <strong><?= htmlspecialchars($kat['nama_kategori']) ?></strong><br>
                    <a href="kategori.php?id=<?= $kat['id'] ?>">Lihat Grafik</a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM statistik WHERE submenu_id = ?");
        $stmt->execute([$current['id']]);
        $stats = $stmt->fetchAll();
        ?>

        <?php foreach ($stats as $i => $stat): ?>
            <h3><?= $stat['judul'] ?></h3>
            <div id="chart<?= $i ?>" style="width:100%; height:400px;"></div>

            <?php
            $labels = [];
            $values = [];
            $data_points = [];

            if ($stat['sumber_data'] === 'manual') {
                $stmt = $pdo->prepare("SELECT * FROM statistik_data_manual WHERE statistik_id = ?");
                $stmt->execute([$stat['id']]);
                $data = $stmt->fetchAll();
                foreach ($data as $row) {
                    $labels[] = $row['label'];
                    $values[] = (float)$row['value'];
                    $data_points[] = ['name' => $row['label'], 'y' => (float)$row['value']];
                }
            } elseif ($stat['sumber_data'] === 'csv' && $stat['file_csv']) {
                $csv_path = "../../uploads/csv/" . $stat['file_csv'];
                if (file_exists($csv_path)) {
                    if (($handle = fopen($csv_path, "r")) !== false) {
                        $first = true;
                        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                            if ($first) { $first = false; continue; }
                            $labels[] = $row[0];
                            $values[] = (float)$row[1];
                            $data_points[] = ['name' => $row[0], 'y' => (float)$row[1]];
                        }
                        fclose($handle);
                    }
                }
            }

            $chartType = $stat['tipe_grafik'] === 'bar' ? 'column' : $stat['tipe_grafik'];
            $isPie = $stat['tipe_grafik'] === 'pie';
            ?>
            <script>
            Highcharts.chart('chart<?= $i ?>', {
                chart: { type: '<?= $chartType ?>' },
                title: { text: '<?= addslashes($stat['judul']) ?>' }
                <?php if (!$isPie): ?>,
                xAxis: { categories: <?= json_encode($labels) ?> },
                yAxis: { title: { text: 'Nilai' }}
                <?php endif; ?>,
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y}'
                        }
                    }
                },
                series: [{
                    name: '<?= addslashes($stat['judul']) ?>',
                    colorByPoint: <?= $isPie ? 'true' : 'false' ?>,
                    data: <?= json_encode($isPie ? $data_points : $values) ?>
                }]
            });
            </script>
        <?php endforeach; ?>
    <?php endif; ?>
<?php else: ?>
    <p>Tidak ada submenu yang ditemukan.</p>
<?php endif; ?>
</body>
</html>
