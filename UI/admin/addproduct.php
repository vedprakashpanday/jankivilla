<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ProductName           = $_POST['ProductName'] ?? '';
    $dimension             = $_POST['dimension'] ?? '';
    $product_type_id       = $_POST['product_type_id'] ?? null;
    $product_category      = $_POST['product_category'] ?? null;
    $Squarefeet            = $_POST['Squarefeet'] !== '' ? $_POST['Squarefeet'] : null;
    $BuildupArea           = $_POST['BuildupArea'] !== '' ? $_POST['BuildupArea'] : null;
    $CarpetArea            = $_POST['CarpetArea'] !== '' ? $_POST['CarpetArea'] : null;
    $Points                = $_POST['Points'] ?? 0;
    $Quantity              = $_POST['Quantity'] ?? 1.00;
    $Status                = 'available';

    // New charges - default to 0 if empty
    $ne_corner_charge      = $_POST['ne_corner_charge'] ?? 0;
    $east_facing_charge    = $_POST['east_facing_charge'] ?? 0;
    $mainroad_corner_charge = $_POST['mainroad_corner_charge'] ?? 0;

    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare("INSERT INTO products (
            product_type_id, product_category, ProductName, dimension, Squarefeet, 
            BuildupArea, CarpetArea, Points, Quantity, Status,
            ne_corner_charge, east_facing_charge, mainroad_corner_charge
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $product_type_id,
            $product_category,
            $ProductName,
            $dimension,
            $Squarefeet,
            $BuildupArea,
            $CarpetArea,
            $Points,
            $Quantity,
            $Status,
            $ne_corner_charge,
            $east_facing_charge,
            $mainroad_corner_charge
        ]);
        echo "<script>alert('Product inserted');window.location.href='';</script>";
    }

    if (isset($_POST['update']) && !empty($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $stmt = $pdo->prepare("UPDATE products SET 
            product_type_id=?, product_category=?, ProductName=?, dimension=?, Squarefeet=?,
            BuildupArea=?, CarpetArea=?, Points=?, Quantity=?,
            ne_corner_charge=?, east_facing_charge=?, mainroad_corner_charge=?
            WHERE id=?");

        $stmt->execute([
            $product_type_id,
            $product_category,
            $ProductName,
            $dimension,
            $Squarefeet,
            $BuildupArea,
            $CarpetArea,
            $Points,
            $Quantity,
            $ne_corner_charge,
            $east_facing_charge,
            $mainroad_corner_charge,
            $edit_id
        ]);
        echo "<script>alert('Product updated');window.location.href='';</script>";
    }
}

// Fetch single product for edit (unchanged)
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $edit_id = $_GET['id'];
}

