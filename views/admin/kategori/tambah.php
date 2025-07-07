<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda' AND tipe_tampilan = 'kategori'");
$stmt->execute();
$submenus = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gambar-option {
            border: 2px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: 0.3s;
        }
        .gambar-option:hover,
        .gambar-option.selected {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
        }
        .gambar-option input[type="radio"] {
            display: none;
        }
        .gambar-option img {
            width: 100%;
            height: 80px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Tambah Kategori</h2>
    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="../../../controllers/kategori.php">

                <div class="mb-3">
                    <label class="form-label">Submenu (Lokasi)</label>
                    <select name="submenu_id" class="form-select" required>
                        <option value="">-- Pilih Submenu --</option>
                        <?php foreach ($submenus as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_submenu']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi Singkat (maks. 25 kata)</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                    <small class="text-muted"><span id="wordCount">0 / 25 kata</span></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Gambar</label>
                    <div class="row">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <div class="col-4 col-md-2 mb-3">
                                <label class="gambar-option d-block">
                                    <input type="radio" name="gambar" value="kategori<?= $i ?>.jpg" <?= $i === 1 ? 'checked' : '' ?>>
                                    <img src="../../../assets/kategori/kategori<?= $i ?>.jpg" alt="Kategori <?= $i ?>">
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary" name="tambah">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const radios = document.querySelectorAll('input[name="gambar"]');
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.gambar-option').forEach(el => el.classList.remove('selected'));
                this.closest('.gambar-option').classList.add('selected');
            });

            if (radio.checked) {
                radio.closest('.gambar-option').classList.add('selected');
            }
        });

        const deskripsiInput = document.getElementById('deskripsi');
        const wordCount = document.getElementById('wordCount');
        deskripsiInput.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/).filter(Boolean);
            wordCount.textContent = words.length + " / 25 kata";
            if (words.length > 25) {
                this.value = words.slice(0, 25).join(" ");
            }
        });
    });
</script>

</body>
</html>
