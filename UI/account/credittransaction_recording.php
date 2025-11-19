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

// Fetch accounts for dropdown
$sql = "SELECT id, account_name FROM accounts ORDER BY account_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_transaction'])) {
//     $account_id = $_POST['account_id'];
//     $txn_date = $_POST['transaction_date'];
//     $description = $_POST['description'];
//     $amount = $_POST['amount'];
//     $type = $_POST['type'];
//     $expense_category = $_POST['expense_category'] ?: null;
//     $other_category = $_POST['other_category'] ?: null;
//     $payment_mode = $_POST['payment_mode'] ?: null;
//     $get_from = $_POST['get_from'] ?: null;
//     $transaction_id = $_POST['transaction_id'] ?: null;
//     $cheque_no = $_POST['cheque_no'] ?: null;
//     $bank_name = $_POST['bank_name'] ?: null;
//     $cheque_date = $_POST['cheque_date'] ?: null;

//     // Validate inputs
//     if (empty($account_id) || empty($txn_date) || empty($description) || empty($amount) || empty($type)) {
//         $error = "Please fill in all required fields.";
//     } elseif ($amount <= 0) {
//         $error = "Amount must be greater than zero.";
//     } elseif ($payment_mode === 'UPI' && empty($transaction_id)) {
//         $error = "UPI Transaction ID is required for UPI payments.";
//     } elseif ($payment_mode === 'Cheque' && (empty($cheque_no) || empty($bank_name) || empty($cheque_date))) {
//         $error = "Cheque Number, Bank Name, and Cheque Date are required for Cheque payments.";
//     } else {
//         // Validate account_id exists
//         $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id]);
//         if ($stmt->fetchColumn() == 0) {
//             $error = "Invalid account selected.";
//         } else {
//             try {
//                 // Start transaction
//                 $pdo->beginTransaction();

//                 // Insert transaction
//                 $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at) 
//                         VALUES (:account_id, :txn_date, :description, :amount, :get_from, :type, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, NOW())";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->execute([
//                     ':account_id' => $account_id,
//                     ':txn_date' => $txn_date,
//                     ':description' => $description,
//                     ':amount' => $amount,
//                     ':get_from' => $get_from,
//                     ':type' => $type,
//                     ':expense_category' => $expense_category,
//                     ':other_category' => $expense_category === 'Other' ? $other_category : null,
//                     ':payment_mode' => $payment_mode,
//                     ':transaction_id' => $payment_mode === 'UPI' ? $transaction_id : null,
//                     ':cheque_no' => $payment_mode === 'Cheque' ? $cheque_no : null,
//                     ':bank_name' => $payment_mode === 'Cheque' ? $bank_name : null,
//                     ':cheque_date' => $payment_mode === 'Cheque' ? $cheque_date : null
//                 ]);
//                 $txn_id = $pdo->lastInsertId();

//                 // Fetch the latest balance or opening balance
//                 $sql = "SELECT balance FROM tbl_account_balances 
//                         WHERE account_id = :account_id 
//                         ORDER BY updated_at DESC LIMIT 1";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->execute([':account_id' => $account_id]);
//                 $latest_balance = $stmt->fetchColumn();

//                 if ($latest_balance === false) {
//                     // No balance records; use opening balance
//                     $sql = "SELECT opening_balance FROM accounts WHERE id = :account_id";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([':account_id' => $account_id]);
//                     $latest_balance = $stmt->fetchColumn() ?: 0.00;
//                 }

//                 // Calculate new balance
//                 $new_balance = $type === 'credit' ? $latest_balance + $amount : $latest_balance - $amount;

//                 // Insert balance update
//                 $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                         VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->execute([
//                     ':account_id' => $account_id,
//                     ':balance' => $new_balance,
//                     ':txn_id' => $txn_id,
//                     ':txn_date' => $txn_date
//                 ]);

//                 // Commit transaction
//                 $pdo->commit();
//                 $success = "Transaction added successfully!";
//             } catch (PDOException $e) {
//                 // Rollback on error
//                 $pdo->rollBack();
//                 $error = "Error saving transaction: " . $e->getMessage();
//             }
//         }
//     }
// }




