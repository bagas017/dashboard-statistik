<?php
require_once '../../config/database.php';

$filter = $_GET['jenis'] ?? 'semua';
$expanded = isset($_GET['expanded']);

$query = "SELECT * FROM galeri";
if ($filter === 'foto') {
    $query .= " WHERE jenis = 'foto'";
} elseif ($filter === 'video') {
    $query .= " WHERE jenis = 'video'";
}
$query .= " ORDER BY tanggal_upload DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$galeri = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Galeri - Bappeda Prov Lampung</title>
  <style>
    /* CSS Styles */
    body {
      font-family: sans-serif;
      background-color: #f1f1f1;
      margin: 0;
      padding: 0;
    }

    header {
      display: flex;
      align-items: center;
      padding: 1rem 2rem;
      background-color: white;
      border-bottom: 1px solid #ccc;
    }

    .logo {
      font-weight: bold;
      margin-right: auto;
    }

    .nav a {
        padding: 0.5rem 1rem;
        background-color: #e2e2e2;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
        color: #000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav a.active {
        background-color: #666;
        color: white;
    }

    .container {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .galeri-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .galeri-header h2 {
      margin: 0;
      font-size: 2rem;
      color: #333;
    }

    .filter-form {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .filter-form select {
      padding: 0.5rem;
      font-size: 1rem;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .galeri-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      max-height: calc(4 * (250px + 1rem)); /* Maksimal 4 baris */
      overflow: hidden;
    }

    .galeri-grid.expanded {
      max-height: none;
    }

    .media-container {
      position: relative;
      overflow: hidden;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
    }

    .media-container:hover {
      transform: translateY(-5px);
    }

    .media-container img, 
    .media-container iframe, 
    .media-container video {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
      display: block;
    }

    .media-container:hover img,
    .media-container:hover video {
      transform: scale(1.05);
    }

    .media-container .ratio-16-9 {
      aspect-ratio: 16/9;
    }

    .media-container .ratio-9-16 {
      aspect-ratio: 9/16;
    }

    .media-title {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(0,0,0,0.7));
      color: white;
      padding: 1rem 0.5rem 0.5rem;
      margin: 0;
      font-size: 0.9rem;
      text-align: center;
    }

    .show-more-btn {
      display: block;
      margin: 1rem auto;
      padding: 0.5rem 1.5rem;
      background-color: #0066cc;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.2s;
    }

    .show-more-btn:hover {
      background-color: #0052a3;
    }

    /* Modal Popup */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      max-width: 800px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      border-bottom: 1px solid #eee;
      padding-bottom: 1rem;
    }

    .modal-title {
      margin: 0;
      font-size: 1.5rem;
      color: #333;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.8rem;
      cursor: pointer;
      color: #666;
      padding: 0 0.5rem;
    }

    .modal-close:hover {
      color: #333;
    }

    .modal-media {
      width: 100%;
      max-height: 500px;
      object-fit: contain;
      margin-bottom: 1.5rem;
      border-radius: 4px;
    }

    .modal-date {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
      font-style: italic;
    }

    .modal-description {
      line-height: 1.6;
      color: #444;
    }

    .info-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(0,0,0,0.7);
      color: white;
      border: none;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      font-size: 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10;
    }

    .info-btn:hover {
      background: rgba(0,0,0,0.9);
    }

    @media (max-width: 1200px) {
      .galeri-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (max-width: 900px) {
      .galeri-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .galeri-grid {
        grid-template-columns: 1fr;
      }
      
      .galeri-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
    }
  </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="container">
  <div class="galeri-header">
    <h2>GALERI</h2>
    <form method="GET" class="filter-form">
      <label for="jenis">Filter:</label>
      <select name="jenis" id="jenis" onchange="this.form.submit()">
        <option value="semua" <?= $filter === 'semua' ? 'selected' : '' ?>>Semua</option>
        <option value="foto" <?= $filter === 'foto' ? 'selected' : '' ?>>Foto</option>
        <option value="video" <?= $filter === 'video' ? 'selected' : '' ?>>Video</option>
      </select>
    </form>
  </div>

  <div class="galeri-grid <?= $expanded ? 'expanded' : '' ?>">
    <?php foreach ($galeri as $item): 
      $ratio = $item['rasio'] ?? '16-9';
    ?>
      <div class="media-container" 
           onclick="window.open('<?= $item['jenis'] === 'foto' ? '../../uploads/foto/'.$item['file_path'] : (strpos($item['file_path'], 'http') === 0 ? $item['file_path'] : '../../uploads/video/'.$item['file_path']) ?>', '_blank')"
           data-title="<?= htmlspecialchars($item['judul']) ?>"
           data-date="<?= date('d F Y', strtotime($item['tanggal_upload'])) ?>"
           data-description="<?= htmlspecialchars($item['deskripsi']) ?>"
           data-file="<?= $item['jenis'] === 'foto' ? '../../uploads/foto/'.$item['file_path'] : (strpos($item['file_path'], 'http') === 0 ? $item['file_path'] : '../../uploads/video/'.$item['file_path']) ?>"
           data-type="<?= $item['jenis'] ?>">
        
        <button class="info-btn" onclick="event.stopPropagation(); showInfoModal(this.parentElement)">i</button>
        
        <?php if ($item['jenis'] === 'foto'): ?>
          <img src="../../uploads/foto/<?= htmlspecialchars($item['file_path']) ?>" class="media ratio-<?= $ratio ?>" alt="<?= htmlspecialchars($item['judul']) ?>">
        <?php elseif (strpos($item['file_path'], 'http') === 0): ?>
          <iframe src="<?= htmlspecialchars($item['file_path']) ?>" class="media ratio-<?= $ratio ?>" allowfullscreen></iframe>
        <?php else: ?>
          <video class="media ratio-<?= $ratio ?>" controls>
            <source src="../../uploads/video/<?= htmlspecialchars($item['file_path']) ?>">
            Browser Anda tidak mendukung video.
          </video>
        <?php endif; ?>
        
        <p class="media-title"><?= htmlspecialchars($item['judul']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($galeri) > 0 && !$expanded): ?>
    <button class="show-more-btn" onclick="window.location.href='?jenis=<?= $filter ?>&expanded=1'">Tampilkan Lebih Banyak</button>
  <?php endif; ?>

  <?php if (count($galeri) === 0): ?>
    <p><em>Tidak ada konten ditemukan.</em></p>
  <?php endif; ?>
</div>

<!-- Modal Popup -->
<div id="infoModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle"></h3>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div id="modalMediaContainer">
      <!-- Media akan dimasukkan di sini -->
    </div>
    <p class="modal-date" id="modalDate"></p>
    <p class="modal-description" id="modalDescription"></p>
  </div>
</div>

<script>
  // Fungsi untuk menampilkan modal info
  function showInfoModal(element) {
    const title = element.getAttribute('data-title');
    const date = element.getAttribute('data-date');
    const description = element.getAttribute('data-description');
    const file = element.getAttribute('data-file');
    const type = element.getAttribute('data-type');
    
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDate').textContent = 'Diunggah pada: ' + date;
    document.getElementById('modalDescription').textContent = description;
    
    const mediaContainer = document.getElementById('modalMediaContainer');
    mediaContainer.innerHTML = '';
    
    if (type === 'foto') {
      const img = document.createElement('img');
      img.src = file;
      img.className = 'modal-media';
      img.alt = title;
      mediaContainer.appendChild(img);
    } else if (type === 'video') {
      if (file.startsWith('http')) {
        const iframe = document.createElement('iframe');
        iframe.src = file;
        iframe.className = 'modal-media';
        iframe.allowFullscreen = true;
        mediaContainer.appendChild(iframe);
      } else {
        const video = document.createElement('video');
        video.className = 'modal-media';
        video.controls = true;
        const source = document.createElement('source');
        source.src = file;
        video.appendChild(source);
        mediaContainer.appendChild(video);
      }
    }
    
    document.getElementById('infoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Mencegah scroll di background
  }
  
  function closeModal() {
    document.getElementById('infoModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Mengembalikan scroll
  }

  // Tutup modal ketika klik di luar konten
  window.onclick = function(event) {
    const modal = document.getElementById('infoModal');
    if (event.target === modal) {  
      closeModal();
    }
  }
  
  // Tutup modal dengan tombol ESC
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeModal();
    }
  });
</script>

</body>
</html>