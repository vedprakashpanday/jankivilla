<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Salary Slip Full Entry Form</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container my-4">
<div class="card shadow">
<div class="card-header bg-primary text-white">
<h5 class="mb-0">Salary Slip – Complete Entry Form</h5>
</div>

<div class="card-body">
<form action="generatesalary.php" method="get">

<!-- EMPLOYEE DETAILS -->
<h6 class="text-primary">Employee Details</h6>
<?php 
                                                                    $stmt = $pdo->prepare("SELECT ar.full_name,ar.designation,ar.designations,ar.member_id, ads.*, bd.*  FROM calc_salary ads left join adm_regist ar on ar.member_id=ads.staff_id 
                                                                    left join tbl_bank_details bd on ads.staff_id=bd.member_id 

                                                                    where ads.id=:id
                                                                    ");
                                                                $stmt->execute(['id'=>$_GET['id']]);
                                                                $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                
                                                                ?>



<div class="row">
<div class="col-md-3 mb-2">
<label>Employee Code</label>
<input type="text" name="emp_code" class="form-control" value="<?= $sponsor['staff_id'] ?>" readonly>
</div>
<div class="col-md-3 mb-2">
<label>Employee Name</label>
<input type="text" name="emp_name" class="form-control" value="<?= $sponsor['full_name'] ?>" required>
</div>
<div class="col-md-3 mb-2">
<label>Date of Joining</label>
<input type="date" name="doj" class="form-control" value="<?= $sponsor['doj']??"-" ?>">
</div>
<div class="col-md-3 mb-2">
<label>Service</label>
<input type="text" name="service" class="form-control" value="<?= $sponsor['service']??"-" ?>">
</div>
</div>

<div class="row">
<div class="col-md-3 mb-2">
<label>Designation</label>
<input type="text" name="designation" class="form-control"  value="<?= htmlspecialchars(json_decode($sponsor['designations'], true)[0]['designation'] ?? '-') ?>">
</div>
<div class="col-md-3 mb-2">
<label>Branch</label>
<input type="text" name="branch" class="form-control" value="<?= $sponsor['o_branch']??"-" ?>">
</div>
</div>

<hr>

<!-- BANK DETAILS -->
<h6 class="text-primary">Bank Details</h6>
<div class="row">
<div class="col-md-3 mb-2">
<label>Bank Name</label>
<input type="text" name="bank_name" class="form-control" value="<?= $sponsor['bank_name']??null ?>" >
</div>
<div class="col-md-3 mb-2">
<label>Account No</label>
<input type="text" name="account_no" class="form-control" value="<?= $sponsor['account_no']??null ?>">
</div>
<div class="col-md-3 mb-2">
<label>IFSC</label>
<input type="text" name="ifsc" class="form-control" value="<?= $sponsor['ifsc_code']??null ?>">
</div>
<div class="col-md-3 mb-2">
<label>Bank Branch</label>
<input type="text" name="bank_branch" class="form-control" value="<?= $sponsor['branch']??null ?>">
</div>
</div>

<hr>

<!-- ATTENDANCE -->
<h6 class="text-primary">Attendance Details</h6>
<div class="row">
    <?php
$paiddays= 30-(($sponsor['absent']-1)+($sponsor['half_day']*0.5));

?>
<div class="col-md-3 mb-2"><label>Total Days</label><input type="number" name="total_days" class="form-control" value="30"></div>
<div class="col-md-3 mb-2"><label>Paid Days</label><input type="number" name="paid_days" class="form-control" value="<?= $paiddays ?>"></div>
<div class="col-md-3 mb-2"><label>LWP</label><input type="number" name="lwp" class="form-control" value="<?= $sponsor['lwp']??"-" ?>"></div>
<div class="col-md-3 mb-2"><label>Overtime</label><input type="number" name="overtime" class="form-control" value="<?= $sponsor['overtime']??"-" ?>" ></div>
</div>

<div class="row">
<div class="col-md-3 mb-2"><label>Fine</label><input type="number" name="fine" class="form-control" value="<?= $sponsor['fine']??"-" ?>"></div>
<div class="col-md-3 mb-2"><label>Leave</label><input type="number" name="leave" class="form-control" value="<?= $sponsor['absent']??"-" ?>"></div>
<div class="col-md-3 mb-2"><label>Half Day</label><input type="number" name="half_day" class="form-control" value="<?= $sponsor['half_day']??"-" ?>"></div>
<div class="col-md-3 mb-2"><label>Total Leave</label><input type="number" name="total_leave" class="form-control" value="<?= ($sponsor['absent']-(1+$sponsor['overtime']))+($sponsor['half_day']*0.5)??"-" ?>"></div>
</div>

<hr>

<!-- EARNINGS -->
<h6 class="text-primary">Earning Details</h6>
<?php
$act_salary=$sponsor['actual_salary'];
$basic_pay=$act_salary*(0.4);
$hra=$basic_pay*(0.2);
$da=$basic_pay*(0.2);
$ma=$basic_pay*(0.2);
$oa=$act_salary-($basic_pay+$hra+$da+$ma);
// ================= REPAYMENT JSON SAFE LOAD =================
$repaymentTypes = json_decode($sponsor['repayment_type'] ?? '', true);
$repaymentDates = json_decode($sponsor['repayment_date'] ?? '', true);

// backward compatibility (old data)
if (!is_array($repaymentTypes)) {
    $repaymentTypes = $sponsor['repayment_type']
        ? [['type' => $sponsor['repayment_type']]]
        : [];
}

if (!is_array($repaymentDates)) {
    $repaymentDates = $sponsor['repayment_date']
        ? [['date' => $sponsor['repayment_date']]]
        : [];
}

// ================= BASIC VALUES =================
$pf    = (float)($sponsor['pf'] ?? 0);
$esi   = (float)($sponsor['esi'] ?? 0);
$other_deduction = (float)($sponsor['other_deduction'] ?? 0);

$loanamount = 0;
$totalloan  = 0;
$gross_cut  = $pf + $esi + $other_deduction;

$repay_type_label = '-';

// ================= PROCESS REPAYMENT =================
foreach ($repaymentTypes as $index => $rep) {

    $type = strtoupper($rep['type'] ?? '');

    // ===== FS =====
    if ($type === 'FS') {

        $repay_type_label = 'From Salary';

        $loanamount = (float)($sponsor['advance'] ?? 0);
        $totalloan  = (float)($sponsor['rem_due'] ?? 0);

        $gross_cut += $loanamount;
    }

    // ===== OD =====
    if ($type === 'OD') {

        $repay_type_label = 'On a Date';

        $loanamount = (float)($sponsor['advance'] ?? 0);
        $totalloan  = (float)($sponsor['rem_due'] ?? 0);

        // OD me salary se loan cut nahi hota
        // isliye gross_cut me add nahi
    }
}

// ================= OTHER VALUES =================
$sal_month = $sponsor['salary_month'];
$cut       = (float)($sponsor['cut'] ?? 0);
$total_cut = (float)$sponsor['actual_salary'] - (float)$sponsor['paid_salary'];
   
?>

<div class="row">
<div class="col-md-4 mb-2"><label>Basic Pay</label><input type="number" name="basic_pay" class="form-control" value="<?= $basic_pay??"-" ?>" readonly></div>
<div class="col-md-4 mb-2"><label>HRA</label><input type="number" name="hra" class="form-control" value="<?= $hra??"-" ?>" readonly></div>
<div class="col-md-4 mb-2"><label>DA</label><input type="number" name="da" class="form-control" value="<?= $da??"-" ?>" readonly></div>
</div>

<div class="row">
<div class="col-md-4 mb-2"><label>Medical Allowance</label><input type="number" name="medical" class="form-control" value="<?= $ma??"-" ?>" readonly></div>
<div class="col-md-4 mb-2"><label>Travel Allowance</label><input type="number" name="travel" class="form-control" value="0" readonly></div>
<div class="col-md-4 mb-2"><label>Other Allowance</label><input type="number" name="other_allowance" class="form-control" value="<?= $oa??"-" ?>" readonly></div>
</div>

<hr>

<!-- LOAN -->
<h6 class="text-primary">Advance / Loan Details</h6>
<div class="row">
<div class="col-md-3 mb-2"><label>Loan Type</label><input type="text" name="loan_type" class="form-control" value="<?= $repay_type_label??"-" ?>"></div>
<div class="col-md-3 mb-2"><label>D.V. No</label><input type="text" name="dv_no" class="form-control"></div>
<div class="col-md-3 mb-2"><label>Loan Amount</label><input type="number" name="loan_amt" class="form-control" value="<?= $loanamount??"-" ?>"></div>
<div class="col-md-3 mb-2"><label>Total Loan Amount</label><input type="number" name="total_loan_amt" class="form-control" value="<?= $totalloan ?? "0" ?>"></div>
</div>

<div class="row">
<div class="col-md-4 mb-2"><label>Deduction Month</label><input type="text" name="deduction_month" class="form-control" value="<?= $sal_month ?? "0" ?>"></div>
<div class="col-md-4 mb-2"><label>Deduction Amount</label><input type="number" name="deduction_amt" class="form-control" value="<?= $cut ?? "0" ?>"></div>
<div class="col-md-4 mb-2"><label>Total Deduction Amount</label><input type="number" name="total_deduction_amt" class="form-control" value="<?= $total_cut ?? "0" ?>"></div>
</div>

<hr>

<!-- DEDUCTION -->
<h6 class="text-primary">Deduction</h6>
<div class="row">
<div class="col-md-3 mb-2"><label>PF</label><input type="number" name="pf" class="form-control" value="<?= $pf ?? "0" ?>"></div>
<div class="col-md-3 mb-2"><label>ESI</label><input type="number" name="esi" class="form-control" value="<?= $esi ?? "0" ?>"></div>
<div class="col-md-3 mb-2"><label>Other Deduction</label><input type="number" name="other_deduction" class="form-control" value="<?= $other_deduction ?? "0" ?>"></div>
<div class="col-md-3 mb-2"><label>Advance / Loan</label><input type="number" name="advance_loan" class="form-control" value="<?= $loanamount ?? "0" ?>"></div>
</div>

<hr>

<!-- FINAL -->
<h6 class="text-primary">Final Amount</h6>
<div class="row">
<div class="col-md-3 mb-2"><label>Gross Earning</label><input type="number" name="gross_earning" class="form-control" value="<?= $act_salary??"-" ?>" ></div>
<div class="col-md-3 mb-2"><label>Gross Deduction</label><input type="number" name="gross_deduction" class="form-control" value="<?= $gross_cut ?? "0" ?>"></div>
<div class="col-md-3 mb-2">
    <label>Net Pay</label>
    <input type="number" name="net_pay" id="netPay" class="form-control"
           value="<?= $sponsor['paid_salary'] ?? '' ?>">
</div>

<div class="col-md-3 mb-2">
    <label>Rupees In Words</label>
    <input type="text" name="rupees_words" id="rupeesWords" class="form-control" readonly>
</div>

</div>

<div class="text-end mt-3">
<button class="btn btn-success">Generate Salary Slip</button>
</div>

</form>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/number-to-words@1.2.4/dist/numberToWords.min.js"></script>

<script>
function numberToWordsIndian(num) {

    num = Math.floor(num); // remove decimals

    if (num === 0) return 'Zero only';

    const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
        'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty',
        'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    function twoDigits(n) {
        return n < 20 ? ones[n] : tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
    }

    function threeDigits(n) {
        let str = '';
        if (n >= 100) {
            str += ones[Math.floor(n / 100)] + ' Hundred';
            n = n % 100;
            if (n > 0) str += ' ';
        }
        if (n > 0) str += twoDigits(n);
        return str;
    }

    let result = '';
    let crore = Math.floor(num / 10000000);
    let lakh = Math.floor((num / 100000) % 100);
    let thousand = Math.floor((num / 1000) % 100);
    let hundred = num % 1000;

    if (crore) result += twoDigits(crore) + ' Crore ';
    if (lakh) result += twoDigits(lakh) + ' Lakh ';
    if (thousand) result += twoDigits(thousand) + ' Thousand ';
    if (hundred) result += threeDigits(hundred);

    return result.trim() + ' only';
}
</script>

<script>
$(function () {

    function updateRupeesWords() {
        let netPay = parseInt($('#netPay').val());

        if (!isNaN(netPay)) {
            $('#rupeesWords').val(numberToWordsIndian(netPay));
        } else {
            $('#rupeesWords').val('');
        }
    }

    updateRupeesWords();
    $('#netPay').on('input change', updateRupeesWords);
});
</script>

</body>
</html>
