<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


$from_date = isset($_GET['from']) ? $_GET['from'] : '';
$to_date = isset($_GET['to']) ? $_GET['to'] : '';

// Validate dates
if (empty($from_date) || empty($to_date)) {
    die("Invalid date range specified.");
}

// Ensure dates are in correct format (YYYY-MM-DD)
if (!DateTime::createFromFormat('Y-m-d', $from_date) || !DateTime::createFromFormat('Y-m-d', $to_date)) {
    die("Invalid date format. Use YYYY-MM-DD.");
}



// Query to fetch commission history for the specified date range
$query = "
    SELECT 
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
        payment_date,
        payment_mode,
        cheque_number,
        bank_name,
        cheque_date,
        utr_number,
        status
    FROM commission_history 
    WHERE from_date = :from_date AND to_date <= :to_date AND status = 'closed'
    ORDER BY id ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':from_date' => $from_date,
    ':to_date' => $to_date
]);
$commission_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Amitabh Builders - Admin Panel
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
        .self-details {
            display: none;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-top: 10px;
            padding: 15px;
            border-radius: 5px;
        }

        .self-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .self-btn:hover {
            background-color: #0056b3;
        }


        .self-btn-info {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .self-btn-info:hover {
            background-color: #138496;
        }

        .emi-badge {
            background-color: #ffc107;
            color: #000;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }

        .onetime-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
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
                <?php include 'adminheadersidepanel.php'; ?>
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
                                <h3 class="m-4">Commission Close Report for <?php echo $from_date . " to " . $to_date; ?></h3>

                                <?php if (empty($commission_data)): ?>
                                    <div class="alert alert-info">No commission data found for this period.</div>
                                <?php else: ?>
                                    <div class="table-responsive card" style="height: 70vh;">
                                        <?php
                                        // Get the from and to dates from URL parameters
                                        $from_date = isset($_GET['from']) ? $_GET['from'] : '';
                                        $to_date = isset($_GET['to']) ? $_GET['to'] : '';

                                        // Validate dates
                                        if (empty($from_date) || empty($to_date)) {
                                            die("Invalid date range specified.");
                                        }

                                        // Ensure dates are in correct format (YYYY-MM-DD)
                                        if (!DateTime::createFromFormat('Y-m-d', $from_date) || !DateTime::createFromFormat('Y-m-d', $to_date)) {
                                            die("Invalid date format. Use YYYY-MM-DD.");
                                        }

                                        // Query to fetch commission history for the specified date range
                                        $query = "
    SELECT 
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
        payment_date,
        payment_mode,
        cheque_number,
        bank_name,
        cheque_date,
        utr_number,
        status,
        payment_details
    FROM commission_history 
    WHERE from_date = :from_date AND to_date <= :to_date AND status = 'closed'
    ORDER BY id ASC
";

                                        $stmt = $pdo->prepare($query);
                                        $stmt->execute([
                                            ':from_date' => $from_date,
                                            ':to_date' => $to_date
                                        ]);
                                        $commission_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        ?>


                                        <table class="table table-bordered" id="salesTable">
                                            <thead>
                                                <tr>
                                                    <th>Details</th>
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
                                                    <th>Payment Status</th>
                                                    <th>Update Status</th>
                                                    <th>Payment Date</th>
                                                    <th>Payment Date for Cheque</th>
                                                    <th>Payment Date for UTR</th>
                                                    <th>Payment Mode</th>
                                                    <th>UTR Number</th>
                                                    <th>Cheque Number</th>
                                                    <th>Bank Name</th>
                                                    <th>Cheque Date</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($commission_data as $data):
                                                    $tds = $data['total_commission'] * 0.05;
                                                    $admin_charge = $data['total_commission'] * 0.05;
                                                    $final_amount = $data['total_commission'] - ($tds + $admin_charge);
                                                    $current_status = $data['payment_status'] ?? 'unpaid';
                                                    // Parse payment details
                                                    $payment_date = $data['payment_date'] ? json_decode($data['payment_date'], true) : [];
                                                    $payment_date_cash = $payment_date['cash'] ?? $data['payment_date'] ?? '';
                                                    $payment_date_cheque = $payment_date['cheque'] ?? $data['cheque_date'] ?? '';
                                                    $payment_date_utr = $payment_date['bank_transfer'] ?? $data['utr_date'] ?? '';
                                                    $payment_mode = $data['payment_mode'] ?? '';
                                                    $cheque_number = $data['cheque_number'] ? implode(',', json_decode($data['cheque_number'], true) ?: [$data['cheque_number']]) : '';
                                                    $bank_name = $data['bank_name'] ? implode(',', json_decode($data['bank_name'], true) ?: [$data['bank_name']]) : '';
                                                    $cheque_date = $data['cheque_date'] ? implode(',', json_decode($data['cheque_date'], true) ?: [$data['cheque_date']]) : '';
                                                    $utr_number = $data['utr_number'] ? implode(',', json_decode($data['utr_number'], true) ?: [$data['utr_number']]) : '';
                                                    $payment_details = $data['payment_details'] ? json_decode($data['payment_details'], true) : [];
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <button class="btn btn-info btn-sm view-details-btn"
                                                                data-member-id="<?= htmlspecialchars($data['member_id']) ?>"
                                                                data-payment-details='<?= htmlspecialchars(json_encode($payment_details)) ?>'>
                                                                View
                                                            </button>
                                                        </td>
                                                        <td><?= htmlspecialchars($data['member_id']) ?></td>
                                                        <td><?= htmlspecialchars($data['member_name']) ?></td>
                                                        <td><?= htmlspecialchars($data['sponsor_id']) ?></td>
                                                        <td>₹<?= number_format($data['direct_amount'], 2) ?></td>
                                                        <td>₹<?= number_format($data['total_group_amount'], 2) ?></td>
                                                        <td><?= htmlspecialchars($data['direct_percent']) ?>%</td>
                                                        <td>₹<?= number_format($data['direct_commission'], 2) ?></td>
                                                        <td>
                                                            <a href="#" target="_blank" class="level-commission-link" data-member-id="<?= htmlspecialchars($data['member_id']) ?>">
                                                                ₹<?= number_format($data['level_commission'], 2) ?>
                                                            </a>
                                                        </td>
                                                        <td>₹<?= number_format($data['total_commission'], 2) ?></td>
                                                        <td>₹<?= number_format($tds, 2) ?></td>
                                                        <td>₹<?= number_format($admin_charge, 2) ?></td>
                                                        <td>₹<?= number_format($final_amount, 2) ?></td>
                                                        <td><?= ucfirst($current_status) ?></td>
                                                        <td>
                                                            <?php if ($current_status === 'paid'): ?>
                                                                <select class="form-control" disabled>
                                                                    <option value="paid" selected>Paid</option>
                                                                </select>
                                                            <?php else: ?>
                                                                <select class="form-control payment-status" data-id="<?= htmlspecialchars($data['id']) ?>">
                                                                    <option value="unpaid" selected>Unpaid</option>
                                                                    <option value="paid">Paid</option>
                                                                </select>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($payment_date_cash) ?></td>
                                                        <td><?= htmlspecialchars($payment_date_cheque) ?></td>
                                                        <td><?= htmlspecialchars($payment_date_utr) ?></td>
                                                        <td><?= htmlspecialchars($payment_mode) ?></td>
                                                        <td><?= htmlspecialchars($utr_number) ?></td>
                                                        <td><?= htmlspecialchars($cheque_number) ?></td>
                                                        <td><?= htmlspecialchars($bank_name) ?></td>
                                                        <td><?= htmlspecialchars($cheque_date) ?></td>
                                                        <td><?= htmlspecialchars($data['remarks'] ?? '') ?></td>

                                                    </tr>
                                                    <tr class="details-row" id="details-<?= htmlspecialchars($data['member_id']) ?>" style="display: none;">
                                                        <td colspan="11">
                                                            <table class="table table-bordered" style="margin: 0;">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Product Name</th>
                                                                        <th>Amount</th>
                                                                        <th>Date</th>
                                                                        <th>Commission</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="details-table-<?= htmlspecialchars($data['member_id']) ?>">
                                                                    <!-- Rows will be populated dynamically via JavaScript -->
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>

                                        <!-- ADD THIS AFTER YOUR TABLE -->
                                        <script>
                                            $(document).ready(function() {
                                                // Level Commission Click Handler
                                                $('body').on('click', '.level-commission-link', function(e) {
                                                    e.preventDefault();
                                                    var memberId = $(this).data('member-id');
                                                    var fromDate = '<?= $from_date ?>';
                                                    var toDate = '<?= $to_date ?>';

                                                    window.location.href = 'level_commission_breakdown.php?member_id=' + memberId +
                                                        '&from_date=' + fromDate + '&to_date=' + toDate;
                                                });
                                            });
                                        </script>

                                        <!-- Bootstrap Modal for Payment Details -->
                                        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="paymentModalLabel">Enter Payment Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="paymentForm">
                                                            <input type="hidden" id="commission_ids" name="commission_ids">
                                                            <div class="form-group">
                                                                <label for="payment_mode">Payment Mode(s)</label>
                                                                <select class="form-control" id="payment_mode" name="payment_mode[]" multiple="multiple" required>
                                                                    <option value="cash">Cash</option>
                                                                    <option value="cheque">Cheque</option>
                                                                    <option value="bank_transfer">Bank Transfer</option>
                                                                </select>
                                                            </div>
                                                            <div id="payment_fields"></div>
                                                            <div class="form-group">
                                                                <label for="remarks">Remarks</label>
                                                                <textarea class="form-control" id="remarks" name="remarks" rows="4"></textarea>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary" id="savePaymentDetails">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <a href="monthlyclosing.php" class="btn btn-secondary">Back to Periods</a>
                            </div>

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
            let pendingIds = new Set();

            // Dynamically generate fields for payment modes
            $('#payment_mode').change(function() {
                var modes = $(this).val() || [];
                $('#payment_fields').empty();
                modes.forEach((mode, index) => {
                    var html = `<div class="form-group payment-section" data-mode="${mode}">`;
                    html += `<h6>Details for ${mode.charAt(0).toUpperCase() + mode.slice(1)}</h6>`;
                    html += `<label for="payment_date_${mode}_${index}">Payment Date</label>`;
                    html += `<input type="date" class="form-control" id="payment_date_${mode}_${index}" name="payment_date[${mode}]" required>`;
                    if (mode === 'cheque') {
                        html += `<label for="cheque_number_${mode}_${index}">Cheque Number</label>`;
                        html += `<input type="text" class="form-control" id="cheque_number_${mode}_${index}" name="cheque_number[${mode}]" required>`;
                        html += `<label for="bank_name_${mode}_${index}">Bank Name</label>`;
                        html += `<input type="text" class="form-control" id="bank_name_${mode}_${index}" name="bank_name[${mode}]" required>`;
                        html += `<label for="cheque_date_${mode}_${index}">Cheque Date</label>`;
                        html += `<input type="date" class="form-control" id="cheque_date_${mode}_${index}" name="cheque_date[${mode}]" required>`;
                    } else if (mode === 'bank_transfer') {
                        html += `<label for="utr_number_${mode}_${index}">UTR Number</label>`;
                        html += `<input type="text" class="form-control" id="utr_number_${mode}_${index}" name="utr_number[${mode}]" required>`;
                        html += `<label for="utr_date_${mode}_${index}">UTR Date</label>`;
                        html += `<input type="date" class="form-control" id="utr_date_${mode}_${index}" name="utr_date[${mode}]" required>`;
                    }
                    html += `</div>`;
                    $('#payment_fields').append(html);
                });
            });

            // Handle payment status change
            $('.payment-status').change(function() {
                var commissionId = $(this).data('id');
                var newStatus = $(this).val();
                if (newStatus === 'paid') {
                    pendingIds.add(commissionId);
                    $('#commission_ids').val(Array.from(pendingIds).join(','));
                    $('#paymentModal').modal('show');
                    $('#paymentForm')[0].reset();
                    $('#payment_fields').empty();
                    $('#payment_mode').val(null).trigger('change');
                    $('#payment_mode').prop('required', true);
                } else {
                    if (pendingIds.has(commissionId)) {
                        pendingIds.delete(commissionId);
                    }
                    updatePaymentStatus([commissionId], newStatus);
                }
            });

            // Save payment details
            $('#savePaymentDetails').click(function() {
                var commissionIds = $('#commission_ids').val().split(',').filter(id => id);
                var modes = $('#payment_mode').val() || [];
                var remarks = $('#remarks').val();

                // Validate required fields
                if (!modes.length) {
                    alert('Please select at least one payment mode.');
                    return;
                }
                var invalid = false;
                modes.forEach(mode => {
                    $(`.payment-section[data-mode="${mode}"] input[required]`).each(function() {
                        if (!$(this).val()) {
                            invalid = true;
                            alert(`Please fill all required fields for ${mode}.`);
                            return false;
                        }
                    });
                });
                if (invalid) return;

                // Collect payment data
                var paymentData = {
                    payment_date: {},
                    cheque_number: {},
                    bank_name: {},
                    cheque_date: {},
                    utr_number: {},
                    utr_date: {}
                };
                modes.forEach(mode => {
                    paymentData.payment_date[mode] = $(`input[name="payment_date[${mode}]"]`).val();
                    if (mode === 'cheque') {
                        paymentData.cheque_number[mode] = $(`input[name="cheque_number[${mode}]"]`).val();
                        paymentData.bank_name[mode] = $(`input[name="bank_name[${mode}]"]`).val();
                        paymentData.cheque_date[mode] = $(`input[name="cheque_date[${mode}]"]`).val();
                    } else if (mode === 'bank_transfer') {
                        paymentData.utr_number[mode] = $(`input[name="utr_number[${mode}]"]`).val();
                        paymentData.utr_date[mode] = $(`input[name="utr_date[${mode}]"]`).val();
                    }
                });

                updatePaymentStatus(commissionIds, 'paid', {
                    payment_mode: modes.join(','),
                    payment_date: JSON.stringify(paymentData.payment_date),
                    cheque_number: JSON.stringify(paymentData.cheque_number),
                    bank_name: JSON.stringify(paymentData.bank_name),
                    cheque_date: JSON.stringify(paymentData.cheque_date),
                    utr_number: JSON.stringify(paymentData.utr_number),
                    utr_date: JSON.stringify(paymentData.utr_date),
                    remarks: remarks
                });
                pendingIds.clear();
                $('#paymentModal').modal('hide');
            });

            function updatePaymentStatus(commissionIds, status, additionalData = {}) {
                $.ajax({
                    url: 'update_payment_status.php',
                    type: 'POST',
                    data: {
                        ids: commissionIds,
                        payment_status: status,
                        ...additionalData
                    },
                    success: function(response) {
                        if (response === 'Success') {
                            alert('Payment status updated successfully');
                            location.reload();
                        } else {
                            alert('Error updating payment status: ' + response);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating payment status');
                        console.log('AJAX Error:', error);
                    }
                });
            }
        });
    </script>



    <script>
        function toggleSelf(id) {
            // Debugging: Log the ID to ensure it's being passed
            console.log('Toggling self details for ID: ' + id);

            // Hide all other self-details rows
            $('.self-details').not('.self-details-' + id).hide();

            // Toggle the specific row
            var $row = $('.self-details-' + id);
            if ($row.is(':visible')) {
                $row.hide();
            } else {
                $row.show();
            }
        }
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle View Details Button Click
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const memberId = this.getAttribute('data-member-id');
                    const paymentDetails = JSON.parse(this.getAttribute('data-payment-details'));
                    const detailsRow = document.querySelector(`#details-${memberId}`);
                    const detailsTableBody = document.querySelector(`#details-table-${memberId}`);

                    // Toggle visibility of the details row
                    const isVisible = detailsRow.style.display === 'table-row';
                    document.querySelectorAll('.details-row').forEach(row => row.style.display = 'none'); // Hide all other details rows
                    detailsRow.style.display = isVisible ? 'none' : 'table-row';

                    // Clear existing table rows
                    detailsTableBody.innerHTML = '';

                    // Check if payment details exist
                    if (paymentDetails && typeof paymentDetails === 'object') {
                        // Assuming payment_details is an object with product keys (e.g., "C-40")
                        Object.keys(paymentDetails).forEach(productName => {
                            paymentDetails[productName].forEach(detail => {
                                // Skip entries with null amount or commission
                                if (detail.amount === null || detail.commission === null) return;

                                const row = document.createElement('tr');
                                row.innerHTML = `
                            <td>${productName}</td>
                            <td>₹${Number(detail.amount).toFixed(2)}</td>
                            <td>${detail.date}</td>
                            <td>₹${Number(detail.commission).toFixed(2)}</td>
                        `;
                                detailsTableBody.appendChild(row);
                            });
                        });

                        // If no valid details were added, show a message
                        if (detailsTableBody.children.length === 0) {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td colspan="4" class="text-center">No self business details available</td>`;
                            detailsTableBody.appendChild(row);
                        }
                    } else {
                        // Display message if no payment details
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="4" class="text-center">No self business details available</td>`;
                        detailsTableBody.appendChild(row);
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