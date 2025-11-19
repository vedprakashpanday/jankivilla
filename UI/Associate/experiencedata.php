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

// ---------- PROCESS FORM ----------
$js_alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_experience'])) {

    $member_id = $_SESSION['sponsor_id'] ?? '';
    if (empty($member_id)) {
        $js_alert = "<script>alert('Error: Member ID not found in session.');</script>";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tbl_experience 
            (member_id, company_name, duration, work_profile) 
            VALUES (?, ?, ?, ?)
        ");

        $success = true;
        $errors  = [];

        for ($i = 0; $i < 4; $i++) {
            $company = trim($_POST['exp_company'][$i] ?? '');
            $duration = trim($_POST['exp_duration'][$i] ?? '');
            $profile = trim($_POST['exp_profile'][$i] ?? '');

            // Optional: require company if duration/profile exists
            if (!empty($duration) && empty($company)) {
                $errors[] = "Row " . ($i + 1) . ": Company name required if duration is filled.";
                $success = false;
            }

            if ($success) {
                try {
                    $stmt->execute([$member_id, $company, $duration, $profile]);
                } catch (Exception $e) {
                    $errors[] = "Row " . ($i + 1) . ": Save failed.";
                    $success = false;
                }
            }
        }

        if ($success) {
            $js_alert = "<script>alert('Experience details saved successfully!');</script>";
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
                                                        <h4 class="mb-0">Add Experience Details</h4>
                                                    </div>
                                                    <div class="card-body">

                                                        <form method="post" action="">
                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <fieldset style="border:2px solid #28a745; padding:15px; border-radius:8px;">
                                                                        <legend style="width:auto; padding:0 10px; font-weight:bold; color:#28a745; font-size:18px;">
                                                                            Add Experience Details
                                                                        </legend>

                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered table-sm" style="font-size:14px;">
                                                                                <thead style="background:#28a745; color:white;">
                                                                                    <tr>
                                                                                        <th style="width:6%;">S.No.</th>
                                                                                        <th style="width:30%;">Company Name</th>
                                                                                        <th style="width:25%;">Duration (Month/Year)</th>
                                                                                        <th style="width:39%;">Work Profile</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <!-- Row 1 -->
                                                                                    <tr>
                                                                                        <td>1.</td>
                                                                                        <td><input type="text" name="exp_company[]" class="form-control form-control-sm" placeholder="e.g. ABC Corp"></td>
                                                                                        <td><input type="text" name="exp_duration[]" class="form-control form-control-sm" placeholder="e.g. Jan 2020 - Dec 2021"></td>
                                                                                        <td><input type="text" name="exp_profile[]" class="form-control form-control-sm" placeholder="e.g. Sales Executive"></td>
                                                                                    </tr>
                                                                                    <!-- Row 2 -->
                                                                                    <tr>
                                                                                        <td>2.</td>
                                                                                        <td><input type="text" name="exp_company[]" class="form-control form-control-sm" placeholder="e.g. XYZ Ltd"></td>
                                                                                        <td><input type="text" name="exp_duration[]" class="form-control form-control-sm" placeholder="e.g. Mar 2018 - Nov 2019"></td>
                                                                                        <td><input type="text" name="exp_profile[]" class="form-control form-control-sm" placeholder="e.g. Marketing Assistant"></td>
                                                                                    </tr>
                                                                                    <!-- Row 3 -->
                                                                                    <tr>
                                                                                        <td>3.</td>
                                                                                        <td><input type="text" name="exp_company[]" class="form-control form-control-sm" placeholder="e.g. PQR Solutions"></td>
                                                                                        <td><input type="text" name="exp_duration[]" class="form-control form-control-sm" placeholder="e.g. Jun 2016 - Feb 2018"></td>
                                                                                        <td><input type="text" name="exp_profile[]" class="form-control form-control-sm" placeholder="e.g. Team Lead"></td>
                                                                                    </tr>
                                                                                    <!-- Row 4 -->
                                                                                    <tr>
                                                                                        <td>4.</td>
                                                                                        <td><input type="text" name="exp_company[]" class="form-control form-control-sm" placeholder="e.g. Freelance"></td>
                                                                                        <td><input type="text" name="exp_duration[]" class="form-control form-control-sm" placeholder="e.g. 2015 - 2016"></td>
                                                                                        <td><input type="text" name="exp_profile[]" class="form-control form-control-sm" placeholder="e.g. Consultant"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                            </div>

                                                            <div class="text-center mt-4">
                                                                <button type="submit" name="save_experience" class="btn btn-success btn-lg">
                                                                    Save Experience Details
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