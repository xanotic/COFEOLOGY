<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: my-orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Get order details using the correct table name and field names
$stmt = $conn->prepare("SELECT * FROM `ORDER` WHERE ORDER_ID = ? AND CUST_ID = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my-orders.php");
    exit();
}

$order = $result->fetch_assoc();

// Process payment
$payment_success = false;
$payment_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($payment_method) || $payment_method !== 'fpx') {
        $payment_error = "Please select FPX Online Banking as payment method";
    } else {
        $stmt = $conn->prepare("UPDATE `ORDER` SET PAYMENT_STATUS = 'completed', PAYMENT_METHOD = ? WHERE ORDER_ID = ?");
        $stmt->bind_param("si", $payment_method, $order_id);
        
        if ($stmt->execute()) {
            // Log activity
            logActivity($conn, $_SESSION['user_id'], $_SESSION['user_type'], 'payment_completed', "Payment completed for order #$order_id");
            
            $payment_success = true;
        } else {
            $payment_error = "Payment processing failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Café Delights</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Payment</h1>
            </div>
        </section>
        
        <section class="payment-section">
            <div class="container">
                <?php if ($payment_success): ?>
                    <div class="payment-success">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Payment Successful!</h2>
                        <p>Your order has been placed successfully.</p>
                        <p>Order #: <?php echo $order_id; ?></p>
                        <div class="payment-actions">
                            <a href="my-orders.php" class="btn btn-primary">View My Orders</a>
                            <a href="index.php" class="btn btn-outline">Back to Home</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($payment_error): ?>
                        <div class="alert alert-danger">
                            <?php echo $payment_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="payment-grid">
                        <div class="payment-form">
                            <h2>Payment Details</h2>
                            <form method="post" action="payment.php?order_id=<?php echo $order_id; ?>">
                                <div class="form-group">
                                    <label class="form-label">Payment Method</label>
                                    <div class="payment-methods">
                                        <input type="radio" name="payment_method" value="fpx" required checked>
                                        <span class="payment-method-icon">
                                            <i class="fas fa-university"></i>
                                        </span>
                                        <span>FPX Online Banking</span>
                                    </div>
                                </div>
                                
                                <div id="fpx-section" class="payment-section-details">
                                    <div class="form-group">
                                        <label for="bank" class="form-label">Select Bank</label>
                                        <select id="bank" name="bank" class="form-control">
                                            <option value="">-- Select Bank --</option>
                                            <option value="maybank">Maybank</option>
                                            <option value="cimb">CIMB Bank</option>
                                            <option value="public">Public Bank</option>
                                            <option value="rhb">RHB Bank</option>
                                            <option value="hong_leong">Hong Leong Bank</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Pay Now</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="order-summary">
                            <h2>Order Summary</h2>
                            <div class="order-details">
                                <div class="order-info">
                                    <p><strong>Order #:</strong> <?php echo $order_id; ?></p>
                                    <p><strong>Order Type:</strong> <?php echo ucfirst($order['ORDER_TYPE']); ?></p>
                                    <?php if ($order['ORDER_TYPE'] === 'delivery'): ?>
                                        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['DELIVERY_ADDRESS'] ?? ''); ?></p>
                                    <?php elseif ($order['ORDER_TYPE'] === 'takeaway' || $order['ORDER_TYPE'] === 'dine-in'): ?>
                                        <?php if ($order['pickup_time']): ?>
                                            <p><strong>Pickup Time:</strong> <?php echo date('d M Y, h:i A', strtotime($order['pickup_time'])); ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="order-items">
                                    <?php
                                    $orderListings = getOrderListings($conn, $order_id);
                                    foreach ($orderListings as $item):
                                    ?>
                                        <div class="order-item">
                                            <div class="order-item-details">
                                                <h3><?php echo htmlspecialchars($item['ITEM_NAME'] ?? 'Unknown Item'); ?></h3>
                                                <p><?php echo $item['ORDER_QUANTITY']; ?> x <?php echo formatCurrency($item['item_price']); ?></p>
                                            </div>
                                            <div class="order-item-total">
                                                <?php echo formatCurrency($item['item_price'] * $item['ORDER_QUANTITY']); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="order-totals">
                                    <div class="summary-row">
                                        <span>Subtotal</span>
                                        <span><?php echo formatCurrency($order['TOT_AMOUNT'] - ($order['ORDER_TYPE'] === 'delivery' ? 5 : 0)); ?></span>
                                    </div>
                                    <?php if ($order['ORDER_TYPE'] === 'delivery'): ?>
                                        <div class="summary-row">
                                            <span>Delivery Fee</span>
                                            <span><?php echo formatCurrency(5); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="summary-row summary-total">
                                        <span>Total</span>
                                        <span><?php echo formatCurrency($order['TOT_AMOUNT']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show FPX section by default since it's the only option
            const fpxSection = document.getElementById('fpx-section');
            if (fpxSection) {
                fpxSection.style.display = 'block';
            }
            
            <?php if ($payment_success): ?>
            // Clear cart after successful payment
            localStorage.removeItem('cart');
            updateCartCount();
            <?php endif; ?>
        });
    </script>
</body>
</html>
