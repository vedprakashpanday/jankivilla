<?php
session_start();
include_once "connectdb.php";

// Check if user is already logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header("Location: ../../adminlogin.php"); // Redirect to dashboard
    exit;
}



if (isset($_POST['btnsubmit'])) {
    $member_id = $_POST['member_id'];
    $sponsor_id = $_POST['sponsor_id'];
    $commission_percent = $_POST['commission_percent'];
    $total_amount = $_POST['total_amount'];
    $commission_amount = $_POST['commission_amount'];
    $sponsor_commission = $_POST['sponsor_commission'];
    $description = $_POST['description'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $is_cycle_failed = $_POST['is_cycle_failed'];

    // Get product names (new field)
    $product_names = isset($_POST['product_names']) ? trim($_POST['product_names']) : '';

    // Ensure we have product names, default to 'All Products' if empty
    if (empty($product_names)) {
        $product_names = 'All Products';
    }

    // Get member name
    $member_name = '';
    $stmt_member = $pdo->prepare("SELECT m_name FROM tbl_regist WHERE mem_sid = ?");
    $stmt_member->execute([$member_id]);
    $member_data = $stmt_member->fetch(PDO::FETCH_ASSOC);
    $member_name = $member_data['m_name'] ?? '';

    // Get sponsor name
    $sponsor_name = '';
    if (!empty($sponsor_id)) {
        $stmt_sponsor = $pdo->prepare("SELECT m_name FROM tbl_regist WHERE mem_sid = ?");
        $stmt_sponsor->execute([$sponsor_id]);
        $sponsor_data = $stmt_sponsor->fetch(PDO::FETCH_ASSOC);
        $sponsor_name = $sponsor_data['m_name'] ?? '';
    }

    // If cycle failed, set sponsor commission to 0
    if ($is_cycle_failed == '1') {
        $sponsor_commission = 0;
    }

    try {
        // Insert with product name support
        $stmt = $pdo->prepare("
            INSERT INTO spot_commission (
                member_id, 
                member_name, 
                sponsor_id, 
                sponsor_name, 
                commission_percent, 
                total_amount, 
                commission_amount, 
                sponsor_commission_amount, 
                from_date, 
                to_date, 
                is_cycle_failed, 
                product_names,
                description, 
                created_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $member_id,
            $member_name,
            $sponsor_id,
            $sponsor_name,
            $commission_percent,
            $total_amount,
            $commission_amount,
            $sponsor_commission,
            $from_date,
            $to_date,
            $is_cycle_failed,
            $product_names,
            $description
        ]);

        // Show success message with product info
        $success_msg = "Commission saved successfully!";
        if (!empty($product_names) && $product_names !== 'All Products') {
            $success_msg .= "\\nProduct(s): " . $product_names;
        }

        echo "<script>alert('" . addslashes($success_msg) . "'); window.location.href=window.location.href;</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error saving commission: " . addslashes($e->getMessage()) . "');</script>";
    }
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
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="container" style="padding-top: 50px; padding-bottom: 50px; margin-left:1rem!important;">
                                        <div class="row" style="display: block;">
                                            <form method="post" id="spotCommissionForm">
                                                <div class="col-md-12">
                                                    <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                        <h2>Spot Commission</h2>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <b>Member Name:</b>
                                                                <i>
                                                                    <select id="member_name" name="member_name" class="form-control select2" style="font-weight:bold;">
                                                                        <option value="">Select Member</option>
                                                                        <?php
                                                                        // Fetch members from tbl_regist table
                                                                        $stmt = $pdo->query("SELECT mem_sid, m_name FROM tbl_regist ORDER BY m_name");
                                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                            echo '<option value="' . htmlspecialchars($row['mem_sid']) . '">' . htmlspecialchars($row['m_name']) . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>From Date:</b>
                                                                <i><input name="from_date" type="date" id="from_date" class="form-control" style="font-weight:bold;" required></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>To Date:</b>
                                                                <i><input name="to_date" type="date" id="to_date" class="form-control" style="font-weight:bold;" required></i>
                                                            </div>
                                                        </div>

                                                        <!-- Product Names Display Row -->
                                                        <div class="row pt-2">
                                                            <div class="col-md-12">
                                                                <b>Product Names:</b>
                                                                <i><input name="product_names_display" type="text" id="product_names_display" class="form-control" style="font-weight:bold;" readonly placeholder="Product names will appear here as comma separated values"></i>
                                                            </div>
                                                        </div>

                                                        <!-- Product Details Table -->
                                                        <div class="row pt-2" id="product_details_section" style="display: none;">
                                                            <div class="col-md-12">
                                                                <b>Product-wise Payment Details:</b>
                                                                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; margin-top: 10px;">
                                                                    <table class="table table-striped table-sm" id="product_details_table">
                                                                        <thead style="background-color: #f8f9fa;">
                                                                            <tr>
                                                                                <th>Product Name</th>
                                                                                <th>Payment Count</th>
                                                                                <th>Total Amount</th>
                                                                                <th>Select</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <!-- Product details will be populated here -->
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row pt-2">
                                                            <div class="col-md-4">
                                                                <b>Commission Percentage:</b>
                                                                <div style="margin-top: 8px;">
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <label style="margin-right: 15px;">
                                                                            <input type="radio" name="commission_percent" value="<?php echo $i; ?>" style="margin-right: 5px;">
                                                                            <?php echo $i; ?>%
                                                                        </label>
                                                                    <?php endfor; ?>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Total Amount:</b>
                                                                <i><input name="total_amount" type="number" id="total_amount" class="form-control" style="font-weight:bold;" readonly></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Commission Status:</b>
                                                                <i><input name="cycle_status" type="text" id="cycle_status" class="form-control" style="font-weight:bold;" readonly></i>
                                                            </div>
                                                        </div>

                                                        <div class="row pt-2">
                                                            <div class="col-md-4">
                                                                <b>Commission Amount:</b>
                                                                <i><input name="commission_amount" type="number" id="commission_amount" class="form-control" style="font-weight:bold;" readonly></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Sponsor Name:</b>
                                                                <i><input name="sponsor_name" type="text" id="sponsor_name" class="form-control" style="font-weight:bold;" readonly></i>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <b>Sponsor Commission:</b>
                                                                <i><input name="sponsor_commission" type="number" id="sponsor_commission" class="form-control" style="font-weight:bold;" readonly></i>
                                                            </div>
                                                        </div>

                                                        <div class="row pt-2">
                                                            <div class="col-md-12">
                                                                <b>Description:</b>
                                                                <i>
                                                                    <textarea name="description" id="description" class="form-control" style="font-weight:bold;" rows="4"></textarea>
                                                                </i>
                                                            </div>
                                                        </div>

                                                        <!-- Hidden fields -->
                                                        <input type="hidden" name="member_id" id="member_id">
                                                        <input type="hidden" name="sponsor_id" id="sponsor_id">
                                                        <input type="hidden" name="is_cycle_failed" id="is_cycle_failed" value="0">
                                                        <input type="hidden" name="product_names" id="product_names">

                                                        <div class="row pt-4">
                                                            <div class="col-md-12">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-7" style="text-align: center;">
                                                                        <input type="submit" name="btnsubmit" value="Save" class="btn-success">
                                                                        <input type="reset" class="btn-secondary" value="Clear Form">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>


                                        </div>
                                    </div>
                                    <div class="row pt-5">
                                        <div class="col-md-11" style="overflow:auto; background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                            <h3>Commission Details</h3>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Member ID</th>
                                                        <th>Member Name</th>
                                                        <th>Sponsor ID</th>
                                                        <th>Sponsor Name</th>
                                                        <th>Percent %</th>
                                                        <th>Total Amount</th>
                                                        <th>Plot Name</th>
                                                        <th>Commission</th>
                                                        <th>Sponsor Commission</th>
                                                        <th>From-To Date</th>
                                                        <th>Description</th>
                                                        <th>Created At</th>
                                                        <!-- <th>Action</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $stmt = $pdo->query("SELECT * FROM spot_commission ORDER BY created_date DESC");
                                                    $sn = 1;
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo "<tr>";
                                                        echo "<td>{$sn}</td>";
                                                        echo "<td>{$row['member_id']}</td>";
                                                        echo "<td>{$row['member_name']}</td>";
                                                        echo "<td>{$row['sponsor_id']}</td>";
                                                        echo "<td>{$row['sponsor_name']}</td>";
                                                        echo "<td>{$row['commission_percent']}%</td>";
                                                        echo "<td>₹" . number_format($row['total_amount'], 2) . "</td>";
                                                        echo "<td>{$row['product_names']}</td>";
                                                        echo "<td>₹" . number_format($row['commission_amount'], 2) . "</td>";
                                                        echo "<td>₹" . number_format($row['sponsor_commission_amount'], 2) . "</td>";
                                                        echo "<td>{$row['from_date']} to {$row['to_date']}</td>";
                                                        echo "<td>{$row['description']}</td>";
                                                        echo "<td>{$row['created_date']}</td>";
                                                        //                                             echo "<td>
                                                        //     <form method='post' onsubmit='return confirm(\"Delete this entry?\")' style='display:inline;'>
                                                        //         <input type='hidden' name='delete_id' value='{$row['id']}'>
                                                        //         <button type='submit' class='btn btn-sm btn-danger'>Delete</button>
                                                        //     </form>
                                                        // </td>";
                                                        echo "</tr>";
                                                        $sn++;
                                                    }

                                                    // Handle deletion
                                                    if (isset($_POST['delete_id'])) {
                                                        $delId = $_POST['delete_id'];
                                                        $pdo->prepare("DELETE FROM spot_commission WHERE id = ?")->execute([$delId]);
                                                        echo "<script>location.reload();</script>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>



                        </div>

                        <?php include 'adminfooter.php'; ?>
                    </div>



                </div>
                <a href="#" target="_blank">
                    <!-- partial -->
                </a>
                <!-- search box for options-->

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

                <script>
                    $(document).ready(function() {
                        // Define cycle failed members
                        const cycleFailedMembers = ['HHD91983', 'HHD10134', 'HHD30752', 'HHD32058'];

                        // Handle member selection change
                        $('#member_name').on('change', function() {
                            var memberId = $(this).val();
                            $('#member_id').val(memberId);

                            // Check if member is in cycle failed list
                            if (cycleFailedMembers.includes(memberId)) {
                                $('#is_cycle_failed').val('1');
                                $('#cycle_status').val('Direct Commission Only');
                            } else {
                                $('#is_cycle_failed').val('0');
                                $('#cycle_status').val('Distribute Commission');
                            }

                            if (memberId !== '') {
                                fetchMemberDetails(memberId);
                            } else {
                                clearFields();
                            }
                        });

                        // Handle commission percentage change
                        $('input[name="commission_percent"]').on('change', function() {
                            calculateCommission();
                        });

                        // Handle date change
                        $('#from_date, #to_date').on('change', function() {
                            var memberId = $('#member_id').val();
                            if (memberId !== '') {
                                fetchMemberDetails(memberId);
                            }
                        });

                        // Handle product selection change - REMOVED (no longer needed)

                        // Handle individual product checkbox changes
                        $(document).on('change', '.product-checkbox', function() {
                            calculateSelectedProductsTotal();
                            calculateCommission();
                        });

                        function fetchMemberDetails(memberId) {
                            var fromDate = $('#from_date').val();
                            var toDate = $('#to_date').val();

                            $.ajax({
                                url: 'fetch_member_details.php',
                                method: 'POST',
                                data: {
                                    member_id: memberId,
                                    from_date: fromDate,
                                    to_date: toDate
                                },
                                dataType: 'json',
                                success: function(data) {
                                    // Populate basic member details
                                    $('#sponsor_name').val(data.sponsor_name);
                                    $('#sponsor_id').val(data.sponsor_id);

                                    // Populate product display and details
                                    populateProductDisplay(data.products || []);
                                    populateProductTable(data.products || []);

                                    // Set total amount (sum of all products)
                                    $('#total_amount').val(data.total_amount || 0);

                                    calculateCommission();
                                },
                                error: function() {
                                    alert('Error fetching member details');
                                }
                            });
                        }

                        function populateProductDisplay(products) {
                            var productNames = products.map(function(product) {
                                return product.product_name;
                            });

                            var displayText = productNames.length > 0 ? productNames.join(', ') : 'No products found';
                            $('#product_names_display').val(displayText);
                            $('#product_names').val(productNames.join(', '));

                            // Show product details section if products exist
                            // if (products.length > 0) {
                            //     $('#product_details_section').show();
                            // } else {
                            //     $('#product_details_section').hide();
                            // }
                        }

                        function populateProductTable(products) {
                            var tbody = $('#product_details_table tbody');
                            tbody.empty();

                            if (products.length === 0) {
                                tbody.append('<tr><td colspan="4" class="text-center">No products found for selected date range</td></tr>');
                                return;
                            }

                            products.forEach(function(product) {
                                var row = '<tr>' +
                                    '<td>' + product.product_name + '</td>' +
                                    '<td>' + product.payment_count + '</td>' +
                                    '<td>₹' + parseFloat(product.total_amount).toFixed(2) + '</td>' +
                                    '<td><input type="checkbox" class="product-checkbox" value="' + product.product_name + '" data-amount="' + product.total_amount + '" checked></td>' +
                                    '</tr>';
                                tbody.append(row);
                            });
                        }

                        function calculateSelectedProductsTotal() {
                            var total = 0;
                            var selectedProducts = [];

                            $('.product-checkbox:checked').each(function() {
                                total += parseFloat($(this).data('amount')) || 0;
                                selectedProducts.push($(this).val());
                            });

                            $('#total_amount').val(total.toFixed(2));

                            // Update product names display and hidden field
                            var displayText = selectedProducts.length > 0 ? selectedProducts.join(', ') : 'No products selected';
                            $('#product_names_display').val(displayText);
                            $('#product_names').val(selectedProducts.join(', '));
                        }

                        function calculateCommission() {
                            var totalAmount = parseFloat($('#total_amount').val()) || 0;
                            var commissionPercent = parseFloat($('input[name="commission_percent"]:checked').val()) || 0;
                            var isCycleFailed = $('#is_cycle_failed').val() === '1';

                            if (totalAmount > 0 && commissionPercent > 0) {
                                var commissionAmount = (totalAmount * commissionPercent) / 100;
                                $('#commission_amount').val(commissionAmount.toFixed(2));

                                // Calculate sponsor commission based on cycle status
                                if (isCycleFailed) {
                                    // Cycle failed members - sponsor gets 0
                                    $('#sponsor_commission').val('0.00');
                                    var sponsorName = $('#sponsor_name').val();
                                    if (sponsorName && !sponsorName.includes('(NO COMMISSION')) {
                                        $('#sponsor_name').val(sponsorName + ' (NO COMMISSION - CYCLE FAILED)');
                                    }
                                } else {
                                    // Normal cycle - sponsor gets 1% less
                                    var sponsorCommissionPercent = commissionPercent - 1;
                                    var sponsorCommission = sponsorCommissionPercent > 0 ? (totalAmount * sponsorCommissionPercent) / 100 : 0;
                                    $('#sponsor_commission').val(sponsorCommission.toFixed(2));
                                }
                            } else {
                                $('#commission_amount').val('');
                                $('#sponsor_commission').val('');
                            }
                        }

                        function clearFields() {
                            $('#total_amount').val('');
                            $('#commission_amount').val('');
                            $('#sponsor_name').val('');
                            $('#sponsor_commission').val('');
                            $('#member_id').val('');
                            $('#sponsor_id').val('');
                            $('#cycle_status').val('');
                            $('#is_cycle_failed').val('0');
                            $('#product_names').val('');
                            $('#product_names_display').val('');
                            $('#product_details_table tbody').empty();
                            $('#product_details_section').hide();
                            $('input[name="commission_percent"]').prop('checked', false);
                        }

                        // Handle form reset
                        $('input[type="reset"]').on('click', function() {
                            setTimeout(clearFields, 100);
                        });
                    });
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