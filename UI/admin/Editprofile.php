<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// Initialize variables
$member_data = null;
$bank_data = null;
$kyc_data = null;  // Added for KYC data
$search_error = '';

// Handle search request
if (isset($_POST['search'])) {
    $member_id = trim($_POST['member_id']);
    if (!empty($member_id)) {
        // Fetch from tbl_regist
        $query_regist = "
            SELECT 
                sponsor_id, mem_sid, s_name, gender, date_of_birth, m_email, m_num, 
                address, city, state_name, date_time
            FROM tbl_regist
            WHERE mem_sid = :sponsor_id
        ";
        $stmt_regist = $pdo->prepare($query_regist);
        $stmt_regist->execute(['sponsor_id' => $member_id]);
        $member_data = $stmt_regist->fetch(PDO::FETCH_ASSOC);

        // Fetch from tbl_bnk
        $query_bnk = "
            SELECT 
                s_name AS bank_holder_name, s_acc, s_bank, b_city, b_ifsc
            FROM tbl_bnk
            WHERE sponsor_id = :sponsor_id
        ";
        $stmt_bnk = $pdo->prepare($query_bnk);
        $stmt_bnk->execute(['sponsor_id' => $member_id]);
        $bank_data = $stmt_bnk->fetch(PDO::FETCH_ASSOC);

        // Fetch from tbl_kyc
        $query_kyc = "
            SELECT 
                address_proof_type, address_proof_file, 
                pan_card_no, pan_card_file, 
                bank_acc_no, bank_preview_file
            FROM tbl_kyc
            WHERE sponsor_id = :sponsor_id
        ";
        $stmt_kyc = $pdo->prepare($query_kyc);
        $stmt_kyc->execute(['sponsor_id' => $member_id]);
        $kyc_data = $stmt_kyc->fetch(PDO::FETCH_ASSOC);

        if (!$member_data) {
            $search_error = "No member found with Member ID: $member_id";
        }
    } else {
        $search_error = "Please enter a Member ID to search.";
    }
}

