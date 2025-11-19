<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}

$start_sponsor_id = $_SESSION['sponsor_id'];


require '../../vendor/autoload.php'; // PHPMailer autoloader
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer OTP function
function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dharamkumar211975@gmail.com';
        $mail->Password = 'luqanzkdffjjlehy'; // Ensure this app password is correct
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('dharamkumar211975@gmail.com', 'Payment System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'OTP for KYC Deletion';
        $mail->Body = "
                <h3>KYC Deletion OTP</h3>
                <p>Your OTP for deleting KYC record is: <strong>{$otp}</strong></p>
                <p>This OTP will expire in 5 minutes.</p>
                <p>If you did not request this, please ignore this email.</p>
            ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Generate OTP
function generateOTP()
{
    return sprintf('%06d', mt_rand(0, 999999));
}

// Handle OTP sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $mem_sid = $_POST['mem_sid'];
    $otp = generateOTP();
    $expire = time() + 300; // 5 minutes
    $_SESSION['otp_data'] = [
        'mem_sid' => $mem_sid,
        'otp' => $otp,
        'expire' => $expire
    ];
    if (sendOTP('dharamkumar211975@gmail.com', $otp)) {
        echo "<div class='alert alert-success'>OTP sent to dharamkumar211975@gmail.com.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to send OTP. Please try again.</div>";
    }
}

