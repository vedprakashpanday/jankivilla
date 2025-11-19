<?php
// Include database connection
include_once 'connectdb.php';

// Set header for JSON response
header('Content-Type: application/json');

if (isset($_POST['search'])) {
    $search = trim($_POST['search']);

    try {
        // Search by Invoice ID or Customer Name from tbl_customeramount
        $stmt = $pdo->prepare("
            SELECT DISTINCT invoice_id, customer_name, productname as search_productname
            FROM tbl_customeramount 
            WHERE invoice_id LIKE :search 
               OR customer_name LIKE :search
            ORDER BY id DESC, invoice_id DESC
            LIMIT 20
        ");

        $searchTerm = "%{$search}%";
        $stmt->execute([':search' => $searchTerm]);

        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($invoices);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No search term provided']);
}
exit;
