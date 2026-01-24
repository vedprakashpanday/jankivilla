<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is logged in 
if (!isset($_SESSION['sponsor_id'])) {
    header('Location: ../../employee.php'); // Redirect to employee login
    exit();
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


                <?php include 'employeesidepanelheader.php'; ?>


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

                                            <h2 style="margin-top: 30px;">Your Salary</h2>
                                                    <hr>
                                                <div class="col-12" style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f; display:flex; flex-wrap:wrap; justify-content:center; border-radius:10px;">
                                                   

                                                <div class="container mt-4 overflow-auto">
    <table id="staffTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="d-none">sl. No</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Employee Designation</th>
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
                <!-- <th>Action</th> -->
            </tr>
        </thead>
        <tbody>
             <?php 
                                                                    $stmt = $pdo->prepare("SELECT ar.full_name,ar.designation,ar.member_id, ads.* FROM adm_regist ar left join calc_salary ads on ar.member_id=ads.staff_id where ar.member_id=:id");
                                                                $stmt->execute(['id'=>$_SESSION['sponsor_id']]);
                                                                $sponsor = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                $count=0;
                                                                foreach($sponsor as $row):
                                                                ?>
                                                                <?php  if($row['paid_salary']>0):  $count++; ?>
                                                                   
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
                <!-- <td>
                    <input type="submit" class="btn btn-sm btn-primary editBtn" name="edit" value="Edit" />
        

                </td> -->
               
            </tr>
             <?php  endif; ?>
           <?php endforeach; 
           
           if ($count== 0) {
    echo "<tr>
        <td colspan='17' class='text-center'>Your First Salary Is On The Way..</td>
    </tr>";
}
           ?>
                    
                    
                    <!-- <input type="submit" class="btn btn-sm btn-primary editBtn" name="edit" value="Edit" /> -->
                </td>
            </tr>
        </tbody>
    </table>
</div>

                                                    </div>
                                                

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'employee-footer.php'; ?>
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