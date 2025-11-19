<?php
// include_once 'connectdb.php';

// session_start();

// require '../../vendor/autoload.php';

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// // Function to send OTP email
// function sendOTP($email, $otp)
// {
//     $mail = new PHPMailer(true);
//     try {
//         $mail->isSMTP();
//         $mail->Host = 'smtp.gmail.com';
//         $mail->SMTPAuth = true;
//         $mail->Username = 'dharamkumar211975@gmail.com';
//         $mail->Password = 'luqanzkdffjjlehy';
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port = 587;

//         $mail->setFrom('dharamkumar211975@gmail.com', 'Payment System');
//         $mail->addAddress($email);
//         $mail->isHTML(true);
//         $mail->Subject = 'OTP for Payment Deletion';
//         $mail->Body = "
//             <h3>Payment Deletion OTP</h3>
//             <p>Your OTP for deleting payment record is: <strong>{$otp}</strong></p>
//             <p>This OTP will expire in 5 minutes.</p>
//             <p>If you did not request this, please ignore this email.</p>
//         ";
//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         error_log("Mailer Error: {$mail->ErrorInfo}");
//         return false;
//     }
// }

// // Function to send invoice email
// function sendInvoiceEmail($pdo, $invoice_id, $customer_email, $customer_name, $data)
// {
//     try {
//         $mail = new PHPMailer(true);
//         $mail->isSMTP();
//         $mail->Host = 'smtp.gmail.com';
//         $mail->SMTPAuth = true;
//         $mail->Username = 'dharamkumar211975@gmail.com';
//         $mail->Password = 'luqanzkdffjjlehy';
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port = 587;

//         $mail->setFrom('dharamkumar211975@gmail.com', 'Hari Home Developers');
//         $mail->addAddress($customer_email, $customer_name);
//         $mail->isHTML(true);
//         $mail->Subject = 'Payment Receipt - ' . $invoice_id;

//         // Create HTML email body
//         $mail->Body = "
//             <h2>Payment Receipt</h2>
//             <p>Dear {$customer_name},</p>
//             <p>Thank you for your payment. Below are the details of your payment receipt:</p>
//             <table border='1' style='border-collapse: collapse; width: 100%;'>
//                 <tr><th>Invoice ID</th><td>{$invoice_id}</td></tr>
//                 <tr><th>Member ID</th><td>" . ($data['member_id'] ?? 'N/A') . "</td></tr>
//                 <tr><th>Refer Name</th><td>" . ($data['refer_name'] ?? 'N/A') . "</td></tr>
//                 <tr><th>Customer Name</th><td>{$data['customer_name']}</td></tr>
//                 <tr><th>Product Name</th><td>{$data['productname']}</td></tr>
//                 <tr><th>Net Amount</th><td>{$data['net_amount']}</td></tr>
//                 <tr><th>Payment Mode</th><td>{$data['payment_mode']}</td></tr>
//                 <tr><th>Paid Amount</th><td>{$data['payamount']}</td></tr>
//                 <tr><th>Due Amount</th><td>{$data['due_amount']}</td></tr>
//                 " . ($data['payment_mode'] == 'cheque' ? "
//                 <tr><th>Cheque Number</th><td>" . ($data['cheque_number'] ?? 'N/A') . "</td></tr>
//                 <tr><th>Bank Name</th><td>" . ($data['bank_name'] ?? 'N/A') . "</td></tr>
//                 <tr><th>Cheque Date</th><td>" . ($data['cheque_date'] ?? 'N/A') . "</td></tr>
//                 " : "") . ($data['payment_mode'] == 'bank_transfer' ? "
//                 <tr><th>UTR Number</th><td>" . ($data['utr_number'] ?? 'N/A') . "</td></tr>
//                 <tr><th>NEFT Payment</th><td>" . ($data['neft_payment'] ?? 'N/A') . "</td></tr>
//                 <tr><th>RTGS Payment</th><td>" . ($data['rtgs_payment'] ?? 'N/A') . "</td></tr>
//                 " : "") . "
//                 <tr><th>Payment Date</th><td>{$data['created_date']}</td></tr>
//             </table>
//             <p>Thank you for your payment!</p>
//             <p>Best regards,<br>Hari Home Developers</p>";

