<?php
session_start();
include_once 'connectdb.php';

require 'vendor/autoload.php'; // PHPMailer autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP email
// function sendOtpEmail($pdo, $sponsorid, $sponsor_name, $otp)
// {
//     try {
//         $mail = new PHPMailer(true);
//         $mail->SMTPDebug = 0;
//         $mail->isSMTP();
//         $mail->Host = 'smtp.gmail.com';
//         $mail->SMTPAuth = true;
//         $mail->Username = 'amitabhkmr989@gmail.com';
//         $mail->Password = 'luqa nzkd ffjj lehy';
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port = 587;

//         $mail->setFrom('amitabhkmr989@gmail.com', 'Amitabh ');
//         $mail->addAddress('amitabhkmr989@gmail.com', $sponsor_name);
//         $mail->isHTML(true);
//         $mail->Subject = 'Your OTP for Login';
//         $mail->Body = "Hello {$sponsor_name},<br><br>Your OTP for login is: <strong>$otp</strong><br>This OTP is valid for 5 minutes.<br>Thank you!";

//         return $mail->send(); // Return true on success, false on failure
//     } catch (Exception $e) {
//         error_log("Failed to send OTP. Error: {$e->getMessage()}");
//         return false;
//     }
// }

// // Function to display OTP form
// function displayOtpForm($pdo, $error = '')
// {
//     $errorHtml = $error ? "<p class=\"error\">$error</p>" : '';
//     $sponsorid = $_SESSION['temp_sponsor_id'] ?? '';
//     echo '<!DOCTYPE html>
// <html lang="en">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <title>Verify OTP</title>
//     <style>
//         body {
//             font-family: Arial, sans-serif;
//             background-color: #f4f4f4;
//             display: flex;
//             justify-content: center;
//             align-items: center;
//             height: 100vh;
//             margin: 0;
//         }
//         .otp-container {
//             background-color: #fff;
//             padding: 30px;
//             border-radius: 10px;
//             box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
//             width: 100%;
//             max-width: 400px;
//             text-align: center;
//         }
//         .otp-container h2 {
//             color: #333;
//             margin-bottom: 20px;
//         }
//         .otp-container p {
//             color: #666;
//             margin-bottom: 20px;
//         }
//         .otp-container .error {
//             color: #dc3545;
//             margin-bottom: 20px;
//         }
//         .otp-container label {
//             display: block;
//             text-align: left;
//             color: #333;
//             margin-bottom: 5px;
//             font-weight: bold;
//         }
//         .otp-container input[type="text"] {
//             width: 100%;
//             padding: 10px;
//             margin-bottom: 20px;
//             border: 1px solid #ddd;
//             border-radius: 5px;
//             box-sizing: border-box;
//             font-size: 16px;
//         }
//         .otp-container button {
//             background-color: #007bff;
//             color: #fff;
//             padding: 10px 20px;
//             border: none;
//             border-radius: 5px;
//             cursor: pointer;
//             font-size: 16px;
//             transition: background-color 0.3s;
//         }
//         .otp-container button:hover {
//             background-color: #0056b3;
//         }
//         .otp-container .resend-link {
//             display: inline-block;
//             margin-top: 10px;
//             color: #007bff;
//             text-decoration: none;
//             font-size: 14px;
//         }
//         .otp-container .resend-link:hover {
//             text-decoration: underline;
//         }
//         #timer {
//             color: #333;
//             font-weight: bold;
//             margin-bottom: 10px;
//         }
//     </style>
// </head>
// <body>
//     <div class="otp-container">
//         <h2>Verify Your OTP</h2>
//         ' . $errorHtml . '
//         <p>OTP has been sent to your email. Please check and enter it below.</p>
//         <div id="timer">Time remaining: 5:00</div>
//         <form method="POST" action="adminlogin.php">
//             <label for="otp">Enter OTP:</label>
//             <input type="text" id="otp" name="otp" required>
//             <button type="submit" name="verify_otp">Verify OTP</button>
//         </form>
//         <div id="resend-section">
//             <a href="?resend_otp=1" class="resend-link">Resend OTP</a>
//         </div>
//     </div>
//     <script>
//         let timeLeft = 300; // 10 minutes in seconds
//         const timerElement = document.getElementById("timer");
//         let hasClearedOtp = false;

