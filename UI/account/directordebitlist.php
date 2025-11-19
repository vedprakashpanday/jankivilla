<?php
session_start();
include_once 'connectdb.php';

// Redirect if not logged in
if (!isset($_SESSION['sponsor_id']) || $_SESSION['role'] !== 'account') {
    header('Location: ../../account.php');
    exit();
}


// $month = $_GET['month'] ?? null;
// $year = $_GET['year'] ?? date('Y'); // Default to current year
// $category = $_GET['category'] ?? null;
// $payment_mode = $_GET['payment_mode'] ?? null;
// $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
// $limit = 20; // Records per page
// $offset = ($page - 1) * $limit;

// // Handle clear filters
// if (isset($_GET['clear_filters'])) {
//     header('Location: ' . $_SERVER['PHP_SELF']);
//     exit;
// }

// // Calculate date range based on month selection
// $date_from = null;
// $date_to = null;

// if ($month && !isset($_GET['clear_filters'])) {
//     // Create date range for selected month
//     $date_from = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
//     $date_to = date('Y-m-t', strtotime($date_from)); // Last day of the month
// } else {
//     // Default to current month if no month selected
//     if (!isset($_GET['clear_filters'])) {
//         $date_from = date('Y-m-01'); // First day of current month
//         $date_to = date('Y-m-t');    // Last day of current month
//         $month = date('n'); // Set current month as selected
//     }
// }

// // Build the filters array
// $filters = [];
// if ($date_from && !isset($_GET['clear_filters'])) {
//     $filters['date_from'] = $date_from;
// }
// if ($date_to && !isset($_GET['clear_filters'])) {
//     $filters['date_to'] = $date_to;
// }
// if ($category && !isset($_GET['clear_filters'])) {
//     $filters['category'] = $category;
// }
// if ($payment_mode && !isset($_GET['clear_filters'])) {
//     $filters['payment_mode'] = $payment_mode;
// }

// try {
//     // Get transactions with pagination
//     $sql = "SELECT 
//                 id,
//                 account_id,
//                 transaction_date,
//                 amount,
//                 authorized_by,
//                 payment_mode,
//                 transaction_id,
//                 cheque_no,
//                 bank_name,
//                 cheque_date,
//                 transaction_category,
//                 payee_name,
//                 description,
//                 expense_category,
//                 vehicle_info,
//                 driver_name,
//                 kilometers,
//                 farmer_name,
//                 salesperson_name,
//                 commission_type,
//                 plot_commission,
//                 sales_expense_type,
//                 created_at
//             FROM tbl_debit_transactions 
//             WHERE 1=1";

//     $params = [];

//     // Add filters to SQL only if they exist
//     if (!empty($filters['date_from'])) {
//         $sql .= " AND transaction_date >= :date_from";
//         $params['date_from'] = $filters['date_from'];
//     }
//     if (!empty($filters['date_to'])) {
//         $sql .= " AND transaction_date <= :date_to";
//         $params['date_to'] = $filters['date_to'];
//     }
//     if (!empty($filters['category'])) {
//         $sql .= " AND transaction_category = :category";
//         $params['category'] = $filters['category'];
//     }
//     if (!empty($filters['payment_mode'])) {
//         $sql .= " AND payment_mode = :payment_mode";
//         $params['payment_mode'] = $filters['payment_mode'];
//     }

//     $sql .= " ORDER BY transaction_date DESC, created_at DESC LIMIT :limit OFFSET :offset";
//     $params['limit'] = $limit;
//     $params['offset'] = $offset;

//     $stmt = $pdo->prepare($sql);
//     foreach ($params as $key => $value) {
//         if ($key === 'limit' || $key === 'offset') {
//             $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
//         } else {
//             $stmt->bindValue($key, $value);
//         }
//     }
//     $stmt->execute();
//     $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     // Get total count for pagination
//     $count_sql = "SELECT COUNT(*) FROM tbl_debit_transactions WHERE 1=1";
//     $count_params = [];
//     if (!empty($filters['date_from'])) {
//         $count_sql .= " AND transaction_date >= :date_from";
//         $count_params['date_from'] = $filters['date_from'];
//     }
//     if (!empty($filters['date_to'])) {
//         $count_sql .= " AND transaction_date <= :date_to";
//         $count_params['date_to'] = $filters['date_to'];
//     }
//     if (!empty($filters['category'])) {
//         $count_sql .= " AND transaction_category = :category";
//         $count_params['category'] = $filters['category'];
//     }
//     if (!empty($filters['payment_mode'])) {
//         $count_sql .= " AND payment_mode = :payment_mode";
//         $count_params['payment_mode'] = $filters['payment_mode'];
//     }

//     $count_stmt = $pdo->prepare($count_sql);
//     $count_stmt->execute($count_params);
//     $total_records = $count_stmt->fetchColumn();
//     $total_pages = ceil($total_records / $limit);

//     // Get summary data
//     $summary_sql = "SELECT 
//                         COUNT(*) as total_transactions,
//                         SUM(amount) as total_amount,
//                         AVG(amount) as average_amount
//                     FROM tbl_debit_transactions WHERE 1=1";
//     $summary_params = [];
//     if (!empty($filters['date_from'])) {
//         $summary_sql .= " AND transaction_date >= :date_from";
//         $summary_params['date_from'] = $filters['date_from'];
//     }
//     if (!empty($filters['date_to'])) {
//         $summary_sql .= " AND transaction_date <= :date_to";
//         $summary_params['date_to'] = $filters['date_to'];
//     }
//     if (!empty($filters['category'])) {
//         $summary_sql .= " AND transaction_category = :category";
//         $summary_params['category'] = $filters['category'];
//     }
//     if (!empty($filters['payment_mode'])) {
//         $summary_sql .= " AND payment_mode = :payment_mode";
//         $summary_params['payment_mode'] = $filters['payment_mode'];
//     }

