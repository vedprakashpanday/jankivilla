<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}


// if (isset($_POST['btnsubmit'])) {

//     $m_name = 'HHD' . rand(123456, 789654);

//     $sid = $_POST['sponsor_id'];
//     $sname = $_POST['sponsor_name'];
//     $mname = $_POST['mem_name'];
//     $m_name = 'HHD' . rand(123456, 789654);
//     $memberid = $m_name;
//     $mmob = $_POST['mem_mob'];
//     $memail = $_POST['mem_email'];
//     $mpass = $_POST['mem_pass'];
//     $aadhar = $_POST['aadhar'];

//     // New fields from old form
//     $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
//     $address = isset($_POST['Address']) ? $_POST['Address'] : null;
//     $city = isset($_POST['City']) ? $_POST['City'] : null;
//     $state = isset($_POST['state']) ? $_POST['state'] : null;
//     $dob = isset($_POST['Dob']) ? $_POST['Dob'] : null;

//     // Date and Time Handling
//     if (isset($_POST['d_time']) && !empty($_POST['d_time'])) {
//         $datetime = $_POST['d_time'];
//         $formatted_datetime = date('Y-m-d H:i:s', strtotime($datetime));
//     } else {
//         echo "Date and time is required.";
//         exit;
//     }

//     // Insert into tbl_regist
//     $insert = $pdo->prepare("INSERT INTO tbl_regist (sponsor_id, s_name, m_name, mem_sid, m_num, m_email, m_password, gender, address, city, state_name, date_of_birth, date_time, aadhar_number) 
//                          VALUES (:sid, :sname, :mname, :mssid, :mmob, :memail, :mpass, :gender, :address, :city, :state, :dob, :dtime, :aadhar_number)");

//     $insert->bindParam(':sid', $sid);
//     $insert->bindParam(':sname', $sname);
//     $insert->bindParam(':mname', $mname);
//     $insert->bindParam(':mssid', $m_name);
//     $insert->bindParam(':mmob', $mmob);
//     $insert->bindParam(':memail', $memail);
//     $insert->bindParam(':mpass', $mpass);
//     $insert->bindParam(':gender', $gender);
//     $insert->bindParam(':address', $address);
//     $insert->bindParam(':city', $city);
//     $insert->bindParam(':state', $state);
//     $insert->bindParam(':dob', $dob);
//     $insert->bindParam(':dtime', $formatted_datetime);
//     $insert->bindParam(':aadhar_number', $aadhar);

//     $insert->execute();

//     // Insert into tbl_hire
//     $insert = $pdo->prepare("INSERT INTO tbl_hire (sponsor_id, s_name, sponsor_pass) 
//                          VALUES (:ssid, :mname, :mpass)");

//     $insert->bindParam(':ssid', $m_name);
//     $insert->bindParam(':mname', $mname);
//     $insert->bindParam(':mpass', $mpass);
//     $insert->execute();

//     // Fetch inserted sponsor ID and password
//     $last_insert_id = $pdo->lastInsertId();
//     $stmt = $pdo->prepare("SELECT sponsor_id, sponsor_pass FROM tbl_hire WHERE id = :id");
//     $stmt->bindParam(':id', $last_insert_id);
//     $stmt->execute();
//     $row = $stmt->fetch();

//     // Success message with redirect
//     echo "<script>
//       if (confirm('Registration Successful!\\n\\nYour ID: " . $row['sponsor_id'] . "\\nYour Password: " . $row['sponsor_pass'] . "\\n\\nClick OK to continue')) {
//         window.location.href = 'DistributerJoining.php';
//       }
//     </script>";
// }

