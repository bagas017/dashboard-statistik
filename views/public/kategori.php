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
</body>
</html>
