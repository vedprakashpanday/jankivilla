<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}



if (isset($_POST['btnsubmit'])) {


    $m_id           = trim($_POST['m_id']);
    $sponsor_id     = trim($_POST['sponsor_id']);
    $sponsor_name   = trim($_POST['sponsor_name']);
    $mem_name       = trim($_POST['mem_name']);        // Full Name
    $so_do_name     = trim($_POST['so_do_name']);      // S/O, D/O, Spouse
    $parents_name   = trim($_POST['parents_name']);    // Mother's Name
    $designation    = $_POST['designation'];
    $gender         = $_POST['gender'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $nationality    = trim($_POST['nationality']);
    $dob = !empty($_POST['Dob']) ? $_POST['Dob'] : null;
    $proofType     = $_POST['proofType'];
    $bloodgroup     = $_POST['bloodgroup'];
     // 1. Get existing designations JSON

    $date_of_anniversary = !empty($_POST['date_of_anniversary'])
        ? $_POST['date_of_anniversary']
        : null;
    $mem_mob        = trim($_POST['mem_mob']);
    $alt_no         = trim($_POST['alt_no']);
    $mem_email      = trim($_POST['mem_email']);
    $pan_number     = trim($_POST['pan_number']);
    $aadhar         = trim($_POST['aadhar']);
    $native_place   = trim($_POST['native_place']);
    $address        = trim($_POST['Address']);
    $city           = trim($_POST['City']);
    $pincode        = trim($_POST['pincode']);
    $mem_pass       = $_POST['mem_pass'];
    $datetime       = date('Y-m-d H:i:s');
    $date = date('Y-m-d');

    $newEntry = [
        'designation' => $designation,
        'date' => $date
    ];



    $designations[] = $newEntry;


    // 4. JSON encode + UPDATE database
    $newJson = json_encode($designations, JSON_UNESCAPED_UNICODE);
    // File Upload
    $upload_dir = 'member_document/';
    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    $files = [];

    if (!empty($_FILES['address_proof_file']['name'][0])) {
        foreach ($_FILES['address_proof_file']['name'] as $k => $n) {
            $tmp = $_FILES['address_proof_file']['tmp_name'][$k];
            $type = $_FILES['address_proof_file']['type'][$k];
            if (in_array($type, $allowed)) {
                $ext = pathinfo($n, PATHINFO_EXTENSION);
                $fname = time() . rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($tmp, $upload_dir . $fname)) {
                    $files[] = $fname;
                }
            }
        }
    }
    if (empty($files)) die("Upload at least one proof.");

    $all_files = implode(',', $files);

    // Insert KYC
    $kyc = $pdo->prepare("INSERT INTO tbl_kyc (sponsor_id, address_proof_file) VALUES (?, ?)");
    $kyc->execute([$m_id, $all_files]);

     $sql = "INSERT INTO tbl_bank_details (
            member_id,account_name,account_no,bank_name,branch,ifsc_code,created_at
        ) VALUES (
            :id,:acc_name,:acc_no,:b_name,:br,:ifsc,now()
        )";

        $stmt = $pdo->prepare($sql);

     $stmt->execute([
     'id'=>$m_id,
     'acc_name'=>$_POST['ach_name']??null,
     'acc_no'=>$_POST['ba_number']??null,
     'b_name'=>$_POST['b_name']??null,
     'br'=>$_POST['br_name']??null,
     'ifsc'=>$_POST['ifsc']??null
     ]);

    // Insert into tbl_regist
    $sql = "INSERT INTO tbl_regist 
            (sponsor_id, s_name, m_name, parents_name, so_do_name, gender, designation, 
             date_of_birth, date_of_anniversary,m_num, m_email, m_password, date_time, address, city, 
             aadhar_number, pan_number, mem_sid, alt_no, native_place, pincode, 
             marital_status, nationality,designations,proof_type, blood_group)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $sponsor_id,
        $sponsor_name,
        $mem_name,
        $parents_name,
        $so_do_name,
        $gender,
        $designation,
        $dob,
        $date_of_anniversary,
        $mem_mob,
        $mem_email,
        $mem_pass,
        $datetime,
        $address,
        $city,
        $aadhar,
        $pan_number,
        $m_id,
        $alt_no,
        $native_place,
        $pincode,
        $marital_status,
        $nationality,
        $newJson,
        $proofType,
        $bloodgroup
    ]);

    // Insert into tbl_hire
    $hire = $pdo->prepare("INSERT INTO tbl_hire (sponsor_id, s_name, sponsor_pass) VALUES (?, ?, ?)");
    $hire->execute([$m_id, $mem_name, $mem_pass]);

    // Success
    $last = $pdo->lastInsertId();
    $row = $pdo->query("SELECT sponsor_id, sponsor_pass FROM tbl_hire WHERE id=$last")->fetch();

    echo "<script>
        if(confirm('Success!\\nID: {$row['sponsor_id']}\\nPass: {$row['sponsor_pass']}')){
            location='DistributerJoining.php';
        }
    </script>";
    exit;
}



