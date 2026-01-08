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



// if (isset($_POST['btnsubmit'])) {


//     $m_id           = trim($_POST['designation']);    
//     $mem_salary       = trim($_POST['salary']);        // Full Name
    
   

//     // Insert into tbl_regist
//     $sql = "INSERT INTO adm_salary 
//             (staff_id, salary)
//             VALUES 
//             ( ?, ?)";

//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([
        
//         $m_id,
//         $mem_salary
       
//     ]);

   

//     echo "<script>
//         if(confirm('Success!\\nID: {$row['sponsor_id']}\\nPass: {$row['sponsor_pass']}')){
//             location='adm_salary.php';
//         }
//     </script>";
//     exit;
// }

if (isset($_POST['update'])) {

    // Safe inputs
    $row_id = trim($_POST['row']);
    $staff_id = trim($_POST['designation']);    
    $due_amount   = (float) $_POST['salary']; 

    // Update due amount
    $sql = "UPDATE calc_salary
            SET rem_due = :rem_due
            WHERE staff_id = :staff_id and id = :row_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'rem_due'   => $due_amount,
        'staff_id'  => $staff_id,
        'row_id'    => $row_id
    ]);

    header("Location: calc_salary.php");
    exit;

}

// Handle update request
if (isset($_POST['btnsubmit'])) {

    // Safe inputs
    $staff_id = trim($_POST['designation']);    
    $absent   = (int) $_POST['absent']; 
    $half_day = (int) $_POST['half_day'];
    $month1    = (int) $_POST['month'];
    $year     = (int) $_POST['year'];
    $cmonth   = date('m');
    $recover= $_POST['recovery'];
    $remark= $_POST['remarks'];

    $smonth = sprintf('%04d-%02d-01', $year, $month1);
    $month = sprintf('%02d',$month1);
    // Total days in month  cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysInMonth = 30;



if ($month == $cmonth) {
    // current month → month + latest record
  $sql = "
    SELECT ar.salary, ads.*
    FROM adm_salary ar
    LEFT JOIN calc_salary ads 
        ON ar.staff_id = ads.staff_id 
        AND ads.salary_month = :salary_month
    WHERE ar.staff_id = :staff_id
    ORDER BY ads.id DESC
    LIMIT 1
";


    $stmt = $pdo->prepare($sql);
$stmt->execute([
    ':staff_id'      => $staff_id,
    ':salary_month' => $smonth
]);

} else {
    // previous month → normal fetch
    $sql = "
    SELECT ar.salary, ads.*
    FROM adm_salary ar
    LEFT JOIN calc_salary ads 
        ON ar.staff_id = ads.staff_id
    WHERE ar.staff_id = :staff_id
    ORDER BY ads.id DESC
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':staff_id' => $staff_id
]);

}



$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo $staff_id;
    die('Salary not found for staff');
}


    $basicSalary = (float) $row['salary'];
    $actualSalary = $basicSalary;
    $dueAmount   = isset($row['rem_due']) ? (float) $row['rem_due'] : 0;
    $cutAmount   = isset($recover) ? (float) $recover : 0;
    $advance_amount = isset($row['advance']) ? (float) $row['advance'] : 0;
    $advance_date = isset($row['advance_date']) ? $row['advance_date'] : null;
    $repayment_type = isset($row['repayment_type']) ? $row['repayment_type'] : null;
    $repayment_date = isset($row['repayment_date']) ? $row['repayment_date'] : null;
    // ---- Salary Calculation ----


    if($dueAmount > 0 && $repayment_date == null){
        if($dueAmount > $cutAmount){
            $cutAmount = $cutAmount;
        } else {
            $cutAmount = $dueAmount;
        }
       
        $dueAmount -= $cutAmount;

      
    }

    if($advance_amount>0)
    {
        $advance_amount = 0;
    }

    // if($dueAmount == 0)
    // {

    // }

    if($absent>0){
    $paidDays  = max(0, $daysInMonth - ($absent-1));   // negative avoid
    } else {
        $paidDays  = $daysInMonth;
    }

    if($half_day > 0){
        $paidDays -= ($half_day * 0.5);
    }
    
    $perDay    = $basicSalary / $daysInMonth;
    $paidSalary = round($perDay * $paidDays, 2);

     if($dueAmount > 0  && $repayment_date == null){
        if($dueAmount > $cutAmount){
            $cutAmount = $cutAmount;
        } else {
            $cutAmount = $dueAmount;
        }
       
        $paidSalary -= $cutAmount;
        //print_r($cutAmount);
      
    }

      if($dueAmount == 0 && $cutAmount > 0 && $repayment_date == null){
       
       
        $paidSalary -= $cutAmount;
        //print_r($cutAmount);
      
    }

