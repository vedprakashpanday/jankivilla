<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}


if (isset($_POST['btnsubmit'])) {
    try {
        // Generate random 5-digit number for customer_id (e.g., "CUST12345")
        $random_id = "CUST" . str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);

        // Generate random 6-digit password (e.g., "483920")
        $random_password = str_pad(rand(0, 999999), 6, "0", STR_PAD_LEFT);

        // Insert into customer_details table
        $sql = "INSERT INTO customer_details (
            customer_id, password, customer_name, customer_mobile, customer_email,
            aadhar_number, pan_number, nominee_name, nominee_aadhar, address,
            state, district
        ) VALUES (
            :customer_id, :password, :customer_name, :customer_mobile, :customer_email,
            :aadhar_number, :pan_number, :nominee_name, :nominee_aadhar, :address,
            :state, :district
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':customer_id'      => $random_id,
            ':password'         => $random_password,
            ':customer_name'    => $_POST['customer_name'],
            ':customer_mobile'  => $_POST['customer_mobile'],
            ':customer_email'   => $_POST['customer_email'],
            ':aadhar_number'    => $_POST['aadhar_number'],
            ':pan_number'       => $_POST['pan_number'],
            ':nominee_name'     => $_POST['nominee_name'],
            ':nominee_aadhar'   => $_POST['nominee_aadhar'],
            ':address'          => $_POST['address'],
            ':state'            => $_POST['state'],
            ':district'         => $_POST['district']
        ]);

        // Success message and display generated credentials
        echo "<script>
                alert('Customer inserted successfully!\\nCustomer ID: $random_id\\nPassword: $random_password');
                window.location.href=window.location.href; // Replace with your form page
              </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
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

    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include 'adminheadersidepanel.php'; ?>

                <div class="main-panel">
                    <style>
                        .col-md-4 {
                            padding: 1rem;
                        }

                        .form-control {
                            margin-top: 7px;
                        }
                    </style>
                    <div class="content-wrapper">
                        <div class="">
                            <div class="">
                                <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                                    <div class="">
                                        <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3><u>Customer Details:-</u></h3>
                                            <form method="POST" action="">
                                                <div class="row">
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Customer Name</b>
                                                        <input name="customer_name" type="text" id="customer_name" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Customer Mobile No</b>
                                                        <input name="customer_mobile" type="text" id="customer_mobile" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Customer Email Id</b>
                                                        <input name="customer_email" type="email" id="customer_email" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Customer Aadhar</b>
                                                        <input name="aadhar_number" type="text" id="aadhar_number" class="form-control">
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Customer Pan</b>
                                                        <input name="pan_number" type="text" id="pan_number" class="form-control">
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Address</b>
                                                        <input name="address" type="text" id="address" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>State</b>
                                                        <input name="state" type="text" id="state" class="form-control">
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>District</b>
                                                        <input name="district" type="text" id="district" class="form-control">
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Nominee Name</b>
                                                        <input name="nominee_name" type="text" id="nominee_name" class="form-control">
                                                    </div>
                                                </div>
                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <b>Nominee Aadhar</b>
                                                        <input name="nominee_aadhar" type="text" id="nominee_aadhar" class="form-control">
                                                    </div>
                                                    <div class="col-md-4" style="padding: 10px">
                                                        <div class="text-center">
                                                            <br>
                                                            <input type="submit" name="btnsubmit" value="Submit" id="btnsubmit" class="btn btn-primary btn-md mt-1" style="width:150px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
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