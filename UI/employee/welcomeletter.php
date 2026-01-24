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
$designation = $fetchmember['designation'];
$memberdate = $fetchmember['date_time'];
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

<body>
    <form method="post" action="./welcomeletter.php" id="form1">

        <div class="wrapper p-0">
            <div class="container-scroller">


                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include "employeesidepanelheader.php"; ?>
                    <div class="main-panel">

                        <div class="" style="padding: 0px;">


                            <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                <div class="col-md-12">
                                    <h2>Welcome Letter</h2>
                                    <hr>


                                    <p>Dear, <b><?= $membername ?></b></p>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px">
                                            <p>
                                                <b><span>User Id: </span>
                                                    <span id=""><?= $memberid ?></span></b>
                                            </p>


                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px">
                                            <p>
                                                <b>
                                                    <span>Name : </span>
                                                    <span id=""><?= $membername ?></span>
                                                </b>
                                            </p>
                                        </div>
                                    </div> -->

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px">
                                            <p>
                                                <b>
                                                    <span>Designation : </span>
                                                    <span id=""><?= $designation ?></span>
                                                </b>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 10px">

                                            <p>
                                                <b>
                                                    <span>Mobile No. :</span>
                                                    <span id=""><?= "+91".$membermobile ?></span>
                                                </b>
                                            </p>


                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 col-xs-6" style="margin-top: 10px">

                                            <p>
                                                <b>
                                                    <span>Address :</span>

                                                    <span id=""><?= $memberaddress ?></span>


                                                </b>
                                            </p>

                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 15px">

                                            <p>
                                                <b>
                                                    <span>Status :</span>
                                                    <span id=""></span>
                                                </b>
                                            </p>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 col-xs-6" style="margin-top: 15px">

                                            <p>
                                                <b>
                                                    <span>Date of Joining : </span>
                                                    <span id=""><?= $memberdate; ?></span>
                                                </b>
                                            </p>

                                        </div>
                                    </div> -->
                                    <br>
                                </div>
                                <div class="col-md-12">
                                    <p><b><span>Warm Greetings and Congratulations!</span></b></p>
                                    <p>
                                        We are pleased to inform you that you are now an important member of the Amitabh Builders & Developers Pvt. Ltd. family. We warmly welcome you and appreciate your decision to associate with our organization. We firmly believe that your experience, honesty, and professional competence will contribute significantly to the continued growth and success of the company.
                                    </p>
                                    <p>
                                        You are joining us at an important phase, as the organization is actively developing and expanding its prestigious residential project “Janki Villa.” This project reflects our commitment to Quality, Trust, and Planned development, and we look forward to your valuable contribution toward its smooth execution and long-term success.
                                    </p>
                                    <p>
                                        <b>Employee Responsibilities & Professional Conduct</b>
                                    </p>
                                    <p>
                                        As a responsible Employee of the organization, you are expected to maintain the highest standards of Honesty, Integrity, and Accountability at all times. You are entrusted with the responsibility to behave in a respectful, ethical, and positive manner while interacting with Customers, Associates, Team Members, and all other stakeholders connected with the Janki Villa project and the organization as a whole.
                                    </p>

                                    <p>
Fulfilling your Duties with Sincerity, Teamwork, and a strong sense of responsibility is an essential part of our organizational culture. We firmly believe that discipline, Ethical conduct, Mutual Respect, and Team Spirit form the foundation of long-term personal as well as organizational success.
                                    </p>
                                    <p>
                                        <b>Our Purpose – Caring for Your Overall Well-Being</b>
                                    </p>
                                    <p>
Our purpose extends beyond business objectives alone. We are committed to supporting every essential aspect of human life. We value your Health, Lifestyle, Work-Life balance, personal growth, and future security, ensuring that you grow not only as a professional but also as a confident and fulfilled individual.

                                    </p>

                                    <p>
                                        We encourage you to make the best use of the resources, services, and opportunities provided by the organization and to actively contribute to the progress and reputation of Amitabh Builders & Developers Pvt. Ltd. and its flagship project “Janki Villa” through your Skills, Discipline, and Dedication. Providing guidance, support, and respect throughout this journey remains our responsibility.
                                    </p>

                                    <p>
                                        <b>“This is not just a job; it is where your future is built — with hard work, discipline, and growth.”
                                            <br>
“Every responsibility is an opportunity, and every opportunity is a step toward success.”</b>


                                    </p>
                                    <p>
                                        Once again, We extend a warm welcome to you into the Family of Responsible, Committed, and Progressive members of Amitabh Builders & Developers Pvt. Ltd.
                                    </p>
                                    <p>
                                        <b>With best wishes for success,</b>

                                    </p>
                                    <p>
                                        <b>Amitabh Builders & Developers Pvt. Ltd.</b>
                                    </p>


                                    <div class="clearfix"></div>
                                </div>
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
    </form>


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>