<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Ambil data submenu
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE id = ?");
$stmt->execute([$id]);
$submenu = $stmt->fetch();

if (!$submenu) {
    echo "Submenu tidak ditemukan.";
    exit;
}

$icons = [
    'bi-bar-chart'  => " ",
    'bi-mortarboard'  => " ",
    'bi-hospital'  => " ",
    'bi-person-badge'  => " ",
    'bi-globe'  => " ",
    'bi-house-door'  => " ",
    'bi-briefcase'  => " ",
    'bi-graph-up'  => " ",
    'bi-water'  => " ",
    'bi-truck'  => " ",
    'bi-building'  => " ",
    'bi-flower1'  => " ",
    'bi-diagram-3'  => " ",
    'bi-bank'  => " ",
    'bi-people'  => " ",
    'bi-exclamation-triangle'  => " ",
    'bi-camera-video'  => " ",
    'bi-cash-coin'  => " ",
    'bi-puzzle' => " ",
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Submenu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="p-4">
    <h2>Edit Submenu</h2>

    <form method="POST" action="../../../controllers/submenu.php">
        <input type="hidden" name="id" value="<?= $submenu['id'] ?>">

        <div class="mb-3">
            <label class="form-label">Nama Submenu</label>
            <input type="text" name="nama_submenu" class="form-control" value="<?= htmlspecialchars($submenu['nama_submenu']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe Tampilan</label>
            <select name="tipe_tampilan" class="form-select" required>
                <option value="langsung" <?= $submenu['tipe_tampilan'] === 'langsung' ? 'selected' : '' ?>>Langsung</option>
                <option value="kategori" <?= $submenu['tipe_tampilan'] === 'kategori' ? 'selected' : '' ?>>Terdapat Kategori</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Icon</label><br>
            <div class="d-flex flex-wrap gap-3">
                <?php foreach ($icons as $class => $label): ?>
                    <label class="text-center">
                        <input type="radio" name="icon_class" value="<?= $class ?>" <?= $submenu['icon_class'] === $class ? 'checked' : '' ?>> <br>
                        <i class="bi <?= $class ?>" style="font-size: 24px;"></i><br>
                        <small><?= $label ?></small>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</body>
</html>
