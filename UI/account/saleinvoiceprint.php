<?php
include_once 'connectdb.php'; // Assuming this file contains your PDO connection

// Get parameters from URL
$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : '';
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';

try {
    // Fetch data from sales_records
    $sql = "
    SELECT 
        sr.*, 
        p.ProductName AS product_name_display 
    FROM 
        sales_records sr
    LEFT JOIN 
        products p ON sr.product_name = p.id
    WHERE 
        sr.invoice_id = :invoice_id 
        AND sr.member_id = :member_id
";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':invoice_id' => $invoice_id,
        ':member_id' => $member_id
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Assign variables for display
    if ($row) {
        $customerName = $row['customer_name'];
        $customerAddress = $row['address'] . ", " . $row['district'] . ", " . $row['state'];
        $invoiceDate = $row['date'];
        $invoiceNo = $row['invoice_id'];
        $productName = $row['product_name_display'];
        $productname = $row['product_name'];

        $squareFeet = $row['squarefeet'];
        $rate = $row['rate'];
        $amount = $row['amount'];
        $grossAmount = $row['gross_amount'];
        $discount = $row['discount_rs'];
        $cornerCharge = $row['corner_charge'];
        $totalAmount = $row['net_amount'];
        $payAmount = floatval($row['cash_amount']) + floatval($row['cheque_amount']) + floatval($row['transfer_amount']);
        $duesAmount = $row['due_amount'];
        $amountWords = numberToWords($totalAmount);
    } else {
        $error = "No record found for the given invoice ID and member ID";
    }
} catch (PDOException $e) {
    $error = "Query failed: " . $e->getMessage();
}

// Function to convert number to words
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

    // Helper function to convert numbers less than 1000 without "Rupees"
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
        // Handle crores
        if ($rupees >= 10000000) {
            $crores = floor($rupees / 10000000);
            $words .= convertBelowThousand($crores, $ones, $tens) . " Crore ";
            $rupees %= 10000000;
        }

        // Handle lakhs
        if ($rupees >= 100000) {
            $lakhs = floor($rupees / 100000);
            $words .= convertBelowThousand($lakhs, $ones, $tens) . " Lakh ";
            $rupees %= 100000;
        }

        // Handle thousands
        if ($rupees >= 1000) {
            $thousands = floor($rupees / 1000);
            $words .= convertBelowThousand($thousands, $ones, $tens) . " Thousand ";
            $rupees %= 1000;
        }

        // Handle remaining amount (hundreds, tens, ones)
        if ($rupees > 0) {
            $words .= convertBelowThousand($rupees, $ones, $tens) . " ";
        }
    }
    // Add "Rupees Only" once at the end
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
        @media print {
            .print-buttons {
                display: none;
            }

            body * {
                visibility: visible;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($error)): ?>
        <div><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div id="print" style="display:block;" class="py-3">
            <div class="py-2 print-buttons">
                <button id="printBtn" onclick="window.print()">
                    <img src="../images/print_icon.gif" alt="Print" width="30px" height="30px">
                </button>
                <button id="exportExcel">
                    <img src="../images/excel_icon.gif" alt="Excel" width="30px" height="30px">
                </button>
            </div>

            <div style="border: thin solid #000000">
                <img src="../images/hariheaderinvoice.png" width="100%" height="152px" alt="Header Image">
            </div>

            <div>
                <table width="100%" style="border-right: thin solid #000; border-left: thin solid #000; padding-bottom: 10px; height: 75px; background-color:#fffbd5; color:black">
                    <tr>
                        <td width="10%">Name:</td>
                        <td width="60%"><b><?php echo htmlspecialchars($customerName); ?></b></td>
                        <td width="10%">Invoice Date:</td>
                        <td width="20%"><b><?php echo htmlspecialchars($invoiceDate); ?></b></td>
                    </tr>
                    <tr>
                        <td width="10%">Address:</td>
                        <td width="60%"><b><?php echo htmlspecialchars($customerAddress); ?></b></td>
                        <td width="10%">Invoice No:</td>
                        <td width="20%"><b><?php echo htmlspecialchars($invoiceNo); ?></b></td>
                    </tr>
                </table>
            </div>

            <div style="border: thin solid #000000; background-color:#fffbd5; text-align: right;">
                <table>
                    <thead>
                        <tr>
                            <th width="80px">SR NO</th>
                            <th width="560px">DESCRIPTION OF GOODS</th>
                            <th width="120px">Square Feet</th>
                            <th width="120px">Rate</th>
                            <th width="120px">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?php echo htmlspecialchars($productname); ?></td>
                            <td><?php echo htmlspecialchars($squareFeet); ?></td>
                            <td><?php echo htmlspecialchars($rate); ?></td>
                            <td><?php echo htmlspecialchars($amount); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="background-color:#fffbd5; padding: 20px;">
                <table>
                    <tr>
                        <td width="50%"><b>GROSS AMOUNT</b></td>
                        <td width="50%"><b><?php echo htmlspecialchars($grossAmount); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Discount</b></td>
                        <td><b><?php echo htmlspecialchars($discount); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Corner Charges</b></td>
                        <td><b><?php echo htmlspecialchars($cornerCharge); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>TOTAL AMOUNT</b></td>
                        <td><b><?php echo htmlspecialchars($totalAmount); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Payment Amount</b></td>
                        <td><b><?php echo htmlspecialchars($payAmount); ?></b></td>
                    </tr>
                    <tr>
                        <td><b>Dues Amount</b></td>
                        <td><b><?php echo htmlspecialchars($duesAmount); ?></b></td>
                    </tr>
                </table>
            </div>

            <div style="background-color:#28ffeb; padding: 20px;">
                <table>
                    <tr>
                        <td style="text-align: left;">
                            <h4 style="font-size: 17px;"></h4>
                            <p style="font-size: 14px; text-align: left">
                                <b style="color:black">Amount (in words) :</b>
                                <b style="color:red"><?php echo htmlspecialchars($amountWords); ?></b>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="background-color:#fffbd5; padding: 20px;">
                <table>
                    <tr>
                        <td colspan="5" style="text-align: left;">
                            <h4 style="font-size: 17px;">Terms & Conditions:</h4>
                            <p style="font-size: 14px;">
                                <b>1. Goods once sold cannot be exchanged.</b><br />
                                <b>2. Any payment failure during the given time will be the responsibility of the Customer.</b><br />
                                <b>3. Subject to Jurisdiction of Darbhanga, Bihar.</b><br />
                            </p>
                        </td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td width="70%"></td>
                        <td width="30%" style="text-align: center;">Authorized Signatory</td>
                    </tr>
                </table>
            </div>

            <div class="container">
                <table>
                    <tr>
                        <td>
                            <h4 style="font-size: 17px;">Declaration</h4>
                            <p style="font-size: 14px;">
                                <b>We declare that this invoice shows the actual price of the goods described and that all particulars are true & correct.</b><br />
                                <b>This is a computer-generated invoice.</b>
                            </p>
                        </td>
                    </tr>
                </table>

                <hr />

                <table>
                    <tr>
                        <td>📍 Address: Barheta Road, Laheriasarai, Darbhanga, Bihar-846001</td>
                    </tr>
                    <tr>
                        <td>🌐 Website: <a href="http://www.harihomes.co/" target="_blank">www.harihomes.co</a> | ✉️ Email: <a href="mailto:Harihomes34@gmail.com">Harihomes34@gmail.com</a></td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Auto print when page loads (except when there's an error)
        <?php if (!isset($error)): ?>
            window.onload = function() {
                window.print();
            }
        <?php endif; ?>
    </script>
</body>

</html>