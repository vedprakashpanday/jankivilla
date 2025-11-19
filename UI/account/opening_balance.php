<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}



// if (isset($_POST['btn_save_opening_balance'])) {
//     $account_id = trim($_POST['account_id']);
//     $opening_balance = floatval($_POST['opening_balance']);
//     $balance_date = $_POST['balance_date'];

//     if (empty($account_id) || empty($balance_date)) {
//         $error_message = "Please fill in all required fields.";
//     } elseif ($opening_balance < 0) {
//         $error_message = "Opening balance cannot be negative.";
//     } else {
//         try {
//             $pdo->beginTransaction();

//             $formal_account_id = null;
//             $temp_account_id = null;
//             $effective_balance = $opening_balance;
//             $txn_id = null;
//             $account_name = null;

//             if (strpos($account_id, 'temp_') === 0) {
//                 // Existing temporary account
//                 $temp_account_id = substr($account_id, 5);
//                 $sql = "SELECT account_name FROM tbl_temp_accounts WHERE id = :temp_account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//                 $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

//                 if (!$temp_account) {
//                     throw new Exception("Temporary account not found.");
//                 }
//                 $account_name = $temp_account['account_name'];
//             } elseif (!is_numeric($account_id) && !empty($account_id)) {
//                 // New account name
//                 $account_name = $account_id;

//                 // Check if account name exists in accounts
//                 $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 if ($stmt->fetchColumn() > 0) {
//                     throw new Exception("Account name '$account_name' already exists in accounts.");
//                 }

//                 // Check if temporary account exists
//                 $sql = "SELECT id FROM tbl_temp_accounts WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

//                 if ($temp_account) {
//                     $temp_account_id = $temp_account['id'];
//                 } else {
//                     // Create new temporary account
//                     $sql = "INSERT INTO tbl_temp_accounts (account_name, created_at) 
//                             VALUES (:account_name, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $temp_account_id = $pdo->lastInsertId();
//                 }
//             } elseif (is_numeric($account_id)) {
//                 // Formal account
//                 $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//                 if ($stmt->fetchColumn() == 0) {
//                     throw new Exception("Invalid account selected.");
//                 }
//                 $formal_account_id = $account_id;

//                 // Check if reopening for a new date
//                 $sql = "SELECT last_transaction_date, current_balance FROM accounts WHERE id = :account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//                 $account = $stmt->fetch(PDO::FETCH_ASSOC);
//                 $last_transaction_date = $account['last_transaction_date'];
//                 $current_balance = $account['current_balance'];

//                 if ($balance_date > $last_transaction_date) {
//                     // Reopening for a new date
//                     $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                             WHERE account_id = :account_id AND balance_date < :balance_date AND is_closed = 1 
//                             ORDER BY balance_date DESC LIMIT 1";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $effective_balance = $stmt->fetchColumn() ?: $current_balance;

//                     if ($opening_balance != $effective_balance) {
//                         throw new Exception("Opening balance must match previous day's closing balance (₹$effective_balance), Or check previous day close or reopen.");
//                     }

//                     // Check for existing opening balance
//                     $sql = "SELECT id FROM tbl_opening_balances 
//                             WHERE account_id = :account_id AND balance_date = :balance_date";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     if ($stmt->fetchColumn()) {
//                         throw new Exception("Opening balance already set for this date.");
//                     }

//                     // Insert into tbl_opening_balances
//                     $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                             VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->execute();

//                     // Check for existing opening balance transaction
//                     $sql = "SELECT id FROM tbl_transactions 
//                             WHERE account_id = :account_id AND txn_date = :txn_date AND is_opening_balance = 1";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     if ($stmt->fetchColumn()) {
//                         throw new Exception("Opening balance transaction already exists for this date.");
//                     }

//                     // Insert opening balance transaction
//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
//                             VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':amount', $effective_balance, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $txn_id = $pdo->lastInsertId();

//                     // Update tbl_account_balances
//                     $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                             VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();

//                     // Update tbl_daily_balances
//                     $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                             VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                             ON DUPLICATE KEY UPDATE 
//                             opening_balance = :opening_balance,
//                             total_credit = :total_credit,
//                             total_debit = :total_debit,
//                             closing_balance = :closing_balance";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_credit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_debit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':closing_balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->execute();

//                     // Update accounts
//                     $sql = "UPDATE accounts SET last_transaction_date = :balance_date WHERE id = :account_id";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->execute();
//                 } else {
//                     // Initial or same-day opening balance
//                     $sql = "SELECT id FROM tbl_opening_balances 
//                             WHERE account_id = :account_id AND balance_date = :balance_date";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     if ($stmt->fetchColumn()) {
//                         throw new Exception("Opening balance already set for this date.");
//                     }

//                     // Insert into tbl_opening_balances
//                     $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                             VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->execute();

//                     $sql = "UPDATE accounts SET opening_balance = :opening_balance, current_balance = :opening_balance, last_transaction_date = :balance_date WHERE id = :account_id";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->execute();

//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
//                             VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':amount', $opening_balance, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $txn_id = $pdo->lastInsertId();

//                     $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                             VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();

//                     $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                             VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                             ON DUPLICATE KEY UPDATE 
//                             opening_balance = :opening_balance,
//                             total_credit = :total_credit,
//                             total_debit = :total_debit,
//                             closing_balance = :closing_balance";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_credit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_debit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':closing_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->execute();
//                 }
//             } else {
//                 throw new Exception("Invalid account selection.");
//             }

//             // Process temporary account if exists
//             if ($temp_account_id && $account_name) {
//                 // Check again for account name in accounts (in case of race condition)
//                 $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 if ($stmt->fetchColumn() > 0) {
//                     throw new Exception("Account name '$account_name' already exists in accounts.");
//                 }

