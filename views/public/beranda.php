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
        <p>[Dummy] Grafik langsung tanpa kategori akan tampil di sini.</p>
    <?php endif; ?>

<?php else: ?>
    <p>Tidak ada submenu yang ditemukan.</p>
<?php endif; ?>

</body>
</html>
