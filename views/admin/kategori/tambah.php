<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil submenu dari beranda
$stmt = $pdo->prepare("SELECT * FROM submenu WHERE nama_menu = 'beranda'");
$stmt->execute();
$submenus = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Kategori</title></head>
<body>
<h2>Tambah Kategori</h2>
<form method="POST" action="../../../controllers/kategori.php">
    <label>Submenu</label><br>
    <select name="submenu_id" required>
        <option value="">-- Pilih Submenu --</option>
        <?php foreach ($submenus as $s): ?>
            <option value="<?= $s['id'] ?>"><?= $s['nama_submenu'] ?></option>
        <?php endforeach; ?>
    </select><br><br>
    
    <label>Nama Kategori</label><br>
    <input type="text" name="nama_kategori" required><br><br>

    <button type="submit" name="tambah">Simpan</button>
</form>
</body>
</html>
