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

$sponsor_id = (string)$_SESSION['sponsor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // File Upload Directory
    $upload_dir = "../admin/member_document/";

    // Get Form Data
    $address_proof_type = $_POST['address_proof_type'];
    $pan_card_no = $_POST['pan_card_no'];
    $bank_acc_no = $_POST['bank_acc_no'];

    // File Handling
    function uploadFile($file_input_name, $upload_dir)
    {
        if ($_FILES[$file_input_name]['error'] == 0) {
            $file_name = time() . "_" . basename($_FILES[$file_input_name]['name']);
            $target_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_path)) {
                return $file_name;
            }
        }
        return false;
    }

    $address_proof_file = uploadFile('address_proof_file', $upload_dir);
    $pan_card_file = uploadFile('pan_card_file', $upload_dir);
    $bank_preview_file = uploadFile('bank_preview_file', $upload_dir);

    if (!$address_proof_file || !$pan_card_file || !$bank_preview_file) {
        die("Error: File upload failed!");
    }

    // Insert into Database
    $query = "INSERT INTO tbl_kyc (sponsor_id, address_proof_type, address_proof_file, pan_card_no, pan_card_file, bank_acc_no, bank_preview_file)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sponsor_id, $address_proof_type, $address_proof_file, $pan_card_no, $pan_card_file, $bank_acc_no, $bank_preview_file]);

    echo "<script>alert('KYC details uploaded successfully!');</script>";
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
    <form method="post" action="./kycdetails.php" id="form1" enctype="multipart/form-data">

        <div class="wrapper">
            <div class="container-scroller">


                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include "associate-headersidepanel.php"; ?>

                    <div class="main-panel" style="padding: 5px">

                        <form method="POST" enctype="multipart/form-data">
                            <div style="background: #fff; padding: 10px; padding-top: 50px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                <h3>KYC Details</h3>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6 pb-3">
                                        <b>Upload Address Proof</b>
                                        <select name="address_proof_type" class="form-control" required>
                                            <option value="">----Select----</option>
                                            <option value="Aadhar Card">Aadhar Card</option>
                                            <option value="Voter Id">Voter Id</option>
                                            <option value="Driving License">Driving License</option>
                                            <option value="Passport">Passport</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <b>Upload Address Proof File</b>
                                        <input type="file" name="address_proof_file" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <b>PAN Card No.</b>
                                        <input type="text" name="pan_card_no" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <b>Upload PAN Card</b>
                                        <input type="file" name="pan_card_file" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <b>Bank Account No</b>
                                        <input type="text" name="bank_acc_no" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 pb-3">
                                        <b>Upload Bank Preview</b>
                                        <input type="file" name="bank_preview_file" class="form-control" required>
                                    </div>
                                </div>

                                <div class="pt-4 text-center">
                                    <input type="submit" name="submit" value="Update" class="btn btn-success">
                                </div>
                            </div>
                        </form>

                        <?php include "associate-footer.php"; ?>
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
        <style>
            i {
                color: yellow;
            }
        </style>
    </form>


</body>

</html>