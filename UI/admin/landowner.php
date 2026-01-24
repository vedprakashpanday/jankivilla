<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

function jsonToText1($json, $type = 'old')
{
    if(!empty($json))
        {
    $data = json_decode($json, true);
    if (!is_array($data)) return '';

    if ($type === 'old' && !empty($data['old_khesra_no'])) {
        return htmlspecialchars($data['old_khesra_no']);
    }

    if ($type === 'new' && !empty($data['new_khesra_no'])) {
        return htmlspecialchars($data['new_khesra_no']);
    }

if ($type === 'new' && !empty($data['new_khata'])) {
        return htmlspecialchars($data['new_khata']);
    }

    if ($type === 'old' && !empty($data['old_khata'])) {
        return htmlspecialchars($data['old_khata']);
    }
        }
    return '';
}

function jsonToText($json)
{
    $data = json_decode($json, true);
    if (!is_array($data)) return '';

    // Measurement order + labels
    $units = [
        'bigha'   => 'Bigha',
        'kattha'  => 'Kattha',
        'dhoor'    => 'Dhoor',
        'kanma'   => 'Kanma',
        'dismil'  => 'Dismil'
    ];

    $output = [];

    foreach ($units as $key => $label) {
        if (isset($data[$key]) && is_numeric($data[$key]) && $data[$key] > 0) {
            $output[] = $data[$key] . ' ' . $label;
        }
    }

    return htmlspecialchars(implode(' ', $output));
}

function jsonToText2($json, $type = '')
{
    if (empty($json) || empty($type)) {
        return '';
    }

    $data = json_decode($json, true);

    if (!is_array($data)) {
        return '';
    }

    // direct key access
    if (isset($data[$type]) && $data[$type] !== '') {
        return htmlspecialchars($data[$type]);
    }

    return '';
}


