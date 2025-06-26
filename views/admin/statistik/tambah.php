<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT kategori.*, submenu.nama_submenu FROM kategori INNER JOIN submenu ON kategori.submenu_id = submenu.id");
$stmt->execute();
$kategori = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Statistik</title>
    <script>
    function toggleForm() {
        const sumber = document.querySelector('input[name="sumber_data"]:checked').value;
        document.getElementById('form_csv').style.display = sumber === 'csv' ? 'block' : 'none';
        document.getElementById('form_manual').style.display = sumber === 'manual' ? 'block' : 'none';
    }
    function tambahBaris() {
        const container = document.getElementById('manual_container');
        const div = document.createElement('div');
        div.innerHTML = '<input name="label[]" placeholder="Label" required> <input name="value[]" type="number" step="any" placeholder="Value" required> <br>';
        container.appendChild(div);
    }
    </script>
</head>
<body onload="toggleForm()">
<h2>Tambah Statistik</h2>
<form method="POST" action="../../../controllers/statistik.php" enctype="multipart/form-data">
    <label>Kategori</label><br>
    <select name="kategori_id" required>
        <?php foreach ($kategori as $k): ?>
            <option value="<?= $k['id'] ?>"><?= $k['nama_submenu'] ?> - <?= $k['nama_kategori'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Judul Grafik</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Tipe Grafik</label><br>
    <select name="tipe_grafik" required>
        <option value="bar">Bar</option>
        <option value="line">Line</option>
        <option value="pie">Pie</option>
    </select><br><br>

    <label>Sumber Data:</label><br>
    <input type="radio" name="sumber_data" value="csv" checked onclick="toggleForm()"> CSV
    <input type="radio" name="sumber_data" value="manual" onclick="toggleForm()"> Manual<br><br>

    <div id="form_csv">
        <label>Upload File CSV (Label,Value)</label><br>
        <input type="file" name="file_csv" accept=".csv" id="file_csv"><br>
    </div>

    <div id="form_manual" style="display:none">
        <label>Input Manual</label><br>
        <div id="manual_container">
            <div><input name="label[]" placeholder="Label" required> <input name="value[]" type="number" step="any" placeholder="Value" required></div>
        </div>
        <button type="button" onclick="tambahBaris()">+ Tambah Baris</button><br>
    </div><br>

    <button type="submit" name="tambah">Simpan Statistik</button>
</form>
</body>

<script>
function toggleForm() {
    const sumber = document.querySelector('input[name="sumber_data"]:checked').value;
    const csvForm = document.getElementById('form_csv');
    const manualForm = document.getElementById('form_manual');
    const fileInput = document.getElementById('file_csv');

    csvForm.style.display = sumber === 'csv' ? 'block' : 'none';
    manualForm.style.display = sumber === 'manual' ? 'block' : 'none';

    // toggle required
    if (sumber === 'csv') {
        fileInput.setAttribute('required', true);
    } else {
        fileInput.removeAttribute('required');
    }
}
</script>

</html>

