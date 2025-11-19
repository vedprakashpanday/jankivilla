<?php

// date_default_timezone_set('Asia/Kolkata');
// // open_accounts.php
// include_once 'connectdb.php';



// date_default_timezone_set('Asia/Kolkata');
// ini_set('display_errors', 0); // Disable display to avoid cron output issues
// ini_set('display_startup_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/var/www/html/harihomes.co/UI/account/open_accounts_debug.log');
// error_reporting(E_ALL | E_STRICT);

// file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//     "Open accounts started: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// try {
 
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Database connection included\n", FILE_APPEND);

//     $pdo->beginTransaction();
//     $balance_date = date('Y-m-d');
//     $prev_date = date('Y-m-d', strtotime('-1 day'));

//     // Part 1: Open Formal Accounts
//     $sql = "SELECT DISTINCT account_id 
//             FROM tbl_daily_balances 
//             WHERE balance_date = :prev_date AND is_closed = 1";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([':prev_date' => $prev_date]);
//     $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Found " . count($accounts) . " formal accounts to open\n", FILE_APPEND);

//     foreach ($accounts as $account) {
//         $account_id = $account['account_id'];

//         // Skip if already opened
//         $sql = "SELECT COUNT(*) FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $balance_date]);
//         if ($stmt->fetchColumn() > 0) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Account $account_id: Already opened for $balance_date\n", FILE_APPEND);
//             continue;
//         }

//         // Check for open dates before balance_date
//         $sql = "SELECT MIN(balance_date) FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND is_closed = 0 AND balance_date < :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $balance_date]);
//         $earliest_open_date = $stmt->fetchColumn();
//         if ($earliest_open_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Account $account_id: Open date $earliest_open_date exists before $balance_date\n", FILE_APPEND);
//             continue;
//         }

//         // Get previous day's closing balance
//         $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date = :prev_date 
//                 ORDER BY balance_date DESC LIMIT 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':prev_date' => $prev_date]);
//         $opening_balance = $stmt->fetchColumn() ?: 0.00;

//         // Insert into tbl_opening_balances
//         $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                 VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance
//         ]);

//         // Insert opening balance transaction
//         $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
//                 VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':txn_date' => $balance_date,
//             ':amount' => $opening_balance
//         ]);
//         $txn_id = $pdo->lastInsertId();

//         // Insert into tbl_account_balances
//         $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                 VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance' => $opening_balance,
//             ':txn_id' => $txn_id,
//             ':txn_date' => $balance_date
//         ]);

//         // Insert into tbl_daily_balances
//         $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                 VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                 ON DUPLICATE KEY UPDATE 
//                 opening_balance = :opening_balance, total_credit = :total_credit, 
//                 total_debit = :total_debit, closing_balance = :closing_balance";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance,
//             ':total_credit' => 0.00,
//             ':total_debit' => 0.00,
//             ':closing_balance' => $opening_balance
//         ]);

//         // Update accounts
//         $sql = "UPDATE accounts SET last_transaction_date = :balance_date, current_balance = :current_balance 
//                 WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':balance_date' => $balance_date,
//             ':current_balance' => $opening_balance,
//             ':account_id' => $account_id
//         ]);

//         file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//             "Opened Formal Account $account_id: Date=$balance_date, Opening=$opening_balance\n", FILE_APPEND);
//     }

//     // Part 2: Process Temporary Accounts
//     $sql = "SELECT id, account_name FROM tbl_temp_accounts";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute();
//     $temp_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Found " . count($temp_accounts) . " temporary accounts to process\n", FILE_APPEND);

//     foreach ($temp_accounts as $temp_account) {
//         $temp_account_id = $temp_account['id'];
//         $account_name = $temp_account['account_name'];

//         // Check if account name exists in formal accounts
//         $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();
//         if ($stmt->fetchColumn() > 0) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Temp Account $account_name: Already exists in accounts, skipping\n", FILE_APPEND);
//             continue;
//         }

