<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include_once 'connectdb.php';

if (isset($_COOKIE['sponsor_login'])) {
    $login_data = json_decode($_COOKIE['sponsor_login'], true);
    $sponsorid = $login_data['sponsorid'];
    $sponsorpass = $login_data['sponsorpass'];

    $select = $pdo->prepare("select * from tbl_hire where sponsor_id='$sponsorid' AND  sponsor_pass='$sponsorpass'");
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row['sponsor_id'] === $sponsorid and $row['sponsor_pass'] === $sponsorpass) {
        $_SESSION['sponsor_id'] = $row['sponsor_id'];
        $_SESSION['sponsor_pass'] = $row['sponsor_pass'];
        $_SESSION['sponsor_name'] = $row['s_name'];
    }
}

// Redirect the user to the login page if they're not logged in
if (!isset($_SESSION['sponsor_id'])) {
    header('location:../../login.php');
    exit();
}

$js_alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_bank'])) {

    $member_id = $_SESSION['sponsor_id'] ?? '';
    if (empty($member_id)) {
        $js_alert = "<script>alert('Error: Member ID not found in session.');</script>";
    } else {
        $account_name = trim($_POST['account_name'] ?? '');
        $account_no   = trim($_POST['account_no'] ?? '');
        $bank_name    = trim($_POST['bank_name'] ?? '');
        $branch       = trim($_POST['branch'] ?? '');
        $ifsc_code    = strtoupper(trim($_POST['ifsc_code'] ?? ''));

        $success = true;
        $errors  = [];

        // Validation
        if (empty($account_name)) {
            $errors[] = "Account Name is required.";
            $success = false;
        }
        if (empty($account_no)) {
            $errors[] = "Account No. is required.";
            $success = false;
        }
        if (empty($bank_name)) {
            $errors[] = "Bank Name is required.";
            $success = false;
        }
        if (empty($branch)) {
            $errors[] = "Branch is required.";
            $success = false;
        }
        if (empty($ifsc_code)) {
            $errors[] = "IFSC Code is required.";
            $success = false;
        } elseif (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc_code)) {
            $errors[] = "Invalid IFSC Code format (e.g. SBIN0001234).";
            $success = false;
        }

        if ($success) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO tbl_bank_details 
                    (member_id, account_name, account_no, bank_name, branch, ifsc_code) 
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    account_name = VALUES(account_name),
                    account_no = VALUES(account_no),
                    bank_name = VALUES(bank_name),
                    branch = VALUES(branch),
                    ifsc_code = VALUES(ifsc_code)
                ");
                $stmt->execute([$member_id, $account_name, $account_no, $bank_name, $branch, $ifsc_code]);
                $js_alert = "<script>alert('Bank details saved successfully!');</script>";
            } catch (Exception $e) {
                $js_alert = "<script>alert('Error: Could not save bank details.');</script>";
            }
        } else {
            $msg = implode("\\n", $errors);
            $js_alert = "<script>alert('Errors:\\n{$msg}');</script>";
        }
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
    <link rel="stylesheet" href="../resources/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="../resources/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
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


    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css">
    <script src="../js/jquery-1.8.2.js" type="text/javascript"></script>
    <script src="../js/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(function() {
            var date = new Date();
            var currentMonth = date.getMonth();
            var currentDate = date.getDate();
            var currentYear = date.getFullYear();

            jQuery("#").datepicker({
                dateFormat: 'dd/mm/yy',
                maxDate: new Date(currentYear - 18, currentMonth, currentDate),
                changeMonth: true,
                changeYear: true
            });
        });
    </script>

</head>

<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper">
        <div class="container-scroller">
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">


                <?php include 'associate-headersidepanel.php'; ?>


                <div class="main-panel">
                    <style>
                        .col-md-4 {
                            padding: 1rem;
                        }

                        .form-control {
                            margin-top: 7px;
                        }
                    </style>
                    <div class="">
                        <div class="">
                            <div class="card">
                                <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <div class="container mt-5">
                                                <div class="card shadow">
                                                    <div class="card-header bg-primary text-white">
                                                        <h4 class="mb-0">Add Bank Details</h4>
                                                    </div>
                                                    <div class="card-body">

                                                        <form method="post" action="">
                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <fieldset style="border:2px solid #ffc107; padding:15px; border-radius:8px;">
                                                                        <legend style="width:auto; padding:0 10px; font-weight:bold; color:#ffc107; font-size:18px;">
                                                                            BANK DETAILS
                                                                        </legend>

                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered table-sm" style="font-size:14px;">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="width:25%; background:#f8f9fa; font-weight:bold;">Account Name</td>
                                                                                        <td><input type="text" name="account_name" class="form-control form-control-sm" placeholder="e.g. John Doe" value="<?php echo htmlspecialchars($_POST['account_name'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Account No.</td>
                                                                                        <td><input type="text" name="account_no" class="form-control form-control-sm" placeholder="e.g. 1234567890" value="<?php echo htmlspecialchars($_POST['account_no'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Bank Name</td>
                                                                                        <td><input type="text" name="bank_name" class="form-control form-control-sm" placeholder="e.g. State Bank of India" value="<?php echo htmlspecialchars($_POST['bank_name'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Branch</td>
                                                                                        <td><input type="text" name="branch" class="form-control form-control-sm" placeholder="e.g. MG Road Branch" value="<?php echo htmlspecialchars($_POST['branch'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">IFSC Code</td>
                                                                                        <td><input type="text" name="ifsc_code" class="form-control form-control-sm" placeholder="e.g. SBIN0001234" maxlength="11" value="<?php echo htmlspecialchars($_POST['ifsc_code'] ?? ''); ?>" style="text-transform:uppercase;"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                            </div>

                                                            <div class="text-center mt-4">
                                                                <button type="submit" name="save_bank" class="btn btn-warning btn-lg" style="background:#ffc107; border:none;">
                                                                    Save Bank Details
                                                                </button>
                                                            </div>

                                                            <!-- SHOW JS ALERT -->
                                                            <?php echo $js_alert; ?>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'associate-footer.php'; ?>
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