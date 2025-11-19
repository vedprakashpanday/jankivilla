<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

if (isset($_POST['calculate'])) {
    $current_from_date =  $_POST['from_date'];
    $current_to_date = $_POST['to_date'];
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
        #salesTable {
            min-width: 1200px;
            /* adjust this value as per your need */
        }

        #salesTable thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
        }
    </style>


</head>

<body class="hold-transition skin-blue sidebar-mini">



    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include 'adminheadersidepanel.php'; ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper" style="background:unset!important;">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <h2 class="">
                            Calculate New Closing Report
                            <small></small>
                        </h2>

                    </section>

                    <!-- Main content -->
                    <section class="container" style="padding-left:unset; padding-right:unset;">

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <!-- <h3 class="box-title">Calculate Closing Report</h3> -->
                            </div>


                            <?php
                            // $current_from_date = date('Y-m-d', strtotime('-30 days'));
                            // $current_to_date = date('Y-m-d');
                            ?>

                            <?php
                            // Assume $pdo is already defined for database connection
                            // $current_from_date = date('Y-m-d', strtotime('-30 days'));
                            // $current_to_date = date('Y-m-d');

                            if (!isset($current_from_date)) {
                                $current_from_date = date('Y-m-d', strtotime('-30 days'));
                            }
                            if (!isset($current_to_date)) {
                                $current_to_date = date('Y-m-d');
                            }
                            ?>

                            <div class="box-body">
                                <form method="POST" id="salesReportForm">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>From Date:</label>
                                                <input type="date" name="from_date" value="<?= $current_from_date; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>To Date:</label>
                                                <input type="date" name="to_date" value="<?= $current_to_date; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4" style="margin-top: 1.9rem;">
                                            <div class="form-group">
                                                <label></label>
                                                <button type="submit" name="calculate" class="btn btn-primary">Calculate</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <?php
                                if (isset($_POST['calculate'])) {
                                    $from_date = $_POST['from_date'];
                                    $to_date = $_POST['to_date'];

                                    function getCommissionPercent($amount)
                                    {
                                        $amount = floatval($amount);
                                        if ($amount <= 300000) return 6;
                                        if ($amount <= 900000) return 7;
                                        if ($amount <= 2500000) return 8;
                                        if ($amount <= 5000000) return 9;
                                        if ($amount <= 12500000) return 10;
                                        if ($amount <= 30000000) return 11;
                                        if ($amount <= 75000000) return 12;
                                        if ($amount <= 250000000) return 13;
                                        if ($amount <= 750000000) return 14;
                                        return 15;
                                    }

                                    // Step 1a: Get all commission periods from commission_history to check eligibility per period
                                    $closed_periods_query = "
            SELECT 
                ch.member_id,
                ch.to_date,
                ch.direct_amount,
                ch.invoice_id
            FROM commission_history ch
            WHERE ch.status = 'closed'
            ORDER BY ch.member_id, ch.to_date
        ";
                                    $stmt = $pdo->prepare($closed_periods_query);
                                    $stmt->execute();
                                    $closed_periods = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Step 1b: Check 25% eligibility for each product up to to_date, considering all payments
                                    $historical_payment_query = "
            SELECT 
                rap.member_id,
                rap.productname,
                rap.invoice_id,
                SUM(rap.payamount) as total_paid,
                MAX(rap.net_amount) as net_amount
            FROM receiveallpayment rap
            INNER JOIN tbl_regist tr ON rap.member_id = tr.mem_sid
            WHERE rap.created_date <= :to_date
            GROUP BY rap.member_id, rap.productname, rap.invoice_id
        ";
                                    $stmt = $pdo->prepare($historical_payment_query);
                                    $stmt->execute(['to_date' => $to_date]);
                                    $historical_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Log eligible members, products, and invoices
                                    $eligible_members_products = [];
                                    $eligible_members = [];
                                    $eligible_invoices = [];
                                    foreach ($historical_payments as $payment) {
                                        $member_id = $payment['member_id'];
                                        $productname = $payment['productname'];
                                        $invoice_id = $payment['invoice_id'];
                                        $total_paid = floatval($payment['total_paid']);
                                        $net_amount = floatval($payment['net_amount']);
                                        $threshold = $net_amount * 0.25;

                                        if ($total_paid >= $threshold) {
                                            $eligible_members_products[$member_id][$productname] = true;
                                            $eligible_members[$member_id] = true;
                                            $eligible_invoices[$member_id][$invoice_id] = true;
                                        }
                                    }
                                    $eligible_members = array_keys($eligible_members);

                                    // Step 1c: Determine the start date for each member and product based on eligibility in closed periods
                                    $member_product_start_dates = [];
                                    foreach ($closed_periods as $period) {
                                        $member_id = $period['member_id'];
                                        $to_date_closed = $period['to_date'];
                                        $invoice_ids = explode(',', $period['invoice_id']);

                                        foreach ($invoice_ids as $invoice_id) {
                                            $closed_payment_query = "
                    SELECT 
                        rap.productname,
                        SUM(rap.payamount) as total_paid,
                        MAX(rap.net_amount) as net_amount
                    FROM receiveallpayment rap
                    WHERE rap.member_id = :member_id
                    AND rap.invoice_id = :invoice_id
                    AND rap.created_date <= :to_date_closed
                    GROUP BY rap.productname
                ";
                                            $stmt = $pdo->prepare($closed_payment_query);
                                            $stmt->execute([
                                                'member_id' => $member_id,
                                                'invoice_id' => $invoice_id,
                                                'to_date_closed' => $to_date_closed
                                            ]);
                                            $closed_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($closed_payments as $payment) {
                                                $productname = $payment['productname'];
                                                $total_paid = floatval($payment['total_paid']);
                                                $net_amount = floatval($payment['net_amount']);
                                                $threshold = $net_amount * 0.25;

                                                if ($total_paid >= $threshold) {
                                                    $member_product_start_dates[$member_id][$productname] = max(
                                                        isset($member_product_start_dates[$member_id][$productname]) ? $member_product_start_dates[$member_id][$productname] : '1970-01-01',
                                                        $to_date_closed
                                                    );
                                                }
                                            }
                                        }
                                    }

                                    // Step 1d: Get payments up to to_date for eligible members with product and date details
                                    $period_payment_query = "
            SELECT 
                rap.member_id,
                rap.productname,
                rap.invoice_id,
                rap.payamount,
                rap.created_date,
                MAX(rap.net_amount) as net_amount
            FROM receiveallpayment rap
            WHERE rap.created_date <= :to_date
            GROUP BY rap.member_id, rap.productname, rap.invoice_id, rap.payamount, rap.created_date
        ";
                                    $stmt = $pdo->prepare($period_payment_query);
                                    $stmt->execute(['to_date' => $to_date]);
                                    $period_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    $period_direct_amounts = [];
                                    $invoice_ids = [];
                                    $member_payment_details = [];
                                    foreach ($period_payments as $payment) {
                                        $member_id = $payment['member_id'];
                                        $productname = $payment['productname'];
                                        $invoice_id = $payment['invoice_id'];
                                        $payamount = floatval($payment['payamount']);
                                        $created_date = $payment['created_date'];
                                        $net_amount = floatval($payment['net_amount']);
                                        $threshold = $net_amount * 0.25;

                                        if (in_array($member_id, $eligible_members) && $payamount > 0 && isset($eligible_members_products[$member_id][$productname]) && isset($eligible_invoices[$member_id][$invoice_id])) {
                                            $start_date = isset($member_product_start_dates[$member_id][$productname]) ? $member_product_start_dates[$member_id][$productname] : null;
                                            $include_payment = true;

                                            if ($start_date && $created_date <= $start_date) {
                                                $include_payment = false;
                                            }

                                            if ($include_payment) {
                                                if (!isset($period_direct_amounts[$member_id])) {
                                                    $period_direct_amounts[$member_id] = 0;
                                                    $invoice_ids[$member_id] = [];
                                                    $member_payment_details[$member_id] = [];
                                                }
                                                $period_direct_amounts[$member_id] += $payamount;
                                                $invoice_ids[$member_id][] = $invoice_id;
                                                $member_payment_details[$member_id][] = [
                                                    'productname' => $productname,
                                                    'payamount' => $payamount,
                                                    'created_date' => $created_date,
                                                    'commission' => 0 // Placeholder, will be calculated later
                                                ];
                                            }
                                        }
                                    }

                                    // Step 2: Get all members from tbl_regist
                                    $members_query = "
            SELECT 
                tr.mem_sid as member_id,
                tr.s_name,
                tr.m_name,
                tr.sponsor_id,
                tr.direct_commission_percent
            FROM tbl_regist tr
        ";
                                    $stmt = $pdo->prepare($members_query);
                                    $stmt->execute();
                                    $members_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Step 3: Get historical max direct_percent from commission_history
                                    $historical_percent_query = "
            SELECT 
                member_id,
                MAX(direct_percent) as max_direct_percent
            FROM commission_history
            GROUP BY member_id
        ";
                                    $stmt = $pdo->prepare($historical_percent_query);
                                    $stmt->execute();
                                    $historical_percentages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    $historical_max_percent = [];
                                    foreach ($historical_percentages as $record) {
                                        $historical_max_percent[$record['member_id']] = floatval($record['max_direct_percent']);
                                    }

                                    $commission_report = [];

                                    // Initialize report
                                    foreach ($members_data as $member) {
                                        $member_id = $member['member_id'];
                                        $direct_amount = isset($period_direct_amounts[$member_id]) ? $period_direct_amounts[$member_id] : 0;
                                        $predefined_percent = $member['direct_commission_percent'] !== null ? floatval($member['direct_commission_percent']) : null;
                                        $member_invoice_ids = isset($invoice_ids[$member_id]) ? implode(',', array_unique($invoice_ids[$member_id])) : '';

                                        // Group payment details by product
                                        $grouped_payment_details = [];
                                        if (isset($member_payment_details[$member_id])) {
                                            foreach ($member_payment_details[$member_id] as $detail) {
                                                $productname = $detail['productname'];
                                                if (!isset($grouped_payment_details[$productname])) {
                                                    $grouped_payment_details[$productname] = [];
                                                }
                                                $grouped_payment_details[$productname][] = $detail;
                                            }
                                        }

                                        $commission_report[$member_id] = [
                                            'member_id' => $member_id,
                                            'name' => $member['m_name'],
                                            'sponsor_id' => $member['sponsor_id'] ?: '-',
                                            'invoice_id' => $member_invoice_ids,
                                            'direct_amount' => $direct_amount,
                                            'payment_details' => $grouped_payment_details,
                                            'total_group_amount' => 0,
                                            'total_business' => 0,
                                            'direct_commission' => 0,
                                            'level_commission' => 0,
                                            'total_commission' => 0,
                                            'direct_percent' => $predefined_percent
                                        ];
                                    }

                                    // Calculate total group amounts (team business) and commissions
                                    foreach ($commission_report as $member_id => &$member) {
                                        $downline_query = "
                WITH RECURSIVE downline AS (
                    SELECT mem_sid, sponsor_id, 0 as level
                    FROM tbl_regist
                    WHERE sponsor_id = :member_id
                    UNION ALL
                    SELECT r.mem_sid, r.sponsor_id, d.level + 1
                    FROM tbl_regist r
                    INNER JOIN downline d ON r.sponsor_id = d.mem_sid
                )
                SELECT mem_sid FROM downline
            ";
                                        $stmt = $pdo->prepare($downline_query);
                                        $stmt->execute(['member_id' => $member_id]);
                                        $downlines = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($downlines as $downline) {
                                            $downline_id = $downline['mem_sid'];
                                            if (isset($period_direct_amounts[$downline_id])) {
                                                $member['total_group_amount'] += $period_direct_amounts[$downline_id];
                                            }
                                        }

                                        $member['total_business'] = $member['direct_amount'] + $member['total_group_amount'];

                                        // Set direct percent
                                        $slab_percent = getCommissionPercent($member['total_business']);
                                        $historical_percent = isset($historical_max_percent[$member_id]) ? $historical_max_percent[$member_id] : 0;
                                        if ($member['direct_percent'] === null) {
                                            $member['direct_percent'] = max($slab_percent, $historical_percent);
                                        } else {
                                            $member['direct_percent'] = max($member['direct_percent'], $historical_percent, $slab_percent);
                                        }

                                        // Calculate commission for each payment
                                        foreach ($member['payment_details'] as $productname => &$details) {
                                            $total_product_commission = 0;
                                            foreach ($details as &$detail) {
                                                $detail['commission'] = ($detail['payamount'] * $member['direct_percent']) / 100;
                                                $total_product_commission += $detail['commission'];
                                            }
                                            $details['total_commission'] = $total_product_commission;
                                        }

                                        // Direct commission
                                        $member['direct_commission'] = 0;
                                        foreach ($member['payment_details'] as $details) {
                                            $member['direct_commission'] += $details['total_commission'];
                                        }

                                        // Level commission (10 levels up)
                                        if (isset($period_direct_amounts[$member_id]) && $period_direct_amounts[$member_id] > 0) {
                                            $business_amount = $period_direct_amounts[$member_id];
                                            $current_percent = $member['direct_percent'];
                                            $current_member_id = $member_id;
                                            $level = 0;

                                            while ($current_member_id && $level < 10) {
                                                $upline_query = "
                        SELECT mem_sid, direct_commission_percent
                        FROM tbl_regist
                        WHERE mem_sid = (SELECT sponsor_id FROM tbl_regist WHERE mem_sid = :current_member_id)
                    ";
                                                $stmt = $pdo->prepare($upline_query);
                                                $stmt->execute(['current_member_id' => $current_member_id]);
                                                $upline = $stmt->fetch(PDO::FETCH_ASSOC);

                                                if (!$upline) break;

                                                $upline_id = $upline['mem_sid'];
                                                if (!isset($commission_report[$upline_id])) break;

                                                $upline_percent = $commission_report[$upline_id]['direct_percent'];
                                                $diff_percent = $upline_percent - $current_percent;

                                                if ($diff_percent > 0 && $business_amount > 0) {
                                                    $level_commission = ($business_amount * $diff_percent) / 100;
                                                    $commission_report[$upline_id]['level_commission'] += $level_commission;
                                                    $commission_report[$upline_id]['total_commission'] += $level_commission;
                                                }

                                                $current_percent = $upline_percent;
                                                $current_member_id = $upline_id;
                                                $level++;
                                            }
                                        }

                                        $member['total_commission'] = $member['direct_commission'] + $member['level_commission'];
                                    }

                                    // Build hierarchy for display
                                    $sorted_report = [];
                                    $processed = [];

                                    function buildHierarchy($report, &$sorted, &$processed)
                                    {
                                        if (isset($report['HHD000001']) && !isset($processed['HHD000001'])) {
                                            $processed['HHD000001'] = true;
                                            $sorted[] = $report['HHD000001'];
                                        }

                                        foreach ($report as $member) {
                                            $member_id = $member['member_id'];
                                            if (!isset($processed[$member_id])) {
                                                $processed[$member_id] = true;
                                                $sorted[] = $member;

                                                foreach ($report as $downline) {
                                                    if ($downline['sponsor_id'] === $member_id && !isset($processed[$downline['member_id']])) {
                                                        $processed[$downline['member_id']] = true;
                                                        $sorted[] = $downline;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    buildHierarchy($commission_report, $sorted_report, $processed);

                                    // Store report in session
                                    $_SESSION['commission_report'] = $sorted_report;
                                    $_SESSION['from_date'] = $from_date;
                                    $_SESSION['to_date'] = $to_date;

                                    // Display report
                                    echo "<div class='table-responsive' style='overflow-x: scroll; height: 500px;'>";
                                    echo "<table class='table table-bordered' id='salesTable'>";
                                    echo "<thead>
            <tr>
                <th>Member ID</th>
                <th>Member Name</th>
                <th>Sponsor ID</th>
                <th>Self Business (Product, Amount, Date, Commission)</th>
                <th>Team Business</th>
                <th>Total Business</th>
                <th>Direct Commission %</th>
                <th>Direct Commission</th>
                <th>Level Commission</th>
                <th>Total Commission</th>
            </tr>
        </thead><tbody>";

                                    foreach ($sorted_report as $report) {
                                        echo "<tr>";
                                        echo "<td>{$report['member_id']}</td>";
                                        echo "<td>{$report['name']}</td>";
                                        echo "<td>{$report['sponsor_id']}</td>";
                                        // Display self business with total amount and grouped details
                                        $self_business_details = empty($report['payment_details']) ? '0' : '₹' . number_format($report['direct_amount'], 2);
                                        if (!empty($report['payment_details'])) {
                                            $self_business_details .= "<br><ul>";
                                            foreach ($report['payment_details'] as $productname => $details) {
                                                $self_business_details .= "<li>$productname:<ul>";
                                                foreach ($details as $detail) {
                                                    $self_business_details .= "<li>Amount: ₹" . number_format($detail['payamount'], 2) .
                                                        ", Date: " . date('d-m-Y', strtotime($detail['created_date'])) .
                                                        ", Commission: ₹" . number_format($detail['commission'], 2) . "</li>";
                                                }
                                                $self_business_details .= "</ul></li>";
                                            }
                                            $self_business_details .= "</ul>";
                                        }
                                        echo "<td>$self_business_details</td>";
                                        echo "<td>₹" . number_format($report['total_group_amount'], 2) . "</td>";
                                        echo "<td>₹" . number_format($report['total_business'], 2) . "</td>";
                                        echo "<td>" . number_format($report['direct_percent'], 2) . "%</td>";
                                        echo "<td>₹" . number_format($report['direct_commission'], 2) . "</td>";
                                        echo "<td>₹" . number_format($report['level_commission'], 2) . "</td>";
                                        echo "<td>₹" . number_format($report['total_commission'], 2) . "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody></table>";
                                    echo "<form method='POST' action='' style='margin:1rem;'>";
                                    echo "<button type='submit' class='btn btn-primary mt-2' name='save_commission'>Save Commission</button>";
                                    echo "</form>";
                                    echo "<div class='alert alert-info mt-3'>Calculations include 25% payment eligibility per product, considering payments only after the last closed period where 25% was achieved for that product. Includes 10-level upline commissions, historical percentage protection, and total business (self + team). Self business shows total amount followed by payments grouped by product with amount, date, and commission.</div>";
                                    echo "</div>";
                                }

                                // Handle save commission
                                if (isset($_POST['save_commission']) && isset($_SESSION['commission_report'])) {
                                    try {
                                        $save_query = "
                INSERT INTO tbl_temp_commission_history (
                    invoice_id,
                    member_id, 
                    member_name, 
                    sponsor_id, 
                    direct_amount, 
                    total_group_amount, 
                    total_business,
                    direct_percent, 
                    direct_commission, 
                    level_commission, 
                    total_commission, 
                    from_date,
                    to_date,
                    status,
                    payment_details
                ) VALUES (
                    :invoice_id,
                    :member_id, 
                    :member_name, 
                    :sponsor_id, 
                    :direct_amount, 
                    :total_group_amount, 
                    :total_business,
                    :direct_percent, 
                    :direct_commission, 
                    :level_commission, 
                    :total_commission, 
                    :from_date,
                    :to_date,
                    'closed',
                    :payment_details
                )
            ";

                                        $stmt = $pdo->prepare($save_query);
                                        $sorted_report = $_SESSION['commission_report'];
                                        $from_date = $_SESSION['from_date'];
                                        $to_date = $_SESSION['to_date'];

                                        foreach ($sorted_report as $report) {
                                            // Prepare payment_details JSON
                                            $payment_json = [];
                                            foreach ($report['payment_details'] as $productname => $details) {
                                                $payment_json[$productname] = [];
                                                foreach ($details as $detail) {
                                                    $payment_json[$productname][] = [
                                                        'amount' => $detail['payamount'],
                                                        'date' => date('d-m-Y', strtotime($detail['created_date'])),
                                                        'commission' => $detail['commission']
                                                    ];
                                                }
                                            }

                                            $stmt->execute([
                                                ':invoice_id' => $report['invoice_id'],
                                                ':member_id' => $report['member_id'],
                                                ':member_name' => $report['name'],
                                                ':sponsor_id' => $report['sponsor_id'],
                                                ':direct_amount' => $report['direct_amount'],
                                                ':total_group_amount' => $report['total_group_amount'],
                                                ':total_business' => $report['total_business'],
                                                ':direct_percent' => $report['direct_percent'],
                                                ':direct_commission' => $report['direct_commission'],
                                                ':level_commission' => $report['level_commission'],
                                                ':total_commission' => $report['total_commission'],
                                                ':from_date' => $from_date,
                                                ':to_date' => $to_date,
                                                ':payment_details' => json_encode($payment_json)
                                            ]);
                                        }

                                        echo "<div class='alert alert-success'>Commission data saved successfully for period " . $from_date . " to " . $to_date . "!</div>";
                                        unset($_SESSION['commission_report']);
                                        unset($_SESSION['from_date']);
                                        unset($_SESSION['to_date']);
                                    } catch (PDOException $e) {
                                        echo "<div class='alert alert-danger'>Error saving commission data: " . $e->getMessage() . "</div>";
                                    }
                                }
                                ?>
                            </div>
                            <!--box body end here-->

                        </div>
                        <p class="fw-bold text-black" style="margin:11px!important;font-weight:bold;">NOTE:&nbsp;<span class="text-danger fw-bold" style="font-weight:bold; font-size:16px;">No members will be displayed below 25%.</span></p>

                </div>
                <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

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


        <script>
            $(document).ready(function() {
                $('#salesTable').DataTable({
                    "ordering": false
                });

                $('.dropdown-toggle').dropdown();

            });
        </script>



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