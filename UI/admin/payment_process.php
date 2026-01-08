<?php

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
        $mail->Username = 'amitabhkmr989@gmail.com';
        $mail->Password = 'ronurtvturnjongr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('amitabhkmr989@gmail.com', 'Payment System');
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
        $mail->Username = 'amitabhkmr989@gmail.com';
        $mail->Password = 'ronurtvturnjongr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('amitabhkmr989@gmail.com', 'Amitabh Builder and Developers');
        $mail->addAddress('amitabhkmr989@gmail.com', 'Amitabh Kumar'); // Admin email
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
            <p>Best regards,<br>Amitabh Builder And Developers</p>";

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
        $duecounter=0;
        $stmt = $pdo->prepare("SELECT * FROM tbl_customeramount WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = [];
        if ($customer) {
            $customer['net_amount'] = floatval($customer['net_amount']);
            $customer['payamount'] = floatval($customer['payamount']);
            $customer['admission_charge'] = floatval($customer['admission_charge']);
            $customer['enrollment_charge'] = floatval($customer['enrollment_charge']);
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
                $payment['cashback'] = floatval($payment['cashback']);

                // if(!empty($payment['enrollment_charge'] ) ){
                    
                //      if($payment['payamount']>0)
                //     {
                //         $payment['due_amount'] = floatval($payment['due_amount'])-(15000+floatval($payment['payamount']));
                //     }
                //     else
                //     {
                //      $duecounter++;
                //     }
                // }

                // if($duecounter==1)
                // {
                //    if(empty($payment['enrollment_charge'] ) ){
                
                //    }
                // }
                // else{
                //     $payment['due_amount'] = floatval($payment['due_amount']);
                // }
                
                $payment['admission_charge'] = floatval($payment['admission_charge']);
                $payment['enrollment_charge'] = floatval($payment['enrollment_charge']);
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

        $email = 'amitabhkmr989@gmail.com';
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

    $debug = []; 
    $debug[] = ['stage' => 'START', 'POST' => $_POST];

    $employee_id = $_SESSION['sponsor_id'] ?? null;
    $employee_name = $_SESSION['sponsor_name'] ?? null;

    $debug[] = ['stage' => 'SESSION', 'employee_id' => $employee_id, 'employee_name' => $employee_name];

    if (!$employee_id || !$employee_name) {
        echo json_encode(['success' => false, 'error' => 'User session expired', 'debug' => $debug]);
        exit;
    }

    $data = [
        'invoice_id' => trim($_POST['invoice_id'] ?? ''),
        'member_id' => trim($_POST['member_id'] ?? ''),
        'customer_name' => trim($_POST['customer_name'] ?? ''),
        'productname' => trim($_POST['productname'] ?? ''),
        'net_amount' => floatval($_POST['net_amount'] ?? 0),
        'payment_mode' => trim($_POST['payment_mode'] ?? ''),
        'payment_type' => trim($_POST['payment_type'] ?? ''),
        'payamount' => floatval($_POST['payamount'] ?? 0),
        'firstallot' => floatval($_POST['firstallot'] ?? 0),
        'admission' => floatval($_POST['admission'] ?? 0),
        'enroll' => floatval($_POST['enroll'] ?? 0),
        'due_amount' => floatval($_POST['due_amount'] ?? 0),
        'voucher_number' => trim($_POST['voucher_number'] ?? '') ?: null,
        'cashback' => floatval($_POST['cashback'] ?? 0),
        'bill_prepared_by_id' => $employee_id,
        'bill_prepared_by_name' => $employee_name,
        'receipt' => trim($_POST['receipt']),
        'cheque_number' => trim($_POST['cheque_number'] ?? '') ?: null,
        'bank_name' => trim($_POST['bank_name'] ?? '') ?: null,
        'cheque_date' => trim($_POST['cheque_date'] ?? '') ?: null,
        'utr_number' => trim($_POST['utr_number'] ?? '') ?: null,
        'neft_payment' => trim($_POST['neft_payment'] ?? '') ?: null,
        'rtgs_payment' => trim($_POST['rtgs_payment'] ?? '') ?: null,
        'created_date' => trim($_POST['payment_date'] ?? '')
    ];

    $debug[] = ['stage' => 'DATA_COLLECTED', 'data' => $data];

    if (empty($data['invoice_id']) || empty($data['customer_name']) || empty($data['productname']) || empty($data['payment_mode']) || empty($data['created_date'])) {
        echo json_encode(['success' => false, 'error' => 'Required fields missing', 'debug' => $debug]);
        exit;
    }

    $allot = 0;
    $count = 1;
    $enroll = 0;
    $flag=0;
    $debug[] = ['stage' => 'INITIALIZED', 'allot' => $allot, 'count' => $count, 'enroll' => $enroll];

    if ($data['payment_type'] == 'enroll') {
        $flag++;
        $count = 6;
        $enroll = 15000;
        $admission = 0;
        $allot = 0;
        $due = $data['due_amount'] - $allot;

        $debug[] = ['stage' => 'ENROLL_LOGIC', 'count' => $count, 'enroll' => $enroll, 'due' => $due];
    }

    else if ($data['payment_type'] == 'allot') {

        // $enroll =$data['enroll'];
        // $admission =$data['admission'];
        $stmt526 = $pdo->prepare("SELECT flag,admission_charge,enrollment_charge,payamount 
                                  FROM receiveallpayment 
                                  WHERE invoice_id = ?");
        $stmt526->execute([$data['invoice_id']]);
        $payments = $stmt526->fetchAll(PDO::FETCH_ASSOC);

        $debug[] = ['stage' => 'ALLOT_FETCH', 'payments' => $payments];

        $loop = 0;

        foreach ($payments as $payment) {

            $debug[] = ['stage' => 'ALLOT_LOOP_BEFORE', 'loop' => $loop, 'payment' => $payment];

            if ($payment['flag']==1 && $payment['enrollment_charge']>0 && ($payment['admission_charge']>=0)) {
                $count=2;
                $allot=$data['payamount'];
                $due=$data['due_amount'] -($data['payamount']+$data['cashback']+15000);

                $pdo->prepare("UPDATE receiveallpayment SET flag = 0 WHERE invoice_id = ?")
                    ->execute([$data['invoice_id']]);

                $debug[] = ['stage'=>'CASE1', 'count'=>$count, 'allot'=>$allot,'due'=>$due,'cashback'=>$data['cashback']];
            }
            else if ($payment['flag']==2 && $payment['enrollment_charge']>0 && $payment['admission_charge']>0 && $payment['payamount']>0) { 
                $count=3;
                $allot=$data['payamount'];
                $due=$data['due_amount'] -($data['payamount']+$data['cashback']);

                $pdo->prepare("UPDATE receiveallpayment SET flag = 0 WHERE invoice_id = ?")
                    ->execute([$data['invoice_id']]);

                $debug[] = ['stage'=>'CASE2','count'=>$count,'allot'=>$allot,'due'=>$due,'cashback'=>$data['cashback']];
            }
            else if ($payment['flag']==0 && $payment['admission_charge']>0 && $payment['enrollment_charge']==0 && $payment['payamount']==0&& $data['payamount']>15000) {
                $count=4;
                $allot=$data['payamount']-15000;
                $due=$data['due_amount'] -($data['payamount']+$data['cashback']);
                if($data['enroll']==0)
                {
                    $enroll=15000;
                }
                else{
                $enroll=$payment['enrollment_charge'];
                }
                
                $pdo->prepare("UPDATE receiveallpayment SET flag = 0 WHERE invoice_id = ?")
                    ->execute([$data['invoice_id']]);

                $debug[] = ['stage'=>'CASE3','count'=>$count,'allot'=>$allot,'due'=>$due];
            }
            else {
                $debug[] = ['stage'=>'CASE_DEFAULT_before','count'=>$count,'allot'=>$allot,'due'=>$data['due_amount'],'cashback'=>$data['cashback']];
                $count=5;
                $allot=$data['payamount'];
                $due=$data['due_amount'] -($data['payamount']+$data['cashback']);

                $debug[] = ['stage'=>'CASE_DEFAULT','count'=>$count,'allot'=>$allot,'due'=>$due,'cashback'=>$data['cashback']];
            }

            $loop++;
        }

        $debug[] = ['stage' => 'ALLOT_END', 'count'=> $count, 'allot'=>$allot, 'due'=>$due,'cashback'=>$data['cashback']];
    }

    else
    {
         echo json_encode(['success' => false, 'error' => 'Please Select Payment Type']);
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

    $debug[] = ['stage' => 'SQL_TRANSACTION_START'];

    $pdo->beginTransaction();

    /* 1. INSERT INTO receiveallpayment */
    $debug[] = [
        'stage' => 'INSERT_RECEIVEALLPAYMENT_PREPARE',
        'values' => [
            $data['invoice_id'],
            $data['member_id'],
            $data['customer_name'],
            $data['productname'],
            $data['net_amount'],
            $data['payment_mode'],
            $allot,
            $due,
            $data['voucher_number'],
            $data['bill_prepared_by_id'],
            $data['bill_prepared_by_name'],
            $data['cheque_number'],
            $data['bank_name'],
            $data['cheque_date'],
            $data['utr_number'],
            $data['neft_payment'],
            $data['rtgs_payment'],
            $data['created_date'],
            $admission,
            $enroll
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO receiveallpayment (
            invoice_id, member_id, customer_name, productname, net_amount,
            payment_mode, payamount, due_amount, voucher_number,
            bill_prepared_by_id, bill_prepared_by_name,
            cheque_number, bank_name, cheque_date, utr_number,
            neft_payment, rtgs_payment, created_date, admission_charge, enrollment_charge,receipt_no
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['invoice_id'],
        $data['member_id'],
        $data['customer_name'],
        $data['productname'],
        $data['net_amount'],
        $data['payment_mode'],
        $allot,
        $due,
        $data['voucher_number'],
        $data['bill_prepared_by_id'],
        $data['bill_prepared_by_name'],
        $data['cheque_number'],
        $data['bank_name'],
        $data['cheque_date'],
        $data['utr_number'],
        $data['neft_payment'],
        $data['rtgs_payment'],
        $data['created_date'],
        $admission,
        $enroll,
        $data['receipt']
    ]);

    $debug[] = ['stage' => 'INSERT_RECEIVEALLPAYMENT_SUCCESS'];


    /* 2. PAYMENT TYPE = ENROLL */
    if ($data['payment_type'] == 'enroll') {

        $debug[] = [
            'stage' => 'ENROLL_FLAG_UPDATE',
            'query' => "UPDATE receiveallpayment SET flag = 1 WHERE invoice_id = ? AND enrollment_charge = 15000",
            'invoice_id' => $data['invoice_id']
        ];

        $update = $pdo->prepare("UPDATE receiveallpayment SET flag = 1 WHERE invoice_id = ? AND enrollment_charge = 15000");
        $update->execute([$data['invoice_id']]);

        $count = 7;

        $debug[] = ['stage' => 'ENROLL_FLAG_UPDATE_SUCCESS', 'count' => $count];
    }



    /* 3. UPDATE LOGIC (3 different branches) */

    // CASE 1 — Admission and Enroll both present
    if (!empty($admission) && !empty($enroll)) {

        $debug[] = [
            'stage' => 'UPDATE_CUSTOMERAMOUNT_CASE1',
            'condition' => '!empty(admission) && !empty(enroll)',
            'values' => [
                'allot' => $allot,
                'due_amount' => $data['due_amount'],
                'admission' => $admission,
                'enroll' => $enroll,
                'created_date' => $data['created_date'],
                'invoice_id' => $data['invoice_id']
            ]
        ];

        $stmt = $pdo->prepare("
            UPDATE tbl_customeramount 
            SET payamount = payamount + ?, 
                due_amount = ?,
                admission_charge = ?,
                enrollment_charge = ?,
                created_date = ?
            WHERE invoice_id = ?
        ");

        $stmt->execute([
            $allot,
            $data['due_amount'],
            $admission,
            $enroll,
            $data['created_date'],
            $data['invoice_id']
        ]);

        $pdo->commit();
        $count = 8;

        $debug[] = ['stage' => 'UPDATE_CASE1_SUCCESS', 'count' => $count];
    }

    // CASE 2 — Only Enroll present
    elseif (!empty($enroll) && empty($admission)) {

        $debug[] = [
            'stage' => 'UPDATE_CUSTOMERAMOUNT_CASE2',
            'condition' => '!empty(enroll) && empty(admission)',
            'values' => [
                'allot' => $allot,
                'due_amount' => $data['due_amount'],
                'enroll' => $enroll,
                'created_date' => $data['created_date'],
                'invoice_id' => $data['invoice_id']
            ]
        ];

        $stmt = $pdo->prepare("
            UPDATE tbl_customeramount 
            SET payamount = payamount + ?, 
                due_amount = ?,                
                enrollment_charge = ?,
                created_date = ?
            WHERE invoice_id = ?
        ");

        $stmt->execute([
            $allot,
            $due,
            $enroll,
            $data['created_date'],
            $data['invoice_id']
        ]);

        $pdo->commit();

        $debug[] = ['stage' => 'UPDATE_CASE2_SUCCESS'];
    }

    // CASE 3 — Default update
    else {

        $debug[] = [
            'stage' => 'UPDATE_CUSTOMERAMOUNT_CASE3_DEFAULT',
            'condition' => 'else (default branch)',
            'values' => [
                'allot' => $allot,
                'due' => $due,
                'created_date' => $data['created_date'],
                'invoice_id' => $data['invoice_id']
            ]
        ];

        $stmt = $pdo->prepare("
            UPDATE tbl_customeramount 
            SET payamount = payamount + ?, 
                due_amount = ?,               
                created_date = ?
            WHERE invoice_id = ?
        ");

        $stmt->execute([
            $allot,
            $due,
            $data['created_date'],
            $data['invoice_id']
        ]);

        $pdo->commit();

        $debug[] = ['stage' => 'UPDATE_CASE3_SUCCESS'];
    }



    /* 4. Send invoice email */
    $debug[] = ['stage' => 'EMAIL_SEND_ATTEMPT_START'];

    if (!sendInvoiceEmail($pdo, $data['invoice_id'], $data['customer_name'], $data, $count, $loop)) {

        $debug[] = ['stage' => 'EMAIL_SEND_FAILED'];

        echo json_encode([
            'success' => true,
            'due_amount' => $data['due_amount'],
            'warning' => 'Payment recorded, but failed to send email',
            'data' => $data,
            'count' => $count,
            'loop' => $loop,
            'debug' => $debug
        ]);
        exit;

    } else {

        $debug[] = ['stage' => 'EMAIL_SEND_SUCCESS'];

        echo json_encode([
            'success' => true,
            'due_amount' => $data['due_amount'],
            'message' => 'Payment recorded and invoice emailed',
            'data' => $data,
            'count' => $count,
            'loop' => $loop,
            'debug' => $debug
        ]);
        exit;
    }




} catch (Exception $e) {

    $debug[] = ['stage' => 'EXCEPTION_THROWN', 'message' => $e->getMessage()];

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'debug' => $debug
    ]);
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
