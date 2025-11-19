<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Amitabh Builders & Developers Pvt. Ltd. - EMI Calculate
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



                                                <div id="" style="overflow:auto;width: 94%">
                                                    <?php
                                                    // EMI Payment Tracking System - Based on First Payment Date

                                                    $query = "
    SELECT
        c.member_id,
        r.m_name,
        c.customer_name,
        c.customer_id,
        cd.customer_mobile,
        c.productname,
        c.producttype,
        pt.product_type_name,
        c.net_amount,
        c.invoice_id,
        p.emi_month
    FROM tbl_customeramount c
    LEFT JOIN tbl_regist r ON c.member_id = r.mem_sid
    LEFT JOIN producttype pt ON c.producttype = pt.product_type_id
    LEFT JOIN products p ON c.productname = p.ProductName 
        AND c.producttype = p.product_type_id
    LEFT JOIN customer_details cd ON c.customer_id = cd.customer_id
    WHERE p.emi_month != ''
    ORDER BY c.member_id ASC, c.productname ASC
";


                                                    $stmt = $pdo->prepare($query);
                                                    $stmt->execute();
                                                    $customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                    // Display main table
                                                    echo "<div class='table-responsive' style='overflow-x: scroll;'>";
                                                    echo "<h3>📊 EMI Payment Tracking System</h3>";
                                                    echo "<div class='legend' style='margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;'>";

                                                    // ---- Legend items ----
                                                    echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
                                                    echo "<span style='background: green; color: white; padding: 8px; margin-right: 10px; border-radius: 4px;'>🟢 ON TIME & CORRECT AMOUNT</span>";
                                                    echo "<span style='background: red; color: white; padding: 8px; margin-right: 10px; border-radius: 4px;'>🔴 LATE OR INSUFFICIENT</span>";
                                                    echo "<span style='background: orange; color: white; padding: 8px; border-radius: 4px;'>🟠 PENDING EMI</span>";

                                                    // ---- Print button (right-aligned) ----
                                                    echo "<button id='openPrintModal' style='background:#007bff; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:bold;'>Print</button>";


                                                    echo "</div>";



                                                    // <!-- ==== PRINT MODAL ==== -->
                                                    echo '<div id="printModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999;">
    <div style="background:#fff; margin:10% auto; padding:25px; width:90%; max-width:420px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
        <h5 style="margin-top:0;">Print EMI Report</h5>
        <p>Select what you want to print:</p>

        <label style="display:block; margin:12px 0; cursor:pointer;">
            <input type="radio" name="printType" value="pending" checked> Pending EMI (orange)
        </label>
        <label style="display:block; margin:12px 0; cursor:pointer;">
            <input type="radio" name="printType" value="late"> Late / Overdue (red)
        </label>
        <label style="display:block; margin:12px 0; cursor:pointer;">
            <input type="radio" name="printType" value="both"> Both (orange + red)
        </label>

        <div style="margin-top:20px; text-align:right;">
            <button id="cancelPrint" style="margin-right:8px; padding:6px 14px;">Cancel</button>
            <button id="doPrint" style="background:#28a745; color:#fff; padding:6px 14px; border:none; border-radius:4px; cursor:pointer;">Print</button>
        </div>
    </div>
