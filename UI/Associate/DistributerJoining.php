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

/* --------------------------------------------------------------
   1. EDIT MODE – Check if ?edit= matches logged-in $memberid
   -------------------------------------------------------------- */
$memberid = $_SESSION['sponsor_id'];
$membername = $_SESSION['sponsor_name'];

$edit_mode = false;
$edit_data = [];

if (isset($_GET['edit'])) {
    $requested_mem_sid = trim($_GET['edit']);

    // SECURITY: Only allow editing OWN profile
    if ($requested_mem_sid !== $memberid) {
        header('Location: DistributerJoining.php');
        exit;
    }

    // Fetch own data from tbl_regist + tbl_kyc
    $stmt = $pdo->prepare("
        SELECT r.*, k.address_proof_file
        FROM tbl_regist AS r
        LEFT JOIN tbl_kyc AS k
            ON r.mem_sid = k.sponsor_id
        WHERE r.mem_sid = ?
    ");
    $stmt->execute([$memberid]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($edit_data) {
        $edit_mode = true;
    } else {
        header('Location: DistributerJoining.php');
        exit;
    }
}


if (isset($_POST['btnsubmit'])) {

    // Common fields
    $mem_name       = trim($_POST['mem_name']);        // Full Name
    $so_do_name     = trim($_POST['so_do_name']);      // S/O, D/O, Spouse
    $parents_name   = trim($_POST['parents_name']);    // Mother's Name
    $designation    = $_POST['designation'];
    $gender         = $_POST['gender'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $nationality    = trim($_POST['nationality']);
    $dob            = $_POST['Dob'];
    $date_of_anniversary = $_POST['date_of_anniversary'] ?? null;
    $mem_mob        = trim($_POST['mem_mob']);
    $alt_no         = trim($_POST['alt_no']);
    $mem_email      = trim($_POST['mem_email']);
    $pan_number     = trim($_POST['pan_number']);
    $aadhar         = trim($_POST['aadhar']);
    $native_place   = trim($_POST['native_place']);
    $address        = trim($_POST['Address']);
    $city           = trim($_POST['City']);
    $pincode        = trim($_POST['pincode']);
    $datetime       = date('Y-m-d H:i:s');

    // File Upload
    $upload_dir = '../admin/member_document/';
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

    // ==============================================
    // 1. EDIT MODE (Update own profile)
    // ==============================================
    if (isset($_POST['mem_sid']) && !empty($_POST['mem_sid'])) {
        $mem_sid = $_POST['mem_sid'];

        // Security: Must match logged-in user
        if ($mem_sid !== $memberid) {
            die("Unauthorized access.");
        }

        // Update tbl_regist (skip: sponsor_id, s_name, mem_sid, m_name? → NO, m_name IS full name → UPDATE IT)
        $sql = "UPDATE tbl_regist SET 
                    m_name = ?,           -- Full Name
                    parents_name = ?,     -- Mother's Name
                    so_do_name = ?,       -- S/O, D/O, Spouse
                    gender = ?, 
                    designation = ?, 
                    date_of_birth = ?, 
                    date_of_anniversary = ?,
                    m_num = ?, 
                    m_email = ?, 
                    address = ?, 
                    city = ?, 
                    aadhar_number = ?, 
                    pan_number = ?, 
                    alt_no = ?, 
                    native_place = ?, 
                    pincode = ?, 
                    marital_status = ?, 
                    nationality = ?
                WHERE mem_sid = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $mem_name,
            $parents_name,
            $so_do_name,
            $gender,
            $designation,
            $dob,
            $date_of_anniversary,
            $mem_mob,
            $mem_email,
            $address,
            $city,
            $aadhar,
            $pan_number,
            $alt_no,
            $native_place,
            $pincode,
            $marital_status,
            $nationality,
            $mem_sid
        ]);

        // Update KYC if new files uploaded
        if (!empty($files)) {
            $all_files = implode(',', $files);

            // Fetch existing files to unlink them
            $kyc_check = $pdo->prepare("SELECT address_proof_file FROM tbl_kyc WHERE sponsor_id = ?");
            $kyc_check->execute([$mem_sid]);
            $existing_kyc = $kyc_check->fetch(PDO::FETCH_ASSOC);

            // If files exist, unlink them
            if ($existing_kyc && !empty($existing_kyc['address_proof_file'])) {
                $old_files = explode(',', $existing_kyc['address_proof_file']);
                foreach ($old_files as $old_file) {
                    $old_file = trim($old_file);
                    $file_path = "../admin/member_document/" . $old_file;
                    if (file_exists($file_path)) {
                        unlink($file_path); // Delete the old file
                    }
                }
            }

            // Update or Insert new files
            if ($kyc_check->rowCount() > 0) {
                $kyc = $pdo->prepare("UPDATE tbl_kyc SET address_proof_file = ? WHERE sponsor_id = ?");
                $kyc->execute([$all_files, $mem_sid]);
            } else {
                $kyc = $pdo->prepare("INSERT INTO tbl_kyc (sponsor_id, address_proof_file) VALUES (?, ?)");
                $kyc->execute([$mem_sid, $all_files]);
            }
        }

        echo "<script>
    alert('Profile updated successfully!');
    location='DistributerJoining.php';
</script>";
        exit;
    }

    // ==============================================
    // 2. ADD NEW MEMBER (Insert)
    // ==============================================
    else {
        $m_id         = trim($_POST['m_id']);
        $sponsor_id   = trim($_POST['sponsor_id']);
        $sponsor_name = trim($_POST['sponsor_name']);
        $mem_pass     = $_POST['mem_pass'];

        if (empty($files)) {
            die("Upload at least one proof.");
        }
        $all_files = implode(',', $files);

        // Check duplicate mem_sid
        $chk = $pdo->prepare("SELECT id FROM tbl_regist WHERE mem_sid = ?");
        $chk->execute([$m_id]);
        if ($chk->rowCount() > 0) {
            die("Member ID already exists!");
        }

        // Insert KYC
        $kyc = $pdo->prepare("INSERT INTO tbl_kyc (sponsor_id, address_proof_file) VALUES (?, ?)");
        $kyc->execute([$m_id, $all_files]);

        // Insert into tbl_regist
        $sql = "INSERT INTO tbl_regist 
                (sponsor_id, s_name, m_name, parents_name, so_do_name, gender, designation, 
                 date_of_birth, date_of_anniversary, m_num, m_email, m_password, date_time, address, city, 
                 aadhar_number, pan_number, mem_sid, alt_no, native_place, pincode, 
                 marital_status, nationality)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
            $nationality
        ]);

        // Insert into tbl_hire
        $hire = $pdo->prepare("INSERT INTO tbl_hire (sponsor_id, s_name, sponsor_pass) VALUES (?, ?, ?)");
        $hire->execute([$m_id, $mem_name, $mem_pass]);

        // Success
        $last = $pdo->lastInsertId();
        $row = $pdo->query("SELECT sponsor_id, sponsor_pass FROM tbl_hire WHERE id = $last")->fetch();

        echo "<script>
            if(confirm('Success!\\nID: {$row['sponsor_id']}\\nPass: {$row['sponsor_pass']}')){
                location='DistributerJoining.php';
            }
        </script>";
        exit;
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
                                            <form method="post" action="" id="form1" enctype="multipart/form-data">
                                                <div style="background:#fff;padding:30px;border:2px solid #fff;box-shadow:1px 3px 12px 4px #988f8f;">
                                                    <h2><?= $edit_mode ? 'Edit Member' : 'Add New Member' ?></h2>
                                                    <hr>

                                                    <!-- EDIT BUTTON -->
                                                    <div class="mt-3 text-center">
                                                        <a href="DistributerJoining.php?edit=<?= urlencode($memberid) ?>"
                                                            class="btn btn-warning btn-sm">Edit</a>
                                                    </div>

                                                    <!-- Hidden mem_sid for update -->
                                                    <?php if ($edit_mode): ?>
                                                        <input type="hidden" name="mem_sid" value="<?= htmlspecialchars($memberid) ?>">
                                                    <?php endif; ?>

                                                    <!-- Sponsor Details → Now shows LOGGED-IN MEMBER's own ID & Name -->
                                                    <div class="row">
                                                        <legend>Member Details</legend>
                                                        <div class="col-md-4">
                                                            <b>Member ID</b>
                                                            <input name="sponsor_id" type="text" value="<?= htmlspecialchars($memberid) ?>" readonly class="form-control">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Member Name</b>
                                                            <input name="sponsor_name" type="text" value="<?= htmlspecialchars($membername) ?>" readonly class="form-control">
                                                        </div>
                                                    </div>

                                                    <!-- Personal Details -->
                                                    <div class="row mt-3">
                                                        <legend>Personal Details</legend>

                                                        <!-- Name -->
                                                        <div class="col-md-4">
                                                            <b>Name in Full</b>
                                                            <input name="mem_name" type="text" class="form-control" placeholder="Enter Full Name" required
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['m_name']) : '' ?>">
                                                        </div>

                                                        <!-- Parents -->
                                                        <div class="col-md-4">
                                                            <b>S/O, D/O, Spouse's Name</b>
                                                            <input name="so_do_name" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['so_do_name']) : '' ?>">
                                                        </div>

                                                        <!-- Mother's Name -->
                                                        <div class="col-md-4">
                                                            <b>Mother's Name</b>
                                                            <input name="parents_name" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['parents_name']) : '' ?>">
                                                        </div>

                                                        <!-- Row 2 – Designation (same logic you already have) -->
                                                        <?php
                                                        /* ---- designation logic (unchanged) ---- */
                                                        if (!$memberid) {
                                                            die("Error: Member not logged in.");
                                                        }
                                                        $stmt = $pdo->prepare("SELECT designation FROM tbl_regist WHERE mem_sid = ?");
                                                        $stmt->execute([$memberid]);
                                                        $sponsor_member = $stmt->fetch(PDO::FETCH_ASSOC);
                                                        if (!$sponsor_member) {
                                                            die("Sponsor member not found.");
                                                        }
                                                        $sponsor_designation = trim($sponsor_member['designation']);

                                                        $designation_to_level = [
                                                            'Sales Executive (S.E.)'                => 1,
                                                            'Senior Sales Executive (S.S.E.)'       => 2,
                                                            'Assistant Marketing Officer (A.M.O.)'  => 3,
                                                            'Marketing Officer (M.O.)'              => 4,
                                                            'Assistant Marketing Manager (A.M.M.)' => 5,
                                                            'Marketing Manager (M.M.)'             => 6,
                                                            'Chief Marketing Manager (C.M.M.)'     => 7,
                                                            'Assistant General Manager (A.G.M.)'   => 8,
                                                            'Deputy General Manager (D.G.M.)'      => 9,
                                                            'General Manager (G.M.)'               => 10,
                                                            'Marketing Director (M.D.)'            => 11,
                                                            'Founder Member (F.M.)'                => 12,
                                                        ];
                                                        $sponsor_level = $designation_to_level[$sponsor_designation] ?? 0;
                                                        if ($sponsor_level === 0) {
                                                            die("Invalid sponsor designation.");
                                                        }

                                                        $all_designations = [
                                                            1  => 'Sales Executive (S.E.)',
                                                            2  => 'Senior Sales Executive (S.S.E.)',
                                                            3  => 'Assistant Marketing Officer (A.M.O.)',
                                                            4  => 'Marketing Officer (M.O.)',
                                                            5  => 'Assistant Marketing Manager (A.M.M.)',
                                                            6  => 'Marketing Manager (M.M.)',
                                                            7  => 'Chief Marketing Manager (C.M.M.)',
                                                            8  => 'Assistant General Manager (A.G.M.)',
                                                            9  => 'Deputy General Manager (D.G.M.)',
                                                            10 => 'General Manager (G.M.)',
                                                            11 => 'Marketing Director (M.D.)',
                                                            12 => 'Founder Member (F.M.)',
                                                        ];
                                                        ?>
                                                        <div class="col-md-4">
                                                            <b>Designation</b>
                                                            <select name="designation" class="form-control" required>
                                                                <option value="">-- Select Designation --</option>
                                                                <?php for ($lvl = 1; $lvl < $sponsor_level; $lvl++):
                                                                    $desig = $all_designations[$lvl] ?? '';
                                                                    $selected = ($edit_mode && $edit_data['designation'] == $desig) ? 'selected' : '';
                                                                ?>
                                                                    <option value="<?= htmlspecialchars($desig) ?>" <?= $selected ?>><?= htmlspecialchars($desig) ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                            <?php if ($sponsor_level <= 1): ?>
                                                                <small class="text-muted">No lower designations available for your level.</small>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Gender -->
                                                        <div class="col-md-4">
                                                            <b>Gender</b><br>
                                                            <?php $g = $edit_mode ? $edit_data['gender'] : ''; ?>
                                                            <label><input type="radio" name="gender" value="Male" <?= $g == 'Male' ? 'checked' : '' ?>> Male</label>&nbsp;&nbsp;
                                                            <label><input type="radio" name="gender" value="Female" <?= $g == 'Female' ? 'checked' : '' ?>> Female</label>&nbsp;&nbsp;
                                                            <label><input type="radio" name="gender" value="Others" <?= $g == 'Others' ? 'checked' : '' ?>> Others</label>
                                                        </div>

                                                        <!-- Marital Status -->
                                                        <div class="col-md-4">
                                                            <b>Marital Status</b><br>
                                                            <?php $ms = $edit_mode ? $edit_data['marital_status'] : ''; ?>
                                                            <label><input type="radio" name="marital_status" value="Married" <?= $ms == 'Married' ? 'checked' : '' ?>> Married</label>&nbsp;&nbsp;
                                                            <label><input type="radio" name="marital_status" value="Unmarried" <?= $ms == 'Unmarried' ? 'checked' : '' ?>> Unmarried</label>
                                                        </div>

                                                        <!-- Row 3 -->
                                                        <div class="col-md-4">
                                                            <b>Nationality</b>
                                                            <input name="nationality" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['nationality']) : 'Indian' ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Date of Birth</b>
                                                            <input name="Dob" type="date" class="form-control"
                                                                value="<?= $edit_mode ? $edit_data['date_of_birth'] : '' ?>">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <b>Date of Anniversary</b>
                                                            <input name="date_of_anniversary" type="date" class="form-control">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <b>Contact No.</b>
                                                            <input name="mem_mob" type="text" class="form-control" required
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['m_num']) : '' ?>">
                                                        </div>

                                                        <!-- Row 4 -->
                                                        <div class="col-md-4">
                                                            <b>Alt. No.</b>
                                                            <input name="alt_no" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['alt_no']) : '' ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Email ID</b>
                                                            <input name="mem_email" type="email" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['m_email']) : '' ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>PAN No.</b>
                                                            <input name="pan_number" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['pan_number']) : '' ?>">
                                                        </div>

                                                        <!-- Row 5 -->
                                                        <div class="col-md-4">
                                                            <b>Aadhar Card No.</b>
                                                            <input name="aadhar" type="text" class="form-control" required
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['aadhar_number']) : '' ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Native Place</b>
                                                            <input name="native_place" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['native_place']) : '' ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Communication Address</b>
                                                            <textarea name="Address" rows="2" class="form-control"><?= $edit_mode ? htmlspecialchars($edit_data['address']) : '' ?></textarea>
                                                        </div>

                                                        <!-- Row 6 -->
                                                        <div class="col-md-4">
                                                            <b>City/Town/Village</b>
                                                            <input name="City" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['city']) : '' ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <b>Pin Code</b>
                                                            <input name="pincode" type="text" class="form-control"
                                                                value="<?= $edit_mode ? htmlspecialchars($edit_data['pincode']) : '' ?>">
                                                        </div>
                                                        <div class="col-md-4">&nbsp;</div>

                                                        <!-- Member ID & Password → Hide in Edit Mode -->
                                                        <?php if (!$edit_mode): ?>
                                                            <div class="col-md-4">
                                                                <b>Member ID</b>
                                                                <input name="m_id" type="text" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <b>Password</b>
                                                                <input name="mem_pass" type="password" class="form-control" required>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- File Upload -->
                                                        <div class="col-md-4">
                                                            <b>Upload Aadhar / Proof</b>
                                                            <input name="address_proof_file[]" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" multiple>

                                                            <?php if ($edit_mode && !empty($edit_data['address_proof_file'])): ?>
                                                                <div class="mt-2">
                                                                    <b>Existing Files:</b><br>
                                                                    <?php
                                                                    $files = explode(',', $edit_data['address_proof_file']);
                                                                    foreach ($files as $file):
                                                                        $file = trim($file);
                                                                        if ($file !== ''):
                                                                            $filepath = "../admin/member_document/" . $file;
                                                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                                            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                                                                echo "<a href='../member_document/{$filepath}' target='_blank'><img src='{$filepath}' alt='Proof' width='100' style='margin:5px;border:1px solid #ccc;border-radius:5px;'></a>";
                                                                            } else {
                                                                                echo "<a href='{$filepath}' target='_blank'>{$file}</a><br>";
                                                                            }
                                                                        endif;
                                                                    endforeach;
                                                                    ?>
                                                                </div>
                                                                <small class="text-muted">Leave blank to keep existing files</small>
                                                            <?php endif; ?>
                                                        </div>


                                                        <!-- Hidden DateTime -->
                                                        <div style="display:none">
                                                            <input type="text" name="d_time" value="<?= date('Y-m-d H:i:s') ?>">
                                                        </div>

                                                        <!-- Submit Button -->
                                                        <div class="row mt-4">
                                                            <div class="col-md-12 text-center">
                                                                <button type="submit" name="btnsubmit"
                                                                    class="btn <?= $edit_mode ? 'btn-success' : 'btn-info' ?> btn-cons">
                                                                    <?= $edit_mode ? 'Update Profile' : 'Submit' ?>
                                                                </button>
                                                                <?php if ($edit_mode): ?>
                                                                    <a href="DistributerJoining.php" class="btn btn-secondary btn-cons ms-2">Cancel</a>
                                                                <?php endif; ?>
                                                            </div>
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