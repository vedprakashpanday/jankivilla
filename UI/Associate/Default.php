<?php
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


$sponsor_id = $_SESSION['sponsor_id']; // change this to session variable
$registdata = $pdo->prepare("SELECT * FROM tbl_regist WHERE mem_sid = :sponsor_id");
$registdata->bindParam(':sponsor_id', $sponsor_id);
$registdata->execute();
$fetchmember = $registdata->fetch(PDO::FETCH_ASSOC);

$memberid = $fetchmember['mem_sid'];
$membername = $fetchmember['m_name'];
$membermobile = $fetchmember['m_num'];
$memberemail = $fetchmember['m_email'];
$memberaddress = $fetchmember['address'];
$memberdate = $fetchmember['date_time'];



try {
    // Prepare and execute the query to sum payamount for the specific member
    $stmt = $pdo->prepare("SELECT SUM(payamount) as total_self_business 
                          FROM tbl_customeramount 
                          WHERE member_id = :member_id");
    $stmt->bindParam(':member_id', $sponsor_id);
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
    // Prepare and execute the query to sum due_amount for the specific member
    $stmt = $pdo->prepare("SELECT SUM(due_amount) as total_due_amount 
                          FROM tbl_customeramount 
                          WHERE member_id = :member_id");
    $stmt->bindParam(':member_id', $sponsor_id);
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


// try {
//     // Prepare and execute the query to sum net_amount for the specific member
//     $stmt = $pdo->prepare("SELECT SUM(net_amount) as total_business 
//                           FROM tbl_customeramount 
//                           WHERE member_id = :member_id");
//     $stmt->bindParam(':member_id', $sponsor_id);
//     $stmt->execute();

//     // Fetch the result
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);
//     $totalBusiness = $result['total_business'] ?? 0; // Default to 0 if no records

//     // Format the number with 2 decimal places
//     $formattedTotalBusiness = number_format($totalBusiness, 2);
// } catch (PDOException $e) {
//     // Handle error (you might want to log this in production)
//     $formattedTotalBusiness = "0.00";
//     // echo "Error: " . $e->getMessage(); // Uncomment for debugging
// }


try {
    // Step 1: Get all team members where this member is the sponsor
    $stmt_team = $pdo->prepare("SELECT mem_sid 
                               FROM tbl_regist 
                               WHERE sponsor_id = :sponsor_id");
    $stmt_team->bindParam(':sponsor_id', $sponsor_id);
    $stmt_team->execute();
    $team_members = $stmt_team->fetchAll(PDO::FETCH_COLUMN); // Gets array of mem_sid

    // If no team members, set total to 0
    if (empty($team_members)) {
        $formattedTeamBusiness = "0.00";
    } else {
        // Step 2: Sum payamount for all team members from tbl_customeramount
        // Using IN clause with placeholders
        $placeholders = str_repeat('?,', count($team_members) - 1) . '?';
        $stmt_business = $pdo->prepare("SELECT SUM(payamount) as team_business 
                                      FROM tbl_customeramount 
                                      WHERE member_id IN ($placeholders)");

        // Bind all team member IDs
        foreach ($team_members as $index => $member_id) {
            $stmt_business->bindValue($index + 1, $member_id);
        }

        $stmt_business->execute();
        $result = $stmt_business->fetch(PDO::FETCH_ASSOC);
        $totalTeamBusiness = $result['team_business'] ?? 0; // Default to 0 if no records

        // Format the number with 2 decimal places
        $formattedTeamBusiness = number_format($totalTeamBusiness, 2);
    }
} catch (PDOException $e) {
    // Handle error (log in production)
    $formattedTeamBusiness = "0.00";
    // echo "Error: " . $e->getMessage(); // Uncomment for debugging
}
?>

<?php
// Get sponsor id from session
$sponsor_id = $_SESSION['sponsor_id'];

// Function to get members for a specific level with details
function getLevelMembers($pdo, $sponsor_ids)
{
    if (empty($sponsor_ids)) return [];

    $placeholders = rtrim(str_repeat('?,', count($sponsor_ids)), ',');
    $query = "SELECT DISTINCT mem_sid, m_name, sponsor_id, s_name, date_time 
              FROM tbl_regist 
              WHERE sponsor_id IN ($placeholders)
              ORDER BY date_time DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($sponsor_ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get total members count under this sponsor
$total_stmt = $pdo->prepare("
    WITH RECURSIVE member_hierarchy AS (
        SELECT mem_sid, sponsor_id
        FROM tbl_regist
        WHERE sponsor_id = ?
        UNION ALL
        SELECT r.mem_sid, r.sponsor_id
        FROM tbl_regist r
        INNER JOIN member_hierarchy mh ON r.sponsor_id = mh.mem_sid
    )
    SELECT COUNT(DISTINCT mem_sid) as total 
    FROM member_hierarchy
");
$total_stmt->execute([$sponsor_id]);
$total_members = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get Level 1 members
$level1_stmt = $pdo->prepare("
    SELECT DISTINCT mem_sid, m_name, sponsor_id, s_name, date_time 
    FROM tbl_regist 
    WHERE sponsor_id = ?
    ORDER BY date_time DESC
");
$level1_stmt->execute([$sponsor_id]);
$level1_members = $level1_stmt->fetchAll(PDO::FETCH_ASSOC);
$level_counts[1] = count($level1_members);
$level_members[1] = $level1_members;
$level1_sponsor_ids = array_column($level1_members, 'mem_sid');

// Get members for subsequent levels (up to 10)
$current_sponsor_ids = $level1_sponsor_ids;
for ($i = 2; $i <= 10; $i++) {
    $members = getLevelMembers($pdo, $current_sponsor_ids);
    $level_counts[$i] = count($members);
    $level_members[$i] = $members;

    if (empty($members)) {
        $current_sponsor_ids = [];
        $level_counts[$i] = 0; // Ensure count is set even if no members
    } else {
        $current_sponsor_ids = array_column($members, 'mem_sid');
    }
}



try {

    // SQL query to get the sum of total_commission
    $sql = "SELECT SUM(total_commission) as total_commission_sum 
            FROM commission_history where member_id = :mid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mid', $sponsor_id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the total commission value, default to 0 if null
    $totalCommission = $result['total_commission_sum'] ?? 0.00;

    // Format the number with 2 decimal places
    $formattedCommission = number_format($totalCommission, 2, '.', '');

    $tds = $formattedCommission * 0.05;
    $admin_charge = $formattedCommission * 0.05;
    $final_amount = $tds + $admin_charge;

    $myincome = $formattedCommission - $final_amount;
} catch (PDOException $e) {
    // Handle any database errors
    $formattedCommission = "0.00";
    $myincome = "0.00";
    error_log("Error calculating total commission: " . $e->getMessage());
}



try {

    // SQL query to get the sum of total_commission
    $sql = "SELECT SUM(total_commission) as total_commission_sum 
            FROM commission_history where member_id = :mid and payment_status = 'paid'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mid', $sponsor_id);
    $stmt->execute();

    // Fetch the result
    $fetchresult = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get the total commission value, default to 0 if null
    $totalCommissiondata = $fetchresult['total_commission_sum'] ?? 0.00;

    // Format the number with 2 decimal places
    $formattedCommissiondata = number_format($totalCommissiondata, 2, '.', '');

    $tdsdata = $formattedCommissiondata * 0.05;
    $admin_chargedata = $formattedCommissiondata * 0.05;
    $final_amountdata = $tdsdata + $admin_chargedata;

    $myincomedata = $formattedCommissiondata - $final_amountdata;
} catch (PDOException $e) {
    // Handle any database errors
    $formattedCommissiondata = "0.00";
    $myincomedata = "0.00";
    error_log("Error calculating total commission: " . $e->getMessage());
}


// Get logged-in member's ID from session
$sponsor_id = $_SESSION['sponsor_id'] ?? 'HHD000001'; // Use your session variable name

// Get date filters if available
$from_date = $_POST['from_date'] ?? '2000-01-01';
$to_date = $_POST['to_date'] ?? '2099-12-31';

// Fetch logged-in member's self business
$self_business_query = "SELECT COALESCE(SUM(payamount), 0) 
                        FROM receiveallpayment 
                        WHERE member_id = :sponsor_id
                        AND created_date BETWEEN :from_date AND :to_date";
$stmt = $pdo->prepare($self_business_query);
$stmt->execute(['sponsor_id' => $sponsor_id, 'from_date' => $from_date, 'to_date' => $to_date]);
$self_business = (float)$stmt->fetchColumn();

// Fetch total group business from direct downlines
$downline_business_query = "SELECT COALESCE(SUM(total_group), 0)
                            FROM (
                                WITH RECURSIVE downline AS (
                                    SELECT mem_sid, sponsor_id 
                                    FROM tbl_regist 
                                    WHERE sponsor_id = :sponsor_id
                                    UNION ALL
                                    SELECT r.mem_sid, r.sponsor_id 
                                    FROM tbl_regist r
                                    INNER JOIN downline d ON r.sponsor_id = d.mem_sid
                                )
                                SELECT COALESCE(SUM(p.payamount), 0) as total_group
                                FROM downline d
                                LEFT JOIN receiveallpayment p ON d.mem_sid = p.member_id
                                    AND p.created_date BETWEEN :from_date AND :to_date
                            ) AS subquery";
$stmt = $pdo->prepare($downline_business_query);
$stmt->execute(['sponsor_id' => $sponsor_id, 'from_date' => $from_date, 'to_date' => $to_date]);
$downline_business = (float)$stmt->fetchColumn();

// Calculate total team business
$total_business = $self_business + $downline_business;
$formattedTotalBusiness = number_format($total_business, 2);

?>


<?php
// Get logged-in member's ID from session
$sponsor_id = $_SESSION['sponsor_id'] ?? 'HHD000001';

// Get date filters
$from_date = $_POST['from_date'] ?? '2000-01-01';
$to_date = $_POST['to_date'] ?? '2099-12-31';

// Calculate team business (excluding self business)
$team_business_query = "SELECT COALESCE(SUM(p.payamount), 0)
                        FROM (
                            WITH RECURSIVE downline AS (
                                SELECT mem_sid 
                                FROM tbl_regist 
                                WHERE sponsor_id = :sponsor_id
                                UNION ALL
                                SELECT r.mem_sid 
                                FROM tbl_regist r
                                INNER JOIN downline d ON r.sponsor_id = d.mem_sid
                            )
                            SELECT mem_sid FROM downline
                        ) AS team
                        LEFT JOIN receiveallpayment p 
                            ON team.mem_sid = p.member_id
                            AND p.created_date BETWEEN :from_date AND :to_date";

$stmt = $pdo->prepare($team_business_query);
$stmt->execute([
    'sponsor_id' => $sponsor_id,
    'from_date' => $from_date,
    'to_date' => $to_date
]);
$team_business = (float)$stmt->fetchColumn();
$formattedTeamBusiness = number_format($team_business, 2);
?>





<html xmlns="http://www.w3.org/1999/xhtml">

<head>
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

    <style>
        #level1_count,
        #level2_count,
        #level3_count,
        #level4_count,
        #level5_count,
        #level6_count,
        #level7_count,
        #level8_count,
        #level9_count,
        #level10_count {
            color: #fff;
        }
    </style>

</head>

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">

    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "associate-headersidepanel.php"; ?>

                <div class="main-panel">

                    <div style="background:#fff;  border: 2px solid #fff; padding:10px; box-shadow: 1px 3px 12px 4px #988f8f; width: 100%;">
                        <p style="color: black; font-size: 20px; font-family: 'Arial Rounded MT'; text-align:center">ASSOCIATE DASHBOARD</p>
                    </div>

                    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">

                        <div class="row mb-4">

                            <div class="col-md-6">
                                <div class="col-md-12 col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;">
                                            <i class="ti-wallet"></i>Total Business
                                        </p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹
                                            <span id="totalBusiness"><?php echo $formattedTotalBusiness; ?></span>
                                        </span>
                                    </div>



                                </div>

                                <div class="col-md-12 col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;">
                                            <i class="ti-wallet"></i> Self Business
                                        </p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹
                                            <span id="selfBusinessTotal"><?php echo $formattedTotal; ?></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;">
                                            <i class="ti-wallet"></i> Team Business
                                        </p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹
                                            <span id="teamBusiness"><?php echo $formattedTeamBusiness; ?></span>
                                        </span>
                                    </div>
                                </div>


                                <div class="col-md-12  col-sm-12 col-12 card-box3">



                                    <!-- HTML Section with the calculated due amount -->
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;">
                                            <i class="ti-wallet"></i> Due Amount
                                        </p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹
                                            <span id="dueAmountTotal"><?php echo $formattedDueTotal; ?></span>
                                        </span>
                                    </div>



                                </div>


                                <div class="col-md-12  col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> My Income</p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id=""><?= $myincome; ?></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12  col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Admin Amount</p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id=""><?= $admin_charge ?? '0.00'; ?></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12  col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Tds Amount</p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id=""><?= $tds ?? '0.00'; ?></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12  col-sm-12 col-12 card-box3">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;"><i class="ti-wallet"></i> Paid Amount</p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">₹<span id=""><?= $myincomedata ?? '0.00'; ?></span>
                                        </span>
                                    </div>
                                </div>




                            </div>


                            <!-- HTML Display -->
                            <div class="col-md-6">
                                <div class="col-md-12 col-sm-12 col-12 card-box3" style="background-color:#003e27">
                                    <div style="display: flex; align-items: center; gap: 10px; padding-top:20px; height:38px">
                                        <p style="font-size: 18px; margin: 0;">
                                            <i class="ti-user"></i> Total Member
                                        </p>
                                        <span style="font-size: 20px; font-weight: bold; margin-left: auto;">
                                            <span id="total_members"><?php echo $total_members; ?></span>
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
                                                        <span id="level1_count"><?php echo $level_counts[1]; ?></span>
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
                                                        <span id="level2_count"><?php echo $level_counts[2]; ?></span>
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

                                <div class="row" style="padding:0px 12px 0px 12px">
                                    <div class="col-md-6 col-sm-6 col-6 card-box1">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td width="220">
                                                        <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                    </td>
                                                    <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                        <span id="level3_count"><?php echo $level_counts[3]; ?></span>
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
                                                        <span id="level4_count"><?php echo $level_counts[4]; ?></span>
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

                                <div class="row" style="padding:0px 12px 0px 12px">
                                    <div class="col-md-6 col-sm-6 col-6 card-box1">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td width="220">
                                                        <p style="font-size: 30px;padding-top:5px"><i class="ti-user"></i></p>
                                                    </td>
                                                    <td style="text-align: right; font-size: 20px; font-weight: bold; margin-left: auto;">
                                                        <span id="level5_count"><?php echo $level_counts[5]; ?></span>
                                                    </td>
                                                </tr>
                                                <tr height="30">
                                                    <td>
                                                        <p style="font-weight:bold;font-size:18px">Level 5</p>
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
                                                        <span id="level6_count"><?php echo $level_counts[6]; ?></span>
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
                                                        <span id="level7_count"><?php echo $level_counts[7]; ?></span>
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
                                                        <span id="level8_count"><?php echo $level_counts[8]; ?></span>
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
                                                        <span id="level9_count"><?php echo $level_counts[9]; ?></span>
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
                                                        <span id="level10_count"><?php echo $level_counts[10]; ?></span>
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




                        <div class="row justify-content-center mx-2">
                            <div class="col-md-12 details-box">
                                <h3 class="text-center mb-4">Associate Details</h3>
                                <div class="details-section">

                                    <div class="row">

                                        <div class="col-md-6 detail-item">
                                            <p style="color: #222; margin-top: 10px">
                                                <b>User Name : </b>
                                                <span id="" style="margin-left: 10px"><?= $membername; ?></span>
                                            </p>
                                        </div>
                                        <div class="col-md-6 detail-item">
                                            <p style="color: #222; margin-top: 10px">
                                                <b>User Id : </b>
                                                <span id="" style="margin-left: 10px"><?= $memberid; ?></span>
                                            </p>
                                        </div>


                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 detail-item">

                                            <b>Registration Date : </b>
                                            <span id="" style="margin-left: 10px"><?= $memberdate ?></span>

                                        </div>
                                        <div class="col-md-6 detail-item">

                                            <b>Mobile No : </b>
                                            <span id="" style="margin-left: 10px;"><?= $membermobile ?></span>



                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6 detail-item">

                                            <b>Email : </b>
                                            <span id="" style="margin-left: 10px"><?= $memberemail ?></span>

                                        </div>
                                        <div class="col-md-6 detail-item">

                                            <b>Address : </b>
                                            <span id="" style="margin-left: 10px"><?= $memberaddress ?></span>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include "associate-footer.php"; ?>

                </div>
            </div>
        </div>
    </div>



    <style>
        .card-box {
            background: #242b47;
            height: 80px;
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

        .card-box:hover {
            /*background-color: #ff9027;*/
            /* Background color on hover */
            color: white;
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


    <style>
        i {
            color: yellow;
        }
    </style>
    </form>


</body>

</html>