//         function updateTimer() {
//             const minutes = Math.floor(timeLeft / 60);
//             const seconds = timeLeft % 60;
//             timerElement.textContent = `Time remaining: ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
//             if (timeLeft <= 0 && !hasClearedOtp) {
//                 timerElement.textContent = "OTP has expired.";
//                 hasClearedOtp = true;
//                 // Send AJAX request to clear OTP
//                 fetch("?clear_otp=1&sponsor_id=' . $sponsorid . '", { method: "POST" })
//                     .then(response => response.text())
//                     .then(data => console.log("OTP cleared:", data))
//                     .catch(error => console.error("Error clearing OTP:", error));
//             } else if (timeLeft > 0) {
//                 timeLeft--;
//                 setTimeout(updateTimer, 1000);
//             }
//         }
//         updateTimer();
//     </script>
// </body>
// </html>';
// }

// // Step 1: Handle initial login form submission
// if (isset($_POST['btn_login'])) {
//     $sponsorid = $_POST['sponsor_id'];
//     $sponsorpass = $_POST['sponsor_pass'];

//     // Check credentials with status = 'active'
//     $select = $pdo->prepare("SELECT * FROM tbl_hire WHERE sponsor_id = ? AND sponsor_pass = ? AND status = 'active'");
//     $select->execute([$sponsorid, $sponsorpass]);
//     $row = $select->fetch(PDO::FETCH_ASSOC);

//     if ($row && $row['sponsor_id'] === $sponsorid && $row['sponsor_pass'] === $sponsorpass && $row['status'] === 'active') {
//         // Generate OTP
//         $otp = rand(100000, 999999); // 6-digit OTP
//         $otp_created_at = date('Y-m-d H:i:s'); // Current timestamp

//         // Store OTP and timestamp in database
//         $update = $pdo->prepare("UPDATE tbl_hire SET otp = ?, otp_created_at = ? WHERE sponsor_id = ? AND status = 'active'");
//         $update->execute([$otp, $otp_created_at, $sponsorid]);

//         // Store temporary session data
//         $_SESSION['temp_sponsor_id'] = $row['sponsor_id'];
//         $_SESSION['temp_sponsor_pass'] = $row['sponsor_pass'];
//         $_SESSION['temp_sponsor_name'] = $row['s_name'];
//         $_SESSION['temp_status'] = $row['status'];

//         // Send OTP via email
//         if (sendOtpEmail($pdo, $sponsorid, $row['s_name'], $otp)) {
//             // Display OTP form
//             displayOtpForm($pdo);
//             exit();
//         } else {
//             echo 'Failed to send OTP. Please try again.';
//         }
//     } else {
//         $errormsg = 'Invalid credentials or inactive account';
//         echo $errormsg;
//     }
// }

// // Step 2: Handle OTP resend
// if (isset($_GET['resend_otp']) && $_GET['resend_otp'] == 1) {
//     $sponsorid = $_SESSION['temp_sponsor_id'];
//     $sponsor_name = $_SESSION['temp_sponsor_name'];

//     // Check if user is still in temp session
//     if (!$sponsorid || !$sponsor_name) {
//         header("Location: adminlogin.php"); // Redirect to login page if session expired
//         exit();
//     }

//     // Generate new OTP
//     $otp = rand(100000, 999999);
//     $otp_created_at = date('Y-m-d H:i:s');

//     // Update OTP and timestamp
//     $update = $pdo->prepare("UPDATE tbl_hire SET otp = ?, otp_created_at = ? WHERE sponsor_id = ? AND status = 'active'");
//     $update->execute([$otp, $otp_created_at, $sponsorid]);

