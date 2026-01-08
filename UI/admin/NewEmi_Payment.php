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

        /* //for invoice  */
        .invoice-search-container {
            position: relative;
        }

        .invoice-dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 350px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            margin-top: 0;
        }

        .invoice-dropdown-list.show {
            display: block;
        }

        .invoice-dropdown-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .invoice-dropdown-item:last-child {
            border-bottom: none;
        }

        .invoice-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .invoice-dropdown-item .invoice-number {
            font-weight: 600;
            color: #212529;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .invoice-dropdown-item .customer-info {
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .invoice-dropdown-item .invoice-date {
            color: #adb5bd;
            font-size: 12px;
        }

        .invoice-no-results {
            padding: 15px;
            color: #6c757d;
            text-align: center;
            font-size: 14px;
        }
    </style>


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
                        <div class="col-md-12 stretch-card">
                            <div class="card">
                                <div class="row justify-content-center">
                                    <div class="col-md-12">
                                        <div class="mt-2 ml-2">
                                            <h2>Re Payment Details</h2>
                                            <hr>
                                            <div class="clr"></div>

                                            <div class="row">
                                                <div class="col-md-3 invoice-search-container">
                                                    <b>Search by Invoice No.</b>
                                                    <input name="invoicesearch" type="text" id="printinvoice"
                                                        class="form-control mt-2"
                                                        placeholder="Type customer name or invoice ID..."
                                                        autocomplete="off">
                                                    <!-- THIS IS THE MISSING DROPDOWN DIV -->
                                                    <div class="invoice-dropdown-list" id="invoice_dropdown"></div>
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
                                                        <th>CashBack</th>
                                                        <td id="cashback"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Admission Paid Amount</th>
                                                        <td id="admission"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Enrollment Paid Amount</th>
                                                        <td id="enroll"></td>
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
                                                        <th>Enter Receipt Number</th>
                                                        <td>
                                                            <input type="text" id="receipt" class="form-control" style="width: 200px;" placeholder="Receipt Number">
                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Enter Payment Amount</th>
                                                        <td>
                                                            <input type="number" id="newPayment" class="form-control" style="width: 200px;" placeholder="Enter Payment Amount">
                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row d-none">
                                                        <th>Enter Voucher Number</th>
                                                        <td>
                                                            <input type="text" id="voucherNumber" class="form-control" style="width: 200px;" placeholder="Enter Voucher">
                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Payment Date</th>
                                                        <td>
                                                            <input type="date" id="payment_date" class="form-control" style="width: 200px;" onchange="formatPaymentDate()">

                                                        </td>
                                                    </tr>
                                                    <tr class="payment-row">
                                                        <th>Payment Type</th>
                                                        <td class='d-flex'>
                                                            <select class="form-control col-3" name="payment_type" id="payment_type"  style="width: 200px;">
                                                                <option value="">Select Payment Type</option>
                                                                <option value="enroll" id="enrolld">Enrollment charge</option>
                                                                <option value="allot">Allotment</option>
                                                                <!-- <option value="bank_transfer">Bank Transfer</option> -->
                                                            </select>
                                                        
                                                   
                      
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
                                                    <tr id="Emiblock" class='d-none'>
                                                        <th>Select EMI</th>
                                                        <td>
                                                                     <div class="form-group col-md-4" id="emidiv">
                          <!-- <label>EMI Months:</label> -->
                          <select class="form-control" name="emi_month" id="emi_month" onchange="calculateEMI()" >
                            <option value="">Select EMI Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="18">18 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                            <option value="54">54 Months</option>
                          </select>
                        </div>
                                                        </td>
                                                    </tr>
                                                    <tr id="emi_report" class='d-none'>
                                                        <th>Your Emi</th>
                                                        <td >
                                                            
                                                              <div class="col-md-5">
                          <!-- EMI Calculation Report -->
                          <div  class="emi-report" >
                            <label>EMI Calculation Report:</label>
                            <div id="emi_calculation" >
                              <p>EMI Amount per Month: <span id="emi_amount">0.00</span></p>
                              <p>Due Amount: <span id="emi_due_amount">0.00</span></p>
                              <table>
                                <thead>
                                  <tr>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                  </tr>
                                </thead>
                                <tbody id="emi_schedule"></tbody>
                              </table>
                            </div>
                          </div>
                        </div>
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
                                                                <th>Receipt number </th>
                                                                <th>Invoice ID</th>
                                                                <th>Customer Name</th>
                                                                <th>Plot Name</th>
                                                                <th>Net Amount</th>
                                                                <th>Payment Mode</th>
                                                                <th>Amount Paid</th>
                                                                <th>Admission Amount</th>
                                                                <th>Enrollment Amount</th>
                                                                <th>CashBack Amount</th>
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

                                                <div style="overflow-x: scroll; height:30vh;width:95%!important;">
                                                    <table id="admissionpaymentHistory">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr No.</th>
                                                                <th>Receipt number </th>
                                                                <th>Invoice ID</th>
                                                                <th>Customer Name</th>
                                                                <th>Plot Name</th>
                                                                <th>Net Amount</th>
                                                                <th>Payment Mode</th>
                                                                <th>Amount Paid</th>
                                                                <th>Admission Amount</th>
                                                                <th>Enrollment Amount</th> 
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
                                                        <tbody id="admissionpaymentHistoryBody">
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

    <!-- jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>



    <!-- for invoice search -->
    <script>
        // Invoice Search Script - Standalone
        (function() {
            const invoiceInput = document.getElementById('printinvoice');
            const invoiceDropdown = document.getElementById('invoice_dropdown');
            let invoiceSearchTimeout;

            if (!invoiceInput || !invoiceDropdown) {
                console.error('Invoice search elements not found');
                return;
            }

            // Search invoices as user types
            invoiceInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();

                clearTimeout(invoiceSearchTimeout);

                if (searchTerm.length < 2) {
                    invoiceDropdown.classList.remove('show');
                    return;
                }

                // Debounce search (300ms delay)
                invoiceSearchTimeout = setTimeout(() => {
                    searchInvoices(searchTerm);
                }, 300);
            });

            // Search function
            function searchInvoices(term) {
                fetch('search_invoices.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'search=' + encodeURIComponent(term)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            console.error('Error:', data.error);
                            invoiceDropdown.innerHTML = '<div class="invoice-no-results">Error: ' + data.error + '</div>';
                            invoiceDropdown.classList.add('show');
                            return;
                        }
                        displayInvoiceResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        invoiceDropdown.innerHTML = '<div class="invoice-no-results">Error loading results</div>';
                        invoiceDropdown.classList.add('show');
                    });
            }

            // Display search results
            function displayInvoiceResults(invoices) {
                if (!invoices || invoices.length === 0) {
                    invoiceDropdown.innerHTML = '<div class="invoice-no-results">No invoices found</div>';
                    invoiceDropdown.classList.add('show');
                    return;
                }

                let html = '';
                invoices.forEach(invoice => {
                    html += `
        <div class="invoice-dropdown-item" data-invoice="${invoice.invoice_id}">
          <div class="invoice-number">Invoice: ${invoice.invoice_id}</div>
          <div class="customer-info">Customer: ${invoice.customer_name || 'N/A'}</div>
          <div class="product-info">Product: ${invoice.search_productname || 'N/A'}</div>
          ${invoice.invoice_date ? `<div class="invoice-date">Date: ${invoice.invoice_date}</div>` : ''}
        </div>
      `;
                });

                invoiceDropdown.innerHTML = html;
                invoiceDropdown.classList.add('show');

                // Add click handlers to dropdown items
                document.querySelectorAll('.invoice-dropdown-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectInvoice(this);
                    });
                });
            }

            // Select invoice from dropdown
            function selectInvoice(element) {
                const invoiceId = element.getAttribute('data-invoice');
                invoiceInput.value = invoiceId;
                invoiceDropdown.classList.remove('show');

                // Optional: Trigger change event if you have listeners
                const event = new Event('change', {
                    bubbles: true
                });
                invoiceInput.dispatchEvent(event);
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.invoice-search-container')) {
                    invoiceDropdown.classList.remove('show');
                }
            });

            // Show dropdown when focusing on input if it has value
            invoiceInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2) {
                    searchInvoices(this.value.trim());
                }
            });

            // Prevent form submission on Enter key in search field
            invoiceInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const visibleItems = invoiceDropdown.querySelectorAll('.invoice-dropdown-item');
                    if (visibleItems.length > 0) {
                        selectInvoice(visibleItems[0]);
                    }
                }
            });

        })();

       
    </script>

