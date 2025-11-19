<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}




$member_id = '';
$total_net = 0;
$total_payment = 0;
$total_dues = 0;
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ctl00$ContentPlaceHolder1$btnsearch'])) {
    $member_id = trim($_POST['ctl00$ContentPlaceHolder1$txtName']);

    if (!empty($member_id)) {
        // Prepare and execute query joining both tables
        $sql = "SELECT 
                    s.net_amount,
                    s.payamount,
                    s.due_amount
                FROM tbl_customeramount s
                LEFT JOIN receiveallpayment r ON s.member_id = r.member_id
                WHERE s.member_id = :member_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':member_id' => $member_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate totals
        foreach ($results as $row) {
            $total_net = floatval($row['net_amount']);
            $total_payment = floatval($row['payamount'] ?? 0); // Use 0 if null
            $total_dues = floatval($row['due_amount'] ?? 0);   // Use 0 if null
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


    <style>
        .navbar .navbar-brand-wrapper .navbar-brand img {
            margin-top: 0px;
        }

        #ct7 {
            color: #fff;
            padding: 18px 8px;
            font-size: 16px;
            font-weight: 900;
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

    <script>
        let defaultMerchantConfiguration = {
            "root": "",
            "style": {
                "bodyColor": "",
                "themeBackgroundColor": "",
                "themeColor": "",
                "headerBackgroundColor": "",
                "headerColor": "",
                "errorColor": "",
                "successColor": ""
            },
            "flow": "DEFAULT",
            "data": {
                "orderId": "",
                "token": "",
                "tokenType": "TXN_TOKEN",
                "amount": "",
                "userDetail": {
                    "mobileNumber": "",
                    "name": ""
                }
            },
            "merchant": {
                "mid": "",
                "name": "",
                "redirect": true
            },
            "labels": {},
            "payMode": {
                "labels": {},
                "filter": [],
                "order": []
            },
            "handler": {}
        };
    </script>
    <style type="text/css">
        /* Chart.js */
        @keyframes chartjs-render-animation {
            from {
                opacity: .99
            }

            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            animation: chartjs-render-animation 1ms
        }

        .chartjs-size-monitor,
        .chartjs-size-monitor-expand,
        .chartjs-size-monitor-shrink {
            position: absolute;
            direction: ltr;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
            visibility: hidden;
            z-index: -1
        }

        .chartjs-size-monitor-expand>div {
            position: absolute;
            width: 1000000px;
            height: 1000000px;
            left: 0;
            top: 0
        }

        .chartjs-size-monitor-shrink>div {
            position: absolute;
            width: 200%;
            height: 200%;
            left: 0;
            top: 0
        }


        .franchiseSidebar:hover {
            background: #ff9027 !important;
        }
    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper">
        <div class="container-scroller">



            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <div class="franchise_nav_menu">


                    <?php include 'adminheadersidepanel.php'; ?>


                </div>


                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="col-md-12 stretch-card">
                            <div class="card">


                                <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">

                                        <div class="col-md-9">
                                            <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                <h2>Income Details</h2>

                                                <hr>
                                                <form method="post" action="">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <b>Search By Memberid</b>
                                                            <input name="ctl00$ContentPlaceHolder1$txtName"
                                                                type="text"
                                                                id="ContentPlaceHolder1_txtName"
                                                                class="form-control"
                                                                value="<?php echo htmlspecialchars($member_id); ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <br>
                                                            <input type="submit"
                                                                name="ctl00$ContentPlaceHolder1$btnsearch"
                                                                value="Search"
                                                                id="ContentPlaceHolder1_btnsearch"
                                                                title="View Details"
                                                                class="btn btn-success">
                                                        </div>
                                                    </div>
                                                </form>

                                                <?php if (!empty($results)): ?>
                                                    <div class="mt-4">
                                                        <table class="table table-bordered d-none">
                                                            <thead>
                                                                <tr>
                                                                    <th>Net Amount (Sales)</th>
                                                                    <th>Payment Amount</th>
                                                                    <th>Due Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($results as $row): ?>
                                                                    <tr>
                                                                        <td><?php echo number_format($row['net_amount'], 2); ?></td>
                                                                        <td><?php echo number_format($row['payamount'] ?? 0, 2); ?></td>
                                                                        <td><?php echo number_format($row['due_amount'] ?? 0, 2); ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>

                                                        <p style="text-align:right"><b>Total Net Amount :</b>
                                                            <span id="ContentPlaceHolder1_txttotal"><?php echo number_format($total_net, 2); ?></span>
                                                        </p>
                                                        <p style="text-align:right"><b>Total Payment Amount :</b>
                                                            <span id="ContentPlaceHolder1_txttotalp"><?php echo number_format($total_payment, 2); ?></span>
                                                        </p>
                                                        <p style="text-align:right"><b>Total Dues Amount :</b>
                                                            <span id="ContentPlaceHolder1_txttotaldues"><?php echo number_format($total_dues, 2); ?></span>
                                                        </p>
                                                    </div>
                                                <?php elseif ($member_id !== '' && empty($results)): ?>
                                                    <div class="mt-4 alert alert-info">No records found for member ID: <?php echo htmlspecialchars($member_id); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <footer class="footer" style="text-align:center">
                <div>
                    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024. <a href="https://www.infoerasoftware.com" target="_blank">Infoera Software Services Pvt. Ltd</a>, All Right Reserved.</span>
                </div>Designed By<a href="#" target="_blank">InfoEra
                </a>
            </footer><a href="#" target="_blank">
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





    </form>


</body>

</html>