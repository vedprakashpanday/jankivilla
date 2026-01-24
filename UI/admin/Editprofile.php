<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);   



session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle update request
if (isset($_POST['update'])) {
// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "<pre>";
    // var_dump($_POST);    
    // echo "</pre>";
    // exit();

    // --- SAFE INPUT FETCH (NO warnings, no undefined index) ---
     $member_id        = $_POST['member_id']        ?? ''; 
     
     
//  $m_id      = $_POST['staff_id'];
$proofType = $_POST['proofType'] ?? null;

/* -----------------------------
   1️⃣ Get old file name
--------------------------------*/
$old = $pdo->prepare("SELECT address_proof_file FROM tbl_kyc WHERE sponsor_id = ?");
$old->execute([$member_id]);
$oldFile = $old->fetchColumn();   // may be null or comma string

/* -----------------------------
   2️⃣ File upload config
--------------------------------*/
$upload_dir = 'member_document/';
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
$files = [];

/* -----------------------------
   3️⃣ Check if NEW file uploaded
--------------------------------*/
if (
    isset($_FILES['address_proof_file']) &&
    !empty($_FILES['address_proof_file']['name'][0])
) {

    // 🔥 NEW FILE UPLOADED → delete old files
    if ($oldFile) {
        foreach (explode(',', $oldFile) as $f) {
            $path = $upload_dir . $f;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    // Upload new files
    foreach ($_FILES['address_proof_file']['name'] as $k => $n) {

        if ($_FILES['address_proof_file']['error'][$k] !== UPLOAD_ERR_OK) {
            continue;
        }

        $tmp  = $_FILES['address_proof_file']['tmp_name'][$k];
        $type = mime_content_type($tmp);

        if (in_array($type, $allowed)) {
            $ext   = pathinfo($n, PATHINFO_EXTENSION);
            $fname = time() . rand(1000, 9999) . '.' . $ext;

            if (move_uploaded_file($tmp, $upload_dir . $fname)) {
                $files[] = $fname;
            }
        }
    }

    // Safety check
    if (empty($files)) {
        die("File upload failed");
    }

    $all_files = implode(',', $files);

} else {
    // 🔥 NO NEW FILE → keep old file
    $all_files = $oldFile;
}


$kyc = $pdo->prepare("
    INSERT INTO tbl_kyc (sponsor_id, address_proof_file)
    VALUES (:sid, :file)
    ON DUPLICATE KEY UPDATE
        address_proof_file = VALUES(address_proof_file),
        updated_at = NOW()
");

$kyc->execute([
    'sid'  => $m_id,
    'file' => $all_files
]);

 $sql = "
INSERT INTO tbl_bank_details 
(member_id, account_name, account_no, bank_name, branch, ifsc_code, created_at,updated_at)
VALUES 
(:id, :acc_name, :acc_no, :b_name, :br, :ifsc, NOW(),NOW())
ON DUPLICATE KEY UPDATE
    account_name = VALUES(account_name),
    account_no   = VALUES(account_no),
    bank_name    = VALUES(bank_name),
    branch       = VALUES(branch),
    ifsc_code    = VALUES(ifsc_code),
    updated_at   = NOW()
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'id'       =>  $member_id,
    'acc_name' => $_POST['ach_name'] ?? null,
    'acc_no'   => $_POST['ba_number'] ?? null,
    'b_name'   => $_POST['b_name'] ?? null,
    'br'       => $_POST['br_name'] ?? null,
    'ifsc'     => $_POST['ifsc'] ?? null
]);





    $m_name            = $_POST['member_name']      ?? '';
    $sponsor_id        = $_POST['sponsor_id']        ?? '';   
    $s_name            = $_POST['sponsor_name']      ?? '';
     $doj            = $_POST['doj']      ?? ''; 
    $spouse            = $_POST['spouse']          ?? '';
    $parents           = $_POST['parents']         ?? '';
    $gender            = $_POST['gender']            ?? '';
    $date_of_birth     = $_POST['dob']     ?? '';
    $m_email           = $_POST['email']           ?? '';
    $m_num             = $_POST['mobile']             ?? '';
    $address           = $_POST['address']           ?? '';
    $city              = $_POST['city']              ?? '';
    //$state_name        = $_POST['state_name']        ?? '';
    $aadhar            = $_POST['aadhar']            ?? '';
    $pan        = $_POST['pan']        ?? '';
    $designation       = $_POST['designation']       ?? '';
    $address_proof_type= $_POST['proofType']?? '';
    $martial        = $_POST['marital']        ?? '';
    // $anniversary       = $_POST['anniversary'] ?? null;

    $anniversary = !empty($_POST['anniversary'])
        ? $_POST['anniversary']
        : null;
    $alternate      = $_POST['alternate']        ?? '';
    $native       = $_POST['native']        ?? '';
    $pin       = $_POST['pincode']        ?? '';
    $password       = $_POST['password']        ?? '';
    $nationality       = $_POST['nationality']        ?? '';
  
    
 

    // --- UPDATE QUERY WITH JSON APPEND ---
  $update_regist = "
    UPDATE tbl_regist
    SET 

    mem_sid = :m_id,
    m_name = :m_name,
    sponsor_id = :s_id,
    s_name = :s_name,
    date_time = :doj,
    so_do_name = :so_do_name,
    parents_name = :parents_name,
    gender = :gender,
    designation = :designation1,
    date_of_birth = :dob,
    date_of_anniversary = :anniversary_date,
    m_num = :contact_no,
    m_email = :email,
    m_password = :password,
    
    address = :communication_address,
    city = :city,
    aadhar_number = :aadhar_no,
    pan_number = :pan_no,   
    alt_no = :alternate_no,
    native_place = :native_place,
    pincode = :pin_code,
    marital_status = :marital_status,
    nationality = :nationality,    
    proof_type=:proof_type,

    

        designations = JSON_ARRAY_APPEND(
            COALESCE(designations, JSON_ARRAY()),
            '$',
            JSON_OBJECT(
                'designation', :designation123,
                'date', :designation_date
            )
        )

    WHERE mem_sid = :member_id
";

    $stmt_regist = $pdo->prepare($update_regist);

   $params = [
    'm_id'           => $member_id,
    'm_name'          => $m_name,
    's_id'           => $sponsor_id,
    's_name'          => $s_name,
    'doj'             => $doj,
    'so_do_name'     => $spouse,
    'parents_name'   => $parents,    
    'gender'          => $gender,
    'designation1'    => $designation,
    'dob'             => $date_of_birth,
    'anniversary_date'=> $anniversary??null,
    'contact_no'      => $m_num,
    'email'           => $m_email,
    'password'        => $password,
    // 'create_time'     => date('Y-m-d H:i:s'),
    'communication_address' => $address,
    'city'            => $city,
    'aadhar_no'       => $aadhar,
    'pan_no'          => $pan,
    'member_id'       => $member_id,
    'alternate_no'    => $alternate,
    'native_place'    => $native,
    'pin_code'        => $pin,
    'marital_status'  => $martial,
    'nationality'       => $nationality,
    'proof_type'      => $address_proof_type,
    'designation123'   => $designation,
    'designation_date' => date('Y-m-d')
];

try {
    $stmt_regist = $pdo->prepare($update_regist);
    $stmt_regist->execute($params);

     echo "<script>alert('Profile Updated Successfully!');</script>";
    header("Location: Editprofile.php");
     exit;

} catch (PDOException $e) {

    echo "<pre>";
    echo "SQL ERROR: " . $e->getMessage() . "\n\n";
    print_r($stmt_regist->errorInfo());
    echo "</pre>";
    exit;
}


  

//     echo "<script>alert('Member data including KYC updated successfully!');</script>";
//    header("Location: Editprofile.php");
// exit();
}

if(isset($_POST['delete']) && $_POST['del_id'] != '') {
    $del_id = $_POST['del_id'] ?? '';

    $delete_stmt = $pdo->prepare("DELETE FROM tbl_regist WHERE mem_sid = :member_id");
    $delete_stmt->bindParam(':member_id', $del_id);

    try {
        $delete_stmt->execute();
        echo "<script>alert('Record deleted successfully!');</script>";
        header("Location: Editprofile.php");
        exit;
    } catch (PDOException $e) {
        echo "<pre>";
        echo "SQL ERROR: " . $e->getMessage() . "\n\n";
        print_r($delete_stmt->errorInfo());
        echo "</pre>";
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">


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
                                                <div class="box-contant" style="padding: 10px 0px;overflow-x:auto;">
                                                   <table id="staffTable" class="display table table-bordered table-striped" style="width:100%;">
<thead>
<tr>
    <th>Member ID</th>
    <th>Member Name</th>
    <th>Sponsor ID</th>
    <th>Sponsor Name</th>
    <th>Date Of Joining</th>
    <th>Spouse</th>
    <th>Parents Name</th>
    <th>Designation</th>
    <th>Bank Name</th>
    <th>Branch Name</th>
    <th>IFSC Code</th>
    <th>Bank Account Number</th>
    <th>Bank Account Holder Name</th>
    <th>Gender</th>
    <th>Marital Status</th>
    <th>Nationality</th>
    <th>DOB</th>
    <th>Date of Anniversary</th>
    <th>Mobile</th>
    <th>Alternate</th>
    <th>Email</th>
     <th>PAN</th>
    <th>Aadhar</th>
    <th>Native Place</th>
    <th>Address</th>
    <th>City</th>
    <th>Pincode</th>
    <th>Password</th>
    <th>Proof Type</th>
    <th>Proof Type image</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
    <?php 
        $stmt = $pdo->prepare("  SELECT
        tr.mem_sid           AS member_id,
        tr.m_name            AS full_name,
        tr.sponsor_id,
        tr.s_name            AS sponsor_name,
        tr.date_time,
        tr.so_do_name        AS father_spouse_name,
        tr.parents_name      AS mother_name,
        tr.designation,
        tr.gender,
        tr.marital_status,
        tr.nationality,
        tr.date_of_birth     AS dob,
        tr.date_of_anniversary AS anniversary_date,
        tr.m_num             AS contact_no,
        tr.alt_no            AS alternate_no,
        tr.m_email           AS email,
        tr.pan_number        AS pan_no,
        tr.aadhar_number     AS aadhar_no,
        tr.native_place,
        tr.address           AS communication_address,
        tr.city,
        tr.pincode           AS pin_code,
        tr.m_password        AS password,
        tr.proof_type ,

         bd.bank_name,
        bd.branch,
        bd.ifsc_code,
        bd.account_no,
        bd.account_name,

        kyc.address_proof_file

    FROM tbl_regist tr
    LEFT JOIN tbl_bank_details bd
        ON tr.mem_sid COLLATE utf8mb4_general_ci
           = bd.member_id COLLATE utf8mb4_general_ci

           LEFT JOIN tbl_kyc kyc
        ON tr.mem_sid COLLATE utf8mb4_general_ci
           = kyc.sponsor_id COLLATE utf8mb4_general_ci
    
    ");
    $stmt->execute();
    $sponsor = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($sponsor as $row):
    ?>
<tr>
    <td><?= $row['member_id'] ?></td>
    <td><?= $row['full_name'] ?></td>
    <td><?= $row['sponsor_id'] ?></td>
    <td><?= $row['sponsor_name'] ?></td>
    <td><?= $row['date_time'] ?></td>
    <td><?= $row['father_spouse_name'] ?></td>
    <td><?= $row['mother_name'] ?></td>
    <td><?= $row['designation'] ?></td>
    <td><?= $row['bank_name'] ?></td>
    <td><?= $row['branch'] ?></td>
    <td><?= $row['ifsc_code'] ?></td>
    <td><?= $row['account_no'] ?></td>
    <td><?= $row['account_name'] ?></td>
    <td><?= $row['gender'] ?></td>
    <td><?= $row['marital_status'] ?></td>
    <td><?= $row['nationality'] ?></td>
    <td><?= $row['dob'] ?></td>
    <td><?= $row['anniversary_date']??null ?></td>
    <td><?= $row['contact_no'] ?></td>
    <td><?= $row['alternate_no'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['pan_no'] ?></td>
    <td><?= $row['aadhar_no'] ?></td>
    <td><?= $row['native_place'] ?></td>
    <td><?= $row['communication_address'] ?></td>
    <td><?= $row['city'] ?></td>
    <td><?= $row['pin_code'] ?></td>
    <td><?= $row['password'] ?></td>
    <td><?= $row['proof_type'] ?></td>
     <td>
    <img 
        src="member_document/<?= $row['address_proof_file'] ?>" 
        class="proofImg"
        alt="proof_image"
        style="height:40px;border-radius: 0%;"
    >
</td>
    <td class="d-flex">
        
        <input type="submit" class="btn btn-sm btn-primary editBtn" name="edit" value="Edit" />

        <form  method="post" >
        <input type="hidden" name="del_id" value="<?= $row['member_id'] ?>" />
        <input type="submit" class="btn btn-sm btn-danger" name="delete" value="Delete" />
        </form>
    </td>
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

                            <!-- ================= EDIT MODAL ================= -->

<div class="modal fade" id="editModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
    
<div class="modal-header">
    <h5 class="modal-title">Edit Staff</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<form id="editForm"  method="post">
<div class="row g-2">

<div class="col-md-6">
    <label>Member ID</label>
    <input type="text" id="staff_id" name="member_id" class="form-control" readonly>
</div>

<div class="col-md-6">
    <label>Member Name</label>
    <input type="text" id="staff_name" name="member_name" class="form-control">
</div>

<div class="col-md-6">
    <label>Sponser ID</label>
    <input type="text" id="sponsor_id" name="sponsor_id" class="form-control">
</div>

<div class="col-md-6">
    <label>Sponser Name</label>
    <input type="text" id="sponsor_name" name="sponsor_name" class="form-control">
</div>

<div class="col-md-6">
    <label>Date Of Joining</label>
    <input type="text" id="doj" name="doj" class="form-control">
</div>

<div class="col-md-6">
    <label>Spouse</label>
    <input type="text" id="spouse" name="spouse" class="form-control">
</div>

<div class="col-md-6">
    <label>Parents Name</label>
    <input type="text" id="parents" name="parents" class="form-control">
</div>

<div class="col-md-6">
    <label>Designation</label>
    <input class="form-control" list="datalistOptions" id="designation" placeholder="Search/Enter Designation" name="designation" value="">
                                                            <datalist id="datalistOptions">
                                                            <option value="Sales Executive (S.E.)">
                                                            <option value="Senior Sales Executive (S.S.E.)">
                                                                <option value="Assistant Marketing Officer (A.M.O.)">
                                                                    <option value="Marketing Officer (M.O.)">
                                                                        <option value="Assistant Marketing Manager (A.M.M.)">
                                                                            <option value="Marketing Manager (M.M.)">
                                                                                <option value="Chief Marketing Manager (C.M.M.)">
                                                                                    <option value="Assistant General Manager (A.G.M.)">
                                                                                        <option value="Deputy General Manager (D.G.M.)">
                                                                                            <option value="General Manager (G.M.)">
                                                                                                <option value="Marketing Director (M.D.)">
                                                                                                    <option value="Founder Member (F.M.)">
                                                                                                        
                                                            </datalist>
</div>

<div class="col-md-6">
    <label>Bank Name</label>
    <input type="text" id="b_name" name="b_name" class="form-control">
</div>
<div class="col-md-6">
    <label>Branch Name</label>
    <input type="text" id="br_name" name="br_name" class="form-control">
</div>
<div class="col-md-6">
    <label>IFSC Code</label>
    <input type="text" id="ifsc" name="ifsc" class="form-control">
</div>
<div class="col-md-6">
    <label>Bank Account Number</label>
    <input type="text" id="ba_number" name="ba_number" class="form-control">
</div>
<div class="col-md-6">
    <label>Bank Account Holder Name</label>
    <input type="text" id="ach_name" name="ach_name" class="form-control">
</div>

<div class="col-md-6">
    <label>Gender</label>
    <input type="text" id="gender" name="gender" class="form-control">
</div>

<div class="col-md-6">
    <label>Marital Status</label>
    <input type="text" id="marital" name="marital" class="form-control">
</div>

<div class="col-md-6">
    <label>Nationality</label>
    <input type="text" id="nationality" name="nationality" class="form-control">
</div>

<div class="col-md-6">
    <label>DOB</label>
    <input type="date" id="dob" name="dob" class="form-control">
</div>

<div class="col-md-6">
    <label>Anniversary</label>
    <input type="date" id="anniversary" name="anniversary" class="form-control">
</div>

<div class="col-md-6">
    <label>Mobile</label>
    <input type="text" id="mobile" name="mobile" class="form-control">
</div>

<div class="col-md-6">
    <label>Alternate</label>
    <input type="text" id="alternate" name="alternate" class="form-control">
</div>

<div class="col-md-6">
    <label>Email</label>
    <input type="text" id="email" name="email" class="form-control">
</div>

<div class="col-md-6">
    <label>PAN</label>
    <input type="text" id="pan" name="pan" class="form-control">
</div>

<div class="col-md-6">
    <label>Aadhar</label>
    <input type="text" id="aadhar" name="aadhar" class="form-control">
</div>

<div class="col-md-6">
    <label>Native Place</label>
    <input type="text" id="native" name="native" class="form-control">
</div>

<div class="col-md-6">
    <label>Address</label>
    <input type="text" id="address" name="address" class="form-control">
</div>

<div class="col-md-6">
    <label>City</label>
    <input type="text" id="city" name="city" class="form-control">
</div>

<div class="col-md-6">
    <label>Pincode</label>
    <input type="text" id="pincode" name="pincode" class="form-control">
</div>

<div class="col-md-6">
    <label>Password</label>
    <input type="text" id="password" name="password" class="form-control">
</div>

<div class="col-md-6">
    
                                                            <label><b>Select Proof Type</b></label>
                                                            <select class="form-control" id="proofType" name="proofType">
                                                                <option value="">-- Select Proof --</option>
                                                                <option value="aadhar">Aadhar Card</option>
                                                                <option value="pan">PAN Card</option>
                                                                <option value="passport">Passport</option>
                                                                <option value="passbook">Bank Passbook</option>
                                                            </select>

                                                             <!-- Preview box -->
    <div id="proofPreview" class="mt-2 d-none">
        <input name="address_proof_file1[]" type="hidden" class="form-control"  id="proofFile1">
    <img id="previewImg" src="" class="img-thumbnail" style="max-height:150px;">
                                                        </div>
</div>

                                                        <div class="mb-3 d-none" id="proofUploadBox">
                                                            <label class="form-label" id="proofLabel"></label>
                                                            <input name="address_proof_file[]" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" id="proofFile" multiple>
                                                            <small class="text-muted" id="proofHint"></small>
                                                        </div>

</div>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success" type="submit" name="update">Update</button>
</div>
</form>
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

                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

                <!-- ================= JS ================= -->

<script>
$(document).ready(function(){

    $('#staffTable').DataTable();

    // EDIT BUTTON
  $('#staffTable').on('click', '.editBtn', function () {

    let row = $(this).closest('tr').children('td');

    $('#staff_id').val(row.eq(0).text().trim());
    $('#staff_name').val(row.eq(1).text().trim());
    $('#sponsor_id').val(row.eq(2).text().trim());
    $('#sponsor_name').val(row.eq(3).text().trim());
    $('#doj').val(row.eq(4).text().trim());
    $('#spouse').val(row.eq(5).text().trim());
    $('#parents').val(row.eq(6).text().trim());
    $('#designation').val(row.eq(7).text().trim());

    // Bank (blank ho sakta hai but index fixed rahega)
    $('#b_name').val(row.eq(8).text().trim());
    $('#br_name').val(row.eq(9).text().trim());
    $('#ifsc').val(row.eq(10).text().trim());
    $('#ba_number').val(row.eq(11).text().trim());
    $('#ach_name').val(row.eq(12).text().trim());

    $('#gender').val(row.eq(13).text().trim());
    $('#marital').val(row.eq(14).text().trim());
    $('#nationality').val(row.eq(15).text().trim());
    $('#dob').val(row.eq(16).text().trim());

    // Anniversary
    if ($('#marital').val() === 'Married') {
        $('#anniversary').prop('disabled', false).show();
        $('#anniversary').val(row.eq(17).text().trim());
    } else {
        $('#anniversary').prop('disabled', true).val('').hide();
    }

    $('#mobile').val(row.eq(18).text().trim());
    $('#alternate').val(row.eq(19).text().trim());
    $('#email').val(row.eq(20).text().trim());
    $('#pan').val(row.eq(21).text().trim());
    $('#aadhar').val(row.eq(22).text().trim());
    $('#native').val(row.eq(23).text().trim());
    $('#address').val(row.eq(24).text().trim());
    $('#city').val(row.eq(25).text().trim());
    $('#pincode').val(row.eq(26).text().trim());
    $('#password').val(row.eq(27).text().trim());

    // Proof type
    let proofType = row.eq(28).text().trim();
    $('#proofType').val(proofType);

    // Image
    let imgSrc = row.eq(29).find('img.proofImg').attr('src');
    if (proofType !== '' && imgSrc) {
        $('#previewImg').attr('src', imgSrc);
        $('#proofPreview').removeClass('d-none');
        $('#proofFile1').val(imgSrc);
    } else {
        $('#proofPreview').addClass('d-none');
        $('#previewImg').attr('src', '');
        $('#proofFile1').val('');
    }

    $('#editModal').modal('show');
});

    // DELETE BUTTON
    $('#staffTable').on('click','.deleteBtn',function(){
        if(confirm('Are you sure you want to delete this record?')){
            $('#staffTable').DataTable().row($(this).closest('tr')).remove().draw();
        }
    });

});
</script>

            <script>
$(document).ready(function(){

   // let ajaxTimer = null;

    $('#sponsor_id').on('input', function(){

        let searchVal = $(this).val().trim();

         // clear name while typing
        $('#sponsor_name').val('');

        if(searchVal === ''){
            return;
        }


        // debounce (fast typing issue)
        // clearTimeout(ajaxTimer);
        // ajaxTimer = setTimeout(function(){

            $.ajax({
                url: 'search_sponsor.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    search: searchVal   // 👈 key "search"
                },
                success: function(response){
                   
                    
                    if(response.mem_sid == searchVal){
                        // first matched record
                        
                        $('#sponsor_name').val(response.m_name);
                    }else{
                        $('#sponsor_name').val("No matching sponsor found");
                    }
                },
                error: function(){
                    $('#sponsor_name').val('Error fetching data');
                }
            });

       // }, 300); // delay for better UX
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