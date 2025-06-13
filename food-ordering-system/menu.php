<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

// Get all menu categories
$stmt = $conn->prepare("SELECT DISTINCT category FROM menu_items WHERE active = 1");
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Get all menu items
$menuItems = getMenuItems($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Café Delights</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Our Menu</h1>
                <p>Explore our delicious offerings and find your favorites</p>
            </div>
        </section>
        
        <section class="menu-section">
            <div class="container">
                <div class="menu-filters">
                    <button class="menu-filter-btn active" data-category="all">All</button>
                    <?php foreach ($categories as $category): ?>
                        <button class="menu-filter-btn" data-category="<?php echo htmlspecialchars($category); ?>">
                            <?php echo htmlspecialchars($category); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="menu-grid">
                    <?php foreach ($menuItems as $item): ?>
                        <div class="menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                            <div class="menu-item-image">
                                <img src="<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="menu-item-content">
                                <div class="menu-item-title">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <div class="menu-item-price"><?php echo formatCurrency($item['price']); ?></div>
                                </div>
                                <div class="menu-item-description">
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </div>
                                <div class="menu-item-actions">
                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', <?php echo $item['price']; ?>, '<?php echo !empty($item['image']) ? addslashes($item['image']) : 'images/placeholder.jpg'; ?>')">
                                        Add to Cart
                                    </button>
                                    <button class="btn btn-outline btn-sm" onclick="showItemDetails(<?php echo $item['id']; ?>)">
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        function showItemDetails(itemId) {
            // Redirect to item details page or show modal
            window.location.href = 'item-details.php?id=' + itemId;
        }
    </script>
</body>
</html>
