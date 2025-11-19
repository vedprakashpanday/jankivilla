<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

// Handle form submission
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_transaction'])) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        $account_id = $_POST['account_id'];
        $authorized_by = $_POST['authorized_by'] ?? null;
        $payment_mode = $_POST['payment_mode'] ?? null;
        $transaction_id = $_POST['transaction_id'] ?? null;
        $cheque_no = $_POST['cheque_no'] ?? null;
        $bank_name = $_POST['bank_name'] ?? null;
        $cheque_date = $_POST['cheque_date'] ?? null;

        // Validate common fields
        if (empty($account_id) || empty($payment_mode)) {
            throw new Exception("Account and Payment Mode are required.");
        }

        // Validate payment mode specific fields
        if ($payment_mode === 'UPI' && empty($transaction_id)) {
            throw new Exception("UPI Transaction ID is required for UPI payments.");
        }

        if ($payment_mode === 'Cheque' && (empty($cheque_no) || empty($bank_name) || empty($cheque_date))) {
            throw new Exception("Cheque Number, Bank Name, and Cheque Date are required for Cheque payments.");
        }

        // Process multiple transactions
        if (isset($_POST['transactions']) && is_array($_POST['transactions'])) {
            $totalAmount = 0;
            $processedCount = 0;

            foreach ($_POST['transactions'] as $index => $transaction) {
                // Validate transaction data
                if (!isset($transaction['transaction_date']) || empty($transaction['transaction_date'])) {
                    throw new Exception("Transaction date is missing for transaction row " . ($index + 1));
                }
                if (empty($transaction['amount']) || $transaction['amount'] <= 0) {
                    throw new Exception("Valid amount is required for transaction row " . ($index + 1));
                }

                // Validate date format
                $date = DateTime::createFromFormat('Y-m-d', $transaction['transaction_date']);
                if ($date === false || $date->format('Y-m-d') !== $transaction['transaction_date']) {
                    throw new Exception("Invalid date format for transaction row " . ($index + 1));
                }

                $totalAmount += floatval($transaction['amount']);

                // Prepare base transaction data
                $txn_data = [
                    'account_id' => $account_id,
                    'transaction_date' => $transaction['transaction_date'],
                    'amount' => $transaction['amount'],
                    'authorized_by' => $authorized_by,
                    'payment_mode' => $payment_mode,
                    'transaction_id' => $payment_mode === 'UPI' ? $transaction_id : null,
                    'cheque_no' => $payment_mode === 'Cheque' ? $cheque_no : null,
                    'bank_name' => $payment_mode === 'Cheque' ? $bank_name : null,
                    'cheque_date' => $payment_mode === 'Cheque' ? $cheque_date : null,
                    'payee_name' => null,
                    'description' => null,
                    'expense_category' => null,
                    'transaction_category' => null,
                    'vehicle_info' => null,
                    'driver_name' => null,
                    'kilometers' => null,
                    'farmer_name' => null,
                    'salesperson_name' => null,
                    'commission_type' => null,
                    'plot_commission' => null,
                    'sales_expense_type' => null
                ];

                // Determine category and set specific fields
                if (isset($transaction['vehicle_info']) && !empty($transaction['vehicle_info'])) {
                    // Fuel category
                    $txn_data['transaction_category'] = 'fuel';
                    $txn_data['vehicle_info'] = $transaction['vehicle_info'];
                    $txn_data['driver_name'] = $transaction['driver_name'] ?? null;
                    $txn_data['kilometers'] = $transaction['kilometers'] ?? null;
                    $txn_data['payee_name'] = $transaction['driver_name'];
                    $txn_data['description'] = "Fuel expense for " . $transaction['vehicle_info'];
                    $txn_data['expense_category'] = 'Fuel';
                } elseif (isset($transaction['farmer_name']) && !empty($transaction['farmer_name'])) {
                    // Farmer category
                    $txn_data['transaction_category'] = 'farmer';
                    $txn_data['farmer_name'] = $transaction['farmer_name'];
                    $txn_data['payee_name'] = $transaction['farmer_name'];
                    $txn_data['description'] = $transaction['description'] ?? 'Payment to farmer';
                    $txn_data['expense_category'] = 'Farmer Payment';
                } elseif (isset($transaction['salesperson_name']) && !empty($transaction['salesperson_name'])) {
                    // Salesperson category
                    $txn_data['transaction_category'] = 'salesperson';
                    $txn_data['salesperson_name'] = $transaction['salesperson_name'];
                    $txn_data['commission_type'] = $transaction['commission_type'] ?? null;
                    $txn_data['plot_commission'] = $transaction['plot_commission'] ?? null;
                    $txn_data['payee_name'] = $transaction['salesperson_name'];
                    $txn_data['description'] = ($transaction['commission_type'] ?? 'Payment') . " for " . $transaction['salesperson_name'];
                    $txn_data['expense_category'] = 'Commission';
                } elseif (isset($transaction['sales_expense']) && !empty($transaction['sales_expense'])) {
                    // Sales expense category
                    $txn_data['transaction_category'] = 'sales_expense';
                    $txn_data['sales_expense_type'] = $transaction['sales_expense'];
                    $txn_data['payee_name'] = $transaction['get_to'] ?? null;
                    $txn_data['description'] = $transaction['description'] ?? 'Sales expense';
                    $txn_data['expense_category'] = $transaction['sales_expense'];
                } else {
                    // General category
                    $txn_data['transaction_category'] = 'general';
                    $txn_data['payee_name'] = $transaction['get_to'] ?? null;
                    $txn_data['description'] = $transaction['description'] ?? 'General expense';
                    $txn_data['expense_category'] = $transaction['expense_category'] ?? 'General';
                }

                // Insert debit transaction
                $sql = "INSERT INTO tbl_debit_transactions (
                    account_id, transaction_date, amount, authorized_by, payment_mode, 
                    transaction_id, cheque_no, bank_name, cheque_date, transaction_category,
                    payee_name, description, expense_category, vehicle_info, driver_name, 
                    kilometers, farmer_name, salesperson_name, commission_type, plot_commission, 
                    sales_expense_type
                ) VALUES (
                    :account_id, :transaction_date, :amount, :authorized_by, :payment_mode,
                    :transaction_id, :cheque_no, :bank_name, :cheque_date, :transaction_category,
                    :payee_name, :description, :expense_category, :vehicle_info, :driver_name,
                    :kilometers, :farmer_name, :salesperson_name, :commission_type, :plot_commission,
                    :sales_expense_type
                )";

                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($txn_data);

                if ($result) {
                    $processedCount++;
                } else {
                    throw new Exception("Failed to insert transaction row " . ($index + 1));
                }
            }

            // Commit transaction
            $pdo->commit();

            $success = "Successfully added {$processedCount} debit transaction(s)! Total amount debited: ₹" . number_format($totalAmount, 2);
        } else {
            throw new Exception("No transactions to process.");
        }
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

