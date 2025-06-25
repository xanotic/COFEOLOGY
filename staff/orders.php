<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not staff
if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

// Get all orders (staff can see all orders)
$stmt = $conn->prepare("
    SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone
    FROM `ORDER` o
    JOIN CUSTOMER c ON o.customer_id = c.customer_id
    ORDER BY 
        CASE 
            WHEN o.order_status = 'pending' THEN 1
            WHEN o.order_status = 'preparing' THEN 2
            WHEN o.order_status = 'ready' THEN 3
            WHEN o.order_status = 'out_for_delivery' THEN 4
            ELSE 5
        END,
        o.order_date DESC
");
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order statistics for today
$today = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_today,
        COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_today,
        COUNT(CASE WHEN order_status = 'preparing' THEN 1 END) as preparing_today,
        COUNT(CASE WHEN order_status = 'ready' THEN 1 END) as ready_today,
        COUNT(CASE WHEN order_status = 'completed' THEN 1 END) as completed_today
    FROM `ORDER`
    WHERE DATE(order_date) = ?
");
$stmt->bind_param("s", $today);
$stmt->execute();
$todayStats = $stmt->get_result()->fetch_assoc();
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
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="refreshOrders()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-secondary" onclick="printKitchenOrders()">
                        <i class="fas fa-print"></i> Kitchen Orders
                    </button>
                </div>
            </header>
            
            <!-- Today's Statistics -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Today's Orders</h3>
                        <p><?php echo number_format($todayStats['total_today']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pending</h3>
                        <p><?php echo number_format($todayStats['pending_today']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Preparing</h3>
                        <p><?php echo number_format($todayStats['preparing_today']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Ready</h3>
                        <p><?php echo number_format($todayStats['ready_today']); ?></p>
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
                            <option value="delivery">Delivery</option>
                            <option value="takeaway">Takeaway</option>
                            <option value="dine-in">Dine-in</option>
                        </select>
                        <input type="date" id="date-filter" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table">
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr data-status="<?php echo $order['order_status']; ?>" 
                                            data-type="<?php echo $order['order_type']; ?>" 
                                            data-date="<?php echo date('Y-m-d', strtotime($order['order_date'])); ?>"
                                            class="order-row <?php echo $order['order_status']; ?>">
                                            <td>
                                                <strong>#<?php echo $order['order_id']; ?></strong>
                                                <?php if ($order['order_status'] === 'pending'): ?>
                                                    <span class="urgent-badge">NEW</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="customer-info">
                                                    <span><?php echo htmlspecialchars($order['customer_name']); ?></span>
                                                    <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo htmlspecialchars($order['customer_phone']); ?>" class="phone-link">
                                                    <?php echo htmlspecialchars($order['customer_phone']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="order-type-badge <?php echo $order['order_type']; ?>">
                                                    <i class="fas <?php echo $order['order_type'] === 'delivery' ? 'fa-truck' : ($order['order_type'] === 'takeaway' ? 'fa-shopping-bag' : 'fa-utensils'); ?>"></i>
                                                    <?php echo ucfirst($order['order_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $order['order_status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                            <td>
                                                <div class="time-info">
                                                    <span><?php echo date('h:i A', strtotime($order['order_date'])); ?></span>
                                                    <small><?php echo date('M d', strtotime($order['order_date'])); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="order-details.php?id=<?php echo $order['order_id']; ?>" class="btn-icon" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($order['order_status'] !== 'completed' && $order['order_status'] !== 'cancelled'): ?>
                                                        <button class="btn-icon update-status" data-id="<?php echo $order['order_id']; ?>" data-current="<?php echo $order['order_status']; ?>" title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button class="btn-icon print-order" data-id="<?php echo $order['order_id']; ?>" title="Print Order">
                                                        <i class="fas fa-print"></i>
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
    
    <!-- Quick Status Update Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <input type="hidden" id="order-id" name="order_id">
                    
                    <div class="status-buttons">
                        <button type="button" class="status-btn pending" data-status="pending">
                            <i class="fas fa-clock"></i>
                            Pending
                        </button>
                        <button type="button" class="status-btn preparing" data-status="preparing">
                            <i class="fas fa-fire"></i>
                            Preparing
                        </button>
                        <button type="button" class="status-btn ready" data-status="ready">
                            <i class="fas fa-check"></i>
                            Ready
                        </button>
                        <button type="button" class="status-btn out_for_delivery" data-status="out_for_delivery">
                            <i class="fas fa-truck"></i>
                            Out for Delivery
                        </button>
                        <button type="button" class="status-btn completed" data-status="completed">
                            <i class="fas fa-check-circle"></i>
                            Completed
                        </button>
                        <button type="button" class="status-btn cancelled" data-status="cancelled">
                            <i class="fas fa-times-circle"></i>
                            Cancelled
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
        .urgent-badge {
            background-color: #ff4757;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: bold;
            margin-left: 5px;
        }
        
        .order-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .order-type-badge.delivery {
            background-color: rgba(52, 152, 219, 0.2);
            color: #2980b9;
        }
        
        .order-type-badge.takeaway {
            background-color: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .order-type-badge.dine-in {
            background-color: rgba(155, 89, 182, 0.2);
            color: #8e44ad;
        }
        
        .phone-link {
            color: #3498db;
            text-decoration: none;
        }
        
        .phone-link:hover {
            text-decoration: underline;
        }
        
        .time-info {
            display: flex;
            flex-direction: column;
        }
        
        .time-info small {
            color: #666;
            font-size: 0.75rem;
        }
        
        .order-row.pending {
            background-color: rgba(255, 193, 7, 0.05);
        }
        
        .order-row.preparing {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .order-row.ready {
            background-color: rgba(40, 167, 69, 0.05);
        }
        
        .status-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .status-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .status-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .status-btn.pending { border-color: #ffc107; color: #856404; }
        .status-btn.preparing { border-color: #007bff; color: #004085; }
        .status-btn.ready { border-color: #28a745; color: #155724; }
        .status-btn.out_for_delivery { border-color: #fd7e14; color: #8a4412; }
        .status-btn.completed { border-color: #20c997; color: #0c5460; }
        .status-btn.cancelled { border-color: #dc3545; color: #721c24; }
        
        .status-btn.pending:hover { background-color: #ffc107; color: white; }
        .status-btn.preparing:hover { background-color: #007bff; color: white; }
        .status-btn.ready:hover { background-color: #28a745; color: white; }
        .status-btn.out_for_delivery:hover { background-color: #fd7e14; color: white; }
        .status-btn.completed:hover { background-color: #20c997; color: white; }
        .status-btn.cancelled:hover { background-color: #dc3545; color: white; }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const statusFilter = document.getElementById('status-filter');
            const typeFilter = document.getElementById('type-filter');
            const dateFilter = document.getElementById('date-filter');
            const ordersTable = document.getElementById('orders-table');
            
            function filterOrders() {
                const statusValue = statusFilter.value;
                const typeValue = typeFilter.value;
                const dateValue = dateFilter.value;
                const rows = ordersTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowType = row.getAttribute('data-type');
                    const rowDate = row.getAttribute('data-date');
                    
                    let showRow = true;
                    
                    if (statusValue && rowStatus !== statusValue) {
                        showRow = false;
                    }
                    
                    if (typeValue && rowType !== typeValue) {
                        showRow = false;
                    }
                    
                    if (dateValue && rowDate !== dateValue) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            statusFilter.addEventListener('change', filterOrders);
            typeFilter.addEventListener('change', filterOrders);
            dateFilter.addEventListener('change', filterOrders);
            
            // Status update modal
            const modal = document.getElementById('status-modal');
            const closeModal = document.querySelector('.close-modal');
            const updateButtons = document.querySelectorAll('.update-status');
            const statusButtons = document.querySelectorAll('.status-btn');
            
            updateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    const currentStatus = this.getAttribute('data-current');
                    document.getElementById('order-id').value = orderId;
                    
                    // Highlight current status
                    statusButtons.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.getAttribute('data-status') === currentStatus) {
                            btn.style.backgroundColor = btn.style.borderColor;
                            btn.style.color = 'white';
                        }
                    });
                    
                    modal.style.display = 'flex';
                });
            });
            
            statusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = document.getElementById('order-id').value;
                    const status = this.getAttribute('data-status');
                    
                    updateOrderStatus(orderId, status);
                });
            });
            
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Print order functionality
            const printButtons = document.querySelectorAll('.print-order');
            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    printOrder(orderId);
                });
            });
        });
        
        function updateOrderStatus(orderId, status) {
            fetch('../api/update-order-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    order_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('status-modal').style.display = 'none';
                    showNotification('Order status updated successfully');
                    
                    // Refresh page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Failed to update order status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            });
        }
        
        function refreshOrders() {
            location.reload();
        }
        
        function printOrder(orderId) {
            window.open(`../api/print-order.php?id=${orderId}`, '_blank');
        }
        
        function printKitchenOrders() {
            window.open('../api/print-kitchen-orders.php', '_blank');
        }
        
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
        
        // Auto-refresh every 30 seconds for new orders
        setInterval(function() {
            // You can implement AJAX refresh here to avoid full page reload
            const currentTime = new Date().toLocaleTimeString();
            console.log('Checking for new orders at', currentTime);
        }, 30000);
    </script>
</body>
</html>