//     // Send new OTP
//     if (sendOtpEmail($pdo, $sponsorid, $sponsor_name, $otp)) {
//         // Stay on OTP form
//         displayOtpForm($pdo, 'New OTP sent to your email.');
//         exit();
//     } else {
//         displayOtpForm($pdo, 'Failed to resend OTP. Please try again.');
//         exit();
//     }
// }

// // Step 3: Handle OTP clearing when timer expires
// if (isset($_GET['clear_otp']) && $_GET['clear_otp'] == 1) {
//     $sponsorid = $_GET['sponsor_id'] ?? '';
//     if ($sponsorid && $sponsorid === ($_SESSION['temp_sponsor_id'] ?? '')) {
//         $update = $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ? AND status = 'active'");
//         $update->execute([$sponsorid]);
//         echo 'OTP cleared';
//     } else {
//         echo 'Invalid request';
//     }
//     exit();
// }

// // Step 4: Handle OTP verification
// if (isset($_POST['verify_otp'])) {
//     $entered_otp = trim($_POST['otp']);
//     $sponsorid = $_SESSION['temp_sponsor_id'];

//     // Log for debugging
//     error_log("Verifying OTP for sponsor_id: $sponsorid, entered OTP: $entered_otp");

//     // Check if session is valid
//     if (!$sponsorid) {
//         header("Location: adminlogin.php"); // Redirect to login if session expired
//         exit();
//     }

//     // Check OTP and expiration
//     $select = $pdo->prepare("SELECT * FROM tbl_hire WHERE sponsor_id = ? AND otp = ? AND status = 'active'");
//     $select->execute([$sponsorid, $entered_otp]);
//     $row = $select->fetch(PDO::FETCH_ASSOC);

//     if ($row && $row['otp'] == $entered_otp && $row['status'] === 'active') {
//         // Check OTP expiration (5 minutes = 600 seconds)
//         $otp_created_at = strtotime($row['otp_created_at']);
//         $current_time = time();
//         if ($current_time - $otp_created_at > 300) {
//             // OTP expired
//             $update = $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ?");
//             $update->execute([$sponsorid]);
//             echo '<!DOCTYPE html>
// <html lang="en">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <title>Verify OTP</title>
//     <style>
//         body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
//         .otp-container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
//         .otp-container h2 { color: #333; margin-bottom: 20px; }
//         .otp-container p { color: #666; margin-bottom: 20px; }
//         .otp-container .error { color: #dc3545; margin-bottom: 20px; }
//         .otp-container .resend-link { color: #007bff; text-decoration: none; font-size: 14px; }
//         .otp-container .resend-link:hover { text-decoration: underline; }
//     </style>
// </head>
// <body>
//     <div class="otp-container">
//         <h2>Verify Your OTP</h2>
//         <p class="error">OTP has expired. Please request a new one.</p>
//         <p><a href="?resend_otp=1" class="resend-link">Resend OTP</a></p>
//     </div>
// </body>
// </html>';
//             exit();
//         }

//         // OTP valid, clear it
//         $update = $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ?");
//         $update->execute([$sponsorid]);

//         // Set permanent session variables
//         $_SESSION['sponsor_id'] = $row['sponsor_id'];
//         $_SESSION['sponsor_pass'] = $row['sponsor_pass'];
//         $_SESSION['sponsor_name'] = $row['s_name'];
//         $_SESSION['status'] = $row['status'];

//         // Clear temporary session data
//         unset($_SESSION['temp_sponsor_id']);
//         unset($_SESSION['temp_sponsor_pass']);
//         unset($_SESSION['temp_sponsor_name']);
//         unset($_SESSION['temp_status']);

//         // Handle "Remember Me"
//         if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
//             setcookie('sponsor_login', json_encode(['sponsorid' => $row['sponsor_id'], 'sponsorpass' => $row['sponsor_pass']]), time() + 2592000, "/");
//         } else {
//             setcookie('sponsor_login', '', time() - 3600, "/");
//         }

