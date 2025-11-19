<?php
include_once 'connectdb.php'; // Assuming this file contains your PDO connection

// Get parameters from URL
$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : '';
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';
$row_id = isset($_GET['row_id']) ? $_GET['row_id'] : '';

try {
    // Fetch data from receiveallpayment and tbl_customeramount
    $sql = "
        SELECT 
            rap.*,
            tc.customer_name AS tc_customer_name,
            tc.customer_address AS address,
            tc.productname AS tc_productname,
            tc.net_amount AS tc_net_amount,
            tc.payamount AS tc_payamount,
            tc.due_amount AS tc_due_amount
        FROM 
            receiveallpayment rap
        LEFT JOIN 
            tbl_customeramount tc 
            ON rap.invoice_id = tc.invoice_id 
            AND rap.member_id = tc.member_id
        WHERE 
            rap.invoice_id = :invoice_id 
            AND rap.member_id = :member_id
            AND rap.id = :row_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':invoice_id' => $invoice_id,
        ':member_id' => $member_id,
        ':row_id' => $row_id
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Assign variables for display
    if ($row) {
        $customerName = $row['tc_customer_name'] ?? $row['customer_name'];
        $customerAddress = $row['address'] ?? '';
        $invoiceDate = $row['created_date']; // Payment date
        $invoiceNo = $row['invoice_id'];
        $receiptno = 'HHD' . $row['id'];
        $productName = $row['productname'] ?? $row['tc_productname'];

        $totalAmount = $row['tc_net_amount'] ?? $row['net_amount']; // Total from tbl_customeramount
        $payAmount = $row['payamount']; // This specific payment
        // $duesAmount = $row['due_amount']; // Remaining due after this payment
        $paymentMode = $row['payment_mode'];

        // Additional payment details
        $chequeNumber = $row['cheque_number'] ?? '';
        $bankName = $row['bank_name'] ?? '';
        $chequeDate = $row['cheque_date'] ?? '';
        $utrNumber = $row['utr_number'] ?? '';

        $amountWords = numberToWords($payAmount);
    } else {
        $error = "No payment record found for the given invoice ID, member ID, and row ID";
    }
} catch (PDOException $e) {
    $error = "Query failed: " . $e->getMessage();
}

