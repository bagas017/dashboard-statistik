<?php
require_once '../../config/database.php';

$id_kategori = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT k.*, s.nama_submenu FROM kategori k JOIN submenu s ON s.id = k.submenu_id WHERE k.id = ?");
$stmt->execute([$id_kategori]);
$kategori = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM statistik WHERE kategori_id = ?");
$stmt->execute([$id_kategori]);
$statistik_list = $stmt->fetchAll();
$judulList = array_unique(array_map(fn($s) => $s['judul'], $statistik_list));

// ADDED: Prepare submenus data for footer
if (!isset($submenus)) {
    $stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
    $stmt->execute();
    $submenus = $stmt->fetchAll();
}

if (!isset($slug)) {
    $slug = $_GET['submenu'] ?? null;
}
?>

<?php include 'partials/header.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Kategori - <?= htmlspecialchars($kategori['nama_kategori']) ?></title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
            --shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* === LAYOUT UTAMA === */
        .main-container {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .content-container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
        }
        
        .header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        h2 {
            color: var(--primary-color);
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
            flex-grow: 1;
        }
        
        h3, h4 {
            color: var(--secondary-color);
            margin-top: 16px;
        }
        
        
        /* === STATISTIK SELECTOR === */
        .statistik-selector-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 20px;
        }

        #statistikSelector {
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-family: 'Inter', sans-serif;
            min-width: 250px;
        }
        
        /* === STATISTIK CONTAINER === */
        .statistik-container {
            background-color: #fdfdfd;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: none;
        }
        
        .statistik-container.active {
            display: block;
        }
        
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
        
        .chart-container {
            margin: 0;
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: var(--shadow);
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background-color: var(--light-gray);
            border-radius: 6px;
        }
        
        @media (max-width: 768px) {
            .content-container {
                padding: 15px;
            }
            
            .header-wrapper {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .statistik-selector-container {
                margin-left: 0;
                width: 100%;
            }
            
            #statistikSelector {
                width: 100%;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
    <div class="content-container">
        <div class="header-wrapper">
            <h2><?= htmlspecialchars($kategori['nama_kategori']) ?></h2>

            <?php if (count($statistik_list) > 0): ?>
                <div class="statistik-selector-container">
                    <select id="statistikSelector">
                        <option value="" disabled selected>-- Pilih Statistik --</option>
                        <?php foreach ($judulList as $i => $judul): ?>
                            <option value="<?= htmlspecialchars($judul) ?>" <?= $i === 0 ? 'selected' : '' ?>>
                                <?= htmlspecialchars($judul) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($statistik_list) > 0): ?>
            <?php foreach ($statistik_list as $i => $stat): ?>
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

                <div class="statistik-container" id="<?= $containerId ?>" data-judul="<?= htmlspecialchars($stat['judul']) ?>" style="<?= $i === 0 ? 'display:block;' : 'display:none;' ?>">
                    <div class="chart-container">
                        <div id="<?= $chartId ?>" style="width:100%; height:400px;"></div>
                    </div>
                    <h4>Deskripsi</h4>
                    <p><?= nl2br(htmlspecialchars($stat['deskripsi'] ?? '')) ?></p>

                    <h4>Data Tabel</h4>
                    <table>
                        <thead>
                            <tr>
                                <?php if ($chartType === 'pie'): ?>
                                    <th>Label</th><th>Value</th>
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
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['y'] ?></td></tr>
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
                    chart: { 
                        type: '<?= $chartType ?>',
                        backgroundColor: 'transparent'
                    },
                    title: { 
                        text: '<?= addslashes($stat['judul']) ?>',
                        style: {
                            fontSize: '18px',
                            fontWeight: 'bold'
                        }
                    },
                    colors: ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c', '#d35400'],
                    plotOptions: {
                        series: {
                            borderRadius: 3,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:.1f}'
                            }
                        }
                    }
                    <?php if ($chartType !== 'pie'): ?>,
                    xAxis: { 
                        categories: <?= json_encode($categories) ?>,
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        }
                    },
                    yAxis: { 
                        title: { 
                            text: 'Nilai',
                            style: {
                                fontSize: '14px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: '14px'
                            }
                        }
                    },
                    series: <?= json_encode($series) ?>
                    <?php else: ?>,
                    series: [{
                        name: 'Data',
                        colorByPoint: true,
                        data: <?= json_encode($series) ?>,
                        showInLegend: true
                    }]
                    <?php endif; ?>,
                    legend: {
                        itemStyle: {
                            fontSize: '14px'
                        }
                    }
                });
                </script>
            <?php endforeach; ?>

            <script>
            document.getElementById('statistikSelector').addEventListener('change', function () {
                let selected = this.value;
                document.querySelectorAll('.statistik-container').forEach(div => {
                    div.style.display = (div.dataset.judul === selected) ? 'block' : 'none';
                });
            });

            document.addEventListener("DOMContentLoaded", function () {
                const selector = document.getElementById('statistikSelector');
                const selected = selector.value;
                document.querySelectorAll('.statistik-container').forEach(div => {
                    div.style.display = (div.dataset.judul === selected) ? 'block' : 'none';
                });
            });
            </script>
        <?php else: ?>
            <div class="no-data">
                <p>Tidak ada data statistik yang tersedia untuk kategori ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer-kategori.php'; ?>
</body>
</html>