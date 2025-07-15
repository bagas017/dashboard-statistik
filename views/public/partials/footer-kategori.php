<?php
// Array warna yang akan digunakan
$colors = ['#f6a100', '#88d1f2', '#1e65d3', '#084d7e', '#3fc5b6', '#ff5e5e', '#7b68ee', '#009688'];
shuffle($colors); // Mengacak warna setiap reload

// Default ke beranda.php jika sedang di kategori.php
$currentPage = basename($_SERVER['PHP_SELF']);
$targetPage = ($currentPage === 'kategori.php') ? 'beranda.php' : $currentPage;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sticky Footer with PHP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* === MAIN LAYOUT STYLES === */
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      position: relative;
      padding-bottom: 4rem;
    }

    .content {
      flex: 1;
      padding: 20px;
    }

    /* === STICKY FOOTER STYLES === */
    .submenu-footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      padding: 15px 20px;
      border-top: 1px solid #eee;
      box-shadow: 0 -2px 5px rgba(0,0,0,0.05);
      z-index: 1000;
    }

    .submenu-nav-container {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding: 10px 0;

      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* IE 10+ */
    }

    .submenu-nav-container::-webkit-scrollbar {
      display: none; /* Chrome, Safari, Opera */
    }

    .submenu-btn {
      flex: 0 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-width: 180px;
      min-height: 50px;
      padding: 12px 16px;
      border-radius: 5px;
      text-decoration: none;
      color: white;
      font-weight: 600;
      box-shadow: 2px 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .submenu-btn span {
      text-align: left;
    }

    .submenu-btn i {
      margin-left: 10px;
      font-size: 1.2em;
    }

    .submenu-btn:hover {
      transform: translateY(-2px);
      box-shadow: 4px 6px 14px rgba(0, 0, 0, 0.15);
    }

    .submenu-btn.active {
      outline: 3px solid #ffffff;
      outline-offset: -4px;
    }

    /* Responsive styles for footer */
    @media (max-width: 768px) {
      .submenu-btn {
        min-width: 150px;
        padding: 10px 12px;
        font-size: 0.9em;
      }

      body {
        padding-bottom: 90px;
      }
    }

    @media (max-width: 480px) {
      .submenu-nav-container {
        gap: 10px;
      }

      .submenu-btn {
        min-width: 120px;
        min-height: 40px;
        padding: 8px 10px;
      }

      body {
        padding-bottom: 80px;
      }
    }
  </style>
</head>
<body>

  <!-- Sticky Footer -->
  <div class="submenu-footer">
    <div class="submenu-nav-container">
      <?php foreach ($submenus as $i => $sm): ?>
        <?php
          $color = $colors[$i % count($colors)];
          $isActive = ($sm['slug'] === $slug);
        ?>
        <a href="<?= $targetPage ?>?submenu=<?= urlencode($sm['slug']) ?>&id=<?= urlencode($sm['id']) ?>"
           class="submenu-btn <?= $isActive ? 'active' : '' ?>"
           style="background-color: <?= $color ?>;">
          <span><?= htmlspecialchars($sm['nama_submenu']) ?></span>
          <?php if (!empty($sm['icon_class'])): ?>
            <i class="bi <?= htmlspecialchars($sm['icon_class']) ?>"></i>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const carouselEl = document.querySelector('#carouselBeranda');
      if (carouselEl) {
        const carousel = new bootstrap.Carousel(carouselEl, {
          interval: 10000,
          ride: 'carousel',
          pause: false,
          wrap: true
        });
      }

      // Smooth scroll jika klik anchor
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            const footerHeight = document.querySelector('.submenu-footer').offsetHeight;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - footerHeight;
            window.scrollTo({
              top: targetPosition,
              behavior: 'smooth'
            });
          }
        });
      });
    });
  </script>

</body>
</html>
