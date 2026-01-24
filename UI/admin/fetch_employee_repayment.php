<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// header('Content-Type: application/json');
session_start();
include_once "connectdb.php";

$sql = "SELECT * FROM employee_repayment ORDER BY repayment_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$data = [];
$i = 1;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // 🔹 Action Button HTML
    $action = '
    <a href="indv_salary.php?id='.$row['id'].'" class="btn btn-sm btn-success text-decoration-none fw-bold ">Print</a>
        <form method="post" class="d-inline"
              onsubmit="return confirm(\'Delete this record?\');">
            <input type="hidden" name="delete_id" value="'.$row['id'].'">
            <button type="submit" class="btn btn-sm btn-danger">
                Delete
            </button>
        </form>
    ';

    $data[] = [
        $action,                     // 👈 Action column FIRST
        $i++,
        $row['employee_name'],
        $row['employee_id'],
        number_format($row['total_due'], 2),
        $row['on_date_due'],
        number_format($row['repayment_amount'], 2),
        $row['repayment_date']?? '-',
        $row['repayment_status'],
        $row['extend_date'] ?? '-'
    ];
}

echo json_encode(['data' => $data]);
