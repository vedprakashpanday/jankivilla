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

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <div class="container-scroller">

            <!-- partial -->
            <div class="container-fluid page-body-wrapper">

                <!-- side panel header -->
                <?php include 'adminheadersidepanel.php'; ?>

                <div class="main-panel">
                    <div class="content-wrapper">
                        <div class="">
                            <div class="card">

                                <div class="" style="padding-top: 0px; padding-bottom: 50px;">
                                    <div class="row justify-content-center">

                                        <div class="col-md-12">
                                            <div style="background: #fff; padding: 20px; border: 2px solid #fff; box-shadow: 1px 3px 12px 4px #988f8f;">

                                                <div class="clr"></div>
                                                <div class="table-section">
                                                    <h3>Associate Sale Report</h3>
                                                    <hr>
                                                </div>

                                                <div class="heading">
                                                    Payment Details</div>
                                                <div>

                                                    <br>
                                                    <div id="" style="overflow:auto;width: 94%">
                                                        <?php

                                                        $query = "
    SELECT 
        c.member_id,
        r.m_name
    FROM tbl_customeramount c
    LEFT JOIN tbl_regist r 
        ON c.member_id = r.mem_sid
    WHERE c.producttype = 1 || c.producttype = 2
    GROUP BY c.member_id, r.m_name
    ORDER BY c.member_id ASC
";

                                                        $stmt = $pdo->prepare($query);
                                                        $stmt->execute();
                                                        $customer_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                        // Display table
                                                        echo "<div class='table-responsive' style='overflow-x: scroll;'>";
                                                        echo "<table class='table table-bordered' id='customerTable'>";
                                                        echo "<thead>
<tr>
    <th>View</th>
    <th>Member ID</th>
    <th>Member Name</th>
</tr>
</thead>
<tbody>";

                                                        if (!empty($customer_data)) {
                                                            $row_index = 0;
                                                            foreach ($customer_data as $row) {
                                                                $member_id = $row['member_id'];
                                                                $unique_row_id = "row-$row_index";

                                                                echo "<tr class='main-row'>";
                                                                echo "<td><button type='button' class='btn btn-primary view-payments' 
                data-row-id='$unique_row_id' 
                data-member-id='$member_id'>View</button></td>";
                                                                echo "<td>" . ($member_id ?: 'N/A') . "</td>";
                                                                echo "<td>" . ($row['m_name'] ?: 'N/A') . "</td>";
                                                                echo "</tr>";

                                                                // hidden row
                                                                echo "<tr class='payment-details-row' id='payment-details-$unique_row_id' style='display:none;'>";
                                                                echo "<td colspan='3' class='payment-details-content'></td>";
                                                                echo "</tr>";

                                                                $row_index++;
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='3'>No records found.</td></tr>";
                                                        }

                                                        echo "</tbody></table></div>";
                                                        ?>

                                                        <script>
                                                            $(document).ready(function() {
                                                                $('.view-payments').on('click', function() {
                                                                    var rowId = $(this).data('row-id');
                                                                    var memberId = $(this).data('member-id');
                                                                    var $detailsRow = $('#payment-details-' + rowId);
                                                                    var $detailsContent = $detailsRow.find('.payment-details-content');

                                                                    if ($detailsRow.is(':visible')) {
                                                                        $detailsRow.hide();
                                                                        return;
                                                                    }

                                                                    $detailsRow.show();
                                                                    $detailsContent.html('<p>Loading...</p>');

                                                                    $.ajax({
                                                                        url: 'member_plot_payments.php',
                                                                        method: 'POST',
                                                                        data: {
                                                                            member_id: memberId
                                                                        },
                                                                        success: function(response) {
                                                                            $detailsContent.html(response);
                                                                        },
                                                                        error: function(xhr, status, error) {
                                                                            $detailsContent.html('<p>Error loading payment details: ' + error + '</p>');
                                                                        }
                                                                    });
                                                                });
                                                            });
                                                        </script>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
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


            <!-- <script>
                $(document).ready(function() {
                    $('#customerTable').DataTable({
                        "ordering": false
                    });

                    $('.dropdown-toggle').dropdown();

                });
            </script> -->

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

</body>

</html>