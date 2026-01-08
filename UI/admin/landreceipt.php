<?php
include_once 'connectdb.php';

// Get land owner ID from URL
$landid = $_GET['landid'] ?? null;

if (!$landid) {
    die("No ID provided");
}

 function jsonToText($json) {
    $arr = json_decode($json, true);
    return htmlspecialchars(implode(', ', is_array($arr) ? $arr : []));
}

function jsonToTextWithLabel($json)
{
    $arr = json_decode($json, true);

    if (!is_array($arr)) {
        return '';
    }

    return implode(', ', array_map(function ($value) {
        return '₹' . htmlspecialchars($value) . ' (Per Katha)';
    }, $arr));
}

// Fetch land owner data
$stmt = $pdo->prepare("SELECT * FROM land_owner_payments WHERE id = ?");
$stmt->execute([$landid]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Record not found");
}

// Fetch all transactions for this land owner
$trans_stmt = $pdo->prepare("SELECT * FROM land_payment_transactions WHERE land_owner_id = ? ORDER BY transaction_date ASC, id ASC");
$trans_stmt->execute([$landid]);
$transactions = $trans_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate running balance
$running_balance = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Owner Payment Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .invoice-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #e8f0e8;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .cin-number {
            font-size: 11px;
            margin-bottom: 8px;
        }

        .address {
            font-size: 12px;
            line-height: 1.6;
        }

        .section-title {
            background: #fff;
            border: 2px solid #000;
            padding: 8px 20px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            display: inline-block;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-title::before,
        .section-title::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 0;
            height: 0;
            border-style: solid;
        }

        .section-title::before {
            left: -20px;
            border-width: 20px 20px 20px 0;
            border-color: transparent #000 transparent transparent;
            transform: translateY(-50%);
        }

        .section-title::after {
            right: -20px;
            border-width: 20px 0 20px 20px;
            border-color: transparent transparent transparent #000;
            transform: translateY(-50%);
        }

        .form-section {
            margin: 20px 0;
        }

        .form-row {
            display: flex;
            margin-bottom: 12px;
            align-items: center;
        }

        .form-label {
            font-weight: bold;
            min-width: 150px;
            font-size: 13px;
        }

        .form-value {
            flex: 1;
            border-bottom: 1px solid #333;
            padding: 2px 5px;
            font-size: 13px;
        }

        .form-row-multi {
            display: flex;
            gap: 20px;
            margin-bottom: 12px;
        }

        .form-group {
            flex: 1;
            display: flex;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 11px;
        }

        table th {
            background: #d0d0d0;
            font-weight: bold;
        }

        table td {
            height: 30px;
        }

        .credit-amount {
            color: green;
            font-weight: bold;
        }

        .debit-amount {
            color: red;
            font-weight: bold;
        }

        .total-row {
            background: #f0f0f0;
            font-weight: bold;
        }

        .balance-row {
            background: #ffffcc;
            font-weight: bold;
        }

        .summary-box {
            margin-top: 20px;
            padding: 15px;
            background: #fff;
            border: 2px solid #000;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-size: 16px;
            font-weight: bold;
            color: #d00;
        }

        .no-print {
            text-align: center;
            margin-top: 20px;
        }

        .print-btn {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .print-btn:hover {
            background: #45a049;
        }

        .back-btn {
            background: #666;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background: #555;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-name">AMITABH BUILDERS & DEVELOPERS PVT. LTD.</div>
            <div class="cin-number">CIN No.: U43299BR2024PTC072712</div>
            <div class="address">
                Address: 1ST FLOOR, PAPPU YADAV BUILDING, SOUTH OF NH-57, KAKARGHATI,<br>
                ADARSH CHOWK, DARBHANGA (BIHAR) 846007
            </div>
        </div>

        <div class="section-title">LAND OWNER PAYMENT DETAILS</div>

        <div class="form-section">
            <div class="form-row">
                <span class="form-label">Land Owner Name</span>
                <span class="form-value"><?php echo htmlspecialchars($data['land_owner_name'] ?? ''); ?></span>
            </div>

            <div class="form-row">
                <span class="form-label">S/o, W/o, D/o</span>
                <span class="form-value"><?php echo htmlspecialchars($data['relation_name'] ?? ''); ?></span>
            </div>

            <div class="form-row">
                <span class="form-label">Address</span>
                <span class="form-value"><?php echo htmlspecialchars($data['address'] ?? ''); ?></span>
            </div>

            <div class="form-row-multi">
                <div class="form-group">
                    <span class="form-label">Mobile No. (1)</span>
                    <span class="form-value"><?php echo htmlspecialchars($data['mobile1'] ?? ''); ?></span>
                </div>
                <div class="form-group">
                    <span class="form-label">(2)</span>
                    <span class="form-value"><?php echo htmlspecialchars($data['mobile2'] ?? ''); ?></span>
                </div>
            </div>

            <div class="form-row-multi">
                <div class="form-group">
                    <span class="form-label">Mauze Name</span>
                    <span class="form-value"><?php echo htmlspecialchars($data['mauze_name'] ?? ''); ?></span>
                </div>
                <div class="form-group">
                    <span class="form-label">Thana No.</span>
                    <span class="form-value"><?php echo htmlspecialchars($data['thana_no'] ?? ''); ?></span>
                </div>
            </div>

            <div class="form-row-multi">
                <div class="form-group">
                    <span class="form-label">Kheshra No.</span>
                    <span class="form-value"><?= jsonToText($data['khesra_no']) ?></span>
                </div>
                <div class="form-group">
                    <span class="form-label">Rakwa</span>
                    <span class="form-value"><?= jsonToText($data['rakuwa']) ?></span>
                </div>
            </div>

            <div class="form-row-multi">
                <div class="form-group">
                    <span class="form-label">Rate</span>
                    <span class="form-value">
    <?= jsonToTextWithLabel($data['rate_per_katha']) ?>
</span>
                </div>
                <div class="form-group">
                    <span class="form-label">Total Land Value</span>
                    <span class="form-value">₹<?php echo number_format($data['total_land_value'], 2); ?></span>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">S.No.</th>
                    <th rowspan="2">Date</th>
                    <th colspan="2">Particular</th>
                    <th rowspan="2">D.V.No.</th>
                    <th rowspan="2">Credit<br>Amount</th>
                    <th rowspan="2">Debit<br>Amount</th>
                    <th rowspan="2">Total<br>Amount</th>
                </tr>
                <tr>
                    <th>Cash</th>
                    <th>Bank</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial = 1;
                $total_credit = 0;
                $total_debit = 0;

                if (count($transactions) > 0):
                    foreach ($transactions as $trans):
                        $credit_amt = $trans['transaction_type'] == 'credit' ? $trans['amount'] : 0;
                        $debit_amt = $trans['transaction_type'] == 'debit' ? $trans['amount'] : 0;
                        $total_credit += $credit_amt;
                        $total_debit += $debit_amt;
                        $running_balance += ($credit_amt - $debit_amt);
                ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($trans['transaction_date'])); ?></td>
                            <td><?php echo $trans['payment_mode'] == 'cash' ? '✓' : ''; ?></td>
                            <td><?= in_array($trans['payment_mode'], ['bank_transfer','cheque'])
        ? htmlspecialchars($trans['bank_name'])
        : '' ?>
</td>
                            <td><?php echo htmlspecialchars($trans['dv_no'] ?? ''); ?></td>
                            <td class="credit-amount"><?php echo $credit_amt > 0 ? '₹' . number_format($credit_amt, 2) : '-'; ?></td>
                            <td class="debit-amount"><?php echo $debit_amt > 0 ? '₹' . number_format($debit_amt, 2) : '-'; ?></td>
                            <td>₹<?php echo number_format($running_balance, 2); ?></td>
                        </tr>
                    <?php
                    endforeach;

                    // Add empty rows to make minimum 12 rows
                    $empty_rows = max(0, 12 - count($transactions));
                    for ($i = 0; $i < $empty_rows; $i++):
                    ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php
                    endfor;
                else:
                    // No transactions, show 12 empty rows
                    for ($i = 1; $i <= 12; $i++):
                    ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                <?php
                    endfor;
                endif;
                ?>

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;">TOTAL:</td>
                    <td class="credit-amount">₹<?php echo number_format($total_credit, 2); ?></td>
                    <td class="debit-amount">₹<?php echo number_format($total_debit, 2); ?></td>
                    <td>₹<?php echo number_format($running_balance, 2); ?></td>
                </tr>

                <!-- Balance Due Row -->
                <?php $balance_due = $data['total_land_value'] - $running_balance; ?>
                <tr class="balance-row">
                    <td colspan="7" style="text-align: right; font-size: 13px;">BALANCE DUE:</td>
                    <td style="font-size: 14px; color: <?php echo $balance_due > 0 ? 'red' : 'green'; ?>;">
                        ₹<?php echo number_format($balance_due, 2); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Summary -->
        <div class="summary-box">
            <h5 style="margin-bottom: 10px; text-align: center; text-decoration: underline;">PAYMENT SUMMARY</h5>
            <div class="summary-item">
                <span>Total Land Value:</span>
                <span>₹<?php echo number_format($data['total_land_value'], 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Total Credit (Received):</span>
                <span style="color: green;">₹<?php echo number_format($total_credit, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Total Debit (Paid Out):</span>
                <span style="color: red;">₹<?php echo number_format($total_debit, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Net Paid Amount:</span>
                <span>₹<?php echo number_format($running_balance, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>Balance Due:</span>
                <span style="color: <?php echo $balance_due > 0 ? 'red' : 'green'; ?>;">
                    ₹<?php echo number_format($balance_due, 2); ?>
                    <?php echo $balance_due <= 0 ? ' (PAID)' : ''; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <a href="lopayment.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</body>

</html>