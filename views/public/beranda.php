<?php
require_once '../../config/database.php';
require_once '../../controllers/carousel.php';

$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();

// Perbaikan di sini: tidak langsung memilih submenu pertama
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

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

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

        .content-wrapper {
            min-height: 80vh;
            max-height: 80vh;
            padding: 20px;
            background-color: #f9f9f9;
            overflow-y: auto;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            overflow-x: scroll;
        }
    </style>
</head>
<body>

<div class="content-wrapper">
<?php if ($current): ?>
    <h2><?= htmlspecialchars($current['nama_submenu']) ?></h2>

    <?php if ($current['tipe_tampilan'] === 'kategori'): ?>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM kategori WHERE submenu_id = ?");
        $stmt->execute([$current['id']]);
        $kategoris = $stmt->fetchAll();
        ?>

        <?php if (count($kategoris) > 0): ?>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($kategoris as $kat): ?>
                    <div class="kategori-box">
                        <?php if (!empty($kat['gambar'])): ?>
                            <img src="../../assets/kategori/<?= htmlspecialchars($kat['gambar']) ?>" alt="Gambar <?= htmlspecialchars($kat['nama_kategori']) ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 5px;">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($kat['nama_kategori']) ?></h3>
                        <p style="font-size: 0.9em; color: #555;"><?= htmlspecialchars($kat['deskripsi']) ?></p>
                        <a href="kategori.php?id=<?= $kat['id'] ?>" style="display:inline-block; margin-top:5px; padding:6px 10px; background:#007bff; color:white; text-decoration:none; border-radius:4px;">Lihat Grafik</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p><em>Tidak ada kategori yang tersedia pada submenu ini.</em></p>
        <?php endif; ?>

    <?php else: ?>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM statistik WHERE submenu_id = ?");
        $stmt->execute([$current['id']]);
        $stats = $stmt->fetchAll();
        ?>

        <?php if (count($stats) > 0): ?>
            <?php
            $judulList = array_unique(array_map(fn($s) => $s['judul'], $stats));
            ?>
            <label for="statistikSelector"><strong>Pilih Judul Statistik:</strong></label>
            <select id="statistikSelector">
                <option value="">-- Pilih Statistik --</option>
                <?php foreach ($judulList as $i => $judul): ?>
                    <option value="<?= htmlspecialchars($judul) ?>" <?= $i === 0 ? 'selected' : '' ?>>
                        <?= htmlspecialchars($judul) ?>
                    </option>
                <?php endforeach; ?>
            </select>

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

                <div class="statistik-container" id="<?= $containerId ?>" data-judul="<?= htmlspecialchars($stat['judul']) ?>" style="<?= $i === 0 ? 'display:block;' : 'display:none;' ?>">
                    <h3><?= htmlspecialchars($stat['judul']) ?></h3>
                    <div id="<?= $chartId ?>" style="width:100%; height:400px;"></div>
                    <h4><?= nl2br(htmlspecialchars($stat['deskripsi'] ?? '')) ?></h4>

                    <h4>Data Tabel</h4>
                    <table>
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

            <script>
            document.getElementById('statistikSelector').addEventListener('change', function () {
                let selectedJudul = this.value;
                document.querySelectorAll('.statistik-container').forEach(div => {
                    div.style.display = (div.dataset.judul === selectedJudul) ? 'block' : 'none';
                });
            });
            </script>
        <?php else: ?>
            <p><em>Tidak ada data statistik yang tersedia pada submenu ini.</em></p>
        <?php endif; ?>
    <?php endif; ?>
<?php else: ?>
    <?php
    $carouselList = getAllCarousel();
    ?>

    <?php if (count($carouselList) > 0): ?>
        <div id="carouselBeranda" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="2000">
            <div class="carousel-inner">
                <?php foreach ($carouselList as $i => $c): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="../../uploads/carousel/<?= htmlspecialchars($c['gambar']) ?>"
                             class="d-block w-100"
                             alt="Carousel <?= $i + 1 ?>"
                             style="height: 600px; object-fit: cover;">
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

<hr>

<div class="submenu-nav">
    <?php foreach ($submenus as $sm): ?>
        <a href="?submenu=<?= $sm['slug'] ?>" class="<?= ($sm['slug'] === $slug ? 'active' : '') ?>">
            <?php if (!empty($sm['icon_class'])): ?>
                <i class="bi <?= htmlspecialchars($sm['icon_class']) ?>" style="margin-right: 5px;"></i>
            <?php endif; ?>
            <?= htmlspecialchars($sm['nama_submenu']) ?>
        </a>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carouselEl = document.querySelector('#carouselBeranda');
    if (carouselEl) {
        const carousel = new bootstrap.Carousel(carouselEl, {
            interval: 10000,
            ride: 'carousel',
            pause: false, // ⬅️ penting: jangan berhenti meskipun mouse hover
            wrap: true
        });
    }
});
</script>

</body>
</html>
