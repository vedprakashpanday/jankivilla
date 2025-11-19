<?php
// Include database connection
include_once 'connectdb.php';

if (isset($_POST['search'])) {
    $search = $_POST['search'];

    try {
        // Search by Customer ID, Name, or Mobile Number
        $stmt = $pdo->prepare("
            SELECT customer_id, pass_book_no, customer_name, customer_mobile, customer_email, 
                   aadhar_number, pan_number, nominee_name, nominee_aadhar, 
                   address, state, district
            FROM customer_details 
            WHERE customer_id LIKE :search 
               OR customer_name LIKE :search
               OR pass_book_no LIKE :search 
               OR customer_mobile LIKE :search
            ORDER BY customer_id 
            LIMIT 15
        ");

        $searchTerm = "%{$search}%";
        $stmt->execute([':search' => $searchTerm]);

        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($customers);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No search term provided']);
}
exit;
