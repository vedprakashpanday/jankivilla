<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

// Handle form submission
$success = '';
$error = '';

// Fetch accounts for dropdown
$sql = "SELECT id, account_name FROM accounts ORDER BY account_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_add_transaction'])) {
    $account_id = $_POST['account_id'];
    $txn_date = $_POST['transaction_date'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $expense_category = $_POST['expense_category'];
    $payment_mode = $_POST['payment_mode'];

    // Validate inputs
    if (empty($account_id) || empty($txn_date) || empty($description) || empty($amount) || empty($type)) {
        $error = "Please fill in all required fields.";
    } elseif ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        // Validate account_id exists
        $sql = "SELECT COUNT(*) FROM accounts WHERE id = :account_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':account_id' => $account_id]);
        if ($stmt->fetchColumn() == 0) {
            $error = "Invalid account selected.";
        } else {
            try {
                // Start transaction
                $pdo->beginTransaction();

                // Insert transaction
                $sql = "INSERT INTO tbl_transactions (account_id, txn_date, description, amount, type, expense_category, payment_mode, created_at) 
                        VALUES (:account_id, :txn_date, :description, :amount, :type, :expense_category, :payment_mode, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $account_id,
                    ':txn_date' => $txn_date,
                    ':description' => $description,
                    ':amount' => $amount,
                    ':type' => $type,
                    ':expense_category' => $expense_category,
                    ':payment_mode' => $payment_mode
                ]);
                $txn_id = $pdo->lastInsertId();

                // Fetch the latest balance or opening balance
                $sql = "SELECT balance FROM tbl_account_balances 
                        WHERE account_id = :account_id 
                        ORDER BY updated_at DESC LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':account_id' => $account_id]);
                $latest_balance = $stmt->fetchColumn();

                if ($latest_balance === false) {
                    // No balance records; use opening balance
                    $sql = "SELECT opening_balance FROM accounts WHERE id = :account_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':account_id' => $account_id]);
                    $latest_balance = $stmt->fetchColumn() ?: 0.00;
                }

                // Calculate new balance
                $new_balance = $type === 'credit' ? $latest_balance + $amount : $latest_balance - $amount;

                // Insert balance update
                $sql = "INSERT INTO tbl_account_balances (account_id, balance, txn_id, txn_date, updated_at) 
                        VALUES (:account_id, :balance, :txn_id, :txn_date, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':account_id' => $account_id,
                    ':balance' => $new_balance,
                    ':txn_id' => $txn_id,
                    ':txn_date' => $txn_date
                ]);

                // Commit transaction
                $pdo->commit();
                $success = "Transaction added successfully!";
            } catch (PDOException $e) {
                // Rollback on error
                $pdo->rollBack();
                $error = "Error saving transaction: " . $e->getMessage();
            }
        }
    }
}
?>


<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers | Accountant Panel
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

    <style>
        .expense-form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .expense-form-container h3 {
            margin-bottom: 25px;
            color: #343a40;
            text-align: center;
            font-weight: 600;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 12px;
        }

        .btn-save {
            width: 100%;
            background-color: #007bff;
            border: none;
            color: white;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-save:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 20px;
        }
    </style>

</head>

<body>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <div class="wrapper ">
        <div class="container-scroller ">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper ">
                <?php include "account-headersidepanel.php"; ?>

                <div class="main-panel">

                    <div class="mx-3 mt-3 rounded shadow-lg" style="background:#fff; border: 2px solid #fff;  margin-bottom:11.3rem;">
                        <div class="col-md-12">

                            <div class="expense-form-container">
                                <h3 class="text-center mb-4">Add Transaction</h3>

                                <!-- Display success or error message -->
                                <?php if ($success): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php endif; ?>
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>

                                <!-- Transaction Form -->
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="account_id">Account</label>
                                        <select class="form-control" name="account_id" required>
                                            <option value="">-- Select Account --</option>
                                            <?php foreach ($accounts as $account): ?>
                                                <option value="<?php echo $account['id']; ?>"><?php echo htmlspecialchars($account['account_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="transaction_date">Transaction Date</label>
                                        <input type="date" class="form-control" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" name="description" placeholder="e.g., Paid Salary, Borrowed from Raj" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Transaction Type</label>
                                        <select class="form-control" name="type" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="debit">Debit (Out)</option>
                                            <option value="credit">Credit (In)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="amount">Amount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" name="amount" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="expense_category">Category</label>
                                        <select class="form-control" name="expense_category">
                                            <option value="">-- Select Category --</option>
                                            <option value="Office">Office</option>
                                            <option value="Medical">Medical</option>
                                            <option value="Utility">Utility</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_mode">Payment Mode</label>
                                        <select class="form-control" name="payment_mode">
                                            <option value="">-- Select Mode --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="UPI">UPI</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Card">Card</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="btn_add_transaction" class="btn btn-primary">Add Transaction</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php include "account-footer.php"; ?>
                </div>
            </div>
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





    <style>
        i {
            color: yellow;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#producttable').DataTable({

            });
        });
    </script>


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>