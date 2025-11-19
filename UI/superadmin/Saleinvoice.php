<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
  header('Location: ../../superadminlogin.php');
  exit();
}

error_reporting(0);


if ($_SESSION['sponsor_id'] === $sponsorid && $_SESSION['sponsor_pass'] === $sponsorpass && $_SESSION['status'] === 'active') {

  header('location:../../adminlogin.php');
}

$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : '';
$memberid = isset($_GET['member_id']) ? $_GET['member_id'] : ''; // Using $memberid to match your variable name


if (isset($_POST['btnsubmit'])) {
  $productname = $_POST['product_name'];

  $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';

  try {
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

    // Check for duplicate UTR number or cheque number
    if ($payment_mode === 'bank_transfer' && !empty($_POST['utr_number'])) {
      $utr_number = $_POST['utr_number'];
      // Check in sales_records
      // $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales_records WHERE utr_number = ?");
      // $stmt->execute([$utr_number]);
      // $sales_count = $stmt->fetchColumn();

      // Check in receiveallpayment
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE utr_number = ?");
      $stmt->execute([$utr_number]);
      $receive_count = $stmt->fetchColumn();

      if ($sales_count > 0 || $receive_count > 0) {
        echo "<script>alert('This UTR number already exists. Please use a unique UTR number.'); window.history.back();</script>";
        exit;
      }
    } elseif ($payment_mode === 'cheque' && !empty($_POST['cheque_number'])) {
      $cheque_number = $_POST['cheque_number'];
      // Check in sales_records
      // $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales_records WHERE cheque_number = ?");
      // $stmt->execute([$cheque_number]);
      // $sales_count = $stmt->fetchColumn();

      // Check in receiveallpayment
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM receiveallpayment WHERE cheque_number = ?");
      $stmt->execute([$cheque_number]);
      $receive_count = $stmt->fetchColumn();

      if ($sales_count > 0 || $receive_count > 0) {
        echo "<script>alert('This cheque number already exists. Please use a unique cheque number.'); window.history.back();</script>";
        exit;
      }
    }

    // Prepare SQL Insert Statement for sales_records
    $sql = "INSERT INTO sales_records (invoice_id,
                    date, member_id, refer_name, refer_mobile, refer_email, customer_id,
                    customer_name, customer_mobile, customer_email, aadhar_number, pan_number,
                    nominee_name, nominee_aadhar, address, state, district,
                    product_type, product_name, squarefeet, rate, point, quantity,
                    amount, discount_percent, discount_rs, gross_amount, plot_type,
                    corner_charge, net_amount, payment_mode, due_amount,
                    cash_amount, cheque_amount, cheque_number, bank_name, cheque_date,
                    transfer_amount, utr_number, remarks
                ) VALUES (:invid,
                    :date, :member_id, :refer_name, :refer_mobile, :refer_email, :customer_id,
                    :customer_name, :customer_mobile, :customer_email, :aadhar_number, :pan_number,
                    :nominee_name, :nominee_aadhar, :address, :state, :district,
                    :product_type, :product_name, :squarefeet, :rate, :point, :quantity,
                    :amount, :discount_percent, :discount_rs, :gross_amount, :plot_type,
                    :corner_charge, :net_amount, :payment_mode, :due_amount,
                    :cash_amount, :cheque_amount, :cheque_number, :bank_name, :cheque_date,
                    :transfer_amount, :utr_number, :remarks
                )";

    // Prepare and execute the sales_records insert
    $stmt = $pdo->prepare($sql);
    $memberid = isset($_POST['member_id']) ? $_POST['member_id'] : '';

    $stmt->execute([
      ':invid'            => $invoice_id,
      ':date'             => $_POST['date'],
      ':member_id'        => $_POST['member_id'],
      ':refer_name'       => $_POST['refer_name'],
      ':refer_mobile'     => $_POST['refer_mobile'],
      ':refer_email'      => $_POST['refer_email'],
      ':customer_id'      => $_POST['customer_id'],
      ':customer_name'    => $_POST['customer_name'],
      ':customer_mobile'  => $_POST['customer_mobile'],
      ':customer_email'   => $_POST['customer_email'],
      ':aadhar_number'    => $_POST['aadhar_number'],
      ':pan_number'       => $_POST['pan_number'],
      ':nominee_name'     => $_POST['nominee_name'],
      ':nominee_aadhar'   => $_POST['nominee_aadhar'],
      ':address'          => $_POST['address'],
      ':state'            => $_POST['state'],
      ':district'         => $_POST['district'],
      ':product_type'     => $_POST['product_type'],
      ':product_name'     => $_POST['product_name'],
      ':squarefeet'       => $_POST['squarefeet'],
      ':rate'             => $_POST['rate'],
      ':point'            => $_POST['point'],
      ':quantity'         => $_POST['quantity'],
      ':amount'           => $_POST['amount'],
      ':discount_percent' => $discount_percent,
      ':discount_rs'      => $discount_rs,
      ':gross_amount'     => $_POST['gross_amount'],
      ':plot_type'        => $_POST['plot_type'],
      ':corner_charge'    => $_POST['corner_charge'],
      ':net_amount'       => $net_amount,
      ':payment_mode'     => $payment_mode,
      ':due_amount'       => $due_amount,
      ':cash_amount'      => $payment_mode == 'cash' ? $cash_amount : null,
      ':cheque_amount'    => $payment_mode == 'cheque' ? $cheque_amount : null,
      ':cheque_number'    => $payment_mode == 'cheque' ? $_POST['cheque_number'] : null,
      ':bank_name'        => $payment_mode == 'cheque' ? $_POST['bank_name'] : null,
      ':cheque_date'      => $payment_mode == 'cheque' ? $_POST['cheque_date'] : null,
      ':transfer_amount'  => $payment_mode == 'bank_transfer' ? $transfer_amount : null,
      ':utr_number'       => $payment_mode == 'bank_transfer' ? $_POST['utr_number'] : null,
      ':remarks'          => $remarks
    ]);

    // Insert into tbl_customeramount
    $customer_sql = "INSERT INTO tbl_customeramount (
            invoice_id, member_id, customer_id, customer_name, mobile_number, customer_address, producttype, productname, area, rate, 
            net_amount, payamount, due_amount, corner_charge, gross_amount, created_date, remarks
        ) VALUES (
            :invoice_id, :member_id, :customer_id, :customer_name, :mobile_number, :customer_address, :producttype, :productname, :area, :rate,
            :net_amount, :payamount, :due_amount, :corner_charge, :gross_amount, :created_date, :remarks
        )";

    $customer_stmt = $pdo->prepare($customer_sql);
    $customer_stmt->execute([
      ':invoice_id'     => $invoice_id,
      ':member_id'      => $_POST['member_id'],
      ':customer_id'    => $_POST['customer_id'],
      ':customer_name'  => $_POST['customer_name'],
      ':mobile_number'  => $_POST['refer_mobile'],
      ':customer_address' => $_POST['address'],
      ':producttype'    => $_POST['product_type'],
      ':productname'    => $productname,
      ':area'           => $_POST['squarefeet'],
      ':rate'           => $_POST['rate'],
      ':net_amount'     => $net_amount,
      ':payamount'      => $payamount,
      ':due_amount'     => $due_amount,
      ':corner_charge'  => $_POST['corner_charge'],
      ':gross_amount'   => $_POST['gross_amount'],
      ':created_date'   => $_POST['date'],
      ':remarks'        => $remarks
    ]);

    // Insert into receiveallpayment
    $payment_sql = "INSERT INTO receiveallpayment (
            invoice_id, member_id, customer_id, customer_name, productname, rate, area, net_amount,
            payment_mode, payamount, discount_percent, discount_rs, plot_type, corner_charge, cheque_number, bank_name, cheque_date,
            utr_number, due_amount, created_date, remarks
        ) VALUES (
            :invoice_id, :member_id, :customer_id, :customer_name, :productname, :rate, :area, :net_amount,
            :payment_mode, :payamount, :discount_percent, :discount_rs, :plot_type, :corner_charge, :cheque_number, :bank_name, :cheque_date,
            :utr_number, :due_amount, :created_date, :remarks
        )";

    $payment_stmt = $pdo->prepare($payment_sql);
    $payment_stmt->execute([
      ':invoice_id'     => $invoice_id,
      ':member_id'      => $_POST['member_id'],
      ':customer_id'    => $_POST['customer_id'],
      ':customer_name'  => $_POST['customer_name'],
      ':productname'    => $productname,
      ':rate'           => $_POST['rate'],
      ':area'           => $_POST['squarefeet'],
      ':net_amount'     => $net_amount,
      ':payment_mode'   => $payment_mode,
      ':payamount'      => $payamount,
      ':discount_percent' => $discount_percent,
      ':discount_rs'    => $discount_rs,
      ':plot_type'      => $_POST['plot_type'],
      ':corner_charge'  => $_POST['corner_charge'],
      ':cheque_number'  => $payment_mode == 'cheque' ? $_POST['cheque_number'] : null,
      ':bank_name'      => $payment_mode == 'cheque' ? $_POST['bank_name'] : null,
      ':cheque_date'    => $payment_mode == 'cheque' ? $_POST['cheque_date'] : null,
      ':utr_number'     => $payment_mode == 'bank_transfer' ? $_POST['utr_number'] : null,
      ':due_amount'     => $due_amount,
      ':created_date'   => $_POST['date'],
      ':remarks'        => $remarks
    ]);

    // Update the products table status to 'booked'
    $update_sql = "UPDATE products SET Status = 'booked' WHERE ProductName = :product_name AND product_type_id IN (1, 2)";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([
      ':product_name' => $productname
    ]);

    // Redirect to success message
    echo "<script>
            alert('Record inserted successfully and plot status updated to booked!');
            window.location.href='Saleinvoice.php?invoice_id=" . urlencode($invoice_id) . "&member_id=" . urlencode($memberid) . "';
        </script>";
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
        <?php include 'adminheadersidepanel.php'; ?>

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
                        <input type="date" class="form-control" name="date" id="date">
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
                        <input type="text" class="form-control" name="rate" id="rate" placeholder="Enter Rate">
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
                        <input type="text" class="form-control" name="discount_percent" id="discount_percent" placeholder="Enter Discount Percentage">
                      </div>
                      <div class="form-group col-md-6">
                        <label>Discount (Rs):</label>
                        <input type="text" class="form-control" name="discount_rs" id="discount_rs" placeholder="Enter Discount Amount">
                      </div>
                      <div class="form-group col-md-6">
                        <label>Gross Amount:</label>
                        <input type="text" class="form-control" name="gross_amount" id="gross_amount" readonly>
                      </div>
                      <div class="form-group col-md-6">
                        <label>Plot Type:</label>
                        <select class="form-control" name="plot_type" id="plot_type">
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
                    </div>

                    <!-- Payment Mode -->
                    <h3>Payment Mode</h3>
                    <div class="form-group">
                      <label>Payment Mode:</label>
                      <select class="form-control" name="payment_mode" id="payment_mode" onchange="showPaymentDetails()">
                        <option value="">Select Payment Mode</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="bank_transfer">Bank Transfer</option>
                      </select>
                    </div>

                    <!-- Payment Details Sections -->
                    <div id="cash_details" class="payment-details" style="display:none;">
                      <div class="form-group">
                        <label>Amount Paid:</label>
                        <input type="text" class="form-control" name="cash_amount" id="cash_amount" placeholder="Enter Cash Amount">
                      </div>
                    </div>

                    <div id="cheque_details" class="payment-details" style="display:none;">
                      <div class="form-row">
                        <div class="form-group col-md-6">
                          <label>Cheque Amount:</label>
                          <input type="text" class="form-control" name="cheque_amount" id="cheque_amount" placeholder="Enter Cheque Amount">
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
                          <input type="text" class="form-control" name="transfer_amount" id="transfer_amount" placeholder="Enter Transfer Amount">
                        </div>
                        <div class="form-group col-md-6">
                          <label>UTR Number:</label>
                          <input type="text" class="form-control" name="utr_number" id="utr_number" placeholder="Enter UTR Number">
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
                  </form>
                </div>


              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <?php include 'adminfooter.php'; ?>

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


  <script>
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
      }
    }
  </script>


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


</body>

</html>