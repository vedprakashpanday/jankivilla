<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
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

$js_alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_nominee'])) {

    $member_id = $_SESSION['sponsor_id'] ?? '';
    if (empty($member_id)) {
        $js_alert = "<script>alert('Error: Member ID not found in session.');</script>";
    } else {

        // Collect fields
        $nominee_name         = trim($_POST['nominee_name'] ?? '');
        $relationship         = trim($_POST['relationship'] ?? '');
        $father_husband_name  = trim($_POST['father_husband_name'] ?? '');
        $aadhar_no            = trim($_POST['aadhar_no'] ?? '');
        $pan_no               = trim($_POST['pan_no'] ?? '');
        $native_place         = trim($_POST['native_place'] ?? '');
        $communication_address = trim($_POST['communication_address'] ?? '');
        $city_town_village    = trim($_POST['city_town_village'] ?? '');
        $contact              = trim($_POST['contact'] ?? '');
        $email                = trim($_POST['email'] ?? '');
        $pin_code             = trim($_POST['pin_code'] ?? '');
        $witness1_name        = trim($_POST['witness1_name'] ?? '');
        $witness1_sign        = trim($_POST['witness1_sign'] ?? '');
        $witness2_name        = trim($_POST['witness2_name'] ?? '');
        $witness2_sign        = trim($_POST['witness2_sign'] ?? '');
        $declaration_agreed   = isset($_POST['declaration_agreed']) ? 1 : 0;

        $success = true;
        $errors  = [];

        // Validation
        if (empty($nominee_name)) {
            $errors[] = "Nominee Name is required.";
            $success = false;
        }
        if (empty($relationship)) {
            $errors[] = "Relationship is required.";
            $success = false;
        }
        if (empty($father_husband_name)) {
            $errors[] = "Father/Husband Name is required.";
            $success = false;
        }
        if (empty($aadhar_no) || !preg_match('/^\d{12}$/', $aadhar_no)) {
            $errors[] = "Valid 12-digit Aadhar No. is required.";
            $success = false;
        }
        if (empty($pan_no) || !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_no)) {
            $errors[] = "Valid PAN No. is required.";
            $success = false;
        }
        if (empty($native_place)) {
            $errors[] = "Native Place is required.";
            $success = false;
        }
        if (empty($communication_address)) {
            $errors[] = "Communication Address is required.";
            $success = false;
        }
        if (empty($city_town_village)) {
            $errors[] = "City/Town/Village is required.";
            $success = false;
        }
        if (empty($contact) || !preg_match('/^\d{10}$/', $contact)) {
            $errors[] = "Valid 10-digit Contact No. is required.";
            $success = false;
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid Email is required.";
            $success = false;
        }
        if (empty($pin_code) || !preg_match('/^\d{6}$/', $pin_code)) {
            $errors[] = "Valid 6-digit Pin Code is required.";
            $success = false;
        }
        if (empty($witness1_name)) {
            $errors[] = "Witness 1 Name is required.";
            $success = false;
        }
        if (empty($witness2_name)) {
            $errors[] = "Witness 2 Name is required.";
            $success = false;
        }
        if (!$declaration_agreed) {
            $errors[] = "You must agree to the declaration.";
            $success = false;
        }

        if ($success) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO tbl_nominee 
                    (member_id, nominee_name, relationship, father_husband_name, aadhar_no, pan_no, native_place, 
                     communication_address, city_town_village, contact, email, pin_code, 
                     witness1_name, witness1_sign, witness2_name, witness2_sign, declaration_agreed)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    nominee_name = VALUES(nominee_name),
                    relationship = VALUES(relationship),
                    father_husband_name = VALUES(father_husband_name),
                    aadhar_no = VALUES(aadhar_no),
                    pan_no = VALUES(pan_no),
                    native_place = VALUES(native_place),
                    communication_address = VALUES(communication_address),
                    city_town_village = VALUES(city_town_village),
                    contact = VALUES(contact),
                    email = VALUES(email),
                    pin_code = VALUES(pin_code),
                    witness1_name = VALUES(witness1_name),
                    witness1_sign = VALUES(witness1_sign),
                    witness2_name = VALUES(witness2_name),
                    witness2_sign = VALUES(witness2_sign),
                    declaration_agreed = VALUES(declaration_agreed)
                ");
                $stmt->execute([
                    $member_id,
                    $nominee_name,
                    $relationship,
                    $father_husband_name,
                    $aadhar_no,
                    $pan_no,
                    $native_place,
                    $communication_address,
                    $city_town_village,
                    $contact,
                    $email,
                    $pin_code,
                    $witness1_name,
                    $witness1_sign,
                    $witness2_name,
                    $witness2_sign,
                    $declaration_agreed
                ]);
                $js_alert = "<script>alert('Nominee details saved successfully!');</script>";
            } catch (Exception $e) {
                $js_alert = "<script>alert('Error: Could not save nominee details.');</script>";
            }
        } else {
            $msg = implode("\\n", $errors);
            $js_alert = "<script>alert('Errors:\\n{$msg}');</script>";
        }
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


                <?php include 'associate-headersidepanel.php'; ?>


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
                                            <div class="container mt-5">
                                                <div class="card shadow">
                                                    <div class="card-header bg-primary text-white">
                                                        <h4 class="mb-0">Add Bank Details</h4>
                                                    </div>
                                                    <div class="card-body">

                                                        <!-- ==================== FAMILY / NOMINEE DETAILS ==================== -->
                                                        <form method="post" action="">
                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <fieldset style="border:2px solid #dc3545; padding:15px; border-radius:8px;">
                                                                        <legend style="width:auto; padding:0 10px; font-weight:bold; color:#dc3545; font-size:18px;">
                                                                            FAMILY / NOMINEE DETAILS
                                                                        </legend>

                                                                        <!-- Nominee Fields -->
                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered table-sm" style="font-size:14px;">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="width:25%; background:#f8f9fa; font-weight:bold;">Full Name</td>
                                                                                        <td><input type="text" name="nominee_name" class="form-control form-control-sm" placeholder="Nominee Full Name" value="<?php echo htmlspecialchars($_POST['nominee_name'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Father's / Husband's Name</td>
                                                                                        <td><input type="text" name="father_husband_name" class="form-control form-control-sm" placeholder="Father/Husband Name" value="<?php echo htmlspecialchars($_POST['father_husband_name'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Relationship</td>
                                                                                        <td><input type="text" name="relationship" class="form-control form-control-sm" placeholder="e.g. Father, Wife" value="<?php echo htmlspecialchars($_POST['relationship'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Aadhar No.</td>
                                                                                        <td><input type="text" name="aadhar_no" class="form-control form-control-sm" placeholder="12-digit Aadhar" maxlength="12" value="<?php echo htmlspecialchars($_POST['aadhar_no'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Pan No.</td>
                                                                                        <td><input type="text" name="pan_no" class="form-control form-control-sm" placeholder="e.g. ABCDE1234F" style="text-transform:uppercase;" value="<?php echo htmlspecialchars($_POST['pan_no'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Native Place</td>
                                                                                        <td><input type="text" name="native_place" class="form-control form-control-sm" placeholder="Village/Town" value="<?php echo htmlspecialchars($_POST['native_place'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Communication Address</td>
                                                                                        <td><textarea name="communication_address" class="form-control form-control-sm" rows="2" placeholder="Full Address"><?php echo htmlspecialchars($_POST['communication_address'] ?? ''); ?></textarea></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">City/Town/Village</td>
                                                                                        <td><input type="text" name="city_town_village" class="form-control form-control-sm" placeholder="City" value="<?php echo htmlspecialchars($_POST['city_town_village'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Contact</td>
                                                                                        <td><input type="text" name="contact" class="form-control form-control-sm" placeholder="10-digit Mobile" maxlength="10" value="<?php echo htmlspecialchars($_POST['contact'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Email</td>
                                                                                        <td><input type="email" name="email" class="form-control form-control-sm" placeholder="email@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="background:#f8f9fa; font-weight:bold;">Pin Code</td>
                                                                                        <td><input type="text" name="pin_code" class="form-control form-control-sm" placeholder="6-digit Pin" maxlength="6" value="<?php echo htmlspecialchars($_POST['pin_code'] ?? ''); ?>"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <!-- ==================== GUIDELINES (1 to 11) ==================== -->
                                                                        <div class="mt-4 p-3 border rounded" style="background:#f0f8ff; font-size:13px; line-height:1.5;">
                                                                            <h6 class="text-primary"><strong>Some Guide Line For The Administration & Marketing Members:</strong></h6>
                                                                            <ol style="margin-left:15px;">
                                                                                <li>Salary Member should be work minimum 8 hours a day.</li>
                                                                                <li>Bonus or Incentive amount will be given as per Tuesday.</li>
                                                                                <li>Experience, Weekly Holiday in Office will be Every Tuesday.</li>
                                                                                <li>Any person Marketing Members will be Purely on Commission Basis.</li>
                                                                                <li>You will be paid Commission on Booked Plot by you & your Team. Commission will paid after deducting of 5% TDS.</li>
                                                                                <li>No Direct Financial Transactions will be done with the Customers & You must follow the Company's rules, and you are not Entitled to Personal Commitments to Clients.</li>
                                                                                <li>Salary will be paid on 10th on every month & Commission will be Paid Twice Every Month.</li>
                                                                                <li>You should refer your services only to <strong>AMITABH BUILDERS & DEVELOPERS PVT. LTD.</strong></li>
                                                                                <li>Marketing services will be terminated and commission will be stopped without notice.</li>
                                                                                <li>The Company reserves all right to refuse, Terminate and cancel your marketing services for the irregularities against the Terms and Conditions of the Company.</li>
                                                                                <li>If you share any types of Company information, Privacy & Data etc. with anyone else, Legal action will be taken against you. This is the Right of the Company and your Full Money will also be stopped.</li>
                                                                            </ol>
                                                                        </div>

                                                                        <!-- ==================== DECLARATION ==================== -->
                                                                        <div class="mt-4 p-3 border rounded" style="background:#fff8e1; font-size:13px;">
                                                                            <h6 class="text-danger"><strong>DECLARATION</strong></h6>
                                                                            <p style="line-height:1.4;">
                                                                                I hereby agree with the above terms & conditions and are explained to me in my Mother Tongue Language for Marketing Services and I understood the same and signing this application with full consent and willingness. I shall abide by the Rules and Regulations of the Company.
                                                                            </p>
                                                                            <div class="form-check mt-3">
                                                                                <input class="form-check-input" type="checkbox" name="declaration_agreed" id="declaration_agreed" value="1" <?php echo (isset($_POST['declaration_agreed'])) ? 'checked' : ''; ?> required>
                                                                                <label class="form-check-label text-danger" for="declaration_agreed">
                                                                                    <strong>I have read and agree to the declaration above.</strong>
                                                                                </label>
                                                                            </div>
                                                                        </div>

                                                                        <!-- ==================== WITNESS & SIGNATURE ==================== -->
                                                                        <div class="row mt-4">
                                                                            <div class="col-md-6">
                                                                                <label><strong>Witness 1:</strong></label>
                                                                                <input type="text" name="witness1_name" class="form-control form-control-sm mb-2" placeholder="Witness 1 Name" value="<?php echo htmlspecialchars($_POST['witness1_name'] ?? ''); ?>">
                                                                                <input type="text" name="witness1_sign" class="form-control form-control-sm" placeholder="Signature" value="<?php echo htmlspecialchars($_POST['witness1_sign'] ?? ''); ?>">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label><strong>Witness 2:</strong></label>
                                                                                <input type="text" name="witness2_name" class="form-control form-control-sm mb-2" placeholder="Witness 2 Name" value="<?php echo htmlspecialchars($_POST['witness2_name'] ?? ''); ?>">
                                                                                <input type="text" name="witness2_sign" class="form-control form-control-sm" placeholder="Signature" value="<?php echo htmlspecialchars($_POST['witness2_sign'] ?? ''); ?>">
                                                                            </div>
                                                                        </div>

                                                                        <!-- ==================== APPLICANT SIGNATURE ==================== -->
                                                                        <div class="text-end mt-4">
                                                                            <label class="d-block"><strong>Signature of the Applicant</strong></label>
                                                                            <input type="text" name="applicant_sign" class="form-control form-control-sm d-inline-block" style="width:300px;" placeholder="Type your name as signature" value="<?php echo htmlspecialchars($_POST['applicant_sign'] ?? ''); ?>" required>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                            </div>

                                                            <div class="text-center mt-4">
                                                                <button type="submit" name="save_nominee" class="btn btn-danger btn-lg">
                                                                    Save Nominee & Declaration
                                                                </button>
                                                            </div>

                                                            <!-- SHOW JS ALERT -->
                                                            <?php echo $js_alert; ?>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'associate-footer.php'; ?>
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