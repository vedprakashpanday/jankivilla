<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

// // Get the logged-in member's ID from session
// $member_id = $_SESSION['sponsor_id'];

// // Get the from and to dates from URL parameters
// $from_date = isset($_GET['from']) ? $_GET['from'] : '';
// $to_date = isset($_GET['to']) ? $_GET['to'] : '';

// // Validate dates
// if (empty($from_date) || empty($to_date)) {
//     die("Invalid date range specified.");
// }

// // Query to fetch commission history (assuming one row per member per period)
// $query = "SELECT 
//     member_id,
//     member_name,
//     sponsor_id,
//     direct_amount,
//     total_group_amount,
//     direct_percent,
//     direct_commission,
//     level_commission,
//     total_commission,
//     from_date,
//     to_date,
//     payment_status,
//     payment_date,
//     payment_mode,
//     cheque_number,
//     bank_name,
//     cheque_date,
//     utr_number,
//     status
// FROM commission_history 
// WHERE member_id = :member_id 
// AND from_date = :from_date 
// AND to_date <= :to_date 
// AND status = 'closed' 
// LIMIT 1";

// $stmt = $pdo->prepare($query);
// $stmt->execute([
//     ':member_id' => $member_id,
//     ':from_date' => $from_date,
//     ':to_date' => $to_date
// ]);
// $data = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch single row



// Get the logged-in member's ID from session
$member_id = $_SESSION['sponsor_id'];

// Get the from and to dates from URL parameters
$from_date = isset($_GET['from']) ? $_GET['from'] : '';
$to_date = isset($_GET['to']) ? $_GET['to'] : '';

// Validate dates
if (empty($from_date) || empty($to_date)) {
    die("Invalid date range specified.");
}

// Query to fetch commission history (including payment_details)
$query = "SELECT 
    member_id,
    member_name,
    sponsor_id,
    direct_amount,
    total_group_amount,
    direct_percent,
    direct_commission,
    level_commission,
    total_commission,
    from_date,
    to_date,
    payment_status,
    payment_date,
    payment_mode,
    cheque_number,
    bank_name,
    cheque_date,
    utr_number,
    status,
    payment_details
FROM commission_history 
WHERE member_id = :member_id 
AND from_date = :from_date 
AND to_date <= :to_date 
AND status = 'closed' 
LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':member_id' => $member_id,
    ':from_date' => $from_date,
    ':to_date' => $to_date
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch single row

// Initialize plot details array
$plot_details = [];

