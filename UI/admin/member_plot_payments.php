<?php
// include_once 'connectdb.php';

// if (isset($_POST['member_id'])) {
//     $member_id = $_POST['member_id'];

//     // Fetch all invoices for this member
//     $invoiceQuery = "
//         SELECT invoice_id, productname, net_amount 
//         FROM tbl_customeramount 
//         WHERE member_id = :member_id
//     ";
//     $stmtInvoice = $pdo->prepare($invoiceQuery);
//     $stmtInvoice->execute(['member_id' => $member_id]);
//     $invoices = $stmtInvoice->fetchAll(PDO::FETCH_ASSOC);

//     if (!$invoices) {
//         echo "<p>No invoices found for Member ID: $member_id</p>";
//         exit;
//     }

//     foreach ($invoices as $inv) {
//         $invoice_id = $inv['invoice_id'];
//         $netAmount  = $inv['net_amount'];

//         // Fetch payments for this invoice
//         $query = "
//             SELECT
//                 rap.invoice_id,
//                 rap.customer_name,
//                 rap.productname,
//                 rap.net_amount,
//                 rap.payment_mode,
//                 rap.payamount,
//                 rap.cheque_number,
//                 rap.bank_name,
//                 rap.cheque_date,
//                 rap.utr_number,
//                 rap.due_amount,
//                 rap.created_date
//             FROM receiveallpayment rap
//             WHERE rap.invoice_id = :invoice_id
//             ORDER BY rap.created_date DESC
//         ";
//         $stmt = $pdo->prepare($query);
//         $stmt->execute(['invoice_id' => $invoice_id]);
//         $payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         // Total paid
//         $totalPaid = array_sum(array_column($payment_data, 'payamount'));
//         $is25Percent = ($netAmount > 0 && $totalPaid >= (0.25 * $netAmount)) ? "Yes" : "No";

//         // Show invoice header
//         echo "<h4>Invoice: $invoice_id</h4>";
//         echo "<p><b>Net Amount:</b> ₹" . number_format($netAmount, 2) .
//             " | <b>Total Paid:</b> ₹" . number_format($totalPaid, 2) .
//             " | <b>25% Paid?:</b> <span style='color:" . ($is25Percent == "Yes" ? "green" : "red") . ";'>$is25Percent</span></p>";

//         // Payment table
//         echo "<div class='payment-details-container' style='max-width: 70vw; overflow-x: auto; margin-bottom:20px;'>";
//         echo "<table class='table table-bordered nowrap' style='min-width: 1300px;' id='otrcustomerTable'>";
//         echo "<thead>
//                 <tr>
//                     <th>Invoice ID</th>
//                     <th>Customer Name</th>
//                     <th>Product Name</th>
//                     <th>Net Amount</th>
//                     <th>Payment Mode</th>
//                     <th>Pay Amount</th>
//                      <th>Created Date</th>
//                     <th>Cheque Number</th>
//                     <th>Bank Name</th>
//                     <th>Cheque Date</th>
//                     <th>UTR Number</th>
//                     <th>Due Amount</th>

//                 </tr>
//               </thead>
//               <tbody>";

//         if (!empty($payment_data)) {
//             $totalPayAmount = 0;

//             foreach ($payment_data as $row) {
//                 $payAmt = floatval($row['payamount']) ?: 0;
//                 $totalPayAmount += $payAmt;

//                 echo "<tr>";
//                 echo "<td>{$row['invoice_id']}</td>";
//                 echo "<td>{$row['customer_name']}</td>";
//                 echo "<td>{$row['productname']}</td>";
//                 echo "<td>₹" . number_format(floatval($row['net_amount']) ?: 0, 2) . "</td>";
//                 echo "<td>{$row['payment_mode']}</td>";
//                 echo "<td>₹" . number_format($payAmt, 2) . "</td>";
//                 echo "<td>" . ($row['created_date'] ? date('d-m-Y', strtotime($row['created_date'])) : 'N/A') . "</td>";
//                 echo "<td>{$row['cheque_number']}</td>";
//                 echo "<td>{$row['bank_name']}</td>";
//                 echo "<td>{$row['cheque_date']}</td>";
//                 echo "<td>{$row['utr_number']}</td>";
//                 echo "<td>₹" . number_format(floatval($row['due_amount']) ?: 0, 2) . "</td>";

