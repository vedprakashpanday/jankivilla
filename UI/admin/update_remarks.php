<?php
include_once "connectdb.php";

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['remarks'])) {
        throw new Exception('Record ID and remarks are required');
    }

    $recordId = $_POST['id'];
    $remarks = $_POST['remarks'];

    // Update remarks in the receiveallpayment table
    $query = "UPDATE receiveallpayment SET remarks = :remarks WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR);
    $stmt->bindParam(':id', $recordId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Remarks updated successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
