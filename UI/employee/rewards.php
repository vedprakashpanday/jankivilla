<?php
 error_reporting(E_ALL);
ini_set('display_errors', 1);;
session_start();
include_once "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header("Location: ../../employee.php"); // Redirect to dashboard
    exit;
}


?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head id="Head1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Amitabh Builders & Developers
    </title>
    <link rel="shortcut icon" type="image/x-icon" href="../../icon/harihomes1-fevicon.png">
    <link rel="stylesheet" href="../resources/vendors/feather/feather.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../resources/js/select.dataTables.min.css">
    <link rel="stylesheet" href="../resources/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../resources/vendors/fullcalendar/fullcalendar.min.css">
    <link rel="stylesheet" href="../resources/css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link href="assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link href="../assets/css/vendor.bundle.base.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/themify-icons.css">


    <script>
        function display_ct7() {
            var x = new Date();
            var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
            var hours = x.getHours() % 12;
            hours = hours ? hours : 12;
            hours = hours.toString().length == 1 ? '0' + hours.toString() : hours;

            var minutes = x.getMinutes().toString();
            minutes = minutes.length == 1 ? '0' + minutes : minutes;

            var seconds = x.getSeconds().toString();
            seconds = seconds.length == 1 ? '0' + seconds : seconds;

            var month = (x.getMonth() + 1).toString();
            month = month.length == 1 ? '0' + month : month;

            var dt = x.getDate().toString();
            dt = dt.length == 1 ? '0' + dt : dt;

            var x1 = dt + "-" + month + "-" + x.getFullYear();
            x1 = x1 + " " + hours + ":" + minutes + ":" + seconds + " " + ampm;
            document.getElementById('ct7').innerHTML = x1;
        }

        function startTime() {
            display_ct7();
            setInterval(display_ct7, 1000);
        }

        window.onload = startTime;
    </script>
</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="aspNetHidden">


        <div class="wrapper">
            <div class="container-scroller">
                <!-- partial -->
                <div class="container-fluid page-body-wrapper">
                    <?php include 'employeesidepanelheader.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="col-md-12">
                                <div class="card">
                                   
                                    <div class="row pt-5 mx-5">
                                        <div class="col-md-12 overflow-auto" style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Rewards Report</h3>
                                         <table class="table table-bordered table-striped table-sm ">
    <thead class="table-dark text-center align-middle">
        <tr>
            <th>#</th>
            <th>Receiver Name</th>
            <th>Receiver Code</th>
            <th>Passbook No</th>
            <th>DV No</th>
            <th>Expense Type</th>
            <th>Payment Mode</th>

            <th>Cash Amount</th>

            <th>Cheque Amount</th>
            <th>Cheque No</th>
            <th>Cheque Bank</th>
            <th>Cheque Date</th>

            <th>Transfer Amount</th>
            <th>NEFT Ref</th>
            <th>RTGS Ref</th>
            <th>UTR No</th>
            <th>Bank Name</th>
            <th>Bank A/C</th>
            <th>IFSC</th>

            <th>Remarks</th>
            <th>Created At</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        $count=0;
        $stmt = $pdo->prepare("SELECT * FROM tbl_rewards WHERE r_code = :r");
$stmt->execute(['r' => $_SESSION['sponsor_id']]);


        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            echo "<tr>";
            echo "<td>{$sn}</td>";
            echo "<td>{$r['r_name']}</td>";
            echo "<td>{$r['r_code']}</td>";
            echo "<td>{$r['pb_no']}</td>";
            echo "<td>{$r['dv_no']}</td>";
            echo "<td>{$r['expense_type']}</td>";
            echo "<td class='text-capitalize'>{$r['payment_mode']}</td>";

            echo "<td>" . ($r['cash_amount'] ? "₹".number_format($r['cash_amount'],2) : '-') . "</td>";

            echo "<td>" . ($r['cheque_amount'] ? "₹".number_format($r['cheque_amount'],2) : '-') . "</td>";
            echo "<td>{$r['cheque_number']}</td>";
            echo "<td>{$r['chequebank_name']}</td>";
            echo "<td>{$r['cheque_date']}</td>";

            echo "<td>" . ($r['transfer_amount'] ? "₹".number_format($r['transfer_amount'],2) : '-') . "</td>";
            echo "<td>{$r['neft_payment']}</td>";
            echo "<td>{$r['rtgs_payment']}</td>";
            echo "<td>{$r['utr_number']}</td>";
            echo "<td>{$r['bank_name']}</td>";
            echo "<td>{$r['ba_number']}</td>";
            echo "<td>{$r['ifsc']}</td>";

            echo "<td>{$r['description']}</td>";
            echo "<td>" . date('d-m-Y H:i', strtotime($r['created_at'])) . "</td>";

       

            echo "</tr>";
            $sn++;
        }
        if ($count== 0) {
    echo "<tr>
        <td colspan='21' class='text-center'>No Record Found</td>
    </tr>";
}
        ?>
        
    </tbody>
</table>


                                        </div>
                                    </div>
                                </div>

                            </div>



                        </div>

                        <?php include 'employee-footer.php'; ?>
                    </div>



                </div>
                <a href="#" target="_blank">
                    <!-- partial -->
                </a>
                <!-- search box for options-->

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
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
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

                <script>
                    function editReward(data) {
                        $('#reward_id').val(data.id);
                        $('#sponsor_id').val(data.sponsor_id).trigger('change'); // Set hidden input
                        $('#associate_name').val(data.sponsor_id).trigger('change'); // Set select2
                        $('input[name="amount"]').val(data.amount);
                        $('#description').val(data.description);
                    }
                </script>

            </div>
        </div>
        <div style="margin-left:250px">
            <span id="lblMsg"></span>
        </div>
        <style>
            #lblMsg {
                visibility: hidden;
            }
        </style>


    <script>
        // Show/hide bank name field based on payment mode
        document.getElementById('paymentMode').addEventListener('change', function() {
            const bankField = document.getElementById('bankNameField');
            const bankAccountField = document.getElementById('bankAccountField');
            const bankIfscField = document.getElementById('bankIfscField');
            if (this.value === 'bank') {
                bankField.style.display = 'block';
                bankAccountField.style.display = 'block';
                bankIfscField.style.display = 'block';
                bankField.querySelector('input').required = true;
                bankAccountField.querySelector('input').required = true;
                bankIfscField.querySelector('input').required = true;
            } else {
                bankField.style.display = 'none';
                bankAccountField.style.display = 'none';
                bankIfscField.style.display = 'none';
                bankField.querySelector('input').required = false;
                bankAccountField.querySelector('input').required = false;
                bankIfscField.querySelector('input').required = false;
            }
        });
    </script>

<script>
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


        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Select Associate',
                    allowClear: true
                });

                $('#associate_name').on('change', function() {
                    let selectedSponsorId = $(this).val(); // sponsor_id is the value of <option>
                    $('#sponsor_id').val(selectedSponsorId); // assign it to hidden input
                });
            });
        </script>


</body>

</html>