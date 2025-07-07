<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Submenu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .icon-option {
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            width: 80px;
        }

        .icon-option:hover,
        .icon-option.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.4);
        }

        .icon-option input[type="radio"] {
            display: none;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Tambah Submenu</h2>

    <div class="card shadow">
        <div class="card-body">
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
                    <label class="form-label">Pilih Icon</label>
                    <div class="row g-3">
                        <?php
                        $icons = [
                            'bi-bar-chart', 'bi-mortarboard', 'bi-hospital', 'bi-person-badge', 'bi-globe',
                            'bi-house-door', 'bi-briefcase', 'bi-graph-up', 'bi-water', 'bi-truck',
                            'bi-building', 'bi-flower1', 'bi-diagram-3', 'bi-bank', 'bi-people',
                            'bi-exclamation-triangle', 'bi-camera-video', 'bi-cash-coin', 'bi-puzzle'
                        ];
                        foreach ($icons as $icon): ?>
                            <div class="col-3 col-md-2">
                                <label class="icon-option d-block">
                                    <input type="radio" name="icon_class" value="<?= $icon ?>" required>
                                    <i class="bi <?= $icon ?>" style="font-size: 24px;"></i>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <br>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>


            </form>
        </div>
    </div>
</div>

<script>
    // Highlight icon selected
    document.addEventListener("DOMContentLoaded", function () {
        const options = document.querySelectorAll('.icon-option');
        options.forEach(option => {
            option.addEventListener('click', () => {
                options.forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
                option.querySelector('input[type="radio"]').checked = true;
            });

            const radio = option.querySelector('input[type="radio"]');
            if (radio.checked) {
                option.classList.add('selected');
            }
        });
    });
</script>

</body>
</html>
