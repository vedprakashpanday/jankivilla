<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

$success = '';
$error = '';

// Fetch accounts for dropdown
$accounts = [];
try {
    $account_query = $pdo->query("SELECT id, account_name FROM accounts ORDER BY account_name");
    $accounts = $account_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching accounts: " . $e->getMessage();
}

// if (isset($_POST['btn_save_expense'])) {
//     $expense_date = $_POST['expense_date'];
//     $account_id = $_POST['account_id'];
//     $authorized_by = $_POST['authorized_by'];
//     $expense_titles = $_POST['expense_title'] ?? [];
//     $expense_descriptions = $_POST['expense_description'] ?? [];
//     $amounts = $_POST['amount'] ?? [];
//     $expense_categories = $_POST['expense_category'] ?? [];
//     $other_categories = $_POST['other_category'] ?? [];
//     $payment_modes = $_POST['payment_mode'] ?? [];
//     $transaction_ids = $_POST['transaction_id'] ?? [];
//     $cheque_nos = $_POST['cheque_no'] ?? [];
//     $bank_names = $_POST['bank_name'] ?? [];
//     $cheque_dates = $_POST['cheque_date'] ?? [];
//     $notes = $_POST['note'] ?? [];

//     if (empty($expense_date) || empty($account_id) || empty($authorized_by)) {
//         $error = "Please fill in expense date, account, and authorized by fields.";
//     } elseif (empty($expense_titles)) {
//         $error = "At least one expense entry is required.";
//     } else {
//         $validation_errors = [];
//         $valid_entries = [];

//         for ($i = 0; $i < count($expense_titles); $i++) {
//             $entry_error = [];
//             if (empty($expense_titles[$i])) $entry_error[] = "Title is required";
//             if (empty($amounts[$i]) || $amounts[$i] <= 0) $entry_error[] = "Valid amount is required";
//             if (empty($expense_categories[$i])) $entry_error[] = "Category is required";
//             if (empty($payment_modes[$i])) $entry_error[] = "Payment mode is required";
//             if ($expense_categories[$i] === 'Other' && empty($other_categories[$i])) $entry_error[] = "Other category specification is required";
//             if ($payment_modes[$i] === 'UPI' && empty($transaction_ids[$i])) $entry_error[] = "UPI Transaction ID is required";
//             if ($payment_modes[$i] === 'Cheque' && (empty($cheque_nos[$i]) || empty($bank_names[$i]) || empty($cheque_dates[$i]))) {
//                 $entry_error[] = "Cheque details are required";
//             }

//             if (!empty($entry_error)) {
//                 $validation_errors[] = "Row " . ($i + 1) . ": " . implode(", ", $entry_error);
//             } else {
//                 $valid_entries[] = [
//                     'expense_date' => $expense_date,
//                     'account_id' => $account_id,
//                     'authorized_by' => $authorized_by,
//                     'expense_title' => $expense_titles[$i],
//                     'expense_description' => $expense_descriptions[$i] ?: null,
//                     'amount' => $amounts[$i],
//                     'expense_category' => $expense_categories[$i],
//                     'other_category' => $expense_categories[$i] === 'Other' ? $other_categories[$i] : null,
//                     'payment_mode' => $payment_modes[$i],
//                     'transaction_id' => $payment_modes[$i] === 'UPI' ? $transaction_ids[$i] : null,
//                     'cheque_no' => $payment_modes[$i] === 'Cheque' ? $cheque_nos[$i] : null,
//                     'bank_name' => $payment_modes[$i] === 'Cheque' ? $bank_names[$i] : null,
//                     'cheque_date' => $payment_modes[$i] === 'Cheque' ? $cheque_dates[$i] : null,
//                     'note' => $notes[$i] ?: null
//                 ];
//             }
//         }

