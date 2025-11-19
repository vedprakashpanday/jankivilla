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


$logged_in_member = $_SESSION['sponsor_id'];

// Get URL parameters
$from_date = isset($_GET['from']) ? $_GET['from'] : '';
$to_date = isset($_GET['to']) ? $_GET['to'] : '';
$zero_option = isset($_GET['zero']) ? $_GET['zero'] : 'with_zero'; // Default to with_zero

// Validate dates
if (empty($from_date) || empty($to_date)) {
    die("Invalid date range specified.");
}

// Fetch logged-in member's commission data
$query = "SELECT 
    member_id,
    member_name,
    total_commission,
    total_group_amount,
    payment_status
FROM commission_history 
WHERE member_id = ? 
AND from_date = ? 
AND to_date <= ? 
AND status = 'closed'";
if ($zero_option === 'without_zero') {
    $query .= " AND total_commission > 0";
}
$stmt = $pdo->prepare($query);
$stmt->execute([$logged_in_member, $from_date, $to_date]);
$logged_in_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Function to get all downline member IDs, excluding logged-in member
function getDownlineMembers($pdo, $sponsor_id, &$downline_members = [])
{
    $query = "SELECT mem_sid FROM tbl_regist WHERE sponsor_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sponsor_id]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($members as $member) {
        $mem_sid = $member['mem_sid'];
        if (!in_array($mem_sid, $downline_members)) {
            $downline_members[] = $mem_sid;
            getDownlineMembers($pdo, $mem_sid, $downline_members);
        }
    }

    return $downline_members;
}

// Get downline members for the logged-in member
$downline_members = getDownlineMembers($pdo, $logged_in_member);

// Query to fetch commission history for downlines
if (empty($downline_members)) {
    $commission_data = [];
} else {
    $placeholders = implode(',', array_fill(0, count($downline_members), '?'));
    $query = "SELECT 
        id,
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
        status
    FROM commission_history 
    WHERE member_id IN ($placeholders)
    AND from_date = ?
    AND to_date <= ?
    AND status = 'closed'";
    if ($zero_option === 'without_zero') {
        $query .= " AND total_commission > 0";
    }

    $stmt = $pdo->prepare($query);
    $params = array_merge($downline_members, [$from_date, $to_date]);
    $stmt->execute($params);
    $commission_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <style>
        .logged-in-row {
            background-color: #e7f3ff;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
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


</head>

<body class="hold-transition skin-blue sidebar-mini">



    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "associate-headersidepanel.php"; ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">

                    </section>

                    <!-- Main content -->
                    <section class="container" style="padding-left: unset; padding-right:unset;">

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <!-- <h3 class="box-title">Closing Report</h3> -->
                            </div>

                            <div class="box-body">
                                <h3 class="m-4">Commission Close Report for <?php echo htmlspecialchars($from_date . " to " . $to_date); ?> (<?php echo $zero_option === 'with_zero' ? 'With Zero' : 'Without Zero'; ?>)</h3>

                                <!-- Logged-in Member's Commission -->
                                <?php if ($logged_in_data):
                                    $tds = $logged_in_data['total_commission'] * 0.05;
                                    $admin_charge = $logged_in_data['total_commission'] * 0.05;
                                    $final_amount = $logged_in_data['total_commission'] - ($tds + $admin_charge);
                                    $current_status = $logged_in_data['payment_status'] ?? 'unpaid';
                                ?>
                                    <div class="logged-in-row">
                                        <h4>Your Commission Summary</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Member ID</th>
                                                <td><?php echo htmlspecialchars($logged_in_data['member_id']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Member Name</th>
                                                <td><?php echo htmlspecialchars($logged_in_data['member_name']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Member Team Business</th>
                                                <td>₹<?php echo number_format($logged_in_data['total_group_amount'], 2); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Total Commission</th>
                                                <td>₹<?php echo number_format($logged_in_data['total_commission'], 2); ?></td>
                                            </tr>
                                            <tr>
                                                <th>TDS (5%)</th>
                                                <td>₹<?php echo number_format($tds, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Admin Charge (5%)</th>
                                                <td>₹<?php echo number_format($admin_charge, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Final Amount</th>
                                                <td>₹<?php echo number_format($final_amount, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td><?php echo ucfirst($current_status); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">No commission data found for you in this period.</div>
                                <?php endif; ?>

                                <!-- Downline Commission Table -->
                                <h4>Downline Commission Details</h4>
                                <?php if (empty($commission_data)): ?>
                                    <div class="alert alert-info">No commission data found for your downlines in this period.</div>
                                <?php else: ?>
                                    <div class="table-responsive card" style="height: 70vh;">
                                        <table class="table table-bordered" id='salesTable'>
                                            <thead>
                                                <tr>
                                                    <th>Member ID</th>
                                                    <th>Member Name</th>
                                                    <th>Sponsor ID</th>
                                                    <th>Self Business Amount</th>
                                                    <th>Team Business</th>
                                                    <th>Direct Commission %</th>
                                                    <th>Direct Commission</th>
                                                    <th>Level Commission</th>
                                                    <th>Total Commission</th>
                                                    <th>TDS (5%)</th>
                                                    <th>Admin Charge (5%)</th>
                                                    <th>Final Amount</th>
                                                    <th>Current Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($commission_data as $data):
                                                    $tds = $data['total_commission'] * 0.05;
                                                    $admin_charge = $data['total_commission'] * 0.05;
                                                    $final_amount = $data['total_commission'] - ($tds + $admin_charge);
                                                    $current_status = $data['payment_status'] ?? 'unpaid';
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($data['member_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($data['member_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($data['sponsor_id']); ?></td>
                                                        <td>₹<?php echo number_format($data['direct_amount'], 2); ?></td>
                                                        <td>₹<?php echo number_format($data['total_group_amount'], 2); ?></td>
                                                        <td><?php echo number_format($data['direct_percent'], 2); ?>%</td>
                                                        <td>₹<?php echo number_format($data['direct_commission'], 2); ?></td>
                                                        <td>₹<?php echo number_format($data['level_commission'], 2); ?></td>
                                                        <td>₹<?php echo number_format($data['total_commission'], 2); ?></td>
                                                        <td>₹<?php echo number_format($tds, 2); ?></td>
                                                        <td>₹<?php echo number_format($admin_charge, 2); ?></td>
                                                        <td>₹<?php echo number_format($final_amount, 2); ?></td>
                                                        <td><?php echo ucfirst($current_status); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>

                                <a href="monthlyclosereport.php" class="btn btn-secondary mt-3">Back to Periods</a>
                            </div>
                            <!-- //box body end here -->
                        </div>
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

    <script>
        $(document).ready(function() {
            $('.payment-status').change(function() {
                var commissionId = $(this).data('id');
                var newStatus = $(this).val();
                console.log('ID:', commissionId, 'New Status:', newStatus); // Debug line
                $.ajax({
                    url: 'update_payment_status.php',
                    type: 'POST',
                    data: {
                        id: commissionId,
                        payment_status: newStatus
                    },
                    success: function(response) {
                        alert('Payment status updated successfully');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating payment status');
                        console.log('AJAX Error:', error); // Improved error logging
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#salesTable').DataTable({
                "ordering": false
            });

            $('.dropdown-toggle').dropdown();

        });
    </script>

</body>

</html>