//         return $mail->send();
//     } catch (Exception $e) {
//         error_log("Failed to send invoice email. Error: {$e->getMessage()}");
//         return false;
//     }
// }

// function generateOTP()
// {
//     return sprintf('%06d', mt_rand(0, 999999));
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $action = $_POST['action'] ?? '';
//     $date = date('Y-m-d');

//     if ($action === 'search') {
//         $invoice_id = $_POST['invoice_id'] ?? '';

//         $stmt = $pdo->prepare("SELECT * FROM tbl_customeramount WHERE invoice_id = ?");
//         $stmt->execute([$invoice_id]);
//         $customer = $stmt->fetch(PDO::FETCH_ASSOC);

//         $response = [];
//         if ($customer) {
//             $customer['net_amount'] = floatval($customer['net_amount']);
//             $customer['payamount'] = floatval($customer['payamount']);
//             $customer['due_amount'] = floatval($customer['due_amount']);
//             $customer['corner_charge'] = floatval($customer['corner_charge']);
//             $customer['gross_amount'] = floatval($customer['gross_amount']);
//             $response['customer'] = $customer;

//             $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE invoice_id = ? ORDER BY id DESC");
//             $stmt->execute([$invoice_id]);
//             $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

//             foreach ($payments as &$payment) {
//                 $payment['net_amount'] = floatval($payment['net_amount']);
//                 $payment['payamount'] = floatval($payment['payamount']);
//                 $payment['due_amount'] = floatval($payment['due_amount']);
//             }
//             $response['payments'] = $payments;
//         } else {
//             $response['error'] = 'Invoice not found';
//         }

//         header('Content-Type: application/json');
//         echo json_encode($response);
//         exit;
//     }

//     if ($action === 'send_otp') {
//         $row_id = $_POST['row_id'] ?? '';
//         $invoice_id = $_POST['invoice_id'] ?? '';

//         if (!$row_id || !$invoice_id) {
//             echo json_encode(['success' => false, 'error' => 'Row ID and Invoice ID are required']);
//             exit;
//         }

//         $otp = generateOTP();
//         $otp_expiry = date('Y-m-d H:i:s', time() + 300);

//         try {
//             $stmt = $pdo->prepare("UPDATE tbl_customeramount SET otp = ?, otp_expiry = ? WHERE invoice_id = ?");
//             $stmt->execute([$otp, $otp_expiry, $invoice_id]);

//             $email = 'dharamkumar211975@gmail.com';
//             if (sendOTP($email, $otp)) {
//                 echo json_encode(['success' => true, 'message' => 'OTP sent successfully']);
//             } else {
//                 echo json_encode(['success' => false, 'error' => 'Failed to send OTP']);
//             }
//         } catch (Exception $e) {
//             echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
//         }
//         exit;
//     }

//     if ($action === 'verify_otp_and_delete') {
//         $row_id = $_POST['row_id'] ?? '';
//         $invoice_id = $_POST['invoice_id'] ?? '';
//         $entered_otp = $_POST['otp'] ?? '';

//         if (!$row_id || !$invoice_id || !$entered_otp) {
//             echo json_encode(['success' => false, 'error' => 'Row ID, Invoice ID, and OTP are required']);
//             exit;
//         }

//         $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM tbl_customeramount WHERE invoice_id = ?");
//         $stmt->execute([$invoice_id]);
//         $otp_data = $stmt->fetch(PDO::FETCH_ASSOC);

//         if (!$otp_data || !$otp_data['otp']) {
//             echo json_encode(['success' => false, 'error' => 'No OTP found for this invoice']);
//             exit;
//         }

//         if (strtotime($otp_data['otp_expiry']) < time()) {
//             $stmt = $pdo->prepare("UPDATE tbl_customeramount SET otp = NULL, otp_expiry = NULL WHERE invoice_id = ?");
//             $stmt->execute([$invoice_id]);
//             echo json_encode(['success' => false, 'error' => 'OTP has expired']);
//             exit;
//         }

//         if ($otp_data['otp'] !== $entered_otp) {
//             echo json_encode(['success' => false, 'error' => 'Invalid OTP']);
//             exit;
//         }

//         try {
//             $pdo->beginTransaction();

//             $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
//             $stmt->execute([$row_id, $invoice_id]);
//             $payment = $stmt->fetch(PDO::FETCH_ASSOC);

