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
                        // Start session
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        // Get logged-in member's ID
                        $logged_in_member_id = $_SESSION['sponsor_id'] ?? 'HHD30752';


                        // Fetch customers for the logged-in member, consolidating by invoice_id
                        $stmt = $pdo->prepare("
        SELECT 
            r.customer_name,
            MAX(r.customer_id) as customer_id,
            r.net_amount as total_net_amount,
            SUM(r.payamount) as total_payamount,
            r.invoice_id
        FROM receiveallpayment r
        WHERE r.member_id = :member_id
        GROUP BY r.invoice_id, r.customer_name, r.net_amount, r.productname
        ORDER BY r.customer_name
    ");
                        $stmt->execute(['member_id' => $logged_in_member_id]);
                        $customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <!-- Main table with DataTable -->
                        <table id="customerTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>View</th>
                                    <th>Customer Name</th>
                                    <th>Customer ID</th>
                                    <th>Net Amount</th>
                                    <th>Pay Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($customer_data)) {
                                    foreach ($customer_data as $customer) {
                                        echo '<tr>';
                                        echo '<td><button class="btn btn-sm view-invoices" data-customer-id="' . htmlspecialchars($customer['customer_id'] ?? '') . '" data-customer-name="' . htmlspecialchars($customer['customer_name']) . '" data-invoice-id="' . htmlspecialchars($customer['invoice_id']) . '"><i class="fas fa-plus"></i></button></td>';
                                        echo '<td>' . htmlspecialchars($customer['customer_name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($customer['customer_id'] ?? 'N/A') . '</td>';
                                        echo '<td>' . number_format($customer['total_net_amount'], 2) . '</td>';
                                        echo '<td>' . number_format($customer['total_payamount'], 2) . '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Modal for invoice details -->
                        <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" id="invoiceDetails"></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dependencies -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

                        <!-- JavaScript -->
                        <script>
                            $(document).ready(function() {
                                // Initialize DataTable
                                var table = $('#customerTable').DataTable({
                                    paging: true,
                                    searching: true,
                                    ordering: true,
                                    info: true,
                                    lengthMenu: [10, 25, 50, 100],
                                    pageLength: 10
                                });

                                // Handle View button click
                                $(document).on('click', '.view-invoices', function() {
                                    var customerId = $(this).data('customer-id') || '';
                                    var customerName = $(this).data('customer-name');
                                    var invoiceId = $(this).data('invoice-id');
                                    var row = $(this).closest('tr');
                                    var rowIndex = table.row(row).index();
                                    var subTableId = 'subTable_' + rowIndex;

                                    // Toggle sub-table
                                    if ($(this).hasClass('expanded')) {
                                        $(this).removeClass('expanded').html('<i class="fas fa-plus"></i>');
                                        $('#' + subTableId).remove();
                                    } else {
                                        $(this).addClass('expanded').html('<i class="fas fa-minus"></i>');
                                        $.ajax({
                                            url: 'fetch_customer_invoices.php',
                                            method: 'POST',
                                            data: {
                                                customer_id: customerId,
                                                customer_name: customerName,
                                                invoice_id: invoiceId,
                                                member_id: '<?php echo $logged_in_member_id; ?>'
                                            },
                                            success: function(response) {
                                                var subTable = '<tr id="' + subTableId + '"><td colspan="5"><div class="sub-table-container">' + response + '</div></td></tr>';
                                                row.after(subTable);
                                                $('.invoice-link').off('click').on('click', function() {
                                                    var invoiceId = $(this).data('invoice-id');
                                                    $.ajax({
                                                        url: 'fetch_invoice_details.php',
                                                        method: 'POST',
                                                        data: {
                                                            invoice_id: invoiceId,
                                                            member_id: '<?php echo $logged_in_member_id; ?>'
                                                        },
                                                        success: function(details) {
                                                            $('#invoiceDetails').html(details);
                                                            $('#invoiceModal').modal('show');
                                                        },
                                                        error: function() {
                                                            $('#invoiceDetails').html('<p>Error loading details.</p>');
                                                            $('#invoiceModal').modal('show');
                                                        }
                                                    });
                                                });
                                            },
                                            error: function() {
                                                row.after('<tr id="' + subTableId + '"><td colspan="5"><p>Error loading invoices.</p></td></tr>');
                                            }
                                        });
                                    }
                                });
                            });
                        </script>

                        <style>
                            .sub-table-container {
                                padding: 15px;
                            }

                            .invoice-link {
                                color: #007bff;
                                cursor: pointer;
                                text-decoration: underline;
                            }

                            .invoice-link:hover {
                                color: #0056b3;
                            }
                        </style>
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