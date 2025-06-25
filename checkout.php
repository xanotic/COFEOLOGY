<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Redirect if not logged in or not a customer
if (!isLoggedIn() || !isCustomer()) {
    $_SESSION['redirect_url'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// Get customer information
$stmt = $conn->prepare("SELECT * FROM CUSTOMER WHERE CUST_ID = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

$errors = [];
$success = false;

// Process checkout form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_type = $_POST['order_type'] ?? '';
    $delivery_address = $_POST['delivery_address'] ?? '';
    $pickup_time = null;
    
    if ($order_type === 'takeaway' || $order_type === 'dine-in') {
        $pickup_date = $_POST['pickup_date'] ?? '';
        $pickup_time_val = $_POST['pickup_time'] ?? '';
        if (!empty($pickup_date) && !empty($pickup_time_val)) {
            $pickup_time = $pickup_date . ' ' . $pickup_time_val;
        }
    }
    
    $special_instructions = $_POST['special_instructions'] ?? '';
    $cart_items = json_decode($_POST['cart_items'], true);
    $total_amount = floatval($_POST['total_amount']);
    
    // Validation
    if (empty($order_type)) {
        $errors[] = "Please select an order type";
    }
    
    if ($order_type === 'delivery' && empty($delivery_address)) {
        $errors[] = "Please provide a delivery address";
    }
    
    if (($order_type === 'takeaway' || $order_type === 'dine-in') && empty($pickup_time)) {
        $errors[] = "Please select a pickup date and time";
    }
    
    if (empty($cart_items)) {
        $errors[] = "Your cart is empty";
    }
    
    // If no errors, create order
    if (empty($errors)) {
        $conn->begin_transaction();
        
        try {
            // Create order
            $order_id = createOrder(
                $conn, 
                $_SESSION['user_id'], 
                $order_type, 
                $total_amount,
                $delivery_address, 
                $pickup_time, 
                $special_instructions
            );
            
            if ($order_id) {
                // Add order listings
                foreach ($cart_items as $item) {
                    addOrderListing(
                        $conn, 
                        $order_id, 
                        $item['id'], 
                        $item['quantity'], 
                        $item['price'], 
                        $item['special_requests'] ?? null
                    );
                    
                    // Update stock level
                    updateStockLevel($conn, $item['id'], $item['quantity']);
                }
                
                // Log activity
                logActivity($conn, $_SESSION['user_id'], 'customer', 'order_placed', "Order #$order_id placed");
                
                $conn->commit();
                
                // Redirect to payment page
                header("Location: payment.php?order_id=$order_id");
                exit();
            } else {
                throw new Exception("Failed to create order");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Café Delights</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Checkout</h1>
            </div>
        </section>
        
        <section class="checkout-section">
            <div class="container">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="checkout-grid">
                    <div class="checkout-form">
                        <h2>Order Details</h2>
                        <form id="checkout-form" method="post" action="checkout.php">
                            <div class="form-group">
                                <label class="form-label">Order Type</label>
                                <div class="order-type-options">
                                    <label class="order-type-option">
                                        <input type="radio" name="order_type" value="delivery" required>
                                        <i class="fas fa-truck"></i>
                                        <span>Delivery</span>
                                    </label>
                                    <label class="order-type-option">
                                        <input type="radio" name="order_type" value="takeaway">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span>Takeaway</span>
                                    </label>
                                    <label class="order-type-option">
                                        <input type="radio" name="order_type" value="dine-in">
                                        <i class="fas fa-utensils"></i>
                                        <span>Dine-in</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div id="delivery-address-section" class="form-group" style="display: none;">
                                <label for="delivery_address" class="form-label">Delivery Address</label>
                                <textarea id="delivery_address" name="delivery_address" class="form-control" rows="3"><?php echo htmlspecialchars($customer['CUST_ADDRESS'] ?? ''); ?></textarea>
                            </div>
                            
                            <div id="pickup-time-section" class="form-group" style="display: none;">
                                <label class="form-label">Pickup/Reservation Date & Time</label>
                                <div class="pickup-time-inputs">
                                    <input type="date" id="pickup_date" name="pickup_date" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                                    <input type="time" id="pickup_time" name="pickup_time" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="special_instructions" class="form-label">Special Instructions</label>
                                <textarea id="special_instructions" name="special_instructions" class="form-control" rows="3" placeholder="Any special requests or notes for your order?"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Contact Information</label>
                                <div class="contact-info">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['CUST_NAME']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['CUST_EMAIL']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['CUST_NPHONE']); ?></p>
                                    <p><strong>Membership:</strong> <?php echo ucfirst($customer['MEMBERSHIP']); ?></p>
                                </div>
                            </div>
                            
                            <input type="hidden" id="cart_items" name="cart_items">
                            <input type="hidden" id="total_amount" name="total_amount">
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="order-summary">
                        <h2>Order Summary</h2>
                        <div id="checkout-items" class="checkout-items">
                            <!-- Cart items will be loaded here via JavaScript -->
                        </div>
                        
                        <div class="summary-totals">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span id="checkout-subtotal">RM 0.00</span>
                            </div>
                            <div class="summary-row" id="delivery-fee-row">
                                <span>Delivery Fee</span>
                                <span>RM 5.00</span>
                            </div>
                            <div class="summary-row" id="membership-discount-row" style="display: none;">
                                <span>Membership Discount</span>
                                <span id="discount-amount">-RM 0.00</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total</span>
                                <span id="checkout-total">RM 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCheckoutItems();
            
            // Order type selection
            const orderTypeInputs = document.querySelectorAll('input[name="order_type"]');
            const deliveryAddressSection = document.getElementById('delivery-address-section');
            const pickupTimeSection = document.getElementById('pickup-time-section');
            const deliveryFeeRow = document.getElementById('delivery-fee-row');
            const membershipDiscountRow = document.getElementById('membership-discount-row');
            
            orderTypeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.value === 'delivery') {
                        deliveryAddressSection.style.display = 'block';
                        pickupTimeSection.style.display = 'none';
                        deliveryFeeRow.style.display = 'flex';
                        membershipDiscountRow.style.display = 'none';
                        updateTotal(true);
                    } else {
                        deliveryAddressSection.style.display = 'none';
                        pickupTimeSection.style.display = 'block';
                        deliveryFeeRow.style.display = 'none';
                        membershipDiscountRow.style.display = 'none';
                        updateTotal(false);
                    }
                });
            });
            
            // Set minimum date for pickup
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickup_date').min = today;
            
            // Form submission
            document.getElementById('checkout-form').addEventListener('submit', function(e) {
                const orderType = document.querySelector('input[name="order_type"]:checked')?.value;
                
                if (!orderType) {
                    e.preventDefault();
                    alert('Please select an order type');
                    return;
                }
                
                if (orderType === 'delivery') {
                    const deliveryAddress = document.getElementById('delivery_address').value.trim();
                    if (!deliveryAddress) {
                        e.preventDefault();
                        alert('Please provide a delivery address');
                        return;
                    }
                } else {
                    const pickupDate = document.getElementById('pickup_date').value;
                    const pickupTime = document.getElementById('pickup_time').value;
                    if (!pickupDate || !pickupTime) {
                        e.preventDefault();
                        alert('Please select a pickup date and time');
                        return;
                    }
                }
            });
        });
        
        function loadCheckoutItems() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const checkoutItems = document.getElementById('checkout-items');
            
            if (cart.length === 0) {
                window.location.href = 'cart.php';
                return;
            }
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                html += `
                    <div class="checkout-item">
                        <div class="checkout-item-image">
                            <img src="${item.image}" alt="${item.name}">
                        </div>
                        <div class="checkout-item-details">
                            <h3>${item.name}</h3>
                            <div class="checkout-item-price">${formatCurrency(item.price)} x ${item.quantity}</div>
                        </div>
                        <div class="checkout-item-total">${formatCurrency(itemTotal)}</div>
                    </div>
                `;
            });
            
            checkoutItems.innerHTML = html;
            
            // Update totals
            document.getElementById('checkout-subtotal').textContent = formatCurrency(subtotal);
            
            // Apply membership discount
            const membershipType = '<?php echo $customer['MEMBERSHIP']; ?>';
            let discountRate = 0;
            if (membershipType === 'premium') {
                discountRate = 0.10; // 10% discount
            } else if (membershipType === 'vip') {
                discountRate = 0.20; // 20% discount
            }
            
            if (discountRate > 0) {
                const discountAmount = subtotal * discountRate;
                document.getElementById('membership-discount-row').style.display = 'flex';
                document.getElementById('discount-amount').textContent = '-' + formatCurrency(discountAmount);
                subtotal -= discountAmount;
            }
            
            updateTotal(true); // Default to delivery
            
            // Set hidden inputs
            document.getElementById('cart_items').value = JSON.stringify(cart);
        }
        
        function updateTotal(includeDeliveryFee) {
            const subtotalText = document.getElementById('checkout-subtotal').textContent.replace('RM ', '');
            const discountText = document.getElementById('discount-amount').textContent.replace('-RM ', '');
            
            let subtotal = parseFloat(subtotalText);
            const discount = parseFloat(discountText) || 0;
            
            // Apply discount to subtotal
            subtotal -= discount;
            
            const deliveryFee = includeDeliveryFee ? 5 : 0;
            const total = subtotal + deliveryFee;
            
            document.getElementById('checkout-total').textContent = formatCurrency(total);
            document.getElementById('total_amount').value = total;
        }
        
        function formatCurrency(amount) {
            return 'RM ' + parseFloat(amount).toFixed(2);
        }
    </script>
</body>
</html>
