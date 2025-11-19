<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add new land owner record
    if (isset($_POST['submit'])) {
        $land_owner_name = $_POST['land_owner_name'];
        $relation_name = $_POST['relation_name'];
        $address = $_POST['address'];
        $mobile1 = $_POST['mobile1'];
        $mobile2 = $_POST['mobile2'];
        $mauze_name = $_POST['mauze_name'];
        $thana_no = $_POST['thana_no'];
        $khesra_no = $_POST['khesra_no'];
        $rakuwa = $_POST['rakuwa'];
        $rate_per_katha = $_POST['rate_per_katha'];
        $total_land_value = $_POST['total_land_value'];
        $status = 'active';

        $stmt = $pdo->prepare("INSERT INTO land_owner_payments 
            (land_owner_name, relation_name, address, mobile1, mobile2, mauze_name, thana_no, khesra_no, rakuwa, rate_per_katha, total_land_value, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt->execute([$land_owner_name, $relation_name, $address, $mobile1, $mobile2, $mauze_name, $thana_no, $khesra_no, $rakuwa, $rate_per_katha, $total_land_value, $status])) {
            $new_id = $pdo->lastInsertId();
            echo "<script>alert('Land Owner Payment inserted successfully!'); window.location.href='?id=$new_id';</script>";
        }
    }

    // Update existing land owner record ONLY
    if (isset($_POST['update']) && !empty($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $land_owner_name = $_POST['land_owner_name'];
        $relation_name = $_POST['relation_name'];
        $address = $_POST['address'];
        $mobile1 = $_POST['mobile1'];
        $mobile2 = $_POST['mobile2'];
        $mauze_name = $_POST['mauze_name'];
        $thana_no = $_POST['thana_no'];
        $khesra_no = $_POST['khesra_no'];
        $rakuwa = $_POST['rakuwa'];
        $rate_per_katha = $_POST['rate_per_katha'];
        $total_land_value = $_POST['total_land_value'];

        $stmt = $pdo->prepare("UPDATE land_owner_payments SET 
            land_owner_name=?, relation_name=?, address=?, mobile1=?, mobile2=?, mauze_name=?, thana_no=?, khesra_no=?, rakuwa=?, rate_per_katha=?, total_land_value=? 
            WHERE id=?");

        if ($stmt->execute([$land_owner_name, $relation_name, $address, $mobile1, $mobile2, $mauze_name, $thana_no, $khesra_no, $rakuwa, $rate_per_katha, $total_land_value, $edit_id])) {
            echo "<script>alert('Record updated successfully!'); window.location.href='?id=$edit_id';</script>";
        }
    }

    // Add payment transaction (Ledger Entry) - SEPARATE ACTION
    if (isset($_POST['add_transaction'])) {
        $land_owner_id = $_POST['land_owner_id']; // Changed from edit_id
        $transaction_date = $_POST['transaction_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'] ?? null;
        $transaction_type = $_POST['transaction_type'];
        $amount = $_POST['amount'];
        $dv_no = $_POST['dv_no'] ?? null;
        $remarks = $_POST['remarks'] ?? null;

        // Validate required fields
        if (empty($transaction_date) || empty($payment_mode) || empty($transaction_type) || empty($amount)) {
            echo "<script>alert('Please fill all required transaction fields!'); window.location.href='?id=$land_owner_id';</script>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO land_payment_transactions 
                (land_owner_id, transaction_date, payment_mode, bank_name, transaction_type, amount, dv_no, remarks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$land_owner_id, $transaction_date, $payment_mode, $bank_name, $transaction_type, $amount, $dv_no, $remarks])) {
                echo "<script>alert('Payment transaction added successfully!'); window.location.href='landreceipt.php?landid=$land_owner_id';</script>";
            }
        }
        exit;
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
                    <div class="card">
                        <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                            <h2>
                                Add Land Owner Payment Details
                            </h2>
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div style="background: #fff; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                        <hr>
                                        <div class="">
                                            <div class="container mt-5" style="margin: unset!important;">
                                                <div class="card shadow rounded-4">
                                                    <div class="card-header bg-primary text-white rounded-top-4">
                                                        <h4 class="mb-0">
                                                            <h4 class="mb-0"><?php echo isset($edit_id) ? 'Edit Land Owner Payment' : 'Add Land Owner Payment'; ?></h4>
                                                        </h4>
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <form method="post" id="landOwnerForm">
                                                            <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? ''; ?>">

                                                            <!-- Basic Land Owner Details -->
                                                            <div class="card mb-4">
                                                                <div class="card-header bg-primary text-white">
                                                                    <h5 class="mb-0">Land Owner Details</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Land Owner Name <span class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control" name="land_owner_name" required value="<?php echo htmlspecialchars($edit_data['land_owner_name'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">S/o, W/o, D/o</label>
                                                                            <input type="text" class="form-control" name="relation_name" value="<?php echo htmlspecialchars($edit_data['relation_name'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-12 mb-3">
                                                                            <label class="form-label fw-semibold">Address</label>
                                                                            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($edit_data['address'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Mobile No. (1)</label>
                                                                            <input type="text" class="form-control" name="mobile1" value="<?php echo htmlspecialchars($edit_data['mobile1'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Mobile No. (2)</label>
                                                                            <input type="text" class="form-control" name="mobile2" value="<?php echo htmlspecialchars($edit_data['mobile2'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Mauza Name</label>
                                                                            <input type="text" class="form-control" name="mauze_name" value="<?php echo htmlspecialchars($edit_data['mauze_name'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Thana No.</label>
                                                                            <input type="text" class="form-control" name="thana_no" value="<?php echo htmlspecialchars($edit_data['thana_no'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Khesra No.</label>
                                                                            <input type="text" class="form-control" name="khesra_no" value="<?php echo htmlspecialchars($edit_data['khesra_no'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Rakwa</label>
                                                                            <input type="text" class="form-control" name="rakuwa" value="<?php echo htmlspecialchars($edit_data['rakuwa'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Rate (Per Katha)</label>
                                                                            <input type="number" step="0.01" class="form-control" name="rate_per_katha" value="<?php echo htmlspecialchars($edit_data['rate_per_katha'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Total Land Value <span class="text-danger">*</span></label>
                                                                            <input type="number" step="0.01" class="form-control" name="total_land_value" required id="totalLandValue" value="<?php echo htmlspecialchars($edit_data['total_land_value'] ?? ''); ?>">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Form Action Buttons for Land Owner Details -->
                                                                    <div class="text-end mt-3">
                                                                        <button type="submit" name="<?php echo isset($edit_id) ? 'update' : 'submit'; ?>" class="btn btn-success px-4 rounded-pill">
                                                                            <i class="fas fa-save"></i> <?php echo isset($edit_id) ? 'Update Land Owner Details' : 'Add Land Owner'; ?>
                                                                        </button>
                                                                        <a href="landowner.php" class="btn btn-warning px-4 rounded-pill text-white" style="text-decoration: none;">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

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
                                                                                <select class="form-control" name="payment_mode" id="paymentMode" required>
                                                                                    <option value="">Select Mode</option>
                                                                                    <option value="cash">Cash</option>
                                                                                    <option value="bank">Bank</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-3 mb-3" id="bankNameField" style="display:none;">
                                                                                <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control" name="bank_name" id="bankNameInput" placeholder="Enter bank name">
                                                                            </div>

                                                                            <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">Transaction Type <span class="text-danger">*</span></label>
                                                                                <select class="form-control" name="transaction_type" id="transactionType" required>
                                                                                    <option value="">Select Type</option>
                                                                                    <option value="credit">Credit (Payment Received)</option>
                                                                                    <option value="debit">Debit (Payment Made)</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-3 mb-3">
                                                                                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                                                                <input type="number" step="0.01" class="form-control" name="amount" placeholder="Enter amount" required>
                                                                            </div>

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
                                                                                    <th>Type</th>
                                                                                    <th>Amount</th>
                                                                                    <th>D.V. No.</th>
                                                                                    <th>Remarks</th>
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
                                                                                            <td>
                                                                                                <span class="badge <?php echo $trans['transaction_type'] == 'credit' ? 'bg-success' : 'bg-danger'; ?>">
                                                                                                    <?php echo strtoupper($trans['transaction_type']); ?>
                                                                                                </span>
                                                                                            </td>
                                                                                            <td class="fw-bold">₹<?php echo number_format($trans['amount'], 2); ?></td>
                                                                                            <td><?php echo $trans['dv_no'] ? htmlspecialchars($trans['dv_no']) : '-'; ?></td>
                                                                                            <td><?php echo $trans['remarks'] ? htmlspecialchars($trans['remarks']) : '-'; ?></td>
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
                                        <hr>
                                        <h4 class="mt-4">Land Owner Payment Report</h4>
                                        <div class="" style="overflow:auto;width:94%">


                                            <div class="">
                                                <table class="table table-striped mt-3">
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
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $records = $pdo->query("SELECT * FROM land_owner_payments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($records as $row): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($row['land_owner_name']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['mobile1']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['mobile2']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['mauze_name']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['thana_no']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['khesra_no']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['rakuwa']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['rate_per_katha']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['total_land_value']); ?></td>
                                                                <td>
                                                                    <a href="?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                                    </form>
                                                                    <a href="landreceipt.php?landid=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Print</a>
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
            if (this.value === 'bank') {
                bankField.style.display = 'block';
                bankField.querySelector('input').required = true;
            } else {
                bankField.style.display = 'none';
                bankField.querySelector('input').required = false;
            }
        });
    </script>

</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>