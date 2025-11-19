<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: ../../superadminlogin.php');
    exit();
}

// Check if customer_id is provided
if (!isset($_GET['customer_id'])) {
    die("Customer ID not provided.");
}

$customer_id = $_GET['customer_id'];

// Fetch customer data
try {
    $stmt = $pdo->prepare("
        SELECT customer_id, password, customer_name, customer_mobile, customer_email,
               aadhar_number, pan_number, nominee_name, nominee_aadhar, address,
               state, district
        FROM customer_details 
        WHERE customer_id = :customer_id
    ");
    $stmt->execute([':customer_id' => $customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        die("Customer not found.");
    }
} catch (PDOException $e) {
    die("Error fetching customer: " . $e->getMessage());
}

// Handle form submission for update
if (isset($_POST['btnsubmit'])) {
    try {
        $update_sql = "UPDATE customer_details SET 
            customer_name = :customer_name,
            customer_mobile = :customer_mobile,
            customer_email = :customer_email,
            aadhar_number = :aadhar_number,
            pan_number = :pan_number,
            nominee_name = :nominee_name,
            nominee_aadhar = :nominee_aadhar,
            address = :address,
            state = :state,
            district = :district,
            password = :password
            WHERE customer_id = :customer_id";

        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([
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
            ':password'         => $_POST['password'],
            ':customer_id'      => $customer_id
        ]);

        // Redirect back to the table page after update
        echo "<script>
                alert('Customer updated successfully!');
                window.location.href='RptCustomerDetails.php'; // Replace with your table page
              </script>";
    } catch (PDOException $e) {
        echo "Error updating customer: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            margin-top: 50px;
        }

        h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .form-label {
            font-weight: 500;
            color: #555;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        textarea.form-control {
            resize: vertical;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Edit Customer Details</h3>
        <form method="POST" action="">
            <div class="row">
                <!-- Customer ID and Password -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Customer ID (Read-only):</label>
                    <input type="text" class="form-control" name="customer_id" value="<?php echo htmlspecialchars($customer['customer_id']); ?>" readonly>
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Password:</label>
                    <input type="text" class="form-control" name="password" value="<?php echo htmlspecialchars($customer['password']); ?>">
                </div>

                <!-- Customer Name and Mobile -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Customer Name:</label>
                    <input type="text" class="form-control" name="customer_name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Customer Mobile Number:</label>
                    <input type="text" class="form-control" name="customer_mobile" value="<?php echo htmlspecialchars($customer['customer_mobile']); ?>" required>
                </div>

                <!-- Email and Aadhar -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="customer_email" value="<?php echo htmlspecialchars($customer['customer_email']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Aadhar Number:</label>
                    <input type="text" class="form-control" name="aadhar_number" value="<?php echo htmlspecialchars($customer['aadhar_number']); ?>">
                </div>

                <!-- PAN and Nominee Name -->
                <div class="col-md-6 form-section">
                    <label class="form-label">PAN:</label>
                    <input type="text" class="form-control" name="pan_number" value="<?php echo htmlspecialchars($customer['pan_number']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Name:</label>
                    <input type="text" class="form-control" name="nominee_name" value="<?php echo htmlspecialchars($customer['nominee_name']); ?>">
                </div>

                <!-- Nominee Aadhar and Address -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Aadhar:</label>
                    <input type="text" class="form-control" name="nominee_aadhar" value="<?php echo htmlspecialchars($customer['nominee_aadhar']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Address:</label>
                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($customer['address']); ?></textarea>
                </div>

                <!-- State and District -->
                <div class="col-md-6 form-section">
                    <label class="form-label">State:</label>
                    <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($customer['state']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">District:</label>
                    <input type="text" class="form-control" name="district" value="<?php echo htmlspecialchars($customer['district']); ?>">
                </div>

                <!-- Buttons -->
                <div class="col-md-12 form-section button-group">
                    <input type="submit" name="btnsubmit" value="Update Customer" class="btn btn-primary">
                    <a href="RptCustomerDetails.php" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (optional, for any interactive features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>