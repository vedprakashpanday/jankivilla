  <?php
 error_reporting(E_ALL);
ini_set('display_errors', 1);;
ob_start(); // Start output buffering to capture any unintended output
include_once 'connectdb.php'; // Assuming this file contains your PDO connection

// Get parameters from URL
$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : '';
$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : '';
$row_id = isset($_GET['row_id']) ? $_GET['row_id'] : '';




$stmt = $pdo->prepare("
    SELECT cashback
    FROM receiveallpayment
    WHERE id < :row_id
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute(['row_id' => $row_id]);

$previous_row = $stmt->fetch(PDO::FETCH_ASSOC);


$admission = isset($_GET['admission']) ? $_GET['admission'] : 'null';
$enroll = isset($_GET['enroll']) ? $_GET['enroll'] : 'null';
$amount = isset($_GET['pay']) ? $_GET['pay'] : 'null';

$slipTitle = 'Payment Slip';

if ($admission > 0 && $enroll == 'null') {
    $slipTitle = 'Admission Payment Slip';
} elseif ($enroll != 'null' && $amount != 'null') {
    $slipTitle = 'Enrollment Payment Slip';
} else {
    $slipTitle = 'Allotment Payment Slip';
}

if($admission>0 && $enroll=='null'&& $amount=='null')
{
    $payAmount = $admission;
}
if($enroll!='null'&& $amount!='null')
{
    $payAmount = $enroll + $amount;
    //$payAmount = $enroll + $amount+$previous_row['cashback'];
}

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
        if(!empty($row['adm_receipt']))
        {
        $receiptno = $row['adm_receipt'];
         
        }

         if(!empty($row['receipt_no']))
        {
        $receiptno = $row['receipt_no'];
        
        }
        
        $productName = $row['productname'] ?? $row['tc_productname'];

        $totalAmount = $row['tc_net_amount'] ?? $row['net_amount']; // Total from tbl_customeramount
        
        // $duesAmount = $row['due_amount']; // Remaining due after this payment
        $paymentMode = $row['payment_mode'];

        // Additional payment details
        $chequeNumber = $row['cheque_number'] ?? '';
        $bankName = $row['bank_name'] ?? '';
        $chequeDate = $row['cheque_date'] ?? '';
        $utrNumber = $row['utr_number'] ?? '';
        $neft_payment = $row['neft_payment'] ?? '';
        $rtgs_payment = $row['rtgs_payment'] ?? '';
        $bill_prepared_by_name = $row['bill_prepared_by_name'] ?? '';
        $voucher_number = $row['voucher_number'] ?? '';
        $cashback=$row['cashback'] ?? '';
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
    SELECT SUM(payamount) AS total_paid,SUM(enrollment_charge) AS enroll
    FROM receiveallpayment
    WHERE invoice_id = :invoice_id AND member_id = :member_id
";
$totalStmt = $pdo->prepare($totalSql);
$totalStmt->execute([ 
    ':invoice_id' => $invoice_id,
    ':member_id' => $member_id
]);
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalPaidAmount = $totalRow['total_paid']+$totalRow['enroll']+$previous_row['cashback'] ?? 0;
$duesAmount = $totalAmount - ($totalPaidAmount+$previous_row['cashback']);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>
  
    <style>
@media print {
    .no-print { display:none; }
    @page { size:A4; margin:8mm; }
    .print-buttons {
                display: none;
            }

                .footer {
        margin-top: 43px !important;  /* PRINT me kam gap */
        /* padding-top: 10px;            /* signature / stamp space */
        /* border-top: 1px solid #999; */ 

        font-size: 11px;
        text-align: center;
    }

    /* Page break ko rokne ke liye */
    .footer {
        page-break-inside: avoid;
    }

    .invoice-header img{
    height:75px !important;
}
}

body{
    font-family: "Segoe UI", Arial, sans-serif;
    background:#f4f4f4;
}

.invoice{
    background:#fff;
    border:1px solid #000;
    padding:12px;
    margin-bottom:20px;
    font-size:10pt;
}

.invoice-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #000;
    padding-bottom:8px;
}

.invoice-header img{
    height:100px;
}

.invoice-title{
    text-align:right;
}

.invoice-title h2{
    margin:0;
    font-size:16pt;
    letter-spacing:1px;
}

.meta{
    width:100%;
    margin-top:10px;
    border-collapse:collapse;
}

.meta td{
    padding:4px;
}

.items{
    width:100%;
    margin-top:10px;
    border-collapse:collapse;
}

.items th{
    background:#eee;
    border:1px solid #000;
    padding:6px;
}

.items td{
    border:1px solid #000;
    padding:6px;
}

.summary{
    margin-top:10px;
    width:40%;
    float:right;
    border-collapse:collapse;
}

.summary td{
    padding:6px;
}

.summary tr:last-child{
    font-weight:bold;
    background:#ffeaea;
}

.highlight{
    clear:both;
    margin-top:10px;
    padding:8px;
    background:#e8fdf5;
    border:1px dashed #008000;
    font-weight:bold;
    text-align:center;
}

.words{
    margin-top:8px;
    padding:6px;
    background:#f0f0ff;
    border:1px solid #000;
}