if(isset($_POST['action']) && $_POST['action'] == 'fetch_land_owner' && isset($_POST['id'])){
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT * FROM land_owner_payments WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if($data){
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}



// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =========================
       INSERT NEW RECORD
    ==========================*/
    if (isset($_POST['submit'])) {


        // echo "<pre>";
        // print_r($_POST);

        // echo "</pre>";
        // exit();
      
// JSON fields (safe)
$khesra_no = !empty($_POST['khesra_no'])
    ? json_encode($_POST['khesra_no'], JSON_UNESCAPED_UNICODE)
    : json_encode([]);

$rakuwa = !empty($_POST['rakuwa'])
    ? json_encode($_POST['rakuwa'], JSON_UNESCAPED_UNICODE)
    : json_encode([]);

$khata = !empty($_POST['khata'])
    ? json_encode($_POST['khata'], JSON_UNESCAPED_UNICODE)
    : json_encode([]);

// Owner details
$land_owner_name  = $_POST['land_owner_name'] ?? null;
$relation_name    = $_POST['relation_name'] ?? null;
$address          = $_POST['address'] ?? null;
$mobile1          = $_POST['mobile1'] ?? null;
$mobile2          = $_POST['mobile2'] ?? null;
$mauze_name       = $_POST['mauze_name'] ?? null;
$thana_no         = $_POST['thana_no'] ?? null;
$total_land_value = $_POST['total_land_value'] ?? null;

$lo_state     = $_POST['lo_state'] ?? null;
$lo_district  = $_POST['lo_district'] ?? null;
$lo_block     = $_POST['lo_block'] ?? null;
$lo_panchayat = $_POST['lo_panchayat'] ?? null;
$lo_village   = $_POST['lo_village'] ?? null;
$lo_aadhar    = $_POST['lo_aadhar'] ?? null;
$lo_pan       = $_POST['lo_pan'] ?? null;
$lo_dob       = !empty($_POST['lo_dob']) ? $_POST['lo_dob'] : null;

$agree_date = !empty($_POST['agree_date']) ? $_POST['agree_date'] : null;
$agree_dur  = $_POST['agree_dur'] ?? null;
$jamabandi  = $_POST['jamabandi'] ?? null;

// Nominee details
$nom_name     = $_POST['nominee_name'] ?? null;
$nom_relation = $_POST['nominee_so_do_wo'] ?? null;
$nom_dob      = $_POST['nominee_dob'] ?? null;
$rate_per_katha = $_POST['rate_per_katha'] ?? null;

$nom_mobile = !empty($_POST['nominee_mobile']) ? (int)$_POST['nominee_mobile'] : null;
$nom_alt_mobile = !empty($_POST['nominee_alternate_mobile']) ? (int)$_POST['nominee_alternate_mobile'] : null;
$nom_pincode = !empty($_POST['nominee_pincode']) ? (int)$_POST['nominee_pincode'] : null;

$nom_email    = $_POST['nominee_email'] ?? null;
$nom_aadhar   = $_POST['nominee_aadhar'] ?? null;
$nom_pan      = $_POST['nominee_pan'] ?? null;
$nom_address  = $_POST['nominee_address'] ?? null;
$nom_state    = $_POST['nominee_state'] ?? null;
$nom_district = $_POST['nominee_district'] ?? null;

$status = 'active';

// PDO Insert
$stmt = $pdo->prepare("
    INSERT INTO land_owner_payments (
        land_owner_name, relation_name, address, mobile1, mobile2,
        mauze_name, thana_no, khesra_no, rakuwa, rate_per_katha,
        total_land_value, nom_name, nom_relation, nom_dob,
        nom_mobile, nom_alt_mobile, nom_email, nom_aadhar, nom_pan,
        nom_address, nom_pin, nom_state, nom_district, status,
        lo_state, lo_district, lo_block, lo_panchayat, lo_village,
        lo_aadhar, lo_pan, agree_date, agree_dur, jamabandi, khata, lo_dob
    )
    VALUES (
        ?,?,?,?,?, ?,?,?,?,?,
        ?,?,?,?,?, ?,?,?,?,?,
        ?,?,?,?,?,
        ?,?,?,?,?, ?,?,?,?, ?,? 
    )
");

$stmt->execute([
    $land_owner_name,
    $relation_name,
    $address,
    $mobile1,
    $mobile2,

    $mauze_name,
    $thana_no,
    $khesra_no,
    $rakuwa,
    $rate_per_katha,

    $total_land_value,
    $nom_name,
    $nom_relation,
    $nom_dob,

    $nom_mobile,
    $nom_alt_mobile,
    $nom_email,
    $nom_aadhar,
    $nom_pan,

    $nom_address,
    $nom_pincode,
    $nom_state,
    $nom_district,
    $status,

    $lo_state,
    $lo_district,
    $lo_block,
    $lo_panchayat,
    $lo_village,
    $lo_aadhar,
    $lo_pan,
    $agree_date,
    $agree_dur,
    $jamabandi,
    $khata,
    $lo_dob
]);

$new_id = $pdo->lastInsertId();

echo "<script>
    alert('Land Owner Payment inserted successfully!');
    window.location.href='landowner.php';
</script>";
exit;

    }

   /* =========================
   UPDATE EXISTING RECORD
==========================*/
if (isset($_POST['update']) && !empty($_POST['edit_id'])) {

    $edit_id = $_POST['edit_id'];

    // JSON fields (same as insert)
    $khesra_no = !empty($_POST['khesra_no'])
        ? json_encode($_POST['khesra_no'], JSON_UNESCAPED_UNICODE)
        : json_encode([]);

    $rakuwa = !empty($_POST['rakuwa'])
        ? json_encode($_POST['rakuwa'], JSON_UNESCAPED_UNICODE)
        : json_encode([]);

    $khata = !empty($_POST['khata'])
        ? json_encode($_POST['khata'], JSON_UNESCAPED_UNICODE)
        : json_encode([]);

    // Owner details
    $land_owner_name  = $_POST['land_owner_name'] ?? null;
    $relation_name    = $_POST['relation_name'] ?? null;
    $address          = $_POST['address'] ?? null;
    $mobile1          = $_POST['mobile1'] ?? null;
    $mobile2          = $_POST['mobile2'] ?? null;
    $mauze_name       = $_POST['mauze_name'] ?? null;
    $thana_no         = $_POST['thana_no'] ?? null;
    $total_land_value = $_POST['total_land_value'] ?? null;

    $lo_state     = $_POST['lo_state'] ?? null;
    $lo_district  = $_POST['lo_district'] ?? null;
    $lo_block     = $_POST['lo_block'] ?? null;
    $lo_panchayat = $_POST['lo_panchayat'] ?? null;
    $lo_village   = $_POST['lo_village'] ?? null;
    $lo_aadhar    = $_POST['lo_aadhar'] ?? null;
    $lo_pan       = $_POST['lo_pan'] ?? null;
    $lo_dob       = !empty($_POST['lo_dob']) ? $_POST['lo_dob'] : null;

    $agree_date = $_POST['agree_date'] ?? null;
    $agree_dur  = $_POST['agree_dur'] ?? null;
    $jamabandi  = $_POST['jamabandi'] ?? null;

    // Nominee details
    $nom_name       = $_POST['nominee_name'] ?? null;
    $nom_relation   = $_POST['nominee_so_do_wo'] ?? null;
    $nom_dob        = $_POST['nominee_dob'] ?? null;
    $rate_per_katha = $_POST['rate_per_katha'] ?? null;

    $nom_mobile = !empty($_POST['nominee_mobile']) ? (int)$_POST['nominee_mobile'] : null;
    $nom_alt_mobile = !empty($_POST['nominee_alternate_mobile']) ? (int)$_POST['nominee_alternate_mobile'] : null;
    $nom_pincode = !empty($_POST['nominee_pincode']) ? (int)$_POST['nominee_pincode'] : null;

    $nom_email    = $_POST['nominee_email'] ?? null;
    $nom_aadhar   = $_POST['nominee_aadhar'] ?? null;
    $nom_pan      = $_POST['nominee_pan'] ?? null;
    $nom_address  = $_POST['nominee_address'] ?? null;
    $nom_state    = $_POST['nominee_state'] ?? null;
    $nom_district = $_POST['nominee_district'] ?? null;

    $stmt = $pdo->prepare("
        UPDATE land_owner_payments SET
            land_owner_name=?,
            relation_name=?,
            address=?,
            mobile1=?,
            mobile2=?,
            mauze_name=?,
            thana_no=?,
            khesra_no=?,
            rakuwa=?,
            rate_per_katha=?,
            total_land_value=?,
            nom_name=?,
            nom_relation=?,
            nom_dob=?,
            nom_mobile=?,
            nom_alt_mobile=?,
            nom_email=?,
            nom_aadhar=?,
            nom_pan=?,
            nom_address=?,
            nom_pin=?,
            nom_state=?,
            nom_district=?,
            lo_state=?,
            lo_district=?,
            lo_block=?,
            lo_panchayat=?,
            lo_village=?,
            lo_aadhar=?,
            lo_pan=?,
            agree_date=?,
            agree_dur=?,
            jamabandi=?,
            khata=?,
            lo_dob=?
        WHERE id=?
    ");

    $stmt->execute([
        $land_owner_name,
        $relation_name,
        $address,
        $mobile1,
        $mobile2,
        $mauze_name,
        $thana_no,
        $khesra_no,
        $rakuwa,
        $rate_per_katha,
        $total_land_value,
        $nom_name,
        $nom_relation,
        $nom_dob,
        $nom_mobile,
        $nom_alt_mobile,
        $nom_email,
        $nom_aadhar,
        $nom_pan,
        $nom_address,
        $nom_pincode,
        $nom_state,
        $nom_district,
        $lo_state,
        $lo_district,
        $lo_block,
        $lo_panchayat,
        $lo_village,
        $lo_aadhar,
        $lo_pan,
        $agree_date,
        $agree_dur,
        $jamabandi,
        $khata,
        $lo_dob,
        $edit_id
    ]);

    echo "<script>
        alert('Record updated successfully!');
        window.location.href='landowner.php';
    </script>";
    exit;
}



    // Add payment transaction (Ledger Entry) - SEPARATE ACTION
    if (isset($_POST['add_transaction'])) {
        $land_owner_id = $_POST['land_owner_id']; // Changed from edit_id
        $transaction_date = $_POST['transaction_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'] ?? null;
        $account_number = $_POST['account_no'] ?? null;
        $ifsc_code = $_POST['ifsc_code'] ?? null;
        $transaction_type = $_POST['transaction_type'];
        $amount = $_POST['amount'];
        $dv_no = $_POST['dv_no'] ?? null;
        $remarks = $_POST['remarks'] ?? null;

        // Validate required fields
        if (empty($transaction_date) || empty($payment_mode) || empty($transaction_type) || empty($amount)) {
            echo "<script>alert('Please fill all required transaction fields!'); window.location.href='?id=$land_owner_id';</script>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO land_payment_transactions 
                (land_owner_id, transaction_date, payment_mode, bank_name, account_number, ifsc, transaction_type, amount, dv_no, remarks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$land_owner_id, $transaction_date, $payment_mode, $bank_name, $account_number, $ifsc_code, $transaction_type, $amount, $dv_no, $remarks])) {
                echo "<script>alert('Payment transaction added successfully!'); window.location.href='landreceipt.php?landid=$land_owner_id';</script>";
            }
        }
        exit;
    }

    // Delete payment transaction
    if (isset($_POST['delete_transaction_id'])) {
        $transaction_id = $_POST['delete_transaction_id'];
        // Get land owner ID before deleting
        $get_id = $pdo->prepare("SELECT land_owner_id FROM land_payment_transactions WHERE id = ?");
        $get_id->execute([$transaction_id]);
        $trans_data = $get_id->fetch(PDO::FETCH_ASSOC);
        $land_owner_id = $trans_data['land_owner_id'];

        $stmt = $pdo->prepare("DELETE FROM land_payment_transactions WHERE id = ?");
        if ($stmt->execute([$transaction_id])) {
            echo "<script>alert('Transaction deleted successfully!'); window.location.href='?id=$land_owner_id';</script>";
        }
        exit;
    }

    // Delete land owner record
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        // Delete transactions first (if not using CASCADE)
        $pdo->prepare("DELETE FROM land_payment_transactions WHERE land_owner_id = ?")->execute([$delete_id]);
        // Delete land owner
        $stmt = $pdo->prepare("DELETE FROM land_owner_payments WHERE id = ?");
        if ($stmt->execute([$delete_id])) {
            echo "<script>alert('Record deleted successfully!'); window.location.href='landowner.php';</script>";
        }
        exit;
    }
}