//         // Fetch temp transactions
//         $sql = "SELECT id, amount, description, txn_date, get_from, type, expense_category, 
//                 other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at 
//                 FROM tbl_temp_transactions 
//                 WHERE account_name = :account_name 
//                 ORDER BY created_at";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();
//         $temp_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         // Calculate total credit for opening balance
//         $total_credit = 0.0;
//         foreach ($temp_transactions as $txn) {
//             if ($txn['type'] === 'credit') {
//                 $total_credit += floatval($txn['amount']);
//             }
//         }
//         $opening_balance = $total_credit;

//         // Insert into accounts
//         $sql = "INSERT INTO accounts (account_name, opening_balance, created_at, current_balance, last_transaction_date) 
//                 VALUES (:account_name, :opening_balance, :created_at, :current_balance, :balance_date)";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_name' => $account_name,
//             ':opening_balance' => 0.00,
//             ':created_at' => $balance_date,
//             ':current_balance' => $opening_balance,
//             ':balance_date' => $balance_date
//         ]);
//         $formal_account_id = $pdo->lastInsertId();

//         // Insert into tbl_opening_balances
//         $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                 VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $formal_account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance
//         ]);

//         // Move transactions and track balance
//         $new_balance = $opening_balance;
//         $last_txn_id = 0;
//         foreach ($temp_transactions as $txn) {
//             $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, 
//                     expense_category, other_category, payment_mode, transaction_id, cheque_no, 
//                     bank_name, cheque_date, created_at, is_opening_balance) 
//                     VALUES (:account_id, :txn_date, :description, :amount, :get_from, :type, 
//                     :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, 
//                     :bank_name, :cheque_date, :created_at, FALSE)";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':account_id' => $formal_account_id,
//                 ':txn_date' => $txn['txn_date'],
//                 ':description' => $txn['description'],
//                 ':amount' => $txn['amount'],
//                 ':get_from' => $txn['get_from'] ?: null,
//                 ':type' => $txn['type'],
//                 ':expense_category' => $txn['expense_category'] ?: null,
//                 ':other_category' => $txn['other_category'] ?: null,
//                 ':payment_mode' => $txn['payment_mode'] ?: null,
//                 ':transaction_id' => $txn['transaction_id'] ?: null,
//                 ':cheque_no' => $txn['cheque_no'] ?: null,
//                 ':bank_name' => $txn['bank_name'] ?: null,
//                 ':cheque_date' => $txn['cheque_date'] ?: null,
//                 ':created_at' => $txn['created_at']
//             ]);
//             $last_txn_id = $pdo->lastInsertId();
//             if ($txn['type'] === 'debit') {
//                 $new_balance -= floatval($txn['amount']);
//             }
//         }

//         // Delete from tbl_temp_transactions
//         $sql = "DELETE FROM tbl_temp_transactions WHERE account_name = :account_name";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();

//         // Delete from tbl_temp_accounts
//         $sql = "DELETE FROM tbl_temp_accounts WHERE id = :temp_account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
//         $stmt->execute();

//         // Insert into tbl_account_balances
//         if ($last_txn_id) {
//             $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                     VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':account_id' => $formal_account_id,
//                 ':balance' => $new_balance,
//                 ':txn_id' => $last_txn_id,
//                 ':txn_date' => $balance_date
//             ]);
//         }

//         // Insert into tbl_daily_balances
//         $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                 VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                 ON DUPLICATE KEY UPDATE 
//                 opening_balance = :opening_balance, total_credit = :total_credit, 
//                 total_debit = :total_debit, closing_balance = :closing_balance";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $formal_account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance,
//             ':total_credit' => $total_credit,
//             ':total_debit' => array_sum(array_column(array_filter($temp_transactions, function($t) {
//                 return $t['type'] === 'debit';
//             }), 'amount')),
//             ':closing_balance' => $new_balance
//         ]);

