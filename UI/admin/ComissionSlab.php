<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// error_reporting(1);


// if ($_SESSION['sponsor_id'] === $sponsorid && $_SESSION['sponsor_pass'] === $sponsorpass && $_SESSION['status'] === 'active') {

//     header('location:adminlogin.php');
// }
if (!empty($_SESSION['msg'])) {
    echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
    unset($_SESSION['msg']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['set'])) {
    if (!empty($_POST['designation']) && !empty($_POST['percent'])) {

    $designation = $_POST['designation'];
    $percent     = $_POST['percent'];

    $sql = "INSERT INTO tbl_commision (designation, commission)
            VALUES (:designation, :commission)
            ON DUPLICATE KEY UPDATE
                commission = :commission";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':designation' => $designation,
        ':commission'  => $percent
    ]);

    header("Location: ComissionSlab.php");
    exit;
}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['update'])) {
    if (!empty($_POST['designation']) && !empty($_POST['percent'])) {

        $designation = $_POST['designation'];
        $percent     = $_POST['percent'];

        $updateQuery = "update tbl_commision set commission=:commission where designation=:designation";
        
        $stmt = $pdo->prepare($updateQuery);

        $stmt->execute([
            ':designation' => $designation,
            ':commission'  => $percent
        ]);

        // $_SESSION['msg'] = "Commission added successfully!";
header("Location: ComissionSlab.php"); // same page
exit;
    }
}



?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">

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


        <!-- CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
         

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
        <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    </head>

<body class="hold-transition skin-blue sidebar-mini">
   

        <div class="wrapper">
            <div class="container-scroller">

                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper" style="padding:unset;">
                            <div class="col-md-12 stretch-card">
                                <div class="card">
                                    <div class="container">
                                        <div class="row justify-content-center">
                                            <div class="col-md-12" style="padding:unset;">
                                                <div class="">
                                                    <div class="container mt-4 col-12">
                                                        
                                                        <h2 class="text-center">Set All Positions Commission Slab</h2>
                                                       <form method="post">
                                                        <div class="parent d-flex">
                                                        <div class="designation col-5 my-3">
                                                            <label for="commissionDataList" class="form-label ">Select/Enter Designation</label>
                                                            <input class="form-control" list="datalistOptions" id="commissionDataList" placeholder="Search/Enter Designation" name="designation" value="">
                                                            <datalist id="datalistOptions">
                                                            <option value="Sales Executive (S.E.)">
                                                            <option value="Senior Sales Executive (S.S.E.)">
                                                                <option value="Assistant Marketing Officer (A.M.O.)">
                                                                    <option value="Marketing Officer (M.O.)">
                                                                        <option value="Assistant Marketing Manager (A.M.M.)">
                                                                            <option value="Marketing Manager (M.M.)">
                                                                                <option value="Chief Marketing Manager (C.M.M.)">
                                                                                    <option value="Assistant General Manager (A.G.M.)">
                                                                                        <option value="Deputy General Manager (D.G.M.)">
                                                                                            <option value="General Manager (G.M.)">
                                                                                                <option value="Marketing Director (M.D.)">
                                                                                                    <option value="Founder Member (F.M.)">
                                                                                                        
                                                            </datalist>
                                                            </div>
                                                            <div class="percentage col-5 my-3">
                                                            <label for="commissionpercenteage" class="form-label ">Enter Designation Percentage</label>
                                                             <input class="form-control" id="commissionDataList" placeholder="Enter Percentage" type="text" name="percent" >
                                                             </div>
                                                        </div>
                                                             <div class="button ps-5 px-3" >
                                                                <input type="submit" value="Set" name="set" class="form btn-lg btn-primary my-4 rounded" style="margin-top:32px;" id="set">
                                                            </div>

                                                       </form>
                                                    </div>
                                                </div> <!-- p-4 shadow-sm -->





                                            <h2 class="border-bottom border-3">Commission Table</h2>
                                                <table class="table table-striped table-hover my-5">
                                                    <thead>
                                                        <tr>
                                                            <th>Designation</th>
                                                            <th>Commission Percentage (%)</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $query = "SELECT designation, commission FROM tbl_commision";
                                                        $stmt = $pdo->query($query);
                                                        $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        if (empty($commissions)) {
                                                            echo "<tr><td colspan='2'>No commission slabs found.</td></tr>";
                                                        }
                                                        else{
                                                        foreach ($commissions as $commission) {
                                                            echo "<tr>";
                                                            echo "<td class='designation1'>" . htmlspecialchars($commission['designation']) . "</td>";
                                                            echo "<td class='commission1'>" . htmlspecialchars($commission['commission']) . "</td>";
                                                             echo "<td><button type='button' class='btn btn-primary update'>Update</button></td>";
                                                            echo "</tr>";
                                                        }
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
                <?php include 'adminfooter.php'; ?>

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

                $(document).on('click','.update',function(){
                    var des=$(this).closest('tr').find('.designation1').text();
                   var comm= $(this).closest('tr').find('.commission1').text();
                    // alert(des + " : " + comm);

                   $('.designation').html(`
                   <label for="commissionDataList" class="form-label ">Select/Enter Designation</label>
                        <input class="form-control"
                            list="datalistOptions"
                            id="commissionDataList"
                            placeholder="Search/Enter Designation"
                            name="designation"
                            value="${des}">
                    `);




                   $('.percentage').html(`
                   <label for="commissionPercentage" class="form-label ">Enter Designation Percentage</label>                   
                        <input class="form-control"
                            id="commissionPercentage"
                            name="percent"
                            type="text"
                            value="${comm}">
                    `);

                   $("#set")
    .val("Update")        // button text
    .attr("name", "update")
    .attr("id", "updateBtn")
    .addClass("btn-outline-warning fw-bold")
    .removeClass("btn-primary");
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

   


</body>

</html>