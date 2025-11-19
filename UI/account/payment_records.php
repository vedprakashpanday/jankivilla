<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}

// Initialize variables
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');
$success_message = '';
$error_message = '';

// Fetch expenses from tbl_daily_expenses
$sql = "SELECT expense_date, expense_title, expense_description, amount, expense_category, other_category, 
               payment_mode, transaction_id, cheque_no, bank_name, cheque_date, created_at 
        FROM tbl_daily_expenses 
        WHERE expense_date <= :end_date" . ($start_date ? " AND expense_date >= :start_date" : "") . " 
        ORDER BY expense_date DESC, created_at DESC";
$stmt = $pdo->prepare($sql);
$params = [':end_date' => $end_date];
if ($start_date) {
    $params[':start_date'] = $start_date;
}
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            /* max-width: 700px; */
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

                    <div class="mx-3 mt-3 rounded" style="">
                        <div class="col-md-12">

                            <div class="expense-form-container">
                                <h3 class="text-center mb-4">Payment Records</h3>

                                <!-- Display success or error message -->
                                <?php if ($success_message): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                                <?php endif; ?>
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                                <?php endif; ?>

                                <!-- Filter Form -->
                                <form method="POST" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" name="start_date" value="<?php echo htmlspecialchars($start_date ?: ''); ?>">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                                        </div>
                                        <div class="col-md-2 align-self-end">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Expense Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Amount (₹)</th>
                                                <th>Category</th>
                                                <th>Payment Mode</th>
                                                <th>Payment Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($expenses)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No expenses found for the selected date range.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($expenses as $expense): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                                                        <td><?php echo htmlspecialchars($expense['expense_title']); ?></td>
                                                        <td><?php echo htmlspecialchars($expense['expense_description'] ?: 'N/A'); ?></td>
                                                        <td><?php echo number_format($expense['amount'], 2); ?></td>
                                                        <td><?php echo htmlspecialchars($expense['expense_category'] === 'Other' ? ($expense['other_category'] ?: 'Other') : ($expense['expense_category'] ?: 'N/A')); ?></td>
                                                        <td><?php echo htmlspecialchars($expense['payment_mode'] ?: 'N/A'); ?></td>
                                                        <td>
                                                            <?php
                                                            if ($expense['payment_mode'] === 'UPI' && $expense['transaction_id']) {
                                                                echo 'UPI ID: ' . htmlspecialchars($expense['transaction_id']);
                                                            } elseif ($expense['payment_mode'] === 'Cheque' && $expense['cheque_no']) {
                                                                echo 'Cheque: ' . htmlspecialchars($expense['cheque_no']) . ', ' . htmlspecialchars($expense['bank_name']) . ', ' . ($expense['cheque_date'] ?: 'N/A');
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php include "account-footer.php"; ?>
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