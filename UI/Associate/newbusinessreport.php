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

                <div class="main-panel" style="padding: 5px">

                    <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">



                        <div class="clr"></div>
                        <div class="table-section">
                            <h3 style="padding-top: 30px;">One Time Registry Dues / Payment</h3>
                            <hr>

                            <!-- Month Filter Form -->
                            <form method="POST" class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="month_filter">Select Month:</label>
                                        <select name="month_filter" id="month_filter" class="form-control" onchange="this.form.submit()">
                                            <option value="">All Months</option>
                                            <?php
                                            // Generate month options (e.g., Jan 2024, Feb 2024, etc.)
                                            $currentYear = date('Y');
                                            for ($year = $currentYear; $year >= $currentYear - 5; $year--) { // Last 5 years
                                                for ($month = 12; $month >= 1; $month--) {
                                                    $monthName = date('M', mktime(0, 0, 0, $month, 1));
                                                    $value = sprintf("%04d-%02d", $year, $month);
                                                    $selected = (isset($_POST['month_filter']) && $_POST['month_filter'] === $value) ? 'selected' : '';
                                                    echo "<option value='$value' $selected>$monthName $year</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <div id="" style="overflow:auto;width: 100%">
                                <?php
                                // Get the logged-in member's ID from session
                                $logged_in_member_id = $_SESSION['sponsor_id'];

                                // Step 1: Fetch all downline member IDs recursively from tbl_regist, including self
                                function getDownlineMembers($pdo, $sponsor_id, &$member_ids = [])
                                {
                                    if (!in_array($sponsor_id, $member_ids)) {
                                        $member_ids[] = $sponsor_id;
                                    }

                                    $query = "SELECT mem_sid FROM tbl_regist WHERE sponsor_id = ?";
                                    $stmt = $pdo->prepare($query);
                                    $stmt->execute([$sponsor_id]);
                                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($results as $row) {
                                        $member_id = $row['mem_sid'];
                                        if (!in_array($member_id, $member_ids)) {
                                            $member_ids[] = $member_id;
                                            getDownlineMembers($pdo, $member_id, $member_ids);
                                        }
                                    }
                                    return $member_ids;
                                }

                                $member_ids = getDownlineMembers($pdo, $logged_in_member_id);

                                // Step 2: Handle month filter
                                if (isset($_POST['month_filter']) && !empty($_POST['month_filter'])) {
                                    $selectedMonth = $_POST['month_filter']; // Format: YYYY-MM
                                    $from_date = "$selectedMonth-01"; // First day of the month
                                    $to_date = date('Y-m-t', strtotime($selectedMonth)); // Last day of the month
                                } else {
                                    // Default to a wide date range if no month is selected
                                    $from_date = '2000-01-01';
                                    $to_date = '2099-12-31';
                                    $selectedMonth = '';
                                }

                                // Step 3: Query tbl_customeramount for self and downline members
                                $placeholders = implode(',', array_fill(0, count($member_ids), '?'));
                                $query = "
            SELECT
                ca.invoice_id,
                ca.created_date,
                ca.producttype AS plot_type,
                ca.productname,
                ca.area,
                ca.rate,
                ca.gross_amount,
                ca.payamount,
                ca.corner_charge,
                ca.due_amount,
                ca.net_amount,
                ca.member_id,
                pt.product_type_name
            FROM tbl_customeramount ca
            INNER JOIN producttype pt ON ca.producttype = pt.product_type_id
            WHERE ca.member_id IN ($placeholders)
            AND pt.product_type_name = 'One Time Registry'
            AND (ca.created_date BETWEEN ? AND ? OR ca.created_date IS NULL)
            ORDER BY ca.created_date DESC
        ";

                                $stmt = $pdo->prepare($query);
                                $params = array_merge($member_ids, [$from_date, $to_date]);
                                $stmt->execute($params);
                                $customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Display the data in a table
                                echo "<div class='table-responsive' style='overflow-x: scroll;'>";
                                echo "<table class='table table-bordered' id='salesTable'>";
                                echo "<thead>
            <tr>
                <th>Member ID</th>
                <th>Invoice ID</th>
                <th>Created Date</th>
                <th>Plot Type</th>
                <th>Product Name</th>
                <th>Area</th>
                <th>Rate</th>
                <th>Gross Amount</th>
                <th>Corner Charge</th>
                <th>Net Amount</th>
                <th>Pay Amount</th>
                <th>Due Amount</th>
                <th>25% Eligibility</th>
            </tr>
        </thead>
        <tbody>";

                                foreach ($customer_data as $row) {
                                    echo "<tr>";
                                    echo "<td>" . ($row['member_id'] ?: 'N/A') . "</td>";
                                    echo "<td>" . ($row['invoice_id'] ?: 'N/A') . "</td>";
                                    echo "<td>" . ($row['created_date'] ?: 'N/A') . "</td>";
                                    echo "<td>" . ($row['product_type_name'] ?: 'N/A') . "</td>";
                                    echo "<td>" . ($row['productname'] ?: 'N/A') . "</td>";
                                    echo "<td>" . ($row['area'] ?: 'N/A') . "</td>";
                                    echo "<td>" . ($row['rate'] ?: 'N/A') . "</td>";
                                    echo "<td>₹" . number_format(floatval($row['gross_amount']) ?: 0, 2) . "</td>";
                                    echo "<td>" . ($row['corner_charge'] ?: 'N/A') . "</td>";
                                    echo "<td>₹" . number_format(floatval($row['net_amount']) ?: 0, 2) . "</td>";
                                    echo "<td>₹" . number_format(floatval($row['payamount']) ?: 0, 2) . "</td>";
                                    echo "<td>₹" . number_format(floatval($row['due_amount']) ?: 0, 2) . "</td>";
                                    $net_amount = floatval($row['net_amount']);
                                    $payamount = floatval($row['payamount']);
                                    $eligibility_amount = $net_amount * 0.25;

                                    $eligibility_status = ($payamount >= $eligibility_amount) ? "Yes" : "No";
                                    if ($eligibility_status === "Yes") {
                                        echo "<td><span class='badge bg-success'>Yes</span></td>";
                                    } else {
                                        echo "<td><span class='badge bg-danger'>No</span></td>";
                                    }
                                    echo "</tr>";
                                }

                                if (empty($customer_data)) {
                                    echo "<tr>
                <td colspan='12'>No records found for member ID: $logged_in_member_id or their downlines with 'One Time Registry'</td>
            </tr>";
                                }

                                echo "</tbody></table></div>";

                                // Note about displayed data
                                echo "<div class='alert alert-info mt-3'>
            Showing data for member ID: <strong>$logged_in_member_id</strong> and their downline members
            " . ($selectedMonth ?
                                    " filtered for " . date('F Y', strtotime($selectedMonth)) :
                                    " (showing all dates)") . "
            <br>Filtered for Product Type: <strong>One Time Registry</strong>
        </div>";
                                ?>
                            </div>
                        </div>

                    </div>
                    <?php include "associate-footer.php"; ?>

                </div>
            </div>
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


    <script>
        $(document).ready(function() {
            $('#salesTable').DataTable({

            });

            $('.dropdown-toggle').dropdown();

        });
    </script>





    <style>
        i {
            color: yellow;
        }
    </style>


    <script type="text/javascript">
        //<![CDATA[
        (function() {
            var fn = function() {
                $get("ContentPlaceHolder1_ToolkitScriptManager1_HiddenField").value = '';
                Sys.Application.remove_init(fn);
            };
            Sys.Application.add_init(fn);
        })(); //]]>
    </script>



</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>