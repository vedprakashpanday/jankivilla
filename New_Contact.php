<?php
include_once 'connectdb.php';

// Process form submission
if (isset($_POST['btnsubmit'])) {
   $full_name = $_POST['full_name'];
   $email = $_POST['email'];
   $phone = $_POST['phone'] ?? null;
   $subject = $_POST['subject'] ?? null;
   $message = $_POST['message'];

   // Prepare and execute query
   $sql = "INSERT INTO contact_form (full_name, email, phone, subject, message) 
           VALUES (:full_name, :email, :phone, :subject, :message)";

   $stmt = $pdo->prepare($sql);

   $stmt->execute([
      ':full_name' => $full_name,
      ':email' => $email,
      ':phone' => $phone,
      ':subject' => $subject,
      ':message' => $message
   ]);

   echo "<script>alert('Your message has been sent successfully!');</script>";
}


?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8" />
   <meta http-equiv="x-ua-compatible" content="ie=edge" />
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

   <title>Buy RERA Plots in Darbhanga Near AIIMS | +919031079721</title>

   <meta name="description" content="Buy RERA-approved plots near AIIMS, DMCH, Airport, Bus Stand & Railway Station in Darbhanga. Trusted & affordable plot sellers. Call now!">
   <meta name="keywords" content="rera approved plot saler, rera approved plots near aiims darbhanga, rera approved plots near me in dmch, best plot saler in darbhanga, cheap and best plot saler in darbhanga, near airport darbhanga, near bus stand darbhanga, near darbhanga medical collage and hospital, near railway station darbhanga, reating, best plot dealer in darbhanga bihar, plots in darbhanga, plots near darbhanga airport, plots near darbhanga bus stand, land for sale near aiims darbhanga, property near buy darbhanga">
   <meta name="keyphrase" content="Darbhanga plot Price, Darbhanga residential plot, land for sale in laxmisagar Darbhanga, Plot for Sale in Darbhanga olx, Plot in darbhanga under rera, Plots in Darbhanga, Residential Land for Sale in Darbhanga, Best Plots in Darbhanga, Cheap and Best Plots in Darbhanga, Top Property Dealer in Darbhanga, Property Dealers in Darbhanga, Affordable Housing Projects in Darbhanga" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="canonical" href="https://www.jankivilla.com/New_Contact.php" title="Amitabh Builders & Developerss" aria-label="Canonical - Amitabh Builders & Developerss" />

   <meta property="og:title" content="Plots for Sale Near AIIMS & Airport Darbhanga">
   <meta property="og:description" content="Explore cheap & best RERA-approved plots near Darbhanga AIIMS, DMCH, Bus Stand & Railway Station. Trusted dealers with verified listings." />
   <meta property="og:image" content="https://www.jankivilla.com/image/banner.png" title="Banner - Amitabh Builders & Developerss">
   <meta property="og:url" content="https://www.jankivilla.com/" title="Amitabh Builders & Developerss">
   <meta property="og:type" content="website">

   <meta name="twitter:card" content="summary_large_image">
   <meta name="twitter:title" content="Buy RERA Plots in Darbhanga | Near You">
   <meta name="twitter:description" content="Find top-rated, affordable plots near AIIMS, Darbhanga Airport, Bus Stand & DMCH. Verified RERA-approved land from trusted sellers. Book now.">
   <meta name="twitter:image" content="https://www.jankivilla.com/image/banner.png">

   <link rel="alternate" hreflang="en-IN" href="https://www.jankivilla.com/" />
   <link rel="alternate" hreflang="hi-IN" href="https://www.jankivilla.com/" />
   <meta name="product" content="Security, Electricity, Garden, Park, Waste Management, Water Supply, Bank, Footpath, Hospital, Underground Drinage, Residential Plots, Commercial Plots, Near AIIMS, Near Airport, Gym, Near Bus Stand " />
   <meta name="theme-color" content="#f3f9ff">
   <link rel="apple-touch-icon" href="https://www.jankivilla.com/image/harihomes1-logo.png">
   <meta property="al:ios:url" content="https://www.jankivilla.com/">
   <meta name="content-language" content="EN" />
   <meta name="search engines" content="ALL" />
   <meta name="Robots" content="INDEX,ALL" />
   <meta name="YahooSeeker" content="INDEX, FOLLOW" />
   <meta name="msnbot" content="INDEX, FOLLOW" />
   <meta name="googlebot" content="INDEX, FOLLOW" />
   <meta name="language" content="en-us" />
   <meta name="Expires" content="never" />
   <meta name="rating" content="General" />
   <meta name="Author" content="https://www.jankivilla.com/" />
   <meta name="Publisher" content="Amitabh Builders & Developerss" />
   <meta name="copyright" content="Copyright (c) 2025 by Amitabh Builders & Developerss" />

   <!-- Place favicon.ico in the root directory -->
   <link
      rel="shortcut icon"
      type="image/x-icon"
      href="icon/harihomes1-fevicon.png" />
   <!-- CSS here -->
   <link rel="stylesheet" href="css/bootstrap.min.css" />
   <link rel="stylesheet" href="css/meanmenu.min.css" />
   <link rel="stylesheet" href="css/animate.css" />
   <link rel="stylesheet" href="css/swiper.min.css" />
   <link rel="stylesheet" href="css/slick.css" />
   <link rel="stylesheet" href="css/magnific-popup.css" />
   <link rel="stylesheet" href="css/fontawesome-pro.css" />
   <link rel="stylesheet" href="css/icomoon.css" />
   <link rel="stylesheet" href="css/spacing.css" />
   <link rel="stylesheet" href="css/sweetalert2.min.css" />
   <link rel="stylesheet" href="css/main.css" />

