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


// Handle update request
if (isset($_POST['btnsubmit'])) {

    // Safe inputs
    $staff_id = trim($_POST['designation']);    
    $absent   = 0; 
    $month1    = (int) $_POST['month'];
    $year     = (int) $_POST['year'];
    $amount   = (float) $_POST['adv_amount'];
    $type = $_POST['adv_type'];
    $cut = isset($_POST['cut']) ? (float) $_POST['cut'] : 0;


    $month = sprintf('%02d',$month1);


   $repayment_date = !empty($_POST['repayment_date'])  ? $_POST['repayment_date'] : null;
    // Total days in month  cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysInMonth = 30;

    // Fetch basic salary
    $stmt = $pdo->prepare("
        SELECT salary 
        FROM adm_salary 
        WHERE staff_id = :staff_id
    ");
    $stmt->execute(['staff_id' => $staff_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die('Salary not found for staff');
    }

    $basicSalary = (float) $row['salary'];

      $stmt = $pdo->prepare("
        SELECT rem_due,advance
        FROM calc_salary 
        WHERE staff_id = :staff_id
    ");
    $stmt->execute(['staff_id' => $staff_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingAdvance = $row ? (float) $row['advance'] : 0;
    $existingDue = $row ? (float) $row['rem_due'] : 0;
    $totalAdvance = $amount;
    $dueAmount = $existingDue + $amount;
    // ---- Salary Calculation ----
    // $paidDays  = max(0, $daysInMonth - ($absent-1));   // negative avoid
    // $perDay    = $basicSalary / $daysInMonth;
    $paidSalary = 0;

    // Insert salary record
    $stmt = $pdo->prepare("
        INSERT INTO calc_salary 
        (staff_id, salary_month, absent, actual_salary, paid_salary, created_at,half_day,advance,cut,rem_due,advance_date,repayment_type,repayment_date)
        VALUES 
        (:staff_id, :salary_month, :absent, :actual_salary, :paid_salary, NOW(),0,:advance,:cut,:rem_due,NOW(),:repayment_type,:repayment_date)
    ");

    $stmt->execute([
        'staff_id'       => $staff_id,
        'salary_month'   => "$year-$month-01",
        'absent'         => $absent,
        'actual_salary'  => $basicSalary,
        'paid_salary'    => $paidSalary,
        'advance'        => $totalAdvance,
        'cut'            => $cut,
        'rem_due'        => $dueAmount,
        'repayment_type' => $type,
        'repayment_date' => $repayment_date
    ]);

    header("Location: add_advance.php");
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
                                                <h2>Add Advance Salary</h2>
                                                    <hr>
                                                <div class="col-12" style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f; display:flex; flex-wrap:wrap; justify-content:center; border-radius:10px;">
                                                    

                                               
                                                <div class="col-12 col-md-4">
                                                     <label><b>Select Staff</b></label>
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
                                                           <div class="col-12 col-md-4">
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
<div class="col-12 col-md-4">
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

                                                 <div class="col-12 col-md-4">
                                                    <label><b>Enter Advance Amount</b></label>
                                                    <input type="number" name="adv_amount" id="adv_amount" class="form-control" required>
                                                </div>

                                                <div class="col-12 col-md-4">
                                                    <label><b>Repayment Type</b></label>
                                                    <select name="adv_type" id="adv_type" class="form-control" required onchange="adv_type()">
                                                        <option value="">-- Select Type --</option>
                                                        <option value="FS">From Salary</option>
                                                        <option value="OD">ON A Date</option>
                                                    </select>
                                                    
                                                </div>

                                                <div class="col-12 col-md-4 d-none">
                                                    <label><b>Cut From Salary</b></label>
                                                    <input type="Number" name="cut" id="cut" class="form-control" placeholder="Enter Cut Amount" >
                                                    
                                                </div>

                                                <div class="col-12 col-md-4 d-none">
                                                    <label><b>Repayment Date</b></label>
                                                    <input type="date" name="repayment_date" id="repayment_date" class="form-control" placeholder="Enter Repayment Date">
                                                    
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <input type="submit" value="Add Advance" class="form-control btn btn-primary" name="btnsubmit" style="margin-top:32px;">
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
                <th>Cut</th>
                <th>Half Day</th>
                <th>Absent</th>
                <th>Paid Salary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             <?php 
                                                                    $stmt = $pdo->prepare("SELECT ar.full_name,ar.designation,ar.member_id, ads.* FROM adm_regist ar left join calc_salary ads on ar.member_id=ads.staff_id");
                                                                $stmt->execute();
                                                                $sponsor = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                foreach($sponsor as $row):
                                                                    if($row['paid_salary']==0):
                                                                ?>
            <tr>
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
                <td><?= htmlspecialchars($row['half_day'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['absent'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['paid_salary'] ?? '') ?></td>
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
                </div>

                <div class="mb-3">
                    <label>Salary</label>
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
$(document).ready(function () {

    $('#staffTable').DataTable();

    $('#staffTable').on('click', '.editBtn', function () {

        //console.log("clicked");

        let row = $(this).closest('tr'); // ✅ pehle declare
                     // ✅ ab use karo

        let staffId = row.find('td:eq(0)').text();
        let salary  = row.find('td:eq(3)').text();

        $('#modal_staff_id').val(staffId);
        $('#modal_salary').val(salary);

        // Bootstrap 5 modal open
        let modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    });




});


</script>

<script>
   document.addEventListener("DOMContentLoaded", function () {

    document.getElementById("adv_type").addEventListener("change", adv_type);

});

function adv_type() {
    var advType = document.getElementById("adv_type").value;
    var cutEl = document.getElementById("cut");
    var repayment = document.getElementById("repayment_date");

    if (advType === "FS") {
        cutEl.parentElement.classList.remove("d-none");
        repayment.parentElement.classList.add("d-none");
        
    } else {
        cutEl.parentElement.classList.add("d-none");
        repayment.parentElement.classList.remove("d-none");
      
    }
}
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