//                 // Fetch all transactions from tbl_temp_transactions
//                 $sql = "SELECT id, amount, description, txn_date, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at 
//                         FROM tbl_temp_transactions 
//                         WHERE account_name = :account_name 
//                         ORDER BY created_at";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 $temp_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//                 // Calculate total credit for opening balance
//                 $total_credit = 0.0;
//                 foreach ($temp_transactions as $txn) {
//                     if ($txn['type'] === 'credit') {
//                         $total_credit += $txn['amount'];
//                     }
//                 }

//                 if ($total_credit > 0 && $opening_balance != $total_credit) {
//                     throw new Exception("Opening balance must match total credit transactions (₹$total_credit).");
//                 }
//                 $effective_balance = $opening_balance;

//                 // Insert into accounts
//                 $sql = "INSERT INTO accounts (account_name, opening_balance, created_at, current_balance, last_transaction_date) 
//                         VALUES (:account_name, :opening_balance, :created_at, :opening_balance, :balance_date)";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->bindValue(':opening_balance', 0.00, PDO::PARAM_STR);
//                 $stmt->bindValue(':created_at', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->execute();
//                 $formal_account_id = $pdo->lastInsertId();

//                 // Insert into tbl_opening_balances
//                 $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                         VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                 $stmt->execute();

//                 // Move transactions to tbl_transactions
//                 foreach ($temp_transactions as $txn) {
//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at, is_opening_balance) 
//                             VALUES (:account_id, :txn_date, :description, :amount, :get_from, :type, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, :created_at, 0)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $txn['txn_date'], PDO::PARAM_STR);
//                     $stmt->bindValue(':description', $txn['description'], PDO::PARAM_STR);
//                     $stmt->bindValue(':amount', $txn['amount'], PDO::PARAM_STR);
//                     $stmt->bindValue(':get_from', $txn['get_from'], PDO::PARAM_STR);
//                     $stmt->bindValue(':type', $txn['type'], PDO::PARAM_STR);
//                     $stmt->bindValue(':expense_category', $txn['expense_category'], PDO::PARAM_STR);
//                     $stmt->bindValue(':other_category', $txn['other_category'], PDO::PARAM_STR);
//                     $stmt->bindValue(':payment_mode', $txn['payment_mode'], PDO::PARAM_STR);
//                     $stmt->bindValue(':transaction_id', $txn['transaction_id'], PDO::PARAM_STR);
//                     $stmt->bindValue(':cheque_no', $txn['cheque_no'], PDO::PARAM_STR);
//                     $stmt->bindValue(':bank_name', $txn['bank_name'], PDO::PARAM_STR);
//                     $stmt->bindValue(':cheque_date', $txn['cheque_date'], PDO::PARAM_STR);
//                     $stmt->bindValue(':created_at', $txn['created_at'], PDO::PARAM_STR);
//                     $stmt->execute();
//                     $txn_id = $pdo->lastInsertId();
//                 }

//                 // Delete from tbl_temp_transactions
//                 $sql = "DELETE FROM tbl_temp_transactions WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();

//                 // Delete from tbl_temp_accounts
//                 $sql = "DELETE FROM tbl_temp_accounts WHERE id = :temp_account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
//                 $stmt->execute();

//                 // Update balances
//                 $new_balance = $effective_balance;
//                 foreach ($temp_transactions as $txn) {
//                     if ($txn['type'] === 'debit') {
//                         $new_balance -= $txn['amount'];
//                     }
//                 }

//                 $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                         VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':balance', $new_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->execute();

//                 $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                         VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0)";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':total_credit', $total_credit, PDO::PARAM_STR);
//                 $stmt->bindValue(':total_debit', array_sum(array_column(array_filter($temp_transactions, function ($t) {
//                     return $t['type'] === 'debit';
//                 }), 'amount')), PDO::PARAM_STR);
//                 $stmt->bindValue(':closing_balance', $new_balance, PDO::PARAM_STR);
//                 $stmt->execute();

//                 $sql = "UPDATE accounts SET current_balance = :current_balance, last_transaction_date = :balance_date WHERE id = :account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':current_balance', $new_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//             }

//             $pdo->commit();
//             $success_message = "Opening balance saved successfully!";
//         } catch (Exception $e) {
//             $pdo->rollBack();
//             $error_message = "Error saving opening balance: " . $e->getMessage();
//         }
//     }
// }


// if (isset($_POST['btn_save_opening_balance'])) {
//     $account_id = trim($_POST['account_id']);
//     $opening_balance = floatval($_POST['opening_balance']);
//     $balance_date = $_POST['balance_date'];

//     if (empty($account_id) || empty($balance_date)) {
//         $error_message = "Please fill in all required fields.";
//     } elseif ($opening_balance < 0) {
//         $error_message = "Opening balance cannot be negative.";
//     } else {
//         try {
//             $pdo->beginTransaction();

//             $formal_account_id = null;
//             $temp_account_id = null;
//             $effective_balance = $opening_balance;
//             $txn_id = null;
//             $account_name = null;

//             if (strpos($account_id, 'temp_') === 0) {
//                 // Existing temporary account
//                 $temp_account_id = substr($account_id, 5);
//                 $sql = "SELECT account_name FROM tbl_temp_accounts WHERE id = :temp_account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//                 $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

//                 if (!$temp_account) {
//                     throw new Exception("Temporary account not found.");
//                 }
//                 $account_name = $temp_account['account_name'];
//             } elseif (!is_numeric($account_id) && !empty($account_id)) {
//                 // New account name
//                 $account_name = $account_id;