.footer{
    margin-top:70px;
    display:flex;
    justify-content:space-between;
    font-size:9pt;
}

.copy{
    font-size:11pt;
    font-weight:bold;
    text-align:center;
    margin-bottom:5px;
}
</style>


</head>

<body>
    <?php if (isset($error)): ?>
        <div><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
        <div class="container">
            <!-- Self Copy -->
        

            <div class="invoice">

    <div class="copy">SELF COPY</div>
 <div class="print-buttons">
                    <button id="printBtn" onclick="window.print()">
                        <img src="../images/print_icon.gif" alt="Print" width="15px" height="15px">
                    </button>
                </div>
    <div class="invoice-header">
        <img src="../../image/harihomes1-logo.png" alt="Company Logo" width="">
        
        <div class="invoice-title">
            <h2><?php echo $slipTitle; ?></h2>
            <div>Date: <?php echo $invoiceDate; ?></div>
            <div>Invoice No: <?php echo $invoiceNo; ?></div>
            <div>Receipt No: <?php echo $receiptno; ?></div>
             <?php if($previous_row['cashback']>0): ?>
                    <div>CashBack Applied: <?php echo $previous_row['cashback']; ?></div>
                <?php endif; ?>
        </div>
    </div>

    <table class="meta">
        <tr>
            <td width="15%">Customer:</td>
            <td width="60%"><b><?php echo $customerName; ?></b></td>
            <td width="25%">Payment Mode:</td>
            <td><b><?php echo strtoupper($paymentMode); ?></b></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td colspan="3"><?php echo $customerAddress; ?></td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Description</th>
                <th>Mode</th>
                <th align="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td><?php echo $productName; ?> Payment</td>
                <td align="center"><?php echo $paymentMode; ?></td>
                <td align="right">₹ <?php echo number_format($payAmount,2); ?></td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Total Amount</td>
            <td align="right">₹ <?php echo number_format($totalAmount,2); ?></td>
        </tr>
        <tr>
            <td>Total Paid</td>
            <td align="right" style="color:green;">₹ <?php echo number_format($totalPaidAmount,2); ?></td>
        </tr>
        <tr>
            <td>Due Amount</td>
            <td align="right" style="color:red;">₹ <?php echo number_format($duesAmount,2); ?></td>
        </tr>
    </table>

    <div class="highlight">
        Current Payment Received: ₹ <?php echo number_format($payAmount,2); ?>
    </div>

    <div class="words">
        <b>Amount in Words:</b> <?php echo $amountWords; ?>
    </div>

    <div class="footer">
        <div>
            Prepared By<br>
            <b><?php echo $bill_prepared_by_name; ?></b>
        </div>
        <div>
            Authorized Signatory
        </div>
        <div>
            Received By
        </div>
    </div>

</div>
        </div>
    <?php endif; ?>
            <!-- Customer Copy -->
           
<div class="invoice">

    <div class="copy">CUSTOMER COPY</div>
<!-- ../images/hariheaderinvoice.png -->

    <div class="invoice-header">
        <img src="../../image/harihomes1-logo.png" alt="Company Logo">
        <div class="invoice-title">
            <h2><?php echo $slipTitle; ?></h2>
            <div>Date: <?php echo $invoiceDate; ?></div>
            <div>Invoice No: <?php echo $invoiceNo; ?></div>
            <div>Receipt No: <?php echo $receiptno; ?></div>
            <?php if($previous_row['cashback']>0): ?>
                    <div>CashBack Applied: <?php echo $previous_row['cashback']; ?></div>
                <?php endif; ?>
        </div>
    </div>

    <table class="meta">
        <tr>
            <td width="15%">Customer:</td>
            <td width="60%"><b><?php echo $customerName; ?></b></td>
            <td width="25%">Payment Mode:</td>
            <td><b><?php echo strtoupper($paymentMode); ?></b></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td colspan="3"><?php echo $customerAddress; ?></td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Sr</th>
                <th>Description</th>
                <th>Mode</th>
                <th align="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td><?php echo $productName; ?> Payment</td>
                <td align="center"><?php echo $paymentMode; ?></td>
                <td align="right">₹ <?php echo number_format($payAmount,2); ?></td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Total Amount</td>
            <td align="right">₹ <?php echo number_format($totalAmount,2); ?></td>
        </tr>
        <tr>
            <td>Total Paid</td>
            <td align="right" style="color:green;">₹ <?php echo number_format($totalPaidAmount,2); ?></td>
        </tr>
        <tr>
            <td>Due Amount</td>
            <td align="right" style="color:red;">₹ <?php echo number_format($duesAmount,2); ?></td>
        </tr>
    </table>

    <div class="highlight">
        Current Payment Received: ₹ <?php echo number_format($payAmount,2); ?>
    </div>

    <div class="words">
        <b>Amount in Words:</b> <?php echo $amountWords; ?>
    </div>

    <div class="footer">
        <div>
            Prepared By<br>
            <b><?php echo $bill_prepared_by_name; ?></b>
        </div>
        <div>
            Authorized Signatory
        </div>
        <div>
            Received By
        </div>
    </div>

</div>


    <script>
        <?php if (!isset($error)): ?>
            window.onload = function() {
                window.print();
            }
        <?php endif; ?>
    </script>
</body>

</html>