<?php
session_start();
include_once 'connectdb.php';

// error_reporting(0);        // this type of predefined function use to avoid some notices like warning type //
error_reporting(E_ALL);
ini_set('display_errors', 1);



if (isset($_POST['btn_login'])) {
    $sponsorid = $_POST['sponsor_id'];
    $sponsorpass = $_POST['sponsor_pass'];

    // Check if "Remember Me" checkbox is checked
    if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
        setcookie('sponsor_login', json_encode(['sponsorid' => $sponsorid, 'sponsorpass' => $sponsorpass]), time() + 2592000, "/");
    } else {
        setcookie('sponsor_login', '', time() - 3600, "/");
    }

    // Verify login credentials
    $select = $pdo->prepare("SELECT * FROM tbl_hire WHERE sponsor_id = ? AND sponsor_pass = ?");
    $select->execute([$sponsorid, $sponsorpass]);
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Check Aadhaar number in tbl_regist using mem_sid
        $aadhar_check = $pdo->prepare("SELECT aadhar_number FROM tbl_regist WHERE mem_sid = ?");
        $aadhar_check->execute([$sponsorid]);
        $aadhar_row = $aadhar_check->fetch(PDO::FETCH_ASSOC);

        if ($aadhar_row && !empty($aadhar_row['aadhar_number'])) {
            // Aadhaar exists, proceed with login
            $_SESSION['sponsor_id'] = $row['sponsor_id'];
            $_SESSION['sponsor_pass'] = $row['sponsor_pass'];
            $_SESSION['sponsor_name'] = $row['s_name'];
            $_SESSION['status'] = $row['status'];

            header("Refresh:1; url=UI/Associate/Default.php");
        } else {
            // Aadhaar missing, set session variable to trigger modal
            $_SESSION['kyc_required'] = $sponsorid;
            $errormsg = 'kyc_required';
        }
    } else {
        $errormsg = 'error';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        .header-logo img {
            /* width: 100%; */
            background-color: #fff;
        }

        .bell-container {
            position: relative;
            display: inline-block;
            font-size: 25px;
            cursor: pointer;
        }



        .bell-badge {
            position: absolute;
            top: 0;
            right: 0px;
            background: red;
            color: white;
            font-size: 11px;
            font-weight: bold;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            text-align: center;
            line-height: 15px;
        }

        /* Shake animation */
        @keyframes vibrate {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(-10deg);
            }

            50% {
                transform: rotate(10deg);
            }

            75% {
                transform: rotate(-10deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .vibrating {
            animation: vibrate 0.3s ease-in-out infinite;
        }
    </style>

</head>

<body>
    <!-- <form method="post" onsubmit="javascript:return WebForm_OnSubmit();" id="form1"> -->
    <div class="aspNetHidden">
        <input type="hidden" name="__EVENTTARGET" id="__EVENTTARGET" value="">
        <input type="hidden" name="__EVENTARGUMENT" id="__EVENTARGUMENT" value="">
        <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwULLTE4OTIxOTg5NjIPZBYCZg9kFgICAw9kFgICAQ9kFgICAQ8PFgIeB1Zpc2libGVoZGRkQ079n1uVoJFXqMZpc32D1rLuVZbZwGZr1A54h7v7iWI=">
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

        <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="B8B84CAE">
        <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/wEdAATsDte+vC+3Vry32dWlORwN9nsvgpcFVLgi4qzbwR9V0Hjjswiyhm+g6KodwobC/fPWEk3uQd90Y7TvVghyoMnc24S4mVSOlfvnTuxILBPzGl2qiSajGs4xVOndv/9xqEc=">
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
                                        <a href="tel:+919031079721"><span class="mailto:Harihomes34@gmail.com">abdeveloperspl@gmail.com</span></a>
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
        <header>
            <div class="container-fluid bg-color-1">
                <div class="header-top">
                    <div class="header-top-welcome-text">
                        <span class="welcome">Welcome to Amitabh Builders & Developers</span>
                    </div>
                    <div class="header-top-contact-info">
                        <span class="mail p-relative"><a href="mailto:abdeveloperspl@gmail.com">abdeveloperspl@gmail.com</a></span>
                        <span class="phone p-relative"><a href="tel:+917070521500">+919031079721</a></span>
                    </div>
                </div>
            </div>
            <div id="header-sticky" class="header-area header-style-one">
                <div class="container-fluid">
                    <div class="mega-menu-wrapper">
                        <div class="header-main">
                            <div class="header-left">
                                <div class="header-logo">
                                    <a href="index.php">
                                        <img src="image/harihomes1-logo.png" alt="header logo">
                                    </a>
                                </div>
                                <div class="menu-with-search-wrapper">
                                    <div class="mean__menu-wrapper d-none d-lg-block">
                                        <div class="main-menu">
                                            <nav id="mobile-menu" style="display: block;">
                                                <ul>
                                                    <li class=" active">
                                                        <a href="index.php">Home</a>
                                                    </li>
                                                    <li class="has-dropdown">
                                                        <a href="#">About</a>
                                                        <ul class="submenu">
                                                            <li><a href="about.php">About Company</a></li>
                                                            <li><a href="about_director.php">About Director</a></li>
                                                        </ul>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="#">Media</a>
                                                        <ul class="submenu">
                                                            <li><a href="Gallery.php">Gallery</a></li>


                                                        </ul>
                                                    </li>
                                                    <li class="has-dropdown">
                                                        <a href="#">Map</a>
                                                        <ul class="submenu">
                                                            <li><a href="2D.php">Phase 1</a></li>
                                                            <li><a href="Phase2map.php">Phase 2</a></li>

                                                        </ul>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="#">Projects</a>
                                                        <ul class="submenu">
                                                            <li><a href="running_projects.php">Running Projects</a></li>
                                                            <li><a href="completed_project.php">Completed Projects</a></li>
                                                            <li><a href="upcoming_projects.php">Upcoming Projects</a></li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a href="blog.php">Blog</a>

                                                    </li>
                                                    <li>
                                                        <a href="New_Contact.php">Contact</a>
                                                    </li>

                                                    <li class="has-dropdown">
                                                        <a href="#">Login</a>
                                                        <ul class="submenu">
                                                            <li><a href="login.php">Associate</a></li>

                                                            <li><a href="Customer_Login.php">Customer</a></li>
                                                        </ul>
                                                    </li>


                                                    <?php
                                                    include 'connectdb.php';
                                                    // Check if there are notices
                                                    $stmt = $pdo->query("SELECT * FROM tbl_notices ORDER BY created_at DESC");
                                                    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    $hasNotices = !empty($notices); // True if there are notices
                                                    ?>

                                                    <?php if ($hasNotices): ?>
                                                        <a href="notice.php">
                                                            <div class="bell-container" id="bell">
                                                                <i class="bi bi-bell-fill" style="color: #ff9027;"></i>
                                                                <span class="bell-badge">!</span>
                                                            </div>
                                                        </a>
                                                    <?php endif; ?>


                                                </ul>
                                            </nav>
                                            <!-- for wp -->
                                            <div class="header__hamburger ml-50 d-none">
                                                <button type="button" class="hamburger-btn offcanvas-open-btn">
                                                    <span>01</span>
                                                    <span>01</span>
                                                    <span>01</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="header-right d-flex justify-content-end">
                                <div class="header-action  gap-5">
                                    <!-- <div class="header-link">
                                        <a class="primary-btn-1 btn-hover" href="Get_quote.php">
                                            Booking <i class="icon-arrow-double-right"></i>
                                            <span style="top: 147.172px; left: 108.5px;"></span>
                                        </a>
                                    </div> -->
                                </div>
                                <div class="header__hamburger my-auto">
                                    <div class="sidebar__toggle" style="background-color:white;padding:5px">
                                        <a class="bar-icon" href="javascript:void(0)">
                                            <svg color="white" width="30" height="24" viewBox="0 0 30 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.3125 4.59375C4.1927 4.59375 4.90625 3.8802 4.90625 3C4.90625 2.1198 4.1927 1.40625 3.3125 1.40625C2.4323 1.40625 1.71875 2.1198 1.71875 3C1.71875 3.8802 2.4323 4.59375 3.3125 4.59375Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M15 4.59375C15.8802 4.59375 16.5938 3.8802 16.5938 3C16.5938 2.1198 15.8802 1.40625 15 1.40625C14.1198 1.40625 13.4062 2.1198 13.4062 3C13.4062 3.8802 14.1198 4.59375 15 4.59375Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M26.6875 4.59375C27.5677 4.59375 28.2812 3.8802 28.2812 3C28.2812 2.1198 27.5677 1.40625 26.6875 1.40625C25.8073 1.40625 25.0938 2.1198 25.0938 3C25.0938 3.8802 25.8073 4.59375 26.6875 4.59375Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M3.3125 13.7812C4.1927 13.7812 4.90625 13.0677 4.90625 12.1875C4.90625 11.3073 4.1927 10.5938 3.3125 10.5938C2.4323 10.5938 1.71875 11.3073 1.71875 12.1875C1.71875 13.0677 2.4323 13.7812 3.3125 13.7812Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M15 13.7812C15.8802 13.7812 16.5938 13.0677 16.5938 12.1875C16.5938 11.3073 15.8802 10.5938 15 10.5938C14.1198 10.5938 13.4062 11.3073 13.4062 12.1875C13.4062 13.0677 14.1198 13.7812 15 13.7812Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M26.6875 13.7812C27.5677 13.7812 28.2812 13.0677 28.2812 12.1875C28.2812 11.3073 27.5677 10.5938 26.6875 10.5938C25.8073 10.5938 25.0938 11.3073 25.0938 12.1875C25.0938 13.0677 25.8073 13.7812 26.6875 13.7812Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M3.3125 22.9688C4.1927 22.9688 4.90625 22.2552 4.90625 21.375C4.90625 20.4948 4.1927 19.7812 3.3125 19.7812C2.4323 19.7812 1.71875 20.4948 1.71875 21.375C1.71875 22.2552 2.4323 22.9688 3.3125 22.9688Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M15 22.9688C15.8802 22.9688 16.5938 22.2552 16.5938 21.375C16.5938 20.4948 15.8802 19.7812 15 19.7812C14.1198 19.7812 13.4062 20.4948 13.4062 21.375C13.4062 22.2552 14.1198 22.9688 15 22.9688Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M26.6875 22.9688C27.5677 22.9688 28.2812 22.2552 28.2812 21.375C28.2812 20.4948 27.5677 19.7812 26.6875 19.7812C25.8073 19.7812 25.0938 20.4948 25.0938 21.375C25.0938 22.2552 25.8073 22.9688 26.6875 22.9688Z" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="breadcrumb__area theme-bg-1 p-relative" data-background="images/page-title-1.jpg" style="background-image: url(&quot;images/page-title-1.jpg&quot;);">
            <div class="bar-top" data-background="images/top-bar.png" style="background-image: url(&quot;images/top-bar.png&quot;);"></div>
            <div class="bar-bottom" data-background="images/bottom-bar.png" style="background-image: url(&quot;images/bottom-bar.png&quot;);"></div>
            <div class="yellow-shape" data-background="images/shape-12.png" style="background-image: url(&quot;images/shape-12.png&quot;);"></div>
            <div class="custom-container">
                <div class="row justify-content-center">
                    <div class="col-xxl-12">
                        <div class="breadcrumb__wrapper p-relative">
                            <div style="position:relative;">
                                <img src="image/royal-d.png" alt="Amitabh Builders & Developers plot in darbhanga">
                                <div class="breadcrumb__menu">
                                    <nav>
                                        <ul>
                                            <li><span><a href="index.php">Home</a></span></li>
                                            <li><span>Associate Portal</span></li>
                                        </ul>
                                    </nav>
                                </div>
                                <h2 class="breadcrumb__title">Login</h2>
                            </div>
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
            <h1> Associate Login </h1>
        </center>

        <form action="" method="post">
            <div class="login-form">
                <div class="container col-md-4">
                    <label>Associate ID: </label>
                    <input type="text" class="form-control" placeholder="Associate ID" name="sponsor_id" required>
                    <label>Password: </label>
                    <input type="password" class="form-control" placeholder="Password" name="sponsor_pass" required>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                        <label class="form-check-label" for="remember_me">Remember Me</label>
                    </div>
                    <div class="d-flex" style="justify-content: end;">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" name="btn_login" style="width:25%;text-align:center;padding:11px;font-size:medium;"><b>Login</b></button>
                    </div>
                    <?php if (isset($errormsg) && $errormsg == 'error') { ?>
                        <div class="alert alert-danger mt-3">Invalid Associate ID or Password</div>
                    <?php } ?>
                    <?php if (isset($errormsg) && $errormsg == 'kyc_required') { ?>
                        <script>
                            alert('KYC Required: Please complete your Aadhaar KYC to proceed with login.');
                            // Show modal after alert
                            document.addEventListener('DOMContentLoaded', function() {
                                var kycModal = new bootstrap.Modal(document.getElementById('kycModal'));
                                kycModal.show();
                            });
                        </script>
                    <?php } ?>
                </div>
            </div>
        </form>

        <!-- KYC Modal (Always present, shown only when needed) -->
        <div class="modal fade" id="kycModal" tabindex="-1" aria-labelledby="kycModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="kycModalLabel">Complete KYC</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="kyc_submit.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="sponsor_id" value="<?php echo isset($_SESSION['kyc_required']) ? htmlspecialchars($_SESSION['kyc_required']) : ''; ?>">
                            <div class="mb-3">
                                <label for="aadhar_number" class="form-label">Aadhaar Number</label>
                                <input type="text" class="form-control" name="aadhar_number" required pattern="[0-9]{12}" title="Aadhaar number must be 12 digits">
                            </div>
                            <div class="mb-3">
                                <label for="address_proof_file" class="form-label">Address Proof Files (Select multiple images)</label>
                                <input type="file" class="form-control" name="address_proof_file[]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="btn_kyc_submit">Submit KYC</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <div class="footer-main bg-color-1 p-relative">
                <div class="shape" data-background="images/footer-bg.png" alt="Residential plots near darbhanga airport, land for sale near darbhanga airport" title="Property Sites Near Darbhanga Airport | Land Near New Railway Station" style="background-image: url(&quot;images/footer-bg.png&quot;);"></div>
                <div class="custom-container p-relative">
                    <div class="footer-top pt-65 pb-30">
                        <div class="footer-logo">
                            <a href="index.php" title="Home Page - Amitabh Builders & Developers">
                                <img src="image/harihomes1-logo.png" width="31%" alt="residential land for sale near darbhanga station, residential land for sale near darbhanga bus stand, residential land for sale near darbhanga airport" title="Affordable Land for Sale Near Darbhanga Railway Station" style="background-color:#fff;">
                            </a>
                        </div>
                        <div class="footer-call">

                            <div class="info">
                                <span>Have Any Question ? Call</span>
                                <h4><a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developers">+919031079721</a></h4>
                            </div>
                        </div>
                    </div>
                    <div class="footer-middle pt-50 pb-70">
                        <div class="row g-4">
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-1">

                                    <ul class="company-info mt-30">
                                        <li class="phone-number"><a href="tel:+919031079721" title="Contact Number - Amitabh Builders & Developers">+919031079721</a></li>
                                        <li class="email"><a href="mailto:abdeveloperspl@gmail.com" title="Email Id - Amitabh Builders & Developers">abdeveloperspl@gmail.com</a></li>
                                        <li class="address">1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk, Bhuskaul, Darbhanga, Bihar, India, 846007</li>
                                    </ul>
                                    <div class="offcanvas__social">
                                        <ul>
                                            <li><a href="https://www.facebook.com/share/17Soc8dWP7/" title="Facebook - Amitabh Builders & Developers" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="https://www.instagram.com/amitabh_builders?utm_source=qr&igsh=MXIzMnZ4aDVkb213MA==" title="Instagram Amitabh Builders & Developers" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                            <li><a href="" title="Twitter - Amitabh Builders & Developers" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="" title="Youtube - Amitabh Builders & Developers" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-2 pl-70 pr-70">
                                    <h5 class="footer-widget-title mb-30">Quick Link</h5>
                                    <ul class="footer-menu-links">
                                        <li><a href="about.php" title="About Us Page - Amitabh Builders & Developers">About Us</a></li>
                                        <li><a href="blog.php" title="Blog Page - Amitabh Builders & Developers">Blogs</a></li>
                                        <li><a href="running_projects.php" title="Projects - Amitabh Builders & Developers">Projects</a></li>
                                        <li><a href="gallery.php" title="Gallery - Amitabh Builders & Developers">Gallery</a></li>
                                        <li><a href="contact.php" title="Contact Us Page - Amitabh Builders & Developers">Contact Us</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <div class="footer-widget-3">
                                    <h5 class="footer-widget-title mb-25">News Letter</h5>
                                    <div class="subscribe-form p-relative">
                                        <form action="#">
                                            <input type="email" placeholder="Email Address" required="">
                                            <button type="submit"><i class="icon-arrow_carrot-right"></i></button>
                                        </form>
                                    </div>

                                    <div class="admin-login mt-3">
                                        <a href="adminlogin.php" title="admin Login - Amitabh Builders & Developers" class="admin-login-link">
                                            <i class="fa fa-user text-white"></i><span class="text-white" style="margin-left: 1rem;">Admin Login</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                                <!-- <div class="footer-widget-4">
                     <h5 class="footer-widget-title mb-15">Gallery</h5>
                     <div class="footer-gallery">
                        <div class="image-area p-relative">
                           <a class="popup-image" href="images/footer-1.jpg">
                              <img src="images/footer-1.jpg" alt="">
                              <i class="icon-plus"></i>
                           </a>
                        </div>
                        <div class="image-area p-relative">
                           <a class="popup-image" href="images/footer-2.jpg">
                              <img src="images/footer-2.jpg" alt="">
                              <i class="icon-plus"></i>
                           </a>
                        </div>
                        <div class="image-area p-relative">
                           <a class="popup-image" href="images/footer-3.jpg">
                              <img src="images/footer-3.jpg" alt="">
                              <i class="icon-plus"></i>
                           </a>
                        </div>
                        <div class="image-area p-relative">
                           <a class="popup-image" href="images/footer-4.jpg">
                              <img src="images/footer-4.jpg" alt="">
                              <i class="icon-plus"></i>
                           </a>
                        </div>
                        <div class="image-area p-relative">
                           <a class="popup-image" href="images/footer-5.jpg">
                              <img src="images/footer-5.jpg" alt="">
                              <i class="icon-plus"></i>
                           </a>
                        </div>
                        <div class="image-area p-relative">
                           <a class="popup-image" href="images/footer-6.jpg">
                              <img src="images/footer-6.jpg" alt="">
                              <i class="icon-plus"></i>
                           </a>
                        </div>
                     </div>
                  </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="footer-bottom bg-color-2 pt-15 pb-15 text-center">
                        <p class="copy-right m-0">© <?php echo date('Y'); ?> Amitabh Builders & Developers, All Rights Reserved Designed and developed by <a href="https://www.infoerasoftware.com/" title="Developed by Info Era Software Services Pvt. Ltd." target="_blank">Info Era Software Services Pvt. Ltd.</a></p>
                    </div>
                </div>
            </div>
        </footer>
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

    <script>
        function vibrateBell() {
            let bell = document.getElementById("bell");
            bell.classList.add("vibrating");

            setTimeout(() => {
                bell.classList.remove("vibrating");
            }, 500); // Stop after 500ms
        }

        setInterval(vibrateBell, 2000); // Vibrate every 2 seconds
    </script>

</body>

</html>