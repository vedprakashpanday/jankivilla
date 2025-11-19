<?php
include_once 'connectdb.php';

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Search by both ID and Name
    $stmt = $pdo->prepare("
        SELECT mem_sid, m_name, m_num, m_email 
        FROM tbl_regist 
        WHERE mem_sid LIKE ? OR m_name LIKE ? 
        ORDER BY mem_sid 
        LIMIT 10
    ");

    $searchTerm = "%{$search}%";
    $stmt->execute([$searchTerm, $searchTerm]);

    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($members);
}
