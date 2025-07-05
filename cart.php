<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Check if user is logged in for checkout
$canCheckout = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Cofeology</title>
    <link rel="stylesheet" href="css/style.css?">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Your Shopping Cart</h1>
            </div>
        </section>
        
        <section class="cart-container">
            <div class="container">
                <div id="cart-content">
                    <!-- Cart content will be loaded via JavaScript -->
                    <div class="loading">Loading cart...</div>
                </div>
                
                <div class="cart-actions" id="cart-actions" style="display: none;">
                    <button class="btn btn-outline" onclick="clearCart()">Clear Cart</button>
                    <?php if ($canCheckout): ?>
                        <button class="btn btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
        });
        
        function loadCart() {
            const cart = getCart();
            const cartContent = document.getElementById('cart-content');
            const cartActions = document.getElementById('cart-actions');
            
            if (cart.length === 0) {
                cartContent.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart fa-4x"></i>
                        <h2>Your cart is empty</h2>
                        <p>Looks like you haven't added any items to your cart yet.</p>
                        <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                    </div>
                `;
                cartActions.style.display = 'none';
            } else {
                let html = `
                    <div class="cart-items">
                        <div class="cart-header">
                            <div>Image</div>
                            <div>Item</div>
                            <div>Price</div>
                            <div>Quantity</div>
                            <div></div>
                        </div>
                `;
                
                cart.forEach(item => {
                    html += `
                        <div class="cart-item" data-id="${item.id}">
                            <div class="cart-item-image">
                                <img src="${item.image}" alt="${item.name}">
                            </div>
                            <div class="cart-item-details">
                                <h3>${item.name}</h3>
                            </div>
                            <div class="cart-item-price">${formatCurrency(item.price)}</div>
                            <div class="cart-item-quantity">
                                <button class="quantity-btn" onclick="updateCartItemQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                                <button class="quantity-btn" onclick="updateCartItemQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                            <div class="cart-item-remove" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                    `;
                });
                
                html += `</div>`;
                
                // Add cart summary
                const total = calculateCartTotal();
                
                html += `
                    <div class="cart-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>${formatCurrency(total)}</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span>${formatCurrency(5)}</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span>${formatCurrency(total + 5)}</span>
                        </div>
                    </div>
                `;
                
                cartContent.innerHTML = html;
                cartActions.style.display = 'flex';
            }
        }
        
        function formatCurrency(amount) {
            return 'RM ' + parseFloat(amount).toFixed(2);
        }
        
        function proceedToCheckout() {
            window.location.href = 'checkout.php';
        }
        
        // Override cart functions to update UI
        const originalRemoveFromCart = removeFromCart;
        removeFromCart = function(itemId) {
            originalRemoveFromCart(itemId);
            loadCart();
        };
        
        const originalUpdateCartItemQuantity = updateCartItemQuantity;
        updateCartItemQuantity = function(itemId, quantity) {
            originalUpdateCartItemQuantity(itemId, quantity);
            loadCart();
        };
        
        const originalClearCart = clearCart;
        clearCart = function() {
            if (confirm('Are you sure you want to clear your cart?')) {
                originalClearCart();
                loadCart();
            }
        };
    </script>
</body>
</html>
