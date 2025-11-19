<?php
session_start();
include_once "connectdb.php";


// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

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
    <style>
        .container {
            background: #fff;
            padding: 20px;
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            margin: 20px;
        }

        .result-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .payment-row td {
            padding: 15px 8px;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
    </style>

    <style>
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 5px;
            width: 100px;
            height: 25px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .print-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 2rem;
            width: 100px;
            height: 25px;
        }

        .print-btn:hover {
            background-color: #0056b3;
        }
    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <!-- side panel header -->
                <?php include 'account-headersidepanel.php'; ?>

                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="mt-2 ml-2">
                                            <h2>Re Payment Details</h2>
                                            <hr>
                                            <div class="clr"></div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <b>Search by Invoice No.</b>
                                                    <input name="invoicesearch" type="text" id="printinvoice" class="form-control mt-2">
                                                </div>
                                                <div class="col-md-2 pt-4">
                                                    <input type="submit" value="Search" id="searchBtn" class="btn btn-dark mt-2" style="width:100px;">
                                                </div>
                                            </div>

                                            <div id="resultContainer" class="result-box" style="display: none;">
                                                <!-- Customer Details Table -->
                                                <table id="customerTable">
                                                    <tr>
                                                        <th>Customer Name</th>
                                                        <td id="custName"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Product Name</th>
                                                        <td id="prodName"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Net Amount</th>
                                                        <td id="netAmount"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Previous Paid Amount</th>
                                                        <td id="prevPaid"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Current Due Amount</th>
                                                        <td id="dueAmount"></td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Enter Payment Amount</th>
                                                        <td>
                                                            <input type="number" id="newPayment" class="form-control" style="width: 200px;">
                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Enter Voucher Number</th>
                                                        <td>
                                                            <input type="text" id="voucherNumber" class="form-control" style="width: 200px;">
                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Payment Date</th>
                                                        <td>
                                                            <input type="date" id="payment_date" class="form-control" style="width: 200px;" onchange="formatPaymentDate()">

                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Payment Mode</th>
                                                        <td>
                                                            <select class="form-control" name="payment_mode" id="payment_mode" onchange="showPaymentDetails()" style="width: 200px;">
                                                                <option value="">Select Payment Mode</option>
                                                                <option value="cash">Cash</option>
                                                                <option value="cheque">Cheque</option>
                                                                <option value="bank_transfer">Bank Transfer</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr id="chequeDetails" class="payment-details" style="display: none;">
                                                        <th>Cheque Details</th>
                                                        <td>
                                                            <input type="text" id="cheque_number" class="form-control" placeholder="Cheque Number" style="width: 200px; margin-bottom: 5px;">
                                                            <input type="text" id="bank_name" class="form-control" placeholder="Bank Name" style="width: 200px; margin-bottom: 5px;">
                                                            <input type="date" id="cheque_date" class="form-control" style="width: 200px;">
                                                        </td>
                                                    </tr>
                                                    <tr id="bankTransferDetails" class="payment-details" style="display: none;">
                                                        <th>Bank Transfer Details</th>
                                                        <td>
                                                            <input type="text" id="neft_payment" class="form-control bank-transfer-field" placeholder="NEFT Reference Number" style="width: 200px; margin-bottom: 5px;">
                                                            <input type="text" id="rtgs_payment" class="form-control bank-transfer-field" placeholder="RTGS Reference Number" style="width: 200px; margin-bottom: 5px;">
                                                            <input type="text" id="utr_number" class="form-control bank-transfer-field" placeholder="UTR Number" style="width: 200px;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Updated Due Amount</th>
                                                        <td id="updatedDue"></td>
                                                    </tr>
                                                </table>

                                                <button id="submitPayment" class="btn btn-dark">Submit Payment</button>

                                                <!-- Payment History Table -->
                                                <h3>Payment History</h3>
                                                <div style="overflow-x: scroll; height:70vh;width:95%!important;">
                                                    <table id="paymentHistory">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr No.</th>
                                                                <th>Invoice ID</th>
                                                                <th>Customer Name</th>
                                                                <th>Plot Name</th>
                                                                <th>Net Amount</th>
                                                                <th>Payment Mode</th>
                                                                <th>Amount Paid</th>
                                                                <th>Payment Date</th>
                                                                <th>Due Amount</th>
                                                                <th>Cheque Number</th>
                                                                <th>Bank Name</th>
                                                                <th>Cheque Date</th>
                                                                <th>UTR Number</th>
                                                                <th>NEFT Reference</th>
                                                                <th>RTGS Reference</th>
                                                                <th>Voucher No.</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="paymentHistoryBody">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="otpModalLabel">Delete Confirmation</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>An OTP has been sent to your email for verification.</p>
                                                            <div class="mb-3">
                                                                <label for="otpInput" class="form-label">Enter OTP:</label>
                                                                <input type="text" class="form-control" id="otpInput" placeholder="Enter 6-digit OTP">
                                                            </div>
                                                            <div id="otpError" class="text-danger" style="display: none;"></div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="button" class="btn btn-danger" id="confirmDelete">Confirm Delete</button>
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
                </div>

            </div>
        </div>
    </div>

    <!-- footer -->


    </div>

    <?php include 'account-footer.php'; ?>
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
        let currentCustomer = null;
        let pendingDeleteRowId = null;
        let pendingDeleteInvoiceId = null;

        // Function to show loading overlay
        function showLoadingOverlay() {
            let overlay = document.getElementById('loadingOverlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'loadingOverlay';
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                overlay.style.display = 'flex';
                overlay.style.justifyContent = 'center';
                overlay.style.alignItems = 'center';
                overlay.style.zIndex = '9999';
                overlay.innerHTML = `
            <div style="text-align: center;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p style="margin-top: 10px; font-size: 18px; color: #333;">Please Wait...</p>
            </div>
        `;
                document.body.appendChild(overlay);
            }
            overlay.style.display = 'flex';
        }

        // Function to hide loading overlay
        function hideLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        function showPaymentDetails() {
            const paymentMode = document.getElementById('payment_mode').value;
            document.getElementById('chequeDetails').style.display = paymentMode === 'cheque' ? 'table-row' : 'none';
            document.getElementById('bankTransferDetails').style.display = paymentMode === 'bank_transfer' ? 'table-row' : 'none';

            if (paymentMode !== 'cheque') {
                document.getElementById('cheque_number').value = '';
                document.getElementById('bank_name').value = '';
                document.getElementById('cheque_date').value = '';
            }

            if (paymentMode !== 'bank_transfer') {
                document.getElementById('neft_payment').value = '';
                document.getElementById('rtgs_payment').value = '';
                document.getElementById('utr_number').value = '';
                document.getElementById('neft_payment').style.display = 'block';
                document.getElementById('rtgs_payment').style.display = 'block';
                document.getElementById('utr_number').style.display = 'block';
            }
        }

        function setupBankTransferFieldListeners() {
            const fields = ['neft_payment', 'rtgs_payment', 'utr_number'];
            fields.forEach(field => {
                document.getElementById(field).addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        fields.forEach(otherField => {
                            if (otherField !== field) {
                                document.getElementById(otherField).style.display = 'none';
                            }
                        });
                    } else {
                        fields.forEach(otherField => {
                            document.getElementById(otherField).style.display = 'block';
                        });
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', setupBankTransferFieldListeners);

        function deletePayment(rowId, invoiceId) {
            if (!confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
                return;
            }

            pendingDeleteRowId = rowId;
            pendingDeleteInvoiceId = invoiceId;

            showLoadingOverlay();
            fetch('payment_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=send_otp&row_id=${encodeURIComponent(rowId)}&invoice_id=${encodeURIComponent(invoiceId)}`
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingOverlay();
                    if (data.success) {
                        const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                        otpModal.show();
                    } else {
                        alert('Failed to send OTP: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error:', error);
                    alert('An error occurred while sending OTP');
                });
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            const otp = document.getElementById('otpInput').value.trim();

            if (!otp) {
                document.getElementById('otpError').textContent = 'Please enter OTP';
                document.getElementById('otpError').style.display = 'block';
                return;
            }

            showLoadingOverlay();
            fetch('payment_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=verify_otp_and_delete&row_id=${encodeURIComponent(pendingDeleteRowId)}&invoice_id=${encodeURIComponent(pendingDeleteInvoiceId)}&otp=${encodeURIComponent(otp)}`
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingOverlay();
                    if (data.success) {
                        alert('Payment deleted successfully!');
                        const otpModal = bootstrap.Modal.getInstance(document.getElementById('otpModal'));
                        otpModal.hide();
                        document.getElementById('otpInput').value = '';
                        document.getElementById('otpError').style.display = 'none';
                        document.getElementById('searchBtn').click();
                    } else {
                        document.getElementById('otpError').textContent = data.error || 'Invalid OTP';
                        document.getElementById('otpError').style.display = 'block';
                    }
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error:', error);
                    alert('An error occurred while verifying OTP');
                });
        });

        document.getElementById('otpModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('otpInput').value = '';
            document.getElementById('otpError').style.display = 'none';
            pendingDeleteRowId = null;
            pendingDeleteInvoiceId = null;
        });

        document.getElementById('searchBtn').addEventListener('click', function() {
            const invoiceId = document.getElementById('printinvoice').value.trim();

            if (!invoiceId) {
                alert('Please enter an invoice number');
                return;
            }

            showLoadingOverlay();
            fetch('payment_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=search&invoice_id=${encodeURIComponent(invoiceId)}`
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingOverlay();
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    currentCustomer = data.customer;

                    const netAmount = parseFloat(data.customer.net_amount) || 0;
                    const payAmount = parseFloat(data.customer.payamount) || 0;
                    const dueAmount = parseFloat(data.customer.due_amount) || 0;

                    document.getElementById('custName').textContent = data.customer.customer_name || 'N/A';
                    document.getElementById('prodName').textContent = data.customer.productname || 'N/A';
                    document.getElementById('netAmount').textContent = netAmount.toFixed(2);
                    document.getElementById('prevPaid').textContent = payAmount.toFixed(2);
                    document.getElementById('dueAmount').textContent = dueAmount.toFixed(2);
                    document.getElementById('updatedDue').textContent = dueAmount.toFixed(2);
                    document.getElementById('newPayment').value = '';
                    document.getElementById('voucherNumber').value = '';
                    document.getElementById('payment_mode').value = '';
                    document.getElementById('neft_payment').value = '';
                    document.getElementById('rtgs_payment').value = '';
                    document.getElementById('utr_number').value = '';
                    showPaymentDetails();

                    const paymentBody = document.getElementById('paymentHistoryBody');
                    paymentBody.innerHTML = '';
                    if (data.payments && data.payments.length > 0) {
                        data.payments.forEach((payment, index) => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${payment.invoice_id || 'N/A'}</td>
                        <td>${payment.customer_name || 'N/A'}</td>
                        <td>${payment.productname || 'N/A'}</td>
                        <td>${(parseFloat(payment.net_amount) || 0).toFixed(2)}</td>
                        <td>${payment.payment_mode || 'N/A'}</td>
                        <td>${(parseFloat(payment.payamount) || 0).toFixed(2)}</td>
                        <td>${payment.created_date ? new Date(payment.created_date).toLocaleDateString('en-GB').replace(/\//g, '-') : 'N/A'}</td>
                        <td>${(parseFloat(payment.due_amount) || 0).toFixed(2)}</td>
                        <td>${payment.cheque_number || 'N/A'}</td>
                        <td>${payment.bank_name || 'N/A'}</td>
                        <td>${payment.cheque_date || 'N/A'}</td>
                        <td>${payment.utr_number || 'N/A'}</td>
                        <td>${payment.neft_payment || 'N/A'}</td>
                        <td>${payment.rtgs_payment || 'N/A'}</td>
                        <td>${payment.voucher_number || 'N/A'}</td>
                        <td>
                            <select class="form-control status-dropdown" data-id="${payment.id}">
                                <option value="pending" ${payment.client_payment_status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="confirmed" ${payment.client_payment_status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                            </select>
                            <div class="btn-group mt-2">
                                <button class="btn btn-primary print-btn" 
                                    data-row-id="${payment.id}"
                                    data-invoice-id="${payment.invoice_id}"
                                    data-member-id="${payment.member_id || currentCustomer.member_id}">
                                    Print
                                </button>                           
                            </div>
                        </td>
                    `;

                            paymentBody.appendChild(row);
                        });

                        document.querySelectorAll('.print-btn').forEach(button => {
                            button.addEventListener('click', function() {
                                const rowId = this.getAttribute('data-row-id');
                                const invoiceId = this.getAttribute('data-invoice-id');
                                const memberId = this.getAttribute('data-member-id');

                                window.location.href = `newemiSaleinvoice.php?invoice_id=${encodeURIComponent(invoiceId)}&member_id=${encodeURIComponent(memberId)}&row_id=${encodeURIComponent(rowId)}`;
                            });
                        });

                        $(document).ready(function() {
                            $('#paymentHistory').DataTable({
                                "destroy": true,
                                "responsive": true,
                                "ordering": true,
                                "paging": true,
                                "searching": true,
                                "info": true
                            });
                        });

                        document.querySelectorAll('.status-dropdown').forEach(select => {
                            select.addEventListener('change', function() {
                                const newStatus = this.value;
                                const paymentId = this.getAttribute('data-id');

                                showLoadingOverlay();
                                fetch('payment_process.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: `action=update_status&id=${encodeURIComponent(paymentId)}&status=${encodeURIComponent(newStatus)}`
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        hideLoadingOverlay();
                                        if (data.success) {
                                            alert('Status updated successfully');
                                        } else {
                                            alert('Error: ' + (data.error || 'Unknown error'));
                                        }
                                    })
                                    .catch(err => {
                                        hideLoadingOverlay();
                                        console.error('Error updating status:', err);
                                        alert('Failed to update status');
                                    });
                            });
                        });
                    } else {
                        paymentBody.innerHTML = '<tr><td colspan="17">No payment history found</td></tr>';
                    }

                    document.getElementById('resultContainer').style.display = 'block';
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error fetching data:', error);
                    alert('An error occurred while fetching data');
                });
        });

        document.getElementById('newPayment').addEventListener('input', function() {
            const newPayment = parseFloat(this.value) || 0;
            const currentDue = parseFloat(currentCustomer?.due_amount) || 0;
            const updatedDue = currentDue - newPayment;
            document.getElementById('updatedDue').textContent = updatedDue >= 0 ? updatedDue.toFixed(2) : '0.00';
        });

        document.getElementById('submitPayment').addEventListener('click', function() {
            if (!currentCustomer || !currentCustomer.invoice_id) {
                alert('Please search for an invoice first');
                return;
            }

            const newPayment = parseFloat(document.getElementById('newPayment').value) || 0;
            const updatedDue = parseFloat(document.getElementById('updatedDue').textContent);
            const paymentMode = document.getElementById('payment_mode').value;
            const paymentDate = document.getElementById('payment_date').value;
            const voucherNumber = document.getElementById('voucherNumber').value.trim();
            const utrNumber = document.getElementById('utr_number').value.trim();
            const neftPayment = document.getElementById('neft_payment').value.trim();
            const rtgsPayment = document.getElementById('rtgs_payment').value.trim();
            const chequeNumber = document.getElementById('cheque_number').value.trim();
            const bankName = document.getElementById('bank_name').value.trim();
            const chequeDate = document.getElementById('cheque_date').value;

            if (!newPayment || newPayment <= 0) {
                alert('Please enter a valid payment amount');
                return;
            }

            if (!paymentMode) {
                alert('Please select a payment mode');
                return;
            }

            if (!paymentDate) {
                alert('Please select a payment date');
                return;
            }

            if (paymentMode === 'bank_transfer') {
                const filledFields = [neftPayment, rtgsPayment, utrNumber].filter(val => val !== '').length;
                if (filledFields === 0) {
                    alert('Please provide NEFT, RTGS, or UTR number for bank transfer');
                    return;
                }
                if (filledFields > 1) {
                    alert('Please provide only one of NEFT, RTGS, or UTR number');
                    return;
                }
            }

            if (paymentMode === 'cheque') {
                if (!chequeNumber || !bankName || !chequeDate) {
                    alert('Please provide cheque number, bank name, and cheque date for cheque payment');
                    return;
                }
            }

            showLoadingOverlay();
            const checkData = new URLSearchParams();
            checkData.append('action', 'check_duplicate');
            checkData.append('invoice_id', currentCustomer.invoice_id);
            if (voucherNumber) {
                checkData.append('voucher_number', voucherNumber);
            }
            if (paymentMode === 'bank_transfer') {
                if (neftPayment) {
                    checkData.append('neft_payment', neftPayment);
                } else if (rtgsPayment) {
                    checkData.append('rtgs_payment', rtgsPayment);
                } else if (utrNumber) {
                    checkData.append('utr_number', utrNumber);
                }
            } else if (paymentMode === 'cheque' && chequeNumber && bankName) {
                checkData.append('cheque_number', chequeNumber);
                checkData.append('bank_name', bankName);
            }

            fetch('payment_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: checkData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.exists) {
                            hideLoadingOverlay();
                            if (data.field === 'voucher_number') {
                                alert('This voucher number already exists for this invoice.');
                            } else if (neftPayment) {
                                alert('This NEFT reference number already exists for this invoice.');
                            } else if (rtgsPayment) {
                                alert('This RTGS reference number already exists for this invoice.');
                            } else if (utrNumber) {
                                alert('This UTR number already exists for this invoice.');
                            } else {
                                alert('This cheque number with the same bank already exists for this invoice.');
                            }
                            return;
                        }

                        const formData = new URLSearchParams();
                        formData.append('action', 'submit_payment');
                        formData.append('invoice_id', currentCustomer.invoice_id);
                        formData.append('member_id', currentCustomer.member_id);
                        formData.append('customer_name', currentCustomer.customer_name);
                        formData.append('productname', currentCustomer.productname);
                        formData.append('net_amount', currentCustomer.net_amount);
                        formData.append('payment_mode', paymentMode);
                        formData.append('payamount', newPayment);
                        formData.append('due_amount', updatedDue);
                        formData.append('payment_date', paymentDate);
                        if (voucherNumber) {
                            formData.append('voucher_number', voucherNumber);
                        }
                        if (paymentMode === 'cheque') {
                            formData.append('cheque_number', chequeNumber);
                            formData.append('bank_name', bankName);
                            formData.append('cheque_date', chequeDate);
                        } else if (paymentMode === 'bank_transfer') {
                            if (neftPayment) {
                                formData.append('neft_payment', neftPayment);
                            } else if (rtgsPayment) {
                                formData.append('rtgs_payment', rtgsPayment);
                            } else if (utrNumber) {
                                formData.append('utr_number', utrNumber);
                            }
                        }

                        return fetch('payment_process.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok: ' + response.statusText);
                                }
                                return response.text();
                            })
                            .then(text => {
                                hideLoadingOverlay();
                                try {
                                    const data = JSON.parse(text);
                                    if (data.success) {
                                        alert('Payment recorded successfully!\nNew Due Amount: ' + updatedDue.toFixed(2));
                                        currentCustomer.payamount = (parseFloat(currentCustomer.payamount) || 0) + newPayment;
                                        currentCustomer.due_amount = updatedDue;
                                        document.getElementById('prevPaid').textContent = currentCustomer.payamount.toFixed(2);
                                        document.getElementById('dueAmount').textContent = updatedDue.toFixed(2);
                                        document.getElementById('newPayment').value = '';
                                        document.getElementById('voucherNumber').value = '';
                                        document.getElementById('payment_mode').value = '';
                                        document.getElementById('neft_payment').value = '';
                                        document.getElementById('rtgs_payment').value = '';
                                        document.getElementById('utr_number').value = '';
                                        showPaymentDetails();
                                        document.getElementById('searchBtn').click();
                                    } else {
                                        alert('Failed to record payment: ' + (data.error || 'Unknown error'));
                                    }
                                } catch (e) {
                                    console.error('Error parsing submit_payment response:', e, text);
                                    alert('Invalid response from server during payment submission');
                                }
                            });
                    } catch (e) {
                        hideLoadingOverlay();
                        console.error('Error parsing check_duplicate response:', e, text);
                        alert('Invalid response from server during duplicate check');
                    }
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error checking duplicates:', error);
                    alert('An error occurred while checking duplicates: ' + error.message);
                });
        });
    </script>


    <script>
        // Pad single-digit numbers with a leading zero
        function padZero(num) {
            return num < 10 ? '0' + num : num;
        }

        function formatPaymentDate() {
            const input = document.getElementById('payment_date');
            const display = document.getElementById('formatted_date');

            const dateValue = input.value;
            if (!dateValue) {
                display.textContent = '';
                return;
            }

            const date = new Date(dateValue);
            const day = padZero(date.getDate());
            const month = padZero(date.getMonth() + 1); // Months are 0-indexed
            const year = date.getFullYear();

            display.textContent = `${day}-${month}-${year}`;
        }

        // Set default date to today and format it on page load
        window.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('payment_date');
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = padZero(today.getMonth() + 1);
            const dd = padZero(today.getDate());

            input.value = `${yyyy}-${mm}-${dd}`; // Format for input type="date"
            formatPaymentDate(); // Show formatted date on load
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