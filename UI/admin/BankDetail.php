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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
$stmt = $pdo->prepare("
    INSERT INTO tbl_bank_details
    (
        member_id,
        account_name,
        account_no,
        bank_name,
        branch,
        ifsc_code
    )
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    !empty($_POST['edit_id'])? $_POST['edit_id'] : $_SESSION['sponsor_id'],
    $_POST['account_name'],
    $_POST['account_no'],
    $_POST['bank_name'],
    $_POST['branch'],
    $_POST['ifsc_code']
]);


}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
$stmt = $pdo->prepare("
    UPDATE tbl_bank_details SET
        account_name = ?,
        account_no   = ?,
        bank_name    = ?,
        branch       = ?,
        ifsc_code    = ?
    WHERE member_id = ?
");

$stmt->execute([
    $_POST['account_name'],
    $_POST['account_no'],
    $_POST['bank_name'],
    $_POST['branch'],
    $_POST['ifsc_code'],
    !empty($_POST['edit_id'])? $_POST['edit_id'] : $_SESSION['sponsor_id']
]);


}

// Fetch single employee for edit
if (isset($_GET['id']) && !isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM tbl_bank_details WHERE member_id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $edit_id = $_GET['id'];
}

if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM tbl_bank_details WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    echo "<script>alert('Bank Detail Deleted'); window.location.href='BankDetail.php';</script>";
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
    <form method="post" action="./BankDetail.php" id="form1">
        <div class="aspNetHidden">


            <div class="wrapper">
                <div class="container-scroller">
                    <!-- partial -->
                    <div class="container-fluid page-body-wrapper">
                        <?php include 'adminheadersidepanel.php'; ?>

                        <div class="main-panel">
                            <div class="content-wrapper">
                                <div class="col-md-12 stretch-card">
                                    <div class="card">


                                        <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                            <div class="row justify-content-center">

                                                <div class="col-md-12">
                                                    <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                       <h2 class="mb-0">
                                            <?php echo isset($edit_id) ? 'Edit Bank Details' : 'Add New Bank Details'; ?>
                                        </h2>
                                                        <hr>
                                                        <form method="post">
                                                            <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? ''; ?>">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>Account Holder Name:</b>
                                                                <i><input name="account_name" type="text" id="account_name" class="form-control" style="font-weight:bold;" value="<?php echo $edit_data['account_name'] ?? '-'; ?>"></i>
                                                            </div>




                                                            <div class="col-md-4">
                                                                <b> Bank A/c No:</b>

                                                                <i> <input name="account_no" type="text" id="account_no" class="form-control" style="font-weight:bold;" value="<?php echo $edit_data['account_no'] ?? '-'; ?>"></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Bank Name:</b>

                                                                <i> <input name="bank_name" type="text" id="bank_name" class="form-control" style="font-weight:bold;" value="<?php echo $edit_data['bank_name'] ?? '-'; ?>"></i>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>
                                                                    Branch:
                                                                </b>
                                                                <i>
                                                                    <input name="branch" type="text" id="branch" class="form-control" style="font-weight:bold;" value="<?php echo $edit_data['branch'] ?? '-'; ?>">
                                                                </i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>
                                                                    IFSC Code:</b>
                                                                <i><input name="ifsc_code" type="text" id="ifsc_code" class="form-control" style="font-weight:bold;" value="<?php echo $edit_data['ifsc_code'] ?? '-'; ?>"></i>

                                                            </div>

                                                            
                                                        </div>
                                                        <div class="row pt-4">
                                                            <div class="col-md-12">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-4">
                                                                        <input type="submit" name="<?php echo isset($edit_id) ? 'update' : 'submit'; ?>" value="<?php echo isset($edit_id) ? 'Update' : 'Add'; ?> Bank Details" id="" class="btn-success" >
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>


                                           <div class="col-md-12 mt-4">
    <div style="background:#fff; padding:20px; border:2px solid #fff; box-shadow:1px 3px 12px 4px #988f8f;">
        
        <h2>Bank Details</h2>
        <hr>

        <div class="table-responsive">
            <table class="table table-bordered">
                
                <thead class="table-light">
                    <tr>
                        <th>Sl No.</th>
                        <th>Account Holder Name</th>
                        <th>Bank A/c No</th>
                        <th>Bank Name</th>
                        <th>Branch</th>
                        <th>IFSC Code</th>                        
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                     <?php
    $customers = $pdo->query(
        "SELECT * FROM tbl_bank_details ORDER BY id DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
                                                                    $i=1;
    foreach ($customers as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $row['account_name'] ?></td>
                        <td><?= $row['account_no'] ?></td>
                        <td><?= $row['bank_name'] ?></td>
                        <td><?= $row['branch'] ?></td>
                        <td><?= $row['ifsc_code'] ?></td>                        
                        <td>
                            <a href="?id=<?= $row['member_id']; ?>" class="btn btn-sm btn-warning">
                    Edit
                </a>

                <form method="post" class="d-inline"
                      onsubmit="return confirm('Delete this record?');">
                    <input type="hidden" name="delete_id"
                           value="<?= $row['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>


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





    </form>


</body>

</html>