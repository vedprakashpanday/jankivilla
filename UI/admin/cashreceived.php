<?php
session_start();
include_once "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header("Location: ../../adminlogin.php"); // Redirect to dashboard
    exit;
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Adjust path to PHPMailer autoloader

function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'amitabhkmr989@gmail.com';
        $mail->Password = 'ronurtvturnjongr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('amitabhkmr989@gmail.com', 'Payment System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'OTP for Payment Deletion';
        $mail->Body = "<h3>Payment Deletion OTP</h3><p>Your OTP for deleting payment record is: <strong>{$otp}</strong></p><p>This OTP will expire in 5 minutes.</p><p>If you did not request this, please ignore this email.</p>";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function generateOTP()
{
    return sprintf('%06d', mt_rand(0, 999999));
}

$modal_trigger = ''; // Variable to store modal trigger script

if (isset($_POST['btnsubmit'])) {
    $plot_no = $_POST['plot_no'];
    $customer_name = $_POST['customer_name'];
    $receiver_name = $_POST['receiver_name'];
    $amount = $_POST['amount'];
    $cash_received_date = $_POST['cash_received_date'];
    $created_at = date('Y-m-d H:i:s');
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (empty($plot_no) || empty($customer_name) || empty($amount) || empty($cash_received_date)) {
        echo "<script>alert('All fields are required.');</script>";
        exit;
    }

    if ($id) {
        // Update existing record
        $update = $pdo->prepare("UPDATE tbl_cash_details SET plot_no = :plot_no, customer_name = :customer_name, receiver_name = :receiver_name, amount = :amount, cash_received_date = :cash_received_date WHERE id = :id");
        $update->bindParam(':id', $id);
    } else {
        // Insert new record
        $update = $pdo->prepare("INSERT INTO tbl_cash_details (plot_no, customer_name, receiver_name, amount, cash_received_date, created_at) VALUES (:plot_no, :customer_name, :receiver_name, :amount, :cash_received_date, :created_at)");
        $update->bindParam(':created_at', $created_at);
    }

    $update->bindParam(':plot_no', $plot_no);
    $update->bindParam(':customer_name', $customer_name);
    $update->bindParam(':receiver_name', $receiver_name);
    $update->bindParam(':amount', $amount);
    $update->bindParam(':cash_received_date', $cash_received_date);
    $update->execute();

    echo "<script>alert('Cash details " . ($id ? "updated" : "added") . " successfully!'); window.location.href = 'cashreceived.php';</script>";
}

if (isset($_POST['initiate_delete']) && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $otp = generateOTP();
    $otp_expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Store OTP in database
    $update = $pdo->prepare("UPDATE tbl_cash_details SET otp = :otp, otp_expires = :otp_expires WHERE id = :id");
    $update->bindParam(':id', $delete_id);
    $update->bindParam(':otp', $otp);
    $update->bindParam(':otp_expires', $otp_expires);
    $update->execute();

    // Send OTP to email
    if (sendOTP('amitabhkmr989@gmail.com', $otp)) {
        // Store the modal trigger script to execute after page load
        $modal_trigger = "<script>document.addEventListener('DOMContentLoaded', function() { showOTPModal($delete_id); });</script>";
    } else {
        echo "<script>alert('Failed to send OTP. Please try again.');</script>";
    }
}

