<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id'])) {
    header('location:../../login.php');
    exit();
}

//$sponsor_id = $_SESSION['sponsor_id'];
require '../../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Common fields
    $cust_name           = $_POST['cus_name'] ?? '';
    $mobile              = $_POST['mobile'] ?? '';
    $alternate_no        = $_POST['alternate_no'] ?? '';
    $address             = $_POST['address'] ?? '';
    $date                = $_POST['date'] ?? null;
    $interested_for      = $_POST['interested_for'] ?? null;
    $status              = $_POST['status'] ?? null;
    $followup_date       = $_POST['followup_date'] ?? null;
    $remark              = $_POST['remark'] ?? '';
    $assigned_telecaller = $_POST['telecaller'] ?? '';
    $email               = $_POST['email'] ?? '';   // ⭐ customer email

    /* =======================
       INSERT NEW RECORD
    ======================== */
    if (isset($_POST['submit'])) {

        $stmt = $pdo->prepare("
            INSERT INTO interested_customer
            (
                assigned_telecaller,
                cust_name,
                mobile,
                alternate_no,
                address,
                date,
                interested_for,
                status,
                followup_date,
                remark
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $assigned_telecaller,
            $cust_name,
            $mobile,
            $alternate_no,
            $address,
            $date,
            $interested_for,
            $status,
            $followup_date,
            $remark
        ]);

        /* =======================
           SEND EMAIL TO CUSTOMER
        ======================== */
        if (!empty($email)) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'amitabhkmr989@gmail.com';
                $mail->Password   = 'ronurtvturnjongr';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('amitabhkmr989@gmail.com', 'Amitabh Builders & Developers');
                $mail->addAddress($email, $cust_name);

                $mail->isHTML(true);
                $mail->Subject = 'Thank you for contacting Amitabh Builders';

                $mail->Body = "
                    <h3>Dear $cust_name,</h3>
                    <p>Thank you for your interest in <b>Amitabh Builders & Developers</b>.</p>

                    <p><b>Interested For:</b> $interested_for</p>
                    <p><b>Assigned Telecaller:</b> $assigned_telecaller</p>
                    <p><b>Status:</b> $status</p>

                    <p>Our team will contact you shortly.</p>

                    <br>
                    <p>Regards,<br>
                    Amitabh Builders & Developers<br>
                    📞 9472467007</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                // Email failure ignored but record saved
            }
        }

        echo "<script>
                alert('Customer Added Successfully');
                window.location.href='interested_customer.php';
              </script>";
        exit;
    }

    /* =======================
       UPDATE EXISTING RECORD
    ======================== */
    if (isset($_POST['update']) && !empty($_POST['edit_id'])) {

        $id = $_POST['edit_id'];

        $stmt = $pdo->prepare("
            UPDATE interested_customer SET
                assigned_telecaller = ?,
                cust_name           = ?,
                mobile              = ?,
                alternate_no        = ?,
                address             = ?,
                date                = ?,
                interested_for      = ?,
                status              = ?,
                followup_date       = ?,
                remark              = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $assigned_telecaller,
            $cust_name,
            $mobile,
            $alternate_no,
            $address,
            $date,
            $interested_for,
            $status,
            $followup_date,
            $remark,
            $id
        ]);

        echo "<script>
                alert('Customer Updated Successfully');
                window.location.href='interested_customer.php';
              </script>";
        exit;
    }
}

// Fetch single employee for edit
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM interested_customer WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $edit_id = $_GET['id'];
}

// Delete employee
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM interested_customer WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    echo "<script>alert('Customer Deleted'); window.location.href='interested_customer.php';</script>";
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">


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
        <div class="container-scroller ">
            <!-- partial -->
            <div class="container-fluid page-body-wrapper rounded">
                <div class="franchise_nav_menu">
                    <?php include "employeesidepanelheader.php"; ?>
                </div>



                <div class="content-wrapper ">
                    <div class="card ">
                        <div class="p-3 mb-5 container rounded" style="background: #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                            <!-- Add/Edit Employee Form -->
                            <h2>Add Interested Customer Data</h2>
                            <hr>
                            <div class="">
                                <div class="card shadow rounded-4">
                                    <div class="card-header bg-primary text-white rounded-top-4">
                                        <h4 class="mb-0">
                                            <?php echo isset($edit_id) ? 'Edit Interested Customer' : 'Add New Interested Customer'; ?>
                                        </h4>
                                    </div>
                                    <div class="card-body p-4">
