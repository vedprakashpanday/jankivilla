<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

$query = "
    SELECT DISTINCT esr.invoice_id, esr.member_id, tr.m_name, esr.productname, 
           esr.net_amount, esr.payamount, esr.due_amount, esr.emi_month
    FROM emi_schedule_records esr
    LEFT JOIN tbl_regist tr ON esr.member_id = tr.mem_sid
    ORDER BY esr.created_date DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers
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

                                <div class="" style="padding-top: 10px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">

                                        <div class="col-md-12">
                                            <div style="background: #fff; padding: 5px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                                <div class="clr"></div>
                                                <div class="table-section">
                                                    <h3>EMI Dues</h3>
                                                    <hr>
                                                </div>

                                                <div class="heading">
                                                    EMI Payment Details</div>
                                                <div>
                                                    <!-- <input type="image" name="" id="" class="ExportBtn65" src="../Images/print_icon.gif" style="height:30px;width:30px;">
                                                    &nbsp;&nbsp;
                                                    <input type="image" name="" id="" src="../Images/excel_icon.gif" style="height:30px;width:30px;"> -->
                                                    <br>
                                                    <div id="emi_schedule_report" style="overflow:auto;width: 94%">
                                                        <div class='table-responsive' style='overflow-x: scroll;'>
                                                            <table class='table table-bordered' id='customerTable'>
                                                                <thead>
                                                                    <tr>
                                                                        <th>Action</th>
                                                                        <th>Invoice ID</th>
                                                                        <th>Member ID / Name</th>
                                                                        <th>Product Name</th>
                                                                        <th>Net Amount</th>
                                                                        <th>Paid Amount</th>
                                                                        <th>Due Amount</th>
                                                                        <th>EMI Months</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    if (!empty($customer_data)) {
                                                                        $row_index = 0; // Unique index for each row
                                                                        foreach ($customer_data as $row) {
                                                                            $member_id = htmlspecialchars($row['member_id'] ?? 'N/A');
                                                                            $member_name = htmlspecialchars($row['mem_name'] ?? 'Unknown');
                                                                            $invoice_id = htmlspecialchars($row['invoice_id']);
                                                                            $unique_row_id = "row-$row_index"; // Unique ID for each row

                                                                            echo "<tr class='main-row'>";
                                                                            echo "<td><button type='button' class='btn btn-primary view-payments' data-row-id='$unique_row_id' data-invoice-id='$invoice_id'>View</button></td>";
                                                                            echo "<td>$invoice_id</td>";
                                                                            echo "<td>$member_id / $member_name</td>";
                                                                            echo "<td>" . htmlspecialchars($row['productname'] ?? 'N/A') . "</td>";
                                                                            echo "<td>₹" . number_format($row['net_amount'] ?? 0, 2, '.', ',') . "</td>";
                                                                            echo "<td>₹" . number_format($row['payamount'] ?? 0, 2, '.', ',') . "</td>";
                                                                            echo "<td>₹" . number_format($row['due_amount'] ?? 0, 2, '.', ',') . "</td>";
                                                                            echo "<td>" . ($row['emi_month'] ? htmlspecialchars($row['emi_month']) : 'N/A') . "</td>";
                                                                            echo "</tr>";

                                                                            // Hidden row for EMI schedule details
                                                                            echo "<tr class='payment-details-row' id='payment-details-$unique_row_id' style='display:none;'>";
                                                                            echo "<td colspan='8' class='payment-details-content'></td>";
                                                                            echo "</tr>";

                                                                            $row_index++;
                                                                        }
                                                                    } else {
                                                                        echo "<tr><td colspan='8'>No records found.</td></tr>";
                                                                    }
                                                                    ?>
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
                    </div>

                    <!-- footer -->

                    <?php include 'adminfooter.php'; ?>
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


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle View button click
            $('.view-payments').on('click', function() {
                var rowId = $(this).data('row-id');
                var invoiceId = $(this).data('invoice-id');
                var detailsRow = $('#payment-details-' + rowId);
                var detailsContent = detailsRow.find('.payment-details-content');

                // Toggle visibility
                if (detailsRow.is(':visible')) {
                    detailsRow.hide();
                    $(this).text('View');
                    detailsContent.empty();
                    return;
                }

                // Show loading message
                detailsContent.html('<p>Loading EMI schedule...</p>');

                // Fetch EMI details via AJAX
                $.ajax({
                    url: 'fetch_emi_details.php',
                    type: 'POST',
                    data: {
                        invoice_id: invoiceId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            var table = `
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>EMI Amount</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                            $.each(response.data, function(index, item) {
                                table += `
                            <tr>
                                <td>${item.month_number}</td>
                                <td>₹${item.emi_amount}</td>
                                <td>${item.due_date}</td>
                            </tr>
                        `;
                            });
                            table += `
                            </tbody>
                        </table>
                    `;
                            detailsContent.html(table);
                            detailsRow.show();
                            $(this).text('Hide');
                        } else {
                            detailsContent.html('<p>No EMI schedule found for this invoice.</p>');
                            detailsRow.show();
                            $(this).text('Hide');
                        }
                    }.bind(this),
                    error: function() {
                        detailsContent.html('<p>Error loading EMI schedule. Please try again.</p>');
                        detailsRow.show();
                        $(this).text('Hide');
                    }.bind(this)
                });
            });
        });
    </script>

</body>

</html>