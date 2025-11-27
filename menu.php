<?php
require_once 'core/functions.php';
checkAuth();

$page_title = 'Menu';

// Ambil semua produk aktif dari database
$products = getProducts(['status' => 'active']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 
    - primary meta tags
  -->
  <title>Prapto Bakmi</title>
  <meta name="title" content="Grilli - Amazing & Delicious Food">
  <meta name="description" content="This is a Restaurant html template made by codewithsadee">

  <!-- 
    - favicon
  -->
  <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">

  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&family=Forum&display=swap" rel="stylesheet">

  <!-- 
    - custom css link
  -->
  <link rel="stylesheet" href="./assets front/css/style.css">

  <!-- 
    - preload images
  -->
  <link rel="preload" as="image" href="./assets front/images/hero-slider-1.jpg">
  <link rel="preload" as="image" href="./assets front/images/hero-slider-2.jpg">
  <link rel="preload" as="image" href="./assets front/images/hero-slider-3.jpg">

</head>

<body id="top">

  <!-- 
    - #PRELOADER
  -->

  <div class="preload" data-preaload>
    <div class="circle"></div>
    <p class="text">Prapto-Bakmi</p>
  </div>





  <!-- 
    - #HEADER
  -->

  <header class="header" data-header>
    <div class="container">

      <a href="#home" class="logo">
        <img src="./assets front/images/logo.svg" width="160" height="50" alt="Prapto - Home">
      </a>

      <nav class="navbar" data-navbar>

        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>

        <a href="#" class="logo">
          <img src="./assets front/images/logo.svg" width="160" height="50" alt="Prapto - Home">
        </a>

        <ul class="navbar-list">

          <li class="navbar-item">
            <a href="index.php#home" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Home</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="menu.php" class="navbar-link hover-underline active">
              <div class="separator"></div>

              <span class="span">Menu</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="index.php#about" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Tentang Kami</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="index.php#reservation" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Kontak</span>
            </a>
          </li>

        </ul>

      </nav>

      <a href="reservation" class="btn btn-secondary">
        <span class="text text-1">RESERVASI</span>

        <span class="text text-2" aria-hidden="true">RESERVASI</span>
      </a>

      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="line line-1"></span>
        <span class="line line-2"></span>
        <span class="line line-3"></span>
      </button>

      <div class="overlay" data-nav-toggler data-overlay></div>

    </div>
  </header>





  <main>
 
      <!-- 
        - #MENU
      -->

      <section class="section menu" aria-label="menu-label" id="menu">
        <div class="container">

          <p class="section-subtitle text-center label-2">Kenikmatan</p>

          <h2 class="headline-1 section-title text-center">Menu Kami</h2>

          <ul class="grid-list">
<?php foreach ($products as $product): ?>
            <li>
              <div class="menu-card hover:card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="<?= htmlspecialchars($product['image'] ? './assets front/uploads/' . $product['image'] : './assets front/images/menu-default.png') ?>" width="100" height="100" loading="lazy" alt="<?= htmlspecialchars($product['name']) ?>" class="img-cover">
                </figure>
                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title"><?= htmlspecialchars($product['name']) ?></a>
                    </h3>
                  </div>
                  <p class="card-text label-1"><?= htmlspecialchars($product['description']) ?></p>
                </div>
              </div>
            </li>
<?php endforeach; ?>
          </ul>

          <img src="./assets front/images/shape-5.png" width="921" height="1036" loading="lazy" alt="shape" class="shape shape-2 move-anim">
          <img src="./assets front/images/shape-6.png" width="343" height="345" loading="lazy" alt="shape" class="shape shape-3 move-anim">

        </div>
      </section>

  <!-- 
    - #FOOTER
  -->

  <footer class="footer section has-bg-image text-center"
    style="background-image: url('./assets front front/images/footer-bg.jpg')">
    <div class="container">

      <div class="footer-top grid-list">

        <div class="footer-brand has-before has-after">

          <a href="#" class="logo">
            <img src="./assets front/images/logo.svg" width="160" height="50" loading="lazy" alt="prapto home">
          </a>

          <address class="body-4">
            Jl. Kali Kepunton 2, RT.03/RW.13, Jagalan, Kec. Jebres, Kota Surakarta, Jawa Tengah 57162
          </address>

          <a href="mailto:booking@grilli.com" class="body-4 contact-link">booking@grilli.com</a>

          <a href="tel:+621231231234567" class="body-4 contact-link">Reservasi & Pemesanan: +62-123-123123456</a>

          <p class="body-4">
            Buka : 18:00 - 25:00
          </p>

        </div>

        <ul class="footer-list">

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Home</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Menu</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Tentang Kami</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Kontak</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Staff</a>
          </li>

        </ul>

        <ul class="footer-list">

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Go-Food</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Shopee-Food</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Grab-Food</a>
          </li>

          <li>
            <a href="#" class="label-2 footer-link hover-underline">Google Map</a>
          </li>

        </ul>

      </div>

      <div class="footer-bottom">

        <p class="copyright">
          &copy;All Rights Reserved | Edited by <a href =""
            target="_blank" class="link">Trinity007</a>
        </p>

      </div>

    </div>
  </footer>





  <!-- 
    - #BACK TO TOP
  -->

  <a href="#top" class="back-top-btn active" aria-label="back to top" data-back-top-btn>
    <ion-icon name="chevron-up" aria-hidden="true"></ion-icon>
  </a>





  <!-- 
    - custom js link
  -->
  <script src="./assets front/js/script.js"></script>

  <!-- 
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>