//                 // Check if account name exists in accounts
//                 $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 if ($stmt->fetchColumn() > 0) {
//                     throw new Exception("Account name '$account_name' already exists in accounts.");
//                 }

//                 // Check if temporary account exists
//                 $sql = "SELECT id FROM tbl_temp_accounts WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

//                 if ($temp_account) {
//                     $temp_account_id = $temp_account['id'];
//                 } else {
//                     // Create new temporary account
//                     $sql = "INSERT INTO tbl_temp_accounts (account_name, created_at) 
//                             VALUES (:account_name, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $temp_account_id = $pdo->lastInsertId();
//                 }
//             } elseif (is_numeric($account_id)) {
//                 // Formal account
//                 $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//                 if ($stmt->fetchColumn() == 0) {
//                     throw new Exception("Invalid account selected.");
//                 }
//                 $formal_account_id = $account_id;

//                 // Check for open dates before balance_date
//                 $sql = "SELECT MIN(balance_date) FROM tbl_daily_balances 
//                         WHERE account_id = :account_id AND is_closed = 0 AND balance_date < :balance_date";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->execute([
//                     ':account_id' => $formal_account_id,
//                     ':balance_date' => $balance_date
//                 ]);
//                 $earliest_open_date = $stmt->fetchColumn();
//                 if ($earliest_open_date) {
//                     throw new Exception("Cannot save opening balance. Please close the date $earliest_open_date first.");
//                 }

//                 // Check if reopening for a new date
//                 $sql = "SELECT last_transaction_date, current_balance FROM accounts WHERE id = :account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//                 $account = $stmt->fetch(PDO::FETCH_ASSOC);
//                 $last_transaction_date = $account['last_transaction_date'];
//                 $current_balance = $account['current_balance'];

//                 if ($balance_date > $last_transaction_date) {
//                     // Reopening for a new date
//                     $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                             WHERE account_id = :account_id AND balance_date < :balance_date 
//                             ORDER BY balance_date DESC LIMIT 1";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $effective_balance = $stmt->fetchColumn() ?: $current_balance;

//                     if ($opening_balance != $effective_balance) {
//                         throw new Exception("Opening balance must match previous day's closing balance (₹$effective_balance).");
//                     }

//                     // Check for existing opening balance
//                     $sql = "SELECT id FROM tbl_opening_balances 
//                             WHERE account_id = :account_id AND balance_date = :balance_date";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     if ($stmt->fetchColumn()) {
//                         throw new Exception("Opening balance already set for this date.");
//                     }

//                     // Insert into tbl_opening_balances
//                     $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                             VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->execute();

//                     // Check for existing opening balance transaction
//                     $sql = "SELECT id FROM tbl_transactions 
//                             WHERE account_id = :account_id AND txn_date = :txn_date AND is_opening_balance = 1";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     if ($stmt->fetchColumn()) {
//                         throw new Exception("Opening balance transaction already exists for this date.");
//                     }

//                     // Insert opening balance transaction
//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
//                             VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':amount', $effective_balance, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $txn_id = $pdo->lastInsertId();

//                     // Update tbl_account_balances
//                     $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                             VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();

//                     // Update tbl_daily_balances
//                     $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                             VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                             ON DUPLICATE KEY UPDATE 
//                             opening_balance = :opening_balance,
//                             total_credit = :total_credit,
//                             total_debit = :total_debit,
//                             closing_balance = :closing_balance";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_credit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_debit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':closing_balance', $effective_balance, PDO::PARAM_STR);
//                     $stmt->execute();

//                     // Update accounts
//                     $sql = "UPDATE accounts SET last_transaction_date = :balance_date WHERE id = :account_id";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->execute();
//                 } else {
//                     // Initial or same-day opening balance
//                     $sql = "SELECT id FROM tbl_opening_balances 
//                             WHERE account_id = :account_id AND balance_date = :balance_date";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();
//                     if ($stmt->fetchColumn()) {
//                         throw new Exception("Opening balance already set for this date.");
//                     }

//                     // Insert into tbl_opening_balances
//                     $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                             VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->execute();

//                     $sql = "UPDATE accounts SET opening_balance = :opening_balance, current_balance = :opening_balance, last_transaction_date = :balance_date WHERE id = :account_id";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->execute();

//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
//                             VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':amount', $opening_balance, PDO::PARAM_STR);
//                     $stmt->execute();
//                     $txn_id = $pdo->lastInsertId();

//                     $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                             VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->execute();

//                     $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                             VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                             ON DUPLICATE KEY UPDATE 
//                             opening_balance = :opening_balance,
//                             total_credit = :total_credit,
//                             total_debit = :total_debit,
//                             closing_balance = :closing_balance";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                     $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_credit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':total_debit', 0.00, PDO::PARAM_STR);
//                     $stmt->bindValue(':closing_balance', $opening_balance, PDO::PARAM_STR);
//                     $stmt->execute();
//                 }
//             } else {
//                 throw new Exception("Invalid account selection.");
//             }

//             // Process temporary account if exists
//             if ($temp_account_id && $account_name) {
//                 // Check again for account name in accounts (in case of race condition)
//                 $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 if ($stmt->fetchColumn() > 0) {
//                     throw new Exception("Account name '$account_name' already exists in accounts.");
//                 }

//                 // Fetch all transactions from tbl_temp_transactions
//                 $sql = "SELECT id, amount, description, txn_date, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at 
//                         FROM tbl_temp_transactions 
//                         WHERE account_name = :account_name 
//                         ORDER BY created_at";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();
//                 $temp_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//                 // Calculate total credit for opening balance
//                 $total_credit = 0.0;
//                 foreach ($temp_transactions as $txn) {
//                     if ($txn['type'] === 'credit') {
//                         $total_credit += $txn['amount'];
//                     }
//                 }

