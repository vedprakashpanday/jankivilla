<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}

// error_reporting(E_ALL);
// ini_set('display_errors', 1);


try {
    // Prepare and execute the query to sum payamount for the specific member
    $stmt = $pdo->prepare("SELECT SUM(payamount) as total_self_business 
                          FROM tbl_customeramount");
    // $stmt->bindParam(':member_id', $sponsor_id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalSelfBusiness = $result['total_self_business'] ?? 0; // Default to 0 if no records

    // Format the number with 2 decimal places
    $formattedTotal = number_format($totalSelfBusiness, 2);
} catch (PDOException $e) {
    // Handle error (you might want to log this in production)
    $formattedTotal = "0.00";
    // echo "Error: " . $e->getMessage(); // Uncomment for debugging
}



try {
    // Prepare and execute the query to sum net_amount for the specific member
    $stmt = $pdo->prepare("SELECT SUM(net_amount) as total_business 
                          FROM tbl_customeramount");
    // $stmt->bindParam(':member_id', $sponsor_id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalBusiness = $result['total_business'] ?? 0; // Default to 0 if no records

    // Format the number with 2 decimal places
    $formattedTotalBusiness = number_format($totalBusiness, 2);
} catch (PDOException $e) {
    // Handle error (you might want to log this in production)
    $formattedTotalBusiness = "0.00";
    // echo "Error: " . $e->getMessage(); // Uncomment for debugging
}



try {
    // Prepare and execute the query to sum due_amount for the specific member
    $stmt = $pdo->prepare("SELECT SUM(due_amount) as total_due_amount 
                          FROM tbl_customeramount");
    // $stmt->bindParam(':member_id', $sponsor_id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalDueAmount = $result['total_due_amount'] ?? 0; // Default to 0 if no records

    // Format the number with 2 decimal places
    $formattedDueTotal = number_format($totalDueAmount, 2);
} catch (PDOException $e) {
    // Handle error (you might want to log this in production)
    $formattedDueTotal = "0.00";
    // echo "Error: " . $e->getMessage(); // Uncomment for debugging
}


// Get total unique members count
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT mem_sid) as total_members FROM tbl_regist");
$stmt->execute();
$total_members = $stmt->fetch(PDO::FETCH_ASSOC)['total_members'];

// Your original level 1 members query remains unchanged
$level1_stmt = $pdo->prepare("SELECT r.mem_sid, r.m_name, r.sponsor_id, r.s_name, p.package, p.status, r.date_time
FROM tbl_regist r
LEFT JOIN tbl_package p ON r.mem_sid = p.member_id
WHERE r.sponsor_id = :sponsor_id");
$level1_stmt->bindParam(':sponsor_id', $sponsor_id);
$level1_stmt->execute();
$level1_members = $level1_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($level1_members as $member) {
    // Your existing loop code here
}
?>


<?php
// Get sponsor id from session
$sponsor_id = $_SESSION['sponsor_id'];

// Function to get members count for a specific level
function getLevelCount($pdo, $sponsor_ids, $level)
{
    if (empty($sponsor_ids)) return 0;

    $placeholders = rtrim(str_repeat('?,', count($sponsor_ids)), ',');
    $query = "SELECT COUNT(DISTINCT mem_sid) as count 
              FROM tbl_regist 
              WHERE sponsor_id IN ($placeholders)";

    $stmt = $pdo->prepare($query);
    $stmt->execute($sponsor_ids);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Get Level 1 members
$level1_stmt = $pdo->prepare("SELECT DISTINCT mem_sid, sponsor_id 
                              FROM tbl_regist 
                              WHERE sponsor_id = ?");
$level1_stmt->execute([$sponsor_id]);
$level1_members = $level1_stmt->fetchAll(PDO::FETCH_ASSOC);
$level1_count = count($level1_members);
$level1_sponsor_ids = array_column($level1_members, 'mem_sid');

// Get counts for subsequent levels
$level_counts = [$level1_count];
$current_sponsor_ids = $level1_sponsor_ids;

for ($i = 2; $i <= 10; $i++) {
    $count = getLevelCount($pdo, $current_sponsor_ids, $i);
    $level_counts[] = $count;

    if ($count > 0) {
        $placeholders = rtrim(str_repeat('?,', count($current_sponsor_ids)), ',');
        $stmt = $pdo->prepare("SELECT DISTINCT mem_sid 
                              FROM tbl_regist 
                              WHERE sponsor_id IN ($placeholders)");
        $stmt->execute($current_sponsor_ids);
        $current_sponsor_ids = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'mem_sid');
    } else {
        $current_sponsor_ids = [];
    }
}


try {

    // SQL query to get the sum of total_commission
    $sql = "SELECT SUM(total_commission) as total_commission_sum 
            FROM commission_history";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the total commission value, default to 0 if null
    $totalCommission = $result['total_commission_sum'] ?? 0.00;

    // Format the number with 2 decimal places
    $formattedCommission = number_format($totalCommission, 2, '.', '');
} catch (PDOException $e) {
    // Handle any database errors
    $formattedCommission = "0.00";
    error_log("Error calculating total commission: " . $e->getMessage());
}



try {
    // Assuming $pdo is already defined and connected to your database

    // SQL query to get the sum of total_commission
    $sql = "SELECT SUM(total_commission) as total_commission_sum 
            FROM commission_history";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the total commission value, default to 0 if null
    $totalCommission = $result['total_commission_sum'] ?? 0.00;

    // Calculate 5% admin amount
    $adminAmount = $totalCommission * 0.05;
    $tdsamount = $totalCommission * 0.05;

    // Format both numbers with 2 decimal places
    $formattedTotalCommission = number_format($totalCommission, 2, '.', '');
    $formattedAdminAmount = number_format($adminAmount, 2, '.', '');
    $formattedtdsamount = number_format($tdsamount, 2, '.', '');
    $finalamount = $formattedAdminAmount + $formattedtdsamount;
    $membercommission = $formattedCommission - $finalamount;
} catch (PDOException $e) {
    // Handle any database errors
    $formattedTotalCommission = "0.00";
    $formattedAdminAmount = "0.00";
    $formattedtdsamount = "0.00";
    $membercommission = "0.00";
    error_log("Error calculating commissions: " . $e->getMessage());
}
?>





<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers | Super Admin Dashboard
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

<body class="hold-transition skin-blue sidebar-mini ">
    <form method="post" action="" id="form1" style="margin: unset;">


        <div class="wrapper">
            <div class="container-scroller">


                <!-- partial -->
                <div class="container-fluid page-body-wrapper">

                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">

                        <div class="" style="background: #fff; padding: 10px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f; width: 100%;">
                            <p style="color: black; font-size: 20px; font-family: 'Arial Rounded MT'; text-align: center">SUPER ADMIN DASHBOARD</p>
                        </div>
                        <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Total Business</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id="ContentPlaceHolder1_Lbltotalbusiness"><?php echo $formattedTotalBusiness; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Paid Amount</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id="ContentPlaceHolder1_lblpaidamount"><?php echo $formattedTotal; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Due Amount</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id="ContentPlaceHolder1_lblDuesamount"><?php echo $formattedDueTotal; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Total Commission</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">
                                                ₹<span id="ContentPlaceHolder1_lbltotalcommission"><?php echo $formattedCommission; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Admin Amount</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">
                                                ₹<span id="ContentPlaceHolder1_lbladminamount"><?php echo $formattedAdminAmount; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Tds Amount</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id="ContentPlaceHolder1_lbltdsamount"><?php echo $formattedtdsamount; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12 card-box3">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Total Member Income</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id="ContentPlaceHolder1_lblmemcommision"><?= $membercommission; ?></span>
                                            </span>
                                        </div>
                                    </div>


                                </div>

                                <div class="col-md-6">

                                    <div class="col-md-12 col-sm-12 col-12 card-box3" style="background-color: #003e27">
                                        <div style="display: flex; align-items: center; gap: 10px; padding-top: 20px; height: 38px">
                                            <p style="font-size: 18px; margin: 0;"><i class="ti-user"></i>Total Member</p>
                                            <span style="font-size: 20px; font-weight: bold; margin-left: auto;">
                                                <span id="ContentPlaceHolder1_lbltotalmember"><?php echo $total_members; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row" style="padding:0px 12px 0px 12px">
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_1admin.php"><span id="ContentPlaceHolder1_lbl1total"><?php echo $level_counts[0]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 1</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_02admin.php"><span id="ContentPlaceHolder1_totlevel2"><?php echo $level_counts[1]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 2</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Repeat similar structure for levels 3-10 -->
                                    <div class="row" style="padding:0px 12px 0px 12px">
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_03admin.php"><span id="ContentPlaceHolder1_totlevel3"><?php echo $level_counts[2]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 3</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_04admin.php"><span id="ContentPlaceHolder1_totlevel4"><?php echo $level_counts[3]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 4</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Continue this pattern for levels 5-10 -->
                                    <div class="row" style="padding:0px 12px 0px 12px">
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_05admin.php"><span id="ContentPlaceHolder1_totlevel5"><?php echo $level_counts[4]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 5</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_06admin.php"><span id="ContentPlaceHolder1_totlevel6"><?php echo $level_counts[5]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 6</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row" style="padding:0px 12px 0px 12px">
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_07admin.php"><span id="ContentPlaceHolder1_totlevel7"><?php echo $level_counts[6]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 7</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_08admin.php"><span id="ContentPlaceHolder1_totlevel8"><?php echo $level_counts[7]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 8</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row" style="padding:0px 12px 0px 12px">
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_09admin.php"><span id="ContentPlaceHolder1_totlevel9"><?php echo $level_counts[8]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 9</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6 card-box1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td width="220">
                                                            <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                        </td>
                                                        <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                            <a href="Label_10admin.php"><span id="ContentPlaceHolder1_totlevel10"><?php echo $level_counts[9]; ?></span></a>
                                                        </td>
                                                    </tr>
                                                    <tr height="30">
                                                        <td>
                                                            <p style="font-weight:bold;font-size:17px">Level 10</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="row justify-content-center">
                                <div class="col-md-12 details-box">
                                    <h3 class="text-center mb-4">My Profile</h3>
                                    <div class="details-section">

                                        <div class="row">

                                            <div class="col-md-6 detail-item">
                                                <p style="color: #222; margin-top: 10px">
                                                    <b>User Name : </b>
                                                    <span id="ContentPlaceHolder1_lblname" style="margin-left: 10px">Dharama(admin)</span>
                                                </p>
                                            </div>
                                            <div class="col-md-6 detail-item">
                                                <p style="color: #222; margin-top: 10px">
                                                    <b>User Id : </b>
                                                    <span id="ContentPlaceHolder1_lbluserid" style="margin-left: 10px">HHD000001</span>
                                                </p>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <div class="col-md-6 detail-item">

                                                <b>Registration Date : </b>
                                                <span id="ContentPlaceHolder1_lblregdate" style="margin-left: 10px">8/9/2024 6:02:10 PM</span>

                                            </div>
                                            <div class="col-md-6 detail-item">

                                                <b>Mobile No : </b>
                                                <span id="ContentPlaceHolder1_lblmob" style="margin-left: 10px">7070521500</span>



                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6 detail-item">

                                                <b>Email : </b>
                                                <span id="ContentPlaceHolder1_lblmailid" style="margin-left: 10px">harihomes34@gmail.com</span>

                                            </div>
                                            <div class="col-md-6 detail-item">

                                                <b>Address : </b>
                                                <span id="ContentPlaceHolder1_lblAddress" style="margin-left: 10px"></span>


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

            <style>
                .card-box3 {
                    background: #242b47;
                    height: 60px;
                    border: 2px solid #fff;
                    box-shadow: 1px 3px 12px 4px #988f8f;
                    margin-bottom: 15px;
                    border-radius: 10px;
                    color: white;
                    transition: background-color 0.3s ease;
                }

                .card-box {
                    background: #242b47;
                    height: 150px;
                    border: 2px solid #fff;
                    box-shadow: 1px 3px 12px 4px #988f8f;
                    margin-bottom: 15px;
                    border-radius: 10px;
                    color: white;
                    transition: background-color 0.3s ease;
                }

                .card-box1 {
                    background: #003e27;
                    height: 77px;
                    border: 2px solid #fff;
                    box-shadow: 1px 3px 12px 4px #988f8f;
                    margin-bottom: 15px;
                    border-radius: 10px;
                    color: white;
                    transition: background-color 0.3s ease;
                }

                .card-box:hover {
                    background-color: #ff9027;
                    /* Background color on hover */
                    color: black;
                }

                .icon-text-wrapper p {
                    margin: 0;
                }

                .details-box {
                    background: #fff;
                    padding: 40px;
                    border: 2px solid #fff;
                    box-shadow: 1px 3px 12px 4px #988f8f;
                    border-radius: 10px;
                }

                .details-section {
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }

                .detail-item {
                    border-bottom: 1px solid #ddd;
                    padding: 15px 0;
                    font-size: 16px;
                }

                b {
                    font-size: 17px;
                    font-weight: 600;
                    color: #242b47;
                }

                p {
                    font-size: 16px;
                    font-weight: 400;
                    color: white;
                }

                .text-center {
                    text-align: center;
                }

                .mb-4 {
                    margin-bottom: 30px;
                }
            </style>



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





            <div style="margin-left:250px">
                <span id="lblMsg"></span>
            </div>
            <style>
                #lblMsg {
                    visibility: hidden;
                }
            </style>





    </form>