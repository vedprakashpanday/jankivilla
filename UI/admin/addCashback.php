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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit']) && $_POST['btnsubmit'] == 'Submit' ) {

    // echo "<pre>";
    // var_dump($_POST); // Debugging line to check POST data
    // echo "insert columns";
    // echo "</pre>";
    // exit();

    $nullamount= 99999999999999999999.99;
    $names      = $_POST['cashback_name'];
    $fromDates  = $_POST['offer_from'];
    $toDates    = $_POST['offer_to'];
    $amtFrom    = $_POST['amount_from'];
    $amtTo      =  $_POST['amount_to'] ;
    $percent    = $_POST['cashback_percent'];

    $stmt = $pdo->prepare("
        INSERT INTO cashback_offers
        (cashback_name, offer_from, offer_to, amount_from, amount_to, cashback_percent)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < count($names); $i++) {

        // skip empty rows
        if (empty($amtFrom[$i])) {          
            
            continue;
        }
         if(empty($amtTo[$i]))
            {
                $amtTo[$i]=$nullamount;
    //             echo "<pre>";
    // var_dump($amtTo[$i]); // Debugging line to check POST data
    // echo "</pre>";
    // exit();
                
            }

        $stmt->execute([
            $names[$i],
            $fromDates[$i],
            $toDates[$i],
            $amtFrom[$i],
            $amtTo[$i],
            $percent[$i]
        ]);
    }

    $message = "Cashback offers inserted successfully!";
    echo "<script>alert('$message'); window.location.href=window.location.href;</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit']) && $_POST['btnsubmit'] == 'Update') {

    // echo "<pre>";
    // var_dump($_POST); // Debugging line to check POST data
    // echo "</pre>";
    // exit();

    $nullamount= 99999999999999999999.99;
    $names      = $_POST['cashback_name'];
    $fromDates  = $_POST['offer_from'];
    $toDates    = $_POST['offer_to'];
    $amtFrom    = $_POST['amount_from'];
    $amtTo      =  !empty($_POST['amount_to']) ? $_POST['amount_to'] : $nullamount;
    $percent    = $_POST['cashback_percent'];
    $cashback_id = $_POST['cashback_id'];
    $stmt = $pdo->prepare("
        UPDATE cashback_offers
        SET cashback_name = ?, offer_from = ?, offer_to = ?, amount_from = ?, amount_to = ?, cashback_percent = ?,created_at = NOW()
        WHERE id = ?    
    ");

    

        // skip empty rows
        
       

        $stmt->execute([
            $names[0],
            $fromDates[0],
            $toDates[0],
            $amtFrom[0],
            $amtTo[0],
            $percent[0],
            $cashback_id
        ]);
    

    $message = "Cashback offers Updated successfully!";
    echo "<script>alert('$message'); window.location.href=window.location.href;</script>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delStmt = $pdo->prepare("DELETE FROM cashback_offers WHERE id = ?");
    $deleted = $delStmt->execute([$delete_id]);

    $delMessage = $deleted ? "Cashback offer deleted successfully!" : "Failed to delete cashback offer.";
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

    <style>
        .action-btns button {
            margin-top: 30px;
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
                                    <div class="container-fluid" style="padding-top: 50px; padding-bottom: 50px;">
                                        <div class="row" style="display: block;">
                                           
                                                <div class="col-md-12">
                                                    <div class="container-fluid mt-4 p-0">
                                                        <h4 class="mb-3">Cashback Offer Form</h4>

                                                        <form id="cashbackForm" method="post" class="col-12 px-0">

                                                            <!-- Cashback Row -->
                                                            <div class="row cashback-row align-items-end mb-3">

                                                                <div class="col-md-2">
                                                                    <label>Cashback Name</label>
                                                                    <input type="text" name="cashback_name[]" id="cashback_name" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <label>Offer Date From</label>
                                                                    <input type="date" name="offer_from[]" id="offer_from" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <label>Offer Date To</label>
                                                                    <input type="date" name="offer_to[]" id="offer_to" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <label>Amount From</label>
                                                                    <input type="number" name="amount_from[]" id="amount_from" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <label>Amount To</label>
                                                                    <input type="number" name="amount_to[]" id="amount_to" class="form-control" >
                                                                </div>

                                                                <div class="col-md-1">
                                                                    <label>% Cashback</label>
                                                                    <input type="number" name="cashback_percent[]" id="cashback_percent" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-2 action-btns">
                                                                    <button type="button" class="btn btn-success add-row">+</button>
                                                                </div>

                                                            </div>
                                                            <!-- End Cashback Row -->

                                                            <div class="row mt-4">
                                                                <div class="col-md-12 text-end">
                                                                    <input type="hidden" name="cashback_id" id="cashback_id" value="">
                                                                    <input type="submit" name="btnsubmit" value="Submit" id="btnsubmit" class="btn-success">
                                                                     <input type="reset" class="btn-secondary" value="Clear Form">
                                                                </div>
                                                        </form>


                                                    </div>
                                                </div>
                                            
                                        </div>
                                    </div>
                                    <div class="row pt-5">
                                        <div class="col-md-12" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Rewards Report</h3>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>CashBack Name</th>
                                                        <th>Offer Date From</th>
                                                        <th>Offer Date To</th>
                                                        <th>Amount From</th>
                                                        <th>Amount To</th>
                                                        <th>% Cashback</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rewards = $pdo->query("SELECT * FROM cashback_offers ORDER BY created_at DESC");
                                                    $sn = 1;
                                                    foreach ($rewards as $reward) {
                                                        echo "<tr>";
                                                        echo "<td>{$sn}</td>";
                                                        echo "<td>{$reward['cashback_name']}</td>";
                                                        echo "<td>{$reward['offer_from']}</td>";
                                                        echo "<td>{$reward['offer_to']}</td>";
                                                        echo "<td>{$reward['amount_from']}</td>";
                                                        echo "<td>{$reward['amount_to']}</td>";
                                                        echo "<td>{$reward['cashback_percent']}</td>";
                                                        echo "<td>
    <button class='btn btn-sm btn-primary' onclick='editReward(" . json_encode($reward) . ")'>Edit</button>
    <form method='post' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this reward?\")'>
        <input type='hidden' name='delete_id' value='" . $reward['id'] . "'>
        <button type='submit' class='btn btn-sm btn-danger'>Delete</button>
    </form>
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
                        $('#cashback_name').val(data.cashback_name);
                        $('#offer_from').val(data.offer_from);
                        $('#offer_to').val(data.offer_to);
                        $('#amount_from').val(data.amount_from);
                        $('#amount_to').val(data.amount_to);
                        $('#cashback_percent').val(data.cashback_percent);
                        $('#btnsubmit').val('Update');
                        $('#cashback_id').val(data.id);
                        $('.action-btns').hide(); // Hide add/remove buttons during edit
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
$(document).on('click', '.add-row', function () {

    let currentRow = $(this).closest('.cashback-row');
    let newRow = currentRow.clone();

    // ❌ Amount fields empty
    newRow.find('input[name="amount_from[]"]').val('');
    newRow.find('input[name="amount_to[]"]').val('');

    // buttons update (add + remove)
    newRow.find('.action-btns').html(`
        <button type="button" class="btn btn-success add-row">+</button>
        <button type="button" class="btn btn-danger remove-row ms-1">-</button>
    `);

    // 🔥 IMPORTANT: submit button ke row se pehle insert
    $('#cashbackForm .row.mt-4').before(newRow);
});

// remove row
$(document).on('click', '.remove-row', function () {
    $(this).closest('.cashback-row').remove();
});
</script>

<script>

</script>



        

</body>

</html>