// Delete product (unchanged)
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
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


    <style type="text/css">
        /* Chart.js */
        @keyframes chartjs-render-animation {
            from {
                opacity: .99
            }

            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            animation: chartjs-render-animation 1ms
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
            z-index: -1
        }

        .chartjs-size-monitor-expand>div {
            position: absolute;
            width: 1000000px;
            height: 1000000px;
            left: 0;
            top: 0
        }

        .chartjs-size-monitor-shrink>div {
            position: absolute;
            width: 200%;
            height: 200%;
            left: 0;
            top: 0
        }


        .franchiseSidebar:hover {
            background: #ff9027 !important;
        }



        /* Form Container */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
        }

        /* Heading Styling */
        .form-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 20px;
        }

        /* Label Styling */
        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        /* Input Fields */
        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="file"]:focus {
            border-color: #4A90E2;
            outline: none;
        }

        /* Button Styling */
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #4A90E2;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #357ABD;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .form-container {
                width: 90%;
                padding: 20px;
            }

            .form-container h2 {
                font-size: 20px;
            }
        }
    </style>

    <script type="text/ecmascript">
        var loadFile = function(event) {
            var image = document.getElementById('output');
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>



</head>

<body class="hold-transition skin-blue sidebar-mini" data-new-gr-c-s-check-loaded="14.1223.0" data-gr-ext-installed="">
    <div class="wrapper">
        <div class="container-scroller">
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <div class="franchise_nav_menu">
                    <?php include "adminheadersidepanel.php"; ?>
                </div>


                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="">
                            <div class="card">
                                <div class="" style="padding-top: 50px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">
                                                <h2>
                                                    Add Plot
                                                </h2>
                                                <hr>
                                                <div class="">
                                                    <div class="container mt-5">
                                                        <div class="card shadow rounded-4">
                                                            <div class="card-header bg-primary text-white rounded-top-4">
                                                                <h4 class="mb-0">
                                                                    <?php echo isset($edit_id) ? 'Edit Product' : 'Add New Product'; ?>
                                                                </h4>
                                                            </div>
                                                            <div class="card-body p-4">
                                                                <form method="post">
                                                                    <input type="hidden" name="edit_id" value="<?php echo $edit_id ?? ''; ?>">

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Plot Type</label>
                                                                        <select class="form-select" name="product_type_id" required>
                                                                            <option value="1" <?php echo (isset($edit_data['product_type_id']) && $edit_data['product_type_id'] == 1) ? 'selected' : ''; ?>>One time Registry</option>
                                                                            <option value="2" <?php echo (isset($edit_data['product_type_id']) && $edit_data['product_type_id'] == 2) ? 'selected' : ''; ?>>EMI Mode</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Product Category</label>
                                                                        <select class="form-select" name="product_category" id="product_category" required>
                                                                            <option value="">-- Select Category --</option>
                                                                            <option value="plot" <?php echo (isset($edit_data['product_category']) && $edit_data['product_category'] == 'plot') ? 'selected' : ''; ?>>Plot</option>
                                                                            <option value="both" <?php echo (isset($edit_data['product_category']) && $edit_data['product_category'] == 'both') ? 'selected' : ''; ?>>Plot &amp; Villa (Both)</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Plot / Product Name</label>
                                                                        <input type="text" class="form-control" name="ProductName" required placeholder="Enter product name" value="<?php echo $edit_data['ProductName'] ?? ''; ?>">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">-- Add Dimension --</label>
                                                                        <input type="text" class="form-control" name="dimension" placeholder="Add Plot Dimension">
                                                                    </div>

                                                                    <!-- Plot fields -->
                                                                    <div id="plot_fields" class="row" style="display:none;">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Square Feet</label>
                                                                            <input type="number" class="form-control" name="Squarefeet" placeholder="e.g. 2700" value="<?php echo $edit_data['Squarefeet'] ?? ''; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4 mb-3">
                                                                        <label class="form-label fw-semibold">North East Corner Charge</label>
                                                                        <input type="number" step="0.01" class="form-control" name="ne_corner_charge" placeholder="e.g. 100000" value="<?php echo $edit_data['ne_corner_charge'] ?? ''; ?>">
                                                                    </div>
                                                                    <div class="col-md-4 mb-3">
                                                                        <label class="form-label fw-semibold">East Facing Charge</label>
                                                                        <input type="number" step="0.01" class="form-control" name="east_facing_charge" placeholder="e.g. 50000" value="<?php echo $edit_data['east_facing_charge'] ?? ''; ?>">
                                                                    </div>
                                                                    <div class="col-md-4 mb-3">
                                                                        <label class="form-label fw-semibold">Main Road Corner Charge</label>
                                                                        <input type="number" step="0.01" class="form-control" name="mainroad_corner_charge" placeholder="e.g. 75000" value="<?php echo $edit_data['mainroad_corner_charge'] ?? ''; ?>">
                                                                    </div>

                                                                    <!-- Villa fields -->
                                                                    <div id="villa_fields" class="row" style="display:none;">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Buildup Area (sqft)</label>
                                                                            <input type="number" step="0.01" class="form-control" name="BuildupArea" placeholder="e.g. 1800" value="<?php echo $edit_data['BuildupArea'] ?? ''; ?>">
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-semibold">Carpet Area (sqft)</label>
                                                                            <input type="number" step="0.01" class="form-control" name="CarpetArea" placeholder="e.g. 1500" value="<?php echo $edit_data['CarpetArea'] ?? ''; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Points & Quantity (keep as hidden / existing) -->
                                                                    <div class="col-md-6 mb-3 d-none">
                                                                        <label class="form-label fw-semibold">Points</label>
                                                                        <input type="hidden" class="form-control" name="Points" placeholder="e.g. 10" value="<?php echo $edit_data['Points'] ?? '0'; ?>">
                                                                    </div>

                                                                    <div class="col-md-6 mb-3 d-none">
                                                                        <label class="form-label fw-semibold">Quantity</label>
                                                                        <input type="hidden" step="0.01" class="form-control" name="Quantity" value="<?php echo $edit_data['Quantity'] ?? '1.00'; ?>">
                                                                    </div>

                                                                    <div class="text-end mt-3">
                                                                        <button type="submit" name="<?php echo isset($edit_id) ? 'update' : 'submit'; ?>" class="btn btn-success px-4 rounded-pill">
                                                                            <?php echo isset($edit_id) ? 'Update' : 'Add'; ?> Product
                                                                        </button>
                                                                    </div>
                                                                </form>

                                                                <script>
                                                                    document.addEventListener("DOMContentLoaded", function() {
                                                                        const cat = document.getElementById("product_category");
                                                                        const plotFields = document.getElementById("plot_fields");
                                                                        const villaFields = document.getElementById("villa_fields");
                                                                        const squareFeetInput = document.querySelector("input[name='Squarefeet']");
                                                                        const buildupAreaInput = document.querySelector("input[name='BuildupArea']");
                                                                        const carpetAreaInput = document.querySelector("input[name='CarpetArea']");
                                                                        const form = document.querySelector("form");

                                                                        function toggleFields() {
                                                                            if (!cat) return;
                                                                            if (cat.value === "plot") {
                                                                                plotFields.style.display = "flex";
                                                                                villaFields.style.display = "none";
                                                                            } else if (cat.value === "both") {
                                                                                plotFields.style.display = "flex";
                                                                                villaFields.style.display = "flex";
                                                                            } else {
                                                                                plotFields.style.display = "none";
                                                                                villaFields.style.display = "none";
                                                                            }
                                                                        }

                                                                        // Validation before submit
                                                                        form.addEventListener("submit", function(e) {
                                                                            if (cat.value === "both") {
                                                                                const sqft = parseFloat(squareFeetInput.value) || 0;
                                                                                const buildup = parseFloat(buildupAreaInput.value) || 0;
                                                                                const carpet = parseFloat(carpetAreaInput.value) || 0;
                                                                                const total = buildup + carpet;

                                                                                // Compare with small tolerance to avoid float issues
                                                                                if (Math.abs(sqft - total) > 0.01) {
                                                                                    e.preventDefault();
                                                                                    alert("❌ Square Feet must be equal to (Buildup Area + Carpet Area). Please recheck your entries.");

                                                                                    // Clear all fields
                                                                                    buildupAreaInput.value = '';
                                                                                    carpetAreaInput.value = '';

                                                                                    // Focus back to Square Feet
                                                                                    squareFeetInput.focus();
                                                                                    return false;
                                                                                }
                                                                            }
                                                                        });

                                                                        cat.addEventListener("change", toggleFields);
                                                                        toggleFields(); // Run on load (edit mode)
                                                                    });
                                                                </script>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="container mt-5" style="overflow: auto;">
                                                                <h4>Product List</h4>
                                                                <table class="table table-bordered table-striped mt-3">
                                                                    <thead class="table-dark">
                                                                        <tr>
                                                                            <th>Plot Type</th> <!-- existing product_type_id -->
                                                                            <th>Category</th> <!-- new product_category -->
                                                                            <th>Plot Name</th>
                                                                            <th>Dimension</th>
                                                                            <th>Square Feet</th>
                                                                            <th>Buildup Area</th>
                                                                            <th>Carpet Area</th>
                                                                            <th>Quantity</th>
                                                                            <th>NE Corner Charge</th>
                                                                            <th>East Facing Charge</th>
                                                                            <th>Main Road Corner Charge</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                                                                        foreach ($products as $row): ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <?php echo $row['product_type_id'] == 1 ? 'One time Registry' : ($row['product_type_id'] == 2 ? 'EMI Mode' : 'Unknown'); ?>
                                                                                </td>
                                                                                <td><?php
                                                                                    echo ($row['product_category'] === 'plot') ? 'Plot' : (($row['product_category'] === 'villa') ? 'Villa' : (($row['product_category'] === 'both') ? 'Plot & Villa' : '-'));
                                                                                    ?></td>
                                                                                <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                                                                                <td><?php echo ($row['dimension'] !== null && $row['dimension'] !== '') ? $row['dimension'] : '-'; ?></td>
                                                                                <td><?php echo ($row['Squarefeet'] !== null && $row['Squarefeet'] !== '') ? $row['Squarefeet'] : '-'; ?></td>
                                                                                <td><?php echo ($row['BuildupArea'] !== null && $row['BuildupArea'] !== '') ? $row['BuildupArea'] : '-'; ?></td>
                                                                                <td><?php echo ($row['CarpetArea'] !== null && $row['CarpetArea'] !== '') ? $row['CarpetArea'] : '-'; ?></td>
                                                                                <td><?php echo $row['Quantity']; ?></td>
                                                                                <td><?php echo number_format($row['ne_corner_charge'] ?? 0, 2); ?></td>
                                                                                <td><?php echo number_format($row['east_facing_charge'] ?? 0, 2); ?></td>
                                                                                <td><?php echo number_format($row['mainroad_corner_charge'] ?? 0, 2); ?></td>
                                                                                <td><?php echo $row['Status']; ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php include "adminfooter.php"; ?>
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

</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>