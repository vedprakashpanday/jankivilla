<?php
include_once 'connectdb.php';

try {
    $id = $_POST['id'];

    $sql = "SELECT * FROM tbl_debit_transactions WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($transaction);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
