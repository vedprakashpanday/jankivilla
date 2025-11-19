<?php
header('Content-Type: application/json');
include_once 'connectdb.php'; // Ensure this file sets up $pdo

if (!isset($_POST['invoice_id']) || empty($_POST['invoice_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid invoice ID']);
    exit;
}

$invoice_id = $_POST['invoice_id'];

// Query to fetch EMI schedule details
$query = "
    SELECT month_number, emi_amount, DATE_FORMAT(due_date, '%d-%m-%Y') AS due_date
    FROM emi_schedule_records
    WHERE invoice_id = :invoice_id AND month_number IS NOT NULL
    ORDER BY month_number
";

$stmt = $pdo->prepare($query);
$stmt->execute([':invoice_id' => $invoice_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format emi_amount in Indian style
foreach ($data as &$row) {
    $row['emi_amount'] = number_format($row['emi_amount'], 2, '.', ',');
}

echo json_encode(['success' => true, 'data' => $data]);
