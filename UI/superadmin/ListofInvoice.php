<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}

$start_sponsor_id = $_SESSION['sponsor_id'];

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


    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

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
                            <div class="">


                                <div class="container">
                                    <div class="row justify-content-center">

                                        <div class="col-md-12">
                                            <div style="background: #fff; padding: 0px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                <h2>List of Invoice</h2>
                                                <hr>

                                                <div id="">
                                                    <div id="" style="overflow:auto;width: 100%;">
                                                        <?php
                                                        include_once 'connectdb.php'; // Ensure this file sets up $pdo

                                                        $from_date = isset($_POST['from_date']) && !empty($_POST['from_date']) ? $_POST['from_date'] : '2000-01-01';
                                                        $to_date = isset($_POST['to_date']) && !empty($_POST['to_date']) ? $_POST['to_date'] : '2099-12-31';

                                                        $query = "
                SELECT 
                    tca.id,
                    tca.invoice_id,
                    tca.created_date AS date,
                    tca.member_id,
                    tca.customer_name,
                    cd.customer_mobile,
                    cd.address,
                    pt.product_type_name AS product_type,
                    tca.productname AS product_name,
                    tca.area AS squarefeet,
                    tca.rate,
                    tca.gross_amount,
                    tca.corner_charge,
                    tca.net_amount,
                    tca.payamount AS pay_amount,
                    tca.due_amount,
                    tca.remarks as remarks_notes
                FROM tbl_customeramount tca
                LEFT JOIN customer_details cd ON tca.customer_id = cd.customer_id
                LEFT JOIN producttype pt ON tca.producttype = pt.product_type_id
                WHERE tca.created_date BETWEEN :from_date AND :to_date OR tca.created_date IS NULL
                ORDER BY tca.id desc
            ";

                                                        $stmt = $pdo->prepare($query);
                                                        $stmt->execute([
                                                            'from_date' => $from_date,
                                                            'to_date' => $to_date
                                                        ]);
                                                        $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        echo "<div class='table-responsive' style='overflow-x: scroll;'>";
                                                        echo "<table class='table table-bordered' id='salesTable'>";
                                                        echo "<thead>
                <tr>
                    <th>Print</th>
                    <th>Member ID</th>
                    <th>Invoice ID</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Product Type</th>
                    <th>Product Name</th>
                    <th>Square Feet</th>
                    <th>Rate</th>
                    <th>Gross Amount</th>
                    <th>Corner Charge</th>
                    <th>Net Amount</th>
                    <th>Pay Amount</th>
                    <th>Due Amount</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>";

                                                        if (!empty($sales_data)) {
                                                            foreach ($sales_data as $row) {
                                                                $member_id = $row['member_id'];
                                                                $invoice_id = $row['invoice_id'];

                                                                echo "<tr>";
                                                                echo "<td><a href='printsaleinvoice.php?invoice_id=$invoice_id&member_id=$member_id' target='_blank' class='btn btn-success'>Print</a></td>";
                                                                echo "<td>" . ($row['member_id'] ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($invoice_id ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['date'] ? date('d-m-Y', strtotime($row['date'])) : 'N/A') . "</td>";
                                                                echo "<td>" . ($row['customer_name'] ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['customer_mobile'] ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['address'] ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['product_type'] ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['product_name'] ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['squarefeet'] ?: 'N/A') . "</td>";
                                                                echo "<td>₹" . number_format(floatval($row['rate']) ?: 0, 2) . "</td>";
                                                                echo "<td>₹" . number_format(floatval($row['gross_amount']) ?: 0, 2) . "</td>";
                                                                echo "<td>₹" . number_format(floatval($row['corner_charge']) ?: 0, 2) . "</td>";
                                                                echo "<td>₹" . number_format(floatval($row['net_amount']) ?: 0, 2) . "</td>";
                                                                echo "<td>₹" . number_format(floatval($row['pay_amount']) ?: 0, 2) . "</td>";
                                                                echo "<td>₹" . number_format(floatval($row['due_amount']) ?: 0, 2) . "</td>";
                                                                echo "<td>" . ($row['remarks_notes'] ?: 'N/A') . "</td>";
                                                                echo "</tr>";
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='16'>No records found.</td></tr>";
                                                        }

                                                        echo "</tbody></table></div>";

                                                        echo "<div class='alert alert-info mt-3'>
                Showing all sales records
                " . (!empty($_POST['from_date']) && !empty($_POST['to_date']) ?
                                                            " filtered from {$_POST['from_date']} to {$_POST['to_date']}" :
                                                            " (showing all dates)") . "
            </div>";
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>

                                        </div>
                                        <br>
                                    </div>


                                    <br>


                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- footer -->


        </div>

        <?php include 'adminfooter.php'; ?>

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
                // "scrollX": true, // Enable horizontal scrolling
                // "pageLength": 10, // Number of rows per page
                // "lengthMenu": [10, 25, 50, 100], // Options for rows per page
                // "order": [
                //     [4, "desc"]
                // ], // Default sort by Date column (descending)
                // "columnDefs": [{
                //         "orderable": false,
                //         "targets": 0
                //     } // Disable sorting on Print column
                // ],
                // "language": {
                //     "emptyTable": "No sales data available",
                //     "search": "Search records:"
                // }
                "ordering": false,
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


    <script>
        function printInvoice(invoiceId, memberId) {
            const url = 'saleinvoiceprint.php?invoice_id=' + invoiceId + '&member_id=' + memberId;
            console.log('Opening URL:', url); // Debug
            const printWindow = window.open(url, '_blank');
            if (!printWindow) {
                alert('Please allow popups for this site to print the invoice.');
            }
        }
    </script>




    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);

        }
    </script>


</body>

</html>