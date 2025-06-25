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
            case 'add':
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = floatval($_POST['price']);
                $category = $_POST['category'];
                $image = $_POST['image'] ?? '';
                $stock_level = intval($_POST['stock_level']);
                $admin_id = $_SESSION['admin_id'];
                
                if (addMenuItem($conn, $name, $description, $price, $category, $image, $admin_id, $stock_level)) {
                    $message = "Menu item added successfully!";
                } else {
                    $error = "Failed to add menu item.";
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = floatval($_POST['price']);
                $category = $_POST['category'];
                $active = isset($_POST['active']) ? 1 : 0;
                $image = $_POST['image'] ?? '';
                $stock_level = intval($_POST['stock_level']);
                
                if (updateMenuItem($conn, $id, $name, $description, $price, $category, $active, $image, $stock_level)) {
                    $message = "Menu item updated successfully!";
                } else {
                    $error = "Failed to update menu item.";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("DELETE FROM " . MENU_ITEM . " WHERE " . ITEM_ID . " = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $message = "Menu item deleted successfully!";
                } else {
                    $error = "Failed to delete menu item.";
                }
                break;
        }
    }
}

// Get all menu items
$stmt = $conn->prepare("SELECT * FROM " . MENU_ITEM . " ORDER BY " . ITEM_CATEGORY . ", " . ITEM_NAME);
$stmt->execute();
$menuItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get categories
$categories = array_unique(array_column($menuItems, ITEM_CATEGORY));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Café Delights</title>
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
                                    <tr data-category="<?php echo htmlspecialchars($item[ITEM_CATEGORY]); ?>">
                                        <td>
                                            <div class="menu-item-image-small">
                                                <img src="<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : '/placeholder.svg?height=50&width=50'; ?>" alt="<?php echo htmlspecialchars($item[ITEM_NAME]); ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="menu-item-info">
                                                <strong><?php echo htmlspecialchars($item[ITEM_NAME]); ?></strong>
                                                <p><?php echo htmlspecialchars($item[ITEM_DESCRIPTION]); ?></p>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($item[ITEM_CATEGORY]); ?></td>
                                        <td><?php echo formatCurrency($item[ITEM_PRICE]); ?></td>
                                        <td><?php echo htmlspecialchars($item['stock_level']); ?></td>
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
                                                <button class="btn-icon" onclick="deleteItem(<?php echo $item[ITEM_ID]; ?>)" title="Delete">
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
    
    <!-- Add/Edit Modal -->
    <div class="modal" id="item-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Add Menu Item</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="item-form" method="post">
                    <input type="hidden" id="item-id" name="id">
                    <input type="hidden" id="form-action" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="form-label">Price (RM)</label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" id="category" name="category" class="form-control" list="categories" required>
                        <datalist id="categories">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    
                    <div class="form-group">
                        <label for="image" class="form-label">Image URL</label>
                        <input type="url" id="image" name="image" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="stock_level" class="form-label">Stock Level</label>
                        <input type="number" id="stock_level" name="stock_level" class="form-control" value="1" required>
                    </div>
                    
                    <div class="form-group" id="active-group" style="display: none;">
                        <label class="form-check">
                            <input type="checkbox" id="active" name="active" class="form-check-input">
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
        .menu-item-image-small {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .menu-item-image-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .menu-item-info p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9rem;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .status-badge.active {
            background-color: rgba(76, 175, 80, 0.2);
            color: #2e7d32;
        }
        
        .status-badge.inactive {
            background-color: rgba(244, 67, 54, 0.2);
            color: #c62828;
        }
        
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
        
        .form-check {
            display: flex;
            align-items: center;
        }
        
        .form-check-input {
            margin-right: 10px;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Category filter
            const categoryFilter = document.getElementById('category-filter');
            const menuTable = document.getElementById('menu-table');
            
            categoryFilter.addEventListener('change', function() {
                const selectedCategory = this.value;
                const rows = menuTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowCategory = row.getAttribute('data-category');
                    if (!selectedCategory || rowCategory === selectedCategory) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
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
            document.getElementById('form-action').value = 'add';
            document.getElementById('item-form').reset();
            document.getElementById('active-group').style.display = 'none';
            document.getElementById('item-modal').style.display = 'flex';
        }
        
        function editItem(item) {
            document.getElementById('modal-title').textContent = 'Edit Menu Item';
            document.getElementById('form-action').value = 'update';
            document.getElementById('item-id').value = item.ITEM_ID;
            document.getElementById('name').value = item.ITEM_NAME;
            document.getElementById('description').value = item.ITEM_DESCRIPTION;
            document.getElementById('price').value = item.ITEM_PRICE;
            document.getElementById('category').value = item.ITEM_CATEGORY;
            document.getElementById('image').value = item.image || '';
            document.getElementById('stock_level').value = item.stock_level;
            document.getElementById('active').checked = item.active == 1;
            document.getElementById('active-group').style.display = 'block';
            document.getElementById('item-modal').style.display = 'flex';
        }
        
        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this menu item?')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
