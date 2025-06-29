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
                $description = $_POST['description'];
                $price = floatval($_POST['price']);
                $category = $_POST['category'];
                $stock_level = intval($_POST['stock_level']);
                $image = $_POST['image'] ?? null;
                $admin_id = $_SESSION['user_id'];
                
                if (addMenuItem($conn, $name, $description, $price, $category, $stock_level, $admin_id, $image)) {
                    $message = "Menu item added successfully!";
                } else {
                    $error = "Failed to add menu item.";
                }
                break;
                
            case 'update_item':
                $item_id = intval($_POST['item_id']);
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = floatval($_POST['price']);
                $category = $_POST['category'];
                $stock_level = intval($_POST['stock_level']);
                $active = isset($_POST['active']) ? 1 : 0;
                $image = $_POST['image'] ?? null;
                
                if (updateMenuItem($conn, $item_id, $name, $description, $price, $category, $stock_level, $active, $image)) {
                    $message = "Menu item updated successfully!";
                } else {
                    $error = "Failed to update menu item.";
                }
                break;
                
            case 'delete_item':
                $item_id = intval($_POST['item_id']);
                $stmt = $conn->prepare("UPDATE MENU_ITEM SET active = 0 WHERE ITEM_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $item_id);
                    if ($stmt->execute()) {
                        $message = "Menu item deleted successfully!";
                    } else {
                        $error = "Failed to delete menu item.";
                    }
                    $stmt->close();
                }
                break;
        }
    }
}

// Get all menu items
$menuItems = [];
$result = $conn->query("SELECT * FROM MENU_ITEM ORDER BY ITEM_CATEGORY, ITEM_NAME");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
}

// Get categories for filter
$categories = [];
$result = $conn->query("SELECT DISTINCT ITEM_CATEGORY FROM MENU_ITEM WHERE active = 1");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['ITEM_CATEGORY'];
    }
}

// Get menu statistics
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM MENU_ITEM WHERE active = 1");
$stats['total_items'] = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(*) as total FROM MENU_ITEM WHERE active = 1 AND STOCK_LEVEL > 0");
$stats['in_stock'] = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(*) as total FROM MENU_ITEM WHERE active = 1 AND STOCK_LEVEL = 0");
$stats['out_of_stock'] = $result ? $result->fetch_assoc()['total'] : 0;

