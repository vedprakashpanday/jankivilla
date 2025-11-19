<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


$current_year = date('Y');
$current_month = date('m');

// Get filter values, default to empty for no filtering
$filter_member_id = $_POST['member_id'] ?? '';
$filter_year = $_POST['year'] ?? '';
$filter_month = $_POST['month'] ?? '';

// Step 1: Get unique member_ids and m_name for the filter dropdown
$members_query = "
    SELECT DISTINCT rap.member_id, r.m_name
    FROM receiveallpayment rap
    LEFT JOIN tbl_regist r ON rap.member_id = r.mem_sid
    ORDER BY rap.member_id
";
$stmt = $pdo->prepare($members_query);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 2: Get all payments with m_name and customer_name to calculate 25% eligibility
$payment_query = "
    SELECT 
        rap.member_id,
        rap.productname,
        rap.invoice_id,
        rap.payamount,
        rap.created_date,
        MAX(rap.net_amount) as net_amount,
        rap.customer_name,
        r.m_name
    FROM receiveallpayment rap
    LEFT JOIN tbl_regist r ON rap.member_id = r.mem_sid
    GROUP BY rap.member_id, rap.productname, rap.invoice_id, rap.payamount, rap.created_date, rap.customer_name, r.m_name
    ORDER BY rap.member_id, rap.productname, rap.invoice_id, rap.created_date
";
$stmt = $pdo->prepare($payment_query);
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Check if payments are retrieved
if (empty($payments)) {
    error_log("No payments found in receiveallpayment.");
}

// Step 3: Process payments to find when 25% was achieved and calculate due amount
$eligibility_data = [];
foreach ($payments as $payment) {
    $member_id = $payment['member_id'];
    $productname = $payment['productname'];
    $invoice_id = $payment['invoice_id'];
    $payamount = floatval($payment['payamount']);
    $created_date = $payment['created_date'];
    $net_amount = floatval($payment['net_amount']);
    $customer_name = $payment['customer_name'] ?? 'Unknown';
    $m_name = $payment['m_name'] ?? 'Unknown';

    if (!isset($eligibility_data[$member_id][$productname][$invoice_id])) {
        $eligibility_data[$member_id][$productname][$invoice_id] = [
            'total_paid' => 0,
            'net_amount' => $net_amount,
            'due_amount' => $net_amount, // Initialize due_amount as net_amount
            'customer_name' => $customer_name,
            'm_name' => $m_name,
            'payments' => [],
            'eligibility_date' => null,
            'eligibility_month' => null
        ];
    }

    $eligibility_data[$member_id][$productname][$invoice_id]['payments'][] = [
        'payamount' => $payamount,
        'created_date' => $created_date
    ];
    $eligibility_data[$member_id][$productname][$invoice_id]['total_paid'] += $payamount;
    $eligibility_data[$member_id][$productname][$invoice_id]['due_amount'] -= $payamount; // Subtract payment from due_amount

    // Check if 25% threshold is met
    $threshold = $net_amount * 0.25;
    if ($eligibility_data[$member_id][$productname][$invoice_id]['total_paid'] >= $threshold && !$eligibility_data[$member_id][$productname][$invoice_id]['eligibility_date']) {
        $eligibility_data[$member_id][$productname][$invoice_id]['eligibility_date'] = $created_date;
        $eligibility_data[$member_id][$productname][$invoice_id]['eligibility_month'] = date('F Y', strtotime($created_date));
    }
}

// Debugging: Check processed eligibility data
if (empty($eligibility_data)) {
    error_log("No eligibility data processed. Check if payments meet 25% threshold.");
}