<form method="post">

    <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? ''; ?>">

    <!-- Customer Name -->
     <div class="row">
    <div class="mb-3 col-md-6">
        <label class="form-label fw-semibold">Customer Name</label>
        <input type="text" class="form-control" name="cus_name" required
               placeholder="Enter Customer Name"
               value="<?php echo $edit_data['cust_name'] ?? ''; ?>">
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label fw-semibold">Assigned Tele-Caller</label>
        <input type="text" class="form-control" name="cus_name" required
               placeholder="Enter Customer Name"
               value="<?php echo $_SESSION['sponsor_name'] ?>" readonly>

    </div>
</div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">Mobile Number</label>
            <input type="text" class="form-control" name="mobile" required
                   placeholder="Enter Mobile Number"
                   value="<?php echo $edit_data['mobile'] ?? ''; ?>">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">Alternate Number</label>
            <input type="text" class="form-control" name="alternate_no"
                   placeholder="Alternate Number"
                   value="<?php echo $edit_data['alternate_no'] ?? ''; ?>">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">Date of Calling</label>
            <input type="date" class="form-control" name="date" required
                   value="<?php echo $edit_data['date'] ?? ''; ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Address</label>
        <input type="text" class="form-control" name="address"
               placeholder="Customer Address"
               value="<?php echo $edit_data['address'] ?? ''; ?>">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Interested For</label>
            <select name="interested_for" class="form-control" required>
                <option value="">-- Select --</option>

                <option value="phase1villa"
                    <?php if(($edit_data['interested_for'] ?? '')=='phase1villa') echo 'selected'; ?>>
                    Phase 1 Villa
                </option>

                <option value="phase1villa&plot"
                    <?php if(($edit_data['interested_for'] ?? '')=='phase1villa&plot') echo 'selected'; ?>>
                    Phase 1 Villa & Plot
                </option>

                <option value="phase2villa"
                    <?php if(($edit_data['interested_for'] ?? '')=='phase2villa') echo 'selected'; ?>>
                    Phase 2 Villa
                </option>

                <option value="phase2villa&plot"
                    <?php if(($edit_data['interested_for'] ?? '')=='phase2villa&plot') echo 'selected'; ?>>
                    Phase 2 Villa & Plot
                </option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-control" required>
                <option value="">-- Select --</option>
                <option value="Connected" <?php if(($edit_data['status'] ?? '')=='Connected') echo 'selected'; ?>>Connected</option>
                <option value="Not Reachable" <?php if(($edit_data['status'] ?? '')=='Not Reachable') echo 'selected'; ?>>Not Reachable</option>
                <option value="Follow Up" <?php if(($edit_data['status'] ?? '')=='Follow Up') echo 'selected'; ?>>Follow Up</option>
                <option value="Site Visit Schedule" <?php if(($edit_data['status'] ?? '')=='Site Visit Schedule') echo 'selected'; ?>>Site Visit Schedule</option>
                <option value="Site Visit Done" <?php if(($edit_data['status'] ?? '')=='Site Visit Done') echo 'selected'; ?>>Site Visit Done</option>
                <option value="Booking Done" <?php if(($edit_data['status'] ?? '')=='Booking Done') echo 'selected'; ?>>Booking Done</option>
                <option value="Lost" <?php if(($edit_data['status'] ?? '')=='Lost') echo 'selected'; ?>>Lost</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Follow-up Date</label>
            <input type="date" class="form-control" name="followup_date"
                   value="<?php echo $edit_data['followup_date'] ?? ''; ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Remarks</label>
            <input type="text" class="form-control" name="remark"
                   placeholder="Enter Remark Here"
                   value="<?php echo $edit_data['remark'] ?? ''; ?>">
        </div>
    </div>

    <div class="text-end">
        <button type="submit"
                name="<?php echo isset($edit_id) ? 'update' : 'submit'; ?>"
                class="btn btn-success px-4 rounded-pill">
            <?php echo isset($edit_id) ? 'Update' : 'Add'; ?> Calling Report
        </button>
    </div>

