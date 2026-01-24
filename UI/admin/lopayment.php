<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =========================
       INSERT NEW RECORD
    ==========================*/
    if (isset($_POST['submit'])) {

        // JSON fields (safe)
        $khesra_no = !empty($_POST['khesra_no'])
            ? json_encode($_POST['khesra_no'], JSON_UNESCAPED_UNICODE)
            : json_encode([]);

        $rakuwa = !empty($_POST['rakuwa'])
            ? json_encode($_POST['rakuwa'], JSON_UNESCAPED_UNICODE)
            : json_encode([]);

        $rate_per_katha = !empty($_POST['rate_per_katha'])
            ? json_encode($_POST['rate_per_katha'], JSON_UNESCAPED_UNICODE)
            : json_encode([]);

        // Owner details
        $land_owner_name = $_POST['land_owner_name'];
        $relation_name   = $_POST['relation_name'];
        $address         = $_POST['address'];
        $mobile1         = $_POST['mobile1'];
        $mobile2         = $_POST['mobile2'];
        $mauze_name      = $_POST['mauze_name'];
        $thana_no        = $_POST['thana_no'];
        $total_land_value = $_POST['total_land_value'];

        // Nominee details
        $nom_name       = $_POST['nominee_name'] ?? null;
        $nom_relation   = $_POST['nominee_so_do_wo'] ?? null;
        $nom_dob        = !empty($_POST['nominee_dob']) ? $_POST['nominee_dob'] : null;
        $nom_mobile = !empty($_POST['nominee_mobile'])
    ? (int) $_POST['nominee_mobile']
    : null;

$nom_alt_mobile = !empty($_POST['nominee_alternate_mobile'])
    ? (int) $_POST['nominee_alternate_mobile']
    : null;

$nom_pincode = !empty($_POST['nominee_pincode'])
    ? (int) $_POST['nominee_pincode']
    : null;
        $nom_email      = $_POST['nominee_email'] ?? null;
        $nom_aadhar     = $_POST['nominee_aadhar'] ?? null;
        $nom_pan        = $_POST['nominee_pan'] ?? null;
        $nom_address    = $_POST['nominee_address'] ?? null;
        // $nom_pincode    = $_POST['nominee_pincode'] ?? null;
        $nom_state      = $_POST['nominee_state'] ?? null;
        $nom_district   = $_POST['nominee_district'] ?? null;

        $status = 'active';

        $stmt = $pdo->prepare("
            INSERT INTO land_owner_payments
            (
                land_owner_name, relation_name, address, mobile1, mobile2,
                mauze_name, thana_no, khesra_no, rakuwa, rate_per_katha,
                total_land_value, nom_name, nom_relation, nom_dob,
                nom_mobile, nom_alt_mobile, nom_email, nom_aadhar, nom_pan,
                nom_address, nom_pin, nom_state, nom_district, status
            )
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $land_owner_name,
            $relation_name,
            $address,
            $mobile1,
            $mobile2,
            $mauze_name,
            $thana_no,
            $khesra_no,
            $rakuwa,
            $rate_per_katha,
            $total_land_value,
            $nom_name,
            $nom_relation,
            $nom_dob,
            $nom_mobile,
            $nom_alt_mobile,
            $nom_email,
            $nom_aadhar,
            $nom_pan,
            $nom_address,
            $nom_pincode,
            $nom_state,
            $nom_district,
            $status
        ]);

        $new_id = $pdo->lastInsertId();

        echo "<script>
            alert('Land Owner Payment inserted successfully!');
            window.location.href='?id={$new_id}';
        </script>";
        exit;
    }

    /* =========================
       UPDATE EXISTING RECORD
    ==========================*/
    if (isset($_POST['update']) && !empty($_POST['edit_id'])) {

        $edit_id = $_POST['edit_id'];

        // JSON fields (update)
        $khesra_no = !empty($_POST['khesra_no'])
            ? json_encode($_POST['khesra_no'], JSON_UNESCAPED_UNICODE)
            : json_encode([]);

        $rakuwa = !empty($_POST['rakuwa'])
            ? json_encode($_POST['rakuwa'], JSON_UNESCAPED_UNICODE)
            : json_encode([]);

        $rate_per_katha = !empty($_POST['rate_per_katha'])
            ? json_encode($_POST['rate_per_katha'], JSON_UNESCAPED_UNICODE)
            : json_encode([]);

        // Owner details
        $land_owner_name = $_POST['land_owner_name'];
        $relation_name   = $_POST['relation_name'];
        $address         = $_POST['address'];
        $mobile1         = $_POST['mobile1'];
        $mobile2         = $_POST['mobile2'];
        $mauze_name      = $_POST['mauze_name'];
        $thana_no        = $_POST['thana_no'];
        $total_land_value = $_POST['total_land_value'];

        // Nominee details
        $nom_name       = $_POST['nominee_name'] ?? null;
        $nom_relation   = $_POST['nominee_so_do_wo'] ?? null;
        $nom_dob        = !empty($_POST['nominee_dob']) ? $_POST['nominee_dob'] : null;
        $nom_mobile = !empty($_POST['nominee_mobile'])
    ? (int) $_POST['nominee_mobile']
    : null;

$nom_alt_mobile = !empty($_POST['nominee_alternate_mobile'])
    ? (int) $_POST['nominee_alternate_mobile']
    : null;

$nom_pincode = !empty($_POST['nominee_pincode'])
    ? (int) $_POST['nominee_pincode']
    : null;
        $nom_email      = $_POST['nominee_email'] ?? null;
        $nom_aadhar     = $_POST['nominee_aadhar'] ?? null;
        $nom_pan        = $_POST['nominee_pan'] ?? null;
        $nom_address    = $_POST['nominee_address'] ?? null;
        //$nom_pincode    = $_POST['nominee_pincode'] ?? null;
        $nom_state      = $_POST['nominee_state'] ?? null;
        $nom_district   = $_POST['nominee_district'] ?? null;

        $stmt = $pdo->prepare("
            UPDATE land_owner_payments SET
                land_owner_name=?,
                relation_name=?,
                address=?,
                mobile1=?,
                mobile2=?,
                mauze_name=?,
                thana_no=?,
                khesra_no=?,
                rakuwa=?,
                rate_per_katha=?,
                total_land_value=?,
                nom_name=?,
                nom_relation=?,
                nom_dob=?,
                nom_mobile=?,
                nom_alt_mobile=?,
                nom_email=?,
                nom_aadhar=?,
                nom_pan=?,
                nom_address=?,
                nom_pin=?,
                nom_state=?,
                nom_district=?
            WHERE id=?
        ");

        $stmt->execute([
            $land_owner_name,
            $relation_name,
            $address,
            $mobile1,
            $mobile2,
            $mauze_name,
            $thana_no,
            $khesra_no,
            $rakuwa,
            $rate_per_katha,
            $total_land_value,
            $nom_name,
            $nom_relation,
            $nom_dob,
            $nom_mobile,
            $nom_alt_mobile,
            $nom_email,
            $nom_aadhar,
            $nom_pan,
            $nom_address,
            $nom_pincode,
            $nom_state,
            $nom_district,
            $edit_id
        ]);

        echo "<script>
            alert('Record updated successfully!');
            window.location.href='?id={$edit_id}';
        </script>";
        exit;
    }


    // Add payment transaction (Ledger Entry) - SEPARATE ACTION
  if (isset($_POST['add_transaction'])) {

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit();
    $id=$_GET['id'];
    $land_owner_id   = $_POST['land_owner_id'];
    $transaction_date = $_POST['transaction_date'];
    $payment_mode     = $_POST['payment_mode'];

    // Bank related (NULL safe)
    $bank_name =
    !empty($_POST['bank_name']) 
        ? $_POST['bank_name'] 
        : (!empty($_POST['chequebank_name']) 
            ? $_POST['chequebank_name'] 
            : null);

    $account_number = !empty($_POST['ba_number']) ? $_POST['ba_number'] : null;
    $ifsc_code = !empty($_POST['ifsc']) ? $_POST['ifsc'] : null;
    $cheque_number=!empty($_POST['cheque_number']) ? $_POST['cheque_number'] : null;
    $cheque_date=!empty($_POST['cheque_date']) ? $_POST['cheque_date'] : null;
    $neft=!empty($_POST['neft_payment']) ? $_POST['neft_payment'] : null;
     $rtgs=!empty($_POST['rtgs_payment']) ? $_POST['rtgs_payment'] : null;
      $utr=!empty($_POST['utr_number']) ? $_POST['utr_number'] : null;

    $transaction_type = $_POST['transaction_type'];
   $amount =
    !empty($_POST['cash_amount'])     ? (float) $_POST['cash_amount'] :
    (!empty($_POST['cheque_amount'])  ? (float) $_POST['cheque_amount'] :
    (!empty($_POST['transfer_amount'])? (float) $_POST['transfer_amount'] :
    null));

    $dv_no   = !empty($_POST['dv_no']) ? $_POST['dv_no'] : null;
    $remarks = !empty($_POST['remarks']) ? $_POST['remarks'] : null;

    // Required validation
    if (empty($transaction_date) || empty($payment_mode) || empty($transaction_type) || empty($amount)) {
        // echo "<script>
        //     alert('Please fill all required transaction fields!');
        //     window.location.href='?id={$land_owner_id}';
        // </script>";
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO land_payment_transactions
        (
            land_owner_id,
            transaction_date,
            payment_mode,
            bank_name,
            account_number,
            ifsc,
            transaction_type,
            amount,
            dv_no,
            cheque_number,
            cheque_date,
            neft,
            rtgs,
            utr,
            created_at,
            remarks
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, now(), ?)
    ");

    if ($stmt->execute([
        $land_owner_id,
        $transaction_date,
        $payment_mode,
        $bank_name,
        $account_number,
        $ifsc_code,
        $transaction_type,
        $amount,
        $dv_no,
        $cheque_number,
        $cheque_date,
        $neft,
        $rtgs,
        $utr,
        $remarks
    ])) {
        echo "<script>
            alert('Payment transaction added successfully!');
            window.location.href='https://jankivilla.com/UI/admin/lopayment.php?id={$id}';
        </script>";
        exit;
    }
}


    // Delete payment transaction
    if (isset($_POST['delete_transaction_id'])) {
        $transaction_id = $_POST['delete_transaction_id'];
        // Get land owner ID before deleting
        $get_id = $pdo->prepare("SELECT land_owner_id FROM land_payment_transactions WHERE id = ?");
        $get_id->execute([$transaction_id]);
        $trans_data = $get_id->fetch(PDO::FETCH_ASSOC);
        $land_owner_id = $trans_data['land_owner_id'];

        $stmt = $pdo->prepare("DELETE FROM land_payment_transactions WHERE id = ?");
        if ($stmt->execute([$transaction_id])) {
            echo "<script>alert('Transaction deleted successfully!'); window.location.href='?id=$land_owner_id';</script>";
        }
        exit;
    }

    // Delete land owner record
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        // Delete transactions first (if not using CASCADE)
        $pdo->prepare("DELETE FROM land_payment_transactions WHERE land_owner_id = ?")->execute([$delete_id]);
        // Delete land owner
        $stmt = $pdo->prepare("DELETE FROM land_owner_payments WHERE id = ?");
        if ($stmt->execute([$delete_id])) {
            echo "<script>alert('Record deleted successfully!'); window.location.href='landowner.php';</script>";
        }
        exit;
    }
}

