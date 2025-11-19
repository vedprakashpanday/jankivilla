<?php
include 'connectdb.php';

// JSON response
header('Content-Type: application/json');

// Disable display errors (for frontend)
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $full_name = trim($_POST['full_name'] ?? '');
    $mobile_no = trim($_POST['mobile_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($full_name)) {    
        $errors[] = "Full name is required";
    }
    
    if (empty($mobile_no)) {
        $errors[] = "Mobile number is required";
    } elseif (!preg_match('/^[0-9+\-\s()]{10,15}$/', $mobile_no)) {
        $errors[] = "Please enter a valid mobile number";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    // If there are validation errors
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fix the following errors:',
            'errors' => $errors
        ]);
        exit;
    }
    
    try {
        // Prepare SQL statement
        $sql = "INSERT INTO bookings_request (full_name, mobile_no, email, address, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Execute with parameters
        $stmt->execute([$full_name, $mobile_no, $email, $address, $message]);
        
        // Check if insert was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Booking submitted successfully! Our team will contact you shortly.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error submitting booking. Please try again later.'
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    
} else {
    // Invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>