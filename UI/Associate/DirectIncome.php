<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include_once 'connectdb.php';

if (isset($_COOKIE['sponsor_login'])) {
    $login_data = json_decode($_COOKIE['sponsor_login'], true);
    $sponsorid = $login_data['sponsorid'];
    $sponsorpass = $login_data['sponsorpass'];

    $select = $pdo->prepare("select * from tbl_hire where sponsor_id='$sponsorid' AND  sponsor_pass='$sponsorpass'");
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row['sponsor_id'] === $sponsorid and $row['sponsor_pass'] === $sponsorpass) {
        $_SESSION['sponsor_id'] = $row['sponsor_id'];
        $_SESSION['sponsor_pass'] = $row['sponsor_pass'];
        $_SESSION['sponsor_name'] = $row['s_name'];
    }
}

// Redirect the user to the login page if they're not logged in
if (!isset($_SESSION['sponsor_id'])) {
    header('location:../../login.php');
    exit();
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Amitabh Builders & Developers
    </title>
    <link rel="shortcut icon" type="image/x-icon" href="../../icon/harihomes1-fevicon.png">
    <link rel="stylesheet" href="../resources/vendors/feather/feather.css">
    <link rel="stylesheet" href="../resources/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../resources/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../resources/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="../resources/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
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
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="../resources/vendors/js/vendor.bundle.base.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>


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

<body data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">

    <div class="wrapper">
        <div class="container-scroller">


            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <?php include "associate-headersidepanel.php"; ?>




                <div class="main-panel">




                    <div style="background: #fff; padding: 21px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                        <h2 style="padding-top: 30px">My Payout Report</h2>

                        <hr>




                        <div class="clr"></div>

                        <div id="">

                            <div id="">
                                <?php
                                // session_start(); // ensure session started earlier
                                $logged_in_member_id = $_SESSION['mem_sid'] ?? $_SESSION['sponsor_id'] ?? 'HHD000001';

                                // Fetch commission_history rows for this member (closed or any status you want)
                                $sql = "
  SELECT id, invoice_id, member_id, member_name, direct_percent, direct_commission, total_commission,
         from_date, to_date, status, created_at, payment_status, payment_date, payment_mode, payment_details
  FROM commission_history
  WHERE member_id = :mid
  ORDER BY to_date DESC, created_at DESC
";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['mid' => $logged_in_member_id]);
                                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($rows)) {
                                    echo "<div class='alert alert-warning'>No commission history found for member: <strong>" . htmlspecialchars($logged_in_member_id) . "</strong></div>";
                                } else {
                                    // Table header
                                    echo "<div class='table-responsive'><table class='table table-bordered' id='commissionHistoryTable'>
            <thead><tr>
              <th>Action</th>
              <th>Member Name</th>
              <th>From</th>
              <th>To</th>
              <th>Direct %</th>
              <th>Direct Commission</th>
              <th>Payment Status</th>            
            </tr></thead><tbody>";

                                    foreach ($rows as $r) {
                                        $invoice = htmlspecialchars($r['invoice_id'] ?? '-');
                                        $mname = htmlspecialchars($r['member_name'] ?? '');
                                        $from = $r['from_date'] ? date('Y-m-d', strtotime($r['from_date'])) : '';
                                        $to = $r['to_date'] ? date('Y-m-d', strtotime($r['to_date'])) : '';
                                        $direct_pct = isset($r['direct_percent']) ? floatval($r['direct_percent']) : 0;
                                        // some rows may store commission in different columns; best-effort:
                                        $direct_comm = isset($r['direct_commission']) && $r['direct_commission'] !== null
                                            ? floatval($r['direct_commission'])
                                            : (isset($r['direct_amount']) ? floatval($r['direct_amount']) : 0);
                                        $total_comm = isset($r['total_commission']) ? floatval($r['total_commission']) : 0;
                                        $pay_status = htmlspecialchars($r['payment_status'] ?? $r['status'] ?? '-');

                                        // store payment_details raw (escaped for safety in attribute)
                                        $pd_raw = $r['payment_details'] ?? '';

                                        // render row with a View button and data attributes containing payment_details (JSON)
                                        echo "<tr data-history-id='" . intval($r['id']) . "'>
               <td>
                  <button class='btn btn-sm btn-primary btn-view-history' 
                          data-history-id='" . intval($r['id']) . "' 
                          data-invoice='" . htmlspecialchars($invoice, ENT_QUOTES) . "'
                          data-member='" . htmlspecialchars($mname, ENT_QUOTES) . "'
                          data-from='" . htmlspecialchars($from, ENT_QUOTES) . "'
                          data-to='" . htmlspecialchars($to, ENT_QUOTES) . "'
                          data-direct-commission='" . number_format($direct_comm, 2, '.', '') . "'
                          data-payment-details='" . htmlspecialchars($pd_raw, ENT_QUOTES) . "'>View</button>
                </td>
                <td>{$mname}</td>
                <td>{$from}</td>
                <td>{$to}</td>
                <td>" . number_format($direct_pct, 2) . "%</td>
                <td>₹" . number_format($direct_comm, 2) . "</td>
                <td>{$pay_status}</td>
                
              </tr>";
                                    }

                                    echo "</tbody></table></div>";
                                }

                                // Preload rows JSON for JS usage (safe encoding)
                                $rows_json = json_encode($rows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
                                ?>

                                <!-- Modal to show payment_details for a commission_history row -->
                                <div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="historyModalTitle">Payment details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body" id="historyModalBody"></div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- Bootstrap 5 (JS + Popper) -->
                                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // rows preloaded (not strictly necessary because we embed details in data-attrs),
                                        // but keep for possible server-side lookups in future.
                                        const histories = <?php echo $rows_json ?: '[]'; ?>;

                                        function safeParseJSON(str) {
                                            if (!str) return null;
                                            try {
                                                return JSON.parse(str);
                                            } catch (e) {
                                                // sometimes DB stores a JSON-like string with single quotes or serialized format — return raw string
                                                return null;
                                            }
                                        }

                                        // render payment_details (best-effort for different shapes)
                                        function renderPaymentDetails(detailsRaw) {
                                            let parsed = safeParseJSON(detailsRaw);
                                            if (parsed === null) {
                                                // try to show raw string (if empty or unparsable)
                                                if (!detailsRaw || detailsRaw === '[]') {
                                                    return '<div class="alert alert-info">No payment_details available.</div>';
                                                }
                                                return '<pre style="white-space:pre-wrap;">' + escapeHtml(detailsRaw) + '</pre>';
                                            }

                                            // parsed may be array or object keyed groups
                                            let html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                                            html += '<thead><tr><th>Product / Key</th><th>Date</th><th>Amount</th><th>Commission</th></tr></thead><tbody>';

                                            if (Array.isArray(parsed)) {
                                                parsed.forEach(item => {
                                                    if (typeof item === 'object' && item !== null) {
                                                        const name = item.productname || item.plot_name || item.name || item.product || '';
                                                        const date = item.date || item.created_date || item.payment_date || '';
                                                        const amount = item.amount ?? item.payamount ?? item.net_amount ?? '';
                                                        const comm = item.commission ?? item.direct_commission ?? '';
                                                        html += `<tr><td>${escapeHtml(name)}</td><td>${escapeHtml(date)}</td><td>${amount ? '₹' + parseFloat(amount).toFixed(2) : ''}</td><td>${comm ? '₹' + parseFloat(comm).toFixed(2) : ''}</td></tr>`;
                                                    } else {
                                                        html += `<tr><td colspan="4">${escapeHtml(String(item))}</td></tr>`;
                                                    }
                                                });
                                            } else if (typeof parsed === 'object' && parsed !== null) {
                                                // might be keyed like {"D-90":[{...}, {...}], "C-11":[...]}
                                                Object.keys(parsed).forEach(key => {
                                                    const group = parsed[key];
                                                    if (Array.isArray(group)) {
                                                        group.forEach(entry => {
                                                            const name = entry.productname || entry.plot_name || entry.name || key;
                                                            const date = entry.date || entry.created_date || entry.payment_date || '';
                                                            const amount = entry.amount ?? entry.payamount ?? entry.net_amount ?? '';
                                                            const comm = entry.commission ?? entry.direct_commission ?? '';
                                                            html += `<tr><td>${escapeHtml(name)}</td><td>${escapeHtml(date)}</td><td>${amount ? '₹' + parseFloat(amount).toFixed(2) : ''}</td><td>${comm ? '₹' + parseFloat(comm).toFixed(2) : ''}</td></tr>`;
                                                        });
                                                    } else {
                                                        html += `<tr><td colspan="4">${escapeHtml(key)}: ${escapeHtml(JSON.stringify(group))}</td></tr>`;
                                                    }
                                                });
                                            } else {
                                                html += `<tr><td colspan="4">${escapeHtml(String(parsed))}</td></tr>`;
                                            }

                                            html += '</tbody></table></div>';
                                            return html;
                                        }

                                        function escapeHtml(str) {
                                            if (str === null || str === undefined) return '';
                                            return String(str)
                                                .replace(/&/g, '&amp;')
                                                .replace(/</g, '&lt;')
                                                .replace(/>/g, '&gt;')
                                                .replace(/"/g, '&quot;')
                                                .replace(/'/g, '&#39;');
                                        }

                                        // click handler for View buttons
                                        document.querySelectorAll('.btn-view-history').forEach(btn => {
                                            btn.addEventListener('click', function(e) {
                                                const invoice = this.getAttribute('data-invoice') || '';
                                                const member = this.getAttribute('data-member') || '';
                                                const from = this.getAttribute('data-from') || '';
                                                const to = this.getAttribute('data-to') || '';
                                                const direct_comm = this.getAttribute('data-direct-commission') || '0.00';
                                                const pd_raw = this.getAttribute('data-payment-details') || '';

                                                // build modal content
                                                let title = invoice ? invoice + ' — ' + member : 'Payment details';
                                                let header = `<p><strong>Period:</strong> ${escapeHtml(from)} → ${escapeHtml(to)} &nbsp; <strong>Direct Commission:</strong> ₹${parseFloat(direct_comm).toFixed(2)}</p>`;
                                                let body = header + renderPaymentDetails(pd_raw);

                                                document.getElementById('historyModalTitle').textContent = title;
                                                document.getElementById('historyModalBody').innerHTML = body;

                                                // show bootstrap modal (BS5)
                                                const modalEl = document.getElementById('historyModal');
                                                if (typeof bootstrap !== 'undefined') {
                                                    const modal = new bootstrap.Modal(modalEl);
                                                    modal.show();
                                                } else if (window.jQuery && $('#historyModal').modal) {
                                                    $('#historyModal').modal('show');
                                                }
                                            });
                                        });
                                    });
                                </script>


                            </div>

                            <hr>
                        </div>

                    </div>
                    <?php include "associate-footer.php"; ?>
                </div>
            </div>
        </div>
    </div>


    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- <script>
            function autoSave(element) {
                // Get the row containing the changed input
                var row = $(element).closest("tr");
                var invoice = row.find("span[id*='lblPOrderNo']").text();
                var Orderdate = row.find("input[id*='lblPOrderDate']").val();
                var Coustomername = row.find("input[id*='lblcustomernamed']").val();
                var NetAmount = row.find("input[id*='txtAmount']").val();
                var Payamount = row.find("input[id*='lblpayAmount']").val();
                var Duesamount = row.find("input[id*='lblDuesAmount']").val();
                var present = row.find("input[id*='lblDue']").val();
                var commission = row.find("input[id*='txtcomamount']").val();
                var tdsamount = row.find("input[id*='txttds']").val();
                var adminamount = row.find("input[id*='lbladmincharge']").val();
                var totalcommssion = row.find("input[id*='totalamountcom']").val();

                // Send the data via AJAX
                $.ajax({
                    type: "POST",
                    url: "DirectIncome.php.cs/AutoSaveRow",
                    data: JSON.stringify({
                        invoice: invoice,
                        Orderdate: Orderdate,
                        Coustomername: Coustomername,
                        NetAmount: NetAmount,
                        Payamount: Payamount,
                        Duesamount: Duesamount,
                        present: present,
                        commission: commission,
                        tdsamount: tdsamount,
                        adminamount: adminamount,
                        totalcommssion: totalcommssion
                    }),
                    contentType: "application/json; charset=utf-8",
                    success: function(response) {
                        console.log("Row saved successfully!");
                    },
                    error: function() {
                        console.error("Error saving row data!");
                    }
                });
            }
        </script> -->


    <a href="#" target="_blank">
        <!-- partial -->
    </a>
    <!-- search box for options-->
    <!-- jQuery (required for DataTables) -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

    <!-- <script src="../resources/vendors/js/vendor.bundle.base.js"></script> -->
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../resources/vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="../resources/vendors/select2/select2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- Plugin js for this page -->
    <script src="../resources/vendors/chart.js/Chart.min.js"></script>
    <!-- <script src="../resources/vendors/datatables.net/jquery.dataTables.js"></script> -->
    <!-- <script src="../resources/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> -->
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


    <script>
        $(document).ready(function() {
            // Destroy any existing DataTable instance
            if ($.fn.DataTable.isDataTable('#salesTable')) {
                $('#salesTable').DataTable().destroy();
            }

            // Initialize DataTable with all features enabled
            $('#salesTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "columns": [{
                        "data": "Date"
                    },
                    {
                        "data": "Invoice ID"
                    },
                    {
                        "data": "Customer Name"
                    },
                    {
                        "data": "Plot Name"
                    },
                    {
                        "data": "Net Amount"
                    },
                    {
                        "data": "Payment Amount"
                    },
                    {
                        "data": "Direct Commission %"
                    },
                    {
                        "data": "Direct Commission"
                    },
                    {
                        "data": "TDS (5%)"
                    },
                    {
                        "data": "Admin Charge (5%)"
                    },
                    {
                        "data": "Net Direct Income"
                    }
                ]
            });

            $('.dropdown-toggle').dropdown();
        });
    </script>





    <style>
        i {
            color: yellow;
        }
    </style>



</body>

</html>