<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


// Check if a notice exists (assuming we work with the latest one)
$sql = "SELECT * FROM tbl_notices ORDER BY created_at DESC LIMIT 1";
$stmt = $pdo->query($sql);
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];

    try {
        if ($notice) {
            // Update existing notice
            $sql = "UPDATE tbl_notices SET title = :title, description = :description WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':id', $notice['id'], PDO::PARAM_INT);
            $stmt->execute();
            echo "<div class='alert alert-success'>Notice updated successfully!</div>";
        } else {
            // Insert new notice
            $sql = "INSERT INTO tbl_notices (title, description, created_at) VALUES (:title, :description, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
            echo "<div class='alert alert-success'>Notice added successfully!</div>";
        }

        // Refresh the notice data after insert/update
        $stmt = $pdo->query("SELECT * FROM tbl_notices ORDER BY created_at DESC LIMIT 1");
        $notice = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
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


        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        table td {
            font-size: 14px;
            color: #333;
        }

        table .btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        table .btn-info {
            background-color: #17a2b8;
            color: white;
            border: none;
        }

        table .btn-info:hover {
            background-color: #138496;
        }

        table .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        table .btn-danger:hover {
            background-color: #c82333;
        }
    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini" data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <form method="post" action="./updatenotice.php" id="form1">
        <div class="aspNetHidden">
            <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwUKLTIyNTYwMTk3MA8WAh4Ib01vZGVsSWRmFgJmD2QWAgIDD2QWAgIFD2QWBgIFDw8WBB4IQ3NzQ2xhc3MFC2J0bi1zdWNjZXNzHgRfIVNCAgJkZAIHDw8WBB8BBQtidG4tc3VjY2Vzcx8CAgJkZAIJD2QWAgIBDzwrABEDAA8WBB4LXyFEYXRhQm91bmRnHgtfIUl0ZW1Db3VudAIBZAEQFgAWABYADBQrAAAWAmYPZBYGAgEPZBYGAgIPZBYCAgEPDxYCHgRUZXh0BQExZGQCAw9kFgICAQ8PFgIfBQUKaW5mb2VyYSBzc2RkAgQPZBYCAgEPDxYCHwUFG0FkZHJlc3MgIHBhdG5hICBiaWhhciBpbmRpYWRkAgIPDxYCHgdWaXNpYmxlaGRkAgMPDxYCHwZoZGQYAQUgY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRndkxpc3QPPCsADAEIAgFk5D5SbsWgeBnsRS9mtUfAxZvXkw6dMOYmADUUkxeQ5CI=">
        </div>

        <div class="aspNetHidden">

            <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="9700BCAB">
            <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/wEdAAcK1JXKNY/ae3OZud2cITASoah0Stozpo7WX+ud2KSUZQd8R3SiTDU3dedTrj08To+w1B9iIwnM9JzM/iCz7TomQXOD6seCtJ99PhZxpHJY6cTaGFy/5wcLULh0HNiIP1JykLrPxjHtix375XHsDoQfLaeMghq/KXsNeyWgPDVtvblFol3cLurv28rcvoPETAU=">
        </div>

        <div class="wrapper">
            <div class="container-scroller">


                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <div class="franchise_nav_menu">
                        <?php include "adminheadersidepanel.php"; ?>
                    </div>


                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="">
                                <div class="card">


                                    <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                                        <div class="row justify-content-center">

                                            <div class="col-md-12">
                                                <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                                    <h2>
                                                        Update Notice
                                                    </h2>
                                                    <hr>

                                                    <div class="container">

                                                        <form action="" method="POST">
                                                            <div class="form-group">
                                                                <label for="title">Notice Title:</label>
                                                                <input type="text" id="title" name="title" class="form-control"
                                                                    value="<?php echo $notice ? htmlspecialchars($notice['title']) : ''; ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="description">Notice Description:</label>
                                                                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $notice ? htmlspecialchars($notice['description']) : ''; ?></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">
                                                                <?php echo $notice ? 'Update Notice' : 'Add Notice'; ?>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="ContentPlaceHolder1_Panel1" style="width:90%;overflow:auto;padding:20px">

                                                                <div class="container">
                                                                    <h2>Notice List</h2>
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>ID</th>
                                                                                <th>Title</th>
                                                                                <th>Description</th>
                                                                                <th>Created At</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php

                                                                            $sql = "SELECT * FROM tbl_notices ORDER BY created_at DESC";
                                                                            $stmt = $pdo->prepare($sql);
                                                                            $stmt->execute();
                                                                            $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                            foreach ($notices as $notice):
                                                                            ?>
                                                                                <tr>
                                                                                    <td><?php echo $notice['id']; ?></td>
                                                                                    <td><?php echo $notice['title']; ?></td>
                                                                                    <td><?php echo nl2br($notice['description']); ?></td>
                                                                                    <td><?php echo $notice['created_at']; ?></td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
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





    </form>


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>