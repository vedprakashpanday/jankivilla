<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// Check if member 'id' is provided (changed from customer_id)
if (!isset($_GET['id'])) {
    die("Member ID not provided.");
}

$member_id = $_GET['id'];

// Fetch member data from tbl_regist
try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM tbl_regist 
        WHERE id = :id
    ");
    $stmt->execute([':id' => $member_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        die("Member not found.");
    }
} catch (PDOException $e) {
    die("Error fetching member: " . $e->getMessage());
}

// Handle form submission for update
if (isset($_POST['btnsubmit'])) {
    try {
        $update_sql = "UPDATE tbl_regist SET 
            m_name = :m_name,
            m_num = :m_num,
            m_email = :m_email,
            m_password = :m_password,
            aadhar_number = :aadhar_number,
            pan_number = :pan_number,
            address = :address,
            state_name = :state_name,
            district_name = :district_name,
            
            -- Nominee fields
            nominee_fullname = :nominee_fullname,
            nominee_father_name = :nominee_father_name,
            nominee_husband_name = :nominee_husband_name,
            nominee_date_of_birth = :nominee_date_of_birth,
            nominee_relationship = :nominee_relationship,
            nominee_aadhar_no = :nominee_aadhar_no,
            nominee_pan_no = :nominee_pan_no,
            nominee_native_place = :nominee_native_place,
            nominee_communication = :nominee_communication,
            nominee_city_town_village = :nominee_city_town_village,
            nominee_pincode = :nominee_pincode,
            nominee_contact = :nominee_contact,
            nominee_email = :nominee_email
            
            WHERE id = :id";

        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([
            // Member fields
            ':m_name'         => $_POST['m_name'],
            ':m_num'          => $_POST['m_num'],
            ':m_email'        => $_POST['m_email'],
            ':m_password'     => $_POST['m_password'],
            ':aadhar_number'  => $_POST['aadhar_number'],
            ':pan_number'     => $_POST['pan_number'],
            ':address'        => $_POST['address'],
            ':state_name'     => $_POST['state_name'],
            ':district_name'  => $_POST['district_name'],

            // Nominee fields
            ':nominee_fullname'     => $_POST['nominee_fullname'],
            ':nominee_father_name'  => $_POST['nominee_father_name'],
            ':nominee_husband_name' => $_POST['nominee_husband_name'],
            ':nominee_date_of_birth' => $_POST['nominee_date_of_birth'] ?? '',
            ':nominee_relationship' => $_POST['nominee_relationship'],
            ':nominee_aadhar_no'    => $_POST['nominee_aadhar_no'],
            ':nominee_pan_no'       => $_POST['nominee_pan_no'],
            ':nominee_native_place' => $_POST['nominee_native_place'],
            ':nominee_communication'  => $_POST['nominee_communication'],
            ':nominee_city_town_village' => $_POST['nominee_city_town_village'],
            ':nominee_pincode'      => $_POST['nominee_pincode'],
            ':nominee_contact'      => $_POST['nominee_contact'],
            ':nominee_email'        => $_POST['nominee_email'],

            // WHERE clause
            ':id'             => $member_id
        ]);

        // Redirect back to the table page after update
        echo "<script>
                alert('Member updated successfully!');
                window.location.href='membernominee.php'; // Changed to new report name
              </script>";
    } catch (PDOException $e) {
        echo "Error updating member: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member Details</title>
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
            max-width: 1000px;
            /* Increased width for new fields */
            margin-top: 50px;
            margin-bottom: 50px;
        }

        h3,
        h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        h4 {
            font-size: 1.25rem;
            border-color: #6c757d;
            margin-top: 30px;
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

        .form-control[readonly] {
            background-color: #e9ecef;
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
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h3>Edit Member and Nominee Details</h3>
        <form method="POST" action="">

            <h4>Member Details</h4>
            <div class="row">
                <!-- Member ID and Password -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Member ID (Read-only):</label>
                    <input type="text" class="form-control" name="mem_sid" value="<?php echo htmlspecialchars($member['mem_sid']); ?>" readonly>
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Password:</label>
                    <input type="text" class="form-control" name="m_password" value="<?php echo htmlspecialchars($member['m_password']); ?>">
                </div>

                <!-- Member Name and Mobile -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Member Name:</label>
                    <input type="text" class="form-control" name="m_name" value="<?php echo htmlspecialchars($member['m_name']); ?>" required>
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Member Mobile Number:</label>
                    <input type="text" class="form-control" name="m_num" value="<?php echo htmlspecialchars($member['m_num']); ?>" required>
                </div>

                <!-- Email and Aadhar -->
                <div class="col-md-6 form-section">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="m_email" value="<?php echo htmlspecialchars($member['m_email']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Aadhar Number:</label>
                    <input type="text" class="form-control" name="aadhar_number" value="<?php echo htmlspecialchars($member['aadhar_number']); ?>">
                </div>

                <!-- PAN and Address -->
                <div class="col-md-6 form-section">
                    <label class="form-label">PAN:</label>
                    <input type="text" class="form-control" name="pan_number" value="<?php echo htmlspecialchars($member['pan_number']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Address:</label>
                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($member['address']); ?></textarea>
                </div>

                <!-- State and District -->
                <div class="col-md-6 form-section">
                    <label class="form-label">State:</label>
                    <input type="text" class="form-control" name="state_name" value="<?php echo htmlspecialchars($member['state_name']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">District:</label>
                    <input type="text" class="form-control" name="district_name" value="<?php echo htmlspecialchars($member['district_name']); ?>">
                </div>
            </div>

            <!-- Nominee Details Section -->
            <h4>Nominee Details</h4>
            <div class="row">
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Fullname:</label>
                    <input type="text" class="form-control" name="nominee_fullname" value="<?php echo htmlspecialchars($member['nominee_fullname']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Father's Name:</label>
                    <input type="text" class="form-control" name="nominee_father_name" value="<?php echo htmlspecialchars($member['nominee_father_name']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Husband's Name:</label>
                    <input type="text" class="form-control" name="nominee_husband_name" value="<?php echo htmlspecialchars($member['nominee_husband_name']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Date of Birth:</label>
                    <input type="date" class="form-control" name="nominee_date_of_birth" value="<?php echo htmlspecialchars($member['nominee_date_of_birth']) ?? ''; ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Relationship with Nominee:</label>
                    <input type="text" class="form-control" name="nominee_relationship" value="<?php echo htmlspecialchars($member['nominee_relationship']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Aadhar No.:</label>
                    <input type="text" class="form-control" name="nominee_aadhar_no" value="<?php echo htmlspecialchars($member['nominee_aadhar_no']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Pan No.:</label>
                    <input type="text" class="form-control" name="nominee_pan_no" value="<?php echo htmlspecialchars($member['nominee_pan_no']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Native Place:</label>
                    <input type="text" class="form-control" name="nominee_native_place" value="<?php echo htmlspecialchars($member['nominee_native_place']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee City/Town/Village:</label>
                    <input type="text" class="form-control" name="nominee_city_town_village" value="<?php echo htmlspecialchars($member['nominee_city_town_village']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Pincode:</label>
                    <input type="text" class="form-control" name="nominee_pincode" value="<?php echo htmlspecialchars($member['nominee_pincode']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Contact No.:</label>
                    <input type="text" class="form-control" name="nominee_contact" value="<?php echo htmlspecialchars($member['nominee_contact']); ?>">
                </div>
                <div class="col-md-6 form-section">
                    <label class="form-label">Nominee Email:</label>
                    <input type="email" class="form-control" name="nominee_email" value="<?php echo htmlspecialchars($member['nominee_email']); ?>">
                </div>
                <div class="col-md-12 form-section">
                    <label class="form-label">Nominee Communication Address:</label>
                    <textarea class="form-control" name="nominee_communication" rows="3"><?php echo htmlspecialchars($member['nominee_communication']); ?></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="row">
                <div class="col-md-12 form-section button-group">
                    <input type="submit" name="btnsubmit" value="Update Member" class="btn btn-primary">
                    <a href="RptMemberDetails.php" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>