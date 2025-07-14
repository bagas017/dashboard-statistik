<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM statistik WHERE id = ?");
$stmt->execute([$id]);
$statistik = $stmt->fetch();

if (!$statistik) {
    echo "Data tidak ditemukan.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM statistik_data_manual WHERE statistik_id = ?");
$stmt->execute([$id]);
$data_manual = $stmt->fetchAll();

$submenus = $pdo->query("SELECT * FROM submenu")->fetchAll();

$kategori = [];
if ($statistik['submenu_id']) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE submenu_id = ?");
    $stmt->execute([$statistik['submenu_id']]);
    $kategori = $stmt->fetchAll();
}

$multi_series = [];
if ($statistik['sumber_data'] === 'manual') {
    foreach ($data_manual as $d) {
        $series_label = $d['series_label'] ?? '';
        $multi_series[$series_label][] = $d;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Statistik - Admin Dashboard</title>
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
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Data Input Styling */
        .data-input-container {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .series-box {
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .data-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .data-row input {
            flex: 1;
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
            
            .data-row {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="bi bi-pencil-square me-2"></i>Edit Statistik</h2>
    </div>

    <div class="form-container">
        <div class="form-card">
            <h3 class="form-title">Edit Data Statistik</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="../../../controllers/statistik.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $statistik['id'] ?>">

                <div class="mb-4">
                    <label class="form-label">Submenu</label>
                    <select name="submenu_id" id="submenuSelect" class="form-select" onchange="loadKategori()" required>
                        <?php foreach ($submenus as $s): ?>
                        <option value="<?= $s['id'] ?>" data-tipe="<?= $s['tipe_tampilan'] ?>" <?= $s['id'] == $statistik['submenu_id'] ? 'selected' : '' ?>>
                            <?= $s['nama_menu'] ?> - <?= $s['nama_submenu'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="kategoriGroup" class="mb-4" style="<?= $statistik['kategori_id'] ? '' : 'display:none' ?>">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" id="kategoriSelect" class="form-select">
                        <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $k['id'] == $statistik['kategori_id'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Judul Grafik</label>
                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($statistik['judul']) ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($statistik['deskripsi']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Tipe Grafik</label>
                    <select name="tipe_grafik" class="form-select" onchange="toggleForm()" required>
                        <option value="bar" <?= $statistik['tipe_grafik'] == 'bar' ? 'selected' : '' ?>>Bar</option>
                        <option value="line" <?= $statistik['tipe_grafik'] == 'line' ? 'selected' : '' ?>>Line</option>
                        <option value="pie" <?= $statistik['tipe_grafik'] == 'pie' ? 'selected' : '' ?>>Pie</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Sumber Data:</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sumber_data" id="sumber_csv" value="csv" <?= $statistik['sumber_data'] == 'csv' ? 'checked' : '' ?> onclick="toggleForm()">
                            <label class="form-check-label" for="sumber_csv">CSV</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sumber_data" id="sumber_manual" value="manual" <?= $statistik['sumber_data'] == 'manual' ? 'checked' : '' ?> onclick="toggleForm()">
                            <label class="form-check-label" for="sumber_manual">Manual</label>
                        </div>
                    </div>
                </div>

                <div id="form_csv" class="data-input-container" style="display: none">
                    <?php if ($statistik['file_csv']): ?>
                        <p class="mb-3"><i class="bi bi-paperclip me-2"></i>Saat ini: <?= $statistik['file_csv'] ?></p>
                    <?php endif; ?>
                    <label class="form-label">Upload File CSV (jika ingin mengganti)</label>
                    <input type="file" name="file_csv" class="form-control" accept=".csv" id="file_csv">
                </div>

                <div id="form_manual" class="data-input-container" style="display: none">
                    <div id="manual_for_pie">
                        <label class="form-label">Input Data (Label - Value)</label>
                        <div id="manual_container">
                            <?php if ($statistik['tipe_grafik'] == 'pie'): ?>
                                <?php foreach ($multi_series[''] ?? [] as $d): ?>
                                    <div class="data-row">
                                        <input name="label[]" class="form-control" value="<?= htmlspecialchars($d['label']) ?>" placeholder="Label" required>
                                        <input name="value[]" class="form-control" type="number" step="any" value="<?= $d['value'] ?>" placeholder="Value" required>
                                        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" onclick="tambahBarisPie()">
                            <i class="bi bi-plus"></i> Tambah Baris
                        </button>
                    </div>

                    <div id="manual_for_others" style="display: none">
                        <label class="form-label">Input Multi-Series</label>
                        <div id="multi_series_container">
                            <?php if ($statistik['tipe_grafik'] != 'pie'): ?>
                                <?php 
                                    $seriesIndex = 0;
                                    foreach ($multi_series as $series => $items): 
                                ?>
                                    <div class="series-box">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Series <?= $seriesIndex + 1 ?></h5>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">
                                                <i class="bi bi-trash"></i> Hapus Series
                                            </button>
                                        </div>
                                        <input name="series_name[]" class="form-control mb-3" value="<?= htmlspecialchars($series) ?>" placeholder="Nama Series" required>
                                        <div id="series_<?= $seriesIndex ?>_rows">
                                            <?php foreach ($items as $d): ?>
                                                <div class="data-row">
                                                    <input name="series_label[<?= $seriesIndex ?>][]" class="form-control" value="<?= htmlspecialchars($d['label']) ?>" placeholder="Label" required>
                                                    <input name="series_value[<?= $seriesIndex ?>][]" class="form-control" type="number" step="any" value="<?= $d['value'] ?>" placeholder="Value" required>
                                                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-primary mt-2" onclick="tambahBaris(<?= $seriesIndex ?>)">
                                            <i class="bi bi-plus"></i> Tambah Baris
                                        </button>
                                    </div>
                                <?php 
                                    $seriesIndex++;
                                    endforeach; 
                                ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="tambahSeries()">
                            <i class="bi bi-plus"></i> Tambah Series
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Statistik
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

        const forPie = document.getElementById('manual_for_pie');
        const forOthers = document.getElementById('manual_for_others');
        forPie.style.display = tipe === 'pie' ? 'block' : 'none';
        forOthers.style.display = tipe !== 'pie' ? 'block' : 'none';
    }

    function tambahBaris(seriesIndex) {
        const container = document.getElementById('series_' + seriesIndex + '_rows');
        const div = document.createElement('div');
        div.className = 'data-row';
        div.innerHTML = `
            <input name="series_label[${seriesIndex}][]" class="form-control" placeholder="Label" required>
            <input name="series_value[${seriesIndex}][]" class="form-control" type="number" step="any" placeholder="Value" required>
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
    }

    function tambahBarisPie() {
        const container = document.getElementById('manual_container');
        const div = document.createElement('div');
        div.className = 'data-row';
        div.innerHTML = `
            <input name="label[]" class="form-control" placeholder="Label" required>
            <input name="value[]" class="form-control" type="number" step="any" placeholder="Value" required>
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
    }

    function tambahSeries() {
        const container = document.getElementById('multi_series_container');
        const index = document.querySelectorAll('.series-box').length;
        const div = document.createElement('div');
        div.className = 'series-box';
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Series ${index + 1}</h5>
                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">
                    <i class="bi bi-trash"></i> Hapus Series
                </button>
            </div>
            <input name="series_name[]" class="form-control mb-3" placeholder="Nama Series" required>
            <div id="series_${index}_rows">
                <div class="data-row">
                    <input name="series_label[${index}][]" class="form-control" placeholder="Label" required>
                    <input name="series_value[${index}][]" class="form-control" type="number" step="any" placeholder="Value" required>
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-2" onclick="tambahBaris(${index})">
                <i class="bi bi-plus"></i> Tambah Baris
            </button>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>