//                 if ($total_credit > 0 && $opening_balance != $total_credit) {
//                     throw new Exception("Opening balance must match total credit transactions (₹$total_credit).");
//                 }
//                 $effective_balance = $opening_balance;

//                 // Insert into accounts
//                 $sql = "INSERT INTO accounts (account_name, opening_balance, created_at, current_balance, last_transaction_date) 
//                         VALUES (:account_name, :opening_balance, :created_at, :current_balance, :balance_date)";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->bindValue(':opening_balance', 0.00, PDO::PARAM_STR);
//                 $stmt->bindValue(':created_at', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':current_balance', $effective_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->execute();
//                 $formal_account_id = $pdo->lastInsertId();

//                 // Insert into tbl_opening_balances
//                 $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                         VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                 $stmt->execute();

//                 // Move transactions to tbl_transactions
//                 foreach ($temp_transactions as $txn) {
//                     $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at, is_opening_balance) 
//                             VALUES (:account_id, :txn_date, :description, :amount, :get_from, :type, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, :created_at, 0)";
//                     $stmt = $pdo->prepare($sql);
//                     $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                     $stmt->bindValue(':txn_date', $txn['txn_date'], PDO::PARAM_STR);
//                     $stmt->bindValue(':description', $txn['description'], PDO::PARAM_STR);
//                     $stmt->bindValue(':amount', $txn['amount'], PDO::PARAM_STR);
//                     $stmt->bindValue(':get_from', $txn['get_from'], PDO::PARAM_STR);
//                     $stmt->bindValue(':type', $txn['type'], PDO::PARAM_STR);
//                     $stmt->bindValue(':expense_category', $txn['expense_category'], PDO::PARAM_STR);
//                     $stmt->bindValue(':other_category', $txn['other_category'], PDO::PARAM_STR);
//                     $stmt->bindValue(':payment_mode', $txn['payment_mode'], PDO::PARAM_STR);
//                     $stmt->bindValue(':transaction_id', $txn['transaction_id'], PDO::PARAM_STR);
//                     $stmt->bindValue(':cheque_no', $txn['cheque_no'], PDO::PARAM_STR);
//                     $stmt->bindValue(':bank_name', $txn['bank_name'], PDO::PARAM_STR);
//                     $stmt->bindValue(':cheque_date', $txn['cheque_date'], PDO::PARAM_STR);
//                     $stmt->bindValue(':created_at', $txn['created_at'], PDO::PARAM_STR);
//                     $stmt->execute();
//                     $txn_id = $pdo->lastInsertId();
//                 }

//                 // Delete from tbl_temp_transactions
//                 $sql = "DELETE FROM tbl_temp_transactions WHERE account_name = :account_name";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//                 $stmt->execute();

//                 // Delete from tbl_temp_accounts
//                 $sql = "DELETE FROM tbl_temp_accounts WHERE id = :temp_account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
//                 $stmt->execute();

//                 // Update balances
//                 $new_balance = $effective_balance;
//                 foreach ($temp_transactions as $txn) {
//                     if ($txn['type'] === 'debit') {
//                         $new_balance -= $txn['amount'];
//                     }
//                 }

//                 $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                         VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':balance', $new_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->execute();

//                 $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                         VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0)";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':total_credit', $total_credit, PDO::PARAM_STR);
//                 $stmt->bindValue(':total_debit', array_sum(array_column(array_filter($temp_transactions, function ($t) {
//                     return $t['type'] === 'debit';
//                 }), 'amount')), PDO::PARAM_STR);
//                 $stmt->bindValue(':closing_balance', $new_balance, PDO::PARAM_STR);
//                 $stmt->execute();

//                 $sql = "UPDATE accounts SET current_balance = :current_balance, last_transaction_date = :balance_date WHERE id = :account_id";
//                 $stmt = $pdo->prepare($sql);
//                 $stmt->bindValue(':current_balance', $new_balance, PDO::PARAM_STR);
//                 $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
//                 $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
//                 $stmt->execute();
//             }

//             $pdo->commit();
//             $success_message = "Opening balance saved successfully!";
//             error_log("Opening Balance Saved: Account: $account_id, Date: $balance_date, Balance: $effective_balance");
//         } catch (Exception $e) {
//             $pdo->rollBack();
//             $error_message = "Error saving opening balance: " . $e->getMessage();
//         }
//     }
// }

//end code here ///