<script>

     $(document).on("change","#payment_type",function()
    {
        var type=$('#payment_type').val();
        var enroll_fee=$('#enroll').text();
        var prevPaidfee=$('#prevPaid').text();

        // console.log(type);
        // console.log(enroll_fee);
        // console.log(prevPaidfee);
        
        
        
        if(type=='allot'&& enroll_fee==0 && prevPaidfee==0)
        {
           $("#Emiblock").removeClass('d-none');
           //calculateEMI();
            
        }
        else if(type=='allot'&& enroll_fee==15000 && prevPaidfee==0)
        {
             $("#Emiblock").removeClass('d-none');
            //calculateEMI();
        }
        else if(type=='enroll')
        {
             $("#Emiblock").addClass('d-none');
            
        }

    //   $("#payment_mode").click(function(){
    //     calculateEMI();

    //   });
    })

    function calculateEMI() {
        var type=$('#payment_type').val();
        var enroll_fee=$('#enroll').text();
        var prevPaidfee=$('#prevPaid').text();
        var dueAmount=$('#dueAmount').text();

      const emiMonths = parseInt(document.getElementById('emi_month').value) || 0;
      const netAmount = parseFloat($('#netAmount').text()) || 0;
      const paymentMode = $('#payment_mode').val();

      let paidAmount = 0;

      if (paymentMode === 'cash') {
        paidAmount = parseFloat(document.getElementById('newPayment').value) || 0;
      } else if (paymentMode === 'cheque') {
        paidAmount = parseFloat(document.getElementById('newPayment').value) || 0;
      } else if (paymentMode === 'bank_transfer') {
        paidAmount = parseFloat(document.getElementById('newPayment').value) || 0;
      }
      else
        {
        alert("please select payment mode!");
       
      }

      if(type=='allot' && enroll_fee==0 && prevPaidfee==0)
        {
             dueAmount = netAmount - paidAmount;
        }
        if(type=='allot'&& enroll_fee==15000 && prevPaidfee==0)
        {
         dueAmount = netAmount - (paidAmount+15000);
        }
      // dueAmount = netAmount - paidAmount;
      const emiReport = document.getElementById('emi_report');
      const emiAmountSpan = document.getElementById('emi_amount');
      const emiDueAmountSpan = document.getElementById('emi_due_amount');
      const emiSchedule = document.getElementById('emi_schedule');
        //  
        
        // if(emiMonths==0 || paymentMode=='')
        // {
        //     alert('select Emimonth or PaymentMode');
        //     location.reload();
        // }
        
      if (emiMonths > 0 && dueAmount > 0 && paymentMode) {
        // console.log("entered condition of emi report");
        
        const emiPerMonth = (dueAmount / emiMonths).toFixed(2);
        console.log(emiPerMonth);
        
        // Format numbers in Indian style
        const formattedEmi = new Intl.NumberFormat('en-IN').format(emiPerMonth);
        const formattedDue = new Intl.NumberFormat('en-IN').format(dueAmount.toFixed(2));
        // console.log(formattedEmi);
        // console.log(formattedDue);
        
        
        //emiReport.style.display = 'flex';
        $("#emi_report").removeClass('d-none');
        emiAmountSpan.textContent = formattedEmi;
        emiDueAmountSpan.textContent = formattedDue;

        emiSchedule.innerHTML = '';
        const startDate = new Date(document.getElementById('payment_date').value || new Date());

        for (let i = 1; i <= emiMonths; i++) {
          const dueDate = new Date(startDate);
          dueDate.setMonth(startDate.getMonth() + i);

          // Format date as DD-MM-YYYY
          const formattedDate = `${String(dueDate.getDate()).padStart(2, '0')}-${String(dueDate.getMonth() + 1).padStart(2, '0')}-${dueDate.getFullYear()}`;

          const tr = document.createElement('tr');
          tr.innerHTML = `
        <td>${i}</td>
        <td>${formattedEmi}</td>
        <td>${formattedDate}</td>
      `;
          emiSchedule.appendChild(tr);
        }
      } else {
        emiReport.style.display = 'none';
      }
    }


    function showPaymentDetails() {
      document.getElementById("cash_details").style.display = "none";
      document.getElementById("cheque_details").style.display = "none";
      document.getElementById("bank_transfer_details").style.display = "none";

      var paymentMethod = document.getElementById("payment_mode").value;

      if (paymentMethod === "cash") {
        document.getElementById("cash_details").style.display = "block";
      } else if (paymentMethod === "cheque") {
        document.getElementById("cheque_details").style.display = "block";
      } else if (paymentMethod === "bank_transfer") {
        document.getElementById("bank_transfer_details").style.display = "block";
        document.getElementById("neft_payment").style.display = "block";
        document.getElementById("rtgs_payment").style.display = "block";
        document.getElementById("utr_number").style.display = "block";
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

    function appendMemberID() {
      var paymentMethod = document.getElementById("payment_mode").value;
      var netAmount = parseFloat(document.getElementById("netAmount").innerText) || 0;
      var cashAmount = parseFloat(document.getElementById("newPayment").value) || 0;
      var chequeAmount = parseFloat(document.getElementById("newPayment").value) || 0;
      var transferAmount = parseFloat(document.getElementById("newPayment").value) || 0;
      var neftPayment = document.getElementById("neft_payment").value.trim();
      var rtgsPayment = document.getElementById("rtgs_payment").value.trim();
      var utrNumber = document.getElementById("utr_number").value.trim();
      var chequeNumber = document.getElementById("cheque_number").value.trim();
      var bankName = document.getElementById("bank_name").value.trim();
      var chequeDate = document.getElementById("cheque_date").value;

      if (!paymentMethod) {
        alert("Please select a payment mode.");
        return false;
      }

      if (paymentMethod === "cash" && (!cashAmount || cashAmount <= 0)) {
        alert("Please enter a valid cash amount.");
        return false;
      }

      if (paymentMethod === "cheque") {
        if (!chequeAmount || chequeAmount <= 0) {
          alert("Please enter a valid cheque amount.");
          return false;
        }
        if (!chequeNumber || !bankName || !chequeDate) {
          alert("Please provide cheque number, bank name, and cheque date.");
          return false;
        }
      }

      if (paymentMethod === "bank_transfer") {
        if (!transferAmount || transferAmount <= 0) {
          alert("Please enter a valid transfer amount.");
          return false;
        }
        const filledFields = [neftPayment, rtgsPayment, utrNumber].filter(val => val !== '').length;
        if (filledFields === 0) {
          alert("Please provide NEFT, RTGS, or UTR number for bank transfer.");
          return false;
        }
        if (filledFields > 1) {
          alert("Please provide only one of NEFT, RTGS, or UTR number.");
          return false;
        }
      }

      return true;
    }
  </script>




    <script>
        let currentCustomer = null;
        let pendingDeleteRowId = null;
        let pendingDeleteInvoiceId = null;
        let paymentTable = null; // Store DataTable instance
        let admissionpaymentTable = null;

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
                    console.log(data);
                    console.log(data.payments[0]);
                    
                    
                    hideLoadingOverlay();
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    currentCustomer = data.customer;

                    const netAmount = parseFloat(data.customer.net_amount) || 0;
                    const payAmount = parseFloat(data.customer.payamount) || 0;
                    const dueAmount = parseFloat(data.customer.due_amount) || 0;
                     const admAmount = parseFloat(data.customer.admission_charge) || 0;
                      const resAmount = parseFloat(data.customer.enrollment_charge) || 0;
                       const cashBack = parseFloat(data.payments[0].cashback) || 0;

                    //    console.log(cashBack);
                       
                      if(data.customer.enrollment_charge != "0.00"){
                        $("#enrolld").css("display", "none");  
                      }

                    document.getElementById('custName').textContent = data.customer.customer_name || 'N/A';
                    document.getElementById('prodName').textContent = data.customer.productname || 'N/A';
                    document.getElementById('netAmount').textContent = netAmount.toFixed(2);
                    document.getElementById('prevPaid').textContent = payAmount.toFixed(2);
                    document.getElementById('dueAmount').textContent = dueAmount.toFixed(2);
                    document.getElementById('updatedDue').textContent = dueAmount.toFixed(2);
                    document.getElementById('admission').textContent = admAmount.toFixed(2);
                    document.getElementById('enroll').textContent = resAmount.toFixed(2);
                    document.getElementById('cashback').textContent = cashBack.toFixed(2);
                    document.getElementById('newPayment').value = '';
                    document.getElementById('voucherNumber').value = '';
                    document.getElementById('payment_mode').value = '';
                    document.getElementById('neft_payment').value = '';
                    document.getElementById('rtgs_payment').value = '';
                    document.getElementById('utr_number').value = '';
                    showPaymentDetails();

                    const paymentBody = document.getElementById('paymentHistoryBody');
                    const admissionpaymentBody = document.getElementById('admissionpaymentHistoryBody');

                    // CRITICAL FIX: Destroy existing DataTable before clearing tbody
                    if (paymentTable) {
                        paymentTable.destroy();
                        paymentTable = null;
                    }
                     if (admissionpaymentTable) {
                        admissionpaymentTable.destroy();
                        admissionpaymentTable = null;
                    }

                    paymentBody.innerHTML = '';
                    admissionpaymentBody.innerHTML = '';
                    if (data.payments && data.payments.length > 0) {

                        // IMPORTANT: Calculate due amounts from LAST to FIRST (oldest to newest)
                        // Start from the last element and work backwards
                        // for (let i = data.payments.length - 1; i >= 0; i--) {
                        //     const payment = data.payments[i];
                        //     const netAmount = parseFloat(payment.net_amount) || 0;
                        //     const paymentAmount = parseFloat(payment.payamount) || 0;

                        //     let dueAmount;
                        //     if (i === data.payments.length - 1) {
                        //         // Last row (oldest payment): Net Amount - Payment Amount
                        //         dueAmount = netAmount - paymentAmount;
                        //     } else {
                        //         // For other rows: Next row's due (which we already calculated) - Current Payment
                        //         const nextRowDue = parseFloat(data.payments[i + 1].calculatedDue) || 0;
                        //         dueAmount = nextRowDue - paymentAmount;
                        //     }

                        //     // Store calculated due
                        //     payment.calculatedDue = dueAmount >= 0 ? dueAmount : 0;
                        // }

                        // Now render the rows with calculated due amounts
                        data.payments.forEach((payment, index) => {
                            const row = document.createElement('tr');
                            const row1 = document.createElement('tr');

                            const netAmount = parseFloat(payment.net_amount) || 0;
                            const paymentAmount = parseFloat(payment.payamount) || 0;
                            // const dueAmount = payment.calculatedDue;
                            const dueAmount = parseFloat(payment.due_amount) || 0;
                            console.log(dueAmount);
                            
                            row.innerHTML = `
            <td>${index + 1}</td>
            <td>${payment.receipt_no}</td>
            <td>${payment.invoice_id || 'N/A'}</td>
            <td>${payment.customer_name || 'N/A'}</td>
            <td>${payment.productname || 'N/A'}</td>
            <td>${netAmount.toFixed(2)}</td>
            <td>${payment.payment_mode || 'N/A'}</td>
            <td>${paymentAmount.toFixed(2)}</td>
            <td>${payment.admission_charge || '0'}</td>
            <td>${payment.enrollment_charge || '0'}</td>
            <td>${payment.cashback || '0'}</td>
            <td>${payment.created_date ? new Date(payment.created_date).toLocaleDateString('en-GB').replace(/\//g, '-') : 'N/A'}</td>
            <td>${dueAmount.toFixed(2)}</td>
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
                        data-member-id="${payment.member_id || currentCustomer.member_id}"
                        data-admission="${payment.admission_charge}"
                        data-enroll="${payment.enrollment_charge}"
                        data-payamount="${paymentAmount}"
                        >
                        Print
                    </button>                           
                </div>
            </td>
        `;
           if(payment.admission_charge==1100)
            {     
        row1.innerHTML = `
            <td>${index + 1}</td>
            <td>${payment.adm_receipt || 'N/A'}</td>
            <td>${payment.invoice_id || 'N/A'}</td>
            <td>${payment.customer_name || 'N/A'}</td>
            <td>${payment.productname || 'N/A'}</td>
            <td>${netAmount.toFixed(2)}</td>
            <td>${payment.payment_mode || 'N/A'}</td>
            <td>${paymentAmount.toFixed(2)}</td>
            <td>${payment.admission_charge || '0'}</td>
            <td>${payment.enrollment_charge || '0'}</td>
            <td>${payment.created_date ? new Date(payment.created_date).toLocaleDateString('en-GB').replace(/\//g, '-') : 'N/A'}</td>
            <td>${dueAmount.toFixed(2)}</td>
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
                        data-member-id="${payment.member_id || currentCustomer.member_id}"
                        data-admission="${payment.admission_charge}"
                       
                        ">
                        Print
                    </button>                           
                </div>
            </td>
        `;
            }
                            paymentBody.appendChild(row);

                            admissionpaymentBody.appendChild(row1);
                        });


                        document.querySelectorAll('.print-btn').forEach(button => {
                            button.addEventListener('click', function() {
                                const rowId = this.getAttribute('data-row-id');
                                const invoiceId = this.getAttribute('data-invoice-id');
                                const memberId = this.getAttribute('data-member-id');
                                const pay = this.getAttribute('data-payamount');
                                const enroll = this.getAttribute('data-enroll');
                                const admission = this.getAttribute('data-admission');
                                // console.log(pay);
                                // console.log(enroll);
                                // console.log(admission);
                                
                                
                                url = `newemiSaleinvoice.php?invoice_id=${encodeURIComponent(invoiceId)}&member_id=${encodeURIComponent(memberId)}&row_id=${encodeURIComponent(rowId)}&pay=${encodeURIComponent(pay)}&enroll=${encodeURIComponent(enroll)}&admission=${encodeURIComponent(admission)}`;
                                // Open in a new tab
                                window.open(url, '_blank');
                            });
                        });

                        setTimeout(function() {
                            paymentTable = $('#paymentHistory').DataTable({
                                "responsive": true,
                                "ordering": true,
                                "paging": true,
                                "searching": true,
                                "info": true,
                                "autoWidth": false
                            });
                              
                        }, 100);
                        setTimeout(function() {
                             admissionpaymentTable = $('#admissionpaymentHistory').DataTable({
                                "responsive": true,
                                "ordering": true,
                                "paging": true,
                                "searching": true,
                                "info": true,
                                "autoWidth": false
                            });
                              
                        }, 100);
                       

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
            const updatedDue = parseFloat(document.getElementById('dueAmount').textContent);
            const paymentMode = document.getElementById('payment_mode').value;
            const paymentType = document.getElementById('payment_type').value;
            const paymentDate = document.getElementById('payment_date').value;
            const firstallot = parseFloat(document.getElementById('prevPaid').textContent);
            const admission = parseFloat(document.getElementById('admission').textContent);
            const enroll = parseFloat(document.getElementById('enroll').textContent);
            const cashback = parseFloat(document.getElementById('cashback').textContent);
            const voucherNumber = document.getElementById('voucherNumber').value.trim();
            const receipt = document.getElementById('receipt').value.trim();
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
                        formData.append('payment_type', paymentType);
                        formData.append('payamount', newPayment);
                        formData.append('firstallot', firstallot);
                        formData.append('admission', admission);
                        formData.append('cashback', cashback); 
                        formData.append('enroll', enroll);
                        formData.append('receipt', receipt);
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

                                        let r = JSON.parse(text);

        console.log("DATA:", r.data);
        console.log("COUNT:", r.count);
        console.log("LOOP:", r.loop);
        console.log("DEBUG FLOW:", r.debug);



                                    const data = JSON.parse(text);
                                    if (data.success) {
                                        console.log(data);
                                        
                                        
                                    let dueAmount =  Number(data.due_amount)-Number(newPayment);

                                alert(
                                    "Payment recorded successfully!\nNew Due Amount: " +
                                    dueAmount.toFixed(2)
                                );
                                        currentCustomer.payamount =  (parseFloat(currentCustomer.payamount) || 0) + newPayment;;
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