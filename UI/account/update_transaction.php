<?php
include_once 'connectdb.php';

try {
    $pdo->beginTransaction();

    $id = $_POST['id'];
    $transaction_date = $_POST['transaction_date'];
    $amount = $_POST['amount'];
    $payee_name = $_POST['payee_name'];
    $description = $_POST['description'];
    $payment_mode = $_POST['payment_mode'];
    $authorized_by = $_POST['authorized_by'];
    $transaction_category = $_POST['transaction_category'];
    $updated_at = date('Y-m-d H:i:s');

    $sql = "UPDATE tbl_debit_transactions SET 
            transaction_date = :transaction_date,
            amount = :amount,
            payee_name = :payee_name,
            description = :description,
            payment_mode = :payment_mode,
            authorized_by = :authorized_by,
            transaction_category = :transaction_category,
            updated_at = :updated_at
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':transaction_date' => $transaction_date,
        ':amount' => $amount,
        ':payee_name' => $payee_name,
        ':description' => $description,
        ':payment_mode' => $payment_mode,
        ':authorized_by' => $authorized_by,
        ':transaction_category' => $transaction_category,
        ':updated_at' => $updated_at,
        ':id' => $id
    ]);

    $pdo->commit();

    header('Location: transactions.php?success=Transaction updated successfully');
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: transactions.php?error=Error updating transaction: ' . $e->getMessage());
    exit();
}
