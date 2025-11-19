<?php
include_once 'connectdb.php';

// Start session if needed for other functionalities
session_start();

// PHPMailer configuration
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Adjust path to PHPMailer autoloader

function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dharamkumar211975@gmail.com';
        $mail->Password   = 'luqanzkdffjjlehy'; // Corrected app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('dharamkumar211975@gmail.com', 'Payment System');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'OTP for Payment Deletion';
        $mail->Body    = "
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

function generateOTP()
{
    return sprintf('%06d', mt_rand(0, 999999));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $date = date('Y-m-d');

    if ($action === 'search') {
        $invoice_id = $_POST['invoice_id'] ?? '';

        // Fetch customer data
        $stmt = $pdo->prepare("SELECT * FROM tbl_customeramount WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = [];
        if ($customer) {
            // Convert numeric fields to float
            $customer['net_amount'] = floatval($customer['net_amount']);
            $customer['payamount'] = floatval($customer['payamount']);
            $customer['due_amount'] = floatval($customer['due_amount']);
            $customer['corner_charge'] = floatval($customer['corner_charge']);
            $customer['gross_amount'] = floatval($customer['gross_amount']);
            $response['customer'] = $customer;

            // Fetch payment history
            $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE invoice_id = ? ORDER BY id DESC");
            $stmt->execute([$invoice_id]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convert numeric fields in payments
            foreach ($payments as &$payment) {
                $payment['net_amount'] = floatval($payment['net_amount']);
                $payment['payamount'] = floatval($payment['payamount']);
                $payment['due_amount'] = floatval($payment['due_amount']);
            }
            $response['payments'] = $payments;
        } else {
            $response['error'] = 'Invoice not found';
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if ($action === 'send_otp') {
        $row_id = $_POST['row_id'] ?? '';
        $invoice_id = $_POST['invoice_id'] ?? '';

        if (!$row_id || !$invoice_id) {
            echo json_encode(['success' => false, 'error' => 'Row ID and Invoice ID are required']);
            exit;
        }

        // Generate OTP
        $otp = generateOTP();
        $otp_expiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes expiry

        // Store OTP in tbl_customeramount
        try {
            $stmt = $pdo->prepare("UPDATE tbl_customeramount SET otp = ?, otp_expiry = ? WHERE invoice_id = ?");
            $stmt->execute([$otp, $otp_expiry, $invoice_id]);

            // Send OTP email
            $email = 'dharamkumar211975@gmail.com';
            if (sendOTP($email, $otp)) {
                echo json_encode(['success' => true, 'message' => 'OTP sent successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to send OTP']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'verify_otp_and_delete') {
        $row_id = $_POST['row_id'] ?? '';
        $invoice_id = $_POST['invoice_id'] ?? '';
        $entered_otp = $_POST['otp'] ?? '';

        if (!$row_id || !$invoice_id || !$entered_otp) {
            echo json_encode(['success' => false, 'error' => 'Row ID, Invoice ID, and OTP are required']);
            exit;
        }

        // Verify OTP
        $stmt = $pdo->prepare("SELECT otp, otp_expiry FROM tbl_customeramount WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        $otp_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otp_data || !$otp_data['otp']) {
            echo json_encode(['success' => false, 'error' => 'No OTP found for this invoice']);
            exit;
        }

        if (strtotime($otp_data['otp_expiry']) < time()) {
            // Clear expired OTP
            $stmt = $pdo->prepare("UPDATE tbl_customeramount SET otp = NULL, otp_expiry = NULL WHERE invoice_id = ?");
            $stmt->execute([$invoice_id]);
            echo json_encode(['success' => false, 'error' => 'OTP has expired']);
            exit;
        }

        if ($otp_data['otp'] !== $entered_otp) {
            echo json_encode(['success' => false, 'error' => 'Invalid OTP']);
            exit;
        }

        // OTP verified, proceed with deletion old code without trash table
        // try {
        //     $pdo->beginTransaction();

        //     // Get payment details before deletion
        //     $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
        //     $stmt->execute([$row_id, $invoice_id]);
        //     $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        //     if (!$payment) {
        //         throw new Exception('Payment record not found');
        //     }

        //     // Delete from receiveallpayment table
        //     $stmt = $pdo->prepare("DELETE FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
        //     $stmt->execute([$row_id, $invoice_id]);

        //     // Update tbl_customeramount
        //     $stmt = $pdo->prepare("
        //         UPDATE tbl_customeramount 
        //         SET payamount = payamount - ?, 
        //             due_amount = due_amount + ?,
        //             otp = NULL,
        //             otp_expiry = NULL
        //         WHERE invoice_id = ?
        //     ");
        //     $stmt->execute([$payment['payamount'], $payment['payamount'], $invoice_id]);

        //     // Check if all payments are deleted for this invoice_id
        //     $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ?");
        //     $stmt->execute([$invoice_id]);
        //     $payment_count = $stmt->fetchColumn();

        //     if ($payment_count == 0) {
        //         // Fetch productname from tbl_customeramount
        //         $stmt = $pdo->prepare("SELECT productname FROM tbl_customeramount WHERE invoice_id = ?");
        //         $stmt->execute([$invoice_id]);
        //         $product_name = $stmt->fetchColumn();

        //         if ($product_name) {
        //             // Update products table to set Status to 'available'
        //             $stmt = $pdo->prepare("UPDATE products SET Status = 'available' WHERE ProductName = ?");
        //             $stmt->execute([$product_name]);

        //             // Delete record from tbl_customeramount
        //             $stmt = $pdo->prepare("DELETE FROM tbl_customeramount WHERE invoice_id = ?");
        //             $stmt->execute([$invoice_id]);
        //         }
        //     } else {
        //         // Update created_date to latest date or NULL if payments remain
        //         $stmt = $pdo->prepare("SELECT MAX(created_date) as latest_date FROM receiveallpayment WHERE invoice_id = ?");
        //         $stmt->execute([$invoice_id]);
        //         $latest_date = $stmt->fetchColumn();

        //         $stmt = $pdo->prepare("UPDATE tbl_customeramount SET created_date = ? WHERE invoice_id = ?");
        //         $stmt->execute([$latest_date ?: null, $invoice_id]);
        //     }

        //     $pdo->commit();

        //     echo json_encode(['success' => true, 'message' => 'Payment deleted successfully']);
        // } catch (Exception $e) {
        //     $pdo->rollBack();
        //     echo json_encode(['success' => false, 'error' => 'Failed to delete payment: ' . $e->getMessage()]);
        // }


        // here new changes code for otp delete and store in trash table 
        try {
            $pdo->beginTransaction();

            // Get payment details before deletion
            $stmt = $pdo->prepare("SELECT * FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
            $stmt->execute([$row_id, $invoice_id]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                throw new Exception('Payment record not found');
            }

            // Insert into trash table first
            $columns = array_keys($payment);
            $colNames = implode(", ", $columns);
            $placeholders = implode(", ", array_fill(0, count($columns), "?"));

            $stmt = $pdo->prepare("INSERT INTO tbl_receiveallpayment_trash ($colNames) VALUES ($placeholders)");
            $stmt->execute(array_values($payment));

            // Delete from receiveallpayment table
            $stmt = $pdo->prepare("DELETE FROM receiveallpayment WHERE id = ? AND invoice_id = ?");
            $stmt->execute([$row_id, $invoice_id]);

            // Update tbl_customeramount
            $stmt = $pdo->prepare("
        UPDATE tbl_customeramount 
        SET payamount = payamount - ?, 
            due_amount = due_amount + ?,
            otp = NULL,
            otp_expiry = NULL
        WHERE invoice_id = ?
    ");
            $stmt->execute([$payment['payamount'], $payment['payamount'], $invoice_id]);

            // Check if all payments are deleted for this invoice_id
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ?");
            $stmt->execute([$invoice_id]);
            $payment_count = $stmt->fetchColumn();

            if ($payment_count == 0) {
                // Fetch productname from tbl_customeramount
                $stmt = $pdo->prepare("SELECT productname FROM tbl_customeramount WHERE invoice_id = ?");
                $stmt->execute([$invoice_id]);
                $product_name = $stmt->fetchColumn();

                if ($product_name) {
                    // Update products table to set Status to 'available'
                    $stmt = $pdo->prepare("UPDATE products SET Status = 'available' WHERE ProductName = ?");
                    $stmt->execute([$product_name]);

                    // Delete record from tbl_customeramount
                    $stmt = $pdo->prepare("DELETE FROM tbl_customeramount WHERE invoice_id = ?");
                    $stmt->execute([$invoice_id]);
                }
            } else {
                // Update created_date to latest date or NULL if payments remain
                $stmt = $pdo->prepare("SELECT MAX(created_date) as latest_date FROM receiveallpayment WHERE invoice_id = ?");
                $stmt->execute([$invoice_id]);
                $latest_date = $stmt->fetchColumn();

                $stmt = $pdo->prepare("UPDATE tbl_customeramount SET created_date = ? WHERE invoice_id = ?");
                $stmt->execute([$latest_date ?: null, $invoice_id]);
            }

            $pdo->commit();

            echo json_encode(['success' => true, 'message' => 'Payment deleted successfully & moved to trash']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => 'Failed to delete payment: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'check_duplicate') {
        $invoice_id = $_POST['invoice_id'] ?? '';
        $utr_number = $_POST['utr_number'] ?? null;
        $cheque_number = $_POST['cheque_number'] ?? null;
        $bank_name = $_POST['bank_name'] ?? null;

        $response = ['exists' => false];

        if ($utr_number) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND utr_number = ?");
            $stmt->execute([$invoice_id, $utr_number]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
            }
        } elseif ($cheque_number && $bank_name) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND cheque_number = ? AND bank_name = ?");
            $stmt->execute([$invoice_id, $cheque_number, $bank_name]);
            if ($stmt->fetchColumn() > 0) {
                $response['exists'] = true;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if ($action === 'submit_payment') {
        $data = [
            'invoice_id' => $_POST['invoice_id'],
            'member_id' => $_POST['member_id'],
            'customer_name' => $_POST['customer_name'],
            'productname' => $_POST['productname'],
            'net_amount' => floatval($_POST['net_amount']),
            'payment_mode' => $_POST['payment_mode'],
            'payamount' => floatval($_POST['payamount']),
            'due_amount' => floatval($_POST['due_amount']),
            'cheque_number' => $_POST['cheque_number'] ?? null,
            'bank_name' => $_POST['bank_name'] ?? null,
            'cheque_date' => $_POST['cheque_date'] ?? null,
            'utr_number' => $_POST['utr_number'] ?? null,
            'created_date' => $_POST['payment_date']
        ];

        // Additional validation
        if ($data['payamount'] <= 0) {
            echo json_encode(['success' => false, 'error' => 'Payment amount must be greater than zero']);
            exit;
        }

        if ($data['payment_mode'] === 'cheque' && (!$data['cheque_number'] || !$data['bank_name'] || !$data['cheque_date'])) {
            echo json_encode(['success' => false, 'error' => 'Cheque number, bank name, and cheque date are required for cheque payment']);
            exit;
        }

        // Double-check for duplicates
        if ($data['payment_mode'] === 'bank_transfer' && $data['utr_number']) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND utr_number = ?");
            $stmt->execute([$data['invoice_id'], $data['utr_number']]);
            if ($stmt->fetchColumn() > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'UTR number already exists for this invoice']);
                exit;
            }
        } elseif ($data['payment_mode'] === 'cheque' && $data['cheque_number'] && $data['bank_name']) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE invoice_id = ? AND cheque_number = ? AND bank_name = ?");
            $stmt->execute([$data['invoice_id'], $data['cheque_number'], $data['bank_name']]);
            if ($stmt->fetchColumn() > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Cheque number with this bank already exists for this invoice']);
                exit;
            }
        }

        try {
            $pdo->beginTransaction();

            // Insert payment record
            $stmt = $pdo->prepare("
                INSERT INTO receiveallpayment (
                    invoice_id, member_id, customer_name, productname, net_amount,
                    payment_mode, payamount, due_amount, cheque_number,
                    bank_name, cheque_date, utr_number, created_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                $data['cheque_number'],
                $data['bank_name'],
                $data['cheque_date'],
                $data['utr_number'],
                $data['created_date']
            ]);

            // Update customer amount table
            $stmt = $pdo->prepare("
                UPDATE tbl_customeramount 
                SET payamount = payamount + ?, 
                    due_amount = ?,
                    created_date = ?
                WHERE invoice_id = ?
            ");
            $stmt->execute([$data['payamount'], $data['due_amount'], $data['created_date'], $data['invoice_id']]);

            $pdo->commit();

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'due_amount' => $data['due_amount']]);
        } catch (Exception $e) {
            $pdo->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
}
