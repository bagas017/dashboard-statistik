<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Submenu</title></head>
<body>
<h2>Tambah Submenu (Beranda)</h2>
<form method="POST" action="../../../controllers/submenu.php">
    <input type="hidden" name="nama_menu" value="beranda">
    <label>Nama Submenu</label><br>
    <input type="text" name="nama_submenu" required><br>
    <input type="hidden" name="slug">
    <label>Tipe Tampilan</label><br>
    <select name="tipe_tampilan">
        <option value="langsung">Langsung</option>
        <option value="kategori">Kategori</option>
    </select><br><br>
    <button type="submit" name="tambah">Simpan</button>
</form>
</body>
</html>