//     echo "<pre>";
//     print_r($cmonth);
//     echo "<br>";
//     print_r($absent);
//     echo "<br>";
//     print_r($paidDays);
//     echo "<br>";
//     print_r($month);
//     echo "<br>";
//     print_r($smonth);
//     echo "<br>";
//     print_r($perDay);
// echo "<br>";
// print_r($row['salary_month']);
// echo "<br>";
// print_r($perDay * $paidDays);
// echo "<br>";
// print_r($dueAmount);
// echo "<br>";
// print_r($cutAmount);
// echo "<br>";
// print_r($paidSalary);
// echo "<br>";
// print_r($row);
// echo "</pre>";

// exit();

$repay += $cutAmount;


    if (!empty($row['salary_month']) && $row['salary_month'] == $smonth) {

    // UPDATE
    $sql = "UPDATE calc_salary
            SET half_day = :half_day,
                absent = :absent,
                paid_salary = :paid_salary,
                rem_due = :rem_due,
                created_at = NOW(),
                total_repay= :t_repay,
                remarks= :remarks
            WHERE salary_month = :salary_month
              AND staff_id = :staff_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'half_day'     => $half_day,
        'absent'       => $absent,
        'paid_salary'  => $paidSalary,
        'salary_month' => $smonth,
        'rem_due'      => $dueAmount,
        'staff_id'     => $staff_id,
        ':t_repay' => $repay,
        ':remarks' => $remark
    ]);
//     echo "Affected Rows: " . $stmt->rowCount();
// exit;

} else {


    // INSERT
    $stmt = $pdo->prepare("
        INSERT INTO calc_salary
        (staff_id, salary_month, absent, actual_salary, paid_salary,
         created_at, half_day, advance, cut, rem_due,
         advance_date, repayment_type, repayment_date,total_repay,remarks)
        VALUES
        (:staff_id, :salary_month, :absent, :actual_salary, :paid_salary,
         NOW(), :half_day, :advance, :cut, :rem_due,
         :advance_date, :repayment_type, :repayment_date,:t_repay,:remarks)
    ");

    $stmt->execute([
        'staff_id'        => $staff_id,
        'salary_month'    => $smonth,
        'absent'          => $absent,
        'actual_salary'   => $actualSalary,
        'paid_salary'     => $paidSalary,
        'half_day'        => $half_day,
        'advance'         => $advance_amount,
        'cut'             => $cutAmount,
        'rem_due'         => $dueAmount,
        'advance_date'    => $advance_date,
        'repayment_type'  => $repayment_type,
        'repayment_date'  => $repayment_date,
        ':t_repay' => $repay,
        ':remarks' => $remark
    ]);
}

   
    
    header("Location: calc_salary.php");
    exit;
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Amitabh Builders & Developers
    </title>
  <link rel="shortcut icon" type="image/x-icon" href="../../icon/harihomes1-fevicon.png">
    <link rel="stylesheet" href="../resources/vendors/feather/feather.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../resources/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="../resources/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link rel="stylesheet" href="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../resources/js/select.dataTables.min.css">
    <link rel="stylesheet" href="../resources/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../resources/vendors/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" href="../resources/css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link href="assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link href="../assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script>
        function display_ct7() {
            var x = new Date();
            var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
            var hours = x.getHours() % 12;
            hours = hours ? hours : 12;
            hours = hours.toString().length == 1 ? '0' + hours.toString() : hours;

            var minutes = x.getMinutes().toString();
            minutes = minutes.length == 1 ? '0' + minutes : minutes;

            var seconds = x.getSeconds().toString();
            seconds = seconds.length == 1 ? '0' + seconds : seconds;

            var month = (x.getMonth() + 1).toString();
            month = month.length == 1 ? '0' + month : month;

            var dt = x.getDate().toString();
            dt = dt.length == 1 ? '0' + dt : dt;

            var x1 = dt + "-" + month + "-" + x.getFullYear();
            x1 = x1 + " " + hours + ":" + minutes + ":" + seconds + " " + ampm;
            document.getElementById('ct7').innerHTML = x1;
        }

        function startTime() {
            display_ct7();
            setInterval(display_ct7, 1000);
        }

        window.onload = startTime;
    </script>


    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css">
    <script src="../js/jquery-1.8.2.js" type="text/javascript"></script>
    <script src="../js/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(function() {
            var date = new Date();
            var currentMonth = date.getMonth();
            var currentDate = date.getDate();
            var currentYear = date.getFullYear();

            jQuery("#").datepicker({
                dateFormat: 'dd/mm/yy',
                maxDate: new Date(currentYear - 18, currentMonth, currentDate),
                changeMonth: true,
                changeYear: true
            });
        });
    </script>