// Handle update request
if (isset($_POST['update'])) {
    // Existing fields
    $sponsor_id = $_POST['sponsor_id'];
    $s_name = $_POST['s_name'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $m_email = $_POST['m_email'];
    $m_num = $_POST['m_num'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state_name = $_POST['state_name'];
    $bank_holder_name = $_POST['bank_holder_name'];
    $s_acc = $_POST['s_acc'];
    $s_bank = $_POST['s_bank'];
    $b_city = $_POST['b_city'];
    $b_ifsc = $_POST['b_ifsc'];
    $pan_option = $_POST['pan_option'];
    $pan_number = $_POST['pan_number'];

    // KYC fields
    $address_proof_type = $_POST['address_proof_type'];
    $pan_card_no = $_POST['pan_card_no'];
    $bank_acc_no = $_POST['bank_acc_no'];

    // Update tbl_regist
    $update_regist = "
        UPDATE tbl_regist
        SET 
            s_name = :s_name,
            gender = :gender,
            date_of_birth = :date_of_birth,
            m_email = :m_email,
            m_num = :m_num,
            address = :address,
            city = :city,
            state_name = :state_name,
            pan_number = :pan_number
        WHERE mem_sid = :sponsor_id
    ";
    $stmt_regist = $pdo->prepare($update_regist);
    $stmt_regist->execute([
        'sponsor_id' => $sponsor_id,
        's_name' => $s_name,
        'gender' => $gender,
        'date_of_birth' => $date_of_birth,
        'm_email' => $m_email,
        'm_num' => $m_num,
        'address' => $address,
        'city' => $city,
        'state_name' => $state_name,
        'pan_number' => $pan_option === 'Yes' ? $pan_number : ''
    ]);

    // Update or Insert into tbl_bnk
    $check_bnk = $pdo->prepare("SELECT COUNT(*) FROM tbl_bnk WHERE sponsor_id = :sponsor_id");
    $check_bnk->execute(['sponsor_id' => $sponsor_id]);
    $bank_exists = $check_bnk->fetchColumn();

    if ($bank_exists) {
        $update_bnk = "
            UPDATE tbl_bnk
            SET 
                s_name = :bank_holder_name,
                s_acc = :s_acc,
                s_bank = :s_bank,
                b_city = :b_city,
                b_ifsc = :b_ifsc
            WHERE sponsor_id = :sponsor_id
        ";
        $stmt_bnk = $pdo->prepare($update_bnk);
        $stmt_bnk->execute([
            'sponsor_id' => $sponsor_id,
            'bank_holder_name' => $bank_holder_name,
            's_acc' => $s_acc,
            's_bank' => $s_bank,
            'b_city' => $b_city,
            'b_ifsc' => $b_ifsc
        ]);
    } else {
        $insert_bnk = "
            INSERT INTO tbl_bnk (sponsor_id, s_name, s_acc, s_bank, b_city, b_ifsc)
            VALUES (:sponsor_id, :bank_holder_name, :s_acc, :s_bank, :b_city, :b_ifsc)
        ";
        $stmt_bnk = $pdo->prepare($insert_bnk);
        $stmt_bnk->execute([
            'sponsor_id' => $sponsor_id,
            'bank_holder_name' => $bank_holder_name,
            's_acc' => $s_acc,
            's_bank' => $s_bank,
            'b_city' => $b_city,
            'b_ifsc' => $b_ifsc
        ]);
    }

    // Update or Insert into tbl_kyc
    $check_kyc = $pdo->prepare("SELECT COUNT(*) FROM tbl_kyc WHERE sponsor_id = :sponsor_id");
    $check_kyc->execute(['sponsor_id' => $sponsor_id]);
    $kyc_exists = $check_kyc->fetchColumn();

    if ($kyc_exists) {
        $update_kyc = "
            UPDATE tbl_kyc
            SET 
                address_proof_type = :address_proof_type,
                pan_card_no = :pan_card_no,
                bank_acc_no = :bank_acc_no
            WHERE sponsor_id = :sponsor_id
        ";
        $stmt_kyc = $pdo->prepare($update_kyc);
        $stmt_kyc->execute([
            'sponsor_id' => $sponsor_id,
            'address_proof_type' => $address_proof_type,
            'pan_card_no' => $pan_card_no,
            'bank_acc_no' => $bank_acc_no
        ]);
    } else {
        $insert_kyc = "
            INSERT INTO tbl_kyc (sponsor_id, address_proof_type, pan_card_no, bank_acc_no)
            VALUES (:sponsor_id, :address_proof_type, :pan_card_no, :bank_acc_no)
        ";
        $stmt_kyc = $pdo->prepare($insert_kyc);
        $stmt_kyc->execute([
            'sponsor_id' => $sponsor_id,
            'address_proof_type' => $address_proof_type,
            'pan_card_no' => $pan_card_no,
            'bank_acc_no' => $bank_acc_no
        ]);
    }

    echo "<script>alert('Member data including KYC updated successfully!');</script>";
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
                    <div class="content-wrapper">
                        <div class="col-md-12 stretch-card">
                            <div class="card">


                                <div class="container" style="padding-top: 50px; padding-bottom: 50px;">

                                    <div class="col-md-12">
                                        <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h2>Edit Member Profile</h2>
                                            <hr>



                                            <div id="">

                                                <hr>
                                                <div class="box-contant" style="padding: 10px 0px;">
                                                    <form method="POST" action="">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>Member ID</b>
                                                                <input name="member_id" type="text" id="member_id" class="form-control" value="<?php echo isset($_POST['member_id']) ? htmlspecialchars($_POST['member_id']) : ''; ?>">
                                                                <span id="member_id_error" style="color:#FF3300; font-weight:bold; display:<?php echo $search_error ? 'block' : 'none'; ?>;">
                                                                    <?php echo $search_error; ?>
                                                                </span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <br>
                                                                <input type="submit" name="search" value="Search" id="search_btn" class="btn btn-success">
                                                            </div>
                                                            <div class="col-md-4"></div>
                                                        </div>
                                                    </form>

                                                    <!-- Display/Update Form -->
                                                    <?php if ($member_data): ?>
                                                        <form method="POST" action="">
                                                            <input type="hidden" name="sponsor_id" value="<?php echo htmlspecialchars($member_data['sponsor_id']); ?>">
                                                            <div class="row mt-4">
                                                                <div class="col-md-4">
                                                                    <b>Sponsor ID<span style="color: red">*</span></b>
                                                                    <span id="display_member_id" class="form-control" style="font-weight:bold;" data-original-value="<?php echo htmlspecialchars($member_data['sponsor_id']); ?>">
                                                                        <?php echo htmlspecialchars($member_data['sponsor_id']); ?>
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Sponsor Name<span style="color: red">*</span></b>
                                                                    <input name="s_name_display" type="text" id="s_name" class="form-control" value="<?php echo htmlspecialchars($member_data['s_name']); ?>" readonly>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Joining Date<span style="color: red">*</span></b>
                                                                    <span id="joining_date" class="form-control" data-original-value="<?php echo htmlspecialchars($member_data['date_time']); ?>">
                                                                        <?php echo htmlspecialchars($member_data['date_time']); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <b>Gender<span style="color: red">*</span></b>
                                                                    <table class="form-control">
                                                                        <tr>
                                                                            <td>
                                                                                <input id="gender_male" type="radio" name="gender" value="Male" <?php echo ($member_data['gender'] === 'Male') ? 'checked' : ''; ?>>
                                                                                <label for="gender_male">Male</label>
                                                                            </td>
                                                                            <td>
                                                                                <input id="gender_female" type="radio" name="gender" value="Female" <?php echo ($member_data['gender'] === 'Female') ? 'checked' : ''; ?>>
                                                                                <label for="gender_female">Female</label>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-12">&nbsp;</div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <b>Address<span style="color: red">*</span></b>
                                                                    <textarea name="address" rows="2" cols="20" id="address" class="form-control"><?php echo htmlspecialchars($member_data['address']); ?></textarea>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>City<span style="color: red">*</span></b>
                                                                    <input name="city" type="text" id="city" class="form-control" value="<?php echo htmlspecialchars($member_data['city']); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>State<span style="color: red">*</span></b>
                                                                    <input name="state_name" type="text" id="state_name" class="form-control" value="<?php echo htmlspecialchars($member_data['state_name']); ?>">
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-12">&nbsp;</div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <b>Email</b>
                                                                    <input name="m_email" type="text" id="m_email" class="form-control" value="<?php echo htmlspecialchars($member_data['m_email']); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Mobile<span style="color: red">*</span></b>
                                                                    <input name="m_num" type="text" id="m_num" class="form-control" value="<?php echo htmlspecialchars($member_data['m_num']); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Date of Birth<span style="color: red">*</span></b>
                                                                    <input name="date_of_birth" type="text" id="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($member_data['date_of_birth']); ?>">
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-12">&nbsp;</div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <b>Account Holder Name<span style="color: red">*</span></b>
                                                                    <input name="bank_holder_name" type="text" id="bank_holder_name" class="form-control" value="<?php echo htmlspecialchars($bank_data['bank_holder_name'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Bank A/c No<span style="color: red">*</span></b>
                                                                    <input name="s_acc" type="text" id="s_acc" class="form-control" value="<?php echo htmlspecialchars($bank_data['s_acc'] ?? ''); ?>">
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-12">&nbsp;</div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <b>Bank Name<span style="color: red">*</span></b>
                                                                    <input name="s_bank" type="text" id="s_bank" class="form-control" value="<?php echo htmlspecialchars($bank_data['s_bank'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Branch<span style="color: red">*</span></b>
                                                                    <input name="b_city" type="text" id="b_city" class="form-control" value="<?php echo htmlspecialchars($bank_data['b_city'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Bank IFSC Code<span style="color: red">*</span></b>
                                                                    <input name="b_ifsc" type="text" id="b_ifsc" class="form-control" value="<?php echo htmlspecialchars($bank_data['b_ifsc'] ?? ''); ?>">
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-12">&nbsp;</div>
                                                            </div>

                                                            <!-- KYC Fields -->
                                                            <div class="row mt-4">
                                                                <h4>KYC Details</h4>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <b>Address Proof Type<span style="color: red">*</span></b>
                                                                    <select name="address_proof_type" class="form-control" required>
                                                                        <option value="">----Select----</option>
                                                                        <option value="Aadhar Card" <?php echo ($kyc_data['address_proof_type'] ?? '') === 'Aadhar Card' ? 'selected' : ''; ?>>Aadhar Card</option>
                                                                        <option value="Voter Id" <?php echo ($kyc_data['address_proof_type'] ?? '') === 'Voter Id' ? 'selected' : ''; ?>>Voter Id</option>
                                                                        <option value="Driving License" <?php echo ($kyc_data['address_proof_type'] ?? '') === 'Driving License' ? 'selected' : ''; ?>>Driving License</option>
                                                                        <option value="Passport" <?php echo ($kyc_data['address_proof_type'] ?? '') === 'Passport' ? 'selected' : ''; ?>>Passport</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Address Proof Document</b>
                                                                    <?php if (!empty($kyc_data['address_proof_file'])): ?>
                                                                        <a href="../admin/member_document/<?php echo htmlspecialchars($kyc_data['address_proof_file']); ?>"
                                                                            target="_blank">View Document</a>
                                                                    <?php else: ?>
                                                                        <span>No document uploaded</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <b>PAN Card Number<span style="color: red">*</span></b>
                                                                    <input name="pan_card_no" type="text" class="form-control"
                                                                        value="<?php echo htmlspecialchars($kyc_data['pan_card_no'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>PAN Card Document</b>
                                                                    <?php if (!empty($kyc_data['pan_card_file'])): ?>
                                                                        <a href="../admin/member_document/<?php echo htmlspecialchars($kyc_data['pan_card_file']); ?>"
                                                                            target="_blank">View Document</a>
                                                                    <?php else: ?>
                                                                        <span>No document uploaded</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-2">
                                                                <div class="col-md-4">
                                                                    <b>Bank Account Number<span style="color: red">*</span></b>
                                                                    <input name="bank_acc_no" type="text" class="form-control"
                                                                        value="<?php echo htmlspecialchars($kyc_data['bank_acc_no'] ?? ''); ?>">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <b>Bank Document</b>
                                                                    <?php if (!empty($kyc_data['bank_preview_file'])): ?>
                                                                        <a href="../admin/member_document/<?php echo htmlspecialchars($kyc_data['bank_preview_file']); ?>"
                                                                            target="_blank">View Document</a>
                                                                    <?php else: ?>
                                                                        <span>No document uploaded</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <input type="submit" name="update" value="Update" id="update_btn" class="btn btn-success">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php include 'adminfooter.php'; ?>
                        </div>
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
                    document.addEventListener('DOMContentLoaded', function() {
                        // Prevent interaction with display fields
                        const sponsorId = document.getElementById('display_member_id');
                        const sponsorName = document.getElementById('s_name');
                        const joiningDate = document.getElementById('joining_date');

                        // Disable interactions for sponsor_id and joining_date spans
                        [sponsorId, joiningDate].forEach(element => {
                            element.style.pointerEvents = 'none'; // Disable clicks, edits
                            element.style.userSelect = 'none'; // Prevent text selection
                        });

                        // Ensure sponsor_name input is readonly and prevent copy-paste
                        sponsorName.setAttribute('readonly', 'readonly');
                        sponsorName.addEventListener('paste', (e) => e.preventDefault());
                        sponsorName.addEventListener('keydown', (e) => {
                            e.preventDefault(); // Prevent any key input
                        });

                        // Form submission validation for displayed values
                        const form = document.querySelector('form');
                        form.addEventListener('submit', function(e) {
                            // Get original values from data attributes
                            const originalSponsorId = sponsorId.getAttribute('data-original-value');
                            const originalJoiningDate = joiningDate.getAttribute('data-original-value');
                            const originalSponsorName = sponsorName.value;

                            // Compare with current displayed values
                            if (sponsorId.textContent.trim() !== originalSponsorId ||
                                joiningDate.textContent.trim() !== originalJoiningDate ||
                                sponsorName.value !== originalSponsorName) {
                                e.preventDefault();
                                alert('Error: Form data has been tampered with. Please reload the page and try again.');
                            }
                        });
                    });
                </script>

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