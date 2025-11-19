<?php include_once 'connectdb.php';
// Handle AJAX request
if (isset($_GET['action']) && $_GET['action'] == 'get_sponsor_name') {
    $sponsor_id = $_GET['sponsor_id'] ?? '';
    $response = ['name' => '', 'error' => ''];

    if (!empty($sponsor_id)) {
        $stmt = $pdo->prepare("SELECT s_name FROM tbl_hire WHERE sponsor_id = :sponsor_id");
        $stmt->execute([':sponsor_id' => $sponsor_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $response['name'] = $result['s_name'];
        } else {
            $response['error'] = 'Sponsor not found';
        }
    }
    echo json_encode($response);
    // exit;
}
