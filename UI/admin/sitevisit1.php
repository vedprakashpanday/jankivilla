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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit'])) {

    // ---- SAFE INPUT FETCH ----
    $associate_id     = $_POST['sponsor_id'] ?? null;
    //$reward_id        = $_POST['reward_id'] ?? null;

    $sv1_amount       = $_POST['sv1'] ?? 0;
    $customer_name    = $_POST['cust_name'] ?? '';
    $customer_mobile  = $_POST['cust_mobile'] ?? '';
    $passbook_no      = $_POST['passbook'] ?? null;

    $site_visit_date  = $_POST['amount'] ?? null; // date input (rename in future)
    $plot_number      = $_POST['plot_number'] ?? null;
    $extend_days      = $_POST['extend'] ?? 0;

    $debit_voucher_no = $_POST['debit_voucher'] ?? null;
    $transaction_type = $_POST['transaction'] ?? 'cash';

    $remark           = $_POST['description'] ?? '';
    $site_visit="site_visit1";
    $imps_neft_type = $_POST['transaction_type'] ?? null;
    $bank_name = $_POST['trans_number'] ?? null;
    $account_no = $_POST['account_no'] ?? null;
    $ifsc_code = $_POST['ifsc_code'] ?? null;

    date_default_timezone_set('Asia/Kolkata');
    $created_at = date('Y-m-d H:i:s');

    // ---- INSERT QUERY ----
    $insert = $pdo->prepare("
        INSERT INTO site_visit (
            associate_id,
           
            sv1_amount,
            customer_name,
            customer_mobile,
            passbook_no,
            site_visit_date,
            plot_number,
            extend_days,
            debit_voucher_no,
            transaction_type,
            remark,
            created_at,
            site_visit,
            imps_neft_type,
            bank_name,
            account_no,
            ifsc_code
        ) VALUES (
            ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $success = $insert->execute([
        $associate_id,
       
        $sv1_amount,
        $customer_name,
        $customer_mobile,
        $passbook_no,
        $site_visit_date,
        $plot_number,
        $extend_days,
        $debit_voucher_no,
        $transaction_type,
        $remark,
        $created_at,
        $site_visit,
        $imps_neft_type,
        $bank_name,
        $account_no,
        $ifsc_code
    ]);

    $message = $success 
        ? "Site visit added successfully!" 
        : "Failed to add site visit.";

    echo "<script>alert('$message'); window.location.href=window.location.href;</script>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delStmt = $pdo->prepare("DELETE FROM site_visit WHERE id = ?");
    $deleted = $delStmt->execute([$delete_id]);

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
                                                        <h2>Add Site Visit 1 Details</h2>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <b>Associate Member Name:</b>
                                                                <i>
                                                                    <select id="associate_name" name="associate_name" class="form-control select2" style="font-weight:bold;">
                                                                        <option value="">Select Associate</option>
                                                                        <?php
                                                                        // PHP: Fetch sponsor list
                                                                        $stmt = $pdo->query("SELECT sponsor_id, s_name FROM tbl_hire");
                                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                            echo '<option value="' . htmlspecialchars($row['sponsor_id']) . '">' . htmlspecialchars($row['s_name']) . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </i>
                                                            </div>

                                                            <!-- Hidden input to store selected sponsor_id -->
                                                            <input type="hidden" name="sponsor_id" id="sponsor_id">
                                                            <input type="hidden" name="reward_id" id="reward_id">



                                                            <div class="col-md-4 mb-3">
                                                                <b>Site Visit1 Amount:</b>

                                                                <i> <input name="sv1" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Customer Name:</b>

                                                                <i> <input name="cust_name" type="text" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Customer Mobile:</b>

                                                                <i> <input name="cust_mobile" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>
 
                                                            <div class="col-md-4 mb-3">
                                                                <b>Passbook No:</b>

                                                                <i> <input name="passbook" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Site Visit1 Date:</b>

                                                                <i> <input name="amount" type="date" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Site Visit1 Plot Number:</b>

                                                                <i> <input name="plot_number" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Extent:</b>

                                                                <i> <input name="extend" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Debit Voucher number:</b>

                                                                <i> <input name="debit_voucher" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>transaction:</b>

                                                                <i> <select name="transaction" id="paymentMode" class="form-control" style="font-weight:bold;">
                                                                    <option value="cash">Cash</option>
                                                                    <option value="bank">Bank</option>
                                                                </select></i>
                                                            </div>

                                                             <div class="col-md-3 mb-3" id="bankNameField" style="display:none;">
                                                                                <label class="form-label fw-semibold">Transaction Number <span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" name="trans_number" id="bankNameInput" placeholder="Enter Transaction Number">
                                                                            </div>

                                                                            <!-- <div class="col-md-3 mb-3" id="bankAccountField" style="display:none;">
                                                                                <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" name="account_no" id="bankAccountInput" placeholder="Enter bank account number">
                                                                            </div>

                                                                            <div class="col-md-3 mb-3" id="bankIfscField" style="display:none;">
                                                                                <label class="form-label fw-semibold">IFSC CODE <span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" name="ifsc_code" id="ifscCodeInput" placeholder="Enter IFSC code">
                                                                            </div> -->

                                                                             <div class="col-md-3 mb-3" style="display:none;" id="transactionTypeField">
                                                                                <label class="form-label fw-semibold">Transaction Type <span class="text-danger">*</span></label>
                                                                                <select class="form-control" name="transaction_type" id="transactionType" >
                                                                                    <option value="">Select Type</option>
                                                                                    <option value="imps">IMPS</option>
                                                                                    <option value="neft">NEFT</option>
                                                                                </select>
                                                                            </div>

                                                                           

                                                            <div class="col-md-4 mb-3">
                                                                <b>Remark:</b>
                                                                <i>
                                                                    <textarea name="description" id="description" class="form-control" style="font-weight:bold;" rows="6" cols="7"></textarea>
                                                                </i>
                                                            </div>

                                                        </div>


                                                        <div class="row pt-4">
                                                            <div class="col-md-12">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-7" style="text-align: center;">
                                                                        <input type="submit" name="btnsubmit" value="Save" id="" class="btn-success">
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
                                    <div class="row pt-5 col-md-12">
                                        <div class="col-md-12 overflow-auto" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Rewards Report</h3>
                                            <table class="table table-bordered table-striped mx-3" >
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Member ID</th>
                                                        <!-- <th>Site Visit</th> -->
                                                        <th>Site Visit Price</th>
                                                        <th>Customer Name</th>
                                                        <th>Customer Mobile</th>
                                                        <th>Passbook Number</th>
                                                        <th>Site Visit Date</th>
                                                        <th>Plot Number</th>
                                                        <th>Extent</th>
                                                        <th>Transaction Number</th>
                                                        <!-- <th>Account No</th>
                                                        <th>IFSC Code</th> -->
                                                        <th>Debit Voucher No</th>
                                                        <th>Transaction Mode</th>
                                                        <th>Transaction Type</th>
                                                        <th>Remark</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rewards = $pdo->query("SELECT * FROM site_visit Where site_visit='site_visit1' ORDER BY created_at DESC");
                                                    $sn = 1;
                                                    foreach ($rewards as $reward) {
                                                        echo "<tr>";
                                                        echo "<td>{$sn}</td>";
                                                        echo "<td>{$reward['associate_id']}</td>";
                                                        // echo "<td>{$reward['site_visit']}</td>";
                                                        echo "<td>{$reward['sv1_amount']}</td>";
                                                        echo "<td>{$reward['customer_name']}</td>";
                                                        echo "<td>{$reward['customer_mobile']}</td>";
                                                        echo "<td>{$reward['passbook_no']}</td>";
                                                        echo "<td>{$reward['site_visit_date']}</td>";
                                                        echo "<td>{$reward['plot_number']}</td>";
                                                        echo "<td>{$reward['extend_days']}</td>";
                                                        echo "<td>{$reward['bank_name']}</td>";
                                                        // echo "<td>{$reward['account_no']}</td>";
                                                        // echo "<td>{$reward['ifsc_code']}</td>";
                                                        echo "<td>{$reward['debit_voucher_no']}</td>";
                                                        echo "<td>{$reward['transaction_type']}</td>";
                                                        echo "<td>{$reward['imps_neft_type']}</td>";
                                                        echo "<td>{$reward['remark']}</td>";
                                                        echo "<td>{$reward['created_at']}</td>";
                                                        echo "<td>
    
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

<!-- <button class='btn btn-sm btn-primary' onclick='editReward(" . json_encode($reward) . ")'>Edit</button> -->

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
$('#paymentMode').on('change', function () {
    if ($(this).val() === 'bank') {
        $('#bankNameField').show();
        $('#transactionTypeField').show();
    } else {
        $('#bankNameField').hide();
        $('#transactionTypeField').hide();
    }
});
</script>

             <script>
function editReward(data) {

    // hidden ids
    $('#reward_id').val(data.id);
    $('#sponsor_id').val(data.associate_id);

    // select2 associate
    $('#associate_name')
        .val(data.associate_id)
        .trigger('change');

    // basic fields
    $('input[name="sv1"]').val(data.sv1_amount);
    $('input[name="cust_name"]').val(data.customer_name);
    $('input[name="cust_mobile"]').val(data.customer_mobile);
    $('input[name="passbook"]').val(data.passbook_no);
    $('input[name="amount"]').val(data.site_visit_date);
    $('input[name="plot_number"]').val(data.plot_number);
    $('input[name="extend"]').val(data.extend_days);
    $('input[name="debit_voucher"]').val(data.debit_voucher_no);
    $('#description').val(data.remark);

    // transaction mode (cash/bank)
    $('#paymentMode').val(data.transaction_type).trigger('change');

    // BANK MODE HANDLING
    if (data.transaction_type === 'bank') {

        $('#bankNameField').show();
        $('#transactionTypeField').show();

        $('input[name="trans_number"]').val(data.bank_name ?? '');
        $('#transactionType').val(data.imps_neft_type ?? '');

    } else {
        $('#bankNameField').hide();
        $('#transactionTypeField').hide();

        $('input[name="trans_number"]').val('');
        $('#transactionType').val('');
    }

    // scroll to form (UX)
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
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
        // Show/hide bank name field based on payment mode
        document.getElementById('paymentMode').addEventListener('change', function() {
            const bankField = document.getElementById('bankNameField');
            // const bankAccountField = document.getElementById('bankAccountField');
            // const bankIfscField = document.getElementById('bankIfscField');
            const transactionTypeField = document.getElementById('transactionTypeField');
            if (this.value === 'bank') {
                bankField.style.display = 'block';
                // bankAccountField.style.display = 'block';
                // bankIfscField.style.display = 'block';
                transactionTypeField.style.display = 'block';
                bankField.querySelector('input').required = true;
                // bankAccountField.querySelector('input').required = true;
                // bankIfscField.querySelector('input').required = true;
            } else {
                bankField.style.display = 'none';
                // bankAccountField.style.display = 'none';
                // bankIfscField.style.display = 'none';
                bankField.querySelector('input').required = false;
                // bankAccountField.querySelector('input').required = false;
                // bankIfscField.querySelector('input').required = false;
                transactionTypeField.style.display = 'none';
            }
        });
    </script>


        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Select Associate',
                    allowClear: true
                });

                $('#associate_name').on('change', function() {
                    let selectedSponsorId = $(this).val(); // sponsor_id is the value of <option>
                    $('#sponsor_id').val(selectedSponsorId); // assign it to hidden input
                });
            });
        </script>


</body>

</html>