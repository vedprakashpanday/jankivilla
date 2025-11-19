<?php

// date_default_timezone_set('Asia/Kolkata');

// // close_accounts.php
// include_once 'connectdb.php';


// date_default_timezone_set('Asia/Kolkata');
// ini_set('display_errors', 0); // Disable display for cron
// ini_set('display_startup_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/var/www/html/harihomes.co/UI/account/close_accounts_debug.log');
// error_reporting(E_ALL | E_STRICT);

// file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//     "Close accounts started: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// try {

//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Database connection included\n", FILE_APPEND);

//     $pdo->beginTransaction();
//     $close_date = date('Y-m-d');
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Processing closure for date: $close_date\n", FILE_APPEND);

//     // Fetch all accounts
//     $sql = "SELECT id FROM accounts";
//     $stmt = $pdo->query($sql);
//     $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Found " . count($accounts) . " accounts to process\n", FILE_APPEND);

//     foreach ($accounts as $account) {
//         $account_id = $account['id'];

//         // Check if day is already closed
//         $sql = "SELECT is_closed FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $is_closed = $stmt->fetchColumn();
//         if ($is_closed === '1' || $is_closed === 1) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: Day $close_date already closed\n", FILE_APPEND);
//             continue;
//         }

//         // Check for opening balance on close_date
//         $sql = "SELECT COUNT(*) FROM tbl_opening_balances 
//                 WHERE account_id = :account_id AND balance_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $has_opening_balance = $stmt->fetchColumn() > 0;

//         // Check for prior closure
//         $sql = "SELECT MAX(balance_date) FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND is_closed = 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id]);
//         $latest_closed_date = $stmt->fetchColumn();

//         if ($latest_closed_date && !$has_opening_balance) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: Prior closure on $latest_closed_date, no opening balance for $close_date\n", FILE_APPEND);
//             continue;
//         }

//         // Find the earliest open date after the latest closed date
//         $sql = "SELECT MIN(date_value) FROM (
//                     SELECT balance_date AS date_value FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND is_closed = 0 
//                     AND (:latest_closed_date IS NULL OR balance_date > :latest_closed_date)
//                     UNION
//                     SELECT txn_date AS date_value FROM tbl_transactions 
//                     WHERE account_id = :account_id 
//                     AND (:latest_closed_date IS NULL OR txn_date > :latest_closed_date)
//                 ) AS open_dates";
//         $params = [':account_id' => $account_id, ':latest_closed_date' => $latest_closed_date];
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute($params);
//         $last_open_date = $stmt->fetchColumn();

//         file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//             "Account $account_id: Close Date=$close_date, Latest Closed=" . ($latest_closed_date ?: 'None') . 
//             ", Last Open=" . ($last_open_date ?: 'None') . ", Has Opening Balance=" . ($has_opening_balance ? 'Yes' : 'No') . "\n", 
//             FILE_APPEND);

//         if ($last_open_date && $close_date !== $last_open_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: Cannot close $close_date, last open date is $last_open_date\n", FILE_APPEND);
//             continue;
//         }
//         if (!$last_open_date && $latest_closed_date && $close_date <= $latest_closed_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: Cannot close $close_date, latest closed date is $latest_closed_date\n", FILE_APPEND);
//             continue;
//         }

//         // Get opening balance
//         $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date < :balance_date 
//                 ORDER BY balance_date DESC LIMIT 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $opening_balance = $stmt->fetchColumn() ?: 0.00;

//         // Calculate totals
//         $sql = "SELECT 
//                     COALESCE(SUM(CASE WHEN type = 'credit' AND is_opening_balance = 0 THEN amount ELSE 0 END), 0) AS total_credit,
//                     COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) AS total_debit
//                 FROM tbl_transactions 
//                 WHERE account_id = :account_id AND txn_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $totals = $stmt->fetch(PDO::FETCH_ASSOC);
//         $total_credit = floatval($totals['total_credit']);
//         $total_debit = floatval($totals['total_debit']);

