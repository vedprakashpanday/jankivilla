<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
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

    <style>
        .remarks-cell textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            min-width: 200px;
            /* Ensure textarea is wide enough */
            min-height: 80px;
            /* Ensure textarea is tall enough */
            box-sizing: border-box;
            resize: vertical;
            /* Allow vertical resizing only */
        }
    </style>

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

    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        } */

        .slab-legend {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .slabs-list h5 {
            margin-top: 0;
            color: #333;
        }

        .slab-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .slab-color {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            margin-right: 10px;
        }

        .plots-container {
            background: #fff;
            padding: 20px;
            border: 2px solid #fff;
            box-shadow: 1px 3px 12px 4px #988f8f;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .plots-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 10px;
        }

        .plot-circle {
            width: 70px;
            height: 50px;
            border-radius: 50%;
            line-height: 50px;
            border: 2px solid #000;
            color: white;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease;
            font-size: 12px;
            font-weight: bold;
        }

        .plot-circle:hover {
            transform: scale(1.1);
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            width: 95%;
            max-width: 1200px;
            max-height: 90vh;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideIn 0.3s ease-in-out;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 0;
            max-height: calc(90vh - 80px);
            overflow-y: auto;
        }

        .table-container {
            overflow-x: auto;
            background: white;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 14px;
            min-width: 1000px;
        }

        .details-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .details-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .details-table tr:hover {
            background-color: #e3f2fd;
            transition: background-color 0.2s ease;
        }

        .amount-cell {
            font-weight: 600;
            color: #28a745;
        }

        .due-amount {
            color: #dc3545;
            font-weight: 600;
        }

        .payment-mode {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 16px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-content {
                width: 98%;
                margin: 10px;
            }

            .modal-header {
                padding: 15px;
            }

            .modal-title {
                font-size: 1.2rem;
            }

            .details-table {
                font-size: 12px;
                min-width: 800px;
            }

            .details-table th,
            .details-table td {
                padding: 8px 6px;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                width: 100%;
                height: 100%;
                max-height: 100vh;
                border-radius: 0;
                transform: none;
                top: 0;
                left: 0;
            }

            .modal-body {
                max-height: calc(100vh - 70px);
            }
        }
    </style>

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <form method="post" id="form1">


        <div class="wrapper">
            <div class="container-scroller">

                <!-- partial -->
                <div class="container-fluid page-body-wrapper">

                    <!-- side panel header -->
                    <?php include 'adminheadersidepanel.php'; ?>

                    <div class="main-panel">
                        <div class="content-wrapper">

                            <!-- Payment Percentage Slabs in HTML -->
                            <div class="slab-legend">
                                <div class="slabs-list">
                                    <h5>Payment Percentage Slabs:</h5>
                                    <div class="slab-item">
                                        <div class="slab-color" style="background-color: grey;"></div>
                                        <span>Grey: 0 - 10%</span>
                                    </div>
                                    <div class="slab-item">
                                        <div class="slab-color" style="background-color: red;"></div>
                                        <span>Red: 0% - 30%</span>
                                    </div>
                                    <div class="slab-item">
                                        <div class="slab-color" style="background-color: orange;"></div>
                                        <span>Orange: 30% - 70%</span>
                                    </div>
                                    <div class="slab-item">
                                        <div class="slab-color" style="background-color: green;"></div>
                                        <span>Green: 70% - 100%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 stretch-card">
                            <div class="card">

                                <div style="padding-top: 50px; padding-bottom: 50px;">
                                    <?php
                                    // Original query for booked plots
                                    $query = "
        SELECT DISTINCT
            p.ProductName,
            p.Squarefeet,
            ca.payamount,
            ca.member_id,
            ca.net_amount
        FROM products p
        LEFT JOIN tbl_customeramount ca
            ON p.ProductName = ca.productname
        WHERE p.Status = 'booked'
    ";
                                    $stmt = $pdo->prepare($query);
                                    $stmt->execute();
                                    $booked_plots = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <div class="plots-container">
                                        <h4>Booked Plots</h4>
                                        <div class="plots-grid">
                                            <?php
                                            if (!empty($booked_plots)) {
                                                foreach ($booked_plots as $plot) {
                                                    $productName = $plot['ProductName'];
                                                    $payAmount = floatval($plot['payamount']) ?: 0;
                                                    $netAmount = floatval($plot['net_amount']) ?: 0;

                                                    // Calculate payment percentage
                                                    $percentage = ($netAmount > 0) ? ($payAmount / $netAmount * 100) : 0;

                                                    // Determine color based on percentage
                                                    $color = 'gray';
                                                    if ($netAmount > 0) {
                                                        if ($percentage >= 0 && $percentage <= 10) {
                                                            $color = 'gray';
                                                        } elseif ($percentage > 10 && $percentage <= 30) {
                                                            $color = 'red';
                                                        } elseif ($percentage > 30 && $percentage <= 70) {
                                                            $color = 'orange';
                                                        } elseif ($percentage > 70 && $percentage <= 100) {
                                                            $color = 'green';
                                                        }
                                                    }
                                            ?>
                                                    <div class="plot-circle"
                                                        style="background: <?php echo $color; ?>;"
                                                        onclick="fetchProductDetails('<?php echo htmlspecialchars($productName); ?>')">
                                                        <span><?php echo htmlspecialchars($productName); ?></span>
                                                    </div>
                                            <?php
                                                }
                                            } else {
                                                echo '<div style="width: 100%; text-align: center;">No booked plots found</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal for Product Details -->
                                <div id="detailsModal" class="modal-overlay" onclick="closeModal(event)">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 id="detailsTitle" class="modal-title">Product Details</h4>
                                            <button class="close-btn" onclick="closeDetails()">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="detailsContent">
                                                <div class="loading">Click on a plot to view details...</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <script>
                                    function fetchProductDetails(productName) {
                                        const detailsModal = document.getElementById('detailsModal');
                                        const detailsContent = document.getElementById('detailsContent');
                                        const detailsTitle = document.getElementById('detailsTitle');

                                        // Show the modal
                                        detailsModal.style.display = 'block';
                                        document.body.style.overflow = 'hidden'; // Prevent background scrolling
                                        detailsTitle.textContent = `Payment Details - ${productName}`;
                                        detailsContent.innerHTML = '<div class="loading">Loading payment records...</div>';

                                        // Make AJAX request to fetch data from receiveallpayment table
                                        fetch('fetch_productplot_details.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: 'productname=' + encodeURIComponent(productName)
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success && data.records.length > 0) {
                                                    displayProductDetails(data.records);
                                                } else {
                                                    detailsContent.innerHTML = '<div class="no-data">No payment records found for this product.</div>';
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                detailsContent.innerHTML = '<div class="no-data">Error loading data. Please try again.</div>';
                                            });
                                    }

                                    function displayProductDetails(records) {
                                        const detailsContent = document.getElementById('detailsContent');

                                        let tableHTML = `
               <div class="table-container">
                    <table class="details-table">
                        <thead>
                            <tr>
                            <th>Sr. No.</th>
                                <th>Invoice ID</th>
                                <th>Customer Name</th>
                                <th>Associate ID</th>
                                <th>Net Amount</th>
                                <th>Pay Amount</th>
                                 <th>Created Date</th>
                                <th>Due Amount</th>
                                <th>Payment Mode</th>
                                <th>Discount %</th>
                                <th>Discount ₹</th>
                                <th>Bank Name</th>
                                <th>Cheque No.</th>
                                <th>UTR Number</th>                               
                                <th>Remarks</th>
                                
                            </tr>
                        </thead>
                        <tbody>
            `;

                                        records.forEach(record => {
                                            const netAmount = parseFloat(record.net_amount || 0);
                                            const payAmount = parseFloat(record.payamount || 0);
                                            const dueAmount = parseFloat(record.due_amount || 0);
                                            const discountRs = parseFloat(record.discount_rs || 0);

                                            tableHTML += `
                    <tr>
                    <td>${records.indexOf(record) + 1}</td>
                        <td><strong>${record.invoice_id || '-'}</strong></td>
                        <td>${record.customer_name || '-'}</td>
                        <td>${record.member_id}</td>
                        <td class="amount-cell">₹${netAmount.toLocaleString('en-IN')}</td>
                        <td class="amount-cell">₹${payAmount.toLocaleString('en-IN')}</td>
                         <td>${formatDate(record.created_date)}</td>    
                        <td class="due-amount">₹${dueAmount.toLocaleString('en-IN')}</td>
                        <td><span class="payment-mode">${record.payment_mode || '-'}</span></td>
                        <td>${record.discount_percent || '-'}%</td>
                        <td class="amount-cell">₹${discountRs.toLocaleString('en-IN')}</td>
                        <td>${record.bank_name || '-'}</td>
                        <td>${record.cheque_number || '-'}</td>
                        <td>${record.utr_number || '-'}</td>
                               
                        <td>${record.remarks || '-'}</td>           
                    </tr>
                `;
                                        });

                                        tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;

                                        detailsContent.innerHTML = tableHTML;
                                    }

                                    function formatDate(dateString) {
                                        if (!dateString) return '-';
                                        const date = new Date(dateString);
                                        return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                    }

                                    function closeDetails() {
                                        document.getElementById('detailsModal').style.display = 'none';
                                        document.body.style.overflow = 'auto'; // Restore background scrolling
                                    }

                                    function closeModal(event) {
                                        if (event.target === event.currentTarget) {
                                            closeDetails();
                                        }
                                    }

                                    // Close modal on Escape key
                                    document.addEventListener('keydown', function(event) {
                                        if (event.key === 'Escape') {
                                            closeDetails();
                                        }
                                    });
                                </script> -->

                                <!-- <script>
                                    function fetchProductDetails(productName) {
                                        const detailsModal = document.getElementById('detailsModal');
                                        const detailsContent = document.getElementById('detailsContent');
                                        const detailsTitle = document.getElementById('detailsTitle');

                                        // Show the modal
                                        detailsModal.style.display = 'block';
                                        document.body.style.overflow = 'hidden'; // Prevent background scrolling
                                        detailsTitle.textContent = `Payment Details - ${productName}`;
                                        detailsContent.innerHTML = '<div class="loading">Loading payment records...</div>';

                                        // Make AJAX request to fetch data from receiveallpayment table
                                        fetch('fetch_productplot_details.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: 'productname=' + encodeURIComponent(productName)
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success && data.records.length > 0) {
                                                    displayProductDetails(data.records);
                                                } else {
                                                    detailsContent.innerHTML = '<div class="no-data">No payment records found for this product.</div>';
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                detailsContent.innerHTML = '<div class="no-data">Error loading data. Please try again.</div>';
                                            });
                                    }

                                    function displayProductDetails(records) {
                                        const detailsContent = document.getElementById('detailsContent');

                                        let tableHTML = `
        <div class="table-container">
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Invoice ID</th>
                        <th>Customer Name</th>
                        <th>Associate ID</th>
                        <th>Net Amount</th>
                        <th>Pay Amount</th>
                        <th>Created Date</th>
                        <th>Due Amount</th>
                        <th>Payment Mode</th>
                        <th>Discount %</th>
                        <th>Discount ₹</th>
                        <th>Bank Name</th>
                        <th>Cheque No.</th>
                        <th>UTR Number</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
    `;

                                        records.forEach((record, index) => {
                                            const netAmount = parseFloat(record.net_amount || 0);
                                            const payAmount = parseFloat(record.payamount || 0);
                                            const dueAmount = parseFloat(record.due_amount || 0);
                                            const discountRs = parseFloat(record.discount_rs || 0);

                                            tableHTML += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${record.invoice_id || '-'}</strong></td>
                <td>${record.customer_name || '-'}</td>
                <td>${record.member_id || '-'}</td>
                <td class="amount-cell">₹${netAmount.toLocaleString('en-IN')}</td>
                <td class="amount-cell">₹${payAmount.toLocaleString('en-IN')}</td>
                <td>${formatDate(record.created_date)}</td>
                <td class="due-amount">₹${dueAmount.toLocaleString('en-IN')}</td>
                <td><span class="payment-mode">${record.payment_mode || '-'}</span></td>
                <td>${record.discount_percent || '-'}%</td>
                <td class="amount-cell">₹${discountRs.toLocaleString('en-IN')}</td>
                <td>${record.bank_name || '-'}</td>
                <td>${record.cheque_number || '-'}</td>
                <td>${record.utr_number || '-'}</td>
                <td class="remarks-cell" data-id="${record.id}" onclick="editRemarks(this, '${record.id}', '${record.remarks || ''}')">${record.remarks || '-'}</td>
            </tr>
        `;
                                        });

                                        tableHTML += `
                </tbody>
            </table>
        </div>
    `;

                                        detailsContent.innerHTML = tableHTML;
                                    }

                                    function editRemarks(cell, recordId, currentRemarks) {
                                        // Replace cell content with an input field
                                        cell.innerHTML = `
        <input type="text" value="${currentRemarks.replace(/"/g, '&quot;')}" id="remarks-input-${recordId}" style="width: 100%;">
        <button onclick="saveRemarks('${recordId}', this)">Save</button>
        <button onclick="cancelEdit('${recordId}', '${currentRemarks.replace(/"/g, '&quot;')}')">Cancel</button>
    `;
                                        cell.onclick = null; // Disable further clicks while editing
                                    }

                                    function saveRemarks(recordId, button) {
                                        const input = document.getElementById(`remarks-input-${recordId}`);
                                        const newRemarks = input.value.trim();
                                        const cell = button.parentElement;

                                        // Send AJAX request to update remarks
                                        fetch('update_remarks.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: `id=${encodeURIComponent(recordId)}&remarks=${encodeURIComponent(newRemarks)}`
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    cell.innerHTML = newRemarks || '-';
                                                    cell.onclick = () => editRemarks(cell, recordId, newRemarks);
                                                    alert('Remarks updated successfully!');
                                                } else {
                                                    alert('Error updating remarks: ' + data.error);
                                                    cancelEdit(recordId, newRemarks);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                alert('Error updating remarks. Please try again.');
                                                cancelEdit(recordId, newRemarks);
                                            });
                                    }

                                    function cancelEdit(recordId, originalRemarks) {
                                        const cell = document.getElementById(`remarks-input-${recordId}`).parentElement;
                                        cell.innerHTML = originalRemarks || '-';
                                        cell.onclick = () => editRemarks(cell, recordId, originalRemarks);
                                    }

                                    function formatDate(dateString) {
                                        if (!dateString) return '-';
                                        const date = new Date(dateString);
                                        return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                    }

                                    function closeDetails() {
                                        document.getElementById('detailsModal').style.display = 'none';
                                        document.body.style.overflow = 'auto'; // Restore background scrolling
                                    }

                                    function closeModal(event) {
                                        if (event.target === event.currentTarget) {
                                            closeDetails();
                                        }
                                    }

                                    // Close modal on Escape key
                                    document.addEventListener('keydown', function(event) {
                                        if (event.key === 'Escape') {
                                            closeDetails();
                                        }
                                    });
                                </script> -->

                                <script>
                                    function fetchProductDetails(productName) {
                                        const detailsModal = document.getElementById('detailsModal');
                                        const detailsContent = document.getElementById('detailsContent');
                                        const detailsTitle = document.getElementById('detailsTitle');

                                        // Show the modal
                                        detailsModal.style.display = 'block';
                                        document.body.style.overflow = 'hidden'; // Prevent background scrolling
                                        detailsTitle.textContent = `Payment Details - ${productName}`;
                                        detailsContent.innerHTML = '<div class="loading">Loading payment records...</div>';

                                        // Make AJAX request to fetch data from receiveallpayment table
                                        fetch('fetch_productplot_details.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: 'productname=' + encodeURIComponent(productName)
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success && data.records.length > 0) {
                                                    displayProductDetails(data.records);
                                                } else {
                                                    detailsContent.innerHTML = '<div class="no-data">No payment records found for this product.</div>';
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                detailsContent.innerHTML = '<div class="no-data">Error loading data. Please try again.</div>';
                                            });
                                    }

                                    function displayProductDetails(records) {
                                        const detailsContent = document.getElementById('detailsContent');

                                        let tableHTML = `
        <div class="table-container">
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Invoice ID</th>
                        <th>Customer Name</th>
                        <th>Associate ID</th>
                        <th>Net Amount</th>
                        <th>Pay Amount</th>
                        <th>Created Date</th>
                        <th>Due Amount</th>
                        <th>Payment Mode</th>
                        <th>Discount %</th>
                        <th>Discount ₹</th>
                        <th>Bank Name</th>
                        <th>Cheque No.</th>
                        <th>UTR Number</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
    `;

                                        records.forEach((record, index) => {
                                            const netAmount = parseFloat(record.net_amount || 0);
                                            const payAmount = parseFloat(record.payamount || 0);
                                            const dueAmount = parseFloat(record.due_amount || 0);
                                            const discountRs = parseFloat(record.discount_rs || 0);

                                            tableHTML += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${record.invoice_id || '-'}</strong></td>
                <td>${record.customer_name || '-'}</td>
                <td>${record.member_id || '-'}</td>
                <td class="amount-cell">₹${netAmount.toLocaleString('en-IN')}</td>
                <td class="amount-cell">₹${payAmount.toLocaleString('en-IN')}</td>
                <td>${formatDate(record.created_date)}</td>
                <td class="due-amount">₹${dueAmount.toLocaleString('en-IN')}</td>
                <td><span class="payment-mode">${record.payment_mode || '-'}</span></td>
                <td>${record.discount_percent || '-'}%</td>
                <td class="amount-cell">₹${discountRs.toLocaleString('en-IN')}</td>
                <td>${record.bank_name || '-'}</td>
                <td>${record.cheque_number || '-'}</td>
                <td>${record.utr_number || '-'}</td>
                <td class="remarks-cell" data-id="${record.id}" onclick="editRemarks(this, '${record.id}', '${record.remarks || ''}')">${record.remarks || '-'}</td>
            </tr>
        `;
                                        });

                                        tableHTML += `
                </tbody>
            </table>
        </div>
    `;

                                        detailsContent.innerHTML = tableHTML;
                                    }

                                    function editRemarks(cell, recordId, currentRemarks) {
                                        // Store original remarks in case we need to revert
                                        cell.dataset.originalRemarks = currentRemarks;
                                        // Replace cell content with a textarea
                                        cell.innerHTML = `
        <textarea id="remarks-input-${recordId}" rows="4" style="width: 100%;" onblur="saveRemarks(this, '${recordId}')">${currentRemarks.replace(/"/g, '&quot;')}</textarea>
    `;
                                        cell.onclick = null; // Disable further clicks while editing
                                        // Focus the textarea
                                        document.getElementById(`remarks-input-${recordId}`).focus();
                                    }

                                    function saveRemarks(textarea, recordId) {
                                        const cell = textarea.parentElement;
                                        const newRemarks = textarea.value.trim();
                                        const originalRemarks = cell.dataset.originalRemarks || '';

                                        // Send AJAX request to update remarks
                                        fetch('update_remarks.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: `id=${encodeURIComponent(recordId)}&remarks=${encodeURIComponent(newRemarks)}`
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    cell.innerHTML = newRemarks || '-';
                                                    cell.onclick = () => editRemarks(cell, recordId, newRemarks);
                                                    // Optional: Show a brief confirmation
                                                    alert('Remarks updated successfully!');
                                                } else {
                                                    console.error('Error:', data.error);
                                                    cell.innerHTML = originalRemarks || '-';
                                                    cell.onclick = () => editRemarks(cell, recordId, originalRemarks);
                                                    alert('Error updating remarks: ' + data.error);
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                cell.innerHTML = originalRemarks || '-';
                                                cell.onclick = () => editRemarks(cell, recordId, originalRemarks);
                                                alert('Error updating remarks. Please try again.');
                                            });
                                    }

                                    function formatDate(dateString) {
                                        if (!dateString) return '-';
                                        const date = new Date(dateString);
                                        return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN', {
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        });
                                    }

                                    function closeDetails() {
                                        document.getElementById('detailsModal').style.display = 'none';
                                        document.body.style.overflow = 'auto'; // Restore background scrolling
                                    }

                                    function closeModal(event) {
                                        if (event.target === event.currentTarget) {
                                            closeDetails();
                                        }
                                    }

                                    // Close modal on Escape key
                                    document.addEventListener('keydown', function(event) {
                                        if (event.key === 'Escape') {
                                            closeDetails();
                                        }
                                    });
                                </script>



                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- footer -->

            <?php include 'adminfooter.php'; ?>
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





    </form>


</body>

</html>