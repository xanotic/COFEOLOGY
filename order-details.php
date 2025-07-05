<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'] ?? 0;

if (!$order_id) {
    header("Location: my-orders.php");
    exit();
}

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, c.CUST_NAME, c.CUST_EMAIL, c.CUST_NPHONE, c.MEMBERSHIP 
    FROM `ORDER` o 
    JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID 
    WHERE o.ORDER_ID = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: my-orders.php?error=order_not_found");
    exit();
}

// Check if user has permission to view this order
if (!isAdmin() && !isStaff() && $order['CUST_ID'] != $_SESSION['user_id']) {
    header("Location: my-orders.php?error=unauthorized");
    exit();
}

// Get order items
$stmt = $conn->prepare("
    SELECT ol.*, mi.ITEM_NAME, mi.ITEM_DESCRIPTION, mi.image 
    FROM ORDER_LISTING ol
    JOIN MENU_ITEM mi ON ol.ITEM_ID = mi.ITEM_ID
    WHERE ol.ORDER_ID = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate discount if applicable
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += ($item['item_price'] ?? $item['ITEM_PRICE'] ?? 0) * ($item['ORDER_QUANTITY'] ?? 1);
}

$discount_rate = 0;
$discount_percentage = '0%';
if ($order['MEMBERSHIP'] === 'premium') {
    $discount_rate = 0.10;
    $discount_percentage = '10%';
} elseif ($order['MEMBERSHIP'] === 'vip') {
    $discount_rate = 0.20;
    $discount_percentage = '20%';
}

$discount_amount = $subtotal * $discount_rate;
$delivery_fee = ($order['ORDER_TYPE'] === 'delivery') ? 5.00 : 0;
$total = $subtotal - $discount_amount + $delivery_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Cofeology</title>
    <link rel="stylesheet" href="css/style.css?v=1.1">
    <style>
        .order-details-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .order-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .order-header h1 {
            margin: 0 0 1rem 0;
            font-size: 2rem;
        }
        
        .order-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .order-meta-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 8px;
        }
        
        .order-meta-item strong {
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending { background: #ffc107; color: #000; }
        .status-preparing { background: #17a2b8; color: white; }
        .status-ready { background: #28a745; color: white; }
        .status-completed { background: #6f42c1; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        
        .order-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .order-section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #ff6b6b;
            padding-bottom: 0.5rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .item-price {
            color: #ff6b6b;
            font-weight: bold;
        }
        
        .item-quantity {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin-left: 1rem;
        }
        
        .customer-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .info-group p {
            margin: 0.5rem 0;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2rem;
            color: #ff6b6b;
        }
        
        .discount-row {
            color: #28a745;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            background: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 2rem;
            transition: background 0.3s;
        }
        
        .back-button:hover {
            background: #5a6268;
            color: white;
        }
        
        .back-button i {
            margin-right: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .order-meta {
                grid-template-columns: 1fr;
            }
            
            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .item-image {
                margin-bottom: 1rem;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <div class="order-details-container">
            <a href="my-orders.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>
            
            <div class="order-header">
                <h1>Order #<?php echo htmlspecialchars($order['ORDER_NUMBER'] ?? $order['ORDER_ID']); ?></h1>
                <div class="order-meta">
                    <div class="order-meta-item">
                        <strong>Status</strong>
                        <span class="status-badge status-<?php echo $order['ORDER_STATUS'] ?? 'pending'; ?>">
                            <?php echo ucfirst($order['ORDER_STATUS'] ?? 'Pending'); ?>
                        </span>
                    </div>
                    <div class="order-meta-item">
                        <strong>Order Type</strong>
                        <i class="fas fa-<?php echo $order['ORDER_TYPE'] === 'delivery' ? 'truck' : ($order['ORDER_TYPE'] === 'takeaway' ? 'shopping-bag' : 'utensils'); ?>"></i>
                        <?php echo ucfirst($order['ORDER_TYPE']); ?>
                    </div>
                    <div class="order-meta-item">
                        <strong>Date & Time</strong>
                        <?php echo date('M d, Y', strtotime($order['ORDER_DATE'])); ?><br>
                        <?php echo date('h:i A', strtotime($order['ORDER_TIME'])); ?>
                    </div>
                    <div class="order-meta-item">
                        <strong>Total Amount</strong>
                        RM <?php echo number_format($order['TOT_AMOUNT'], 2); ?>
                    </div>
                </div>
            </div>
            
            <div class="order-section">
                <h2><i class="fas fa-shopping-cart"></i> Order Items</h2>
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/80x80/ff6b6b/ffffff?text=Item'); ?>" 
                             alt="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>" 
                             class="item-image">
                        <div class="item-details">
                            <div class="item-name"><?php echo htmlspecialchars($item['ITEM_NAME']); ?></div>
                            <div class="item-price">RM <?php echo number_format($item['item_price'] ?? $item['ITEM_PRICE'] ?? 0, 2); ?></div>
                            <?php if (!empty($item['special_requests'])): ?>
                                <div class="item-special">
                                    <small><strong>Special Request:</strong> <?php echo htmlspecialchars($item['special_requests']); ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="item-quantity">
                            Qty: <?php echo $item['ORDER_QUANTITY'] ?? 1; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-section">
                <h2><i class="fas fa-user"></i> Customer Information</h2>
                <div class="customer-info">
                    <div class="info-group">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['CUST_NAME']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['CUST_EMAIL']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['CUST_NPHONE']); ?></p>
                        <p><strong>Membership:</strong> <?php echo ucfirst($order['MEMBERSHIP']); ?></p>
                    </div>
                    <div class="info-group">
                        <?php if ($order['ORDER_TYPE'] === 'delivery' && !empty($order['DELIVERY_ADDRESS'])): ?>
                            <p><strong>Delivery Address:</strong><br><?php echo nl2br(htmlspecialchars($order['DELIVERY_ADDRESS'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($order['pickup_time'])): ?>
                            <p><strong>Pickup Time:</strong> <?php echo date('M d, Y h:i A', strtotime($order['pickup_time'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($order['special_instructions'])): ?>
                            <p><strong>Special Instructions:</strong><br><?php echo nl2br(htmlspecialchars($order['special_instructions'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="order-section">
                <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>RM <?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <?php if ($discount_amount > 0): ?>
                    <div class="summary-row discount-row">
                        <span>Membership Discount (<?php echo $discount_percentage; ?>)</span>
                        <span>-RM <?php echo number_format($discount_amount, 2); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($delivery_fee > 0): ?>
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span>RM <?php echo number_format($delivery_fee, 2); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="summary-row">
                    <span>Total</span>
                    <span>RM <?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