//                 echo "</tr>";
//             }


//             echo "<tr style='font-weight:bold; background:#f9f9f9;'>
//             <td colspan='5' align='right'>Total Pay Amount:</td>
//             <td>₹" . number_format($totalPayAmount, 2) . "</td>
//             <td colspan='6'></td>
//           </tr>";
//         } else {
//             echo "<tr><td colspan='12'>No payment records found for this invoice.</td></tr>";
//         }

//         echo "</tbody></table></div>";
//     }
// }


include_once 'connectdb.php';

if (isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];

    // Fetch all invoices for this member
    $invoiceQuery = "
        SELECT invoice_id, productname, net_amount 
        FROM tbl_customeramount 
        WHERE member_id = :member_id
    ";
    $stmtInvoice = $pdo->prepare($invoiceQuery);
    $stmtInvoice->execute(['member_id' => $member_id]);
    $invoices = $stmtInvoice->fetchAll(PDO::FETCH_ASSOC);

    if (!$invoices) {
        echo "<p>No invoices found for Member ID: $member_id</p>";
        exit;
    }

    foreach ($invoices as $inv) {
        $invoice_id = $inv['invoice_id'];
        $netAmount  = $inv['net_amount'];
        $twentyFivePercent = $netAmount * 0.25; // Calculate 25% threshold

        // Fetch payments for this invoice ORDERED BY created_date ASC to track cumulative progress
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
                rap.created_date
            FROM receiveallpayment rap
            WHERE rap.invoice_id = :invoice_id
            ORDER BY rap.created_date ASC
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['invoice_id' => $invoice_id]);
        $payment_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate cumulative payments and find 25% milestone
        $cumulativeAmount = 0;
        $milestoneRowIndex = -1;
        $milestoneDate = null;
        $milestoneAmount = 0;

        foreach ($payment_data as $index => $row) {
            $payAmt = floatval($row['payamount']) ?: 0;
            $cumulativeAmount += $payAmt;

            // Check if this payment crossed the 25% threshold
            if ($milestoneRowIndex == -1 && $cumulativeAmount >= $twentyFivePercent) {
                $milestoneRowIndex = $index;
                $milestoneDate = $row['created_date'];
                $milestoneAmount = $cumulativeAmount;
            }
        }

        // Total paid
        $totalPaid = $cumulativeAmount;
        $is25Percent = ($netAmount > 0 && $totalPaid >= $twentyFivePercent) ? "Yes" : "No";

        // Show invoice header with milestone information
        echo "<h4>Invoice: $invoice_id</h4>";
        echo "<p><b>Net Amount:</b> ₹" . number_format($netAmount, 2) .
            " | <b>25% Target:</b> ₹" . number_format($twentyFivePercent, 2) .
            " | <b>Total Paid:</b> ₹" . number_format($totalPaid, 2) .
            " | <b>25% Achieved?:</b> <span style='color:" . ($is25Percent == "Yes" ? "green" : "red") . ";font-weight:bold;'>$is25Percent</span>";

        // Show milestone date if achieved
        if ($is25Percent == "Yes" && $milestoneDate) {
            echo " | <b>25% Completed On:</b> <span style='color:green; font-weight:bold;'>" .
                date('d-m-Y', strtotime($milestoneDate)) . "</span>";
        }
        echo "</p>";

        // Payment table
        echo "<div class='payment-details-container' style='max-width: 70vw; overflow-x: auto; margin-bottom:20px;'>";
        echo "<table class='table table-bordered nowrap' style='min-width: 1400px;' id='otrcustomerTable'>";
        echo "<thead>
                <tr>
                    
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Net Amount</th>
                    <th>Pay Amount</th>
                    <th>Cumulative Paid</th>
                    <th>Created Date</th>
                    <th>25% Status</th>
                    <th>Payment Mode</th>
                    <th>Cheque Number</th>
                    <th>Bank Name</th>
                    <th>Cheque Date</th>
                    <th>UTR Number</th>
                    <th>Due Amount</th>
                </tr>
              </thead>
              <tbody>";

        if (!empty($payment_data)) {
            $cumulativeForDisplay = 0;

            foreach ($payment_data as $index => $row) {
                $payAmt = floatval($row['payamount']) ?: 0;
                $cumulativeForDisplay += $payAmt;

                // Determine if this is the milestone row
                $isMilestoneRow = ($index == $milestoneRowIndex);
                $rowClass = $isMilestoneRow ? "style='background-color: #d4edda; border: 2px solid #28a745; font-weight: bold;'" : "";

                // Calculate percentage achieved at this point
                $percentageAchieved = ($netAmount > 0) ? ($cumulativeForDisplay / $netAmount) * 100 : 0;

                echo "<tr $rowClass>";

                echo "<td>{$row['customer_name']}</td>";
                echo "<td>{$row['productname']}</td>";
                echo "<td>₹" . number_format(floatval($row['net_amount']) ?: 0, 2) . "</td>";
                echo "<td>₹" . number_format($payAmt, 2) . "</td>";
                echo "<td><strong>₹" . number_format($cumulativeForDisplay, 2) . "</strong></td>";
                echo "<td>" . ($row['created_date'] ? date('d-m-Y', strtotime($row['created_date'])) : 'N/A') . "</td>";

                // 25% Status column with visual indicators
                if ($isMilestoneRow) {
                    echo "<td><span style='color: #28a745; font-weight: bold; background: #d4edda; padding: 2px 6px; border-radius: 3px;'>
                          🎉 25% ACHIEVED!</span><br><small>(" . number_format($percentageAchieved, 1) . "%)</small></td>";
                } elseif ($cumulativeForDisplay >= $twentyFivePercent) {
                    echo "<td><span style='color: green; font-weight: bold;'>✓ Above 25%</span><br><small>(" . number_format($percentageAchieved, 1) . "%)</small></td>";
                } elseif ($percentageAchieved >= 20) {
                    echo "<td><span style='color: orange; font-weight: bold;'>⚡ Near 25%</span><br><small>(" . number_format($percentageAchieved, 1) . "%)</small></td>";
                } else {
                    echo "<td><span style='color: #dc3545;'>Below 25%</span><br><small>(" . number_format($percentageAchieved, 1) . "%)</small></td>";
                }
                echo "<td>{$row['payment_mode']}</td>";
                echo "<td>{$row['cheque_number']}</td>";
                echo "<td>{$row['bank_name']}</td>";
                echo "<td>{$row['cheque_date']}</td>";
                echo "<td>{$row['utr_number']}</td>";
                echo "<td>₹" . number_format(floatval($row['due_amount']) ?: 0, 2) . "</td>";
                echo "</tr>";
            }

            // Total row
            echo "<tr style='font-weight:bold; background:#f9f9f9;'>
                    <td colspan='6' align='right'>Total Pay Amount:</td>
                    <td>₹" . number_format($totalPaid, 2) . "</td>
                    <td colspan='7'></td>
                  </tr>";

            // Summary row showing 25% achievement details
            if ($is25Percent == "Yes" && $milestoneDate) {
                echo "<tr style='background-color: #e8f5e8; border: 1px solid #28a745;'>
                        <td colspan='14' style='text-align: center; font-weight: bold; color: #28a745;'>
                            🏆 25% Milestone Achieved on " . date('d-m-Y', strtotime($milestoneDate)) .
                    " with cumulative payment of ₹" . number_format($milestoneAmount, 2) .
                    " (" . number_format(($milestoneAmount / $netAmount) * 100, 1) . "% of total amount)
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='14'>No payment records found for this invoice.</td></tr>";
        }

        echo "</tbody></table></div>";
    }
}
