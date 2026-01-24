<?php
session_start();
include_once "connectdb.php";

$fromDate = $_POST['from_date'] ?? '';
$toDate   = $_POST['to_date'] ?? '';
$telecaller = $_SESSION['sponsor_id'] ?? '';

if (!$fromDate || !$toDate) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT 
        cust_name,
        assigned_telecaller,
        mobile,
        alternate_no,
        address,
        date,
        interested_for,
        status,
        followup_date,
        remark
    FROM interested_customer
    WHERE followup_date BETWEEN :fromDate AND :toDate
    and assigned_telecaller = :telecaller
    ORDER BY followup_date ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':fromDate' => $fromDate,
    ':toDate'   => $toDate,
    ':telecaller' => $telecaller
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