//             if (!$payment) {
//                 throw new Exception('Payment record not found');
//             }

//             $stmt = $pdo->prepare("DELETE FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
//             $stmt->execute([$row_id, $invoice_id]);

//             $stmt = $pdo->prepare("
//                 UPDATE tbl_customeramount 
//                 SET payamount = payamount - ?, 
//                     due_amount = due_amount + ?,
//                     otp = NULL,
//                     otp_expiry = NULL
//                 WHERE invoice_id = ?
//             ");
//             $stmt->execute([$payment['payamount'], $payment['payamount'], $invoice_id]);

//             $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ?");
//             $stmt->execute([$invoice_id]);
//             $payment_count = $stmt->fetchColumn();

//             if ($payment_count == 0) {
//                 $stmt = $pdo->prepare("SELECT productname FROM tbl_customeramount WHERE invoice_id = ?");
//                 $stmt->execute([$invoice_id]);
//                 $product_name = $stmt->fetchColumn();

//                 if ($product_name) {
//                     $stmt = $pdo->prepare("UPDATE products SET Status = 'available' WHERE ProductName = ?");
//                     $stmt->execute([$product_name]);

//                     $stmt = $pdo->prepare("DELETE FROM tbl_customeramount WHERE invoice_id = ?");
//                     $stmt->execute([$invoice_id]);
//                 }
//             } else {
//                 $stmt = $pdo->prepare("SELECT MAX(created_date) as latest_date FROM receiveallpayment WHERE invoice_id = ?");
//                 $stmt->execute([$invoice_id]);
//                 $latest_date = $stmt->fetchColumn();

//                 $stmt = $pdo->prepare("UPDATE tbl_customeramount SET created_date = ? WHERE invoice_id = ?");
//                 $stmt->execute([$latest_date ?: null, $invoice_id]);
//             }

//             $pdo->commit();

//             echo json_encode(['success' => true, 'message' => 'Payment deleted successfully']);
//         } catch (Exception $e) {
//             $pdo->rollBack();
//             echo json_encode(['success' => false, 'error' => 'Failed to delete payment: ' . $e->getMessage()]);
//         }
//         exit;
//     }

//     if ($action === 'check_duplicate') {
//         $invoice_id = $_POST['invoice_id'] ?? '';
//         $utr_number = $_POST['utr_number'] ?? null;
//         $neft_payment = $_POST['neft_payment'] ?? null;
//         $rtgs_payment = $_POST['rtgs_payment'] ?? null;
//         $cheque_number = $_POST['cheque_number'] ?? null;
//         $bank_name = $_POST['bank_name'] ?? null;

//         $response = ['exists' => false];

//         if ($utr_number) {
//             $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND utr_number = ?");
//             $stmt->execute([$invoice_id, $utr_number]);
//             if ($stmt->fetchColumn() > 0) {
//                 $response['exists'] = true;
//             }
//         } elseif ($neft_payment) {
//             $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND neft_payment = ?");
//             $stmt->execute([$invoice_id, $neft_payment]);
//             if ($stmt->fetchColumn() > 0) {
//                 $response['exists'] = true;
//             }
//         } elseif ($rtgs_payment) {
//             $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND rtgs_payment = ?");
//             $stmt->execute([$invoice_id, $rtgs_payment]);
//             if ($stmt->fetchColumn() > 0) {
//                 $response['exists'] = true;
//             }
//         } elseif ($cheque_number && $bank_name) {
//             $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND cheque_number = ? AND bank_name = ?");
//             $stmt->execute([$invoice_id, $cheque_number, $bank_name]);
//             if ($stmt->fetchColumn() > 0) {
//                 $response['exists'] = true;
//             }
//         }

//         header('Content-Type: application/json');
//         echo json_encode($response);
//         exit;
//     }

