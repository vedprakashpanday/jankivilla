<?php
include_once 'connectdb.php'; // Ensure this sets up $pdo

$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : '';
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';

try {
    // Fetch main invoice details from tbl_customeramount
    $sql = "
        SELECT 
            tca.invoice_id,
            tca.member_id,
            tca.created_date,
            tca.customer_name,
            tca.customer_address,
            tca.productname AS product_name,
            tca.rate,
            tca.area,
            tca.net_amount,
            tca.due_amount,
            tca.corner_charge,
            tca.gross_amount
        FROM tbl_customeramount tca
        WHERE tca.invoice_id = :invoice_id 
        AND tca.member_id = :member_id
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':invoice_id' => $invoice_id,
        ':member_id' => $member_id
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $customerName = $row['customer_name'];
        $customerAddress = $row['customer_address'];
        $invoiceDate = $row['created_date'];
        $invoiceNo = $row['invoice_id'];
        $productName = $row['product_name'];
        $area = $row['area'];
        $rate = $row['rate'];
        $netAmount = $row['net_amount'];
        $cornerCharge = $row['corner_charge'];
        $grossAmount = $row['gross_amount'];
        $dueAmount = $row['due_amount'];

        // Fetch all payment details from receiveallpayment
        $sql_payment = "
            SELECT 
                created_date,
                payment_mode,
                payamount,
                cheque_number,
                bank_name,
                cheque_date,
                utr_number,
                neft_payment,
                rtgs_payment
            FROM receiveallpayment
            WHERE invoice_id = :invoice_id
            ORDER BY created_date ASC
        ";
        $stmt_payment = $pdo->prepare($sql_payment);
        $stmt_payment->execute([':invoice_id' => $invoice_id]);
        $payment_rows = $stmt_payment->fetchAll(PDO::FETCH_ASSOC);

        // Calculate total paid amount
        $totalPaid = 0;
        if ($payment_rows) {
            foreach ($payment_rows as $payment_row) {
                $totalPaid += floatval($payment_row['payamount']);
            }
        }

        $amountWords = numberToWords($netAmount);
    } else {
        $error = "No record found for the given invoice ID and member ID";
    }
} catch (PDOException $e) {
    $error = "Query failed: " . $e->getMessage();
}

