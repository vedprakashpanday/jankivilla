<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header("Location: ../../adminlogin.php"); // Redirect to dashboard
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit']) && $_POST['btnsubmit'] == 'Apply') {
    
    $customer_id = $_POST['customer_name'] ?? '';
    $customer_name = $_POST['customer_name1'] ?? '';
    $cashback_name = $_POST['cashback_name'] ?? '';
    $cashback_percent = $_POST['cashback_percent'] ?? 0;
    $cashback_amount = $_POST['cashback_amount'] ?? 0;
    $remark = $_POST['remark'] ?? '';
 

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;
    // Get s_name from tbl_hire
    $stmt = $pdo->prepare("INSERT INTO apply_cashback (customer_id, customer_name, cashback_name, cashback_percent, cashback_amount, remarks,created_at,updated_at) VALUES (?, ?, ?, ?, ?, ?,now(),now())");
    $stmt->execute([$customer_id, $customer_name, $cashback_name, $cashback_percent, $cashback_amount, $remark]);

    $stmt2 = $pdo->prepare("SELECT distinct(id),bill_date
        FROM receiveallpayment
        WHERE customer_id = ? and customer_name = ?
        ORDER BY id DESC
        LIMIT 1");
    $stmt2->execute([$customer_id, $customer_name]);
    $row12 = $stmt2->fetch(PDO::FETCH_ASSOC);

    //  $stmt21 = $pdo->prepare("SELECT *
    //     FROM receiveallpayment
    //     WHERE customer_id = ? and id = ?
    //     ORDER BY id DESC
    //     LIMIT 1");
    // $stmt21->execute([$customer_id, $row12['id']]);
    // $row123 = $stmt21->fetch(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // print_r($row12);
    
    // echo "</pre>";
    // exit();
    
    $stmt1 = $pdo->prepare("
UPDATE receiveallpayment
SET cashback = ?
WHERE id = ? and bill_date = ?
");

$stmt1->execute([$cashback_amount, $row12['id'],$row12['bill_date']]);

    //  echo "<pre>";
    // print_r($stmt1->rowCount());
    // echo "</pre>";
    // exit;
    $message = "Cashback applied successfully!";

    echo "<script>alert('$message'); window.location.href=window.location.href;</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit']) && $_POST['btnsubmit'] == 'update') {
    
    $customer_id = $_POST['customer_name'] ?? '';
    $customer_name = $_POST['customer_name1'] ?? '';
    $cashback_name = $_POST['cashback_name'] ?? '';
    $cashback_percent = $_POST['cashback_percent'] ?? 0;
    $cashback_amount = $_POST['cashback_amount'] ?? 0;
    $remark = $_POST['remark'] ?? '';
    $cashback_id = $_POST['cashback_id'] ?? 0;
 

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;
    // Get s_name from tbl_hire
    $stmt = $pdo->prepare("Update apply_cashback set customer_id=?, customer_name=?, cashback_name=?, cashback_percent=?, cashback_amount=?, remarks=?, updated_at=now() where id=?");
    $stmt->execute([$customer_id, $customer_name, $cashback_name, $cashback_percent, $cashback_amount, $remark, $cashback_id]);

    $stmt1 = $pdo->prepare("Update receiveallpayment set cashback=? where customer_id=?");
    $stmt1->execute([$cashback_amount, $customer_id]);
    
    $message = "Cashback applied successfully!";

    echo "<script>alert('$message'); window.location.href=window.location.href;</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sponsor_id'])) {
    
    $customer_id = $_POST['sponsor_id'] ?? '';
   

    // Get s_name from tbl_hire
    $stmt1 = $pdo->prepare("SELECT customer_name,payamount,created_date FROM receiveallpayment WHERE customer_id = ? and cashback = 0 order by created_date desc limit 1");
    $stmt1->execute([$customer_id]);
    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    $amount = $row1['payamount'] ;
    $paydate = $row1['created_date'] ;


       $stmt = $pdo->prepare("
    SELECT *
    FROM cashback_offers
    WHERE offer_from <= ?
      AND offer_to >= ?
      AND amount_from <= ?
      AND amount_to >= ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([
    $paydate,   // current / payment date
    $paydate,
    $amount,    // paid amount
    $amount
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $cashback_percent = $row['cashback_percent'];
    $cashback_amount = ($cashback_percent / 100) * $amount;

    echo json_encode([
        'cashback_name' => $row['cashback_name'],
        'customer_name' => $row1['customer_name'],
        'cashback_percent' => $cashback_percent,
        'cashback_amount' => $cashback_amount,
        'amount_paid' => $amount

    ]);
} else {
    echo json_encode([
        'cashback_percent' => 0,
        'cashback_amount' => 0,
         'amount paid' => $amount,
         'paydate' => $paydate
    ]);
}
    exit;
    
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delStmt = $pdo->prepare("DELETE FROM apply_cashback WHERE id = ?");
    $deleted = $delStmt->execute([$delete_id]);
    
    $customer_id = $_POST['customer_id'];

     $stmt1 = $pdo->prepare("Update receiveallpayment set cashback=0 where customer_id=? order by created_date desc limit 1");
    $stmt1->execute([$customer_id]);

    $delMessage = $deleted ? "Reward deleted successfully!" : "Failed to delete reward.";
    echo "<script>alert('$delMessage'); window.location.href = window.location.href;</script>";
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
</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="aspNetHidden">


        <div class="wrapper">
            <div class="container-scroller">
                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                        <div class="row" style="display: block;">
                                            <form method="post">
                                                <div class="col-md-12">
                                                    <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                        <h2>Apply CashBack</h2>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <b>Customer Name:</b>
                                                                <i>
                                                                    <select id="customer_name" name="customer_name" class="form-control select2" style="font-weight:bold;">
                                                                        <option value="">Select Customer</option>
                                                                        <?php
                                                                        // PHP: Fetch sponsor list
                                                                        $stmt = $pdo->query("SELECT customer_id, customer_name FROM receiveallpayment ORDER BY customer_name ASC");
                                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                            echo '<option value="' . htmlspecialchars($row['customer_id']) . '">' . htmlspecialchars($row['customer_name']) . '</option>                                                                           
                                                                            ';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </i>
                                                            </div>

                                                             <div class="col-md-4 mb-3">
                                                                <b> Amount Paid:</b>

                                                                <i> <input name="Amount_paid" type="text" id="Amount_paid" class="form-control" style="font-weight:bold;" value=" " readonly></i>
                                                            </div>

                                                                <div class="col-md-4 mb-3">
                                                                <b> CashBack Name:</b>

                                                                <i> <input name="cashback_name" type="text" id="cashback_name" class="form-control" style="font-weight:bold;" value=" " readonly></i>
                                                            </div>

                                                            <!-- Hidden input to store selected sponsor_id -->
                                                            <input type="hidden" name="Paidamount" id="Paidamount">
                                                            <input type="hidden" name="customer_name1" id="customer_name1">
                                                            <input type="hidden" name="cashback_id" id="cashback_id">


                                                            <div class="col-md-4 mb-3">
                                                                <b> CashBack Percentage:</b>

                                                                <i> <input name="cashback_percent" type="number" id="cashback_percent" class="form-control" style="font-weight:bold;" value=""></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>CashBack Amount:</b>
 
                                                                <i> <input name="cashback_amount" type="number" id="cashback_amount" class="form-control" style="font-weight:bold;" value="" readonly></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Remark:</b>
                                                                <i>
                                                                    <textarea name="remark" id="remark" class="form-control" style="font-weight:bold;" rows="6" cols="7"></textarea>
                                                                </i>
                                                            </div>

                                                        </div>


                                                        <div class="row pt-4">
                                                            <div class="col-md-12">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-7" style="text-align: center;">
                                                                        <input type="submit" name="btnsubmit" value="Apply" id="btnsubmit" class="btn-success">
                                                                        <input type="reset" class="btn-secondary" value="Clear Form">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row pt-5">
                                        <div class="col-md-12" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Rewards Report</h3>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Customer ID</th>
                                                        <th>Customer Name</th>
                                                        <th>Amount Paid</th>
                                                        <th>CashBack Name</th>
                                                         <th>CashBack Amount</th>
                                                       <th>CashBack Percent</th>
                                                       <th>Remark</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rewards = $pdo->query("SELECT ac.*, rap.payamount
FROM apply_cashback ac
INNER JOIN (
    SELECT 
        customer_id COLLATE utf8mb4_general_ci AS customer_id,
        payamount 
    FROM receiveallpayment
    WHERE cashback > 0
    GROUP BY customer_id
) rap
ON ac.customer_id COLLATE utf8mb4_general_ci = rap.customer_id

");
                                                    $sn = 1;
                                                    foreach ($rewards as $reward) {
                                                        echo "<tr>";
                                                        echo "<td>{$sn}</td>";
                                                        echo "<td>{$reward['customer_id']}</td>";
                                                        echo "<td>{$reward['customer_name']}</td>";
                                                        echo "<td>{$reward['payamount']}</td>";
                                                        echo "<td>{$reward['cashback_name']}</td>";
                                                        echo "<td>{$reward['cashback_amount']}</td>";
                                                        echo "<td>{$reward['cashback_percent']}</td>";
                                                        echo "<td>{$reward['remarks']}</td>";
                                                        echo "<td>{$reward['created_at']}</td>";
                                                        echo "<td>
    <button class='btn btn-sm btn-primary' onclick='editReward(" . json_encode($reward) . ")'>Edit</button>

    <form method='post' style='display:inline;' 
          onsubmit='return confirm(\"Are you sure you want to delete this reward?\")'>
        <input type='hidden' name='customer_id' value='" . htmlspecialchars($reward['customer_id']) . "'>
        <input type='hidden' name='delete_id' value='" . htmlspecialchars($reward['id']) . "'>
        <button type='submit' class='btn btn-sm btn-danger'>Delete</button>
    </form>

    <a href='print_cashback.php?id=" . htmlspecialchars($reward['id']) . "' 
       class='btn btn-sm btn-success printBtn fw-bold d-block'>
      print
    </a>
</td>";

                                                        echo "</tr>";
                                                        $sn++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
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
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                <script>
                    function editReward(data) {
                        // $('#reward_id').val(data.id);
                        // $('#sponsor_id').val(data.sponsor_id).trigger('change'); // Set hidden input
                        // $('#associate_name').val(data.sponsor_id).trigger('change'); // Set select2
                        // $('input[name="amount"]').val(data.amount);
                        // $('#description').val(data.description);

                       
                        
                           $('#customer_name')
    .html(
        `<option value="${data.customer_id}" selected>
            ${data.customer_name}
         </option>`
    );
                         $('#cashback_name').val(data.cashback_name);
                            $('#cashback_percent').val(data.cashback_percent);
                            $('#cashback_amount').val(data.cashback_amount);
                            $('#Amount_paid').val(data.payamount);
                                $('#Paidamount').val(data.payamount);
                            $('#customer_name1').val(data.customer_name);
                            $('#remark').val(data.remarks);
                             $('#btnsubmit').val('update');
                             $('#cashback_id').val(data.id);
                    }
                </script>

            </div>
        </div>
        <div style="margin-left:250px">
            <span id="lblMsg"></span>
        </div>
        <style>
            #lblMsg {
                visibility: hidden;
            }
        </style>





        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Select Customer',
                    allowClear: true
                });

                $('.select3').select2({
                    placeholder: 'Select CashBack Name',
                    allowClear: true
                });

                $('#customer_name').on('change', function() {
                    let selectedSponsorId = $(this).val(); // sponsor_id is the value of <option>
                     
                    console.log(selectedSponsorId);
                    
                    // assign it to hidden input

                    $.ajax({
                        url:'applyCashback.php',
                        type:'POST',
                        data:{sponsor_id:selectedSponsorId},
                        success:function(response){
                            console.log(response);
                            var response = JSON.parse(response);
                            $('#cashback_name').val(response.cashback_name);
                            $('#cashback_percent').val(response.cashback_percent);
                            $('#cashback_amount').val(response.cashback_amount);
                            $('#Paidamount').val(response.amount_paid);
                            $('#Amount_paid').val(response.amount_paid);
                            $('#customer_name1').val(response.customer_name);

                        }
                    })
                });

                 $('#cashback_percent').on('input', function() {
                    console.log("entered");
                    
                    let percent = parseFloat($(this).val());
                    let paidAmount = parseFloat($('#Paidamount').val()) || 0;

                    console.log(percent);
                    console.log(paidAmount);
                    
                    if (!isNaN(percent) && !isNaN(paidAmount)) {
                        let cashbackAmount = (percent / 100) * paidAmount;
                        $('#cashback_amount').val(cashbackAmount.toFixed(2));
                    } else {
                        $('#cashback_amount').val('0.00');
                    }
                 });

            });
        </script>


</body>

</html>