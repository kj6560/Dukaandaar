<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>FizSell Mobile POS</title>
  <meta name="description" content="">
  <meta name="keywords" content="Point Of Sale Software,Mobile Point Of Sale,point of sale">

  <!-- Favicons -->
  <link href="{{ asset('theme') }}/assets/img/favicon.png" rel="icon">
  <link href="{{ asset('theme') }}/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('theme') }}/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('theme') }}/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="{{ asset('theme') }}/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="{{ asset('theme') }}/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="{{ asset('theme') }}/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('theme') }}/assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:kj6560@gmail.com">kj6560@gmail.com</a></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"><span>+91-9599362404</span></i>
        </div>
      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-center">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.html" class="logo d-flex align-items-center">
          <h1 class="sitename">FizSell</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#" class=""></a></li>
            <li><a href="#"></a></li>
            <li><a href="#"></a></li>
            <li><a href="{{ asset('storage/uploads/rawbt.apk') }}">Download RawBT App</a></li>
            <li><a href="{{ asset('storage/uploads/fizsell.apk') }}">Download App</a></li>
            <li class="dropdown"><a href="#"><span></span> </a>
              <ul>
                <li><a href="#">Dropdown 1</a></li>
                <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="#">Deep Dropdown 1</a></li>
                    <li><a href="#">Deep Dropdown 2</a></li>
                    <li><a href="#">Deep Dropdown 3</a></li>
                    <li><a href="#">Deep Dropdown 4</a></li>
                    <li><a href="#">Deep Dropdown 5</a></li>
                  </ul>
                </li>
                <li><a href="#">Dropdown 2</a></li>
                <li><a href="#">Dropdown 3</a></li>
                <li><a href="#">Dropdown 4</a></li>
              </ul>
            </li>
            <li><a href="#"></a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      </div>
    </div>

  </header>

  <main class="main">

    <section id="hero" class="hero section dark-background">

      <img src="{{ asset('theme') }}/assets/img/hero-bg.jpg" alt="" data-aos="fade-in">

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-start">
          <div class="col-lg-8">
            <h2>Welcome to FizSell</h2>
            <p>India's very own premier Mobile Based POS and Sales Enablement Platform.</p>
            <a href="/login" class="btn-get-started">Get Started</a>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->  
      <!-- Features Section with Graphics and Wow Factors -->
    <section id="features" class="features section">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Application Features</h2>
          <p>Explore the powerful modules offered by FizSell Mobile POS:</p>
        </div>

        <div class="row">
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="100">
              <h4>Products</h4>
              <p>Manage products with detailed information, including categories, pricing, and product images.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="200">
              <h4>Inventory Management</h4>
              <p>Track stock levels, manage suppliers, and handle stock movements with comprehensive reporting.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="300">
              <h4>Schemes</h4>
              <p>Create promotional schemes to boost sales, including discounts, bundle offers, and loyalty programs.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="400">
              <h4>Orders</h4>
              <p>Manage customer orders, generate invoices, and track order statuses in real-time.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="500">
              <h4>Customers</h4>
              <p>Maintain customer profiles, transaction history, and contact information for targeted marketing.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="600">
              <h4>Contact Us</h4>
              <p>Get in touch with our support team for any assistance or inquiries regarding the application.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="700">
              <h4>Users</h4>
              <p>Manage application users, set roles and permissions, and monitor user activity effectively.</p>
            </div>
          </div>
        </div>

      </div>
    </section>

      <section id="features" class="features section">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Unleash the Power of FizSell</h2>
          <p>Empower your retail operations with FizSell’s comprehensive suite of modules, each crafted to streamline your business processes and drive growth.</p>
        </div>

        <div class="row">
          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="feature-box wow bounceIn" data-wow-delay="0.3s">
              <img src="{{ asset('theme') }}/assets/img/products.png" class="img-fluid mb-3" alt="Products">
              <h4>Products</h4>
              <p>Centralized product management with detailed categorization, pricing control, and visual product representation. Seamlessly integrate new products and monitor existing inventories in real-time.</p>
            </div>
          </div>

          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="feature-box wow bounceIn" data-wow-delay="0.4s">
              <img src="{{ asset('theme') }}/assets/img/inventory.png" class="img-fluid mb-3" alt="Inventory Management">
              <h4>Inventory Management</h4>
              <p>Real-time stock tracking, supplier management, and automated alerts for low stock levels. Make data-driven decisions with powerful analytics and reporting capabilities.</p>
            </div>
          </div>

          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="feature-box wow bounceIn" data-wow-delay="0.5s">
              <img src="{{ asset('theme') }}/assets/img/schemes.png" class="img-fluid mb-3" alt="Schemes">
              <h4>Schemes</h4>
              <p>Boost sales with customizable promotional schemes, loyalty programs, and targeted discounts. Track scheme performance to optimize customer engagement strategies.</p>
            </div>
          </div>

          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="feature-box wow bounceIn" data-wow-delay="0.6s">
              <img src="{{ asset('theme') }}/assets/img/orders.png" class="img-fluid mb-3" alt="Orders">
              <h4>Orders</h4>
              <p>Effortlessly manage orders, generate invoices, and monitor delivery statuses. Enhance customer satisfaction with accurate order tracking and quick fulfillment.</p>
            </div>
          </div>

          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="500">
            <div class="feature-box wow bounceIn" data-wow-delay="0.7s">
              <img src="{{ asset('theme') }}/assets/img/customers.png" class="img-fluid mb-3" alt="Customers">
              <h4>Customers</h4>
              <p>Build lasting relationships with customer profiles, purchase history analysis, and targeted marketing campaigns. Stay connected with your most valuable clients.</p>
            </div>
          </div>

          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="600">
            <div class="feature-box wow bounceIn" data-wow-delay="0.8s">
              <img src="{{ asset('theme') }}/assets/img/contact_us.png" class="img-fluid mb-3" alt="Contact Us">
              <h4>Contact Us</h4>
              <p>Need assistance? Reach out to our dedicated support team for prompt solutions and expert guidance. We’re here to help you succeed.</p>
            </div>
          </div>

          <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="700">
            <div class="feature-box wow bounceIn" data-wow-delay="0.9s">
              <img src="{{ asset('theme') }}/assets/img/users.png" class="img-fluid mb-3" alt="Users">
              <h4>Users</h4>
              <p>Manage user roles, permissions, and activity logs with robust user management tools. Ensure security and accountability at every level of operation.</p>
            </div>
          </div>
        </div>

        <div class="wow-factor text-center mt-5" data-aos="zoom-in">
          <h3>Wow Factors that Set FizSell Apart</h3>
          <p>FizSell is more than just a POS system – it’s your comprehensive business growth partner. From predictive analytics to AI-powered inventory forecasting, FizSell integrates cutting-edge technology to optimize your retail operations. Stay ahead of the curve and watch your sales soar!</p>
        </div>
      </div>
    </section>

  </main>

  <footer id="footer" class="footer position-relative dark-background">

    <div class="container text-center mt-4">
      <p>© 2025 Shiwkesh Schematics Private Limited. All Rights Reserved.</p>
    </div>

  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <script src="{{ asset('theme') }}/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('theme') }}/assets/vendor/php-email-form/validate.js"></script>
  <script src="{{ asset('theme') }}/assets/vendor/aos/aos.js"></script>
  <script src="{{ asset('theme') }}/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="{{ asset('theme') }}/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="{{ asset('theme') }}/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="{{ asset('theme') }}/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <script src="{{ asset('theme') }}/assets/js/main.js"></script>

</body>

</html>