if ($data && !empty($data['payment_details'])) {
    $payment_details = json_decode($data['payment_details'], true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($payment_details) && is_array($payment_details)) {
        foreach (array_keys($payment_details) as $product_name) {
            // Query to fetch product type from tbl_customeramount and producttype tables
            $query_product_type = "
                SELECT pt.product_type_name
                FROM tbl_customeramount ca
                JOIN producttype pt ON ca.producttype = pt.product_type_id
                WHERE ca.member_id = :member_id 
                AND ca.productname = :productname
                LIMIT 1";

            $stmt_product_type = $pdo->prepare($query_product_type);
            $stmt_product_type->execute([
                ':member_id' => $member_id,
                ':productname' => $product_name
            ]);
            $product_type_data = $stmt_product_type->fetch(PDO::FETCH_ASSOC);

            $plot_details[] = [
                'plot_name' => htmlspecialchars($product_name),
                'plot_type' => $product_type_data ? htmlspecialchars($product_type_data['product_type_name']) : 'N/A'
            ];
        }
    }
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


    <style>
        /* A4 size at 96 DPI */
        .invoice-container {
            /* width: 210mm; */
            /* Exact A4 width */
            padding: 10mm;
            /* Reduced padding to save space */
            background: #fff;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header-img {
            width: 100%;
            /* max-width: 150mm; */
            /* Reduced significantly to save vertical space */
            margin: 0 auto 5px;
            /* Tightened spacing */
            display: block;
        }

        .invoice-title {
            color: #2c3e50;
            font-size: 24px;
            /* Slightly smaller but still readable */
            font-weight: bold;
            text-align: center;
            margin: 5px 0;
            /* Reduced spacing */
        }

        .invoice-period {
            color: #7f8c8d;
            font-size: 16px;
            /* Readable size */
            text-align: center;
            margin-bottom: 8px;
            /* Tightened spacing */
        }

        .invoice-details {
            background: #f9f9f9;
            padding: 8px;
            /* Reduced padding */
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .invoice-details p {
            margin: 2px 0;
            /* Minimal spacing between lines */
            font-size: 16px;
            /* Increased for readability */
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dotted #e0e0e0;
            padding-bottom: 2px;
            line-height: 1.2;
            /* Reduced line height to save space */
        }

        .invoice-details p strong {
            color: #34495e;
            font-weight: 600;
            width: 50%;
            /* Adjusted for balance */
        }

        .invoice-details p span {
            color: #2c3e50;
            width: 50%;
            text-align: right;
        }

        .buttons {
            text-align: center;
            margin-top: 8px;
            /* Reduced spacing */
        }

        .btn-print,
        .btn-back {
            padding: 6px 15px;
            /* Smaller buttons */
            font-size: 14px;
            /* Readable button text */
            border-radius: 5px;
        }

        .btn-print {
            background: #3498db;
            border-color: #3498db;
            color: #fff;
        }

        .btn-back {
            background: #7f8c8d;
            border-color: #7f8c8d;
            color: #fff;
            margin-left: 10px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }

            body * {
                visibility: hidden;
            }

            .invoice-container,
            .invoice-container * {
                visibility: visible;
            }

            .invoice-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 210mm;
                height: auto;
                padding: 5mm;
                margin: 0;
                border: none;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .invoice-container {
                zoom: 2.7;
                /* Scale down content slightly */
                -moz-transform: scale(0.9);
                -moz-transform-origin: 0 0;
            }

            .header-img {
                max-width: 100%;
                height: auto;
                max-height: 30mm;
            }

            .invoice-title {
                font-size: 18px;
                margin: 3px 0;
            }

            .invoice-period {
                font-size: 14px;
                margin-bottom: 5px;
            }

            .invoice-details p {
                font-size: 12px;
                line-height: 1.1;
                margin: 1px 0;
            }

            .buttons {
                display: none;
            }
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 14px;
            text-align: left;
        }

        .table th {
            background: #e0e0e0;
            font-weight: bold;
        }

        .table td.text-center {
            text-align: center;
        }


        .table {
            margin-top: 3px;
        }

        .table th,
        .table td {
            padding: 2px;
            font-size: 12px;
            line-height: 1.1;
        }
    </style>

</head>

<body>


    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php
                include "associate-headersidepanel.php";
                ?>

                <div class="main-panel">
                    <div class="box-body">
                        <div class="invoice-container">
                            <!-- Header Image -->
                            <img src="https://www.jankivilla.com/UI/images/hariheaderinvoice.png" alt="Hari Homes Header" class="header-img">

                            <!-- Invoice Title -->
                            <h2 class="invoice-title">Commission Invoice</h2>
                            <p class="invoice-period">Period: <?php echo htmlspecialchars($from_date) . " to " . htmlspecialchars($to_date); ?></p>

                            <?php if (!$data): ?>
                                <div class="alert alert-info">No commission data found for this period.</div>
                            <?php else: ?>
                                <div class="invoice-details">
                                    <?php
                                    $tds = $data['total_commission'] * 0.05;
                                    $admin_charge = $data['total_commission'] * 0.05;
                                    $final_amount = $data['total_commission'] - ($tds + $admin_charge);
                                    ?>
                                    <p><strong>Member ID:</strong> <span><?= htmlspecialchars($data['member_id']) ?></span></p>
                                    <p><strong>Member Name:</strong> <span><?= htmlspecialchars($data['member_name']) ?></span></p>
                                    <p><strong>Direct Business Amount:</strong> <span>₹<?= number_format($data['direct_amount'], 2) ?></span></p>
                                    <p><strong>Team Income:</strong> <span>₹<?= number_format($data['total_group_amount'], 2) ?></span></p>
                                    <p><strong>Total Income:</strong> <span>₹<?= number_format($data['total_group_amount'], 2) ?></span></p>
                                    <p><strong>Direct Commission %:</strong> <span><?= htmlspecialchars($data['direct_percent']) ?>%</span></p>
                                    <p><strong>Direct Commission:</strong> <span>₹<?= number_format($data['direct_commission'], 2) ?></span></p>
                                    <p><strong>Level Commission:</strong> <span>₹<?= number_format($data['level_commission'], 2) ?></span></p>
                                    <p><strong>Total Commission:</strong> <span>₹<?= number_format($data['total_commission'], 2) ?></span></p>
                                    <p><strong>TDS (5%):</strong> <span>₹<?= number_format($tds, 2) ?></span></p>
                                    <p><strong>Admin Charge (5%):</strong> <span>₹<?= number_format($admin_charge, 2) ?></span></p>
                                    <p><strong>Final Amount:</strong> <span>₹<?= number_format($final_amount, 2) ?></span></p>
                                    <p><strong>Payment Status:</strong>
                                        <span style="color: <?= ($data['payment_status'] == 'paid') ? 'green' : 'red'; ?>;">
                                            <?= ($data['payment_status'] == 'paid') ? 'Paid ✅' : 'Unpaid ❌'; ?>
                                        </span>
                                    </p>
                                    <?php if ($data['payment_status'] == 'paid'): ?>
                                        <p><strong>Payment Date:</strong> <span><?= htmlspecialchars($data['payment_date']) ?></span></p>
                                        <p><strong>Payment Mode:</strong> <span><?= htmlspecialchars(ucfirst($data['payment_mode'])) ?></span></p>
                                        <?php if (strtolower($data['payment_mode']) === 'cheque'): ?>
                                            <p><strong>Cheque Number:</strong> <span><?= htmlspecialchars($data['cheque_number']) ?></span></p>
                                            <p><strong>Bank Name:</strong> <span><?= htmlspecialchars($data['bank_name']) ?></span></p>
                                            <p><strong>Cheque Date:</strong> <span><?= htmlspecialchars($data['cheque_date']) ?></span></p>
                                        <?php elseif (strtolower($data['payment_mode']) === 'bank'): ?>
                                            <p><strong>UTR Number:</strong> <span><?= htmlspecialchars($data['utr_number']) ?></span></p>
                                        <?php elseif (strtolower($data['payment_mode']) === 'cash'): ?>
                                            <p><strong>Cash Payment:</strong> <span>Paid in Cash</span></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <h3>Plot Details</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Plot Name</th>
                                                <th>Plot Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($plot_details)): ?>
                                                <?php foreach ($plot_details as $plot): ?>
                                                    <tr>
                                                        <td><?= $plot['plot_name'] ?></td>
                                                        <td><?= $plot['plot_type'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">No plot details available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <!-- Buttons (hidden on print) -->
                            <div class="buttons">
                                <button class="btn btn-primary btn-print mb-2" onclick="window.print()">Print Invoice</button>&nbsp;&nbsp;
                                <a href="Total-Incomereport.php" class="btn btn-secondary btn-back mb-2">Back to Periods</a>
                            </div>
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



        </div>
    </div>
    <style>
        i {
            color: yellow;
        }
    </style>



</body>

</html>