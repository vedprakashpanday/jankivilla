<?php
error_reporting(0);
session_start();
include_once "connectdb.php";



// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
  header('Location: ../../account.php');
  exit();
}



$employee_id = $_SESSION['sponsor_id'];
$employee_name = $_SESSION['sponsor_name'];


require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




// if ($_SESSION['sponsor_id'] === $sponsorid && $_SESSION['sponsor_pass'] === $sponsorpass && $_SESSION['status'] === 'active') {

//   header('location:../../adminlogin.php');
// }

$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : '';
$memberid = isset($_GET['member_id']) ? $_GET['member_id'] : ''; // Using $memberid to match your variable name

// Function to send invoice email
function sendInvoiceEmail($pdo, $invoice_id, $customer_email, $customer_name, $data)
{
  $mail = new PHPMailer(true);
  $success = true;
  $errors = [];

  try {
    // SMTP Configuration
    $mail->SMTPDebug = 0; // Set to 2 for debugging, 0 for production
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dharamkumar211975@gmail.com';
    $mail->Password = 'luqa nzkd ffjj lehy'; // Ensure this is a valid App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email content (same for both recipients)
    $emi_months = isset($data['emi_month']) && $data['emi_month'] !== '' ? intval($data['emi_month']) : 0;
    $emi_content = '';
    if ($emi_months > 0 && $data['due_amount'] > 0) {
      $emi_per_month = $data['due_amount'] / $emi_months;
      $emi_content .= "<tr><th>EMI Months</th><td>{$emi_months}</td></tr>";
      $emi_content .= "<tr><th>EMI Amount per Month</th><td>" . number_format($emi_per_month, 2) . "</td></tr>";
      $emi_content .= "<tr><th>Payment Schedule</th><td><table border='1' style='border-collapse: collapse; width: 100%;'>";
      $emi_content .= "<tr><th>Month</th><th>Amount</th><th>Due Date</th></tr>";
      $start_date = new DateTime($data['date']);
      for ($i = 1; $i <= $emi_months; $i++) {
        $due_date = clone $start_date;
        $due_date->modify("+$i month");
        $emi_content .= "<tr><td>$i</td><td>" . number_format($emi_per_month, 2) . "</td><td>" . $due_date->format('Y-m-d') . "</td></tr>";
      }
      $emi_content .= "</table></td></tr>";
    }

    $email_body = "
      <h2>Sale Invoice</h2>
      <p>Dear {$customer_name},</p>
      <p>Thank you for your purchase. Below are the details of your invoice:</p>
      <table border='1' style='border-collapse: collapse; width: 100%;'>
        <tr><th>Invoice ID</th><td>{$invoice_id}</td></tr>
        <tr><th>Voucher Number</th><td>" . ($data['voucher_number'] ?? 'N/A') . "</td></tr>
        <tr><th>Prepared By ID</th><td>" . ($data['bill_prepared_by_id'] ?? 'N/A') . "</td></tr>
        <tr><th>Prepared By Name</th><td>" . ($data['bill_prepared_by_name'] ?? 'N/A') . "</td></tr>
        <tr><th>Member ID</th><td>" . ($data['member_id'] ?? 'N/A') . "</td></tr>
        <tr><th>Refer Name</th><td>" . ($data['refer_name'] ?? 'N/A') . "</td></tr>
        <tr><th>Date</th><td>{$data['date']}</td></tr>
        <tr><th>Customer Name</th><td>{$data['customer_name']}</td></tr>
        <tr><th>Customer Mobile</th><td>{$data['customer_mobile']}</td></tr>
        <tr><th>Customer Email</th><td>{$data['customer_email']}</td></tr>
        <tr><th>Product Type</th><td>{$data['product_type']}</td></tr>
        <tr><th>Product Name</th><td>{$data['product_name']}</td></tr>
        <tr><th>Square Feet</th><td>{$data['squarefeet']}</td></tr>
        <tr><th>Rate</th><td>{$data['rate']}</td></tr>
        <tr><th>Quantity</th><td>{$data['quantity']}</td></tr>
        <tr><th>Gross Amount</th><td>{$data['gross_amount']}</td></tr>
        <tr><th>Discount (%)</th><td>{$data['discount_percent']}</td></tr>
        <tr><th>Discount (Rs)</th><td>{$data['discount_rs']}</td></tr>
        <tr><th>Corner Charge</th><td>{$data['corner_charge']}</td></tr>
        <tr><th>Net Amount</th><td>{$data['net_amount']}</td></tr>
        <tr><th>Payment Mode</th><td>{$data['payment_mode']}</td></tr>
        <tr><th>Paid Amount</th><td>{$data['payamount']}</td></tr>
        <tr><th>Due Amount</th><td>{$data['due_amount']}</td></tr>
        {$emi_content}
        " . ($data['payment_mode'] == 'cheque' ? "
        <tr><th>Cheque Number</th><td>" . ($data['cheque_number'] ?? 'N/A') . "</td></tr>
        <tr><th>Bank Name</th><td>" . ($data['bank_name'] ?? 'N/A') . "</td></tr>
        <tr><th>Cheque Date</th><td>" . ($data['cheque_date'] ?? 'N/A') . "</td></tr>
        " : "") . ($data['payment_mode'] == 'bank_transfer' ? "
        <tr><th>UTR Number</th><td>" . ($data['utr_number'] ?? 'N/A') . "</td></tr>
        <tr><th>NEFT Payment</th><td>" . ($data['neft_payment'] ?? 'N/A') . "</td></tr>
        <tr><th>RTGS Payment</th><td>" . ($data['rtgs_payment'] ?? 'N/A') . "</td></tr>
        " : "") . "
        <tr><th>Remarks</th><td>" . ($data['remarks'] ?? 'None') . "</td></tr>
      </table>
      <p>Thank you for choosing us!</p>
      <p>Best regards,<br>Hari Home Developers</p>";

    // Send email to admin
    $mail->setFrom('dharamkumar211975@gmail.com', 'Hari Home Developers');
    $mail->addAddress('dharamkumar211975@gmail.com', 'Dharam Kumar');
    $mail->isHTML(true);
    $mail->Subject = 'Sale Invoice - ' . $invoice_id;
    $mail->Body = $email_body;

    if (!$mail->send()) {
      $success = false;
      $errors[] = "Failed to send email to admin. Error: {$mail->ErrorInfo}";
    }

    // Clear recipients for the next email
    $mail->clearAddresses();

    // Send email to customer, if valid
    if (filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
      $mail->addAddress($customer_email, $customer_name);
      $mail->Subject = 'Sale Invoice - ' . $invoice_id;
      $mail->Body = $email_body;

      if (!$mail->send()) {
        $success = false;
        $errors[] = "Failed to send email to customer ($customer_email). Error: {$mail->ErrorInfo}";
      }
    } else {
      $success = false;
      $errors[] = "Invalid customer email address: $customer_email";
    }

    // Log errors if any
    if (!empty($errors)) {
      foreach ($errors as $error) {
        error_log($error);
      }
    }

    return $success;
  } catch (Exception $e) {
    error_log("Failed to send invoice email. General Error: {$e->getMessage()}");
    return false;
  }
}

