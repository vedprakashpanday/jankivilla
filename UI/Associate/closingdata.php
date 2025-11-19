<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
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


// Commission percentage calculation function (for fallback)
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

// Get the logged-in member's ID from session
$logged_in_member_id = $_SESSION['sponsor_id'];

// Fetch logged-in member's commission data (latest closed record)
$query = "SELECT member_id, member_name, direct_amount, direct_percent, direct_commission, 
                 level_commission, total_commission, from_date, to_date 
          FROM commission_history 
          WHERE member_id = :member_id AND status = 'closed' 
          ORDER BY created_at DESC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['member_id' => $logged_in_member_id]);
$member_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Function to get downline members
function getDownlineMembers($pdo, $sponsor_id, &$downlines = [], $level = 1, $max_level = 10)
{
    if ($level > $max_level) {
        return;
    }

    $query = "SELECT mem_sid, m_name, sponsor_id, direct_commission_percent 
              FROM tbl_regist 
              WHERE sponsor_id = :sponsor_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['sponsor_id' => $sponsor_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $member) {
        $downlines[] = [
            'member_id' => $member['mem_sid'],
            'm_name' => $member['m_name'] ?? 'N/A',
            'direct_percent' => $member['direct_commission_percent'] > 0
                ? floatval($member['direct_commission_percent'])
                : 0,
            'level' => $level
        ];
        // Recursive call for next level
        getDownlineMembers($pdo, $member['mem_sid'], $downlines, $level + 1, $max_level);
    }

    return $downlines;
}

// Fetch downlines
$downlines = getDownlineMembers($pdo, $logged_in_member_id);

// Fetch commission data for downlines from commission_history
$downline_commissions = [];
if (!empty($downlines)) {
    $downline_ids = array_column($downlines, 'member_id');
    $placeholders = implode(',', array_fill(0, count($downline_ids), '?'));
    $query = "SELECT member_id, member_name, direct_percent, total_commission, direct_amount 
              FROM commission_history 
              WHERE member_id IN ($placeholders) AND status = 'closed' 
              ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($downline_ids);
    $downline_commissions_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Index by member_id for easier lookup
    foreach ($downline_commissions_raw as $comm) {
        $downline_commissions[$comm['member_id']] = $comm;
    }
}

// Get logged-in member's direct_percent for differential calculations
$logged_in_percent = $member_data && $member_data['direct_percent'] > 0
    ? floatval($member_data['direct_percent'])
    : getCommissionPercent($member_data['direct_amount'] ?? 0);

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
                    <h3 class="p-4">Closing Report</h3>
                    <div style="overflow:auto;width: 100%">
                        <?php if ($member_data): ?>
                            <h4>Logged-in Member: <?php echo htmlspecialchars($member_data['member_name']); ?> (<?php echo htmlspecialchars($logged_in_member_id); ?>)</h4>
                            <p>Period: <?php echo htmlspecialchars($member_data['from_date']); ?> to <?php echo htmlspecialchars($member_data['to_date']); ?></p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Member ID</th>
                                            <th>Member Name</th>
                                            <th>Direct Commission</th>
                                            <th>Level Commission</th>
                                            <th>Total Commission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Logged-in Member Row -->
                                        <tr>
                                            <td><?php echo htmlspecialchars($member_data['member_id']); ?></td>
                                            <td><?php echo htmlspecialchars($member_data['member_name']); ?></td>
                                            <td>₹<?php echo number_format($member_data['direct_commission'], 2); ?></td>
                                            <td>₹<?php echo number_format($member_data['level_commission'], 2); ?></td>
                                            <td>₹<?php echo number_format($member_data['total_commission'], 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (!empty($downlines)): ?>
                                <h4>Downline Contributions</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Downline ID</th>
                                                <th>Downline Name</th>
                                                <th>Level</th>
                                                <th>Their Total Commission</th>
                                                <th>Contribution to You (Differential %)</th>
                                                <th>Contribution Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($downlines as $downline): ?>
                                                <?php
                                                $downline_id = $downline['member_id'];
                                                $comm_data = $downline_commissions[$downline_id] ?? null;
                                                if (!$comm_data) {
                                                    continue; // Skip if no commission data
                                                }
                                                $downline_percent = $comm_data['direct_percent'] > 0
                                                    ? floatval($comm_data['direct_percent'])
                                                    : ($downline['direct_percent'] ?: getCommissionPercent($comm_data['direct_amount'] ?? 0));
                                                $diff_percent = max(0, $logged_in_percent - $downline_percent);
                                                $contribution_amount = ($comm_data['direct_amount'] ?? 0) * ($diff_percent / 100);
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($downline_id); ?></td>
                                                    <td><?php echo htmlspecialchars($downline['m_name']); ?></td>
                                                    <td><?php echo $downline['level']; ?></td>
                                                    <td>₹<?php echo number_format($comm_data['total_commission'] ?? 0, 2); ?></td>
                                                    <td><?php echo number_format($diff_percent, 2); ?>%</td>
                                                    <td>₹<?php echo number_format($contribution_amount, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No downline members found.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>No commission data found for the logged-in member.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- //here the main code end// -->


            </div>
            <?php include "associate-footer.php"; ?>

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





        </div>
    </div>
    <style>
        i {
            color: yellow;
        }
    </style>



</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>