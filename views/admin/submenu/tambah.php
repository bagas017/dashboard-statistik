<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Submenu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="p-4">
    <h2>Tambah Submenu</h2>
    <form method="POST" action="../../../controllers/submenu.php">
        <input type="hidden" name="nama_menu" value="beranda">

        <div class="mb-3">
            <label class="form-label">Nama Submenu</label>
            <input type="text" name="nama_submenu" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe Tampilan</label>
            <select name="tipe_tampilan" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="langsung">Langsung</option>
                <option value="kategori">Terdapat Kategori</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Icon</label><br>
            <div class="d-flex flex-wrap gap-3">
                <?php
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
                foreach ($icons as $class => $label): ?>
                    <label class="text-center">
                        <input type="radio" name="icon_class" value="<?= $class ?>" required> <br>
                        <i class="bi <?= $class ?>" style="font-size: 24px;"></i><br>
                        <small><?= $label ?></small>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" name="tambah" class="btn btn-primary mt-3">Simpan</button>
    </form>
</body>
</html>
