<?php
include_once 'connectdb.php';

header('Content-Type: application/json');

if (isset($_GET['product_type_id'])) {
    $product_type_id = $_GET['product_type_id'];

    $sql = "SELECT * FROM products WHERE product_type_id = ? and status = 'available'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_type_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} else {
    echo json_encode([]);
}