//     if ($action === 'submit_payment') {
//         $data = [
//             'invoice_id' => $_POST['invoice_id'],
//             'member_id' => $_POST['member_id'],
//             'customer_name' => $_POST['customer_name'],
//             'productname' => $_POST['productname'],
//             'net_amount' => floatval($_POST['net_amount']),
//             'payment_mode' => $_POST['payment_mode'],
//             'payamount' => floatval($_POST['payamount']),
//             'due_amount' => floatval($_POST['due_amount']),
//             'cheque_number' => $_POST['cheque_number'] ?? null,
//             'bank_name' => $_POST['bank_name'] ?? null,
//             'cheque_date' => $_POST['cheque_date'] ?? null,
//             'utr_number' => $_POST['utr_number'] ?? null,
//             'neft_payment' => $_POST['neft_payment'] ?? null,
//             'rtgs_payment' => $_POST['rtgs_payment'] ?? null,
//             'created_date' => $_POST['payment_date']
//         ];

//         // Fetch refer_name and customer_email from tbl_customeramount
//         $stmt = $pdo->prepare("SELECT refer_name, customer_email FROM tbl_customeramount WHERE invoice_id = ?");
//         $stmt->execute([$data['invoice_id']]);
//         $customer_data = $stmt->fetch(PDO::FETCH_ASSOC);
//         if ($customer_data) {
//             $data['refer_name'] = $customer_data['refer_name'];
//             $customer_email = $customer_data['customer_email'];
//         } else {
//             $data['refer_name'] = 'N/A';
//             $customer_email = 'dharamkumar211975@gmail.com'; // Fallback email
//         }

//         if ($data['payamount'] <= 0) {
//             echo json_encode(['success' => false, 'error' => 'Payment amount must be greater than zero']);
//             exit;
//         }

//         if ($data['payment_mode'] === 'cheque' && (!$data['cheque_number'] || !$data['bank_name'] || !$data['cheque_date'])) {
//             echo json_encode(['success' => false, 'error' => 'Cheque number, bank name, and cheque date are required for cheque payment']);
//             exit;
//         }

//         if ($data['payment_mode'] === 'bank_transfer') {
//             $filled_fields = array_filter([$data['neft_payment'], $data['rtgs_payment'], $data['utr_number']], function ($val) {
//                 return !empty($val);
//             });
//             if (count($filled_fields) === 0) {
//                 echo json_encode(['success' => false, 'error' => 'Please provide NEFT, RTGS, or UTR number for bank transfer']);
//                 exit;
//             }
//             if (count($filled_fields) > 1) {
//                 echo json_encode(['success' => false, 'error' => 'Please provide only one of NEFT, RTGS, or UTR number']);
//                 exit;
//             }

//             if ($data['neft_payment']) {
//                 $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND neft_payment = ?");
//                 $stmt->execute([$data['invoice_id'], $data['neft_payment']]);
//                 if ($stmt->fetchColumn() > 0) {
//                     echo json_encode(['success' => false, 'error' => 'NEFT reference number already exists for this invoice']);
//                     exit;
//                 }
//             } elseif ($data['rtgs_payment']) {
//                 $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND rtgs_payment = ?");
//                 $stmt->execute([$data['invoice_id'], $data['rtgs_payment']]);
//                 if ($stmt->fetchColumn() > 0) {
//                     echo json_encode(['success' => false, 'error' => 'RTGS reference number already exists for this invoice']);
//                     exit;
//                 }
//             } elseif ($data['utr_number']) {
//                 $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND utr_number = ?");
//                 $stmt->execute([$data['invoice_id'], $data['utr_number']]);
//                 if ($stmt->fetchColumn() > 0) {
//                     echo json_encode(['success' => false, 'error' => 'UTR number already exists for this invoice']);
//                     exit;
//                 }
//             }
//         }

//         try {
//             $pdo->beginTransaction();

//             $stmt = $pdo->prepare("
//                 INSERT INTO receiveallpayment (
//                     invoice_id, member_id, customer_name, productname, net_amount,
//                     payment_mode, payamount, due_amount, cheque_number,
//                     bank_name, cheque_date, utr_number, neft_payment,
//                     rtgs_payment, created_date
//                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
//             ");
//             $stmt->execute([
//                 $data['invoice_id'],
//                 $data['member_id'],
//                 $data['customer_name'],
//                 $data['productname'],
//                 $data['net_amount'],
//                 $data['payment_mode'],
//                 $data['payamount'],
//                 $data['due_amount'],
//                 $data['cheque_number'],
//                 $data['bank_name'],
//                 $data['cheque_date'],
//                 $data['utr_number'],
//                 $data['neft_payment'],
//                 $data['rtgs_payment'],
//                 $data['created_date']
//             ]);

