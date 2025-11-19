<?php
include_once 'connectdb.php';
session_start();

if (isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';

    try {
        // Fetch member details from tbl_regist
        $stmt = $pdo->prepare("SELECT m_name, sponsor_id FROM tbl_regist WHERE mem_sid = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch sponsor name
        $sponsor_name = '';
        if (!empty($member['sponsor_id'])) {
            $stmt2 = $pdo->prepare("SELECT m_name FROM tbl_regist WHERE mem_sid = ?");
            $stmt2->execute([$member['sponsor_id']]);
            $sponsor = $stmt2->fetch(PDO::FETCH_ASSOC);
            $sponsor_name = $sponsor['m_name'] ?? 'Not Found';
        }

        // Fetch product-wise payment details
        $products = [];
        $total_amount = 0;

        if (!empty($from_date) && !empty($to_date)) {
            // With date range filter - fetch product-wise data
            $stmt3 = $pdo->prepare("
                SELECT 
                    productname,
                    COUNT(*) as payment_count,
                    SUM(payamount) as total_amount
                FROM receiveallpayment 
                WHERE member_id = ? 
                AND DATE(created_date) >= ? 
                AND DATE(created_date) <= ?
                AND productname IS NOT NULL 
                AND productname != ''
                GROUP BY productname
                ORDER BY productname
            ");
            $stmt3->execute([$member_id, $from_date, $to_date]);

            while ($product = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $products[] = [
                    'product_name' => $product['productname'],
                    'payment_count' => (int)$product['payment_count'],
                    'total_amount' => (float)$product['total_amount']
                ];
                $total_amount += (float)$product['total_amount'];
            }

            // Also get overall total (including records without product names)
            $stmt4 = $pdo->prepare("SELECT SUM(payamount) AS total FROM receiveallpayment WHERE member_id = ? AND DATE(created_date) >= ? AND DATE(created_date) <= ?");
            $stmt4->execute([$member_id, $from_date, $to_date]);
            $overall_total = $stmt4->fetch(PDO::FETCH_ASSOC);
            $total_amount = (float)($overall_total['total'] ?? 0);
        } else {
            // Without date filter - fetch all product-wise data
            $stmt3 = $pdo->prepare("
                SELECT 
                    productname,
                    COUNT(*) as payment_count,
                    SUM(payamount) as total_amount
                FROM receiveallpayment 
                WHERE member_id = ?
                AND productname IS NOT NULL 
                AND productname != ''
                GROUP BY productname
                ORDER BY productname
            ");
            $stmt3->execute([$member_id]);

            while ($product = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $products[] = [
                    'product_name' => $product['productname'],
                    'payment_count' => (int)$product['payment_count'],
                    'total_amount' => (float)$product['total_amount']
                ];
                $total_amount += (float)$product['total_amount'];
            }

            // Also get overall total
            $stmt4 = $pdo->prepare("SELECT SUM(payamount) AS total FROM receiveallpayment WHERE member_id = ?");
            $stmt4->execute([$member_id]);
            $overall_total = $stmt4->fetch(PDO::FETCH_ASSOC);
            $total_amount = (float)($overall_total['total'] ?? 0);
        }

        // Debug information
        $debug_info = [
            'member_id' => $member_id,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'query_used' => !empty($from_date) && !empty($to_date) ? 'with_date_filter' : 'without_date_filter',
            'products_found' => count($products),
            'total_calculated' => $total_amount
        ];

        echo json_encode([
            'total_amount' => $total_amount,
            'sponsor_name' => $sponsor_name,
            'sponsor_id' => $member['sponsor_id'] ?? '',
            'products' => $products,
            'debug' => $debug_info
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'error' => $e->getMessage(),
            'debug' => [
                'member_id' => $member_id ?? 'not_set',
                'from_date' => $from_date ?? 'not_set',
                'to_date' => $to_date ?? 'not_set'
            ]
        ]);
    }
}
