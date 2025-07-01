<?php
require_once '../../config/database.php';

// Ambil semua submenu dari menu 'beranda'
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();

// Ambil slug submenu yang dipilih dari URL
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
            <h3><?= htmlspecialchars($stat['judul']) ?></h3>
            <div id="chart<?= $i ?>" style="width:100%; height:400px;"></div>

            <?php
            $chartType = $stat['tipe_grafik'] === 'bar' ? 'column' : $stat['tipe_grafik'];
            $categories = [];
            $series = [];

            if ($stat['sumber_data'] === 'manual') {
                $stmt = $pdo->prepare("SELECT * FROM statistik_data_manual WHERE statistik_id = ?");
                $stmt->execute([$stat['id']]);
                $data = $stmt->fetchAll();

                if ($chartType === 'pie') {
                    foreach ($data as $row) {
                        $series[] = [
                            'name' => $row['label'],
                            'y' => (float) $row['value']
                        ];
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
                                $series[] = [
                                    'name' => $row[0],
                                    'y' => (float) $row[1]
                                ];
                            }
                        } else {
                            $seriesNames = array_slice($headers, 1);
                            $seriesData = [];
                            foreach ($seriesNames as $name) {
                                $seriesData[$name] = [];
                            }

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

            <script>
            Highcharts.chart('chart<?= $i ?>', {
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
    <?php endif; ?>

<?php else: ?>
    <p>Tidak ada submenu yang ditemukan.</p>
<?php endif; ?>

</body>
</html>
