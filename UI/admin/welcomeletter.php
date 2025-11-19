<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
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
    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini" data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <form method="post" action="./welcomeletter.php" id="form1">
        <div class="aspNetHidden">
            <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwULLTE5MjE0MDU4ODhkZFqlW0UCQ5JwVFF0+H5a43hewk4VT7JMprgY567Xt7OR">
        </div>

        <div class="aspNetHidden">

            <input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="C6A8E015">
            <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/wEdAAIK/xgkFfKgbXkqZr9RrnCY57WO95nKSImdyGw0l0zc02fJ/BPQvM2IP8/1Sx4xCYmbTRmVg43iQzBh51gtDzw+">
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
                            <div class="col-md-12 stretch-card">
                                <div class="card">


                                    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                        <div class="row justify-content-center">

                                            <div class="col-md-12">
                                                <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                    <h2>Welcome Letter</h2>
                                                    <hr>
                                                    <p>Dear, <b></b></p>
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:15px">
                                                            <p>
                                                                <b><i> User Id: </i> <span id="ContentPlaceHolder1_useridlbl"></span></b>
                                                            </p>


                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:15px">
                                                            <p>
                                                                <b>
                                                                    <i> Name : </i> <span id="ContentPlaceHolder1_namelbl"></span>
                                                                </b>
                                                            </p>




                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:15px">

                                                            <p>
                                                                <b>
                                                                    <i>Mobile No. :</i> <span id="ContentPlaceHolder1_mobilenolbl"></span>
                                                                </b>
                                                            </p>


                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-6" style="margin-top:15px">

                                                            <p>
                                                                <b>
                                                                    <i>Address :</i>

                                                                    <span id="ContentPlaceHolder1_addresslbl"></span>
                                                                    <span id="ContentPlaceHolder1_distlbl"></span>
                                                                    <span id="ContentPlaceHolder1_statelbl"></span>
                                                                    <span id="ContentPlaceHolder1_pincodelbl"></span>

                                                                </b>
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:15px">

                                                            <p>
                                                                <b>
                                                                    <i>Status :</i> <span id="ContentPlaceHolder1_statudlbl"></span>
                                                                </b>
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 col-sm-6 col-xs-6" style="margin-top:15px">

                                                            <p>
                                                                <b>
                                                                    <i>Date of Joining : </i> <span id="ContentPlaceHolder1_datelbl"></span>
                                                                </b>
                                                            </p>

                                                        </div>
                                                    </div>
                                                    <br>

                                                    <p><b><i> Congratulations!</i></b></p>
                                                    <p>


                                                        Welcome to the family of Amitabh Builders & Developers Pvt. Ltd.. Thank you for joining us. Now you are part of a wonderful business opportunity for a bright future.
                                                    </p>
                                                    <p>
                                                        We have commenced upon a journey which has been remarkable so far and our pre launch has been a huge success beyond our expectations.
                                                    </p>
                                                    <p>
                                                        <b>Taking care of your life from all sides:</b>
                                                    </p>
                                                    <p>
                                                        Our motto is to take care of all the required needs of human life and to spread happiness all around. We take care of your Health, your Lifestyle, your Holidays and thus make your life more enjoyable. Take the best use of our products, services and plan for a Great Success and Better Future. We always strive for excellence. We are here to lead, guide and accompany you, towards your world of success.
                                                    </p>

                                                    <p>
                                                        Once again, Welcome to Amitabh Builders & Developers Pvt. Ltd. family of prosperous and health conscious members.
                                                    </p>
                                                    <p>
                                                        Coming together is a Beginning, Keeping together is Progress and Working together is success in Amitabh Builders & Developers Pvt. Ltd..
                                                    </p>
                                                    <p>
                                                        <b>With Winning Regards,</b>

                                                    </p>
                                                    <p>
                                                        <b>Amitabh Builders & Developers Pvt. Ltd.</b>
                                                    </p>

                                                    <p style="text-align:center">
                                                        <input type="submit" name="ctl00$ContentPlaceHolder1$ctl00" value="Print" class="btn-primary">
                                                    </p>
                                                    <div class="clearfix"></div>

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