//     $summary_stmt = $pdo->prepare($summary_sql);
//     $summary_stmt->execute($summary_params);
//     $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);

//     // Get today's total
//     $today_sql = "SELECT COALESCE(SUM(amount), 0) as today_total 
//                   FROM tbl_debit_transactions 
//                   WHERE DATE(transaction_date) = CURDATE()";
//     $today_total = $pdo->query($today_sql)->fetchColumn();

//     // Get selected month's total (for display in summary)
//     $selected_month_total = 0;
//     if ($month) {
//         $month_sql = "SELECT COALESCE(SUM(amount), 0) as month_total 
//                       FROM tbl_debit_transactions 
//                       WHERE YEAR(transaction_date) = :year 
//                       AND MONTH(transaction_date) = :month";
//         $month_stmt = $pdo->prepare($month_sql);
//         $month_stmt->execute([
//             'year' => $year,
//             'month' => $month
//         ]);
//         $selected_month_total = $month_stmt->fetchColumn();
//     }

//     // Get category-wise summary for selected filters
//     $category_sql = "SELECT 
//                         transaction_category,
//                         COUNT(*) as count,
//                         SUM(amount) as total
//                      FROM tbl_debit_transactions 
//                      WHERE 1=1";
//     $category_params = [];
//     if (!empty($filters['date_from'])) {
//         $category_sql .= " AND transaction_date >= :date_from";
//         $category_params['date_from'] = $filters['date_from'];
//     }
//     if (!empty($filters['date_to'])) {
//         $category_sql .= " AND transaction_date <= :date_to";
//         $category_params['date_to'] = $filters['date_to'];
//     }
//     if (!empty($filters['category'])) {
//         $category_sql .= " AND transaction_category = :category";
//         $category_params['category'] = $filters['category'];
//     }
//     if (!empty($filters['payment_mode'])) {
//         $category_sql .= " AND payment_mode = :payment_mode";
//         $category_params['payment_mode'] = $filters['payment_mode'];
//     }
//     $category_sql .= " GROUP BY transaction_category ORDER BY total DESC";
//     $category_stmt = $pdo->prepare($category_sql);
//     $category_stmt->execute($category_params);
//     $category_summary = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (Exception $e) {
//     $error = "Error fetching data: " . $e->getMessage();
// }

// // Helper functions
// function formatCurrency($amount)
// {
//     return '₹' . number_format($amount, 2);
// }

// function getCategoryBadgeClass($category)
// {
//     $classes = [
//         'fuel' => 'category-fuel',
//         'farmer' => 'category-farmer',
//         'salesperson' => 'category-salesperson',
//         'general' => 'category-general',
//         'sales_expense' => 'category-sales_expense'
//     ];
//     return $classes[$category] ?? 'category-general';
// }

// function getPaymentBadgeClass($payment_mode)
// {
//     $classes = [
//         'Cash' => 'payment-cash',
//         'UPI' => 'payment-upi',
//         'Cheque' => 'payment-cheque',
//         'Bank Transfer' => 'payment-bank_transfer',
//         'Card' => 'payment-card'
//     ];
//     return $classes[$payment_mode] ?? 'payment-cash';
// }

// function formatDate($date)
// {
//     return date('d-m-Y', strtotime($date));
// }

// function getCategoryDetails($transaction)
// {
//     $details = [];
//     switch ($transaction['transaction_category']) {
//         case 'fuel':
//             if ($transaction['vehicle_info']) $details[] = "Vehicle: " . htmlspecialchars($transaction['vehicle_info']);
//             if ($transaction['driver_name']) $details[] = "Driver: " . htmlspecialchars($transaction['driver_name']);
//             if ($transaction['kilometers']) $details[] = "KM: " . htmlspecialchars($transaction['kilometers']);
//             break;
//         case 'farmer':
//             if ($transaction['farmer_name']) $details[] = "Farmer: " . htmlspecialchars($transaction['farmer_name']);
//             break;
//         case 'salesperson':
//             if ($transaction['salesperson_name']) $details[] = "Salesperson: " . htmlspecialchars($transaction['salesperson_name']);
//             if ($transaction['commission_type']) $details[] = "Type: " . htmlspecialchars($transaction['commission_type']);
//             if ($transaction['plot_commission']) $details[] = "Plot: " . htmlspecialchars($transaction['plot_commission']);
//             break;
//         case 'sales_expense':
//             if ($transaction['sales_expense_type']) $details[] = "Type: " . htmlspecialchars($transaction['sales_expense_type']);
//             break;
//         default:
//             return 'N/A';
//     }
//     return implode(', ', $details) ?: 'N/A';
// }

