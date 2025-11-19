<?php
include 'config.php'; // ✅ Database Connection Include करें

header("Content-Type: application/json"); // ✅ JSON Response के लिए Header Set करें

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ✅ Step 1: Input को Secure तरीके से लें
    $name = trim($_POST['name']); 
    $pswd = md5($_POST['password']); 

    // ✅ Step 2: SQL Query (Prepared Statement से SQL Injection से बचें)
    $sql = "SELECT * FROM tbl_regist WHERE sponsor_id = :name";
    $result = $pdo->prepare($sql);
    $result->execute(['name' => $name]); // ✅ Placeholder को सही से Bind करें

    $row = $result->fetch(PDO::FETCH_ASSOC);

    
    // ✅ Step 3: Password Verify करें
    if ($pswd==$row['m_password']) {
        echo json_encode(['success' => 1, 'message' => 'Login Successful']);
    } else {
        echo json_encode(['success' => 0, 'message' => 'Invalid credentials','your_password'=> $pswd,'database_pasword'=>$row['m_password']]);
    }

   
    
}

?>