// AJAX: Get allowed designations based on sponsor
if (isset($_GET['get_designations'])) {
    $sponsor_id = trim($_GET['sponsor_id'] ?? '');
    if (empty($sponsor_id)) {
        echo json_encode(['options' => '<option value="">-- Select Designation --</option><option disabled>No Sponsor ID</option>']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT designation FROM tbl_regist WHERE mem_sid = ?");
    $stmt->execute([$sponsor_id]);
    $sponsor = $stmt->fetch(PDO::FETCH_ASSOC); 

    if (!$sponsor) {
        echo json_encode(['options' => '<option value="">-- Select Designation --</option>
                                            <option value="Sales Executive (S.E.)">Sales Executive (S.E.)</option>
                                            <option value="Senior Sales Executive (S.S.E.)">Senior Sales Executive (S.S.E.)</option>
                                            <option value="Assistant Marketing Officer (A.M.O.)">Assistant Marketing Officer (A.M.O.)</option>
                                            <option value="Marketing Officer (M.O.)">Marketing Officer (M.O.)</option>
                                            <option value="Assistant Marketing Manager (A.M.M.)">Assistant Marketing Manager (A.M.M.)</option>
                                            <option value="Marketing Manager (M.M.)">Marketing Manager (M.M.)</option>
                                            <option value="Chief Marketing Manager (C.M.M.)">Chief Marketing Manager (C.M.M.)</option>
                                            <option value="Assistant General Manager (A.G.M.)">Assistant General Manager (A.G.M.)</option>
                                            <option value="Deputy General Manager (D.G.M.)">Deputy General Manager (D.G.M.)</option>
                                            <option value="General Manager (G.M.)">General Manager (G.M.)</option>
                                            <option value="Marketing Director (M.D.)">Marketing Director (M.D.)</option>
                                            <option value="Founder Member (F.M.)">Founder Member (F.M.)</option>
                                            
                                            
                                            ']);
        exit;
    }

    $sponsor_designation = trim($sponsor['designation']);

    // Level mapping
    $designation_to_level = [
        'Sales Executive (S.E.)' => 1,
        'Senior Sales Executive (S.S.E.)' => 2,
        'Assistant Marketing Officer (A.M.O.)' => 3,
        'Marketing Officer (M.O.)' => 4,
        'Assistant Marketing Manager (A.M.M.)' => 5,
        'Marketing Manager (M.M.)' => 6,
        'Chief Marketing Manager (C.M.M.)' => 7,
        'Assistant General Manager (A.G.M.)' => 8,
        'Deputy General Manager (D.G.M.)' => 9,
        'General Manager (G.M.)' => 10,
        'Marketing Director (M.D.)' => 11,
        'Founder Member (F.M.)' => 12,
    ];

    $all_designations = array_flip($designation_to_level); // level => name

    $sponsor_level = $designation_to_level[$sponsor_designation] ?? 0;
    if ($sponsor_level === 0) {
        echo json_encode(['options' => '<option value="">-- Select Designation --</option><option disabled>Invalid sponsor designation</option>']);
        exit;
    }

    // Build allowed options (equal or lower than sponsor)
    $options = '<option value="">-- Select Designation --</option>';
    for ($level = 1; $level < $sponsor_level; $level++) {
        $name = $all_designations[$level];
        $options .= "<option value=\"$name\">$name</option>";
    }

    if ($sponsor_level == 1) {
        $options .= '<option disabled>(No lower rank available)</option>';
    }

    echo json_encode(['options' => $options]);
    exit;
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
                                            <form method="post" action="" id="form1" enctype="multipart/form-data">
                                                <div style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f;">
                                                    <h2>Add New Member</h2>
                                                    <hr>

                                                    <!-- Sponsor Details -->
                                                    <div class="row">
                                                        <legend>Sponsor Details</legend>
                                                        <div class="col-md-4">
                                                            <b>Enter Sponsor Id</b>
                                                            <input name="sponsor_id" type="text" id="sponsor_id" class="form-control"
                                                                value="<?php echo $_GET['sponsor_id'] ?? 'JV000001'; ?>"
                                                                placeholder="e.g. JV000001" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Sponsor Name</b>
                                                            <input name="sponsor_name" type="text" id="sponsor_name" value="admin" readonly class="form-control">
                                                        </div>
                                                    </div>

                                                    <!-- Personal Details - EXACTLY AS IN PICTURE -->
                                                    <div class="row mt-3">
                                                        <legend>Personal Details</legend>

                                                        <!-- Row 1 -->
                                                        <div class="col-md-4">
                                                            <b>Name in Full</b>
                                                            <input name="mem_name" type="text" class="form-control" placeholder="Enter Full Name" required>
                                                        </div>

                                                          <div class="col-md-4">
    
                                                            <b>Select Blood Group</b>
                                                            <select class="form-control" id="bloodgroup" name="bloodgroup">
                                                                 <option value="">-- Select Blood Group --</option>
                                                                            <option value="A+">A+</option>
                                                                            <option value="A-">A-</option>
                                                                            <option value="B+">B+</option>
                                                                            <option value="B-">B-</option>
                                                                            <option value="AB+">AB+</option>
                                                                            <option value="AB-">AB-</option>
                                                                            <option value="O+">O+</option>
                                                                            <option value="O-">O-</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <b>S/O, D/O, Spouse's Name</b>
                                                            <input name="so_do_name" type="text" class="form-control" placeholder="Father/Spouse Name">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Mother's Name</b>
                                                            <input name="parents_name" type="text" class="form-control" placeholder="Mother's Name">
                                                        </div>


                                                        <!-- DESIGNATION DROPDOWN -->
                                                        <div class="col-md-4">
                                                            <label><b>Designation</b></label>
                                                            <select name="designation" id="designation" class="form-control" required>
                                                                <option value="">-- Select Designation --</option>
                                                            </select>
                                                            <div id="loading" class="text-muted small mt-1" style="display:none;">Loading...</div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <b>Gender</b><br>
                                                            <label><input type="radio" name="gender" value="Male"> Male</label>&nbsp;&nbsp;
                                                            <label><input type="radio" name="gender" value="Female"> Female</label>&nbsp;&nbsp;
                                                            <label><input type="radio" name="gender" value="Others"> Others</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Marital Status</b><br>
                                                            <label><input type="radio" name="marital_status" id="married" value="Married"> Married</label>&nbsp;&nbsp;
                                                            <label><input type="radio" name="marital_status" id="unmarried" value="Unmarried"> Unmarried</label>
                                                        </div>

                                                        <!-- Row 3 -->
                                                        <div class="col-md-4">
                                                            <b>Nationality</b>
                                                            <input name="nationality" type="text" class="form-control" value="Indian" placeholder="Nationality">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Date of Birth</b>
                                                            <input name="Dob" type="date" class="form-control">
                                                        </div>

                                                        <div class="col-md-4" id="DOA">
                                                            <b>Date of Anniversary</b>
                                                            <input name="date_of_anniversary" type="date" class="form-control" >
                                                        </div>

                                                        <div class="col-md-4">
                                                            <b>Contact No.</b>
                                                            <input name="mem_mob" type="text" class="form-control" placeholder="Enter Mobile No." required>
                                                        </div>

                                                        <!-- Row 4 -->
                                                        <div class="col-md-4">
                                                            <b>Alt. No.</b>
                                                            <input name="alt_no" type="text" class="form-control" placeholder="Alternate Number">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Email ID</b>
                                                            <input name="mem_email" type="email" class="form-control" placeholder="Email Id">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>PAN No.</b>
                                                            <input name="pan_number" type="text" class="form-control" placeholder="Enter PAN No.">
                                                        </div>

                                                        <!-- Row 5 -->
                                                        <div class="col-md-4">
                                                            <b>Aadhar Card No.</b>
                                                            <input name="aadhar" type="text" class="form-control" placeholder="Enter Aadhar No." required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Native Place</b>
                                                            <input name="native_place" type="text" class="form-control" placeholder="Native Place">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Communication Address</b>
                                                            <textarea name="Address" rows="2" class="form-control" placeholder="Full Address"></textarea>
                                                        </div>

                                                        <!-- Row 6 -->
                                                        <div class="col-md-4">
                                                            <b>City/Town/Village</b>
                                                            <input name="City" type="text" class="form-control" placeholder="City/Town/Village">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Pin Code</b>
                                                            <input name="pincode" type="text" class="form-control" placeholder="Pin Code">
                                                        </div>


                                                        <!-- ID & Password -->
                                                        <div class="col-md-4">
                                                            <b>Member ID</b>
                                                            <input name="m_id" type="text" class="form-control" placeholder="" required>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Password</b>
                                                            <input name="mem_pass" type="password" class="form-control" placeholder="Enter Password" required>
                                                        </div>

                                                        <!-- File Upload -->
                                                       <div class="col-md-4">
    
                                                            <label><b>Select Proof Type</b></label>
                                                            <select class="form-control" id="proofType" name="proofType">
                                                                <option value="">-- Select Proof --</option>
                                                                <option value="aadhar">Aadhar Card</option>
                                                                <option value="pan">PAN Card</option>
                                                                <option value="passport">Passport</option>
                                                                <option value="passbook">Bank Passbook</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3 d-none" id="proofUploadBox">
                                                            <label class="form-label" id="proofLabel"></label>
                                                            <input name="address_proof_file[]" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" id="proofFile" multiple>
                                                            <small class="text-muted" id="proofHint"></small>
                                                        </div>


                                                        <!-- Hidden DateTime -->
                                                        <div style="display:none">
                                                            <input type="text" name="d_time" value="<?php echo date('Y-m-d H:i:s'); ?>">
                                                        </div>


                                                          <!-- ==================== Bank DETAILS  start==================== -->
 <div class="col-md-12">
                                                   
                                                        <h2>Bank Details</h2>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>Account Holder Name:</b>
                                                                <i><input name="ach_name" type="text" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>




                                                            <div class="col-md-4">
                                                                <b> Bank A/c No:</b>

                                                                <i> <input name="ba_number" type="text" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Bank Name:</b>

                                                                <i> <input name="b_name" type="text" id="" class="form-control" style="font-weight:bold;"></i>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>
                                                                    Branch Name:
                                                                </b>
                                                                <i>
                                                                    <input name="br_name" type="text" id="" class="form-control" style="font-weight:bold;">
                                                                </i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>
                                                                    IFSC Code:</b>
                                                                <i><input name="ifsc" type="text" id="" class="form-control" style="font-weight:bold;"></i>

                                                            </div>
                                                            
                                                        </div>
                                                        

                                                    </div>
                                              


<!-- ==================== Bank DETAILS end ==================== -->
                                                    </div>
                                                    <!-- Submit -->
                                                    <div class="row mt-4">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" name="btnsubmit" class="btn btn-info btn-cons">Submit</button>
                                                        </div>
                                                    </div>
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

<script>
document.getElementById('proofType').addEventListener('change', function () {

    const uploadBox = document.getElementById('proofUploadBox');
    const label = document.getElementById('proofLabel');
    const hint = document.getElementById('proofHint');
    const fileInput = document.getElementById('proofFile');

    fileInput.value = ''; // reset file

    if (!this.value) {
        uploadBox.classList.add('d-none');
        return;
    }

    uploadBox.classList.remove('d-none');

    switch (this.value) {
        case 'aadhar':
            label.innerText = 'Upload Aadhar Card';
            hint.innerText = 'Accepted: JPG, PNG, PDF (Max 2MB)';
            break;

        case 'pan':
            label.innerText = 'Upload PAN Card';
            hint.innerText = 'Accepted: JPG, PNG, PDF (Max 2MB)';
            break;

        case 'passport':
            label.innerText = 'Upload Passport';
            hint.innerText = 'Accepted: JPG, PNG, PDF (Max 2MB)';
            break;

        case 'passbook':
            label.innerText = 'Upload Bank Passbook';
            hint.innerText = 'Accepted: JPG, PNG, PDF (Max 2MB)';
            break;
    }
});
</script>
<script>
 $(document).on('click', '#unmarried', function() {
                        $('#DOA').css('display', 'none');
                    });

                    $(document).on('click', '#married', function() {
                        $('#DOA').css('display', 'block');
                    });
</script>
            <script>
                // Auto-load on page load
                document.addEventListener('DOMContentLoaded', function() {
                    const sponsorId = document.getElementById('sponsor_id').value.trim();
                    if (sponsorId) fetchDesignations(sponsorId);
                });

                // Live update when sponsor ID changes
                document.getElementById('sponsor_id').addEventListener('input', function() {
                    const sponsorId = this.value.trim();
                    if (sponsorId.length >= 5) {
                        fetchDesignations(sponsorId);
                    } else {
                        document.getElementById('designation').innerHTML = '<option value="">-- Select Designation --</option>';
                    }
                });

                function fetchDesignations(sponsorId) {
                    const select = document.getElementById('designation');
                    const loading = document.getElementById('loading');
                    loading.style.display = 'block';
                    select.innerHTML = '<option value="">-- Loading... --</option>';

                    fetch(`?get_designations=1&sponsor_id=${encodeURIComponent(sponsorId)}`)
                        .then(res => res.json())
                        .then(data => {
                            select.innerHTML = data.options;
                            loading.style.display = 'none';
                        })
                        .catch(() => {
                            select.innerHTML = '<option value="">-- Error --</option>';
                            loading.style.display = 'none';
                        });
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



</body>

</html>