</form>
</div>

                                </div>
                            </div>

                            <!-- Employee Table -->
                             <hr >
                            <h4 class="my-3 py-3">Download Interested Customer List</h4>
                            <hr>
                            <div class="container my-4">
    <div class="card shadow-sm py-4">
        <div class="card-body ">
            <div class="row align-items-end">

                <!-- From Date -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" id="fromDate" class="form-control">
                </div>

                <!-- To Date -->
                <div class="col-md-2">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" id="toDate" class="form-control">
                </div>

                <!-- Excel Button -->
                <div class="col-md-2">
                    <button class="btn btn-success w-100 mt-3" onclick="downloadExcel()">
                        📥 Download Excel
                    </button>
                </div>

                <!-- Print Button -->
                <div class="col-md-2">
                    <button class="btn btn-primary w-100 mt-3" onclick="printTable()">
                        🖨 Print
                    </button>
                </div>

                  <div class="col-md-2">
                    <button class="btn btn-success w-100 mt-3" onclick="downloadExcel()">
                        📥 Today Excel
                    </button>
                </div>

                 <div class="col-md-2">
                    <button class="btn btn-primary w-100 mt-3" onclick="printTable()">
                        🖨 Today Print
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

                           <hr>
                            <h4>Employee List</h4>
                   <div class="table-responsive mt-3" style="overflow-x:auto;">
