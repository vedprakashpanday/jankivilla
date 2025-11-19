<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}




$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');
$selected_account_id = isset($_POST['account_id']) ? trim($_POST['account_id']) : null;
$success_message = '';
$error_message = '';

// Handle day closing
// if (isset($_POST['btn_close_day'])) {
//     $account_id = trim($_POST['account_id']);
//     $close_date = $_POST['close_date'];

//     if (empty($account_id) || empty($close_date)) {
//         $error_message = "Please fill in all required fields.";
//     } else {
//         try {
//             $pdo->beginTransaction();

//             // Validate account
//             $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([':account_id' => $account_id]);
//             if ($stmt->fetchColumn() == 0) {
//                 throw new Exception("Invalid account selected.");
//             }

//             // Check if day is already closed
//             $sql = "SELECT is_closed FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND balance_date = :balance_date";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//             $is_closed = $stmt->fetchColumn();
//             if ($is_closed === '1') {
//                 throw new Exception("Day is already closed.");
//             }

//             // Get opening balance from previous day's closing balance
//             $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND balance_date < :balance_date AND is_closed = 1 
//                     ORDER BY balance_date DESC LIMIT 1";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//             $opening_balance = $stmt->fetchColumn() ?: 0.00;

//             // Calculate total_credit and total_debit from transactions
//             $sql = "SELECT 
//                         COALESCE(SUM(CASE WHEN type = 'credit' AND is_opening_balance = 0 THEN amount ELSE 0 END), 0) AS total_credit,
//                         COALESCE(SUM(CASE WHEN type = 'debit' AND is_opening_balance = 0 THEN amount ELSE 0 END), 0) AS total_debit
//                     FROM tbl_transactions 
//                     WHERE account_id = :account_id AND txn_date = :balance_date";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//             $totals = $stmt->fetch(PDO::FETCH_ASSOC);
//             $total_credit = $totals['total_credit'];
//             $total_debit = $totals['total_debit'];

//             // Calculate closing balance
//             $closing_balance = $opening_balance + $total_credit - $total_debit;

//             // Update or insert tbl_daily_balances
//             $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed, created_at, updated_at) 
//                     VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 1, NOW(), NOW()) 
//                     ON DUPLICATE KEY UPDATE 
//                     opening_balance = :opening_balance,
//                     total_credit = :total_credit,
//                     total_debit = :total_debit,
//                     closing_balance = :closing_balance,
//                     is_closed = 1,
//                     updated_at = NOW()";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':account_id' => $account_id,
//                 ':balance_date' => $close_date,
//                 ':opening_balance' => $opening_balance,
//                 ':total_credit' => $total_credit,
//                 ':total_debit' => $total_debit,
//                 ':closing_balance' => $closing_balance
//             ]);

//             // Update accounts table
//             $sql = "UPDATE accounts 
//                     SET current_balance = :closing_balance, last_transaction_date = :close_date 
//                     WHERE id = :account_id";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':closing_balance' => $closing_balance,
//                 ':close_date' => $close_date,
//                 ':account_id' => $account_id
//             ]);

//             $pdo->commit();
//             $success_message = "Day closed successfully!";
//         } catch (Exception $e) {
//             $pdo->rollBack();
//             $error_message = "Error closing day: " . $e->getMessage();
//         }
//     }
// }

// Handle transaction update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_update_transaction'])) {
    try {
        $pdo->beginTransaction();

        $txn_id = $_POST['txn_id'];
        $source_table = $_POST['source_table'];
        $txn_date = $_POST['txn_date'];
        $amount = floatval($_POST['amount']);
        $type = $_POST['type'];
        $description = trim($_POST['description']);
        $authorized_by = trim($_POST['authorized_by']);
        $transaction_category = trim($_POST['transaction_category']);

        if ($amount < 0) {
            throw new Exception("Amount cannot be negative.");
        }

        // Fetch original transaction details
        $original_amount = 0.00;
        $original_type = '';
        $original_date = '';
        $account_id = null;
        if ($source_table === 'transaction') {
            $sql = "SELECT amount, type, txn_date, account_id FROM tbl_transactions WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $txn_id]);
            $original = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($original) {
                $original_amount = $original['amount'];
                $original_type = $original['type'];
                $original_date = $original['txn_date'];
                $account_id = $original['account_id'];
            }
        }

        // Update transaction
        if ($source_table === 'transaction') {
            $sql = "UPDATE tbl_transactions 
                    SET amount = :amount, type = :type, description = :description, 
                        authorized_by = :authorized_by, transaction_category = :transaction_category,
                        txn_date = :txn_date
                    WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':type' => $type,
                ':description' => $description,
                ':authorized_by' => $authorized_by,
                ':transaction_category' => $transaction_category,
                ':txn_date' => $txn_date,
                ':txn_id' => $txn_id
            ]);
        } else {
            $sql = "UPDATE tbl_temp_transactions 
                    SET amount = :amount, type = :type, description = :description,
                        txn_date = :txn_date
                    WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':type' => $type,
                ':description' => $description,
                ':txn_date' => $txn_date,
                ':txn_id' => $txn_id
            ]);

            $sql = "SELECT account_name FROM tbl_temp_transactions WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $txn_id]);
            $account_name = $stmt->fetchColumn();
        }

        // Recalculate balances for formal accounts
        if ($account_id) {
            // Determine earliest affected date
            $start_recalc_date = min($original_date, $txn_date);

            // Get the latest date in tbl_daily_balances or use end_date
            $sql = "SELECT MAX(balance_date) FROM tbl_daily_balances 
                    WHERE account_id = :account_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id]);
            $latest_balance_date = $stmt->fetchColumn() ?: $end_date;

            // Get all relevant dates
            $sql = "SELECT DISTINCT date_value FROM (
                        SELECT txn_date AS date_value FROM tbl_transactions 
                        WHERE account_id = :account_id AND txn_date >= :start_date AND txn_date <= :end_date
                        UNION
                        SELECT balance_date AS date_value FROM tbl_daily_balances 
                        WHERE account_id = :account_id AND balance_date >= :start_date AND balance_date <= :end_date
                    ) AS dates ORDER BY date_value";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $account_id,
                ':start_date' => $start_recalc_date,
                ':end_date' => $latest_balance_date
            ]);
            $all_dates = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'date_value');

            // Get opening balance before the earliest affected date
            $sql = "SELECT closing_balance FROM tbl_daily_balances 
                    WHERE account_id = :account_id AND balance_date < :start_date 
                    ORDER BY balance_date DESC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id, ':start_date' => $start_recalc_date]);
            $running_balance = $stmt->fetchColumn() ?: 0.00;

            // Fetch transactions
            $sql = "SELECT id, amount, type, txn_date, is_opening_balance 
                    FROM tbl_transactions 
                    WHERE account_id = :account_id AND txn_date >= :start_date AND txn_date <= :end_date 
                    ORDER BY txn_date, created_at";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $account_id,
                ':start_date' => $start_recalc_date,
                ':end_date' => $latest_balance_date
            ]);
            $affected_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate daily totals
            $daily_totals = [];
            foreach ($affected_transactions as $txn) {
                $date = $txn['txn_date'];
                if (!isset($daily_totals[$date])) {
                    $daily_totals[$date] = ['credit' => 0.00, 'debit' => 0.00];
                }
                if ($txn['type'] === 'credit' && !$txn['is_opening_balance']) {
                    $daily_totals[$date]['credit'] += $txn['amount'];
                } elseif ($txn['type'] === 'debit') {
                    $daily_totals[$date]['debit'] += $txn['amount'];
                }
            }

            // Process all dates
            $date_to_opening_balance = [];
            foreach ($all_dates as $date) {
                if (!isset($date_to_opening_balance[$date])) {
                    $sql = "SELECT closing_balance FROM tbl_daily_balances 
                            WHERE account_id = :account_id AND balance_date < :balance_date 
                            ORDER BY balance_date DESC LIMIT 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':account_id' => $account_id, ':balance_date' => $date]);
                    $date_to_opening_balance[$date] = $stmt->fetchColumn() ?: $running_balance;
                }
                $opening_balance = $date_to_opening_balance[$date];

                $total_credit = $daily_totals[$date]['credit'] ?? 0.00;
                $total_debit = $daily_totals[$date]['debit'] ?? 0.00;

                $closing_balance = $opening_balance - $total_debit + $total_credit;

                if (isset($daily_totals[$date])) {
                    foreach ($affected_transactions as $txn) {
                        if ($txn['txn_date'] === $date) {
                            $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                                    VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())
                                    ON DUPLICATE KEY UPDATE balance = :balance, updated_at = NOW()";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                ':account_id' => $account_id,
                                ':balance' => $closing_balance,
                                ':txn_id' => $txn['id'],
                                ':txn_date' => $date
                            ]);
                        }
                    }
                }

                $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, updated_at) 
                        VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, NOW()) 
                        ON DUPLICATE KEY UPDATE 
                        opening_balance = :opening_balance,
                        total_credit = :total_credit,
                        total_debit = :total_debit,
                        closing_balance = :closing_balance,
                        updated_at = NOW()";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $account_id,
                    ':balance_date' => $date,
                    ':opening_balance' => $opening_balance,
                    ':total_credit' => $total_credit,
                    ':total_debit' => $total_debit,
                    ':closing_balance' => $closing_balance
                ]);

                $running_balance = $closing_balance;
                $date_to_opening_balance[$date] = $closing_balance;
            }

            $sql = "UPDATE accounts 
                    SET current_balance = :current_balance, 
                        last_transaction_date = :last_transaction_date 
                    WHERE id = :account_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':current_balance' => $running_balance,
                ':last_transaction_date' => $latest_balance_date,
                ':account_id' => $account_id
            ]);
        }

        $pdo->commit();
        $success_message = "Transaction updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error updating transaction: " . $e->getMessage();
    }
}