//         // Display success message
//         echo '<!DOCTYPE html>
// <html lang="en">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <title>Login Success</title>
//     <style>
//         body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
//         .success-container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
//         .success-container h2 { color: #28a745; margin-bottom: 20px; }
//         .success-container p { color: #666; margin-bottom: 20px; }
//     </style>
// </head>
// <body>
//     <div class="success-container">
//         <h2>Login Successful!</h2>
//         <p>OTP verified successfully! Welcome to the dashboard.</p>
//     </div>
// </body>
// </html>';
//         header("Refresh:1; url=UI/admin/dashboard.php");
//         exit();
//     } else {
//         // Log failure for debugging
//         error_log("OTP verification failed for sponsor_id: $sponsorid, entered OTP: $entered_otp, DB OTP: " . ($row['otp'] ?? 'none'));
//         // Invalid OTP
//         displayOtpForm($pdo, 'Invalid OTP. Please try again.');
//         exit();
//     }
// }

// Function to send OTP email
function sendOtpEmail($pdo, $user_id, $user_name, $user_email, $otp)
{
    try {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'amitabhkmr989@gmail.com';
        $mail->Password = 'ronurtvturnjongr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('amitabhkmr989@gmail.com', 'Amitabh');
        $mail->addAddress('amitabhkmr989@gmail.com', 'Amitabh Kumar'); // Hardcoded as per original
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Login';
        $mail->Body = "Hello {$user_name},<br><br>Your OTP for login is: <strong>$otp</strong><br>This OTP is valid for 5 minutes.<br>Thank you!";
        return $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send OTP. Error: {$e->getMessage()}");
        return false;
    }
}