// function getPaymentDetails($transaction)
// {
//     $details = [];
//     if ($transaction['payment_mode'] === 'UPI') {
//         if ($transaction['transaction_id']) $details[] = "Transaction ID: " . htmlspecialchars($transaction['transaction_id']);
//     } elseif ($transaction['payment_mode'] === 'Cheque') {
//         if ($transaction['cheque_no']) $details[] = "Cheque No: " . htmlspecialchars($transaction['cheque_no']);
//         if ($transaction['bank_name']) $details[] = "Bank: " . htmlspecialchars($transaction['bank_name']);
//         if ($transaction['cheque_date']) $details[] = "Date: " . formatDate($transaction['cheque_date']);
//     } else {
//         return 'N/A';
//     }
//     return implode(', ', $details) ?: 'N/A';
// }

// function getMonthName($month_num)
// {
//     $months = [
//         1 => 'January',
//         2 => 'February',
//         3 => 'March',
//         4 => 'April',
//         5 => 'May',
//         6 => 'June',
//         7 => 'July',
//         8 => 'August',
//         9 => 'September',
//         10 => 'October',
//         11 => 'November',
//         12 => 'December'
//     ];
//     return $months[$month_num] ?? '';
// }



// Get filter parameters from GET request
$month = isset($_GET['month']) && $_GET['month'] !== '' ? $_GET['month'] : null;
$year = $_GET['year'] ?? date('Y'); // Default to current year
$category = $_GET['category'] ?? null;
$payment_mode = $_GET['payment_mode'] ?? null;

