<?php
session_start();
include_once "connectdb.php";


$member_id = $_GET['member_id'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

if (empty($member_id) || empty($from_date) || empty($to_date)) {
    die('Invalid parameters');
}

// Step 1: Get TARGET MEMBER details
$target_member_query = "
    SELECT member_name, direct_percent, level_commission
    FROM commission_history 
    WHERE member_id COLLATE utf8mb4_general_ci = :member_id 
    AND from_date = :from_date 
    AND to_date = :to_date
";
$stmt = $pdo->prepare($target_member_query);
$stmt->execute([
    'member_id' => $member_id,
    'from_date' => $from_date,
    'to_date' => $to_date
]);
$target_member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target_member) {
    die('Member not found');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level Commission Breakdown | <?= htmlspecialchars($target_member['member_name']) ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Professional Styles -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --text-muted: #6c757d;
        }

        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .header-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .header-info {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        .card {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .total-commission {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--success-color);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table {
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .table th {
            background: var(--secondary-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table td {
            padding: 12px 8px;
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.03);
        }

        .combined-business-row {
            background-color: #e8f5e8;
            border-left: 4px solid var(--success-color);
        }

        .self-business-row {
            background-color: #d1ecf1;
            border-left: 4px solid var(--info-color);
        }

        .team-business-row {
            background-color: #fff3cd;
            border-left: 4px solid var(--warning-color);
        }

        .zero-commission-row {
            background-color: #f8f9fa;
            border-left: 4px solid var(--text-muted);
        }

        .total-row {
            background: linear-gradient(90deg, var(--success-color), #2ecc71);
            color: white;
            font-weight: 600;
        }

        .business-amount {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .total-business {
            background: linear-gradient(90deg, var(--info-color), var(--warning-color));
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .percent-diff {
            color: var(--success-color);
            font-weight: 600;
        }

        .zero-percent {
            color: var(--text-muted);
            font-style: italic;
        }

        .commission-amount {
            color: var(--info-color);
            font-weight: 700;
            font-size: 1rem;
        }

        .zero-commission {
            color: var(--text-muted);
            font-weight: 500;
        }

        .calculation {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* EXPANDABLE ROW STYLES */
        .details-row {
            background-color: #f8f9fa;
            display: none;
        }

        .details-table {
            font-size: 0.8rem;
            margin: 0;
        }

        .details-table th {
            background: var(--info-color);
            color: white;
            padding: 8px 4px;
        }

        .details-table td {
            padding: 6px 4px;
            border-color: var(--border-color);
        }

        .view-btn {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        /* TEAM BUSINESS TABLE */
        .team-business-table th {
            background: var(--warning-color);
        }

        .sub-member-row {
            background-color: #fff8e1;
            border-left: 3px solid var(--warning-color);
        }

        .hierarchy-flow {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 5px solid var(--primary-color);
        }

        .hierarchy-member {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .hierarchy-upline {
            background: var(--info-color);
            color: white;
        }

        .hierarchy-downline {
            background: var(--success-color);
            color: white;
        }

        .hierarchy-zero {
            background: var(--text-muted);
            color: white;
        }

        .hierarchy-arrow {
            color: var(--text-muted);
            font-weight: bold;
            margin: 0 0.5rem;
        }

        .btn-back {
            background: var(--secondary-color);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-back:hover {
            background: var(--primary-color);
            transform: translateY(-1px);
            color: #fff;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.8rem;
            }

            .total-commission {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="header-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Level Commission Breakdown
                    </h1>
                    <p class="header-info mb-0">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($target_member['member_name']) ?>
                        (<?= $member_id ?>) | <i class="fas fa-percentage me-1"></i><?= $target_member['direct_percent'] ?>%
                        | <i class="fas fa-calendar me-1"></i><?= date('d-m-Y', strtotime($from_date)) ?> to <?= date('d-m-Y', strtotime($to_date)) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-body p-4">
                <!-- Total Commission Display -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0"><i class="fas fa-calculator text-muted me-2"></i>Total Level Commission</h5>
                    <div class="total-commission">₹<?= number_format($target_member['level_commission'], 2) ?></div>
                </div>

                <?php
                // Step 2: Get ALL DIRECT DOWNLINES
                $direct_downlines_query = "
                    SELECT 
                        ch.member_id,
                        ch.member_name,
                        ch.direct_percent,
                        ch.direct_amount as self_business,
                        ch.total_group_amount as team_business,
                        ch.payment_details
                    FROM commission_history ch
                    INNER JOIN tbl_regist tr ON ch.member_id COLLATE utf8mb4_general_ci = tr.mem_sid COLLATE utf8mb4_general_ci
                    WHERE tr.sponsor_id COLLATE utf8mb4_general_ci = :target_member
                    AND ch.from_date = :from_date 
                    AND ch.to_date = :to_date 
                    AND ch.status COLLATE utf8mb4_general_ci = 'closed'
                    ORDER BY ch.member_id
                ";

                $stmt = $pdo->prepare($direct_downlines_query);
                $stmt->execute(['target_member' => $member_id, 'from_date' => $from_date, 'to_date' => $to_date]);
                $direct_downlines = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Step 3: Get TEAM MEMBERS for each downline (if team_business > 0)
                $all_team_members = [];
                foreach ($direct_downlines as $downline) {
                    if (floatval($downline['team_business']) > 0) {
                        $team_members_query = "
                            SELECT 
                                ch.member_id,
                                ch.member_name,
                                ch.direct_amount as self_business,
                                ch.payment_details
                            FROM commission_history ch
                            INNER JOIN tbl_regist tr ON ch.member_id COLLATE utf8mb4_general_ci = tr.mem_sid COLLATE utf8mb4_general_ci
                            WHERE tr.sponsor_id COLLATE utf8mb4_general_ci = :downline_id
                            AND ch.from_date = :from_date 
                            AND ch.to_date = :to_date 
                            AND ch.status COLLATE utf8mb4_general_ci = 'closed'
                            AND ch.direct_amount > 0
                            ORDER BY ch.member_id
                        ";
                        $stmt = $pdo->prepare($team_members_query);
                        $stmt->execute([
                            'downline_id' => $downline['member_id'],
                            'from_date' => $from_date,
                            'to_date' => $to_date
                        ]);
                        $team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($team_members as $team_member) {
                            $team_member['payment_details'] = $team_member['payment_details'] ? json_decode($team_member['payment_details'], true) : [];
                            $all_team_members[$downline['member_id']][] = $team_member;
                        }
                    }
                }

                // Step 4: Calculate contributions
                $level_contributions = [];
                $target_percent = floatval($target_member['direct_percent']);
                $total_calculated = 0;

                foreach ($direct_downlines as $downline) {
                    $downline_percent = floatval($downline['direct_percent']);
                    $self_business = floatval($downline['self_business']);
                    $team_business = floatval($downline['team_business']);
                    $payment_details = $downline['payment_details'] ? json_decode($downline['payment_details'], true) : [];

                    $total_business = $self_business + $team_business;

                    if ($self_business > 0 && $team_business > 0) {
                        $business_type = 'Self+Team';
                    } elseif ($self_business > 0) {
                        $business_type = 'Self';
                    } elseif ($team_business > 0) {
                        $business_type = 'Team';
                    } else {
                        $business_type = 'None';
                    }

                    $diff_percent = $target_percent - $downline_percent;
                    $level_amount = 0;

                    if ($diff_percent > 0 && $total_business > 0) {
                        $level_amount = ($total_business * $diff_percent) / 100;
                        $total_calculated += $level_amount;
                    }

                    $level_contributions[] = [
                        'member_id' => $downline['member_id'],
                        'member_name' => $downline['member_name'],
                        'self_business' => $self_business,
                        'team_business' => $team_business,
                        'total_business' => $total_business,
                        'business_type' => $business_type,
                        'downline_percent' => $downline_percent,
                        'diff_percent' => $diff_percent,
                        'level_amount' => $level_amount,
                        'payment_details' => $payment_details,
                        'team_members' => $all_team_members[$downline['member_id']] ?? []
                    ];
                }
                ?>

                <!-- Breakdown Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i></th>
                                <th><i class="fas fa-user"></i> Downline</th>
                                <th><i class="fas fa-shopping-cart"></i> Self</th>
                                <th><i class="fas fa-users"></i> Team</th>
                                <th><i class="fas fa-plus"></i> Total</th>
                                <th><i class="fas fa-check-circle"></i> Used</th>
                                <th><i class="fas fa-percentage"></i> %</th>
                                <th><i class="fas fa-minus"></i> Diff</th>
                                <th><i class="fas fa-rupee-sign"></i></th>
                                <th>Calc</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($level_contributions as $index => $contrib):
                                $row_class = '';
                                $has_self_business = $contrib['self_business'] > 0;
                                $has_team_business = $contrib['team_business'] > 0;

                                if ($contrib['level_amount'] > 0) {
                                    if ($contrib['business_type'] == 'Self+Team') $row_class = 'combined-business-row';
                                    elseif ($contrib['business_type'] == 'Self') $row_class = 'self-business-row';
                                    else $row_class = 'team-business-row';
                                } else {
                                    $row_class = 'zero-commission-row';
                                }
                            ?>
                                <!-- MAIN ROW -->
                                <tr class="<?= $row_class ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($contrib['member_id']) ?></strong><br>
                                        <small><?= htmlspecialchars($contrib['member_name']) ?></small>
                                    </td>
                                    <td class="business-amount">
                                        ₹<?= number_format($contrib['self_business'], 2) ?>
                                        <?php if ($has_self_business): ?>
                                            <br><button class="btn btn-info btn-sm view-btn view-self-details-btn mt-1"
                                                data-member-id="<?= htmlspecialchars($contrib['member_id']) ?>"
                                                data-payment-details='<?= htmlspecialchars(json_encode($contrib['payment_details'])) ?>'
                                                data-type="self">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="business-amount">
                                        ₹<?= number_format($contrib['team_business'], 2) ?>
                                        <?php if ($has_team_business): ?>
                                            <br><button class="btn btn-warning btn-sm view-btn view-team-details-btn mt-1"
                                                data-downline-id="<?= htmlspecialchars($contrib['member_id']) ?>"
                                                data-team-members='<?= htmlspecialchars(json_encode($contrib['team_members'])) ?>'
                                                data-type="team">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="total-business">₹<?= number_format($contrib['total_business'], 2) ?></span></td>
                                    <td>
                                        <span class="badge <?= $contrib['level_amount'] > 0 ? 'bg-info' : 'bg-secondary' ?>">
                                            <?= $contrib['business_type'] ?>
                                        </span><br>
                                        <strong>₹<?= number_format($contrib['total_business'], 2) ?></strong>
                                    </td>
                                    <td><?= $contrib['downline_percent'] ?>%</td>
                                    <td class="<?= $contrib['diff_percent'] > 0 ? 'percent-diff' : 'zero-percent' ?>">
                                        <?= $contrib['diff_percent'] ?>%
                                    </td>
                                    <td class="<?= $contrib['level_amount'] > 0 ? 'commission-amount' : 'zero-commission' ?>">
                                        ₹<?= number_format($contrib['level_amount'], 2) ?>
                                    </td>
                                    <td class="calculation">
                                        <?= number_format($contrib['total_business'], 0) ?>×<?= $contrib['diff_percent'] ?>%
                                    </td>
                                </tr>

                                <!-- SELF DETAILS ROW -->
                                <tr class="details-row self-details-row" id="self-details-<?= htmlspecialchars($contrib['member_id']) ?>">
                                    <td colspan="10">
                                        <div class="p-2">
                                            <h6 class="mb-2"><i class="fas fa-shopping-cart text-info"></i> Self Business Details</h6>
                                            <table class="table details-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Commission</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="self-details-table-<?= htmlspecialchars($contrib['member_id']) ?>"></tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>

                                <!-- TEAM DETAILS ROW -->
                                <tr class="details-row team-details-row" id="team-details-<?= htmlspecialchars($contrib['member_id']) ?>">
                                    <td colspan="10">
                                        <div class="p-2">
                                            <h6 class="mb-2"><i class="fas fa-users text-warning"></i> Team Business Breakdown</h6>
                                            <div id="team-members-table-<?= htmlspecialchars($contrib['member_id']) ?>"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <tr class="total-row">
                                <td colspan="8"><strong>TOTAL</strong></td>
                                <td><strong>₹<?= number_format($total_calculated, 2) ?></strong></td>
                                <td><strong>✓</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if (empty($level_contributions)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Direct Downlines</h5>
                        <p class="text-muted">No direct downlines found for this period.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="card-footer bg-light border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        <?php
                        date_default_timezone_set('Asia/Kolkata');
                        echo date('d-m-Y H:i:s');
                        ?>
                    </small>

                    <a href="members_report.php?from=<?= $from_date ?>&to=<?= $to_date ?>" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Back to Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SELF BUSINESS VIEW
            document.querySelectorAll('.view-self-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const memberId = this.getAttribute('data-member-id');
                    const paymentDetails = JSON.parse(this.getAttribute('data-payment-details'));
                    const detailsRow = document.querySelector(`#self-details-${memberId}`);
                    const detailsTableBody = document.querySelector(`#self-details-table-${memberId}`);

                    toggleDetailsRow(detailsRow, 'self-details-row');
                    populateSelfDetails(detailsTableBody, paymentDetails);
                });
            });

            // TEAM BUSINESS VIEW
            document.querySelectorAll('.view-team-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const downlineId = this.getAttribute('data-downline-id');
                    const teamMembers = JSON.parse(this.getAttribute('data-team-members'));
                    const detailsRow = document.querySelector(`#team-details-${downlineId}`);
                    const teamContainer = document.querySelector(`#team-members-table-${downlineId}`);

                    toggleDetailsRow(detailsRow, 'team-details-row');
                    populateTeamMembers(teamContainer, teamMembers, downlineId);
                });
            });

            function toggleDetailsRow(row, className) {
                const isVisible = row.style.display === 'table-row';
                document.querySelectorAll(`.${className}`).forEach(r => r.style.display = 'none');
                row.style.display = isVisible ? 'none' : 'table-row';
            }

            function populateSelfDetails(tableBody, paymentDetails) {
                tableBody.innerHTML = '';
                if (paymentDetails && typeof paymentDetails === 'object') {
                    Object.keys(paymentDetails).forEach(productName => {
                        paymentDetails[productName].forEach(detail => {
                            if (detail.amount === null || detail.commission === null) return;
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${productName}</td>
                                <td>₹${Number(detail.amount).toFixed(2)}</td>
                                <td>${detail.date}</td>
                                <td>₹${Number(detail.commission).toFixed(2)}</td>
                            `;
                            tableBody.appendChild(row);
                        });
                    });
                    if (tableBody.children.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No details available</td></tr>';
                    }
                } else {
                    tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No details available</td></tr>';
                }
            }

            function populateTeamMembers(container, teamMembers, downlineId) {
                container.innerHTML = '';
                if (teamMembers && teamMembers.length > 0) {
                    let tableHTML = `
                        <table class="table team-business-table table-bordered">
                            <thead>
                                <tr>
                                    <th>Team Member</th>
                                    <th>Self Business</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    teamMembers.forEach(member => {
                        const hasSelfBusiness = member.self_business > 0;
                        tableHTML += `
                            <tr class="sub-member-row">
                                <td>
                                    <strong>${member.member_id}</strong><br>
                                    <small>${member.member_name}</small>
                                </td>
                                <td>₹${Number(member.self_business).toFixed(2)}</td>
                                <td>
                                    ${hasSelfBusiness ? `
                                        <button class="btn btn-info btn-sm view-sub-member-btn" 
                                                data-member-id="${member.member_id}"
                                                data-payment-details='${JSON.stringify(member.payment_details)}'>
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    ` : '<span class="text-muted">N/A</span>'}
                                </td>
                            </tr>
                        `;
                    });
                    tableHTML += '</tbody></table>';
                    container.innerHTML = tableHTML;

                    // Add click handlers for sub-member view buttons
                    container.querySelectorAll('.view-sub-member-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const subMemberId = this.getAttribute('data-member-id');
                            const subPaymentDetails = JSON.parse(this.getAttribute('data-payment-details'));

                            // Create modal or inline table for sub-member details
                            const modal = createSubMemberModal(subMemberId, subPaymentDetails);
                            document.body.appendChild(modal);
                            new bootstrap.Modal(modal).show();
                        });
                    });
                } else {
                    container.innerHTML = '<p class="text-muted text-center">No team members found</p>';
                }
            }

            function createSubMemberModal(memberId, paymentDetails) {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Self Business Details - ${memberId}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <thead><tr><th>Product</th><th>Amount</th><th>Date</th><th>Commission</th></tr></thead>
                                    <tbody id="sub-member-details-${memberId}"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                modal.addEventListener('hidden.bs.modal', () => modal.remove());

                // Populate after modal is shown
                modal.addEventListener('shown.bs.modal', () => {
                    const tableBody = modal.querySelector(`#sub-member-details-${memberId}`);
                    populateSelfDetails(tableBody, paymentDetails);
                });

                return modal;
            }
        });
    </script>
</body>

</html>