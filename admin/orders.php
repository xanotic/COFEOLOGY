<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
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
        
        if (updateOrderStatus($conn, $order_id, $new_status)) {
            $message = "Order status updated successfully!";
        } else {
            $error = "Failed to update order status.";
        }
    }
    
    if ($_POST['action'] === 'assign_staff') {
        $order_id = intval($_POST['order_id']);
        $staff_id = intval($_POST['staff_id']);
        
        if (assignStaffToOrder($conn, $order_id, $staff_id)) {
            $message = "Staff assigned to order successfully!";
        } else {
            $error = "Failed to assign staff to order.";
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Build query with filters
$query = "
    SELECT 
        o.ORDER_ID,
        o.ORDER_NUMBER,
        o.ORDER_TIME,
        o.ORDER_DATE,
        o.ORDER_TYPE,
        o.ORDER_STATUS,
        o.TOT_AMOUNT,
        o.DELIVERY_ADDRESS,
        o.PAYMENT_METHOD,
        COALESCE(o.PAYMENT_STATUS, 'pending') as payment_status,
        o.special_instructions,
        o.pickup_time,
        c.CUST_NAME as customer_name,
        c.CUST_EMAIL as customer_email,
        c.CUST_NPHONE as customer_phone,
        s.STAFF_NAME as staff_name,
        COUNT(ol.LISTING_ID) as item_count
    FROM `order` o
    JOIN customer c ON o.CUST_ID = c.CUST_ID
    LEFT JOIN staff s ON o.STAFF_ID = s.STAFF_ID
    LEFT JOIN order_listing ol ON o.ORDER_ID = ol.ORDER_ID
";

$conditions = [];
$params = [];
$types = '';

if ($status_filter) {
    $conditions[] = "o.ORDER_STATUS = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if ($date_filter) {
    $conditions[] = "o.ORDER_DATE = ?";
    $params[] = $date_filter;
    $types .= 's';
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY o.ORDER_ID ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all staff for assignment dropdown
$staff_stmt = $conn->prepare("SELECT STAFF_ID, STAFF_NAME FROM staff ORDER BY STAFF_NAME");
$staff_stmt->execute();
$staff_members = $staff_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN ORDER_STATUS = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN ORDER_STATUS = 'preparing' THEN 1 ELSE 0 END) as preparing_orders,
        SUM(CASE WHEN ORDER_STATUS = 'ready' THEN 1 ELSE 0 END) as ready_orders,
        SUM(CASE WHEN ORDER_STATUS = 'completed' THEN 1 ELSE 0 END) as completed_orders
    FROM `order`
    WHERE ORDER_DATE = CURDATE()
";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Café Delights</title>
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
                    <h1>Order Management</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="refreshOrders()">
                        <i class="fas fa-refresh"></i> Refresh
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
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Orders Today</p>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending_orders']; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                <div class="stat-card preparing">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['preparing_orders']; ?></h3>
                        <p>Preparing</p>
                    </div>
                </div>
                <div class="stat-card ready">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['ready_orders']; ?></h3>
                        <p>Ready</p>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card">
                <div class="card-header">
                    <h2>Filter Orders</h2>
                </div>
                <div class="card-body">
                    <form method="GET" class="filter-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="preparing" <?php echo $status_filter === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="ready" <?php echo $status_filter === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                    <option value="out_for_delivery" <?php echo $status_filter === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="orders.php" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h2>Orders (<?php echo count($orders); ?>)</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date/Time</th>
                                    <th>Type</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Staff</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['ORDER_NUMBER']); ?></strong>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                                <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                <small><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="datetime-info">
                                                <strong><?php echo date('M j, Y', strtotime($order['ORDER_DATE'])); ?></strong>
                                                <small><?php echo date('g:i A', strtotime($order['ORDER_TIME'])); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="order-type <?php echo $order['ORDER_TYPE']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $order['ORDER_TYPE'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $order['item_count']; ?> items</td>
                                        <td><strong><?php echo formatCurrency($order['TOT_AMOUNT']); ?></strong></td>
                                        <td>
                                            <span class="status-badge <?php echo $order['ORDER_STATUS']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $order['ORDER_STATUS'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="payment-status <?php echo $order['payment_status']; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $order['staff_name'] ? htmlspecialchars($order['staff_name']) : 'Unassigned'; ?>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="btn-icon" onclick="viewOrder(<?php echo $order['ORDER_ID']; ?>)" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon" onclick="updateStatus(<?php echo $order['ORDER_ID']; ?>, '<?php echo $order['ORDER_STATUS']; ?>')" title="Update Status">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon" onclick="assignStaff(<?php echo $order['ORDER_ID']; ?>)" title="Assign Staff">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            </div>
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
    
    <!-- Status Update Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="status-form" method="post">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" id="status-order-id" name="order_id">
                    
                    <div class="form-group">
                        <label for="new-status" class="form-label">New Status</label>
                        <select id="new-status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Staff Assignment Modal -->
    <div class="modal" id="staff-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Assign Staff</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="staff-form" method="post">
                    <input type="hidden" name="action" value="assign_staff">
                    <input type="hidden" id="staff-order-id" name="order_id">
                    
                    <div class="form-group">
                        <label for="staff-select" class="form-label">Select Staff Member</label>
                        <select id="staff-select" name="staff_id" class="form-control" required>
                            <option value="">Choose staff member...</option>
                            <?php foreach ($staff_members as $staff): ?>
                                <option value="<?php echo $staff['STAFF_ID']; ?>">
                                    <?php echo htmlspecialchars($staff['STAFF_NAME']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Assign Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            background-color: #007bff;
        }
        
        .stat-card.pending .stat-icon { background-color: #ffc107; }
        .stat-card.preparing .stat-icon { background-color: #fd7e14; }
        .stat-card.ready .stat-icon { background-color: #28a745; }
        
        .stat-content h3 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .stat-content p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .filter-form .form-row {
            display: flex;
            gap: 20px;
            align-items: end;
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
        }
        
        .customer-info small {
            color: #666;
            font-size: 12px;
        }
        
        .datetime-info {
            display: flex;
            flex-direction: column;
        }
        
        .datetime-info small {
            color: #666;
            font-size: 12px;
        }
        
        .order-type {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .order-type.delivery { background-color: #e3f2fd; color: #1976d2; }
        .order-type.takeaway { background-color: #f3e5f5; color: #7b1fa2; }
        .order-type.dine-in { background-color: #e8f5e8; color: #388e3c; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-badge.pending { background-color: #fff3cd; color: #856404; }
        .status-badge.preparing { background-color: #ffeaa7; color: #d63031; }
        .status-badge.ready { background-color: #d4edda; color: #155724; }
        .status-badge.out_for_delivery { background-color: #cce5ff; color: #004085; }
        .status-badge.completed { background-color: #d1ecf1; color: #0c5460; }
        .status-badge.cancelled { background-color: #f8d7da; color: #721c24; }
        
        .payment-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .payment-status.pending { background-color: #fff3cd; color: #856404; }
        .payment-status.completed { background-color: #d4edda; color: #155724; }
        .payment-status.failed { background-color: #f8d7da; color: #721c24; }
        
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
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
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
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-actions {
            text-align: right;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-icon {
            background: none;
            border: none;
            padding: 5px;
            cursor: pointer;
            color: #666;
            margin: 0 2px;
        }
        
        .btn-icon:hover {
            color: #333;
        }
        
        .table-actions {
            display: flex;
            gap: 5px;
        }
    </style>
    
    <script>
        // Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.modal');
            const closeButtons = document.querySelectorAll('.close-modal');
            
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
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
        
        function updateStatus(orderId, currentStatus) {
            document.getElementById('status-order-id').value = orderId;
            document.getElementById('new-status').value = currentStatus;
            document.getElementById('status-modal').style.display = 'flex';
        }
        
        function assignStaff(orderId) {
            document.getElementById('staff-order-id').value = orderId;
            document.getElementById('staff-modal').style.display = 'flex';
        }
        
        function viewOrder(orderId) {
            // Implement order details view
            window.location.href = `order-details.php?id=${orderId}`;
        }
        
        function refreshOrders() {
            window.location.reload();
        }
    </script>
</body>
</html>