// Handle OTP verification and deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $mem_sid = $_POST['mem_sid'];

    if (isset($_SESSION['otp_data']) && $_SESSION['otp_data']['mem_sid'] === $mem_sid) {
        $stored_otp = $_SESSION['otp_data']['otp'];
        $expire = $_SESSION['otp_data']['expire'];

        if (time() <= $expire && $entered_otp === $stored_otp) {
            try {
                // Begin transaction
                $pdo->beginTransaction();

                // Delete aadhar_number from tbl_regist
                $stmt = $pdo->prepare("UPDATE tbl_regist SET aadhar_number = NULL WHERE mem_sid = ?");
                $stmt->execute([$mem_sid]);

                // Fetch and delete files from tbl_kyc
                $kyc_stmt = $pdo->prepare("SELECT address_proof_file FROM tbl_kyc WHERE sponsor_id = ?");
                $kyc_stmt->execute([$mem_sid]);
                $kyc_row = $kyc_stmt->fetch(PDO::FETCH_ASSOC);

                if ($kyc_row && !empty($kyc_row['address_proof_file'])) {
                    $files = explode(',', $kyc_row['address_proof_file']);
                    foreach ($files as $file) {
                        $file_path = "member_document/" . $file;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }

                // Delete record from tbl_kyc
                $kyc_delete = $pdo->prepare("DELETE FROM tbl_kyc WHERE sponsor_id = ?");
                $kyc_delete->execute([$mem_sid]);

                // Commit transaction
                $pdo->commit();

                // Clear OTP session
                unset($_SESSION['otp_data']);

                // Redirect to refresh
                header("Location: fetch_members.php");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "<div class='alert alert-danger'>Error deleting record: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid or expired OTP.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No OTP session found.</div>";
    }
}
?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers
    </title>
    <link rel="shortcut icon" type="image/x-icon" href="../../icon/harihomes1-fevicon.png">
    <link rel="stylesheet" href="../resources/vendors/feather/feather.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../resources/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="../resources/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link rel="stylesheet" href="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../resources/js/select.dataTables.min.css">
    <link rel="stylesheet" href="../resources/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../resources/vendors/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" href="../resources/css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link href="assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link href="../assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>


    <style>
        table {
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        thead {
            background-color: #ddd;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }

        th {
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #ddd;
        }
    </style>


    <script>
        function display_ct7() {
            var x = new Date();
            var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
            var hours = x.getHours() % 12;
            hours = hours ? hours : 12;
            hours = hours.toString().length == 1 ? '0' + hours.toString() : hours;

            var minutes = x.getMinutes().toString();
            minutes = minutes.length == 1 ? '0' + minutes : minutes;

            var seconds = x.getSeconds().toString();
            seconds = seconds.length == 1 ? '0' + seconds : seconds;

            var month = (x.getMonth() + 1).toString();
            month = month.length == 1 ? '0' + month : month;

            var dt = x.getDate().toString();
            dt = dt.length == 1 ? '0' + dt : dt;

            var x1 = dt + "-" + month + "-" + x.getFullYear();
            x1 = x1 + " " + hours + ":" + minutes + ":" + seconds + " " + ampm;
            document.getElementById('ct7').innerHTML = x1;
        }

        function startTime() {
            display_ct7();
            setInterval(display_ct7, 1000);
        }

        window.onload = startTime;
    </script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">

                <!-- side panel header -->
                <?php include 'adminheadersidepanel.php'; ?>

                <div class="main-panel">
                    <div class="card" style="background: #fff; padding: 10px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                        <h2>Member KYC Details</h2>
                        <hr>
                        <div id="" style="overflow-x: scroll!important;width:92%; margin: 0 auto; height:500px;">
                            <table id="salesTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Member Name</th>
                                        <th>Member Id</th>
                                        <th>Mobile Num</th>
                                        <th>Email Id</th>
                                        <th>Aadhar Number</th>
                                        <th>Date Time</th>
                                        <th>Address Proof</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch all members from tbl_regist with address_proof_file from tbl_kyc
                                    $stmt = $pdo->prepare("
                SELECT r.aadhar_number, r.m_name, r.mem_sid, r.m_num, r.m_email, r.date_time, k.address_proof_file
                FROM tbl_regist r
                LEFT JOIN tbl_kyc k ON r.mem_sid = k.sponsor_id
                ORDER BY r.id ASC
            ");
                                    $stmt->execute();
                                    $all_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Display all members in the HTML table
                                    $i = 0;
                                    foreach ($all_members as $member) {
                                        $i++;
                                        // Split address_proof_file into individual files
                                        $files = !empty($member['address_proof_file']) ? explode(',', $member['address_proof_file']) : [];
                                        $file_links = [];
                                        foreach ($files as $file) {
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $file_path = "member_document/" . $file;
                                            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                                $file_links[] = "<a href='#' class='image-link' data-bs-toggle='modal' data-bs-target='#imageModal' data-image='$file_path'>" . htmlspecialchars($file) . "</a>";
                                            } else {
                                                $file_links[] = "<a href='$file_path' download>" . htmlspecialchars($file) . "</a>";
                                            }
                                        }
                                        $address_proof_display = !empty($file_links) ? implode(', ', $file_links) : 'N/A';

                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td>" . htmlspecialchars($member['m_name'] ?? 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($member['mem_sid']) . "</td>";
                                        echo "<td>" . htmlspecialchars($member['m_num']) . "</td>";
                                        echo "<td>" . htmlspecialchars($member['m_email'] ?? 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($member['aadhar_number'] ?? 'N/A') . "</td>";
                                        echo "<td>" . htmlspecialchars($member['date_time']) . "</td>";
                                        echo "<td>$address_proof_display</td>";
                                        echo "<td>";
                                        echo "<form method='POST'>";
                                        echo "<input type='hidden' name='mem_sid' value='" . htmlspecialchars($member['mem_sid']) . "'>";
                                        echo "<button type='submit' name='send_otp' class='btn btn-danger btn-sm'>Delete</button>";
                                        echo "</form>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <!-- Image Modal -->
                            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Address Proof Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img id="modalImage" src="" class="img-fluid" alt="Address Proof">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- OTP Modal -->
                            <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" id="otpForm">
                                                <input type="hidden" name="mem_sid" id="otpMemSid">
                                                <div class="mb-3">
                                                    <label for="otpInput" class="form-label">Enter OTP sent to your email</label>
                                                    <input type="text" class="form-control" id="otpInput" name="otp" maxlength="6" required>
                                                </div>
                                                <button type="submit" name="verify_otp" class="btn btn-primary">Verify OTP</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Update modal image source
                                document.querySelectorAll('.image-link').forEach(link => {
                                    link.addEventListener('click', function() {
                                        document.getElementById('modalImage').setAttribute('src', this.getAttribute('data-image'));
                                    });
                                });

                                // Trigger OTP modal after sending OTP
                                <?php if (isset($_POST['send_otp']) && isset($_SESSION['otp_data'])): ?>
                                    document.getElementById('otpMemSid').value = '<?php echo htmlspecialchars($_SESSION['otp_data']['mem_sid']); ?>';
                                    new bootstrap.Modal(document.getElementById('otpModal')).show();
                                <?php endif; ?>
                            </script>
                        </div>
                    </div>
                    <!-- footer -->

                    <?php include 'adminfooter.php'; ?>
                </div>

            </div>
            <a href="#" target="_blank">
                <!-- partial -->
            </a>
            <!-- search box for options-->
            <!-- jQuery (required for DataTables) -->
            <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
            <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
            <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

            <!-- <script src="../resources/vendors/js/vendor.bundle.base.js"></script> -->
            <!-- endinject -->
            <!-- Plugin js for this page -->
            <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
            <script src="../resources/vendors/select2/select2.min.js"></script>
            <!-- End plugin js for this page -->
            <!-- Plugin js for this page -->
            <script src="../resources/vendors/chart.js/Chart.min.js"></script>
            <!-- <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script> -->
            <!-- <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> -->
            <script src="../resources/js/dataTables.select.min.js"></script>
            <script src="../resources/js/custom.js"></script>
            <!-- End plugin js for this page -->
            <script src="../resources/vendors/moment/moment.min.js"></script>
            <script src="../resources/vendors/fullcalendar/fullcalendar.min.js"></script>

            <!-- inject:js -->
            <script src="../resources/js/off-canvas.js"></script>
            <script src="../resources/js/hoverable-collapse.js"></script>
            <script src="../resources/js/template.js"></script>
            <script src="../resources/js/settings.js"></script>
            <script src="../resources/js/todolist.js"></script>

            <script src="../resources/js/calendar.js"></script>
            <script src="../resources/js/tabs.js"></script>

            <!-- endinject -->
            <!-- Custom js for this page-->
            <script src="../resources/js/dashboard.js"></script>
            <script src="../resources/js/Chart.roundedBarCharts.js"></script>
            <!-- End custom js for this page-->
            <!-- Custom js for this page-->
            <script src="../resources/js/file-upload.js"></script>
            <script src="../resources/js/typeahead.js"></script>
            <script src="../resources/js/select2.js"></script>
            <!-- End custom js for this page-->

            <!-- plugin js for this page -->
            <script src="../resources/vendors/tinymce/tinymce.min.js"></script>
            <script src="../resources/vendors/quill/quill.min.js"></script>
            <script src="../resources/vendors/simplemde/simplemde.min.js"></script>
            <script src="../resources/js/editorDemo.js"></script>

            <!-- Custom js for this page-->
            <script src="../resources/js/data-table.js"></script>


            <script>
                $(document).ready(function() {
                    $('#salesTable').DataTable({

                    });

                    $('.dropdown-toggle').dropdown();

                });
            </script>

        </div>
    </div>
    <div style="margin-left:250px">
        <span id="lblMsg"></span>
    </div>
    <style>
        #lblMsg {
            visibility: hidden;
        }
    </style>

</body>

</html>