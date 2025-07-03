<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Ambil data kategori
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
$stmt->execute([$id]);
$kategori = $stmt->fetch();

if (!$kategori) {
    echo "Data tidak ditemukan.";
    exit;
}

// Ambil submenu
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda' AND tipe_tampilan = 'kategori'");
$stmt->execute();
$submenus = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Kategori</title>
    <style>
        .gambar-option {
            display: inline-block;
            margin: 5px;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .gambar-option img {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .gambar-option input[type="radio"] {
            display: none;
        }
        .gambar-option.selected {
            border-color: blue;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll('input[name="gambar"]');
            radios.forEach(radio => {
                if (radio.checked) {
                    radio.closest('.gambar-option').classList.add('selected');
                }

                radio.addEventListener('change', function() {
                    document.querySelectorAll('.gambar-option').forEach(el => el.classList.remove('selected'));
                    this.closest('.gambar-option').classList.add('selected');
                });
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
</head>
<body>

<h2>Edit Kategori</h2>

<form method="POST" action="../../../controllers/kategori.php">
    <input type="hidden" name="id" value="<?= $kategori['id'] ?>">

    <!-- Submenu -->
    <label>Submenu (Lokasi)</label><br>
    <select name="submenu_id" required>
        <?php foreach ($submenus as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $kategori['submenu_id'] == $s['id'] ? 'selected' : '' ?>>
                <?= $s['nama_submenu'] ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <!-- Nama -->
    <label>Nama Kategori</label><br>
    <input type="text" name="nama_kategori" value="<?= htmlspecialchars($kategori['nama_kategori']) ?>" required><br><br>

    <!-- Deskripsi -->
    <label>Deskripsi Singkat (maks. 25 kata)</label><br>
    <textarea name="deskripsi" id="deskripsi" rows="3" style="width:300px;"><?= htmlspecialchars($kategori['deskripsi']) ?></textarea><br>
    <small><span id="wordCount">0 / 25 kata</span></small><br><br>

    <!-- Gambar -->
    <label>Pilih Gambar</label><br>
    <div style="display: flex; flex-wrap: wrap;">
        <?php for ($i = 1; $i <= 10; $i++): 
            $gambar = "kategori$i.jpg"; ?>
            <label class="gambar-option">
                <input type="radio" name="gambar" value="<?= $gambar ?>" <?= $kategori['gambar'] == $gambar ? 'checked' : '' ?>>
                <img src="../../../assets/kategori/<?= $gambar ?>" alt="Gambar <?= $i ?>">
            </label>
        <?php endfor; ?>
    </div><br><br>

    <button type="submit" name="update">Update</button>
</form>

</body>
</html>
