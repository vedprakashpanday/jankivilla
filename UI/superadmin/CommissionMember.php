<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mem_sid']) && isset($_POST['direct_commission_percent'])) {
        $mem_sid = $_POST['mem_sid'];
        $direct_commission_percent = $_POST['direct_commission_percent'];

        $updateQuery = "UPDATE tbl_regist SET direct_commission_percent = :direct_commission_percent WHERE mem_sid = :mem_sid";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute(['direct_commission_percent' => $direct_commission_percent, 'mem_sid' => $mem_sid]);

        echo "Commission updated successfully!";
    }
}

$query = "SELECT mem_sid, sponsor_id, s_name, m_name, m_num, direct_commission_percent FROM tbl_regist";
$stmt = $pdo->query($query);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">

    <head id="Head1">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
        <title>
            Hari Home Developers
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
    <form method="post" action="./CommissionMember.php" id="form1">

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
                                                    <div class="container mt-4">
                                                        <h2 class="text-center">Set All Member Commission Slab</h2>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered text-center" id='salesTable'>
                                                                <thead class="">
                                                                    <tr>
                                                                        <th>Member ID</th>
                                                                        <th>Sponsor ID</th>
                                                                        <th>Name</th>
                                                                        <th>Mobile</th>
                                                                        <th>Commission (%)</th>
                                                                        <th>Update</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($members as $member): ?>
                                                                        <tr>
                                                                            <form method="POST">
                                                                                <td><?php echo htmlspecialchars($member['mem_sid']); ?></td>
                                                                                <td><?php echo htmlspecialchars($member['sponsor_id']); ?></td>
                                                                                <td><?php echo htmlspecialchars($member['m_name']); ?></td>
                                                                                <td><?php echo htmlspecialchars($member['m_num']); ?></td>
                                                                                <td>
                                                                                    <input type="text" name="direct_commission_percent"
                                                                                        value="<?php echo number_format($member['direct_commission_percent'], 2); ?>"
                                                                                        class="form-control form-control-sm text-center">
                                                                                    <input type="hidden" name="mem_sid"
                                                                                        value="<?php echo htmlspecialchars($member['mem_sid']); ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                                                </td>
                                                                            </form>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div> <!-- table-responsive -->
                                                    </div>
                                                </div> <!-- p-4 shadow-sm -->
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