</head>

<body class="hold-transition skin-blue sidebar-mini">


    <div class="wrapper">
        <div class="container-scroller">
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">


                <?php include 'adminheadersidepanel.php'; ?>


                <div class="main-panel">
                    <style>
                        .col-md-4 {
                            padding: 1rem;
                        }

                        .form-control {
                            margin-top: 7px;
                        }
                    </style>
                    <div class="">
                        <div class="">
                            <div class="card">
                                <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <form method="post" action="" id="form1" enctype="multipart/form-data" style="margin-bottom: 10px;">
                                                <h2>Calculate Salary</h2>
                                                    <hr>
                                                <div class="col-12" style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f; display:flex; flex-wrap:wrap; justify-content:center; border-radius:10px;">
                                                    

                                               
                                                <div class="col-12 col-md-3 mb-3">
                                                     <label><b>Select Employee</b></label>
                                                        <input 
                                                            type="text" 
                                                            name="designation" 
                                                            id="designation" 
                                                            class="form-control " 
                                                            list="designationList"
                                                            placeholder="-- Select / Type Designation --"
                                                            required
                                                        >

                                                        <datalist id="designationList">
                                                              <?php 
                                                                    $stmt = $pdo->prepare("SELECT full_name,designation,member_id FROM adm_regist");
                                                                $stmt->execute();
                                                                $sponsor = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                foreach($sponsor as $row):
                                                                ?>
                                                             <option value="<?= htmlspecialchars($row['member_id']) ?>">
                                                                <?= htmlspecialchars($row['full_name']) ?> — <?= htmlspecialchars($row['designation']) ?>
                                                                 
                                                            </option>
                                                                   <hr style="width: 3px;">
                                                                 <?php endforeach; ?>
                                                        </datalist>
                                                  
                                                        </div>

                                                 <div class="col-12 col-md-3 mb-3">
    <label><b>Select Month</b></label>
    <select name="month" id="month" class="form-control" required>
        <option value="">-- Select Month --</option>
        <option value="1">January</option>
        <option value="2">February</option>
        <option value="3">March</option>
        <option value="4">April</option>
        <option value="5">May</option>
        <option value="6">June</option>
        <option value="7">July</option>
        <option value="8">August</option>
        <option value="9">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
</div>

   <div class="col-12 col-md-3">
    <label><b>Select Year</b></label>
    <select name="year" id="year" class="form-control" required>
        <option value="">-- Select Year --</option>

        <?php
        $currentYear = date('Y'); // eg: 2026
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            ?>
            <option value="<?= $year ?>" <?= ($year == $currentYear) ? 'selected' : '' ?>>
                <?= $year ?>
            </option>
            <?php
        }
        ?>
    </select>
