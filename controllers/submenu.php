
<?php
session_start();
require_once '../config/database.php';

// Fungsi generate slug dari nama submenu
function generateSlug($text) {
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return rtrim($slug, '-');
}

// Tambah submenu
if (isset($_POST['tambah'])) {
    $menu = $_POST['nama_menu'] ?? 'beranda';
    $nama = trim($_POST['nama_submenu']);
    $slug = generateSlug($nama);
    $tipe = $_POST['tipe_tampilan'];
    $icon = $_POST['icon_class'] ?? null;

    // Cek apakah submenu dengan nama dan menu yang sama sudah ada
    $cek = $pdo->prepare("SELECT COUNT(*) FROM submenu WHERE nama_menu = ? AND nama_submenu = ?");
    $cek->execute([$menu, $nama]);
    if ($cek->fetchColumn() > 0) {
        $_SESSION['error'] = "Submenu '$nama' sudah ada pada menu '$menu'.";
        header("Location: ../views/admin/submenu/tambah.php");
        exit;
    }

    // Insert jika tidak ada duplikat
    $stmt = $pdo->prepare("INSERT INTO submenu (nama_menu, nama_submenu, slug, tipe_tampilan, icon_class) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$menu, $nama, $slug, $tipe, $icon]);

    // $_SESSION['success'] = "Submenu berhasil ditambahkan.";
    header("Location: ../views/admin/submenu/index.php");
    exit;
}

// Update submenu
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = trim($_POST['nama_submenu']);
    $slug = generateSlug($nama);
    $tipe = $_POST['tipe_tampilan'];
    $icon = $_POST['icon_class'] ?? null;

    // Cek duplikat submenu selain yang sedang diupdate
    $cek = $pdo->prepare("SELECT COUNT(*) FROM submenu WHERE nama_submenu = ? AND id != ?");
    $cek->execute([$nama, $id]);
    if ($cek->fetchColumn() > 0) {
        $_SESSION['error'] = "Nama submenu '$nama' sudah digunakan.";
        header("Location: ../views/admin/submenu/edit.php?id=$id");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE submenu SET nama_submenu = ?, slug = ?, tipe_tampilan = ?, icon_class = ? WHERE id = ?");
    $stmt->execute([$nama, $slug, $tipe, $icon, $id]);

    // $_SESSION['success'] = "Submenu berhasil diperbarui.";
    header("Location: ../views/admin/submenu/index.php");
    exit;
}

// Hapus submenu
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM submenu WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Submenu berhasil dihapus.";
    header("Location: ../views/admin/submenu/index.php");
    exit;
}

