<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not staff
if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$error = '';

// Handle menu item status updates (staff can only toggle availability)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'toggle_availability') {
        $item_id = intval($_POST['item_id']);
        $active = isset($_POST['active']) ? 1 : 0;
        
        // Use correct column name 'active' instead of 'AVAILABILITY'
        $stmt = $conn->prepare("UPDATE menu_item SET active = ? WHERE ITEM_ID = ?");
        $stmt->bind_param("ii", $active, $item_id);
        
        if ($stmt->execute()) {
            $message = "Menu item availability updated successfully!";
        } else {
            $error = "Failed to update menu item availability.";
        }
    }
}

// Get all menu items with error handling - use correct table name
$stmt = $conn->prepare("SELECT * FROM menu_item ORDER BY ITEM_NAME");
$stmt->execute();
$result = $stmt->get_result();
$menuItems = $result->fetch_all(MYSQLI_ASSOC);

// Get categories (if category column exists, otherwise use a default)
$categories = [];
if (!empty($menuItems)) {
    $firstItem = $menuItems[0];
    if (isset($firstItem['ITEM_CATEGORY'])) {
        $categories = array_unique(array_column($menuItems, 'ITEM_CATEGORY'));
    } elseif (isset($firstItem['CATEGORY'])) {
        $categories = array_unique(array_column($menuItems, 'CATEGORY'));
    } else {
        // Use default categories if column doesn't exist
        $categories = ['Coffee', 'Tea', 'Pastries', 'Sandwiches'];
    }
}

