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

// Handle order status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $order_id = intval($_POST['order_id']);
        $new_status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE `ORDER` SET ORDER_STATUS = ? WHERE ORDER_ID = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            $message = "Order status updated successfully!";
        } else {
            $error = "Failed to update order status.";
        }
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query with filters
$query = "SELECT o.*, c.CUST_NAME, c.CUST_EMAIL, c.CUST_NPHONE 
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
    $query .= " AND (c.CUST_NAME LIKE ? OR o.ORDER_ID LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$query .= " ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
$stats = $stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Café Delights Staff</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
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
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
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
                                <div class="order-card" data-status="<?php echo $order['ORDER_STATUS']; ?>">
                                    <div class="order-header">
                                        <div class="order-id">
                                            <strong>#<?php echo $order['ORDER_ID']; ?></strong>
                                        </div>
                                        <div class="order-status">
                                            <span class="status-badge status-<?php echo $order['ORDER_STATUS']; ?>">
                                                <?php echo ucfirst($order['ORDER_STATUS']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-customer">
                                        <div class="customer-info">
                                            <i class="fas fa-user"></i>
                                            <span><?php echo htmlspecialchars($order['CUST_NAME'] ?? 'Guest'); ?></span>
                                        </div>
                                        <?php if ($order['CUST_NPHONE']): ?>
                                        <div class="customer-phone">
                                            <i class="fas fa-phone"></i>
                                            <span><?php echo htmlspecialchars($order['CUST_NPHONE']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="order-details">
                                        <div class="order-type">
                                            <i class="fas fa-<?php echo $order['ORDER_TYPE'] === 'dine-in' ? 'utensils' : ($order['ORDER_TYPE'] === 'takeaway' ? 'shopping-bag' : 'truck'); ?>"></i>
                                            <span><?php echo ucfirst(str_replace('-', ' ', $order['ORDER_TYPE'])); ?></span>
                                        </div>
                                        <div class="order-time">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo date('M j, Y g:i A', strtotime($order['ORDER_DATE'] . ' ' . $order['ORDER_TIME'])); ?></span>
                                        </div>
                                        <div class="order-total">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>RM<?php echo number_format($order['TOT_AMOUNT'], 2); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-actions">
                                        <a href="order-details.php?id=<?php echo $order['ORDER_ID']; ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        
                                        <?php if ($order['ORDER_STATUS'] !== 'completed' && $order['ORDER_STATUS'] !== 'cancelled'): ?>
                                        <div class="status-update">
                                            <form method="post" class="status-form">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?php echo $order['ORDER_ID']; ?>">
                                                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                    <option value="pending" <?php echo $order['ORDER_STATUS'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="preparing" <?php echo $order['ORDER_STATUS'] === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                    <option value="ready" <?php echo $order['ORDER_STATUS'] === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                                    <option value="completed" <?php echo $order['ORDER_STATUS'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo $order['ORDER_STATUS'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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
        
        .order-card[data-status="pending"] {
            border-left-color: #ffc107;
        }
        
        .order-card[data-status="preparing"] {
            border-left-color: #fd7e14;
        }
        
        .order-card[data-status="ready"] {
            border-left-color: #20c997;
        }
        
        .order-card[data-status="completed"] {
            border-left-color: #28a745;
        }
        
        .order-card[data-status="cancelled"] {
            border-left-color: #dc3545;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .order-id {
            font-size: 1.2rem;
            color: #2d3436;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .status-preparing {
            background-color: rgba(253, 126, 20, 0.2);
            color: #8a4a00;
        }
        
        .status-ready {
            background-color: rgba(32, 201, 151, 0.2);
            color: #0f5132;
        }
        
        .status-completed {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }
        
        .order-customer {
            margin-bottom: 15px;
        }
        
        .customer-info, .customer-phone {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-details {
            margin-bottom: 20px;
        }
        
        .order-type, .order-time, .order-total {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-total {
            font-weight: bold;
            color: #28a745;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
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
    </style>
    
    <script>
        function refreshOrders() {
            location.reload();
        }
        
        function printOrders() {
            window.print();
        }
        
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
