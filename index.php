<?php
require_once 'core/functions.php';
require_once 'config/database.php';

startSession();

// Proses simpan data reservasi
$reservation_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_submit'])) {
    $nama = trim($_POST['nama'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $jumlah_anggota = isset($_POST['jumlah_anggota']) ? preg_replace('/[^0-9]/', '', $_POST['jumlah_anggota']) : 1;
    $tanggal_pemesanan = $_POST['tanggal_pemesanan'] ?? '';

    if ($nama && $no_hp && $email && $jumlah_anggota && $tanggal_pemesanan) {
        $data = [
            'nama' => sanitizeInput($nama),
            'no_hp' => sanitizeInput($no_hp),
            'email' => sanitizeInput($email),
            'jumlah_anggota' => (int)$jumlah_anggota,
            'tanggal_pemesanan' => $tanggal_pemesanan,
            'catatan' => ''
        ];
        $reservation_success = createReservation($data);
    }
}
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

      <a href="<section>'home'" class="logo">
        <img src="./assets front/images/logo.png" width="160" height="50" alt="Grilli - Home">
      </a>

      <nav class="navbar" data-navbar>

        <button class="close-btn" aria-label="close menu" data-nav-toggler>
          <ion-icon name="close-outline" aria-hidden="true"></ion-icon>
        </button>

        <a href="#home" class="logo">
          <img src="./assets front/images/logo.png" width="160" height="50">
        </a>

        <ul class="navbar-list">

          <li class="navbar-item">
            <a href="#home" class="navbar-link hover-underline active">
              <div class="separator"></div>

              <span class="span">Home</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#menu" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Menu</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#about" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Tentang Kami</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="#reservation" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Kontak</span>
            </a>
          </li>

          <li class="navbar-item">
            <a href="auth/login.php" class="navbar-link hover-underline">
              <div class="separator"></div>

              <span class="span">Staff</span>
            </a>
          </li>

        </ul>


      </nav>

      <a href="#reservation" class="btn btn-secondary">
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
    <article>

      <!-- 
        - #HERO
      -->

      <section class="hero text-center" aria-label="home" id="home">

        <ul class="hero-slider" data-hero-slider>

          <li class="slider-item active" data-hero-slider-item>

            <div class="slider-bg">
              <img src="./assets front/images/hero-slider-1.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>

            <p class="label-2 section-subtitle slider-reveal">Tradisional & Sehat</p>

            <h1 class="display-1 hero-title slider-reveal">
              Hidangan Tradisional <br>
              yang sehat dan lezat
            </h1>

            <p class="body-2 hero-text slider-reveal">
              Datanglah bersama keluarga & rasakan nikmatnya hidangan yang menggugah selera
            </p>

            <a href="#menu" class="btn btn-primary slider-reveal">
              <span class="text text-1">Menu Kami</span>

              <span class="text text-2" aria-hidden="true">Menu Kami</span>
            </a>

          </li>

          <li class="slider-item" data-hero-slider-item>

            <div class="slider-bg">
              <img src="./assets front/images/hero-slider-2.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>

            <p class="label-2 section-subtitle slider-reveal">Cita Rasa Nusantara</p>

            <h1 class="display-1 hero-title slider-reveal">
              Nikmati Kelezatan <br>
              disetiap suapan
            </h1>

            <p class="body-2 hero-text slider-reveal">
               Suasana hangat, rasa otentik, untuk momen tak terlupakan bersama orang tercinta
            </p>

            <a href="#menu" class="btn btn-primary slider-reveal">
              <span class="text text-1">Menu Kami</span>

              <span class="text text-2" aria-hidden="true">Menu Kami</span>
            </a>

          </li>

          <li class="slider-item" data-hero-slider-item>

            <div class="slider-bg">
              <img src="./assets front/images/hero-slider-3.jpg" width="1880" height="950" alt="" class="img-cover">
            </div>

            <p class="label-2 section-subtitle slider-reveal">Lezat & Bergizi</p>

            <h1 class="display-1 hero-title slider-reveal">
              Sajian Istimewa <br>
              untuk setiap selera
            </h1>

            <p class="body-2 hero-text slider-reveal">
               Ajak keluarga bersantap & ciptakan kenangan manis dengan hidangan pilihan
            </p>

            <a href="#menu" class="btn btn-primary slider-reveal">
              <span class="text text-1">Menu Kami</span>

              <span class="text text-2" aria-hidden="true">Menu Kami</span>
            </a>

          </li>

        </ul>

        <button class="slider-btn prev" aria-label="slide to previous" data-prev-btn>
          <ion-icon name="chevron-back"></ion-icon>
        </button>

        <button class="slider-btn next" aria-label="slide to next" data-next-btn>
          <ion-icon name="chevron-forward"></ion-icon>
        </button>

        <a href="#" class="hero-btn has-after">
          <img src="./assets front/images/hero-icon.png" width="48" height="48" alt="booking icon">

          <span class="label-2 text-center span">RESERVASI</span>
        </a>

      </section>





      <!-- 
        - #
      -->

      <section class="section service bg-black-10 text-center" aria-label="service">
        <div class="container">

          <p class="section-subtitle label-2">LEZAT DISETIAP GIGITAN</p>

          <h2 class="headline-1 section-title">Rasa Yang Tiada Banding</h2>

          <p class="section-text">
            Kami menyajikan hidangan lezat yang terbuat dari bahan-bahan segar dan alami, <br>
            dengan harga ekonomis dan pelayanan yang ramah. <br>
            
          </p>

          <ul class="grid-list">

            <li>
              <div class="service-card">

                <a class="has-before hover:shine">
                  <figure class="card-banner img-holder" style="--width: 285; --height: 336;">
                    <img src="./assets front/images/service-1.jpg" width="285" height="336" loading="lazy" alt="Breakfast"
                      class="img-cover">
                  </figure>
                </a>

                <div class="card-content">

                  <h3 class="title-4 card-title">
                    <a>Cap Cay</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="service-card">

                <a class="has-before hover:shine">
                  <figure class="card-banner img-holder" style="--width: 285; --height: 336;">
                    <img src="./assets front/images/service-2.jpg" width="285" height="336" loading="lazy" alt="Appetizers"
                      class="img-cover">
                  </figure>
                </a>

                <div class="card-content">

                  <h3 class="title-4 card-title">
                    <a>Nasi Goreng</a>
                  </h3>

                </div>

              </div>
            </li>

            <li>
              <div class="service-card">

                <a class="has-before hover:shine">
                  <figure class="card-banner img-holder" style="--width: 285; --height: 336;">
                    <img src="./assets front/images/service-3.jpg" width="285" height="336" loading="lazy" alt="Drinks"
                      class="img-cover">
                  </figure>
                </a>

                <div class="card-content">

                  <h3 class="title-4 card-title">
                    <a>Bakmi Goreng</a>
                  </h3>

                </div>

              </div>
            </li>

          </ul>

          <img src="./assets front/images/shape-1.png" width="246" height="412" loading="lazy" alt="shape"
            class="shape shape-1 move-anim">
          <img src="./assets front/images/shape-2.png" width="343" height="345" loading="lazy" alt="shape"
            class="shape shape-2 move-anim">

        </div>
      </section>





      <!-- 
        - #ABOUT
      -->

      <section class="section about text-center" aria-labelledby="about-label" id="about">
        <div class="container">

          <div class="about-content">

            <p class="label-2 section-subtitle" id="about-label">Tentang Kami</p>

            <h2 class="headline-1 section-title">Setiap Rasa Memiliki Cerita</h2>

            <p class="section-text">
              Kami adalah restoran yang mengutamakan cita rasa otentik dan kualitas bahan baku.
              Dengan pengalaman Puluhan tahun di industri kuliner, kami berkomitmen untuk menyajikan hidangan yang orisinil dan terus berinovasi
            </p>

            <div class="contact-label">Pesan Via Whatsapp</div>

            <a href="tel:+621231234567" class="body-1 contact-number hover-underline">+62 123 123 4567</a>

            <a href="wa.me/+621231234567" class="btn btn-primary">
              <span class="text text-1">Pesan Sekarang</span>

              <span class="text text-2" aria-hidden="true">Pesan Sekarang</span>
            </a>

          </div>

          <figure class="about-banner">

            <img src="./assets front/images/about-banner.jpg" width="570" height="570" loading="lazy" alt="about banner"
              class="w-100" data-parallax-item data-parallax-speed="1">

            <div class="abs-img abs-img-1 has-before" data-parallax-item data-parallax-speed="1.75">
              <img src="./assets front/images/about-abs-image.jpg" width="285" height="285" loading="lazy" alt=""
                class="w-100">
            </div>

          </figure>

          <img src="./assets front/images/shape-3.png" width="197" height="194" loading="lazy" alt="" class="shape">

        </div>
      </section>





      <!-- 
        - #SPECIAL DISH
      -->

      <section class="special-dish text-center" aria-labelledby="dish-label">

        <div class="special-dish-banner">
          <img src="./assets front/images/service-2.jpg" width="940" height="900" loading="lazy" alt="special dish"
            class="img-cover">
        </div>

        <div class="special-dish-content bg-black-10">
          <div class="container">

            <img src="./assets front/images/badge-1.png" width="28" height="41" loading="lazy" alt="badge" class="abs-img">

            <p class="section-subtitle label-2">Masakan spesial</p>

            <h2 class="headline-1 section-title">Nasi-Goreng Campur</h2>

            <p class="section-text">
            Perpaduan lezat antara nasi goreng khas Jawa dan bakmi yang gurih, disajikan dengan topping ayam suwir, telur, sayuran segar, dan acar asam-manis yang menyegarkan. Satu porsi penuh cita rasa nusantara dalam setiap suapan!
            </p>


            <a href="#menu" class="btn btn-primary">
              <span class="text text-1">Lihat Menu kami</span>

              <span class="text text-2" aria-hidden="true">View All Menu</span>
            </a>

          </div>
        </div>

        <img src="./assets front/images/shape-4.png" width="179" height="359" loading="lazy" alt="" class="shape shape-1">

        <img src="./assets front/images/shape-9.png" width="351" height="462" loading="lazy" alt="" class="shape shape-2">

      </section>





      <!-- 
        - #MENU
      -->

      <section class="section menu" aria-label="menu-label" id="menu">
        <div class="container">

          <p class="section-subtitle text-center label-2">Kenikmatan</p>

          <h2 class="headline-1 section-title text-center">Menu Kami</h2>

          <ul class="grid-list">

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets front/images/menu-1.png" width="100" height="100" loading="lazy" alt="Greek Salad" class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a class="card-title">Nasi-Goreng</a>
                    </h3>

                

                </div>
                    <p class="card-text label-1">Nasi Goreng Campur Telur,Ayam Topping Acar Dan Kerupuk Udang</p>
              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets front/images/bak gr.png" width="100" height="100" loading="lazy" alt="Lasagne" class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a class="card-title">Bakmi-Jawa Godog</a>
                    </h3>

                    
                  </div>

                  <p class="card-text label-1">Bakmi Jowo Godog(Hanya Bakmi Kuning Saja) Topping Telur Dan Ayam Dicampur Dimasakan</p>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets front/images/pak.png" width="100" height="100" loading="lazy" alt="Butternut Pumpkin" class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a class="card-title">Pak-lay Sayur</a>
                    </h3>

                </span>
                  </div>

                  <p class="card-text label-1">Paklay Sayur</p>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets front/images/cap gr.png" width="100" height="100" loading="lazy" alt="Tokusen Wagyu" class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a class="card-title">Cap-Cay Goreng</a>
                    </h3>

                    

             
                  </div>

                  <p class="card-text label-1">Capjay Goreng Topping Telur Dan Ayam Dicampur Dimasakan</p>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets front/images/bak gr.png" width="100" height="100" loading="lazy" alt="Olivas Rellenas" class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a class="card-title">Bakmi-Jawa Goreng</a>
                    </h3>

                 
                  </div>

                  <p class="card-text label-1">Bakmi Jowo Goreng(Hanya Bakmi Kuning Saja) Topping Telur Dan Ayam Dicampur Dimasakan</p>

                </div>

              </div>
            </li>

            <li>
              <div class="menu-card hover:card">

                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets front/images/cap go.png" width="100" height="100" loading="lazy" alt="Opu Fish" class="img-cover">
                </figure>

                <div>

                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a class="card-title">Cap-Cay Godog</a>
                    </h3>

                  </div>

                  <p class="card-text label-1">Capjay Godog Topping Telur Dan Ayam Dicampur Dimasakan</p>

                </div>

              </div>
            </li>

          </ul>


          <img src="./assets front/images/shape-5.png" width="921" height="1036" loading="lazy" alt="shape" class="shape shape-2 move-anim">
          <img src="./assets front/images/shape-6.png" width="343" height="345" loading="lazy" alt="shape" class="shape shape-3 move-anim">

        </div>
      </section>




      <!-- 
        - #TESTIMONIALS
      -->

      <section class="section testi text-center has-bg-image"
        style="background-image: url('./assets front/images/testimonial-bg.jpg')" aria-label="testimonials">
        <div class="container">

        </div>
      </section>





      <!-- 
        - #RESERVATION
      -->

      <section class="reservation" id="reservation">
        <div class="container">

          <div class="form reservation-form bg-black-10">

            

            <form action="" method="post" class="form-left">

              <h2 class="headline-1 text-center">Reservasi Online</h2>

              <p class="form-text text-center">
                Booking <a href="tel:+62123123456" class="link">+62-123-123456</a>
                atau isi form dibawah
              </p>
            <?php if ($reservation_success): ?>
              <div class="alert alert-success" style="color:green;text-align:center;">Reservasi berhasil dikirim!</div>
            <?php endif; ?>
              <div class="input-wrapper">
                <input type="text" name="nama" placeholder="Nama Pemesan" autocomplete="off" class="input-field" required>

                <input type="tel" name="no_hp" placeholder="Nomor Telepon" autocomplete="off" class="input-field" required>
                <input type="email" name="email" placeholder="Email" autocomplete="off" class="input-field" required>
              </div>

              <div class="input-wrapper">
                <div class="icon-wrapper">
                  <ion-icon name="person-outline" aria-hidden="true"></ion-icon>
                  <select name="jumlah_anggota" class="input-field" required>
                    <option value="1">1 Person</option>
                    <option value="2">2 Person</option>
                    <option value="3">3 Person</option>
                    <option value="4">4 Person</option>
                    <option value="5">5 Person</option>
                    <option value="6">6 Person</option>
                    <option value="7">7 Person</option>
                  </select>
                  <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                </div>
                <div class="icon-wrapper">
                  <ion-icon name="calendar-clear-outline" aria-hidden="true"></ion-icon>
                  <input type="datetime-local" name="tanggal_pemesanan" class="input-field" required>
                  <ion-icon name="chevron-down" aria-hidden="true"></ion-icon>
                </div>
              </div>
              <button type="submit" name="reservation_submit" class="btn btn-secondary">
                <span class="text text-1">Pesan Tanggal</span>
                <span class="text text-2" aria-hidden="true">Pesan Tanggal</span>
              </button>
            </form>

            <div class="form-right text-center" style="background-image: url('./assets front/images/form-pattern.png')">

              <h2 class="headline-1 text-center">Kontak Kami</h2>

              <p class="contact-label">Booking & Pemesanan</p>

              <a href="tel:+62123123456" class="body-1 contact-number hover-underline">+62-123-123456</a>

              <div class="separator"></div>

              <p class="contact-label">Lokasi</p>

              <address class="body-4">
                Jl. Kali Kepunton 2, RT.03/RW.13, Jagalan, Kec. Jebres, Kota Surakarta, Jawa Tengah 57162
              </address>

              <p class="contact-label">Waktu Buka</p>

              <p class="body-4">
                Senin - Minggu <br>
                16.00  - 00.00
              </p>

            </div>

          </div>

        </div>
      </section>





      <!-- 
        - #FEATURES
      -->

      <section class="section features text-center" aria-label="features">
        <div class="container">

          <p class="section-subtitle label-2">pesan online</p>

          <h2 class="headline-1 section-title">Mitra Kami</h2>

          <ul class="grid-list">

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets front/images/features-icon-1.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Go-Food</h3>

              </div>
            </li>

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets front/images/features-icon-2.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Shopee-Food</h3>

              </div>
            </li>

            <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets front/images/features-icon-3.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Grab-Food</h3>


              </div>
            </li>

             <li class="feature-item">
              <div class="feature-card">

                <div class="card-icon">
                  <img src="./assets front/images/features-icon-3.png" width="100" height="80" loading="lazy" alt="icon">
                </div>

                <h3 class="title-2 card-title">Pesan-Antar</h3>


              </div>
            </li>


          </ul>

          <img src="./assets front/images/shape-7.png" width="208" height="178" loading="lazy" alt="shape"
            class="shape shape-1">

          <img src="./assets front/images/shape-8.png" width="120" height="115" loading="lazy" alt="shape"
            class="shape shape-2">

        </div>
      </section>





      <!-- 
        - #EVENT
      -->

      <section class="section event bg-black-10" aria-label="event">
        <div class="container">

          <p class="section-subtitle label-2 text-center">LOKASI</p>

          <h2 class="section-title headline-1 text-center">lOKASI KAMI</h2>

              <div class="event-card">

             <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d293.96125363586145!2d110.84425456301561!3d-7.56893614933562!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a16579abfc7e1%3A0x645dd20fbe6eda44!2sBakmi%20Jawa%20Pak%20Prapto!5e0!3m2!1sid!2sid!4v1764261910932!5m2!1sid!2sid" width="1200" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </section>

    </article>
  </main>





  <!-- 
    - #FOOTER
  -->

  <footer class="footer section has-bg-image text-center"
    style="background-image: url('./assets front/images/footer-bg.jpg')">
    <div class="container">

      <div class="footer-top grid-list">

        <div class="footer-brand has-before has-after">

          <a href="#" class="logo">
            <img src="./assets front/images/logo.png" width="160" height="50" loading="lazy" alt="grilli home">
          </a>

          <address class="body-4">
            Jl. Kali Kepunton 2, RT.03/RW.13, Jagalan, Kec. Jebres, Kota Surakarta, Jawa Tengah 57162
          </address>

          <a href="mailto:booking@grilli.com" class="body-4 contact-link">booking@grilli.com</a>

          <a href="tel:+621231231234567" class="body-4 contact-link">Reservasi & Pemesanan: +62-123-123123456</a>

          <p class="body-4">
            Buka : 16:00 - 00:00
          </p>

        </div>

        <ul class="footer-list">

          <li>
            <a href="#home" class="label-2 footer-link hover-underline">Home</a>
          </li>

          <li>
            <a href="#menu" class="label-2 footer-link hover-underline">Menu</a>
          </li>

          <li>
            <a href="#about" class="label-2 footer-link hover-underline">Tentang Kami</a>
          </li>

          <li>
            <a href="#reservation" class="label-2 footer-link hover-underline">Kontak</a>
          </li>

          <li>
            <a href="../auth/login.php" class="label-2 footer-link hover-underline">Staff</a>
          </li>

        </ul>

        <ul class="footer-list">

          <li>
            <a class="label-2 footer-link hover-underline">Go-Food</a>
          </li>

          <li>
            <a class="label-2 footer-link hover-underline">Shopee-Food</a>
          </li>

          <li>
            <a class="label-2 footer-link hover-underline">Grab-Food</a>
          </li>

          <li>
            <a class="label-2 footer-link hover-underline">Google Map</a>
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