//         // Update accounts
//         $sql = "UPDATE accounts SET current_balance = :current_balance, last_transaction_date = :balance_date 
//                 WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':current_balance' => $new_balance,
//             ':balance_date' => $balance_date,
//             ':account_id' => $formal_account_id
//         ]);

//         file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//             "Converted Temp Account $temp_account_id to Formal Account $formal_account_id: Date=$balance_date, Opening=$opening_balance\n", FILE_APPEND);
//     }

//     $pdo->commit();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Account opening completed for $balance_date at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
//     echo "Success: Accounts opened for $balance_date\n";
// } catch (Exception $e) {
//     if (isset($pdo)) {
//         $pdo->rollBack();
//     }
//     $error = "Error opening accounts for $balance_date: " . $e->getMessage() . "\n" . $e->getTraceAsString();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', $error . "\n", FILE_APPEND);
//     echo "Error: " . $e->getMessage() . "\n";
//     exit(1);
// }
?>

<!-- here is below code for open account handle back date when  -->
<?php
// date_default_timezone_set('Asia/Kolkata');

// // open_accounts.php
// include_once 'connectdb.php';

// ini_set('display_errors', 0); // Disable display to avoid cron output issues
// ini_set('display_startup_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/var/www/html/harihomes.co/UI/account/open_accounts_debug.log');
// error_reporting(E_ALL | E_STRICT);

// file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//     "Open accounts started: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// try {
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Database connection included\n", FILE_APPEND);

//     $pdo->beginTransaction();

//     // Part 1: Open Formal Accounts
//     $sql = "SELECT id FROM accounts";
//     $stmt = $pdo->query($sql);
//     $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Found " . count($accounts) . " formal accounts to process\n", FILE_APPEND);

//     foreach ($accounts as $account) {
//         $account_id = $account['id'];

//         // Find the earliest date that needs to be opened
//         $sql = "SELECT MIN(date_value) FROM (
//                     SELECT txn_date AS date_value FROM tbl_transactions 
//                     WHERE account_id = :account_id 
//                     AND NOT EXISTS (
//                         SELECT 1 FROM tbl_daily_balances 
//                         WHERE account_id = :account_id AND balance_date = tbl_transactions.txn_date
//                     )
//                     UNION
//                     SELECT balance_date AS date_value FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND is_closed = 1 
//                     AND NOT EXISTS (
//                         SELECT 1 FROM tbl_daily_balances 
//                         WHERE account_id = :account_id AND balance_date = DATE_ADD(tbl_daily_balances.balance_date, INTERVAL 1 DAY)
//                     )
//                 ) AS open_dates";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id]);
//         $balance_date = $stmt->fetchColumn();

//         if (!$balance_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Account $account_id: No dates need opening\n", FILE_APPEND);
//             continue;
//         }

//         $prev_date = date('Y-m-d', strtotime($balance_date . ' -1 day'));

//         file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//             "Processing opening for account $account_id on date: $balance_date\n", FILE_APPEND);

//         // Skip if already opened
//         $sql = "SELECT COUNT(*) FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $balance_date]);
//         if ($stmt->fetchColumn() > 0) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Account $account_id: Already opened for $balance_date\n", FILE_APPEND);
//             continue;
//         }

//         // Check for open dates before balance_date
//         $sql = "SELECT MIN(balance_date) FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND is_closed = 0 AND balance_date < :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $balance_date]);
//         $earliest_open_date = $stmt->fetchColumn();
//         if ($earliest_open_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Account $account_id: Open date $earliest_open_date exists before $balance_date\n", FILE_APPEND);
//             continue;
//         }

//         // Get previous day's closing balance
//         $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date = :prev_date 
//                 ORDER BY balance_date DESC LIMIT 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':prev_date' => $prev_date]);
//         $opening_balance = $stmt->fetchColumn() ?: 0.00;

//         // Insert into tbl_opening_balances
//         $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                 VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance
//         ]);

