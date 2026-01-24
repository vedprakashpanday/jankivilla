<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// header('Content-Type: application/json');
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'get_latest_advance') {

    header('Content-Type: application/json');

    $memberId = $_POST['member_id'] ?? '';

    if ($memberId === '') {
        echo json_encode(['success'=>false,'message'=>'Member ID missing']);
        exit;
    }

    // Step 1: get all advances of employee
    $stmt = $pdo->prepare("
        SELECT 
            cs.id AS advance_id,
            cs.rem_due,
            cs.repayment_date,
            cs.total_repay,
            ar.full_name,
            ar.member_id
        FROM calc_salary cs
        JOIN adm_regist ar ON ar.member_id = cs.staff_id
        WHERE cs.staff_id = :member_id
        order by cs.id DESC limit 1
    ");
    $stmt->execute(['member_id' => $memberId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $today = new DateTime(date('Y-m-d'));
    $nearestDiff = null;
    $selected = null;

    // Step 2: find nearest repayment date
    foreach ($rows as $row) {
        $dates = json_decode($row['repayment_date'], true);
        if (!is_array($dates)) continue;

        foreach ($dates as $d) {
            if (empty($d['date'])) continue;

            $repDate = new DateTime($d['date']);
            $diff = abs($repDate->diff($today)->days);

            if ($nearestDiff === null || $diff < $nearestDiff) {
                $nearestDiff = $diff;
                $selected = [
                    'advance_id'     => $row['advance_id'],
                    'full_name'      => $row['full_name'],
                    'member_id'      => $row['member_id'],
                    'rem_due'        => $row['rem_due'],
                    'total_repay'    => $row['total_repay'],
                    'repayment_date' => $d['date']
                ];
            }
        }
    }

    if ($selected) {
        echo json_encode(['success'=>true] + $selected);
    } else {
        echo json_encode(['success'=>false,'message'=>'No repayment date found']);
    }
    exit;
}
if (isset($_POST['update'])) {

   
    // exit(); // remove this line after testing

     $employee_name     = $_POST['empName'] ?? '';
     $memberId          = $_POST['mem_id'] ?? '';
    $employee_id       = $_POST['emp_id'] ?? '';
    $total_due         = $_POST['totalDue'] ?? 0;
    $on_date_due       = $_POST['onDateDue'] ?? 0;
    $repayment_amount  = $_POST['repay_amt'] ?? 0;
    $total_repay       = $_POST['total_repay'] ?? 0;
    
    $repayment_status  = $_POST['repaymentStatus'] ?? '';
    $repayment_date    = $_POST['repaymentStatus']==='paid' ? date('Y-m-d') : null ;
    $extend_date       = $_POST['extend_date'] ?? null;

    // 🔹 If status is NOT Extend → extend_date null
    if ($repayment_status !== 'extend' && empty($extend_date) && $extend_date !== 'unpaid') {
        $extend_date = null;
    }

     $total_due -= $repayment_amount;

    //   echo "<pre>";
    //   print_r($repayment_status);
    // print_r($_POST);
    // echo "</pre>";

    $sql = "
        INSERT INTO employee_repayment
        (
            employee_name,
            employee_id,
            total_due,
            on_date_due,
            repayment_amount,
            repayment_date,
            repayment_status,
            extend_date,
            total_repay
        )
        VALUES
        (
            :employee_name,
            :employee_id,
            :total_due,
            :on_date_due,
            :repayment_amount,
            :repayment_date,
            :repayment_status,
            :extend_date,
            :total_repay
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':employee_name'    => $employee_name,
        ':employee_id'      => $memberId,
        ':total_due'        => $total_due,
        ':on_date_due'      => $on_date_due,
        ':repayment_amount' => $repayment_amount,
        ':repayment_date'   => $repayment_date,
        ':repayment_status' => $repayment_status,
        ':extend_date'      => $extend_date,
        ':total_repay'      => $total_repay + $repayment_amount
    ]);







    $emp_id = $_POST['emp_id'];
    $repay_amt = floatval($_POST['repay_amt']);
    $repaymentStatus = $_POST['repaymentStatus'];
    $extend_date = $_POST['extend_date'];

    // Step 1: find the advance row & nearest repayment date
    $stmt = $pdo->prepare("
        SELECT cs.id, cs.rem_due,total_repay, jt.rep_date AS nearest_date, cs.repayment_date AS json_column
        FROM calc_salary cs
        JOIN JSON_TABLE(
            cs.repayment_date,
            '$[*]' COLUMNS (
                rep_date DATE PATH '$.date'
            )
        ) jt
        WHERE cs.id = :emp_id
          AND jt.rep_date IS NOT NULL
        ORDER BY ABS(DATEDIFF(jt.rep_date, CURDATE())) ASC
        LIMIT 1
    ");

    $stmt->execute(['emp_id' => $emp_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success'=>false, 'message'=>'No matching advance found']);
        exit;
    }

    // nearest date & rem_due
    $nearest_date = $row['nearest_date'];
    $rem_due = floatval($row['rem_due']);
    $advance_id = $row['id'];
    $json_column = $row['json_column'];
    $updated_total_repay = floatval($row['total_repay']) + $repay_amt;
    // Step 2: update rem_due
    $new_rem_due = max($rem_due - $repay_amt, 0);

    // Step 3: update repayment_date JSON if extend_date given
    if ($extend_date) {
        $json_array = json_decode($json_column, true);

        if (is_array($json_array) && count($json_array) > 0) {
            // last element set to extend_date
            $json_array[count($json_array)-1]['date'] = $extend_date;
            $new_json = json_encode($json_array, JSON_UNESCAPED_SLASHES);
        } else {
            $new_json = $json_column; // fallback
        }
    } else {
        $new_json = $json_column;
    }

    // Step 4: update database
    $update_stmt = $pdo->prepare("
        UPDATE calc_salary
        SET rem_due = :new_rem_due,
            repayment_date = :new_json,
            total_repay = :updated_total_repay,
            repayment_status = :repayment_status
        WHERE id = :advance_id
    ");

    $update_stmt->execute([
        'new_rem_due' => $new_rem_due,
        'new_json' => $new_json,
        'updated_total_repay' => $updated_total_repay,
        'repayment_status' => $repaymentStatus,
        'advance_id' => $advance_id
    ]);

    // Response
    echo json_encode([
        'success'=>true,
        'message'=>"Advance updated successfully",
        'new_rem_due'=>$new_rem_due,
        'repayment_date'=>$new_json
    ]);

    // Redirect (optional)
    header("Location: repayment_emp.php");
    exit;
}

if(isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    $stmt = $pdo->prepare("SELECT * FROM employee_repayment WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    $new_total_due = $record['total_due'] + $record['repayment_amount'];
    $new_total_pay = $record['total_repay'] - $record['repayment_amount'];
    
    $update_stmt = $pdo->prepare("
        UPDATE calc_salary
        SET rem_due = :new_total_due,
            total_repay = :new_total_pay
        WHERE staff_id = :staff_id
        ORDER BY id DESC LIMIT 1
    ");

    $update_stmt->execute([
        'new_total_due' => $new_total_due,
        'new_total_pay' => $new_total_pay,
        'staff_id' => $record['employee_id']
    ]);


    $stmt = $pdo->prepare("DELETE FROM employee_repayment WHERE id = :id");
    $stmt->execute(['id' => $delete_id]);

    // Redirect back to the repayment page
    header("Location: repayment_emp.php");
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
    <style>
        .card{
    border-radius:10px;
}

.form-label{
    font-weight:600;
}

.hidden{
    display:none;
}
    </style>
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
                   
                  <div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
 
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Advance Repayment</h5>
                </div>

                <div class="card-body">
                   <?php
$stmt = $pdo->prepare("
    SELECT member_id, full_name 
    FROM adm_regist 
    ORDER BY full_name ASC
");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<input list="employeeList"  id="employeeInput" class="form-control" placeholder="Select Employee by ID">

<datalist id="employeeList">
<?php foreach ($members as $m): ?>
    <option 
        value="<?= htmlspecialchars($m['member_id']) ?>"
        data-name="<?= htmlspecialchars($m['full_name']) ?>">
        <?= htmlspecialchars($m['member_id']) ?> - <?= htmlspecialchars($m['full_name']) ?>
    </option>
<?php endforeach; ?>
</datalist>
                    <!-- Repayment Form -->
                   
<form id="repaymentForm" class="hidden" method="post">
    <input type="hidden" name="emp_id" id="emp_id">
    <input type="hidden" name="mem_id" id="mem_id">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee Name</label>
                                <input type="text" id="empName" name="empName" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Total Due</label>
                                <input type="text" id="totalDue" name="totalDue" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">On-Date Due</label>
                                <input type="text" id="onDateDue" name="onDateDue" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                             <div class="col-md-4">
                                <label class="form-label">Total Repay</label>
                                <input type="text" id="total_repay" name="total_repay" class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Repayment Amount</label>
                                <input type="number" class="form-control" placeholder="Enter amount" name="repay_amt" id="repay_amt">
                                <span id="warn_msg"></span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Repayment Status</label>
                                <select id="repaymentStatus" name="repaymentStatus" class="form-select">
                                    <option value="">-- Select Status --</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="extend">Extend</option>
                                </select>
                            </div>
                        </div>

                        <!-- Extend Date -->
                        <div class="row mb-3 hidden" id="extendDateBox">
                            <div class="col-md-6">
                                <label class="form-label">Extend Date</label>
                                <input type="date" class="form-control" name="extend_date" id="extend_date">
                            </div>
                        </div>

                          <!-- Payment Date -->
                        <div class="row mb-3 hidden" id="paidDateBox">
                            <div class="col-md-6">
                                <label class="form-label">Payment Date</label>
                                <input type="date" class="form-control" name="paid_date" id="paid_date">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success" name="update" id="update" >
                                Submit Repayment
                            </button>
                        </div>

                    </form>
  
                </div>
            </div>

            <div class="container mt-4">

    <!-- 🔹 Heading -->
    <div class="card mb-3">
        <div class="card-body text-center">
            <h4 class="fw-bold mb-0">Employee Repayment History</h4>
        </div>
    </div>

    <!-- 🔹 DataTable -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="repaymentTable" class="table table-bordered table-striped w-100">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-dark">Action</th>
                            <th class="text-dark">SL No</th>
                            <th class="text-dark">Employee Name</th>
                            <th class="text-dark">Employee ID</th>
                            <th class="text-dark">Total Due</th>
                            <th class="text-dark">On-Date Due</th>
                            <th class="text-dark">Repayment Amount</th>
                            <th class="text-dark">Repayment Date</th>
                            <th class="text-dark">Repayment Status</th>
                            <th class="text-dark">Extend Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX DATA -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

        </div>
       
    </div>
   
</div>
                  
                </div>


            </div>
            <a href="#" target="_blank">
                <!-- partial -->
            </a>
                 <!-- search box for options-->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
$('#employeeInput').on('change', function () {
    let memberId = $(this).val();

    if (!memberId) return;
console.log(memberId);

    $.ajax({
        url: '', // same page
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'get_latest_advance',
            member_id: memberId
        },
        success: function (res) {

            console.log(res);

            if (res.success)
                 {
                $('#emp_id').val(res.advance_id);
                $('#mem_id').val(res.member_id);
                $('#empName').val(res.full_name);
                $('#totalDue').val(res.rem_due);
                $('#onDateDue').val(res.repayment_date);
                $('#total_repay').val(res.total_repay);

                $('#rem_due').val(res.rem_due);
                $('#salary_month').val(res.repayment_date);

                $('#repaymentForm').removeClass('hidden');
            } 
            else
            {
                alert(res.message);
            }
        }
    });
});
</script>


<script>
$(document).ready(function(){

    $('#repaymentStatus').on('change', function(){

        const status = $(this).val();

        if (status === 'unpaid' || status === 'extend') {
            // show extend date
            $('#extendDateBox').removeClass('hidden');
            $('#paidDateBox').addClass('hidden');
             $('#paidDateBox').val('');
        }
        else if (status === 'paid') {
            // hide extend date
            $('#extendDateBox').addClass('hidden');
            $('#extend_date').val(''); // optional: clear date
            $('#paidDateBox').removeClass('hidden');
        }
        else {
            // hide extend date (paid or empty)
            $('#extendDateBox').addClass('hidden');
            $('#extend_date').val(''); // optional: clear date
            $('#paidDateBox').addClass('hidden');
             $('#paidDateBox').val('');
        }

    });

});
</script>

<script>
$(document).ready(function () {

    $('#repaymentTable').DataTable({
        ajax: {
            url: 'fetch_employee_repayment.php',
            type: 'POST',
            dataSrc: function (json) {
    console.log(json);
    return json.data;
}
        },
        processing: true,
        pageLength: 10,
        order: [[7, 'desc']],
        columnDefs: [
            { targets: [0], className: 'text-center' },
            { targets: [3,4,5], className: 'text-end' },
            { targets: [7], className: 'text-center' }
        ]
    });

});
</script>


<?php include 'adminfooter.php'; ?>  

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