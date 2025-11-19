<?php
session_start();
include_once 'connectdb.php';


// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
   header("Location: UI/CustomerP/index.php"); // Redirect to dashboard
   exit;
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
   $customer_id = trim($_POST['customer_id']);
   $password = trim($_POST['password']);

   if (!empty($customer_id) && !empty($password)) {
      $stmt = $pdo->prepare("SELECT * FROM customer_details WHERE customer_id = ? AND password = ?");
      $stmt->execute([$customer_id, $password]);
      $customer = $stmt->fetch();

      if ($customer) {
         $_SESSION['customer_id'] = $customer['customer_id']; // Store customer ID in session
         echo "<script>alert('Login Successful!'); window.location.href='UI/CustomerP/index.php';</script>";
      } else {
         $error = "Invalid Customer ID or Password.";
      }
   } else {
      $error = "Please enter both Customer ID and Password.";
   }
}
?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>
      Amitabh Builders & Developers
   </title>
   <meta name="description">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="icon/harihomes1-fevicon.png">
   <!-- CSS here -->
   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/meanmenu.min.css">
   <link rel="stylesheet" href="css/animate.css">
   <link rel="stylesheet" href="css/swiper.min.css">
   <link rel="stylesheet" href="css/slick.css">
   <link rel="stylesheet" href="css/magnific-popup.css">
   <link rel="stylesheet" href="css/fontawesome-pro.css">
   <link rel="stylesheet" href="css/icomoon.css">
   <link rel="stylesheet" href="css/spacing.css">
   <link rel="stylesheet" href="css/sweetalert2.min.css">
   <link rel="stylesheet" href="css/main.css">

</head>

