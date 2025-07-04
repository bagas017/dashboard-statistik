<?php
session_start();
require_once '../../../controllers/carousel.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $urutan = $_POST['urutan'];
    $gambar = '';

    // Cek apakah urutan sudah dipakai
    $carousel = getAllCarousel();
    foreach ($carousel as $c) {
        if ($c['urutan'] == $urutan) {
            $error = "Urutan ke-$urutan sudah dipakai. Silakan pilih urutan lain.";
            break;
        }
    }

    if (!$error) {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar = time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/carousel/' . $gambar);
        }

        tambahCarousel($gambar, $urutan);
        header("Location: index.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Carousel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Tambah Gambar Carousel</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Carousel:</label>
                <input type="file" class="form-control" name="gambar" id="gambar" required>
            </div>

            <div class="mb-3">
                <label for="urutan" class="form-label">Urutan Tampil:</label>
                <input type="number" class="form-control" name="urutan" id="urutan"
                       min="1" value="<?= htmlspecialchars($_POST['urutan'] ?? 1) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
            