if (isset($_POST['btn_save_opening_balance'])) {
    $account_id = trim($_POST['account_id']);
    $opening_balance = floatval($_POST['opening_balance']);
    $balance_date = $_POST['balance_date'];

    if (empty($account_id) || empty($balance_date)) {
        $error_message = "Please fill in all required fields.";
    } elseif ($opening_balance < 0) {
        $error_message = "Opening balance cannot be negative.";
    } else {
        try {
            $pdo->beginTransaction();

            $formal_account_id = null;
            $temp_account_id = null;
            $effective_balance = $opening_balance;
            $txn_id = null;
            $account_name = null;

            if (strpos($account_id, 'temp_') === 0) {
                // Existing temporary account
                $temp_account_id = substr($account_id, 5);
                $sql = "SELECT account_name FROM tbl_temp_accounts WHERE id = :temp_account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
                $stmt->execute();
                $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$temp_account) {
                    throw new Exception("Temporary account not found.");
                }
                $account_name = $temp_account['account_name'];
            } elseif (!is_numeric($account_id) && !empty($account_id)) {
                // New account name
                $account_name = $account_id;

                // Check if account name exists in accounts
                $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Account name '$account_name' already exists in accounts.");
                }

                // Check if temporary account exists
                $sql = "SELECT id FROM tbl_temp_accounts WHERE account_name = :account_name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                $stmt->execute();
                $temp_account = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($temp_account) {
                    $temp_account_id = $temp_account['id'];
                } else {
                    // Create new temporary account
                    $sql = "INSERT INTO tbl_temp_accounts (account_name, created_at) 
                            VALUES (:account_name, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                    $stmt->execute();
                    $temp_account_id = $pdo->lastInsertId();
                }
            } elseif (is_numeric($account_id)) {
                // Formal account
                $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_id', $account_id, PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->fetchColumn() == 0) {
                    throw new Exception("Invalid account selected.");
                }
                $formal_account_id = $account_id;

                // Check for open dates before balance_date
                $sql = "SELECT MIN(balance_date) FROM tbl_daily_balances 
                        WHERE account_id = :account_id AND is_closed = 0 AND balance_date < :balance_date";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $formal_account_id,
                    ':balance_date' => $balance_date
                ]);
                $earliest_open_date = $stmt->fetchColumn();
                if ($earliest_open_date) {
                    throw new Exception("Cannot save opening balance. Please close the date $earliest_open_date first.");
                }

                // Check if reopening for a new date
                $sql = "SELECT last_transaction_date, current_balance FROM accounts WHERE id = :account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                $stmt->execute();
                $account = $stmt->fetch(PDO::FETCH_ASSOC);
                $last_transaction_date = $account['last_transaction_date'];
                $current_balance = $account['current_balance'];

                if ($balance_date > $last_transaction_date) {
                    // Reopening for a new date
                    $sql = "SELECT closing_balance FROM tbl_daily_balances 
                            WHERE account_id = :account_id AND balance_date < :balance_date 
                            ORDER BY balance_date DESC LIMIT 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->execute();
                    $effective_balance = $stmt->fetchColumn() ?: $current_balance;

                    if ($opening_balance != $effective_balance) {
                        throw new Exception("Opening balance must match previous day's closing balance (₹$effective_balance).");
                    }

                    // Check for existing opening balance
                    $sql = "SELECT id FROM tbl_opening_balances 
                            WHERE account_id = :account_id AND balance_date = :balance_date";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->execute();
                    if ($stmt->fetchColumn()) {
                        throw new Exception("Opening balance already set for this date.");
                    }

                    // Insert into tbl_opening_balances
                    $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
                            VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
                    $stmt->execute();

                    // Check for existing opening balance transaction
                    $sql = "SELECT id FROM tbl_transactions 
                            WHERE account_id = :account_id AND txn_date = :txn_date AND is_opening_balance = 1";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
                    $stmt->execute();
                    if ($stmt->fetchColumn()) {
                        throw new Exception("Opening balance transaction already exists for this date.");
                    }

                    // Insert opening balance transaction
                    $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
                            VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':amount', $effective_balance, PDO::PARAM_STR);
                    $stmt->execute();
                    $txn_id = $pdo->lastInsertId();

                    // Update tbl_account_balances
                    $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                            VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance', $effective_balance, PDO::PARAM_STR);
                    $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
                    $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
                    $stmt->execute();

                    // Update tbl_daily_balances
                    $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
                            VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
                            ON DUPLICATE KEY UPDATE 
                            opening_balance = :opening_balance,
                            total_credit = :total_credit,
                            total_debit = :total_debit,
                            closing_balance = :closing_balance";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
                    $stmt->bindValue(':total_credit', 0.00, PDO::PARAM_STR);
                    $stmt->bindValue(':total_debit', 0.00, PDO::PARAM_STR);
                    $stmt->bindValue(':closing_balance', $effective_balance, PDO::PARAM_STR);
                    $stmt->execute();

                    // Update accounts
                    $sql = "UPDATE accounts SET last_transaction_date = :balance_date WHERE id = :account_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // Initial or same-day opening balance
                    $sql = "SELECT id FROM tbl_opening_balances 
                            WHERE account_id = :account_id AND balance_date = :balance_date";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->execute();
                    if ($stmt->fetchColumn()) {
                        throw new Exception("Opening balance already set for this date.");
                    }

                    // Insert into tbl_opening_balances
                    $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
                            VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
                    $stmt->execute();

                    $sql = "UPDATE accounts SET opening_balance = :opening_balance, current_balance = :opening_balance, last_transaction_date = :balance_date WHERE id = :account_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
                            VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':amount', $opening_balance, PDO::PARAM_STR);
                    $stmt->execute();
                    $txn_id = $pdo->lastInsertId();

                    $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                            VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance', $opening_balance, PDO::PARAM_STR);
                    $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
                    $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
                    $stmt->execute();

                    $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
                            VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
                            ON DUPLICATE KEY UPDATE 
                            opening_balance = :opening_balance,
                            total_credit = :total_credit,
                            total_debit = :total_debit,
                            closing_balance = :closing_balance";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                    $stmt->bindValue(':opening_balance', $opening_balance, PDO::PARAM_STR);
                    $stmt->bindValue(':total_credit', 0.00, PDO::PARAM_STR);
                    $stmt->bindValue(':total_debit', 0.00, PDO::PARAM_STR);
                    $stmt->bindValue(':closing_balance', $opening_balance, PDO::PARAM_STR);
                    $stmt->execute();
                }
            } else {
                throw new Exception("Invalid account selection.");
            }

            // Process temporary account if exists
            if ($temp_account_id && $account_name) {
                // Check again for account name in accounts (in case of race condition)
                $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Account name '$account_name' already exists in accounts.");
                }

                // Fetch all transactions from tbl_temp_transactions
                $sql = "SELECT id, amount, description, txn_date, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at 
                        FROM tbl_temp_transactions 
                        WHERE account_name = :account_name 
                        ORDER BY created_at";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                $stmt->execute();
                $temp_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Calculate total credit for opening balance
                $total_credit = 0.0;
                foreach ($temp_transactions as $txn) {
                    if ($txn['type'] === 'credit') {
                        $total_credit += $txn['amount'];
                    }
                }

                if ($total_credit > 0 && $opening_balance != $total_credit) {
                    throw new Exception("Opening balance must match total credit transactions (₹$total_credit).");
                }
                $effective_balance = $opening_balance;

                // Insert into accounts
                $sql = "INSERT INTO accounts (account_name, opening_balance, created_at, current_balance, last_transaction_date) 
                        VALUES (:account_name, :opening_balance, :created_at, :current_balance, :balance_date)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                $stmt->bindValue(':opening_balance', 0.00, PDO::PARAM_STR);
                $stmt->bindValue(':created_at', $balance_date, PDO::PARAM_STR);
                $stmt->bindValue(':current_balance', $effective_balance, PDO::PARAM_STR);
                $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                $stmt->execute();
                $formal_account_id = $pdo->lastInsertId();

                // Insert into tbl_opening_balances
                $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
                        VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
                $stmt->execute();

                // Move transactions to tbl_transactions
                foreach ($temp_transactions as $txn) {
                    $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, expense_category, other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at, is_opening_balance) 
                            VALUES (:account_id, :txn_date, :description, :amount, :get_from, :type, :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, :bank_name, :cheque_date, :created_at, 0)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                    $stmt->bindValue(':txn_date', $txn['txn_date'], PDO::PARAM_STR);
                    $stmt->bindValue(':description', $txn['description'], PDO::PARAM_STR);
                    $stmt->bindValue(':amount', $txn['amount'], PDO::PARAM_STR);
                    $stmt->bindValue(':get_from', $txn['get_from'], PDO::PARAM_STR);
                    $stmt->bindValue(':type', $txn['type'], PDO::PARAM_STR);
                    $stmt->bindValue(':expense_category', $txn['expense_category'], PDO::PARAM_STR);
                    $stmt->bindValue(':other_category', $txn['other_category'], PDO::PARAM_STR);
                    $stmt->bindValue(':payment_mode', $txn['payment_mode'], PDO::PARAM_STR);
                    $stmt->bindValue(':transaction_id', $txn['transaction_id'], PDO::PARAM_STR);
                    $stmt->bindValue(':cheque_no', $txn['cheque_no'], PDO::PARAM_STR);
                    $stmt->bindValue(':bank_name', $txn['bank_name'], PDO::PARAM_STR);
                    $stmt->bindValue(':cheque_date', $txn['cheque_date'], PDO::PARAM_STR);
                    $stmt->bindValue(':created_at', $txn['created_at'], PDO::PARAM_STR);
                    $stmt->execute();
                    $txn_id = $pdo->lastInsertId();
                }

                // Delete from tbl_temp_transactions
                $sql = "DELETE FROM tbl_temp_transactions WHERE account_name = :account_name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
                $stmt->execute();

                // Delete from tbl_temp_accounts
                $sql = "DELETE FROM tbl_temp_accounts WHERE id = :temp_account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
                $stmt->execute();

                // Update balances
                $new_balance = $effective_balance;
                foreach ($temp_transactions as $txn) {
                    if ($txn['type'] === 'debit') {
                        $new_balance -= $txn['amount'];
                    }
                }

                $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                        VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                $stmt->bindValue(':balance', $new_balance, PDO::PARAM_STR);
                $stmt->bindValue(':txn_id', $txn_id, PDO::PARAM_INT);
                $stmt->bindValue(':txn_date', $balance_date, PDO::PARAM_STR);
                $stmt->execute();

                $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
                        VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                $stmt->bindValue(':opening_balance', $effective_balance, PDO::PARAM_STR);
                $stmt->bindValue(':total_credit', $total_credit, PDO::PARAM_STR);
                $stmt->bindValue(':total_debit', array_sum(array_column(array_filter($temp_transactions, function ($t) {
                    return $t['type'] === 'debit';
                }), 'amount')), PDO::PARAM_STR);
                $stmt->bindValue(':closing_balance', $new_balance, PDO::PARAM_STR);
                $stmt->execute();

                $sql = "UPDATE accounts SET current_balance = :current_balance, last_transaction_date = :balance_date WHERE id = :account_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':current_balance', $new_balance, PDO::PARAM_STR);
                $stmt->bindValue(':balance_date', $balance_date, PDO::PARAM_STR);
                $stmt->bindValue(':account_id', $formal_account_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $pdo->commit();
            $success_message = "Opening balance saved successfully!";
            error_log("Opening Balance Saved: Account: $account_id, Date: $balance_date, Balance: $effective_balance");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error saving opening balance: " . $e->getMessage();
        }
    }
}




