<?php
// Include your database connection file
include_once "connectdb.php";

// Check if from, to dates and zero option are provided
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

// Add condition for without zero option
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

// Set headers for Excel download
$filename_suffix = ($zero_option === 'with_zero') ? '_with_zero' : '_without_zero';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Commission_Report_' . $from_date . '_to_' . $to_date . $filename_suffix . '.xls"');
header('Cache-Control: max-age=0');

// Format dates for display
$from_display = date('M d Y', strtotime($from_date));
$to_display = date('M d Y', strtotime($to_date));
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <h3>Commission Close Report for <?php echo $from_display . " to " . $to_display; ?>
        (<?php echo $zero_option === 'with_zero' ? 'With Zero' : 'Without Zero'; ?>)</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Member ID</th>
                <th>Member Name</th>
                <th>Sponsor ID</th>
                <th>Self Business Amount</th>
                <th>Team Business</th>
                <th>Direct Commission %</th>
                <th>Direct Commission</th>
                <th>Level Commission</th>
                <th>Total Commission</th>
                <th>TDS (5%)</th>
                <th>Admin Charge (5%)</th>
                <th>Final Amount (After TDS & Admin)</th>
                <th>Current Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commission_data as $data):
                // Calculate TDS (5% of Total Commission)
                $tds = $data['total_commission'] * 0.05;
                // Calculate Admin Charge (5% of Total Commission)
                $admin_charge = $data['total_commission'] * 0.05;
                // Calculate Final Amount
                $final_amount = $data['total_commission'] - ($tds + $admin_charge);
                // Safely get payment_status, default to 'unpaid' if not set
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
</body>

</html>