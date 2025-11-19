<?php
header('Content-Type: application/json');

// Include database connection
include_once 'connectdb.php'; // Ensure this file sets up $pdo correctly

if (isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT customer_name, customer_mobile, customer_email, aadhar_number, 
                   pan_number, nominee_name, nominee_aadhar, address, state, district
            FROM customer_details 
            WHERE customer_id = :customer_id
        ");
        $stmt->execute([':customer_id' => $customer_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            echo json_encode(array_merge(['success' => true], $customer));
        } else {
            echo json_encode(['success' => false, 'message' => 'Customer not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No customer ID provided']);
}
exit; // Ensure no extra output
