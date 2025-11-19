<?php
session_start();
include_once 'connectdb.php'; // Your PDO database connection file

if (isset($_POST['invoice_id']) && isset($_POST['member_id'])) {
    $invoice_id = $_POST['invoice_id'];
    $member_id = $_POST['member_id'];

    // Query to fetch payment details for the specific downline member
    $query = "
        SELECT
            invoice_id,
            customer_name,
            productname,
            net_amount,
            payment_mode,
            payamount,
            cheque_number,
            bank_name,
            cheque_date,
            utr_number,
            due_amount,
            created_date
        FROM receiveallpayment
        WHERE invoice_id = :invoice_id AND member_id = :member_id
        ORDER BY created_date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'invoice_id' => $invoice_id,
        'member_id' => $member_id
    ]);
    $payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build the payment details table
    $output = "<table class='table table-bordered mt-2'>";
    $output .= "<thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Customer Name</th>
                        <th>Product Name</th>
                        <th>Net Amount</th>
                        <th>Payment Mode</th>
                        <th>Pay Amount</th>
                        <th>Created Date</th>
                        <th>Cheque Number</th>
                        <th>Bank Name</th>
                        <th>Cheque Date</th>
                        <th>UTR Number</th>
                        <th>Due Amount</th>
                    </tr>
                </thead>
                <tbody>";

    if (!empty($payment_data)) {
        foreach ($payment_data as $row) {
            $output .= "<tr>";
            $output .= "<td>" . ($row['invoice_id'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['customer_name'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['productname'] ?: 'N/A') . "</td>";
            $output .= "<td>₹" . number_format(floatval($row['net_amount']) ?: 0, 2) . "</td>";
            $output .= "<td>" . ($row['payment_mode'] ?: 'N/A') . "</td>";
            $output .= "<td>₹" . number_format(floatval($row['payamount']) ?: 0, 2) . "</td>";
            $output .= "<td>" . ($row['created_date'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['cheque_number'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['bank_name'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['cheque_date'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['utr_number'] ?: 'N/A') . "</td>";
            $output .= "<td>₹" . number_format(floatval($row['due_amount']) ?: 0, 2) . "</td>";
            $output .= "</tr>";
        }
    } else {
        $output .= "<tr><td colspan='12'>No payment records found for this invoice.</td></tr>";
    }

    $output .= "</tbody></table>";
    echo $output;
}
