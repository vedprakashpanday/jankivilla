<?php
 error_reporting(E_ALL);
ini_set('display_errors', 1);;
session_start();
include_once "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header("Location: ../../adminlogin.php"); // Redirect to dashboard
    exit;
}

if(isset($_POST['btnsubmit'])){

$cash_amount = null;
$cheque_amount = null;
$transfer_amount = null;

if ($_POST['payment_mode'] === 'cash') {
    $cash_amount = isset($_POST['cash_amount']) && $_POST['cash_amount'] !== ''
        ? (float) $_POST['cash_amount']
        : 0.00;
}

if ($_POST['payment_mode'] === 'cheque') {
    $cheque_amount = isset($_POST['cheque_amount']) && $_POST['cheque_amount'] !== ''
        ? (float) $_POST['cheque_amount']
        : 0.00;
}

if ($_POST['payment_mode'] === 'bank_transfer') {
    $transfer_amount = isset($_POST['transfer_amount']) && $_POST['transfer_amount'] !== ''
        ? (float) $_POST['transfer_amount']
        : 0.00;
}
    $sql = "INSERT INTO tbl_rewards (
    r_name, r_code, r_desig, pb_no, dv_no, expense_type,
    payment_mode, cash_amount,
    cheque_amount, cheque_number, chequebank_name, cheque_date,
    transfer_amount, neft_payment, rtgs_payment, utr_number,
    bank_name, ba_number, ifsc,
    description, date
) VALUES (
    ?,?,?,?,?,?,
    ?,?,
    ?,?,?,?,
    ?,?,?,?,
    ?,?,?,
    ?,?
)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $_POST['r_name'],
        $_POST['r_code'] ?? null,
        $_POST['r_desig'] ?? null,
        $_POST['pb_no'] ?? null,
        $_POST['dv_no'] ?? null,
        $_POST['expense_type'],

        $_POST['payment_mode'],

        $_POST['payment_mode']=='cash' ? $cash_amount : null,

        $_POST['payment_mode']=='cheque' ? $cheque_amount : null,
        $_POST['payment_mode']=='cheque' ? $_POST['cheque_number'] : null,
        $_POST['payment_mode']=='cheque' ? $_POST['chequebank_name'] : null,
        $_POST['payment_mode']=='cheque' ? $_POST['cheque_date'] : null,

        $_POST['payment_mode']=='bank_transfer' ? $transfer_amount : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['neft_payment'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['rtgs_payment'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['utr_number'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['bank_name'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['ba_number'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['ifsc'] : null,

        $_POST['description'] ?? null,
        $_POST['date'] ?? null
    ]);

    echo "<script>alert('Reward Added Successfully');</script>";
}

if(isset($_POST['btnupdate'])){

    $sql = "UPDATE tbl_rewards SET
        r_name=?, r_code=?, r_desig=?,pb_no=?, dv_no=?, expense_type=?,
        payment_mode=?, cash_amount=?,
        cheque_amount=?, cheque_number=?, chequebank_name=?, cheque_date=?,
        transfer_amount=?, neft_payment=?, rtgs_payment=?, utr_number=?, bank_name=?, ba_number=?, ifsc=?,
        description=?,date=?
        WHERE id=?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $_POST['r_name'],
        $_POST['r_code'],
        $_POST['r_code'],
        $_POST['pb_no'],
        $_POST['dv_no'],
        $_POST['expense_type'],

        $_POST['payment_mode'],

        $_POST['payment_mode']=='cash' ? $_POST['cash_amount'] : null,

        $_POST['payment_mode']=='cheque' ? $_POST['cheque_amount'] : null,
        $_POST['payment_mode']=='cheque' ? $_POST['cheque_number'] : null,
        $_POST['payment_mode']=='cheque' ? $_POST['chequebank_name'] : null,
        $_POST['payment_mode']=='cheque' ? $_POST['cheque_date'] : null,

        $_POST['payment_mode']=='bank_transfer' ? $_POST['transfer_amount'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['neft_payment'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['rtgs_payment'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['utr_number'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['bank_name'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['ba_number'] : null,
        $_POST['payment_mode']=='bank_transfer' ? $_POST['ifsc'] : null,

        $_POST['description'],
        $_POST['date'],
        $_POST['reward_id']
    ]);

    echo "<script>alert('Reward Updated Successfully');</script>";
}

if(isset($_POST['delete_id'])){
    $stmt = $pdo->prepare("DELETE FROM tbl_rewards WHERE id=?");
    $stmt->execute([$_POST['delete_id']]);

    header("Location: rewards.php");
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
                                                        <input type="hidden" name="reward_id" value="<?= $edit['id'] ?>">

                                                        <h2>Add Rewards</h2>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <b>Name Of Employee/Marketing/others:</b>
                                                                <i>
                                                                   <i> <input name="r_name" type="text" id="r_name" class="form-control" style="font-weight:bold;" placeholder="Enter Receiver's Name Here"></i>
                                                                </i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>Code Of Employee/Marketing/others:</b>
                                                                <i>
                                                                   <i> <input name="r_code" type="text" id="r_code" class="form-control" style="font-weight:bold;" placeholder="Enter Receiver's Code Here"></i>
                                                                </i>
                                                            </div> 
                                                            <div class="col-md-4 mb-3">
                                                                <b>Designation  Of Employee/Marketing/others :</b>
                                                                <i>
                                                                   <i> <input name="r_desig" type="text" id="r_desig" class="form-control" style="font-weight:bold;" placeholder="Enter Receiver's Designation Here"></i>
                                                                </i>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <b>On The Behalf of PB No. :</b>
                                                                <i>
                                                                   <i> <input name="pb_no" type="text" id="pb_no" class="form-control" style="font-weight:bold;" placeholder="Enter Receiver's PassBook No. Here"></i>
                                                                </i>
                                                            </div>

                                                            

                                                           

                                                            <div class="col-md-4 mb-3">
                                                                <b>Select Occassion:</b>
                                                                <i>
                                                                   <i> <input
        type="text"
        name="expense_type"
        class="form-control"
        list="expenseTypeList"
        placeholder="-- Select Expense Type --"
        required
    >

    <datalist id="expenseTypeList">
        <option value="Cash Prize"></option>
        <option value="Incentive"></option>
        <option value="Gift Expense"></option>
    </datalist></i>
                                                                </i>
                                                            </div>

                                                             <div class="col-md-4 mb-3">
                                                                <b>Payment Mode :</b>
                                                                               <i>
                                                                   <i>
                                                                                <select class="form-control" name="payment_mode" id="payment_mode" onchange="showPaymentDetails();">
                            <option value="">Select Payment Mode</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="bank_transfer">Bank Transfer</option>
                          </select>
                          </i>
                                                                </i>
                                                                            </div>

                                                                           
                                                                           <div id="cash_details" class="payment-details col-md-3 mb-3" style="display:none;">
                            
                                                                        <label>Amount Paid:</label>
                                                                        <input type="text" class="form-control" name="cash_amount" id="cash_amount" placeholder="Enter Cash Amount" >
                                                                    
                                                                    </div>

                          <div id="cheque_details" class="payment-details col-12" style="display:none;">
                            <div class="form-row">
                              <div class="form-group col-md-3">
                                <label>Cheque Amount:</label>
                                <input type="text" class="form-control" name="cheque_amount" id="cheque_amount" placeholder="Enter Cheque Amount" >
                              </div>
                              <div class="form-group col-md-3">
                                <label>Cheque Number:</label>
                                <input type="text" class="form-control" name="cheque_number" id="cheque_number" placeholder="Enter Cheque Number">
                              </div>
                              <div class="form-group col-md-3">
                                <label>Bank Name:</label>
                                <input type="text" class="form-control" name="chequebank_name" id="chequebank_name" placeholder="Enter Bank Name">
                              </div>
                              <div class="form-group col-md-3">
                                <label>Cheque Date:</label>
                                <input type="date" class="form-control" name="cheque_date" id="cheque_date">
                              </div>
                            </div>
                          </div>

                          <div id="bank_transfer_details" class="payment-details col-12" style="display:none;">
                            <div class="form-row">
                              <div class="form-group col-md-3">
                                <label>Amount Transferred:</label>
                                <input type="text" class="form-control" name="transfer_amount" id="transfer_amount" placeholder="Enter Transfer Amount" >
                              </div>
                              <div class="form-group col-md-3">
                                <label>NEFT Reference Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="neft_payment" id="neft_payment" placeholder="Enter NEFT Reference Number">
                              </div>
                              <div class="form-group col-md-3">
                                <label>RTGS Reference Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="rtgs_payment" id="rtgs_payment" placeholder="Enter RTGS Reference Number">
                              </div>
                              <div class="form-group col-md-3">
                                <label>UTR Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="utr_number" id="utr_number" placeholder="Enter UTR Number">
                              </div>
                               <div class="form-group col-md-3">
                                <label>Bank Name:</label>
                                <input type="text" class="form-control bank-transfer-field" name="bank_name" id="bank_name" placeholder="Enter UTR Number">
                              </div>
                               <div class="form-group col-md-3">
                                <label>Bank Account Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="ba_number" id="ba_number" placeholder="Enter UTR Number">
                              </div>
                               <div class="form-group col-md-3">
                                <label>Bank IFSC Code:</label>
                                <input type="text" class="form-control bank-transfer-field" name="ifsc" id="ifsc" placeholder="Enter UTR Number">
                              </div>
                                 <div class="form-group col-md-3">
                                <label>Select Sender's Bank:</label>
                                <select name="s_bank" id="s_bank" class="form-control bank-transfer-field">
                                    <option >Select  Bank</option>
                                     <?php
    $customers = $pdo->query(
        "SELECT bank_name FROM tbl_bank_details where member_id='JV000001' ORDER BY id DESC"
    )->fetchAll(PDO::FETCH_ASSOC);

    foreach($customers as $row):
    ?>
                                    <option value="<?= $row['bank_name'] ?>"><?= $row['bank_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                              </div>
                            </div>
                          </div>
                           <div class="col-md-4 mb-3">
                                                                <b>D.V. Number:</b>
                                                                <i>
                                                                   <i> <input name="dv_no" type="text" id="dv_no" class="form-control" style="font-weight:bold;" placeholder="Enter Receiver's Debit Voucher No. Here"></i>
                                                                </i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Remarks:</b>
                                                                <i>
                                                                    <textarea name="description" id="description" class="form-control" style="font-weight:bold;" rows="6" cols="7"></textarea>
                                                                </i>
                                                            </div>
                                                             <div class="col-md-4 mb-3">
                                                                <b>Date:</b>
                                                                <i>
                                                                   <i> <input name="date" type="date" id="date" class="form-control" style="font-weight:bold;" placeholder="Enter Date Here"></i>
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
                                    <div class="row pt-5 mx-5">
                                        <div class="col-md-12 overflow-auto" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Rewards Report</h3>
                                         <table class="table table-bordered table-striped table-sm ">
    <thead class="table-dark text-center align-middle">
        <tr>
            <th>#</th>
            <th>Name Of Employee/Marketing/others</th>
            <th>Code Of Employee/Marketing/others</th>
            <th>Designation  Of Employee/Marketing/others</th>
            <th>Passbook No</th>
            <th>DV No</th>
            <th>Expense Type</th>
            <th>Payment Mode</th>

            <th>Cash Amount</th>

            <th>Cheque Amount</th>
            <th>Cheque No</th>
            <th>Cheque Bank</th>
            <th>Cheque Date</th>

            <th>Transfer Amount</th>
            <th>NEFT Ref</th>
            <th>RTGS Ref</th>
            <th>UTR No</th>
            <th>Bank Name</th>
            <th>Bank A/C</th>
            <th>IFSC</th>

            <th>Remarks</th>
            <th>Created At</th>
            <th width="180">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM tbl_rewards ORDER BY created_at DESC");
        $sn = 1;

        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$sn}</td>";
            echo "<td>{$r['r_name']}</td>";
            echo "<td>{$r['r_code']}</td>";
            echo "<td>{$r['r_desig']}</td>";
            echo "<td>{$r['pb_no']}</td>";
            echo "<td>{$r['dv_no']}</td>";
            echo "<td>{$r['expense_type']}</td>";
            echo "<td class='text-capitalize'>{$r['payment_mode']}</td>";

            echo "<td>" . ($r['cash_amount'] ? "₹".number_format($r['cash_amount'],2) : '-') . "</td>";

            echo "<td>" . ($r['cheque_amount'] ? "₹".number_format($r['cheque_amount'],2) : '-') . "</td>";
            echo "<td>{$r['cheque_number']}</td>";
            echo "<td>{$r['chequebank_name']}</td>";
            echo "<td>{$r['cheque_date']}</td>";

            echo "<td>" . ($r['transfer_amount'] ? "₹".number_format($r['transfer_amount'],2) : '-') . "</td>";
            echo "<td>{$r['neft_payment']}</td>";
            echo "<td>{$r['rtgs_payment']}</td>";
            echo "<td>{$r['utr_number']}</td>";
            echo "<td>{$r['bank_name']}</td>";
            echo "<td>{$r['ba_number']}</td>";
            echo "<td>{$r['ifsc']}</td>";

            echo "<td>{$r['description']}</td>";
            echo "<td>" . date('d-m-Y H:i', strtotime($r['created_at'])) . "</td>";

            echo "<td>
                <button class='btn btn-sm btn-primary mb-1'
                    onclick='editReward(" . json_encode($r) . ")'>Edit</button>

                <form method='post' style='display:inline'
                    onsubmit='return confirm(\"Delete this record?\")'>
                    <input type='hidden' name='delete_id' value='{$r['id']}'>
                    <button class='btn btn-sm btn-danger mb-1'>Delete</button>
                </form>

                <a href='print_rewards.php?id={$r['id']}'
                   class='btn btn-sm btn-success fw-bold d-block'>
                   Print
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
                        $('#reward_id').val(data.id);
                        $('#sponsor_id').val(data.sponsor_id).trigger('change'); // Set hidden input
                        $('#associate_name').val(data.sponsor_id).trigger('change'); // Set select2
                        $('input[name="amount"]').val(data.amount);
                        $('#description').val(data.description);
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
            const bankAccountField = document.getElementById('bankAccountField');
            const bankIfscField = document.getElementById('bankIfscField');
            if (this.value === 'bank') {
                bankField.style.display = 'block';
                bankAccountField.style.display = 'block';
                bankIfscField.style.display = 'block';
                bankField.querySelector('input').required = true;
                bankAccountField.querySelector('input').required = true;
                bankIfscField.querySelector('input').required = true;
            } else {
                bankField.style.display = 'none';
                bankAccountField.style.display = 'none';
                bankIfscField.style.display = 'none';
                bankField.querySelector('input').required = false;
                bankAccountField.querySelector('input').required = false;
                bankIfscField.querySelector('input').required = false;
            }
        });
    </script>

<script>
    function showPaymentDetails() {
      document.getElementById("cash_details").style.display = "none";
      document.getElementById("cheque_details").style.display = "none";
      document.getElementById("bank_transfer_details").style.display = "none";

      var paymentMethod = document.getElementById("payment_mode").value;

      if (paymentMethod === "cash") {
        document.getElementById("cash_details").style.display = "block";
      } else if (paymentMethod === "cheque") {
        document.getElementById("cheque_details").style.display = "block";
      } else if (paymentMethod === "bank_transfer") {
        document.getElementById("bank_transfer_details").style.display = "block";
        document.getElementById("neft_payment").style.display = "block";
        document.getElementById("rtgs_payment").style.display = "block";
        document.getElementById("utr_number").style.display = "block";
      }
    }

    function setupBankTransferFieldListeners() {
      const fields = ['neft_payment', 'rtgs_payment', 'utr_number'];
      fields.forEach(field => {
        document.getElementById(field).addEventListener('input', function() {
          if (this.value.trim() !== '') {
            fields.forEach(otherField => {
              if (otherField !== field) {
                document.getElementById(otherField).style.display = 'none';
              }
            });
          } else {
            fields.forEach(otherField => {
              document.getElementById(otherField).style.display = 'block';
            });
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', setupBankTransferFieldListeners);

    function appendMemberID() {
      var paymentMethod = document.getElementById("payment_mode").value;
      var netAmount = parseFloat(document.getElementById("net_amount").value) || 0;
      var cashAmount = parseFloat(document.getElementById("cash_amount").value) || 0;
      var chequeAmount = parseFloat(document.getElementById("cheque_amount").value) || 0;
      var transferAmount = parseFloat(document.getElementById("transfer_amount").value) || 0;
      var neftPayment = document.getElementById("neft_payment").value.trim();
      var rtgsPayment = document.getElementById("rtgs_payment").value.trim();
      var utrNumber = document.getElementById("utr_number").value.trim();
      var chequeNumber = document.getElementById("cheque_number").value.trim();
      var bankName = document.getElementById("bank_name").value.trim();
      var chequeDate = document.getElementById("cheque_date").value;

      if (!paymentMethod) {
        alert("Please select a payment mode.");
        return false;
      }

      if (paymentMethod === "cash" && (!cashAmount || cashAmount <= 0)) {
        alert("Please enter a valid cash amount.");
        return false;
      }

      if (paymentMethod === "cheque") {
        if (!chequeAmount || chequeAmount <= 0) {
          alert("Please enter a valid cheque amount.");
          return false;
        }
        if (!chequeNumber || !bankName || !chequeDate) {
          alert("Please provide cheque number, bank name, and cheque date.");
          return false;
        }
      }

      if (paymentMethod === "bank_transfer") {
        if (!transferAmount || transferAmount <= 0) {
          alert("Please enter a valid transfer amount.");
          return false;
        }
        const filledFields = [neftPayment, rtgsPayment, utrNumber].filter(val => val !== '').length;
        if (filledFields === 0) {
          alert("Please provide NEFT, RTGS, or UTR number for bank transfer.");
          return false;
        }
        if (filledFields > 1) {
          alert("Please provide only one of NEFT, RTGS, or UTR number.");
          return false;
        }
      }

      return true;
    }
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