//         // Insert opening balance transaction
//         $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, payment_mode, created_at, is_opening_balance) 
//                 VALUES (:account_id, :txn_date, 'Opening Balance', :amount, 'credit', 'Cash', NOW(), 1)";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':txn_date' => $balance_date,
//             ':amount' => $opening_balance
//         ]);
//         $txn_id = $pdo->lastInsertId();

//         // Insert into tbl_account_balances
//         $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                 VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance' => $opening_balance,
//             ':txn_id' => $txn_id,
//             ':txn_date' => $balance_date
//         ]);

//         // Insert into tbl_daily_balances
//         $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                 VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                 ON DUPLICATE KEY UPDATE 
//                 opening_balance = :opening_balance, total_credit = :total_credit, 
//                 total_debit = :total_debit, closing_balance = :closing_balance";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance,
//             ':total_credit' => 0.00,
//             ':total_debit' => 0.00,
//             ':closing_balance' => $opening_balance
//         ]);

//         // Update accounts
//         $sql = "UPDATE accounts SET last_transaction_date = :balance_date, current_balance = :current_balance 
//                 WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':balance_date' => $balance_date,
//             ':current_balance' => $opening_balance,
//             ':account_id' => $account_id
//         ]);

//         file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//             "Opened Formal Account $account_id: Date=$balance_date, Opening=$opening_balance\n", FILE_APPEND);
//     }

//     // Part 2: Process Temporary Accounts
//     $sql = "SELECT id, account_name FROM tbl_temp_accounts";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute();
//     $temp_accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Found " . count($temp_accounts) . " temporary accounts to process\n", FILE_APPEND);

//     foreach ($temp_accounts as $temp_account) {
//         $temp_account_id = $temp_account['id'];
//         $account_name = $temp_account['account_name'];

//         // Find the earliest transaction date for the temp account
//         $sql = "SELECT MIN(txn_date) FROM tbl_temp_transactions 
//                 WHERE account_name = :account_name";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();
//         $balance_date = $stmt->fetchColumn();

//         if (!$balance_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Temp Account $account_name: No transactions found, skipping\n", FILE_APPEND);
//             continue;
//         }

//         file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//             "Processing temp account $account_name for date: $balance_date\n", FILE_APPEND);

//         // Check if account name exists in formal accounts
//         $sql = "SELECT COUNT(*) FROM accounts WHERE account_name = :account_name";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();
//         if ($stmt->fetchColumn() > 0) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//                 "Temp Account $account_name: Already exists in accounts, skipping\n", FILE_APPEND);
//             continue;
//         }

//         // Fetch temp transactions
//         $sql = "SELECT id, amount, description, txn_date, get_from, type, expense_category, 
//                 other_category, payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at 
//                 FROM tbl_temp_transactions 
//                 WHERE account_name = :account_name 
//                 ORDER BY created_at";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();
//         $temp_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         // Calculate total credit for opening balance
//         $total_credit = 0.0;
//         foreach ($temp_transactions as $txn) {
//             if ($txn['type'] === 'credit') {
//                 $total_credit += floatval($txn['amount']);
//             }
//         }
//         $opening_balance = $total_credit;

//         // Insert into accounts
//         $sql = "INSERT INTO accounts (account_name, opening_balance, created_at, current_balance, last_transaction_date) 
//                 VALUES (:account_name, :opening_balance, :created_at, :current_balance, :balance_date)";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_name' => $account_name,
//             ':opening_balance' => 0.00,
//             ':created_at' => $balance_date,
//             ':current_balance' => $opening_balance,
//             ':balance_date' => $balance_date
//         ]);
//         $formal_account_id = $pdo->lastInsertId();

//         // Insert into tbl_opening_balances
//         $sql = "INSERT INTO tbl_opening_balances (account_id, balance_date, opening_balance, created_at, updated_at) 
//                 VALUES (:account_id, :balance_date, :opening_balance, NOW(), NOW())";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $formal_account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance
//         ]);

