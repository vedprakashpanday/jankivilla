<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

$sponsor_id = $_SESSION['sponsor_id']; // change this to session variable

// Output level 1 members

$level1_stmt = $pdo->prepare("SELECT r.m_name, r.mem_sid, p.package, p.date_time FROM tbl_regist r LEFT JOIN tbl_package p ON r.mem_sid = p.member_id  WHERE r.sponsor_id = :sponsor_id");
$level1_stmt->bindParam(':sponsor_id', $sponsor_id);
$level1_stmt->execute();
$level1_members = $level1_stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($level1_members as $member) {
}



// Output level 2 members

$level2_members = array();
foreach ($level1_members as $level1_member) {
    $level2_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level2_stmt->bindParam(':member_id', $level1_member['mem_sid']);
    $level2_stmt->execute();
    $level2_members = array_merge($level2_members, $level2_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level2_members as $member) {
}



// Output level 3 members

$level3_members = array();
foreach ($level2_members as $level2_member) {
    $level3_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level3_stmt->bindParam(':member_id', $level2_member['mem_sid']);
    $level3_stmt->execute();
    $level3_members = array_merge($level3_members, $level3_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level3_members as $member) {
}




// Output level 4 members

$level4_members = array();
foreach ($level3_members as $level3_member) {
    $level4_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level4_stmt->bindParam(':member_id', $level3_member['mem_sid']);
    $level4_stmt->execute();
    $level4_members = array_merge($level4_members, $level4_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level4_members as $member) {
}

// Output level 5 members

$level5_members = array();
foreach ($level4_members as $level4_member) {
    $level5_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level5_stmt->bindParam(':member_id', $level4_member['mem_sid']);
    $level5_stmt->execute();
    $level5_members = array_merge($level5_members, $level5_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level5_members as $member) {
}

// Output level 6 members

$level6_members = array();
foreach ($level5_members as $level5_member) {
    $level6_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level6_stmt->bindParam(':member_id', $level5_member['mem_sid']);
    $level6_stmt->execute();
    $level6_members = array_merge($level6_members, $level6_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level6_members as $member) {
}


// Output level 7 members

$level7_members = array();
foreach ($level6_members as $level6_member) {
    $level7_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level7_stmt->bindParam(':member_id', $level6_member['mem_sid']);
    $level7_stmt->execute();
    $level7_members = array_merge($level7_members, $level7_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level7_members as $member) {
}


// Output level 8 members

$level8_members = array();
foreach ($level7_members as $level7_member) {
    $level8_stmt = $pdo->prepare("SELECT * FROM tbl_regist WHERE sponsor_id = :member_id");
    $level8_stmt->bindParam(':member_id', $level7_member['mem_sid']);
    $level8_stmt->execute();
    $level8_members = array_merge($level8_members, $level8_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level8_members as $member) {
}



// Output level 9 members

$level9_members = array();
foreach ($level8_members as $level8_member) {
    $level9_stmt = $pdo->prepare("SELECT r.s_name, r.m_name, r.sponsor_id, r.mem_sid, p.package, r.date_time, p.status  FROM tbl_regist r LEFT JOIN tbl_package p ON r.mem_sid = p.member_id WHERE r.sponsor_id = :member_id");
    $level9_stmt->bindParam(':member_id', $level8_member['mem_sid']);
    $level9_stmt->execute();
    $level9_members = array_merge($level9_members, $level9_stmt->fetchAll(PDO::FETCH_ASSOC));
}
foreach ($level9_members as $member) {
    echo $member['m_name'] . "<br>";
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
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>



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
                    <div class="content-wrapper" style="background:unset!important;">

                        <div class="sec-box">

                            <header>
                                <h2 class="heading" style="margin:1rem;">Deputy General Manager (D.G.M.)</h2>
                            </header>
                        </div>

                        <div class="col-md-12" style="padding: unset;">

                            <div id="" style="overflow:auto;width:100%">


                                <table id='salesTable' class="table table-striped">

                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Member Id</th>
                                            <th>Member Name</th>
                                            <th>Sponsor ID</th>
                                            <th>Sponsor Name</th>
                                            <th>Date Of Joining</th>
                                            <!-- //here treat sponsor as a member -->
                                        </tr>
                                    </thead>
                                     <tbody>
                                            <?php

                                          $designation = 'Deputy General Manager (D.G.M.)';

$level1_stmt = $pdo->prepare("
    SELECT 
        mem_sid,
        m_name,
        sponsor_id,
        s_name,
        date_time,
        designations
    FROM tbl_regist
    WHERE designation=:designation 
");

$level1_stmt->bindParam(':designation', $designation, PDO::PARAM_STR);
$level1_stmt->execute();

$level1_members1 = $level1_stmt->fetchAll(PDO::FETCH_ASSOC);

// echo '<pre>';
// print_r($level1_members1);
// echo '</pre>';
// exit;
$i=0;
                                            foreach ($level1_members1 as $member) {
                                                $i++;
                                            ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= $member['mem_sid']; ?></td>
                                                    <td><?= $member['m_name']; ?></td>
                                                    <td><?= $member['sponsor_id']; ?></td>
                                                    <td><?= $member['s_name']; ?></td>
                                                    <td><?= $member['date_time']; ?></td>
                                                </tr>

                                        </tbody>
                                    <?php
                                            }
                                    ?>
                                </table>


                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- footer -->

            <?php include 'adminfooter.php'; ?>
        </div>

    </div>
    <a href="#" target="_blank">
        <!-- partial -->
    </a>
    <!-- search box for options-->
    <!-- jQuery (required for DataTables) -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

    <!-- <script src="../resources/vendors/js/vendor.bundle.base.js"></script> -->
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="../resources/vendors/select2/select2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- Plugin js for this page -->
    <script src="../resources/vendors/chart.js/Chart.min.js"></script>
    <!-- <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script> -->
    <!-- <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> -->
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


    <script>
        $(document).ready(function() {
            $('#salesTable').DataTable({

            });

            $('.dropdown-toggle').dropdown();

        });
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





    </form>


</body>

</html>