if (isset($_POST['verify_otp']) && isset($_POST['delete_id']) && isset($_POST['otp'])) {
    $delete_id = $_POST['delete_id'];
    $submitted_otp = $_POST['otp'];

    // Verify OTP
    $stmt = $pdo->prepare("SELECT otp, otp_expires FROM tbl_cash_details WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['otp'] === $submitted_otp && strtotime($row['otp_expires']) > time()) {
        // OTP valid, delete record
        $delete = $pdo->prepare("DELETE FROM tbl_cash_details WHERE id = :id");
        $delete->bindParam(':id', $delete_id);
        $delete->execute();
        echo "<script>alert('Cash detail deleted successfully!'); window.location.href = 'cashreceived.php';</script>";
    } else {
        // Trigger modal again with error
        $modal_trigger = "<script>document.addEventListener('DOMContentLoaded', function() { alert('Invalid or expired OTP. Please try again.'); showOTPModal($delete_id); });</script>";
    }
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Amitabh Builders & Developers
    </title>
    <link rel="shortcut icon" type="image/x-icon" href="../../icon/harihomes1-fevicon.png">
    <link rel="stylesheet" href="../resources/vendors/feather/feather.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../resources/js/select.dataTables.min.css">
    <link rel="stylesheet" href="../resources/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../resources/vendors/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" href="../resources/css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link href="assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link href="../assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/themify-icons.css">


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
</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="aspNetHidden">

        <div class="wrapper">
            <div class="container-scroller">
                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                        <div class="row" style="display: block;">
                                            <form method="post" enctype="multipart/form-data" id="cashForm">
                                                <div class="col-md-12">
                                                    <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                        <h2 id="formTitle">Add Cash Details</h2>
                                                        <hr>
                                                        <input type="hidden" name="id" id="id">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>Plot No.:<span style="color: red">*</span></b>
                                                                <input name="plot_no" id="plot_no" type="text" class="form-control" style="font-weight:bold;" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <b>Customer Name:<span style="color: red">*</span></b>
                                                                <input name="customer_name" id="customer_name" type="text" class="form-control" style="font-weight:bold;" required>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Receiver Name:<span style="color: red">*</span></b>
                                                                <input name="receiver_name" id="receiver_name" type="text" class="form-control" style="font-weight:bold;" required>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Amount:<span style="color: red">*</span></b>
                                                                <input name="amount" id="amount" type="number" step="0.01" class="form-control" style="font-weight:bold;" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <b>Cash Received Date:<span style="color: red">*</span></b>
                                                                <input name="cash_received_date" id="cash_received_date" type="date" class="form-control" style="font-weight:bold;" required>
                                                            </div>
                                                        </div>
                                                        <div class="row pt-4">
                                                            <div class="col-md-12">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-7" style="text-align: center;">
                                                                        <input type="submit" name="btnsubmit" value="Save" class="btn btn-success">
                                                                        <input type="button" class="btn btn-secondary" value="Clear Form" onclick="resetForm()">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row pt-5">
                                        <div class="col-md-12" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Cash Details Report</h3>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Plot No.</th>
                                                        <th>Customer Name</th>
                                                        <th>Receiver Name</th>
                                                        <th>Amount</th>
                                                        <th>Cash Received Date</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $stmt = $pdo->query("SELECT id, plot_no, customer_name, amount, cash_received_date, created_at 
                                                    FROM tbl_cash_details 
                                                    ORDER BY created_at DESC");
                                                    $sn = 1;
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo "<tr>";
                                                        echo "<td>{$sn}</td>";
                                                        echo "<td>" . htmlspecialchars($row['plot_no']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['receiver_name']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['cash_received_date']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                                        echo "<td>
                                            <button class='btn btn-sm btn-primary' onclick='editCashDetail(" . json_encode($row) . ")'>Edit</button>
                                            <form method='post' style='display:inline;'>
                                                <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                                                <button type='submit' name='initiate_delete' class='btn btn-sm btn-danger'>Delete</button>
                                            </form>
                                          </td>";
                                                        echo "</tr>";
                                                        $sn++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- OTP Verification Modal -->
                        <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="otpModalLabel">Verify OTP</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" id="otpForm">
                                            <input type="hidden" name="delete_id" id="otp_delete_id">
                                            <div class="mb-3">
                                                <label for="otp" class="form-label">Enter OTP sent to Mail ID</label>
                                                <input type="text" class="form-control" name="otp" id="otp" required>
                                            </div>
                                            <button type="submit" name="verify_otp" class="btn btn-primary">Verify</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>





                    </div>

                    <?php include 'adminfooter.php'; ?>
                </div>



            </div>
            <a href="#" target="_blank">
                <!-- partial -->
            </a>
            <!-- search box for options-->

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

            <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
            <!-- endinject -->
            <!-- Plugin js for this page -->
            <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
            <script src="../resources/vendors/select2/select2.min.js"></script>
            <!-- End plugin js for this page -->
            <!-- Plugin js for this page -->
            <script src="../resources/vendors/chart.js/Chart.min.js"></script>
            <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script>
            <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
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
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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

    <script>
        function editCashDetail(data) {
            document.getElementById('formTitle').innerText = 'Edit Cash Details';
            document.getElementById('id').value = data.id;
            document.getElementById('plot_no').value = data.plot_no;
            document.getElementById('customer_name').value = data.customer_name;
            document.getElementById('amount').value = data.amount;
            document.getElementById('cash_received_date').value = data.cash_received_date;
            document.getElementById('cashForm').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetForm() {
            document.getElementById('cashForm').reset();
            document.getElementById('formTitle').innerText = 'Add Cash Details';
            document.getElementById('id').value = '';
        }

        function showOTPModal(deleteId) {
            document.getElementById('otp_delete_id').value = deleteId;
            const otpModal = new bootstrap.Modal(document.getElementById('otpModal'), {
                keyboard: false
            });
            otpModal.show();
        }
    </script>
    <?php echo $modal_trigger; ?>

</body>

</html>