// Fetch accounts data
$sql = "SELECT id, account_name, created_at FROM accounts ORDER BY account_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($selected_account_id) && !is_numeric($selected_account_id)) {
    try {
        $sql = "SELECT id FROM tbl_temp_accounts WHERE account_name = :account_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':account_name' => $selected_account_id]);
        $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$temp_account) {
            $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_name' => $selected_account_id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Account name '$selected_account_id' already exists.");
            }

            $sql = "INSERT INTO tbl_temp_accounts (account_name, created_at) VALUES (:account_name, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_name' => $selected_account_id]);
            $temp_account_id = $pdo->lastInsertId();
        } else {
            $temp_account_id = $temp_account['id'];
        }
    } catch (Exception $e) {
        $error_message = "Error creating temporary account: " . $e->getMessage();
        $selected_account_id = null;
    }
}

$account_name = 'Unknown';
$opening_date = date('Y-m-d');
if ($selected_account_id && is_numeric($selected_account_id)) {
    $sql = "SELECT account_name, created_at FROM accounts WHERE id = :account_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':account_id' => $selected_account_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account) {
        $account_name = $account['account_name'];
        $opening_date = $account['created_at'];
    }
} elseif ($selected_account_id) {
    $account_name = $selected_account_id;
}

$running_balance = 0.00;
if ($selected_account_id && is_numeric($selected_account_id)) {
    $sql = "SELECT closing_balance FROM tbl_daily_balances 
            WHERE account_id = :account_id AND balance_date < :start_date 
            ORDER BY balance_date DESC LIMIT 1";
    $params = [':account_id' => $selected_account_id, ':start_date' => $start_date ?: date('Y-m-d')];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $running_balance = $stmt->fetchColumn() ?: 0.00;
}

