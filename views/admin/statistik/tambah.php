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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Statistik - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --danger-color: #f72585;
            --warning-color: #f77f00;
            --success-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 280px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 250px;
            z-index: 1000;
            background-color: #f8f9fa;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        
        /* Main Content Styling */
        .content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        /* Form Card Styling */
        .form-container {
            max-width: 2500px;
        }
        
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
            border: none;
        }
        
        .form-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        /* Series Box Styling */
        .series-box {
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }
        
        .series-box:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        /* Button Styling */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-outline-danger {
            border-color: var(--danger-color);
            color: var(--danger-color);
        }
        
        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        /* Alert Styling */
        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
            
            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="bi bi-plus-circle me-2"></i>Tambah Statistik</h2>
    </div>

    <div class="form-container">
        <div class="form-card">
            <h3 class="form-title">Form Tambah Statistik</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="../../../controllers/statistik.php" enctype="multipart/form-data">
                <div class="mb-4">
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

                <div id="kategoriGroup" style="display:none" class="mb-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" id="kategoriSelect" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Judul Grafik</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" class="form-control" placeholder="Tambahkan deskripsi grafik..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Tipe Grafik</label>
                    <select name="tipe_grafik" class="form-select" onchange="toggleForm()" required>
                        <option value="bar">Bar</option>
                        <option value="line">Line</option>
                        <option value="pie">Pie</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Sumber Data</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sumber_data" id="csvRadio" value="csv" checked onclick="toggleForm()">
                        <label class="form-check-label" for="csvRadio">CSV</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sumber_data" id="manualRadio" value="manual" onclick="toggleForm()">
                        <label class="form-check-label" for="manualRadio">Manual</label>
                    </div>
                </div>

                <div id="form_csv" class="mb-4">
                    <label class="form-label">Upload File CSV</label>
                    <input type="file" name="file_csv" accept=".csv" class="form-control" id="file_csv">
                </div>

                <div id="form_manual" style="display:none">
                    <div id="manual_for_pie" class="mb-4">
                        <label class="form-label">Input Data (Label - Value)</label>
                        <div id="manual_container" class="mb-3"></div>
                        <button type="button" class="btn btn-secondary" onclick="tambahBarisPie()">
                            <i class="bi bi-plus-lg"></i> Tambah Baris
                        </button>
                    </div>

                    <div id="manual_for_others" class="mb-4" style="display:none">
                        <label class="form-label">Input Multi-Series</label>
                        <div id="multi_series_container" class="mb-3"></div>
                        <button type="button" class="btn btn-secondary" onclick="tambahSeries()">
                            <i class="bi bi-plus-lg"></i> Tambah Series
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" name="tambah">
                        <i class="bi bi-save"></i> Simpan Statistik
                    </button>
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
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
            <i class="bi bi-trash"></i> Hapus
        </button>
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
        <button type="button" class="btn btn-sm btn-outline-danger float-end" onclick="this.parentElement.remove()">
            <i class="bi bi-trash"></i> Hapus Series
        </button><br><br>
        <input name="series_name[]" class="form-control mb-3" placeholder="Nama Series" required>
        <div id="series_${index}_rows" class="mb-3">
            <div class="d-flex gap-2 mb-2">
                <input name="series_label[${index}][]" class="form-control" placeholder="Label" required>
                <input name="series_value[${index}][]" type="number" step="any" class="form-control" placeholder="Value" required>
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="tambahBaris(${index})">
            <i class="bi bi-plus-lg"></i> Tambah Baris
        </button>
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
        <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
            <i class="bi bi-trash"></i> Hapus
        </button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>