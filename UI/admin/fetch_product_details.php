<?php
include_once 'connectdb.php';

header('Content-Type: application/json');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $sql = "SELECT Squarefeet, Points, dimension, Quantity FROM products WHERE ProductName = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $productDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($productDetails);
} else {
    echo json_encode([]);
}
