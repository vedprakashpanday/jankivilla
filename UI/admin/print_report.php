<?php
// Include your database connection file
include_once "connectdb.php";

// Check if required parameters are provided
if (!isset($_GET['from']) || !isset($_GET['to']) || !isset($_GET['zero'])) {
    die("Required parameters not provided");
}

$from_date = $_GET['from'];
$to_date = $_GET['to'];
$zero_option = $_GET['zero'];

// Validate dates
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $from_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $to_date)) {
    die("Invalid date format");
}

// Validate zero option
if (!in_array($zero_option, ['with_zero', 'without_zero'])) {
    die("Invalid zero option");
}

// Query to get the commission data
$query = "SELECT * FROM commission_history 
          WHERE from_date = :from_date 
          AND to_date = :to_date 
          AND status = 'closed'";

if ($zero_option === 'without_zero') {
    $query .= " AND total_commission > 0";
}

$stmt = $pdo->prepare($query);
$stmt->bindParam(':from_date', $from_date);
$stmt->bindParam(':to_date', $to_date);
$stmt->execute();
$commission_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no data found
if (empty($commission_data)) {
    die("No commission data found for this period");
}

// Format dates for display
$from_display = date('M d Y', strtotime($from_date));
$to_display = date('M d Y', strtotime($to_date));
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Commission Report</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            width: 100%;
            max-width: 185mm;
            margin: 0 auto;
        }

        h3 {
            font-size: 19px;
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            padding: 11px;
            text-align: left;
            /* word-wrap: break-word; */
            overflow-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        th:nth-child(1),
        td:nth-child(1) {
            width: 8%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 14%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 8%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 9%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 9%;
        }

        th:nth-child(6),
        td:nth-child(6) {
            width: 7%;
        }

        th:nth-child(7),
        td:nth-child(7) {
            width: 9%;
        }

        th:nth-child(8),
        td:nth-child(8) {
            width: 9%;
        }

        th:nth-child(9),
        td:nth-child(9) {
            width: 9%;
        }

        th:nth-child(10),
        td:nth-child(10) {
            width: 7%;
        }

        th:nth-child(11),
        td:nth-child(11) {
            width: 7%;
        }

        th:nth-child(12),
        td:nth-child(12) {
            width: 9%;
        }

        th:nth-child(13),
        td:nth-child(13) {
            width: 8%;
        }
    </style>
</head>

<body>
    <h3>Commission Close Report for <?php echo $from_display . " to " . $to_display; ?>
        (<?php echo $zero_option === 'with_zero' ? 'With Zero' : 'Without Zero'; ?>)</h3>

    <table>
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Member Name</th>
                <th>Sponsor ID</th>
                <th>Self Business</th>
                <th>Team Business</th>
                <th>Direct %</th>
                <th>Direct Comm</th>
                <th>Level Comm</th>
                <th>Total Comm</th>
                <th>TDS (5%)</th>
                <th>Admin (5%)</th>
                <th>Final Amt</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commission_data as $data):
                $tds = $data['total_commission'] * 0.05;
                $admin_charge = $data['total_commission'] * 0.05;
                $final_amount = $data['total_commission'] - ($tds + $admin_charge);
                $current_status = isset($data['payment_status']) ? $data['payment_status'] : 'unpaid';
            ?>
                <tr>
                    <td><?php echo $data['member_id']; ?></td>
                    <td><?php echo $data['member_name']; ?></td>
                    <td><?php echo $data['sponsor_id']; ?></td>
                    <td>₹<?php echo number_format($data['direct_amount'], 2); ?></td>
                    <td>₹<?php echo number_format($data['total_group_amount'], 2); ?></td>
                    <td><?php echo $data['direct_percent']; ?>%</td>
                    <td>₹<?php echo number_format($data['direct_commission'], 2); ?></td>
                    <td>₹<?php echo number_format($data['level_commission'], 2); ?></td>
                    <td>₹<?php echo number_format($data['total_commission'], 2); ?></td>
                    <td>₹<?php echo number_format($tds, 2); ?></td>
                    <td>₹<?php echo number_format($admin_charge, 2); ?></td>
                    <td>₹<?php echo number_format($final_amount, 2); ?></td>
                    <td><?php echo ucfirst($current_status); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>