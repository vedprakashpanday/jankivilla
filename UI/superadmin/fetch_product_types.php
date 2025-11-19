<?php
include_once 'connectdb.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM producttype";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productTypes);
