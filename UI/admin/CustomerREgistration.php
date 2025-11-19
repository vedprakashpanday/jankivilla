<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


$js_alert = '';
if (isset($_POST['btnsubmit'])) {
    try {
        // Generate ID & Password
        $customer_id = "CUST" . str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);
        $password    = str_pad(rand(0, 999999), 6, "0", STR_PAD_LEFT);

        $sql = "INSERT INTO customer_details (
            customer_id, pass_book_no, password, customer_name, so_do_wo, dob, mothers_name, occupation, gender, marital_status,
            nationality, customer_mobile, alternate_mobile, customer_email, aadhar_number, pan_number,
            native_place, address, city_town_village, pincode, state, district,
            nominee_name, nominee_so_do_wo, nominee_dob, nominee_mobile, nominee_alternate_mobile,
            nominee_email, nominee_aadhar, nominee_pan, nominee_address, nominee_pincode,
            nominee_state, nominee_district
        ) VALUES (
            :customer_id, :pass_book_no, :password, :customer_name, :so_do_wo, :dob, :mothers_name, :occupation, :gender, :marital_status,
            :nationality, :customer_mobile, :alternate_mobile, :customer_email, :aadhar_number, :pan_number,
            :native_place, :address, :city_town_village, :pincode, :state, :district,
            :nominee_name, :nominee_so_do_wo, :nominee_dob, :nominee_mobile, :nominee_alternate_mobile,
            :nominee_email, :nominee_aadhar, :nominee_pan, :nominee_address, :nominee_pincode,
            :nominee_state, :nominee_district
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':customer_id'              => $customer_id,
            ':pass_book_no'             => $_POST['pass_book_no'],
            ':password'                 => $password,
            ':customer_name'            => $_POST['customer_name'],
            ':so_do_wo'                 => $_POST['so_do_wo'],
            ':dob'                      => $_POST['dob'],
            ':mothers_name'             => $_POST['mothers_name'],
            ':occupation'               => $_POST['occupation'],
            ':gender'                   => $_POST['gender'],
            ':marital_status'           => $_POST['marital_status'],
            ':nationality'              => $_POST['nationality'] ?? 'Indian',
            ':customer_mobile'          => $_POST['customer_mobile'],
            ':alternate_mobile'         => $_POST['alternate_mobile'],
            ':customer_email'           => $_POST['customer_email'],
            ':aadhar_number'            => $_POST['aadhar_number'],
            ':pan_number'               => $_POST['pan_number'],
            ':native_place'             => $_POST['native_place'],
            ':address'                  => $_POST['address'],
            ':city_town_village'        => $_POST['city_town_village'],
            ':pincode'                  => $_POST['pincode'],
            ':state'                    => $_POST['state'],
            ':district'                 => $_POST['district'],
            ':nominee_name'             => $_POST['nominee_name'],
            ':nominee_so_do_wo'         => $_POST['nominee_so_do_wo'],
            ':nominee_dob'              => $_POST['nominee_dob'],
            ':nominee_mobile'           => $_POST['nominee_mobile'],
            ':nominee_alternate_mobile' => $_POST['nominee_alternate_mobile'],
            ':nominee_email'            => $_POST['nominee_email'],
            ':nominee_aadhar'           => $_POST['nominee_aadhar'],
            ':nominee_pan'              => $_POST['nominee_pan'],
            ':nominee_address'          => $_POST['nominee_address'],
            ':nominee_pincode'          => $_POST['nominee_pincode'],
            ':nominee_state'            => $_POST['nominee_state'],
            ':nominee_district'         => $_POST['nominee_district']
        ]);

        $js_alert = "<script>
            alert('Customer registered successfully!\\nCustomer ID: $customer_id\\nPassword: $password');
            window.location = window.location.href;
        </script>";
    } catch (Exception $e) {
        $js_alert = "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
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

    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include 'adminheadersidepanel.php'; ?>

                <div class="main-panel">
                    <style>
                        .col-md-4 {
                            padding: 1rem;
                        }

                        .form-control {
                            margin-top: 7px;
                        }
                    </style>
                    <div class="content-wrapper">
                        <div class="">
                            <div class="">
                                <div class="" style="padding-bottom: 50px;">
                                    <div class="">
                                        <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3><u>Customer Details:-</u></h3>
                                            <form method="POST" action="">

                                                <!-- ==================== PERSONAL DETAILS ==================== -->
                                                <div class="form-section">
                                                    <legend>PERSONAL DETAILS</legend>

                                                    <div class="row g-3">

                                                        <div class="col-md-4">
                                                            <label><b>Pass Book No.</b></label>
                                                            <input name="pass_book_no" type="text" class="form-control" required>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label><b>Name in Full</b></label>
                                                            <input name="customer_name" type="text" class="form-control" required>
                                                        </div>

                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>S/o, D/o, W/o</b></label>
                                                            <input name="so_do_wo" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Name</b></label>
                                                            <input name="nominee_name" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Relation</b></label>
                                                            <input name="nominee_so_do_wo" type="text" class="form-control" placeholder="e.g. Father, Wife">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Mother's Name</b></label>
                                                            <input name="mothers_name" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Occupation</b></label>
                                                            <input name="occupation" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Date of Birth</b></label>
                                                            <input name="dob" type="date" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2 align-items-center">
                                                        <div class="col-md-2">
                                                            <label><b>Gender</b></label>
                                                            <div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="gender" value="Male" id="male">
                                                                    <label class="form-check-label" for="male">Male</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="gender" value="Female" id="female">
                                                                    <label class="form-check-label" for="female">Female</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="gender" value="Others" id="others">
                                                                    <label class="form-check-label" for="others">Others</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label><b>Marital Status</b></label>
                                                            <div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="marital_status" value="Married" id="married">
                                                                    <label class="form-check-label" for="married">Married</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="marital_status" value="Unmarried" id="unmarried">
                                                                    <label class="form-check-label" for="unmarried">Unmarried</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label><b>Nationality</b></label>
                                                            <input name="nationality" type="text" class="form-control" value="Indian" readonly>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Alt. No.</b></label>
                                                            <input name="alternate_mobile" type="text" class="form-control" placeholder="Alternate Mobile">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Contact No.</b></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">+91</span>
                                                                <input name="customer_mobile" type="text" class="form-control" required maxlength="10">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label><b>Email ID</b></label>
                                                            <input name="customer_email" type="email" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-6">
                                                            <label><b>Aadhar Card No.</b></label>
                                                            <input name="aadhar_number" type="text" class="form-control" placeholder="XXXX XXXX XXXX" maxlength="12">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label><b>PAN NO.</b></label>
                                                            <input name="pan_number" type="text" class="form-control" style="text-transform:uppercase;" maxlength="10">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Native Place</b></label>
                                                            <input name="native_place" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label><b>Communication Address</b></label>
                                                            <input name="address" type="text" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>City/Town/Village</b></label>
                                                            <input name="city_town_village" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label><b>Pin Code</b></label>
                                                            <input name="pincode" type="text" class="form-control" maxlength="6">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label><b>State</b></label>
                                                                    <input name="state" type="text" class="form-control">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label><b>District</b></label>
                                                                    <input name="district" type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ==================== NOMINEE DETAILS ==================== -->
                                                <div class="form-section mt-4">
                                                    <legend>NOMINEE DETAILS</legend>

                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Name</b></label>
                                                            <input name="nominee_name" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>S/o, D/o, W/o</b></label>
                                                            <input name="nominee_so_do_wo" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Date of Birth</b></label>
                                                            <input name="nominee_dob" type="date" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Mobile No</b></label>
                                                            <input name="nominee_mobile" type="text" class="form-control" maxlength="10">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Alternate Mobile No</b></label>
                                                            <input name="nominee_alternate_mobile" type="text" class="form-control" maxlength="10">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Email Id</b></label>
                                                            <input name="nominee_email" type="email" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Aadhar</b></label>
                                                            <input name="nominee_aadhar" type="text" class="form-control" maxlength="12">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Nominee PAN</b></label>
                                                            <input name="nominee_pan" type="text" class="form-control" style="text-transform:uppercase;" maxlength="10">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Address</b></label>
                                                            <input name="nominee_address" type="text" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Nominee PIN Code</b></label>
                                                            <input name="nominee_pincode" type="text" class="form-control" maxlength="6">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Nominee State</b></label>
                                                            <input name="nominee_state" type="text" class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Nominee District</b></label>
                                                            <input name="nominee_district" type="text" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button type="submit" name="btnsubmit" class="btn btn-danger btn-lg px-5">
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
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