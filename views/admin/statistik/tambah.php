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
    <title>Tambah Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .series-box {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body onload="toggleForm()" class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">Tambah Statistik</h2>
    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="../../../controllers/statistik.php" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Submenu</label>
                    <select class="form-select" name="submenu_id" id="submenuSelect" onchange="loadKategori()" required>
                        <option value="">-- Pilih Submenu --</option>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM submenu");
                        $stmt->execute();
                        foreach ($stmt->fetchAll() as $s):
                        ?>
                            <option value="<?= $s['id'] ?>" data-tipe="<?= $s['tipe_tampilan'] ?>">
                                <?= $s['nama_menu'] ?> - <?= $s['nama_submenu'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="kategoriGroup" style="display:none" class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" id="kategoriSelect" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Judul Grafik</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="form-control" placeholder="Tambahkan deskripsi grafik..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Grafik</label>
                    <select name="tipe_grafik" class="form-select" onchange="toggleForm()" required>
                        <option value="bar">Bar</option>
                        <option value="line">Line</option>
                        <option value="pie">Pie</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sumber Data</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sumber_data" value="csv" checked onclick="toggleForm()">
                        <label class="form-check-label">CSV</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sumber_data" value="manual" onclick="toggleForm()">
                        <label class="form-check-label">Manual</label>
                    </div>
                </div>

                <div id="form_csv" class="mb-3">
                    <label class="form-label">Upload File CSV</label>
                    <input type="file" name="file_csv" accept=".csv" class="form-control" id="file_csv">
                </div>

                <div id="form_manual" style="display:none">
                    <div id="manual_for_pie" class="mb-3">
                        <label class="form-label">Input Data (Label - Value)</label>
                        <div id="manual_container" class="mb-2"></div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="tambahBarisPie()">+ Tambah Baris</button>
                    </div>

                    <div id="manual_for_others" class="mb-3" style="display:none">
                        <label class="form-label">Input Multi-Series</label>
                        <div id="multi_series_container" class="mb-2"></div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="tambahSeries()">+ Tambah Series</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary" name="tambah">Simpan Statistik</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleForm() {
    const sumber = document.querySelector('input[name="sumber_data"]:checked').value;
    const tipe = document.querySelector('select[name="tipe_grafik"]').value;
    const csvForm = document.getElementById('form_csv');
    const manualForm = document.getElementById('form_manual');
    const fileInput = document.getElementById('file_csv');
    csvForm.style.display = sumber === 'csv' ? 'block' : 'none';
    manualForm.style.display = sumber === 'manual' ? 'block' : 'none';
    fileInput.disabled = sumber !== 'csv';

    document.getElementById('manual_for_pie').style.display = tipe === 'pie' ? 'block' : 'none';
    document.getElementById('manual_for_others').style.display = tipe !== 'pie' ? 'block' : 'none';

    document.querySelectorAll('#form_manual input').forEach(input => input.removeAttribute('required'));

    if (sumber === 'manual') {
        const selector = tipe === 'pie' ? '#manual_for_pie input' : '#manual_for_others input';
        document.querySelectorAll(selector).forEach(input => input.setAttribute('required', 'required'));
    }

    if (sumber === 'csv') fileInput.setAttribute('required', 'required');
    else fileInput.removeAttribute('required');
}

function tambahBarisPie() {
    const container = document.getElementById('manual_container');
    const div = document.createElement('div');
    div.className = "d-flex gap-2 mb-2";
    div.innerHTML = `
        <input name="label[]" class="form-control" placeholder="Label" required>
        <input name="value[]" type="number" step="any" class="form-control" placeholder="Value" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">Hapus</button>
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
        <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="this.parentElement.remove()">Hapus Series</button><br><br>
        <input name="series_name[]" class="form-control mb-2" placeholder="Nama Series" required>
        <div id="series_${index}_rows" class="mb-2">
            <div class="d-flex gap-2 mb-2">
                <input name="series_label[${index}][]" class="form-control" placeholder="Label" required>
                <input name="series_value[${index}][]" type="number" step="any" class="form-control" placeholder="Value" required>
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">Hapus</button>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary" onclick="tambahBaris(${index})">+ Tambah Baris</button>
    `;
    container.appendChild(div);
}

function tambahBaris(seriesIndex) {
    const container = document.getElementById(`series_${seriesIndex}_rows`);
    const div = document.createElement('div');
    div.className = "d-flex gap-2 mb-2";
    div.innerHTML = `
        <input name="series_label[${seriesIndex}][]" class="form-control" placeholder="Label" required>
        <input name="series_value[${seriesIndex}][]" type="number" step="any" class="form-control" placeholder="Value" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">Hapus</button>
    `;
    container.appendChild(div);
}

function loadKategori() {
    const select = document.getElementById('submenuSelect');
    const tipe = select.options[select.selectedIndex].getAttribute('data-tipe');
    const group = document.getElementById('kategoriGroup');
    const katSelect = document.getElementById('kategoriSelect');

    if (tipe === 'kategori') {
        group.style.display = 'block';
        fetch('../../../controllers/kategori.php?submenu_id=' + select.value)
            .then(res => res.json())
            .then(data => {
                katSelect.innerHTML = '';

                if (data.length === 0) {
                    katSelect.innerHTML = '<option value="">Belum ada kategori yang ditambahkan</option>';
                    katSelect.setAttribute('disabled', 'disabled');
                } else {
                    data.forEach(k => {
                        katSelect.innerHTML += `<option value="${k.id}">${k.nama_kategori}</option>`;
                    });
                    katSelect.removeAttribute('disabled');
                }

            });
    } else {
        group.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelector("form").addEventListener("submit", function(e) {
        const labels = [
            ...document.querySelectorAll('input[name="label[]"]'),
            ...document.querySelectorAll('input[name^="series_label"]')
        ];
        for (const input of labels) {
            const value = input.value.trim();
            if (/^\d+$/.test(value)) {
                alert("Label tidak boleh hanya angka. Contoh: gunakan 'Tahun 2023' bukan '" + value + "'");
                input.focus();
                e.preventDefault();
                return;
            }
        }
    });
});
</script>
</body>
</html>
