<?php
include_once 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['temp_account_id'])) {
    $temp_account_id = trim($_POST['temp_account_id']);

    try {
        $sql = "SELECT t.amount 
                FROM tbl_temp_transactions t
                JOIN tbl_temp_accounts a ON t.account_name = a.account_name
                WHERE a.id = :temp_account_id AND t.type = 'credit'
                ORDER BY t.created_at DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':temp_account_id' => $temp_account_id]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode([
            'amount' => $transaction ? $transaction['amount'] : '0.00'
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['amount' => '0.00']);
    }
}