// Fetch single record for edit
$edit_data = null;
$edit_id = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM land_owner_payments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($edit_data) {
        $edit_id = $_GET['id'];
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



        /* Form Container */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
        }

        /* Heading Styling */
        .form-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 20px;
        }

        /* Label Styling */
        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        /* Input Fields */
        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="file"]:focus {
            border-color: #4A90E2;
            outline: none;
        }

        /* Button Styling */
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4A90E2;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #357ABD;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .form-container {
                width: 90%;
                padding: 20px;
            }

            .form-container h2 {
                font-size: 20px;
            }
        }
    </style>

    <script type="text/ecmascript">
        var loadFile = function(event) {
            var image = document.getElementById('output');
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>



</head>

<body class="hold-transition skin-blue sidebar-mini" data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <div class="wrapper">
        <div class="container-scroller">
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <div class="franchise_nav_menu">
                    <?php include "adminheadersidepanel.php"; ?>
                </div>


                <div class="main-panel">
                    <div class="card px-5">
                        <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                            <h2>
                                Add Land Owner Details
                            </h2>
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div style="background: #fff; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                        
                                        <div class="">
                                            <div class="col-12 mt-5" style="margin: unset!important;">
                                                <div class="card shadow rounded-4 mb-5 mt-3 shadow-lg">
                                                    <!-- <div class="card-header bg-primary text-white rounded-top-4">
                                                        <h4 class="mb-0">
                                                            <h4 class="mb-0"><?php echo isset($edit_id) ? 'Edit Land Owner Payment' : 'Add Land Owner Payment'; ?></h4>
                                                        </h4>
                                                    </div> -->
                                                    <div class="card-body p-4">
                                                        <form method="post" id="landOwnerForm">
                                                            <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? ''; ?>">

                                                            <!-- Basic Land Owner Details -->
                                                            <div class="card mb-4">
                                                                <!-- <div class="card-header bg-primary text-white">
                                                                    <h5 class="mb-0">Land Owner Details</h5>
                                                                </div> -->
                                                                <div class="card-body">
                                                                    <div class="row" id="khesraContainer">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Land Owner Name <span class="text-danger">*</span></label>
                                                                           <input 
    type="text"
    class="form-control" 
    name="land_owner_name" 
    id="land_owner_name"
    list="designationList"
    placeholder="-- Select/Enter Landowner Name"
    required
    value="<?= htmlspecialchars($edit_data['land_owner_name'] ?? '') ?>"
>

<datalist id="designationList">
    <?php 
        $stmt = $pdo->prepare("SELECT id, land_owner_name, nom_name FROM land_owner_payments");
        $stmt->execute();
        $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($sponsors as $row):
    ?>
    <option data-id="<?= $row['id'] ?>" value="<?= htmlspecialchars($row['land_owner_name']) ?>">
        <?= htmlspecialchars($row['nom_name']) ?>
    </option>
    <?php endforeach; ?>
</datalist>
                                                                        </div>
                                                                       

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">S/o, W/o, D/o</label>
                                                                            <input type="text" class="form-control" name="relation_name" value="<?php echo htmlspecialchars($edit_data['relation_name'] ?? ''); ?>">
                                                                        </div>

                                                                         <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Date Of Birth</label>
                                                                            <input type="date" class="form-control" name="lo_dob" value="<?php echo htmlspecialchars($edit_data['lo_dob'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">State</label>
                                                                            <input type="text" class="form-control" name="lo_state" value="<?php echo htmlspecialchars($edit_data['lo_state'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">District</label>
                                                                            <input type="text" class="form-control" name="lo_district" value="<?php echo htmlspecialchars($edit_data['lo_district'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Block</label>
                                                                            <input type="text" class="form-control" name="lo_block" value="<?php echo htmlspecialchars($edit_data['lo_block'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Panchayat</label>
                                                                            <input type="text" class="form-control" name="lo_panchayat" value="<?php echo htmlspecialchars($edit_data['lo_panchayat'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Village</label>
                                                                            <input type="text" class="form-control" name="lo_village" value="<?php echo htmlspecialchars($edit_data['lo_village'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Aadhar Number</label>
                                                                            <input type="number" class="form-control" name="lo_aadhar" value="<?php echo htmlspecialchars($edit_data['lo_aadhar'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">PAN Number</label>
                                                                            <input type="text" class="form-control" name="lo_pan" value="<?php echo htmlspecialchars($edit_data['lo_pan'] ?? ''); ?>">
                                                                        </div>

                                                                         <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Agreement Date</label>
                                                                            <input type="date" class="form-control" name="agree_date" value="<?php echo htmlspecialchars($edit_data['agree_date'] ?? ''); ?>">
                                                                        </div>

                                                                         <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Agreement Duration(In Months)</label>
                                                                            <input type="text" class="form-control" name="agree_dur" value="<?php echo htmlspecialchars($edit_data['agree_dur'] ?? ''); ?>">
                                                                        </div>

                                                                         <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Jamabandi Number</label>
                                                                            <input type="text" class="form-control" name="jamabandi" value="<?php echo htmlspecialchars($edit_data['jamabandi'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-12 mb-3">
                                                                            <label class="form-label fw-semibold">Address</label>
                                                                            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($edit_data['address'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Mobile No. (1)</label>
                                                                            <input type="text" class="form-control" name="mobile1" value="<?php echo htmlspecialchars($edit_data['mobile1'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Mobile No. (2)</label>
                                                                            <input type="text" class="form-control" name="mobile2" value="<?php echo htmlspecialchars($edit_data['mobile2'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Mauza Name</label>
                                                                            <input type="text" class="form-control" name="mauze_name" value="<?php echo htmlspecialchars($edit_data['mauze_name'] ?? ''); ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Thana No.</label>
                                                                            <input type="text" class="form-control" name="thana_no" value="<?php echo htmlspecialchars($edit_data['thana_no'] ?? ''); ?>">
                                                                        </div>

                                                                         <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold">Old Khesra No.</label>
                                                                        <input type="text"
                                                                            class="form-control"
                                                                            name="khesra_no[old_khesra_no]"
                                                                            placeholder="Enter Old Khesra No."
                                                                            value="<?= jsonToText1($edit_data['khesra_no'], 'old') ?? 'null' ?>">
                                                                            
                                                                            
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold">New Khesra No.</label>
                                                                        <input type="text"
                                                                            class="form-control"
                                                                            name="khesra_no[new_khesra_no]"
                                                                            placeholder="Enter New Khesra No."
                                                                             value="<?= jsonToText1($edit_data['khesra_no'], 'new') ?? 'null' ?>">
                                                                            
                                                                            
                                                                            
                                                                    </div>

                                                                     <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Old Khata Number </label>
                                                                            <input type="text"  class="form-control" name="khata[old_khata]" placeholder="Enter Old Khata Number" value="<?= jsonToText1($edit_data['khata'], 'old') ?? 'null' ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">New Khata Number </label>
                                                                            <input type="text"  class="form-control" name="khata[new_khata]" placeholder="Enter New Khata Number" value="<?= jsonToText1($edit_data['khata'], 'new') ?? 'null' ?>">
                                                                        </div>

                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Rate Per Kattha</label>
                                                                            <input type="number" step="0.01" class="form-control" name="rate_per_katha" placeholder="Enter Rate Per Katha" value="<?php echo htmlspecialchars($edit_data['rate_per_katha'] ?? ''); ?>">
                                                                        </div>  

                                                                         <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Total Land Value <span class="text-danger">*</span></label>
                                                                            <input type="number" step="0.01" class="form-control" name="total_land_value" required id="totalLandValue" value="<?php echo htmlspecialchars($edit_data['total_land_value'] ?? ''); ?>">
                                                                        </div>


                                                                    <div class="col align-items-end mb-3 col-md-12">
       <label class="form-label fw-semibold mb-2"><strong>Rakwa</strong></label>
       <div class="row align-items-end mb-3 col-md-12">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Bigha</label>
            <input type="number" step="any" class="form-control" name="rakuwa[bigha]" placeholder="0" value="<?= jsonToText2($edit_data['rakuwa'], 'bigha') ?? 'null' ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Kattha</label>
            <input type="number" step="any" class="form-control" name="rakuwa[kattha]" placeholder="0" value="<?= jsonToText2($edit_data['rakuwa'], 'kattha') ?? 'null' ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Dhoor</label>
            <input type="number" step="any" class="form-control" name="rakuwa[dhoor]" placeholder="0" value="<?= jsonToText2($edit_data['rakuwa'], 'dhoor') ?? 'null' ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Kanma</label>
            <input type="number" step="any" class="form-control" name="rakuwa[kanma]" placeholder="0" value="<?= jsonToText2($edit_data['rakuwa'], 'kanma') ?? 'null' ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Dismil</label>
            <input type="number" step="any" class="form-control" name="rakuwa[dismil]" placeholder="0" value="<?= jsonToText2($edit_data['rakuwa'], 'dismil') ?? 'null' ?>">
        </div>
 </div>
    </div>

    
                                                                        <!-- <div class="col-md-4 mb-3" >
                                                                            <label class="form-label fw-semibold">Enter Number Of Khesra</label>
                                                                            <select name="num_o_k" id="num_o_k" class="form-control">
                                                                                <option value="">Select</option>
                                                                                <?php
                                                                                for ($i = 1; $i <= 10; $i++) {
                                                                                    $selected = (isset($edit_data['num_o_k']) && $edit_data['num_o_k'] == $i) ? 'selected' : '';
                                                                                    echo "<option value='$i' $selected>$i</option>";
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                         <div class="col-md-12 d-flex flex-wrap" id="khesraFields"></div>

                                                                       
                                                                        <div class="col-md-12 d-flex flex-wrap" id="rakwaFields"></div>


                                                                      
                                                                        <div class="col-md-12 d-flex flex-wrap" id="rateFields"></div>
                                                                        <div class="col-md-12 d-flex flex-wrap" id="khataFields"></div> -->
                                                                       

                                                                       


                                                                        <div class="col-md-12 mb-3">

                                                                           <!-- ==================== Agent DETAILS ==================== -->
                                                <div class="form-section mt-4">
                                                    <legend>Agent DETAILS</legend>

                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label><b>Agent Name</b></label>
                                                            <input name="nominee_name" type="text" class="form-control" value="<?php echo htmlspecialchars($edit_data['nom_name'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Agent Mobile No</b></label>
                                                            <input name="nominee_mobile" type="text" class="form-control" maxlength="10" value="<?php echo htmlspecialchars($edit_data['nom_mobile'] ?? ''); ?>">
                                                        </div>
                                                        <!-- <div class="col-md-4">
                                                            <label><b>Agent Alternate Mobile No</b></label>
                                                            <input name="nominee_alternate_mobile" type="text" class="form-control" maxlength="10" value="<?php echo htmlspecialchars($edit_data['nom_alt_mobile'] ?? ''); ?>">
                                                        </div> -->
                                                       
                                                    </div>

                                                    <!-- <div class="row g-3 mt-2">
                                                        
                                                        <div class="col-md-4">
                                                            <label><b>Nominee Email Id</b></label>
                                                            <input name="nominee_email" type="email" class="form-control" value="<?php echo htmlspecialchars($edit_data['nom_email'] ?? ''); ?>">
                                                        </div>
                                                    </div> -->

                                                    <div class="row g-3 mt-2">
                                                        <!-- <div class="col-md-4">
                                                            <label><b>Agent Aadhar</b></label>
                                                            <input name="nominee_aadhar" type="text" class="form-control" maxlength="12"  value="<?php echo htmlspecialchars($edit_data['nom_aadhar'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Agent PAN</b></label>
                                                            <input name="nominee_pan" type="text" class="form-control" style="text-transform:uppercase;" maxlength="10" value="<?php echo htmlspecialchars($edit_data['nom_pan'] ?? ''); ?>">
                                                        </div> -->
                                                        <div class="col-md-4">
                                                            <label><b>Agent Address</b></label>
                                                            <input name="nominee_address" type="text" class="form-control" value="<?php echo htmlspecialchars($edit_data['nom_address'] ?? ''); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-2">
                                                        <div class="col-md-4">
                                                            <label><b>Agent Comission(Per Kattha)</b></label>
                                                            <input name="nominee_pincode" type="text" class="form-control"  value="<?php echo htmlspecialchars($edit_data['nom_pin'] ?? ''); ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label><b>Agent Total Comission</b></label>
                                                            <input name="nominee_state" type="text" class="form-control" value="<?php echo htmlspecialchars($edit_data['nom_state'] ?? ''); ?>">
                                                        </div>
                                                        <!-- <div class="col-md-4">
                                                            <label><b>Nominee District</b></label>
                                                            <input name="nominee_district" type="text" class="form-control" value="<?php echo htmlspecialchars($edit_data['nom_district'] ?? ''); ?>">
                                                        </div> -->
                                                    </div>
                                                </div>
                                                                        </div>






                                                                    </div>

                                                                    <!-- Form Action Buttons for Land Owner Details -->
                                                                    <div class="text-end mt-3">
                                                                        <button type="submit" name="<?php echo isset($edit_id) ? 'update' : 'submit'; ?>" class="btn btn-success px-4 rounded-pill">
                                                                            <i class="fas fa-save"></i> <?php echo isset($edit_id) ? 'Update Land Owner Details' : 'Add Land Owner'; ?>
                                                                        </button>
                                                                        <a href="landowner.php" class="btn btn-warning px-4 rounded-pill text-white" style="text-decoration: none;">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                       
                                                        

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <h4 class="mt-4">Land Owner Payment Report</h4>
                                        <div class="" style="overflow:auto;">


                                            <div class="">
                                                <table class="table table-striped mt-3">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Land Owner</th>
                                                            <th>So/Do/Wo</th>
                                                            <th>LandOwner DOB</th>
                                                            <th>LandOwner State</th>
                                                            <th>LandOwner District</th>
                                                            <th>LandOwner Block</th>
                                                            <th>LandOwner Panchayat</th>
                                                            <th>LandOwner Village</th>
                                                            <th>LandOwner Aadhar</th>
                                                            <th>LandOwner PAN</th>
                                                            <th>LandOwner Address</th>
                                                            <th>Agreement Date</th>
                                                            <th>Agreement Duration</th>
                                                            <th>Jamabandi Number</th>
                                                            <th>Mobile(1)</th>
                                                            <th>Mobile(2)</th>
                                                            <th>Mauza Name</th>
                                                            <th>Thana No</th>
                                                            <th>Old Khesra No</th>
                                                            <th>New Khesra No</th>
                                                            <th>Rakwa</th>
                                                             <th>New Khata Number</th>
                                                             <th>Old Khata Number</th>
                                                            <th>Rate (Per Katha)</th>
                                                            <th>Total Land Value</th>
                                                            <th>Agent Name</th> 
                                                            <th>Agent Mobile</th>  
                                                            <th>Agent Address</th>
                                                            <th>Agent Comission(Per Kattha)</th>
                                                            <th>Agent Total Comission</th>
                                                            
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        
                                                        $records = $pdo->query("SELECT * FROM land_owner_payments ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($records as $row): ?>
                                                            <tr>
                                                               <td><?= htmlspecialchars($row['land_owner_name'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_dob'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['relation_name'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_state'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_district'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_block'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_panchayat'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_village'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_aadhar'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['lo_pan'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['agree_date'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['agree_dur'] ?? '') ?></td>
                                                               <td><?= htmlspecialchars($row['jamabandi'] ?? '') ?></td>
<td><?= htmlspecialchars($row['mobile1'] ?? '') ?></td>
<td><?= htmlspecialchars($row['mobile2'] ?? '') ?></td>
<td><?= htmlspecialchars($row['mauze_name'] ?? '') ?></td>
<td><?= htmlspecialchars($row['thana_no'] ?? '') ?></td>
<td><?= jsonToText1($row['khesra_no'], 'old') ?? 'null' ?></td>
<td><?= jsonToText1($row['khesra_no'], 'new') ?? 'null' ?></td>
<td><?= jsonToText($row['rakuwa']) ?></td>
<td><?= jsonToText1($row['khata'], 'old') ?? 'null' ?></td>
<td><?= jsonToText1($row['khata'], 'new') ?? 'null' ?></td>
<td><?= htmlspecialchars($row['rate_per_katha'] ?? '') ?></td>
<td><?= htmlspecialchars($row['total_land_value'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nom_name'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nom_mobile'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nom_address'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nom_pin'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nom_state'] ?? '') ?></td>

                                                                <td>
                                                                    <a href="?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                                    </form>
                                                                    <a href="landownerdetails.php?landid=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Print</a>
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

                    </div>

                    <?php include "adminfooter.php"; ?>
                </div>




            </div>

            <a href="#" target="_blank">
                <!-- partial -->
            </a>
            <!-- search box for options-->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

   <script>
$(document).ready(function(){

    $('#land_owner_name').on('change', function(){ // use change instead of input
        let val = $(this).val().trim();

        // Find the matching option
        let option = $("#designationList option").filter(function() {
            return $(this).val() === val;
        });

        if(option.length){
            let id = option.data('id');
            console.log("Selected ID:", id);

            $.ajax({
                url: '<?= $_SERVER['PHP_SELF']; ?>',
                type: 'POST',
                dataType: 'json',
                data: { action: 'fetch_land_owner', id: id },
                success: function(response){
                    console.log(response);
                    if(response.status === 'success'){
                        console.log(response.data.land_owner_name);
                        
                        $('input[name="land_owner_name"]').val(response.data.land_owner_name);
                         $('input[name="relation_name"]').val(response.data.relation_name);
                        $('input[name="lo_dob"]').val(response.data.lo_dob);
                        $('input[name="lo_state"]').val(response.data.lo_state);
                        $('input[name="lo_district"]').val(response.data.lo_district);
                        $('input[name="lo_block"]').val(response.data.lo_block);
                        $('input[name="lo_panchayat"]').val(response.data.lo_panchayat);
                        $('input[name="lo_village"]').val(response.data.lo_village);
                        $('input[name="lo_aadhar"]').val(response.data.lo_aadhar);
                        $('input[name="lo_pan"]').val(response.data.lo_pan);
                        $('input[name="address"]').val(response.data.address);
                        $('input[name="mobile1"]').val(response.data.mobile1);
                        $('input[name="mobile2"]').val(response.data.mobile2);
                        $('input[name="nominee_name"]').val(response.data.nom_name);
                        $('input[name="nominee_mobile"]').val(response.data.nom_mobile);
                        $('input[name="nominee_address"]').val(response.data.nom_address);
                        
                    } else {
                        alert('Data not found!');
                    }
                }
            });
        } else {
            console.log("No matching option found for:", val);
        }
    });

});
</script>


    <script>
        // Show/hide bank name field based on payment mode
        document.getElementById('paymentMode').addEventListener('change', function() {
            const bankField = document.getElementById('bankNameField');
            const bankAccountField = document.getElementById('bankAccountField');
            const bankIfscField = document.getElementById('bankIfscField');
            if (this.value === 'bank') {
                bankField.style.display = 'block';
                bankAccountField.style.display = 'block';
                bankIfscField.style.display = 'block';
                bankField.querySelector('input').required = true;
                bankAccountField.querySelector('input').required = true;
                bankIfscField.querySelector('input').required = true;
            } else {
                bankField.style.display = 'none';
                bankAccountField.style.display = 'none';
                bankIfscField.style.display = 'none';
                bankField.querySelector('input').required = false;
                bankAccountField.querySelector('input').required = false;
                bankIfscField.querySelector('input').required = false;
            }
        });
    </script>
   <script>
$(document).ready(function () {

    $('#num_o_k').on('change', function () {

        let selectedValue = parseInt($(this).val()) || 0;

        // Clear old fields
        $('#khesraFields').html('');
        $('#rakwaFields').html('');
        $('#rateFields').html('');

        for (let i = 1; i <= selectedValue; i++) {
            $('#khesraFields').append(`
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Khesra No. ${i}</label>
                    <input type="text"
                           class="form-control"
                           name="khesra_no[]"
                           placeholder="Enter Khesra No. ${i}">
                </div>
            `);

            $('#rakwaFields').append(`
             
    <div class="col align-items-end mb-3 col-md-6">
       <label class="form-label fw-semibold mb-2"><strong>Rakuwa ${i}</strong></label>
       <div class="row align-items-end mb-3 col-md-12">
        <div class="col-md-3">
            <label class="form-label fw-semibold">Bigha</label>
            <input type="number" step="any" class="form-control" name="rakuwa[${i}][bigha]" placeholder="0">
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Kattha</label>
            <input type="number" step="any" class="form-control" name="rakuwa[${i}][kattha]" placeholder="0">
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Dhoor</label>
            <input type="number" step="any" class="form-control" name="rakuwa[${i}][dhoor]" placeholder="0">
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Kadi</label>
            <input type="number" step="any" class="form-control" name="rakuwa[${i}][kadi]" placeholder="0">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Decimal</label>
            <input type="number" step="any" class="form-control" name="rakuwa[${i}][decimal]" placeholder="0">
        </div>
 </div>
    </div>
`);


                 $('#khataFields').append(`
               <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Khata Number ${i}</label>
                                                                            <input type="number" step="0.01" class="form-control" name="khata[]" placeholder="Enter Khata Number - ${i}"">
                                                                        </div>  
            `);

           $('#rateFields').append(`
               <div class="col-md-4 mb-3">
                                                                            <label class="form-label fw-semibold">Rate Per Kattha ${i}</label>
                                                                            <input type="number" step="0.01" class="form-control" name="rate_per_katha[]" placeholder="Enter Rate Per Katha - ${i}"">
                                                                        </div>  
            `);
    

         
        }
    });

});
</script>

   
        <script>
document.getElementById('rakuwa').addEventListener('input', function () {
    let val = this.value;

    // allow only digits + one dot
    val = val.replace(/[^0-9.]/g, '');

    // allow only one dot
    const parts = val.split('.');
    if (parts.length > 2) {
        val = parts[0] + '.' + parts.slice(1).join('');
    }

    // ❌ integer not allowed → must contain dot
    if (!val.includes('.') && val.length > 0) {
        this.setCustomValidity("Only decimal value allowed (e.g. 10.25)");
    } else {
        this.setCustomValidity("");
    }

    this.value = val;
});
</script>
    


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>