//         if (!empty($validation_errors)) {
//             $error = implode("<br>", $validation_errors);
//         } elseif (empty($valid_entries)) {
//             $error = "No valid entries found to save.";
//         } else {
//             try {
//                 $pdo->beginTransaction();

//                 $total_expense_amount = 0;
//                 foreach ($valid_entries as $entry) {
//                     $total_expense_amount += $entry['amount'];
//                 }

//                 // Insert transactions
//                 foreach ($valid_entries as $entry) {
//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at) 
//                             VALUES (:account_id, :txn_date, :description, :amount, :get_from, 'debit', :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([
//                         ':account_id' => $entry['account_id'],
//                         ':txn_date' => $entry['expense_date'],
//                         ':description' => $entry['expense_title'] . ($entry['expense_description'] ? ' - ' . $entry['expense_description'] : ''),
//                         ':amount' => $entry['amount'],
//                         ':get_from' => $entry['authorized_by'],
//                         ':expense_category' => $entry['expense_category'],
//                         ':other_category' => $entry['other_category'],
//                         ':payment_mode' => $entry['payment_mode'],
//                         ':transaction_id' => $entry['transaction_id'],
//                         ':cheque_no' => $entry['cheque_no'],
//                         ':bank_name' => $entry['bank_name'],
//                         ':cheque_date' => $entry['cheque_date']
//                     ]);
//                     $txn_id = $pdo->lastInsertId();

//                     // Insert into tbl_daily_expenses
//                     $sql = "INSERT INTO tbl_daily_expenses (expense_date, account_id, authorized_by, expense_title, expense_description, amount, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, note, created_at, txn_id) 
//                             VALUES (:expense_date, :account_id, :authorized_by, :expense_title, :expense_description, :amount, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, :note, NOW(), :txn_id)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([
//                         ':expense_date' => $entry['expense_date'],
//                         ':account_id' => $entry['account_id'],
//                         ':authorized_by' => $entry['authorized_by'],
//                         ':expense_title' => $entry['expense_title'],
//                         ':expense_description' => $entry['expense_description'],
//                         ':amount' => $entry['amount'],
//                         ':expense_category' => $entry['expense_category'],
//                         ':other_category' => $entry['other_category'],
//                         ':payment_mode' => $entry['payment_mode'],
//                         ':transaction_id' => $entry['transaction_id'],
//                         ':cheque_no' => $entry['cheque_no'],
//                         ':bank_name' => $entry['bank_name'],
//                         ':cheque_date' => $entry['cheque_date'],
//                         ':note' => $entry['note'],
//                         ':txn_id' => $txn_id
//                     ]);

//                     // Update balances
//                     $sql = "SELECT balance FROM tbl_account_balances WHERE account_id = :account_id ORDER BY updated_at DESC LIMIT 1";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([':account_id' => $entry['account_id']]);
//                     $latest_balance = $stmt->fetchColumn() ?: 0.00;

//                     $new_balance = $latest_balance - $entry['amount'];

//                     $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                             VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([
//                         ':account_id' => $entry['account_id'],
//                         ':balance' => $new_balance,
//                         ':txn_id' => $txn_id,
//                         ':txn_date' => $entry['expense_date']
//                     ]);

//                     $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                             VALUES (:account_id, :balance_date, :opening_balance, 0.00, :total_debit, :opening_balance - :total_debit, 0) 
//                             ON DUPLICATE KEY UPDATE 
//                             total_debit = total_debit + :total_debit,
//                             closing_balance = opening_balance + total_credit - total_debit - :total_debit";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([
//                         ':account_id' => $entry['account_id'],
//                         ':balance_date' => $entry['expense_date'],
//                         ':opening_balance' => $latest_balance,
//                         ':total_debit' => $entry['amount']
//                     ]);

//                     $sql = "UPDATE accounts SET current_balance = :balance, last_transaction_date = :txn_date WHERE id = :account_id";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->execute([
//                         ':balance' => $new_balance,
//                         ':txn_date' => $entry['expense_date'],
//                         ':account_id' => $entry['account_id']
//                     ]);
//                 }

