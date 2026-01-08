<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once 'connectdb.php';

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ==================== SEND OTP EMAIL FUNCTION (WITH DEBUG) ====================
function sendOtpEmail($sponsorid, $sponsor_name, $user_email, $otp)
{
    try {
        $mail = new PHPMailer(true);

        // REMOVE THESE TWO LINES IN PRODUCTION!
        $mail->SMTPDebug = 2;                                      // Enable verbose debug
        $mail->Debugoutput = function ($str, $level) {              // Log to PHP error log
            error_log("PHPMailer [$level]: $str");
        };

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'vedikavillage@gmail.com';             // Your Gmail
        $mail->Password   = 'dehefyqgxqswmzzm';                 // Must be valid App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('vedikavillage@gmail.com', 'Vedika Village');
        $mail->addAddress($user_email, $sponsor_name);             // Real user email

        $mail->isHTML(true);
        $mail->Subject = 'Your Login OTP - Vedika Village';
        $mail->Body    = "
            <h2>Hello {$sponsor_name},</h2>
            <p>Your OTP for login is:</p>
            <h1 style='color:#007bff;'><strong>$otp</strong></h1>
            <p>This OTP is valid for <strong>5 minutes only</strong>.</p>
            <p>Thank you!<br><small>Vedika Village Team</small></p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("OTP Email failed for $user_email. Error: " . $mail->ErrorInfo);
        return false;
    }
}

