<?php
session_start();
require_once 'connectdb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_kyc_submit'])) {
    $sponsor_id = $_POST['sponsor_id'];
    $aadhar_number = $_POST['aadhar_number'];
    $upload_dir = "UI/admin/member_document/";

    // File Handling for multiple files
    function uploadFile($file, $upload_dir)
    {
        if ($file['error'] == 0) {
            $file_name = time() . "_" . basename($file['name']);
            $target_path = $upload_dir . $file_name;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return $file_name;
            }
        }
        return false;
    }

    $uploaded_files = [];
    $errors = false;

    // Handle multiple address proof files
    if (!empty($_FILES['address_proof_file']['name'])) {

        // Normalize to an array
        $files = [];
        if (is_array($_FILES['address_proof_file']['name'])) {
            // Multiple files
            foreach ($_FILES['address_proof_file']['name'] as $key => $name) {
                $files[] = [
                    'name' => $_FILES['address_proof_file']['name'][$key],
                    'tmp_name' => $_FILES['address_proof_file']['tmp_name'][$key],
                    'error' => $_FILES['address_proof_file']['error'][$key],
                    'type' => $_FILES['address_proof_file']['type'][$key]
                ];
            }
        } else {
            // Single file
            $files[] = [
                'name' => $_FILES['address_proof_file']['name'],
                'tmp_name' => $_FILES['address_proof_file']['tmp_name'],
                'error' => $_FILES['address_proof_file']['error'],
                'type' => $_FILES['address_proof_file']['type']
            ];
        }

        // Allowed file types
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];

        foreach ($files as $file) {
            if ($file['error'] === 0 && in_array($file['type'], $allowed_types)) {
                $uploaded_file = uploadFile($file, $upload_dir);
                if ($uploaded_file) {
                    $uploaded_files[] = $uploaded_file;
                } else {
                    $errors = true;
                }
            } else {
                $errors = true;
            }
        }
    } else {
        $errors = true;
    }

    if ($errors || empty($uploaded_files)) {
        echo "<script>alert('Error: At least one address proof file must be uploaded successfully!'); window.location.href='login.php';</script>";
    } else {
        // Combine uploaded file names into a comma-separated string
        $address_proof_files = implode(',', $uploaded_files);

        // Insert single row into tbl_kyc using sponsor_id
        $query = "INSERT INTO tbl_kyc (sponsor_id, address_proof_file) VALUES (?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$sponsor_id, $address_proof_files]);

        // Update aadhar_number in tbl_regist using mem_sid
        $update_aadhar = $pdo->prepare("UPDATE tbl_regist SET aadhar_number = ? WHERE mem_sid = ?");
        $update_aadhar->execute([$aadhar_number, $sponsor_id]);

        // Clear KYC session variable
        unset($_SESSION['kyc_required']);
        echo "<script>alert('KYC details uploaded successfully! Please log in again.'); window.location.href='login.php';</script>";
    }
}
