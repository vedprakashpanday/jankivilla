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
                    <h3 class="p-4">Team Purchase Plot</h3>
                    <div id="" style="overflow:auto;width: 100%">
                        <?php
                        // Get the logged-in member's ID from session
                        $logged_in_member_id = $_SESSION['sponsor_id'];

                        // Step 1: Fetch all downline member IDs recursively from tbl_regist
                        function getDownlineMembers($pdo, $sponsor_id, &$downline_ids = [])
                        {
                            $query = "SELECT mem_sid FROM tbl_regist WHERE sponsor_id = :sponsor_id";
                            $stmt = $pdo->prepare($query);
                            $stmt->execute(['sponsor_id' => $sponsor_id]);
                            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($results as $row) {
                                $member_id = $row['mem_sid'];
                                if (!in_array($member_id, $downline_ids)) {
                                    $downline_ids[] = $member_id;
                                    getDownlineMembers($pdo, $member_id, $downline_ids); // Recursive call
                                }
                            }
                            return $downline_ids;
                        }

                        $downline_ids = getDownlineMembers($pdo, $logged_in_member_id);

                        if (empty($downline_ids)) {
                            echo "<div class='alert alert-warning'>No downline members found for sponsor ID: $logged_in_member_id</div>";
                            exit;
                        }

                        // Step 2: Filter downlines present in both tables
                        $filtered_downline_ids = [];
                        foreach ($downline_ids as $member_id) {
                            $check_customer = $pdo->prepare("SELECT COUNT(*) FROM tbl_customeramount WHERE member_id = ?");
                            $check_customer->execute([$member_id]);
                            $customer_count = $check_customer->fetchColumn();

                            $check_payment = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE member_id = ?");
                            $check_payment->execute([$member_id]);
                            $payment_count = $check_payment->fetchColumn();

                            if ($customer_count > 0 && $payment_count > 0) {
                                $filtered_downline_ids[] = $member_id;
                            }
                        }

                        if (empty($filtered_downline_ids)) {
                            echo "<div class='alert alert-warning'>No downline members found in both tbl_customeramount and receiveallpayment.</div>";
                            exit;
                        }

                        // Get date filter if provided, otherwise use a wide date range
                        $from_date = isset($_POST['from_date']) && !empty($_POST['from_date']) ? $_POST['from_date'] : '2000-01-01';
                        $to_date = isset($_POST['to_date']) && !empty($_POST['to_date']) ? $_POST['to_date'] : '2099-12-31';

                        // Query to fetch data from tbl_customeramount for filtered downline members (using only ? placeholders)
                        $placeholders = implode(',', array_fill(0, count($filtered_downline_ids), '?'));
                        //                     $query = "
                        //     SELECT
                        //         invoice_id,
                        //         producttype,
                        //         created_date,
                        //         area,
                        //         rate,
                        //         productname,
                        //         gross_amount,
                        //         payamount,
                        //         corner_charge,
                        //         due_amount,
                        //         net_amount,
                        //         member_id
                        //     FROM tbl_customeramount
                        //     WHERE member_id IN ($placeholders)
                        //     AND (created_date BETWEEN ? AND ? OR created_date IS NULL)
                        //     ORDER BY created_date DESC
                        // ";

                        $query = "
    SELECT
        c.invoice_id,
        c.producttype,
        c.created_date,
        c.area,
        c.rate,
        c.productname,
        c.gross_amount,
        c.payamount,
        c.corner_charge,
        c.due_amount,
        c.net_amount,
        c.member_id,
        r.m_name
    FROM tbl_customeramount c
    JOIN tbl_regist r ON c.member_id = r.mem_sid
    WHERE c.member_id IN ($placeholders)
    AND (c.created_date BETWEEN ? AND ? OR c.created_date IS NULL)
    ORDER BY c.created_date DESC
