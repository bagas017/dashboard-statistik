<?php
session_start();
require_once '../../../controllers/carousel.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$carousel = getCarouselById($id);

if (!$carousel) {
    echo "Data tidak ditemukan.";
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $urutan = $_POST['urutan'];
    $gambar = $carousel['gambar'];

    // Cek apakah urutan sudah dipakai oleh ID lain
    $allCarousel = getAllCarousel();
    foreach ($allCarousel as $item) {
        if ($item['urutan'] == $urutan && $item['id'] != $id) {
            $error = "Urutan ke-$urutan sudah digunakan carousel lain. Silakan pilih urutan yang berbeda.";
            break;
        }
    }

    if (!$error) {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $gambar = time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../../../uploads/carousel/' . $gambar);
        }

        updateCarousel($id, $gambar, $urutan);
        header("Location: index.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Carousel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Edit Carousel</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="../../../uploads/carousel/<?= htmlspecialchars($carousel['gambar']) ?>" width="300" class="mb-3">
            </div>

            <div class="mb-3">
                <label class="form-label">Ganti Gambar (opsional):</label>
                <input type="file" class="form-control" name="gambar">
            </div>

            <div class="mb-3">
                <label class="form-label">Urutan Tampil:</label>
                <input type="number" class="form-control" name="urutan" min="1"
                       value="<?= htmlspecialchars($_POST['urutan'] ?? $carousel['urutan']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
