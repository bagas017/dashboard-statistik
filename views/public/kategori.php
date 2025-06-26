<?php
require_once '../../config/database.php';

$id_kategori = $_GET['id'] ?? 0;

// Ambil info kategori
$stmt = $pdo->prepare("SELECT k.*, s.nama_submenu FROM kategori k JOIN submenu s ON s.id = k.submenu_id WHERE k.id = ?");
$stmt->execute([$id_kategori]);
$kategori = $stmt->fetch();

// Ambil semua statistik dari kategori ini
$stmt = $pdo->prepare("SELECT * FROM statistik WHERE kategori_id = ?");
$stmt->execute([$id_kategori]);
$statistik_list = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistik Kategori - <?= htmlspecialchars($kategori['nama_kategori']) ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h2><?= $kategori['nama_submenu'] ?> / <?= $kategori['nama_kategori'] ?></h2>

<?php foreach ($statistik_list as $i => $stat): ?>
    <div style="margin-bottom: 50px;">
        <h3><?= htmlspecialchars($stat['judul']) ?></h3>
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

        // Jika nanti versi CSV ingin ditampilkan, tambahkan pembacaan CSV di sini
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
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
                    ],
                    borderColor: 'rgba(0,0,0,0.3)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: <?= $stat['tipe_grafik'] === 'pie' ? 'true' : 'false' ?>
                    }
                }
            }
        });
        </script>
    </div>
<?php endforeach; ?>
</body>
</html>