//end code here///

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

//             // Get the latest closed date
//             $sql = "SELECT MAX(balance_date) FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND is_closed = 1";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([':account_id' => $account_id]);
//             $latest_closed_date = $stmt->fetchColumn();

//             // Find the earliest open date after the latest closed date
//             $sql = "SELECT MIN(date_value) FROM (
//                         SELECT balance_date AS date_value FROM tbl_daily_balances 
//                         WHERE account_id = :account_id AND is_closed = 0 
//                         AND balance_date > :latest_closed_date
//                         UNION
//                         SELECT txn_date AS date_value FROM tbl_transactions 
//                         WHERE account_id = :account_id 
//                         AND txn_date > :latest_closed_date
//                     ) AS open_dates";
//             $params = [':account_id' => $account_id, ':latest_closed_date' => $latest_closed_date ?: '1970-01-01'];
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute($params);
//             $last_open_date = $stmt->fetchColumn();

//             // Validate close_date is the last open date
//             if ($last_open_date && $close_date !== $last_open_date) {
//                 throw new Exception("Cannot close this date. Please close the last open date ($last_open_date) first.");
//             }
//             if (!$last_open_date && $latest_closed_date && $close_date <= $latest_closed_date) {
//                 throw new Exception("Cannot close a date on or before the latest closed date ($latest_closed_date).");
//             }

