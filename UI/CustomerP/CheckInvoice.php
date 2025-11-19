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
try {
    // SQL query with JOIN to fetch customer and financial details
    $sql = "SELECT 
                c.id,
                c.customer_id,
                c.customer_name,
                c.customer_mobile,
                c.customer_email,
                a.productname,
                a.producttype,
                COALESCE(SUM(a.net_amount), 0) AS total_net,
                COALESCE(SUM(a.payamount), 0) AS total_pay,
                COALESCE(SUM(a.due_amount), 0) AS total_due
            FROM customer_details c
            LEFT JOIN tbl_customeramount a ON c.customer_id = a.customer_id
            WHERE c.customer_id = :customerid
            GROUP BY c.customer_id";

    // Prepare and execute query
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":customerid", $customerid, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the result
    $customerData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
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

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <div class="wrapper">
        <div class="container-scroller">

            <div class="container-fluid page-body-wrapper">
                <?php include "customerheadersidepanel.php"; ?>

                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="col-md-12 stretch-card">
                            <div class="card" style="overflow: auto;">
                                <div class="card-body">

                                    <div class="" style="padding-top: 0px;padding-bottom:20px;">
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="salesTable" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Customer Id</th>
                                                                <th>Name</th>
                                                                <th>Mobile Number</th>
                                                                <th>Email</th>
                                                                <th>Plot Name</th>
                                                                <th>Plot Type</th>
                                                                <th>Net Amount</th>
                                                                <th>Pay Amount</th>
                                                                <th>Dues Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($customerData) { ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($customerData['id']) ?></td>
                                                                    <td><?= htmlspecialchars($customerData['customer_id']) ?></td>
                                                                    <td><?= htmlspecialchars($customerData['customer_name']) ?></td>
                                                                    <td><?= htmlspecialchars($customerData['customer_mobile']) ?></td>
                                                                    <td><?= htmlspecialchars($customerData['customer_email']) ?></td>
                                                                    <td><?= htmlspecialchars($customerData['productname']) ?></td>
                                                                    <td><?php
                                                                        if ($customerData['producttype'] == 1) {
                                                                            echo "One Time Registry";
                                                                        } elseif ($customerData['producttype'] == 2) {
                                                                            echo "EMI Mode";
                                                                        } else {
                                                                            echo "N/A";
                                                                        }
                                                                        ?></td>
                                                                    <td><?= number_format($customerData['total_net'], 2) ?></td>
                                                                    <td><?= number_format($customerData['total_pay'], 2) ?></td>
                                                                    <td><?= number_format($customerData['total_due'], 2) ?></td>
                                                                </tr>
                                                            <?php } else { ?>
                                                                <tr>
                                                                    <td colspan="8" class="text-center">No records found</td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    @media (max-width: 768px) {

                        .card-box .details-box {
                            background: #fff;
                            padding: 40x;
                            border: 2px solid #fff;
                            box-shadow: 1px 3px 12px 4px #988f8f;
                            border-radius: 10px;
                        }

                        .row {
                            margin-left: -35px;
                            margin-right: -35px;
                            margin-top: 0px;


                        }

                        .stretch-card {
                            width: 100%;
                            display: flex;
                            flex-direction: column;
                            padding-left: 1px;
                            padding-right: 1px;
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
            </div>
        </div>
    </div>
    <style>
        .card-box {
            background: rgb(255, 255, 255);
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            margin-bottom: 50px;
            border-radius: 10px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .card-box1 {
            background: #003e27;
            height: 80px;
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

        }
    </style>

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

        });
    </script>

</body>

</html>