<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = $_POST['emp_id'] ?: 'EMP' . substr(md5(uniqid()), 0, 8);
    $emp_name = $_POST['emp_name'];
    $mobile = $_POST['mobile'];
    $dob = $_POST['dob'];
    $aadhar_no = $_POST['aadhar_no'] ?? null;
    $pancard_no = $_POST['pancard_no'] ?? null;
    $role = $_POST['role'];
    $email = $_POST['email'];
    $address = $_POST['address'] ?? null;
    $password = $_POST['password'] ?? rand(10000, 999999); // auto-generate 5-6 digit password

    // Insert new
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare("INSERT INTO employees 
    (emp_id, emp_name, mobile, dob, aadhar_no, pancard_no, role, email, address, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$emp_id, $emp_name, $mobile, $dob, $aadhar_no, $pancard_no, $role, $email, $address, $password]);

        // Send email to employee
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'amitabhkmr989@gmail.com';
            $mail->Password = 'ronurtvturnjongr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('amitabhkmr989@gmail.com', 'Amitabh Builders & Developers');
            $mail->addAddress($email, $emp_name);
            $mail->isHTML(true);
            $mail->Subject = "Welcome to Amitabh Builders & Developers - Employee ID $emp_id";

            $mail->Body = "
                <h2>Welcome, $emp_name!</h2>
                <p>You have been successfully registered as an employee.</p>
                <p><strong>Employee ID:</strong> $emp_id</p>
                <p><strong>Role:</strong> $role</p>
                <p><strong>Mobile:</strong> $mobile</p>
                <p><strong>DOB:</strong> $dob</p>
                <p><strong>Password:</strong> $password</p>
                <p>Please keep this email safe for your records.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            echo "<script>alert('Employee added, but email failed to send.');</script>";
        }

        echo "<script>alert('Employee Added'); window.location.href='add_employee.php';</script>";
    }

    // Update existing
    if (isset($_POST['update']) && !empty($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $stmt = $pdo->prepare("UPDATE employees 
    SET emp_id=?, emp_name=?, mobile=?, dob=?, aadhar_no=?, pancard_no=?, role=?, email=?, address=?, password=? 
    WHERE id=?");
        $stmt->execute([$emp_id, $emp_name, $mobile, $dob, $aadhar_no, $pancard_no, $role, $email, $address, $password, $edit_id]);

        echo "<script>alert('Employee Updated'); window.location.href='add_employee.php';</script>";
    }
}

// Fetch single employee for edit
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $edit_id = $_GET['id'];
}

// Delete employee
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    echo "<script>alert('Employee Deleted'); window.location.href='add_employee.php';</script>";
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



                <div class="content-wrapper">
                    <div class="card">
                        <div class="p-1" style="background: #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                            <!-- Add/Edit Employee Form -->
                            <h2>Add Employee Data</h2>
                            <hr>
                            <div class="">
                                <div class="card shadow rounded-4">
                                    <div class="card-header bg-primary text-white rounded-top-4">
                                        <h4 class="mb-0">
                                            <?php echo isset($edit_id) ? 'Edit Employee' : 'Add New Employee'; ?>
                                        </h4>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post">
                                            <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? ''; ?>">

                                            <!-- Employee Name -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Employee Name</label>
                                                <input type="text" class="form-control" name="emp_name" required
                                                    placeholder="Enter Employee Name"
                                                    value="<?php echo $edit_data['emp_name'] ?? ''; ?>">
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Mobile Number</label>
                                                    <input type="text" class="form-control" name="mobile" required
                                                        placeholder="Enter Mobile Number"
                                                        value="<?php echo $edit_data['mobile'] ?? ''; ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Date of Birth</label>
                                                    <input type="date" class="form-control" name="dob" required
                                                        value="<?php echo $edit_data['dob'] ?? ''; ?>">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Aadhar Number</label>
                                                    <input type="text" class="form-control" name="aadhar_no"
                                                        placeholder="Enter Aadhar Number"
                                                        value="<?php echo $edit_data['aadhar_no'] ?? ''; ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">PAN Card Number</label>
                                                    <input type="text" class="form-control" name="pancard_no"
                                                        placeholder="Enter PAN Card Number"
                                                        value="<?php echo $edit_data['pancard_no'] ?? ''; ?>">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Role</label>
                                                    <select name="role" class="form-control" required>
                                                        <option value="">-- Select Role --</option>
                                                        <option value="Admin" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="Accountant" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'Accountant') ? 'selected' : ''; ?>>Accountant</option>
                                                        <option value="HR" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'HR') ? 'selected' : ''; ?>>HR</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                        placeholder="Enter Employee Email"
                                                        value="<?php echo $edit_data['email'] ?? ''; ?>">
                                                </div>
                                            </div>

                                            <div class="mb-3 d-none">
                                                <label class="form-label fw-semibold">Password</label>
                                                <input type="text" class="form-control" name="password" readonly
                                                    value="<?php echo $edit_data['password'] ?? rand(10000, 999999); ?>">
                                                <small class="text-muted">Auto-generated (5–6 digit)</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Address</label>
                                                <textarea class="form-control" name="address" rows="2" placeholder="Enter Address"><?php echo $edit_data['address'] ?? ''; ?></textarea>
                                            </div>

                                            <div class="text-end">
                                                <button type="submit" name="<?php echo isset($edit_id) ? 'update' : 'submit'; ?>"
                                                    class="btn btn-success px-4 rounded-pill">
                                                    <?php echo isset($edit_id) ? 'Update' : 'Add'; ?> Employee
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Table -->
                            <hr>
                            <h4>Employee List</h4>
                            <div class="table-responsive mt-3" style="overflow-x:auto;">
                                <table class="table table-bordered table-striped" style="min-width: 1200px;">
                                    <thead class="table-dark">
                                        <tr>
                                            <!-- <th>Action</th> -->
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>DOB</th>
                                            <th>Aadhar</th>
                                            <th>PAN</th>
                                            <th>Role</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Password</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $employees = $pdo->query("SELECT * FROM employees ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($employees as $row): ?>
                                            <tr>
                                                <!-- <td>
                                                    <a href="?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this employee?');">
                                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                </td> -->
                                                <td><?php echo htmlspecialchars($row['emp_id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['emp_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['mobile']); ?></td>
                                                <td><?php echo htmlspecialchars($row['dob']); ?></td>
                                                <td><?php echo htmlspecialchars($row['aadhar_no']); ?></td>
                                                <td><?php echo htmlspecialchars($row['pancard_no']); ?></td>
                                                <td><?php echo htmlspecialchars($row['role']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                                <td><?php echo htmlspecialchars($row['password']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div> <!-- /.table-responsive -->

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

</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>