// Function to convert number to words (unchanged)
function numberToWords($number)
{
    $ones = array(
        0 => "Zero",
        1 => "One",
        2 => "Two",
        3 => "Three",
        4 => "Four",
        5 => "Five",
        6 => "Six",
        7 => "Seven",
        8 => "Eight",
        9 => "Nine",
        10 => "Ten",
        11 => "Eleven",
        12 => "Twelve",
        13 => "Thirteen",
        14 => "Fourteen",
        15 => "Fifteen",
        16 => "Sixteen",
        17 => "Seventeen",
        18 => "Eighteen",
        19 => "Nineteen"
    );
    $tens = array(
        2 => "Twenty",
        3 => "Thirty",
        4 => "Forty",
        5 => "Fifty",
        6 => "Sixty",
        7 => "Seventy",
        8 => "Eighty",
        9 => "Ninety"
    );

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
                if ($ones_num > 0) {
                    $words .= " " . $ones[$ones_num];
                }
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


$totalSql = "
    SELECT SUM(payamount) AS total_paid
    FROM receiveallpayment
    WHERE invoice_id = :invoice_id AND member_id = :member_id
";
$totalStmt = $pdo->prepare($totalSql);
$totalStmt->execute([
    ':invoice_id' => $invoice_id,
    ':member_id' => $member_id
]);
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalPaidAmount = $totalRow['total_paid'] ?? 0;
$duesAmount = $totalAmount - $totalPaidAmount;

?>

<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
    <style>
        @media print {
            .print-buttons {
                display: none;
            }

            @page {
                size: A4;
                margin: 5mm;
                /* Reduced margin to fit more content */
            }

            /* Remove browser-added headers and footers */
            @page {
                margin-top: 5mm;
                margin-bottom: 5mm;
            }

            body {
                margin: 0;
                /* Remove default body margin */
            }

            /* Ensure no extra content is added by the browser */
            header,
            footer {
                display: none !important;
            }
        }

        .container {
            width: 100%;
            max-width: 297mm;
            /* A4 width in landscape */
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 3mm;
            /* Reduced gap between copies */
        }

        .receipt-copy {
            width: 100%;
            padding: 3mm;
            /* Increased padding for a cleaner look */
            border: 1px solid #000;
            box-sizing: border-box;
            font-size: 10pt;
            /* Increased font size for better readability */
            height: auto;
            /* Allow flexible height */
            /* min-height: 270mm; */
            /* Ensures full use of A4 page */
        }

        .copy-label {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1mm;
            /* Increased margin for better spacing */
            font-size: 12pt;
            /* Increased font size for better visibility */
        }

        table {
            width: 100%;
            font-size: 9pt;
            /* Slightly larger font for tables */
            border-collapse: collapse;
            /* Reduce spacing */
        }

        th,
        td {
            padding: 1mm;
            /* Increased padding for clarity */
        }

        .header-img {
            width: 100%;
            height: auto;
            max-height: 25mm;
            /* Increased header size for better visibility */
        }

        /* Minimize vertical space */
        div {
            margin: 0;
            padding: 2mm;
            /* Reduced padding for less space wastage */
        }

        /* Custom footer styling */
        .custom-footer {
            font-size: 8pt;
            /* Slightly larger font for footer */
            text-align: center;
            margin-top: 5mm;
        }

        .custom-footer a {
            text-decoration: none;
            color: black;
            /* Ensure links don’t appear clickable */
        }
    </style>

</head>

<body>
    <?php if (isset($error)): ?>
        <div><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div class="container">
            <!-- Self Copy -->
            <div class="receipt-copy">
                <div class="copy-label">Self Copy</div>
                <div class="print-buttons">
                    <button id="printBtn" onclick="window.print()">
                        <img src="../images/print_icon.gif" alt="Print" width="15px" height="15px">
                    </button>
                </div>

                <div style="border: thin solid #000000">
                    <img src="../images/hariheaderinvoice.png" class="header-img" alt="Header Image">
                </div>

                <table style="border-right: thin solid #000; border-left: thin solid #000; background-color:#fffbd5; color:black">
                    <tr>
                        <td width="10%">Name:</td>
                        <td width="60%"><b><?php echo htmlspecialchars($customerName); ?></b></td>
                        <td width="10%">Date:</td>
                        <td width="20%"><b><?php echo htmlspecialchars($invoiceDate); ?></b></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><b><?php echo htmlspecialchars($customerAddress); ?></b></td>
                        <td>Invoice No:</td>
                        <td><b><?php echo htmlspecialchars($invoiceNo); ?></b></td>
                    </tr>
                    <tr>
                        <td>Receipt No:</td>
                        <td><b><?php echo htmlspecialchars($receiptno); ?></b></td>
                    </tr>
                </table>

                <div style="border: thin solid #000000; background-color:#fffbd5;">
                    <table style="border-collapse: collapse; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%; text-align: center; border-bottom: thin solid #000; padding: 0.5mm;">SR NO</th>
                                <th style="width: 50%; text-align: left; border-bottom: thin solid #000; padding: 0.5mm;">DESCRIPTION</th>
                                <th style="width: 20%; text-align: center; border-bottom: thin solid #000; padding: 0.5mm;">Mode</th>
                                <th style="width: 20%; text-align: right; border-bottom: thin solid #000; padding: 0.5mm;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding: 0.5mm;">1</td>
                                <td style="text-align: left; padding: 0.5mm;"><?php echo htmlspecialchars($productName); ?> Payment</td>
                                <td style="text-align: center; padding: 0.5mm;"><?php echo htmlspecialchars($paymentMode); ?></td>
                                <td style="text-align: right; padding: 0.5mm;"><?php echo htmlspecialchars($payAmount); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="background-color:#fffbd5;">
                    <table>
                        <tr>
                            <td width="50%"><b>Total:</b></td>
                            <td width="50%"><b><?php echo htmlspecialchars($totalAmount); ?></b></td>
                        </tr>
                        <tr>
                            <td><b>Paid:</b></td>
                            <td><b><?php echo htmlspecialchars($payAmount); ?></b></td>
                        </tr>
                        <tr>
                            <td><b>Total Paid:</b></td>
                            <td><b><?php echo htmlspecialchars($totalPaidAmount); ?></b></td>
                        </tr>
                        <tr>
                            <td><b>Due:</b></td>
                            <td><b><?php echo htmlspecialchars($duesAmount); ?></b></td>
                        </tr>
                        <?php if ($paymentMode === 'cheque'): ?>
                            <tr>
                                <td><b>Cheque No:</b></td>
                                <td><b><?php echo htmlspecialchars($chequeNumber); ?></b></td>
                            </tr>
                            <tr>
                                <td><b>Bank:</b></td>
                                <td><b><?php echo htmlspecialchars($bankName); ?></b></td>
                            </tr>
                        <?php elseif ($paymentMode === 'bank_transfer'): ?>
                            <tr>
                                <td><b>UTR:</b></td>
                                <td><b><?php echo htmlspecialchars($utrNumber); ?></b></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div style="background-color:#28ffeb;">
                    <b>Amount in words:</b>
                    <b style="color:red"><?php echo htmlspecialchars($amountWords); ?></b>
                </div>

                <div style="background-color:#fffbd5; font-size: 6pt;">
                    <b>Terms:</b> 1. No exchanges. 2. Payment failure responsibility of customer. 3. Darbhanga jurisdiction.
                    <div style="text-align: right;">Authorized Signatory</div>
                </div>

                <div class="custom-footer">
                    📍 Barheta Road, Laheriasarai, Darbhanga, Bihar-846001 |
                    🌐 <a href="http://www.harihomes.co/">www.harihomes.co</a> |
                    ✉️ <a href="mailto:Harihomes34@gmail.com">Harihomes34@gmail.com</a>
                </div>
            </div>

            <!-- Customer Copy -->
            <div class="receipt-copy">
                <div class="copy-label">Customer Copy</div>
                <div style="border: thin solid #000000">
                    <img src="../images/hariheaderinvoice.png" class="header-img" alt="Header Image">
                </div>

                <table style="border-right: thin solid #000; border-left: thin solid #000; background-color:#fffbd5; color:black">
                    <tr>
                        <td width="10%">Name:</td>
                        <td width="60%"><b><?php echo htmlspecialchars($customerName); ?></b></td>
                        <td width="10%">Date:</td>
                        <td width="20%"><b><?php echo htmlspecialchars($invoiceDate); ?></b></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><b><?php echo htmlspecialchars($customerAddress); ?></b></td>
                        <td>Invoice No:</td>
                        <td><b><?php echo htmlspecialchars($invoiceNo); ?></b></td>
                    </tr>
                    <tr>
                        <td>Receipt No:</td>
                        <td><b><?php echo htmlspecialchars($receiptno); ?></b></td>
                    </tr>
                </table>

                <div style="border: thin solid #000000; background-color:#fffbd5;">
                    <table style="border-collapse: collapse; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 10%; text-align: center; border-bottom: thin solid #000; padding: 0.5mm;">SR NO</th>
                                <th style="width: 50%; text-align: left; border-bottom: thin solid #000; padding: 0.5mm;">DESCRIPTION</th>
                                <th style="width: 20%; text-align: center; border-bottom: thin solid #000; padding: 0.5mm;">Mode</th>
                                <th style="width: 20%; text-align: right; border-bottom: thin solid #000; padding: 0.5mm;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding: 0.5mm;">1</td>
                                <td style="text-align: left; padding: 0.5mm;"><?php echo htmlspecialchars($productName); ?> Payment</td>
                                <td style="text-align: center; padding: 0.5mm;"><?php echo htmlspecialchars($paymentMode); ?></td>
                                <td style="text-align: right; padding: 0.5mm;"><?php echo htmlspecialchars($payAmount); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="background-color:#fffbd5;">
                    <table>
                        <tr>
                            <td width="50%"><b>Total:</b></td>
                            <td width="50%"><b><?php echo htmlspecialchars($totalAmount); ?></b></td>
                        </tr>
                        <tr>
                            <td><b>Paid:</b></td>
                            <td><b><?php echo htmlspecialchars($payAmount); ?></b></td>
                        </tr>
                        <tr>
                            <td><b>Total Paid:</b></td>
                            <td><b><?php echo htmlspecialchars($totalPaidAmount); ?></b></td>
                        </tr>
                        <tr>
                            <td><b>Due:</b></td>
                            <td><b><?php echo htmlspecialchars($duesAmount); ?></b></td>
                        </tr>
                        <?php if ($paymentMode === 'cheque'): ?>
                            <tr>
                                <td><b>Cheque No:</b></td>
                                <td><b><?php echo htmlspecialchars($chequeNumber); ?></b></td>
                            </tr>
                            <tr>
                                <td><b>Bank:</b></td>
                                <td><b><?php echo htmlspecialchars($bankName); ?></b></td>
                            </tr>
                        <?php elseif ($paymentMode === 'bank_transfer'): ?>
                            <tr>
                                <td><b>UTR:</b></td>
                                <td><b><?php echo htmlspecialchars($utrNumber); ?></b></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div style="background-color:#28ffeb;">
                    <b>Amount in words:</b>
                    <b style="color:red"><?php echo htmlspecialchars($amountWords); ?></b>
                </div>

                <div style="background-color:#fffbd5; font-size: 6pt;">
                    <b>Terms:</b> 1. No exchanges. 2. Payment failure responsibility of customer. 3. Darbhanga jurisdiction.
                    <div style="text-align: right;">Authorized Signatory</div>
                </div>

                <div class="custom-footer">
                    📍 Barheta Road, Laheriasarai, Darbhanga, Bihar-846001 |
                    🌐 <a href="http://www.harihomes.co/">www.harihomes.co</a> |
                    ✉️ <a href="mailto:Harihomes34@gmail.com">Harihomes34@gmail.com</a>
                </div>
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