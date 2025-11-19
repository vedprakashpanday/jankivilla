<?php
session_start();
include_once 'connectdb.php';

if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

$employeeid = $_SESSION['sponsor_id'];

$select = "SELECT * FROM employees WHERE emp_id = ?";
$stmt = $pdo->prepare($select);
$stmt->execute([$employeeid]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $membername    = $row['emp_name'];
    $memberid      = $row['emp_id'];
    $membermobile  = $row['mobile'];
    $registerdate  = $row['created_at'];
    $memberemail   = $row['email'];
    $memberaddress = $row['address'];
}



?>





<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers | Accountant Panel
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

    <style>
        #level1_count,
        #level2_count,
        #level3_count,
        #level4_count,
        #level5_count,
        #level6_count,
        #level7_count,
        #level8_count,
        #level9_count,
        #level10_count {
            color: #fff;
        }
    </style>

</head>

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">

    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "account-headersidepanel.php"; ?>

                <div class="main-panel">

                    <div style="background:#fff;  border: 2px solid #fff; padding:10px; box-shadow: 1px 3px 12px 4px #988f8f; width: 100%;">
                        <p style="color: black; font-size: 20px; font-family: 'Arial Rounded MT'; text-align:center">ACCOUNT DASHBOARD</p>
                    </div>

                    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">

                        <div class="row mb-4">

                            <div class="col-md-6">

                                <h3>Welcome! to Account Dashboard</h3>

                                <div class="col-md-12 col-sm-12 col-12 card-box3 d-none">

                                </div>

                                <div class="col-md-12 col-sm-12 col-12 card-box3 d-none">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">

                                    </div>
                                </div>
                            </div>


                        </div>




                        <div class="row justify-content-center mx-2">
                            <div class="col-md-12 details-box">
                                <h3 class="text-center mb-4">Details</h3>
                                <div class="details-section">

                                    <div class="row">

                                        <div class="col-md-6 detail-item">
                                            <p style="color: #222; margin-top: 10px">
                                                <b>User Name : </b>
                                                <span id="" style="margin-left: 10px"><?= $membername; ?></span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 detail-item">
                                            <p style="color: #222; margin-top: 10px">
                                                <b>User Id : </b>
                                                <span id="" style="margin-left: 10px"><?= $memberid; ?></span>
                                            </p>
                                        </div>


                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 detail-item">

                                            <b>Registration Date : </b>
                                            <span id="" style="margin-left: 10px"><?= $registerdate; ?></span>

                                        </div>
                                        <div class="col-md-6 detail-item">

                                            <b>Mobile No : </b>
                                            <span id="" style="margin-left: 10px;"><?= $membermobile ?></span>



                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 detail-item">

                                            <b>Email : </b>
                                            <span id="" style="margin-left: 10px"><?= $memberemail ?? 'N/A' ?></span>

                                        </div>
                                        <div class="col-md-6 detail-item">

                                            <b>Address : </b>
                                            <span id="" style="margin-left: 10px"><?= $memberaddress ?></span>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <?php include "account-footer.php"; ?>
        </div>
    </div>



    <style>
        .card-box {
            background: #242b47;
            height: 80px;
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            margin-bottom: 15px;
            border-radius: 10px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .card-box1 {
            background: #003e27;
            height: 77px;
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            margin-bottom: 15px;
            border-radius: 10px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .card-box3 {
            background: #242b47;
            height: 60px;
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            margin-bottom: 15px;
            border-radius: 10px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .card-box:hover {
            /*background-color: #ff9027;*/
            /* Background color on hover */
            color: white;
        }

        .icon-text-wrapper p {
            margin: 0;
        }

        .details-box {
            background: #fff;
            padding: 40px;
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            border-radius: 10px;
        }

        .details-section {
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .detail-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            font-size: 16px;
        }

        b {
            font-size: 17px;
            font-weight: 600;
            color: #242b47;
        }

        p {
            font-size: 16px;
            font-weight: 400;
            color: white;
        }

        .text-center {
            text-align: center;
        }

        .mb-4 {
            margin-bottom: 30px;
        }
    </style>



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
    </form>


</body>

</html>