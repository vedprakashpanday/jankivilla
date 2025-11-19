<?php
// emi_payment_details.php - EMI Details based on First Payment Date Logic

include_once 'connectdb.php';

if (isset($_POST['member_id']) && isset($_POST['product_name'])) {
    $member_id = $_POST['member_id'];
    $product_name = $_POST['product_name'];

    // Get product and customer information
    $productInfoQuery = "
        SELECT 
            c.invoice_id,
            c.net_amount,
            c.producttype,
            c.customer_name,
            r.m_name,
            p.emi_month
        FROM tbl_customeramount c
        LEFT JOIN tbl_regist r ON c.member_id = r.mem_sid
        LEFT JOIN products p ON c.productname = p.ProductName AND c.producttype = p.product_type_id
        WHERE c.member_id = :member_id AND c.productname = :product_name
        LIMIT 1
    ";

    $productStmt = $pdo->prepare($productInfoQuery);
    $productStmt->execute(['member_id' => $member_id, 'product_name' => $product_name]);
    $productInfo = $productStmt->fetch(PDO::FETCH_ASSOC);

    if (!$productInfo) {
        echo "<div class='alert alert-warning'>No information found for Member ID: $member_id, Product: $product_name</div>";
        exit;
    }

    $invoice_id = $productInfo['invoice_id'];
    $net_amount = $productInfo['net_amount'];
    $emi_months = $productInfo['emi_month'];
    $customer_name = $productInfo['m_name'];
    $associate_customer_name = $productInfo['customer_name'];

    // Get all payments ordered by date
    $paymentQuery = "
        SELECT
            rap.payamount,
            rap.created_date,
            rap.payment_mode,
            rap.cheque_number,
            rap.bank_name,
            rap.utr_number,
            rap.due_amount
        FROM receiveallpayment rap
        WHERE rap.member_id = :member_id 
            AND rap.productname = :product_name
        ORDER BY rap.created_date ASC
    ";

    $paymentStmt = $pdo->prepare($paymentQuery);
    $paymentStmt->execute(['member_id' => $member_id, 'product_name' => $product_name]);
    $payment_data = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($payment_data)) {
        echo "<div class='alert alert-info'>
                <h5>🏦 No Payments Found</h5>
                <p><strong>Customer:</strong> $customer_name</p>
                <p><strong>Product:</strong> $product_name</p>
                <p><strong>Total Amount:</strong> ₹" . number_format($net_amount, 2) . "</p>
                <p><strong>EMI Duration:</strong> $emi_months months</p>
                <p class='text-danger'><strong>Status:</strong> Payment not started</p>
              </div>";
        exit;
    }

    // Calculate EMI details from first payment
    $first_payment_date = $payment_data[0]['created_date'];
    $total_paid = array_sum(array_column($payment_data, 'payamount'));
    $remaining_amount = $net_amount - $total_paid;
    $monthly_emi = ($emi_months > 0) ? round($remaining_amount / $emi_months, 2) : 0;
    $payment_day = date('j', strtotime($first_payment_date));

    // Create EMI schedule based on first payment date
    $emi_schedule = [];
    $current_date = new DateTime($first_payment_date);

    for ($i = 0; $i < $emi_months; $i++) {
        if ($i > 0) {
            $current_date->add(new DateInterval('P1M')); // Add 1 month
        }
        $emi_schedule[] = [
            'due_date' => $current_date->format('Y-m-d'),
            'emi_number' => $i + 1,
            'expected_amount' => $monthly_emi
        ];
    }

    // Match payments to EMI schedule
    foreach ($emi_schedule as &$emi) {
        $emi['payments'] = [];
        $emi['total_paid'] = 0;
        $emi['status'] = 'Pending';
        $emi['status_color'] = 'orange';
        $emi['payment_date'] = null;

        // Find payments for this EMI period
        $due_date = new DateTime($emi['due_date']);
        $next_due = clone $due_date;
        $next_due->add(new DateInterval('P1M'));

        foreach ($payment_data as $payment) {
            $payment_date = new DateTime($payment['created_date']);

            // Check if payment falls in this EMI period
            if ($payment_date >= $due_date && $payment_date < $next_due) {
                $emi['payments'][] = $payment;
                $emi['total_paid'] += $payment['payamount'];
                if (!$emi['payment_date']) {
                    $emi['payment_date'] = $payment['created_date'];
                }
            }
        }

        // Determine EMI status
        if ($emi['total_paid'] > 0) {
            $first_payment_in_cycle = new DateTime($emi['payment_date']);

            if ($emi['total_paid'] >= $emi['expected_amount']) {
                // Sufficient amount paid
                if ($first_payment_in_cycle <= $due_date) {
                    $emi['status'] = 'Paid On Time';
                    $emi['status_color'] = 'green';
                } else {
                    $emi['status'] = 'Paid Late';
                    $emi['status_color'] = 'red';
                }
            } else {
                // Insufficient amount
                $emi['status'] = 'Insufficient Amount';
                $emi['status_color'] = 'red';
            }
        } else {
            // No payment made
            $today = new DateTime();
            if ($today > $due_date) {
                $emi['status'] = 'Overdue';
                $emi['status_color'] = 'red';
            } else {
                $emi['status'] = 'Pending';
                $emi['status_color'] = 'orange';
            }
        }
    }

    // Display summary header
    echo "<div class='emi-summary' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
    echo "<h4 style='margin: 0 0 15px 0;'>📊 EMI Payment Analysis - $product_name</h4>";
    echo "<div class='row'>";
    echo "<div class='col-md-2'><strong>Customer:</strong><br>$associate_customer_name</div>";
    echo "<div class='col-md-2'><strong>Member ID:</strong><br>$member_id</div>";
    echo "<div class='col-md-2'><strong>Total Amount:</strong><br>₹" . number_format($net_amount, 2) . "</div>";
    echo "<div class='col-md-2'><strong>EMI Duration:</strong><br>$emi_months months</div>";
    echo "<div class='col-md-2'><strong>Monthly EMI:</strong><br>₹" . number_format($monthly_emi, 2) . "</div>";
    echo "<div class='col-md-2'><strong>EMI Started:</strong><br>" . date('d-M-Y', strtotime($first_payment_date)) . "</div>";
    echo "</div>";
    echo "<div class='row' style='margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 15px;'>";
    echo "<div class='col-md-3'><strong>Total Paid:</strong><br>₹" . number_format($total_paid, 2) . "</div>";
    echo "<div class='col-md-3'><strong>Remaining:</strong><br>₹" . number_format($remaining_amount, 2) . "</div>";
    echo "<div class='col-md-3'><strong>Completion:</strong><br>" . number_format(($total_paid / $net_amount) * 100, 1) . "%</div>";
    echo "<div class='col-md-3'><strong>Payment Day:</strong><br>" . $payment_day . " of each month</div>";
    echo "</div>";
    echo "</div>";

    // EMI Schedule Table
    echo "<h5>📅 EMI Payment Schedule & Status</h5>";
    echo "<div class='table-responsive' style='border: 2px solid #dee2e6; border-radius: 8px;'>";
    echo "<table class='table table-hover mb-0'>";
    echo "<thead style='background: linear-gradient(135deg, #28a745, #20c997); color: white;'>
            <tr>
                <th>EMI #</th>
                <th>Due Date</th>
                <th>Expected Amount</th>
                <th>Actual Paid</th>
                <th>Payment Date</th>
                <th>Days Late</th>
                <th>Status</th>
                <th>Excess/Shortfall</th>
            </tr>
          </thead>
          <tbody>";

    $completed_emis = 0;
    $total_excess = 0;

    foreach ($emi_schedule as $emi) {
        $due_date_obj = new DateTime($emi['due_date']);
        $payment_date_obj = $emi['payment_date'] ? new DateTime($emi['payment_date']) : null;
        $days_late = 0;

        if ($payment_date_obj && $payment_date_obj > $due_date_obj) {
            $days_late = $payment_date_obj->diff($due_date_obj)->days;
        }

        $excess_shortfall = $emi['total_paid'] - $emi['expected_amount'];
        $total_excess += $excess_shortfall;

        if ($emi['status_color'] == 'green') {
            $completed_emis++;
        }

        $row_class = '';
        switch ($emi['status_color']) {
            case 'green':
                $row_class = 'table-success';
                break;
            case 'red':
                $row_class = 'table-danger';
                break;
            case 'orange':
                $row_class = 'table-warning';
                break;
        }

        echo "<tr class='$row_class'>";
        echo "<td><strong>EMI {$emi['emi_number']}</strong></td>";
        echo "<td><strong>" . date('d-M-Y', strtotime($emi['due_date'])) . "</strong></td>";
        echo "<td>₹" . number_format($emi['expected_amount'], 2) . "</td>";
        echo "<td><strong>₹" . number_format($emi['total_paid'], 2) . "</strong></td>";
        echo "<td>" . ($emi['payment_date'] ? date('d-M-Y', strtotime($emi['payment_date'])) : '-') . "</td>";
        echo "<td>" . ($days_late > 0 ? "<span class='text-danger'>$days_late days</span>" : '-') . "</td>";

        $status_icons = [
            'Paid On Time' => '✅',
            'Paid Late' => '⚠️',
            'Insufficient Amount' => '❌',
            'Overdue' => '🚫',
            'Pending' => '⏳'
        ];
        $icon = $status_icons[$emi['status']] ?? '❓';

        echo "<td><strong>$icon {$emi['status']}</strong></td>";

        if ($excess_shortfall > 0) {
            echo "<td><span class='text-success'>+₹" . number_format($excess_shortfall, 2) . "</span></td>";
        } elseif ($excess_shortfall < 0) {
            echo "<td><span class='text-danger'>-₹" . number_format(abs($excess_shortfall), 2) . "</span></td>";
        } else {
            echo "<td><span class='text-muted'>Exact</span></td>";
        }

        echo "</tr>";
    }

    echo "</tbody></table></div>";

    // Next EMI notification
    $today = new DateTime();
    $next_emi = null;

    foreach ($emi_schedule as $emi) {
        if ($emi['status'] == 'Pending' || $emi['status'] == 'Overdue') {
            $next_emi = $emi;
            break;
        }
    }

    if ($next_emi) {
        $next_due_date = new DateTime($next_emi['due_date']);
        $days_until_due = $today->diff($next_due_date)->days;
        $is_overdue = $today > $next_due_date;

        $alert_class = $is_overdue ? 'alert-danger' : ($days_until_due <= 5 ? 'alert-warning' : 'alert-info');
        $alert_icon = $is_overdue ? '🚨' : ($days_until_due <= 5 ? '⚠️' : '📅');

        echo "<div class='alert $alert_class' style='margin-top: 20px; border: 2px solid;'>
                <h5>$alert_icon Next EMI Information</h5>";

        if ($is_overdue) {
            echo "<p><strong>OVERDUE EMI:</strong> EMI #{$next_emi['emi_number']} was due on " . date('d-M-Y', strtotime($next_emi['due_date'])) . "</p>";
            echo "<p><strong>Amount Due:</strong> ₹" . number_format($next_emi['expected_amount'], 2) . "</p>";
            echo "<p><strong>Days Overdue:</strong> $days_until_due days</p>";
        } else {
            echo "<p><strong>Next EMI:</strong> EMI #{$next_emi['emi_number']} due on " . date('d-M-Y', strtotime($next_emi['due_date'])) . "</p>";
            echo "<p><strong>Amount:</strong> ₹" . number_format($next_emi['expected_amount'], 2) . "</p>";
            echo "<p><strong>Days Remaining:</strong> $days_until_due days</p>";
        }
        echo "</div>";
    } else {
        echo "<div class='alert alert-success' style='margin-top: 20px;'>
                <h5>🎉 All EMIs Completed!</h5>
                <p>Congratulations! All scheduled EMI payments have been made.</p>
              </div>";
    }

    // Summary statistics
    echo "<div class='row' style='margin-top: 20px;'>";
    echo "<div class='col-md-3'>
            <div class='card text-center' style='border: 2px solid #28a745;'>
                <div class='card-body'>
                    <h4 class='text-success'>$completed_emis</h4>
                    <p>EMIs Completed</p>
                </div>
            </div>
          </div>";
    echo "<div class='col-md-3'>
            <div class='card text-center' style='border: 2px solid #dc3545;'>
                <div class='card-body'>
                    <h4 class='text-danger'>" . ($emi_months - $completed_emis) . "</h4>
                    <p>EMIs Pending</p>
                </div>
            </div>
          </div>";
    echo "<div class='col-md-3'>
            <div class='card text-center' style='border: 2px solid #007bff;'>
                <div class='card-body'>
                    <h4 class='text-primary'>" . number_format(($completed_emis / $emi_months) * 100, 1) . "%</h4>
                    <p>EMI Progress</p>
                </div>
            </div>
          </div>";
    echo "<div class='col-md-3'>
            <div class='card text-center' style='border: 2px solid " . ($total_excess >= 0 ? '#28a745' : '#dc3545') . ";'>
                <div class='card-body'>
                    <h4 class='" . ($total_excess >= 0 ? 'text-success' : 'text-danger') . "'>₹" . number_format(abs($total_excess), 2) . "</h4>
                    <p>" . ($total_excess >= 0 ? 'Total Excess' : 'Total Shortfall') . "</p>
                </div>
            </div>
          </div>";
    echo "</div>";

    // All payments transaction table
    echo "<h5 style='margin-top: 30px;'>💳 All Payment Transactions</h5>";
    echo "<div class='table-responsive' style='border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<table class='table table-striped mb-0'>";
    echo "<thead style='background-color: #6c757d; color: white;'>
            <tr>
                <th>#</th>
                <th>Payment Date</th>
                <th>Amount Paid</th>
                <th>Payment Mode</th>
                <th>Bank/Reference</th>
                <th>Cumulative Total</th>
                <th>EMI Applied To</th>
            </tr>
          </thead>
          <tbody>";

    $cumulative = 0;
    foreach ($payment_data as $index => $payment) {
        $cumulative += $payment['payamount'];

        // Find which EMI this payment belongs to
        $payment_date = new DateTime($payment['created_date']);
        $applied_to_emi = 'N/A';

        if (!empty($emi_schedule)) {
            foreach ($emi_schedule as $emi) {
                $due_date = new DateTime($emi['due_date']);
                $next_due = clone $due_date;
                $next_due->add(new DateInterval('P1M'));

                if ($payment_date >= $due_date && $payment_date < $next_due) {
                    $applied_to_emi = "EMI #{$emi['emi_number']}";
                    break;
                }
            }
        }

        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td><strong>" . date('d-M-Y', strtotime($payment['created_date'])) . "</strong></td>";
        echo "<td><strong>₹" . number_format($payment['payamount'], 2) . "</strong></td>";
        echo "<td>" . $payment['payment_mode'] . "</td>";
        echo "<td>" . ($payment['bank_name'] ?: ($payment['utr_number'] ?: ($payment['cheque_number'] ?: 'N/A'))) . "</td>";
        echo "<td>₹" . number_format($cumulative, 2) . "</td>";
        echo "<td><span class='badge badge-info'>$applied_to_emi</span></td>";
        echo "</tr>";
    }

    // Total summary row
    echo "<tr style='background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #007bff;'>
            <td colspan='2'>TOTAL PAYMENTS</td>
            <td>₹" . number_format($total_paid, 2) . "</td>
            <td colspan='2'>Remaining Amount</td>
            <td>₹" . number_format($remaining_amount, 2) . "</td>
            <td>" . count($payment_data) . " Transactions</td>
          </tr>";

    echo "</tbody></table></div>";
} else {
    echo "<div class='alert alert-danger'>Invalid request parameters. Member ID and Product Name are required.</div>";
}