// Step 4: Filter and collect eligibility data
$filtered_eligibility_data = [];
foreach ($eligibility_data as $member_id => $products) {
    foreach ($products as $productname => $invoices) {
        foreach ($invoices as $invoice_id => $data) {
            if (!$data['eligibility_date']) {
                continue; // Skip if 25% not achieved
            }
            // Apply filters only if set
            $include = true;
            if ($filter_member_id && $member_id !== $filter_member_id) {
                $include = false;
            }
            if ($filter_year && $filter_month) {
                $eligibility_year = date('Y', strtotime($data['eligibility_date']));
                $eligibility_month = date('m', strtotime($data['eligibility_date']));
                if ($eligibility_year != $filter_year || $eligibility_month != $filter_month) {
                    $include = false;
                }
            }
            if ($include) {
                $filtered_eligibility_data[] = [
                    'member_id' => $member_id,
                    'm_name' => $data['m_name'],
                    'customer_name' => $data['customer_name'],
                    'productname' => $productname,
                    'invoice_id' => $invoice_id,
                    'eligibility_date' => $data['eligibility_date'],
                    'eligibility_month' => $data['eligibility_month'],
                    'net_amount' => $data['net_amount'],
                    'due_amount' => $data['due_amount'],
                    'payments' => $data['payments']
                ];
            }
        }
    }
}

