<?php
include_once 'connectdb.php';


// Get POST data
$customer_id = $_POST['customer_id'] ?? '';
$customer_name = $_POST['customer_name'] ?? '';
$invoice_id = $_POST['invoice_id'] ?? '';
$member_id = $_POST['member_id'] ?? '';

// Fetch consolidated invoice data for the invoice_id
$stmt = $pdo->prepare("
    SELECT 
        r.member_id, 
        r.invoice_id, 
        r.net_amount, 
        SUM(r.payamount) as payamount, 
        MAX(r.created_date) as created_date, 
        t.m_name
    FROM receiveallpayment r
    LEFT JOIN tbl_regist t ON r.member_id = t.mem_sid
    WHERE r.member_id = :member_id
    AND r.invoice_id = :invoice_id
    GROUP BY r.invoice_id, r.member_id, r.net_amount, r.productname
    ORDER BY r.created_date DESC
");
$stmt->execute([
    'member_id' => $member_id,
    'invoice_id' => $invoice_id
]);
$payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output sub-table
if (!empty($payment_data)) {
    echo '<table class="table table-bordered table-striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Sr. No.</th>';
    echo '<th>Invoice ID</th>';
    echo '<th>Member ID</th>';
    echo '<th>Net Amount</th>';
    echo '<th>Paid Amount</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($payment_data as $index => $payment) {
        echo '<tr>';
        echo '<td>' . ($index + 1) . '</td>';
        echo '<td><span class="invoice-link" data-invoice-id="' . htmlspecialchars($payment['invoice_id']) . '">' . htmlspecialchars($payment['invoice_id']) . '</span></td>';
        echo '<td>' . htmlspecialchars($payment['member_id']) . '</td>';
        echo '<td>' . number_format($payment['net_amount'], 2) . '</td>';
        echo '<td>' . number_format($payment['payamount'], 2) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No invoices found for customer: ' . htmlspecialchars($customer_name) . '</p>';
}
