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

    .container {
      padding: 1rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .galeri-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      border-radius: 8px;
      color: white;
    }

    .galeri-header h2 {
      margin: 0;
      font-size: 2rem;
      color: black;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 700;
    }

    .filter-form {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      background-color: white;
      padding: 0.5rem 1rem;
      border-radius: 30px;
    }

    .filter-form label {
      font-weight: 600;
      color: #0066cc;
    }

    .filter-form select {
      padding: 0.5rem 1rem;
      font-size: 1rem;
      border-radius: 20px;
      border: 2px solid #0066cc;
      background-color: white;
      color: #333;
      font-weight: 500;
      cursor: pointer;
      outline: none;
      transition: all 0.3s ease;
    }

    .filter-form select:hover {
      background-color: #f0f8ff;
    }

    .filter-form select:focus {
      border-color: #004d99;
      box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
    }

    .galeri-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      max-height: calc(4 * (250px + 1rem));
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
      padding: 0.75rem 2rem;
      background-color: #0066cc;
      color: white;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 10px rgba(0, 102, 204, 0.3);
    }

    .show-more-btn:hover {
      background-color: #0052a3;
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 102, 204, 0.4);
    }

    .show-more-btn.hidden {
      display: none;
    }

    /* Modal Styles */
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

    /* Media Modal Specific Styles */
    #mediaModal .modal-content {
      background: transparent;
      padding: 0;
      width: auto;
      max-width: 90vw;
      max-height: 90vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    #mediaModalContent {
      position: relative;
      max-width: 100%;
      max-height: 100%;
    }

    #mediaModal img {
      max-height: 80vh;
      max-width: 90vw;
      width: auto;
      height: auto;
      display: block;
    }

    #mediaModal video {
      max-height: 80vh;
      max-width: 90vw;
      width: auto;
      height: auto;
    }

    #mediaModal iframe {
      width: 80vw;
      height: 45vw;
      max-height: 80vh;
      border: none;
    }

    #mediaModal .modal-close {
      position: absolute;
      top: 20px;
      right: 20px;
      color: white;
      background: rgba(0,0,0,0.5);
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      font-size: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 10;
    }

    /* Info Modal Styles */
    #infoModal .modal-content {
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

    #infoModal .modal-close {
      background: none;
      border: none;
      font-size: 1.8rem;
      cursor: pointer;
      color: #666;
      padding: 0 0.5rem;
    }

    #infoModal .modal-close:hover {
      color: #333;
    }

  .modal-media-container {
    width: 100%;
    margin-bottom: 1.5rem;
  }

  /* Untuk video */
  .modal-media-container.video-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
  }

  /* Untuk foto */
  .modal-media-container.image-container {
    text-align: center;
  }

  .modal-media-container iframe,
  .modal-media-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 4px;
  }

  .modal-media-container img {
    max-height: 70vh;
    max-width: 100%;
    width: auto;
    height: auto;
    display: inline-block;
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
      text-align: justify;  
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

    .video-thumbnail {
      position: relative;
    }

    .video-thumbnail::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 60px;
      height: 60px;
      background-color: rgba(255, 255, 255, 0.7);
      border-radius: 50%;
      background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%230066cc"><path d="M8 5v14l11-7z"/></svg>');
      background-repeat: no-repeat;
      background-position: center;
      background-size: 30px;
      pointer-events: none;
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

      #infoModal .modal-content {
        padding: 1rem;
      }

      .modal-media-container {
        padding-bottom: 75%;
      }

      #mediaModal iframe {
        width: 90vw;
        height: 50.625vw;
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
      $thumbnail_path = '';
      if ($item['jenis'] === 'video') {
        // If video is from external URL, try to get thumbnail (example for YouTube)
        if (strpos($item['file_path'], 'youtube.com') !== false || strpos($item['file_path'], 'youtu.be') !== false) {
          $video_id = '';
          if (preg_match('%(?:youtube(?:nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $item['file_path'], $match)) {
            $video_id = $match[1];
          }
          $thumbnail_path = $video_id ? 'https://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg' : '';
        } else {
          // For local videos, use a generic video thumbnail or first frame (you might need to generate this)
          $thumbnail_path = '../../assets/img/video-thumbnail.jpg'; // Replace with your generic video thumbnail
        }
      }
    ?>
      <div class="media-container" 
           onclick="showMediaModal(this)"
           data-title="<?= htmlspecialchars($item['judul']) ?>"
           data-date="<?= date('d F Y', strtotime($item['tanggal_upload'])) ?>"
           data-description="<?= htmlspecialchars($item['deskripsi']) ?>"
           data-file="<?= $item['jenis'] === 'foto' ? '../../uploads/foto/'.$item['file_path'] : (strpos($item['file_path'], 'http') === 0 ? $item['file_path'] : '../../uploads/video/'.$item['file_path']) ?>"
           data-type="<?= $item['jenis'] ?>">
        
        <button class="info-btn" onclick="event.stopPropagation(); showInfoModal(this.parentElement)">i</button>
        
        <?php if ($item['jenis'] === 'foto'): ?>
          <img src="../../uploads/foto/<?= htmlspecialchars($item['file_path']) ?>" class="media ratio-<?= $ratio ?>" alt="<?= htmlspecialchars($item['judul']) ?>">
        <?php else: ?>
          <div class="video-thumbnail">
            <img src="<?= $thumbnail_path ?>" class="media ratio-<?= $ratio ?>" alt="<?= htmlspecialchars($item['judul']) ?>">
          </div>
        <?php endif; ?>
        
        <p class="media-title"><?= mb_substr(strip_tags($item['judul']), 0, 75) ?>...</p>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if (count($galeri) > 8 && !$expanded): ?>
    <button class="show-more-btn" onclick="window.location.href='?jenis=<?= $filter ?>&expanded=1'">Tampilkan Lebih Banyak</button>
  <?php else: ?>
    <button class="show-more-btn hidden" onclick="window.location.href='?jenis=<?= $filter ?>&expanded=1'">Tampilkan Lebih Banyak</button>
  <?php endif; ?>

  <?php if (count($galeri) === 0): ?>
    <p><em>Tidak ada konten ditemukan.</em></p>
  <?php endif; ?>
</div>

<!-- Media Modal Popup -->
<div id="mediaModal" class="modal">
  <div class="modal-content">
    <button class="modal-close" onclick="closeMediaModal()">&times;</button>
    <div id="mediaModalContent"></div>
  </div>
</div>

<!-- Info Modal Popup -->
<div id="infoModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle"></h3>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div id="modalMediaContainer"></div>
    <p class="modal-date" id="modalDate"></p>
    <p class="modal-description" id="modalDescription"></p>
  </div>
</div>

<script>
  // Fungsi untuk menampilkan modal media
  function showMediaModal(element) {
    const file = element.getAttribute('data-file');
    const type = element.getAttribute('data-type');
    const title = element.getAttribute('data-title');
    
    const mediaContainer = document.getElementById('mediaModalContent');
    mediaContainer.innerHTML = '';
    
    if (type === 'foto') {
      const img = document.createElement('img');
      img.src = file;
      img.alt = title;
      mediaContainer.appendChild(img);
    } else if (type === 'video') {
      if (file.startsWith('http')) {
        const iframe = document.createElement('iframe');
        iframe.src = file.includes('?') ? `${file}&autoplay=1` : `${file}?autoplay=1`;
        iframe.allowFullscreen = true;
        iframe.setAttribute('allow', 'autoplay');
        iframe.setAttribute('title', title);
        mediaContainer.appendChild(iframe);
      } else {
        const video = document.createElement('video');
        video.controls = true;
        video.autoplay = true;
        video.setAttribute('title', title);
        const source = document.createElement('source');
        source.src = file;
        video.appendChild(source);
        mediaContainer.appendChild(video);
      }
    }
    
    document.getElementById('mediaModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  
  function closeMediaModal() {
    document.getElementById('mediaModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // Stop video/iframe when modal is closed
    const mediaContainer = document.getElementById('mediaModalContent');
    const iframe = mediaContainer.querySelector('iframe');
    const video = mediaContainer.querySelector('video');
    
    if (iframe) {
      iframe.src = '';
    }
    if (video) {
      video.pause();
      video.currentTime = 0;
    }
  }

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
    
    const mediaWrapper = document.createElement('div');
    
    if (type === 'foto') {
      mediaWrapper.className = 'modal-media-container image-container';
      const img = document.createElement('img');
      img.src = file;
      img.alt = title;
      mediaWrapper.appendChild(img);
    } else if (type === 'video') {
      mediaWrapper.className = 'modal-media-container video-container';
      if (file.startsWith('http')) {
        const iframe = document.createElement('iframe');
        iframe.src = file;
        iframe.allowFullscreen = true;
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
        mediaWrapper.appendChild(iframe);
      } else {
        const video = document.createElement('video');
        video.controls = true;
        video.setAttribute('playsinline', '');
        const source = document.createElement('source');
        source.src = file;
        source.type = 'video/mp4';
        video.appendChild(source);
        mediaWrapper.appendChild(video);
      }
    }
    
    mediaContainer.appendChild(mediaWrapper);
    document.getElementById('infoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  
  function closeModal() {
    document.getElementById('infoModal').style.display = 'none';
    document.body.style.overflow = 'auto';
  }

  // Tutup modal ketika klik di luar konten
  window.onclick = function(event) {
    const mediaModal = document.getElementById('mediaModal');
    const infoModal = document.getElementById('infoModal');
    
    if (event.target === mediaModal) {  
      closeMediaModal();
    }
    if (event.target === infoModal) {  
      closeModal();
    }
  }
  
  // Tutup modal dengan tombol ESC
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeMediaModal();
      closeModal();
    }
  });
</script>

</body>
</html>