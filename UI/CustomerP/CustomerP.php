<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hari Home Developers</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../icon/harihomes1-fevicon.png" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="../resources/vendors/feather/feather.css" />
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="../resources/vendors/css/vendor.bundle.base.css" />
    <link rel="stylesheet" href="../resources/vendors/select2/select2.min.css" />
    <link rel="stylesheet" href="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
    <link rel="stylesheet" href="../resources/vendors/mdi/css/materialdesignicons.min.css" />
    <link rel="stylesheet" href="../resources/vendors/fullcalendar/fullcalendar.min.css" />
    <link rel="stylesheet" href="../resources/css/vertical-layout-light/style.css" />
    <link rel="stylesheet" href="../resources/css/style.css" />

    <style>
        .navbar .navbar-brand-wrapper .navbar-brand img {
            margin-top: 0px;
        }

        .franchiseSidebar:hover {
            background: #ff9027 !important;
        }
    </style>
    <script>
        function display_ct7() {
            var x = new Date();
            var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
            var hours = x.getHours() % 12 || 12;
            var minutes = (x.getMinutes() < 10 ? '0' : '') + x.getMinutes();
            var seconds = (x.getSeconds() < 10 ? '0' : '') + x.getSeconds();
            var month = (x.getMonth() + 1 < 10 ? '0' : '') + (x.getMonth() + 1);
            var date = (x.getDate() < 10 ? '0' : '') + x.getDate();
            var x1 = date + "-" + month + "-" + x.getFullYear() + " " + hours + ":" + minutes + ":" + seconds + " " + ampm;
            document.getElementById('ct7').innerHTML = x1;
        }

        function startTime() {
            display_ct7();
            setInterval(display_ct7, 1000);
        }
        window.onload = startTime;
    </script>
</head>

<body>
    <div class="wrapper">
        <nav class="navbar fixed-top d-flex flex-row">
            <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand" href="index.php">
                    <img src="../../image/harihomes1-logo.png" alt="Logo">
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav">
                    <span id="ct7"></span>
                    <li class="nav-item">
                        <a class="ti-power-off btn btn-warning" href="../../Customer_Login.php"></a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas">
                <ul class="nav">
                    <li class="nav-item active">
                        <a class="nav-link franchiseSidebar" href="index.php" style="background-color:#ff9027;">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link franchiseSidebar" data-toggle="collapse" href="#exam" aria-expanded="false">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Sale Management</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="exam">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item"><a class="nav-link" href="CheckInvoice.php">Sale Invoice</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-panel">
                <div class="content-wrapper">
                    <!-- <?php include 'content.php'; ?> -->
                </div>
                <footer class="footer text-center">
                    <span>&copy; 2024. <a href="https://www.infoerasoftware.com" target="_blank">Infoera Software Services Pvt. Ltd</a>, All Rights Reserved.</span>
                    <br>Designed By <a href="#" target="_blank">InfoEra</a>
                </footer>
            </div>
        </div>
    </div>
    <!-- JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
    <script src="../resources/vendors/chart.js/Chart.min.js"></script>
    <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="../resources/js/custom.js"></script>
    <script src="../resources/js/off-canvas.js"></script>
    <script src="../resources/js/hoverable-collapse.js"></script>
    <script src="../resources/js/template.js"></script>
    <script src="../resources/js/settings.js"></script>
    <script src="../resources/js/todolist.js"></script>
    <script src="../resources/js/dashboard.js"></script>
    <script src="../resources/js/data-table.js"></script>
</body>

</html>