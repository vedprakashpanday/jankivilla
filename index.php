<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


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

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8" />
   <meta http-equiv="x-ua-compatible" content="ie=edge" />
   <meta charset="utf-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

   <title>Affordable Plots & Homes in Darbhanga | Amitabh Builders & Developers</title>

   <meta name="description" content="Find RERA-approved plots, flats & property deals near AIIMS, Laheriasarai & NH 57. Best real estate projects in Darbhanga by Amitabh Builders & Developers.">
   <meta name="keywords" content="plots in darbhanga, construction companies in darbhanga, best builders in darbhanga, top property developers in darbhanga, recidential projects darbhanga, affordable housing in darbhanga, residential plots near laheriasarai, new residential projects in darbhanga 2025, property for sale in shahpurchakka darbhanga, ongoing projects near barheta road darbhanga, affordable housing projects in darbhanga, plots near bus stand in darbhanga, plots near aiims darbhanga, Plots near DMCH Darbhanga, residential plots near darbhanga airport, rera approved plot in darbhanga, best property dealers in darbhanga, Plots for sale near Darbhanga Museum, Land near Darbhanga bus stand,popular Estate Agents For Residential Plots , plots in darbhanga, land in darbhanga ">
   <meta name="keyphrase" content="Darbhanga plot Price, Darbhanga residential plot, land for sale in laxmisagar Darbhanga, Plot for Sale in Darbhanga olx, Plot in darbhanga under rera, Plots in Darbhanga, Residential Land for Sale in Darbhanga, Best Plots in Darbhanga, Cheap and Best Plots in Darbhanga, Top Property Dealer in Darbhanga, Property Dealers in Darbhanga, Affordable Housing Projects in Darbhanga" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="canonical" href="https://www.jankivilla.com/index.php" title="Amitabh Builders & Developers" aria-label="Canonical - Amitabh Builders & Developers" />

   <meta property="og:title" content="Best Property Deals in Darbhanga | Amitabh Builders & Developers Pvt. Ltd.">
   <meta property="og:description" content="Buy RERA-approved plots & homes near AIIMS, NH-57, & Airport. Affordable housing & top residential projects in Darbhanga by Amitabh Builders & Developers.">
   <meta property="og:image" content="https://www.jankivilla.com/image/banner.png" title="Banner - Amitabh Builders & Developers Pvt. Ltd.">
   <meta property="og:url" content="https://www.jankivilla.com/" title="Amitabh Builders & Developers Pvt. Ltd.">
   <meta property="og:type" content="website">

   <meta name="twitter:card" content="summary_large_image">
   <meta name="twitter:title" content="Plots Near Bus Stand, Airport & AIIMS in Darbhanga">
   <meta name="twitter:description" content="Search best plots, homes & builders near Laheriasarai, New residential projects in Darbhanga 2025, Shahpur Chakka & Barheta Road. Affordable & RERA-approved projects by Amitabh Builders & Developers.">
   <meta name="twitter:image" content="https://www.jankivilla.com/image/banner.png">

   <link rel="alternate" hreflang="en-IN" href="https://www.jankivilla.com/" />
   <link rel="alternate" hreflang="hi-IN" href="https://www.jankivilla.com/" />
   <meta name="product" content="Security, Electricity, Garden, Park, Waste Management, Water Supply, Bank, Footpath, Hospital, Underground Drinage, Residential Plots, Commercial Plots, Near AIIMS, Near Airport, Near Bus Stand " />
   <meta name="theme-color" content="#f3f9ff">
   <link rel="apple-touch-icon" href="https://www.jankivilla.com/image/harihomes1-logo.png">
   <meta property="al:ios:url" content="https://www.jankivilla.com/">

   <meta name="search engines" content="ALL" />
   <meta name="Robots" content="INDEX,ALL" />
   <meta name="YahooSeeker" content="INDEX, FOLLOW" />
   <meta name="msnbot" content="INDEX, FOLLOW" />
   <meta name="googlebot" content="INDEX, FOLLOW" />
   <meta name="language" content="en-us" />
   <meta name="Expires" content="never" />
   <meta name="rating" content="General" />
   <meta name="Author" content="https://www.jankivilla.com/" />
   <meta name="Publisher" content="Amitabh Builders & Developers" />
   <meta name="copyright" content="Copyright (c) 2025 by Amitabh Builders & Developers" />

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="icon/harihomes1-fevicon.png" />
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

   <style>
      /* Set fixed dimensions for the swiper container */
      .swiper-wrapper {
         width: 35rem;
         /* Fixed width */
         height: 30rem;
         /* Fixed height */
      }

      /* Ensure the slides take the full size of the container */
      .swiper-slide {
         display: flex;
         justify-content: center;
         align-items: center;
         overflow: hidden;
         /* Prevents overflow */
         width: 100%;
         height: 100%;
      }

      /* Ensure images fit properly */
      .swiper-slide img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         /* Makes image cover the div without distortion */
         display: block;
      }
   </style>
</head>

