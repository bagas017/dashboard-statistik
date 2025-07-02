<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Ambil data statistik utama
$stmt = $pdo->prepare("SELECT * FROM statistik WHERE id = ?");
$stmt->execute([$id]);
$statistik = $stmt->fetch();

if (!$statistik) {
    echo "Data tidak ditemukan.";
    exit;
}

// Ambil data manual jika ada
$stmt = $pdo->prepare("SELECT * FROM statistik_data_manual WHERE statistik_id = ?");
$stmt->execute([$id]);
$data_manual = $stmt->fetchAll();

// Ambil submenu
$submenus = $pdo->query("SELECT * FROM submenu")->fetchAll();

// Ambil kategori berdasarkan submenu yang sesuai
$kategori = [];
if ($statistik['submenu_id']) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE submenu_id = ?");
    $stmt->execute([$statistik['submenu_id']]);
    $kategori = $stmt->fetchAll();
}

// Pisahkan data manual jika multi-series
$multi_series = [];
if ($statistik['sumber_data'] === 'manual') {
    foreach ($data_manual as $d) {
        $series_label = $d['series_label'] ?? '';
        $multi_series[$series_label][] = $d;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Statistik</title>
    <script>
    // Copy dari toggleForm() dan dynamic input sama seperti tambah.php
    // Tapi ditambahkan logika untuk menampilkan data lama saat load
    function toggleForm() {
        const sumber = document.querySelector('input[name="sumber_data"]:checked').value;
        const tipe = document.querySelector('select[name="tipe_grafik"]').value;

        const csvForm = document.getElementById('form_csv');
        const manualForm = document.getElementById('form_manual');
        const fileInput = document.getElementById('file_csv');
        csvForm.style.display = sumber === 'csv' ? 'block' : 'none';
        manualForm.style.display = sumber === 'manual' ? 'block' : 'none';
        fileInput.disabled = sumber !== 'csv';

        const forPie = document.getElementById('manual_for_pie');
        const forOthers = document.getElementById('manual_for_others');
        forPie.style.display = tipe === 'pie' ? 'block' : 'none';
        forOthers.style.display = tipe !== 'pie' ? 'block' : 'none';
    }

    function tambahBaris(seriesIndex) {
        const container = document.getElementById('series_' + seriesIndex + '_rows');
        const div = document.createElement('div');
        div.innerHTML = `
            <input name="series_label[${seriesIndex}][]" placeholder="Label" required>
            <input name="series_value[${seriesIndex}][]" type="number" step="any" placeholder="Value" required>
            <button type="button" onclick="this.parentElement.remove()">Hapus Baris</button>
        `;
        container.appendChild(div);
    }

    function tambahBarisPie() {
        const container = document.getElementById('manual_container');
        const div = document.createElement('div');
        div.innerHTML = `
            <input name="label[]" placeholder="Label" required>
            <input name="value[]" type="number" step="any" placeholder="Value" required>
            <button type="button" onclick="this.parentElement.remove()">Hapus Baris</button>
        `;
        container.appendChild(div);
    }

    function tambahSeries() {
        const container = document.getElementById('multi_series_container');
        const index = document.querySelectorAll('.series-box').length;
        const div = document.createElement('div');
        div.className = 'series-box';
        div.innerHTML = `
            <strong>Series ${index + 1}</strong>
            <button type="button" onclick="this.parentElement.remove()">Hapus Series</button><br>
            <input name="series_name[]" placeholder="Nama Series" required><br><br>
            <div id="series_${index}_rows">
                <div>
                    <input name="series_label[${index}][]" placeholder="Label" required>
                    <input name="series_value[${index}][]" type="number" step="any" placeholder="Value" required>
                    <button type="button" onclick="this.parentElement.remove()">Hapus Baris</button>
                </div>
            </div>
            <button type="button" onclick="tambahBaris(${index})">+ Tambah Baris</button>
        `;
        container.appendChild(div);
    }

    function loadKategori() {
        const select = document.getElementById('submenuSelect');
        const tipe = select.options[select.selectedIndex].getAttribute('data-tipe');
        if (tipe === 'kategori') {
            document.getElementById('kategoriGroup').style.display = 'block';
            const submenuId = select.value;
            fetch('../../../controllers/kategori.php?submenu_id=' + submenuId)
            .then(res => res.json())
            .then(data => {
                const katSelect = document.getElementById('kategoriSelect');
                katSelect.innerHTML = '';
                data.forEach(k => {
                    katSelect.innerHTML += `<option value="${k.id}" ${k.id == <?= json_encode($statistik['kategori_id']) ?> ? 'selected' : ''}>${k.nama_kategori}</option>`;
                });
            });
        } else {
            document.getElementById('kategoriGroup').style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", toggleForm);
    </script>
</head>
<body>
<h2>Edit Statistik</h2>

<form method="POST" action="../../../controllers/statistik.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $statistik['id'] ?>">

    <label>Submenu</label><br>
    <select name="submenu_id" id="submenuSelect" onchange="loadKategori()" required>
        <?php foreach ($submenus as $s): ?>
        <option value="<?= $s['id'] ?>" data-tipe="<?= $s['tipe_tampilan'] ?>" <?= $s['id'] == $statistik['submenu_id'] ? 'selected' : '' ?>>
            <?= $s['nama_menu'] ?> - <?= $s['nama_submenu'] ?>
        </option>
        <?php endforeach; ?>
    </select><br><br>

    <div id="kategoriGroup" style="<?= $statistik['kategori_id'] ? '' : 'display:none' ?>">
        <label>Kategori</label><br>
        <select name="kategori_id" id="kategoriSelect">
            <?php foreach ($kategori as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $k['id'] == $statistik['kategori_id'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
            <?php endforeach; ?>
        </select><br><br>
    </div>

    <label>Judul Grafik</label><br>
    <input type="text" name="judul" value="<?= htmlspecialchars($statistik['judul']) ?>" required><br><br>

    <label>Deskripsi</label><br>
    <textarea name="deskripsi" rows="4" cols="50"><?= htmlspecialchars($statistik['deskripsi']) ?></textarea><br><br>

    <label>Tipe Grafik</label><br>
    <select name="tipe_grafik" onchange="toggleForm()" required>
        <option value="bar" <?= $statistik['tipe_grafik'] == 'bar' ? 'selected' : '' ?>>Bar</option>
        <option value="line" <?= $statistik['tipe_grafik'] == 'line' ? 'selected' : '' ?>>Line</option>
        <option value="pie" <?= $statistik['tipe_grafik'] == 'pie' ? 'selected' : '' ?>>Pie</option>
    </select><br><br>

    <label>Sumber Data:</label><br>
    <input type="radio" name="sumber_data" value="csv" <?= $statistik['sumber_data'] == 'csv' ? 'checked' : '' ?> onclick="toggleForm()"> CSV
    <input type="radio" name="sumber_data" value="manual" <?= $statistik['sumber_data'] == 'manual' ? 'checked' : '' ?> onclick="toggleForm()"> Manual<br><br>

    <div id="form_csv">
        <?php if ($statistik['file_csv']): ?>
            <p>📎 Saat ini: <?= $statistik['file_csv'] ?></p>
        <?php endif; ?>
        <label>Upload File CSV (jika ingin mengganti)</label><br>
        <input type="file" name="file_csv" accept=".csv" id="file_csv"><br>
    </div>

    <div id="form_manual" style="display:none">
        <div id="manual_for_pie">
            <label>Input Data (Label - Value)</label><br>
            <div id="manual_container">
                <?php if ($statistik['tipe_grafik'] == 'pie'): ?>
                    <?php foreach ($multi_series[''] ?? [] as $d): ?>
                        <div>
                            <input name="label[]" value="<?= htmlspecialchars($d['label']) ?>" required>
                            <input name="value[]" type="number" step="any" value="<?= $d['value'] ?>" required>
                            <button type="button" onclick="this.parentElement.remove()">Hapus Baris</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" onclick="tambahBarisPie()">+ Tambah Baris</button>
        </div>

        <div id="manual_for_others" style="display:none">
            <label>Input Multi-Series</label><br>
            <div id="multi_series_container">
                <?php if ($statistik['tipe_grafik'] != 'pie'): ?>
                    <?php foreach ($multi_series as $series => $items): ?>
                        <div class="series-box">
                            <strong>Series</strong>
                            <button type="button" onclick="this.parentElement.remove()">Hapus Series</button><br>
                            <input name="series_name[]" value="<?= htmlspecialchars($series) ?>" required><br><br>
                            <div id="series_<?= uniqid() ?>_rows">
                                <?php foreach ($items as $d): ?>
                                    <div>
                                        <input name="series_label[<?= $loopIndex = uniqid() ?>][]" value="<?= htmlspecialchars($d['label']) ?>" required>
                                        <input name="series_value[<?= $loopIndex ?>][]" type="number" step="any" value="<?= $d['value'] ?>" required>
                                        <button type="button" onclick="this.parentElement.remove()">Hapus Baris</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" onclick="tambahSeries()">+ Tambah Series</button>
        </div>
    </div><br>

    <button type="submit" name="update">Update Statistik</button>
</form>
</body>
</html>