//         // Move transactions and track balance
//         $new_balance = $opening_balance;
//         $last_txn_id = 0;
//         foreach ($temp_transactions as $txn) {
//             $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, get_from, type, 
//                     expense_category, other_category, payment_mode, transaction_id, cheque_no, 
//                     bank_name, cheque_date, created_at, is_opening_balance) 
//                     VALUES (:account_id, :txn_date, :description, :amount, :get_from, :type, 
//                     :expense_category, :other_category, :payment_mode, :transaction_id, :cheque_no, 
//                     :bank_name, :cheque_date, :created_at, FALSE)";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':account_id' => $formal_account_id,
//                 ':txn_date' => $txn['txn_date'],
//                 ':description' => $txn['description'],
//                 ':amount' => $txn['amount'],
//                 ':get_from' => $txn['get_from'] ?: null,
//                 ':type' => $txn['type'],
//                 ':expense_category' => $txn['expense_category'] ?: null,
//                 ':other_category' => $txn['other_category'] ?: null,
//                 ':payment_mode' => $txn['payment_mode'] ?: null,
//                 ':transaction_id' => $txn['transaction_id'] ?: null,
//                 ':cheque_no' => $txn['cheque_no'] ?: null,
//                 ':bank_name' => $txn['bank_name'] ?: null,
//                 ':cheque_date' => $txn['cheque_date'] ?: null,
//                 ':created_at' => $txn['created_at']
//             ]);
//             $last_txn_id = $pdo->lastInsertId();
//             if ($txn['type'] === 'debit') {
//                 $new_balance -= floatval($txn['amount']);
//             }
//         }

//         // Delete from tbl_temp_transactions
//         $sql = "DELETE FROM tbl_temp_transactions WHERE account_name = :account_name";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':account_name', $account_name, PDO::PARAM_STR);
//         $stmt->execute();

//         // Delete from tbl_temp_accounts
//         $sql = "DELETE FROM tbl_temp_accounts WHERE id = :temp_account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(':temp_account_id', $temp_account_id, PDO::PARAM_INT);
//         $stmt->execute();

//         // Insert into tbl_account_balances
//         if ($last_txn_id) {
//             $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
//                     VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 ':account_id' => $formal_account_id,
//                 ':balance' => $new_balance,
//                 ':txn_id' => $last_txn_id,
//                 ':txn_date' => $balance_date
//             ]);
//         }

//         // Insert into tbl_daily_balances
//         $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed) 
//                 VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 0) 
//                 ON DUPLICATE KEY UPDATE 
//                 opening_balance = :opening_balance, total_credit = :total_credit, 
//                 total_debit = :total_debit, closing_balance = :closing_balance";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $formal_account_id,
//             ':balance_date' => $balance_date,
//             ':opening_balance' => $opening_balance,
//             ':total_credit' => $total_credit,
//             ':total_debit' => array_sum(array_column(array_filter($temp_transactions, function($t) {
//                 return $t['type'] === 'debit';
//             }), 'amount')),
//             ':closing_balance' => $new_balance
//         ]);

//         // Update accounts
//         $sql = "UPDATE accounts SET current_balance = :current_balance, last_transaction_date = :balance_date 
//                 WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':current_balance' => $new_balance,
//             ':balance_date' => $balance_date,
//             ':account_id' => $formal_account_id
//         ]);

//         file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//             "Converted Temp Account $temp_account_id to Formal Account $formal_account_id: Date=$balance_date, Opening=$opening_balance\n", FILE_APPEND);
//     }

//     $pdo->commit();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', 
//         "Account opening completed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
//     echo "Success: Accounts opened\n";
// } catch (Exception $e) {
//     if (isset($pdo)) {
//         $pdo->rollBack();
//     }
//     $error = "Error opening accounts: " . $e->getMessage() . "\n" . $e->getTraceAsString();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/open_accounts_debug.log', $error . "\n", FILE_APPEND);
//     echo "Error: " . $e->getMessage() . "\n";
//     exit(1);
// }
?>
