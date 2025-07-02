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
    <style>
        table {
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #888;
        }
        th, td {
            padding: 5px 10px;
            text-align: center;
        }
        .statistik-container {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<h2><?= htmlspecialchars($kategori['nama_submenu']) ?> / <?= htmlspecialchars($kategori['nama_kategori']) ?></h2>

<?php if (count($statistik_list) > 0): ?>
    <!-- Dropdown Statistik -->
    <label for="statistikSelector"><strong>Pilih Judul Statistik:</strong></label>
    <select id="statistikSelector">
        <?php foreach ($statistik_list as $i => $stat): ?>
            <option value="statistik<?= $i ?>" <?= $i === 0 ? 'selected' : '' ?>>
                <?= htmlspecialchars($stat['judul']) ?>
            </option>
        <?php endforeach; ?>
    </select>

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

        <div class="statistik-container" id="<?= $containerId ?>" style="<?= $i === 0 ? 'display:block;' : 'display:none;' ?>">
            <h3><?= htmlspecialchars($stat['judul']) ?></h3>
            <div id="<?= $chartId ?>" style="width:100%; height:400px;"></div>
            <h4><?= nl2br(htmlspecialchars($stat['deskripsi'] ?? '')) ?></h4>

            <!-- TABEL DATA -->
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

    <!-- Script JS untuk interaksi dropdown -->
    <script>
    document.getElementById('statistikSelector').addEventListener('change', function () {
        let selected = this.value;
        document.querySelectorAll('.statistik-container').forEach(div => {
            div.style.display = 'none';
        });
        if (selected) {
            document.getElementById(selected).style.display = 'block';
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const selector = document.getElementById('statistikSelector');
        const selected = selector.value;
        document.querySelectorAll('.statistik-container').forEach(div => {
            div.style.display = 'none';
        });
        if (selected) {
            document.getElementById(selected).style.display = 'block';
        }
    });
    </script>

<?php else: ?>
    <p><em>Tidak ada data statistik yang tersedia untuk kategori ini.</em></p>
<?php endif; ?>
</body>
</html>
