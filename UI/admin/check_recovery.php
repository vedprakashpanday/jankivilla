<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

if(isset($_POST['member_id'])){

    $member_id = $_POST['member_id'];

    $sql = "
        SELECT rem_due
        FROM calc_salary
        WHERE staff_id = :staff_id
        ORDER BY id DESC
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':staff_id' => $member_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row && (float)$row['rem_due'] > 0 ){
        echo json_encode([
            'has_due' => 1
        ]);
    } else {
        echo json_encode([
            'has_due' => 0
        ]);
    }
}