// Function to convert number to words (unchanged from original)
function numberToWords($number)
{
    $ones = array(0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen");
    $tens = array(2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety");

    $number = floatval($number);
    $rupees = floor($number);
    $paise = round(($number - $rupees) * 100);

    function convertBelowThousand($num, $ones, $tens)
    {
        $words = "";
        if ($num >= 100) {
            $hundreds = floor($num / 100);
            $words .= $ones[$hundreds] . " Hundred ";
            $num %= 100;
        }
        if ($num > 0) {
            if ($num < 20) {
                $words .= $ones[$num];
            } else {
                $tens_num = floor($num / 10);
                $ones_num = $num % 10;
                $words .= $tens[$tens_num];
                if ($ones_num > 0) $words .= " " . $ones[$ones_num];
            }
        }
        return trim($words);
    }

    $words = "";
    if ($rupees == 0) {
        $words = "Zero";
    } else {
        if ($rupees >= 10000000) {
            $crores = floor($rupees / 10000000);
            $words .= convertBelowThousand($crores, $ones, $tens) . " Crore ";
            $rupees %= 10000000;
        }
        if ($rupees >= 100000) {
            $lakhs = floor($rupees / 100000);
            $words .= convertBelowThousand($lakhs, $ones, $tens) . " Lakh ";
            $rupees %= 100000;
        }
        if ($rupees >= 1000) {
            $thousands = floor($rupees / 1000);
            $words .= convertBelowThousand($thousands, $ones, $tens) . " Thousand ";
            $rupees %= 1000;
        }
        if ($rupees > 0) {
            $words .= convertBelowThousand($rupees, $ones, $tens) . " ";
        }
    }
    $words = trim($words);
    $words .= " Rupees";
    if ($paise > 0) {
        $words .= " and " . convertBelowThousand($paise, $ones, $tens) . " Paise";
    }
    $words .= " Only";
    return trim($words);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        @media print {
            .print-buttons {
                display: none;
            }

            @page {
                size: A4;
                margin: 0;
            }

            body {
                margin: 5mm;
            }

            table {
                border-collapse: collapse;
                width: 100%;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 3px;
                font-size: 12px;
            }

            h4 {
                font-size: 14px;
                margin: 5px 0;
            }

            p {
                font-size: 11px;
                margin: 2px 0;
            }
        }

        .invoice-table th,
        .invoice-table td {
            text-align: center;
        }

        .payment-table th,
        .payment-table td {
            text-align: left;
        }

        .compact-section {
            padding: 5px;
        }
    </style>
</head>

<body>
    <?php if (isset($error)): ?>
        <div><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div id="print">
            <div class="py-2 print-buttons">
                <button id="printBtn" onclick="window.print()">
                    <img src="../images/print_icon.gif" alt="Print" width="30px" height="30px">
                </button>
            </div>

            <div style="border: thin solid #000000">
                <img src="../images/hariheaderinvoice.png" width="100%" height="120px" alt="Header Image">
            </div>

            <div>
                <table width="100%" style="border-right: thin solid #000; border-left: thin solid #000; padding: 5px; background-color:#fffbd5;">
                    <tr>
                        <td width="10%">Name:</td>
                        <td width="60%"><b><?php echo htmlspecialchars($customerName); ?></b></td>
                        <td width="10%">Date:</td>
                        <td width="20%"><b><?php echo date('d-m-Y', strtotime($invoiceDate)); ?></b></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><b><?php echo htmlspecialchars($customerAddress); ?></b></td>
                        <td>Inv No:</td>
                        <td><b><?php echo htmlspecialchars($invoiceNo); ?></b></td>
                    </tr>
                    <tr>
                        <td>Member ID:</td>
                        <td><b><?php echo htmlspecialchars($member_id); ?></b></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <div style="border: thin solid #000000; background-color:#fffbd5;">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th width="60px">SR NO</th>
                            <th width="400px">DESCRIPTION</th>
                            <th width="100px">Area (Sq Ft)</th>
                            <th width="100px">Rate</th>
                            <th width="120px">Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?php echo htmlspecialchars($productName); ?></td>
                            <td><?php echo htmlspecialchars($area); ?></td>
                            <td>₹<?php echo number_format($rate, 2); ?></td>
                            <td>₹<?php echo number_format($netAmount, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="compact-section" style="border: thin solid #000000; background-color:#fffbd5;">
                <h4>Payment Details</h4>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th width="80px">Date</th>
                            <th width="100px">Mode</th>
                            <th width="100px">Amount</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($payment_rows): ?>
                            <?php foreach ($payment_rows as $payment_row): ?>
                                <tr>
                                    <td><?php echo date('d-m-Y', strtotime($payment_row['created_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($payment_row['payment_mode']); ?></td>
                                    <td>₹<?php echo number_format($payment_row['payamount'], 2); ?></td>
                                    <td>
                                        <?php
                                        if ($payment_row['payment_mode'] == 'cheque') {
                                            echo "Cheque: " . ($payment_row['cheque_number'] ? htmlspecialchars($payment_row['cheque_number']) : 'N/A') .
                                                ", Bank: " . ($payment_row['bank_name'] ? htmlspecialchars($payment_row['bank_name']) : 'N/A') .
                                                ", " . ($payment_row['cheque_date'] ? date('d-m-Y', strtotime($payment_row['cheque_date'])) : 'N/A');
                                        } elseif ($payment_row['payment_mode'] == 'bank_transfer') {
                                            if ($payment_row['neft_payment']) {
                                                echo "NEFT: " . htmlspecialchars($payment_row['neft_payment']);
                                            } elseif ($payment_row['rtgs_payment']) {
                                                echo "RTGS: " . htmlspecialchars($payment_row['rtgs_payment']);
                                            } elseif ($payment_row['utr_number']) {
                                                echo "UTR: " . htmlspecialchars($payment_row['utr_number']);
                                            } else {
                                                echo "N/A";
                                            }
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No payment records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="compact-section" style="background-color:#fffbd5;">
                <table>
                    <tr>
                        <td width="50%"><b>Gross Amount</b></td>
                        <td width="50%"><b>₹<?php echo number_format($grossAmount, 2); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Corner Charges</b></td>
                        <td><b>₹<?php echo number_format($cornerCharge, 2); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Total Amount</b></td>
                        <td><b>₹<?php echo number_format($netAmount, 2); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Paid Amount</b></td>
                        <td><b>₹<?php echo number_format($totalPaid, 2); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Due Amount</b></td>
                        <td><b>₹<?php echo number_format($dueAmount, 2); ?></b></td>
                    </tr>
                </table>
            </div>

            <div class="compact-section" style="background-color:#28ffeb;">
                <table>
                    <tr>
                        <td><b>Amount (in words):</b> <b style="color:red"><?php echo htmlspecialchars($amountWords); ?></b></td>
                    </tr>
                </table>
            </div>

            <div class="compact-section" style="background-color:#fffbd5;">
                <table>
                    <tr>
                        <td>
                            <h4>Terms & Conditions:</h4>
                            <p>
                                <b>1. Goods once sold cannot be exchanged.</b><br />
                                <b>2. Payment failure is customer's responsibility.</b><br />
                                <b>3. Jurisdiction: Darbhanga, Bihar.</b>
                            </p>
                        </td>
                        <td style="text-align: center; vertical-align: bottom;">Authorized Signatory</td>
                    </tr>
                </table>
            </div>

            <div class="compact-section">
                <table>
                    <tr>
                        <td>
                            <h4>Declaration</h4>
                            <p>
                                <b>Invoice shows actual price; all particulars are true.</b><br />
                                <b>Computer-generated invoice.</b>
                            </p>
                        </td>
                    </tr>
                </table>
                <hr style="margin: 2px 0;" />
                <table>
                    <tr>
                        <td>📍 1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk, Bhuskaul, Darbhanga, Bihar, India, 846007</td>
                    </tr>
                    <tr>
                        <td>🌐 <a href="http://www.jankivilla.com/">www.jankivilla.com</a> | ✉️ <a href="mailto:abdeveloperspl@gmail.com">abdeveloperspl@gmail.com</a></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <script>
        <?php if (!isset($error)): ?>
            window.onload = function() {
                window.print();
            }
        <?php endif; ?>
    </script>
</body>

</html>