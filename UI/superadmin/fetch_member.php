<?php
include_once 'connectdb.php';

if (isset($_GET['member_id'])) {
    $member_id = $_GET['member_id'];

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT m_name, m_num, m_email FROM tbl_regist WHERE mem_sid = ?");
    $stmt->execute([$member_id]);

    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($member) {
        echo json_encode($member);
    } else {
        echo json_encode(['m_name' => '', 'm_num' => '', 'm_email' => '']);
    }
}