<body>
   <form method="post" action="./" id="form1">

      <div>
         <div class="backtotop-wrap cursor-pointer">
            <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
               <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
         </div>
         <!-- Back to top end -->

         <!-- Offcanvas area start -->
         <div class="fix">
            <div class="offcanvas__info">
               <div class="offcanvas__wrapper">
                  <div class="offcanvas__content">
                     <div class="offcanvas__top mb-40 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                           <a href="index.php" title="Home Page - Amitabh Builders & Developers">
                              <img src="image/harihomes1-logo.png" alt="Header Logo" title=" Best Community Plots Near Darbhanga Airport">
                           </a>
                        </div>
                        <div class="offcanvas__close">
                           <button>
                              <i class="fal fa-times"></i>
                           </button>
                        </div>
                     </div>
                     <div class="offcanvas__search mb-25">
                        <p class="text-white">Amitabh Builders & Developers is very humbly endeavoring to generate the sense of belonging and responsibility to propagate and deliver only good in the best possible manner.</p>
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
                                 <a target="_blank"
                                    href="#">1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk, Bhuskaul, Darbhanga, Bihar, India, 846007</a>
                              </div>
                           </li>
                           <li class="d-flex align-items-center">
                              <div class="offcanvas__contact-icon mr-15">
                                 <i class="fal fa-phone"></i>
                              </div>
                              <div class="offcanvas__contact-text">
                                 <a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developers">+919031079721</a>
                              </div>
                           </li>
                           <li class="d-flex align-items-center">
                              <div class="offcanvas__contact-icon mr-15">
                                 <i class="fal fa-envelope">abdeveloperspl@gmail.com</i>
                              </div>
                              <div class="offcanvas__contact-text">
                                 <a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developers"><span class="mailto:abdeveloperspl@gmail.com" title="Email Id - Amitabh Builders & Developers"><span class=""></span></span></a>
                              </div>
                           </li>
                        </ul>
                     </div>
                     <div class="offcanvas__social">
                        <ul>
                           <li><a href="https://www.facebook.com/share/17Soc8dWP7/" title="Facebook - Amitabh Builders & Developers" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                           <li><a href="https://www.instagram.com/amitabh_builders?utm_source=qr&igsh=MXIzMnZ4aDVkb213MA==" title="Instagram - Amitabh Builders & Developers" target="_blank"><i class="fab fa-instagram"></i></a></li>
                           <li><a href="" title="Twitter - Amitabh Builders & Developers" target="_blank"><i class="fab fa-twitter"></i></a></li>
                           <li><a href="" title="Youtube - Amitabh Builders & Developers" target="_blank"><i class="fab fa-youtube"></i></a></li>
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
            <div class="bar-1"></div>
            <div class="bar-2"></div>
            <div class="bar-3"></div>
            <div class="bar-4"></div>


            <!-- Banner area start -->
            <section class="banner-section p-relative bg-color-1 fix">
               <div class="image-1 wow fadeInLeft" data-background="images/crain.png" alt="darbhanga plot, darbhanga residential plots, plot for sale in darbhanga" title="Best Plots for Sale in Darbhanga"></div>
               <div class="shape wow fadeInRight" data-wow-delay="700ms" data-background="images/banner-bg.png" alt="rera approved plot in darbhanga, rera approved projects in darbhanga, plot sale in darbhanga" title="Cheap and Best Residential land for sale in darbhanga"></div>
               <div class="container-fluid g-0">
                  <div class="row">
                     <div class="col-lg-6 col-md-12">

                        <div class="banner-content p-relative z-2 wow img-custom-anim-left" data-wow-delay="700ms">

                           <h1 class="banner-title">Your Gateway to Premium Properties</h1>
                           <p class="banner-text">Explore our portfolio to see a selection of our completed projects.</p>
                           <div class="banner-link">
                              <a class="primary-btn-1 btn-hover" href="completed_project.php" title="Completed Projects - Amitabh Builders & Developers">
                                 Explore Projects <i class="icon-arrow-double-right"></i>
                                 <span style="top: 147.172px; left: 108.5px;"></span>
                              </a>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-12">
                        <div class="image-3 w-img" style="mask-image:url(images/shape-10.png); -webkit-mask-image: url(images/shape-10.png);" alt="Affordable plots near railway station, residential land for sale near darbhanga station " title="Property Near Darbhanga Train Terminal | Land Development Near Darbhanga Medical College">
                           <img class="image" src="image/banner.png" alt="affordable housing in darbhanga, 2 bhk flats in shahpur darbhanga, 3 bhk flats in shahpur darbhanga " title="Top property developers in Darbhanga">
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Banner area end -->

            <!-- extra-image -->
            <div class="extra-image-container p-relative">
               <div class="extra-image text-center wow im float-bob-x" data-wow-delay="800ms">
                  <img src="image/clipart-man-standing.png" alt="Plots in Amitabh Builders & Developers Phase 3 Darbhanga" title="Plot for sale in Darbhanga Near Airport AIIMS">
               </div>
               <div class="extra-image-2 wow im float-bob-x" data-wow-delay="800ms">
                  <img src="images/shape-1.png" alt="Plot for sale in Darbhanga Near Airport Railway station AIIMS,  Plot near. Dilli more Darbhanga nearby bus stand and airport" title="Plot for sale at Sonki and Khutwara Chowk">
               </div>
            </div>
            <!-- extra-image -->

            <!-- About area start -->
            <section class="about-us-section pt-200 p-relative">
               <div class="custom-container">
                  <div class="row">
                     <div class="col-xxl-7 col-xl-7 col-lg-7">
                        <div class="about-us-image-area p-relative pr-15">
                           <div class="shape-bg-1 wow fadeInLeft" data-wow-delay="700ms" style="mask-image:url(images/about-bg-shape.png); -webkit-mask-image: url(images/about-bg-shape.png);" title="Residential Land Near Medical College Darbhanga | Property Near AIIMS Darbhanga Campus">
                              <img class="image-1" src="image/building pic3.jpg" alt="आप भी चाहते हैं,Darbhanga NH57Mabbi Me plot Lenato Aaj He sampark Kare, Land / Plot in Darbhanga" title="Plots for sale in Darbhanga - 119+ Residential Land / Plots in Darbhanga">
                           </div>
                           <div class="content">
                              <div class="inner p-relative">
                                 <div class="icon-box">
                                    <i class="icon-cross"></i>
                                 </div>
                                 <h4><span class="counter">24</span> + Years </h4>
                                 <h5>Experience</h5>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-xxl-5 col-xl-5 col-lg-5">
                        <div class="about-us-content-area wow fadeInRight" data-wow-delay=".5s">
                           <div class="title-box mb-15">
                              <span class="section-sub-title">About Us</span>
                              <h3 class="section-title mt-10">Trusted by Thousands</h3>
                           </div>
                           <p>Welcome to Amitabh Builders & Developers , where we specialize in transforming visions into reality. As a leading real estate development company, we pride ourselves on delivering high-quality, sustainable, and innovative building solutions.</p>
                           <div class="list-items-area mt-30 mb-30">
                              <ul class="list-items">
                                 <li>Find Your Future Home</li>
                                 <li>Experienced Agents</li>
                                 <li>Wide Range of Properties</li>
                              </ul>
                              <div class="icon-box float-bob-y">
                                 <div class="icon-1">
                                    <i class="icon-reward"></i>
                                 </div>
                                 <h3>
                                    <span class="counter">30</span><span class="plus">+</span>
                                 </h3>
                                 <span class="text-1">Awards Won</span>
                              </div>
                           </div>
                           <div class="icon-content-area pt-20 pb-20">
                              <div class="inner">
                                 <div class="icon-box-1">
                                    <i class="icon-crain"></i>
                                 </div>
                                 <div class="content">
                                    <h5>The Art and Science of Building</h5>
                                 </div>
                              </div>
                              <div class="inner">
                                 <div class="icon-box-1">
                                    <i class="icon-mixer-truck"></i>
                                 </div>
                                 <div class="content">
                                    <h5>Navigating Your Real Estate Journey</h5>
                                 </div>
                              </div>
                           </div>
                           <div class="about-us-button-area  mt-30">
                              <a class="primary-btn-1 btn-hover" href="#">
                                 Explore Services <i class="icon-arrow-double-right"></i>
                                 <span style="top: 147.172px; left: 108.5px;"></span>
                              </a>
                              <div class="chat-us">
                                 <div class="icon-2">
                                    <svg width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M31.4058 6.35513C28.1145 4.32519 24.2144 3.20898 20.1855 3.20898C9.21848 3.20898 0 11.3798 0 21.8418C0 25.5099 1.1395 29.0262 3.30401 32.063L0.28032 41.556C-0.0391289 42.5588 0.711981 43.5801 1.76008 43.5801C1.99971 43.5801 2.24091 43.5247 2.46346 43.4115L11.659 38.736C12.0313 38.8963 12.4083 39.0455 12.7891 39.184C10.6601 35.8609 9.52344 32.0464 9.52344 28.0527C9.52344 16.1591 19.5311 7.01546 31.4058 6.35513Z" fill="#FF8F27" />
                                       <path d="M49.696 38.274C51.8605 35.2371 53 31.7208 53 28.0527C53 17.5869 43.7776 9.41992 32.8145 9.41992C21.8474 9.41992 12.6289 17.5907 12.6289 28.0527C12.6289 38.5186 21.8513 46.6855 32.8145 46.6855C35.7561 46.6855 38.6871 46.0861 41.3405 44.9467L50.5365 49.6224C51.0946 49.9061 51.7687 49.8269 52.2458 49.4215C52.7229 49.016 52.9098 48.3636 52.7198 47.7671L49.696 38.274ZM26.5 29.6055C25.6425 29.6055 24.9473 28.9103 24.9473 28.0527C24.9473 27.1952 25.6425 26.5 26.5 26.5C27.3575 26.5 28.0527 27.1952 28.0527 28.0527C28.0527 28.9103 27.3575 29.6055 26.5 29.6055ZM32.7109 29.6055C31.8534 29.6055 31.1582 28.9103 31.1582 28.0527C31.1582 27.1952 31.8534 26.5 32.7109 26.5C33.5685 26.5 34.2637 27.1952 34.2637 28.0527C34.2637 28.9103 33.5685 29.6055 32.7109 29.6055ZM38.9219 29.6055C38.0644 29.6055 37.3691 28.9103 37.3691 28.0527C37.3691 27.1952 38.0644 26.5 38.9219 26.5C39.7794 26.5 40.4746 27.1952 40.4746 28.0527C40.4746 28.9103 39.7794 29.6055 38.9219 29.6055Z" fill="#1F212D" />
                                    </svg>
                                 </div>
                                 <div class="content p-relative z-1">
                                    <span>Chat Us Anytime</span>
                                    <h5><a href="tel:+919031079721" title="Contact Number - Amitabh Developers">+919031079721</a></h5>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- About area end -->

            <!-- Service area start -->
            <section class="service-slider-section section-space-bottom p-relative pb-0">
               <div class="shape-1" data-background="images/services-bg.png"></div>
               <div class="shape-2 float-bob-y" data-background="images/shape-2.png" alt="Residential land / Plot in Basdeopur, Darbhanga" title="Plot for Sale and Buy in Darbhanga"></div>
               <div class="custom-container">
                  <div class="title-box text-center mb-60 wow fadeInLeft" data-wow-delay=".5s">
                     <span class="section-sub-title">Best Services</span>
                     <h3 class="section-title mt-10 mb-25">Transforming Real Estate <br> Dreams into Reality</h3>
                  </div>
                  <div class="swiper service-active-1">
                     <div class="swiper-wrapper">
                        <!-- block -->
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">


                              <a class="image w-img" href="#">
                                 <img src="images/garden.png" alt="best property dealer in darbhanga, residential plots in darbhanga" title="Top property developers in Darbhanga">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-saw"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Garden</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <!-- block -->
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/park.png" alt="low budget plots in Shahpur Darbhanga, plots under 1000000 in darbhanga" title="Residential land for sale in darbhanga">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Park</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <!-- block -->
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="images/waste garbage.png" alt="plots near bus stand in darbhanga, land near bus stand in darbhanga" title="Commercial Plots Near Bus Stand Darbhanga">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-house"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Waste Management</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <!-- block -->
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/Water Supply.png" alt="cheap and best commercial plots near aiims darbhanga, low cost commercial plots near darbhanga" title="Cheap and Best Commercial Plots Near Darbhanga">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Water Supply</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/bank.png " alt="darbhanga plot price, residential projects in darbhanga, new plots development in darbhanga, upcoming residential projects in darbhanga" title="Residential Projects in Darbhanga">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Bank</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/Footpath.png " alt="affordable plots in darbhanga, real estate investment darbhanga" title="Residential Plot Near Darbhanga Airport">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Footpath</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/Healthcare.png " alt="residential plot for sale in darbhanga, new plots development in darbhanga" title="Residential Projects in Darbhanga">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Healthcare</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="images/yoga.png" alt="Amitabh Builders & Developers phase 1 darbhanga, new residential projects in darbhanga 2025" title="Amitabh Builders & Developers Barheta Road Laheriasarai">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Yoga & Medication</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/Security.png" alt="Ongoing projects in laheriasarai darbhanga, new plots development in darbhanga" title="Residential Plots Near Darbhanga airport">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Security</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="image/Drainage.png" alt="airport plots, darbhanga airport plots, darbhanga airport land" title="Cheap and best Land in Darbhanga Airport">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Underground Drainage</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>
                        <div class="swiper-slide">
                           <div class="service-slider-area p-relative">
                              <a class="image w-img" href="#">
                                 <img src="images/electric city.png" alt="plot for sale in darbhanga bus stand, buying plots near darbhanga bus stand, top realestate company in darbhanga" title="Best Real Estate Company in Darbhanga Bihar">
                              </a>
                              <div class="content-area">
                                 <div class="content text-center" style="padding: 0px 35px 84px;">
                                    <div class="icon-box">
                                       <i class="icon-concrete-mixer-1"></i>
                                    </div>
                                    <h5 class="mb-10"><a href="#">Electricity</a></h5>
                                 </div>
                              </div>
                              <a class="btn-icon" href="#">
                                 <i class="icon-right-arrow"></i>
                              </a>
                           </div>
                        </div>

                     </div>
                     <!-- If we need navigation buttons -->
                     <div class="service_1_navigation__wrapprer position-relative z-1 mt-100">
                        <div class="common-slider-navigation">
                           <div class="swiper-scrollbar"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>



            <!-- Service area end -->


            <!-- Gallery section -->




            <!-- Service provider area start -->
            <section class="service-provider-section p-relative">
               <div class="service-provider-container p-relative pt-90 pb-130 fix">
                  <div class="bg-image-1" data-background="images/feaures-bg-1.jpg" alt="residential plots near darbhanga railway station, commercial plots near darbhanga bus stand" title="Best Commercial plots Dealer in Darbhanga"></div>
                  <div class="shape-1" data-background="images/shape-3.png" alt="Amitabh Builders & Developers NH-57 Darbhanga, real estate agent for commercial in darbhanga, commercial real estate for sale" title="Commercial Residential Real Estate for Sale"></div>
                  <div class="custom-container">
                     <div class="row">
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
                           <div class="service-provider-content p-relative wow fadeInRight" data-wow-delay=".5s">
                              <div class="title-box mb-30 wow fadeInLeft" data-wow-delay=".5s">
                                 <span class="section-sub-title">amazing industry</span>
                                 <h3 class="section-title mt-10 mb-25">Best Industrial Services Provider</h3>
                                 <p>We leverage cutting-edge technologies to create compelling content <br>across various digital platforms.</p>
                              </div>
                              <div class="counter-area">
                                 <div class="counter-box">
                                    <div class="icon-box">
                                       <i class="icon-left-angle"></i>
                                    </div>
                                    <h2><span class="counter">230</span>+</h2>
                                    <h5>Project Complete</h5>
                                 </div>
                                 <div class="counter-box">
                                    <div class="icon-box">
                                       <i class="icon-left-angle"></i>
                                    </div>
                                    <h2><span class="counter">20</span>+</h2>
                                    <h5>Quality Team</h5>
                                 </div>
                                 <div class="counter-box">
                                    <div class="icon-box">
                                       <i class="icon-left-angle"></i>
                                    </div>
                                    <h2><span class="counter">30</span>+</h2>
                                    <h5>Years Of Experience</h5>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
                           <div class="service-provider-image-area">
                              <div class="image-1" style="mask-image:url(images/shape-5.png); -webkit-mask-image: url(images/shape-5.png);">
                                 <img src="images/service-provider-1.png" alt="commercial real estate land for sale, commercial land to buy, commercial real estate property management" title="Biggest Commercial Real Estate Companies">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="image-2 wow fadeInRight" data-wow-delay=".5s">
                  <img src="images/shape-4.png" alt="large commercial real estate companies, cheap commercial space for sale in darbhanga, find a commercial real estate broker" title="real estate business websites, commercial property business">
               </div>
            </section>
            <!-- Service provider area end -->

            <!-- Project area start -->
            <section class="project-section section-space">
               <div class="custom-container">
                  <div class="title-box text-center mb-60 wow fadeInLeft" data-wow-delay=".5s">
                     <span class="section-sub-title">Our Projects</span>
                     <h3 class="section-title mt-10 mb-25">Projects Nearby Places</h3>
                  </div>
                  <div class="row g-0">
                     <!-- block -->
                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInRight" data-wow-delay=".5s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Dharbhanga Airpoart.png" alt="buy commercial space, best commercial real estate company in near darbhanga airport, low price plots near me in darbhanga airport" title="Cheap and Best Commercial Real Estate Company in Near Darbhanga Airport">
                           </figure>
                           <div class="content">
                              <h2>4.5 KM</h2>
                              <h4>Dharbhanga Airpoart</h4>
                           </div>
                        </div>
                     </div>
                     <!-- block -->
                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInDown" data-wow-delay=".5s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Dharbhanga Railway station.png" alt="top 10 real estate company in near darbhanga railway station, best property consultant near me in darbhanga railway station" title="Commercial Property for Sale by Owner near me in Darbhanga Railway Station">
                           </figure>
                           <div class="content">
                              <h2>3 KM</h2>
                              <h4>Dharbhanga New Railway station</h4>
                           </div>
                        </div>
                     </div>
                     <!-- block -->
                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInLeft" data-wow-delay=".5s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/DMCH (1).png" alt="cheap land in near dmch, cheap and best land in near dmch, best property dealer company in dmch" title="Cheapest Plot in Darbhanga - Commercial Residential Real Estate in DMCH">
                           </figure>
                           <div class="content">
                              <h2>8 KM</h2>
                              <h4>DMCH</h4>
                           </div>
                        </div>
                     </div>

                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInRight" data-wow-delay=".7s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Darbhanga museum.png" alt="commercial real estate company in near darbhanga museum, large commercial real estate companies in darbhanga museum, business and real estate for sale in darbhanga museum" title="Large Commercial Real Estate Companies in Darbhanga Museum">
                           </figure>
                           <div class="content">
                              <h2>7.0 KM</h2>
                              <h4>Darbhanga Museum</h4>
                           </div>
                        </div>
                     </div>
                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInRight" data-wow-delay=".7s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Darbhanga Civil Court.png" alt="proerty management companies in darbhanga, commercial property management near me in near mabbi thana, land and commercial property for sale near mabbi thana darbhanga " title="Property Management Company Near Mabbi Thana Darbhanga">
                           </figure>
                           <div class="content">
                              <h2>10.0 KM</h2>
                              <h4>Dharbhanga Civil Court</h4>
                           </div>
                        </div>
                     </div>
                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInRight" data-wow-delay=".7s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Darbhanga AIIMS.png" alt="property for sale near me, property to buy near me, buy property in darbhanga" title="Property for Sale Near Me in Darbhanga">
                           </figure>
                           <div class="content">
                              <h2>3.5 KM</h2>
                              <h4>Dharbhanga AIIMS</h4>
                           </div>
                        </div>
                     </div>
                     <!-- block -->
                     <div class="col-xxl-4 col-xxl-4 col-lg-4 wow fadeInRight" data-wow-delay=".7s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Dharbhanga Bus Stand.png" alt="property website,sell plots in darbhanga" title="Residential Plots Near Bus Stand Darbhanga ">
                           </figure>
                           <div class="content">
                              <h2>4.0 KM</h2>
                              <h4>Dharbhanga Bus Stand</h4>
                           </div>
                        </div>
                     </div>
                     <!-- block -->
                     <div class="col-xxl-8 col-xxl-8 col-lg-8 wow fadeInLeft" data-wow-delay=".7s">
                        <div class="project-box-area p-relative">
                           <figure class="image w-img">
                              <img src="image/Mithila haat.png" alt="Plots near mithila haat, buy plots close to mithila haat, sit development near mithila haat" title="Property Near Mithila Haat Darbhanga">
                           </figure>
                           <div class="content">
                              <h2>35.0 KM</h2>
                              <h4>Mithila Haat</h4>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Project area end -->

            <!-- Features area start -->
            <section class="features-section bg-color-1 p-relative fix">
               <div class="shape-2" data-background="images/shape-6.png" alt="land near darbhanga bus stand, land near darbhanga bus stand, land near aiims darbhanga, property near railway station darbhanga" title="Residential Plots for Sale Near Bus Stand Darbhanga"></div>
               <div class="shape-3" data-background="images/shape-7.png" alt="residential plots for sale in darbhanga, approved plots in darbhanga city, plot developers in darbhanga" title="Residential Sites Near Prime Locations Darbhanga"></div>
               <div class="white-shape"></div>
               <div class="custom-container">
                  <div class="row g-0">
                     <div class="col-xxl-6 col-xl-6 col-lg-6 wow fadeInRight" data-wow-delay=".5s">
                        <div class="features-image-area p-relative" data-wow-delay="700ms">
                           <figure class="image w-img">
                              <img src="images/features-2.jpg" alt="plots near darbhanga railway station, land near new railway station darbhanga, residential land for sale near darbhanga station" title="Affordable Plots Near Railway Station Darbhanga">
                           </figure>
                           <figure class="image-shape w-img">
                              <img src="images/bg-1.png" alt="cheap and best residential land for buy near darbhanga railway station, property sites near darbhanga airport, community plots near darbhanga airport" title="Community Plots Near Darbhanga Airport">
                           </figure>
                        </div>
                     </div>
                     <div class="col-xxl-6 col-xl-6 col-lg-6">
                        <div class="features-content p-relative">
                           <div class="title-box mb-30 wow fadeInLeft" data-wow-delay=".5s">
                              <span class="section-sub-title">our core features</span>
                              <h3 class="section-title mt-10 mb-25">We're Building Better Projects</h3>
                              <p>

                                 Our projects are designed with modern architecture, premium materials, and sustainable practices, ensuring that every home we build offers comfort, elegance, and long-term value. Whether you're looking for your dream home or a profitable investment, Amitabh Builders & Developers is here to turn your vision into reality.
                                 <br>
                                 🏡 Quality Construction | Prime Locations | Customer-Centric Approach

                                 Let's build the future together!
                              </p>
                           </div>
                           <div class="row">
                              <div class="col-lg-6">
                                 <div class="icon-box">
                                    <div class="icon-1">
                                       <i class="icon-crain"></i>
                                    </div>
                                    <div class="content p-relative z-1">
                                       <h5><a href="#">Science of Building</a></h5>
                                       <span>We leverage cutting-edge.</span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div class="icon-box">
                                    <div class="icon-1">
                                       <i class="icon-mixer-truck"></i>
                                    </div>
                                    <div class="content p-relative z-1">
                                       <h5><a href="#">Engineering Marvels</a></h5>
                                       <span>Cutting edge technologies </span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>

            <!-- Features area end -->

            <!-- Team area start -->
            <!-- <section class="team-section p-relative z-1">
               <div class="custom-container">
                  <div class="title-box text-center mb-60 wow fadeInLeft" data-wow-delay=".5s">
                     <span class="section-sub-title">Professional Expert</span>
                     <h3 class="section-title mt-10 mb-25">Meet The Team</h3>
                  </div>
                  <div class="row g-3">
                     <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-15 wow fadeInRight" data-wow-delay=".5s">
                        <div class="team-area-box p-relative">
                           <figure class="image w-img">
                              <img src="image/HAriTeam/ceo.png" alt="Harihar Mahto - CEO of Amitabh Builders & Developers, Harihar Mahto - CEO of Amitabh Builders & Developers, CEO of Amitabh Builders & Developers, best property dealer in darbhanga bihar" title="Harihar Mahto is the CEO of Hari Homes / Amitabh Builders & Developers">
                           </figure>
                           <div class="content">
                              <div>
                                 <h5><a href="#"></a></h5>
                                 <span style="color:white">CEO</span><br />
                                 <span style="color:white">Harihar mahto</span>
                              </div>

                           </div>
                        </div>
                     </div>
                     
                     <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-15 wow fadeInLeft" data-wow-delay=".5s">
                        <div class="team-area-box p-relative">
                           <figure class="image w-img">

                              <img src="image/HAriTeam/Manager.png" alt="md akbar - manager - Amitabh Builders & Developers, md akbar - project head, cheap and best commercial land saler in darbhanga" title="MD Akbar is the Project Head of Amitabh Builders & Developers / Amitabh Builders & Developers" />
                           </figure>
                           <div class="content">
                              <div>
                                 <h5><a href="#"></a></h5>
                                 <span style="color:white">Manager / Project Head</span><br />
                                 <span style="color:white">Md Akbar</span>
                              </div>

                           </div>
                        </div>
                     </div>

                     <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-15 wow fadeInLeft" data-wow-delay=".5s">
                        <div class="team-area-box p-relative">
                           <figure class="image w-img">

                              <img src="image/HAriTeam/saleshead.png" alt="Himanshu Shekhar  Sales Head - Amitabh Builders & Developers, Best plot saler in shahpur chakka darbhanga" title="Best Plot Saler in NH-57 Near Vastu Vihar Darbhanga" />
                           </figure>
                           <div class="content">
                              <div>
                                 <h5><a href="#"></a></h5>
                                 <span style="color:white">Sales Head</span><br />
                                 <span style="color:white">Himanshu Shekhar </span>
                              </div>

                           </div>
                        </div>
                     </div>
                     <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-15 wow fadeInDown" data-wow-delay=".5s">
                        <div class="team-area-box p-relative">
                           <figure class="image w-img">
                              <img src="image/HAriTeam/saleshead.jpg" height="150" alt="rahul kumar - Sales Head Amitabh Builders & Developers, rahul kumar sales head Amitabh Builders & Developers" title="Rahul Kumar Sales Hade Amitabh Builders & Developers / Amitabh Builders & Developers" />
                           </figure>
                           <div class="content">
                              <div>
                                 <h5><a href="#"></a></h5>
                                 <span style="color:white">Founder Member / Sales Head</span><br />
                                 <span style="color:white">Mr Rahul Kumar </span>
                              </div>

                           </div>
                        </div>
                     </div>

                  
                  </div>
               </div>
            </section> -->
            <!-- Team area end -->

            <!-- Testimonials area start -->
            <section class="testimonials-section section-space-bottom p-relative">
               <div class="shape-1" data-background="images/shape-8.png" alt="best plots for home construcion in darbhanga, affordable land for sale darbhanga, residential plots for sale in darbhanga" title="Cheap and  Best Residential plot Saler in Darbhanga Bihar"></div>
               <div class="custom-container">
                  <div class="testimonials-title-box mb-60 p-relative">
                     <div class="row">
                        <div class="col-lg-6">
                           <div class="title-box wow fadeInLeft" data-wow-delay=".5s">
                              <span class="section-sub-title">Client Feedback</span>
                              <h3 class="section-title mt-10 mb-25">What Our Customers <br>Are Talking About</h3>
                           </div>
                        </div>

                     </div>
                  </div>
                  <div class="swiper testimonial-active-1">
                     <div class="swiper-wrapper">
                        <!-- block -->
                        <div class="item swiper-slide">
                           <div class="testimonials-box-area">
                              <div class="inner p-relative">
                                 <div class="upper-area mb-20">
                                    <div class="author-image">
                                       <img src="images/testimonial-1.png" alt="cheap and best property dealer in darbhanga bihar, best land broker in darbhanga bihar, land development near darbhanga medical collage" title="Residential Plots for Sale Near Bus Stand Darbhanga">
                                    </div>
                                    <div class="icon-1">
                                       <i class="icon-quote-2"></i>
                                    </div>
                                    <div class="author-info">
                                       <h5>Aarav Patel</h5>

                                       <ul class="testimonials-ratings">
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                       </ul>
                                    </div>
                                 </div>
                                 <p>It is a very good work to fulfill the dreams of the people, we have full faith and hope that people will continue to get a lot of love like this✨🙏</p>
                              </div>
                           </div>
                        </div>
                        <!-- block -->
                        <div class="item swiper-slide">
                           <div class="testimonials-box-area">
                              <div class="inner p-relative">
                                 <div class="upper-area mb-20">
                                    <div class="author-image">
                                       <img src="images/testimonial-2.png" alt="residential plots near darbhanga airport, land for sale near darbhanga airport, land near new railway station darbhanga" title="Land Development Near Darbhanga Medical Collage">
                                    </div>
                                    <div class="icon-1">
                                       <i class="icon-quote-2"></i>
                                    </div>
                                    <div class="author-info">
                                       <h5>Aryan Gupta</h5>
                                       <ul class="testimonials-ratings">
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                          <li><i class="fal fa-square-star"></i></li>
                                       </ul>
                                    </div>
                                 </div>
                                 <p>Nice location and plots in affordable price 🤝👌</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Testimonials area end -->

            <!-- Projects form area start -->
            <section class="projects-form-section p-relative bg-color-1 fix">
               <div class="car-shape" data-background="images/shape-9.png" alt="Land for residential use near aiims, site development near aiis hospital darbhanga, buy property near darbhanga bus depot" title="Land Investment Near Kameshwar Singh Museum"></div>
               <div class="round-yellow-shape" data-background="images/shape-11.png" alt="Land for residential use near aiims, site development near aiis hospital darbhanga, buy property near darbhanga bus depot" title="Land Investment Near Kameshwar Singh Museum"></div>
               <div class="custom-container">
                  <div class="row">
                     <div class="col-lg-6 col-md-12">
                        <div class="project-form-container pt-70 pb-70">
                           <div class="title-box mb-50 wow fadeInLeft" data-wow-delay=".5s">
                              <span class="section-sub-title">Get In touch</span>
                              <h3 class="section-title mt-10 mb-25">Talk About Your Next Dream Project</h3>
                           </div>
                           <div class="projects-form-area p-relative z-2 wow fadeInRight" data-wow-delay=".5s">
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
                                    <div class="col-lg-12">
                                       <button type="submit" name="btnsubmit" class="primary-btn-1 btn-hover">
                                          Send Message <i class="icon-arrow-double-right"></i>
                                          <span style="top: 147.172px; left: 108.5px;"></span>
                                       </button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-12 wow fadeInLeft" data-wow-delay=".5s">
                        <div class="projects-form-image-area p-relative">
                           <div class="shape-bg w-img">
                              <img class="image-1" src="images/project-form-1.png" alt="residential land near darbhanga historic zone, land development near darbhanga medical collage, property near darbhanga train terminal" title="Plots Near Laheriasarai Bus Stand">
                           </div>
                           <div class="play-btn">
                              <div class="video_player_btn">
                                 <a href="https://www.youtube.com/watch?v=eEzD-Y97ges" title="Affordable Land for Sale in Darbhanga Bihar" class="popup-video"><i class="icon-arrow_triangle-right"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Projects form area end -->

            <!-- Blog area start -->
            <section class="blog-section section-space">
               <div class="custom-container">
                  <div class="title-box text-center mb-60 wow fadeInLeft" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInLeft;">
                     <span class="section-sub-title">Latest News & Blogs</span>
                     <h3 class="section-title mt-10 mb-25">Read Our News <br>Blogs & Articles</h3>
                  </div>
                  <div class="row g-4 wow fadeInRight" data-wow-delay=".5s">
                     <!-- block -->
                     <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 mb-15">
                        <div class="blog-style-one p-relative">
                           <a class="image w-img" href="blog.php">
                              <img src="images/blog-1.jpg" alt="rera approved company in darbhanga bihar, rera approved plots in darbhanga bihar, best plot saler and buyer in darbhanga bihar, real estate plots under budget in darbhanga" title="RERA Approved Plots in Darbhanga bihar - Residential Plots for Sale in Darbhanga">
                           </a>
                           <div class="blog-content-area p-relative">
                              <div class="date-meta">
                                 <a class="date" href="#">14</a>
                                 <a class="month" href="#">April 2021</a>
                              </div>
                              <div class="post-meta">
                                 <ul>
                                    <li><i class="icon-user-2"></i> <a href="#">Login</a></li>
                                    <li><i class="icon-chat-bubble-1"></i> <a href="#">0 comments</a></li>
                                 </ul>
                              </div>
                              <h5 class="blog-title"><a href="blog.php">Bihar RERA penalises Green Vatika Homes, Arunendra Developers & Shiba Welcome.</a></h5>
                              <a class="blog-btn-box" href="blog.php" title="top 10 commercial real estate companies | Commercial and Residential Real Estate in Darbhanga Bihar">
                                 <span>Read More</span>
                                 <span><i class="icon-round-arrow-right"></i></span>
                              </a>
                           </div>
                        </div>
                     </div>
                     <!-- block -->
                     <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 mb-15">
                        <div class="blog-style-one p-relative">
                           <a class="image w-img" href="blog.php">
                              <img src="images/blog-2.jpg" alt="commercialreal estate investors near me, top commercial real estate companies in darbhanga, biggest commercial real estate companies in Darbhanga Bihar" title="Cheap Commercial Space for Sale | Find a commercial real estate broker in Darbhanga">
                           </a>
                           <div class="blog-content-area p-relative">
                              <div class="date-meta">
                                 <a class="date" href="#">15</a>
                                 <a class="month" href="#">DEC 2021</a>
                              </div>
                              <div class="post-meta">
                                 <ul>
                                    <li><i class="icon-user-2"></i> <a href="#">Login</a></li>
                                    <li><i class="icon-chat-bubble-1"></i> <a href="#">0 comments</a></li>
                                 </ul>
                              </div>
                              <h5 class="blog-title"><a href="blog.php" title="Selling Commercial Property by Owner | Commercial and Recidential Real Estate Market Analysis">Patna metro rail project awaits funds from Japan agency.</a></h5>
                              <a class="blog-btn-box" href="blog.php" title="List of Commercial Real Estate Brokers | Off Market commercial Real Estate for Sale | Cheap land in Darbhanga">
                                 <span>Read More</span>
                                 <span><i class="icon-round-arrow-right"></i></span>
                              </a>
                           </div>
                        </div>
                     </div>
                     <!-- block -->
                     <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 mb-15">
                        <div class="blog-style-one p-relative">
                           <a class="image w-img" href="blog.php" title="RERA Approved land for residential use near AIIMS Darbhanga Bihar">
                              <img src="images/blog-3.jpg" alt="rera approved plots near laeriasarai bus stand, rera approved site development near aiims hospital darbhanga, rera approved land development near darbhanga medical collage" title="RERA Approved Land Development Near Darbhanga Medical Collage">
                           </a>
                           <div class="blog-content-area p-relative">
                              <div class="date-meta">
                                 <a class="date" href="#">6</a>
                                 <a class="month" href="#">Nov 2023</a>
                              </div>
                              <div class="post-meta">
                                 <ul>
                                    <li><i class="icon-user-2"></i> <a href="#">Login</a></li>
                                    <li><i class="icon-chat-bubble-1"></i> <a href="#">0 comments</a></li>
                                 </ul>
                              </div>
                              <h5 class="blog-title"><a href="blog.php">Amina Construction fined Rs 7 lakh, RERA imposed penalty for advertising SUDHA COMPLEX without registration.</a></h5>
                              <a class="blog-btn-box" href="blog.php" title="Land for Residential use Near AIIMS Darbhanga">
                                 <span>Read More</span>
                                 <span><i class="icon-round-arrow-right"></i></span>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Blog area end -->


            <!-- Brand area start-->
            <!-- <section class="">
               <div class="custom-container">
                  <div class="row">
                     <div class="col-lg-12 col-md-12">
                        <div class="title-box wow fadeInLeft" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInLeft;">
                           <p style="text-align:center">Our Partners</p>
                           <h3 class="section-title mt-10 mb-25" style="text-align:center">RERA Registered</h3>
                        </div>
                     </div>

                     <div class="col-md-6 pt-2 pb-4">
                        <img src="image/indeximg/Rera%20Agent%20Certificate_page-0001.jpg" alt="site development near aiims darbhanga, land development near dmch darbhanga, plots near dmch darbhanga, plots near darbhanga airport" title="Lands Near Darbhanga Airport | Property for Sale Near Darbhanga Railway Station" />
                     </div>
                     <div class="col-md-6 pt-2 pb-4">
                        <img src="image/indeximg/Rera%20Agent%20Certificate_page-0002.jpg" alt="affordable plots near railway station darbhanga, affordable plots near bus stand darbhanga, affordable land near dmch darbhanga" title="Affordable Plots Near Railway Station Darbhanga" />
                     </div>

                  </div>
               </div>
            </section> -->
            <!-- Brand area end-->


         </div><!-- bar-wrapper-end -->

         <?php include 'footer.php'; ?>
         <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>


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
            emailjs.init('user_4JNFd46byV1DFNJopV4hK')
         </script>
         <script src="js/email-validation.js"></script>
         <script src="js/main.js"></script>
      </div>
   </form>




</body>

</html>