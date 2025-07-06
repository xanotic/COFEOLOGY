<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not staff
if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$error = '';

// First, let's check what columns exist in the CUSTOMER table
$customer_columns = [];
$result = $conn->query("DESCRIBE CUSTOMER");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customer_columns[] = $row['Field'];
    }
}

// Helper function to safely get customer data
function getCustomerData($order, $field_type) {
    switch ($field_type) {
        case 'name':
            return $order['CUST_NAME'] ?? $order['CUSTOMER_NAME'] ?? 'Guest';
        case 'email':
            return $order['CUST_EMAIL'] ?? $order['CUSTOMER_EMAIL'] ?? 'N/A';
        case 'phone':
            return $order['CUST_NPHONE'] ?? $order['CUST_PHONE'] ?? $order['CUSTOMER_PHONE'] ?? 'N/A';
        case 'address':
            return $order['CUST_ADDRESS'] ?? $order['CUST_ADD'] ?? $order['CUSTOMER_ADDRESS'] ?? 'N/A';
        case 'membership':
            return $order['MEMBERSHIP_TYPE'] ?? $order['CUST_MEMBERSHIP'] ?? 'Basic';
        default:
            return 'N/A';
    }
}

// Handle order status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE `ORDER` SET ORDER_STATUS = ? WHERE ORDER_ID = ?");
        if ($stmt) {
            $stmt->bind_param("si", $new_status, $order_id);
            if ($stmt->execute()) {
                $message = "Order status updated successfully!";
            } else {
                $error = "Failed to update order status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

// Build dynamic query based on available columns
$customer_select = "c.CUST_ID";
if (in_array('CUST_NAME', $customer_columns)) $customer_select .= ", c.CUST_NAME";
if (in_array('CUSTOMER_NAME', $customer_columns)) $customer_select .= ", c.CUSTOMER_NAME";
if (in_array('CUST_EMAIL', $customer_columns)) $customer_select .= ", c.CUST_EMAIL";
if (in_array('CUSTOMER_EMAIL', $customer_columns)) $customer_select .= ", c.CUSTOMER_EMAIL";
if (in_array('CUST_NPHONE', $customer_columns)) $customer_select .= ", c.CUST_NPHONE";
if (in_array('CUST_PHONE', $customer_columns)) $customer_select .= ", c.CUST_PHONE";
if (in_array('CUSTOMER_PHONE', $customer_columns)) $customer_select .= ", c.CUSTOMER_PHONE";
if (in_array('CUST_ADDRESS', $customer_columns)) $customer_select .= ", c.CUST_ADDRESS";
if (in_array('CUST_ADD', $customer_columns)) $customer_select .= ", c.CUST_ADD";
if (in_array('CUSTOMER_ADDRESS', $customer_columns)) $customer_select .= ", c.CUSTOMER_ADDRESS";
if (in_array('MEMBERSHIP_TYPE', $customer_columns)) $customer_select .= ", c.MEMBERSHIP_TYPE";
if (in_array('CUST_MEMBERSHIP', $customer_columns)) $customer_select .= ", c.CUST_MEMBERSHIP";

// Build the query with filters
$query = "SELECT o.*, $customer_select 
          FROM `ORDER` o 
          LEFT JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID 
          WHERE 1=1";

$params = [];
$types = "";

if ($status_filter) {
    $query .= " AND o.ORDER_STATUS = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($type_filter) {
    $query .= " AND o.ORDER_TYPE = ?";
    $params[] = $type_filter;
    $types .= "s";
}

if ($search) {
    // Use dynamic search based on available columns
    $search_conditions = [];
    if (in_array('CUST_NAME', $customer_columns)) $search_conditions[] = "c.CUST_NAME LIKE ?";
    if (in_array('CUSTOMER_NAME', $customer_columns)) $search_conditions[] = "c.CUSTOMER_NAME LIKE ?";
    $search_conditions[] = "o.ORDER_ID LIKE ?";
    
    if (!empty($search_conditions)) {
        $query .= " AND (" . implode(" OR ", $search_conditions) . ")";
        foreach ($search_conditions as $condition) {
            $params[] = "%$search%";
            $types .= "s";
        }
    }
}

$query .= " ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC";

$stmt = $conn->prepare($query);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $orders = [];
    $error = "Database query error: " . $conn->error;
}

// Get order statistics
$stats_query = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN ORDER_STATUS = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN ORDER_STATUS = 'preparing' THEN 1 ELSE 0 END) as preparing_orders,
    SUM(CASE WHEN ORDER_STATUS = 'ready' THEN 1 ELSE 0 END) as ready_orders,
    SUM(CASE WHEN ORDER_STATUS = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN ORDER_DATE = CURDATE() THEN TOT_AMOUNT ELSE 0 END) as today_revenue
    FROM `ORDER`";

