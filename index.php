<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database connection (with error handling)
if(file_exists('includes/db_connect.php')) {
    include 'includes/db_connect.php';
} else {
    $conn = null;
}

// Include functions (with error handling)
if(file_exists('includes/functions.php')) {
    include 'includes/functions.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café Delights - Food Ordering System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Inline CSS as backup */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .hero {
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
            color: white;
            text-align: center;
            padding: 100px 20px;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: white;
            color: #ff6b6b;
        }
        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .features {
            padding: 80px 0;
            background-color: white;
        }
        .section-title {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            color: #2d3436;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .feature-card {
            text-align: center;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .feature-icon {
            font-size: 3rem;
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        .site-header {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo h1 {
            color: #ff6b6b;
            margin: 0;
        }
        .main-nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .main-nav li {
            margin-left: 20px;
        }
        .main-nav a {
            color: #2d3436;
            text-decoration: none;
            font-weight: 500;
        }
        .header-actions a {
            margin-left: 15px;
        }
        .site-footer {
            background-color: #2d3436;
            color: white;
            padding: 60px 0 20px;
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .footer-section h3 {
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .popular-items {
            padding: 80px 0;
            background-color: #f9f9f9;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .menu-item {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .menu-item:hover {
            transform: translateY(-5px);
        }
        .menu-item-image {
            height: 200px;
            overflow: hidden;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .menu-item-content {
            padding: 20px;
        }
        .menu-item-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .menu-item-title h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        .menu-item-price {
            color: #ff6b6b;
            font-weight: bold;
        }
        .menu-item-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .menu-item-actions {
            display: flex;
            justify-content: space-between;
        }
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php 
    if(file_exists('includes/header.php')) {
        include 'includes/header.php'; 
    } else {
        // Fallback header
        echo '<header class="site-header">
                <div class="container">
                    <div class="header-content">
                        <div class="logo">
                            <h1>Café Delights</h1>
                        </div>
                        <nav class="main-nav">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="menu.php">Menu</a></li>
                                <li><a href="login.php">Login</a></li>
                                <li><a href="register.php">Register</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
              </header>';
    }
    ?>
    
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Café Delights</h1>
                <p>Order your favorite meals online for delivery, takeaway, or dine-in</p>
                <div class="hero-buttons">
                    <a href="menu.php" class="btn btn-primary">View Menu</a>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn btn-secondary">Login / Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2 class="section-title">How It Works</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>Browse Menu</h3>
                        <p>Explore our delicious offerings and find your favorites</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Place Order</h3>
                        <p>Customize your meal and add it to your cart</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Choose Delivery Option</h3>
                        <p>Select delivery, takeaway, or dine-in</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Secure Payment</h3>
                        <p>Pay securely online with FPX</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="popular-items">
            <div class="container">
                <h2 class="section-title">Popular Items</h2>
                <div class="menu-grid" id="popular-items-container">
                    <?php
                    // Try to load popular items from database
                    if($conn && function_exists('getPopularItems')) {
                        try {
                            $popularItems = getPopularItems($conn, 4);
                            if(!empty($popularItems)) {
                                foreach($popularItems as $item) {
                                    echo '<div class="menu-item">';
                                    echo '<div class="menu-item-image">';
                                    if(!empty($item['image']) && file_exists($item['image'])) {
                                        echo '<img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '">';
                                    } else {
                                        echo '<img src="/placeholder.svg?height=200&width=280" alt="' . htmlspecialchars($item['name']) . '">';
                                    }
                                    echo '</div>';
                                    echo '<div class="menu-item-content">';
                                    echo '<div class="menu-item-title">';
                                    echo '<h3>' . htmlspecialchars($item['name']) . '</h3>';
                                    echo '<div class="menu-item-price">RM ' . number_format($item['price'], 2) . '</div>';
                                    echo '</div>';
                                    echo '<div class="menu-item-description">' . htmlspecialchars($item['description']) . '</div>';
                                    echo '<div class="menu-item-actions">';
                                    echo '<button class="btn btn-primary btn-sm" onclick="addToCart(' . $item['id'] . ', \'' . addslashes($item['name']) . '\', ' . $item['price'] . ', \'/placeholder.svg?height=200&width=280\')">Add to Cart</button>';
                                    echo '<a href="menu.php" class="btn btn-secondary btn-sm">View Menu</a>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="empty-message">';
                                echo '<p>No popular items available at the moment. <a href="menu.php">Browse our full menu</a></p>';
                                echo '</div>';
                            }
                        } catch(Exception $e) {
                            echo '<div class="empty-message">';
                            echo '<p>Unable to load popular items. <a href="menu.php">Browse our full menu</a></p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="empty-message">';
                        echo '<p>Database not connected. Please run the setup script first.</p>';
                        echo '<p><a href="scripts/setup.php" class="btn btn-primary">Run Setup</a></p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <?php 
    if(file_exists('includes/footer.php')) {
        include 'includes/footer.php'; 
    } else {
        // Fallback footer
        echo '<footer class="site-footer">
                <div class="container">
                    <div class="footer-content">
                        <div class="footer-section">
                            <h3>Café Delights</h3>
                            <p>Enjoy delicious meals delivered to your doorstep.</p>
                        </div>
                    </div>
                    <div class="footer-bottom">
                        <p>&copy; ' . date('Y') . ' Café Delights. All rights reserved.</p>
                    </div>
                </div>
              </footer>';
    }
    ?>
    
    <script>
        console.log('Page loaded successfully!');
        
        // Basic cart functionality
        if (!localStorage.getItem("cart")) {
            localStorage.setItem("cart", JSON.stringify([]));
        }
        
        function updateCartCount() {
            const cartCount = document.getElementById("cart-count");
            if (cartCount) {
                const cart = JSON.parse(localStorage.getItem("cart")) || [];
                const count = cart.reduce((total, item) => total + item.quantity, 0);
                cartCount.textContent = count;
            }
        }
        
        function addToCart(itemId, name, price, image, quantity = 1) {
            const cart = JSON.parse(localStorage.getItem("cart")) || [];
            const existingItem = cart.find(item => item.id === itemId);
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    id: itemId,
                    name: name,
                    price: price,
                    image: image,
                    quantity: quantity
                });
            }
            
            localStorage.setItem("cart", JSON.stringify(cart));
            updateCartCount();
            
            // Show notification
            showNotification(name + ' added to cart!');
        }
        
        function showNotification(message) {
            // Simple notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #4CAF50;
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 3000);
        }
        
        updateCartCount();
    </script>
</body>
</html>