// Function to display OTP form
function displayOtpForm($pdo, $error = '')
{
    $errorHtml = $error ? "<p class=\"error\">$error</p>" : '';
    $user_id = $_SESSION['temp_user_id'] ?? '';
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .otp-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .otp-container h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .otp-container p {
            color: #666;
            margin-bottom: 20px;
        }
        .otp-container .error {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .otp-container label {
            display: block;
            text-align: left;
            color: #333;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .otp-container input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .otp-container button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .otp-container button:hover {
            background-color: #0056b3;
        }
        .otp-container .resend-link {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .otp-container .resend-link:hover {
            text-decoration: underline;
        }
        #timer {
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <h2>Verify Your OTP</h2>
        ' . $errorHtml . '
        <p>OTP has been sent to your email. Please check and enter it below.</p>
        <div id="timer">Time remaining: 5:00</div>
        <form method="POST" action="adminlogin.php">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <button type="submit" name="verify_otp">Verify OTP</button>
        </form>
        <div id="resend-section">
            <a href="?resend_otp=1" class="resend-link">Resend OTP</a>
        </div>
    </div>
    <script>
        let timeLeft = 300; // 5 minutes in seconds
        const timerElement = document.getElementById("timer");
        let hasClearedOtp = false;
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `Time remaining: ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
            if (timeLeft <= 0 && !hasClearedOtp) {
                timerElement.textContent = "OTP has expired.";
                hasClearedOtp = true;
                fetch("?clear_otp=1&user_id=' . $user_id . '", { method: "POST" })
                    .then(response => response.text())
                    .then(data => console.log("OTP cleared:", data))
                    .catch(error => console.error("Error clearing OTP:", error));
            } else if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
        }
        updateTimer();
    </script>
</body>
</html>';
}

// Step 1: Handle initial login form submission
if (isset($_POST['btn_login'])) {
    $user_id = $_POST['sponsor_id'];
    $password = $_POST['sponsor_pass'];

    // Check tbl_hire first
    $select = $pdo->prepare("SELECT * FROM tbl_hire WHERE sponsor_id = ? AND sponsor_pass = ? AND status = 'active'");
    $select->execute([$user_id, $password]);
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Account found in tbl_hire
        $otp = rand(100000, 999999); // 6-digit OTP
        $otp_created_at = date('Y-m-d H:i:s');
        $update = $pdo->prepare("UPDATE tbl_hire SET otp = ?, otp_created_at = ? WHERE sponsor_id = ?");
        $update->execute([$otp, $otp_created_at, $user_id]);

        // Store temporary session data
        $_SESSION['temp_user_id'] = $row['sponsor_id'];
        $_SESSION['temp_user_name'] = $row['s_name'];
        $_SESSION['temp_user_email'] = 'amitabhkmr989@gmail.com'; // Hardcoded as per original
        $_SESSION['temp_status'] = $row['status'];
        $_SESSION['temp_user_type'] = 'hire';

        if (sendOtpEmail($pdo, $user_id, $row['s_name'], $_SESSION['temp_user_email'], $otp)) {
            displayOtpForm($pdo);
            exit();
        } else {
            echo 'Failed to send OTP. Please try again.';
        }
    } else {
        // Check employees table
        $select = $pdo->prepare("SELECT * FROM employees WHERE emp_id = ? AND password = ? AND role = 'Admin'");
        $select->execute([$user_id, $password]);
        $employee = $select->fetch(PDO::FETCH_ASSOC);

        if ($employee) {
            // Account found in employees with role = Admin
            $otp = rand(100000, 999999); // 6-digit OTP
            $otp_created_at = date('Y-m-d H:i:s');
            $update = $pdo->prepare("UPDATE employees SET otp = ?, created_at = ? WHERE emp_id = ?");
            $update->execute([$otp, $otp_created_at, $user_id]);

            // Store temporary session data
            $_SESSION['temp_user_id'] = $employee['emp_id'];
            $_SESSION['temp_user_name'] = $employee['emp_name'];
            $_SESSION['temp_user_email'] = $employee['email'];
            $_SESSION['temp_status'] = 'active'; // Assuming active status for employees
            $_SESSION['temp_role'] = $employee['role'];
            $_SESSION['temp_user_type'] = 'employee';

            if (sendOtpEmail($pdo, $user_id, $employee['emp_name'], $employee['email'], $otp)) {
                displayOtpForm($pdo);
                exit();
            } else {
                echo 'Failed to send OTP. Please try again.';
            }
        } else {
            echo 'Invalid credentials, inactive account, or non-Admin role';
        }
    }
}

// Step 2: Handle OTP resend
if (isset($_GET['resend_otp']) && $_GET['resend_otp'] == 1) {
    $user_id = $_SESSION['temp_user_id'];
    $user_name = $_SESSION['temp_user_name'];
    $user_email = $_SESSION['temp_user_email'];
    $user_type = $_SESSION['temp_user_type'];

    if (!$user_id || !$user_name || !$user_email) {
        header("Location: adminlogin.php");
        exit();
    }

    // Generate new OTP
    $otp = rand(100000, 999999);
    $otp_created_at = date('Y-m-d H:i:s');

    if ($user_type === 'hire') {
        $update = $pdo->prepare("UPDATE tbl_hire SET otp = ?, otp_created_at = ? WHERE sponsor_id = ? AND status = 'active'");
    } else {
        $update = $pdo->prepare("UPDATE employees SET otp = ?, created_at = ? WHERE emp_id = ? AND role = 'Admin'");
    }
    $update->execute([$otp, $otp_created_at, $user_id]);

    if (sendOtpEmail($pdo, $user_id, $user_name, $user_email, $otp)) {
        displayOtpForm($pdo, 'New OTP sent to your email.');
        exit();
    } else {
        displayOtpForm($pdo, 'Failed to resend OTP. Please try again.');
        exit();
    }
}

// Step 3: Handle OTP clearing when timer expires
if (isset($_GET['clear_otp']) && $_GET['clear_otp'] == 1) {
    $user_id = $_GET['user_id'] ?? '';
    $user_type = $_SESSION['temp_user_type'] ?? '';

    if ($user_id && $user_id === ($_SESSION['temp_user_id'] ?? '')) {
        if ($user_type === 'hire') {
            $update = $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ?");
        } else {
            $update = $pdo->prepare("UPDATE employees SET otp = NULL, created_at = NULL WHERE emp_id = ?");
        }
        $update->execute([$user_id]);
        echo 'OTP cleared';
    } else {
        echo 'Invalid request';
    }
    exit();
}

// Step 4: Handle OTP verification
if (isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp']);
    $user_id = $_SESSION['temp_user_id'];
    $user_type = $_SESSION['temp_user_type'];

    if (!$user_id || !$user_type) {
        header("Location: adminlogin.php");
        exit();
    }

    // Check OTP
    if ($user_type === 'hire') {
        $select = $pdo->prepare("SELECT * FROM tbl_hire WHERE sponsor_id = ? AND otp = ? AND status = 'active'");
        $select->execute([$user_id, $entered_otp]);
        $row = $select->fetch(PDO::FETCH_ASSOC);
        $otp_created_at = $row ? strtotime($row['otp_created_at']) : 0;
    } else {
        $select = $pdo->prepare("SELECT * FROM employees WHERE emp_id = ? AND otp = ? AND role = 'Admin'");
        $select->execute([$user_id, $entered_otp]);
        $row = $select->fetch(PDO::FETCH_ASSOC);
        $otp_created_at = $row ? strtotime($row['created_at']) : 0;
    }

    if ($row && $row['otp'] == $entered_otp) {
        // Check OTP expiration (5 minutes = 300 seconds)
        $current_time = time();
        if ($current_time - $otp_created_at > 300) {
            if ($user_type === 'hire') {
                $update = $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ?");
            } else {
                $update = $pdo->prepare("UPDATE employees SET otp = NULL, created_at = NULL WHERE emp_id = ?");
            }
            $update->execute([$user_id]);
            displayOtpForm($pdo, 'OTP has expired. Please request a new one.');
            exit();
        }

        // OTP valid, clear it
        if ($user_type === 'hire') {
            $update = $pdo->prepare("UPDATE tbl_hire SET otp = NULL, otp_created_at = NULL WHERE sponsor_id = ?");
            $_SESSION['sponsor_id'] = $row['sponsor_id'];
            $_SESSION['sponsor_pass'] = $row['sponsor_pass'];
            $_SESSION['sponsor_name'] = $row['s_name'];
            $_SESSION['status'] = $row['status'];
        } else {
            $update = $pdo->prepare("UPDATE employees SET otp = NULL, created_at = NULL WHERE emp_id = ?");
            $_SESSION['sponsor_id'] = $row['emp_id'];
            $_SESSION['sponsor_pass'] = $row['password'];
            $_SESSION['sponsor_name'] = $row['emp_name'];
            $_SESSION['status'] = 'active'; // Assuming active for employees
            $_SESSION['role'] = $row['role'];
        }
        $update->execute([$user_id]);

        // Handle "Remember Me"
        if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
            setcookie('sponsor_login', json_encode(['sponsorid' => $_SESSION['sponsor_id'], 'sponsorpass' => $_SESSION['sponsor_pass']]), time() + 2592000, "/");
        } else {
            setcookie('sponsor_login', '', time() - 3600, "/");
        }

        // Clear temporary session data
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_user_name']);
        unset($_SESSION['temp_user_email']);
        unset($_SESSION['temp_status']);
        unset($_SESSION['temp_role']);
        unset($_SESSION['temp_user_type']);

        // Display success message and redirect
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .success-container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
        .success-container h2 { color: #28a745; margin-bottom: 20px; }
        .success-container p { color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="success-container">
        <h2>Login Successful!</h2>
        <p>OTP verified successfully! Welcome to the dashboard.</p>
    </div>
</body>
</html>';
        header("Refresh:1; url=UI/admin/dashboard.php");
        exit();
    } else {
        error_log("OTP verification failed for user_id: $user_id, entered OTP: $entered_otp, DB OTP: " . ($row['otp'] ?? 'none'));
        displayOtpForm($pdo, 'Invalid OTP. Please try again.');
        exit();
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