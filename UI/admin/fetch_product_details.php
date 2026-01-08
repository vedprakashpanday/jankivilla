<?php
include_once 'connectdb.php';

header('Content-Type: application/json');

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $sql = "SELECT Squarefeet, Points, dimension, Quantity,ne_corner_charge,east_facing_charge,mainroad_corner_charge FROM products WHERE ProductName = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $productDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($productDetails);
} else {
    echo json_encode([]);
}
