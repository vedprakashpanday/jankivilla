<?php
session_start();
include "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../Customer_Login.php"); // Redirect to dashboard
    exit;
}

// Get customer ID from session
$customerid = $_SESSION['customer_id'];

// Fetch customer details
$stmt = $pdo->prepare("SELECT * FROM customer_details WHERE customer_id = ?");
$stmt->execute([$customerid]);
$customer = $stmt->fetch();

// Fetch customer financial details
$stmt2 = $pdo->prepare("SELECT SUM(net_amount) AS total_net, SUM(payamount) AS total_pay, SUM(due_amount) AS total_due FROM tbl_customeramount WHERE customer_id = ?");
$stmt2->execute([$customerid]);
$amounts = $stmt2->fetch();


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

    <style>
        /* Styling for financial summary cards */
        .card-box {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            min-height: 130px;
        }

        .card-box:hover {
            transform: translateY(-5px);
        }

        .card-box i {
            font-size: 30px;
            color: #007bff;
        }

        .card-box p {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .amount {
            font-size: 22px;
            color: #28a745;
            font-weight: bold;
        }

        /* Improved spacing for better layout */
        .card-container {
            margin-bottom: 30px;
            /* Space between financial summary and customer details */
            padding: 20px;
        }

        .details-box {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .details-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .detail-item {
            font-size: 16px;
            font-weight: 600;
        }

        /* Responsive fixes */
        @media (max-width: 768px) {
            .card-container {
                flex-direction: column;
                align-items: center;
            }

            .card-box {
                width: 100%;
                margin-bottom: 15px;
            }

            .details-section {
                grid-template-columns: 1fr;
                /* Stack details on smaller screens */
            }
        }
    </style>

</head>

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">


    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "customerheadersidepanel.php"; ?>

                <div class="main-panel">
                    <div class="content-wrapper p-0">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <p style="color:black;font-size:17px;font-family:'Arial Rounded MT';font-weight:600;">Dashboard</p>

                                    <div class="container py-3">
                                        <!-- Financial Summary Cards -->
                                        <div class="row justify-content-center card-container">
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="card-box">
                                                    <i class="ti-credit-card"></i>
                                                    <p>Net Amount</p>
                                                    <p class="amount"><?= number_format($amounts['total_net'], 2) ?></p>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="card-box">
                                                    <i class="ti-user"></i>
                                                    <p>Pay Amount</p>
                                                    <p class="amount"><?= number_format($amounts['total_pay'], 2) ?></p>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="card-box">
                                                    <i class="ti-credit-card"></i>
                                                    <p>Dues Amount</p>
                                                    <p class="amount"><?= number_format($amounts['total_due'], 2) ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer Details Section -->
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 details-box">
                                                <h3 class="text-center mb-4">Customer Details</h3>
                                                <div class="details-section">
                                                    <div class="detail-item"><b>Customer Id:</b> <?= htmlspecialchars($customer['customer_id']) ?></div>
                                                    <div class="detail-item"><b>Name:</b> <?= htmlspecialchars($customer['customer_name']) ?></div>
                                                    <div class="detail-item"><b>Mobile No:</b> <?= htmlspecialchars($customer['customer_mobile']) ?></div>
                                                    <div class="detail-item"><b>Email Id:</b> <?= htmlspecialchars($customer['customer_email']) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- container -->
                                </div> <!-- card-body -->
                            </div> <!-- card -->
                        </div> <!-- stretch-card -->
                    </div>
                </div>


            </div>
        </div>
    </div>



    <style>
        .table-container {
            width: 100%;
            overflow-x: auto;
            /* Mobile view me scroll enable karega */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensure karega ki column widths fix ho */
            margin-top: 1.5rem;
        }

        td,
        th {
            padding: 8px;
            /* border: 1px solid #ddd; */
            text-align: left;
            word-wrap: break-word;
            /* Text wrap hone de */
            overflow-wrap: break-word;
        }

        .icon-text {
            width: clamp(9rem, 10.5rem, 14rem);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .digits {
            min-width: 6rem;
            /* Ensure karega ki digits squeeze na ho */
            max-width: 14rem;
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            /* Desktop view me ek line me rakhne ke liye */
        }

        @media (max-width: 768px) {
            .digits {
                white-space: normal;
                /* Mobile pe wrapping allow karein */
                text-align: right;
            }

            .d-flex {
                flex-direction: column;
                /* Mobile pe items ek ke neeche aayein */
            }

            .timeslab {
                width: fit-content;
            }

            .timeslab2 {
                width: fit-content;
                flex-direction: row;
            }

            #ct7 {
                font-size: 12px;
            }


        }

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


</body>

</html>