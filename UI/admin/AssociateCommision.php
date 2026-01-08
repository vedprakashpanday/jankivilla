<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// error_reporting(1);
// error_reporting(E_ALL & ~E_NOTICE);


// if ($_SESSION['sponsor_id'] === $sponsorid && $_SESSION['sponsor_pass'] === $sponsorpass && $_SESSION['status'] === 'active') {

//     header('location:adminlogin.php');
// }
if (!empty($_SESSION['msg'])) {
    echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
    unset($_SESSION['msg']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['set'])) {
    if (!empty($_POST['designation']) && !empty($_POST['percent'])) {

        $designation = $_POST['designation'];
        $percent     = $_POST['percent'];

        $updateQuery = "INSERT INTO tbl_commision (`designation`, `commission`) 
                        VALUES (:designation, :commission)";
        
        $stmt = $pdo->prepare($updateQuery);

        $stmt->execute([
            ':designation' => $designation,
            ':commission'  => $percent
        ]);

        // $_SESSION['msg'] = "Commission added successfully!";
header("Location: ComissionSlab.php"); // same page
exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['update'])) {
    if (!empty($_POST['designation']) && !empty($_POST['percent'])) {

        $designation = $_POST['designation'];
        $percent     = $_POST['percent'];

        $updateQuery = "update tbl_commision set commission=:commission where designation=:designation";
        
        $stmt = $pdo->prepare($updateQuery);

        $stmt->execute([
            ':designation' => $designation,
            ':commission'  => $percent
        ]);

        // $_SESSION['msg'] = "Commission added successfully!";
header("Location: ComissionSlab.php"); // same page
exit;
    }
}



?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">

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


        <!-- CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->
         

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
        <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    </head>

