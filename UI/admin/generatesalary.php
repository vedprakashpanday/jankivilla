<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-size: 13px;
        }

        /* ===== HEADER FIX ===== */
.header-wrap{
    display: flex;
    align-items: center;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 10px;
}

.header-logo{
    width: 20%;
    text-align: left;
}

.header-text{
    width: 75%;
    text-align: center;
}

.company-title{
    font-size: 22px;
    font-weight: 700;
    margin: 0;
}

.cin-text{
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
}

.small-text{
    font-size: 12px;
    line-height: 1.4;
}




        .table-bordered td,
        .table-bordered th {
            border: 1px solid #000 !important;
        }

        .company-title {
            font-weight: 700;
            font-size: 30px;
        }

        .small-text {
            font-size: 14px;
        }
.logo-image{
    width: 20rem; /* screen ke liye normal */
}

/* sirf PDF ke liye */

        /* ===== OUTER TABLE CLEAN LOOK ===== */
.salary-split-table {
    border: 0 !important;
}

.salary-split-table > tbody > tr > td {
    border: 0 !important;
    padding: 0 6px !important; /* thoda gap between two tables */
}

/* ===== INNER TABLES CLEAR & SIMPLE ===== */
.salary-split-table table {
    margin-bottom: 0 !important;
}
        

       @media print {

       .company-title{
        font-size: 15px !important;
    }

    .small-text{
        font-size: 10px !important;
    }

       .logo-image{
         width: 9rem !important;
       }
           .salary-split-table,
    .salary-split-table td {
        border: 0 !important;
    }

    .salary-split-table table {
        page-break-inside: avoid !important;
    }

    @page {
        size: A4 portrait;
        margin: 10mm;
    }
      /* table {
        page-break-inside: avoid !important;
    }
    td {
        page-break-inside: avoid !important;
    } */

    body {
        font-size: 13px;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    

    table {
        margin-bottom: 4px !important;
        page-break-inside: avoid !important;
    }

    tr, td, th {
        padding: 4px !important;
        line-height: 1.5 !important;
        page-break-inside: avoid !important;
    }

    .row {
        margin: 0 !important;
        page-break-inside: avoid !important;
    }

    #salarySlip {
        page-break-inside: avoid !important;
    }

    .company-title {
        font-size: 15px !important;
    }

    .small-text {
        font-size: 10px !important;
    }

    .no-print {
        display: none !important;
    }
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
    </style>
</head>

<body>

    <div class="container my-3">

        <div class="text-end mb-2 no-print">
            <button class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
            <button class="btn btn-danger btn-sm" onclick="downloadPDF()">Download PDF</button>
        </div>

        <div id="salarySlip" class="border p-3">
<div class="watermark"><img id="Img" src="../../image/harihomes1-logo.png" class="mr-2" /></div>

<div class="header-wrap">
    <div class="header-logo">
        <img src="../../image/harihomes1-logo.png" class="logo-image">
    </div>

    <div class="header-text">
        <div class="company-title">
            AMITABH BUILDERS AND DEVELOPERS PVT LTD
        </div>

        <div class="cin-text">
            CIN NO. : U24299BR2024PTC072712
        </div>

        <div class="small-text">
            <b>Head Office :</b>
            1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk,
            Bhuskaul, Darbhanga-846007
        </div>

        <div class="small-text">
            <b>Branch Office :</b>
            Near Jha Indian Petrol Pump, Mohana Chowk,
            Jhanjharpur, Madhubani, Pin-847404 (Bihar)
        </div>

        <!-- ❌ Icons hata diye (PDF issue) -->
        <div class="small-text">
            Phone : <b>9060218 / 222 / 333 / 666</b> |
            WhatsApp : <b>9472467007</b> |
            Website : <b>www.jankivilla.com</b>
        </div>
    </div>
</div>

            <table class="table table-bordered mb-2">
                <tr>
                    <td><b>Employee Code</b></td>
                    <td><?= $_GET['emp_code'] ?? '' ?></td>
                    <td><b>DOJ</b></td>
                    <td><?= $doj ?? '' ?></td>
                    <td><b>Service</b></td>
                    <td><?= $_GET['service'] ?? '' ?></td>
                </tr>
                <tr>
                    <td><b>Employee Name</b></td>
                    <td><?= $_GET['emp_name'] ?? '' ?></td>
                    <td><b>Designation</b></td>
                    <td><?= $_GET['designation'] ?? '' ?></td>
                    <td><b>Branch</b></td>
                    <td><?= $_GET['branch'] ?? '' ?></td>
                </tr>
            </table>

            <table class="table table-bordered mb-2">
                <tr class="text-center fw-bold">
                    <td colspan="4">BANK DETAILS</td>
                </tr>
                <tr>
                    <td><b>Bank Name</b></td>
                    <td><?= $_GET['bank_name'] ?? '' ?></td>
                    <td><b>Account No</b></td>
                    <td><?= $_GET['account_no'] ?? '' ?></td>
                </tr>
                <tr>
                    <td><b>IFSC</b></td>
                    <td><?= $_GET['ifsc'] ?? '' ?></td>
                    <td><b>Branch</b></td>
                    <td><?= $_GET['bank_branch'] ?? '' ?></td>
                </tr>
            </table>

            <table class="table table-bordered mb-2 text-center">
                <tr class="fw-bold">
                    <td>Total Days</td>
                    <td>Paid Days</td>
                    <td>LWP</td>
                    <td>Overtime</td>
                    <td>Fine</td>
                    <td>Leave</td>
                    <td>Half Day</td>
                    <td>Total Leave</td>
                </tr>
                <tr>
                    <td><?= $_GET['total_days'] ?? '' ?></td>
                    <td><?= $_GET['paid_days'] ?? '' ?></td>
                    <td><?= $_GET['lwp'] ?? '' ?></td>
                    <td><?= $_GET['overtime'] ?? '' ?></td>
                    <td><?= $_GET['fine'] ?? '' ?></td>
                    <td><?= $_GET['leave'] ?? '' ?></td>
                    <td><?= $_GET['half_day'] ?? '' ?></td>
                    <td><?= $_GET['total_leave'] ?? '' ?></td>
                </tr>
            </table>


          <table class="table  mb-2 b-0" style="width:100%; table-layout:fixed;">
    <tr>
        <!-- EARNINGS -->
        <td style="width:50%; vertical-align:top; padding:0;">
            <table class="table table-bordered mb-0">
                <tr class="fw-bold text-center">
                    <td colspan="2">EARNINGS</td>
                </tr>
                <tr>
                    <td>Basic Pay</td>
                    <td class="text-end"><?= $_GET['basic_pay'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>HRA</td>
                    <td class="text-end"><?= $_GET['hra'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>DA</td>
                    <td class="text-end"><?= $_GET['da'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>Medical Allowance</td>
                    <td class="text-end"><?= $_GET['medical'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>Travel Allowance</td>
                    <td class="text-end"><?= $_GET['travel'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>Other Allowance</td>
                    <td class="text-end"><?= $_GET['other_allowance'] ?? '' ?></td>
                </tr>
                <tr class="fw-bold">
                    <td>Gross Earning</td>
                    <td class="text-end"><?= $_GET['gross_earning'] ?? '' ?></td>
                </tr>
            </table>
        </td>

        <!-- DEDUCTIONS -->
        <td style="width:50%; vertical-align:top; padding:0;">
            <table class="table table-bordered mb-0">
                <tr class="fw-bold text-center">
                    <td colspan="2">DEDUCTIONS</td>
                </tr>
                <tr>
                    <td>PF</td>
                    <td class="text-end"><?= $_GET['pf'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>ESI</td>
                    <td class="text-end"><?= $_GET['esi'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>Other Deduction</td>
                    <td class="text-end"><?= $_GET['other_deduction'] ?? '' ?></td>
                </tr>
                <tr>
                    <td>Advance / Loan</td>
                    <td class="text-end"><?= $_GET['advance_loan'] ?? '' ?></td>
                </tr>
                <tr class="fw-bold">
                    <td>Gross Deduction</td>
                    <td class="text-end"><?= $_GET['gross_deduction'] ?? '' ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>


            <table class="table table-bordered mb-2">
                <tr class="fw-bold text-center">
                    <td colspan="8">ADVANCE / LOAN BALANCE</td>
                </tr>
                <tr class="fw-bold text-center small">
                    <td>LOAN TYPE</td>
                    <td>D.V. NO.</td>
                    <td>LOAN AMT</td>
                    <td>TOTAL LOAN AMT</td>
                    <td>DEDUCTION MONTH</td>
                    <td>DEDUCTION AMT</td>
                    <td>TOTAL DEDUC. AMT</td>
                    <td>BALANCE LOAN AMT</td>
                </tr>
                <tr class="text-center">
                    <td><?= $_GET['loan_type'] ?? '' ?></td>
                    <td><?= $_GET['dv_no'] ?? '' ?></td>
                    <td><?= $_GET['loan_amt'] ?? '' ?></td>
                    <td><?= $_GET['total_loan_amt'] ?? '' ?></td>
                    <td><?= $_GET['deduction_month'] ?? '' ?></td>
                    <td><?= $_GET['deduction_amt'] ?? '' ?></td>
                    <td><?= $_GET['total_deduction_amt'] ?? '' ?></td>
                    <td><?= $_GET['total_loan_amt'] ?? '' ?></td>
                </tr>
            </table>

            <table class="table table-bordered">
                <tr>
                    <td><b>Rupees in Words</b></td>
                    <td colspan="3"><?= $_GET['rupees_words'] ?? '' ?></td>
                </tr>
                <tr class="fw-bold">
                    <td>NET PAY</td>
                    <td colspan="3">₹ <?= $_GET['net_pay'] ?? '' ?></td>
                </tr>
            </table>

            <table class="table text-end">
                <tr>
                    <td>
                        <div class="col-12 footer-box  mt-3 pt-5">
            <strong>Authorized Sign</strong>
            <div class="sign-title"><strong>Chairman & Managing Director</strong></div>
        </div>
    </td>
                    
                </tr>                
            </table>

        </div>
    </div>

    <script>
  const deductionMonth = "<?= $_GET['deduction_month'] ?? '' ?>";
</script>


<script>
function getMonthName(month){
    const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    return months[month - 1] || '';
}
</script>


   <script>
function downloadPDF(){

    const element = document.getElementById('salarySlip');

    /* 🔥 PDF ke liye logo force resize */
    const logos = element.querySelectorAll('.logo-image');
    logos.forEach(img => {
        img.dataset.oldWidth = img.style.width;
        img.style.width = '10rem';
    });

    let month = "<?= $_GET['deduction_month'] ?? '' ?>";
    if(!isNaN(month)){
        const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        month = months[month-1] || '';
    }

    const filename = month ? `salary-slip-${month}.pdf` : 'salary-slip.pdf';

    html2pdf().set({
        margin: 4,
        filename: filename,
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(element).save().then(() => {

        /* 🔄 Screen ke liye original width wapas */
        logos.forEach(img => {
            img.style.width = img.dataset.oldWidth || '20rem';
        });

    });
}
</script>

</body>

</html>