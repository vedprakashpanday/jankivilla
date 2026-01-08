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
     $member_id        = $_POST['staff_id']        ?? '';   
    $s_name            = $_POST['staff_name']      ?? '';
    $spouse            = $_POST['spouse']          ?? '';
    $parents           = $_POST['parents']         ?? '';
    $gender            = $_POST['gender']            ?? '';
    $date_of_birth     = $_POST['dob']     ?? '';
    $m_email           = $_POST['email']           ?? '';
    $m_num             = $_POST['mobile']             ?? '';
    $address           = $_POST['address']           ?? '';
    $city              = $_POST['city']              ?? '';
    $state_name        = $_POST['state_name']        ?? '';
    $aadhar            = $_POST['aadhar']            ?? '';
    $pan        = $_POST['pan']        ?? '';
    $designation       = $_POST['designation']       ?? '';
    $address_proof_type= $_POST['proofType']?? '';
    $martial        = $_POST['marital']        ?? '';
    $anniversary       = $_POST['anniversary'] ?? null;
    $alternate      = $_POST['alternate']        ?? '';
    $native       = $_POST['native']        ?? '';
    $pin       = $_POST['pincode']        ?? '';
    $password       = $_POST['password']        ?? '';
    $nationality       = $_POST['nationality']        ?? '';
  
    


    // --- UPDATE QUERY WITH JSON APPEND ---
  $update_regist = "
    UPDATE adm_regist
    SET 

    full_name = :full_name,
    mother_name = :mother_name,
    father_spouse_name = :father_spouse_name,
    gender = :gender,
    designation = :designation1,
    dob = :dob,
    anniversary_date = :anniversary_date,
    contact_no = :contact_no,
    email = :email,
    password = :password,
    create_time = :create_time,
    communication_address = :communication_address,
    city = :city,
    aadhar_no = :aadhar_no,
    pan_no = :pan_no,   
    alternate_no = :alternate_no,
    native_place = :native_place,
    pin_code = :pin_code,
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

    WHERE member_id = :member_id
";

    $stmt_regist = $pdo->prepare($update_regist);

   $params = [
    'full_name'       => $s_name,
    'mother_name'     => $parents,
    'father_spouse_name' => $spouse,
    'gender'          => $gender,
    'designation1'    => $designation,
    'dob'             => $date_of_birth,
    'anniversary_date'=> $anniversary,
    'contact_no'      => $m_num,
    'email'           => $m_email,
    'password'        => $password,
    'create_time'     => date('Y-m-d H:i:s'),
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
    header("Location: updateadm.php");
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

    $delete_stmt = $pdo->prepare("DELETE FROM adm_regist WHERE member_id = :member_id");
    $delete_stmt->bindParam(':member_id', $del_id);

    try {
        $delete_stmt->execute();
        echo "<script>alert('Record deleted successfully!');</script>";
        header("Location: updateadm.php");
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
    <th>Staff ID</th>
    <th>Staff Name</th>
    <th>Spouse</th>
    <th>Parents Name</th>
    <th>Designation</th>
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
    <th>Action</th>
</tr>
</thead>

<tbody>
    <?php 
        $stmt = $pdo->prepare("SELECT * FROM adm_regist");
    $stmt->execute();
    $sponsor = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($sponsor as $row):
    ?>
<tr>
    <td><?= $row['member_id'] ?></td>
    <td><?= $row['full_name'] ?></td>
    <td><?= $row['father_spouse_name'] ?></td>
    <td><?= $row['mother_name'] ?></td>
    <td><?= $row['designation'] ?></td>
    <td><?= $row['gender'] ?></td>
    <td><?= $row['marital_status'] ?></td>
    <td><?= $row['nationality'] ?></td>
    <td><?= $row['dob'] ?></td>
    <td><?= $row['anniversary_date'] ?></td>
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
    <label>Staff ID</label>
    <input type="text" id="staff_id" name="staff_id" class="form-control" readonly>
</div>

<div class="col-md-6">
    <label>Staff Name</label>
    <input type="text" id="staff_name" name="staff_name" class="form-control">
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
    <input type="text" id="designation" name="designation" class="form-control">
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
                                                        </div>

                                                        <!-- <div class="mb-3 d-none" id="proofUploadBox">
                                                            <label class="form-label" id="proofLabel"></label>
                                                            <input name="address_proof_file[]" type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" id="proofFile" multiple>
                                                            <small class="text-muted" id="proofHint"></small>
                                                        </div> -->

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
    $('#staffTable').on('click','.editBtn',function(){

        let row = $(this).closest('tr').children('td');

        $('#staff_id').val(row.eq(0).text());
        $('#staff_name').val(row.eq(1).text());
        $('#spouse').val(row.eq(2).text());
        $('#parents').val(row.eq(3).text());
        $('#designation').val(row.eq(4).text());
        $('#gender').val(row.eq(5).text());
        $('#marital').val(row.eq(6).text());

        $('#nationality').val(row.eq(7).text());
        $('#dob').val(row.eq(8).text());

       $('#marital').val(row.eq(6).text().trim());

// check value
if ($('#marital').val() === 'Married') {
    $('#anniversary').prop('disabled', false).closest('.col-md-6').show();
} else {
    $('#anniversary').prop('disabled', true).val('').closest('.col-md-6').hide();
}

// set anniversary only if married
if ($('#marital').val() === 'Married') {
    $('#anniversary').val(row.eq(9).text().trim());
}
        $('#mobile').val(row.eq(10).text());
        $('#alternate').val(row.eq(11).text());
        $('#email').val(row.eq(12).text());
        $('#pan').val(row.eq(13).text());
        $('#aadhar').val(row.eq(14).text());
        $('#native').val(row.eq(15).text());
        $('#address').val(row.eq(16).text());
        $('#city').val(row.eq(17).text());
        $('#pincode').val(row.eq(18).text());
        $('#password').val(row.eq(19).text());
        $('#proof').val(row.eq(20).text());

        $('#editModal').modal('show');
    });

    // DELETE BUTTON
    $('#staffTable').on('click','.deleteBtn',function(){
        if(confirm('Are you sure you want to delete this record?')){
            $('#staffTable').DataTable().row($(this).parents('tr')).remove().draw();
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