$result = $conn->query("SELECT COUNT(DISTINCT ITEM_CATEGORY) as total FROM MENU_ITEM WHERE active = 1");
$stats['categories'] = $result ? $result->fetch_assoc()['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Cofeology</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
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
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Menu Item
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
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>In Stock</h3>
                        <p><?php echo number_format($stats['in_stock']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Out of Stock</h3>
                        <p><?php echo number_format($stats['out_of_stock']); ?></p>
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
                                <option value="<?php echo $category; ?>"><?php echo ucfirst($category); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="status-filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="in-stock">In Stock</option>
                            <option value="out-of-stock">Out of Stock</option>
                        </select>
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
                                <?php foreach ($menuItems as $item): ?>
                                    <tr data-category="<?php echo $item['ITEM_CATEGORY']; ?>" 
                                        data-status="<?php echo $item['active'] ? 'active' : 'inactive'; ?>"
                                        data-stock="<?php echo $item['STOCK_LEVEL'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                        <td>
                                            <img src="<?php echo $item['image'] ?: 'https://via.placeholder.com/50x50/ff6b6b/ffffff?text=Item'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['ITEM_NAME']); ?>" 
                                                 class="item-image">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['ITEM_NAME']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars(substr($item['ITEM_DESCRIPTION'], 0, 50)); ?>...</small>
                                        </td>
                                        <td><?php echo ucfirst($item['ITEM_CATEGORY']); ?></td>
                                        <td><?php echo formatCurrency($item['ITEM_PRICE']); ?></td>
                                        <td>
                                            <span class="stock-badge <?php echo $item['STOCK_LEVEL'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
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
                    <input type="hidden" name="action" id="form-action" value="add_item">
                    <input type="hidden" name="item_id" id="item-id">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price" class="form-label">Price (RM)</label>
                            <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="stock_level" class="form-label">Stock Level</label>
                            <input type="number" id="stock_level" name="stock_level" class="form-control" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="coffee">Coffee</option>
                            <option value="tea">Tea</option>
                            <option value="pastry">Pastry</option>
                            <option value="sandwich">Sandwich</option>
                            <option value="dessert">Dessert</option>
                            <option value="beverage">Beverage</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image" class="form-label">Image URL</label>
                        <input type="url" id="image" name="image" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div class="form-group" id="active-group" style="display: none;">
                        <label class="checkbox-label">
                            <input type="checkbox" id="active" name="active" checked>
                            <span class="checkmark"></span>
                            Active
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">Add Item</button>
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
        
        .stock-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .stock-badge.in-stock {
            background-color: #d4edda;
            color: #155724;
        }
        
        .stock-badge.out-of-stock {
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
            max-width: 500px;
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
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .checkbox-label input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .card-filters {
            display: flex;
            gap: 10px;
        }
        
        .card-filters .form-control {
            width: auto;
            min-width: 150px;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const categoryFilter = document.getElementById('category-filter');
            const statusFilter = document.getElementById('status-filter');
            const menuTable = document.getElementById('menu-table');
            
            function filterItems() {
                const categoryValue = categoryFilter.value;
                const statusValue = statusFilter.value;
                const rows = menuTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowCategory = row.getAttribute('data-category');
                    const rowStatus = row.getAttribute('data-status');
                    const rowStock = row.getAttribute('data-stock');
                    
                    let showRow = true;
                    
                    if (categoryValue && rowCategory !== categoryValue) {
                        showRow = false;
                    }
                    
                    if (statusValue) {
                        if (statusValue === 'active' && rowStatus !== 'active') {
                            showRow = false;
                        } else if (statusValue === 'inactive' && rowStatus !== 'inactive') {
                            showRow = false;
                        } else if (statusValue === 'in-stock' && rowStock !== 'in-stock') {
                            showRow = false;
                        } else if (statusValue === 'out-of-stock' && rowStock !== 'out-of-stock') {
                            showRow = false;
                        }
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            categoryFilter.addEventListener('change', filterItems);
            statusFilter.addEventListener('change', filterItems);
            
            // Modal functionality
            const modal = document.getElementById('item-modal');
            const closeModal = document.querySelector('.close-modal');
            
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
        
        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Menu Item';
            document.getElementById('form-action').value = 'add_item';
            document.getElementById('submit-btn').textContent = 'Add Item';
            document.getElementById('active-group').style.display = 'none';
            document.getElementById('item-form').reset();
            document.getElementById('item-modal').style.display = 'flex';
        }
        
        function editItem(item) {
            document.getElementById('modal-title').textContent = 'Edit Menu Item';
            document.getElementById('form-action').value = 'update_item';
            document.getElementById('submit-btn').textContent = 'Update Item';
            document.getElementById('active-group').style.display = 'block';
            
            document.getElementById('item-id').value = item.ITEM_ID;
            document.getElementById('name').value = item.ITEM_NAME;
            document.getElementById('description').value = item.ITEM_DESCRIPTION;
            document.getElementById('price').value = item.ITEM_PRICE;
            document.getElementById('category').value = item.ITEM_CATEGORY;
            document.getElementById('stock_level').value = item.STOCK_LEVEL;
            document.getElementById('image').value = item.image || '';
            document.getElementById('active').checked = item.active == 1;
            
            document.getElementById('item-modal').style.display = 'flex';
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
        
        function closeModal() {
            document.getElementById('item-modal').style.display = 'none';
        }
    </script>
</body>
</html>