// Handle clear filters
if (isset($_GET['clear_filters'])) {
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Calculate date range based on month selection
$date_from = null;
$date_to = null;

// Set date range only if month is specifically selected
if ($month && !isset($_GET['clear_filters'])) {
    $date_from = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
    $date_to = date('Y-m-t', strtotime($date_from)); // Last day of the month
} elseif (!isset($_GET['month']) && !isset($_GET['clear_filters'])) {
    // Default to current month if no month parameter is present
    $date_from = date('Y-m-01'); // First day of current month
    $date_to = date('Y-m-t');    // Last day of current month
    $month = date('n'); // Set current month as selected
}

// Build the filters array
$filters = [];
if ($date_from && !isset($_GET['clear_filters'])) {
    $filters['date_from'] = $date_from;
}
if ($date_to && !isset($_GET['clear_filters'])) {
    $filters['date_to'] = $date_to;
}
if ($category && !isset($_GET['clear_filters'])) {
    $filters['category'] = $category;
}
if ($payment_mode && !isset($_GET['clear_filters'])) {
    $filters['payment_mode'] = $payment_mode;
}

// Add year filter when "All Months" is selected
if (!$month && $year && !isset($_GET['clear_filters'])) {
    $filters['year'] = $year;
}

try {
    // Get all transactions (no pagination)
    $sql = "SELECT 
                id,
                account_id,
                transaction_date,
                amount,
                authorized_by,
                payment_mode,
                transaction_id,
                cheque_no,
                bank_name,
                cheque_date,
                transaction_category,
                payee_name,
                description,
                expense_category,
                vehicle_info,
                driver_name,
                kilometers,
                farmer_name,
                salesperson_name,
                commission_type,
                plot_commission,
                sales_expense_type,
                created_at
            FROM tbl_debit_transactions 
            WHERE 1=1";

    $params = [];

    // Add filters to SQL only if they exist
    if (!empty($filters['date_from'])) {
        $sql .= " AND transaction_date >= :date_from";
        $params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " AND transaction_date <= :date_to";
        $params['date_to'] = $filters['date_to'];
    }
    if (!empty($filters['year'])) {
        $sql .= " AND YEAR(transaction_date) = :year";
        $params['year'] = $filters['year'];
    }
    if (!empty($filters['category'])) {
        $sql .= " AND transaction_category = :category";
        $params['category'] = $filters['category'];
    }
    if (!empty($filters['payment_mode'])) {
        $sql .= " AND payment_mode = :payment_mode";
        $params['payment_mode'] = $filters['payment_mode'];
    }

    $sql .= " ORDER BY transaction_date DESC, created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count
    $count_sql = "SELECT COUNT(*) FROM tbl_debit_transactions WHERE 1=1";
    $count_params = [];
    if (!empty($filters['date_from'])) {
        $count_sql .= " AND transaction_date >= :date_from";
        $count_params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $count_sql .= " AND transaction_date <= :date_to";
        $count_params['date_to'] = $filters['date_to'];
    }
    if (!empty($filters['year'])) {
        $count_sql .= " AND YEAR(transaction_date) = :year";
        $count_params['year'] = $filters['year'];
    }
    if (!empty($filters['category'])) {
        $count_sql .= " AND transaction_category = :category";
        $count_params['category'] = $filters['category'];
    }
    if (!empty($filters['payment_mode'])) {
        $count_sql .= " AND payment_mode = :payment_mode";
        $count_params['payment_mode'] = $filters['payment_mode'];
    }

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($count_params);
    $total_records = $count_stmt->fetchColumn();

    // Get summary data
    $summary_sql = "SELECT 
                        COUNT(*) as total_transactions,
                        COALESCE(SUM(amount), 0) as total_amount,
                        COALESCE(AVG(amount), 0) as average_amount
                    FROM tbl_debit_transactions WHERE 1=1";
    $summary_params = [];
    if (!empty($filters['date_from'])) {
        $summary_sql .= " AND transaction_date >= :date_from";
        $summary_params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $summary_sql .= " AND transaction_date <= :date_to";
        $summary_params['date_to'] = $filters['date_to'];
    }
    if (!empty($filters['year'])) {
        $summary_sql .= " AND YEAR(transaction_date) = :year";
        $summary_params['year'] = $filters['year'];
    }
    if (!empty($filters['category'])) {
        $summary_sql .= " AND transaction_category = :category";
        $summary_params['category'] = $filters['category'];
    }
    if (!empty($filters['payment_mode'])) {
        $summary_sql .= " AND payment_mode = :payment_mode";
        $summary_params['payment_mode'] = $filters['payment_mode'];
    }

    $summary_stmt = $pdo->prepare($summary_sql);
    $summary_stmt->execute($summary_params);
    $summary = $summary_stmt->fetch(PDO::FETCH_ASSOC);

    // Get today's total
    $today_sql = "SELECT COALESCE(SUM(amount), 0) as today_total 
                  FROM tbl_debit_transactions 
                  WHERE DATE(transaction_date) = CURDATE()";
    $today_total = $pdo->query($today_sql)->fetchColumn();

    // Get category-wise summary
    $category_sql = "SELECT 
                        transaction_category,
                        COUNT(*) as count,
                        SUM(amount) as total
                     FROM tbl_debit_transactions 
                     WHERE 1=1";
    $category_params = [];
    if (!empty($filters['date_from'])) {
        $category_sql .= " AND transaction_date >= :date_from";
        $category_params['date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $category_sql .= " AND transaction_date <= :date_to";
        $category_params['date_to'] = $filters['date_to'];
    }
    if (!empty($filters['year'])) {
        $category_sql .= " AND YEAR(transaction_date) = :year";
        $category_params['year'] = $filters['year'];
    }
    if (!empty($filters['category'])) {
        $category_sql .= " AND transaction_category = :category";
        $category_params['category'] = $filters['category'];
    }
    if (!empty($filters['payment_mode'])) {
        $category_sql .= " AND payment_mode = :payment_mode";
        $category_params['payment_mode'] = $filters['payment_mode'];
    }
    $category_sql .= " GROUP BY transaction_category ORDER BY total DESC";
    $category_stmt = $pdo->prepare($category_sql);
    $category_stmt->execute($category_params);
    $category_summary = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching data: " . $e->getMessage();
    $transactions = [];
    $summary = ['total_transactions' => 0, 'total_amount' => 0, 'average_amount' => 0];
    $category_summary = [];
    $today_total = 0;
}

// Helper functions (unchanged)
function formatCurrency($amount)
{
    return '₹' . number_format($amount, 2);
}

function getCategoryBadgeClass($category)
{
    $classes = [
        'fuel' => 'category-fuel',
        'farmer' => 'category-farmer',
        'salesperson' => 'category-salesperson',
        'general' => 'category-general',
        'sales_expense' => 'category-sales_expense'
    ];
    return $classes[$category] ?? 'category-general';
}

function getPaymentBadgeClass($payment_mode)
{
    $classes = [
        'Cash' => 'payment-cash',
        'UPI' => 'payment-upi',
        'Cheque' => 'payment-cheque',
        'Bank Transfer' => 'payment-bank_transfer',
        'Card' => 'payment-card'
    ];
    return $classes[$payment_mode] ?? 'payment-cash';
}

function formatDate($date)
{
    return date('d-m-Y', strtotime($date));
}

function getCategoryDetails($transaction)
{
    $details = [];
    switch ($transaction['transaction_category']) {
        case 'fuel':
            if (!empty($transaction['vehicle_info'])) $details[] = "Vehicle: " . htmlspecialchars($transaction['vehicle_info']);
            if (!empty($transaction['driver_name'])) $details[] = "Driver: " . htmlspecialchars($transaction['driver_name']);
            if (!empty($transaction['kilometers'])) $details[] = "KM: " . htmlspecialchars($transaction['kilometers']);
            break;
        case 'farmer':
            if (!empty($transaction['farmer_name'])) $details[] = "Farmer: " . htmlspecialchars($transaction['farmer_name']);
            break;
        case 'salesperson':
            if (!empty($transaction['salesperson_name'])) $details[] = "Salesperson: " . htmlspecialchars($transaction['salesperson_name']);
            if (!empty($transaction['commission_type'])) $details[] = "Type: " . htmlspecialchars($transaction['commission_type']);
            if (!empty($transaction['plot_commission'])) $details[] = "Plot: " . htmlspecialchars($transaction['plot_commission']);
            break;
        case 'sales_expense':
            if (!empty($transaction['sales_expense_type'])) $details[] = "Type: " . htmlspecialchars($transaction['sales_expense_type']);
            break;
        default:
            return 'N/A';
    }
    return implode(', ', $details) ?: 'N/A';
}

function getPaymentDetails($transaction)
{
    $details = [];
    if ($transaction['payment_mode'] === 'UPI' && !empty($transaction['transaction_id'])) {
        $details[] = "Transaction ID: " . htmlspecialchars($transaction['transaction_id']);
    } elseif ($transaction['payment_mode'] === 'Cheque') {
        if (!empty($transaction['cheque_no'])) $details[] = "Cheque No: " . htmlspecialchars($transaction['cheque_no']);
        if (!empty($transaction['bank_name'])) $details[] = "Bank: " . htmlspecialchars($transaction['bank_name']);
        if (!empty($transaction['cheque_date'])) $details[] = "Date: " . formatDate($transaction['cheque_date']);
    }
    return implode(', ', $details) ?: 'N/A';
}

function getMonthName($month_num)
{
    $months = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];
    return $months[$month_num] ?? '';
}
?>

<!-- Debug Information (Remove in production) -->
<?php if (isset($_GET['debug'])): ?>
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        Year: <?php echo $year; ?><br>
        Month: <?php echo $month ? $month : 'All Months'; ?><br>
        Category: <?php echo $category ? $category : 'All Categories'; ?><br>
        Payment Mode: <?php echo $payment_mode ? $payment_mode : 'All Modes'; ?><br>
        Date From: <?php echo $date_from ? $date_from : 'Not set'; ?><br>
        Date To: <?php echo $date_to ? $date_to : 'Not set'; ?><br>
        Active Filters: <?php echo json_encode($filters); ?><br>
        Total Records: <?php echo $total_records; ?>
    </div>
<?php endif; ?>





<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0">
    <title>
        Hari Home Developers | Accountant Panel
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


    <style>
        .category-fuel {
            background-color: #ffcccb;
        }

        .category-farmer {
            background-color: #d4edda;
        }

        .category-salesperson {
            background-color: #cce5ff;
        }

        .category-general {
            background-color: #f8f9fa;
        }

        .category-sales_expense {
            background-color: #fff3cd;
        }

        .payment-cash {
            background-color: #d4edda;
        }

        .payment-upi {
            background-color: #cce5ff;
        }

        .payment-cheque {
            background-color: #fff3cd;
        }

        .payment-bank_transfer {
            background-color: #f8d7da;
        }

        .payment-card {
            background-color: #e2e3e5;
        }

        .category-badge,
        .payment-mode-badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.9em;
        }

        .summary-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .summary-card .amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #dc3545;
        }

        .no-data {
            text-align: center;
            padding: 50px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .table {
                font-size: 10px;
            }

            .table td,
            .table th {
                padding: 4px;
            }
        }
    </style>

    <style>
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }

        .summary-card h5 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .summary-card .amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
        }

        .transaction-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table thead th {
            background: #667eea;
            color: white;
            border: none;
            font-weight: 600;
        }

        .category-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .category-fuel {
            background-color: #ffc107;
            color: #000;
        }

        .category-farmer {
            background-color: #28a745;
            color: white;
        }

        .category-salesperson {
            background-color: #17a2b8;
            color: white;
        }

        .category-general {
            background-color: #6c757d;
            color: white;
        }

        .category-sales_expense {
            background-color: #fd7e14;
            color: white;
        }

        .payment-mode-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .payment-cash {
            background-color: #198754;
            color: white;
        }

        .payment-upi {
            background-color: #0d6efd;
            color: white;
        }

        .payment-cheque {
            background-color: #6f42c1;
            color: white;
        }

        .payment-bank_transfer {
            background-color: #20c997;
            color: white;
        }

        .payment-card {
            background-color: #fd7e14;
            color: white;
        }
    </style>