<table class="table table-bordered table-striped" style="min-width: 1600px;" id="employeeTable">
    <thead class="table-dark">
        <tr >
            <th class="text-dark">Action</th>
            <th class="text-dark">SL No</th>
            <th class="text-dark">Customer Name</th>
            <th class="text-dark">Assigned Telecaller</th>
            <th class="text-dark">Mobile</th>
            <th class="text-dark">Alternate No</th>
            <th class="text-dark">Address</th>
            <th class="text-dark">Date of Calling</th>
            <th class="text-dark">Interested For</th>
            <th class="text-dark">Status</th>
            <th class="text-dark">Follow-up Date</th>
            <th class="text-dark">Remarks</th>

        </tr>
    </thead>

    <tbody>
    <?php
   $stmt = $pdo->prepare("
    SELECT * 
    FROM interested_customer 
    WHERE assigned_telecaller = :telecaller_id
    ORDER BY id DESC
");

$stmt->execute([
    ':telecaller_id' => $_SESSION['sponsor_id']
]);

$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                    $i=1;
    foreach ($customers as $row): ?>
        <tr>
              <td>
                <a href="?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">
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
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['cust_name'])??"-" ?></td>
             <td><?= htmlspecialchars($row['assigned_telecaller']??'-') ?></td>
            <td><?= htmlspecialchars($row['mobile'])??'-' ?></td>
            <td><?= htmlspecialchars($row['alternate_no'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['address'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['interested_for'])??'-' ?></td>
            <td><?= htmlspecialchars($row['status'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['followup_date'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['remark'] ?? '-') ?></td>
          
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div> <!-- /.table-responsive -->

<div id="printArea" style="display:none"  class="text-center">

    <!-- HEADER (IMAGE जैसा) -->
    <div class="header-text ">
        <div class="company-title">
            AMITABH BUILDERS AND DEVELOPERS PVT LTD
        </div>

        <div class="cin-text">
            CIN NO. : U24299BR2024PTC072712
        </div>

        <div class="small-text">
            <b>Head Office :</b>
            1st Floor, Pappu Yadav Building, South of NH-27, Kakarghati Chowk,
            Bhuskaul, Darbhanga-846007
        </div>

        <div class="small-text">
            <b>Branch Office :</b>
            Near Jha Indian Petrol Pump, Mohana Chowk,
            Jhanjharpur, Madhubani, Pin-847404 (Bihar)
        </div>

        <!-- ❌ Icons hata diye (PDF issue) -->
        <div class="small-text">
            Phone : <b>9060218 / 222 / 333 / 666</b> |
            WhatsApp : <b>9472467007</b> |
            Website : <b>www.jankivilla.com</b>
        </div>
    </div>

    <table border="1" width="100%" cellspacing="0" cellpadding="6" id="excelTable">
        <thead>
            <tr>
                <th>SL No</th>
                <th>Customer Name</th>
                <th>Assigned Telecaller</th>
                <th>Mobile</th>
                <th>Alternate No</th>
                <th>Address</th>
                <th>Date of Calling</th>
                <th>Interested For</th>
                <th>Status</th>
                <th>Follow-up Date</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody id="tableBody">
           
        </tbody>
    </table>
</div>

                        </div>
                         <?php include "employee-footer.php"; ?> 
                    </div>
                   
                </div>


                
            </div>



                
        </div>
      

        <a href="#" target="_blank">
            <!-- partial -->
        </a>
          <!-- search box for options-->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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


                <!-- excel file -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function loadFollowupData(callback = null) {

var today = new Date().toISOString().split('T')[0];


    let fromDate = document.getElementById('fromDate').value;
    let toDate   = document.getElementById('toDate').value;
    //let s_id=<?php echo $_SESSION['sponsor_id']; ?>;

    if (!fromDate && !toDate) {
        fromDate = today;
        toDate = today;
    }

    else if (!fromDate || !toDate) {    

    
        alert('Please select From Date and To Date');
        return;
   
    }
    let formData = new FormData();
    formData.append('from_date', fromDate);
    formData.append('to_date', toDate);
    // formData.append('s_id', s_id);

    fetch('fetch_followup_customers.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        let tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
console.log(data);

        if (data.length === 0) {
            alert('No record found');
            return;
        }

        let i = 1;
        data.forEach(row => {
            tbody.innerHTML += `
                <tr>
                    <td>${i++}</td>
                    <td>${row.cust_name ?? '-'}</td>
                    <td>${row.assigned_telecaller ?? '-'}</td>
                    <td>${row.mobile ?? '-'}</td>
                    <td>${row.alternate_no ?? '-'}</td>
                    <td>${row.address ?? '-'}</td>
                    <td>${row.date ?? '-'}</td>
                    <td>${row.interested_for ?? '-'}</td>
                    <td>${row.status ?? '-'}</td>
                    <td>${row.followup_date ?? '-'}</td>
                    <td>${row.remark ?? '-'}</td>
                </tr>
            `;
        });

        // 👉 callback (Excel / Print)
        if (callback) callback();
    })
    .catch(err => {
        console.error(err);
        alert('Server error');
    });
}
</script>

<script>
function downloadExcel() {
    loadFollowupData(() => {

        let table = document.getElementById("excelTable");
        let workbook = XLSX.utils.table_to_book(table, {
            sheet: "Follow-Up Report"
        });

        XLSX.writeFile(workbook, "Client_Followup_Report.xlsx");
    });
}
</script>

<script>
function printTable() {
    loadFollowupData(() => {

        let content = document.getElementById('printArea').innerHTML;
        let win = window.open('', '', 'width=1200,height=800');

        win.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                @page {
                    size: A4 landscape;
                    margin: 8mm;
                }

                body {
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                }

                /* OUTER BORDER */
                .print-wrapper {
                    border: 1px solid #000;
                    padding: 8px;
                }

                h4, h5, p {
                    margin: 3px 0;
                    text-align: center;
                }

                table {
                    width: 100%;                   
                    border-collapse: collapse;
                    table-layout: fixed; /* 🔑 important */
                }

                thead {
                    // border: 2px solid #000;
                    background: #f2f2f2;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 4px;
                    vertical-align: top;
                    word-wrap: break-word;
                    word-break: break-word;
                }
                    
.header-text{
    width: 100%;
    text-align: center;
    margin-bottom: 12px;
}

.company-title{
    font-size: 22px;
    font-weight: 700;
    margin: 0;
}

.cin-text{
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
}

.small-text{
    font-size: 12px;
    line-height: 1.4;
}


                th {
                    font-size: 10px;
                    text-align: center;
                    font-weight: bold;
                }

                td {
                    font-size: 9.5px;
                }

                /* 🔹 Prevent row breaking across pages */
                tr {
                    page-break-inside: avoid;
                }

                /* Optional: shrink columns */
                th:nth-child(1), td:nth-child(1) { width: 4%; }
                th:nth-child(4), td:nth-child(4) { width: 8%; }
                th:nth-child(5), td:nth-child(5) { width: 8%; }
                th:nth-child(10), td:nth-child(10) { width: 8%; }

            </style>
        </head>
        <body>
            <div class="print-wrapper">
                ${content}
            </div>
        </body>
        </html>
        `);

        win.document.close();
        win.focus();
        win.print();
    });
}
</script>

        <script>
           // $('#staffTable').DataTable();
            $('#employeeTable').DataTable();
        </script>



    
    </div>
    <div style="margin-left:250px">
        <span id="lblMsg"></span>
    </div>
    <style>
        #lblMsg {
            visibility: hidden;
        }
    </style>

</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>