</div>

                                                <div class="col-12 col-md-3 mb-3">
                                                    <label><b>Absent</b></label>
                                                    <input type="text" name="absent" id="absent" class="form-control" placeholder="Enter Absent Days" required>
                                                    
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <label><b>Half Day</b></label>
                                                    <input type="text" name="half_day" id="half_day" class="form-control" placeholder="Enter Half Day Count" required>
                                                    
                                                </div>
                                                  <div class="col-12 col-md-3 mb-3 ">
                                                    <label><b>Recovery</b></label>
                                                    <input type="text" name="recovery" id="recovery" class="form-control" placeholder="Enter Recovery Amount" required>
                                                    
                                                </div>
                                                  <div class="col-12 col-md-3 mb-3">
                                                    <label><b>Remarks if any</b></label>
                                                    <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Enter Remarks" >
                                                    
                                                </div>
                                                <div class="col-12 col-md-3 mb-3">
                                                    <input type="submit" value="Calculate" class="form-control btn btn-primary" name="btnsubmit" style="margin-top:32px;">
                                                </div>

                                                    </div>
                                            </form>

                                            <h2 style="margin-top: 30px;">Edit Salary</h2>
                                                    <hr>
                                                <div class="col-12" style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f; display:flex; flex-wrap:wrap; justify-content:center; border-radius:10px;">
                                                   

                                                <div class="container mt-4 overflow-auto">
    <table id="staffTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="d-none">sl. No</th>
                <th>Staff ID</th>
                <th>Staff Name</th>
                <th>Staff Designation</th>
                <th>Basic Salary</th>
                <th>Salary month</th>
                <th>Advance Amount</th>
                <th>Advance Date</th>
                <th>Repayment Type</th>
                <th>Repayment Date</th>
                <th>Due Amount</th>
                <th>Recovery</th>
                <th>Total Repayment</th>
                <th>Half Day</th>
                <th>Absent</th>
                <th>Paid Salary</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             <?php 
                                                                    $stmt = $pdo->prepare("SELECT ar.full_name,ar.designation,ar.member_id, ads.* FROM adm_regist ar left join calc_salary ads on ar.member_id=ads.staff_id");
                                                                $stmt->execute();
                                                                $sponsor = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                foreach($sponsor as $row):
                                                                ?>
                                                                <?php  if($row['paid_salary']>0): ?>
            <tr>
                <td class="d-none"><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['member_id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['designation']) ?></td>
                <td><?= htmlspecialchars($row['actual_salary'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['salary_month'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['advance'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['advance_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['repayment_type'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['repayment_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['rem_due'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['cut'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['total_repay'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['half_day'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['absent'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['paid_salary'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['remarks'] ?? '') ?></td>
                <td>
                    <input type="submit" class="btn btn-sm btn-primary editBtn" name="edit" value="Edit" />
        

                </td>
               
            </tr>
             <?php  endif; ?>
           <?php endforeach; ?>
                    
                    
                    <!-- <input type="submit" class="btn btn-sm btn-primary editBtn" name="edit" value="Edit" /> -->
                </td>
            </tr>
        </tbody>
    </table>
</div>

                                                    </div>
                                                    <!-- //end -->
  <h2 style="margin-top: 30px;">Print Salary Sheet</h2>
                                                    <hr>
                                                <div class="col-12 d-flex align-items-end " style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f;  border-radius:10px;">

    <div class="col-12 col-md-3">
    <label><b>Select Month</b></label>
    <select name="month" id="months" class="form-control" required>
        <option value="">-- Select Month --</option>
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    </div>

    <div class="col-12 col-md-3">
    <label><b>Select Year</b></label>
    <select name="years" id="years" class="form-control" required>
        <option value="">-- Select Year --</option>

        <?php
        $currentYear = date('Y'); // eg: 2026
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            ?>
            <option value="<?= $year ?>" <?= ($year == $currentYear) ? 'selected' : '' ?>>
                <?= $year ?>
            </option>
            <?php
        }
        ?>
    </select>
</div>


    <div class="col-12 col-md-3">
        <button type="button" onclick="printSalary()" class="form-control btn btn-success">
            Print
        </button>
    </div>                              

                                                    </div>
                                        </div>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Update Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
<form method="post" action="">
            <div class="modal-body">
                <div class="mb-3">
                    <label>Staff ID</label>
                    <input type="text" id="modal_staff_id" class="form-control" name="designation" readonly>
                    <input type="hidden" id="modal_row_id" class="form-control" name="row">
                </div>

                <div class="mb-3">
                    <label>Due Amount</label>
                    <input type="number" id="modal_salary" class="form-control" name="salary" required>
                </div>


            </div>

            <div class="modal-footer">
                
                <button class="btn btn-success"  type="submit" name="update">Update</button>
                
            </div>
</form>
        </div>
    </div>
</div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'adminfooter.php'; ?>
                </div>


            </div>
            <a href="#" target="_blank">
                <!-- partial -->
            </a>
                 <!-- search box for options-->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

                <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
                <!-- endinject -->
                <!-- Plugin js for this page -->
                <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
                <script src="../resources/vendors/select2/select2.min.js"></script>
                <!-- End plugin js for this page -->
                <!-- Plugin js for this page -->
                <script src="../resources/vendors/chart.js/Chart.min.js"></script>
                <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script>
                <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
                <script src="../resources/js/dataTables.select.min.js"></script>
                <script src="../resources/js/custom.js"></script>
                <!-- End plugin js for this page -->
                <script src="../resources/vendors/moment/moment.min.js"></script>
                <script src="../resources/vendors/fullcalendar/fullcalendar.min.js"></script>

                <!-- inject:js -->
                <script src="../resources/js/off-canvas.js"></script>
                <script src="../resources/js/hoverable-collapse.js"></script>
                <script src="../resources/js/template.js"></script>
                <script src="../resources/js/settings.js"></script>
                <script src="../resources/js/todolist.js"></script>

                <script src="../resources/js/calendar.js"></script>
                <script src="../resources/js/tabs.js"></script>

                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <!-- endinject -->
                <!-- Custom js for this page-->
                <script src="../resources/js/dashboard.js"></script>
                <script src="../resources/js/Chart.roundedBarCharts.js"></script>
                <!-- End custom js for this page-->
                <!-- Custom js for this page-->
                <script src="../resources/js/file-upload.js"></script>
                <script src="../resources/js/typeahead.js"></script>
                <script src="../resources/js/select2.js"></script>
                <!-- End custom js for this page-->

                <!-- plugin js for this page -->
                <script src="../resources/vendors/tinymce/tinymce.min.js"></script>
                <script src="../resources/vendors/quill/quill.min.js"></script>
                <script src="../resources/vendors/simplemde/simplemde.min.js"></script>
                <script src="../resources/js/editorDemo.js"></script>

                <!-- Custom js for this page-->
                <script src="../resources/js/data-table.js"></script>
            



                <script>
                function printSalary()
                {
                    let month = document.getElementById('months').value;
let year = document.getElementById('years').value;
                    console.log(month);
                    
                    if(month === '')
                        {
                        alert('Please select month');
                        return;
                        }


                   let url = `print_salary.php?month=${month}&years=${year}`;

                    window.open(url, '_blank');
                }
</script>

<script>
$(document).ready(function () {

    $('#staffTable').DataTable();

    $('#staffTable').on('click', '.editBtn', function () {

        //console.log("clicked");

        let row = $(this).closest('tr'); // ✅ pehle declare
                     // ✅ ab use karo

                     let rowId = row.find('td:eq(0)').text();
        let staffId = row.find('td:eq(1)').text();
        let salary  = row.find('td:eq(10)').text();

        $('#modal_row_id').val(rowId);
        $('#modal_staff_id').val(staffId);
        $('#modal_salary').val(salary);

        // Bootstrap 5 modal open
        let modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    });

});
</script>



 </div>

            <div style="margin-left:250px">
                <span id="lblMsg"></span>
            </div>
            <style>
                #lblMsg {
                    visibility: hidden;
                }
            </style>


</body>

</html>