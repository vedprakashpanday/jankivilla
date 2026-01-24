<?php
session_start();
include_once 'connectdb.php';

if (isset($_COOKIE['sponsor_login'])) {
    $login_data = json_decode($_COOKIE['sponsor_login'], true);
    $sponsorid = $login_data['sponsorid'];
    $sponsorpass = $login_data['sponsorpass'];

    $select = $pdo->prepare("select * from tbl_hire where sponsor_id='$sponsorid' AND  sponsor_pass='$sponsorpass'");
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row['sponsor_id'] === $sponsorid and $row['sponsor_pass'] === $sponsorpass) {
        $_SESSION['sponsor_id'] = $row['sponsor_id'];
        $_SESSION['sponsor_pass'] = $row['sponsor_pass'];
        $_SESSION['sponsor_name'] = $row['s_name'];
    }
}

// Redirect the user to the login page if they're not logged in
if (!isset($_SESSION['sponsor_id'])) {
    header('location:../../login.php');
    exit();
}


$sponsor_id = $_SESSION['sponsor_id']; // change this to session variable
$registdata = $pdo->prepare("SELECT * FROM adm_regist WHERE member_id = :sponsor_id");
$registdata->bindParam(':sponsor_id', $sponsor_id);
$registdata->execute();
$fetchmember = $registdata->fetch(PDO::FETCH_ASSOC);

$memberid = $fetchmember['member_id'];
$membername = $fetchmember['full_name'];
$membermobile = $fetchmember['contact_no'];
$memberemail = $fetchmember['email'];
$memberaddress = $fetchmember['communication_address'];
$memberdate = $fetchmember['create_time'];


if (isset($_POST['update'])) {
    $new_email = $_POST['m_email'];
    $new_address = $_POST['address'];
    $new_district = $_POST['district_name'];
    $new_state = $_POST['state_name'];

    // Update query
    $updateQuery = $pdo->prepare("UPDATE adm_regist 
        SET email = :email, 
            communication_address = :address, 
            city = :district, 
            native_place = :state 
        WHERE member_id = :memberid");

    // Bind parameters
    $updateQuery->bindParam(':email', $new_email);
    $updateQuery->bindParam(':address', $new_address);
    $updateQuery->bindParam(':district', $new_district);
    $updateQuery->bindParam(':state', $new_state);
    $updateQuery->bindParam(':memberid', $memberid);

    if ($updateQuery->execute()) {
        echo "<script>alert('Your details have been updated successfully!'); window.location.href=window.location.href;</script>";
    } else {
        echo "<script>alert('Error updating details. Please try again!');</script>";
    }
}


?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
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

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">


    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "employeesidepanelheader.php"; ?>

                <div class="main-panel" style=" padding: 5px">
                    <div style="background: #fff; padding: 10px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">


                        <span id="ContentPlaceHolder1_lblMsg"></span>
                        <div class="clearfix"></div>
                        <div class="content">
                            <div class="page-title" style="padding-top:30px;">
                                <h3>Personal Details</h3>
                                <hr>
                            </div>
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-4" style="padding: 20px">
                                        Name<br>
                                        <input name="m_name" type="text" value="<?php echo htmlspecialchars($membername); ?>" readonly="readonly" class="form-control">
                                    </div>
                                    <div class="col-md-4" style="padding: 20px">
                                        Email Id<br>
                                        <input name="m_email" type="text" value="<?php echo htmlspecialchars($memberemail); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-4" style="padding: 20px">
                                        Mobile<br>
                                        <input name="m_num" type="text" value="<?php echo htmlspecialchars($membermobile); ?>" readonly="readonly" class="form-control">
                                    </div>
                                    <div class="col-md-4" style="padding: 20px">
                                        Address<br>
                                        <textarea name="address" rows="2" cols="20" class="form-control"><?php echo htmlspecialchars($memberaddress); ?></textarea>
                                    </div>
                                    <div class="col-md-4" style="padding: 20px">
                                        City<br>
                                        <input name="district_name" type="text" value="<?php echo htmlspecialchars($fetchmember['city']); ?>" class="form-control">
                                    </div>
                                    <div class="col-md-4" style="padding: 20px">
                                        Native Place<br>
                                        <input name="state_name" type="text" value="<?php echo htmlspecialchars($fetchmember['native_place']); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">&nbsp;</div>
                                    <div class="col-md-4">&nbsp;</div>
                                    <div class="col-md-4">
                                        <input type="submit" name="update" value="Update" class="btn btn-info btn-cons">
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    <?php include "associate-footer.php"; ?>
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
    <style>
        i {
            color: yellow;
        }
    </style>



</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>