//             $stmt = $pdo->prepare("
//                 UPDATE tbl_customeramount 
//                 SET payamount = payamount + ?, 
//                     due_amount = ?,
//                     created_date = ?
//                 WHERE invoice_id = ?
//             ");
//             $stmt->execute([$data['payamount'], $data['due_amount'], $data['created_date'], $data['invoice_id']]);

//             $pdo->commit();

//             // Send invoice email
//             if (!sendInvoiceEmail($pdo, $data['invoice_id'], $customer_email, $data['customer_name'], $data)) {
//                 echo json_encode(['success' => true, 'due_amount' => $data['due_amount'], 'warning' => 'Payment recorded, but failed to send invoice email']);
//             } else {
//                 echo json_encode(['success' => true, 'due_amount' => $data['due_amount'], 'message' => 'Payment recorded and invoice emailed to customer']);
//             }
//         } catch (Exception $e) {
//             $pdo->rollBack();
//             echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
//         }
//         exit;
//     }

//     if ($action === 'update_status') {
//         $id = $_POST['id'];
//         $status = $_POST['status'];

//         if (!in_array($status, ['pending', 'confirmed'])) {
//             echo json_encode(['success' => false, 'error' => 'Invalid status']);
//             exit;
//         }

//         try {
//             $stmt = $pdo->prepare("UPDATE receiveallpayment SET client_payment_status = ? WHERE id = ?");
//             $stmt->execute([$status, $id]);

//             echo json_encode(['success' => true]);
//         } catch (Exception $e) {
//             echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
//         }
//         exit;
//     }
// }



// Suppress notices and warnings to prevent invalid JSON output
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ob_start(); // Start output buffering to capture any unintended output

include_once 'connectdb.php';
session_start();

require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP email
function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dharamkumar211975@gmail.com';
        $mail->Password = 'luqanzkdffjjlehy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('dharamkumar211975@gmail.com', 'Payment System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'OTP for Payment Deletion';
        $mail->Body = "
            <h3>Payment Deletion OTP</h3>
            <p>Your OTP for deleting payment record is: <strong>{$otp}</strong></p>
            <p>This OTP will expire in 5 minutes.</p>
            <p>If you did not request this, please ignore this email.</p>
        ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Function to send invoice email
function sendInvoiceEmail($pdo, $invoice_id, $customer_name, $data)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dharamkumar211975@gmail.com';
        $mail->Password = 'luqanzkdffjjlehy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('dharamkumar211975@gmail.com', 'Hari Home Developers');
        $mail->addAddress('dharamkumar211975@gmail.com', 'Dharam Kumar'); // Admin email
        $mail->isHTML(true);
        $mail->Subject = 'Payment Receipt - ' . $invoice_id;

        $mail->Body = "
            <h2>Payment Receipt</h2>
            <p>Dear {$customer_name},</p>
            <p>Thank you for your payment. Below are the details of your payment receipt:</p>
            <table border='1' style='border-collapse: collapse; width: 100%;'>
                <tr><th>Invoice ID</th><td>{$invoice_id}</td></tr>
                <tr><th>Voucher Number</th><td>" . ($data['voucher_number'] ?? 'N/A') . "</td></tr>
                <tr><th>Prepared By ID</th><td>" . ($data['bill_prepared_by_id'] ?? 'N/A') . "</td></tr>
                <tr><th>Prepared By Name</th><td>" . ($data['bill_prepared_by_name'] ?? 'N/A') . "</td></tr>
                <tr><th>Member ID</th><td>" . ($data['member_id'] ?? 'N/A') . "</td></tr>
                <tr><th>Customer Name</th><td>" . ($data['customer_name'] ?? 'N/A') . "</td></tr>
                <tr><th>Product Name</th><td>" . ($data['productname'] ?? 'N/A') . "</td></tr>
                <tr><th>Net Amount</th><td>" . (isset($data['net_amount']) ? number_format($data['net_amount'], 2) : 'N/A') . "</td></tr>
                <tr><th>Payment Mode</th><td>" . ($data['payment_mode'] ?? 'N/A') . "</td></tr>
                <tr><th>Paid Amount</th><td>" . (isset($data['payamount']) ? number_format($data['payamount'], 2) : 'N/A') . "</td></tr>
                <tr><th>Due Amount</th><td>" . (isset($data['due_amount']) ? number_format($data['due_amount'], 2) : 'N/A') . "</td></tr>
                " . ($data['payment_mode'] == 'cheque' ? "
                <tr><th>Cheque Number</th><td>" . ($data['cheque_number'] ?? 'N/A') . "</td></tr>
                <tr><th>Bank Name</th><td>" . ($data['bank_name'] ?? 'N/A') . "</td></tr>
                <tr><th>Cheque Date</th><td>" . ($data['cheque_date'] ?? 'N/A') . "</td></tr>
                " : "") . ($data['payment_mode'] == 'bank_transfer' ? "
                <tr><th>UTR Number</th><td>" . ($data['utr_number'] ?? 'N/A') . "</td></tr>
                <tr><th>NEFT Payment</th><td>" . ($data['neft_payment'] ?? 'N/A') . "</td></tr>
                <tr><th>RTGS Payment</th><td>" . ($data['rtgs_payment'] ?? 'N/A') . "</td></tr>
                " : "") . "
                <tr><th>Payment Date</th><td>" . ($data['created_date'] ?? 'N/A') . "</td></tr>
            </table>
            <p>Thank you for your payment!</p>
            <p>Best regards,<br>Hari Home Developers</p>";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send invoice email. Error: {$e->getMessage()}");
        return false;
    }
}