</div>';


                                                    echo "</div>";

                                                    echo "<table class='table table-bordered' id='emiTrackingTable'>";
                                                    echo "<thead style='background-color: #007bff; color: white;'>
            <tr>
                <th>Action</th>
                <th>Member ID/Member Name</th>
                <th>Customer Name</th>
                <th>Customer Mobile</th>
                <th>Product</th>
                <th>Total Amount</th>
                <th>EMI Started</th>
                <th>Monthly EMI</th>
                <th>Next EMI Due</th>
                <th>Current Status</th>
            </tr>
          </thead>
          <tbody>";

                                                    if (!empty($customer_data)) {
                                                        $row_index = 0;
                                                        foreach ($customer_data as $row) {
                                                            $member_id = $row['member_id'];
                                                            $product_name = $row['productname'];
                                                            $net_amount = $row['net_amount'];
                                                            $emi_months = $row['emi_month'];
                                                            $unique_row_id = "row-{$member_id}-{$row_index}";

                                                            // Get first payment date and calculate EMI details
                                                            $first_payment_query = "
                SELECT 
                    MIN(created_date) as first_payment_date,
                    SUM(payamount) as total_paid,
                    COUNT(*) as payment_count
                FROM receiveallpayment
                WHERE member_id = :member_id 
                    AND productname = :productname
            ";
                                                            $first_stmt = $pdo->prepare($first_payment_query);
                                                            $first_stmt->execute([
                                                                'member_id' => $member_id,
                                                                'productname' => $product_name
                                                            ]);
                                                            $payment_info = $first_stmt->fetch(PDO::FETCH_ASSOC);

                                                            $first_payment_date = $payment_info['first_payment_date'];
                                                            $total_paid = $payment_info['total_paid'] ?: 0;

                                                            if (!$first_payment_date) {
                                                                // No payments made yet
                                                                $emi_start_date = 'Not Started';
                                                                $monthly_emi = 0;
                                                                $next_emi_date = 'Payment Required';
                                                                $status_color = 'orange';
                                                                $status_text = 'No Payment Made';
                                                                $status_icon = '🟠';
                                                            } else {
                                                                // Calculate EMI from first payment date
                                                                $remaining_amount = $net_amount - $total_paid;
                                                                $monthly_emi = ($emi_months > 0) ? round($remaining_amount / $emi_months, 2) : 0;

                                                                // Calculate next EMI due date
                                                                $payment_day = date('j', strtotime($first_payment_date));
                                                                $current_date = date('Y-m-d');

                                                                // Find next EMI date
                                                                $next_month = date('Y-m', strtotime('+1 month'));
                                                                $next_emi_date = date('Y-m-' . sprintf('%02d', $payment_day), strtotime($next_month . '-01'));

                                                                // Check current month payment status
                                                                $current_month_due = date('Y-m-' . sprintf('%02d', $payment_day));

                                                                $current_payment_query = "
                    SELECT 
                        SUM(payamount) as month_paid,
                        MIN(created_date) as first_payment_this_cycle
                    FROM receiveallpayment
                    WHERE member_id = :member_id 
                        AND productname = :productname
                        AND created_date >= :due_date
                        AND created_date < :next_due_date
                ";
                                                                $current_stmt = $pdo->prepare($current_payment_query);
                                                                $current_stmt->execute([
                                                                    'member_id' => $member_id,
                                                                    'productname' => $product_name,
                                                                    'due_date' => date('Y-m-01', strtotime($current_month_due)),
                                                                    'next_due_date' => date('Y-m-01', strtotime('+1 month', strtotime($current_month_due)))
                                                                ]);
                                                                $current_payment = $current_stmt->fetch(PDO::FETCH_ASSOC);

                                                                $month_paid = $current_payment['month_paid'] ?: 0;
                                                                $payment_date_this_cycle = $current_payment['first_payment_this_cycle'];

                                                                // Determine status
                                                                if ($month_paid == 0) {
                                                                    if ($current_date > $current_month_due) {
                                                                        $status_color = 'red';
                                                                        $status_text = 'EMI Overdue';
                                                                        $status_icon = '🔴';
                                                                    } else {
                                                                        $status_color = 'orange';
                                                                        $status_text = 'EMI Pending';
                                                                        $status_icon = '🟠';
                                                                    }
                                                                } else {
                                                                    // Payment made - check amount and timing
                                                                    if ($month_paid >= $monthly_emi) {
                                                                        // Sufficient amount - check timing
                                                                        if ($payment_date_this_cycle <= $current_month_due) {
                                                                            $status_color = 'green';
                                                                            $status_text = 'Paid On Time';
                                                                            $status_icon = '🟢';
                                                                        } else {
                                                                            $status_color = 'red';
                                                                            $status_text = 'Paid Late';
                                                                            $status_icon = '🔴';
                                                                        }
                                                                    } else {
                                                                        $status_color = 'red';
                                                                        $status_text = 'Insufficient Amount';
                                                                        $status_icon = '🔴';
                                                                    }
                                                                }

                                                                $emi_start_date = date('d-M-Y', strtotime($first_payment_date));
                                                                $next_emi_date = ($remaining_amount > 0) ? date('d-M-Y', strtotime($next_emi_date)) : 'Completed';
                                                            }

                                                            echo "<tr class='main-row'>";
                                                            echo "<td><button type='button' class='btn btn-info view-emi-details' 
                    data-row-id='$unique_row_id' 
                    data-member-id='$member_id'
                    data-product='$product_name'>
                    View Details</button></td>";
                                                            echo "<td><strong>" . ($member_id ?: 'N/A') . "/" . ($row['m_name'] ?: 'N/A') . "</strong></td>";

                                                            echo "<td>" . ($row['customer_name'] ?: 'N/A') . "</td>";
                                                            echo "<td>" . ($row['customer_mobile'] ?: 'N/A') . "</td>";
                                                            echo "<td><strong>" . $product_name . "</strong></td>";
                                                            echo "<td>₹" . number_format($net_amount, 2) . "</td>";
                                                            echo "<td>" . $emi_start_date . "</td>";
                                                            echo "<td><strong>₹" . number_format($monthly_emi, 2) . "</strong></td>";
                                                            echo "<td><strong style='color: #dc3545;'>" . $next_emi_date . "</strong></td>";
                                                            echo "<td style='background-color: $status_color; color: white; font-weight: bold; text-align: center;'>
                    $status_icon $status_text
                  </td>";
                                                            echo "</tr>";

                                                            // Hidden detail row
                                                            echo "<tr class='emi-details-row' id='emi-details-$unique_row_id' style='display:none;'>";
                                                            echo "<td colspan='9' class='emi-details-content' style='background-color: #f8f9fa;'></td>";
                                                            echo "</tr>";

                                                            $row_index++;
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='9'>No EMI records found.</td></tr>";
                                                    }

                                                    echo "</tbody></table></div>";
                                                    ?>

                                                    <script>
                                                        $(document).ready(function() {
                                                            $('.view-emi-details').on('click', function() {
                                                                var rowId = $(this).data('row-id');
                                                                var memberId = $(this).data('member-id');
                                                                var productName = $(this).data('product');
                                                                var $detailsRow = $('#emi-details-' + rowId);
                                                                var $detailsContent = $detailsRow.find('.emi-details-content');

                                                                // Toggle visibility
                                                                if ($detailsRow.is(':visible')) {
                                                                    $detailsRow.hide();
                                                                    $(this).text('View Details');
                                                                    return;
                                                                }

                                                                // Show loading and fetch data
                                                                $detailsRow.show();
                                                                $(this).text('Hide Details');
                                                                $detailsContent.html('<div style="text-align: center; padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Loading EMI payment history...</div>');

                                                                $.ajax({
                                                                    url: 'emi_payment_details.php',
                                                                    method: 'POST',
                                                                    data: {
                                                                        member_id: memberId,
                                                                        product_name: productName
                                                                    },
                                                                    success: function(response) {
                                                                        $detailsContent.html(response);
                                                                    },
                                                                    error: function(xhr, status, error) {
                                                                        $detailsContent.html('<div class="alert alert-danger">Error loading EMI details: ' + error + '</div>');
                                                                    }
                                                                });
                                                            });

                                                            // Initialize DataTable
                                                            $('#emiTrackingTable').DataTable({
                                                                "pageLength": 25,
                                                                "ordering": true,
                                                                "searching": true,
                                                                "info": true,
                                                                "responsive": true,
                                                                "order": [
                                                                    [7, "asc"]
                                                                ],
                                                                "fixedHeader": true, // Enable fixed header
                                                                "rowCallback": function(row, data, index) {
                                                                    if ($(row).hasClass('emi-details-row')) {
                                                                        $(row).addClass('dtr-hidden');
                                                                    }
                                                                },
                                                                "initComplete": function() {
                                                                    console.log("DataTable initialized successfully");
                                                                }
                                                            });
                                                        });
                                                    </script>

                                                    <!-- <style>
                                                            #emiTrackingTable th {
                                                                position: sticky;
                                                                top: 0;
                                                                z-index: 10;
                                                            }

                                                            .legend span {
                                                                border-radius: 4px;
                                                                display: inline-block;
                                                                font-weight: bold;
                                                            }

                                                            .main-row:hover {
                                                                background-color: #f5f5f5;
                                                            }

                                                            .emi-details-content {
                                                                border-left: 4px solid #007bff;
                                                            }

                                                            #emiTrackingTable th {
                                                                position: sticky;
                                                                top: 0;
                                                                z-index: 10;
                                                            }

                                                            .table td,
                                                            .table th {
                                                                vertical-align: middle;
                                                                text-align: center;
                                                            }
                                                        </style> -->

                                                    <style>
                                                        .table-responsive {
                                                            position: relative;
                                                            max-height: 657px;
                                                            /* Adjust based on your needs */
                                                            overflow-y: auto;
                                                        }

                                                        #emiTrackingTable thead th {
                                                            position: sticky;
                                                            top: 0;
                                                            background-color: #007bff;
                                                            /* Maintain header background */
                                                            color: white;
                                                            z-index: 100;
                                                            /* Ensure header stays above content */
                                                            border-bottom: 2px solid #dee2e6;
                                                            /* Optional: Add border for clarity */
                                                        }

                                                        #emiTrackingTable {
                                                            width: 100%;
                                                            border-collapse: collapse;
                                                        }

                                                        .table-responsive::-webkit-scrollbar {
                                                            width: 8px;
                                                        }

                                                        .table-responsive::-webkit-scrollbar-thumb {
                                                            background-color: #888;
                                                            border-radius: 4px;
                                                        }
                                                    </style>

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

        <script>
            /* -------------------------------------------------
   1. Open / close modal
   ------------------------------------------------- */
            $('#openPrintModal').on('click', function() {
                $('#printModal').fadeIn(200);
            });
            $('#cancelPrint').on('click', function() {
                $('#printModal').fadeOut(200);
            });

            /* -------------------------------------------------
               2. Collect rows that match the selected type
               ------------------------------------------------- */
            $('#doPrint').on('click', function() {
                const type = $('input[name="printType"]:checked').val(); // pending | late | both
                const rows = [];

                // Loop through every main row (skip the hidden detail rows)
                $('#emiTrackingTable tbody tr.main-row').each(function() {
                    const $row = $(this);
                    const statusCell = $row.find('td').eq(9).text().trim(); // status column

                    // Detect colour from the cell background (the PHP code sets it)
                    const bg = $row.find('td').eq(9).css('background-color');

                    let isPending = bg.includes('255, 165, 0') || bg.includes('orange'); // orange
                    let isLate = bg.includes('255, 0, 0') || bg.includes('red'); // red
                    let isGreen = bg.includes('0, 128, 0') || bg.includes('green'); // green

                    let include = false;
                    if (type === 'pending' && isPending) include = true;
                    if (type === 'late' && isLate) include = true;
                    if (type === 'both' && (isPending || isLate)) include = true;

                    if (include) {
                        // Gather the data you asked for
                        const memberId = $row.find('td').eq(1).text().split('/')[0].trim(); // Member ID
                        const custName = $row.find('td').eq(2).text().trim();
                        const mobile = $row.find('td').eq(3).text().trim();
                        const product = $row.find('td').eq(4).find('strong').text().trim();
                        const totalAmt = $row.find('td').eq(5).text().trim(); // total amount (not due)
                        const emiAmt = $row.find('td').eq(7).find('strong').text().trim(); // Monthly EMI
                        const dueDate = $row.find('td').eq(8).find('strong').text().trim();

                        rows.push({
                            memberId,
                            custName,
                            mobile,
                            product,
                            totalAmt,
                            emiAmt,
                            dueDate,
                            status: statusCell
                        });
                    }
                });

                if (rows.length === 0) {
                    alert('No records match the selected filter.');
                    return;
                }

                // -------------------------------------------------
                // 3. Build printable HTML
                // -------------------------------------------------
                let printHTML = `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EMI Print Report</title>
    <style>
        body {font-family: Arial, sans-serif; margin:20px;}
        h2 {text-align:center; margin-bottom:5px;}
        .subtitle {text-align:center; color:#555; margin-bottom:20px;}
        table {width:100%; border-collapse:collapse; margin-top:15px;}
        th, td {border:1px solid #aaa; padding:8px; text-align:left;}
        th {background:#007bff; color:#fff;}
        .pending {background:#fff3cd;}
        .late    {background:#f8d7da;}
        @media print {
            body {margin:0;}
            .no-print {display:none;}
        }
    </style>
</head>
<body>
    <h2>EMI Payment Report</h2>
    <p class="subtitle">Only ${type === 'both' ? 'Pending & Late' : type === 'pending' ? 'Pending' : 'Late/Overdue'} records</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member ID</th>
                <th>Customer Name</th>
                <th>Mobile</th>
                <th>Product</th>
                <th>Total Amount</th>
                <th>Monthly EMI</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>`;

                rows.forEach((r, i) => {
                    const rowClass = r.status.includes('Pending') ? 'pending' : 'late';
                    printHTML += `
            <tr class="${rowClass}">
                <td>${i + 1}</td>
                <td>${r.memberId}</td>
                <td>${r.custName}</td>
                <td>${r.mobile}</td>
                <td>${r.product}</td>
                <td>${r.totalAmt}</td>
                <td>${r.emiAmt}</td>
                <td>${r.dueDate}</td>
                <td>${r.status}</td>
            </tr>`;
                });

                printHTML += `
        </tbody>
    </table>
    <div class="no-print" style="margin-top:30px; text-align:center;">
        <button onclick="window.print()">Print this page</button>
        <button onclick="window.close()" style="margin-left:10px;">Close</button>
    </div>
</body>
</html>`;

                // -------------------------------------------------
                // 4. Open in new tab and print
                // -------------------------------------------------
                const printWin = window.open('', '_blank');
                printWin.document.open();
                printWin.document.write(printHTML);
                printWin.document.close();

                // Give the browser a moment to render, then auto-print
                printWin.onload = function() {
                    setTimeout(() => printWin.print(), 500);
                };

                // Close modal
                $('#printModal').fadeOut(200);
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