<body class="hold-transition skin-blue sidebar-mini">
   

        <div class="wrapper">
            <div class="container-scroller">

                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper" style="padding:unset;">
                            <div class="col-md-12 stretch-card">
                                <div class="card">
                                    <div class="container">
                                        <div class="row justify-content-center">
                                            <div class="col-md-12" style="padding:unset;">
                                                <div class="">
                                                    <div class="container mt-4 col-12">
                                                        
                                                        <h2 class="text-center">Associate Commission</h2>
                                                       <form method="get">
                                                        <div class="parent d-flex">
                                                        <div class="col-5 my-3">
                                                            <label for="commissionDataList1" class="form-label ">Select Member</label>
                                                            <input class="form-control" list="datalistOptions1" id="commissionDataList1" placeholder="Search/Enter Designation" name="designation" value="">
                                                            <datalist id="datalistOptions1">

                                                            <?php 
                                                                $stmt= $pdo->prepare("SELECT m_name,mem_sid FROM tbl_regist");
                                                                $stmt->execute();
                                                                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                foreach($members as $member){
                                                                    echo '<option value="'.$member['mem_sid'].'" class="w-100">'.$member['m_name'].'</option>';
                                                                }

                                                            ?>                                                           
                                                                                                        
                                                            </datalist>
                                                            </div>
                                                            <div class="sdate col-3 my-3">
                                                            <label for="sdate" class="form-label ">Start Date</label>
                                                             <input class="form-control" id="sdate" type="date" name="sdate" >
                                                             </div>
                                                              <div class="edate col-3 my-3">
                                                            <label for="edate" class="form-label ">End Date</label>
                                                             <input class="form-control" id="edate"  type="date" name="edate" >
                                                             </div>
                                                        </div>
                                                             <div class="button ps-5 px-3" >
                                                                <input type="submit" value="Show" name="show" class="form btn-lg btn-primary my-4 rounded" style="margin-top:32px;" id="show">
                                                            </div>

                                                       </form>
                                                    </div>
                                                </div> <!-- p-4 shadow-sm -->





                                            <h2 class="border-bottom border-3">Commission Table</h2>
                                            <div class="overflow-auto">
                                                <table class="table table-striped table-hover my-5 overflow-auto" id="commissionTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Member Id</th>
                                                            <th>Member Name</th>
                                                            <th>Designation</th>
                                                            <th>Sponsor Id</th>
                                                            <th>Self Business(Product,Amount,Date,Commission)</th>
                                                            <th>Team Business</th>
                                                            <th>Total Business</th>
                                                            <th>Direct Commission %</th>
                                                            <th>Direct Commission</th>
                                                            <th>Level Commission(%)</th>
                                                            <th>Total Level Commision</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $id = $_GET['designation'] ?? '';
                                                        $sdate = $_GET['sdate'] ?? '';
                                                        $edate = $_GET['edate'] ?? '';
                                                        $finalcom =0;

                                                        
                                                         $stmt526= $pdo->prepare("SELECT mem_sid,m_name,sponsor_id,designation FROM tbl_regist WHERE mem_sid=:id");
                                                        $stmt526->execute([':id' => $id]);
                                                        $members526 = $stmt526->fetch(PDO::FETCH_ASSOC);

                                                        $ownerdesignation = $members526['designation'] ?? '';
                                                        $ownername = $members526['m_name'] ?? '';
                                                        $ownersponsorid = $members526['sponsor_id'] ?? '';


                                                                if (empty($sdate) && empty($edate)) {

                                                                        echo "<script>alert('Please select both Start Date and End Date');</script>";
                                                                        return; // stop execution
                                                                    }
                                                                    else if (empty($edate)) {

                                                                        echo "<script>alert('Please select End Date');</script>";
                                                                        return; // stop execution
                                                                    }
                                                                    else if (empty($sdate)) {

                                                                        echo "<script>alert('Please select Start Date');</script>";
                                                                        return; // stop execution
                                                                    }
                                                                  
                                                                        

                                                       $query = "
                                                            SELECT 
                                                                tbr.mem_sid,
                                                                tbr.m_name,
                                                                tbr.designation,
                                                                tbr.sponsor_id,
                                                                rap.productname,
                                                                rap.payamount,
                                                                rap.net_amount,
                                                                rap.created_date,
                                                                rap.created_date
                                                            FROM   receiveallpayment rap
                                                            LEFT JOIN tbl_regist tbr
                                                                ON rap.member_id = tbr.mem_sid
                                                            WHERE rap.member_id = :id
                                                            AND rap.created_date BETWEEN :sdate AND :edate
                                                            GROUP BY 
                                                                tbr.mem_sid,
                                                                tbr.m_name,
                                                                tbr.designation,
                                                                tbr.sponsor_id,
                                                                rap.productname
                                                            ORDER BY rap.productname ASC
                                                        ";



                                                        $stmt = $pdo->prepare($query);
                                                        $stmt->execute([
                                                            ':id' => $id,
                                                            ':sdate' => $sdate,
                                                            ':edate' => $edate
                                                        ]);

                                                        $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        // var_dump($commissions);
                                                        // exit();

                                                        if (!empty($commissions)) {

                                                                

                                                            foreach ($commissions as $c) {
                                                                echo "<tr>";
                                                                echo "<td>".($c['mem_sid'])."</td>";
                                                                echo "<td>".($c['m_name'])."</td>";
                                                                echo "<td>".($c['designation'])."</td>";
                                                                echo "<td>".($c['sponsor_id'])."</td>";

                                                                
                                                        // 1️⃣ Total Payamount for this product + member
    $stmt2 = $pdo->prepare("
    SELECT 
        COALESCE(SUM(payamount),0) + COALESCE(SUM(enrollment_charge),0) AS total
    FROM receiveallpayment
    WHERE productname = :product
    AND member_id = :mid
");

$stmt2->execute([
    ':product' => $c['productname'],
    ':mid'     => $c['mem_sid']
]);

$totalSelfBusiness = $stmt2->fetchColumn();

                                                                  

// INR FORMATTER (define only once)
if (! function_exists('inr'))  {
    function inr($number) {
        return "₹" . number_format((float)$number, 2, '.', ',');
    }
}


// 0️⃣ Fetch FULL designation timeline (ALWAYS use this for designation)
$sqlAll = "
    SELECT designations 
    FROM tbl_regist 
    WHERE mem_sid = ?
";

$stmtAll = $pdo->prepare($sqlAll);
$stmtAll->execute([$id]);
$row = $stmtAll->fetch(PDO::FETCH_ASSOC);

$fullDesignations = [];

// PHP JSON Parsing (MariaDB/XAMPP Compatible)
if ($row && !empty($row['designations'])) {
    $designationsArray = json_decode($row['designations'], true);
    
    // Check if JSON decoded successfully
    if (json_last_error() === JSON_ERROR_NONE && is_array($designationsArray)) {
        // Sort by date ASC (same as your ORDER BY)
        usort($designationsArray, function($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });
        
        foreach ($designationsArray as $item) {
            $fullDesignations[] = [
                'designation' => $item['designation'] ?? '',
                'date' => $item['date'] ?? ''
            ];
        }
    }
}



// 1️⃣ If full designations JSON empty → fallback to tbl_regist.designation
// if (!$fullDesignations) {
//     $fullDesignations = [
//         [
//             'designation' => $c['designation'],   // fallback to main column
//             'date'        => '1900-01-01'
//         ]
//     ];
// }



// 2️⃣ Fetch all payments inside given date range
$stmt3 = $pdo->prepare("
    SELECT payamount, created_date, net_amount,enrollment_charge
    FROM receiveallpayment
    WHERE productname = :product
    AND member_id   = :mid
    AND created_date BETWEEN :sdate AND :edate
    ORDER BY created_date ASC
");

$stmt3->execute([
    ':product' => $c['productname'],
    ':mid'     => $c['mem_sid'],
    ':sdate'   => $sdate,
    ':edate'   => $edate
]);

$records = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$stmt3526 = $pdo->prepare("
    SELECT SUM(enrollment_charge) AS enrollment_charge
    FROM receiveallpayment
    WHERE productname = :product
    AND member_id   = :mid
    AND created_date BETWEEN :sdate AND :edate
    ORDER BY created_date ASC
");

$stmt3526->execute([
    ':product' => $c['productname'],
    ':mid'     => $c['mem_sid'],
    ':sdate'   => $sdate,
    ':edate'   => $edate
]);

$records526 = (float) ($stmt3526->fetchColumn() ?? 0);


// 3️⃣ Function: ALWAYS determine designation from full timeline
if (!function_exists('getDesignationForDate')) {
    function getDesignationForDate($timeline, $date) {
        $current = $timeline[0]['designation'];  // fallback default

        foreach ($timeline as $d) {
            if ($date >= $d['date']) {
                $current = $d['designation'];    // update to latest valid
            }
        }

        return $current;
    }
}



// 4️⃣ PRINT OUTPUT
echo "<td>";

echo "<strong>" . inr($totalSelfBusiness) . "</strong><br>";
echo "<strong>" . ($c['productname']) . ":</strong><br>";

$totcom = 0;

$flag=0;

// 5️⃣ Loop through each payment and calculate correct commission
foreach ($records as $r) {

//     echo "<pre>";
// var_dump($records526);
// var_dump($records);
// var_dump($flag);
// echo "</pre>";
// // exit();
    $recordDate = $r['created_date'];

    // CORRECT DESIGNATION (full timeline logic)
    $designation = getDesignationForDate($fullDesignations, $recordDate);


    // Fetch commission % slab
    $stmt4 = $pdo->prepare("SELECT id FROM tbl_commision WHERE TRIM(designation)=TRIM(:designation)");
    $stmt4->execute([':designation' => $designation]);
    $comm = $stmt4->fetch(PDO::FETCH_ASSOC);

    $stmt5 = $pdo->prepare("SELECT commission FROM tbl_commision WHERE id <= :id");
    $stmt5->execute([':id' => $comm['id']]);
    $pc = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    $totalpercent = array_sum(array_column($pc, 'commission'));


    // Unlock logic
    $generate = $c['net_amount'] * 0.25;

    if ($totalSelfBusiness >= $generate) {
        
        if($records526>0 && $flag==0 && $r['payamount']>0){
            $commissionEarned = (($records526+$r['payamount']) * $totalpercent) / 100;
            $flag++;
        }
       else{  
        $commissionEarned = ($r['payamount'] * $totalpercent) / 100;
        }
    } else {
        $commissionEarned = 0;
    }

    $totcom += $commissionEarned;


    // Print row
    echo "<b>Amount:</b> " . inr($r['payamount'])
        . ", Date: " . date("d-m-Y", strtotime($r['created_date']))
        . ", <b>Commission(%)</b>: " . $totalpercent
        . ", <b>Commission</b>: " . inr($commissionEarned)
        . ", <b>Designation</b>: " . $designation
        . "<br>";
}


// Add to final
$finalcom += $totcom;

echo "</td>";


                                                                  
                                                                     
                                                                          $stmt6 = $pdo->prepare("
                                                                    SELECT SUM(rap.payamount) AS totalPay
                                                                    FROM receiveallpayment rap
                                                                    LEFT JOIN tbl_regist tbr 
                                                                        ON rap.member_id = tbr.mem_sid
                                                                    WHERE tbr.sponsor_id = :id
                                                                    and rap.productname = :product
                                                                ");
                                                                $stmt6->execute([
                                                                    ':id' => $c['mem_sid'],
                                                                    ':product' => $c['productname']
                                                                ]);

                                                                $totalSelfBusiness1 = $stmt6->fetchColumn() ?? 0;

                                                                echo "<td>" . ($totalSelfBusiness1) . "</td>";

                                                                echo "<td>".($totalSelfBusiness1)+($totalSelfBusiness)."</td>";
                                                                echo "<td>".($totalpercent)."</td>";
                                                                echo "<td>".($totcom)."</td>";
                                                                echo "<td>".($totalSelfBusiness1)."</td>";
                                                                echo "<td>".($totalSelfBusiness1)+($totcom)."</td>";
                                                                echo "</tr>";
                                                            }
                                                            
                                                        }

/* ================= BASIC ================= */

if (!function_exists('inr')) {
    function inr($n) {
        return "₹" . number_format((float)$n, 2, '.', ',');
    }
}

$sdate = date('Y-m-d', strtotime($sdate));
$edate = date('Y-m-d', strtotime($edate));

/* ================= STATEMENTS ================= */

$stmPaymentsGrouped = $pdo->prepare("
    SELECT SUM(payamount) AS totalPay, productname
    FROM receiveallpayment
    WHERE member_id = :mid
      AND created_date BETWEEN :sdate AND :edate
    GROUP BY productname
");

$stmPaymentsList = $pdo->prepare("
    SELECT payamount, created_date
    FROM receiveallpayment
    WHERE member_id = :mid
      AND productname = :product
      AND created_date BETWEEN :sdate AND :edate
    ORDER BY created_date ASC
");

$stmMember = $pdo->prepare("
    SELECT mem_sid, m_name, sponsor_id, designation, designations
    FROM tbl_regist WHERE mem_sid = :m
");

$stmCommByDes = $pdo->prepare("
    SELECT id, commission FROM tbl_commision
    WHERE TRIM(designation)=TRIM(:d)
");

$stmCommAtId = $pdo->prepare("
    SELECT commission FROM tbl_commision WHERE id=:id
");

$stmCommRange = $pdo->prepare("
    SELECT IFNULL(SUM(commission),0) total
    FROM tbl_commision WHERE id>:low AND id<=:high
");

/* ================= HELPERS ================= */

$desigIdCache = [];

function getDesignationId($desig, $stm, &$cache) {
    if (!$desig) return null;
    if (isset($cache[$desig])) return $cache[$desig];
    $stm->execute([':d'=>$desig]);
    $r = $stm->fetch(PDO::FETCH_ASSOC);
    return $cache[$desig] = $r ? (int)$r['id'] : null;
}

function getCommAt($id, $stm) {
    if (!$id) return 0;
    $stm->execute([':id'=>$id]);
    $r = $stm->fetch(PDO::FETCH_ASSOC);
    return $r ? (float)$r['commission'] : 0;
}

function getCommBetween($low,$high,$stm) {
    if ($high <= $low) return 0;
    $stm->execute([':low'=>$low,':high'=>$high]);
    $r = $stm->fetch(PDO::FETCH_ASSOC);
    return $r ? (float)$r['total'] : 0;
}

function getDesignationHistory(PDO $pdo,$mid) {
    $stm = $pdo->prepare("
        SELECT designation, designations FROM tbl_regist WHERE mem_sid=?
    ");
    $stm->execute([$mid]);
    $r = $stm->fetch(PDO::FETCH_ASSOC);

    $hist = [];
    if (!empty($r['designations'])) {
        $j = json_decode($r['designations'], true);
        if (is_array($j)) {
            foreach ($j as $it) {
                if (!empty($it['designation']) && !empty($it['date'])) {
                    $hist[] = [
                        'designation'=>$it['designation'],
                        'date'=>date('Y-m-d',strtotime($it['date']))
                    ];
                }
            }
            usort($hist, fn($a,$b)=>strtotime($a['date']) <=> strtotime($b['date']));
        }
    }
    return ['history'=>$hist,'fallback'=>$r['designation']];
}

function designationOnDate($hist,$fallback,$date) {
    $d = $fallback;
    foreach ($hist as $h) {
        if ($date >= $h['date']) $d = $h['designation'];
        else break;
    }
    return $d;
}

/* ================= OWNER ================= */

$stmMember->execute([':m'=>$id]);
$owner = $stmMember->fetch(PDO::FETCH_ASSOC);

$ownername      = $owner['m_name'];
$ownersponsorid = $owner['sponsor_id'];

$designationCache = [];
$designationCache[$id] = getDesignationHistory($pdo,$id);

/* ================= DOWNLINE ================= */

function getDownline(PDO $pdo,$root) {
    $all = [];
    $q = [$root];
    while ($q) {
        $p = array_shift($q);
        $stm = $pdo->prepare("SELECT mem_sid FROM tbl_regist WHERE sponsor_id=?");
        $stm->execute([$p]);
        foreach ($stm->fetchAll(PDO::FETCH_COLUMN) as $c) {
            if (!in_array($c,$all)) {
                $all[] = $c;
                $q[] = $c;
            }
        }
    }
    return $all;
}

$downlineMembers = getDownline($pdo,$id);

/* ================= MAIN ================= */

foreach ($downlineMembers as $memberId) {

    $stmPaymentsGrouped->execute([
        ':mid'=>$memberId,
        ':sdate'=>$sdate,
        ':edate'=>$edate
    ]);
    $groups = $stmPaymentsGrouped->fetchAll(PDO::FETCH_ASSOC);
    if (!$groups) continue;

    $designationCache[$memberId] = getDesignationHistory($pdo,$memberId);

    foreach ($groups as $grp) {

        $productName = $grp['productname'];

        $stmPaymentsList->execute([
            ':mid'=>$memberId,
            ':product'=>$productName,
            ':sdate'=>$sdate,
            ':edate'=>$edate
        ]);
        $payments = $stmPaymentsList->fetchAll(PDO::FETCH_ASSOC);
        if (!$payments) continue;

        /* 🔒 FIRST BUSINESS DATE */
        $firstBusinessDate = date('Y-m-d',strtotime($payments[0]['created_date']));

        /* 🔒 FREEZE DESIGNATIONS */
        $payerDesig = designationOnDate(
            $designationCache[$memberId]['history'],
            $designationCache[$memberId]['fallback'],
            $firstBusinessDate
        );
        $payerDesId = getDesignationId($payerDesig,$stmCommByDes,$desigIdCache);

        $ownerDesig = designationOnDate(
            $designationCache[$id]['history'],
            $designationCache[$id]['fallback'],
            $firstBusinessDate
        );
        $ownerDesId = getDesignationId($ownerDesig,$stmCommByDes,$desigIdCache);

        if (!$payerDesId || !$ownerDesId || $ownerDesId < $payerDesId) continue;

        if ($ownerDesId > $payerDesId) {
            $percent = getCommBetween($payerDesId,$ownerDesId,$stmCommRange);
        } else {
            $percent = getCommAt($ownerDesId,$stmCommAtId);
        }

        $paymentDetailsHtml = '';
        $finalcom1 = 0;

        foreach ($payments as $p) {

            $amt = (float)$p['payamount'];
            $dt  = date('d-m-Y',strtotime($p['created_date']));
            $comm = ($amt * $percent) / 100;
            $finalcom1 += $comm;

            $paymentDetailsHtml .=
                "Amount: ".inr($amt).
                ", Date: $dt".
                ", PayerDesig: <b>$payerDesig</b>".
                ", OwnerDesig: <b>$ownerDesig</b>".
                ", %: $percent".
                ", Comm: ".inr($comm)."<br>";
        }
        $finalcom += $finalcom1;

        /* ===== TABLE OUTPUT (AS PER YOUR TDs) ===== */

        echo "<tr>
            <td>$id</td>
            <td>$ownername</td>
            <td>{$owner['designation']}</td>
            <td>$ownersponsorid</td>
            <td>0</td>
            <td>$paymentDetailsHtml</td>
            <td>{$grp['totalPay']}</td>
            <td>0</td>
            <td>0</td>
            <td>$percent</td>
            <td>".inr($finalcom1)."</td>
        </tr>";
    }
}
?>



                                                        </tbody>

                                                    
                                                </table>
                                              
                                                </div>
                                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body d-flex justify-content-between">  
                                                <?php 
                                                $fcommission=0;
                                                $stmt523= $pdo->prepare("SELECT SUM(payamount) AS totalPay
                                                FROM receiveallpayment
                                                Where date(created_date) BETWEEN :sdate AND :edate");
                                                $stmt523->execute([':sdate' => $sdate, ':edate' => $edate]);
                                                $totalbusiness523 = $stmt523->fetch(PDO::FETCH_ASSOC);
                                                ?>
                                                <?php if($ownerdesignation == 'Founder Member (F.M.)'){ ?>
                                                    <div  style="display:flex; flex-direction:column; align-items:center; justify-content:start; padding:1rem;">
                                                <span class="text-start w-100"  style="padding: 1rem 0;" ><b>Overall Business Commission:</b> <?php echo $totalbusiness523['totalPay']*0.01; ?></span>
                                                 <span class="text-start w-100" style="padding: 1rem 0;" ><b>Total commission :</b> <?php echo $finalcom; ?></span>
                                                  <span class="text-start w-100"  style="padding: 1rem 0;" ><b>Founder Member's Total commission :</b> <?php
                                                  $fcommission=$finalcom+($totalbusiness523['totalPay']*0.01);
                                                  echo $fcommission; ?></span>
                                                    </div>
                                                <?php } else { ?>   
                                                <span  style="padding: 5rem 0;" ><b>Total commission :</b> <?php echo $finalcom; ?></span>
                                                <?php } ?>
                                                <span  style="padding: 5rem 0;" ><b>TDS(%) :</b> 5</span>

                                                  <?php if($ownerdesignation == 'Founder Member (F.M.)'){ ?>

                                                    <div>                                            
                                                  <span  style="padding: 5rem 0;" ><b>Final Total commission :</b> <?php
                                                  $fcommission=$fcommission - ($fcommission * 0.05);
                                                  echo $fcommission; ?></span>

                                                    </div>
                                                <?php } else { ?>   
                                                <span  style="padding: 5rem 0;" ><b>Final Total commission :</b> <?php
                                                $finalcom = $finalcom - ($finalcom * 0.05);
                                                 echo $finalcom; ?></span>
                                                <?php } ?>
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'adminfooter.php'; ?>

            </div>
            <a href="#" target="_blank">
                <!-- partial -->
            </a>
            <!-- search box for options-->
            <!-- jQuery (required for DataTables) -->
            <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
            <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
            <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

            <!-- <script src="../resources/vendors/js/vendor.bundle.base.js"></script> -->
            <!-- endinject -->
            <!-- Plugin js for this page -->
            <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
            <script src="../resources/vendors/select2/select2.min.js"></script>
            <!-- End plugin js for this page -->
            <!-- Plugin js for this page -->
            <script src="../resources/vendors/chart.js/Chart.min.js"></script>
            <!-- <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script> -->
            <!-- <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> -->
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