</head>

<body>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>



    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <?php include "account-headersidepanel.php"; ?>

        <div class="main-panel">

            <div class="row" style="">
                <div class="col-md-12">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="report-header">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2><i class="fas fa-chart-line me-3"></i>Director's Debit Transactions Report </h2>
                                </div>
                                <!-- <div class="col-md-4 text-end no-print">
                                    <button class="btn btn-light btn-lg" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Print Report
                                    </button>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <!-- Filters Section -->
                        <div class="filter-section no-print">
                            <h5><i class="fas fa-filter me-2"></i>Filter Transactions</h5>
                            <form method="GET" id="filterForm">
                                <div class="row">
                                    <!-- Year Selection -->
                                    <div class="col-md-2">
                                        <label for="year" class="form-label">Year</label>
                                        <select class="form-control" name="year" id="year">
                                            <?php
                                            $current_year = date('Y');
                                            for ($y = $current_year; $y >= $current_year - 5; $y--): ?>
                                                <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>>
                                                    <?php echo $y; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <!-- Month Selection -->
                                    <div class="col-md-2">
                                        <label for="month" class="form-label">Month</label>
                                        <select class="form-control" name="month" id="month">
                                            <option value="">All Months</option>
                                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                                <option value="<?php echo $m; ?>" <?php echo $month == $m ? 'selected' : ''; ?>>
                                                    <?php echo getMonthName($m); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <!-- Category Selection -->
                                    <div class="col-md-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control" name="category" id="category">
                                            <option value="">All Categories</option>
                                            <option value="fuel" <?php echo $category === 'fuel' ? 'selected' : ''; ?>>Fuel</option>
                                            <option value="farmer" <?php echo $category === 'farmer' ? 'selected' : ''; ?>>Farmer</option>
                                            <option value="salesperson" <?php echo $category === 'salesperson' ? 'selected' : ''; ?>>Salesperson</option>
                                            <option value="general" <?php echo $category === 'general' ? 'selected' : ''; ?>>General</option>
                                            <option value="sales_expense" <?php echo $category === 'sales_expense' ? 'selected' : ''; ?>>Sales Expense</option>
                                        </select>
                                    </div>

                                    <!-- Payment Mode Selection -->
                                    <div class="col-md-3">
                                        <label for="payment_mode" class="form-label">Payment Mode</label>
                                        <select class="form-control" name="payment_mode" id="payment_mode">
                                            <option value="">All Modes</option>
                                            <option value="Cash" <?php echo $payment_mode === 'Cash' ? 'selected' : ''; ?>>Cash</option>
                                            <option value="UPI" <?php echo $payment_mode === 'UPI' ? 'selected' : ''; ?>>UPI</option>
                                            <option value="Cheque" <?php echo $payment_mode === 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                                            <option value="Bank Transfer" <?php echo $payment_mode === 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                            <option value="Card" <?php echo $payment_mode === 'Card' ? 'selected' : ''; ?>>Card</option>
                                        </select>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="col-md-2 d-flex align-items-end">
                                        <div class="btn-group w-100">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-1"></i>Apply
                                            </button>
                                            <a href="?clear_filters=1" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i>Clear
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter Status Display -->
                                <?php if ($month || $category || $payment_mode): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-info d-flex align-items-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <span>
                                                    <strong>Active Filters:</strong>
                                                    <?php if ($month): ?>
                                                        <span class="badge bg-primary me-1 text-white"><?php echo getMonthName($month) . ' ' . $year; ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($category): ?>
                                                        <span class="badge bg-success me-1 text-white"><?php echo ucfirst(str_replace('_', ' ', $category)); ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($payment_mode): ?>
                                                        <span class="badge bg-warning me-1"><?php echo $payment_mode; ?></span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- Quick Month Navigation -->
                        <div class="quick-nav no-print mb-3">
                            <div class="btn-group" role="group">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <a href="?month=<?php echo $m; ?>&year=<?php echo $year; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $payment_mode ? '&payment_mode=' . $payment_mode : ''; ?>"
                                        class="btn btn-sm <?php echo $month == $m ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        <?php echo substr(getMonthName($m), 0, 3); ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        </div>


                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <h5><i class="fas fa-calculator me-2"></i>Total Transactions</h5>
                                    <div class="amount"><?php echo number_format($summary['total_transactions'] ?? 0); ?></div>
                                    <?php if ($month): ?>
                                        <small class="text-muted">For <?php echo getMonthName($month) . ' ' . $year; ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <h5><i class="fas fa-money-bill-wave me-2"></i>Total Amount</h5>
                                    <div class="amount"><?php echo formatCurrency($summary['total_amount'] ?? 0); ?></div>
                                    <?php if ($month): ?>
                                        <small class="text-muted">For <?php echo getMonthName($month) . ' ' . $year; ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <h5><i class="fas fa-chart-bar me-2"></i>Average Amount</h5>
                                    <div class="amount"><?php echo formatCurrency($summary['average_amount'] ?? 0); ?></div>
                                    <?php if ($month): ?>
                                        <small class="text-muted">For <?php echo getMonthName($month) . ' ' . $year; ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <h5><i class="fas fa-calendar me-2"></i>Today's Total</h5>
                                    <div class="amount"><?php echo formatCurrency($today_total ?? 0); ?></div>
                                    <small class="text-muted">Today: <?php echo date('d-m-Y'); ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Category Summary -->
                        <?php if (!empty($category_summary)): ?>
                            <div class="category-summary">
                                <h5><i class="fas fa-chart-pie me-2"></i>Category-wise Summary</h5>
                                <div class="row">
                                    <?php foreach ($category_summary as $cat): ?>
                                        <div class="col-md-2 text-center mb-3">
                                            <div class="border rounded p-2">
                                                <div class="fw-bold text-capitalize"><?php echo str_replace('_', ' ', $cat['transaction_category']); ?></div>
                                                <div class="text-muted small"><?php echo $cat['count']; ?> transactions</div>
                                                <div class="text-danger fw-bold"><?php echo formatCurrency($cat['total']); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <style>
                            /* Normal UI styles */
                            .dataTables_wrapper .dataTables_filter {
                                display: block !important;
                                visibility: visible !important;
                                margin-bottom: 10px;
                            }

                            .dataTables_wrapper .dataTables_filter input {
                                border: 1px solid #ccc;
                                padding: 5px;
                                font-size: 12px;
                                width: 200px;
                            }

                            /* Print styles */
                            @media print {
                                * {
                                    visibility: hidden;
                                }

                                .table-responsive,
                                .table-responsive * {
                                    visibility: visible;
                                }

                                /* Hide DataTable elements (search, pagination, info, etc.) */
                                .dataTables_wrapper .dataTables_filter,
                                .dataTables_wrapper .dataTables_info,
                                .dataTables_wrapper .dataTables_paginate,
                                .dataTables_wrapper .dataTables_length {
                                    display: none !important;
                                    visibility: hidden !important;
                                }

                                /* Hide unwanted elements */
                                .no-data,
                                .dropdown-toggle,
                                .fas.fa-inbox,
                                .mb-3,
                                h5,
                                p {
                                    display: none !important;
                                    visibility: hidden !important;
                                }

                                .table-responsive {
                                    position: absolute;
                                    left: 0;
                                    top: 0;
                                    width: 100%;
                                    height: auto !important;
                                    overflow: visible !important;
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    max-width: none !important;
                                    /* Ensure no width constraints */
                                }

                                body {
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    font-size: 10pt !important;
                                    color: black !important;
                                    background: white !important;
                                }

                                .table {
                                    border-collapse: collapse !important;
                                    width: 100% !important;
                                    max-width: none !important;
                                    /* Prevent any width capping */
                                    margin: 0 !important;
                                    font-size: 9pt !important;
                                    table-layout: fixed !important;
                                }

                                .table th,
                                .table td {
                                    border: 1px solid #000 !important;
                                    padding: 4px 2px !important;
                                    /* Reduced padding slightly to maximize space */
                                    font-size: 9pt !important;
                                    word-wrap: break-word !important;
                                    overflow-wrap: break-word !important;
                                    hyphens: auto !important;
                                    word-break: break-all !important;
                                    white-space: normal !important;
                                    color: #000 !important;
                                    text-align: left !important;
                                    box-sizing: border-box !important;
                                }

                                .table th {
                                    background-color: #d0d0d0 !important;
                                    font-weight: bold !important;
                                    -webkit-print-color-adjust: exact !important;
                                    print-color-adjust: exact !important;
                                    text-align: center !important;
                                }

                                /* Adjusted column widths to fill the page fully */
                                .table th:nth-child(1),
                                .table td:nth-child(1) {
                                    width: 10% !important;
                                    text-align: center !important;
                                }

                                /* category */
                                .table th:nth-child(2),
                                .table td:nth-child(2) {
                                    width: 8% !important;
                                    text-align: center !important;

                                }


                                .table td:nth-child(2) {
                                    background-color: transparent !important;
                                    width: 9% !important;
                                }

                                /* To payee */
                                .table th:nth-child(3),
                                .table td:nth-child(3) {
                                    width: 12% !important;
                                    text-align: right;
                                }

                                /* Description */
                                .table th:nth-child(4),
                                .table td:nth-child(4) {
                                    width: 14% !important;
                                    line-height: 1.2 !important;
                                }

                                /* Amount */
                                .table th:nth-child(5),
                                .table td:nth-child(5) {
                                    width: 10% !important;
                                    text-align: center !important;
                                }

                                /* Payment Mode */
                                .table th:nth-child(6),
                                .table td:nth-child(6) {
                                    width: 7% !important;
                                    text-align: center !important;
                                }

                                /* Authorized By */
                                .table th:nth-child(7),
                                .table td:nth-child(7) {
                                    width: 8% !important;
                                }

                                /* Details */
                                .table th:nth-child(8),
                                .table td:nth-child(8) {
                                    width: 17% !important;
                                    line-height: 1.2 !important;
                                }

                                /* Pay Details */
                                .table th:nth-child(9),
                                .table td:nth-child(9) {
                                    width: 22% !important;
                                    line-height: 1.2 !important;
                                }

                                .table th:nth-child(10),
                                .table td:nth-child(10) {
                                    display: none !important;
                                }

                                /* Payment Details */

                                /* Badges */
                                .category-badge,
                                .payment-mode-badge {
                                    border: 1px transparent #000 !important;
                                    padding: 2px 3px !important;
                                    border-radius: 2px !important;
                                    background: #e0e0e0 !important;
                                    color: #000 !important;
                                    font-size: 8pt !important;
                                    display: inline-block !important;
                                    font-weight: bold !important;
                                    word-break: break-all !important;
                                    white-space: normal !important;
                                }

                                /* Strong tags */
                                strong {
                                    color: #000 !important;
                                    font-weight: bold !important;
                                }

                                @page {
                                    margin: 0mm 1.5mm 0mm 1.5mm;
                                    size: A4 portrait !important;

                                }

                                /* Pagination rules */
                                .table {
                                    page-break-before: auto !important;
                                    page-break-after: auto !important;
                                }

                                .table tr {
                                    page-break-inside: avoid !important;
                                    page-break-before: auto !important;
                                    page-break-after: auto !important;
                                }

                                .table thead {
                                    display: table-header-group !important;
                                }

                                .table tbody {
                                    display: table-row-group !important;
                                }
                            }
                        </style>

                        <!-- Print Header (only visible when printing) -->
                        <div class="print-header d-none d-print-block">
                            <h2>Transaction Report</h2>
                            <p>Generated on: <span id="print-date"></span></p>
                            <?php if ($month && $category): ?>
                                <p>Category: <?php echo ucfirst(str_replace('_', ' ', $category)); ?> | Month: <?php echo getMonthName($month) . ' ' . $year; ?></p>
                            <?php elseif ($month): ?>
                                <p>Month: <?php echo getMonthName($month) . ' ' . $year; ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Print Button -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" onclick="printTable()">
                                <i class="fas fa-print me-2"></i>Print Table
                            </button>
                        </div>

                        <!-- Transactions Table -->
                        <!-- <div class="table-responsive">
                            <table class="table table-hover mb-0" id="debitlist">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>To/Payee</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Authorized By</th>
                                        <th>Details</th>
                                        <th>Payment Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transactions)): ?>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo formatDate($transaction['transaction_date']); ?></td>
                                                <td><span class="category-badge <?php echo getCategoryBadgeClass($transaction['transaction_category']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_category'])); ?>
                                                    </span></td>
                                                <td><?php echo htmlspecialchars($transaction['payee_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?></td>
                                                <td><strong><?php echo formatCurrency($transaction['amount']); ?></strong></td>
                                                <td><span class="payment-mode-badge <?php echo getPaymentBadgeClass($transaction['payment_mode']); ?>">
                                                        <?php echo htmlspecialchars($transaction['payment_mode']); ?>
                                                    </span></td>
                                                <td><?php echo htmlspecialchars($transaction['authorized_by'] ?? 'N/A'); ?></td>
                                                <td><?php echo getCategoryDetails($transaction); ?></td>
                                                <td><?php echo getPaymentDetails($transaction); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="no-data">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h5>No transactions found</h5>
                                                <p>
                                                    <?php if ($month && $category): ?>
                                                        No <?php echo str_replace('_', ' ', $category); ?> transactions found for <?php echo getMonthName($month) . ' ' . $year; ?>
                                                    <?php elseif ($month): ?>
                                                        No transactions found for <?php echo getMonthName($month) . ' ' . $year; ?>
                                                    <?php else: ?>
                                                        Try adjusting your filters.
                                                    <?php endif; ?>
                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div> -->


                        <!-- Transactions Table -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="debitlist">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>To/Payee</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Payment Mode</th>
                                        <th>Authorized By</th>
                                        <th>Details</th>
                                        <th>Payment Details</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transactions)): ?>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo formatDate($transaction['transaction_date']); ?></td>
                                                <td><span class="category-badge <?php echo getCategoryBadgeClass($transaction['transaction_category']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_category'])); ?>
                                                    </span></td>
                                                <td><?php echo htmlspecialchars($transaction['payee_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?></td>
                                                <td><strong><?php echo formatCurrency($transaction['amount']); ?></strong></td>
                                                <td><span class="payment-mode-badge <?php echo getPaymentBadgeClass($transaction['payment_mode']); ?>">
                                                        <?php echo htmlspecialchars($transaction['payment_mode']); ?>
                                                    </span></td>
                                                <td><?php echo htmlspecialchars($transaction['authorized_by'] ?? 'N/A'); ?></td>
                                                <td><?php echo getCategoryDetails($transaction); ?></td>
                                                <td><?php echo getPaymentDetails($transaction); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary edit-transaction"
                                                        data-id="<?php echo $transaction['id']; ?>"
                                                        data-toggle="modal"
                                                        data-target="#editTransactionModal">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="no-data">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h5>No transactions found</h5>
                                                <p>
                                                    <?php if ($month && $category): ?>
                                                        No <?php echo str_replace('_', ' ', $category); ?> transactions found for <?php echo getMonthName($month) . ' ' . $year; ?>
                                                    <?php elseif ($month): ?>
                                                        No transactions found for <?php echo getMonthName($month) . ' ' . $year; ?>
                                                    <?php else: ?>
                                                        Try adjusting your filters.
                                                    <?php endif; ?>
                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Edit Transaction Modal -->
                        <div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <form id="editTransactionForm" method="POST" action="update_transaction.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Transaction</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" id="transaction_id">

                                            <div class="form-group">
                                                <label for="transaction_date">Date</label>
                                                <input type="date" class="form-control" name="transaction_date" id="transaction_date" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="amount">Amount</label>
                                                <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="payee_name">Payee Name</label>
                                                <input type="text" class="form-control" name="payee_name" id="payee_name">
                                            </div>

                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea class="form-control" name="description" id="description"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="payment_mode">Payment Mode</label>
                                                <select class="form-control" name="payment_mode" id="payment_mode">
                                                    <option value="Cash">Cash</option>
                                                    <option value="UPI">UPI</option>
                                                    <option value="Cheque">Cheque</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="authorized_by">Authorized By</label>
                                                <input type="text" class="form-control" name="authorized_by" id="authorized_by">
                                            </div>

                                            <div class="form-group">
                                                <label for="transaction_category">Category</label>
                                                <select class="form-control" name="transaction_category" id="transaction_category">
                                                    <option value="farmer">Farmer</option>
                                                    <option value="expense">Expense</option>
                                                    <option value="commission">Commission</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <script>
                            function printTable() {
                                // Trigger print directly - only table will be visible
                                window.print();
                            }

                            // Optional: Add keyboard shortcut for printing (Ctrl+P)
                            document.addEventListener('keydown', function(e) {
                                if (e.ctrlKey && e.key === 'p') {
                                    e.preventDefault();
                                    printTable();
                                }
                            });
                        </script>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <!-- <nav aria-label="Pagination" class="mt-4 no-print">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav> -->
                        <?php endif; ?>
                    </div>

                    <!-- //end here -->

                    <div class="mt-5">
                        <?php include "account-footer.php"; ?>
                    </div>
                </div>

            </div>
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


    <script>
        $(document).ready(function() {
            $('#debitlist').DataTable({
                "ordering": false, // Disable column sorting
                "paging": true, // Enable pagination in UI
                "searching": true, // Explicitly enable search
                "info": true, // Show table info
                "dom": 'lfrtip', // Layout: l=length, f=filter (search), r=processing, t=table, i=info, p=paging
                "pageLength": 10, // Default rows per page
                "language": {
                    "emptyTable": "No transactions available",
                    "search": "Search Transactions:" // Customize search placeholder
                }
            });

            $('.dropdown-toggle').dropdown();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.edit-transaction').click(function() {
                const id = $(this).data('id');

                // AJAX call to get transaction details
                $.ajax({
                    url: 'get_transaction.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        $('#transaction_id').val(data.id);
                        $('#transaction_date').val(data.transaction_date);
                        $('#amount').val(data.amount);
                        $('#payee_name').val(data.payee_name);
                        $('#description').val(data.description);
                        $('#payment_mode').val(data.payment_mode);
                        $('#authorized_by').val(data.authorized_by);
                        $('#transaction_category').val(data.transaction_category);
                    }
                });
            });
        });
    </script>


    <style>
        i {
            color: yellow;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#producttable').DataTable({

            });
        });
    </script>


</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration>

</html>