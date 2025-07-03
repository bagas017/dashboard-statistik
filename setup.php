<?php
$host = 'localhost:3308';
$user = 'root';
$pass = ''; // ganti sesuai konfigurasi MySQL kamu
$dbname = 'dashboard_statistik';

try {
    // Koneksi ke MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buat database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Database '$dbname' dibuat atau sudah ada.<br>";

    // Koneksi ke database yang baru dibuat
    $pdo->exec("USE $dbname");

    // Buat tabel admin
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL
    )");

    // Tambahkan admin default
    $hashedPassword = password_hash("admin123", PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->execute(['admin', $hashedPassword]);
    echo "Admin default dibuat: admin / admin123<br>";

    // Tambahan: buat tabel submenu, kategori, statistik, galeri, berita, agenda
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS submenu (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_menu ENUM('beranda', 'galeri', 'agenda', 'berita') NOT NULL,
            nama_submenu VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            tipe_tampilan ENUM('langsung', 'kategori') DEFAULT 'langsung'
        );
        
        CREATE TABLE IF NOT EXISTS kategori (
            id INT AUTO_INCREMENT PRIMARY KEY,
            submenu_id INT NOT NULL,
            nama_kategori VARCHAR(100) NOT NULL,
            FOREIGN KEY (submenu_id) REFERENCES submenu(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS statistik (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kategori_id INT NOT NULL,
            judul VARCHAR(100) NOT NULL,
            tipe_grafik ENUM('bar', 'line', 'pie') NOT NULL,
            sumber_data ENUM('csv', 'manual') NOT NULL,
            file_csv VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS statistik_data_manual (
            id INT AUTO_INCREMENT PRIMARY KEY,
            statistik_id INT NOT NULL,
            label VARCHAR(100) NOT NULL,
            value FLOAT NOT NULL,
            FOREIGN KEY (statistik_id) REFERENCES statistik(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS galeri (
            id INT AUTO_INCREMENT PRIMARY KEY,
            jenis ENUM('foto','video') NOT NULL,
            judul VARCHAR(100),
            file_path VARCHAR(255),
            deskripsi TEXT,
            tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS berita (
            id INT AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(200),
            gambar VARCHAR(255),
            isi TEXT,
            tanggal DATE
        );

        CREATE TABLE IF NOT EXISTS agenda (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_agenda VARCHAR(150),
            tanggal DATE,
            jam_mulai TIME,
            jam_selesai TIME,
            lokasi VARCHAR(100)
        );
    ");
    
    // Modifikasi struktur tabel statistik
    $pdo->exec("ALTER TABLE statistik MODIFY COLUMN kategori_id INT NULL");
    
    // Tambahkan submenu_id jika belum ada
    $stmt = $pdo->query("SHOW COLUMNS FROM statistik LIKE 'submenu_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE statistik ADD COLUMN submenu_id INT NULL AFTER kategori_id");
    }
    
    // Tambahkan series_label jika belum ada
    $stmt = $pdo->query("SHOW COLUMNS FROM statistik_data_manual LIKE 'series_label'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE statistik_data_manual ADD COLUMN series_label VARCHAR(255) DEFAULT NULL");
    }
    
    // Tambahkan deskripsi jika belum ada
    $stmt = $pdo->query("SHOW COLUMNS FROM statistik LIKE 'deskripsi'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE statistik ADD COLUMN deskripsi TEXT DEFAULT NULL");
    }
    
    // Tambahkan deskripsi dan gambar ke tabel kategori
    $stmt = $pdo->query("SHOW COLUMNS FROM kategori LIKE 'deskripsi'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE kategori ADD COLUMN deskripsi TEXT");
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM kategori LIKE 'gambar'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE kategori ADD COLUMN gambar VARCHAR(255)");
    }
    
    echo "Seluruh tabel berhasil dibuat.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>