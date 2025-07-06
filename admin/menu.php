<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_item':
                $name = $_POST['name'];
                $price = floatval($_POST['price']);
                $description = $_POST['description'];
                $category = $_POST['category'];
                $stock = intval($_POST['stock']);
                $image = $_POST['image'];
                
                $stmt = $conn->prepare("INSERT INTO menu_item (ITEM_NAME, ITEM_PRICE, ITEM_DESCRIPTION, ITEM_CATEGORY, STOCK_LEVEL, image, active) VALUES (?, ?, ?, ?, ?, ?, 1)");
                if ($stmt) {
                    $stmt->bind_param("sdssiss", $name, $price, $description, $category, $stock, $image);
                    if ($stmt->execute()) {
                        $message = "Menu item added successfully!";
                    } else {
                        $error = "Failed to add menu item: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
                break;
                
            case 'update_item':
                $item_id = intval($_POST['item_id']);
                $name = $_POST['name'];
                $price = floatval($_POST['price']);
                $description = $_POST['description'];
                $category = $_POST['category'];
                $stock = intval($_POST['stock']);
                $image = $_POST['image'];
                $active = isset($_POST['active']) ? 1 : 0;
                
                $stmt = $conn->prepare("UPDATE menu_item SET ITEM_NAME = ?, ITEM_PRICE = ?, ITEM_DESCRIPTION = ?, ITEM_CATEGORY = ?, STOCK_LEVEL = ?, image = ?, active = ? WHERE ITEM_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("sdssisii", $name, $price, $description, $category, $stock, $image, $active, $item_id);
                    if ($stmt->execute()) {
                        $message = "Menu item updated successfully!";
                    } else {
                        $error = "Failed to update menu item: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
                break;
                
            case 'delete_item':
                $item_id = intval($_POST['item_id']);
                
                $stmt = $conn->prepare("UPDATE menu_item SET active = 0 WHERE ITEM_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $item_id);
                    if ($stmt->execute()) {
                        $message = "Menu item deleted successfully!";
                    } else {
                        $error = "Failed to delete menu item: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
                break;
                
            case 'update_stock':
                $item_id = intval($_POST['item_id']);
                $stock = intval($_POST['stock']);
                
                $stmt = $conn->prepare("UPDATE menu_item SET STOCK_LEVEL = ? WHERE ITEM_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("ii", $stock, $item_id);
                    if ($stmt->execute()) {
                        $message = "Stock updated successfully!";
                    } else {
                        $error = "Failed to update stock: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
                break;
        }
    }
}

// Get all menu items
$menu_items = [];
$result = $conn->query("SELECT * FROM menu_item ORDER BY ITEM_CATEGORY, ITEM_NAME");
if ($result) {
    $menu_items = $result->fetch_all(MYSQLI_ASSOC);
}

// Get categories
$categories = [
    'Coffee',
    'Tea & Hot Beverages',
    'Cold Beverages',
    'Smoothies & Shakes',
    'Fresh Juices',
    'Local Courses',
    'Italian Cuisines',
    'Western Favorites',
    'Mexican Cuisines',
    'International Cuisines',
    'Asian Fusion',
    'Salads',
    'Side Dishes',
    'Appetizers',
    'Soups',
    'Desserts',
    'Pastries',
    'Cakes',
    'Ice Cream',
    'Snacks'
];

// Get statistics
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM menu_item WHERE active = 1");
$stats['total_items'] = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(*) as total FROM menu_item WHERE STOCK_LEVEL <= 10 AND active = 1");
$stats['low_stock'] = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(*) as total FROM menu_item WHERE active = 0");
$stats['inactive_items'] = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(DISTINCT ITEM_CATEGORY) as total FROM menu_item WHERE active = 1");
$stats['categories'] = $result ? $result->fetch_assoc()['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Cofeology Admin</title>
    <link rel="stylesheet" href="../css/style.css?v=1.1">
    <link rel="stylesheet" href="../css/dashboard.css?v=1.1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Menu Management</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddItemModal()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Items</h3>
                        <p><?php echo number_format($stats['total_items']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Low Stock</h3>
                        <p><?php echo number_format($stats['low_stock']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Inactive Items</h3>
                        <p><?php echo number_format($stats['inactive_items']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Categories</h3>
                        <p><?php echo number_format($stats['categories']); ?></p>
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
                                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="status-filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <input type="text" id="search-filter" class="form-control" placeholder="Search items...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="menu-table">
                                <?php foreach ($menu_items as $item): ?>
                                    <tr data-category="<?php echo $item['ITEM_CATEGORY']; ?>" 
                                        data-status="<?php echo $item['active']; ?>"
                                        data-search="<?php echo strtolower($item['ITEM_NAME'] . ' ' . $item['ITEM_DESCRIPTION']); ?>">
                                        <td>
                                            <img src="<?php echo $item['image'] ?: '/placeholder.svg?height=50&width=50'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>" 
                                                 class="item-image">
                                        </td>
                                        <td>
                                            <div class="item-info">
                                                <strong><?php echo htmlspecialchars($item['ITEM_NAME']); ?></strong>
                                                <small><?php echo htmlspecialchars(substr($item['ITEM_DESCRIPTION'], 0, 50)) . '...'; ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($item['ITEM_CATEGORY']); ?></td>
                                        <td>RM <?php echo number_format($item['ITEM_PRICE'], 2); ?></td>
                                        <td>
                                            <span class="stock-level <?php echo $item['STOCK_LEVEL'] <= 10 ? 'low' : ''; ?>">
                                                <?php echo $item['STOCK_LEVEL']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $item['active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $item['active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="btn-icon" onclick="editItem(<?php echo htmlspecialchars(json_encode($item)); ?>)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-icon" onclick="updateStock(<?php echo $item['ITEM_ID']; ?>, <?php echo $item['STOCK_LEVEL']; ?>)" title="Update Stock">
                                                    <i class="fas fa-boxes"></i>
                                                </button>
                                                <button class="btn-icon btn-danger" onclick="deleteItem(<?php echo $item['ITEM_ID']; ?>, '<?php echo htmlspecialchars($item['ITEM_NAME']); ?>')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Item Modal -->
    <div class="modal" id="item-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Add Menu Item</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="item-form" method="post">
                    <input type="hidden" id="item-action" name="action" value="add_item">
                    <input type="hidden" id="item-id" name="item_id">
                    
                    <div class="form-group">
                        <label for="item-name" class="form-label">Item Name</label>
                        <input type="text" id="item-name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-category" class="form-label">Category</label>
                        <select id="item-category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-price" class="form-label">Price (RM)</label>
                        <input type="number" id="item-price" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-stock" class="form-label">Stock Level</label>
                        <input type="number" id="item-stock" name="stock" class="form-control" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-description" class="form-label">Description</label>
                        <textarea id="item-description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="item-image" class="form-label">Image URL</label>
                        <input type="url" id="item-image" name="image" class="form-control">
                    </div>
                    
                    <div class="form-group" id="active-group" style="display: none;">
                        <label class="form-label">
                            <input type="checkbox" id="item-active" name="active" value="1">
                            Active
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('item-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Stock Update Modal -->
    <div class="modal" id="stock-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Stock Level</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="stock-form" method="post">
                    <input type="hidden" name="action" value="update_stock">
                    <input type="hidden" id="stock-item-id" name="item_id">
                    
                    <div class="form-group">
                        <label for="stock-level" class="form-label">Stock Level</label>
                        <input type="number" id="stock-level" name="stock" class="form-control" min="0" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('stock-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Stock</button>
                    </div>
                </form>
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
        
        .item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .item-info strong {
            display: block;
        }
        
        .item-info small {
            color: #666;
            display: block;
        }
        
        .stock-level {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            background-color: #d4edda;
            color: #155724;
        }
        
        .stock-level.low {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .card-filters {
            display: flex;
            gap: 10px;
        }
        
        .card-filters .form-control {
            width: auto;
            min-width: 150px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #333;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const categoryFilter = document.getElementById('category-filter');
            const statusFilter = document.getElementById('status-filter');
            const searchFilter = document.getElementById('search-filter');
            const menuTable = document.getElementById('menu-table');
            
            function filterItems() {
                const categoryValue = categoryFilter.value;
                const statusValue = statusFilter.value;
                const searchValue = searchFilter.value.toLowerCase();
                const rows = menuTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowCategory = row.getAttribute('data-category');
                    const rowStatus = row.getAttribute('data-status');
                    const rowSearch = row.getAttribute('data-search');
                    
                    let showRow = true;
                    
                    if (categoryValue && rowCategory !== categoryValue) {
                        showRow = false;
                    }
                    
                    if (statusValue && rowStatus !== statusValue) {
                        showRow = false;
                    }
                    
                    if (searchValue && !rowSearch.includes(searchValue)) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            categoryFilter.addEventListener('change', filterItems);
            statusFilter.addEventListener('change', filterItems);
            searchFilter.addEventListener('input', filterItems);
            
            // Modal functionality
            const modals = document.querySelectorAll('.modal');
            const closeButtons = document.querySelectorAll('.close-modal');
            
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    modal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', function(event) {
                modals.forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
        });
        
        function openAddItemModal() {
            document.getElementById('item-form').reset();
            document.getElementById('item-action').value = 'add_item';
            document.getElementById('modal-title').textContent = 'Add Menu Item';
            document.getElementById('submit-btn').textContent = 'Add Item';
            document.getElementById('active-group').style.display = 'none';
            document.getElementById('item-modal').style.display = 'flex';
        }
        
        function editItem(item) {
            document.getElementById('item-action').value = 'update_item';
            document.getElementById('item-id').value = item.ITEM_ID;
            document.getElementById('item-name').value = item.ITEM_NAME;
            document.getElementById('item-category').value = item.ITEM_CATEGORY;
            document.getElementById('item-price').value = item.ITEM_PRICE;
            document.getElementById('item-stock').value = item.STOCK_LEVEL;
            document.getElementById('item-description').value = item.ITEM_DESCRIPTION;
            document.getElementById('item-image').value = item.image || '';
            document.getElementById('item-active').checked = item.active == 1;
            
            document.getElementById('modal-title').textContent = 'Edit Menu Item';
            document.getElementById('submit-btn').textContent = 'Update Item';
            document.getElementById('active-group').style.display = 'block';
            document.getElementById('item-modal').style.display = 'flex';
        }
        
        function updateStock(itemId, currentStock) {
            document.getElementById('stock-item-id').value = itemId;
            document.getElementById('stock-level').value = currentStock;
            document.getElementById('stock-modal').style.display = 'flex';
        }
        
        function deleteItem(itemId, itemName) {
            if (confirm(`Are you sure you want to delete "${itemName}"?`)) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_item">
                    <input type="hidden" name="item_id" value="${itemId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
    </script>
</body>
</html>