if (isset($_POST['btnsubmit'])) {
    $m_name = 'HHD' . rand(123456, 789654);

    $sid = $_POST['sponsor_id'];
    $sname = $_POST['sponsor_name'];
    $mname = $_POST['mem_name'];
    $memberid = $m_name;
    $mmob = $_POST['mem_mob'];
    $memail = $_POST['mem_email'];
    $mpass = $_POST['mem_pass'];
    $aadhar = $_POST['aadhar'];

    // New fields from old form
    $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
    $address = isset($_POST['Address']) ? $_POST['Address'] : null;
    $city = isset($_POST['City']) ? $_POST['City'] : null;
    $state = isset($_POST['state']) ? $_POST['state'] : null;
    $dob = isset($_POST['Dob']) ? $_POST['Dob'] : null;

    // Date and Time Handling
    if (isset($_POST['d_time']) && !empty($_POST['d_time'])) {
        $datetime = $_POST['d_time'];
        $formatted_datetime = date('Y-m-d H:i:s', strtotime($datetime));
    } else {
        echo "Date and time is required.";
        exit;
    }

    // File upload handling
    $upload_dir = 'member_document/'; // Directory for file storage
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

    // Address Proof File
    $address_proof_file = null;
    if (isset($_FILES['address_proof_file']) && $_FILES['address_proof_file']['error'] == 0) {
        if (in_array($_FILES['address_proof_file']['type'], $allowed_types)) {
            $file = time() . '_' . rand(1000, 9999) . '_' . basename($_FILES['address_proof_file']['name']);
            $file_path = $upload_dir . $file;
            if (move_uploaded_file($_FILES['address_proof_file']['tmp_name'], $file_path)) {
                $address_proof_file = $file; // Store only the filename in the database
            } else {
                echo "Failed to upload address proof file.";
                exit;
            }
        } else {
            echo "Invalid address proof file type.";
            exit;
        }
    } else {
        echo "Address proof file is required.";
        exit;
    }

    // Insert into tbl_regist
    $insert = $pdo->prepare("INSERT INTO tbl_regist (sponsor_id, s_name, m_name, mem_sid, m_num, m_email, m_password, gender, address, city, state_name, date_of_birth, date_time, aadhar_number) 
                         VALUES (:sid, :sname, :mname, :mssid, :mmob, :memail, :mpass, :gender, :address, :city, :state, :dob, :dtime, :aadhar_number)");

    $insert->bindParam(':sid', $sid);
    $insert->bindParam(':sname', $sname);
    $insert->bindParam(':mname', $mname);
    $insert->bindParam(':mssid', $m_name);
    $insert->bindParam(':mmob', $mmob);
    $insert->bindParam(':memail', $memail);
    $insert->bindParam(':mpass', $mpass);
    $insert->bindParam(':gender', $gender);
    $insert->bindParam(':address', $address);
    $insert->bindParam(':city', $city);
    $insert->bindParam(':state', $state);
    $insert->bindParam(':dob', $dob);
    $insert->bindParam(':dtime', $formatted_datetime);
    $insert->bindParam(':aadhar_number', $aadhar);

    $insert->execute();

    // Insert into tbl_hire
    $insert = $pdo->prepare("INSERT INTO tbl_hire (sponsor_id, s_name, sponsor_pass) 
                         VALUES (:ssid, :mname, :mpass)");

    $insert->bindParam(':ssid', $m_name);
    $insert->bindParam(':mname', $mname);
    $insert->bindParam(':mpass', $mpass);
    $insert->execute();

    // Insert into tbl_kyc
    $insert_kyc = $pdo->prepare("INSERT INTO tbl_kyc (sponsor_id, address_proof_file) 
                              VALUES (:sponsor_id, :address_proof_file)");

    $insert_kyc->bindParam(':sponsor_id', $m_name);
    $insert_kyc->bindParam(':address_proof_file', $address_proof_file);

    $insert_kyc->execute();

    // Fetch inserted sponsor ID and password
    $last_insert_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT sponsor_id, sponsor_pass FROM tbl_hire WHERE id = :id");
    $stmt->bindParam(':id', $last_insert_id);
    $stmt->execute();
    $row = $stmt->fetch();

    // Success message with redirect
    echo "<script>
      if (confirm('Registration Successful!\\n\\nYour ID: " . $row['sponsor_id'] . "\\nYour Password: " . $row['sponsor_pass'] . "\\n\\nClick OK to continue')) {
        window.location.href = 'DistributerJoining.php';
      }
    </script>";
}

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


    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css">
    <script src="../js/jquery-1.8.2.js" type="text/javascript"></script>
    <script src="../js/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(function() {
            var date = new Date();
            var currentMonth = date.getMonth();
            var currentDate = date.getDate();
            var currentYear = date.getFullYear();

            jQuery("#").datepicker({
                dateFormat: 'dd/mm/yy',
                maxDate: new Date(currentYear - 18, currentMonth, currentDate),
                changeMonth: true,
                changeYear: true
            });
        });
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
                    <div class="">
                        <div class="">
                            <div class="card">
                                <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <form method="post" action="" onsubmit="" id="form1">
                                                <div style="background: #fff; padding: 30px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                    <h2>Add New Member</h2>
                                                    <hr>

                                                    <div class="row">
                                                        <legend>Sponsor Details </legend>
                                                        <div class="col-md-4">
                                                            <b>Enter Sponsor Id</b>
                                                            <input name="sponsor_id"
                                                                type="text"
                                                                id="sponsor_id"
                                                                value="HHD000001"
                                                                placeholder="Sponsor ID"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Sponsor Name</b>
                                                            <input name="sponsor_name"
                                                                type="text"
                                                                id="sponsor_name"
                                                                value="admin"
                                                                readonly="readonly"
                                                                class="form-control"
                                                                placeholder="Sponsor Name">
                                                        </div>
                                                        <div class="col-md-4 d-none">
                                                            <b>Select Position</b>
                                                            <select name="position" class="form-control">
                                                                <option selected="selected" value="---Select Position---">---Select Position---</option>
                                                                <option value="1">Left</option>
                                                                <option value="2">Right</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <legend>Personal Details</legend>
                                                        <div class="col-md-4">
                                                            <b>Name</b>
                                                            <input name="mem_name" type="text" class="form-control" placeholder="Enter Name">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Parents Name</b>
                                                            <input name="parents_name" type="text" class="form-control" placeholder="S/O | D/O | W/O">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Gender</b>
                                                            <table>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><input type="radio" name="gender" value="Male">&nbsp;&nbsp;<label>Male</label></td>
                                                                        <td><input type="radio" name="gender" value="Female">&nbsp;&nbsp;<label>Female</label></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <b>Address</b>
                                                            <textarea name="Address" rows="2" class="form-control" placeholder="Address"></textarea>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>City</b>
                                                            <input name="City" type="text" class="form-control" placeholder="Enter City">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>State</b>
                                                            <select name="state" class="form-control">
                                                                <option value="">---Select State---</option>
                                                                <option value="Bihar">Bihar</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="datetime" style="display:none">
                                                        <label for="psw"><b>Date & Time</b></label>
                                                        <input type="text" name="d_time" id="psw" value="<?php echo date('Y-m-d H:i:s'); ?>">

                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <b>Email Id</b>
                                                            <input name="mem_email" type="text" class="form-control" placeholder="Email Id">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Mobile No</b>
                                                            <input name="mem_mob" type="text" class="form-control" placeholder="Enter Mobile No.">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Aadhar No.</b>
                                                            <input name="aadhar" type="text" class="form-control" placeholder="Enter Aadhar No." required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Date of Birth</b>
                                                            <input name="Dob" type="date" class="form-control" placeholder="Date of Birth">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <b>Upload Aadhar</b>
                                                            <input name="address_proof_file" type="file" class="form-control" accept="image/*" required>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <b>Password</b>
                                                            <input name="mem_pass" type="password" class="form-control" placeholder="Enter Password">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">&nbsp;</div>
                                                        <div class="col-md-4">&nbsp;</div>
                                                        <div class="col-md-4">
                                                            <center>
                                                                <input type="submit" name="btnsubmit" value="Submit" class="btn btn-info btn-cons">
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>



                                            <fieldset style="visibility:hidden">
                                                <legend>Nominee Detail</legend>

                                                <table class="add-tbl">
                                                    <tbody>
                                                        <tr>
                                                            <td>Name</td>
                                                            <td>
                                                                <input name="Nomineename" type="text" id="" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Gender</td>
                                                            <td>
                                                                <table id="" class="form-control">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td><input id="" type="radio" name="" value="Male" checked="checked"><label for="">Male</label></td>
                                                                            <td><input id="" type="radio" name="" value="Female"><label for="">Female</label></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Date of birth</td>
                                                            <td>
                                                                <input name="NomDOB" type="text" id="DOB" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Relationship</td>
                                                            <td>
                                                                <input name="NomRel" type="text" id="Rel" class="form-control">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>

                                            <fieldset style="visibility:hidden">
                                                <legend>Bank Details </legend>
                                                <table class="add-tbl">
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                Account Holder Name
                                                            </td>
                                                            <td>
                                                                <input name="ACHolder" type="text" id="ACHolder" class="form-control">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Bank A/c No
                                                            </td>
                                                            <td>
                                                                <input name="ACno" type="text" id="ACno" class="form-control">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Bank Name
                                                            </td>
                                                            <td>
                                                                <input name="BankName" type="text" id="BankName" class="form-control">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Branch
                                                            </td>
                                                            <td>
                                                                <input name="Branch" type="text" id="Branch" class="form-control">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Bank IFSC Code
                                                            </td>
                                                            <td>
                                                                <input name="IFSCCode" type="text" id="IFSCCode" class="form-control">

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                PAN Number
                                                            </td>
                                                            <td>

                                                                <input name="PanNo" type="text" id="PanNo" class="form-control">

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>

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
            <script>
                $(document).ready(function() {
                    $('#sponsor_id').on('mouseleave', function() {
                        var sponsorId = $(this).val().trim();

                        if (sponsorId !== '') {
                            $.ajax({
                                url: 'registration.php',
                                type: 'GET',
                                data: {
                                    action: 'get_sponsor_name',
                                    sponsor_id: sponsorId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.name) {
                                        $('#sponsor_name').val(response.name);
                                    } else if (response.error) {
                                        $('#sponsor_name').val('Not found');
                                    }
                                },
                                error: function() {
                                    $('#sponsor_name').val('Error fetching name');
                                }
                            });
                        }
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


</body>

</html>