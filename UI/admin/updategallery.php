<?php
session_start();
include_once "connectdb.php";

// Check if user is logged in and has admin status
if (!isset($_SESSION['sponsor_id']) || $_SESSION['status'] !== 'active') {
    header('Location: ../../adminlogin.php'); // Redirect to admin login
    exit();
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
//     $delete_id = $_POST['delete_id'];

//     if (!empty($delete_id)) {
//         try {
//             // Fetch the image filename before deleting
//             $stmt = $pdo->prepare("SELECT image FROM img_gallery WHERE id = :id");
//             $stmt->execute(['id' => $delete_id]);
//             $imageData = $stmt->fetch(PDO::FETCH_ASSOC);

//             if ($imageData) {
//                 $imagePath = '../../img_gallery/' . $imageData['image'];

//                 // Delete the record from the database
//                 $stmt = $pdo->prepare("DELETE FROM img_gallery WHERE id = :id");
//                 $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

//                 if ($stmt->execute()) {
//                     // Unlink (delete) the image file if it exists
//                     if (file_exists($imagePath) && !empty($imageData['image'])) {
//                         unlink($imagePath);
//                     }

//                     echo "<script>alert('Image deleted successfully!'); window.location.href = '';</script>";
//                 } else {
//                     echo "<script>alert('Failed to delete image!');</script>";
//                 }
//             } else {
//                 echo "<script>alert('Image not found!');</script>";
//             }
//         } catch (PDOException $e) {
//             echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
//         }
//     }
// }


// Fetch all images for display
$sql = "SELECT * FROM img_gallery order by id desc";
$stmt = $pdo->query($sql);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if editing an existing image
$edit_mode = false;
$edit_id = null;
$edit_title = '';
$edit_image = '';

if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $sql = "SELECT * FROM img_gallery WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($edit_data) {
        $edit_mode = true;
        $edit_title = $edit_data['title'];
        $edit_image = $edit_data['image'];
    }
}

// Handle form submission
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $title = $_POST['title'];
//     $image = $edit_image; // Default to existing image

//     // Handle image upload if a new file is provided
//     if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
//         $image = $_FILES['image']['name'];
//         $target_dir = '../../img_gallery/';
//         $target_file = $target_dir . basename($image);

//         if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
//             echo "<div class='alert alert-danger'>Error uploading file.</div>";
//             $image = $edit_image; // Revert to old image on failure
//         }
//     }

//     try {
//         if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
//             // Update existing record
//             $sql = "UPDATE img_gallery SET title = :title, image = :image WHERE id = :id";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 'title' => $title,
//                 'image' => $image,
//                 'id' => $_POST['edit_id']
//             ]);

//             if ($edit_mode && $image !== $edit_image) {
//                 unlink($target_dir . $edit_image);
//             }

//             echo "<div class='alert alert-success'>Image updated successfully!</div>";
//         } else {
//             // Insert new record
//             $sql = "INSERT INTO img_gallery (title, image) VALUES (:title, :image)";
//             $stmt = $pdo->prepare($sql);
//             $stmt->execute([
//                 'title' => $title,
//                 'image' => $image
//             ]);
//             echo "<div class='alert alert-success'>Image uploaded successfully!</div>";
//         }

//         // Refresh page after submission to clear POST data and reload images
//         header("Location: updategallery.php");
//         exit();
//     } catch (PDOException $e) {
//         echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
//     }
// }
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
                                                    Update Gallery
                                                </h2>
                                                <hr>
                                                <div class="">
                                                    <form action="" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="edit_id" value="<?php echo $edit_mode ? $edit_id : ''; ?>">
                                                        <div class="form-group">
                                                            <label for="title">Title:</label>
                                                            <input type="text" id="title" name="title" class="form-control"
                                                                value="<?php echo $edit_mode ? htmlspecialchars($edit_title) : ''; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="image">Select Image:</label>
                                                            <input type="file" id="image" name="image" class="form-control-file"
                                                                <?php echo $edit_mode ? '' : 'required'; ?>>
                                                            <?php if ($edit_mode && $edit_image): ?>
                                                                <img src="../../img_gallery/<?php echo htmlspecialchars($edit_image); ?>" style="height:250px;width:auto;">
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-primary">
                                                                <?php echo $edit_mode ? 'Update Image' : 'Upload Image'; ?>
                                                            </button>
                                                            <?php if ($edit_mode): ?>
                                                                <a href="updategallery.php" class="btn btn-secondary">Cancel Edit</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </form>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <table class="table-style" cellspacing="0" cellpadding="3" rules="all" id="ContentPlaceHolder1_gvList"
                                                                style="background-color:White;border-color:#E7E7FF;border-width:1px;border-style:None;font-weight:bold;width:100%;border-collapse:collapse;">
                                                                <thead>
                                                                    <tr style="color:#F7F7F7;background-color:#383F3F;font-weight:bold;">
                                                                        <th scope="col" style="width:100px;">Edit</th>
                                                                        <th class="hideColumn" align="left" scope="col" style="width:100px;">Delete</th>
                                                                        <th align="left" scope="col" style="width:100px;">Title</th>
                                                                        <th align="left" scope="col" style="width:100px;">Image</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($images as $row): ?>
                                                                        <tr style="color:#4A3C8C;background-color:#F7F7F7;">
                                                                            <td>
                                                                                <center>
                                                                                    <a href="updategallery.php?id=<?php echo $row['id']; ?>">
                                                                                        <button class="btn-success" style="width:80px; text-decoration:none; color:white;">Edit</button>
                                                                                    </a>
                                                                                </center>
                                                                            </td>
                                                                            <td class="hideColumn">
                                                                                <center>
                                                                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                                                        <button type="submit" name="delete" class="btn-danger" style="width:80px; text-decoration:none; color:white;">Delete</button>
                                                                                    </form>
                                                                                </center>
                                                                            </td>

                                                                            <td>
                                                                                <span><?php echo htmlspecialchars($row['title']); ?></span>
                                                                            </td>
                                                                            <td>
                                                                                <img src="../../img_gallery/<?php echo htmlspecialchars($row['image']); ?>" style="height:70px;width:70px;">
                                                                            </td>
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