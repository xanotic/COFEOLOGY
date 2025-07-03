<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';


// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// Get all active menu items
$menu_items = [];
$result = $conn->query("SELECT * FROM menu_item WHERE active = 1 ORDER BY ITEM_CATEGORY, ITEM_NAME");
if ($result) {
    $menu_items = $result->fetch_all(MYSQLI_ASSOC);
}

// Get unique categories for filter buttons
$categories = [];
foreach ($menu_items as $item) {
    if (!in_array($item['ITEM_CATEGORY'], $categories)) {
        $categories[] = $item['ITEM_CATEGORY'];
    }
}

// Group items by category
$grouped_items = [];
foreach ($menu_items as $item) {
    $grouped_items[$item['ITEM_CATEGORY']][] = $item;
}

// Determine user type - check all possible session variables
$is_admin = isset($_SESSION['admin_id']) || isset($_SESSION['admin_logged_in']) || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$is_staff = isset($_SESSION['staff_id']) || isset($_SESSION['staff_logged_in']) || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'staff') || (isset($_SESSION['role']) && $_SESSION['role'] === 'staff');
$is_customer = isset($_SESSION['user_id']) && !$is_admin && !$is_staff;
$is_guest = !isset($_SESSION['user_id']) && !$is_admin && !$is_staff;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Cofeology</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .main-content {
            padding: 80px 0 40px;
            min-height: calc(100vh - 160px);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-size: 2.5rem;
            color: #8B4513;
            margin-bottom: 10px;
        }
        
        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }
        
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .search-box {
            position: relative;
            width: 100%;
            max-width: 400px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            border-color: #8B4513;
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .category-filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 40px;
            padding: 0 20px;
        }
        
        .category-btn {
            padding: 10px 20px;
            background: white;
            border: 2px solid #8B4513;
            border-radius: 25px;
            color: #8B4513;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: capitalize;
        }
        
        .category-btn:hover,
        .category-btn.active {
            background: #8B4513;
            color: white;
        }
        
        .menu-container {
            padding: 20px 0;
        }
        
        .category-section {
            margin-bottom: 60px;
        }
        
        .category-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #8B4513;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 4px solid #8B4513;
            text-align: center;
            text-transform: capitalize;
            position: relative;
        }
        
        .category-title::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #8B4513, #A0522D);
            border-radius: 2px;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            padding: 0 20px;
        }
        
        .menu-item-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .item-image {
            position: relative;
            height: 220px;
            overflow: hidden;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .menu-item-card:hover .item-image img {
            transform: scale(1.05);
        }
        
        .out-of-stock-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        
        .item-details {
            padding: 20px;
        }
        
        .item-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .item-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 15px;
            height: 45px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .item-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .item-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #8B4513;
        }
        
        .stock-info {
            font-size: 0.85rem;
            padding: 4px 10px;
            border-radius: 15px;
            background-color: #e8f5e8;
            color: #2d5a2d;
            font-weight: 500;
        }
        
        .stock-info.low-stock {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .item-actions {
            text-align: center;
        }
        
        .add-to-cart-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #8B4513, #A0522D);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, #A0522D, #8B4513);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .view-only-message {
            width: 100%;
            padding: 12px;
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .no-items {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.1rem;
        }
        
        
        
        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
                padding: 0 10px;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .category-title {
                font-size: 1.8rem;
            }
            
            .category-filters {
                padding: 0 10px;
            }
            
            .category-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="main-content">
        <div class="container">
            
            
            <header class="page-header">
                <h1>Our Menu</h1>
                <p>Discover our delicious selection of coffee, food, and beverages</p>
            </header>
            
            <!-- Search Bar -->
            <div class="search-container">
                <div class="search-box">
                    <input type="text" id="menu-search" placeholder="Search menu items..." class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
            
            <!-- Category Filter Buttons -->
            <div class="category-filters">
                <button class="category-btn active" data-category="all">All Items</button>
                <?php foreach ($categories as $category): ?>
                    <button class="category-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Menu Categories -->
            <div class="menu-container">
                <?php if (empty($grouped_items)): ?>
                    <div class="no-items">
                        <i class="fas fa-coffee" style="font-size: 3rem; margin-bottom: 20px; color: #8B4513;"></i>
                        <p>No menu items available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($grouped_items as $category => $items): ?>
                        <section class="category-section" data-category="<?php echo htmlspecialchars($category); ?>">
                            <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                            <div class="menu-grid">
                                <?php foreach ($items as $item): ?>
                                    <div class="menu-item-card" 
                                         data-search="<?php echo strtolower($item['ITEM_NAME'] . ' ' . $item['ITEM_DESCRIPTION']); ?>"
                                         data-category="<?php echo htmlspecialchars($item['ITEM_CATEGORY']); ?>">
                                        <div class="item-image">
                                            <img src="<?php echo $item['image'] ?: '/placeholder.svg?height=250&width=300'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>">
                                            <?php if ($item['STOCK_LEVEL'] <= 0): ?>
                                                <div class="out-of-stock-overlay">
                                                    <span>Out of Stock</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-details">
                                            <h3 class="item-name"><?php echo htmlspecialchars($item['ITEM_NAME']); ?></h3>
                                            <p class="item-description"><?php echo htmlspecialchars($item['ITEM_DESCRIPTION']); ?></p>
                                            <div class="item-info">
                                                <span class="item-price">RM <?php echo number_format($item['ITEM_PRICE'], 2); ?></span>
                                                <span class="stock-info <?php echo $item['STOCK_LEVEL'] <= 10 ? 'low-stock' : ''; ?>">
                                                    <?php if ($item['STOCK_LEVEL'] > 0): ?>
                                                        Stock: <?php echo $item['STOCK_LEVEL']; ?>
                                                    <?php else: ?>
                                                        Out of Stock
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            
                                            <!-- Action buttons based on user type -->
                                            <div class="item-actions">
                                                <?php if ($is_admin): ?>
                                                    <!-- Admin - View only -->
                                                    <div class="view-only-message">
                                                        <i class="fas fa-user-shield"></i> Admin View Only - No Ordering
                                                    </div>
                                                <?php elseif ($is_staff): ?>
                                                    <!-- Staff - View only -->
                                                    <div class="view-only-message">
                                                        <i class="fas fa-user-tie"></i> Staff View Only - No Ordering
                                                    </div>
                                                <?php elseif ($item['STOCK_LEVEL'] <= 0): ?>
                                                    <!-- Out of stock -->
                                                    <button class="btn btn-secondary" disabled>
                                                        <i class="fas fa-times"></i> Out of Stock
                                                    </button>
                                                <?php elseif ($is_customer): ?>
                                                    <!-- Customer - Add to cart -->
                                                    <button class="add-to-cart-btn" 
                                                            onclick="addToCart(<?php echo $item['ITEM_ID']; ?>, '<?php echo htmlspecialchars($item['ITEM_NAME']); ?>', <?php echo $item['ITEM_PRICE']; ?>, '<?php echo htmlspecialchars($item['image']); ?>')">
                                                        <i class="fas fa-plus"></i> Add to Cart
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Guest - Login prompt -->
                                                    <a href="login.php" class="btn btn-secondary">
                                                        <i class="fas fa-sign-in-alt"></i> Login to Order
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('menu-search');
            const menuItems = document.querySelectorAll('.menu-item-card');
            const categoryButtons = document.querySelectorAll('.category-btn');
            const categorySections = document.querySelectorAll('.category-section');
            
            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                if (searchTerm === '') {
                    // Show all items and sections when search is empty
                    menuItems.forEach(item => {
                        item.style.display = 'block';
                    });
                    categorySections.forEach(section => {
                        section.style.display = 'block';
                    });
                } else {
                    // Filter items based on search
                    menuItems.forEach(item => {
                        const searchData = item.getAttribute('data-search');
                        const shouldShow = searchData.includes(searchTerm);
                        item.style.display = shouldShow ? 'block' : 'none';
                    });
                    
                    // Hide/show category sections based on visible items
                    categorySections.forEach(section => {
                        const visibleItems = section.querySelectorAll('.menu-item-card[style="display: block"], .menu-item-card:not([style*="display: none"])');
                        section.style.display = visibleItems.length > 0 ? 'block' : 'none';
                    });
                }
            });
            
            // Category filter functionality
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const selectedCategory = this.getAttribute('data-category');
                    
                    // Update active button
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Clear search when changing category
                    searchInput.value = '';
                    
                    if (selectedCategory === 'all') {
                        // Show all categories and items
                        categorySections.forEach(section => {
                            section.style.display = 'block';
                        });
                        menuItems.forEach(item => {
                            item.style.display = 'block';
                        });
                    } else {
                        // Show only selected category
                        categorySections.forEach(section => {
                            const sectionCategory = section.getAttribute('data-category');
                            if (sectionCategory === selectedCategory) {
                                section.style.display = 'block';
                                // Show all items in this category
                                const categoryItems = section.querySelectorAll('.menu-item-card');
                                categoryItems.forEach(item => {
                                    item.style.display = 'block';
                                });
                            } else {
                                section.style.display = 'none';
                            }
                        });
                    }
                });
            });
        });
        
        // Only define addToCart function if user is a customer (not admin or staff)
        <?php if ($is_customer): ?>
        function addToCart(itemId, itemName, itemPrice, itemImage) {
            // Get cart from localStorage or initialize empty array
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Check if item already exists in cart
            const existingItem = cart.find(item => item.id === itemId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    image: itemImage,
                    quantity: 1
                });
            }
            
            // Save cart to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Show success message
            showNotification(`${itemName} added to cart!`, 'success');
            
            // Update cart count in header
            updateCartCount();
        }
        
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                font-weight: 500;
                z-index: 1000;
                transition: all 0.3s;
                ${type === 'success' ? 'background: #28a745;' : 'background: #dc3545;'}
            `;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
        
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = totalItems;
            }
        }
        
        // Update cart count on page load
        updateCartCount();
        <?php else: ?>
        // No cart functionality for admin/staff users
        console.log('Cart functionality disabled for admin/staff users');
        <?php endif; ?>
    </script>
</body>
</html>
