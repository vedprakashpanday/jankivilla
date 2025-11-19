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
if (!isset($_SESSION['mem_id'])) {
    header('location:../../login.php');
    exit();
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
    <!-- <link href="assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link href="../assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/themify-icons.css"> -->
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


</head>

<body>


    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "associate-headersidepanel.php"; ?>
                <div class="main-panel p-1">


                    <h3 class="p-4">Team Income</h3>
                    <div style="width:97%;">
                        <?php
                        // Get the logged-in member's ID from session
                        $logged_in_member_id = $_SESSION['sponsor_id'] ?? 'HHD000001';

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

                        // Step 1a: Check 25% eligibility
                        $historical_payment_query = "
        SELECT 
            rap.member_id,
            rap.productname,
            SUM(rap.payamount) as total_paid,
            MAX(rap.net_amount) as net_amount
        FROM receiveallpayment rap
        GROUP BY rap.member_id, rap.productname
    ";
                        $stmt = $pdo->prepare($historical_payment_query);
                        $stmt->execute();
                        $historical_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Determine eligible members
                        $eligible_members_products = [];
                        foreach ($historical_payments as $payment) {
                            $member_id = $payment['member_id'];
                            $productname = $payment['productname'];
                            $total_paid = floatval($payment['total_paid']);
                            $net_amount = floatval($payment['net_amount']);
                            $threshold = $net_amount * 0.25;

                            if ($total_paid >= $threshold) {
                                $eligible_members_products[$member_id][$productname] = true;
                            }
                        }

                        // Step 1b: Get all payments
                        $payment_query = "
        SELECT 
            rap.member_id,
            rap.productname,
            rap.payamount as total_paid,
            rap.created_date,
            rap.net_amount
        FROM receiveallpayment rap
    ";
                        $stmt = $pdo->prepare($payment_query);
                        $stmt->execute();
                        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Map direct amounts and active members
                        $direct_amounts = [];
                        $active_members = [];
                        $member_payments = [];
                        foreach ($payments as $payment) {
                            $member_id = $payment['member_id'];
                            $productname = $payment['productname'];
                            $total_paid = floatval($payment['total_paid']);
                            $created_date = $payment['created_date'];
                            $net_amount = floatval($payment['net_amount']);

                            if ($total_paid > 0 && !empty($eligible_members_products[$member_id][$productname])) {
                                $direct_amounts[$member_id] = ($direct_amounts[$member_id] ?? 0) + $total_paid;
                                $active_members[$member_id] = true;
                                $member_payments[$member_id][] = [
                                    'productname' => $productname,
                                    'payamount' => $total_paid,
                                    'created_date' => $created_date,
                                    'net_amount' => $net_amount
                                ];
                            }
                        }

                        // Step 2: Get members and downlines
                        $members_query = "
        SELECT 
            tr.mem_sid as member_id,
            COALESCE(tr.m_name, 'Unknown') as m_name,
            COALESCE(tr.sponsor_id, '') as sponsor_id,
            tr.direct_commission_percent
        FROM tbl_regist tr
        WHERE tr.mem_sid = :logged_in_member_id
        OR EXISTS (
            WITH RECURSIVE downline AS (
                SELECT mem_sid, sponsor_id, 0 as level FROM tbl_regist WHERE sponsor_id = :logged_in_member_id
                UNION ALL
                SELECT r.mem_sid, r.sponsor_id, d.level + 1 FROM tbl_regist r
                INNER JOIN downline d ON r.sponsor_id = d.mem_sid
                WHERE d.level < 9
            )
            SELECT 1 FROM downline WHERE downline.mem_sid = tr.mem_sid
        )
    ";
                        $stmt = $pdo->prepare($members_query);
                        $stmt->execute(['logged_in_member_id' => $logged_in_member_id]);
                        $members_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $commission_report = [];
                        $all_downline_level_commission = [];

                        // Initialize report
                        foreach ($members_data as $member) {
                            $member_id = $member['member_id'];
                            $direct_amount = $direct_amounts[$member_id] ?? 0;
                            $predefined_percent = $member['direct_commission_percent'] !== null ? floatval($member['direct_commission_percent']) : null;

                            $commission_report[$member_id] = [
                                'member_id' => $member_id,
                                'name' => $member['m_name'],
                                'sponsor_id' => $member['sponsor_id'],
                                'direct_amount' => $direct_amount,
                                'total_group_amount' => 0,
                                'direct_commission' => 0,
                                'level_commission' => 0,
                                'total_commission' => 0,
                                'direct_percent' => $predefined_percent,
                                'level' => 0
                            ];
                        }

                        // Calculate total group amounts and assign levels
                        $downline_query = "
        WITH RECURSIVE downline AS (
            SELECT mem_sid, sponsor_id, 0 as level
            FROM tbl_regist
            WHERE sponsor_id = :member_id
            UNION ALL
            SELECT r.mem_sid, r.sponsor_id, d.level + 1
            FROM tbl_regist r
            INNER JOIN downline d ON r.sponsor_id = d.mem_sid
            WHERE d.level < 9
        )
        SELECT mem_sid, level FROM downline
    ";

                        foreach ($commission_report as $member_id => &$member) {
                            $stmt = $pdo->prepare($downline_query);
                            $stmt->execute(['member_id' => $member_id]);
                            $downlines = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($downlines as $downline) {
                                $downline_id = $downline['mem_sid'];
                                if (isset($direct_amounts[$downline_id])) {
                                    $member['total_group_amount'] += $direct_amounts[$downline_id];
                                }
                                if (isset($commission_report[$downline_id])) {
                                    $commission_report[$downline_id]['level'] = $downline['level'];
                                }
                            }

                            // Set direct percent if not predefined
                            if ($member['direct_percent'] === null) {
                                $member['direct_percent'] = getCommissionPercent($member['total_group_amount']);
                            }
                        }
                        unset($member);

                        // Calculate commissions
                        foreach ($commission_report as $member_id => &$member) {
                            $group_percent = floatval($member['direct_percent']);

                            // Direct commission
                            if (isset($active_members[$member_id]) && $member['direct_amount'] > 0) {
                                $member['direct_commission'] = ($member['direct_amount'] * $group_percent) / 100;
                            }

                            // Level commission
                            $stmt = $pdo->prepare($downline_query);
                            $stmt->execute(['member_id' => $member_id]);
                            $downlines = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($downlines as $downline) {
                                $downline_id = $downline['mem_sid'];
                                if (isset($commission_report[$downline_id])) {
                                    $downline_data = $commission_report[$downline_id];
                                    $downline_percent = isset($active_members[$downline_id]) ? floatval($downline_data['direct_percent']) : 0;
                                    $diff_percent = $group_percent - $downline_percent;

                                    if ($diff_percent > 0 && isset($direct_amounts[$downline_id]) && $direct_amounts[$downline_id] > 0) {
                                        $level_commission = ($direct_amounts[$downline_id] * $diff_percent) / 100;
                                        $member['level_commission'] += $level_commission;

                                        // Store breakdown for logged-in member
                                        if ($member_id == $logged_in_member_id && isset($member_payments[$downline_id])) {
                                            foreach ($member_payments[$downline_id] as $payment) {
                                                $all_downline_level_commission[] = [
                                                    'member_id' => $downline_id,
                                                    'name' => $downline_data['name'],
                                                    'sponsor_id' => $downline_data['sponsor_id'],
                                                    'self_amount' => $payment['payamount'],
                                                    'direct_percent' => $downline_percent,
                                                    'diff_percent' => $diff_percent,
                                                    'level_commission' => ($payment['payamount'] * $diff_percent) / 100,
                                                    'productname' => $payment['productname'],
                                                    'created_date' => $payment['created_date'],
                                                    'net_amount' => $payment['net_amount']
                                                ];
                                            }
                                        }
                                    }
                                }
                            }

                            $member['total_commission'] = $member['direct_commission'] + $member['level_commission'];
                        }
                        unset($member);

                        // Extract logged-in member's data
                        $logged_in_member = $commission_report[$logged_in_member_id] ?? [
                            'member_id' => $logged_in_member_id,
                            'name' => 'Unknown',
                            'sponsor_id' => '',
                            'direct_amount' => 0,
                            'total_group_amount' => 0,
                            'direct_commission' => 0,
                            'level_commission' => 0,
                            'total_commission' => 0,
                            'direct_percent' => 0,
                            'level' => 0
                        ];

                        // Build hierarchy
                        $sorted_report = [];
                        $processed = [];

                        function buildHierarchy($member_id, $report, &$sorted, &$processed, $level = 0)
                        {
                            if ($level >= 10 || !isset($report[$member_id]) || isset($processed[$member_id])) {
                                return;
                            }
                            $processed[$member_id] = true;
                            $report[$member_id]['level'] = $level;
                            $sorted[] = $report[$member_id];

                            // Find direct downlines
                            $downlines = [];
                            foreach ($report as $m) {
                                if ($m['sponsor_id'] == $member_id && !isset($processed[$m['member_id']])) {
                                    $downlines[] = $m;
                                }
                            }

                            // Sort downlines by member_id for consistent order
                            usort($downlines, function ($a, $b) {
                                // Prioritize members with non-zero total_commission or team_business
                                $a_priority = ($a['total_commission'] > 0 || $a['total_group_amount'] > 0) ? 1 : 0;
                                $b_priority = ($b['total_commission'] > 0 || $b['total_group_amount'] > 0) ? 1 : 0;
                                if ($a_priority !== $b_priority) {
                                    return $b_priority - $a_priority; // Active members first
                                }
                                return strcmp($a['member_id'], $b['member_id']); // Then by member_id
                            });

                            foreach ($downlines as $downline) {
                                buildHierarchy($downline['member_id'], $report, $sorted, $processed, $level + 1);
                            }
                        }

                        // Start with logged-in member
                        buildHierarchy($logged_in_member_id, $commission_report, $sorted_report, $processed);

                        // Display report
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-bordered'>";
                        echo "<thead>
        <tr>
            <th>Member ID</th>
            <th>Member Name</th>
            <th>Sponsor ID</th>
            <th>Self Business Amount</th>
            <th>Total Team Business</th>
            <th>Direct Commission %</th>
            <th>Total Commission</th>
        </tr>
    </thead><tbody>";

                        foreach ($sorted_report as $report) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($report['member_id']) . "</td>";
                            echo "<td>" . str_repeat('  ', $report['level'] * 2) . htmlspecialchars($report['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($report['sponsor_id']) . "</td>";
                            echo "<td>₹" . number_format($report['direct_amount'], 2) . "</td>";
                            echo "<td>₹" . number_format($report['total_group_amount'], 2) . "</td>";
                            echo "<td>" . number_format($report['direct_percent'], 2) . "%</td>";
                            echo "<td>₹" . number_format($report['total_commission'], 2) . "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody></table></div>";

                        // Commission breakdown
                        echo "<div class='card mt-4'>";
                        echo "<div class='card-header bg-primary text-white'>";
                        echo "<h5 class='card-title mb-0'>Commission Breakdown for {$logged_in_member['name']} ({$logged_in_member['member_id']})</h5>";
                        echo "</div>";
                        echo "<div class='card-body'>";

                        // Direct Commission
                        echo "<div class='alert alert-info'>";
                        echo "<h6>Direct Commission:</h6>";
                        echo "Self Business Amount: ₹" . number_format($logged_in_member['direct_amount'], 2) . " × {$logged_in_member['direct_percent']}% = ₹" . number_format($logged_in_member['direct_commission'], 2);
                        echo "</div>";

                        // Level Commission
                        echo "<div class='alert alert-success'>";
                        echo "<h6>Level Commission from Downlines:</h6>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-sm table-striped'>";
                        echo "<thead><tr><th>Member ID</th><th>Name</th><th>Sponsor ID</th><th>Product Name</th><th>Created Date</th><th>Net Amount</th><th>Self Business</th><th>Member %</th><th>Diff %</th><th>Commission</th></tr></thead>";
                        echo "<tbody>";

                        foreach ($all_downline_level_commission as $dl) {
                            echo "<tr>";
                            echo "<td>{$dl['member_id']}</td>";
                            echo "<td>{$dl['name']}</td>";
                            echo "<td>{$dl['sponsor_id']}</td>";
                            echo "<td>{$dl['productname']}</td>";
                            echo "<td>" . date('d-m-Y', strtotime($dl['created_date'])) . "</td>";
                            echo "<td>₹" . number_format($dl['net_amount'], 2) . "</td>";
                            echo "<td>₹" . number_format($dl['self_amount'], 2) . "</td>";
                            echo "<td>{$dl['direct_percent']}%</td>";
                            echo "<td>{$dl['diff_percent']}%</td>";
                            echo "<td>₹" . number_format($dl['level_commission'], 2) . "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody>";
                        echo "<tfoot><tr class='table-dark'><th colspan='9' class='text-end'>Total Level Commission:</th><th>₹" . number_format($logged_in_member['level_commission'], 2) . "</th></tr></tfoot>";
                        echo "</table>";
                        echo "</div>";
                        echo "</div>";

                        // Total Commission Summary
                        echo "<div class='alert alert-warning'>";
                        echo "<h6>Total Commission Summary:</h6>";
                        echo "<strong>Direct Commission:</strong> ₹" . number_format($logged_in_member['direct_commission'], 2) . "<br>";
                        echo "<strong>Level Commission:</strong> ₹" . number_format($logged_in_member['level_commission'], 2) . "<br>";
                        echo "<strong>Total Commission:</strong> ₹" . number_format($logged_in_member['total_commission'], 2);
                        echo "</div>";

                        echo "</div>"; // card-body
                        echo "</div>"; // card

                        // Note
                        echo "<div class='alert alert-info mt-3'>
        Showing commission data for member ID: <strong>$logged_in_member_id</strong> and their team (all historical data)
    </div>";
                        ?>
                    </div>


                    <?php include "associate-footer.php"; ?>
                </div>
            </div>


            <a href="#" target="_blank">
                <!-- partial -->
            </a>
            <!-- search box for options-->
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
    <style>
        i {
            color: yellow;
        }
    </style>



</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>