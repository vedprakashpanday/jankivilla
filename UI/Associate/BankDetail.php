<?php
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

// Ensure sponsor_id is set in session
if (!isset($_SESSION['sponsor_id'])) {
    die("Error: Sponsor ID not found in session.");
}

$sponsor_id = (string)$_SESSION['sponsor_id']; // Ensure it's treated as a string

// Fetch existing bank details
$query = "SELECT * FROM tbl_bnk WHERE sponsor_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$sponsor_id]);
$bankDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $account_name = $_POST['txtACName'];
    $account_no = $_POST['txtACNo'];
    $bank_name = $_POST['txtBName'];
    $branch = $_POST['txtBranch'];
    $ifsc_code = $_POST['txtIFSCCode'];
    $pan_no = $_POST['txtPan'];
    $b_city = $_POST['txtBCity'] ?? ''; // Handle `b_city`, optional

    if ($bankDetails) {
        // Update existing record
        $updateQuery = "UPDATE tbl_bnk SET 
            s_name = ?, 
            s_acc = ?, 
            s_bank = ?, 
            b_branch = ?, 
            b_city = ?, 
            b_ifsc = ?, 
            b_pan = ? 
            WHERE sponsor_id = ?";

        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$account_name, $account_no, $bank_name, $branch, $b_city, $ifsc_code, $pan_no, $sponsor_id]);
        $message = "Bank details updated successfully!";
    } else {
        // Insert new record
        $insertQuery = "INSERT INTO tbl_bnk (sponsor_id, s_name, s_acc, s_bank, b_branch, b_city, b_ifsc, b_pan) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$sponsor_id, $account_name, $account_no, $bank_name, $branch, $b_city, $ifsc_code, $pan_no]);
        $message = "Bank details saved successfully!";
    }

    // Output message instead of redirecting for debugging
    echo $message;
}

?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head>
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




</head>

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">


    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper ">
                <?php include "associate-headersidepanel.php"; ?>



                <div class="main-panel ">

                    <div class="mx-3 p-3 my-4 rounded" style="background: #fff;  border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                        <h3>Bank Details </h3>
                        <hr>
                        <?php if (isset($_GET['msg'])): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <b>Account Name:</b>
                                    <input name="txtACName" type="text" class="form-control" value="<?php echo $bankDetails['s_name'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <b>Bank A/c No:</b>
                                    <input name="txtACNo" type="text" class="form-control" value="<?php echo $bankDetails['s_acc'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <b>Bank Name:</b>
                                    <input name="txtBName" type="text" class="form-control" value="<?php echo $bankDetails['s_bank'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <b>Branch:</b>
                                    <input name="txtBranch" type="text" class="form-control" value="<?php echo $bankDetails['b_branch'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <b>IFSC Code:</b>
                                    <input name="txtIFSCCode" type="text" class="form-control" value="<?php echo $bankDetails['b_ifsc'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <b>PAN No:</b>
                                    <input name="txtPan" type="text" class="form-control" value="<?php echo $bankDetails['b_pan'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Save</button>
                        </form>
                    </div>
                    <?php include "associate-footer.php"; ?>
                </div>
            </div>
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





    <style>
        i {
            color: yellow;
        }
    </style>



</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>