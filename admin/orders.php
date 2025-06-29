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

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $order_id = intval($_POST['order_id']);
                $status = $_POST['status'];
                
                if (updateOrderStatus($conn, $order_id, $status)) {
                    $message = "Order status updated successfully!";
                } else {
                    $error = "Failed to update order status.";
                }
                break;
                
            case 'assign_staff':
                $order_id = intval($_POST['order_id']);
                $staff_id = intval($_POST['staff_id']);
                
                if (assignStaffToOrder($conn, $order_id, $staff_id)) {
                    $message = "Staff assigned to order successfully!";
                } else {
                    $error = "Failed to assign staff to order.";
                }
                break;
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$type_filter = $_GET['type'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter) {
    $where_conditions[] = "o.ORDER_STATUS = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($date_filter) {
    $where_conditions[] = "DATE(o.ORDER_DATE) = ?";
    $params[] = $date_filter;
    $param_types .= 's';
}

if ($type_filter) {
    $where_conditions[] = "o.ORDER_TYPE = ?";
    $params[] = $type_filter;
    $param_types .= 's';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get all orders with customer information
$sql = "
    SELECT 
        o.ORDER_ID, 
        o.ORDER_NUMBER, 
        o.ORDER_DATE, 
        o.ORDER_TIME, 
        o.ORDER_TYPE, 
        COALESCE(o.ORDER_STATUS, 'pending') as ORDER_STATUS,
        COALESCE(o.PAYMENT_STATUS, 'pending') as PAYMENT_STATUS,
        o.TOT_AMOUNT, 
        o.DELIVERY_ADDRESS,
        o.PAYMENT_METHOD,
        o.special_instructions,
        o.pickup_time,
        c.CUST_NAME as customer_name, 
        c.CUST_EMAIL as customer_email,
        c.CUST_NPHONE as customer_phone,
        s.STAFF_NAME as staff_name
    FROM `ORDER` o
    LEFT JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
    LEFT JOIN STAFF s ON o.STAFF_ID = s.STAFF_ID
    $where_clause
    ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM `ORDER`");
$stats['total_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Pending orders
$result = $conn->query("SELECT COUNT(*) as total FROM `ORDER` WHERE ORDER_STATUS = 'pending'");
$stats['pending_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Completed orders
$result = $conn->query("SELECT COUNT(*) as total FROM `ORDER` WHERE ORDER_STATUS = 'completed'");
$stats['completed_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Today's orders
$today = date('Y-m-d');
$result = $conn->query("SELECT COUNT(*) as total FROM `ORDER` WHERE DATE(ORDER_DATE) = '$today'");
$stats['today_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Get all staff for assignment
$staff_list = [];
$result = $conn->query("SELECT STAFF_ID, STAFF_NAME FROM STAFF ORDER BY STAFF_NAME");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $staff_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Cofeology</title>
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
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
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
                        <h3>Pending Orders</h3>
                        <p><?php echo number_format($stats['pending_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Completed Orders</h3>
                        <p><?php echo number_format($stats['completed_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Today's Orders</h3>
                        <p><?php echo number_format($stats['today_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>All Orders</h2>
                    <div class="card-filters">
                        <form method="get" class="filter-form">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
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
                            <input type="date" name="date" value="<?php echo $date_filter; ?>" class="form-control">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="orders.php" class="btn btn-secondary">Clear</a>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Date/Time</th>
                                    <th>Staff</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $order['ORDER_NUMBER'] ?? '#' . $order['ORDER_ID']; ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></small>
                                                </div>
                                            </td>
                                            <td><?php echo ucfirst($order['ORDER_TYPE']); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $order['ORDER_STATUS']; ?>">
                                                    <?php echo ucfirst($order['ORDER_STATUS']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="payment-badge <?php echo $order['PAYMENT_STATUS']; ?>">
                                                    <?php echo ucfirst($order['PAYMENT_STATUS']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatCurrency($order['TOT_AMOUNT']); ?></td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($order['ORDER_DATE'])); ?>
                                                <br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($order['ORDER_TIME'])); ?></small>
                                            </td>
                                            <td>
                                                <?php echo $order['staff_name'] ? htmlspecialchars($order['staff_name']) : '<em>Unassigned</em>'; ?>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    
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
                                <?php endif; ?>
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
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('status-modal')">Cancel</button>
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
                <h3>Assign Staff to Order</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="staff-form" method="post">
                    <input type="hidden" name="action" value="assign_staff">
                    <input type="hidden" id="staff-order-id" name="order_id">
                    
                    <div class="form-group">
                        <label for="staff_id" class="form-label">Staff Member</label>
                        <select id="staff_id" name="staff_id" class="form-control" required>
                            <option value="">Select Staff</option>
                            <?php foreach ($staff_list as $staff): ?>
                                <option value="<?php echo $staff['STAFF_ID']; ?>">
                                    <?php echo htmlspecialchars($staff['STAFF_NAME']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('staff-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Staff</button>
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-badge.confirmed {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-badge.preparing {
            background-color: #ffeaa7;
            color: #6c5ce7;
        }
        
        .status-badge.ready {
            background-color: #a8e6cf;
            color: #00b894;
        }
        
        .status-badge.completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .payment-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .payment-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .payment-badge.paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-badge.failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-form .form-control {
            width: auto;
            min-width: 120px;
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
            max-width: 400px;
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
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal functionality
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
        
        
        
        function updateStatus(orderId, currentStatus) {
            document.getElementById('status-order-id').value = orderId;
            document.getElementById('status').value = currentStatus;
            document.getElementById('status-modal').style.display = 'flex';
        }
        
        function assignStaff(orderId) {
            document.getElementById('staff-order-id').value = orderId;
            document.getElementById('staff-modal').style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function exportOrders() {
            const params = new URLSearchParams(window.location.search);
            window.open('../api/export-orders.php?' + params.toString(), '_blank');
        }
    </script>
</body>
</html>
