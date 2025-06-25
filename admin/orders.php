<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Get all orders
$stmt = $conn->prepare("
    SELECT o.*, c.CUST_NAME as customer_name, c.CUST_EMAIL as customer_email, c.CUST_NPHONE as customer_phone
    FROM `ORDER` o
    JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
    ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC
");
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                    <button class="btn btn-primary" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </header>
            
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
                        <input type="date" id="date-filter" class="form-control">
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
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table">
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr data-status="<?php echo $order['ORDER_STATUS']; ?>" data-date="<?php echo $order['ORDER_DATE']; ?>">
                                            <td>#<?php echo $order['ORDER_ID']; ?></td>
                                            <td>
                                                <div class="customer-info">
                                                    <span><?php echo htmlspecialchars($order['customer_name']); ?></span>
                                                    <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                            <td><?php echo ucfirst($order['ORDER_TYPE']); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $order['ORDER_STATUS']; ?>">
                                                    <?php echo ucfirst($order['ORDER_STATUS']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="payment-badge <?php echo $order['payment_status']; ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatCurrency($order['TOT_AMOUNT']); ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($order['ORDER_DATE'] . ' ' . $order['ORDER_TIME'])); ?></td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="order-details.php?id=<?php echo $order['ORDER_ID']; ?>" class="btn-icon" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
            // Filter functionality
            const statusFilter = document.getElementById('status-filter');
            const dateFilter = document.getElementById('date-filter');
            const ordersTable = document.getElementById('orders-table');
            
            function filterOrders() {
                const statusValue = statusFilter.value;
                const dateValue = dateFilter.value;
                const rows = ordersTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    const rowDate = row.getAttribute('data-date');
                    
                    let showRow = true;
                    
                    if (statusValue && rowStatus !== statusValue) {
                        showRow = false;
                    }
                    
                    if (dateValue && rowDate !== dateValue) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            statusFilter.addEventListener('change', filterOrders);
            dateFilter.addEventListener('change', filterOrders);
            
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
        
        function exportOrders() {
            window.open('../api/export-orders.php', '_blank');
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
    </script>
</body>
</html>
