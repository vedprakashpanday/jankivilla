<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $customer_id = $_POST['customer_id'];

    if (!empty($customer_id)) {
        $stmt = $pdo->prepare("DELETE FROM customer_details WHERE customer_id = ?");
        if ($stmt->execute([$customer_id])) {
            echo "<script>alert('Customer deleted successfully!'); window.location.href='RptCustomerDetails.php';</script>";
        } else {
            echo "<script>alert('Failed to delete customer.');</script>";
        }
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

    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">

                <!-- side panel header -->
                <?php include 'adminheadersidepanel.php'; ?>
                <div class="main-panel">

                    <div class="col-md-12 stretch-card">
                        <div class="card">


                            <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                                <div class="row justify-content-center">

                                    <div class="col-md-12">
                                        <div style="background: #fff; padding: 0px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Customer Details</h3>
                                            <div id="" style="width:95%;overflow:auto;padding:20px; height:70vh;">

                                                <div>
                                                    <table class="table-style table-bordered" cellspacing="0" cellpadding="3" rules="all" id="" style="background-color:White;border-color:#E7E7FF;border-width:1px;border-style:None;font-weight:bold;width:100%;border-collapse:collapse;">
                                                        <tbody>
                                                            <tr style="color:#F7F7F7;background-color:#383F3F;font-weight:bold;">
                                                                <th scope="col">ID</th>
                                                                <th scope="col" style="width:100px;">Delete</th>
                                                                <th scope="col">Edit</th>
                                                                <th align="left" scope="col" style="width:100px;">Customer Id</th>
                                                                <th align="left" scope="col" style="width:100px;">Customer Password</th>
                                                                <th align="left" scope="col" style="width:100px;">Customer Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Customer Mobile</th>
                                                                <th align="left" scope="col" style="width:100px;">Customer Email</th>
                                                                <th align="left" scope="col" style="width:100px;">Customer Aadhar</th>
                                                                <th align="left" scope="col" style="width:100px;">Bank Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Branch Name</th>
                                                                <th align="left" scope="col" style="width:100px;">IFSC Code</th>
                                                                <th align="left" scope="col" style="width:100px;">Bank Account Number</th>
                                                                <th align="left" scope="col" style="width:100px;">Bank Account Holder Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Pan No</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Aadhar</th>
                                                                <th align="left" scope="col" style="width:100px;">Address</th>
                                                                <th align="left" scope="col" style="width:100px;">State</th>
                                                                <th align="left" scope="col" style="width:100px;">District</th>
                                                                <th scope="col" style="">Booking Date</th>
                                                            </tr>
                                                            <?php
                                                            try {
                                                                $stmt = $pdo->prepare("
    SELECT 
        cd.id,
        cd.customer_id,
        cd.password,
        cd.customer_name,
        cd.customer_mobile,
        cd.customer_email,
        cd.aadhar_number,
        cd.pan_number,
        cd.created_at,
        cd.nominee_name,
        cd.nominee_aadhar,
        cd.address,
        cd.state,
        cd.district,
        cd.booking_date,
        bd.bank_name,
        bd.branch,
        bd.ifsc_code,
        bd.account_no,
        bd.account_name
    FROM customer_details cd
    LEFT JOIN tbl_bank_details bd
        ON cd.customer_id COLLATE utf8mb4_general_ci
           = bd.member_id COLLATE utf8mb4_general_ci
    ORDER BY cd.id DESC
");

                                                                $stmt->execute();
                                                                $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                $i = 1;

                                                                foreach ($customers as $customer) {
                                                                    echo '<tr style="color:#4A3C8C;background-color:#E7E7FF;">';
                                                                    echo '<td><span>' . $i++ . '</span></td>';
                                                                    echo '<td><form method="POST" action="" onsubmit="return confirm(\'Are you sure you want to delete this customer?\');">
                        <input type="hidden" name="customer_id" value="' . htmlspecialchars($customer['customer_id']) . '">
                        <input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm">
                      </form></td>';
                                                                    echo '<td><a class="btn btn-warning btn-sm" href="edit_customer.php?customer_id=' . urlencode($customer['customer_id']) . '">Edit</a></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['customer_id']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['password']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['customer_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['customer_mobile']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['customer_email']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['aadhar_number']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['bank_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['branch']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['ifsc_code']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['account_no']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['account_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['pan_number']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['nominee_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['nominee_aadhar']) . '</span></td>';
                                                                    echo '<td style="width:250px;"><span>' . htmlspecialchars($customer['address']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['state']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($customer['district']) . '</span></td>';
                                                                    $date = date('d-M-Y h:i A', strtotime($customer['created_at']));
                                                                    echo '<td><span>' . htmlspecialchars($customer['booking_date']) . '</span></td>';

                                                                    echo '</tr>';
                                                                }
                                                            } catch (PDOException $e) {
                                                                echo '<tr><td colspan="15">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                                            }
                                                            ?>
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


                    <!-- footer -->

                    <?php include 'adminfooter.php'; ?>
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