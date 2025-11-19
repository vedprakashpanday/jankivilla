<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

$sponsor_id = $_SESSION['sponsor_id'];

// Handle form submission
$success = '';
$error = '';

// Fetch accounts for dropdown
$sql = "SELECT id, account_name FROM accounts ORDER BY account_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_transaction'])) {
    try {
        $pdo->beginTransaction();

        $account_id = $_POST['account_id'];

        $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':account_id' => $account_id]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("Invalid account selected.");
        }

        if (isset($_POST['transactions']) && is_array($_POST['transactions'])) {
            $totalAmount = 0;
            $transactionIds = [];

            foreach ($_POST['transactions'] as $index => $transaction) {
                if (empty($transaction['txn_date'])) {
                    throw new Exception("Transaction date is required for row " . ($index + 1));
                }
                if (empty($transaction['amount']) || $transaction['amount'] <= 0) {
                    throw new Exception("Valid amount is required for row " . ($index + 1));
                }
                if (empty($transaction['expense_category'])) {
                    throw new Exception("Category is required for row " . ($index + 1));
                }
                if (empty($transaction['description'])) {
                    throw new Exception("Description is required for row " . ($index + 1));
                }

                // Calculate total from payment amounts
                $paymentAmounts = [
                    'cash' => floatval($transaction['cash_amount'] ?? 0),
                    'upi' => floatval($transaction['upi_amount'] ?? 0),
                    'cheque' => floatval($transaction['cheque_amount'] ?? 0),
                    'transfer' => floatval($transaction['transfer_amount'] ?? 0),
                    'card' => floatval($transaction['card_amount'] ?? 0)
                ];
                $totalPayment = array_sum($paymentAmounts);
                if (abs($totalPayment - floatval($transaction['amount'])) > 0.01) { // Allow for minor floating-point differences
                    throw new Exception("Payment amounts do not match total amount for row " . ($index + 1) . ". Expected: ₹" . number_format($transaction['amount'], 2) . ", Got: ₹" . number_format($totalPayment, 2));
                }

                $totalAmount += floatval($transaction['amount']);

                $transaction_category = '';
                switch ($transaction['expense_category']) {
                    case 'Fuel':
                        $transaction_category = 'fuel';
                        break;
                    case 'Farmer Payment':
                        $transaction_category = 'farmer';
                        break;
                    case 'Commission':
                        $transaction_category = 'salesperson';
                        break;
                    case 'Sales Expense':
                        $transaction_category = 'sales_expense';
                        break;
                    default:
                        $transaction_category = 'general';
                        break;
                }

                // Construct payment details JSON
                $paymentDetails = [];
                if ($paymentAmounts['cash'] > 0) {
                    $paymentDetails['cash'] = ['amount' => $paymentAmounts['cash']];
                }
                if ($paymentAmounts['upi'] > 0) {
                    $paymentDetails['upi'] = [
                        'amount' => $paymentAmounts['upi'],
                        'transaction_id' => $transaction['transaction_id'] ?? null
                    ];
                }
                if ($paymentAmounts['cheque'] > 0) {
                    $paymentDetails['cheque'] = [
                        'amount' => $paymentAmounts['cheque'],
                        'cheque_no' => $transaction['cheque_no'] ?? null,
                        'bank_name' => $transaction['bank_name'] ?? null,
                        'cheque_date' => $transaction['cheque_date'] ?? null
                    ];
                }
                if ($paymentAmounts['transfer'] > 0) {
                    $paymentDetails['transfer'] = ['amount' => $paymentAmounts['transfer']];
                }
                if ($paymentAmounts['card'] > 0) {
                    $paymentDetails['card'] = ['amount' => $paymentAmounts['card']];
                }
                $paymentDetailsJson = json_encode($paymentDetails);

                $txn_data = [
                    ':account_id' => $account_id,
                    ':txn_date' => $transaction['txn_date'],
                    ':description' => $transaction['description'],
                    ':amount' => $transaction['amount'],
                    ':get_to' => $transaction['description'],
                    ':type' => 'debit',
                    ':expense_category' => $transaction['expense_category'],
                    ':payment_mode' => implode(',', array_keys(array_filter($paymentAmounts))),
                    ':payment_details_combine' => $paymentDetailsJson,
                    ':authorized_by' => $transaction['authorized_by'] ?? null,
                    ':transaction_category' => $transaction_category,
                    ':vehicle_info' => null,
                    ':driver_name' => null,
                    ':kilometers' => null,
                    ':farmer_name' => null,
                    ':salesperson_name' => null,
                    ':commission_type' => null,
                    ':plot_commission' => null,
                    ':sponsor_id' => $sponsor_id
                ];

                $sql = "INSERT INTO tbl_transactions (
                    account_id, txn_date, description, amount, get_to, type, 
                    expense_category, payment_mode, payment_details_combine, authorized_by, 
                    transaction_category, vehicle_info, driver_name, kilometers, farmer_name, 
                    salesperson_name, commission_type, plot_commission, created_at, sponsor_id
                ) VALUES (
                    :account_id, :txn_date, :description, :amount, :get_to, :type,
                    :expense_category, :payment_mode, :payment_details_combine, :authorized_by,
                    :transaction_category, :vehicle_info, :driver_name, :kilometers, :farmer_name,
                    :salesperson_name, :commission_type, :plot_commission, NOW(), :sponsor_id
                )";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($txn_data);
                $transactionIds[] = $pdo->lastInsertId();
            }

            $sql = "SELECT balance FROM tbl_account_balances 
                    WHERE account_id = :account_id 
                    ORDER BY updated_at DESC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id]);
            $latest_balance = $stmt->fetchColumn();

            if ($latest_balance === false) {
                $sql = "SELECT opening_balance FROM accounts WHERE id = :account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':account_id' => $account_id]);
                $latest_balance = $stmt->fetchColumn() ?: 0.00;
            }

            $new_balance = $latest_balance - $totalAmount;

            $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                    VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $account_id,
                ':balance' => $new_balance,
                ':txn_id' => $transactionIds[0],
                ':txn_date' => $_POST['transactions'][array_key_first($_POST['transactions'])]['txn_date']
            ]);

            $pdo->commit();
            $success = "Successfully added " . count($_POST['transactions']) . " transaction(s)! Total amount: ₹" . number_format($totalAmount, 2);
        } else {
            throw new Exception("No transactions to process.");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
// Generate JSON response for AJAX if needed
if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => !empty($success),
        'message' => $success ?: $error
    ]);
    exit;
}
?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers | Accountant Panel
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

    <!-- <style>
        .form-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .transaction-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }

        .table-header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }

        .dynamic-fields {
            background: #e3f2fd;
            border: 2px dashed #2196f3;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }

        .btn-add-row {
            position: relative;
            top: -7px;
            background: #28a745;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-add-row:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .btn-remove-row {
            background: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        .category-selector {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .payment-details {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        .submit-section {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
        }
    </style> -->

    <style>
        .form-container {
            padding: 20px;
            background: #fff;
        }

        .transaction-row {
            display: flex;
            align-items: end;
            gap: 10px;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background: #f8f9fa;
            flex-wrap: nowrap;
            min-width: 1400px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            min-width: fit-content;
        }

        .field-group label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 3px;
            white-space: nowrap;
        }

        .field-group input,
        .field-group select {
            height: 32px;
            font-size: 13px;
            padding: 4px 8px;
        }

        .date-field {
            width: 130px;
        }

        .voucher-field {
            width: 120px;
        }

        .category-field {
            width: 140px;
        }

        .particulars-field {
            width: 150px;
        }

        .amount-field {
            width: 100px;
        }

        .payment-mode-field {
            width: 120px;
        }

        .payment-amount-field {
            width: 90px;
        }

        .payment-fields {
            display: flex;
            gap: 10px;
        }

        .payment-field {
            display: none;
        }

        .payment-field.show {
            display: flex;
            flex-direction: column;
        }

        .payment-field input {
            width: 90px;
        }

        .btn-add-row {
            background: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            height: 32px;
            white-space: nowrap;
        }

        .btn-remove-row {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            height: 32px;
        }

        .transaction-header {
            background: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-container {
            overflow-x: auto;
            width: 100%;
        }
    </style>

</head>

<body>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <div class="wrapper ">
        <div class="container-scroller ">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper ">
                <?php include "account-headersidepanel.php"; ?>

                <div class="main-panel">

                    <div class="mx-3 mt-3 rounded shadow-lg" style="background:#fff; border: 2px solid #fff; margin-bottom:11.3rem;">
                        <div class="col-md-12">
                            <!-- Success/Error Messages -->
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?php echo htmlspecialchars($success); ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?php echo htmlspecialchars($error); ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>

                            <div class="form-container">
                                <h2 class="text-center mb-4">
                                    <i class="fas fa-minus-circle text-danger"></i>
                                    Debit Transaction Entry
                                </h2>

                                <form id="debitForm" method="POST">
                                    <!-- Hidden Account Field -->
                                    <input type="hidden" name="account_id" value="46" required>

                                    <!-- Transaction Header -->
                                    <div class="transaction-header">
                                        <span>Transaction Details</span>
                                        <button type="button" class="btn-add-row" onclick="addTransactionRow()">
                                            <i class="fas fa-plus"></i> Add Entry
                                        </button>
                                    </div>

                                    <!-- Scrollable Container -->
                                    <div class="table-container">
                                        <div id="transactionRows">
                                            <!-- Dynamic transaction rows will be added here -->
                                        </div>
                                    </div>

                                    <!-- Submit Section -->
                                    <div class="submit-section text-center mt-4">
                                        <button type="submit" name="btn_add_transaction" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Save All Transactions
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset Form
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php include "account-footer.php"; ?>
                </div>
            </div>
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
        let rowCount = 0;

        function addTransactionRow() {
            rowCount++;

            const rowHtml = `
        <div class="transaction-row" id="row-${rowCount}">
            <!-- Date -->
            <div class="field-group">
                <label>Date</label>
                <input type="date" class="form-control date-field" name="transactions[${rowCount}][txn_date]" 
                       value="${new Date().toISOString().split('T')[0]}" required>
            </div>
            
            <!-- Voucher Number -->
            <div class="field-group">
                <label>Voucher No.</label>
                <input type="text" class="form-control voucher-field" name="transactions[${rowCount}][authorized_by]" 
                       placeholder="Voucher#">
            </div>
            
            <!-- Category -->
            <div class="field-group">
                <label>Category</label>
                <select class="form-control category-field" name="transactions[${rowCount}][expense_category]" required>
                    <option value="">Select</option>
                    <option value="Fuel">Fuel</option>
                    <option value="Farmer Payment">Farmer</option>
                    <option value="General">General</option>
                    <option value="Sales Expense">Sales</option>
                </select>
            </div>
            
            <!-- Particulars -->
            <div class="field-group">
                <label>Particulars</label>
                <input type="text" class="form-control particulars-field" name="transactions[${rowCount}][description]" 
                       placeholder="Description" required>
            </div>
            
            <!-- Amount -->
            <div class="field-group">
                <label>Amount</label>
                <input type="number" class="form-control amount-field" name="transactions[${rowCount}][amount]" 
                       placeholder="0.00" required>
            </div>
            
            <!-- Payment Mode -->
            <div class="field-group">
                <label>Payment Modes</label>
                <select class="form-control payment-mode-field" name="transactions[${rowCount}][payment_mode][]" 
                        multiple onchange="togglePaymentFields(${rowCount}); checkPaymentSum(${rowCount});" required>
                    <option value="Cash">Cash</option>
                    <option value="UPI">UPI</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Bank Transfer">Transfer</option>
                    <option value="Card">Card</option>
                </select>
            </div>
            
            <!-- Payment Amount Fields -->
            <div class="payment-fields" id="payment-fields-${rowCount}">
                <!-- Cash Amount -->
                <div class="field-group payment-field" id="cash-field-${rowCount}" style="display:none;">
                    <label>Cash</label>
                    <input type="number" class="form-control payment-amount-field" 
                           name="transactions[${rowCount}][cash_amount]" placeholder="0.00" oninput="checkPaymentSum(${rowCount})">
                </div>
                
                <!-- UPI Amount + ID -->
                <div class="field-group payment-field" id="upi-field-${rowCount}" style="display:none;">
                    <label>UPI Amount</label>
                    <input type="number" class="form-control payment-amount-field" 
                           name="transactions[${rowCount}][upi_amount]" placeholder="0.00" oninput="checkPaymentSum(${rowCount})">
                </div>
                <div class="field-group payment-field" id="upi-id-field-${rowCount}" style="display:none;">
                    <label>UPI ID</label>
                    <input type="text" class="form-control" style="width: 120px;" 
                           name="transactions[${rowCount}][transaction_id]" placeholder="UPI Transaction ID">
                </div>
                
                <!-- Cheque Amount + Details -->
                <div class="field-group payment-field" id="cheque-field-${rowCount}" style="display:none;">
                    <label>Cheque Amount</label>
                    <input type="number" class="form-control payment-amount-field" 
                           name="transactions[${rowCount}][cheque_amount]" placeholder="0.00" oninput="checkPaymentSum(${rowCount})">
                </div>
                <div class="field-group payment-field" id="cheque-no-field-${rowCount}" style="display:none;">
                    <label>Cheque No.</label>
                    <input type="text" class="form-control" style="width: 100px;" 
                           name="transactions[${rowCount}][cheque_no]" placeholder="Cheque#">
                </div>
                <div class="field-group payment-field" id="bank-field-${rowCount}" style="display:none;">
                    <label>Bank</label>
                    <input type="text" class="form-control" style="width: 120px;" 
                           name="transactions[${rowCount}][bank_name]" placeholder="Bank Name">
                </div>
                <div class="field-group payment-field" id="cheque-date-field-${rowCount}" style="display:none;">
                    <label>Cheque Date</label>
                    <input type="date" class="form-control" style="width: 130px;" 
                           name="transactions[${rowCount}][cheque_date]">
                </div>
                
                <!-- Transfer Amount -->
                <div class="field-group payment-field" id="transfer-field-${rowCount}" style="display:none;">
                    <label>Transfer</label>
                    <input type="number" class="form-control payment-amount-field" 
                           name="transactions[${rowCount}][transfer_amount]" placeholder="0.00" oninput="checkPaymentSum(${rowCount})">
                </div>
                
                <!-- Card Amount -->
                <div class="field-group payment-field" id="card-field-${rowCount}" style="display:none;">
                    <label>Card</label>
                    <input type="number" class="form-control payment-amount-field" 
                           name="transactions[${rowCount}][card_amount]" placeholder="0.00" oninput="checkPaymentSum(${rowCount})">
                </div>
            </div>
            
            <!-- Remove Button -->
            <div class="field-group">
                <label>&nbsp;</label>
                <button type="button" class="btn-remove-row" onclick="removeTransactionRow(${rowCount})" 
                        ${rowCount === 1 ? 'style="display:none;"' : ''}>
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

            document.getElementById('transactionRows').insertAdjacentHTML('beforeend', rowHtml);
        }

        function removeTransactionRow(rowId) {
            const row = document.getElementById(`row-${rowId}`);
            if (row) {
                row.remove();
            }
        }

        function togglePaymentFields(rowId) {
            const paymentModeSelect = document.querySelector(`#row-${rowId} select[name="transactions[${rowId}][payment_mode][]"]`);
            if (!paymentModeSelect) {
                console.error(`Payment mode select not found for row ${rowId}`);
                return;
            }
            const selectedModes = Array.from(paymentModeSelect.selectedOptions).map(option => option.value.toLowerCase());

            const paymentFields = document.getElementById(`payment-fields-${rowId}`);
            if (!paymentFields) {
                console.error(`Payment fields container not found for row ${rowId}`);
                return;
            }
            const allPaymentFields = paymentFields.getElementsByClassName('payment-field');

            Array.from(allPaymentFields).forEach(field => {
                const mode = field.id.split('-')[0].toLowerCase(); // e.g., "cash", "upi", "cheque"
                if (selectedModes.includes(mode)) {
                    field.style.display = 'block';
                    if (mode === 'upi') {
                        const upiIdField = document.getElementById(`upi-id-field-${rowId}`);
                        if (upiIdField) upiIdField.style.display = 'block';
                    } else if (mode === 'cheque') {
                        ['cheque-no-field', 'bank-field', 'cheque-date-field'].forEach(idPrefix => {
                            const relatedField = document.getElementById(`${idPrefix}-${rowId}`);
                            if (relatedField) relatedField.style.display = 'block';
                        });
                    }
                } else {
                    field.style.display = 'none';
                    const inputs = field.getElementsByTagName('input');
                    Array.from(inputs).forEach(input => {
                        if (input.type === 'number' && input.classList.contains('payment-amount-field') && !selectedModes.includes(mode)) {
                            input.value = '';
                        }
                    });
                }
            });
        }

        function checkPaymentSum(rowId) {
            const amountField = document.querySelector(`#row-${rowId} input[name="transactions[${rowId}][amount]"]`);
            if (!amountField) {
                console.error(`Amount field not found for row ${rowId}`);
                return;
            }
            const mainAmount = parseFloat(amountField.value) || 0;
            let totalPayment = 0;

            // Track the last changed input
            let lastChangedInput = null;
            ['cash', 'upi', 'cheque', 'transfer', 'card'].forEach(mode => {
                const amountInput = document.querySelector(`#row-${rowId} input[name="transactions[${rowId}][${mode}_amount]"]`);
                if (amountInput && amountInput.value) {
                    totalPayment += parseFloat(amountInput.value) || 0;
                    if (document.activeElement === amountInput) {
                        lastChangedInput = amountInput;
                    }
                }
            });

            const paymentModeSelect = document.querySelector(`#row-${rowId} select[name="transactions[${rowId}][payment_mode][]"]`);
            if (totalPayment > mainAmount) {
                alert("Payment amounts exceed the main amount by ₹" + (totalPayment - mainAmount).toFixed(2) + "! Please adjust.");
                if (lastChangedInput) {
                    lastChangedInput.value = ''; // Clear the last changed input
                    totalPayment -= parseFloat(lastChangedInput.value) || 0; // Recalculate total
                }
            } else if (totalPayment === mainAmount) {
                alert("Entered amounts match the total amount of ₹" + mainAmount.toFixed(2) + ".");
                Array.from(paymentModeSelect.options).forEach(option => {
                    if (!option.selected) option.disabled = true;
                });
            } else {
                Array.from(paymentModeSelect.options).forEach(option => {
                    option.disabled = false;
                });
            }
        }

        function resetForm() {
            document.getElementById('debitForm').reset();
            document.getElementById('transactionRows').innerHTML = '';
            rowCount = 0;
            addTransactionRow();
        }

        document.addEventListener('DOMContentLoaded', function() {
            addTransactionRow();
        });
    </script>


    <style>
        i {
            color: yellow;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#producttable').DataTable({

            });
        });
    </script>


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>