<?php
require_once 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? '';
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';

    if (empty($member_id)) {
        echo '<div style="color: red;">Invalid member ID</div>';
        exit;
    }

    try {
        // Query to get self business data
        $query = "
            SELECT 
                r.invoice_id,
                r.customer_name,
                r.productname,
                r.rate,
                r.area,
                r.net_amount,
                r.payment_mode,
                r.payamount,
                r.due_amount,
                r.created_date,
                r.bill_date,
                ca.producttype,
                pt.product_type_name
            FROM receiveallpayment r
            LEFT JOIN tbl_customeramount ca ON r.invoice_id = ca.invoice_id
            LEFT JOIN producttype pt ON ca.producttype = pt.product_type_id
            WHERE r.member_id = :member_id 
            AND DATE(r.created_date) BETWEEN :from_date AND :to_date
            ORDER BY r.created_date DESC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':member_id' => $member_id,
            ':from_date' => $from_date,
            ':to_date' => $to_date
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            echo '<div style="padding: 10px; color: #666;">No self business found for this member in the selected period.</div>';
            exit;
        }

        $total_amount = 0;
        $total_paid = 0;
        $total_due = 0;

        foreach ($results as $row) {
            $total_amount += $row['net_amount'];
            $total_paid += $row['payamount'];
            $total_due += $row['due_amount'];
        }
?>

        <div style="padding: 10px;">

            <table class="table table-striped" style="font-size: 12px;">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Customer Name</th>
                        <th>Product Name</th>
                        <th>Product Type</th>
                        <th>Area</th>
                        <th>Rate</th>
                        <th>Net Amount</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Payment Mode</th>
                        <th>Bill Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['invoice_id']) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['productname']) ?></td>
                            <td>
                                <?php if ($row['product_type_name']): ?>
                                    <span class="<?= $row['producttype'] == 1 ? 'onetime-badge' : 'emi-badge' ?>">
                                        <?= htmlspecialchars($row['product_type_name']) ?>
                                    </span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td>₹<?= number_format($row['rate'], 2) ?></td>
                            <td>₹<?= number_format($row['net_amount'], 2) ?></td>
                            <td>₹<?= number_format($row['payamount'], 2) ?></td>
                            <td>₹<?= number_format($row['due_amount'], 2) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', htmlspecialchars($row['payment_mode']))) ?></td>
                            <td><?= htmlspecialchars($row['bill_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

<?php

    } catch (Exception $e) {
        echo '<div style="color: red;">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div style="color: red;">Invalid request</div>';
}
?>