// Get menu statistics
$totalItems = count($menuItems);
$activeItems = count(array_filter($menuItems, function($item) { 
    return isset($item['active']) ? $item['active'] : (isset($item['AVAILABILITY']) ? $item['AVAILABILITY'] : 1); 
}));
$inactiveItems = $totalItems - $activeItems;
$categoriesCount = count($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Cofeology Staff</title>
    <link rel="stylesheet" href="../css/style.css?v=1.1">
    <link rel="stylesheet" href="../css/dashboard.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Menu Management</h1>
                    <p>Manage menu item availability</p>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Menu Statistics -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Items</h3>
                        <p><?php echo number_format($totalItems); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Available</h3>
                        <p><?php echo number_format($activeItems); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Unavailable</h3>
                        <p><?php echo number_format($inactiveItems); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Categories</h3>
                        <p><?php echo number_format($categoriesCount); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Menu Items</h2>
                    <div class="card-filters">
                        <select id="category-filter" class="form-control">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="status-filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="1">Available</option>
                            <option value="0">Unavailable</option>
                        </select>
                        <input type="text" id="search-filter" class="form-control" placeholder="Search items...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="menu-grid" id="menu-grid">
                        <?php if (empty($menuItems)): ?>
                            <div class="no-items">
                                <p>No menu items found. Please contact administrator to add menu items.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($menuItems as $item): ?>
                                <?php
                                // Get the price - check different possible column names
                                $price = 0;
                                if (isset($item['ITEM_PRICE'])) {
                                    $price = $item['ITEM_PRICE'];
                                } elseif (isset($item['PRICE'])) {
                                    $price = $item['PRICE'];
                                } elseif (isset($item['price'])) {
                                    $price = $item['price'];
                                }
                                
                                // Get the name
                                $name = '';
                                if (isset($item['ITEM_NAME'])) {
                                    $name = $item['ITEM_NAME'];
                                } elseif (isset($item['name'])) {
                                    $name = $item['name'];
                                } elseif (isset($item['NAME'])) {
                                    $name = $item['NAME'];
                                }
                                
                                // Get availability - use correct column name
                                $availability = 1; // default to available
                                if (isset($item['active'])) {
                                    $availability = intval($item['active']);
                                } elseif (isset($item['AVAILABILITY'])) {
                                    $availability = intval($item['AVAILABILITY']);
                                } elseif (isset($item['available'])) {
                                    $availability = intval($item['available']);
                                } elseif (isset($item['ACTIVE'])) {
                                    $availability = intval($item['ACTIVE']);
                                }
                                
                                // Get category
                                $category = 'General';
                                if (isset($item['ITEM_CATEGORY'])) {
                                    $category = $item['ITEM_CATEGORY'];
                                } elseif (isset($item['CATEGORY'])) {
                                    $category = $item['CATEGORY'];
                                } elseif (isset($item['category'])) {
                                    $category = $item['category'];
                                }
                                
                                // Get description
                                $description = 'No description available';
                                if (isset($item['ITEM_DESCRIPTION'])) {
                                    $description = $item['ITEM_DESCRIPTION'];
                                } elseif (isset($item['DESCRIPTION'])) {
                                    $description = $item['DESCRIPTION'];
                                } elseif (isset($item['description'])) {
                                    $description = $item['description'];
                                }
                                
                                // Get image
                                $image = '';
                                if (isset($item['image'])) {
                                    $image = $item['image'];
                                } elseif (isset($item['IMAGE_URL'])) {
                                    $image = $item['IMAGE_URL'];
                                } elseif (isset($item['IMAGE'])) {
                                    $image = $item['IMAGE'];
                                }
                                ?>
                                <div class="menu-card <?php echo $availability ? '' : 'disabled'; ?>" 
                                     data-category="<?php echo htmlspecialchars($category); ?>" 
                                     data-status="<?php echo $availability; ?>"
                                     data-search="<?php echo strtolower($name . ' ' . $description); ?>">
                                    <div class="menu-card-image">
                                        <img src="<?php echo !empty($image) ? htmlspecialchars($image) : '/placeholder.svg?height=150&width=200'; ?>" 
                                             alt="<?php echo htmlspecialchars($name); ?>">
                                        <div class="availability-toggle">
                                            <form method="post" class="toggle-form">
                                                <input type="hidden" name="action" value="toggle_availability">
                                                <input type="hidden" name="item_id" value="<?php echo $item['ITEM_ID']; ?>">
                                                <label class="switch">
                                                    <input type="checkbox" name="active" <?php echo $availability ? 'checked' : ''; ?> 
                                                           onchange="this.form.submit()">
                                                    <span class="slider"></span>
                                                </label>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="menu-card-content">
                                        <div class="menu-card-header">
                                            <h3><?php echo htmlspecialchars($name); ?></h3>
                                            <div class="menu-card-price">RM<?php echo number_format($price, 2); ?></div>
                                        </div>
                                        <div class="menu-card-category">
                                            <span class="category-badge"><?php echo htmlspecialchars($category); ?></span>
                                        </div>
                                        <div class="menu-card-description">
                                            <?php echo htmlspecialchars($description); ?>
                                        </div>
                                        <div class="menu-card-status">
                                            <span class="status-badge <?php echo $availability ? 'available' : 'unavailable'; ?>">
                                                <i class="fas <?php echo $availability ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                                <?php echo $availability ? 'Available' : 'Unavailable'; ?>
                                            </span>
                                        </div>
                                        <!-- NO ADD TO CART BUTTON FOR STAFF -->
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .no-items {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.1rem;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .menu-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        /* Disabled menu item styling */
        .menu-card.disabled {
            opacity: 0.6;
            background: #f8f9fa;
        }
        
        .menu-card.disabled:hover {
            transform: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .menu-card-image {
            position: relative;
            height: 150px;
            overflow: hidden;
        }
        
        .menu-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .availability-toggle {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 5px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #28a745;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .menu-card-content {
            padding: 15px;
        }
        
        .menu-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .menu-card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #2d3436;
        }
        
        .menu-card-price {
            font-weight: bold;
            color: #ff6b6b;
            font-size: 1.1rem;
        }
        
        .menu-card-category {
            margin-bottom: 10px;
        }
        
        .category-badge {
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .menu-card-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 15px;
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .menu-card-status {
            display: flex;
            justify-content: center;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-badge.available {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .status-badge.unavailable {
            background-color: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }
        
        .toggle-form {
            margin: 0;
        }
        
        .header-title p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const categoryFilter = document.getElementById('category-filter');
            const statusFilter = document.getElementById('status-filter');
            const searchFilter = document.getElementById('search-filter');
            const menuGrid = document.getElementById('menu-grid');
            
            function filterMenu() {
                const categoryValue = categoryFilter.value;
                const statusValue = statusFilter.value;
                const searchValue = searchFilter.value.toLowerCase();
                const cards = menuGrid.querySelectorAll('.menu-card');
                
                cards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    const cardStatus = card.getAttribute('data-status');
                    const cardSearch = card.getAttribute('data-search');
                    
                    let showCard = true;
                    
                    if (categoryValue && cardCategory !== categoryValue) {
                        showCard = false;
                    }
                    
                    if (statusValue && cardStatus !== statusValue) {
                        showCard = false;
                    }
                    
                    if (searchValue && !cardSearch.includes(searchValue)) {
                        showCard = false;
                    }
                    
                    card.style.display = showCard ? '' : 'none';
                });
            }
            
            categoryFilter.addEventListener('change', filterMenu);
            statusFilter.addEventListener('change', filterMenu);
            searchFilter.addEventListener('input', filterMenu);
        });
    </script>
</body>
</html>