$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : [
    'total_orders' => 0,
    'pending_orders' => 0,
    'preparing_orders' => 0,
    'ready_orders' => 0,
    'completed_orders' => 0,
    'today_revenue' => 0
];

// Generate random transaction reference
function generateTransactionRef($order_id, $date) {
    return 'TXN' . date('Ymd', strtotime($date)) . str_pad($order_id, 4, '0', STR_PAD_LEFT) . rand(100, 999);
}

// Generate random Malaysian bank
function getRandomBank() {
    $banks = ['Maybank', 'CIMB Bank', 'Public Bank', 'RHB Bank', 'Hong Leong Bank', 'AmBank', 'Bank Islam', 'OCBC Bank'];
    return $banks[array_rand($banks)];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Cofeology Staff</title>
    <link rel="stylesheet" href="../css/style.css?v=1.1">
    <link rel="stylesheet" href="../css/dashboard.css?v=1.1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Orders Management</h1>
                    <p>Manage and track customer orders</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="refreshOrders()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-secondary" onclick="printOrders()">
                        <i class="fas fa-print"></i> Print Orders
                    </button>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            
            <!-- Order Statistics -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <p><?php echo number_format($stats['total_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <p><?php echo number_format($stats['pending_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Preparing</h3>
                        <p><?php echo number_format($stats['preparing_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Ready</h3>
                        <p><?php echo number_format($stats['ready_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Today's Revenue</h3>
                        <p>RM<?php echo number_format($stats['today_revenue'], 2); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Orders</h2>
                    <div class="card-filters">
                        <form method="GET" class="filter-form">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="preparing" <?php echo $status_filter === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                <option value="ready" <?php echo $status_filter === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="dine-in" <?php echo $type_filter === 'dine-in' ? 'selected' : ''; ?>>Dine In</option>
                                <option value="takeaway" <?php echo $type_filter === 'takeaway' ? 'selected' : ''; ?>>Takeaway</option>
                                <option value="delivery" <?php echo $type_filter === 'delivery' ? 'selected' : ''; ?>>Delivery</option>
                            </select>
                            <input type="text" name="search" class="form-control" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="orders.php" class="btn btn-secondary">Clear</a>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="orders-grid">
                        <?php if (empty($orders)): ?>
                            <div class="no-orders">
                                <p>No orders found matching your criteria.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card" data-status="<?php echo htmlspecialchars($order['ORDER_STATUS']); ?>">
                                    <div class="order-header">
                                        <div class="order-id">
                                            <strong>#<?php echo htmlspecialchars($order['ORDER_ID']); ?></strong>
                                        </div>
                                        <div class="order-status">
                                            <span class="status-badge status-<?php echo htmlspecialchars($order['ORDER_STATUS']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($order['ORDER_STATUS'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Customer Information Section -->
                                    <div class="order-section">
                                        <h4><i class="fas fa-user"></i> Customer Information</h4>
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <span class="label">User ID:</span>
                                                <span class="value"><?php echo htmlspecialchars($order['CUST_ID'] ?? 'N/A'); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Name:</span>
                                                <span class="value"><?php echo htmlspecialchars(getCustomerData($order, 'name')); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Phone:</span>
                                                <span class="value">
                                                    <?php 
                                                    $phone = getCustomerData($order, 'phone');
                                                    if ($phone !== 'N/A'): 
                                                    ?>
                                                        <a href="tel:<?php echo htmlspecialchars($phone); ?>"><?php echo htmlspecialchars($phone); ?></a>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Email:</span>
                                                <span class="value"><?php echo htmlspecialchars(getCustomerData($order, 'email')); ?></span>
                                            </div>
                                            <?php if (($order['ORDER_TYPE'] ?? '') === 'delivery'): ?>
                                            <div class="info-item full-width">
                                                <span class="label">Address:</span>
                                                <span class="value"><?php echo htmlspecialchars(getCustomerData($order, 'address')); ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="info-item">
                                                <span class="label">Membership:</span>
                                                <span class="membership-badge membership-<?php echo strtolower(getCustomerData($order, 'membership')); ?>">
                                                    <?php echo htmlspecialchars(getCustomerData($order, 'membership')); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Details Section -->
                                    <div class="order-section">
                                        <h4><i class="fas fa-shopping-bag"></i> Order Details</h4>
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <span class="label">Order Type:</span>
                                                <span class="value">
                                                    <i class="fas fa-<?php echo ($order['ORDER_TYPE'] ?? '') === 'dine-in' ? 'utensils' : (($order['ORDER_TYPE'] ?? '') === 'takeaway' ? 'shopping-bag' : 'truck'); ?>"></i>
                                                    <?php echo ucfirst(str_replace('-', ' ', htmlspecialchars($order['ORDER_TYPE'] ?? 'N/A'))); ?>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Date:</span>
                                                <span class="value"><?php echo date('M j, Y', strtotime($order['ORDER_DATE'] ?? 'now')); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Time:</span>
                                                <span class="value"><?php echo date('g:i A', strtotime($order['ORDER_TIME'] ?? 'now')); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Status:</span>
                                                <span class="status-badge status-<?php echo htmlspecialchars($order['ORDER_STATUS'] ?? 'pending'); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($order['ORDER_STATUS'] ?? 'Pending')); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Information Section -->
                                    <div class="order-section">
                                        <h4><i class="fas fa-credit-card"></i> Payment Information</h4>
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <span class="label">Transaction Ref:</span>
                                                <span class="value transaction-ref"><?php echo generateTransactionRef($order['ORDER_ID'], $order['ORDER_DATE'] ?? date('Y-m-d')); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Amount:</span>
                                                <span class="value amount">RM<?php echo number_format($order['TOT_AMOUNT'] ?? 0, 2); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Payment Method:</span>
                                                <span class="value">
                                                    <i class="fas fa-<?php echo (($order['PAYMENT_METHOD'] ?? 'cash') === 'card') ? 'credit-card' : 'money-bill-wave'; ?>"></i>
                                                    <?php echo ucfirst(htmlspecialchars($order['PAYMENT_METHOD'] ?? 'Cash')); ?>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="label">Bank:</span>
                                                <span class="value"><?php echo getRandomBank(); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="order-actions">
                                        <?php if (($order['ORDER_STATUS'] ?? '') !== 'completed' && ($order['ORDER_STATUS'] ?? '') !== 'cancelled'): ?>
                                        <div class="status-update">
                                            <form method="post" class="status-form">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['ORDER_ID']); ?>">
                                                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo ($order['ORDER_STATUS'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="preparing" <?php echo ($order['ORDER_STATUS'] ?? '') === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                    <option value="ready" <?php echo ($order['ORDER_STATUS'] ?? '') === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                                    <option value="completed" <?php echo ($order['ORDER_STATUS'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo ($order['ORDER_STATUS'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-info" onclick="generateEmail('<?php echo htmlspecialchars($order['ORDER_ID']); ?>', '<?php echo htmlspecialchars(getCustomerData($order, 'name')); ?>', '<?php echo htmlspecialchars(getCustomerData($order, 'email')); ?>')">
                                            <i class="fas fa-envelope"></i> Generate Email
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Email Modal -->
    <div class="modal" id="email-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Generate Customer Email</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="email-form">
                    <div class="form-group">
                        <label>To:</label>
                        <input type="email" id="email-to" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Subject:</label>
                        <input type="text" id="email-subject" class="form-control" value="Order Update - Cofeology">
                    </div>
                    <div class="form-group">
                        <label>Message:</label>
                        <textarea id="email-message" class="form-control" rows="6"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('email-modal')">Close</button>
                        <button type="button" class="btn btn-primary" onclick="copyEmail()">Copy to Clipboard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-form .form-control {
            min-width: 120px;
        }
        
        .orders-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }
        
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #ddd;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .order-card[data-status="pending"] { border-left-color: #ffc107; }
        .order-card[data-status="preparing"] { border-left-color: #fd7e14; }
        .order-card[data-status="ready"] { border-left-color: #20c997; }
        .order-card[data-status="completed"] { border-left-color: #28a745; }
        .order-card[data-status="cancelled"] { border-left-color: #dc3545; }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .order-id {
            font-size: 1.3rem;
            color: #2d3436;
            font-weight: bold;
        }
        
        .order-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .order-section h4 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .info-item.full-width {
            grid-column: 1 / -1;
        }
        
        .info-item .label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .info-item .value {
            font-size: 0.9rem;
            color: #212529;
            font-weight: 500;
        }
        
        .info-item .value a {
            color: #007bff;
            text-decoration: none;
        }
        
        .info-item .value a:hover {
            text-decoration: underline;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-pending { background-color: rgba(255, 193, 7, 0.2); color: #856404; }
        .status-preparing { background-color: rgba(253, 126, 20, 0.2); color: #8a4a00; }
        .status-ready { background-color: rgba(32, 201, 151, 0.2); color: #0f5132; }
        .status-completed { background-color: rgba(40, 167, 69, 0.2); color: #155724; }
        .status-cancelled { background-color: rgba(220, 53, 69, 0.2); color: #721c24; }
        
        .membership-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .membership-vip { background-color: #ffd700; color: #8b6914; }
        .membership-premium { background-color: #e6e6fa; color: #4b0082; }
        .membership-basic { background-color: #f0f0f0; color: #666; }
        
        .transaction-ref {
            font-family: monospace;
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .amount {
            font-weight: bold;
            color: #28a745;
            font-size: 1rem;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
        
        .status-form {
            flex: 1;
        }
        
        .status-form select {
            width: 100%;
        }
        
        .no-orders {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.1rem;
        }
        
        .header-title p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .filter-form .btn {
            min-width: 100px;
            height: 40px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-form .form-control {
            min-width: 150px;
        }
    </style>
    
    <script>
        function refreshOrders() {
            location.reload();
        }
        
        function printOrders() {
            window.print();
        }
        
        function generateEmail(orderId, customerName, customerEmail) {
            document.getElementById('email-to').value = customerEmail;
            document.getElementById('email-subject').value = `Order #${orderId} Update - Cofeology`;
            document.getElementById('email-message').value = `Dear ${customerName},\n\nWe hope this email finds you well.\n\nThis is to update you regarding your order #${orderId}. Your order status has been updated and we wanted to keep you informed.\n\nIf you have any questions or concerns, please don't hesitate to contact us.\n\nThank you for choosing Cofeology!\n\nBest regards,\nCofeology Team`;
            
            document.getElementById('email-modal').style.display = 'flex';
        }
        
        function copyEmail() {
            const subject = document.getElementById('email-subject').value;
            const message = document.getElementById('email-message').value;
            const emailContent = `Subject: ${subject}\n\n${message}`;
            
            navigator.clipboard.writeText(emailContent).then(function() {
                alert('Email content copied to clipboard!');
            });
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');
            const closeButtons = document.querySelectorAll('.close-modal');
            
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    modal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', function(event) {
                modals.forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
        });
        
        // Auto-refresh every 30 seconds for active orders
        setInterval(function() {
            const activeStatuses = ['pending', 'preparing', 'ready'];
            const currentStatus = new URLSearchParams(window.location.search).get('status');
            
            if (!currentStatus || activeStatuses.includes(currentStatus)) {
                // Only refresh if we're viewing active orders
                const xhr = new XMLHttpRequest();
                xhr.open('GET', window.location.href, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Update the orders grid without full page reload
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(xhr.responseText, 'text/html');
                        const newOrdersGrid = doc.querySelector('.orders-grid');
                        const currentOrdersGrid = document.querySelector('.orders-grid');
                        
                        if (newOrdersGrid && currentOrdersGrid) {
                            currentOrdersGrid.innerHTML = newOrdersGrid.innerHTML;
                        }
                    }
                };
                xhr.send();
            }
        }, 30000); // 30 seconds
    </script>
</body>
</html>
