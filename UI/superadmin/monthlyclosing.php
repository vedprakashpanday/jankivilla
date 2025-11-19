<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}


// Query to fetch distinct date ranges from commission_history
$query = "SELECT DISTINCT from_date, to_date 
          FROM commission_history 
          WHERE status = 'closed' 
          ORDER BY from_date ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$periods = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<html xmlns="http://www.w3.org/1999/xhtml">

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
    <form method="post" action="./Dailyclosing.php" id="form1">


        <div class="wrapper">
            <div class="container-scroller">


                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'adminheadersidepanel.php'; ?>
                    <!-- Content Wrapper. Contains page content -->
                    <div class="content-wrapper">
                        <!-- Content Header (Page header) -->
                        <section class="content-header">
                            <h2 class="m-4">
                                Closing Report

                            </h2>

                        </section>

                        <!-- Main content -->
                        <section class="container" style="padding-left:unset;padding-right:unset;">
                            <div class="box box-primary">
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Serial Number</th>
                                                <th>Period Range</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $serial = 1;
                                            foreach ($periods as $period):
                                                // Format dates for display
                                                $from_display = date('M d Y', strtotime($period['from_date']));
                                                $to_display = date('M d Y', strtotime($period['to_date']));
                                                $period_range = "$from_display - $to_display";
                                            ?>
                                                <tr>
                                                    <td><?php echo $serial++; ?></td>
                                                    <td class="period-range"><?php echo $period_range; ?></td>
                                                    <td class="action-buttons">
                                                        <div class="btn-group">
                                                            <a href="members_report.php?from=<?php echo $period['from_date']; ?>&to=<?php echo $period['to_date']; ?>"
                                                                class="btn btn-m btn-primary view-members">
                                                                View Members
                                                            </a>
                                                            <a href="export_excel.php?from=<?php echo $period['from_date']; ?>&to=<?php echo $period['to_date']; ?>&zero=with_zero"
                                                                class="btn btn-m btn-success export-excel ml-2">
                                                                Export Excel
                                                            </a>
                                                            <button onclick="printReport('<?php echo $period['from_date']; ?>', '<?php echo $period['to_date']; ?>', this.closest('tr').querySelector('.zero-option').value)"
                                                                class="btn btn-m btn-info print-btn ml-2">
                                                                Print
                                                            </button>
                                                        </div>
                                                        <select class="form-control mt-2 zero-option" name="zero_option">
                                                            <option value="with_zero">With Zero</option>
                                                            <option value="without_zero">Without Zero</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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