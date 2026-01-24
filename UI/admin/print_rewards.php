  <?php
 error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // Start output buffering to capture any unintended output
include_once 'connectdb.php'; // Assuming this file contains your PDO connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cash Prize / Incentive Register</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f7f2;
}

.register-wrapper{
    background:#eef2c9;
    padding:20px;
    position:relative;
    font-family: "Times New Roman", serif;
}

.company-name{
    font-size:26px;
    font-weight:bold;
    text-align:center;
    letter-spacing:1px;
}

.sub-title{
    text-align:center;
    font-size:14px;
    margin-top:-5px;
}

.cin{
    text-align:center;
    font-size:13px;
    margin-bottom:8px;
}

.address{
    text-align:center;
    font-size:13px;
    margin-bottom:10px;
}

.register-title{
    border:1px solid #000;
    padding:5px;
    text-align:center;
    font-weight:bold;
    margin-bottom:10px;
}

.table th, .table td{
    border:1px solid #000 !important;
    font-size:13px;
    padding:6px;
    height:28px;
}

.table th{
    text-align:center;
    vertical-align:middle;
}

.watermark{
    position:absolute;
    inset: 0;
    display:flex;
    align-items:center;
    justify-content:center;
    opacity:0.08;
    pointer-events:none;
    z-index:0;
}

.watermark img{
    max-width: 450px;
}

.action-buttons{
    text-align:right;
    margin-bottom:10px;
}

/* PRINT SETTINGS */
@media print {

    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    body {
        background: #fff;
        margin: 0;
    }

    .container {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
    }

    .register-wrapper {
        width: 100%;
        padding: 15px;
        box-sizing: border-box;
    }

    .action-buttons {
        display: none;
    }
        table{
        width:100% !important;
        table-layout: fixed;
    }

    th, td{
        word-wrap: break-word;
    }
}
</style>
</head>

<body>

<div class="container mt-4">

    <div class="action-buttons">
        <button class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
        <button class="btn btn-success btn-sm" onclick="downloadPDF()">Download PDF</button>
    </div>

    <div id="printArea" class="register-wrapper">

        <div class="watermark"><img id="Img" src="../../image/harihomes1-logo.png" class="mr-2" /></div>

        <div class="company-name">
            AMITABH BUILDERS & DEVELOPERS PVT. LTD.
        </div>

        <div class="cin">
            CIN No.: U43299BR2024PTC072712
        </div>

        <div class="address">
            Address : 1ST FLOOR, PAPPU YADAV BUILDING, SOUTH OF NH-57, KAKARGHATIA, ADARSH CHOWK, DARBHANGA (BIHAR) 846007
        </div>

        <div class="register-title">
            REGISTER OF CASH PRIZE / INCENTIVE / OTHER PERKS
        </div>

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Date</th>
                    <th>P.B. No.</th>
                    <th>D.V. No.</th>
                    <th>Name of Receiver</th>
                    <th>Code of Receiver</th>
                    <th>Designation</th>
                    <th>Particulars / Occasion</th>
                    <th colspan="2">Amount Paid</th>
                    <th>Signature of Receiver</th>
                    <th>Remarks</th>
                </tr>
                <tr>
                    <th colspan="8"></th>
                    <th>Cash</th>
                    <th>Bank</th>
                    <th colspan="2"></th>
                </tr>
            </thead>
        <tbody>
<?php
$stmt = $pdo->query("SELECT * FROM tbl_rewards ORDER BY created_at ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRecords = count($rows);
$minRows = 15;
$displayRows = max($totalRecords, $minRows);

$sn = 1;

for ($i = 0; $i < $displayRows; $i++) {

    if (isset($rows[$i])) {

        $row = $rows[$i];

        $date = !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : '';

        // default blank
        $cashAmt   = '';
        $bankAmt   = '';
        $bankLabel = '';

        if ($row['payment_mode'] === 'cash') {
            $cashAmt = number_format((float)$row['cash_amount'], 2);
        }

        if ($row['payment_mode'] === 'bank_transfer') {
            $bankAmt   = number_format((float)$row['transfer_amount'], 2);
            $bankLabel = $row['bank_name'];
        }

        if ($row['payment_mode'] === 'cheque') {
            $bankAmt   = number_format((float)$row['cheque_amount'], 2);
            $bankLabel = 'Cheque';
        }

        echo "<tr>
            <td>{$sn}</td>
            <td>{$date}</td>
            <td>{$row['pb_no']}</td>
            <td>{$row['dv_no']}</td>
            <td>{$row['r_name']}</td>
            <td>{$row['r_code']}</td>
            <td>" . ($row['r_desig'] ?? '') . "</td>
            <td>{$row['expense_type']}</td>
            <td class='text-end'>{$cashAmt}</td>
            <td class='text-center'>{$bankLabel}" .
                ($bankAmt !== '' ? " || {$bankAmt}" : "") .
            "</td>
            <td></td>
            <td>{$row['description']}</td>
        </tr>";

    } else {

        // Blank rows
        echo "<tr>
            <td>{$sn}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>";
    }

    $sn++;
}
?>
</tbody>


        </table>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF(){
    var element = document.getElementById('printArea');
    html2pdf().from(element).set({
        margin: 5,
        filename: 'cash_prize_register.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    }).save();
}
</script>

</body>
</html>
