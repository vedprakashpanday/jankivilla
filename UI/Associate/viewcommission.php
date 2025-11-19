<?php
session_start();
include_once 'connectdb.php'; // Your PDO database connection file

// === 1. DESIGNATION → % MAP (SAME AS MAIN COMMISSION SCRIPT) ===
function getPercentByDesignation($pdo, $member_id)
{
    $stmt = $pdo->prepare("SELECT designation FROM tbl_regist WHERE mem_sid = ?");
    $stmt->execute([$member_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || empty($row['designation'])) {
        return 0;
    }

    $full = trim($row['designation']);
    $short = '';

    // Extract (A.M.O.), (S.E.), etc.
    if (preg_match('/\(([^)]+)\)/', $full, $matches)) {
        $short = strtoupper(str_replace(['.', ' '], '', $matches[1])); // (A.M.O.) → AMO
    }

    // FINAL MAP (MATCHES YOUR MAIN SYSTEM)
    $map = [
        'SE'  => 3,
        'SSE' => 2,
        'AMO' => 2,
        'MO'  => 1.5,
        'AMM' => 1.5,
        'MM'  => 1.5,
        'CMM' => 1.5,
        'AGM' => 1,
        'DGM' => 1,
        'GM'  => 1,
        'MD'  => 1,
        'FM'  => 1,
    ];

    return $map[$short] ?? 0;
}

// === 2. GET SPONSOR CHAIN WITH CORRECT % LOGIC ===
function getSponsorChain($pdo, $member_id, $payamount, $logged_in_member, $prev_percent = 0, $sponsor_chain = [], $seen_members = [])
{
    if (in_array($member_id, $seen_members)) {
        error_log("Cycle detected for member_id: $member_id");
        return $sponsor_chain;
    }
    $seen_members[] = $member_id;

    $query = "SELECT mem_sid, m_name, direct_commission_percent, sponsor_id FROM tbl_regist WHERE mem_sid = :member_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['member_id' => $member_id]);
    $current_member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$current_member) {
        error_log("No data found for member_id: $member_id");
        return $sponsor_chain;
    }

    // === PRIORITY 1: direct_commission_percent (if > 0) ===
    if (!empty($current_member['direct_commission_percent']) && $current_member['direct_commission_percent'] > 0) {
        $current_percent = floatval($current_member['direct_commission_percent']);
    }
    // === PRIORITY 2: designation → % map ===
    else {
        $current_percent = getPercentByDesignation($pdo, $member_id);
    }

    // === First member gets full %, uplines get difference ===
    $effective_percent = count($sponsor_chain) === 0 ? $current_percent : max(0, $current_percent - $prev_percent);
    $commission_amount = round(($payamount * $effective_percent) / 100, 2);

    $sponsor_chain[] = [
        'member_id' => $member_id,
        'm_name' => $current_member['m_name'] ?? 'N/A',
        'total_percent' => $current_percent,
        'commission_percent' => $effective_percent,
        'commission_amount' => $commission_amount
    ];

    // Stop if we reach the logged-in member
    if ($member_id === $logged_in_member) {
        return $sponsor_chain;
    }

    // Continue up the sponsor chain
    if (!empty($current_member['sponsor_id'])) {
        $sponsor_chain = getSponsorChain(
            $pdo,
            $current_member['sponsor_id'],
            $payamount,
            $logged_in_member,
            $current_percent,
            $sponsor_chain,
            $seen_members
        );
    }

    return $sponsor_chain;
}

// === 3. VALIDATE INPUTS ===
if (!isset($_GET['invoice_id']) || !isset($_GET['member_id'])) {
    echo "<div class='alert alert-danger'>Invalid request. Please provide invoice ID and member ID.</div>";
    exit;
}

if (!isset($_SESSION['sponsor_id'])) {
    echo "<div class='alert alert-danger'>You must be logged in to view commissions.</div>";
    exit;
}

$invoice_id = $_GET['invoice_id'];
$member_id = $_GET['member_id'];
$logged_in_member = $_SESSION['sponsor_id'];

// === 4. FETCH PAY AMOUNT ===
$query = "SELECT payamount FROM tbl_customeramount WHERE invoice_id = :invoice_id AND member_id = :member_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['invoice_id' => $invoice_id, 'member_id' => $member_id]);
$invoice_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice_data) {
    echo "<div class='alert alert-warning'>No invoice found for Invoice ID: $invoice_id and Member ID: $member_id.</div>";
    exit;
}

$payamount = floatval($invoice_data['payamount']);

// === 5. GET FINAL SPONSOR CHAIN ===
$sponsor_chain = getSponsorChain($pdo, $member_id, $payamount, $logged_in_member);

// === 6. DEBUG LOG (Optional - remove in production) ===
error_log("Final sponsor chain: " . json_encode($sponsor_chain));

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th {
            background-color: #f8f9fa;
        }

        .highlight {
            background-color: #d4edda !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3>Commission Details for Invoice ID: <?php echo htmlspecialchars($invoice_id); ?></h3>
        <p>
            <strong>Member ID:</strong> <?php echo htmlspecialchars($member_id); ?> |
            <strong>Pay Amount:</strong> ₹<?php echo number_format($payamount, 2); ?>
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Member/Sponsor ID</th>
                        <th>Member Name</th>
                        <th>Total %</th>
                        <th>Diff %</th>
                        <th>Commission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sponsor_chain)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-danger">No commission chain found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sponsor_chain as $i => $member): ?>
                            <tr <?php echo $i === 0 ? 'class="highlight"' : ''; ?>>
                                <td><strong><?php echo htmlspecialchars($member['member_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($member['m_name']); ?></td>
                                <td><?php echo number_format($member['total_percent'], 2); ?>%</td>
                                <td><?php echo number_format($member['commission_percent'], 2); ?>%</td>
                                <td><strong>₹<?php echo number_format($member['commission_amount'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>