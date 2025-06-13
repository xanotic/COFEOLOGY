<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get user orders
$orders = getUserOrders($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Café Delights</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>My Orders</h1>
            </div>
        </section>
        
        <section class="orders-section">
            <div class="container">
                <?php if (empty($orders)): ?>
                    <div class="empty-orders">
                        <i class="fas fa-shopping-bag fa-4x"></i>
                        <h2>No orders yet</h2>
                        <p>You haven't placed any orders yet.</p>
                        <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h3>Order #<?php echo $order['id']; ?></h3>
                                        <p class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                                    </div>
                                    <div class="order-status <?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </div>
                                </div>
                                
                                <div class="order-details">
                                    <div class="order-type">
                                        <i class="fas <?php echo $order['order_type'] === 'delivery' ? 'fa-truck' : ($order['order_type'] === 'takeaway' ? 'fa-shopping-bag' : 'fa-utensils'); ?>"></i>
                                        <span><?php echo ucfirst($order['order_type']); ?></span>
                                    </div>
                                    
                                    <?php if ($order['order_type'] === 'delivery'): ?>
                                        <div class="order-address">
                                            <strong>Delivery Address:</strong>
                                            <p><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                                        </div>
                                    <?php elseif ($order['order_type'] === 'takeaway' || $order['order_type'] === 'dine-in'): ?>
                                        <div class="order-pickup">
                                            <strong>Pickup Time:</strong>
                                            <p><?php echo date('d M Y, h:i A', strtotime($order['pickup_time'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="order-items">
                                        <?php
                                        $orderDetails = getOrderDetails($conn, $order['id']);
                                        foreach ($orderDetails as $item):
                                        ?>
                                            <div class="order-item">
                                                <div class="order-item-image">
                                                    <img src="<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                </div>
                                                <div class="order-item-details">
                                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                                    <p><?php echo $item['quantity']; ?> x <?php echo formatCurrency($item['price']); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (!empty($order['special_instructions'])): ?>
                                        <div class="order-instructions">
                                            <strong>Special Instructions:</strong>
                                            <p><?php echo htmlspecialchars($order['special_instructions']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="order-total">
                                        <strong>Total:</strong>
                                        <span><?php echo formatCurrency($order['total_amount']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="order-actions">
                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline btn-sm">View Details</a>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $order['id']; ?>)">Cancel Order</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                // Send AJAX request to cancel order
                fetch('api/cancel-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order_id: orderId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Order cancelled successfully');
                        // Reload page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message || 'Failed to cancel order', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            }
        }
    </script>
</body>
</html>