//         // Calculate closing balance
//         $closing_balance = $opening_balance + $total_credit - $total_debit;

//         // Update tbl_daily_balances
//         $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed, created_at, updated_at) 
//                 VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 1, NOW(), NOW()) 
//                 ON DUPLICATE KEY UPDATE 
//                 opening_balance = :opening_balance_wallet, total_credit = :total_credit, 
//                 total_debit = :total_debit, closing_balance = :closing_balance_wallet, 
//                 is_closed = 1, updated_at = NOW()";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance_date' => $close_date,
//             ':opening_balance' => $opening_balance,
//             ':total_credit' => $total_credit,
//             ':total_debit' => $total_debit,
//             ':closing_balance' => $closing_balance,
//             ':opening_balance_wallet' => $opening_balance,
//             ':closing_balance_wallet' => $closing_balance
//         ]);

//         // Update accounts
//         $sql = "UPDATE accounts 
//                 SET current_balance = :current_balance, last_transaction_date = :close_date 
//                 WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':current_balance' => $closing_balance,
//             ':close_date' => $close_date,
//             ':account_id' => $account_id
//         ]);

//         file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//             "Closed Account $account_id: Date=$close_date, Opening=$opening_balance, Debit=$total_debit, Credit=$total_credit, Closing=$closing_balance\n", 
//             FILE_APPEND);
//     }

//     $pdo->commit();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Account closure completed for $close_date at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
//     echo "Success: Accounts closed for $close_date\n";
// } catch (Exception $e) {
//     if (isset($pdo)) {
//         $pdo->rollBack();
//     }
//     $error = "Error closing accounts for $close_date: " . $e->getMessage() . "\n" . $e->getTraceAsString();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', $error . "\n", FILE_APPEND);
//     echo "Error: " . $e->getMessage() . "\n";
//     exit(1);
// }
?>


<!-- //here is below code for closing accounts in case of back date -->
<?php
// date_default_timezone_set('Asia/Kolkata');

// // close_accounts.php
// include_once 'connectdb.php';

// ini_set('display_errors', 0); // Disable display for cron
// ini_set('display_startup_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/var/www/html/harihomes.co/UI/account/close_accounts_debug.log');
// error_reporting(E_ALL | E_STRICT);

// file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//     "Close accounts started: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// try {
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Database connection included\n", FILE_APPEND);

//     $pdo->beginTransaction();

//     // Fetch all accounts
//     $sql = "SELECT id FROM accounts";
//     $stmt = $pdo->query($sql);
//     $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Found " . count($accounts) . " accounts to process\n", FILE_APPEND);

//     foreach ($accounts as $account) {
//         $account_id = $account['id'];

//         // Check for prior closure
//         $sql = "SELECT MAX(balance_date) FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND is_closed = 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id]);
//         $latest_closed_date = $stmt->fetchColumn();

//         // Find the earliest open date after the latest closed date
//         $sql = "SELECT MIN(date_value) FROM (
//                     SELECT balance_date AS date_value FROM tbl_daily_balances 
//                     WHERE account_id = :account_id AND is_closed = 0 
//                     AND (:latest_closed_date IS NULL OR balance_date > :latest_closed_date)
//                     UNION
//                     SELECT txn_date AS date_value FROM tbl_transactions 
//                     WHERE account_id = :account_id 
//                     AND (:latest_closed_date IS NULL OR txn_date > :latest_closed_date)
//                 ) AS open_dates";
//         $params = [':account_id' => $account_id, ':latest_closed_date' => $latest_closed_date];
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute($params);
//         $close_date = $stmt->fetchColumn();

//         if (!$close_date) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: No open dates found for closure\n", FILE_APPEND);
//             continue;
//         }

//         file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//             "Processing closure for account $account_id on date: $close_date\n", FILE_APPEND);

//         // Check if day is already closed
//         $sql = "SELECT is_closed FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $is_closed = $stmt->fetchColumn();
//         if ($is_closed === '1' || $is_closed === 1) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: Day $close_date already closed\n", FILE_APPEND);
//             continue;
//         }

