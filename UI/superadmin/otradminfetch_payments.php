<?php
include_once 'connectdb.php'; // Your PDO database connection file

if (isset($_POST['invoice_id'])) {
    $member_id = $_POST['member_id'];
    $invoice_id = $_POST['invoice_id'];
    $from_date = $_POST['from_date'] ?? '2000-01-01';
    $to_date = $_POST['to_date'] ?? '2099-12-31';

    // Query to fetch payment details for the selected member
    $query = "
        SELECT
            rap.invoice_id,
            rap.customer_name,
            rap.productname,
            rap.net_amount,
            rap.payment_mode,
            rap.payamount,
            rap.cheque_number,
            rap.bank_name,
            rap.cheque_date,
            rap.utr_number,
            rap.due_amount,
            rap.neft_payment,
            rap.rtgs_payment,
            rap.created_date
        FROM receiveallpayment rap
        INNER JOIN tbl_customeramount tca
            ON rap.invoice_id = tca.invoice_id
        WHERE rap.invoice_id = :invoice_id
        AND tca.producttype = 1
        AND (rap.created_date BETWEEN :from_date AND :to_date OR rap.created_date IS NULL)
        ORDER BY rap.created_date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'invoice_id' => $invoice_id,
        'from_date' => $from_date,
        'to_date' => $to_date
    ]);
    $payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build the payment details table
    $output = "<h4>Payment Details for Member ID: $invoice_id</h4>";
    $output .= "<div class='payment-details-container' style='max-width: 75vw; overflow-x: auto;'>";
    $output .= "<table class='table table-bordered nowrap' style='min-width: 1200px;' id='otrcustomerTable'>";
    $output .= "<thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Customer Name</th>
                        <th>Product Name</th>
                        <th>Net Amount</th>
                        <th>Payment Mode</th>
                        <th>Pay Amount</th>
                        <th>Cheque Number</th>
                        <th>Bank Name</th>
                        <th>Cheque Date</th>
                        <th>UTR Number</th>
                        <th>NEFT_Payment</th>
                        <th>RTGS_Payment</th>
                        <th>Due Amount</th>
                        <th>Created Date</th>
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
            $output .= "<td>" . ($row['cheque_number'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['bank_name'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['cheque_date'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['utr_number'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['neft_payment'] ?: 'N/A') . "</td>";
            $output .= "<td>" . ($row['rtgs_payment'] ?: 'N/A') . "</td>";
            $output .= "<td>₹" . number_format(floatval($row['due_amount']) ?: 0, 2) . "</td>";
            $output .= "<td>" . ($row['created_date'] ? date('d-m-Y', strtotime($row['created_date'])) : 'N/A') . "</td>";
            $output .= "</tr>";
        }
    } else {
        $output .= "<tr><td colspan='12'>No payment records found for this member.</td></tr>";
    }

    $output .= "</tbody></table></div>";
    echo $output;
}
