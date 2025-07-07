<?php
require_once '../../config/database.php';

$berita = $pdo->query("SELECT * FROM berita ORDER BY tanggal DESC")->fetchAll();

function waktuRelatif($tanggalJam) {
    $detik = time() - strtotime($tanggalJam);
    if ($detik < 60) return "$detik detik yang lalu";
    elseif ($detik < 3600) return floor($detik / 60) . " menit yang lalu";
    elseif ($detik < 86400) return floor($detik / 3600) . " jam yang lalu";
    else return floor($detik / 86400) . " hari yang lalu";
}
?>

<?php include 'partials/header.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Terkini - BARPEDA Prov Lampung</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* News Section */
        .section-title {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        /* News Grid Container */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        /* News Card */
        .news-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            background-color: white;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%; /* Memastikan tinggi semua card sama */
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .news-image-container {
            height: 200px;
            overflow: hidden;
        }

        .news-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .news-card:hover .news-image {
            transform: scale(1.03);
        }

        .news-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .news-title {
            margin: 0 0 12px 0;
            font-size: 18px;
            color: #0056b3;
            line-height: 1.4;
        }

        .news-meta {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 12px;
        }

        .news-meta strong {
            color: #343a40;
        }

        .news-excerpt {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            color: #495057;
            flex: 1;
        }

        .news-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto; /* Memastikan footer tetap di bawah */
        }

        .news-time {
            font-size: 12px;
            color: #6c757d;
            font-style: italic;
        }

        .read-more {
            display: inline-block;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            transition: background-color 0.3s;
        }

        .read-more:hover {
            background-color: #218838;
            text-decoration: none;
        }

        .no-news {
            grid-column: 1 / -1;
            font-size: 16px;
            color: #6c757d;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .news-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .news-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .main-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <main>
            <h2 class="section-title">Berita Terkini</h2>
            
            <div class="news-grid">
                <?php if (count($berita) === 0): ?>
                    <p class="no-news">Tidak ada berita untuk saat ini.</p>
                <?php else: ?>
                    <?php foreach ($berita as $b): ?>
                        <article class="news-card">
                            <div class="news-image-container">
                                <img src="../../uploads/berita/<?= $b['gambar'] ?>" class="news-image" alt="<?= htmlspecialchars($b['judul']) ?>">
                            </div>
                            <div class="news-content">
                                <h3 class="news-title"><?= htmlspecialchars($b['judul']) ?></h3>
                                <div class="news-meta">
                                    <strong><?= htmlspecialchars($b['divisi']) ?></strong> - <?= date('d M Y H:i', strtotime($b['tanggal'])) ?>
                                </div>
                                <p class="news-excerpt"><?= mb_substr(strip_tags($b['isi']), 0, 100) ?>...</p>
                                <div class="news-footer">
                                    <span class="news-time"><?= waktuRelatif($b['tanggal']) ?></span>
                                    <a href="detail_berita.php?id=<?= $b['id'] ?>" class="read-more">Selengkapnya</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>