//             // Get opening balance from previous day's closing balance
//             $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND balance_date < :balance_date 
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
//             $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed, created_at_timestamp, updated_at) 
//                     VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 1, NOW(), NOW()) 
//                     ON DUPLICATE KEY UPDATE 
//                     opening_balance = :opening_balance_wallet,
//                     total_credit = :total_credit,
//                     total_debit = :total_debit,
//                     closing_balance = :closing_balance_wallet,
//                     is_closed = 1,
//                     updated_at = NOW()";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':account_id' => $account_id,
//                 ':balance_date' => $close_date,
//                 ':opening_balance' => $opening_balance,
//                 ':total_credit' => $total_credit,
//                 ':total_debit' => $total_debit,
//                 ':closing_balance' => $closing_balance,
//                 ':opening_balance_wallet' => $opening_balance,
//                 ':closing_balance_wallet' => $closing_balance
//             ]);

//             // Update accounts table
//             $sql = "UPDATE accounts 
//                     SET amount = :amount, last_transaction_id = :close_date 
//                 WHERE id = :account_id";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':amount' => $closing_balance,
//                 ':close_date' => $close_date,
//                 ':account_id' => $account_id
//             ]);

//             $pdo->commit();
//             $success_message = "Day closed successfully!";
//             error_log("Closed Date: $close_date, Account: $account_id, Opening: $opening_balance, Debit: $total_debit, Amount: $total_amount, Closing: $closing_balance");
//         } catch (Exception $e) {
//             $pdo->rollBack();
//             $error_message = "Error closing day: " . $e->getMessage();
//         }
//     }
// }

