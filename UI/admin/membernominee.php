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
            echo "<script>alert('deleted successfully!'); window.location.href='membernominee.php';</script>";
        } else {
            echo "<script>alert('Failed to delete.');</script>";
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
                                            <h3>Family / Nominee Details</h3>
                                            <div id="" style="width:94%;overflow:auto;padding:0px; height:70vh;">

                                                <div>
                                                    <table class="table-style table-bordered" cellspacing="0" cellpadding="3" rules="all" id="" style="background-color:White;border-color:#E7E7FF;border-width:1px;border-style:None;font-weight:bold;width:100%;border-collapse:collapse;">
                                                        <tbody>
                                                            <tr style="color:#F7F7F7;background-color:#383F3F;font-weight:bold;">
                                                                <th scope="col">S.No.</th>
                                                                <th scope="col" style="width:100px;">Delete</th>
                                                                <th scope="col">Edit</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Id</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Password</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Mobile</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Email</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Aadhar</th>
                                                                <th align="left" scope="col" style="width:100px;">Member Pan No</th>
                                                                <th align="left" scope="col" style="width:100px;">Address</th>
                                                                <th align="left" scope="col" style="width:100px;">State</th>
                                                                <th align="left" scope="col" style="width:100px;">District</th>

                                                                <!-- NEW NOMINEE FIELDS START HERE -->
                                                                <th align="left" scope="col" style="width:100px;">Nominee Fullname</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Father Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Husband Name</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee DOB</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Relationship</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Aadhar No.</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Pan No.</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Native Place</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Communication</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee City/Town/Village</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Pincode</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Contact</th>
                                                                <th align="left" scope="col" style="width:100px;">Nominee Email</th>
                                                                <!-- NEW NOMINEE FIELDS END HERE -->

                                                                <th scope="col" style="">Registration Date</th>
                                                            </tr>
                                                            <?php
                                                            try {
                                                                // Query is now from tbl_regist and selects all member and new nominee fields
                                                                $stmt = $pdo->prepare("
                SELECT 
                    id, mem_sid, m_password, m_name, m_num, m_email, aadhar_number, pan_number, 
                    address, state_name, district_name, date_time,
                    
                    -- All the new nominee fields
                    nominee_fullname, nominee_father_name, nominee_husband_name, 
                    nominee_date_of_birth, nominee_relationship, nominee_aadhar_no, 
                    nominee_pan_no, nominee_native_place, nominee_communication, 
                    nominee_city_town_village, nominee_pincode, nominee_contact, nominee_email
                    
                FROM tbl_regist 
                ORDER BY id DESC
            ");
                                                                $stmt->execute();
                                                                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                $i = 1;

                                                                foreach ($members as $member) {
                                                                    echo '<tr style="color:#4A3C8C;background-color:#E7E7FF;">';
                                                                    echo '<td><span>' . $i++ . '</span></td>';

                                                                    // Delete form now uses the member's primary key 'id'
                                                                    echo '<td><form method="POST" action="" onsubmit="return confirm(\'Are you sure you want to delete this member?\');">
                    <input type="hidden" name="member_id" value="' . htmlspecialchars($member['id']) . '">
                    <input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm">
                </form></td>';

                                                                    // Edit link now passes the primary key 'id'
                                                                    echo '<td><a class="btn btn-warning btn-sm" href="edit_membernominee.php?id=' . urlencode($member['id']) . '">Edit</a></td>';

                                                                    // Displaying member data from tbl_regist
                                                                    echo '<td><span>' . htmlspecialchars($member['mem_sid']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['m_password']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['m_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['m_num']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['m_email']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['aadhar_number']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['pan_number']) . '</span></td>'; // Member's PAN
                                                                    echo '<td style="width:250px;"><span>' . htmlspecialchars($member['address']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['state_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['district_name']) . '</span></td>';

                                                                    // Displaying all new nominee fields
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_fullname']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_father_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_husband_name']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_date_of_birth']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_relationship']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_aadhar_no']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_pan_no']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_native_place']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_communication']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_city_town_village']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_pincode']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_contact']) . '</span></td>';
                                                                    echo '<td><span>' . htmlspecialchars($member['nominee_email']) . '</span></td>';

                                                                    $date = date('d-M-Y h:i A', strtotime($member['date_time']));
                                                                    echo '<td><span>' . $date . '</span></td>';

                                                                    echo '</tr>';
                                                                }
                                                            } catch (PDOException $e) {
                                                                // Updated colspan to match the new number of columns (13 base + 13 nominee + 3 action = 29)
                                                                echo '<tr><td colspan="29">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
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