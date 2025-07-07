<?php
require_once '../../config/database.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->execute([$id]);
$berita = $stmt->fetch();

if (!$berita) {
    echo "Berita tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($berita['judul']) ?> - BAPPEDA Prov Lampung</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Container */
        .news-detail-container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #0056b3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #003d7a;
        }

        .back-button::before {
            content: "←";
            margin-right: 8px;
        }

        /* News Header */
        .news-title {
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 28px;
            line-height: 1.3;
        }

        .news-meta {
            color: #6c757d;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .news-date {
            font-size: 14px;
            display: block;
            margin-top: 5px;
        }

        /* News Content */
        .news-image {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .news-content {
            font-size: 16px;
            line-height: 1.8;
            text-align: justify; /* Ini bagian yang ditambahkan */
        }

        .news-content p {
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .news-detail-container {
                padding: 20px;
            }

            .news-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <div class="news-detail-container">
        <!-- Tombol Kembali -->
        <a href="berita.php" class="back-button">Kembali ke halaman berita</a>

        <!-- Judul Berita -->
        <h1 class="news-title"><?= htmlspecialchars($berita['judul']) ?></h1>
        
        <!-- Meta Berita -->
        <div class="news-meta">
            <strong><?= htmlspecialchars($berita['divisi']) ?></strong>
            <span class="news-date"><?= date('l, d F Y', strtotime($berita['tanggal'])) ?></span>
        </div>

        <!-- Gambar Berita -->
        <img src="../../uploads/berita/<?= $berita['gambar'] ?>" class="news-image" alt="<?= htmlspecialchars($berita['judul']) ?>">

        <!-- Isi Berita -->
        <div class="news-content">
            <?= nl2br(htmlspecialchars($berita['isi'])) ?>
        </div>
    </div>
</body>
</html>
