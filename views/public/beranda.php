<?php
require_once '../../config/database.php';

// Ambil semua submenu dari menu 'beranda'
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();

// Ambil slug submenu yang dipilih dari URL (default ke submenu pertama jika tidak ada)
$slug = $_GET['submenu'] ?? ($submenus[0]['slug'] ?? null);

// Temukan submenu yang aktif berdasarkan slug
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<!-- Tampilan Konten Berdasarkan Submenu yang Dipilih -->
<?php if ($current): ?>
    <h2><?= htmlspecialchars($current['nama_submenu']) ?></h2>

    <?php if ($current['tipe_tampilan'] === 'kategori'): ?>
        <?php
        // Ambil kategori berdasarkan submenu yang aktif
        $stmt = $pdo->prepare("SELECT * FROM kategori WHERE submenu_id = ?");
        $stmt->execute([$current['id']]);
        $kategoris = $stmt->fetchAll();
        ?>

        <!-- Tampilkan kategori dalam grid -->
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php foreach ($kategoris as $kat): ?>
                <div class="kategori-box">
                    <strong><?= htmlspecialchars($kat['nama_kategori']) ?></strong><br>
                    <a href="kategori.php?id=<?= $kat['id'] ?>">Lihat Grafik</a>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- Jika submenu langsung menampilkan grafik tanpa kategori -->
        <?php
        $stmt = $pdo->prepare("SELECT * FROM statistik WHERE submenu_id = ?");
        $stmt->execute([$current['id']]);
        $stats = $stmt->fetchAll();
        ?>

        <?php foreach ($stats as $i => $stat): ?>
            <h3><?= $stat['judul'] ?></h3>
            <canvas id="chart<?= $i ?>" width="400" height="200"></canvas>

            <?php
            $labels = [];
            $values = [];
            if ($stat['sumber_data'] === 'manual') {
                $stmt = $pdo->prepare("SELECT * FROM statistik_data_manual WHERE statistik_id = ?");
                $stmt->execute([$stat['id']]);
                $data = $stmt->fetchAll();

                foreach ($data as $row) {
                    $labels[] = $row['label'];
                    $values[] = $row['value'];
                }
            }
            ?>

            <script>
            const ctx<?= $i ?> = document.getElementById('chart<?= $i ?>').getContext('2d');
            new Chart(ctx<?= $i ?>, {
                type: '<?= $stat['tipe_grafik'] ?>',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: '<?= addslashes($stat['judul']) ?>',
                        data: <?= json_encode($values) ?>,
                        backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
            </script>
        <?php endforeach; ?>

    <?php endif; ?>

<?php else: ?>
    <p>Tidak ada submenu yang ditemukan.</p>
<?php endif; ?>

</body>
</html>
