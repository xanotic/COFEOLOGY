<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Redirect if not logged in or not customer
if (!isLoggedIn() || !isCustomer()) {
    header("Location: login.php");
    exit();
}

// Get customer orders
$orders = getCustomerOrders($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Cofeology</title>
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
                                        <h3>Order #<?php echo $order['ORDER_ID']; ?></h3>
                                        <p class="order-date"><?php echo date('d M Y, h:i A', strtotime($order['ORDER_DATE'] . ' ' . $order['ORDER_TIME'])); ?></p>
                                    </div>
                                    <div class="order-status <?php echo $order['ORDER_STATUS']; ?>">
                                        <?php echo ucfirst($order['ORDER_STATUS']); ?>
                                    </div>
                                </div>
                                
                                <div class="order-details">
                                    <div class="order-type">
                                        <i class="fas <?php echo $order['ORDER_TYPE'] === 'delivery' ? 'fa-truck' : ($order['ORDER_TYPE'] === 'takeaway' ? 'fa-shopping-bag' : 'fa-utensils'); ?>"></i>
                                        <span><?php echo ucfirst($order['ORDER_TYPE']); ?></span>
                                    </div>
                                    
                                    <?php if ($order['ORDER_TYPE'] === 'delivery'): ?>
                                        <div class="order-address">
                                            <strong>Delivery Address:</strong>
                                            <p><?php echo htmlspecialchars($order['DELIVERY_ADDRESS']); ?></p>
                                        </div>
                                    <?php elseif ($order['ORDER_TYPE'] === 'takeaway' || $order['ORDER_TYPE'] === 'dine-in'): ?>
                                        <?php if ($order['pickup_time']): ?>
                                            <div class="order-pickup">
                                                <strong>Pickup Time:</strong>
                                                <p><?php echo date('d M Y, h:i A', strtotime($order['pickup_time'])); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <div class="order-items">
                                        <?php
                                        $orderListings = getOrderListings($conn, $order['ORDER_ID']);
                                        foreach ($orderListings as $item):
                                        ?>
                                            <div class="order-item">
                                                <div class="order-item-image">
                                                    <img src="<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : '/placeholder.svg?height=60&width=60'; ?>" alt="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>">
                                                </div>
                                                <div class="order-item-details">
                                                    <h4><?php echo htmlspecialchars($item['ITEM_NAME']); ?></h4>
                                                    <p><?php echo $item['ORDER_QUANTITY']; ?> x <?php echo formatCurrency($item['item_price']); ?></p>
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
                                        <span><?php echo formatCurrency($order['TOT_AMOUNT']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="order-actions">
                                    <a href="order-details.php?id=<?php echo $order['ORDER_ID']; ?>" class="btn btn-outline btn-sm">View Details</a>
                                    <?php if ($order['ORDER_STATUS'] === 'pending'): ?>
                                        <button class="btn btn-danger btn-sm" onclick="cancelOrder(<?php echo $order['ORDER_ID']; ?>)">Cancel Order</button>
                                    <?php endif; ?>
                                    <?php if ($order['ORDER_STATUS'] === 'completed'): ?>
                                        <button class="btn btn-secondary btn-sm" onclick="reorderItems(<?php echo $order['ORDER_ID']; ?>)">Reorder</button>
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
        
        function reorderItems(orderId) {
            // Get order items and add to cart
            fetch(`api/get-order-items.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear current cart
                    clearCart();
                    
                    // Add items to cart
                    data.items.forEach(item => {
                        addToCart(item.ITEM_ID, item.ITEM_NAME, item.item_price, item.image, item.ORDER_QUANTITY);
                    });
                    
                    showNotification('Items added to cart successfully');
                    
                    // Redirect to cart after a short delay
                    setTimeout(() => {
                        window.location.href = 'cart.php';
                    }, 1000);
                } else {
                    showNotification(data.message || 'Failed to reorder items', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            });
        }
    </script>
    
    <style>
        .orders-section {
            padding: 50px 0;
        }
        
        .empty-orders {
            text-align: center;
            padding: 50px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .empty-orders i {
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-orders h2 {
            margin-bottom: 15px;
        }
        
        .empty-orders p {
            color: #666;
            margin-bottom: 30px;
        }
        
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .order-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .order-info h3 {
            margin: 0;
            color: #333;
        }
        
        .order-date {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .order-status.pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .order-status.preparing {
            background-color: rgba(0, 123, 255, 0.2);
            color: #004085;
        }
        
        .order-status.ready {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .order-status.out_for_delivery {
            background-color: rgba(255, 87, 34, 0.2);
            color: #bf360c;
        }
        
        .order-status.completed {
            background-color: rgba(76, 175, 80, 0.2);
            color: #2e7d32;
        }
        
        .order-status.cancelled {
            background-color: rgba(244, 67, 54, 0.2);
            color: #c62828;
        }
        
        .order-type {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .order-type i {
            color: #ff6b6b;
        }
        
        .order-address, .order-pickup, .order-instructions {
            margin-bottom: 15px;
        }
        
        .order-items {
            margin-bottom: 15px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .order-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .order-item-details h4 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        
        .order-item-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .order-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .order-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
