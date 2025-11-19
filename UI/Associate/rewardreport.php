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

$sponsorid = $_SESSION['sponsor_id'];

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

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .period-range {
            font-weight: bold;
        }

        .view-members {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }

        .view-members:hover {
            background-color: #45a049;
        }
    </style>

    <style>
        .action-buttons .btn {
            border-radius: 4px;
            padding: 6px 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .action-buttons .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .action-buttons .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-group {
            display: flex;
            gap: 5px;
        }
    </style>


</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "associate-headersidepanel.php"; ?>
                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <h2 class="m-4">
                            Reward Report

                        </h2>

                    </section>

                    <!-- Main content -->
                    <section class="container" style="padding-left:unset;padding-right:unset;">
                        <div class="box box-primary">
                            <div class="box-body table-responsive">
                                <div class="row pt-5">
                                    <div class="col-md-12">
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
                                                    <!-- <th>Action</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Make sure session is active
                                                if (session_status() === PHP_SESSION_NONE) {
                                                    session_start();
                                                }

                                                // Logged-in user's sponsor_id
                                                $sponsorid = $_SESSION['sponsor_id'];

                                                // Fetch only rewards for this sponsor
                                                $stmt = $pdo->prepare("SELECT * FROM tbl_rewards WHERE sponsor_id = ? ORDER BY created_at DESC");
                                                $stmt->execute([$sponsorid]);
                                                $rewards = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                $sn = 1;
                                                foreach ($rewards as $reward) {
                                                    echo "<tr>";
                                                    echo "<td>{$sn}</td>";
                                                    echo "<td>{$reward['sponsor_id']}</td>";
                                                    echo "<td>{$reward['s_name']}</td>";
                                                    echo "<td>{$reward['amount']}</td>";
                                                    echo "<td>{$reward['description']}</td>";
                                                    echo "<td>{$reward['created_at']}</td>";
                                                    //                                 echo "<td>
                                                    //     <button class='btn btn-sm btn-primary' onclick='editReward(" . json_encode($reward) . ")'>Edit</button>
                                                    //     <form method='post' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this reward?\")'>
                                                    //         <input type='hidden' name='delete_id' value='{$reward['id']}'>
                                                    //         <button type='submit' class='btn btn-sm btn-danger'>Delete</button>
                                                    //     </form>
                                                    // </td>";
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
                <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.zero-option').forEach(select => {
                    select.addEventListener('change', function() {
                        const row = this.closest('tr');
                        const exportBtn = row.querySelector('.export-excel');
                        const baseExportUrl = exportBtn.getAttribute('href').split('&zero=')[0];
                        exportBtn.setAttribute('href', `${baseExportUrl}&zero=${this.value}`);
                    });
                });
            });

            function printReport(fromDate, toDate, zeroOption) {
                const url = `print_report.php?from=${fromDate}&to=${toDate}&zero=${zeroOption}`;
                const printWindow = window.open(url, '_blank', 'width=800,height=600');

                if (printWindow) {
                    printWindow.focus();
                    // Wait for the window to load before printing
                    printWindow.onload = function() {
                        printWindow.print();
                        // Optionally close after printing (uncomment if desired)
                        // printWindow.close();
                    };
                } else {
                    alert('Please allow popups for this website to enable printing.');
                }
            }
        </script>

        <script>
            document.querySelectorAll('.zero-option').forEach(select => {
                select.addEventListener('change', function() {
                    const baseUrl = this.closest('.action-buttons').querySelector('.view-members').getAttribute('data-base-url');
                    const newUrl = baseUrl + this.value;
                    this.closest('.action-buttons').querySelector('.view-members').href = newUrl;
                });
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