if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_transaction'])) {
    $account_id = trim($_POST['account_id']);
    $txn_date = $_POST['transaction_date'];
    $description = $_POST['description'];
    $amount = floatval($_POST['amount']);
    $type = $_POST['type'];
    $expense_category = $_POST['expense_category'] ?: null;
    $other_category = $_POST['other_category'] ?: null;
    $payment_mode = $_POST['payment_mode'] ?: null;
    $get_from = $_POST['get_from'] ?: null;
    $transaction_id = $_POST['transaction_id'] ?: null;
    $cheque_no = $_POST['cheque_no'] ?: null;
    $bank_name = $_POST['bank_name'] ?: null;
    $cheque_date = $_POST['cheque_date'] ?: null;

    // Validate inputs
    if (empty($account_id) || empty($txn_date) || empty($description) || empty($amount) || empty($type) || empty($payment_mode)) {
        $error = "Please fill in all required fields.";
    } elseif ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } elseif ($payment_mode === 'UPI' && empty($transaction_id)) {
        $error = "UPI Transaction ID is required for UPI payments.";
    } elseif ($payment_mode === 'Cheque' && (empty($cheque_no) || empty($bank_name) || empty($cheque_date))) {
        $error = "Cheque Number, Bank Name, and Cheque Date are required for Cheque payments.";
    } elseif (!in_array($type, ['credit', 'debit'])) {
        $error = "Invalid transaction type. Must be 'credit' or 'debit'.";
    } else {
        try {
            $pdo->beginTransaction();

            $temp_account_id = null;
            $formal_account_id = null;

            // Handle account
            if (!is_numeric($account_id)) {
                // Check tbl_temp_accounts
                $sql = "SELECT id FROM tbl_temp_accounts WHERE account_name = :account_name";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':account_name' => $account_id]);
                $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$temp_account) {
                    // Check accounts to avoid duplicates
                    $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':account_name' => $account_id]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("Account name '$account_id' already exists in accounts. Please set opening balance first.");
                    }

                    // Insert into tbl_temp_accounts
                    $sql = "INSERT INTO tbl_temp_accounts (account_name, created_at) VALUES (:account_name, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':account_name' => $account_id]);
                    $temp_account_id = $pdo->lastInsertId();
                } else {
                    $temp_account_id = $temp_account['id'];
                }

                // Insert into tbl_temp_transactions for new accounts
                $sql = "INSERT INTO tbl_temp_transactions (account_name, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at) 
                        VALUES (:account_name, :txn_date, :description, :amount, :get_from, :type, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_name' => $account_id,
                    ':txn_date' => $txn_date,
                    ':description' => $description,
                    ':amount' => $amount,
                    ':get_from' => $get_from,
                    ':type' => $type,
                    ':expense_category' => $expense_category,
                    ':other_category' => $expense_category === 'Other' ? $other_category : null,
                    ':payment_mode' => $payment_mode,
                    ':transaction_id' => $payment_mode === 'UPI' ? $transaction_id : null,
                    ':cheque_no' => $payment_mode === 'Cheque' ? $cheque_no : null,
                    ':bank_name' => $payment_mode === 'Cheque' ? $bank_name : null,
                    ':cheque_date' => $payment_mode === 'Cheque' ? $cheque_date : null
                ]);
            } else {
                // Validate existing account
                $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':account_id' => $account_id]);
                if ($stmt->fetchColumn() == 0) {
                    throw new Exception("Invalid account selected.");
                }
                $formal_account_id = $account_id;

                // Insert into tbl_transactions for formal accounts
                $sql = "INSERT INTO tbl_transactions (account_id, temp_account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at, is_opening_balance) 
                        VALUES (:account_id, :temp_account_id, :txn_date, :description, :amount, :get_from, :type, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, NOW(), 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $formal_account_id,
                    ':temp_account_id' => null,
                    ':txn_date' => $txn_date,
                    ':description' => $description,
                    ':amount' => $amount,
                    ':get_from' => $get_from,
                    ':type' => $type,
                    ':expense_category' => $expense_category,
                    ':other_category' => $expense_category === 'Other' ? $other_category : null,
                    ':payment_mode' => $payment_mode,
                    ':transaction_id' => $payment_mode === 'UPI' ? $transaction_id : null,
                    ':cheque_no' => $payment_mode === 'Cheque' ? $cheque_no : null,
                    ':bank_name' => $payment_mode === 'Cheque' ? $bank_name : null,
                    ':cheque_date' => $payment_mode === 'Cheque' ? $cheque_date : null
                ]);
                $txn_id = $pdo->lastInsertId();

                // Update balances for formal accounts
                $sql = "SELECT balance FROM tbl_account_balances WHERE account_id = :account_id ORDER BY updated_at DESC LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':account_id' => $formal_account_id]);
                $latest_balance = $stmt->fetchColumn() ?: 0.00;

                $new_balance = $type === 'credit' ? $latest_balance + $amount : $latest_balance - $amount;

                $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                        VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $formal_account_id,
                    ':balance' => $new_balance,
                    ':txn_id' => $txn_id,
                    ':txn_date' => $txn_date
                ]);

                $sql = "SELECT id FROM tbl_daily_balances WHERE account_id = :account_id AND balance_date = :balance_date";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':account_id' => $formal_account_id, ':balance_date' => $txn_date]);
                $daily_balance = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$daily_balance) {
                    $sql = "SELECT closing_balance FROM tbl_daily_balances 
                            WHERE account_id = :account_id AND balance_date < :balance_date AND is_closed = 1 
                            ORDER BY balance_date DESC LIMIT 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':account_id' => $formal_account_id, ':balance_date' => $txn_date]);
                    $opening_balance = $stmt->fetchColumn() ?: 0.00;

                    $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
                            VALUES (:account_id, :balance_date, :opening_balance, 0.00, 0.00, :opening_balance, 0)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':account_id' => $formal_account_id,
                        ':balance_date' => $txn_date,
                        ':opening_balance' => $opening_balance
                    ]);
                }

                // $sql = "UPDATE tbl_daily_balances 
                //         SET total_credit = total_credit + :credit_amount,
                //             total_debit = total_debit + :debit_amount,
                //             closing_balance = opening_balance + total_credit + :credit_amount - total_debit - :debit_amount,
                //             updated_at = NOW()
                //         WHERE account_id = :account_id AND balance_date = :balance_date";

                $sql = "UPDATE tbl_daily_balances 
                        SET total_credit = total_credit + :credit_amount,
                            total_debit = total_debit + :debit_amount,
                            closing_balance = opening_balance + total_credit - total_debit,
                            updated_at = NOW()
                        WHERE account_id = :account_id AND balance_date = :balance_date";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $formal_account_id,
                    ':balance_date' => $txn_date,
                    ':credit_amount' => $type === 'credit' ? $amount : 0,
                    ':debit_amount' => $type === 'debit' ? $amount : 0
                ]);

                $sql = "UPDATE accounts SET current_balance = :balance, last_transaction_date = :txn_date WHERE id = :account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':balance' => $new_balance,
                    ':txn_date' => $txn_date,
                    ':account_id' => $formal_account_id
                ]);
            }

            $pdo->commit();
            $success = "Transaction added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error saving transaction: " . $e->getMessage();
        }
    }
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
        .expense-form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .expense-form-container h3 {
            margin-bottom: 25px;
            color: #343a40;
            text-align: center;
            font-weight: 600;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 12px;
        }

        .btn-save {
            width: 100%;
            background-color: #007bff;
            border: none;
            color: white;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-save:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
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

                            <div class="expense-form-container">
                                <h3 class="text-center mb-4">Credit</h3>

                                <!-- Display success or error message -->
                                <?php if ($success): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php endif; ?>
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>



                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="account_id">Account</label>
                                        <select class="form-control select2" name="account_id" id="account_id" required>
                                            <option value="">-- Select or type account name --</option>
                                            <?php foreach ($accounts as $account): ?>
                                                <option value="<?php echo $account['id']; ?>" <?php echo $account['id'] == $selected_account_id ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($account['account_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="transaction_date">Date</label>
                                        <input type="date" class="form-control" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Transaction Type</label>
                                        <select class="form-control" name="type" required>
                                            <option value="credit" selected>Credit (In)</option>
                                            <!-- <option value="debit">Debit (Out)</option> -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="get_from">From (Credit)</label>
                                        <input type="text" class="form-control" name="get_from" placeholder="e.g., Received from / Paid to..." required>
                                    </div>
                                    <div class="form-group">
                                        <label for="amount">Amount (₹):</label>
                                        <input type="number" step="0.01" class="form-control" name="amount" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <input type="text" class="form-control" name="description" placeholder="e.g., Plot Sale / Office Supplies" required>
                                    </div>
                                    <div class="form-group d-none">
                                        <label for="expense_category">Category</label>
                                        <select class="form-control" name="expense_category" id="expense_category">
                                            <option value="">-- Select Category --</option>
                                            <option value="Office">Office</option>
                                            <option value="Medical">Medical</option>
                                            <option value="Utility">Utility</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Commission">Commission</option>
                                            <option value="Fuel">Fuel</option>
                                            <option value="Farmer Payment">Farmer Payment</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="other_category_group" style="display: none;">
                                        <label for="other_category">Specify Other Category</label>
                                        <input type="text" class="form-control" name="other_category" id="other_category" placeholder="Enter category">
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_mode">Payment Mode</label>
                                        <select class="form-control" name="payment_mode" id="payment_mode" required>
                                            <option value="">-- Select Mode --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="UPI">UPI</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Card">Card</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="upi_fields" style="display: none;">
                                        <label for="transaction_id">UPI Transaction ID</label>
                                        <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="e.g., 123456789012">
                                    </div>
                                    <div class="form-group" id="cheque_fields" style="display: none;">
                                        <label for="cheque_no">Cheque Number</label>
                                        <input type="text" class="form-control" name="cheque_no" id="cheque_no" placeholder="e.g., 123456">
                                        <label for="bank_name" class="mt-2">Bank Name</label>
                                        <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="e.g., HDFC Bank">
                                        <label for="cheque_date" class="mt-2">Cheque Date</label>
                                        <input type="date" class="form-control" name="cheque_date" id="cheque_date">
                                    </div>
                                    <div class="d-flex justify-content-center mt-4">
                                        <button type="submit" name="btn_add_transaction" class="btn btn-primary">Add Transaction</button>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#account_id').select2({
                placeholder: "-- Select or type account name --",
                allowClear: true,
                tags: true, // Allows typing new values
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term, // New account name as value
                        text: term,
                        newTag: true
                    };
                }
            });
        });
    </script>

    <script>
        document.getElementById('expense_category').addEventListener('change', function() {
            document.getElementById('other_category_group').style.display = this.value === 'Other' ? 'block' : 'none';
        });

        document.getElementById('payment_mode').addEventListener('change', function() {
            document.getElementById('upi_fields').style.display = this.value === 'UPI' ? 'block' : 'none';
            document.getElementById('cheque_fields').style.display = this.value === 'Cheque' ? 'block' : 'none';
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