//                 $pdo->commit();
//                 $success = "Successfully recorded $inserted_count expense(s) and updated account balance.";
//                 $_POST = [];
//             } catch (PDOException $e) {
//                 $pdo->rollBack();
//                 $error = "Database Error: " . $e->getMessage();
//             }
//         }
//     }
// }


if (isset($_POST['btn_save_expense'])) {
    $expense_date = $_POST['expense_date'];
    $account_id = $_POST['account_id'];
    $authorized_by = $_POST['authorized_by'];
    $expense_titles = $_POST['expense_title'] ?? [];
    $expense_descriptions = $_POST['expense_description'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    $expense_categories = $_POST['expense_category'] ?? [];
    $other_categories = $_POST['other_category'] ?? [];
    $payment_modes = $_POST['payment_mode'] ?? [];
    $transaction_ids = $_POST['transaction_id'] ?? [];
    $cheque_nos = $_POST['cheque_no'] ?? [];
    $bank_names = $_POST['bank_name'] ?? [];
    $cheque_dates = $_POST['cheque_date'] ?? [];
    $notes = $_POST['note'] ?? [];

    if (empty($expense_date) || empty($account_id) || empty($authorized_by)) {
        $error = "Please fill in expense date, account, and authorized by fields.";
    } elseif (empty($expense_titles)) {
        $error = "At least one expense entry is required.";
    } else {
        $validation_errors = [];
        $valid_entries = [];

        for ($i = 0; $i < count($expense_titles); $i++) {
            $entry_error = [];
            if (empty($expense_titles[$i])) $entry_error[] = "Title is required";
            if (empty($amounts[$i]) || $amounts[$i] <= 0) $entry_error[] = "Valid amount is required";
            if (empty($expense_categories[$i])) $entry_error[] = "Category is required";
            if (empty($payment_modes[$i])) $entry_error[] = "Payment mode is required";
            if ($expense_categories[$i] === 'Other' && empty($other_categories[$i])) $entry_error[] = "Other category specification is required";
            if ($payment_modes[$i] === 'UPI' && empty($transaction_ids[$i])) $entry_error[] = "UPI Transaction ID is required";
            if ($payment_modes[$i] === 'Cheque' && (empty($cheque_nos[$i]) || empty($bank_names[$i]) || empty($cheque_dates[$i]))) {
                $entry_error[] = "Cheque details are required";
            }

            if (!empty($entry_error)) {
                $validation_errors[] = "Row " . ($i + 1) . ": " . implode(", ", $entry_error);
            } else {
                $valid_entries[] = [
                    'expense_date' => $expense_date,
                    'account_id' => $account_id,
                    'authorized_by' => $authorized_by,
                    'expense_title' => $expense_titles[$i],
                    'expense_description' => $expense_descriptions[$i] ?: null,
                    'amount' => $amounts[$i],
                    'expense_category' => $expense_categories[$i],
                    'other_category' => $expense_categories[$i] === 'Other' ? $other_categories[$i] : null,
                    'payment_mode' => $payment_modes[$i],
                    'transaction_id' => $payment_modes[$i] === 'UPI' ? $transaction_ids[$i] : null,
                    'cheque_no' => $payment_modes[$i] === 'Cheque' ? $cheque_nos[$i] : null,
                    'bank_name' => $payment_modes[$i] === 'Cheque' ? $bank_names[$i] : null,
                    'cheque_date' => $payment_modes[$i] === 'Cheque' ? $cheque_dates[$i] : null,
                    'note' => $notes[$i] ?: null
                ];
            }
        }

        if (!empty($validation_errors)) {
            $error = implode("<br>", $validation_errors);
        } elseif (empty($valid_entries)) {
            $error = "No valid entries found to save.";
        } else {
            try {
                $pdo->beginTransaction();
                $inserted_count = 0;

                foreach ($valid_entries as $entry) {
                    // Get latest balance
                    $stmt = $pdo->prepare("SELECT balance FROM tbl_account_balances WHERE account_id = :account_id ORDER BY updated_at DESC LIMIT 1");
                    $stmt->execute([':account_id' => $entry['account_id']]);
                    $latest_balance = $stmt->fetchColumn() ?: 0.00;

                    $new_balance = $latest_balance - $entry['amount'];

                    // Insert transaction
                    $stmt = $pdo->prepare("INSERT INTO tbl_transactions 
                        (account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at) 
                        VALUES 
                        (:account_id, :txn_date, :description, :amount, :get_from, 'debit', :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, NOW())");
                    $stmt->execute([
                        ':account_id' => $entry['account_id'],
                        ':txn_date' => $entry['expense_date'],
                        ':description' => $entry['expense_title'] . ($entry['expense_description'] ? ' - ' . $entry['expense_description'] : ''),
                        ':amount' => $entry['amount'],
                        ':get_from' => $entry['authorized_by'],
                        ':expense_category' => $entry['expense_category'],
                        ':other_category' => $entry['other_category'],
                        ':payment_mode' => $entry['payment_mode'],
                        ':transaction_id' => $entry['transaction_id'],
                        ':cheque_no' => $entry['cheque_no'],
                        ':bank_name' => $entry['bank_name'],
                        ':cheque_date' => $entry['cheque_date']
                    ]);
                    $txn_id = $pdo->lastInsertId();

                    // Insert into daily expenses
                    $stmt = $pdo->prepare("INSERT INTO tbl_daily_expenses 
                        (expense_date, account_id, authorized_by, expense_title, expense_description, amount, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, note, created_at, txn_id) 
                        VALUES 
                        (:expense_date, :account_id, :authorized_by, :expense_title, :expense_description, :amount, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, :note, NOW(), :txn_id)");
                    $stmt->execute([
                        ':expense_date' => $entry['expense_date'],
                        ':account_id' => $entry['account_id'],
                        ':authorized_by' => $entry['authorized_by'],
                        ':expense_title' => $entry['expense_title'],
                        ':expense_description' => $entry['expense_description'],
                        ':amount' => $entry['amount'],
                        ':expense_category' => $entry['expense_category'],
                        ':other_category' => $entry['other_category'],
                        ':payment_mode' => $entry['payment_mode'],
                        ':transaction_id' => $entry['transaction_id'],
                        ':cheque_no' => $entry['cheque_no'],
                        ':bank_name' => $entry['bank_name'],
                        ':cheque_date' => $entry['cheque_date'],
                        ':note' => $entry['note'],
                        ':txn_id' => $txn_id
                    ]);

                    // Update balance history
                    $stmt = $pdo->prepare("INSERT INTO tbl_account_balances 
                        (account_id, balance, txn_id, txn_date, updated_at) 
                        VALUES 
                        (:account_id, :balance, :txn_id, :txn_date, NOW())");
                    $stmt->execute([
                        ':account_id' => $entry['account_id'],
                        ':balance' => $new_balance,
                        ':txn_id' => $txn_id,
                        ':txn_date' => $entry['expense_date']
                    ]);

                    // Update daily balance
                    $stmt = $pdo->prepare("INSERT INTO tbl_daily_balances 
                        (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
                        VALUES 
                        (:account_id, :balance_date, :opening_balance, 0.00, :total_debit, :closing_balance, 0) 
                        ON DUPLICATE KEY UPDATE 
                        total_debit = total_debit + :total_debit,
                        closing_balance = opening_balance + total_credit - total_debit - :total_debit");
                    $stmt->execute([
                        ':account_id' => $entry['account_id'],
                        ':balance_date' => $entry['expense_date'],
                        ':opening_balance' => $latest_balance,
                        ':total_debit' => $entry['amount'],
                        ':closing_balance' => $latest_balance - $entry['amount']
                    ]);

                    // Update main account table
                    $stmt = $pdo->prepare("UPDATE accounts SET current_balance = :balance, last_transaction_date = :txn_date WHERE id = :account_id");
                    $stmt->execute([
                        ':balance' => $new_balance,
                        ':txn_date' => $entry['expense_date'],
                        ':account_id' => $entry['account_id']
                    ]);

                    $inserted_count++;
                }

                $pdo->commit();
                $success = "Successfully recorded $inserted_count expense(s) and updated account balances.";
                $_POST = [];
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}

// Display messages
if (!empty($success)) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $success .
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

if (!empty($error)) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $error .
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
?>

<!-- Populate accounts dropdown -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const accountSelect = document.getElementById('account_id');
        const accounts = <?php echo json_encode($accounts); ?>;

        accounts.forEach(account => {
            const option = document.createElement('option');
            option.value = account.id;
            option.textContent = account.account_name;
            accountSelect.appendChild(option);
        });
    });
</script>


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
        .expense-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }

        .add-row-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .add-row-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .remove-row-btn {
            background: #dc3545;
            border: none;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .remove-row-btn:hover {
            background: #c82333;
        }

        .form-control,
        .form-select {
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .expense-row {
            transition: all 0.3s ease;
        }

        .expense-row:hover {
            background-color: #f8f9fa;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            font-size: 18px;
        }

        .payment-fields {
            display: none;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-top: 4px;
        }

        .payment-fields.show {
            display: block;
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

                            <?php if ($success_message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                            <?php endif; ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                            <?php endif; ?>

                            <div class="expense-form-container">
                                <!-- Expense Form -->
                                <div class="card-header text-center">
                                    <h3 class="mb-0">Daily Expense Entry</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="expenseForm">
                                        <!-- Common Fields -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="expense_date" class="form-label fw-bold">Expense Date</label>
                                                <input type="date" class="form-control" name="expense_date" id="expense_date" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="account_id" class="form-label fw-bold">Select Account</label>
                                                <select class="form-select" name="account_id" id="account_id" required>
                                                    <option value="">-- Select Account --</option>
                                                    <!-- This will be populated by PHP -->
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="authorized_by" class="form-label fw-bold">Authorized By</label>
                                                <input type="text" class="form-control" name="authorized_by" id="authorized_by" placeholder="Enter authorizer name" required>
                                            </div>
                                        </div>

                                        <!-- Expense Table -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="expenseTable">
                                                <thead class="table-header">
                                                    <tr>
                                                        <th width="15%">Title</th>
                                                        <th width="20%">Description</th>
                                                        <th width="10%">Amount (₹)</th>
                                                        <th width="12%">Category</th>
                                                        <th width="12%">Payment Mode</th>
                                                        <th width="20%">Payment Details</th>
                                                        <th width="8%">Note</th>
                                                        <th width="3%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="expenseTableBody">
                                                    <tr class="expense-row">
                                                        <td>
                                                            <input type="text" class="form-control" name="expense_title[]" placeholder="e.g., Office Supplies" required>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control" name="expense_description[]" rows="2" placeholder="Optional details..."></textarea>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" class="form-control" name="amount[]" placeholder="0.00" required>
                                                        </td>
                                                        <td>
                                                            <select class="form-select expense-category" name="expense_category[]" required>
                                                                <option value="">Select</option>
                                                                <option value="Office">Office</option>
                                                                <!-- <option value="Medical">Medical</option>
                                                                <option value="Utility">Utility</option>
                                                                <option value="Maintenance">Maintenance</option>
                                                                <option value="Commission">Commission</option> -->
                                                                <option value="Other">Other</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-1 other-category" name="other_category[]" placeholder="Specify category" style="display: none;">
                                                        </td>
                                                        <td>
                                                            <select class="form-select payment-mode" name="payment_mode[]" required>
                                                                <option value="">Select</option>
                                                                <option value="Cash">Cash</option>
                                                                <option value="UPI">UPI</option>
                                                                <option value="Cheque">Cheque</option>
                                                                <option value="Bank Transfer">Bank Transfer</option>
                                                                <option value="Card">Card</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="payment-details">
                                                                <!-- UPI Fields -->
                                                                <div class="payment-fields upi-fields">
                                                                    <input type="text" class="form-control" name="transaction_id[]" placeholder="UPI Transaction ID">
                                                                </div>
                                                                <!-- Cheque Fields -->
                                                                <div class="payment-fields cheque-fields">
                                                                    <input type="text" class="form-control mb-1" name="cheque_no[]" placeholder="Cheque No">
                                                                    <input type="text" class="form-control mb-1" name="bank_name[]" placeholder="Bank Name">
                                                                    <input type="date" class="form-control" name="cheque_date[]">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control" name="note[]" rows="2" placeholder="Note..."></textarea>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="remove-row-btn" onclick="removeRow(this)" title="Remove Row">×</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <button type="button" class="add-row-btn" onclick="addExpenseRow()">+ Add Another Expense</button>
                                            <button type="submit" name="btn_save_expense" class="submit-btn">Save All Expenses</button>
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
            // Set today's date as default
            document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];

            function addExpenseRow() {
                const tableBody = document.getElementById('expenseTableBody');
                const newRow = `
                <tr class="expense-row">
                    <td>
                        <input type="text" class="form-control" name="expense_title[]" placeholder="e.g., Office Supplies" required>
                    </td>
                    <td>
                        <textarea class="form-control" name="expense_description[]" rows="2" placeholder="Optional details..."></textarea>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control" name="amount[]" placeholder="0.00" required>
                    </td>
                    <td>
                        <select class="form-select expense-category" name="expense_category[]" required>
                            <option value="">Select</option>
                            <option value="Office">Office</option>
                            <option value="Medical">Medical</option>
                            <option value="Utility">Utility</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Commission">Commission</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control mt-1 other-category" name="other_category[]" placeholder="Specify category" style="display: none;">
                    </td>
                    <td>
                        <select class="form-select payment-mode" name="payment_mode[]" required>
                            <option value="">Select</option>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Card">Card</option>
                        </select>
                    </td>
                    <td>
                        <div class="payment-details">
                            <!-- UPI Fields -->
                            <div class="payment-fields upi-fields">
                                <input type="text" class="form-control" name="transaction_id[]" placeholder="UPI Transaction ID">
                            </div>
                            <!-- Cheque Fields -->
                            <div class="payment-fields cheque-fields">
                                <input type="text" class="form-control mb-1" name="cheque_no[]" placeholder="Cheque No">
                                <input type="text" class="form-control mb-1" name="bank_name[]" placeholder="Bank Name">
                                <input type="date" class="form-control" name="cheque_date[]">
                            </div>
                        </div>
                    </td>
                    <td>
                        <textarea class="form-control" name="note[]" rows="2" placeholder="Note..."></textarea>
                    </td>
                    <td>
                        <button type="button" class="remove-row-btn" onclick="removeRow(this)" title="Remove Row">×</button>
                    </td>
                </tr>
            `;
                tableBody.insertAdjacentHTML('beforeend', newRow);
                attachEventListeners();
            }

            function removeRow(button) {
                const row = button.closest('tr');
                const tableBody = document.getElementById('expenseTableBody');
                if (tableBody.children.length > 1) {
                    row.remove();
                } else {
                    alert('At least one expense entry is required.');
                }
            }

            function attachEventListeners() {
                // Handle category change
                document.querySelectorAll('.expense-category').forEach(select => {
                    select.addEventListener('change', function() {
                        const otherInput = this.parentNode.querySelector('.other-category');
                        if (this.value === 'Other') {
                            otherInput.style.display = 'block';
                            otherInput.required = true;
                        } else {
                            otherInput.style.display = 'none';
                            otherInput.required = false;
                            otherInput.value = '';
                        }
                    });
                });

                // Handle payment mode change
                document.querySelectorAll('.payment-mode').forEach(select => {
                    select.addEventListener('change', function() {
                        const paymentDetails = this.closest('td').nextElementSibling.querySelector('.payment-details');
                        const upiFields = paymentDetails.querySelector('.upi-fields');
                        const chequeFields = paymentDetails.querySelector('.cheque-fields');

                        // Hide all payment fields first
                        upiFields.classList.remove('show');
                        chequeFields.classList.remove('show');

                        // Clear required attributes
                        upiFields.querySelectorAll('input').forEach(input => {
                            input.required = false;
                            input.value = '';
                        });
                        chequeFields.querySelectorAll('input').forEach(input => {
                            input.required = false;
                            input.value = '';
                        });

                        // Show relevant fields based on selection
                        if (this.value === 'UPI') {
                            upiFields.classList.add('show');
                            upiFields.querySelector('input[name="transaction_id[]"]').required = true;
                        } else if (this.value === 'Cheque') {
                            chequeFields.classList.add('show');
                            chequeFields.querySelectorAll('input').forEach(input => {
                                input.required = true;
                            });
                            // Set default cheque date to today
                            chequeFields.querySelector('input[name="cheque_date[]"]').value = new Date().toISOString().split('T')[0];
                        }
                    });
                });
            }

            // Initialize event listeners for the first row
            attachEventListeners();

            // Form validation before submit
            document.getElementById('expenseForm').addEventListener('submit', function(e) {
                const rows = document.querySelectorAll('#expenseTableBody tr');
                let hasError = false;

                rows.forEach((row, index) => {
                    const amount = row.querySelector('input[name="amount[]"]').value;
                    const paymentMode = row.querySelector('select[name="payment_mode[]"]').value;
                    const category = row.querySelector('select[name="expense_category[]"]').value;

                    if (amount && parseFloat(amount) <= 0) {
                        alert(`Row ${index + 1}: Amount must be greater than zero.`);
                        hasError = true;
                        return;
                    }

                    if (paymentMode === 'UPI') {
                        const transactionId = row.querySelector('input[name="transaction_id[]"]').value;
                        if (!transactionId.trim()) {
                            alert(`Row ${index + 1}: UPI Transaction ID is required for UPI payments.`);
                            hasError = true;
                            return;
                        }
                    }

                    if (paymentMode === 'Cheque') {
                        const chequeNo = row.querySelector('input[name="cheque_no[]"]').value;
                        const bankName = row.querySelector('input[name="bank_name[]"]').value;
                        const chequeDate = row.querySelector('input[name="cheque_date[]"]').value;

                        if (!chequeNo.trim() || !bankName.trim() || !chequeDate) {
                            alert(`Row ${index + 1}: Cheque Number, Bank Name, and Cheque Date are required for Cheque payments.`);
                            hasError = true;
                            return;
                        }
                    }

                    if (category === 'Other') {
                        const otherCategory = row.querySelector('input[name="other_category[]"]').value;
                        if (!otherCategory.trim()) {
                            alert(`Row ${index + 1}: Please specify the category when "Other" is selected.`);
                            hasError = true;
                            return;
                        }
                    }
                });

                if (hasError) {
                    e.preventDefault();
                }
            });
        </script>


        <!-- <script>
            document.getElementById('expense_category').addEventListener('change', function() {
                document.getElementById('other_category_group').style.display = this.value === 'Other' ? 'block' : 'none';
            });

            document.getElementById('payment_mode').addEventListener('change', function() {
                document.getElementById('upi_fields').style.display = this.value === 'UPI' ? 'block' : 'none';
                document.getElementById('cheque_fields').style.display = this.value === 'Cheque' ? 'block' : 'none';
            });
        </script> -->


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