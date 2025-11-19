<?php
try {
    include_once 'connectdb.php';

    // Get POST data
    $ids = isset($_POST['ids']) ? (is_array($_POST['ids']) ? $_POST['ids'] : explode(',', $_POST['ids'])) : [];
    $payment_status = $_POST['payment_status'] ?? null;
    $payment_mode = $_POST['payment_mode'] ?? null;
    $payment_date = $_POST['payment_date'] ?? null;
    $cheque_number = $_POST['cheque_number'] ?? null;
    $bank_name = $_POST['bank_name'] ?? null;
    $cheque_date = $_POST['cheque_date'] ?? null;
    $utr_number = $_POST['utr_number'] ?? null;
    $remarks = $_POST['remarks'] ?? null;

    if (empty($ids)) {
        throw new Exception("No IDs provided");
    }

    // Prepare and execute update query
    $stmt = $pdo->prepare("
        UPDATE commission_history 
        SET 
            payment_status = :payment_status,
            payment_mode = :payment_mode,
            payment_date = :payment_date,
            cheque_number = :cheque_number,
            bank_name = :bank_name,
            cheque_date = :cheque_date,
            utr_number = :utr_number,
            remarks = :remarks
        WHERE id = :id
    ");

    foreach ($ids as $id) {
        $stmt->execute([
            ':payment_status' => $payment_status,
            ':payment_mode' => $payment_mode,
            ':payment_date' => $payment_date,
            ':cheque_number' => $cheque_number,
            ':bank_name' => $bank_name,
            ':cheque_date' => $cheque_date,
            ':utr_number' => $utr_number,
            ':remarks' => $remarks,
            ':id' => $id
        ]);
    }

    echo "Success";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