// Fetch single record for edit
$edit_data = null;
$edit_id = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM land_owner_payments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($edit_data) {
        $edit_id = $_GET['id'];
    }
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


    <style>
        .navbar .navbar-brand-wrapper .navbar-brand img {
            margin-top: 0px;
        }

        #ct7 {
            color: #fff;
            padding: 18px 8px;
            font-size: 16px;
            font-weight: 900;
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


    <style type="text/css">
        /* Chart.js */
        @keyframes chartjs-render-animation {
            from {
                opacity: .99
            }

            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            animation: chartjs-render-animation 1ms
        }

        .chartjs-size-monitor,
        .chartjs-size-monitor-expand,
        .chartjs-size-monitor-shrink {
            position: absolute;
            direction: ltr;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
            visibility: hidden;
            z-index: -1
        }

        .chartjs-size-monitor-expand>div {
            position: absolute;
            width: 1000000px;
            height: 1000000px;
            left: 0;
            top: 0
        }

        .chartjs-size-monitor-shrink>div {
            position: absolute;
            width: 200%;
            height: 200%;
            left: 0;
            top: 0
        }


        .franchiseSidebar:hover {
            background: #ff9027 !important;
        }



        /* Form Container */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
        }

        /* Heading Styling */
        .form-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 20px;
        }

        /* Label Styling */
        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        /* Input Fields */
        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="file"]:focus {
            border-color: #4A90E2;
            outline: none;
        }

        /* Button Styling */
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4A90E2;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #357ABD;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .form-container {
                width: 90%;
                padding: 20px;
            }

            .form-container h2 {
                font-size: 20px;
            }
        }
    </style>

    <script type="text/ecmascript">
        var loadFile = function(event) {
            var image = document.getElementById('output');
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>



</head>

<body class="hold-transition skin-blue sidebar-mini" data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <div class="wrapper">
        <div class="container-scroller">
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <div class="franchise_nav_menu">
                    <?php include "adminheadersidepanel.php"; ?>
                </div>


                <div class="main-panel">
                    <div class="card px-5">
                        <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                            <h2>
                                Add Land Owner Payment Details
                            </h2>
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div style="background: #fff; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                        
                                        <div class="">
                                            <div class="col-12" style="margin: unset!important;">
                                                 <div id="editCard" class="card shadow mb-5 rounded-4 <?= isset($_GET['id']) ? '' : 'd-none' ?>">

                                                    <div class="card-header bg-primary text-white rounded-top-4">
                                                        <h4 class="mb-0">
                                                            <h4 class="mb-0"><?php echo isset($edit_id) ? 'Edit Land Owner Payment' : 'Add Land Owner Payment'; ?></h4>
                                                        </h4>
                                                    </div>
                                                    <div class="card-body p-4">                                                        

                                                        <!-- Payment Ledger Entry Section - SEPARATE FORM -->
                                                        <?php if (isset($edit_id)): ?>
                                                            <form method="post" id="paymentForm">
                                                                <input type="hidden" name="land_owner_id" value="<?php echo $edit_id; ?>">

                                                                <div class="card mb-4">
                                                                    <div class="card-header bg-success text-white">
                                                                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Payment Ledger Entry</h5>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">Transaction Date <span class="text-danger">*</span></label>
                                                                                <input type="date" class="form-control" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                                            </div>

                                                                            <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">Payment Mode <span class="text-danger">*</span></label>
                                                                                <select class="form-control" name="payment_mode" id="payment_mode" onchange="showPaymentDetails();">
                            <option value="">Select Payment Mode</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="bank_transfer">Bank Transfer</option>
                          </select>
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
                               <div class="form-group col-md-4">
                                <label>Bank Name:</label>
                                <input type="text" class="form-control bank-transfer-field" name="bank_name" id="bank_name" placeholder="Enter UTR Number">
                              </div>
                               <div class="form-group col-md-4">
                                <label>Bank Account Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="ba_number" id="ba_number" placeholder="Enter UTR Number">
                              </div>
                               <div class="form-group col-md-4">
                                <label>Bank IFSC Code:</label>
                                <input type="text" class="form-control bank-transfer-field" name="ifsc" id="ifsc" placeholder="Enter UTR Number">
                              </div>
                            </div>
                          </div>
                                                                            <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">Transaction Type <span class="text-danger">*</span></label>
                                                                                <select class="form-control" name="transaction_type" id="transactionType" required>
                                                                                    <option value="">Select Type</option>
                                                                                    <option value="credit">Credit (Payment Received)</option>
                                                                                    <option value="debit">Debit (Payment Made)</option>
                                                                                </select>
                                                                            </div>

                                                                            <!-- <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                                                                <input type="number" step="0.01" class="form-control" name="amount" placeholder="Enter amount" required>
                                                                            </div> -->

                                                                            <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">D.V. No.</label>
                                                                                <input type="text" class="form-control" name="dv_no" placeholder="Document Voucher No.">
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label class="form-label fw-semibold">Remarks</label>
                                                                                <input type="text" class="form-control" name="remarks" placeholder="Additional notes">
                                                                            </div>
                                                                        </div>

                                                                        <div class="alert alert-info">
                                                                            <?php
                                                                            // Calculate balance
                                                                            $stmt = $pdo->prepare("SELECT 
                    SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE -amount END) as paid_amount 
                    FROM land_payment_transactions WHERE land_owner_id = ?");
                                                                            $stmt->execute([$edit_id]);
                                                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                            $paid_amount = $result['paid_amount'] ?? 0;
                                                                            $total_land_value = $edit_data['total_land_value'] ?? 0;
                                                                            $balance = $total_land_value - $paid_amount;
                                                                            ?>
                                                                            <strong><i class="fas fa-info-circle"></i> Payment Summary:</strong><br>
                                                                            <div class="row mt-2">
                                                                                <div class="col-md-4">
                                                                                    <strong>Total Land Value:</strong> ₹<?php echo number_format($total_land_value, 2); ?>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <strong>Paid Amount:</strong> <span class="text-success">₹<?php echo number_format($paid_amount, 2); ?></span>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <strong>Balance Due:</strong>
                                                                                    <span class="<?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?> fw-bold">
                                                                                        ₹<?php echo number_format($balance, 2); ?>
                                                                                        <?php if ($balance <= 0): ?>
                                                                                            <i class="fas fa-check-circle"></i>
                                                                                        <?php endif; ?>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="text-end">
                                                                            <button type="submit" name="add_transaction" class="btn btn-success px-4 rounded-pill">
                                                                                <i class="fas fa-plus-circle"></i> Add Payment Entry
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>

                                                            <!-- Payment History Table -->
                                                            <div class="card mb-4">
                                                                <div class="card-header bg-info text-white">
                                                                    <h5 class="mb-0"><i class="fas fa-history"></i> Payment History</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-striped table-hover">
                                                                            <thead class="table-dark">
                                                                                <tr>
                                                                                    <th>#</th>
                                                                                    <th>Date</th>
                                                                                    <th>Mode</th>
                                                                                    <th>Bank</th>
                                                                                    <th>Bank Account Number</th>
                                                                                    <th>Bank IFSC Code</th>
                                                                                    <th>Cheque Number</th>
                                                                                    <th>Cheque Date</th>
                                                                                    <th>NEFT Ref. No.</th>
                                                                                    <th>RTGS Ref. No.</th>
                                                                                    <th>UTR Ref. No.</th>
                                                                                    <th>Type</th>
                                                                                    <th>Amount</th>
                                                                                    <th>D.V. No.</th>
                                                                                    <th>Remarks</th>
                                                                                    <th>Created_at</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                $transactions = $pdo->prepare("SELECT * FROM land_payment_transactions WHERE land_owner_id = ? ORDER BY transaction_date DESC, id DESC");
                                                                                $transactions->execute([$edit_id]);
                                                                                $trans_list = $transactions->fetchAll(PDO::FETCH_ASSOC);

                                                                                if (count($trans_list) > 0):
                                                                                    $counter = 1;
                                                                                    foreach ($trans_list as $trans):
                                                                                ?>
                                                                                        <tr>
                                                                                            <td><?php echo $counter++; ?></td>
                                                                                            <td><?php echo date('d-M-Y', strtotime($trans['transaction_date'])); ?></td>
                                                                                            <td>
                                                                                                <span class="badge <?php echo $trans['payment_mode'] == 'cash' ? 'bg-success' : 'bg-primary'; ?>">
                                                                                                    <?php echo strtoupper($trans['payment_mode']); ?>
                                                                                                </span>
                                                                                            </td>
                                                                                            <td><?php echo $trans['bank_name'] ? htmlspecialchars($trans['bank_name']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['account_number'] ? htmlspecialchars($trans['account_number']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['ifsc'] ? htmlspecialchars($trans['ifsc']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['cheque_number'] ? htmlspecialchars($trans['cheque_number']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['cheque_date'] ? htmlspecialchars($trans['cheque_date']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['neft'] ? htmlspecialchars($trans['neft']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['rtgs'] ? htmlspecialchars($trans['rtgs']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['utr'] ? htmlspecialchars($trans['utr']) : '-'; ?></td>
                                                                                            <td>
                                                                                                <span class="badge <?php echo $trans['transaction_type'] == 'credit' ? 'bg-success' : 'bg-danger'; ?>">
                                                                                                    <?php echo strtoupper($trans['transaction_type']); ?>
                                                                                                </span>
                                                                                            </td>
                                                                                            <td class="fw-bold">₹<?php echo number_format($trans['amount'], 2); ?></td>
                                                                                            <td><?php echo $trans['dv_no'] ? htmlspecialchars($trans['dv_no']) : '-'; ?></td>
                                                                                            
                                                                                            <td><?php echo $trans['remarks'] ? htmlspecialchars($trans['remarks']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['created_at'] ? htmlspecialchars($trans['created_at']) : '-'; ?></td>
                                                                                            <td>
                                                                                                <form method="post" class="d-inline" onsubmit="return confirm('Delete this transaction?');">
                                                                                                    <input type="hidden" name="delete_transaction_id" value="<?php echo $trans['id']; ?>">
                                                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                                                        🗑️
                                                                                                    </button>
                                                                                                </form>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php
                                                                                    endforeach;
                                                                                else:
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td colspan="9" class="text-center text-muted py-4">
                                                                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                                                            No payment transactions yet. Add your first payment above.
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php endif; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <?php if (count($trans_list) > 0): ?>
                                                                        <div class="text-end mt-3">
                                                                            <a href="landreceipt.php?landid=<?php echo $edit_id; ?>" class="btn btn-primary px-4 rounded-pill" target="_blank">
                                                                                <i class="fas fa-print"></i> Print Invoice
                                                                            </a>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>

                                                        <script>
                                                            // Show/hide bank name field based on payment mode
                                                            document.getElementById('paymentMode')?.addEventListener('change', function() {
                                                                const bankField = document.getElementById('bankNameField');
                                                                const bankInput = document.getElementById('bankNameInput');
                                                                if (this.value === 'bank') {
                                                                    bankField.style.display = 'block';
                                                                    bankInput.required = true;
                                                                } else {
                                                                    bankField.style.display = 'none';
                                                                    bankInput.required = false;
                                                                    bankInput.value = '';
                                                                }
                                                            });
                                                        </script>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <hr>
                                        <h4 class="mt-4">Land Owner Payment Report</h4> -->
                                        <div class="" style="overflow:auto;">


                                            <div class="">
                                                <table class="table table-striped">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Land Owner</th>
                                                            <th>Mobile(1)</th>
                                                            <th>Mobile(2)</th>
                                                            <th>Mauza Name</th>
                                                            <th>Thana No</th>
                                                            <th>Khesra No</th>
                                                            <th>Rakwa</th>
                                                            <th>Rate (Per Katha)</th>
                                                            <th>Total Land Value</th>
                                                            <!-- <th>Nominee Name</th>
                                                            <th>So/Do/Wo</th>
                                                            <th>Nominee Dob</th>
                                                            <th>Nominee Mobile</th>
                                                            <th>Nominee Alt Mobile</th>
                                                            <th>Nominee Email</th>
                                                            <th>Nominee Aadhar</th>
                                                            <th>Nominee PAN</th>
                                                            <th>Nominee Address</th>
                                                            <th>Nominee Pincode</th>
                                                            <th>Nominee State</th>
                                                            <th>Nominee District</th> -->
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        function jsonToText($json) {
    $arr = json_decode($json, true);
    return htmlspecialchars(implode(', ', is_array($arr) ? $arr : []));
}

                                                        $records = $pdo->query("SELECT * FROM land_owner_payments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($records as $row): ?>
                                                            <tr>
                                                               <td><?= htmlspecialchars($row['land_owner_name'] ?? '') ?></td>
<td><?= htmlspecialchars($row['mobile1'] ?? '') ?></td>
<td><?= htmlspecialchars($row['mobile2'] ?? '') ?></td>
<td><?= htmlspecialchars($row['mauze_name'] ?? '') ?></td>
<td><?= htmlspecialchars($row['thana_no'] ?? '') ?></td>
<td><?= jsonToText($row['khesra_no']) ?></td>
<td><?= jsonToText($row['rakuwa']) ?></td>
<td><?= htmlspecialchars($row['rate_per_katha']) ?></td>
<td><?= htmlspecialchars($row['total_land_value'] ?? '') ?></td>

                                                                <td>
                                                                    <a href="?id=<?= $row['id'] ?>&class='d-block'" 
   class="btn btn-sm btn-warning editBtn fw-bold">
   Add
</a>

                                                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                                    </form>
                                                                    <!-- <a href="landreceipt.php?landid=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Print</a> -->
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <?php include "adminfooter.php"; ?>
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
$(document).on('click', '.editBtn', function () {

    // d-none remove (show card)
    //$('#editCard').removeClass('d-none');

   // OR toggle (optional)
    $('#editCard').toggleClass('d-none');

});
</script>





   <script>
$(document).ready(function () {

    $('#num_o_k').on('change', function () {

        let selectedValue = parseInt($(this).val()) || 0;

        // Clear old fields
        $('#khesraFields').html('');
        $('#rakwaFields').html('');
        $('#rateFields').html('');

        for (let i = 1; i <= selectedValue; i++) {
            $('#khesraFields').append(`
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Khesra No. ${i}</label>
                    <input type="text"
                           class="form-control"
                           name="khesra_no[]"
                           placeholder="Enter Khesra No. ${i}">
                </div>
            `);

             $('#rakwaFields').append(`
                <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Rakwa ${i}</label>
                                                                            <input 
    type="text"
    class="form-control"
    name="rakuwa[]"
    id="rakuwa"
    value="<?php echo htmlspecialchars($edit_data['rakuwa'] ?? ''); ?>"
    placeholder="Enter value (e.g. 12.50)- ${i}"
>
                </div>
            `);

                 $('#rateFields').append(`
               <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Rate (Per Katha) ${i}</label>
                                                                            <input type="number" step="0.01" class="form-control" name="rate_per_katha[]" value="<?php echo htmlspecialchars($edit_data['rate_per_katha'] ?? ''); ?>">
                                                                        </div>  
            `);

               

         
        }
    });

});
</script>

   
        <script>
document.getElementById('rakuwa').addEventListener('input', function () {
    let val = this.value;

    // allow only digits + one dot
    val = val.replace(/[^0-9.]/g, '');

    // allow only one dot
    const parts = val.split('.');
    if (parts.length > 2) {
        val = parts[0] + '.' + parts.slice(1).join('');
    }

    // ❌ integer not allowed → must contain dot
    if (!val.includes('.') && val.length > 0) {
        this.setCustomValidity("Only decimal value allowed (e.g. 10.25)");
    } else {
        this.setCustomValidity("");
    }

    this.value = val;
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
</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>