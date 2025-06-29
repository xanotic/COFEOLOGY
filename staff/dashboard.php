<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not staff
if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

// Get pending orders
$stmt = $conn->prepare("
    SELECT o.*, c.CUST_NAME as customer_name, c.CUST_NPHONE as customer_phone
    FROM `ORDER` o
    JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
    WHERE o.ORDER_STATUS != 'completed' AND o.ORDER_STATUS != 'cancelled'
    ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC
");
$stmt->execute();
$pendingOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get completed orders (last 10)
$stmt = $conn->prepare("
    SELECT o.*, c.CUST_NAME as customer_name
    FROM `ORDER` o
    JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
    WHERE o.ORDER_STATUS = 'completed'
    ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC
    LIMIT 10
");
$stmt->execute();
$completedOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Cofeology</title>
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
                    <h1>Staff Dashboard</h1>
                </div>
                <div class="header-actions">
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="../logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </header>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pending Orders</h3>
                        <p id="pending-count"><?php echo count($pendingOrders); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Processing</h3>
                        <p id="processing-count">
                            <?php 
                            $processingCount = 0;
                            foreach($pendingOrders as $order) {
                                if($order['ORDER_STATUS'] === 'preparing') $processingCount++;
                            }
                            echo $processingCount;
                            ?>
                        </p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Completed Today</h3>
                        <p id="completed-count"><?php echo count($completedOrders); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Ready</h3>
                        <p id="ready-count">
                            <?php 
                            $readyCount = 0;
                            foreach($pendingOrders as $order) {
                                if($order['ORDER_STATUS'] === 'ready') $readyCount++;
                            }
                            echo $readyCount;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-main">
                <div class="card">
                    <div class="card-header">
                        <h2>Pending Orders</h2>
                        <button class="refresh-btn" onclick="refreshOrders()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pending-orders">
                                    <?php if (empty($pendingOrders)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No pending orders</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pendingOrders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['ORDER_ID']; ?></td>
                                                <td>
                                                    <div class="customer-info">
                                                        <span><?php echo htmlspecialchars($order['customer_name']); ?></span>
                                                        <small><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                                                    </div>
                                                </td>
                                                <td><?php echo ucfirst($order['ORDER_TYPE']); ?></td>
                                                <td><?php echo date('h:i A', strtotime($order['ORDER_DATE'] . ' ' . $order['ORDER_TIME'])); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo $order['ORDER_STATUS']; ?>">
                                                        <?php echo ucfirst($order['ORDER_STATUS']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatCurrency($order['TOT_AMOUNT']); ?></td>
                                                <td>
                                                    <div class="table-actions">
                                                        
                                                        <button class="btn-icon update-status" data-id="<?php echo $order['ORDER_ID']; ?>" title="Update Status">
                                                            <i class="fas fa-edit"></i>
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
                
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Completed Orders</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($completedOrders)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No completed orders</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($completedOrders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['ORDER_ID']; ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td><?php echo ucfirst($order['ORDER_TYPE']); ?></td>
                                                <td><?php echo date('d M Y, h:i A', strtotime($order['ORDER_DATE'] . ' ' . $order['ORDER_TIME'])); ?></td>
                                                <td><?php echo formatCurrency($order['TOT_AMOUNT']); ?></td>
                                                <td>
                                                    
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
    </div>
    
    <!-- Status Update Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <input type="hidden" id="order-id" name="order_id">
                    
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-control">
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status update modal
            const modal = document.getElementById('status-modal');
            const closeModal = document.querySelector('.close-modal');
            const updateButtons = document.querySelectorAll('.update-status');
            const statusForm = document.getElementById('status-form');
            
            updateButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-id');
                    document.getElementById('order-id').value = orderId;
                    modal.style.display = 'flex';
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
            
            statusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const orderId = document.getElementById('order-id').value;
                const status = document.getElementById('status').value;
                
                // Send AJAX request to update status
                fetch('../api/update-order-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modal.style.display = 'none';
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
            });
        });
        
        function refreshOrders() {
            location.reload();
        }
        
        function showNotification(message, type = 'success') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            // Add to document
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>