$transactions = [];
if ($selected_account_id && is_numeric($selected_account_id)) {
    $sql = "SELECT id, txn_date as date, description, amount, type, expense_category, other_category, authorized_by, plot_commission,
                   payment_mode, payment_details_combine, created_at, 
                   get_from, get_to, 'transaction' as source_table, transaction_category, is_opening_balance,
                   vehicle_info, driver_name, kilometers, farmer_name, salesperson_name, commission_type
            FROM tbl_transactions 
            WHERE account_id = :account_id" . ($start_date ? " AND txn_date >= :start_date" : "") . " AND txn_date <= :end_date 
            ORDER BY txn_date, created_at";
    $params = [':account_id' => $selected_account_id, ':end_date' => $end_date];
    if ($start_date) $params[':start_date'] = $start_date;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($selected_account_id) {
    $sql = "SELECT id, txn_date as date, description, amount, type, expense_category, other_category, authorized_by, plot_commission,
                   payment_mode, payment_details_combine, created_at, 
                   get_from, '' as get_to, 'temp_transaction' as source_table, transaction_category, 0 as is_opening_balance,
                   vehicle_info, driver_name, kilometers, farmer_name, salesperson_name, commission_type
            FROM tbl_temp_transactions 
            WHERE account_name = :account_name" . ($start_date ? " AND txn_date >= :start_date" : "") . " AND txn_date <= :end_date 
            ORDER BY txn_date, created_at";
    $params = [':account_name' => $selected_account_id, ':end_date' => $end_date];
    if ($start_date) $params[':start_date'] = $start_date;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$ledger = [
    [
        'date' => $start_date ?: $opening_date,
        'description' => "Opening Balance ({$account_name})",
        'debit' => 0,
        'credit' => $running_balance,
        'balance' => $running_balance,
        'category' => 'N/A',
        'payment_mode' => 'N/A',
        'payment_details' => 'N/A',
        'get_from' => 'N/A',
        'get_to' => 'N/A',
        'source' => 'opening',
        'category_details' => 'N/A',
        'authorized_by' => 'N/A',
        'txn_id' => null,
        'transaction_category' => null
    ]
];

foreach ($transactions as $transaction) {
    $debit = $transaction['type'] === 'debit' ? $transaction['amount'] : 0;
    $credit = $transaction['type'] === 'credit' && !$transaction['is_opening_balance'] ? $transaction['amount'] : 0;
    $running_balance = $running_balance - $debit + $credit;

    // Determine category and category-specific details
    $category = $transaction['expense_category'] === 'Other' ? ($transaction['other_category'] ?: 'General') : ($transaction['expense_category'] ?: 'General');
    if ($transaction['source_table'] === 'transaction' || $transaction['source_table'] === 'temp_transaction') {
        $transaction_category = $transaction['transaction_category'] ?: 'general';
        $category_map = [
            'fuel' => 'Fuel',
            'farmer' => 'Farmer Payment',
            'salesperson' => 'Salesperson Commission',
            'sales_expenses' => 'Sales Expense',
            'general' => 'General'
        ];
        $category = $category_map[$transaction_category] ?? 'General';

        $category_details = '';
        if ($transaction_category === 'fuel') {
            $category_details = implode(', ', array_filter([
                $transaction['vehicle_info'] ? 'Vehicle: ' . $transaction['vehicle_info'] : null,
                $transaction['driver_name'] ? 'Driver: ' . $transaction['driver_name'] : null,
                $transaction['kilometers'] ? 'KM: ' . $transaction['kilometers'] : null
            ]));
        } elseif ($transaction_category === 'farmer') {
            $category_details = implode(', ', array_filter([
                $transaction['farmer_name'] ? 'Farmer: ' . $transaction['farmer_name'] : null
            ]));
        } elseif ($transaction_category === 'salesperson') {
            $category_details = implode(', ', array_filter([
                $transaction['salesperson_name'] ? 'Salesperson: ' . $transaction['salesperson_name'] : null,
                $transaction['commission_type'] ? 'Commission: ' . $transaction['commission_type'] : null,
                $transaction['plot_commission'] ? 'On Plot: ' . $transaction['plot_commission'] : null
            ]));
        } elseif ($transaction_category === 'sales_expenses') {
            $category_details = implode(', ', array_filter([]));
        } else {
            $category_details = $transaction['authorized_by'] ? 'Authorized By: ' . $transaction['authorized_by'] : 'N/A';
        }
    } else {
        $category_details = $transaction['authorized_by'] ? 'Authorized By: ' . $transaction['authorized_by'] : 'N/A';
    }

    // Decode and format payment_details_combine JSON
    $payment_details = 'N/A';
    if ($transaction['payment_details_combine'] && json_decode($transaction['payment_details_combine'], true)) {
        $payment_data = json_decode($transaction['payment_details_combine'], true);
        $details = [];
        foreach ($payment_data as $mode => $data) {
            $mode_details = "[$mode: ₹" . number_format($data['amount'], 2);
            if ($mode === 'upi' && isset($data['transaction_id'])) {
                $mode_details .= ", UPI ID: " . $data['transaction_id'];
            } elseif ($mode === 'cheque' && isset($data['cheque_no'])) {
                $mode_details .= ", Cheque No: " . $data['cheque_no'];
                if (isset($data['bank_name'])) $mode_details .= ", Bank: " . $data['bank_name'];
                if (isset($data['cheque_date'])) $mode_details .= ", Date: " . date('d-m-Y', strtotime($data['cheque_date']));
            }
            $mode_details .= "]";
            $details[] = $mode_details;
        }
        $payment_details = implode(', ', $details);
    }

    $source = $transaction['is_opening_balance'] ? 'opening' : $transaction['source_table'];
    $description = $transaction['is_opening_balance'] ? 'Opening Balance' : $transaction['description'];

    $ledger[] = [
        'date' => $transaction['date'],
        'description' => $description,
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $running_balance,
        'category' => $category,
        'payment_mode' => $transaction['payment_mode'] ?: 'N/A',
        'payment_details' => $payment_details,
        'get_from' => $transaction['get_from'] ?: 'N/A',
        'get_to' => $transaction['get_to'] ?: 'N/A',
        'source' => $source,
        'category_details' => $category_details,
        'authorized_by' => $transaction['authorized_by'] ?: 'N/A',
        'txn_id' => $transaction['id'],
        'transaction_category' => $transaction['transaction_category']
    ];
}
error_log('Ledger: ' . print_r(array_map(function ($l) {
    return ['date' => $l['date'], 'balance' => $l['balance']];
}, $ledger), true));

// Existing filtering logic and HTML rendering remain unchanged
$ledger_account_id = $_POST['ledger_account_id'] ?? null;
$ledger_start_date = $_POST['ledger_start_date'] ?? '';
$ledger_end_date = $_POST['ledger_end_date'] ?? '';
$ledger_category = $_POST['ledger_category'] ?? '';
$ledger_payment_mode = $_POST['ledger_payment_mode'] ?? '';
$ledger_month = $_POST['ledger_month'] ?? '2024-01-01';

// Set default date range
$current_year = date('Y'); // 2025
$default_start_date = date('Y-m-d', strtotime('-30 days')); // Last 30 days
$default_end_date = date('Y-m-d'); // Today

// Determine fetch date range
if ($ledger_month) {
    $ledger_fetch_start_date = "$current_year-$ledger_month-01";
    $ledger_fetch_end_date = date('Y-m-t', strtotime("$current_year-$ledger_month-01"));
} elseif ($ledger_start_date || $ledger_end_date) {
    $ledger_fetch_start_date = $ledger_start_date ?: "$current_year-01-01";
    $ledger_fetch_end_date = $ledger_end_date ?: "$current_year-12-31";
} else {
    $ledger_fetch_start_date = "2024-01-01"; // Fetch all of 2025 by default
    $ledger_fetch_end_date = "$current_year-12-31";
}

try {
    // Fetch accounts for dropdown
    $sql = "SELECT id, account_name, created_at FROM accounts ORDER BY account_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ledger_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ledger = [];
    $ledger_running_balance = 0.00;
    $ledger_account_name = 'N/A';
    $ledger_opening_date = date('Y-m-d');

    if ($ledger_account_id) {
        // Fetch account details
        $sql = "SELECT account_name, created_at FROM accounts WHERE id = :account_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':account_id' => $ledger_account_id]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($account) {
            $ledger_account_name = $account['account_name'];
            $ledger_opening_date = $account['created_at'];
        }

        // Get opening balance before start_date
        $sql = "SELECT closing_balance FROM tbl_daily_balances 
                WHERE account_id = :account_id AND balance_date < :start_date AND is_closed = 1 
                ORDER BY balance_date DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':account_id' => $ledger_account_id, ':start_date' => $ledger_fetch_start_date]);
        $ledger_running_balance = $stmt->fetchColumn() ?: 0.00;

        // Add opening balance to ledger
        $ledger[] = [
            'date' => $ledger_fetch_start_date,
            'description' => "Opening Balance ($ledger_account_name)",
            'debit' => 0,
            'credit' => $ledger_running_balance,
            'balance' => $ledger_running_balance,
            'category' => 'N/A',
            'payment_mode' => 'N/A',
            'payment_details' => 'N/A',
            'get_from' => 'N/A',
            'get_to' => 'N/A',
            'source' => 'opening',
            'category_details' => 'N/A',
            'authorized_by' => 'N/A',
            'txn_id' => null,
            'transaction_category' => null
        ];

        // Get all transactions for the account in 2025
        $sql = "SELECT 
                    id,
                    txn_date AS date,
                    description,
                    amount,
                    type,
                    payment_mode,
                    payment_details_combine,
                    transaction_id,
                    cheque_no,
                    bank_name,
                    cheque_date,
                    authorized_by,
                    get_from,
                    get_to,
                    transaction_category,
                    vehicle_info,
                    driver_name,
                    kilometers,
                    farmer_name,
                    salesperson_name,
                    commission_type,
                    plot_commission,
                    is_opening_balance,
                    created_at,
                    expense_category,
                    other_category
                FROM tbl_transactions 
                WHERE account_id = :account_id AND txn_date >= :start_date AND txn_date <= :end_date
                ORDER BY txn_date, created_at";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':account_id' => $ledger_account_id,
            ':start_date' => $ledger_fetch_start_date,
            ':end_date' => $ledger_fetch_end_date
        ]);
        $ledger_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug: Log query results
        if (empty($ledger_transactions)) {
            error_log("No transactions found for account_id=$ledger_account_id, start_date=$ledger_fetch_start_date, end_date=$ledger_fetch_end_date");
        }

        // Process transactions
        foreach ($ledger_transactions as $transaction) {
            $debit = $transaction['type'] === 'debit' ? $transaction['amount'] : 0;
            $credit = $transaction['type'] === 'credit' && !$transaction['is_opening_balance'] ? $transaction['amount'] : 0;
            $ledger_running_balance = $ledger_running_balance - $debit + $credit;

            // Determine category and category-specific details
            $category = $transaction['expense_category'] === 'Other' ? ($transaction['other_category'] ?: 'General') : ($transaction['expense_category'] ?: 'General');
            $transaction_category = $transaction['transaction_category'] ?: 'general';
            $category_map = [
                'fuel' => 'Fuel',
                'farmer' => 'Farmer Payment',
                'salesperson' => 'Salesperson Commission',
                'sales_expenses' => 'Sales Expense',
                'general' => 'General'
            ];
            $category = $category_map[$transaction_category] ?? 'General';

            // Get category details
            $category_details = '';
            if ($transaction_category === 'fuel') {
                $category_details = implode(', ', array_filter([
                    $transaction['vehicle_info'] ? 'Vehicle: ' . htmlspecialchars($transaction['vehicle_info']) : null,
                    $transaction['driver_name'] ? 'Driver: ' . htmlspecialchars($transaction['driver_name']) : null,
                    $transaction['kilometers'] ? 'KM: ' . htmlspecialchars($transaction['kilometers']) : null
                ]));
            } elseif ($transaction_category === 'farmer') {
                $category_details = implode(', ', array_filter([
                    $transaction['farmer_name'] ? 'Farmer: ' . htmlspecialchars($transaction['farmer_name']) : null
                ]));
            } elseif ($transaction_category === 'salesperson') {
                $category_details = implode(', ', array_filter([
                    $transaction['salesperson_name'] ? 'Salesperson: ' . htmlspecialchars($transaction['salesperson_name']) : null,
                    $transaction['commission_type'] ? 'Commission: ' . htmlspecialchars($transaction['commission_type']) : null,
                    $transaction['plot_commission'] ? 'On Plot: ' . htmlspecialchars($transaction['plot_commission']) : null
                ]));
            } elseif ($transaction_category === 'sales_expenses') {
                $category_details = 'N/A';
            } else {
                $category_details = 'N/A';
            }

            // Decode and format payment_details_combine JSON
            $payment_details = 'N/A';
            if ($transaction['payment_details_combine'] && json_decode($transaction['payment_details_combine'], true)) {
                $payment_data = json_decode($transaction['payment_details_combine'], true);
                $details = [];
                foreach ($payment_data as $mode => $data) {
                    $mode_details = "[$mode: ₹" . number_format($data['amount'], 2);
                    if ($mode === 'upi' && isset($data['transaction_id'])) {
                        $mode_details .= ", UPI ID: " . $data['transaction_id'];
                    } elseif ($mode === 'cheque' && isset($data['cheque_no'])) {
                        $mode_details .= ", Cheque No: " . $data['cheque_no'];
                        if (isset($data['bank_name'])) $mode_details .= ", Bank: " . $data['bank_name'];
                        if (isset($data['cheque_date'])) $mode_details .= ", Date: " . date('d-m-Y', strtotime($data['cheque_date']));
                    }
                    $mode_details .= "]";
                    $details[] = $mode_details;
                }
                $payment_details = implode(', ', $details);
            }

            // Source and description
            $source = $transaction['is_opening_balance'] ? 'opening' : 'transaction';
            $description = $transaction['is_opening_balance'] ? 'Opening Balance' : $transaction['description'];

            $ledger[] = [
                'date' => $transaction['date'],
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $ledger_running_balance,
                'category' => $category,
                'payment_mode' => $transaction['payment_mode'] ?: 'N/A',
                'payment_details' => $payment_details,
                'get_from' => $transaction['get_from'] ?: 'N/A',
                'get_to' => $transaction['get_to'] ?: 'N/A',
                'source' => $source,
                'category_details' => $category_details,
                'authorized_by' => $transaction['authorized_by'] ?: 'N/A',
                'txn_id' => $transaction['id'],
                'transaction_category' => $transaction_category
            ];
        }
    }
} catch (Exception $e) {
    $ledger_error = "Error fetching data: " . $e->getMessage();
    error_log($ledger_error);
}

