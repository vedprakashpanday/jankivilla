<?php
// fetch_product_details.php
header('Content-Type: application/json');

include_once "connectdb.php";

try {
    if (!isset($_POST['productname']) || empty($_POST['productname'])) {
        throw new Exception('Product name is required');
    }

    $productname = $_POST['productname'];

    // Query to fetch all records from receiveallpayment table for the specific product
    $query = "
        SELECT 
            id,
            invoice_id,
            member_id,
            customer_id,
            customer_name,
            productname,
            rate,
            area,
            net_amount,
            payment_mode,
            payamount,
            discount_percent,
            discount_rs,
            plot_type,
            corner_charge,
            cheque_number,
            bank_name,
            cheque_date,
            utr_number,
            due_amount,
            created_date,
            close_status,
            remarks,
            bill_date
        FROM receiveallpayment 
        WHERE productname = :productname
        ORDER BY created_date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':productname', $productname, PDO::PARAM_STR);
    $stmt->execute();

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data for better presentation
    foreach ($records as &$record) {
        // Format dates
        if ($record['created_date']) {
            $record['created_date'] = date('Y-m-d H:i:s', strtotime($record['created_date']));
        }
        if ($record['bill_date']) {
            $record['bill_date'] = date('Y-m-d H:i:s', strtotime($record['bill_date']));
        }
        if ($record['cheque_date']) {
            $record['cheque_date'] = date('Y-m-d', strtotime($record['cheque_date']));
        }

        // Format amounts
        $record['net_amount'] = $record['net_amount'];
        $record['payamount'] = $record['payamount'];
        $record['due_amount'] = $record['due_amount'];
        $record['discount_rs'] = $record['discount_rs'];
    }

    echo json_encode([
        'success' => true,
        'records' => $records,
        'count' => count($records)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
