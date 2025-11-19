<?php
include_once 'connectdb.php';

// Get POST data
$invoice_id = $_POST['invoice_id'] ?? '';
$member_id = $_POST['member_id'] ?? '';

// Function to get commission percentage
function getCommissionPercent($amount)
{
    $amount = floatval($amount);
    if ($amount <= 300000) return 6;
    if ($amount <= 900000) return 7;
    if ($amount <= 2500000) return 8;
    if ($amount <= 5000000) return 9;
    if ($amount <= 12500000) return 10;
    if ($amount <= 30000000) return 11;
    if ($amount <= 75000000) return 12;
    if ($amount <= 250000000) return 13;
    if ($amount <= 750000000) return 14;
    return 15;
}

// Fetch invoice details
$stmt = $pdo->prepare("
    SELECT r.productname, r.net_amount, r.payamount, r.created_date, t.m_name, t.direct_commission_percent
    FROM receiveallpayment r
    LEFT JOIN tbl_regist t ON r.member_id = t.mem_sid
    WHERE r.member_id = :member_id
    AND r.invoice_id = :invoice_id
    ORDER BY r.created_date DESC
");
$stmt->execute(['member_id' => $member_id, 'invoice_id' => $invoice_id]);
$payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output modal content
if (!empty($payment_data)) {
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Sr. No.</th>';
    echo '<th>Product Name</th>';
    echo '<th>Net Amount</th>';
    echo '<th>Paid Amount</th>';
    echo '<th>Commission (%)</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($payment_data as $index => $payment) {
        $commission_percent = $payment['direct_commission_percent'] ?? getCommissionPercent($payment['payamount']);
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td>' . htmlspecialchars($payment['productname']) . '</td>';
        echo '<td>' . number_format($payment['net_amount'], 2) . '</td>';
        echo '<td>' . number_format($payment['payamount'], 2) . '</td>';
        echo '<td>' . number_format($commission_percent, 2) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No details found for invoice ID: ' . htmlspecialchars($invoice_id) . '</p>';
}