// Helper functions
function ledger_formatCurrency($amount)
{
    return '₹' . number_format($amount, 2);
}

function ledger_formatDate($date)
{
    return date('d-m-Y', strtotime($date));
}

// Generate month options for current year
$month_options = [];
for ($month = 1; $month <= 12; $month++) {
    $month_name = date('F', mktime(0, 0, 0, $month, 1));
    $month_value = sprintf('%02d', $month);
    $month_options[] = ['value' => $month_value, 'label' => $month_name];
}


// Handle transaction delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_delete_transaction'])) {
    try {
        $pdo->beginTransaction();

        $delete_txn_id = $_POST['txn_id'];
        $delete_source_table = $_POST['source_table'];
        $delete_txn_date = $_POST['txn_date'];
        $delete_end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

        // Validate transaction
        if ($delete_source_table === 'transaction') {
            $sql = "SELECT account_id, is_opening_balance FROM tbl_transactions WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $delete_txn_id]);
            $txn = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$txn) {
                throw new Exception("Transaction not found.");
            }
            if ($txn['is_opening_balance']) {
                throw new Exception("Opening balance transactions cannot be deleted.");
            }
            $delete_account_id = $txn['account_id'];

            // Delete related records from tbl_daily_expenses first to avoid foreign key constraint
            $sql = "DELETE FROM tbl_daily_expenses WHERE txn_id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $delete_txn_id]);

            // Delete transaction
            $sql = "DELETE FROM tbl_transactions WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $delete_txn_id]);
        } else {
            $sql = "SELECT account_name FROM tbl_temp_transactions WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $delete_txn_id]);
            if (!$stmt->fetchColumn()) {
                throw new Exception("Transaction not found.");
            }

            // Delete transaction
            $sql = "DELETE FROM tbl_temp_transactions WHERE id = :txn_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':txn_id' => $delete_txn_id]);
            $delete_account_id = null;
        }

        // Recalculate balances for formal accounts
        if ($delete_account_id) {
            // Get the latest date in tbl_daily_balances or use end_date
            $sql = "SELECT MAX(balance_date) FROM tbl_daily_balances 
                    WHERE account_id = :account_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $delete_account_id]);
            $latest_balance_date = $stmt->fetchColumn() ?: $delete_end_date;

            // Get all relevant dates (transaction dates and daily balance dates)
            $sql = "SELECT DISTINCT date_value FROM (
                        SELECT txn_date AS date_value FROM tbl_transactions 
                        WHERE account_id = :account_id AND txn_date >= :start_date AND txn_date <= :end_date
                        UNION
                        SELECT balance_date AS date_value FROM tbl_daily_balances 
                        WHERE account_id = :account_id AND balance_date >= :start_date AND balance_date <= :end_date
                    ) AS dates ORDER BY date_value";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $delete_account_id,
                ':start_date' => $delete_txn_date,
                ':end_date' => $latest_balance_date
            ]);
            $all_dates = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'date_value');

            // Get opening balance before the transaction date
            $sql = "SELECT closing_balance FROM tbl_daily_balances 
                    WHERE account_id = :account_id AND balance_date < :txn_date 
                    ORDER BY balance_date DESC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $delete_account_id, ':txn_date' => $delete_txn_date]);
            $delete_running_balance = $stmt->fetchColumn() ?: 0.00;

            // Fetch transactions from txn_date to latest_balance_date
            $sql = "SELECT id, amount, type, txn_date, is_opening_balance 
                    FROM tbl_transactions 
                    WHERE account_id = :account_id AND txn_date >= :start_date AND txn_date <= :end_date 
                    ORDER BY txn_date, created_at";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $delete_account_id,
                ':start_date' => $delete_txn_date,
                ':end_date' => $latest_balance_date
            ]);
            $delete_affected_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate daily totals
            $delete_daily_totals = [];
            foreach ($delete_affected_transactions as $txn) {
                $date = $txn['txn_date'];
                if (!isset($delete_daily_totals[$date])) {
                    $delete_daily_totals[$date] = ['credit' => 0.00, 'debit' => 0.00];
                }
                if ($txn['type'] === 'credit' && !$txn['is_opening_balance']) {
                    $delete_daily_totals[$date]['credit'] += $txn['amount'];
                } elseif ($txn['type'] === 'debit') {
                    $delete_daily_totals[$date]['debit'] += $txn['amount'];
                }
            }

            // Process all dates
            $date_to_opening_balance = [];
            foreach ($all_dates as $date) {
                // Fetch opening balance for the date
                if (!isset($date_to_opening_balance[$date])) {
                    $sql = "SELECT closing_balance FROM tbl_daily_balances 
                            WHERE account_id = :account_id AND balance_date < :balance_date 
                            ORDER BY balance_date DESC LIMIT 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':account_id' => $delete_account_id, ':balance_date' => $date]);
                    $date_to_opening_balance[$date] = $stmt->fetchColumn() ?: $delete_running_balance;
                }
                $opening_balance = $date_to_opening_balance[$date];

                // Set totals for dates with no transactions
                $total_credit = $delete_daily_totals[$date]['credit'] ?? 0.00;
                $total_debit = $delete_daily_totals[$date]['debit'] ?? 0.00;

                // Calculate closing balance
                $closing_balance = $opening_balance - $total_debit + $total_credit;

                // Update tbl_account_balances for transaction dates
                if (isset($delete_daily_totals[$date])) {
                    foreach ($delete_affected_transactions as $txn) {
                        if ($txn['txn_date'] === $date) {
                            $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                                    VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())
                                    ON DUPLICATE KEY UPDATE balance = :balance, updated_at = NOW()";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                ':account_id' => $delete_account_id,
                                ':balance' => $closing_balance,
                                ':txn_id' => $txn['id'],
                                ':txn_date' => $date
                            ]);
                        }
                    }
                }

                // Update or insert tbl_daily_balances (preserve is_closed)
                $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, updated_at) 
                        VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, NOW()) 
                        ON DUPLICATE KEY UPDATE 
                        opening_balance = :opening_balance,
                        total_credit = :total_credit,
                        total_debit = :total_debit,
                        closing_balance = :closing_balance,
                        updated_at = NOW()";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $delete_account_id,
                    ':balance_date' => $date,
                    ':opening_balance' => $opening_balance,
                    ':total_credit' => $total_credit,
                    ':total_debit' => $total_debit,
                    ':closing_balance' => $closing_balance
                ]);

                // Update running balance for next date
                $delete_running_balance = $closing_balance;
                $date_to_opening_balance[$date] = $closing_balance;
                error_log("Deleted Date: $date, Opening: $opening_balance, Debit: $total_debit, Credit: $total_credit, Closing: $closing_balance");
            }

            // Update accounts
            $sql = "UPDATE accounts 
                    SET current_balance = :current_balance, 
                        last_transaction_date = :last_transaction_date 
                    WHERE id = :account_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':current_balance' => $delete_running_balance,
                ':last_transaction_date' => $latest_balance_date,
                ':account_id' => $delete_account_id
            ]);
        }

        $pdo->commit();
        $success_message = "Transaction deleted successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error deleting transaction: " . $e->getMessage();
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
        .transaction-row {
            background-color: #f8f9fa;
        }

        .expense-row {
            background-color: #fff3cd;
        }

        .opening-row {
            background-color: #d1ecf1;
        }

        .source-badge {
            font-size: 0.7em;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .badge-transaction {
            background-color: #6c757d;
            color: white;
        }

        .badge-expense {
            background-color: #ffc107;
            color: black;
        }

        .badge-opening {
            background-color: #17a2b8;
            color: white;
        }
    </style>

    <style>
        .fuel-row {
            background-color: #e6f3ff;
        }

        .farmer-row {
            background-color: #e6ffe6;
        }

        .salesperson-row {
            background-color: #fff5e6;
        }

        .general-row {
            background-color: #f0f0f0;
        }

        .expense-row {
            background-color: #ffe6e6;
        }

        .opening-row {
            background-color: #f5f5f5;
        }

        .source-badge.badge-opening {
            background-color: #6c757d;
            color: white;
        }

        .source-badge.badge-expense {
            background-color: #dc3545;
            color: white;
        }

        .source-badge.badge-transaction {
            background-color: #007bff;
            color: white;
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

                    <div class="mx-3 mt-3 rounded" style="">
                        <div class="col-md-12">

                            <?php if ($success_message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                            <?php endif; ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                            <?php endif; ?>

                            <div class="expense-form-container">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h3 class="text-center">Ledger Report of&nbsp;<?php echo $account['account_name']; ?></h3>
                                        <?php if ($error_message): ?>
                                            <div class="alert alert-warning"><?php echo $error_message; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>


                                <!-- Filter Form -->
                                <form id="ledgerFilterForm" method="POST">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="ledger_account_id" class="form-label">Account</label>
                                            <select class="form-control" name="ledger_account_id" id="ledger_account_id" required onchange="submitLedgerForm()">
                                                <option value="">-- Select Account --</option>
                                                <?php foreach ($ledger_accounts as $account): ?>
                                                    <option value="<?php echo $account['id']; ?>" <?php echo $ledger_account_id == $account['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($account['account_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="ledger_month" class="form-label">Month (<?php echo $current_year; ?>)</label>
                                            <select class="form-control" name="ledger_month" id="ledger_month" onchange="updateDateInputs()">
                                                <option value="">-- Select Month --</option>
                                                <?php foreach ($month_options as $option): ?>
                                                    <option value="<?php echo $option['value']; ?>" <?php echo $ledger_month === $option['value'] ? 'selected' : ''; ?>>
                                                        <?php echo $option['label']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="ledger_start_date" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="ledger_start_date" id="ledger_start_date" value="<?php echo htmlspecialchars($ledger_start_date); ?>" <?php echo $ledger_month ? 'disabled' : ''; ?>>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="ledger_end_date" class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="ledger_end_date" id="ledger_end_date" value="<?php echo htmlspecialchars($ledger_end_date); ?>" <?php echo $ledger_month ? 'disabled' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <label for="ledger_category" class="form-label">Category</label>
                                            <select class="form-control" name="ledger_category" id="ledger_category">
                                                <option value="">All Categories</option>
                                                <option value="fuel" <?php echo $ledger_category === 'fuel' ? 'selected' : ''; ?>>Fuel</option>
                                                <option value="farmer" <?php echo $ledger_category === 'farmer' ? 'selected' : ''; ?>>Farmer</option>
                                                <option value="salesperson" <?php echo $ledger_category === 'salesperson' ? 'selected' : ''; ?>>Salesperson</option>
                                                <option value="general" <?php echo $ledger_category === 'general' ? 'selected' : ''; ?>>General</option>
                                                <option value="sales_expenses" <?php echo $ledger_category === 'sales_expenses' ? 'selected' : ''; ?>>Sales Expense</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="ledger_payment_mode" class="form-label">Payment Mode</label>
                                            <select class="form-control" name="ledger_payment_mode" id="ledger_payment_mode">
                                                <option value="">All Modes</option>
                                                <option value="Cash" <?php echo $ledger_payment_mode === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                                <option value="UPI" <?php echo $ledger_payment_mode === 'UPI' ? 'selected' : ''; ?>>UPI</option>
                                                <option value="Cheque" <?php echo $ledger_payment_mode === 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                                                <option value="Bank Transfer" <?php echo $ledger_payment_mode === 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                                <option value="Card" <?php echo $ledger_payment_mode === 'Card' ? 'selected' : ''; ?>>Card</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary me-2" onclick="applyLedgerFilters()">
                                                <i class="fas fa-search me-2"></i>Apply Filters
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="clearLedgerFilters()">
                                                <i class="fas fa-times me-2"></i>Clear Filters
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Summary Cards -->
                            <?php if ($ledger_account_id): ?>
                                <div class="row mb-4" id="summaryCards">
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Opening Balance</h5>
                                                <h3 id="openingBalance">0.00</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Current Balance</h5>
                                                <h3 id="currentBalance"><?php echo number_format($ledger_running_balance, 2); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Expenses</h5>
                                                <h3 id="totalExpenses">0.00</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Credits</h5>
                                                <h3 id="totalCredits">0.00</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <style>
                                /* Print Button Styling */
                                .print-btn {
                                    margin: 20px 0;
                                    padding: 10px 30px;
                                    font-size: 16px;
                                    background-color: #28a745;
                                    color: white;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: pointer;
                                }

                                .print-btn:hover {
                                    background-color: #218838;
                                }

                                @media print {
                                    @page {
                                        size: A4 portrait;
                                        margin: 0mm 0mm 0mm 0mm;
                                    }

                                    /* Hide everything except the table and header */
                                    body * {
                                        visibility: hidden;
                                    }

                                    .transaction-table,
                                    .transaction-table *,
                                    .print-header,
                                    .print-header * {
                                        visibility: visible;
                                    }

                                    /* Reset body styles - Remove all spacing */
                                    body {
                                        margin: 0 !important;
                                        padding: 0 !important;
                                        font-size: 12px;
                                        line-height: 1.4;
                                        font-family: Arial, sans-serif;
                                    }

                                    /* Print header - Very compact */
                                    .print-header {
                                        position: absolute !important;
                                        top: -31px !important;
                                        left: 0 !important;
                                        right: 0 !important;
                                        width: 100% !important;
                                        text-align: center;
                                        font-size: 14px;
                                        font-weight: bold;
                                        padding: 5px 0 !important;
                                        margin: 0 !important;

                                        /* height: 30px !important; */
                                        z-index: 1000;
                                    }

                                    .print-date {
                                        font-size: 10px;
                                        font-weight: normal;
                                        margin: 2px 0 0 0 !important;
                                    }

                                    /* Table container - Start immediately after header */
                                    .transaction-table {
                                        position: absolute !important;
                                        top: 30px !important;
                                        left: 0 !important;
                                        right: 0 !important;
                                        width: 100% !important;
                                        margin: 0 !important;
                                        padding: 0 !important;
                                    }

                                    /* Hide elements not needed in print */
                                    .print-btn,
                                    .btn,
                                    .no-print,
                                    button {
                                        display: none !important;
                                    }

                                    /* Table styling */
                                    .table {
                                        width: 100% !important;
                                        margin: 0 !important;
                                        font-size: 12px;
                                        border-collapse: collapse;
                                        table-layout: fixed;
                                    }

                                    .table th,
                                    .table td {
                                        border: 1px solid #000 !important;
                                        padding: 6px 4px !important;
                                        vertical-align: top;
                                        word-wrap: break-word;
                                        overflow-wrap: break-word;
                                        line-height: 1.3;
                                    }

                                    .table thead th {
                                        background-color: #000 !important;
                                        color: black !important;
                                        font-weight: bold;
                                        text-align: center;
                                        font-size: 12px;
                                        padding: 8px 4px !important;
                                        white-space: nowrap;
                                    }

                                    /* Hide Action and Is Closed columns in print */
                                    .table th:nth-child(1),
                                    .table td:nth-child(1),
                                    .table th:nth-child(15),
                                    .table td:nth-child(15) {
                                        display: none !important;
                                    }

                                    /* Adjusted column widths for remaining 13 columns */
                                    .table th:nth-child(2),
                                    .table td:nth-child(2) {
                                        width: 7%;
                                        /* Date */
                                    }

                                    .table th:nth-child(3),
                                    .table td:nth-child(3) {
                                        width: 10%;
                                        /* Source */
                                    }

                                    .table th:nth-child(4),
                                    .table td:nth-child(4) {
                                        width: 19%;
                                        /* Description (increased to use freed space) */
                                    }

                                    .table th:nth-child(5),
                                    .table td:nth-child(5) {
                                        width: 9%;
                                        /* Debit */
                                    }

                                    .table th:nth-child(6),
                                    .table td:nth-child(6) {
                                        width: 9%;
                                        /* Credit */
                                    }

                                    .table th:nth-child(7),
                                    .table td:nth-child(7) {
                                        width: 9%;
                                        /* Balance */
                                    }

                                    .table th:nth-child(8),
                                    .table td:nth-child(8) {
                                        width: 10%;
                                        /* Category */
                                    }

                                    .table th:nth-child(9),
                                    .table td:nth-child(9) {
                                        width: 9%;
                                        /* From */
                                    }

                                    .table th:nth-child(10),
                                    .table td:nth-child(10) {
                                        width: 10%;
                                        /* To */
                                    }

                                    .table th:nth-child(11),
                                    .table td:nth-child(11) {
                                        width: 8%;
                                        /* Mode */
                                    }

                                    .table th:nth-child(12),
                                    .table td:nth-child(12) {
                                        width: 19%;
                                        /* Payment Details (increased slightly) */
                                    }

                                    .table th:nth-child(13),
                                    .table td:nth-child(13) {
                                        width: 10%;
                                        /* Details */
                                    }

                                    .table th:nth-child(14),
                                    .table td:nth-child(14) {
                                        width: 13%;
                                        /* Authorized By */
                                    }

                                    /* Text styling */
                                    .table td {
                                        white-space: normal !important;
                                        word-break: break-word;
                                        font-size: 10px;
                                        line-height: 1.3;
                                    }

                                    /* Amount columns - Right aligned, bold */
                                    .table td:nth-child(5),
                                    .table td:nth-child(6),
                                    .table td:nth-child(7) {
                                        text-align: right !important;
                                        font-weight: bold;
                                        font-size: 11px;
                                        padding-right: 6px !important;
                                    }

                                    /* Date column */
                                    .table td:nth-child(2) {
                                        font-size: 11px;
                                        white-space: nowrap;
                                        text-align: center;
                                    }

                                    /* Description column */
                                    .table td:nth-child(4) {
                                        font-size: 11px;
                                        line-height: 1.3;
                                        word-break: break-word;
                                    }

                                    /* Source badge styling */
                                    .source-badge {
                                        font-size: 9px;
                                        padding: 2px 4px;
                                        border-radius: 3px;
                                        font-weight: bold;
                                        white-space: nowrap;
                                        display: inline-block;
                                        color: black !important;
                                        font-weight: bold !important;
                                    }

                                    .badge-opening {
                                        background-color: #000 !important;
                                        color: black !important;
                                    }

                                    .badge-transaction {
                                        background-color: #555 !important;
                                        color: black !important;
                                    }

                                    /* Table container settings */
                                    .table-responsive {
                                        overflow: visible !important;
                                        height: auto !important;
                                        padding: 0 !important;
                                        margin: 0 !important;
                                        max-height: none !important;
                                    }

                                    /* Table header repetition */
                                    .table thead {
                                        display: table-header-group;
                                    }

                                    .table tbody tr {
                                        page-break-inside: avoid;
                                        height: auto;
                                        min-height: 26px;
                                    }

                                    /* Alternating row colors */
                                    .table tbody tr:nth-child(even) {
                                        background-color: #f8f9fa !important;
                                    }

                                    .table tbody tr:nth-child(odd) {
                                        background-color: white !important;
                                    }

                                    /* Opening balance rows */
                                    .opening-row {
                                        background-color: #e3f2fd !important;
                                        font-weight: bold;
                                    }

                                    /* Row borders */
                                    .table tbody tr {
                                        border-bottom: 1px solid #000 !important;
                                    }

                                    /* Text contrast */
                                    .table td,
                                    .table th {
                                        color: #000 !important;
                                    }

                                    /* Balance column formatting */
                                    .table td.fw-bold,
                                    .table td:nth-child(7) {
                                        font-weight: bold !important;
                                        background-color: #f0f0f0 !important;
                                    }

                                    /* Compact text fields */
                                    .table td:nth-child(8),
                                    .table td:nth-child(9),
                                    .table td:nth-child(10),
                                    .table td:nth-child(11),
                                    .table td:nth-child(13),
                                    .table td:nth-child(14) {
                                        font-size: 11px;
                                        text-align: center;
                                        padding: 5px 2px !important;
                                    }

                                    /* Payment Details */
                                    .table td:nth-child(12) {
                                        font-size: 11px;
                                        line-height: 1.2;
                                        word-break: break-word;
                                        padding: 5px 3px !important;
                                    }

                                    /* CRITICAL: Force everything to start from page 1 */
                                    .print-header {
                                        page-break-before: avoid !important;
                                        page-break-after: avoid !important;
                                    }

                                    .transaction-table {
                                        page-break-before: avoid !important;
                                    }

                                    /* Remove any hidden margins/padding that might cause page breaks */
                                    * {
                                        box-sizing: border-box !important;
                                    }
                                }
                            </style>


                            <div class="text-center">
                                <button class="print-btn" onclick="printLedger()">
                                    🔄 Print Ledger
                                </button>
                            </div>

                            <!-- Print Header (only visible when printing) -->
                            <div class="print-header" style="display: none;">
                                <div>Ledger Report</div>
                                <div class="print-date">Generated on: <span id="printDate"></span></div>
                            </div>

                            <div class="transaction-table">
                                <?php
                                // Pre-fetch is_closed values
                                $closed_status = [];
                                if (!empty($ledger_account_id) && is_numeric($ledger_account_id) && isset($pdo)) {
                                    try {
                                        // Use min/max dates from ledger to ensure all dates are covered
                                        $ledger_dates = array_column($ledger, 'date');
                                        $min_date = !empty($ledger_dates) ? min($ledger_dates) : ($start_date ?: '2024-01-01');
                                        $max_date = !empty($ledger_dates) ? max($ledger_dates) : ($end_date ?: date('Y-m-d'));
                                        $sql = "SELECT balance_date, COALESCE(is_closed, 0) AS is_closed 
                    FROM tbl_daily_balances 
                    WHERE account_id = :account_id 
                    AND balance_date BETWEEN :min_date AND :max_date";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute([
                                            ':account_id' => $ledger_account_id,
                                            ':min_date' => $min_date,
                                            ':max_date' => $max_date
                                        ]);
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $closed_status[$row['balance_date']] = (int)$row['is_closed'];
                                        }
                                    } catch (Exception $e) {
                                        // Silently handle errors in production
                                    }
                                }
                                ?>
                                <div class="table-responsive" style="height: 61vh;">
                                    <table class="table table-bordered table-hover" id="ledgerTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Action</th>
                                                <th>Date</th>
                                                <th>Source</th>
                                                <th>Description</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Balance</th>
                                                <th>Category</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Mode</th>
                                                <th>Payment Details</th>
                                                <th>Details</th>
                                                <th>Voucher Number</th>
                                                <th>Is Closed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($ledger) || empty($ledger_account_id)): ?>
                                                <tr>
                                                    <td colspan="15" class="text-center">No transactions found. Please select an account.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($ledger as $entry): ?>
                                                    <tr class="<?php
                                                                echo htmlspecialchars($entry['source'] === 'opening' ? 'opening-row' : ($entry['transaction_category'] ? $entry['transaction_category'] . '-row' : 'general-row'), ENT_QUOTES, 'UTF-8');
                                                                ?>"
                                                        data-date="<?php echo htmlspecialchars($entry['date'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-category="<?php echo htmlspecialchars($entry['transaction_category'] ?? 'general', ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-payment-mode="<?php echo htmlspecialchars($entry['payment_mode'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-debit="<?php echo htmlspecialchars($entry['debit'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-credit="<?php echo htmlspecialchars($entry['credit'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-balance="<?php echo htmlspecialchars($entry['balance'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <td>
                                                            <?php if ($entry['source'] !== 'opening' && !empty($entry['txn_id'])): ?>
                                                                <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#editTransactionModal<?php echo htmlspecialchars($entry['txn_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                    Edit
                                                                </button>
                                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTransactionModal<?php echo htmlspecialchars($entry['txn_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                    Delete
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars(ledger_formatDate($entry['date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <span class="source-badge badge-<?php echo htmlspecialchars($entry['source'] === 'opening' ? 'opening' : 'transaction', ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo htmlspecialchars($entry['source'] === 'opening' ? 'Opening' : 'Transaction', ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($entry['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end"><?php echo $entry['debit'] ? htmlspecialchars(ledger_formatCurrency($entry['debit']), ENT_QUOTES, 'UTF-8') : ''; ?></td>
                                                        <td class="text-end"><?php echo $entry['credit'] ? htmlspecialchars(ledger_formatCurrency($entry['credit']), ENT_QUOTES, 'UTF-8') : ''; ?></td>
                                                        <td class="text-end fw-bold"><?php echo htmlspecialchars(ledger_formatCurrency($entry['balance']), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['get_from'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['get_to'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['payment_mode'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['payment_details'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['category_details'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($entry['authorized_by'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo isset($closed_status[$entry['date']]) && $closed_status[$entry['date']] ? 'Yes' : 'No'; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <script>
                                function printLedger() {
                                    // Set current date for print
                                    const now = new Date();
                                    const dateStr = now.toLocaleDateString('en-IN', {
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: true
                                    });
                                    document.getElementById('printDate').textContent = dateStr;

                                    // Show print header
                                    document.querySelector('.print-header').style.display = 'block';

                                    // Small delay to ensure header is rendered before printing
                                    setTimeout(function() {
                                        window.print();

                                        // Hide print header after printing dialog closes
                                        setTimeout(function() {
                                            document.querySelector('.print-header').style.display = 'none';
                                        }, 1000);
                                    }, 100);
                                }

                                // Alternative method using CSS media queries for better browser compatibility
                                window.addEventListener('beforeprint', function() {
                                    const now = new Date();
                                    const dateStr = now.toLocaleDateString('en-IN', {
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: true
                                    });
                                    document.getElementById('printDate').textContent = dateStr;
                                    document.querySelector('.print-header').style.display = 'block';
                                });

                                window.addEventListener('afterprint', function() {
                                    document.querySelector('.print-header').style.display = 'none';
                                });
                            </script>

                            <!-- Pagination -->
                            <nav aria-label="Page navigation" class="mt-4 no-print" id="ledgerPagination"></nav>

                            <!-- Edit Transaction Modals -->
                            <?php foreach ($ledger as $entry): ?>
                                <?php if ($entry['source'] !== 'opening' && !empty($entry['txn_id'])): ?>
                                    <div class="modal fade" id="editTransactionModal<?php echo $entry['txn_id']; ?>" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editTransactionModalLabel">Edit Transaction</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="txn_id" value="<?php echo $entry['txn_id']; ?>">
                                                        <input type="hidden" name="source_table" value="<?php echo $entry['source']; ?>">
                                                        <input type="hidden" name="txn_date" value="<?php echo $entry['date']; ?>">
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Description</label>
                                                            <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($entry['description']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="amount" class="form-label">Amount</label>
                                                            <input type="number" class="form-control" name="amount" value="<?php echo $entry['debit'] ?: $entry['credit']; ?>" step="0.01" min="0" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="type" class="form-label">Type</label>
                                                            <select class="form-control" name="type" required>
                                                                <option value="credit" <?php echo $entry['credit'] > 0 ? 'selected' : ''; ?>>Credit</option>
                                                                <option value="debit" <?php echo $entry['debit'] > 0 ? 'selected' : ''; ?>>Debit</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="authorized_by" class="form-label">Voucher:</label>
                                                            <input type="text" class="form-control" name="authorized_by" value="<?php echo htmlspecialchars($entry['authorized_by']); ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="transaction_category" class="form-label">Category</label>
                                                            <select class="form-control" name="transaction_category">
                                                                <option value="general" <?php echo $entry['transaction_category'] === 'general' ? 'selected' : ''; ?>>General</option>
                                                                <option value="fuel" <?php echo $entry['transaction_category'] === 'fuel' ? 'selected' : ''; ?>>Fuel</option>
                                                                <option value="farmer" <?php echo $entry['transaction_category'] === 'farmer' ? 'selected' : ''; ?>>Farmer Payment</option>
                                                                <option value="salesperson" <?php echo $entry['transaction_category'] === 'salesperson' ? 'selected' : ''; ?>>Salesperson Commission</option>
                                                                <option value="sales_expenses" <?php echo $entry['transaction_category'] === 'sales_expenses' ? 'selected' : ''; ?>>Sales Expense</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" name="btn_update_transaction" class="btn btn-primary">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>

                        </div>


                        <?php foreach ($ledger as $entry): ?>
                            <?php if ($entry['source'] !== 'opening' && !empty($entry['txn_id'])): ?>
                                <div class="modal fade" id="deleteTransactionModal<?php echo $entry['txn_id']; ?>" tabindex="-1" aria-labelledby="deleteTransactionModalLabel<?php echo $entry['txn_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteTransactionModalLabel<?php echo $entry['txn_id']; ?>">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the transaction: <strong><?php echo htmlspecialchars($entry['description']); ?></strong> (<?php echo ledger_formatCurrency($entry['debit'] ?: $entry['credit']); ?>)?</p>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="txn_id" value="<?php echo $entry['txn_id']; ?>">
                                                    <input type="hidden" name="source_table" value="<?php echo $entry['source']; ?>">
                                                    <input type="hidden" name="txn_date" value="<?php echo $entry['date']; ?>">
                                                    <button type="submit" name="btn_delete_transaction" class="btn btn-danger">Delete</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <?php include "account-footer.php"; ?>
    </div>


    <a href="#" target="_blank">
        <!-- partial -->
    </a>

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
        function exportToCSV() {
            let csv = 'Date,Source,Description,Debit,Credit,Balance,Category,From,To,Payment Mode,Payment Details\n';

            <?php foreach ($ledger as $entry): ?>
                csv += '<?php echo addslashes($entry['date']); ?>,';
                csv += '<?php echo isset($entry['source']) ? addslashes($entry['source']) : ""; ?>,';
                csv += '<?php echo addslashes($entry['description']); ?>,';
                csv += '<?php echo $entry['debit']; ?>,';
                csv += '<?php echo $entry['credit']; ?>,';
                csv += '<?php echo $entry['balance']; ?>,';
                csv += '<?php echo addslashes($entry['category']); ?>,';
                csv += '<?php echo addslashes($entry['get_from']); ?>,';
                csv += '<?php echo addslashes($entry['get_to']); ?>,';
                csv += '<?php echo addslashes($entry['payment_mode']); ?>,';
                csv += '<?php echo addslashes($entry['payment_details']); ?>\n';
            <?php endforeach; ?>

            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'ledger_report_<?php echo date('Y-m-d'); ?>.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function printReport() {
            window.print();
        }
    </script>


    <script>
        const rowsPerPage = 20;
        const currentYear = <?php echo $current_year; ?>;

        function submitLedgerForm() {
            const form = document.getElementById('ledgerFilterForm');
            // Clear date inputs if month is selected
            if (document.getElementById('ledger_month').value) {
                document.getElementById('ledger_start_date').value = '';
                document.getElementById('ledger_end_date').value = '';
            }
            form.submit();
        }

        function clearMonthFilter() {
            const monthSelect = document.getElementById('ledger_month');
            if (monthSelect.value) {
                monthSelect.value = '';
                submitLedgerForm();
            }
        }

        function updateSummaryCards(visibleRows) {
            let totalExpenses = 0;
            let totalCredits = 0;
            let currentBalance = 0;

            visibleRows.forEach(row => {
                const debit = parseFloat(row.getAttribute('data-debit')) || 0;
                const credit = parseFloat(row.getAttribute('data-credit')) || 0;
                totalExpenses += debit;
                totalCredits += credit;
                currentBalance = parseFloat(row.getAttribute('data-balance')) || 0; // Use last row's balance
            });

            const openingBalance = currentBalance + totalExpenses;

            document.getElementById('openingBalance').textContent = openingBalance.toFixed(2);
            document.getElementById('totalExpenses').textContent = totalExpenses.toFixed(2);
            document.getElementById('totalCredits').textContent = totalCredits.toFixed(2);
            document.getElementById('currentBalance').textContent = currentBalance.toFixed(2);
        }

        function applyLedgerFilters() {
            const month = document.getElementById('ledger_month').value;
            const startDate = document.getElementById('ledger_start_date').value;
            const endDate = document.getElementById('ledger_end_date').value;
            const category = document.getElementById('ledger_category').value;
            const paymentMode = document.getElementById('ledger_payment_mode').value;

            let filterStartDate = null;
            let filterEndDate = null;

            // Prioritize month filter
            if (month) {
                filterStartDate = `${currentYear}-${month}-01`;
                filterEndDate = new Date(currentYear, parseInt(month), 0).toISOString().split('T')[0];
            } else if (startDate || endDate) {
                filterStartDate = startDate;
                filterEndDate = endDate;
            }

            const rows = document.querySelectorAll('#ledgerTable tbody tr');
            let visibleRows = [];

            rows.forEach(row => {
                const rowDate = row.getAttribute('data-date');
                const rowCategory = row.getAttribute('data-category');
                const rowPaymentMode = row.getAttribute('data-payment-mode');

                let showRow = true;

                if (filterStartDate && rowDate < filterStartDate) showRow = false;
                if (filterEndDate && rowDate > filterEndDate) showRow = false;
                if (category && rowCategory !== category) showRow = false;
                if (paymentMode && rowPaymentMode !== paymentMode) showRow = false;

                if (showRow) {
                    visibleRows.push(row);
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            updateSummaryCards(visibleRows);
            updatePagination(visibleRows);
        }

        function clearLedgerFilters() {
            document.getElementById('ledger_month').value = '';
            document.getElementById('ledger_start_date').value = '';
            document.getElementById('ledger_end_date').value = '';
            document.getElementById('ledger_category').value = '';
            document.getElementById('ledger_payment_mode').value = '';
            submitLedgerForm();
        }

        function updatePagination(visibleRows) {
            const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
            const pagination = document.getElementById('ledgerPagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const ul = document.createElement('ul');
            ul.className = 'pagination justify-content-center';

            // Previous
            if (currentPage > 1) {
                const prevLi = document.createElement('li');
                prevLi.className = 'page-item';
                prevLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage - 1})">Previous</a>`;
                ul.appendChild(prevLi);
            }

            // Page numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>`;
                ul.appendChild(li);
            }

            // Next
            if (currentPage < totalPages) {
                const nextLi = document.createElement('li');
                nextLi.className = 'page-item';
                nextLi.innerHTML = `<a class="page-link" href="#" onclick="goToPage(${currentPage + 1})">Next</a>`;
                ul.appendChild(nextLi);
            }

            pagination.appendChild(ul);
            showPage(visibleRows, currentPage);
        }

        let currentPage = 1;

        function goToPage(page) {
            currentPage = page;
            applyLedgerFilters();
        }

        function showPage(visibleRows, page) {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            visibleRows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });
        }

        // Initial filter application
        document.addEventListener('DOMContentLoaded', () => {
            applyLedgerFilters();
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