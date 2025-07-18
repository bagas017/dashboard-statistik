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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - Admin Dashboard</title>
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
        
        /* Image Selection Styling */
        .gambar-selection {
            margin-top: 15px;
        }
        
        .gambar-option {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            background-color: white;
            margin-bottom: 10px;
            height: 100%;
        }
        
        .gambar-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }
        
        .gambar-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }
        
        .gambar-option input[type="radio"] {
            display: none;
        }
        
        .gambar-option img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
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
        
        /* Word Counter */
        .word-counter {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .word-counter.limit-reached {
            color: var(--danger-color);
            font-weight: 500;
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
            
            .gambar-option {
                padding: 10px;
            }
            
            .gambar-option img {
                height: 80px;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '../../partials/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="bi bi-plus-circle me-2"></i>Tambah Kategori</h2>
    </div>

    <div class="form-container">
        <div class="form-card">
            <h3 class="form-title">Tambah Kategori Baru</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="../../../controllers/kategori.php">
                <div class="mb-4">
                    <label class="form-label">Submenu (Lokasi)</label>
                    <select name="submenu_id" class="form-select" required>
                        <option value="">-- Pilih Submenu --</option>
                        <?php foreach ($submenus as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_submenu']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi Singkat (maks. 25 kata)</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required></textarea>
                    <div class="word-counter mt-1"><span id="wordCount">0 / 25 kata</span></div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Pilih Gambar</label>
                    <div class="gambar-selection">
                        <div class="row g-3">
                            <?php for ($i = 1; $i <= 15; $i++): ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                    <label class="gambar-option d-block">
                                        <input type="radio" name="gambar" value="kategori<?= $i ?>.jpg" <?= $i === 1 ? 'checked' : '' ?>>
                                        <img src="../../../assets/kategori/kategori<?= $i ?>.jpg" alt="Kategori <?= $i ?>">
                                    </label>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Image selection
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

        // Word counter
        const deskripsiInput = document.getElementById('deskripsi');
        const wordCount = document.getElementById('wordCount');
        const wordCounter = document.querySelector('.word-counter');
        
        deskripsiInput.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/).filter(Boolean);
            wordCount.textContent = words.length + " / 25 kata";
            
            if (words.length >= 25) {
                wordCounter.classList.add('limit-reached');
                if (words.length > 25) {
                    this.value = words.slice(0, 25).join(" ");
                }
            } else {
                wordCounter.classList.remove('limit-reached');
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>