<body>
   <div class="aspNetHidden">
      <input type="hidden" name="" id="" value="">
      <input type="hidden" name="" id="" value="">
      <input type="hidden" name="" id="" value="">
   </div>

   <script type="text/javascript">
      //<![CDATA[
      var theForm = document.forms['form1'];
      if (!theForm) {
         theForm = document.form1;
      }

      function __doPostBack(eventTarget, eventArgument) {
         if (!theForm.onsubmit || (theForm.onsubmit() != false)) {
            theForm.__EVENTTARGET.value = eventTarget;
            theForm.__EVENTARGUMENT.value = eventArgument;
            theForm.submit();
         }
      }
      //]]>
   </script>


   <script src="/WebResource.axd?d=0wWLeSTyfKNTGForrd_HM7UcQ_BlZaHtlaMZYJoOu9xXDeX9f5g_c_F792l8_FFaX8Vif-PfoTSahp8UfAIOvxOxy7fHqxuaN3u5bjQfhsc1&amp;t=638627972640000000" type="text/javascript"></script>


   <script src="/WebResource.axd?d=YcP6f8KgqV_koqqHLnEnewXDz3QqPu1rXZAo9-FjPog7j0T5pjBLWCsC-h5cU5MvFXBh326CoacEeLDqoeVBnwdu8gSSOKzscu4R9epO2hA1&amp;t=638627972640000000" type="text/javascript"></script>
   <script type="text/javascript">
      //<![CDATA[
      function WebForm_OnSubmit() {
         if (typeof(ValidatorOnSubmit) == "function" && ValidatorOnSubmit() == false) return false;
         return true;
      }
      //]]>
   </script>

   <div class="aspNetHidden">

      <input type="hidden" name="" id="" value="">
      <input type="hidden" name="" id="" value="">
   </div>
   <div>
      <div class="backtotop-wrap cursor-pointer">
         <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
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
                        <button class="vw-search-close-btn vw-search-close-btn"></button>
                     </div>

                     <div class="vw-search-input">
                        <input type="text" placeholder="Type your keyword &amp; hit the enter button...">
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
                  <div class="offcanvas__top mb-40 d-flex justify-content-between align-items-center">
                     <div class="offcanvas__logo">
                        <a href="index.php">
                           <img src="image/harihomes1-logo.png" alt="Header Logo">
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
                              <a target="_blank" href="#">1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk, Bhuskaul, Darbhanga, Bihar, India, 846007</a>
                           </div>
                        </li>
                        <li class="d-flex align-items-center">
                           <div class="offcanvas__contact-icon mr-15">
                              <i class="fal fa-phone"></i>
                           </div>
                           <div class="offcanvas__contact-text">
                              <a href="tel:+919031079721">+919031079721</a>
                           </div>
                        </li>
                        <li class="d-flex align-items-center">
                           <div class="offcanvas__contact-icon mr-15">
                              <i class="fal fa-envelope"></i>
                           </div>
                           <div class="offcanvas__contact-text">
                              <a href="tel:+919031079721"><span class="mailto:abdeveloperspl@gmail.com">abdeveloperspl@gmail.com</span></a>
                           </div>
                        </li>
                     </ul>
                  </div>
                  <div class="offcanvas__social">
                     <ul>
                        <li><a href="https://www.facebook.com/share/17Soc8dWP7/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="https://www.instagram.com/amitabh_builders?utm_source=qr&igsh=MXIzMnZ4aDVkb213MA==" target="_blank"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="" target="_blank"><i class="fab fa-youtube"></i></a></li>
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

      <div class="breadcrumb__area theme-bg-1 p-relative" data-background="images/page-title-1.jpg" style="background-image: url(&quot;images/page-title-1.jpg&quot;);">
         <div class="bar-top" data-background="images/top-bar.png" style="background-image: url(&quot;images/top-bar.png&quot;);"></div>
         <div class="bar-bottom" data-background="images/bottom-bar.png" style="background-image: url(&quot;images/bottom-bar.png&quot;);"></div>
         <div class="yellow-shape" data-background="images/shape-12.png" style="background-image: url(&quot;images/shape-12.png&quot;);"></div>
         <div class="custom-container">
            <div class="row justify-content-center">
               <div class="col-xxl-12">
                  <div class="breadcrumb__wrapper p-relative" style="position:relative; top:55px;">
                     <img src="image/royal-d.png" alt="royal enclave plot in darbhanga">
                     <div class="breadcrumb__menu">
                        <nav>
                           <ul>
                              <li><span><a href="index.php">Home</a></span></li>
                              <li><span>Customer Portal</span></li>
                           </ul>
                        </nav>
                     </div>
                     <h2 class="breadcrumb__title">Login</h2>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- BREADCRUMB AREA END -->

      <!-- LOGIN AREA START -->



      <style>
         .login-form {
            border: 3px solid #f1f1f1;
            padding-top: 40px;
            padding-bottom: 40px;
            background: url(images/shape-8.png);
         }

         input[type=text],
         input[type=password] {
            width: 100%;
            margin: 8px 0;
            padding: 12px 20px;
            display: inline-block;
            border: 2px solid #ff8f27;
            box-sizing: border-box;
         }

         button:hover {
            opacity: 0.7;
         }

         .cancelbtn {
            width: auto;
            padding: 10px 18px;
            margin: 10px 5px;
         }


         .container {
            padding: 35px 35px;
            background: url(images/shape-12.png);
         }
      </style>
      <center>
         <h1> Customer Login </h1>
      </center>
      <div class="login-form">
         <div class="container col-md-4 mt-5">
            <!-- <h2 class="text-center">Customer Login</h2> -->

            <?php if (isset($error)): ?>
               <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
               <label>Customer ID:</label>
               <input type="text" name="customer_id" class="form-control" placeholder="Enter Customer ID" required>

               <label>Password:</label>
               <input type="password" name="password" class="form-control" placeholder="Enter Password" required>

               <input type="submit" name="login" value="Log In" class="btn btn-primary mt-3" style="height: 50px;font-size:medium;">
            </form>
         </div>
      </div>


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
         emailjs.init('user_4JNFd46byV1DFNJopV4hK')
      </script>
      <script src="js/email-validation.js"></script>
      <script src="js/main.js"></script>
   </div>
   </form>

   <script type="text/javascript">
      //<![CDATA[
      var Page_Validators = new Array(document.getElementById("ContentPlaceHolder1_RequiredFieldValidator1"), document.getElementById("ContentPlaceHolder1_RequiredFieldValidator2"));
      //]]>
   </script>

   <script type="text/javascript">
      //<![CDATA[
      var ContentPlaceHolder1_RequiredFieldValidator1 = document.all ? document.all["ContentPlaceHolder1_RequiredFieldValidator1"] : document.getElementById("ContentPlaceHolder1_RequiredFieldValidator1");
      ContentPlaceHolder1_RequiredFieldValidator1.controltovalidate = "ContentPlaceHolder1_txtUserName";
      ContentPlaceHolder1_RequiredFieldValidator1.focusOnError = "t";
      ContentPlaceHolder1_RequiredFieldValidator1.errormessage = "Required";
      ContentPlaceHolder1_RequiredFieldValidator1.display = "None";
      ContentPlaceHolder1_RequiredFieldValidator1.validationGroup = "login";
      ContentPlaceHolder1_RequiredFieldValidator1.evaluationfunction = "RequiredFieldValidatorEvaluateIsValid";
      ContentPlaceHolder1_RequiredFieldValidator1.initialvalue = "";
      var ContentPlaceHolder1_RequiredFieldValidator2 = document.all ? document.all["ContentPlaceHolder1_RequiredFieldValidator2"] : document.getElementById("ContentPlaceHolder1_RequiredFieldValidator2");
      ContentPlaceHolder1_RequiredFieldValidator2.controltovalidate = "ContentPlaceHolder1_txtPassword";
      ContentPlaceHolder1_RequiredFieldValidator2.focusOnError = "t";
      ContentPlaceHolder1_RequiredFieldValidator2.errormessage = "Required";
      ContentPlaceHolder1_RequiredFieldValidator2.display = "None";
      ContentPlaceHolder1_RequiredFieldValidator2.validationGroup = "login";
      ContentPlaceHolder1_RequiredFieldValidator2.evaluationfunction = "RequiredFieldValidatorEvaluateIsValid";
      ContentPlaceHolder1_RequiredFieldValidator2.initialvalue = "";
      //]]>
   </script>


   <script type="text/javascript">
      //<![CDATA[

      var Page_ValidationActive = false;
      if (typeof(ValidatorOnLoad) == "function") {
         ValidatorOnLoad();
      }

      function ValidatorOnSubmit() {
         if (Page_ValidationActive) {
            return ValidatorCommonOnSubmit();
         } else {
            return true;
         }
      }
      //]]>
   </script>



</body>

</html>