";

                        $stmt = $pdo->prepare($query);
                        // Combine all parameters in order: downline IDs followed by from_date and to_date
                        $params = array_merge($filtered_downline_ids, [$from_date, $to_date]);
                        $stmt->execute($params);
                        $customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Display the data in a table
                        echo "<div class='table-responsive' style='overflow-x: scroll;height:75vh;'>";
                        echo "<table class='table table-bordered table-striped' id='customerTable'>";
                        echo "<thead>
    <tr>
        <th>View</th>
      <th>Member ID / Name</th>
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
                            $invoice_id = $row['invoice_id'];
                            $member_id = $row['member_id'];
                            $member_name = $row['m_name'];
                            echo "<tr class='main-row'>";
                            echo "<td><button class='btn btn-primary view-payments' data-invoice-id='$invoice_id' data-member-id='$member_id'>View</button></td>";
                            echo "<td>" . ($member_id ?: 'N/A') . " / " . ($member_name ?: 'N/A') . "</td>";
                            echo "<td>" . ($invoice_id ?: 'N/A') . "</td>";
                            echo "<td>" . ($row['created_date'] ?: 'N/A') . "</td>";
                            echo "<td>";
                            if ($row['producttype'] == 1) {
                                echo "One Time Registry";
                            } elseif ($row['producttype'] == 2) {
                                echo "EMI Mode";
                            } else {
                                echo "N/A";
                            }
                            echo "</td>";
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
        <td colspan='13'>No records found for downline members.</td>
    </tr>";
                        }

                        echo "</tbody></table></div>";

                        // Note about displayed data
                        echo "<div class='alert alert-info mt-3'>
        Showing data for downline members of sponsor ID: <strong>$logged_in_member_id</strong>
        " . (!empty($_POST['from_date']) && !empty($_POST['to_date']) ?
                            " filtered from {$_POST['from_date']} to {$_POST['to_date']}" :
                            " (showing all dates)") . "
    </div>";
                        ?>

                        <!-- Include jQuery for AJAX -->
                        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
                        <script>
                            $(document).ready(function() {
                                // Destroy any existing DataTable instance
                                if ($.fn.DataTable.isDataTable('#customerTable')) {
                                    $('#customerTable').DataTable().destroy();
                                }

                                // Initialize DataTable
                                var table = $('#customerTable').DataTable({
                                    "paging": true, // Enable pagination
                                    "searching": true, // Enable search
                                    "ordering": true, // Enable column sorting
                                    "info": true, // Show "Showing X of Y entries"
                                    "columns": [{
                                            "data": "View"
                                        },
                                        {
                                            "data": "Member ID"
                                        },
                                        {
                                            "data": "Invoice ID"
                                        },
                                        {
                                            "data": "Created Date"
                                        },
                                        {
                                            "data": "Plot Type"
                                        },
                                        {
                                            "data": "Product Name"
                                        },
                                        {
                                            "data": "Area"
                                        },
                                        {
                                            "data": "Rate"
                                        },
                                        {
                                            "data": "Gross Amount"
                                        },
                                        {
                                            "data": "Corner Charge"
                                        },
                                        {
                                            "data": "Net Amount"
                                        },
                                        {
                                            "data": "Pay Amount"
                                        },
                                        {
                                            "data": "Due Amount"
                                        },
                                        {
                                            "data": "25% Eligibility"
                                        }
                                    ]
                                });

                                // Delegate click event to handle dynamically rendered buttons
                                $('#customerTable tbody').on('click', '.view-payments', function() {
                                    var tr = $(this).closest('tr');
                                    var row = table.row(tr);
                                    var invoiceId = $(this).data('invoice-id');
                                    var memberId = $(this).data('member-id');

                                    if (row.child.isShown()) {
                                        // Hide the child row if already shown
                                        row.child.hide();
                                        tr.removeClass('shown');
                                    } else {
                                        // Show a loading message in the child row
                                        row.child('<div class="payment-details-content"><p>Loading...</p></div>').show();
                                        tr.addClass('shown');

                                        // Fetch payment details via AJAX
                                        $.ajax({
                                            url: 'teamplotfetch_payments.php',
                                            method: 'POST',
                                            data: {
                                                invoice_id: invoiceId,
                                                member_id: memberId
                                            },
                                            success: function(response) {
                                                // Update the child row with the fetched data
                                                row.child('<div class="payment-details-content">' + response + '</div>').show();
                                            },
                                            error: function(xhr, status, error) {
                                                // Show error message in the child row
                                                row.child('<div class="payment-details-content"><p>Error loading payment details: ' + error + '</p></div>').show();
                                            }
                                        });
                                    }
                                });

                                $('.dropdown-toggle').dropdown();
                            });
                        </script>
                    </div>



                </div>
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