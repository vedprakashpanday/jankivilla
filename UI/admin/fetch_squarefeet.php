<?php
include_once 'connectdb.php';
header('Content-Type: application/json');

if (isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];

    $sql = "SELECT DISTINCT Squarefeet FROM products WHERE ProductName = ? AND status = 'available'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_name]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} else {
    echo json_encode([]);
}