if (isset($_POST['btnsubmit'])) {
  $productname = $_POST['product_name'];
  $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
  $voucher_number = $_POST['voucher_num'] ?? '';
  $emi_month = isset($_POST['emi_month']) && $_POST['emi_month'] !== '' ? intval($_POST['emi_month']) : null;
  $employee_id = $_SESSION['sponsor_id'] ?? '';
  $employee_name = $_SESSION['sponsor_name'] ?? '';

  try {
    // Validate session variables
    if (empty($employee_id) || empty($employee_name)) {
      echo "<script>alert('User session expired. Please log in again.'); window.location.href='adminlogin.php';</script>";
      exit;
    }



    $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE voucher_number = ?");
    $stmt->execute([$voucher_number]);
    if ($stmt->fetchColumn() > 0) {
      echo "<script>alert('This voucher number already exists in payment records. Please use a unique voucher number.'); window.history.back();</script>";
      exit;
    }

    // Generate invoice ID
    $invoice_id = "HHD" . strtoupper(uniqid());

    // Validate and set default values for discount fields
    $discount_percent = isset($_POST['discount_percent']) && $_POST['discount_percent'] !== '' ? floatval($_POST['discount_percent']) : 0;
    $discount_rs = isset($_POST['discount_rs']) && $_POST['discount_rs'] !== '' ? floatval($_POST['discount_rs']) : 0;

    // Get the net amount from the form
    $net_amount = isset($_POST['net_amount']) && $_POST['net_amount'] !== '' ? floatval($_POST['net_amount']) : 0;

    // Get the payment details from form
    $cash_amount = isset($_POST['cash_amount']) ? floatval($_POST['cash_amount']) : 0;
    $cheque_amount = isset($_POST['cheque_amount']) ? floatval($_POST['cheque_amount']) : 0;
    $transfer_amount = isset($_POST['transfer_amount']) ? floatval($_POST['transfer_amount']) : 0;
    $neft_payment = isset($_POST['neft_payment']) ? $_POST['neft_payment'] : null;
    $rtgs_payment = isset($_POST['rtgs_payment']) ? $_POST['rtgs_payment'] : null;
    $utr_number = isset($_POST['utr_number']) ? $_POST['utr_number'] : null;

    // Determine payamount based on payment mode
    $payamount = 0;
    $payment_mode = $_POST['payment_mode'];
    switch ($payment_mode) {
      case 'cash':
        $payamount = $cash_amount;
        break;
      case 'cheque':
        $payamount = $cheque_amount;
        break;
      case 'bank_transfer':
        $payamount = $transfer_amount;
        break;
    }

    // Calculate the due amount
    $due_amount = $net_amount - $payamount;

    // Check for duplicate UTR number, NEFT, or RTGS
    if ($payment_mode === 'bank_transfer') {
      $filled_fields = array_filter([$neft_payment, $rtgs_payment, $utr_number], function ($val) {
        return !empty($val);
      });
      if (count($filled_fields) === 0) {
        echo "<script>alert('Please provide NEFT, RTGS, or UTR number for bank transfer.'); window.history.back();</script>";
        exit;
      }
      if (count($filled_fields) > 1) {
        echo "<script>alert('Please provide only one of NEFT, RTGS, or UTR number.'); window.history.back();</script>";
        exit;
      }

      if ($neft_payment) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE neft_payment = ?");
        $stmt->execute([$neft_payment]);
        if ($stmt->fetchColumn() > 0) {
          echo "<script>alert('This NEFT reference number already exists. Please use a unique NEFT reference number.'); window.history.back();</script>";
          exit;
        }
      } elseif ($rtgs_payment) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE rtgs_payment = ?");
        $stmt->execute([$rtgs_payment]);
        if ($stmt->fetchColumn() > 0) {
          echo "<script>alert('This RTGS reference number already exists. Please use a unique RTGS reference number.'); window.history.back();</script>";
          exit;
        }
      } elseif ($utr_number) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE utr_number = ?");
        $stmt->execute([$utr_number]);
        if ($stmt->fetchColumn() > 0) {
          echo "<script>alert('This UTR number already exists. Please use a unique UTR number.'); window.history.back();</script>";
          exit;
        }
      }
    } elseif ($payment_mode === 'cheque' && !empty($_POST['cheque_number'])) {
      $cheque_number = $_POST['cheque_number'];
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE cheque_number = ?");
      $stmt->execute([$cheque_number]);
      if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('This cheque number already exists. Please use a unique cheque number.'); window.history.back();</script>";
        exit;
      }
    }


    $memberid = isset($_POST['member_id']) ? $_POST['member_id'] : '';

    // Insert into tbl_customeramount
    $customer_sql = "INSERT INTO tbl_customeramount (
                    invoice_id, member_id, customer_id, customer_name, mobile_number, customer_address,
                    producttype, productname, area, rate, net_amount, payamount, due_amount,
                    corner_charge, gross_amount, created_date, remarks, emi_month
                ) VALUES (
                    :invoice_id, :member_id, :customer_id, :customer_name, :mobile_number, :customer_address,
                    :producttype, :productname, :area, :rate, :net_amount, :payamount, :due_amount,
                    :corner_charge, :gross_amount, :created_date, :remarks, :emi_month
                )";

    $customer_stmt = $pdo->prepare($customer_sql);
    $customer_stmt->execute([
      ':invoice_id'       => $invoice_id,
      ':member_id'        => $_POST['member_id'],
      ':customer_id'      => $_POST['customer_id'],
      ':customer_name'    => $_POST['customer_name'],
      ':mobile_number'    => $_POST['refer_mobile'],
      ':customer_address' => $_POST['address'],
      ':producttype'      => $_POST['product_type'],
      ':productname'      => $productname,
      ':area'             => $_POST['squarefeet'],
      ':rate'             => $_POST['rate'],
      ':net_amount'       => $net_amount,
      ':payamount'        => $payamount,
      ':due_amount'       => $due_amount,
      ':corner_charge'    => $_POST['corner_charge'],
      ':gross_amount'     => $_POST['gross_amount'],
      ':created_date'     => $_POST['date'],
      ':remarks'          => $remarks,
      ':emi_month'        => $emi_month
    ]);

    // Insert into receiveallpayment
    $payment_sql = "INSERT INTO receiveallpayment (
                    invoice_id, voucher_number, bill_prepared_by_id, bill_prepared_by_name,
                    member_id, customer_id, customer_name, productname, rate, area,
                    net_amount, payment_mode, payamount, discount_percent, discount_rs,
                    plot_type, corner_charge, cheque_number, bank_name, cheque_date,
                    utr_number, neft_payment, rtgs_payment, due_amount, created_date, remarks, emi_month
                ) VALUES (
                    :invoice_id, :voucher_number, :bill_prepared_by_id, :bill_prepared_by_name,
                    :member_id, :customer_id, :customer_name, :productname, :rate, :area,
                    :net_amount, :payment_mode, :payamount, :discount_percent, :discount_rs,
                    :plot_type, :corner_charge, :cheque_number, :bank_name, :cheque_date,
                    :utr_number, :neft_payment, :rtgs_payment, :due_amount, :created_date, :remarks, :emi_month
                )";

    $payment_stmt = $pdo->prepare($payment_sql);
    $payment_stmt->execute([
      ':invoice_id'           => $invoice_id,
      ':voucher_number'       => $voucher_number,
      ':bill_prepared_by_id'   => $employee_id,
      ':bill_prepared_by_name' => $employee_name,
      ':member_id'            => $_POST['member_id'],
      ':customer_id'          => $_POST['customer_id'],
      ':customer_name'        => $_POST['customer_name'],
      ':productname'          => $productname,
      ':rate'                 => $_POST['rate'],
      ':area'                 => $_POST['squarefeet'],
      ':net_amount'           => $net_amount,
      ':payment_mode'         => $payment_mode,
      ':payamount'            => $payamount,
      ':discount_percent'     => $discount_percent,
      ':discount_rs'          => $discount_rs,
      ':plot_type'            => $_POST['plot_type'],
      ':corner_charge'        => $_POST['corner_charge'],
      ':cheque_number'        => $payment_mode == 'cheque' ? $_POST['cheque_number'] : null,
      ':bank_name'            => $payment_mode == 'cheque' ? $_POST['bank_name'] : null,
      ':cheque_date'          => $payment_mode == 'cheque' ? $_POST['cheque_date'] : null,
      ':utr_number'           => $payment_mode == 'bank_transfer' && $utr_number ? $utr_number : null,
      ':neft_payment'         => $payment_mode == 'bank_transfer' && $neft_payment ? $neft_payment : null,
      ':rtgs_payment'         => $payment_mode == 'bank_transfer' && $rtgs_payment ? $rtgs_payment : null,
      ':due_amount'           => $due_amount,
      ':created_date'         => $_POST['date'],
      ':remarks'              => $remarks,
      ':emi_month'            => $emi_month
    ]);

    // Insert into emi_schedule_records
    $emi_sql = "INSERT INTO emi_schedule_records (
                    invoice_id, voucher_number, bill_prepared_by_id, bill_prepared_by_name,
                    member_id, customer_id, customer_name, customer_mobile, customer_email,
                    aadhar_number, pan_number, nominee_name, nominee_aadhar, customer_address,
                    state, district, refer_name, refer_mobile, refer_email,
                    productname, rate, area, net_amount, payment_mode, payamount,
                    discount_percent, discount_rs, plot_type, corner_charge,
                    cheque_number, bank_name, cheque_date, utr_number, neft_payment,
                    rtgs_payment, due_amount, created_date, remarks, emi_month,
                    product_type, quantity, gross_amount, point, month_number,
                    emi_amount, due_date
                ) VALUES (
                    :invoice_id, :voucher_number, :bill_prepared_by_id, :bill_prepared_by_name,
                    :member_id, :customer_id, :customer_name, :customer_mobile, :customer_email,
                    :aadhar_number, :pan_number, :nominee_name, :nominee_aadhar, :customer_address,
                    :state, :district, :refer_name, :refer_mobile, :refer_email,
                    :productname, :rate, :area, :net_amount, :payment_mode, :payamount,
                    :discount_percent, :discount_rs, :plot_type, :corner_charge,
                    :cheque_number, :bank_name, :cheque_date, :utr_number, :neft_payment,
                    :rtgs_payment, :due_amount, :created_date, :remarks, :emi_month,
                    :product_type, :quantity, :gross_amount, :point, :month_number,
                    :emi_amount, :due_date
                )";

    $emi_stmt = $pdo->prepare($emi_sql);

    // If EMI is applicable, insert one record per EMI month
    if ($emi_month > 0 && $due_amount > 0) {
      $emi_per_month = $due_amount / $emi_month;
      $start_date = new DateTime($_POST['date']);
      for ($i = 1; $i <= $emi_month; $i++) {
        $due_date = clone $start_date;
        $due_date->modify("+$i month");
        $emi_stmt->execute([
          ':invoice_id'           => $invoice_id,
          ':voucher_number'       => $voucher_number,
          ':bill_prepared_by_id'   => $employee_id,
          ':bill_prepared_by_name' => $employee_name,
          ':member_id'            => $_POST['member_id'],
          ':customer_id'          => $_POST['customer_id'],
          ':customer_name'        => $_POST['customer_name'],
          ':customer_mobile'      => $_POST['customer_mobile'],
          ':customer_email'       => $_POST['customer_email'],
          ':aadhar_number'        => $_POST['aadhar_number'],
          ':pan_number'           => $_POST['pan_number'],
          ':nominee_name'         => $_POST['nominee_name'],
          ':nominee_aadhar'       => $_POST['nominee_aadhar'],
          ':customer_address'     => $_POST['address'],
          ':state'                => $_POST['state'],
          ':district'             => $_POST['district'],
          ':refer_name'           => $_POST['refer_name'],
          ':refer_mobile'         => $_POST['refer_mobile'],
          ':refer_email'          => $_POST['refer_email'],
          ':productname'          => $productname,
          ':rate'                 => $_POST['rate'],
          ':area'                 => $_POST['squarefeet'],
          ':net_amount'           => $net_amount,
          ':payment_mode'         => $payment_mode,
          ':payamount'            => $payamount,
          ':discount_percent'     => $discount_percent,
          ':discount_rs'          => $discount_rs,
          ':plot_type'            => $_POST['plot_type'],
          ':corner_charge'        => $_POST['corner_charge'],
          ':cheque_number'        => $payment_mode == 'cheque' ? $_POST['cheque_number'] : null,
          ':bank_name'            => $payment_mode == 'cheque' ? $_POST['bank_name'] : null,
          ':cheque_date'          => $payment_mode == 'cheque' ? $_POST['cheque_date'] : null,
          ':utr_number'           => $payment_mode == 'bank_transfer' && $utr_number ? $utr_number : null,
          ':neft_payment'         => $payment_mode == 'bank_transfer' && $neft_payment ? $neft_payment : null,
          ':rtgs_payment'         => $payment_mode == 'bank_transfer' && $rtgs_payment ? $rtgs_payment : null,
          ':due_amount'           => $due_amount,
          ':created_date'         => $_POST['date'],
          ':remarks'              => $remarks,
          ':emi_month'            => $emi_month,
          ':product_type'         => $_POST['product_type'],
          ':quantity'             => $_POST['quantity'],
          ':gross_amount'         => $_POST['gross_amount'],
          ':point'                => $_POST['point'],
          ':month_number'         => $i,
          ':emi_amount'           => $emi_per_month,
          ':due_date'             => $due_date->format('Y-m-d')
        ]);
      }
    } else {
      // Insert a single record without EMI details if no EMI is selected
      $emi_stmt->execute([
        ':invoice_id'           => $invoice_id,
        ':voucher_number'       => $voucher_number,
        ':bill_prepared_by_id'   => $employee_id,
        ':bill_prepared_by_name' => $employee_name,
        ':member_id'            => $_POST['member_id'],
        ':customer_id'          => $_POST['customer_id'],
        ':customer_name'        => $_POST['customer_name'],
        ':customer_mobile'      => $_POST['customer_mobile'],
        ':customer_email'       => $_POST['customer_email'],
        ':aadhar_number'        => $_POST['aadhar_number'],
        ':pan_number'           => $_POST['pan_number'],
        ':nominee_name'         => $_POST['nominee_name'],
        ':nominee_aadhar'       => $_POST['nominee_aadhar'],
        ':customer_address'     => $_POST['address'],
        ':state'                => $_POST['state'],
        ':district'             => $_POST['district'],
        ':refer_name'           => $_POST['refer_name'],
        ':refer_mobile'         => $_POST['refer_mobile'],
        ':refer_email'          => $_POST['refer_email'],
        ':productname'          => $productname,
        ':rate'                 => $_POST['rate'],
        ':area'                 => $_POST['squarefeet'],
        ':net_amount'           => $net_amount,
        ':payment_mode'         => $payment_mode,
        ':payamount'            => $payamount,
        ':discount_percent'     => $discount_percent,
        ':discount_rs'          => $discount_rs,
        ':plot_type'            => $_POST['plot_type'],
        ':corner_charge'        => $_POST['corner_charge'],
        ':cheque_number'        => $payment_mode == 'cheque' ? $_POST['cheque_number'] : null,
        ':bank_name'            => $payment_mode == 'cheque' ? $_POST['bank_name'] : null,
        ':cheque_date'          => $payment_mode == 'cheque' ? $_POST['cheque_date'] : null,
        ':utr_number'           => $payment_mode == 'bank_transfer' && $utr_number ? $utr_number : null,
        ':neft_payment'         => $payment_mode == 'bank_transfer' && $neft_payment ? $neft_payment : null,
        ':rtgs_payment'         => $payment_mode == 'bank_transfer' && $rtgs_payment ? $rtgs_payment : null,
        ':due_amount'           => $due_amount,
        ':created_date'         => $_POST['date'],
        ':remarks'              => $remarks,
        ':emi_month'            => $emi_month,
        ':product_type'         => $_POST['product_type'],
        ':quantity'             => $_POST['quantity'],
        ':gross_amount'         => $_POST['gross_amount'],
        ':point'                => $_POST['point'],
        ':month_number'         => null,
        ':emi_amount'           => null,
        ':due_date'             => null
      ]);
    }

    // Update the products table status to 'booked'
    $update_sql = "UPDATE products SET Status = 'booked' WHERE ProductName = :product_name AND product_type_id IN (1, 2)";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([
      ':product_name' => $productname
    ]);

    // Prepare data for email
    $email_data = [
      'voucher_number'        => $voucher_number,
      'bill_prepared_by_id'    => $employee_id,
      'bill_prepared_by_name'  => $employee_name,
      'member_id'             => $_POST['member_id'],
      'refer_name'            => $_POST['refer_name'],
      'date'                  => $_POST['date'],
      'customer_name'         => $_POST['customer_name'],
      'customer_mobile'       => $_POST['customer_mobile'],
      'customer_email'        => $_POST['customer_email'],
      'product_type'          => $_POST['product_type'],
      'product_name'          => $_POST['product_name'],
      'squarefeet'            => $_POST['squarefeet'],
      'rate'                  => $_POST['rate'],
      'quantity'              => $_POST['quantity'],
      'gross_amount'          => $_POST['gross_amount'],
      'discount_percent'      => $discount_percent,
      'discount_rs'           => $discount_rs,
      'corner_charge'         => $_POST['corner_charge'],
      'net_amount'            => $net_amount,
      'payment_mode'          => $payment_mode,
      'payamount'             => $payamount,
      'due_amount'            => $due_amount,
      'cheque_number'         => $payment_mode == 'cheque' ? $_POST['cheque_number'] : null,
      'bank_name'             => $payment_mode == 'cheque' ? $_POST['bank_name'] : null,
      'cheque_date'           => $payment_mode == 'cheque' ? $_POST['cheque_date'] : null,
      'utr_number'            => $payment_mode == 'bank_transfer' ? $utr_number : null,
      'neft_payment'          => $payment_mode == 'bank_transfer' ? $neft_payment : null,
      'rtgs_payment'          => $payment_mode == 'bank_transfer' ? $rtgs_payment : null,
      'remarks'               => $remarks,
      'emi_month'             => $emi_month
    ];

    // Send invoice email
    if (!sendInvoiceEmail($pdo, $invoice_id, $_POST['customer_email'], $_POST['customer_name'], $email_data)) {
      echo "<script>alert('Record inserted successfully, but failed to send invoice email to one or more recipients.');</script>";
    } else {
      echo "<script>alert('Record inserted successfully and invoice emailed to customer and admin!');</script>";
    }

    // Redirect to success message
    echo "<script>
                    window.location.href='Saleinvoice.php?invoice_id=" . urlencode($invoice_id) . "&member_id=" . urlencode($memberid) . "';
                </script>";
  } catch (PDOException $e) {
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
  }
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta
    name="viewport"
    content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0" />
  <title>Hari Home Developers</title>
  <link
    rel="shortcut icon"
    type="image/x-icon"
    href="../../icon/harihomes1-fevicon.png" />
  <link rel="stylesheet" href="../resources/vendors/feather/feather.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/ti-icons/css/themify-icons.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/css/vendor.bundle.base.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/select2/select2.min.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/select2-bootstrap-theme/select2-bootstrap.min.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/ti-icons/css/themify-icons.css" />
  <link
    rel="stylesheet"
    type="text/css"
    href="../resources/js/select.dataTables.min.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/mdi/css/materialdesignicons.min.css" />
  <link
    rel="stylesheet"
    href="../resources/vendors/fullcalendar/fullcalendar.min.css" />
  <link
    rel="stylesheet"
    href="../resources/css/vertical-layout-light/style.css" />
  <link rel="stylesheet" href="../resources/css/style.css" />
  <link href="assets/css/vendor.bundle.base.css" rel="stylesheet" />
  <link href="../assets/css/vendor.bundle.base.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/themify-icons.css" />

  <style>
    .navbar .navbar-brand-wrapper .navbar-brand img {
      margin-top: 0px;
    }

    #ct7 {
      color: #fff;
      padding: 18px 8px;
      font-size: 16px;
      font-weight: 900;
    }
  </style>

  <style>
    .payment-details,
    .emi-report {
      padding: 15px;
      border-radius: 5px;
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
    }

    .emi-report table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .emi-report th,
    .emi-report td {
      border: 1px solid #dee2e6;
      padding: 8px;
      text-align: left;
    }

    .emi-report th {
      background-color: #e9ecef;
      font-weight: bold;
    }

    .emi-report {
      margin-left: 10px;
    }

    .form-group label {
      font-weight: 600;
    }

    .box-body {
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .box-footer {
      margin-top: 20px;
    }

    h3 {
      color: #343a40;
      margin-top: 20px;
      margin-bottom: 15px;
    }
  </style>

  <script>
    function display_ct7() {
      var x = new Date();
      var ampm = x.getHours() >= 12 ? " PM" : " AM";
      var hours = x.getHours() % 12;
      hours = hours ? hours : 12;
      hours = hours.toString().length == 1 ? "0" + hours.toString() : hours;

      var minutes = x.getMinutes().toString();
      minutes = minutes.length == 1 ? "0" + minutes : minutes;

      var seconds = x.getSeconds().toString();
      seconds = seconds.length == 1 ? "0" + seconds : seconds;

      var month = (x.getMonth() + 1).toString();
      month = month.length == 1 ? "0" + month : month;

      var dt = x.getDate().toString();
      dt = dt.length == 1 ? "0" + dt : dt;

      var x1 = dt + "-" + month + "-" + x.getFullYear();
      x1 = x1 + " " + hours + ":" + minutes + ":" + seconds + " " + ampm;
      document.getElementById("ct7").innerHTML = x1;
    }

    function startTime() {
      display_ct7();
      setInterval(display_ct7, 1000);
    }

    window.onload = startTime;
  </script>

  <script>
    let defaultMerchantConfiguration = {
      root: "",
      style: {
        bodyColor: "",
        themeBackgroundColor: "",
        themeColor: "",
        headerBackgroundColor: "",
        headerColor: "",
        errorColor: "",
        successColor: "",
      },
      flow: "DEFAULT",
      data: {
        orderId: "",
        token: "",
        tokenType: "TXN_TOKEN",
        amount: "",
        userDetail: {
          mobileNumber: "",
          name: "",
        },
      },
      merchant: {
        mid: "",
        name: "",
        redirect: true,
      },
      labels: {},
      payMode: {
        labels: {},
        filter: [],
        order: [],
      },
      handler: {},
    };
  </script>
  <style type="text/css">
    /* Chart.js */
    @keyframes chartjs-render-animation {
      from {
        opacity: 0.99;
      }

      to {
        opacity: 1;
      }
    }

    .chartjs-render-monitor {
      animation: chartjs-render-animation 1ms;
    }

    .chartjs-size-monitor,
    .chartjs-size-monitor-expand,
    .chartjs-size-monitor-shrink {
      position: absolute;
      direction: ltr;
      left: 0;
      top: 0;
      right: 0;
      bottom: 0;
      overflow: hidden;
      pointer-events: none;
      visibility: hidden;
      z-index: -1;
    }

    .chartjs-size-monitor-expand>div {
      position: absolute;
      width: 1000000px;
      height: 1000000px;
      left: 0;
      top: 0;
    }

    .chartjs-size-monitor-shrink>div {
      position: absolute;
      width: 200%;
      height: 200%;
      left: 0;
      top: 0;
    }

    .franchiseSidebar:hover {
      background: #ff9027 !important;
    }
  </style>

  <script language="javascript" type="text/javascript">
    function isNumberKey(evt) {
      var charCode = evt.which ? evt.which : event.keyCode;
      if (
        charCode > 31 &&
        charCode != 45 &&
        charCode != 40 &&
        charCode != 46 &&
        charCode != 41 &&
        (charCode < 48 || charCode > 57)
      )
        return false;

      return true;
    }
  </script>

  <style>
    b {
      color: #464e58;
    }
  </style>

  <!-- <style>
    /* To make the form responsive with two columns */
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      /* Space between the form elements */
    }

    .form-group {
      flex: 1 1 45%;
      /* Makes the fields responsive, each taking up 45% of the width */
    }

    /* //here is the form design// */

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin: 20px auto;
      max-width: 900px;
    }

    .card-body {
      padding: 30px;
    }

    h3 {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 20px;
      border-bottom: 2px solid #3498db;
      padding-bottom: 5px;
    }

    .form-group label {
      font-weight: 500;
      color: #34495e;
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #ced4da;
      padding: 10px;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
      border-color: #3498db;
      box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
    }

    .form-control[readonly] {
      background-color: #e9ecef;
    }

    .btn-info,
    .btn-success {
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 500;
      transition: transform 0.2s ease, background-color 0.3s ease;
    }

    .btn-info:hover,
    .btn-success:hover {
      transform: translateY(-2px);
    }

    .btn-info {
      background-color: #3498db;
      border-color: #3498db;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
    }

    .payment-details {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      margin-top: 10px;
    }

    .box-footer {
      display: flex;
      justify-content: center;
      gap: 17px;
      margin-top: 21px;
    }


    /* //end here  */
    @media (max-width: 768px) {
      .form-group {
        flex: 1 1 100%;
        /* On smaller screens, take 100% width */
      }
    }
  </style> -->

  <style>
    /* * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f5f7fa;
      color: #333;
      line-height: 1.6;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    } */

    /* Form design */
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
      margin: 20px auto;
      max-width: 1000px;
      background-color: #fff;
    }

    .card-body {
      padding: 30px;
    }

    h3 {
      color: #2c3e50;
      font-weight: 600;
      margin: 30px 0 20px 0;
      border-bottom: 2px solid #3498db;
      padding-bottom: 8px;
      font-size: 22px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -10px;
    }

    .form-group {
      flex: 1 1 calc(50% - 20px);
      margin: 0 10px 20px 10px;
    }

    .form-group.col-md-6 {
      flex: 1 1 calc(50% - 20px);
      margin: 0 10px 20px 10px;
    }

    .form-group label {
      display: block;
      font-weight: 500;
      color: #34495e;
      margin-bottom: 8px;
    }

    .form-control {
      width: 100%;
      border-radius: 8px;
      border: 1px solid #cbd5e0;
      padding: 12px 15px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #3498db;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.25);
      outline: none;
    }

    .form-control[readonly] {
      background-color: #edf2f7;
      cursor: not-allowed;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 100px;
    }

    select.form-control {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 16px;
      padding-right: 40px;
    }

    .payment-details {
      background: #f8fafc;
      padding: 20px;
      border-radius: 12px;
      margin-top: 15px;
      border: 1px solid #e2e8f0;
    }

    .box-footer {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 30px;
      padding: 20px 0;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      padding: 12px 25px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      color: #fff;
    }

    .btn i {
      margin-right: 8px;
    }

    .btn-info {
      background-color: #3498db;
      box-shadow: 0 4px 6px rgba(52, 152, 219, 0.25);
    }

    .btn-success {
      background-color: #2ecc71;
      box-shadow: 0 4px 6px rgba(46, 204, 113, 0.25);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
    }

    .btn-info:hover {
      background-color: #2980b9;
    }

    .btn-success:hover {
      background-color: #27ae60;
    }

    /* Responsive design */
    @media (max-width: 768px) {

      .form-group,
      .form-group.col-md-6 {
        flex: 1 1 100%;
        margin: 0 10px 15px 10px;
      }

      .card-body {
        padding: 20px;
      }

      .box-footer {
        flex-direction: column;
        align-items: center;
      }

      .btn {
        width: 100%;
        margin-bottom: 10px;
      }
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card {
      animation: fadeIn 0.5s ease-out;
    }
  </style>


  <link
    href="/WebResource.axd?d=SIiLqK_Hn_nAW7HOB23rGvpDqc-LDIVnCyI3P0T_WT4YqxfWEeejUQ40y98xMT8saEkxuPHd1ZqUCrya2t0V5mJzzmXO7nIFHUiTMyWggKklRknJsW4_HpBvzWnkl6mKClKXQ1SNIJBck2mezqRNTg2&amp;t=638629042599655538"
    type="text/css"
    rel="stylesheet" />
</head>

<body class="hold-transition skin-blue sidebar-mini">

  <div class="wrapper">
    <div class="container-scroller">


      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <?php include 'account-headersidepanel.php';


        ?>

        <div class="main-panel">
          <div class="stretch-card">
            <div class="container" style="max-width: unset;">
              <div class="card" style="margin:unset;max-width:unset;">
                <div class="card-body">
                  <form method="post" action="" onsubmit="return appendMemberID();" id="form1">
                    <div class="box-body">
                      <!-- Date Field -->
                      <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" name="date" id="date" onchange="calculateEMI()">
                      </div>

                      <!-- Refer By Section -->
                      <h3>Refer By</h3>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label>Member ID:</label>
                          <input type="text" class="form-control" name="member_id" id="member_id" placeholder="Enter Member ID">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Name:</label>
                          <input type="text" class="form-control" name="refer_name" id="refer_name" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Mobile Number:</label>
                          <input type="text" class="form-control" name="refer_mobile" id="refer_mobile" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Email ID:</label>
                          <input type="email" class="form-control" name="refer_email" id="refer_email" readonly>
                        </div>
                      </div>

                      <div class="form-group">
                        <label>Customer ID:</label>
                        <input type="text" class="form-control" name="customer_id" id="customer_id" onmouseleave="fetchCustomerData()" placeholder="Enter Customer ID">
                      </div>

                      <div class="form-row d-none">
                        <div class="form-group col-md-6">
                          <label>Customer Name:</label>
                          <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Enter Customer Name">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Customer Mobile Number:</label>
                          <input type="text" class="form-control" name="customer_mobile" id="customer_mobile" placeholder="Enter Mobile Number">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Email:</label>
                          <input type="email" class="form-control" name="customer_email" id="customer_email" placeholder="Enter Email">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Aadhar Number:</label>
                          <input type="text" class="form-control" name="aadhar_number" id="aadhar_number" placeholder="Enter Aadhar Number">
                        </div>
                        <div class="form-group col-md-6">
                          <label>PAN:</label>
                          <input type="text" class="form-control" name="pan_number" id="pan_number" placeholder="Enter PAN">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Nominee Name:</label>
                          <input type="text" class="form-control" name="nominee_name" id="nominee_name" placeholder="Enter Nominee Name">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Nominee Aadhar:</label>
                          <input type="text" class="form-control" name="nominee_aadhar" id="nominee_aadhar" placeholder="Enter Nominee Aadhar">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Address:</label>
                          <textarea class="form-control" name="address" rows="3" id="address" placeholder="Enter Address"></textarea>
                        </div>
                        <div class="form-group col-md-6">
                          <label>State:</label>
                          <input type="text" class="form-control" name="state" id="state" placeholder="Enter State">
                        </div>
                        <div class="form-group col-md-6">
                          <label>District:</label>
                          <input type="text" class="form-control" name="district" id="district" placeholder="Enter District">
                        </div>
                      </div>

                      <!-- Product Details Section -->
                      <h3>Product Details</h3>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label>Product Type:</label>
                          <select class="form-control" name="product_type" id="product_type">
                            <option value="">Select Product Type</option>
                            <!-- dynamic_product_type -->
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Product Name:</label>
                          <select class="form-control" name="product_name" id="product_name">
                            <option value="">Select Product</option>
                            <!-- dynamic_product_name -->
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Square Feet:</label>
                          <select class="form-control" name="squarefeet" id="squarefeet">
                            <option value="">Select Square Feet</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Rate:</label>
                          <input type="text" class="form-control" name="rate" id="rate" placeholder="Enter Rate" oninput="calculateEMI()">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Point:</label>
                          <input type="text" class="form-control" name="point" id="point" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Quantity:</label>
                          <input type="text" class="form-control" name="quantity" id="quantity" readonly>
                        </div>
                      </div>

                      <!-- Payment Details -->
                      <h3>Payment Details</h3>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label>Amount:</label>
                          <input type="text" class="form-control" name="amount" id="amount" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Discount %:</label>
                          <input type="text" class="form-control" name="discount_percent" id="discount_percent" placeholder="Enter Discount Percentage" oninput="calculateEMI()">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Discount (Rs):</label>
                          <input type="text" class="form-control" name="discount_rs" id="discount_rs" placeholder="Enter Discount Amount" oninput="calculateEMI()">
                        </div>
                        <div class="form-group col-md-6">
                          <label>Gross Amount:</label>
                          <input type="text" class="form-control" name="gross_amount" id="gross_amount" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Plot Type:</label>
                          <select class="form-control" name="plot_type" id="plot_type" onchange="calculateEMI()">
                            <option value="normal">Normal Charge</option>
                            <option value="corner">Corner Charge</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Corner Charge:</label>
                          <input type="text" class="form-control" name="corner_charge" id="corner_charge" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Net Amount:</label>
                          <input type="text" class="form-control" name="net_amount" id="net_amount" readonly>
                        </div>
                        <div class="form-group col-md-6">
                          <label>Voucher Number:</label>
                          <input type="text" class="form-control" name="voucher_num" id="voucher" placeholder="Enter Voucher Number">
                        </div>
                      </div>

                      <!-- Payment Mode -->
                      <h3>Payment Mode</h3>
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label>Payment Mode:</label>
                          <select class="form-control" name="payment_mode" id="payment_mode" onchange="showPaymentDetails(); calculateEMI()">
                            <option value="">Select Payment Mode</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="bank_transfer">Bank Transfer</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                          <label>EMI Months:</label>
                          <select class="form-control" name="emi_month" id="emi_month" onchange="calculateEMI()">
                            <option value="">Select EMI Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                            <option value="18">18 Months</option>
                            <option value="24">24 Months</option>
                            <option value="36">36 Months</option>
                            <option value="54">54 Months</option>
                          </select>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <!-- Payment Details Sections -->
                          <div id="cash_details" class="payment-details" style="display:none;">
                            <div class="form-group">
                              <label>Amount Paid:</label>
                              <input type="text" class="form-control" name="cash_amount" id="cash_amount" placeholder="Enter Cash Amount" oninput="calculateEMI()">
                            </div>
                          </div>

                          <div id="cheque_details" class="payment-details" style="display:none;">
                            <div class="form-row">
                              <div class="form-group col-md-6">
                                <label>Cheque Amount:</label>
                                <input type="text" class="form-control" name="cheque_amount" id="cheque_amount" placeholder="Enter Cheque Amount" oninput="calculateEMI()">
                              </div>
                              <div class="form-group col-md-6">
                                <label>Cheque Number:</label>
                                <input type="text" class="form-control" name="cheque_number" id="cheque_number" placeholder="Enter Cheque Number">
                              </div>
                              <div class="form-group col-md-6">
                                <label>Bank Name:</label>
                                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="Enter Bank Name">
                              </div>
                              <div class="form-group col-md-6">
                                <label>Cheque Date:</label>
                                <input type="date" class="form-control" name="cheque_date" id="cheque_date">
                              </div>
                            </div>
                          </div>

                          <div id="bank_transfer_details" class="payment-details" style="display:none;">
                            <div class="form-row">
                              <div class="form-group col-md-6">
                                <label>Amount Transferred:</label>
                                <input type="text" class="form-control" name="transfer_amount" id="transfer_amount" placeholder="Enter Transfer Amount" oninput="calculateEMI()">
                              </div>
                              <div class="form-group col-md-6">
                                <label>NEFT Reference Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="neft_payment" id="neft_payment" placeholder="Enter NEFT Reference Number">
                              </div>
                              <div class="form-group col-md-6">
                                <label>RTGS Reference Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="rtgs_payment" id="rtgs_payment" placeholder="Enter RTGS Reference Number">
                              </div>
                              <div class="form-group col-md-6">
                                <label>UTR Number:</label>
                                <input type="text" class="form-control bank-transfer-field" name="utr_number" id="utr_number" placeholder="Enter UTR Number">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <!-- EMI Calculation Report -->
                          <div id="emi_report" class="emi-report" style="display:none;">
                            <label>EMI Calculation Report:</label>
                            <div id="emi_calculation">
                              <p>EMI Amount per Month: <span id="emi_amount">0.00</span></p>
                              <p>Due Amount: <span id="emi_due_amount">0.00</span></p>
                              <table>
                                <thead>
                                  <tr>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                  </tr>
                                </thead>
                                <tbody id="emi_schedule"></tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label>Remarks:</label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Enter any remarks"></textarea>
                      </div>

                      <!-- Form Footer -->
                      <div class="box-footer">
                        <button type="submit" class="btn btn-info" id="submitBtn" name="btnsubmit">
                          <i class="fas fa-save"></i> Submit
                        </button>
                        <button type="button" class="btn btn-success" onclick="printInvoice('<?php echo $invoice_id; ?>', '<?php echo $memberid; ?>')">
                          <i class="fas fa-print"></i> Print
                        </button>
                      </div>
                    </div>
                  </form>
                </div>


              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <?php include 'account-footer.php'; ?>

  </div>

  <!-- print section starts here  -->



  <a href="#" target="_blank">
    <!-- partial -->
  </a>
  <!-- search box for options-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link
    href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
  <script src="../resources/vendors/select2/select2.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- Plugin js for this page -->
  <script src="../resources/vendors/chart.js/Chart.min.js"></script>
  <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="../resources/js/dataTables.select.min.js"></script>
  <script src="../resources/js/custom.js"></script>
  <!-- End plugin js for this page -->
  <script src="../resources/vendors/moment/moment.min.js"></script>
  <script src="../resources/vendors/fullcalendar/fullcalendar.min.js"></script>

  <!-- inject:js -->
  <script src="../resources/js/off-canvas.js"></script>
  <script src="../resources/js/hoverable-collapse.js"></script>
  <script src="../resources/js/template.js"></script>
  <script src="../resources/js/settings.js"></script>
  <script src="../resources/js/todolist.js"></script>

  <script src="../resources/js/calendar.js"></script>
  <script src="../resources/js/tabs.js"></script>

  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="../resources/js/dashboard.js"></script>
  <script src="../resources/js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->
  <!-- Custom js for this page-->
  <script src="../resources/js/file-upload.js"></script>
  <script src="../resources/js/typeahead.js"></script>
  <script src="../resources/js/select2.js"></script>
  <!-- End custom js for this page-->

  <!-- plugin js for this page -->
  <script src="../resources/vendors/tinymce/tinymce.min.js"></script>
  <script src="../resources/vendors/quill/quill.min.js"></script>
  <script src="../resources/vendors/simplemde/simplemde.min.js"></script>
  <script src="../resources/js/editorDemo.js"></script>

  <!-- Custom js for this page-->
  <script src="../resources/js/data-table.js"></script>

  <div style="margin-left: 250px">
    <span id="lblMsg"></span>
  </div>
  <style>
    #lblMsg {
      visibility: hidden;
    }
  </style>

  <script type="text/javascript">
    //<![CDATA[
    var Page_Validators = new Array(
      document.getElementById(
        "ContentPlaceHolder1_RegularExpressionValidator4"
      )
    );
    //]]>
  </script>

  <script type="text/javascript">
    //<![CDATA[
    var ContentPlaceHolder1_RegularExpressionValidator4 = document.all ?
      document.all["ContentPlaceHolder1_RegularExpressionValidator4"] :
      document.getElementById(
        "ContentPlaceHolder1_RegularExpressionValidator4"
      );
    ContentPlaceHolder1_RegularExpressionValidator4.controltovalidate =
      "ContentPlaceHolder1_txtpurdate";
    ContentPlaceHolder1_RegularExpressionValidator4.focusOnError = "t";
    ContentPlaceHolder1_RegularExpressionValidator4.errormessage =
      "Invalid Date Format";
    ContentPlaceHolder1_RegularExpressionValidator4.validationGroup =
      "save";
    ContentPlaceHolder1_RegularExpressionValidator4.evaluationfunction =
      "RegularExpressionValidatorEvaluateIsValid";
    ContentPlaceHolder1_RegularExpressionValidator4.validationexpression =
      "^(((((0[1-9])|(1\\d)|(2[0-8]))\\/((0[1-9])|(1[0-2])))|((31\\/((0[13578])|(1[02])))|((29|30)\\/((0[1,3-9])|(1[0-2])))))\\/((20[0-9][0-9])|(19[0-9][0-9])))|((29\\/02\\/(19|20)(([02468][048])|([13579][26]))))$ ";
    //]]>
  </script>

  <script type="text/javascript">
    //<![CDATA[
    (function() {
      var fn = function() {
        $get(
          "ContentPlaceHolder1_ToolkitScriptManager1_HiddenField"
        ).value = "";
        Sys.Application.remove_init(fn);
      };
      Sys.Application.add_init(fn);
    })();
    var Page_ValidationActive = false;
    if (typeof ValidatorOnLoad == "function") {
      ValidatorOnLoad();
    }

    function ValidatorOnSubmit() {
      if (Page_ValidationActive) {
        return ValidatorCommonOnSubmit();
      } else {
        return true;
      }
    }
    Sys.Application.add_init(function() {
      $create(
        Sys.Extended.UI.CalendarBehavior, {
          format: "dd/MM/yyyy",
          id: "ContentPlaceHolder1_txtpurdate_CalendarExtender",
        },
        null,
        null,
        $get("ContentPlaceHolder1_txtpurdate")
      );
    });

    document.getElementById(
      "ContentPlaceHolder1_RegularExpressionValidator4"
    ).dispose = function() {
      Array.remove(
        Page_Validators,
        document.getElementById(
          "ContentPlaceHolder1_RegularExpressionValidator4"
        )
      );
    };
    //]]>
  </script>
  <!-- //here is the php all script// -->
  <script>
    document.getElementById('member_id').addEventListener('blur', function() {
      var memberId = this.value;
      if (memberId) {
        fetch('fetch_member.php?member_id=' + memberId)
          .then(response => response.json())
          .then(data => {
            document.getElementById('refer_name').value = data.m_name;
            document.getElementById('refer_mobile').value = data.m_num;
            document.getElementById('refer_email').value = data.m_email;
          });
      }
    });
  </script>


  <script>
    // Fetch and Populate Product Types
    fetch('fetch_product_types.php')
      .then(response => response.json())
      .then(data => {
        let productTypeSelect = document.getElementById('product_type');
        data.forEach(type => {
          let option = document.createElement('option');
          option.value = type.product_type_id;
          option.textContent = type.product_type_name;
          productTypeSelect.appendChild(option);
        });
      });

    // document.getElementById('product_type').addEventListener('change', function() {
    //   let productTypeId = this.value;
    //   fetch('fetch_products.php?product_type_id=' + productTypeId)
    //     .then(response => response.json())
    //     .then(data => {
    //       let productSelect = document.getElementById('product_name');
    //       productSelect.innerHTML = '<option value="">Select Product</option>';
    //       data.forEach(product => {
    //         let option = document.createElement('option');
    //         option.value = product.ProductName;
    //         option.textContent = product.ProductName;
    //         productSelect.appendChild(option);
    //       });
    //     });
    // });


    document.getElementById('product_type').addEventListener('change', function() {
      let productTypeId = this.value;
      fetch('fetch_products.php?product_type_id=' + productTypeId)
        .then(response => response.json())
        .then(data => {
          let productSelect = document.getElementById('product_name');
          productSelect.innerHTML = '<option value="">Select Product</option>';
          data.forEach(product => {
            let option = document.createElement('option');
            option.value = product.ProductName;
            option.textContent = product.ProductName;
            productSelect.appendChild(option);
          });

          // Clear squarefeet dropdown
          document.getElementById('squarefeet').innerHTML = '<option value="">Select Square Feet</option>';
        });
    });

    // When product name changes, fetch squarefeet options
    document.getElementById('product_name').addEventListener('change', function() {
      let productName = this.value;
      fetch('fetch_squarefeet.php?product_name=' + encodeURIComponent(productName))
        .then(response => response.json())
        .then(data => {
          let squarefeetSelect = document.getElementById('squarefeet');
          squarefeetSelect.innerHTML = '<option value="">Select Square Feet</option>';
          data.forEach(item => {
            let option = document.createElement('option');
            option.value = item.Squarefeet;
            option.textContent = item.Squarefeet;
            squarefeetSelect.appendChild(option);
          });
        });
    });


    document.getElementById('product_name').addEventListener('change', function() {
      let productId = this.value;
      fetch('fetch_product_details.php?product_id=' + productId)
        .then(response => response.json())
        .then(data => {
          document.getElementById('squarefeet').value = data.Squarefeet;
          document.getElementById('point').value = data.Points;
          document.getElementById('quantity').value = data.Quantity;
        });
    });
  </script>


  <script>
    // Add event listeners
    document.getElementById('squarefeet').addEventListener('input', calculateAmount);
    document.getElementById('rate').addEventListener('input', calculateAmount);
    document.getElementById('discount_percent').addEventListener('input', calculateDiscount);
    document.getElementById('discount_rs').addEventListener('input', calculateDiscount);
    document.getElementById('plot_type').addEventListener('change', calculateDiscount);

    function calculateAmount() {
      let rate = parseFloat(document.getElementById('rate').value) || 0;
      let squareFeet = parseFloat(document.getElementById('squarefeet').value) || 0;
      let amount = rate * squareFeet;

      document.getElementById('amount').value = amount.toFixed(2);
      calculateDiscount();
    }

    function calculateDiscount() {
      // Get base amount
      let amount = parseFloat(document.getElementById('amount').value) || 0;

      // Calculate percentage discount
      let discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
      let percentageDiscountAmount = (amount * discountPercent) / 100;
      let grossAmount = amount - percentageDiscountAmount;

      document.getElementById('gross_amount').value = grossAmount.toFixed(2);

      // Calculate corner charge
      let plotType = document.getElementById('plot_type').value;
      let cornerCharge = (plotType === 'corner') ? (amount * 0.05) : 0;
      document.getElementById('corner_charge').value = cornerCharge.toFixed(2);

      // Calculate intermediate net amount (after % discount and corner charge)
      let intermediateNetAmount = grossAmount + cornerCharge;

      // Apply Rs discount
      let discountRs = parseFloat(document.getElementById('discount_rs').value) || 0;
      let finalNetAmount = intermediateNetAmount - discountRs;

      // Set final net amount
      document.getElementById('net_amount').value = finalNetAmount.toFixed(2);
    }
  </script>


  <!-- <script>
    function showPaymentDetails() {
      // Hide all payment-related sections initially
      document.getElementById("cash_details").style.display = "none";
      document.getElementById("cheque_details").style.display = "none";
      document.getElementById("bank_transfer_details").style.display = "none";

      // Get the selected payment method
      var paymentMethod = document.getElementById("payment_mode").value;

      // Show the corresponding section based on the selected payment method
      if (paymentMethod === "cash") {
        document.getElementById("cash_details").style.display = "block";
      } else if (paymentMethod === "cheque") {
        document.getElementById("cheque_details").style.display = "block";
      } else if (paymentMethod === "bank_transfer") {
        document.getElementById("bank_transfer_details").style.display = "block";
        // Ensure all bank transfer fields are visible initially
        document.getElementById("neft_payment").style.display = "block";
        document.getElementById("rtgs_payment").style.display = "block";
        document.getElementById("utr_number").style.display = "block";
      }
    }

    // Handle input events to hide other bank transfer fields
    function setupBankTransferFieldListeners() {
      const fields = ['neft_payment', 'rtgs_payment', 'utr_number'];
      fields.forEach(field => {
        document.getElementById(field).addEventListener('input', function() {
          if (this.value.trim() !== '') {
            fields.forEach(otherField => {
              if (otherField !== field) {
                document.getElementById(otherField).style.display = 'none';
              }
            });
          } else {
            // If the field is cleared, show all fields again
            fields.forEach(otherField => {
              document.getElementById(otherField).style.display = 'block';
            });
          }
        });
      });
    }

    // Call the setup function when the page loads
    document.addEventListener('DOMContentLoaded', setupBankTransferFieldListeners);

    // Form submission validation
    function appendMemberID() {
      var paymentMethod = document.getElementById("payment_mode").value;
      var netAmount = parseFloat(document.getElementById("net_amount").value) || 0;
      var cashAmount = parseFloat(document.getElementById("cash_amount").value) || 0;
      var chequeAmount = parseFloat(document.getElementById("cheque_amount").value) || 0;
      var transferAmount = parseFloat(document.getElementById("transfer_amount").value) || 0;
      var neftPayment = document.getElementById("neft_payment").value.trim();
      var rtgsPayment = document.getElementById("rtgs_payment").value.trim();
      var utrNumber = document.getElementById("utr_number").value.trim();
      var chequeNumber = document.getElementById("cheque_number").value.trim();
      var bankName = document.getElementById("bank_name").value.trim();
      var chequeDate = document.getElementById("cheque_date").value;

      if (!paymentMethod) {
        alert("Please select a payment mode.");
        return false;
      }

      if (paymentMethod === "cash" && (!cashAmount || cashAmount <= 0)) {
        alert("Please enter a valid cash amount.");
        return false;
      }

      if (paymentMethod === "cheque") {
        if (!chequeAmount || chequeAmount <= 0) {
          alert("Please enter a valid cheque amount.");
          return false;
        }
        if (!chequeNumber || !bankName || !chequeDate) {
          alert("Please provide cheque number, bank name, and cheque date.");
          return false;
        }
      }

      if (paymentMethod === "bank_transfer") {
        if (!transferAmount || transferAmount <= 0) {
          alert("Please enter a valid transfer amount.");
          return false;
        }
        const filledFields = [neftPayment, rtgsPayment, utrNumber].filter(val => val !== '').length;
        if (filledFields === 0) {
          alert("Please provide NEFT, RTGS, or UTR number for bank transfer.");
          return false;
        }
        if (filledFields > 1) {
          alert("Please provide only one of NEFT, RTGS, or UTR number.");
          return false;
        }
      }

      return true;
    }
  </script> -->


  <!-- <script>
      function printInvoice(invoiceId, memberId) {
        // Construct the URL with parameters
        const url = 'saleinvoiceprint.php?invoice_id=' + encodeURIComponent(invoiceId) + '&member_id=' + encodeURIComponent(memberId);

        // Open in new window and trigger print
        const printWindow = window.open(url, '_blank');
        printWindow.onload = function() {
          printWindow.print();
        }
      }
    </script> -->


  <script>
    function printInvoice(invoiceId, memberId) {
      const url = 'saleinvoiceprint.php?invoice_id=' + invoiceId + '&member_id=' + memberId;
      console.log('Opening URL:', url); // Debug
      const printWindow = window.open(url, '_blank');
      if (!printWindow) {
        alert('Please allow popups for this site to print the invoice.');
      }
    }
  </script>


  <script>
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);

    }
  </script>


  <script>
    function fetchCustomerData() {
      const customerId = document.getElementById('customer_id').value.trim();
      console.log('Fetching data for Customer ID:', customerId); // Debug: Check input value

      if (!customerId) {
        clearForm();
        return;
      }

      fetch('fetch_customer.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'customer_id=' + encodeURIComponent(customerId)
        })
        .then(response => {
          console.log('Response status:', response.status); // Debug: Check response status
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          console.log('Received data:', data); // Debug: Log the response data
          if (data.success) {
            document.getElementById('customer_name').value = data.customer_name || '';
            document.getElementById('customer_mobile').value = data.customer_mobile || '';
            document.getElementById('customer_email').value = data.customer_email || '';
            document.getElementById('aadhar_number').value = data.aadhar_number || '';
            document.getElementById('pan_number').value = data.pan_number || '';
            document.getElementById('nominee_name').value = data.nominee_name || '';
            document.getElementById('nominee_aadhar').value = data.nominee_aadhar || '';
            document.getElementById('address').value = data.address || '';
            document.getElementById('state').value = data.state || '';
            document.getElementById('district').value = data.district || '';
          } else {
            clearForm();
            alert('Customer not found: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Fetch error:', error); // Debug: Log any fetch errors
          clearForm();
          alert('An error occurred while fetching customer data: ' + error.message);
        });
    }

    function clearForm() {
      document.getElementById('customer_name').value = '';
      document.getElementById('customer_mobile').value = '';
      document.getElementById('customer_email').value = '';
      document.getElementById('aadhar_number').value = '';
      document.getElementById('pan_number').value = '';
      document.getElementById('nominee_name').value = '';
      document.getElementById('nominee_aadhar').value = '';
      document.getElementById('address').value = '';
      document.getElementById('state').value = '';
      document.getElementById('district').value = '';
    }
  </script>



  <script>
    function calculateEMI() {
      const emiMonths = parseInt(document.getElementById('emi_month').value) || 0;
      const netAmount = parseFloat(document.getElementById('net_amount').value) || 0;
      const paymentMode = document.getElementById('payment_mode').value;
      let paidAmount = 0;

      if (paymentMode === 'cash') {
        paidAmount = parseFloat(document.getElementById('cash_amount').value) || 0;
      } else if (paymentMode === 'cheque') {
        paidAmount = parseFloat(document.getElementById('cheque_amount').value) || 0;
      } else if (paymentMode === 'bank_transfer') {
        paidAmount = parseFloat(document.getElementById('transfer_amount').value) || 0;
      }

      const dueAmount = netAmount - paidAmount;
      const emiReport = document.getElementById('emi_report');
      const emiAmountSpan = document.getElementById('emi_amount');
      const emiDueAmountSpan = document.getElementById('emi_due_amount');
      const emiSchedule = document.getElementById('emi_schedule');

      if (emiMonths > 0 && dueAmount > 0 && paymentMode) {
        const emiPerMonth = (dueAmount / emiMonths).toFixed(2);

        // Format numbers in Indian style
        const formattedEmi = new Intl.NumberFormat('en-IN').format(emiPerMonth);
        const formattedDue = new Intl.NumberFormat('en-IN').format(dueAmount.toFixed(2));

        emiReport.style.display = 'block';
        emiAmountSpan.textContent = formattedEmi;
        emiDueAmountSpan.textContent = formattedDue;

        emiSchedule.innerHTML = '';
        const startDate = new Date(document.getElementById('date').value || new Date());

        for (let i = 1; i <= emiMonths; i++) {
          const dueDate = new Date(startDate);
          dueDate.setMonth(startDate.getMonth() + i);

          // Format date as DD-MM-YYYY
          const formattedDate = `${String(dueDate.getDate()).padStart(2, '0')}-${String(dueDate.getMonth() + 1).padStart(2, '0')}-${dueDate.getFullYear()}`;

          const tr = document.createElement('tr');
          tr.innerHTML = `
        <td>${i}</td>
        <td>${formattedEmi}</td>
        <td>${formattedDate}</td>
      `;
          emiSchedule.appendChild(tr);
        }
      } else {
        emiReport.style.display = 'none';
      }
    }

    function showPaymentDetails() {
      document.getElementById("cash_details").style.display = "none";
      document.getElementById("cheque_details").style.display = "none";
      document.getElementById("bank_transfer_details").style.display = "none";

      var paymentMethod = document.getElementById("payment_mode").value;

      if (paymentMethod === "cash") {
        document.getElementById("cash_details").style.display = "block";
      } else if (paymentMethod === "cheque") {
        document.getElementById("cheque_details").style.display = "block";
      } else if (paymentMethod === "bank_transfer") {
        document.getElementById("bank_transfer_details").style.display = "block";
        document.getElementById("neft_payment").style.display = "block";
        document.getElementById("rtgs_payment").style.display = "block";
        document.getElementById("utr_number").style.display = "block";
      }
    }

    function setupBankTransferFieldListeners() {
      const fields = ['neft_payment', 'rtgs_payment', 'utr_number'];
      fields.forEach(field => {
        document.getElementById(field).addEventListener('input', function() {
          if (this.value.trim() !== '') {
            fields.forEach(otherField => {
              if (otherField !== field) {
                document.getElementById(otherField).style.display = 'none';
              }
            });
          } else {
            fields.forEach(otherField => {
              document.getElementById(otherField).style.display = 'block';
            });
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', setupBankTransferFieldListeners);

    function appendMemberID() {
      var paymentMethod = document.getElementById("payment_mode").value;
      var netAmount = parseFloat(document.getElementById("net_amount").value) || 0;
      var cashAmount = parseFloat(document.getElementById("cash_amount").value) || 0;
      var chequeAmount = parseFloat(document.getElementById("cheque_amount").value) || 0;
      var transferAmount = parseFloat(document.getElementById("transfer_amount").value) || 0;
      var neftPayment = document.getElementById("neft_payment").value.trim();
      var rtgsPayment = document.getElementById("rtgs_payment").value.trim();
      var utrNumber = document.getElementById("utr_number").value.trim();
      var chequeNumber = document.getElementById("cheque_number").value.trim();
      var bankName = document.getElementById("bank_name").value.trim();
      var chequeDate = document.getElementById("cheque_date").value;

      if (!paymentMethod) {
        alert("Please select a payment mode.");
        return false;
      }

      if (paymentMethod === "cash" && (!cashAmount || cashAmount <= 0)) {
        alert("Please enter a valid cash amount.");
        return false;
      }

      if (paymentMethod === "cheque") {
        if (!chequeAmount || chequeAmount <= 0) {
          alert("Please enter a valid cheque amount.");
          return false;
        }
        if (!chequeNumber || !bankName || !chequeDate) {
          alert("Please provide cheque number, bank name, and cheque date.");
          return false;
        }
      }

      if (paymentMethod === "bank_transfer") {
        if (!transferAmount || transferAmount <= 0) {
          alert("Please enter a valid transfer amount.");
          return false;
        }
        const filledFields = [neftPayment, rtgsPayment, utrNumber].filter(val => val !== '').length;
        if (filledFields === 0) {
          alert("Please provide NEFT, RTGS, or UTR number for bank transfer.");
          return false;
        }
        if (filledFields > 1) {
          alert("Please provide only one of NEFT, RTGS, or UTR number.");
          return false;
        }
      }

      return true;
    }
  </script>

</body>

</html>