// Function to get debit transactions report
function getDebitTransactionsReport($pdo, $filters = [])
{
    $sql = "SELECT 
                id,
                account_id,
                transaction_date,
                amount,
                authorized_by,
                payment_mode,
                transaction_id,
                cheque_no,
                bank_name,
                cheque_date,
                transaction_category,
                payee_name,
                description,
                expense_category,
                vehicle_info,
                driver_name,
                kilometers,
                farmer_name,
                salesperson_name,
                commission_type,
                plot_commission,
                sales_expense_type,
                created_at
            FROM tbl_debit_transactions 
            WHERE 1=1";

    $params = [];

    // Add filters
    if (!empty($filters['date_from'])) {
        $sql .= " AND transaction_date >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }

    if (!empty($filters['date_to'])) {
        $sql .= " AND transaction_date <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }

    if (!empty($filters['category'])) {
        $sql .= " AND transaction_category = :category";
        $params['category'] = $filters['category'];
    }

    if (!empty($filters['payment_mode'])) {
        $sql .= " AND payment_mode = :payment_mode";
        $params['payment_mode'] = $filters['payment_mode'];
    }

    $sql .= " ORDER BY transaction_date DESC, created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get category-wise summary
function getDebitSummaryByCategory($pdo, $date_from = null, $date_to = null)
{
    $sql = "SELECT 
                transaction_category,
                COUNT(*) as transaction_count,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount
            FROM tbl_debit_transactions 
            WHERE 1=1";

    $params = [];

    if ($date_from) {
        $sql .= " AND transaction_date >= :date_from";
        $params['date_from'] = $date_from;
    }

    if ($date_to) {
        $sql .= " AND transaction_date <= :date_to";
        $params['date_to'] = $date_to;
    }

    $sql .= " GROUP BY transaction_category ORDER BY total_amount DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get monthly summary
function getMonthlyDebitSummary($pdo, $year = null)
{
    $year = $year ?? date('Y');

    $sql = "SELECT 
                MONTH(transaction_date) as month,
                MONTHNAME(transaction_date) as month_name,
                COUNT(*) as transaction_count,
                SUM(amount) as total_amount
            FROM tbl_debit_transactions 
            WHERE YEAR(transaction_date) = :year
            GROUP BY MONTH(transaction_date), MONTHNAME(transaction_date)
            ORDER BY month";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['year' => $year]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <style>
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

                    <div class="mx-3 mt-3 rounded shadow-lg" style="background:#fff; border: 2px solid #fff;  margin-bottom:11.3rem;">
                        <div class="col-md-12">
                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show">
                                    <?php echo htmlspecialchars($success); ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <?php echo htmlspecialchars($error); ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php endif; ?>
                            <div class="form-container">
                                <h2 class="text-center mb-4">
                                    <i class="fas fa-minus-circle text-danger"></i>
                                    Director Debit Transaction Entry
                                </h2>

                                <!-- Category Selection -->
                                <div class="category-selector">
                                    <label for="transactionCategory"><strong>Select Transaction Category:</strong></label>
                                    <select id="transactionCategory" class="form-control" onchange="updateFieldsBasedOnCategory()">
                                        <option value="">-- Select Category --</option>
                                        <option value="fuel">Fuel Expense</option>
                                        <option value="farmer">Farmer Payment</option>
                                        <option value="salesperson">Sales Person Commission</option>
                                        <option value="general">General Expense</option>
                                        <option value="sales_expense">Sales Expense</option>
                                    </select>
                                </div>

                                <form id="debitForm" method="POST">
                                    <!-- Common Fields -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="account_id">Account</label>
                                                <select class="form-control" name="account_id">
                                                    <option value="Main_Account" selected>Main Account</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="authorized_by">Authorized By</label>
                                                <input type="text" class="form-control" name="authorized_by" placeholder="e.g., Manager Name">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dynamic Transaction Table -->
                                    <div class="transaction-table">
                                        <div class="table-header">
                                            Transaction Details
                                            <button type="button" class="btn-add-row float-right" onclick="addTransactionRow()">
                                                <i class="fas fa-plus"></i> Add Row
                                            </button>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-striped" id="transactionTable">
                                                <thead id="tableHeader">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Description</th>
                                                        <th>Amount (₹)</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="transactionRows">
                                                    <!-- Dynamic rows will be added here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Payment Mode Section -->
                                    <div class="payment-details">
                                        <h5>Payment Details</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="payment_mode">Payment Mode *</label>
                                                    <select class="form-control" name="payment_mode" id="payment_mode" required onchange="togglePaymentFields()">
                                                        <option value="">-- Select Mode --</option>
                                                        <option value="Cash">Cash</option>
                                                        <option value="UPI">UPI</option>
                                                        <option value="Cheque">Cheque</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Card">Card</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div id="paymentModeFields">
                                                    <!-- Dynamic payment fields will appear here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Section -->
                                    <div class="submit-section">
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
        let transactionCount = 0;
        let currentCategory = '';

        // Category-based field configurations
        const categoryFields = {
            fuel: {
                headers: ['Date', 'Vehicle No/Name', 'Amount (₹)', 'Driver Name', 'K.M', 'Action'],
                fields: [{
                        name: 'transaction_date',
                        type: 'date',
                        placeholder: '',
                        required: true
                    },
                    {
                        name: 'vehicle_info',
                        type: 'text',
                        placeholder: 'e.g., MH12AB1234 or Vehicle Name',
                        required: true
                    },
                    {
                        name: 'amount',
                        type: 'number',
                        placeholder: '0.00',
                        required: true,
                        step: '0.01'
                    },
                    {
                        name: 'driver_name',
                        type: 'text',
                        placeholder: 'Driver Name',
                        required: false
                    },
                    {
                        name: 'kilometers',
                        type: 'number',
                        placeholder: 'K.M',
                        required: false
                    }
                ]
            },
            farmer: {
                headers: ['Date', 'Farmer Name', 'Amount (₹)', 'Description', 'Action'],
                fields: [{
                        name: 'transaction_date',
                        type: 'date',
                        placeholder: '',
                        required: true
                    },
                    {
                        name: 'farmer_name',
                        type: 'text',
                        placeholder: 'Farmer Name',
                        required: true
                    },
                    {
                        name: 'amount',
                        type: 'number',
                        placeholder: '0.00',
                        required: true,
                        step: '0.01'
                    },
                    {
                        name: 'description',
                        type: 'text',
                        placeholder: 'Payment description',
                        required: false
                    }
                ]
            },
            salesperson: {
                headers: ['Date', 'Sales Person', 'Amount (₹)', 'Commission/Advance', 'commission on plot', 'Action'],
                fields: [{
                        name: 'transaction_date',
                        type: 'date',
                        placeholder: '',
                        required: true
                    },
                    {
                        name: 'salesperson_name',
                        type: 'text',
                        placeholder: 'Sales Person Name',
                        required: false
                    },
                    {
                        name: 'amount',
                        type: 'number',
                        placeholder: '0.00',
                        required: true,
                        step: '0.01'
                    },
                    {
                        name: 'commission_type',
                        type: 'text',
                        placeholder: 'commission/advance',
                        required: false
                    },
                    {
                        name: 'plot_commission',
                        type: 'text',
                        placeholder: 'Commission on Plot',
                        required: false
                    }
                ]
            },
            general: {
                headers: ['Date', 'To/Payee', 'Amount (₹)', 'Description', 'Category', 'Action'],
                fields: [{
                        name: 'transaction_date',
                        type: 'date',
                        placeholder: '',
                        required: true
                    },
                    {
                        name: 'get_to',
                        type: 'text',
                        placeholder: 'Payee Name',
                        required: false
                    },
                    {
                        name: 'amount',
                        type: 'number',
                        placeholder: '0.00',
                        required: true,
                        step: '0.01'
                    },
                    {
                        name: 'description',
                        type: 'text',
                        placeholder: 'Transaction description',
                        required: false
                    },
                    {
                        name: 'expense_category',
                        type: 'select',
                        options: ['Office', 'Medical', 'Utility', 'Maintenance', 'Other'],
                        required: false
                    }
                ]
            },
            sales_expense: {
                headers: ['Date', 'To/Payee', 'Amount (₹)', 'Description', 'Category', 'Action'],
                fields: [{
                        name: 'transaction_date',
                        type: 'date',
                        placeholder: '',
                        required: true
                    },
                    {
                        name: 'get_to',
                        type: 'text',
                        placeholder: 'Name of Sales Expense',
                        required: true,
                    },
                    {
                        name: 'amount',
                        type: 'number',
                        placeholder: '0.00',
                        required: true,
                        step: '0.01'
                    },
                    {
                        name: 'description',
                        type: 'text',
                        placeholder: 'Transaction description',
                        required: false
                    },
                    {
                        name: 'sales_expense',
                        type: 'text',
                        placeholder: 'Sales Expense',
                        required: false
                    }
                ]
            }
        };

        function updateFieldsBasedOnCategory() {
            const category = document.getElementById('transactionCategory').value;
            currentCategory = category;

            if (category) {
                updateTableHeaders();
                clearTransactionRows();
                addTransactionRow(); // Add first row automatically
            }
        }

        function updateTableHeaders() {
            const headerRow = document.getElementById('tableHeader').querySelector('tr');
            const headers = categoryFields[currentCategory]?.headers || [];

            headerRow.innerHTML = '';
            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
        }

        function addTransactionRow() {
            if (!currentCategory) {
                alert('Please select a transaction category first!');
                return;
            }

            const tbody = document.getElementById('transactionRows');
            const row = document.createElement('tr');
            const fields = categoryFields[currentCategory].fields;

            transactionCount++;

            fields.forEach(field => {
                const td = document.createElement('td');
                let input = '';

                if (field.type === 'select') {
                    input = `<select class="form-control" name="transactions[${transactionCount}][${field.name}]" ${field.required ? 'required' : ''}>
                                <option value="">-- Select --</option>`;
                    field.options.forEach(option => {
                        input += `<option value="${option}">${option}</option>`;
                    });
                    input += '</select>';
                } else {
                    input = `<input type="${field.type}" 
                                   class="form-control" 
                                   name="transactions[${transactionCount}][${field.name}]"
                                   placeholder="${field.placeholder}"
                                   ${field.step ? `step="${field.step}"` : ''}
                                   ${field.required ? 'required' : ''}
                                   ${field.type === 'date' ? `value="${new Date().toISOString().split('T')[0]}"` : ''}>`;
                }

                td.innerHTML = input;
                row.appendChild(td);
            });

            // Add action button
            const actionTd = document.createElement('td');
            actionTd.innerHTML = `<button type="button" class="btn-remove-row" onclick="removeTransactionRow(this)">
                                    <i class="fas fa-trash"></i>Remove
                                  </button>`;
            row.appendChild(actionTd);

            tbody.appendChild(row);
        }

        function removeTransactionRow(button) {
            const row = button.closest('tr');
            row.remove();
        }

        function clearTransactionRows() {
            document.getElementById('transactionRows').innerHTML = '';
            transactionCount = 0;
        }

        function togglePaymentFields() {
            const paymentMode = document.getElementById('payment_mode').value;
            const fieldsContainer = document.getElementById('paymentModeFields');

            let fields = '';

            if (paymentMode === 'UPI') {
                fields = `
                    <div class="form-group">
                        <label for="transaction_id">UPI Transaction ID *</label>
                        <input type="text" class="form-control" name="transaction_id" placeholder="e.g., 123456789012" required>
                    </div>
                `;
            } else if (paymentMode === 'Cheque') {
                fields = `
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cheque_no">Cheque Number *</label>
                                <input type="text" class="form-control" name="cheque_no" placeholder="e.g., 123456" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bank_name">Bank Name *</label>
                                <input type="text" class="form-control" name="bank_name" placeholder="e.g., HDFC Bank" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cheque_date">Cheque Date *</label>
                                <input type="date" class="form-control" name="cheque_date" required>
                            </div>
                        </div>
                    </div>
                `;
            }

            fieldsContainer.innerHTML = fields;
        }

        function resetForm() {
            document.getElementById('debitForm').reset();
            clearTransactionRows();
            document.getElementById('paymentModeFields').innerHTML = '';
            document.getElementById('transactionCategory').value = '';
            currentCategory = '';
        }

        // Initialize with today's date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            // Set default date for any date fields
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