<?php
include_once 'connectdb.php';

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Search by both ID and Name
    $stmt = $pdo->prepare("
        SELECT m_name, mem_sid
        FROM tbl_regist 
        WHERE mem_sid LIKE  ?
      
    ");

    $searchTerm = "%{$search}%";
    $stmt->execute([$searchTerm]);

    $members = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($members);
}

 