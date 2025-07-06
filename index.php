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
    <title>Cofeology - Food Ordering System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Inline CSS as backup */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgb(247, 247, 247);
        }
        
        .hero {
            /* Background GIF */
            background-image: url('https://animesher.com/orig/2/205/2059/20591/animesher.com_food-aesthetic-gif-2059113.gif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            color: white;
            text-align: center;
            padding: 100px 20px;
            opacity: 0.9; /* transparency */
        }
        
        
        .hero-content {
            position: relative;
            z-index: 2;
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
            background-color:rgb(185, 78, 78);
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
        .hero h1, .hero p {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
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
                            <h1>Cofeology</h1>
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
                <h1 class="cafe-name">Welcome to ⋆ COFEOLOGY ⋆</h1>
                <p>Order your favorite meals online for delivery, takeaway, or dine-in!</p>
                <div class="hero-buttons">
                    <a href="menu.php" class="btn btn-primary">View Menu</a>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn btn-secondary">Log In / Register</a>
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
                        <p>Select Delivery, Takeaway, or Dine-In</p>
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
                <h2 class="section-title" style="color: #FF6B6B; font-size: 2.2rem; font-weight: 700; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 4px solid #FF6B6B; text-align: center; position: relative;">
                    Popular Items
                    <div style="content: ''; position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%); width: 100px; height: 4px; background: linear-gradient(90deg, #FF6B6B); border-radius: 2px;"></div>
                </h2>
                <div class="menu-grid" id="popular-items-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; padding: 0 20px;">
                    <?php
                    // Determine user type - check all possible session variables
                    $is_admin = isset($_SESSION['admin_id']) || isset($_SESSION['admin_logged_in']) || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
                    $is_staff = isset($_SESSION['staff_id']) || isset($_SESSION['staff_logged_in']) || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'staff') || (isset($_SESSION['role']) && $_SESSION['role'] === 'staff');
                    $is_customer = isset($_SESSION['user_id']) && !$is_admin && !$is_staff;
                    $is_guest = !isset($_SESSION['user_id']) && !$is_admin && !$is_staff;
                    
                    // Try to load popular items from database
                    if($conn && function_exists('getPopularItems')) {
                        try {
                            $popularItems = getPopularItems($conn, 4);
                            if(!empty($popularItems)) {
                                foreach($popularItems as $item) {
                                    // Ensure all fields exist with defaults
                                    $itemId = $item['id'] ?? 0;
                                    $itemName = $item['name'] ?? 'Unknown Item';
                                    $itemDescription = $item['description'] ?? 'No description available';
                                    $itemPrice = $item['price'] ?? 0.00;
                                    $itemImage = $item['image'] ?? '';

                                    // Use placeholder if no image or if image is empty
                                    if(empty($itemImage)) {
                                        $itemImage = 'https://via.placeholder.com/320x220/8B4513/ffffff?text=' . urlencode($itemName);
                                    }
                                    
                                    echo '<div class="menu-item-card" style="background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;">';
                                    echo '<div class="item-image" style="position: relative; height: 220px; overflow: hidden;">';
                                    echo '<img src="' . htmlspecialchars($itemImage) . '" alt="' . htmlspecialchars($itemName) . '" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" onerror="this.src=\'https://via.placeholder.com/320x220/8B4513/ffffff?text=' . urlencode($itemName) . '\'">';
                                    echo '</div>';
                                    echo '<div class="item-details" style="padding: 20px;">';
                                    echo '<div class="item-name" style="font-size: 1.3rem; font-weight: 600; color: #333; margin-bottom: 10px;">' . htmlspecialchars($itemName) . '</div>';
                                    echo '<div class="item-description" style="color: #666; font-size: 0.95rem; line-height: 1.5; margin-bottom: 15px; height: 45px; overflow: hidden; text-overflow: ellipsis;">' . htmlspecialchars($itemDescription) . '</div>';
                                    echo '<div class="item-info" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">';
                                    echo '<div class="item-price" style="font-size: 1.4rem; font-weight: 700; color: #FF6B6B;">RM ' . number_format((float)$itemPrice, 2) . '</div>';
                                    echo '</div>';
                                    echo '<div class="item-actions" style="display: flex; gap: 10px;">';
                                    
                                    // Action buttons based on user type
                                    if ($is_admin) {
                                        // Admin - View only
                                        echo '<div style="flex: 1; text-align: center; padding: 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; color: #6c757d; font-size: 14px; display: flex; align-items: center; justify-content: center; height: 44px;">';
                                        echo '<i class="fas fa-user-shield" style="margin-right: 8px;"></i> Admin View Only';
                                        echo '</div>';
                                    } elseif ($is_staff) {
                                        // Staff - View only
                                        echo '<div style="flex: 1; text-align: center; padding: 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; color: #6c757d; font-size: 14px; display: flex; align-items: center; justify-content: center; height: 44px;">';
                                        echo '<i class="fas fa-user-tie" style="margin-right: 8px;"></i> Staff View Only';
                                        echo '</div>';
                                    } elseif ($is_customer) {
                                        // Customer - Add to cart
                                        echo '<button class="btn btn-primary" style="flex: 1; background: #FF6B6B; border: none; color: white; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 14px;" onclick="addToCart(' . (int)$itemId . ', \'' . addslashes($itemName) . '\', ' . (float)$itemPrice . ', \'' . addslashes($itemImage) . '\')">Add to Cart</button>';
                                    } else {
                                        // Guest - Login prompt
                                        echo '<a href="login.php" class="btn btn-secondary" style="flex: 1; background: transparent; border: 2px solid #FF6B6B; color: #FF6B6B; padding: 12px; border-radius: 8px; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.3s; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 14px;">Log In to Order</a>';
                                    }
                                    
                                    echo '<button onclick="window.location.href=\'menu.php\'" class="btn btn-secondary" style="flex: 1; background: transparent; border: 2px solid #FF6B6B; color: #FF6B6B; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 14px;">View Menu</button>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="empty-message" style="text-align: center; padding: 40px; color: #666; grid-column: 1 / -1;">';
                                echo '<p>No popular items available at the moment. <a href="menu.php" style="color: #FF6B6B;">Browse our full menu</a></p>';
                                echo '</div>';
                            }
                        } catch(Exception $e) {
                            echo '<div class="empty-message" style="text-align: center; padding: 40px; color: #666; grid-column: 1 / -1;">';
                            echo '<p>Unable to load popular items. <a href="menu.php" style="color: #FF6B6B;">Browse our full menu</a></p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="empty-message" style="text-align: center; padding: 40px; color: #666; grid-column: 1 / -1;">';
                        echo '<p>Database not connected. Please run the setup script first.</p>';
                        echo '<p><a href="scripts/setup.php" class="btn btn-primary" style="background: #FF6B6B; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none;">Run Setup</a></p>';
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
                            <h3>Cofeology</h3>
                            <p>Enjoy delicious meals delivered to your doorstep.</p>
                        </div>
                    </div>
                    <div class="footer-bottom">
                        <p>&copy; ' . date('Y') . ' Cofeology. All rights reserved.</p>
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
