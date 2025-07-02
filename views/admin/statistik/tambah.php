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
    <title>Tambah Statistik</title>
    <script>
    function toggleForm() {
        const sumber = document.querySelector('input[name="sumber_data"]:checked').value;
        const tipe = document.querySelector('select[name="tipe_grafik"]').value;

        // tampil/sembunyikan form CSV/manual
        const csvForm = document.getElementById('form_csv');
        const manualForm = document.getElementById('form_manual');
        const fileInput = document.getElementById('file_csv');
        csvForm.style.display = sumber === 'csv' ? 'block' : 'none';
        manualForm.style.display = sumber === 'manual' ? 'block' : 'none';
        fileInput.disabled = sumber !== 'csv';

        // tampilkan form input manual berdasarkan tipe grafik
        const forPie = document.getElementById('manual_for_pie');
        const forOthers = document.getElementById('manual_for_others');
        forPie.style.display = tipe === 'pie' ? 'block' : 'none';
        forOthers.style.display = tipe !== 'pie' ? 'block' : 'none';

        // reset semua input manual agar tidak required
        document.querySelectorAll('#form_manual input').forEach(input => {
            input.removeAttribute('required');
        });

        // hanya aktifkan required untuk input yang tampil
        if (sumber === 'manual') {
            if (tipe === 'pie') {
                document.querySelectorAll('#manual_for_pie input').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            } else {
                document.querySelectorAll('#manual_for_others input').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            }
        }

        // jika sumber csv, pastikan file input required
        if (sumber === 'csv') {
            fileInput.setAttribute('required', 'required');
        } else {
            fileInput.removeAttribute('required');
        }
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
        const selected = select.options[select.selectedIndex];
        const tipe = selected.getAttribute('data-tipe');

        if (tipe === 'kategori') {
            document.getElementById('kategoriGroup').style.display = 'block';
            const submenuId = select.value;

            fetch('../../../controllers/kategori.php?submenu_id=' + submenuId)
            .then(res => res.json())
            .then(data => {
                const katSelect = document.getElementById('kategoriSelect');
                katSelect.innerHTML = '';
                data.forEach(k => {
                    katSelect.innerHTML += `<option value="${k.id}">${k.nama_kategori}</option>`;
                });
            });
        } else {
            document.getElementById('kategoriGroup').style.display = 'none';
        }
    }

    // VALIDASI: Cek label tidak boleh hanya angka
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelector("form").addEventListener("submit", function(e) {
            const labels = [
                ...document.querySelectorAll('input[name="label[]"]'),
                ...document.querySelectorAll('input[name^="series_label"]')
            ];

            for (const input of labels) {
                const value = input.value.trim();
                if (/^\d+$/.test(value)) {
                    alert("Label tidak boleh hanya angka. Misalnya: gunakan 'Tahun 2020' bukan '" + value + "' saja.");
                    input.focus();
                    e.preventDefault();
                    return;
                }
            }
        });
    });
    </script>
</head>
<body onload="toggleForm()">
<h2>Tambah Statistik</h2>

<form method="POST" action="../../../controllers/statistik.php" enctype="multipart/form-data">

    <label>Submenu</label><br>
    <select name="submenu_id" id="submenuSelect" onchange="loadKategori()" required>
        <option value="">-- Pilih Submenu --</option>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM submenu");
        $stmt->execute();
        $submenus = $stmt->fetchAll();
        foreach ($submenus as $s):
        ?>
            <option value="<?= $s['id'] ?>" data-tipe="<?= $s['tipe_tampilan'] ?>"><?= $s['nama_menu'] ?> - <?= $s['nama_submenu'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <div id="kategoriGroup" style="display:none">
        <label>Kategori</label><br>
        <select name="kategori_id" id="kategoriSelect">
            <option value="">-- Pilih Kategori --</option>
        </select><br><br>
    </div>

    <label>Judul Grafik</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Deskripsi</label><br>
    <textarea name="deskripsi" rows="4" cols="50" placeholder="Tambahkan deskripsi grafik..." required></textarea><br><br>

    <label>Tipe Grafik</label><br>
    <select name="tipe_grafik" onchange="toggleForm()" required>
        <option value="bar">Bar</option>
        <option value="line">Line</option>
        <option value="pie">Pie</option>
    </select><br><br>

    <label>Sumber Data:</label><br>
    <input type="radio" name="sumber_data" value="csv" checked onclick="toggleForm()"> CSV
    <input type="radio" name="sumber_data" value="manual" onclick="toggleForm()"> Manual<br><br>

    <div id="form_csv">
        <label>Upload File CSV</label><br>
        <input type="file" name="file_csv" accept=".csv" id="file_csv"><br>
    </div>

    <div id="form_manual" style="display:none">
        <div id="manual_for_pie">
            <label>Input Data (Label - Value)</label><br>
            <div id="manual_container"></div>
            <button type="button" onclick="tambahBarisPie()">+ Tambah Baris</button>
        </div>

        <div id="manual_for_others" style="display:none">
            <label>Input Multi-Series</label><br>
            <div id="multi_series_container"></div>
            <button type="button" onclick="tambahSeries()">+ Tambah Series</button>
        </div>
    </div><br>

    <button type="submit" name="tambah">Simpan Statistik</button>
</form>
</body>
</html>
