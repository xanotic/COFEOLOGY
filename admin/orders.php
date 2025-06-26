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
                
                $stmt = $conn->prepare("UPDATE `order` SET ORDER_STATUS = ? WHERE ORDER_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $status, $order_id);
                    if ($stmt->execute()) {
                        $message = "Order status updated successfully!";
                    } else {
                        $error = "Failed to update order status.";
                    }
                    $stmt->close();
                }
                break;
                
            case 'assign_staff':
                $order_id = intval($_POST['order_id']);
                $staff_id = intval($_POST['staff_id']);
                
                $stmt = $conn->prepare("UPDATE `order` SET STAFF_ID = ? WHERE ORDER_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("ii", $staff_id, $order_id);
                    if ($stmt->execute()) {
                        $message = "Staff assigned successfully!";
                    } else {
                        $error = "Failed to assign staff.";
                    }
                    $stmt->close();
                }
                break;
                
            case 'update_payment':
                $order_id = intval($_POST['order_id']);
                $payment_status = $_POST['payment_status'];
                
                $stmt = $conn->prepare("UPDATE `order` SET PAYMENT_STATUS = ? WHERE ORDER_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $payment_status, $order_id);
                    if ($stmt->execute()) {
                        $message = "Payment status updated successfully!";
                    } else {
                        $error = "Failed to update payment status.";
                    }
                    $stmt->close();
                }
                break;
        }
    }
}

// Get all orders with customer and staff information
$orders = [];
$query = "SELECT o.*, c.CUST_NAME, c.CUST_EMAIL, c.CUST_NPHONE, s.STAFF_NAME,
          COALESCE(o.PAYMENT_STATUS, 'pending') as payment_status
          FROM `order` o 
          LEFT JOIN customer c ON o.CUST_ID = c.CUST_ID 
          LEFT JOIN staff s ON o.STAFF_ID = s.STAFF_ID 
          ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC";
$result = $conn->query($query);
if ($result) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}

// Get all staff for assignment
$staff = [];
$result = $conn->query("SELECT STAFF_ID, STAFF_NAME FROM staff ORDER BY STAFF_NAME");
if ($result) {
    $staff = $result->fetch_all(MYSQLI_ASSOC);
}

