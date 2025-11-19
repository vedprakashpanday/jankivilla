<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
                            Calculate Closing Report
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
                            // Assume $pdo is already defined for database connection
                            // if (!isset($current_from_date)) {
                            //     $current_from_date = date('Y-m-d', strtotime('-30 days'));
                            // }
                            // if (!isset($current_to_date)) {
                            //     $current_to_date = date('Y-m-d');
                            // }
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

                                    // NEW FUNCTION: Get commission percent based on DESIGNATION instead of amount
                                    function getCommissionPercentByDesignation($designation)
                                    {
                                        if (empty($designation)) {
                                            return 0;
                                        }

                                        // Extract designation code from format like "SE (S.E.)" or "(A.M.O.)"
                                        $short = '';

                                        // Try to extract from parentheses first
                                        if (preg_match('/\(([^)]+)\)/', $designation, $matches)) {
                                            $short = strtoupper(str_replace(['.', ' '], '', $matches[1])); // (A.M.O.) → AMO
                                        }

                                        // If no parentheses, try to get the first word
                                        if (empty($short)) {
                                            $parts = explode(' ', trim($designation));
                                            $short = strtoupper(str_replace('.', '', $parts[0]));
                                        }

                                        // Designation to percentage mapping
                                        $map = [
                                            'SE'  => 3,
                                            'SSE' => 2,
                                            'AMO' => 2,
                                            'MO'  => 1.5,
                                            'AMM' => 1.5,
                                            'MM'  => 1.5,
                                            'CMM' => 1.5,
                                            'AGM' => 1,
                                            'DGM' => 1,
                                            'GM'  => 1,
                                            'MD'  => 1,
                                            'FM'  => 1,
                                        ];

                                        return $map[$short] ?? 0;
                                    }

                                    // Step 1: Get all closed commission history to track what has been counted before
                                    $closed_history_query = "
            SELECT 
                member_id,
                to_date,
                payment_details,
                invoice_id
            FROM commission_history
            WHERE status = 'closed'
            ORDER BY member_id, to_date DESC
        ";
                                    $stmt = $pdo->prepare($closed_history_query);
                                    $stmt->execute();
                                    $closed_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Build tracking maps for already counted amounts and last counted dates
                                    $last_counted_date = [];
                                    $product_already_processed = [];

                                    foreach ($closed_history as $history) {
                                        $member_id = $history['member_id'];
                                        $history_to_date = $history['to_date'];
                                        $payment_details = json_decode($history['payment_details'], true);

                                        if ($payment_details) {
                                            foreach ($payment_details as $productname => $details) {
                                                // Update last counted date for this product
                                                if (
                                                    !isset($last_counted_date[$member_id][$productname]) ||
                                                    $history_to_date > $last_counted_date[$member_id][$productname]
                                                ) {
                                                    $last_counted_date[$member_id][$productname] = $history_to_date;
                                                }

                                                // Mark this product as already processed
                                                $product_already_processed[$member_id][$productname] = true;
                                            }
                                        }
                                    }

                                    // Step 2: Get all payments up to current to_date for eligibility check
                                    $all_payments_query = "
            SELECT 
                rap.member_id,
                rap.productname,
                rap.invoice_id,
                rap.payamount,
                rap.created_date,
                rap.net_amount
            FROM receiveallpayment rap
            INNER JOIN tbl_regist tr ON rap.member_id = tr.mem_sid
            WHERE rap.created_date <= :to_date
            AND rap.payamount > 0
            ORDER BY rap.member_id, rap.productname, rap.invoice_id, rap.created_date
        ";
                                    $stmt = $pdo->prepare($all_payments_query);
                                    $stmt->execute(['to_date' => $to_date]);
                                    $all_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Step 3: Check 25% eligibility per product per invoice
                                    $product_eligibility = [];
                                    $cumulative_tracking = [];
                                    $product_25_achieved_date = [];

                                    foreach ($all_payments as $payment) {
                                        $member_id = $payment['member_id'];
                                        $productname = $payment['productname'];
                                        $invoice_id = $payment['invoice_id'];
                                        $payamount = floatval($payment['payamount']);
                                        $net_amount = floatval($payment['net_amount']);
                                        $created_date = $payment['created_date'];
                                        $threshold_25_percent = $net_amount * 0.25;

                                        $key = $member_id . '|' . $productname . '|' . $invoice_id;

                                        if (!isset($cumulative_tracking[$key])) {
                                            $cumulative_tracking[$key] = [
                                                'member_id' => $member_id,
                                                'productname' => $productname,
                                                'invoice_id' => $invoice_id,
                                                'net_amount' => $net_amount,
                                                'threshold' => $threshold_25_percent,
                                                'cumulative_paid' => 0,
                                                'payments' => [],
                                                'is_eligible' => false,
                                                'first_eligible_date' => null
                                            ];
                                        }

                                        $cumulative_tracking[$key]['cumulative_paid'] += $payamount;
                                        $cumulative_tracking[$key]['payments'][] = $payment;

                                        // Check if 25% threshold is met for the first time
                                        if (
                                            !$cumulative_tracking[$key]['is_eligible'] &&
                                            $cumulative_tracking[$key]['cumulative_paid'] >= $threshold_25_percent
                                        ) {
                                            $cumulative_tracking[$key]['is_eligible'] = true;
                                            $cumulative_tracking[$key]['first_eligible_date'] = $created_date;
                                            $product_eligibility[$member_id][$productname][$invoice_id] = true;

                                            // Track when 25% was achieved for this product
                                            $product_key = $member_id . '|' . $productname;
                                            if (
                                                !isset($product_25_achieved_date[$product_key]) ||
                                                $created_date < $product_25_achieved_date[$product_key]
                                            ) {
                                                $product_25_achieved_date[$product_key] = $created_date;
                                            }
                                        }
                                    }

                                    // Step 4: Calculate eligible amounts for current period
                                    $period_eligible_amounts = [];
                                    $member_payment_details = [];
                                    $member_invoice_ids = [];

                                    foreach ($cumulative_tracking as $key => $tracking_data) {
                                        if ($tracking_data['is_eligible']) {
                                            $member_id = $tracking_data['member_id'];
                                            $productname = $tracking_data['productname'];
                                            $invoice_id = $tracking_data['invoice_id'];

                                            $product_key = $member_id . '|' . $productname;
                                            $product_25_date = $product_25_achieved_date[$product_key];

                                            // Check if this product was already processed in previous closings
                                            $is_already_processed = isset($product_already_processed[$member_id][$productname]);

                                            // Calculate eligible amount for this period
                                            $current_period_eligible = 0;
                                            $eligible_payments_for_period = [];

                                            if (!$is_already_processed) {
                                                // CASE 1: Product achieving 25% for the first time
                                                if ($product_25_date >= $from_date && $product_25_date <= $to_date) {
                                                    foreach ($tracking_data['payments'] as $payment) {
                                                        $payment_amount = floatval($payment['payamount']);
                                                        $current_period_eligible += $payment_amount;
                                                        $eligible_payments_for_period[] = [
                                                            'productname' => $productname,
                                                            'payamount' => $payment_amount,
                                                            'created_date' => $payment['created_date'],
                                                            'invoice_id' => $invoice_id,
                                                            'commission' => 0
                                                        ];
                                                    }
                                                }
                                            } else {
                                                // CASE 2: Product already achieved 25% in previous closings
                                                $last_count_date = isset($last_counted_date[$member_id][$productname])
                                                    ? $last_counted_date[$member_id][$productname] : null;

                                                foreach ($tracking_data['payments'] as $payment) {
                                                    $payment_date = $payment['created_date'];
                                                    $payment_amount = floatval($payment['payamount']);

                                                    $include_payment = false;

                                                    if ($payment_date >= $from_date && $payment_date <= $to_date) {
                                                        if ($last_count_date === null || $payment_date > $last_count_date) {
                                                            $include_payment = true;
                                                        }
                                                    }

                                                    if ($include_payment) {
                                                        $current_period_eligible += $payment_amount;
                                                        $eligible_payments_for_period[] = [
                                                            'productname' => $productname,
                                                            'payamount' => $payment_amount,
                                                            'created_date' => $payment_date,
                                                            'invoice_id' => $invoice_id,
                                                            'commission' => 0
                                                        ];
                                                    }
                                                }
                                            }

                                            // Product achieved 25% BEFORE the current period but have new payments
                                            if (!$is_already_processed && $product_25_date < $from_date) {
                                                foreach ($tracking_data['payments'] as $payment) {
                                                    $payment_date = $payment['created_date'];
                                                    $payment_amount = floatval($payment['payamount']);

                                                    if ($payment_date >= $from_date && $payment_date <= $to_date) {
                                                        $current_period_eligible += $payment_amount;
                                                        $eligible_payments_for_period[] = [
                                                            'productname' => $productname,
                                                            'payamount' => $payment_amount,
                                                            'created_date' => $payment_date,
                                                            'invoice_id' => $invoice_id,
                                                            'commission' => 0
                                                        ];
                                                    }
                                                }
                                            }

                                            // Add to period eligible amounts
                                            if ($current_period_eligible > 0 && !empty($eligible_payments_for_period)) {
                                                if (!isset($period_eligible_amounts[$member_id])) {
                                                    $period_eligible_amounts[$member_id] = 0;
                                                    $member_payment_details[$member_id] = [];
                                                    $member_invoice_ids[$member_id] = [];
                                                }

                                                $period_eligible_amounts[$member_id] += $current_period_eligible;

                                                if (!isset($member_payment_details[$member_id][$productname])) {
                                                    $member_payment_details[$member_id][$productname] = [];
                                                }

                                                $member_payment_details[$member_id][$productname] = array_merge(
                                                    $member_payment_details[$member_id][$productname],
                                                    $eligible_payments_for_period
                                                );

                                                $member_invoice_ids[$member_id][] = $invoice_id;
                                            }
                                        }
                                    }

                                    // Step 5: Get all members from tbl_regist with DESIGNATION
                                    $members_query = "
            SELECT 
                tr.mem_sid as member_id,
                tr.s_name,
                tr.m_name,
                tr.sponsor_id,
                tr.direct_commission_percent,
                tr.designation as designation
            FROM tbl_regist tr
        ";
                                    $stmt = $pdo->prepare($members_query);
                                    $stmt->execute();
                                    $members_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Step 6: Get historical max direct_percent from commission_history
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

                                    // Initialize commission report - Only for members with actual payments in the period
                                    foreach ($members_data as $member) {
                                        $member_id = $member['member_id'];
                                        $direct_amount = isset($period_eligible_amounts[$member_id]) ? $period_eligible_amounts[$member_id] : 0;

                                        // Skip members with no payments in the current period
                                        if ($direct_amount == 0 && !isset($member_payment_details[$member_id])) {
                                            continue;
                                        }

                                        $predefined_percent = $member['direct_commission_percent'] !== null ? floatval($member['direct_commission_percent']) : null;
                                        $member_invoice_list = isset($member_invoice_ids[$member_id]) ? implode(',', array_unique($member_invoice_ids[$member_id])) : '';

                                        // Group payment details by product
                                        $grouped_payment_details = [];
                                        if (isset($member_payment_details[$member_id])) {
                                            $grouped_payment_details = $member_payment_details[$member_id];
                                        }

                                        $commission_report[$member_id] = [
                                            'member_id' => $member_id,
                                            'name' => $member['m_name'],
                                            'sponsor_id' => $member['sponsor_id'] ?: '-',
                                            'invoice_id' => $member_invoice_list,
                                            'direct_amount' => $direct_amount,
                                            'payment_details' => $grouped_payment_details,
                                            'total_group_amount' => 0,
                                            'total_business' => 0,
                                            'direct_commission' => 0,
                                            'level_commission' => 0,
                                            'total_commission' => 0,
                                            'direct_percent' => $predefined_percent,
                                            'designation' => $member['designation'] ?? ''
                                        ];
                                    }

                                    // Step 7: Add members to report if they have downline business in the period
                                    foreach ($members_data as $member) {
                                        $member_id = $member['member_id'];

                                        // Skip if member is already in the report
                                        if (isset($commission_report[$member_id])) {
                                            continue;
                                        }

                                        // Get downline members
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

                                        // Calculate team business
                                        $total_group_amount = 0;
                                        foreach ($downlines as $downline) {
                                            $downline_id = $downline['mem_sid'];
                                            if (isset($period_eligible_amounts[$downline_id])) {
                                                $total_group_amount += $period_eligible_amounts[$downline_id];
                                            }
                                        }

                                        // Add member to report if they have team business
                                        if ($total_group_amount > 0) {
                                            $predefined_percent = $member['direct_commission_percent'] !== null ? floatval($member['direct_commission_percent']) : null;

                                            $commission_report[$member_id] = [
                                                'member_id' => $member_id,
                                                'name' => $member['m_name'],
                                                'sponsor_id' => $member['sponsor_id'] ?: '-',
                                                'invoice_id' => '',
                                                'direct_amount' => 0,
                                                'payment_details' => [],
                                                'total_group_amount' => $total_group_amount,
                                                'total_business' => $total_group_amount,
                                                'direct_commission' => 0,
                                                'level_commission' => 0,
                                                'total_commission' => 0,
                                                'direct_percent' => $predefined_percent,
                                                'designation' => $member['designation'] ?? ''
                                            ];
                                        }
                                    }

                                    // Step 8: Calculate commissions for all members in the report
                                    foreach ($commission_report as $member_id => &$member) {
                                        // Get downline members
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

                                        // Calculate team business if not already set
                                        if ($member['total_group_amount'] == 0) {
                                            foreach ($downlines as $downline) {
                                                $downline_id = $downline['mem_sid'];
                                                if (isset($period_eligible_amounts[$downline_id])) {
                                                    $member['total_group_amount'] += $period_eligible_amounts[$downline_id];
                                                }
                                            }
                                        }

                                        $member['total_business'] = $member['direct_amount'] + $member['total_group_amount'];

                                        // MODIFIED: Set direct percent based on DESIGNATION instead of amount slab
                                        $designation_percent = getCommissionPercentByDesignation($member['designation']);
                                        $historical_percent = isset($historical_max_percent[$member_id]) ? $historical_max_percent[$member_id] : 0;

                                        if ($member['direct_percent'] === null) {
                                            $member['direct_percent'] = max($designation_percent, $historical_percent);
                                        } else {
                                            $member['direct_percent'] = max($member['direct_percent'], $historical_percent, $designation_percent);
                                        }

                                        // Calculate commission for each payment and update payment details
                                        foreach ($member['payment_details'] as $productname => &$details) {
                                            foreach ($details as &$detail) {
                                                $detail['commission'] = ($detail['payamount'] * $member['direct_percent']) / 100;
                                            }
                                            unset($detail);
                                        }
                                        unset($details);

                                        // Calculate direct commission based on member's self business
                                        $member['direct_commission'] = ($member['direct_amount'] * $member['direct_percent']) / 100;

                                        // Level commission calculation will be done in a separate loop after all percentages are set
                                    }
                                    unset($member);

                                    // Step 8.5: Calculate level commissions for all members (after all direct_percent values are finalized)
                                    foreach ($commission_report as $member_id => $member) {
                                        // Only calculate level commission if member has self business in this period
                                        if (isset($period_eligible_amounts[$member_id]) && $period_eligible_amounts[$member_id] > 0) {
                                            $business_amount = $period_eligible_amounts[$member_id];
                                            $current_percent = $member['direct_percent'];
                                            $current_member_id = $member_id;
                                            $level = 0;

                                            // Go up the sponsor chain (10 levels)
                                            while ($current_member_id && $level < 10) {
                                                // Get the direct sponsor
                                                $upline_query = "
                        SELECT sponsor_id 
                        FROM tbl_regist 
                        WHERE mem_sid = :current_member_id
                    ";
                                                $stmt = $pdo->prepare($upline_query);
                                                $stmt->execute(['current_member_id' => $current_member_id]);
                                                $upline_data = $stmt->fetch(PDO::FETCH_ASSOC);

                                                if (!$upline_data || !$upline_data['sponsor_id']) break;

                                                $upline_id = $upline_data['sponsor_id'];

                                                // Check if upline exists in commission report
                                                if (!isset($commission_report[$upline_id])) break;

                                                $upline_percent = $commission_report[$upline_id]['direct_percent'];
                                                $diff_percent = $upline_percent - $current_percent;

                                                // If upline has higher percentage, give them the differential
                                                if ($diff_percent > 0 && $business_amount > 0) {
                                                    $level_commission = ($business_amount * $diff_percent) / 100;
                                                    $commission_report[$upline_id]['level_commission'] += $level_commission;
                                                }

                                                // Move up to next level
                                                $current_percent = $upline_percent;
                                                $current_member_id = $upline_id;
                                                $level++;
                                            }
                                        }
                                    }
                                    unset($member);

                                    // Step 9: Calculate total commission
                                    foreach ($commission_report as $member_id => &$member) {
                                        $member['total_commission'] = $member['direct_commission'] + $member['level_commission'];
                                    }
                                    unset($member);

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
                <th>Designation</th>
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
                                        echo "<td>{$report['designation']}</td>";
                                        echo "<td>{$report['sponsor_id']}</td>";

                                        $self_business_details = empty($report['payment_details']) ? '0' : '₹' . number_format($report['direct_amount'], 2);
                                        if (!empty($report['payment_details'])) {
                                            $self_business_details .= "<br><ul>";
                                            foreach ($report['payment_details'] as $productname => $details) {
                                                $self_business_details .= "<li>$productname:<ul>";
                                                foreach ($details as $detail) {
                                                    if (isset($detail['payamount'])) {
                                                        $self_business_details .= "<li>Amount: ₹" . number_format($detail['payamount'], 2) .
                                                            ", Date: " . date("d-m-Y", strtotime($detail['created_date'])) .
                                                            ", Commission: ₹" . number_format($detail['commission'], 2) . "</li>";
                                                    }
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
                                    echo "<div class='alert alert-info mt-3'>Commission calculations:<br>
            • Commission percentage is based on member DESIGNATION (not business amount)<br>
            • Products achieving 25% for the first time within the selected date range: ALL payments from beginning are included<br>
            • Products that achieved 25% before the current period: Only NEW payments within the selected date range are included<br>
            • Products already processed in previous closings: Only payments after last closing date AND within current date range are included</div>";
                                    echo "</div>";
                                }

                                // Handle save commission
                                if (isset($_POST['save_commission']) && isset($_SESSION['commission_report'])) {
                                    try {
                                        $save_query = "
                INSERT INTO commission_history (
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
                                            if (!empty($report['payment_details'])) {
                                                foreach ($report['payment_details'] as $productname => $details) {
                                                    $payment_json[$productname] = [];
                                                    foreach ($details as $detail) {
                                                        if (isset($detail['payamount']) && isset($detail['created_date'])) {
                                                            $payment_json[$productname][] = [
                                                                'amount' => $detail['payamount'],
                                                                'date' => date('d-m-Y', strtotime($detail['created_date'])),
                                                                'commission' => $detail['commission']
                                                            ];
                                                        }
                                                    }
                                                    if (empty($payment_json[$productname])) {
                                                        unset($payment_json[$productname]);
                                                    }
                                                }
                                            }

                                            $total_commission_to_save = $report['direct_commission'] + $report['level_commission'];

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
                                                ':total_commission' => $total_commission_to_save,
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