//here is the code for close date handle of two accounts
if (isset($_POST['btn_close_day'])) {
    $account_id = trim($_POST['account_id']);
    $close_date = $_POST['close_date'];

    if (empty($account_id) || empty($close_date)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            $pdo->beginTransaction();

            // Validate account
            $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id]);
            if ($stmt->fetchColumn() == 0) {
                throw new Exception("Invalid account selected.");
            }

            // Check if day is already closed
            $sql = "SELECT is_closed FROM tbl_daily_balances 
                    WHERE account_id = :account_id AND balance_date = :balance_date";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
            $is_closed = $stmt->fetchColumn();
            if ($is_closed === '1') {
                throw new Exception("Day is already closed.");
            }

            // Check for opening balance on close_date
            $sql = "SELECT COUNT(*) FROM tbl_opening_balances 
                    WHERE account_id = :account_id AND balance_date = :balance_date";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
            $has_opening_balance = $stmt->fetchColumn() > 0;

            // Check for prior closure
            $sql = "SELECT MAX(balance_date) FROM tbl_daily_balances 
                    WHERE account_id = :account_id AND is_closed = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id]);
            $latest_closed_date = $stmt->fetchColumn();

            // Block closure if prior closure exists and no opening balance for close_date
            if ($latest_closed_date && !$has_opening_balance) {
                throw new Exception("Cannot close this account. It has a prior closure on $latest_closed_date. Reopen it for this date first.");
            }

            // Find the earliest open date after the latest closed date
            $sql = "SELECT MIN(date_value) FROM (
                        SELECT balance_date AS date_value FROM tbl_daily_balances 
                        WHERE account_id = :account_id AND is_closed = 0 
                        AND (:latest_closed_date IS NULL OR balance_date > :latest_closed_date)
                        UNION
                        SELECT txn_date AS date_value FROM tbl_transactions 
                        WHERE account_id = :account_id 
                        AND (:latest_closed_date IS NULL OR txn_date > :latest_closed_date)
                    ) AS open_dates";
            $params = [':account_id' => $account_id, ':latest_closed_date' => $latest_closed_date];
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $last_open_date = $stmt->fetchColumn();

            // Log validation details
            error_log("Closing Account: $account_id, Close Date: $close_date, Latest Closed: " . ($latest_closed_date ?: 'None') . ", Last Open: " . ($last_open_date ?: 'None') . ", Has Opening Balance: " . ($has_opening_balance ? 'Yes' : 'No'));

            // Validate close_date is the last open date or the next logical date
            if ($last_open_date && $close_date !== $last_open_date) {
                throw new Exception("Cannot close this date. Please close the last open date ($last_open_date) first.");
            }
            if (!$last_open_date && $latest_closed_date && $close_date <= $latest_closed_date) {
                throw new Exception("Cannot close a date on or before the latest closed date ($latest_closed_date).");
            }

            // Get opening balance from previous day's closing balance
            $sql = "SELECT closing_balance FROM tbl_daily_balances 
                    WHERE account_id = :account_id AND balance_date < :balance_date 
                    ORDER BY balance_date DESC LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
            $opening_balance = $stmt->fetchColumn() ?: 0.00;

            // Calculate total_credit and total_debit from transactions
            $sql = "SELECT 
                        COALESCE(SUM(CASE WHEN type = 'credit' AND is_opening_balance = 0 THEN amount ELSE 0 END), 0) AS total_credit,
                        COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) AS total_debit
                    FROM tbl_transactions 
                    WHERE account_id = :account_id AND txn_date = :balance_date";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
            $totals = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_credit = $totals['total_credit'];
            $total_debit = $totals['total_debit'];

            // Calculate closing balance
            $closing_balance = $opening_balance + $total_credit - $total_debit;

            // Update or insert tbl_daily_balances
            $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed, created_at, updated_at) 
                    VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 1, NOW(), NOW()) 
                    ON DUPLICATE KEY UPDATE 
                    opening_balance = :opening_balance_wallet,
                    total_credit = :total_credit,
                    total_debit = :total_debit,
                    closing_balance = :closing_balance_wallet,
                    is_closed = 1,
                    updated_at = NOW()";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':account_id' => $account_id,
                ':balance_date' => $close_date,
                ':opening_balance' => $opening_balance,
                ':total_credit' => $total_credit,
                ':total_debit' => $total_debit,
                ':closing_balance' => $closing_balance,
                ':opening_balance_wallet' => $opening_balance,
                ':closing_balance_wallet' => $closing_balance
            ]);

            // Update accounts table
            $sql = "UPDATE accounts 
                    SET current_balance = :current_balance, last_transaction_date = :close_date 
                    WHERE id = :account_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':current_balance' => $closing_balance,
                ':close_date' => $close_date,
                ':account_id' => $account_id
            ]);

            $pdo->commit();
            $success_message = "Day closed successfully!";
            error_log("Closed Date: $close_date, Account: $account_id, Opening: $opening_balance, Debit: $total_debit, Credit: $total_credit, Closing: $closing_balance");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error closing day: " . $e->getMessage();
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
                                <h3>Set Opening Balance</h3>

                                <!-- Display success or error message -->
                                <?php if ($success_message): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                                <?php endif; ?>
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                                <?php endif; ?>

                                <!-- Opening Balance Form -->
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="account_id">Account:</label>
                                        <select class="form-control select2" name="account_id" id="account_id" required>
                                            <option value="">-- Select or type Account --</option>
                                            <!-- Formal accounts -->
                                            <?php
                                            $sql = "SELECT id, account_name FROM accounts ORDER BY account_name";
                                            $stmt = $pdo->query($sql);
                                            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($accounts as $account) {
                                                echo "<option value='{$account['id']}'" . (isset($selected_account_id) && $account['id'] == $selected_account_id ? ' selected' : '') . ">" . htmlspecialchars($account['account_name']) . "</option>";
                                            }
                                            ?>
                                            <!-- Temporary accounts -->
                                            <?php
                                            $sql = "SELECT id, account_name FROM tbl_temp_accounts WHERE NOT EXISTS (
                SELECT 1 FROM accounts WHERE account_name = tbl_temp_accounts.account_name
            ) ORDER BY account_name";
                                            $stmt = $pdo->query($sql);
                                            $temp_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($temp_accounts as $account) {
                                                echo "<option value='temp_{$account['id']}'" . (isset($selected_account_id) && "temp_{$account['id']}" == $selected_account_id ? ' selected' : '') . ">" . htmlspecialchars($account['account_name']) . " (Temporary)</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="opening_balance">Opening Balance (₹):</label>
                                        <input type="number" step="0.01" class="form-control" name="opening_balance" id="opening_balance" value="0.00" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="balance_date">Date:</label>
                                        <input type="date" class="form-control" name="balance_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <button type="submit" name="btn_save_opening_balance" class="btn btn-primary">Save Opening Balance</button>
                                </form>


                                <!-- Day Closing Form -->
                                <form method="POST" action="" class="mb-4">
                                    <div class="form-group">
                                        <label for="account_id">Account</label>
                                        <select class="form-control select2" name="account_id" required>
                                            <option value="">-- Select Account --</option>
                                            <?php foreach ($accounts as $account): ?>
                                                <option value="<?php echo $account['id']; ?>">
                                                    <?php echo htmlspecialchars($account['account_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="close_date">Closing Date</label>
                                        <input type="date" class="form-control" name="close_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <button type="submit" name="btn_close_day" class="btn btn-danger">Close Day</button>
                                </form>

                                <?php

                                try {
                                    $stmt = $pdo->prepare("
        SELECT a.account_name, ob.opening_balance, ob.created_at, ob.balance_date
        FROM tbl_opening_balances ob
        INNER JOIN accounts a ON ob.account_id = a.id
        ORDER BY ob.created_at DESC
    ");
                                    $stmt->execute();
                                    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    echo "Error fetching opening balances: " . $e->getMessage();
                                    $accounts = [];
                                }
                                ?>

                                <!-- Existing Accounts Table -->
                                <h4>Existing Opening Balances</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Opening Balance</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($accounts)): ?>
                                            <?php foreach ($accounts as $account): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($account['account_name']); ?></td>
                                                    <td><?php echo number_format($account['opening_balance'], 2); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($account['balance_date'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3">No opening balances found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#account_id').select2({
                placeholder: "-- Select or type account name --",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $('#account_id').on('change', function() {
                var accountId = $(this).val();
                if (accountId.startsWith('temp_')) {
                    var tempAccountId = accountId.replace('temp_', '');
                    // Fetch transaction amount via AJAX
                    $.ajax({
                        url: 'get_temp_transaction.php',
                        method: 'POST',
                        data: {
                            temp_account_id: tempAccountId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.amount) {
                                $('#opening_balance').val(response.amount);
                            } else {
                                $('#opening_balance').val('0.00');
                            }
                        },
                        error: function() {
                            $('#opening_balance').val('0.00');
                        }
                    });
                } else {
                    $('#opening_balance').val('0.00');
                }
            });

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