function generateOTP()
{
    return sprintf('%06d', mt_rand(0, 999999));
}

// Clear any output buffer before sending JSON
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'search') {
    $invoice_id = trim($_POST['invoice_id'] ?? '');

    if (empty($invoice_id)) {
        echo json_encode(['success' => false, 'error' => 'Invoice ID is required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM tbl_customeramount WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = [];
        if ($customer) {
            $customer['net_amount'] = floatval($customer['net_amount']);
            $customer['payamount'] = floatval($customer['payamount']);
            $customer['due_amount'] = floatval($customer['due_amount']);
            $customer['corner_charge'] = floatval($customer['corner_charge'] ?? 0);
            $customer['gross_amount'] = floatval($customer['gross_amount'] ?? 0);
            $response['customer'] = $customer;

            $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE invoice_id = ? ORDER BY id DESC");
            $stmt->execute([$invoice_id]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($payments as &$payment) {
                $payment['net_amount'] = floatval($payment['net_amount']);
                $payment['payamount'] = floatval($payment['payamount']);
                $payment['due_amount'] = floatval($payment['due_amount']);
            }
            $response['payments'] = $payments;
        } else {
            $response['error'] = 'Invoice not found';
        }

        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'send_otp') {
    $row_id = trim($_POST['row_id'] ?? '');
    $invoice_id = trim($_POST['invoice_id'] ?? '');

    if (!$row_id || !$invoice_id) {
        echo json_encode(['success' => false, 'error' => 'Row ID and Invoice ID are required']);
        exit;
    }

    $otp = generateOTP();
    $otp_expiry = date('Y-m-d H:i:s', time() + 300);

    try {
        $stmt = $pdo->prepare("UPDATE tbl_customeramount SET otp = ?, otp_expiry = ? WHERE invoice_id = ?");
        $stmt->execute([$otp, $otp_expiry, $invoice_id]);

        $email = 'dharamkumar211975@gmail.com';
        if (sendOTP($email, $otp)) {
            echo json_encode(['success' => true, 'message' => 'OTP sent successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to send OTP']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'verify_otp_and_delete') {
    $row_id = trim($_POST['row_id'] ?? '');
    $invoice_id = trim($_POST['invoice_id'] ?? '');
    $entered_otp = trim($_POST['otp'] ?? '');

    if (!$row_id || !$invoice_id || !$entered_otp) {
        echo json_encode(['success' => false, 'error' => 'Row ID, Invoice ID, and OTP are required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM tbl_customeramount WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        $otp_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otp_data || !$otp_data['otp']) {
            echo json_encode(['success' => false, 'error' => 'No OTP found for this invoice']);
            exit;
        }

        if (strtotime($otp_data['otp_expiry']) < time()) {
            $stmt = $pdo->prepare("UPDATE tbl_customeramount SET otp = NULL, otp_expiry = NULL WHERE invoice_id = ?");
            $stmt->execute([$invoice_id]);
            echo json_encode(['success' => false, 'error' => 'OTP has expired']);
            exit;
        }

        if ($otp_data['otp'] !== $entered_otp) {
            echo json_encode(['success' => false, 'error' => 'Invalid OTP']);
            exit;
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
        $stmt->execute([$row_id, $invoice_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            throw new Exception('Payment record not found');
        }

        $stmt = $pdo->prepare("DELETE FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
        $stmt->execute([$row_id, $invoice_id]);

        $stmt = $pdo->prepare("
            UPDATE tbl_customeramount 
            SET payamount = payamount - ?, 
                due_amount = due_amount + ?,
                otp = NULL,
                otp_expiry = NULL
            WHERE invoice_id = ?
        ");
        $stmt->execute([$payment['payamount'], $payment['payamount'], $invoice_id]);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        $payment_count = $stmt->fetchColumn();

        if ($payment_count == 0) {
            $stmt = $pdo->prepare("SELECT productname FROM tbl_customeramount WHERE invoice_id = ?");
            $stmt->execute([$invoice_id]);
            $product_name = $stmt->fetchColumn();

            if ($product_name) {
                $stmt = $pdo->prepare("UPDATE products SET Status = 'available' WHERE ProductName = ?");
                $stmt->execute([$product_name]);

                $stmt = $pdo->prepare("DELETE FROM tbl_customeramount WHERE invoice_id = ?");
                $stmt->execute([$invoice_id]);
            }
        } else {
            $stmt = $pdo->prepare("SELECT MAX(created_date) as latest_date FROM receiveallpayment WHERE invoice_id = ?");
            $stmt->execute([$invoice_id]);
            $latest_date = $stmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE tbl_customeramount SET created_date = ? WHERE invoice_id = ?");
            $stmt->execute([$latest_date ?: null, $invoice_id]);
        }

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Payment deleted successfully']);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Failed to delete payment: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'check_duplicate') {
    $invoice_id = trim($_POST['invoice_id'] ?? '');
    $voucher_number = trim($_POST['voucher_number'] ?? '');
    $utr_number = trim($_POST['utr_number'] ?? '');
    $neft_payment = trim($_POST['neft_payment'] ?? '');
    $rtgs_payment = trim($_POST['rtgs_payment'] ?? '');
    $cheque_number = trim($_POST['cheque_number'] ?? '');
    $bank_name = trim($_POST['bank_name'] ?? '');

    if (empty($invoice_id)) {
        echo json_encode(['success' => false, 'error' => 'Invoice ID is required']);
        exit;
    }

    try {
        $response = ['exists' => false, 'field' => ''];

        if ($voucher_number) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND voucher_number = ?");
            $stmt->execute([$invoice_id, $voucher_number]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
                $response['field'] = 'voucher_number';
            }
        }

        if (!$response['exists'] && $utr_number) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND utr_number = ?");
            $stmt->execute([$invoice_id, $utr_number]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
                $response['field'] = 'utr_number';
            }
        }

        if (!$response['exists'] && $neft_payment) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND neft_payment = ?");
            $stmt->execute([$invoice_id, $neft_payment]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
                $response['field'] = 'neft_payment';
            }
        }

        if (!$response['exists'] && $rtgs_payment) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND rtgs_payment = ?");
            $stmt->execute([$invoice_id, $rtgs_payment]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
                $response['field'] = 'rtgs_payment';
            }
        }

        if (!$response['exists'] && $cheque_number && $bank_name) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND cheque_number = ? AND bank_name = ?");
            $stmt->execute([$invoice_id, $cheque_number, $bank_name]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
                $response['field'] = 'cheque_number';
            }
        }

        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error in duplicate check: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'submit_payment') {
    $employee_id = $_SESSION['sponsor_id'] ?? null;
    $employee_name = $_SESSION['sponsor_name'] ?? null;

    if (!$employee_id || !$employee_name) {
        echo json_encode(['success' => false, 'error' => 'User session expired. Please log in again.']);
        exit;
    }

    $data = [
        'invoice_id' => trim($_POST['invoice_id'] ?? ''),
        'member_id' => trim($_POST['member_id'] ?? ''),
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'productname' => trim($_POST['productname'] ?? ''),
        'net_amount' => floatval($_POST['net_amount'] ?? 0),
        'payment_mode' => trim($_POST['payment_mode'] ?? ''),
        'payamount' => floatval($_POST['payamount'] ?? 0),
        'due_amount' => floatval($_POST['due_amount'] ?? 0),
        'voucher_number' => trim($_POST['voucher_number'] ?? '') ?: null,
        'bill_prepared_by_id' => $employee_id,
        'bill_prepared_by_name' => $employee_name,
        'cheque_number' => trim($_POST['cheque_number'] ?? '') ?: null,
        'bank_name' => trim($_POST['bank_name'] ?? '') ?: null,
        'cheque_date' => trim($_POST['cheque_date'] ?? '') ?: null,
        'utr_number' => trim($_POST['utr_number'] ?? '') ?: null,
        'neft_payment' => trim($_POST['neft_payment'] ?? '') ?: null,
        'rtgs_payment' => trim($_POST['rtgs_payment'] ?? '') ?: null,
        'created_date' => trim($_POST['payment_date'] ?? '')
    ];

    if (empty($data['invoice_id']) || empty($data['customer_name']) || empty($data['productname']) || empty($data['payment_mode']) || empty($data['created_date'])) {
        echo json_encode(['success' => false, 'error' => 'Required fields are missing']);
        exit;
    }


    if ($data['payamount'] <= 0) {
        echo json_encode(['success' => false, 'error' => 'Payment amount must be greater than zero']);
        exit;
    }

    if ($data['payment_mode'] === 'cheque' && (empty($data['cheque_number']) || empty($data['bank_name']) || empty($data['cheque_date']))) {
        echo json_encode(['success' => false, 'error' => 'Cheque number, bank name, and cheque date are required for cheque payment']);
        exit;
    }

    if ($data['payment_mode'] === 'bank_transfer') {
        $filled_fields = array_filter([$data['neft_payment'], $data['rtgs_payment'], $data['utr_number']], function ($val) {
            return !empty($val);
        });
        if (count($filled_fields) === 0) {
            echo json_encode(['success' => false, 'error' => 'Please provide NEFT, RTGS, or UTR number for bank transfer']);
            exit;
        }
        if (count($filled_fields) > 1) {
            echo json_encode(['success' => false, 'error' => 'Please provide only one of NEFT, RTGS, or UTR number']);
            exit;
        }
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO receiveallpayment (
                invoice_id, member_id, customer_name, productname, net_amount,
                payment_mode, payamount, due_amount, voucher_number,
                bill_prepared_by_id, bill_prepared_by_name,
                cheque_number, bank_name, cheque_date, utr_number,
                neft_payment, rtgs_payment, created_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['invoice_id'],
            $data['member_id'],
            $data['customer_name'],
            $data['productname'],
            $data['net_amount'],
            $data['payment_mode'],
            $data['payamount'],
            $data['due_amount'],
            $data['voucher_number'],
            $data['bill_prepared_by_id'],
            $data['bill_prepared_by_name'],
            $data['cheque_number'],
            $data['bank_name'],
            $data['cheque_date'],
            $data['utr_number'],
            $data['neft_payment'],
            $data['rtgs_payment'],
            $data['created_date']
        ]);

        $stmt = $pdo->prepare("
            UPDATE tbl_customeramount 
            SET payamount = payamount + ?, 
                due_amount = ?,
                created_date = ?
            WHERE invoice_id = ?
        ");
        $stmt->execute([$data['payamount'], $data['due_amount'], $data['created_date'], $data['invoice_id']]);

        $pdo->commit();

        // Send invoice email
        if (!sendInvoiceEmail($pdo, $data['invoice_id'], $data['customer_name'], $data)) {
            echo json_encode(['success' => true, 'due_amount' => $data['due_amount'], 'warning' => 'Payment recorded, but failed to send invoice email']);
        } else {
            echo json_encode(['success' => true, 'due_amount' => $data['due_amount'], 'message' => 'Payment recorded and invoice emailed to customer']);
        }
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

if ($action === 'update_status') {
    $id = trim($_POST['id'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (empty($id) || !in_array($status, ['pending', 'confirmed'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid status or ID']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE receiveallpayment SET client_payment_status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
exit;