// Step 5: Sort filtered_eligibility_data by member_id, eligibility_month (chronologically), and productname
usort($filtered_eligibility_data, function ($a, $b) {
    // Sort by member_id first
    if ($a['member_id'] !== $b['member_id']) {
        return strcmp($a['member_id'], $b['member_id']);
    }
    // Within same member_id, sort by eligibility_month (chronologically)
    $date_a = DateTime::createFromFormat('F Y', $a['eligibility_month']);
    $date_b = DateTime::createFromFormat('F Y', $b['eligibility_month']);
    if ($date_a !== $date_b) {
        return $date_a <=> $date_b;
    }
    // Within same eligibility_month, sort by productname
    return strcmp($a['productname'], $b['productname']);
});

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
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>




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
        #eligibilityTables thead th {
            position: sticky;
            top: 0;
            background: #f9f9f9;
            z-index: 5;
        }
    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">

                <!-- side panel header -->
                <?php include 'adminheadersidepanel.php'; ?>

                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="">
                            <div class="card">

                                <div class="" style="padding-top: 0px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">

                                        <div class="col-md-12">
                                            <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                                <div class="clr"></div>
                                                <div class="table-section">
                                                    <h3>25% Report</h3>
                                                    <hr>
                                                </div>

                                                <div class="heading">
                                                    Payment Details</div>
                                                <div>
                                                    <br>
                                                    <div id="" style="overflow:auto;width: 99%">


                                                        <div class="box-body">
                                                            <div class="table-section">
                                                                <h3>25% Eligibility Report</h3>
                                                                <hr>
                                                            </div>

                                                            <form method="POST" id="eligibilityReportForm">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Member ID:</label>
                                                                            <select name="member_id" class="form-control">
                                                                                <option value="">All Members</option>
                                                                                <?php foreach ($members as $member): ?>
                                                                                    <option value="<?= $member['member_id'] ?>" <?= $member['member_id'] === $filter_member_id ? 'selected' : '' ?>>
                                                                                        <?= $member['member_id'] . ' / ' . ($member['m_name'] ?: 'Unknown') ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Year:</label>
                                                                            <select name="year" class="form-control">
                                                                                <option value="">All Years</option>
                                                                                <?php for ($y = $current_year - 5; $y <= $current_year + 1; $y++): ?>
                                                                                    <option value="<?= $y ?>" <?= $y == $filter_year ? 'selected' : '' ?>><?= $y ?></option>
                                                                                <?php endfor; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Month:</label>
                                                                            <select name="month" class="form-control">
                                                                                <option value="">All Months</option>
                                                                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                                                                    <option value="<?= sprintf('%02d', $m) ?>" <?= $m == $filter_month ? 'selected' : '' ?>>
                                                                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                                                                    </option>
                                                                                <?php endfor; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12" style="margin-top: 1rem;">
                                                                        <button type="submit" name="filter" class="btn btn-primary">Filter</button>
                                                                    </div>
                                                                </div>
                                                            </form>

                                                            <div class="heading" style="margin-top: 1rem;">25% Eligibility Details</div>
                                                            <div style="max-height:600px; overflow-y:auto; border:1px solid #ddd;">
                                                                <table class="table table-bordered" id="eligibilityTables">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>View Payments</th>
                                                                            <th>Member ID / Name</th>
                                                                            <th>Customer Name</th>
                                                                            <th>Plot Name</th>
                                                                            <th>Eligibility Month</th>
                                                                            <th>Plot Amount</th>
                                                                            <th>Due Amount</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        if (!empty($filtered_eligibility_data)) {
                                                                            $row_index = 0;
                                                                            foreach ($filtered_eligibility_data as $row) {
                                                                                $member_id = $row['member_id'];
                                                                                $m_name = $row['m_name'];
                                                                                $customer_name = $row['customer_name'];
                                                                                $unique_row_id = "row-$row_index";

                                                                                echo "<tr class='main-row'>";
                                                                                echo "<td><button type='button' class='btn btn-primary view-payments' 
                            data-row-id='$unique_row_id' 
                            data-member-id='$member_id' 
                            data-productname='{$row['productname']}' 
                            data-invoice-id='{$row['invoice_id']}'>View</button></td>";
                                                                                echo "<td>" . ($member_id ? ($member_id . ' / ' . ($m_name ?: 'Unknown')) : 'N/A') . "</td>";
                                                                                echo "<td>" . ($customer_name ?: 'N/A') . "</td>";
                                                                                echo "<td>" . ($row['productname'] ?: 'N/A') . "</td>";
                                                                                echo "<td>" . ($row['eligibility_month'] ?: 'N/A') . "</td>";
                                                                                echo "<td>₹" . number_format($row['net_amount'], 2) . "</td>";
                                                                                echo "<td>₹" . number_format($row['due_amount'], 2) . "</td>";
                                                                                echo "</tr>";

                                                                                // Hidden row for payment details
                                                                                echo "<tr class='payment-details-row' id='payment-details-$unique_row_id' style='display:none;'>";
                                                                                echo "<td colspan='6' class='payment-details-content'>";
                                                                                echo "<table class='table table-bordered'>";
                                                                                echo "<thead><tr><th>Amount</th><th>Date</th></tr></thead><tbody>";
                                                                                foreach ($row['payments'] as $payment) {
                                                                                    echo "<tr>";
                                                                                    echo "<td>₹" . number_format($payment['payamount'], 2) . "</td>";
                                                                                    echo "<td>" . $payment['created_date'] . "</td>";
                                                                                    echo "</tr>";
                                                                                }
                                                                                echo "</tbody></table>";
                                                                                echo "</td>";
                                                                                echo "</tr>";

                                                                                $row_index++;
                                                                            }
                                                                        } else {
                                                                            echo "<tr><td colspan='6'>No records found. Payments in receiveallpayment may not meet the 25% eligibility threshold (total payamount >= net_amount * 0.25) for any member_id, productname, invoice_id combination, or member_id may not match mem_sid in tab.</td></tr>";
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                                <div class="alert alert-info mt-3">Report shows members whose plots achieved 25% payment eligibility, grouped by Member ID and sorted by eligibility month and plot name. Member ID includes name. Customer Name from receiveallpayment is included. Use filters to view specific members or months. Expand rows to view payment details.</div>
                                                            </div>
                                                        </div>

                                                        <script>
                                                            $(document).ready(function() {
                                                                $('.view-payments').on('click', function() {
                                                                    var rowId = $(this).data('row-id');
                                                                    var $detailsRow = $('#payment-details-' + rowId);

                                                                    if ($detailsRow.is(':visible')) {
                                                                        $detailsRow.hide();
                                                                    } else {
                                                                        $('.payment-details-row').hide(); // Hide other open rows
                                                                        $detailsRow.show();
                                                                    }
                                                                });
                                                            });
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- footer -->

                    <?php include 'adminfooter.php'; ?>
                </div>


            </div>
            <a href="#" target="_blank">
                <!-- partial -->
            </a>
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


            <!-- <script>
                $(document).ready(function() {
                    $('#customerTable').DataTable({
                        "ordering": false
                    });

                    $('.dropdown-toggle').dropdown();

                });
            </script> -->

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