// ==================== DISPLAY OTP FORM (ALWAYS SHOWS) ====================
function displayOtpForm($error = '', $success = '', $debugOtp = null)
{
    $errorHtml   = $error ? "<div class='alert error'>$error</div>" : '';
    $successHtml = $success ? "<div class='alert success'>$success</div>" : '';
    $debugOtpHtml = $debugOtp ? "<div class='alert debug'>Warning: OTP (Dev Mode): <strong style='font-size:20px;color:red;'>$debugOtp</strong></div>" : '';

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Vedika Village</title>
    <style>
        body {font-family: Arial, sans-serif;background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);display: flex;justify-content: center;align-items: center;height: 100vh;margin: 0;}
        .otp-box {background: white;padding: 40px;border-radius: 15px;box-shadow: 0 15px 35px rgba(0,0,0,0.3);width: 100%;max-width: 420px;text-align: center;}
        h2 {color: #333;margin-bottom: 10px;}
        .alert {padding: 12px;margin: 15px 0;border-radius: 8px;font-size: 15px;}
        .error {background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
        .success {background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
        .debug {background:#fff3cd;color:#856404;border:1px solid #ffeaa7;}
        input[type="text"] {width: 100%;padding: 15px;margin: 10px 0;border: 2px solid #ddd;border-radius: 8px;font-size: 18px;text-align:center;letter-spacing: 5px;}
        button {background:#007bff;color:white;padding: 14px 30px;border:none;border-radius:8px;font-size:16px;cursor:pointer;margin:10px 0;width:100%;}
        button:hover {background:#0056b3;}
        .resend {margin-top: 15px;font-size: 14px;}
        .resend a {color:#007bff;text-decoration:none;}
        .resend a:hover {text-decoration:underline;}
        #timer {font-weight:bold;color:#e74c3c;margin:15px 0;}
    </style>
</head>
<body>
    <div class="otp-box">
        <h2>Enter OTP</h2>
        ' . $successHtml . '
        ' . $errorHtml . '
        ' . $debugOtpHtml . '
        <p>We have sent a 6-digit OTP to your email.</p>
        <div id="timer">Time remaining: 5:00</div>
        
        <form method="POST">
            <input type="text" name="otp" maxlength="6" placeholder="______" required autocomplete="off">
            <button type="submit" name="verify_otp">Verify OTP</button>
        </form>
        
        <div class="resend">
            Didn\'t receive? <a href="?resend_otp=1">Resend OTP</a>
        </div>
    </div>

    <script>
        let timeLeft = 300;
        const timer = document.getElementById("timer");
        function updateTimer() {
            let m = String(Math.floor(timeLeft / 60)).padStart(2, "0");
            let s = String(timeLeft % 60).padStart(2, "0");
            timer.textContent = `Time remaining: ${m}:${s}`;
            if (timeLeft <= 0) {
                timer.innerHTML = "<span style=\'color:red;\'>OTP Expired!</span>";
                document.querySelector("button").disabled = true;
            } else {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
        }
        updateTimer();
    </script>
</body>
</html>';
    exit();
}

// ==================== MAIN LOGIN LOGIC ====================
if (isset($_POST['btn_login'])) {
    $user_id = trim($_POST['sponsor_id']);
    $password = trim($_POST['sponsor_pass']);

    // Try tbl_hire first
    $stmt = $pdo->prepare("SELECT * FROM tbl_hire WHERE sponsor_id = ? AND sponsor_pass = ? AND status = 'active'");
    $stmt->execute([$user_id, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_type = null;
    $user_email = null;
    $user_name = null;

    if ($user) {
        $user_type = 'hire';
        $user_email = $user['email'] ?? 'noemail@vedika.com';
        $user_name = $user['s_name'];
    } else {
        // Try employees table (Admin only)
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE emp_id = ? AND password = ? AND role = 'Admin'");
        $stmt->execute([$user_id, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_type = 'employee';
            $user_email = $user['email'];
            $user_name = $user['emp_name'];
        }
    }

    if (!$user) {
        echo "<h3 style='color:red;text-align:center;margin-top:50px;'>Invalid credentials or inactive account!</h3>";
        exit();
    }

    // Generate OTP
    $otp = rand(100000, 999999);
    $now = date('Y-m-d H:i:s');

    if ($user_type === 'hire') {
        $pdo->prepare("UPDATE tbl_hire SET otp = ?, otp_created_at = ? WHERE sponsor_id = ?")
            ->execute([$otp, $now, $user_id]);
    } else {
        $pdo->prepare("UPDATE employees SET otp = ?, created_at = ? WHERE emp_id = ?")
            ->execute([$otp, $now, $user_id]);
    }

    // Store temp session
    $_SESSION['temp_user_id']   = $user_id;
    $_SESSION['temp_user_name'] = $user_name;
    $_SESSION['temp_user_email'] = $user_email;
    $_SESSION['temp_user_type'] = $user_type;

    // Try to send email
    $emailSent = sendOtpEmail($user_id, $user_name, $user_email, $otp);

    if ($emailSent) {
        displayOtpForm('', 'OTP sent successfully to <strong>' . htmlspecialchars($user_email) . '</strong>');
    } else {
        // STILL SHOW FORM + REVEAL OTP (FOR TESTING)
        displayOtpForm(
            'Failed to send email (check logs). But you can still login.',
            '',
            $otp  // This shows OTP on screen in dev mode
        );
    }
}

// ==================== RESEND OTP ====================
if (isset($_GET['resend_otp'])) {
    if (!isset($_SESSION['temp_user_id'])) {
        header("Location: adminlogin.php");
        exit();
    }

    $user_id   = $_SESSION['temp_user_id'];
    $user_name = $_SESSION['temp_user_name'];
    $user_email = $_SESSION['temp_user_email'];
    $user_type = $_SESSION['temp_user_type'];

    $otp = rand(100000, 999999);
    $now = date('Y-m-d H:i:s');

    if ($user_type === 'hire') {
        $pdo->prepare("UPDATE tbl_hire SET otp = ?, otp_created_at = ? WHERE sponsor_id = ?")
            ->execute([$otp, $now, $user_id]);
    } else {
        $pdo->prepare("UPDATE employees SET otp = ?, created_at = ? WHERE emp_id = ?")
            ->execute([$otp, $now, $user_id]);
    }

    $sent = sendOtpEmail($user_id, $user_name, $user_email, $otp);
    displayOtpForm(
        $sent ? '' : 'Email failed again.',
        $sent ? 'New OTP sent!' : '',
        $sent ? null : $otp
    );
}

// ==================== VERIFY OTP ====================
if (isset($_POST['verify_otp'])) {
    $entered = trim($_POST['otp']);
    $user_id = $_SESSION['temp_user_id'] ?? null;
    $type    = $_SESSION['temp_user_type'] ?? null;

    if (!$user_id || !$type) {
        header("Location: adminlogin.php");
        exit();
    }

    $query = $type === 'hire'
        ? "SELECT * FROM tbl_hire WHERE sponsor_id = ? AND otp = ?"
        : "SELECT * FROM employees WHERE emp_id = ? AND otp = ? AND role = 'Admin'";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $entered]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['otp'] != $entered) {
        displayOtpForm('Invalid OTP. Try again.');
    }

    // Check expiry
    $created_at = $type === 'hire' ? strtotime($row['otp_created_at']) : strtotime($row['created_at']);
    if ((time() - $created_at) > 300) {
        displayOtpForm('OTP has expired. Please request a new one.');
    }

    // SUCCESS - Clear OTP & Login
    $clear = $type === 'hire'
        ? $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ?")
        : $pdo->prepare("UPDATE employees SET otp = NULL, created_at = NULL WHERE emp_id = ?");
    $clear->execute([$user_id]);

    // Set real session
    if ($type === 'hire') {
        $_SESSION['sponsor_id']   = $row['sponsor_id'];
        $_SESSION['sponsor_name'] = $row['s_name'];
        $_SESSION['status']       = $row['status'];
    } else {
        $_SESSION['sponsor_id']   = $row['emp_id'];
        $_SESSION['sponsor_name'] = $row['emp_name'];
        $_SESSION['role']         = $row['role'];
        $_SESSION['status']       = 'active';
    }

    // Clear temp data
    unset($_SESSION['temp_user_id'], $_SESSION['temp_user_name'], $_SESSION['temp_user_email'], $_SESSION['temp_user_type']);

    // Redirect
    echo "<h2 style='text-align:center;color:green;margin-top:100px;'>Login Successful! Redirecting...</h2>";
    header("Refresh: 2; url=UI/admin/dashboard.php");
    exit();
}

// If someone opens this page directly without login
// if (empty($_SESSION['temp_user_id']) && !isset($_POST['btn_login']) && !isset($_GET['resend_otp'])) {
//     // Show normal login form or redirect
//     header("Location: adminlogin.php"); // or your login page
//     exit();
// }
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

</head>

<body>
    <form method="post" action="./adminlogin.php" id="form1">


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





        <div>
            <div class="backtotop-wrap cursor-pointer">
                <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
                </svg>
            </div>
            <!-- Back to top end -->



            <?php include 'header.php'; ?>

            <div class="breadcrumb__area theme-bg-1 p-relative" data-background="images/page-title-1.jpg" style="background-image: url(&quot;images/page-title-1.jpg&quot;);">

                <div class="bar-top" data-background="images/top-bar.png" style="background-image: url(&quot;images/top-bar.png&quot;);"></div>
                <div class="bar-bottom" data-background="images/bottom-bar.png" style="background-image: url(&quot;images/bottom-bar.png&quot;);"></div>
                <div class="yellow-shape" data-background="images/shape-12.png" style="background-image: url(&quot;images/shape-12.png&quot;);"></div>
                <div class="custom-container">
                    <div class="row justify-content-center">
                        <div class="col-xxl-12">
                            <div class="breadcrumb__wrapper p-relative">
                                <img src="image/royal-d.png" alt="Amitabh Builders & Developers plot in darbhanga">
                                <div class="breadcrumb__menu">
                                    <nav>
                                        <ul>
                                            <li><span><a href="index.php" title="Home Page - Amitabh Builders & Developers">Home</a></span></li>
                                            <li><span>admin Portal</span></li>
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
                <h1> Admin Login </h1>
            </center>

            <form action="" method="post">
                <div class="login-form">
                    <div class="container col-md-4">
                        <label>ID: </label>
                        <input type="text" class="form-control" placeholder="Enter ID" name="sponsor_id" required>
                        <label>Password: </label>
                        <input type="password" class="form-control" placeholder="Password" name="sponsor_pass" required>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                            <label class="form-check-label" for="remember_me">Remember Me</label>
                        </div>
                        <div class="d-flex" style="justify-content: end;">
                            <button type="submit" class="btn btn-primary btn-block btn-flat" name="btn_login" style="width:25%;text-align:center;padding:11px;font-size:medium;"><b>Login</b></button>
                        </div>
                    </div>
                </div>
            </form>


            <?php include 'footer.php'; ?>


        </div>
    </form>







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
    <script src="js/adminfunction.js"></script>
</body>

</html>