</head>

<body>



   <div>
      <div class="backtotop-wrap cursor-pointer">
         <svg
            class="backtotop-circle svg-content"
            width="100%"
            height="100%"
            viewBox="-1 -1 102 102">
            <path
               d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
               style="
                transition: stroke-dashoffset 10ms linear;
                stroke-dasharray: 307.919, 307.919;
                stroke-dashoffset: 307.919;
              "></path>
         </svg>
      </div>
      <!-- Back to top end -->

      <!-- search area start -->
      <div class="vw-search-area">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="vw-search-form">
                     <div class="vw-search-close text-center mb-20">
                        <button
                           class="vw-search-close-btn vw-search-close-btn"></button>
                     </div>

                     <div class="vw-search-input">
                        <input
                           type="text"
                           placeholder="Type your keyword &amp; hit the enter button..." />
                        <button type="submit"><i class="icon-search"></i></button>
                     </div>
                     <div class="vw-search-category">
                        <!-- <span>Search by : </span>
                        <a href="#">Agency, </a>
                        <a href="#">Artist Portfolio, </a>
                        <a href="#">Branding, </a>
                        <a href="#">creative portfolio, </a>
                        <a href="#">Design </a> -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="body-overlay"></div>
      <!-- search area end -->

      <!-- Offcanvas area start -->
      <div class="fix">
         <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
               <div class="offcanvas__content">
                  <div
                     class="offcanvas__top mb-40 d-flex justify-content-between align-items-center">
                     <div class="offcanvas__logo">
                        <a href="index.php" title="Home Page - Amitabh Builders & Developerss ">
                           <img src="image/harihomes1-logo.png" alt="rera approved plot in darbhanga, best plots in darbhanga" title="Rera Approved plots in Darbhanga" />
                        </a>
                     </div>
                     <div class="offcanvas__close">
                        <button>
                           <i class="fal fa-times"></i>
                        </button>
                     </div>
                  </div>
                  <div class="offcanvas__search mb-25">
                     <p class="text-white">
                        Amitabh Builders & Developerss is very humbly endeavoring to generate
                        the sense of belonging and responsibility to propagate and
                        deliver only good in the best possible manner.
                     </p>
                  </div>
                  <div class="mobile-menu fix mb-40"></div>
                  <div class="offcanvas__contact mt-30 mb-20">
                     <h4>Contact Info</h4>
                     <ul>
                        <li class="d-flex align-items-center">
                           <div class="offcanvas__contact-icon mr-15">
                              <i class="fal fa-map-marker-alt"></i>
                           </div>
                           <div class="offcanvas__contact-text">
                              <a target="_blank" href="#">1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk, Bhuskaul, Darbhanga, Bihar, India, 846007</a>
                           </div>
                        </li>
                        <li class="d-flex align-items-center">
                           <div class="offcanvas__contact-icon mr-15">
                              <i class="fal fa-phone"></i>
                           </div>
                           <div class="offcanvas__contact-text">
                              <a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developerss">+919031079721</a>
                           </div>
                        </li>
                        <li class="d-flex align-items-center">
                           <div class="offcanvas__contact-icon mr-15">
                              <i class="fal fa-envelope"></i>
                           </div>
                           <div class="offcanvas__contact-text">
                              <a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developerss"><span class="mailto:abdeveloperspl@gmail.com" title="Email Id - Amitabh Builders & Developerss">abdeveloperspl@gmail.com</span></a>
                           </div>
                        </li>
                     </ul>
                  </div>
                  <div class="offcanvas__social">
                     <ul>
                        <li>
                           <a
                              href="https://www.facebook.com/share/17Soc8dWP7/" title="Facebook - Amitabh Builders & Developerss"
                              target="_blank"><i class="fab fa-facebook-f"></i></a>
                        </li>
                        <li>
                           <a
                              href="https://www.instagram.com/amitabh_builders?utm_source=qr&igsh=MXIzMnZ4aDVkb213MA==" title="Instagram - Amitabh Builders & Developerss"
                              target="_blank"><i class="fab fa-instagram"></i></a>
                        </li>
                        <li>
                           <a href="" title="Twitter - - Amitabh Builders & Developerss" target="_blank"><i class="fab fa-twitter"></i></a>
                        </li>
                        <li>
                           <a
                              href="" title="Youtube - Amitabh Builders & Developerss"
                              target="_blank"><i class="fab fa-youtube"></i></a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="offcanvas__overlay"></div>
      <div class="offcanvas__overlay-white"></div>
      <!-- Offcanvas area start -->

      <!-- Header area start -->
      <?php include 'header.php'; ?>

      <div class="bar-wrapper-container p-relative">
         <!-- page title area start -->
         <div
            class="breadcrumb__area theme-bg-1 p-relative"
            data-background="images/page-title-1.jpg" alt="plots in darbhanga, best plots in darbhanga, cheap and best plot saler in darbhanga" title="RERA Approved Projects in Near AIIMS Darbhanga"
            style="background-image: url('images/page-title-1.jpg')" alt="residential plots in darbhanga, commercial plots in darbhanga, rera approved plots in darbhanga" title="Top Plot saler in darbhanga">
            <div
               class="bar-top"
               data-background="images/top-bar.png"
               style="background-image: url('images/top-bar.png')" alt="plots near aiims darbhanga, lands near railway station darbhanga, plots near dmch" title="Residential plots Near Darbhanga Callege and Medical Hospital Darbhanga"></div>
            <div
               class="bar-bottom"
               data-background="images/bottom-bar.png"
               style="background-image: url('images/bottom-bar.png')" alt="best plot saler in darbhanga bihar, commercial plots near airport darbhanga" title="Residential Plots For Sale Near AIIMS and Airport Darbhanga "></div>
            <div
               class="yellow-shape"
               data-background="images/shape-12.png"
               style="background-image: url('images/shape-12.png')" alt="best plots in darbhanga, low cost land near aiims darbhanga, low cost land near airport Darbhanga" title="Low Cost Plots Near AIIMS, Airport and Railway Station Darbhanga"></div>
            <div class="custom-container">
               <div class="row justify-content-center">
                  <div class="col-xxl-12">
                     <div class="breadcrumb__wrapper p-relative">
                        <img
                           src="image/royal-d.png"
                           alt="Amitabh Builders & Developers plot in darbhanga" />
                        <div class="breadcrumb__menu">
                           <nav>
                              <ul>
                                 <li>
                                    <span><a href="index.php" title="Home Page - Amitabh Builders & Developerss ">Home</a></span>
                                 </li>
                                 <li><span>Contact</span></li>
                              </ul>
                           </nav>
                        </div>
                        <h2 class="breadcrumb__title">Contact</h2>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- page title area end -->

         <section class="contact-info-section section-space">
            <div class="container">
               <div class="row g-4">
                  <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                     <div class="contact-info-box">
                        <div class="icon">
                           <i class="fal fa-location-dot"></i>
                        </div>
                        <h5 class="mt-15 mb-15">Our Location</h5>
                        <a href="#">1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk, Bhuskaul, Darbhanga, Bihar, India, 846007</a>
                     </div>
                  </div>
                  <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                     <div class="contact-info-box">
                        <div class="icon">
                           <i class="far fa-envelope"></i>
                        </div>
                        <h5 class="mt-15 mb-15">Email Id</h5>
                        <a href="mailto:abdeveloperspl@gmail.com" title="Email id - Amitabh Builders & Developerss.">abdeveloperspl@gmail.com</a>
                     </div>
                  </div>
                  <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                     <div class="contact-info-box">
                        <div class="icon">
                           <i class="fal fa-phone-alt"></i>
                        </div>
                        <h5 class="mt-15 mb-15">Contact Number</h5>
                        <a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developerss">+91-9031079721</a>
                     </div>
                  </div>
                  <div class="contact-wrapper pt-80">
                     <div class="row gy-50">
                        <div class="col-xxl-6 col-xl-6">
                           <div class="contact-map">
                              <iframe
                                 src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14322.331372765317!2d85.8688755!3d26.177712!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x66dbc4b931bed05%3A0xb33846685742e4d!2sHari%20Home%20Developers!5e0!3m2!1sen!2sin!4v1719488614563!5m2!1sen!2sin"
                                 width="600"
                                 height="450"
                                 style="border: 0"
                                 allowfullscreen=""
                                 loading="lazy"
                                 referrerpolicy="no-referrer-when-downgrade"></iframe>
                           </div>
                        </div>
                        <div class="col-xxl-6 col-xl-6">
                           <div class="contact-from">
                              <form method="post">
                                 <div class="row">
                                    <div class="col-lg-6">
                                       <div class="contact__from-input">
                                          <input name="full_name" type="text" placeholder="Full Name" required />
                                       </div>
                                    </div>
                                    <div class="col-lg-6">
                                       <div class="contact__from-input">
                                          <input name="email" type="email" placeholder="Email Address*" required />
                                       </div>
                                    </div>
                                    <div class="col-lg-6">
                                       <div class="contact__from-input">
                                          <input name="phone" type="text" placeholder="Phone Number" />
                                       </div>
                                    </div>
                                    <div class="col-lg-6">
                                       <div class="contact__from-input">
                                          <input name="subject" type="text" placeholder="Subject" />
                                       </div>
                                    </div>
                                    <div class="col-lg-12">
                                       <div class="contact__from-input">
                                          <textarea name="message" rows="2" placeholder="Your Message*" required></textarea>
                                       </div>
                                    </div>
                                    <div class="col-12">
                                       <div class="">
                                          <input type="submit" name="btnsubmit" value="Submit" class="primary-btn-4 btn-hover" />
                                       </div>
                                    </div>
                                 </div>
                              </form>

                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
      </div>
      <!-- bar-wrapper-end -->

      <?php include 'footer.php'; ?>


      <script src="js/jquery-3.6.0.min.js"></script>
      <script src="js/waypoints.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/meanmenu.min.js"></script>
      <script src="js/swiper.min.js"></script>
      <script src="js/slick.min.js"></script>
      <script src="js/magnific-popup.min.js"></script>
      <script src="js/counterup.js"></script>
      <script src="js/wow.js"></script>
      <script src="js/TweenMax.min.js"></script>
      <script src="js/sweetalert2.all.min.js"></script>
      <script src="js/email.min.js"></script>
      <script>
         emailjs.init("user_4JNFd46byV1DFNJopV4hK");
      </script>
      <script src="js/email-validation.js"></script>
      <script src="js/main.js"></script>
   </div>

</body>

</html>