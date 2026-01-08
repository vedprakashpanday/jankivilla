<?php
session_start();
include_once "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header("Location: ../../adminlogin.php"); // Redirect to dashboard
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnsubmit'])) {
    $reward_id = $_POST['reward_id'] ?? '';
    $sponsor_id = $_POST['sponsor_id'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $description = $_POST['description'] ?? '';

    // Get s_name from tbl_hire
    $stmt = $pdo->prepare("SELECT s_name FROM tbl_hire WHERE sponsor_id = ?");
    $stmt->execute([$sponsor_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $s_name = $row ? $row['s_name'] : '';
 
    date_default_timezone_set('Asia/Kolkata');
    $created_at = date('Y-m-d H:i:s');

    if ($reward_id) {
        // Update existing reward
        // $update = $pdo->prepare("UPDATE tbl_rewards SET sponsor_id = ?, s_name = ?, amount = ?, description = ? WHERE id = ?");
        // $success = $update->execute([$sponsor_id, $s_name, $amount, $description, $reward_id]);
        // $message = $success ? "Reward updated successfully!" : "Failed to update reward.";
    } else {
        // Insert new reward
        $insert = $pdo->prepare("INSERT INTO tbl_rewards (sponsor_id, s_name, amount, description, created_at) VALUES (?, ?, ?, ?, ?)");
        $success = $insert->execute([$sponsor_id, $s_name, $amount, $description, $created_at]);
        $message = $success ? "Reward added successfully!" : "Failed to add reward.";
    }

    echo "<script>alert('$message'); window.location.href=window.location.href;</script>";
}


// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
//     $delete_id = $_POST['delete_id'];
//     $delStmt = $pdo->prepare("DELETE FROM tbl_rewards WHERE id = ?");
//     $deleted = $delStmt->execute([$delete_id]);

//     $delMessage = $deleted ? "Reward deleted successfully!" : "Failed to delete reward.";
//     echo "<script>alert('$delMessage'); window.location.href = window.location.href;</script>";
// }

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

    <div class="aspNetHidden">


        <div class="wrapper">
            <div class="container-scroller">
                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                        <div class="row" style="display: block;">
                                            <form method="post">
                                                <div class="col-md-12">
                                                    <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                        <h2>Add Rewards</h2>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>Associate Name:</b>
                                                                <i>
                                                                    <select id="associate_name" name="associate_name" class="form-control select2" style="font-weight:bold;">
                                                                        <option value="">Select Associate</option>
                                                                        <?php
                                                                        // PHP: Fetch sponsor list
                                                                        $stmt = $pdo->query("SELECT sponsor_id, s_name FROM tbl_hire");
                                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                            echo '<option value="' . htmlspecialchars($row['sponsor_id']) . '">' . htmlspecialchars($row['s_name']) . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </i>
                                                            </div>

                                                            <!-- Hidden input to store selected sponsor_id -->
                                                            <input type="hidden" name="sponsor_id" id="sponsor_id">
                                                            <input type="hidden" name="reward_id" id="reward_id">



                                                            <div class="col-md-4">
                                                                <b> Amount:</b>

                                                                <i> <input name="amount" type="number" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Description:</b>
                                                                <i>
                                                                    <textarea name="description" id="description" class="form-control" style="font-weight:bold;" rows="6" cols="7"></textarea>
                                                                </i>
                                                            </div>

                                                        </div>


                                                        <div class="row pt-4">
                                                            <div class="col-md-12">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-7" style="text-align: center;">
                                                                        <input type="submit" name="btnsubmit" value="Save" id="" class="btn-success">
                                                                        <input type="reset" class="btn-secondary" value="Clear Form">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row pt-5">
                                        <div class="col-md-12" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Rewards Report</h3>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Sponsor ID</th>
                                                        <th>Associate Name</th>
                                                        <th>Amount</th>
                                                        <th>Description</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $rewards = $pdo->query("SELECT * FROM tbl_rewards ORDER BY created_at DESC");
                                                    $sn = 1;
                                                    foreach ($rewards as $reward) {
                                                        echo "<tr>";
                                                        echo "<td>{$sn}</td>";
                                                        echo "<td>{$reward['sponsor_id']}</td>";
                                                        echo "<td>{$reward['s_name']}</td>";
                                                        echo "<td>{$reward['amount']}</td>";
                                                        echo "<td>{$reward['description']}</td>";
                                                        echo "<td>{$reward['created_at']}</td>";
                                                        echo "<td>
    <button class='btn btn-sm btn-primary' onclick='editReward(" . json_encode($reward) . ")'>Edit</button>
    <form method='post' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this reward?\")'>
        <input type='hidden' name='delete_id' value='" . $reward['id'] . "'>
        <button type='submit' class='btn btn-sm btn-danger'>Delete</button>
    </form>
</td>";
                                                        echo "</tr>";
                                                        $sn++;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
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
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                <script>
                    function editReward(data) {
                        $('#reward_id').val(data.id);
                        $('#sponsor_id').val(data.sponsor_id).trigger('change'); // Set hidden input
                        $('#associate_name').val(data.sponsor_id).trigger('change'); // Set select2
                        $('input[name="amount"]').val(data.amount);
                        $('#description').val(data.description);
                    }
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





        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Select Associate',
                    allowClear: true
                });

                $('#associate_name').on('change', function() {
                    let selectedSponsorId = $(this).val(); // sponsor_id is the value of <option>
                    $('#sponsor_id').val(selectedSponsorId); // assign it to hidden input
                });
            });
        </script>


</body>

</html>