<?php
require_once '../../config/database.php';

$id_kategori = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT k.*, s.nama_submenu FROM kategori k JOIN submenu s ON s.id = k.submenu_id WHERE k.id = ?");
$stmt->execute([$id_kategori]);
$kategori = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM statistik WHERE kategori_id = ?");
$stmt->execute([$id_kategori]);
$statistik_list = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistik Kategori - <?= htmlspecialchars($kategori['nama_kategori']) ?></title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body>
<h2><?= $kategori['nama_submenu'] ?> / <?= $kategori['nama_kategori'] ?></h2>

<?php foreach ($statistik_list as $i => $stat): ?>
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
            // Ambil semua series unik
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
</body>
</html>
