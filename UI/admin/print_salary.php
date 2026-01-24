<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Sheet</title>

    <!-- Bootstrap (only CSS needed for PDF) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-size: 13px;
        }

        .company-header {
            background-color: #c7d6ee;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border: 1px solid #000;
        }

        .office-address {
            background-color: #f4e3d7;
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
        }

        .salary-title {
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 6px;
        }

        table {
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        .text-left {
            text-align: left;
        }

        .footer-box {
            height: 80px;
            border: 1px solid #000;
            text-align: center;
            padding-top: 35px;
            font-weight: bold;
        }

        .sign-title {
            margin-top: 5px;
            font-weight: normal;
        }

        @media print {
    .no-print {
        display: none !important;
    }

    body {
        margin: 0;
    }

    table {
        page-break-inside: avoid;
    }
}

    </style>
</head>
<body>
<div class="text-end p-2 no-print">
    <button onclick="window.print()" class="btn btn-primary">
        🖨️ Print Salary Sheet
    </button>
</div>
<div class="container-fluid p-0">

    <!-- HEADER -->
    <div class="company-header">
        AMITABH BUILDERS & DEVELOPERS PVT. LTD.<br>
        CIN NO.-U43299BR2024PTC072712
    </div>

    <div class="office-address">
        OFFICE ADD. - 1st FLOOR, PAPPU YADAV BUILDING, KAKARGHATTI CHOWK,
        BHUSKAUL, DARBHANGA, (BIHAR) 846007
    </div>

    <div class="salary-title">
        SALARY SHEET FOR THE MONTH OF

        <?php
$months= $_GET['month'] ?? 0;
$year = $_GET['years'] ?? 0;
switch ($months) {
    case '01':
       $s_month='january';
        break;

        case '02':
       $s_month='february';
        break;
        case '03':
       $s_month='march';
        break;
        case '04':
       $s_month='april';
        break;
        case '05':
       $s_month='may';
        break;
        case '06':
       $s_month='june';
        break;
        case '07':
       $s_month='july';
        break;
        case '08':
       $s_month='august';
        break;
        case '09':
       $s_month='september';
        break;
        case '10':
       $s_month='october';
        break;
        case '11':
       $s_month='november';
        break;
        case '12':
       $s_month='december';
        break;
    
    default:
        $s_month='invalid month selection';
        break;
}
?>

        <strong class="text-uppercase"><?php echo $s_month; ?>  <?php echo $year; ?></strong>
    </div>

    <!-- TABLE -->
    <table class="table table-bordered w-100">
        <thead>
            <tr>
                <th>SL.NO.</th>
                <th>Employee Name</th>
                <th>Employee Code</th>
                <th>Designation</th>
                <th>Salary (₹)</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Earned Salary (₹)</th>
                <th>Advance (₹)</th>
                <th>Recovery (₹)</th>
                <th>Net Advance (₹)</th>
                <th>Net Pay (₹)</th>
                <th>Remark's</th>
            </tr>
        </thead>

        <tbody>
            <!-- LOOP FROM BACKEND -->
            <?php 
            $month=$_GET['month'];
            $year=$_GET['years'];
            $date= $year. "-" .$month. "-01";

            $t_salary=0;
            $t_earn=0;
            $t_advance=0;
            $t_recover=0;
            $t_net_advance=0;
            $t_net_pay=0;
            $absent=0;
            $half_day=0;

                                                        $stmt = $pdo->prepare("
    SELECT 
        ar.full_name,
        ar.designation,
        ar.member_id,

        ads.salary_month,

        MAX(ads.actual_salary)   AS actual_salary,
        MAX(ads.absent)          AS absent,
        MAX(ads.half_day)        AS half_day,
        MAX(ads.paid_salary)     AS paid_salary,

        SUM(ads.advance)         AS advance,   -- ✔ multiple advances
        MAX(ads.cut)             AS cut,       -- ✔ single recovery
        MAX(ads.rem_due)         AS rem_due,   -- ✔ remaining due

        MAX(ads.remarks)         AS remarks

    FROM adm_regist ar
    LEFT JOIN calc_salary ads 
        ON ar.member_id = ads.staff_id

    WHERE ads.salary_month = :id

    GROUP BY ar.member_id, ads.salary_month
");
$stmt->execute([
    ":id" => $date
]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                $i=1;
                                                               
                                                                // echo "<pre>";
                                                                // print_r($rows);
                                                                //  print_r($year);
                                                                // echo "</pre>";
                                                                // exit();

                                                                
                                                                foreach($rows as $row):
                                                                    if($row['half_day']>=1)
                                                                    {
                                                                    $half_day=($row['half_day']*0.5);
                                                                    }
                                                                    if($row['absent']>=1)
                                                                    {
                                                                    $absent=$row['absent']-1;
                                                                    }
                                                                    $present=30-($absent+$half_day);
                                                                    $total_absent=$absent+$half_day;
                                                                     $perDay    = $row['actual_salary'] / 30;
                                                                     $p=$present;
                                                                    $earned = round($perDay * $p, 2);

                                                                    $t_salary += $row['actual_salary'];
                                                                    $t_earn += $earned;
                                                                    $t_advance += $row['advance'];
                                                                    $t_recover += $row['cut'];
                                                                    $t_net_advance += $row['rem_due'];
                                                                    
                                                                    $netpay=$row['paid_salary'];
                                                                    $t_net_pay += $netpay;
                                                                    if($netpay>=0):
                                                                ?>
            <tr>
                <td><?= $i++ ?></td>
                 <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['member_id']) ?></td>
                <td><?= htmlspecialchars($row['designation']) ?></td>
               <td><?= htmlspecialchars($row['actual_salary'] ?? '') ?></td>
                <td><?= $present ?></td>
                <td><?=  $total_absent ?></td>
                 <td><?=  $earned ?></td>
                <td><?= htmlspecialchars($row['advance'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['cut'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['rem_due'] ?? '') ?></td>              
               <td><?=  $netpay ?></td> 
                <td><?= htmlspecialchars($row['remarks'] ?? '') ?></td>
            </tr>
          
        </tbody>

       <?php endif; endforeach; ?>
        <!-- TOTAL ROW -->
        <tfoot>
            <tr>
                <th colspan="4">Total Monthly Salary</th>
                <th><?php echo $t_salary; ?></th>
                <th></th>
                <th></th>
                <th><?php echo $t_earn; ?></th>
                <th><?php echo $t_advance; ?></th>
                <th><?php echo $t_recover; ?></th>
                <th><?php echo $t_net_advance; ?></th>
                <th><?php echo $t_net_pay; ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <!-- FOOTER SIGNATURE -->
    <div class="row m-0">
        <div class="col-3 footer-box">
            Prepared By
            <div class="sign-title">Accountant</div>
        </div>
        <div class="col-3 footer-box">
            Checked By
            <div class="sign-title">Accounts Officer</div>
        </div>
        <div class="col-3 footer-box">
            Approved By
            <div class="sign-title">Branch Manager</div>
        </div>
        <div class="col-3 footer-box">
            Authorized Sign
            <div class="sign-title">Chairman & Managing Director</div>
        </div>
    </div>

</div>

</body>
</html>