//         // Check for opening balance on close_date
//         $sql = "SELECT COUNT(*) FROM tbl_opening_balances 
//                 WHERE account_id = :account_id AND balance_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $has_opening_balance = $stmt->fetchColumn() > 0;

//         if ($latest_closed_date && !$has_opening_balance) {
//             file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//                 "Account $account_id: Prior closure on $latest_closed_date, no opening balance for $close_date\n", FILE_APPEND);
//             continue;
//         }

//         file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//             "Account $account_id: Close Date=$close_date, Latest Closed=" . ($latest_closed_date ?: 'None') . 
//             ", Has Opening Balance=" . ($has_opening_balance ? 'Yes' : 'No') . "\n", FILE_APPEND);

//         // Get opening balance
//         $sql = "SELECT closing_balance FROM tbl_daily_balances 
//                 WHERE account_id = :account_id AND balance_date < :balance_date 
//                 ORDER BY balance_date DESC LIMIT 1";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $opening_balance = $stmt->fetchColumn() ?: 0.00;

//         // Calculate totals
//         $sql = "SELECT 
//                     COALESCE(SUM(CASE WHEN type = 'credit' AND is_opening_balance = 0 THEN amount ELSE 0 END), 0) AS total_credit,
//                     COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) AS total_debit
//                 FROM tbl_transactions 
//                 WHERE account_id = :account_id AND txn_date = :balance_date";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':account_id' => $account_id, ':balance_date' => $close_date]);
//         $total_credit = 0.00;
//         $total_debit = 0.00;
//         $totals = $stmt->fetch(PDO::FETCH_ASSOC);
//         if ($totals) {
//             $total_credit = floatval($totals['total_credit']);
//             $total_debit = floatval($totals['total_debit']);
//         }

//         // Calculate closing balance
//         $closing_balance = $opening_balance + $total_credit - $total_debit;

//         // Update tbl_daily_balances
//         $sql = "INSERT INTO tbl_daily_balances (account_id, balance_date, opening_balance, total_credit, total_debit, closing_balance, is_closed, created_at, updated_at) 
//                 VALUES (:account_id, :balance_date, :opening_balance, :total_credit, :total_debit, :closing_balance, 1, NOW(), NOW()) 
//                 ON DUPLICATE KEY UPDATE 
//                 opening_balance = :opening_balance_wallet, total_credit = :total_credit, 
//                 total_debit = :total_debit, closing_balance = :closing_balance_wallet, 
//                 is_closed = 1, updated_at = NOW()";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':account_id' => $account_id,
//             ':balance_date' => $close_date,
//             ':opening_balance' => $opening_balance,
//             ':total_credit' => $total_credit,
//             ':total_debit' => $total_debit,
//             ':closing_balance' => $closing_balance,
//             ':opening_balance_wallet' => $opening_balance,
//             ':closing_balance_wallet' => $closing_balance
//         ]);

//         // Update accounts
//         $sql = "UPDATE accounts 
//                 SET current_balance = :current_balance, last_transaction_date = :close_date 
//                 WHERE id = :account_id";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([
//             ':current_balance' => $closing_balance,
//             ':close_date' => $close_date,
//             ':account_id' => $account_id
//         ]);

//         file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//             "Closed Account $account_id: Date=$close_date, Opening=$opening_balance, Debit=$total_debit, Credit=$total_credit, Closing=$closing_balance\n", 
//             FILE_APPEND);
//     }

//     $pdo->commit();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', 
//         "Account closure completed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
//     echo "Success: Accounts closed\n";
// } catch (Exception $e) {
//     if (isset($pdo)) {
//         $pdo->rollBack();
//     }
//     $error = "Error closing accounts: " . $e->getMessage() . "\n" . $e->getTraceAsString();
//     file_put_contents('/var/www/html/harihomes.co/UI/account/close_accounts_debug.log', $error . "\n", FILE_APPEND);
//     echo "Error: " . $e->getMessage() . "\n";
//     exit(1);
// }
?>