// Get order statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM `order`");
$stats['total_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Pending orders
$result = $conn->query("SELECT COUNT(*) as total FROM `order` WHERE ORDER_STATUS = 'pending'");
$stats['pending_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Today's orders
$today = date('Y-m-d');
$result = $conn->query("SELECT COUNT(*) as total FROM `order` WHERE ORDER_DATE = '$today'");
$stats['today_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Today's revenue
$result = $conn->query("SELECT SUM(TOT_AMOUNT) as total FROM `order` WHERE ORDER_DATE = '$today' AND PAYMENT_STATUS = 'completed'");
$stats['today_revenue'] = $result ? ($result->fetch_assoc()['total'] ?: 0) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Café Delights</title>
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
                    <button class="btn btn-secondary" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="btn btn-primary" onclick="refreshOrders()">
                        <i class="fas fa-sync-alt"></i> Refresh
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
                        <h3>Pending Orders</h3>
                        <p><?php echo number_format($stats['pending_orders']); ?></p>
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
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Today's Revenue</h3>
                        <p>RM <?php echo number_format($stats['today_revenue'], 2); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>All Orders</h2>
                    <div class="card-filters">
                        <select id="status-filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select id="type-filter" class="form-control">
                            <option value="">All Types</option>
                            <option value="dine-in">Dine In</option>
                            <option value="takeaway">Takeaway</option>
                            <option value="delivery">Delivery</option>
                        </select>
                        <select id="payment-filter" class="form-control">
                            <option value="">All Payments</option>
                            <option value="pending">Payment Pending</option>
                            <option value="completed">Payment Completed</option>
                            <option value="failed">Payment Failed</option>
                        </select>
                        <input type="date" id="date-filter" class="form-control">
                        <input type="text" id="search-filter" class="form-control" placeholder="Search orders...">
                    </div>
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
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Staff</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table">
                                <?php foreach ($orders as $order): ?>
                                    <tr data-status="<?php echo $order['ORDER_STATUS']; ?>" 
                                        data-type="<?php echo $order['ORDER_TYPE']; ?>"
                                        data-payment="<?php echo $order['payment_status']; ?>"
                                        data-date="<?php echo $order['ORDER_DATE']; ?>"
                                        data-search="<?php echo strtolower($order['ORDER_NUMBER'] . ' ' . $order['CUST_NAME'] . ' ' . $order['CUST_EMAIL']); ?>">
                                        <td>
                                            <strong><?php echo $order['ORDER_NUMBER']; ?></strong>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?php echo htmlspecialchars($order['CUST_NAME']); ?></strong>
                                                <small><?php echo htmlspecialchars($order['CUST_EMAIL']); ?></small>
                                                <small><?php echo htmlspecialchars($order['CUST_NPHONE']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="datetime-info">
                                                <strong><?php echo date('M d, Y', strtotime($order['ORDER_DATE'])); ?></strong>
                                                <small><?php echo date('h:i A', strtotime($order['ORDER_TIME'])); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="type-badge <?php echo $order['ORDER_TYPE']; ?>">
                                                <?php echo ucfirst(str_replace('-', ' ', $order['ORDER_TYPE'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>RM <?php echo number_format($order['TOT_AMOUNT'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $order['ORDER_STATUS']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $order['ORDER_STATUS'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="payment-badge <?php echo $order['payment_status']; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $order['STAFF_NAME'] ? htmlspecialchars($order['STAFF_NAME']) : 'Unassigned'; ?>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="btn-icon" onclick="viewOrderDetails(<?php echo $order['ORDER_ID']; ?>)" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn-icon" onclick="updateOrderStatus(<?php echo $order['ORDER_ID']; ?>, '<?php echo $order['ORDER_STATUS']; ?>')" title="Update Status">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon" onclick="assignStaff(<?php echo $order['ORDER_ID']; ?>, <?php echo $order['STAFF_ID'] ?: 0; ?>)" title="Assign Staff">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                                <button class="btn-icon" onclick="updatePaymentStatus(<?php echo $order['ORDER_ID']; ?>, '<?php echo $order['payment_status']; ?>')" title="Update Payment">
                                                    <i class="fas fa-credit-card"></i>
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
    
    <!-- Update Status Modal -->
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
                        <label for="order-status" class="form-label">Order Status</label>
                        <select id="order-status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="out_for_delivery">Out for Delivery</option>
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
    
    <!-- Assign Staff Modal -->
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
                        <label for="staff-select" class="form-label">Select Staff</label>
                        <select id="staff-select" name="staff_id" class="form-control" required>
                            <option value="">Select Staff Member</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?php echo $member['STAFF_ID']; ?>">
                                    <?php echo htmlspecialchars($member['STAFF_NAME']); ?>
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
    
    <!-- Payment Status Modal -->
    <div class="modal" id="payment-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Payment Status</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="payment-form" method="post">
                    <input type="hidden" name="action" value="update_payment">
                    <input type="hidden" id="payment-order-id" name="order_id">
                    
                    <div class="form-group">
                        <label for="payment-status" class="form-label">Payment Status</label>
                        <select id="payment-status" name="payment_status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('payment-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Payment</button>
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
        
        .customer-info strong {
            display: block;
        }
        
        .customer-info small {
            color: #666;
            display: block;
        }
        
        .datetime-info strong {
            display: block;
        }
        
        .datetime-info small {
            color: #666;
        }
        
        .type-badge, .status-badge, .payment-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .type-badge.dine-in {
            background-color: #e2e3e5;
            color: #495057;
        }
        
        .type-badge.takeaway {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .type-badge.delivery {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-badge.preparing {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-badge.ready {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-badge.out_for_delivery {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-badge.completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .payment-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .payment-badge.completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-badge.failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .card-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .card-filters .form-control {
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
            max-width: 500px;
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
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const statusFilter = document.getElementById('status-filter');
            const typeFilter = document.getElementById('type-filter');
            const paymentFilter = document.getElementById('payment-filter');
            const dateFilter = document.getElementById('date-filter');
            const searchFilter = document.getElementById('search-filter');
            const ordersTable = document.getElementById('orders-table');
            
            function filterOrders() {
                const statusValue = statusFilter.value;
                const typeValue = typeFilter.value;
                const paymentValue = paymentFilter.value;
                const dateValue = dateFilter.value;
                const searchValue = searchFilter.value.toLowerCase();
                const rows = ordersTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowType = row.getAttribute('data-type');
                    const rowPayment = row.getAttribute('data-payment');
                    const rowDate = row.getAttribute('data-date');
                    const rowSearch = row.getAttribute('data-search');
                    
                    let showRow = true;
                    
                    if (statusValue && rowStatus !== statusValue) {
                        showRow = false;
                    }
                    
                    if (typeValue && rowType !== typeValue) {
                        showRow = false;
                    }
                    
                    if (paymentValue && rowPayment !== paymentValue) {
                        showRow = false;
                    }
                    
                    if (dateValue && rowDate !== dateValue) {
                        showRow = false;
                    }
                    
                    if (searchValue && !rowSearch.includes(searchValue)) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            statusFilter.addEventListener('change', filterOrders);
            typeFilter.addEventListener('change', filterOrders);
            paymentFilter.addEventListener('change', filterOrders);
            dateFilter.addEventListener('change', filterOrders);
            searchFilter.addEventListener('input', filterOrders);
            
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
        
        function updateOrderStatus(orderId, currentStatus) {
            document.getElementById('status-order-id').value = orderId;
            document.getElementById('order-status').value = currentStatus;
            document.getElementById('status-modal').style.display = 'flex';
        }
        
        function assignStaff(orderId, currentStaffId) {
            document.getElementById('staff-order-id').value = orderId;
            document.getElementById('staff-select').value = currentStaffId;
            document.getElementById('staff-modal').style.display = 'flex';
        }
        
        function updatePaymentStatus(orderId, currentPaymentStatus) {
            document.getElementById('payment-order-id').value = orderId;
            document.getElementById('payment-status').value = currentPaymentStatus;
            document.getElementById('payment-modal').style.display = 'flex';
        }
        
        function viewOrderDetails(orderId) {
            window.location.href = 'order-details.php?id=' + orderId;
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function exportOrders() {
            window.open('../api/export-orders.php', '_blank');
        }
        
        function refreshOrders() {
            location.reload();
        }
    </script>
</body>
</html>
