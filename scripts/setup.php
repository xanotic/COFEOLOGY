<?php
// Database setup script
$host = "localhost";
$username = "root";
$password = "";

try {
    // Create connection to MySQL server
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS food_ordering";
    $conn->exec($sql);
    echo "Database created successfully<br>";
    
    // Select the database
    $conn->exec("USE food_ordering");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address TEXT,
        role ENUM('customer', 'staff', 'admin') DEFAULT 'customer',
        is_member BOOLEAN DEFAULT 0,
        points INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Users table created successfully<br>";
    
    // Create categories table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        image VARCHAR(255)
    )";
    $conn->exec($sql);
    echo "Categories table created successfully<br>";
    
    // Create menu_items table
    $sql = "CREATE TABLE IF NOT EXISTS menu_items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        category_id INT(11),
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        is_available BOOLEAN DEFAULT 1,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )";
    $conn->exec($sql);
    echo "Menu items table created successfully<br>";
    
    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11),
        order_type ENUM('delivery', 'takeaway', 'dine-in') NOT NULL,
        status ENUM('pending', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled') DEFAULT 'pending',
        total_amount DECIMAL(10,2) NOT NULL,
        payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        payment_method VARCHAR(50),
        special_instructions TEXT,
        delivery_address TEXT,
        pickup_time DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $conn->exec($sql);
    echo "Orders table created successfully<br>";
    
    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        order_id INT(11) NOT NULL,
        menu_item_id INT(11),
        quantity INT(11) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        special_requests TEXT,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL
    )";
    $conn->exec($sql);
    echo "Order items table created successfully<br>";
    
    // Insert default admin user
    $password_hash = password_hash("admin123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, full_name, phone, role) 
            VALUES ('admin', :password, 'admin@foodordering.com', 'System Administrator', '1234567890', 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':password', $password_hash);
    $stmt->execute();
    echo "Default admin user created<br>";
    
    // Insert sample categories
    $categories = [
        ['Main Dishes', 'Delicious main course options', 'main_dishes.jpg'],
        ['Appetizers', 'Start your meal right', 'appetizers.jpg'],
        ['Desserts', 'Sweet treats to finish', 'desserts.jpg'],
        ['Beverages', 'Refreshing drinks', 'beverages.jpg']
    ];
    
    $sql = "INSERT INTO categories (name, description, image) VALUES (:name, :description, :image)";
    $stmt = $conn->prepare($sql);
    
    foreach ($categories as $category) {
        $stmt->bindParam(':name', $category[0]);
        $stmt->bindParam(':description', $category[1]);
        $stmt->bindParam(':image', $category[2]);
        $stmt->execute();
    }
    echo "Sample categories created<br>";
    
    // Insert sample menu items
    $menu_items = [
        [1, 'Grilled Chicken', 'Tender grilled chicken with herbs', 12.99, 'grilled_chicken.jpg'],
        [1, 'Beef Steak', 'Premium cut beef steak', 18.99, 'beef_steak.jpg'],
        [1, 'Vegetable Pasta', 'Fresh pasta with seasonal vegetables', 10.99, 'veg_pasta.jpg'],
        [2, 'Garlic Bread', 'Crispy bread with garlic butter', 4.99, 'garlic_bread.jpg'],
        [2, 'Chicken Wings', 'Spicy chicken wings', 8.99, 'chicken_wings.jpg'],
        [3, 'Chocolate Cake', 'Rich chocolate layer cake', 6.99, 'chocolate_cake.jpg'],
        [3, 'Ice Cream', 'Vanilla ice cream with toppings', 4.99, 'ice_cream.jpg'],
        [4, 'Soft Drinks', 'Assorted soft drinks', 2.99, 'soft_drinks.jpg'],
        [4, 'Coffee', 'Freshly brewed coffee', 3.99, 'coffee.jpg']
    ];
    
    $sql = "INSERT INTO menu_items (category_id, name, description, price, image) 
            VALUES (:category_id, :name, :description, :price, :image)";
    $stmt = $conn->prepare($sql);
    
    foreach ($menu_items as $item) {
        $stmt->bindParam(':category_id', $item[0]);
        $stmt->bindParam(':name', $item[1]);
        $stmt->bindParam(':description', $item[2]);
        $stmt->bindParam(':price', $item[3]);
        $stmt->bindParam(':image', $item[4]);
        $stmt->execute();
    }
    echo "Sample menu items created<br>";
    
    echo "Database setup completed successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
