-- Create database
CREATE DATABASE IF NOT EXISTS cafe_ordering_system;
USE cafe_ordering_system;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    role ENUM('customer', 'staff', 'admin') DEFAULT 'customer',
    is_member BOOLEAN DEFAULT FALSE,
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create menu_items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_type ENUM('delivery', 'takeaway', 'dine-in') NOT NULL,
    status ENUM('pending', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_method VARCHAR(50),
    total_amount DECIMAL(10, 2) NOT NULL,
    delivery_address TEXT,
    pickup_time DATETIME,
    special_instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create order_details table
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Create activity_logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user
INSERT INTO users (name, email, password, phone, role) VALUES 
('Admin User', 'admin@cafedelights.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', 'admin');

-- Insert sample staff user
INSERT INTO users (name, email, password, phone, role) VALUES 
('Staff User', 'staff@cafedelights.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'staff');

-- Insert sample menu items
INSERT INTO menu_items (name, description, price, category, image) VALUES
('Grilled Chicken Burger', 'Juicy grilled chicken breast with fresh lettuce, tomato, and our special sauce', 12.90, 'Main Course', 'images/chicken-burger.jpg'),
('Beef Steak', 'Premium beef steak cooked to perfection with mashed potatoes and vegetables', 24.90, 'Main Course', 'images/beef-steak.jpg'),
('Caesar Salad', 'Fresh romaine lettuce with parmesan cheese, croutons, and caesar dressing', 8.90, 'Salads', 'images/caesar-salad.jpg'),
('Margherita Pizza', 'Classic pizza with tomato sauce, mozzarella cheese, and fresh basil', 16.90, 'Pizza', 'images/margherita-pizza.jpg'),
('Chocolate Cake', 'Rich chocolate cake with chocolate ganache and fresh berries', 6.90, 'Desserts', 'images/chocolate-cake.jpg'),
('Iced Coffee', 'Refreshing iced coffee with milk and sugar', 4.90, 'Beverages', 'images/iced-coffee.jpg'),
('Fresh Orange Juice', 'Freshly squeezed orange juice', 3.90, 'Beverages', 'images/orange-juice.jpg'),
('Fish and Chips', 'Crispy battered fish with golden fries and tartar sauce', 14.90, 'Main Course', 'images/fish-chips.jpg'),
('Chicken Wings', 'Spicy buffalo chicken wings with blue cheese dip', 9.90, 'Appetizers', 'images/chicken-wings.jpg'),
('Tiramisu', 'Classic Italian dessert with coffee-soaked ladyfingers and mascarpone', 7.90, 'Desserts', 'images/tiramisu.jpg');

-- Insert sample orders for testing
INSERT INTO orders (user_id, order_type, status, payment_status, payment_method, total_amount, delivery_address, special_instructions) VALUES
(1, 'delivery', 'completed', 'completed', 'fpx', 25.80, '123 Main Street, City', 'Please ring the doorbell'),
(1, 'takeaway', 'ready', 'completed', 'credit_card', 18.90, NULL, 'Extra sauce please'),
(1, 'dine-in', 'preparing', 'completed', 'ewallet', 32.70, NULL, 'Table for 2');

-- Insert sample order details
INSERT INTO order_details (order_id, item_id, quantity, price) VALUES
(1, 1, 1, 12.90),
(1, 6, 1, 4.90),
(1, 3, 1, 8.90),
(2, 4, 1, 16.90),
(3, 2, 1, 24.90),
(3, 5, 1, 6.90);

-- Create indexes for better performance
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_order_details_order_id ON order_details(order_id);
CREATE INDEX idx_menu_items_category ON menu_items(category);
CREATE INDEX idx_menu_items_active ON menu_items(active);

-- Create view for order summary
CREATE VIEW order_summary AS
SELECT 
    o.id,
    o.user_id,
    u.name as customer_name,
    u.email as customer_email,
    u.phone as customer_phone,
    o.order_type,
    o.status,
    o.payment_status,
    o.payment_method,
    o.total_amount,
    o.delivery_address,
    o.pickup_time,
    o.special_instructions,
    o.created_at,
    COUNT(od.id) as item_count
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_details od ON o.id = od.order_id
GROUP BY o.id;

-- Create view for popular items
CREATE VIEW popular_items AS
SELECT 
    mi.id,
    mi.name,
    mi.description,
    mi.price,
    mi.category,
    mi.image,
    COUNT(od.id) as order_count,
    SUM(od.quantity) as total_quantity
FROM menu_items mi
LEFT JOIN order_details od ON mi.id = od.item_id
WHERE mi.active = TRUE
GROUP BY mi.id
ORDER BY order_count DESC, total_quantity DESC;

-- Create trigger to update user points when order is completed
DELIMITER //
CREATE TRIGGER update_user_points 
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        UPDATE users 
        SET points = points + FLOOR(NEW.total_amount)
        WHERE id = NEW.user_id AND is_member = TRUE;
    END IF;
END//
DELIMITER ;

-- Create trigger to log order status changes
DELIMITER //
CREATE TRIGGER log_order_status_change
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status != OLD.status THEN
        INSERT INTO activity_logs (user_id, action, details)
        VALUES (NEW.user_id, 'order_status_changed', 
                CONCAT('Order #', NEW.id, ' status changed from ', OLD.status, ' to ', NEW.status));
    END IF;
END//
DELIMITER ;

-- Create stored procedure for daily sales report
DELIMITER //
CREATE PROCEDURE GetDailySalesReport(IN report_date DATE)
BEGIN
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as total_orders,
        SUM(total_amount) as total_sales,
        AVG(total_amount) as average_order_value,
        COUNT(CASE WHEN order_type = 'delivery' THEN 1 END) as delivery_orders,
        COUNT(CASE WHEN order_type = 'takeaway' THEN 1 END) as takeaway_orders,
        COUNT(CASE WHEN order_type = 'dine-in' THEN 1 END) as dine_in_orders
    FROM orders 
    WHERE DATE(created_at) = report_date 
    AND status = 'completed'
    GROUP BY DATE(created_at);
END//
DELIMITER ;

-- Create stored procedure for menu item sales report
DELIMITER //
CREATE PROCEDURE GetMenuItemSalesReport(IN start_date DATE, IN end_date DATE)
BEGIN
    SELECT 
        mi.name,
        mi.category,
        SUM(od.quantity) as total_quantity_sold,
        SUM(od.quantity * od.price) as total_revenue,
        COUNT(DISTINCT od.order_id) as number_of_orders
    FROM menu_items mi
    JOIN order_details od ON mi.id = od.item_id
    JOIN orders o ON od.order_id = o.id
    WHERE DATE(o.created_at) BETWEEN start_date AND end_date
    AND o.status = 'completed'
    GROUP BY mi.id, mi.name, mi.category
    ORDER BY total_quantity_sold DESC;
END//
DELIMITER ;

-- Insert sample activity logs
INSERT INTO activity_logs (user_id, action, details) VALUES
(1, 'user_registered', 'Admin user account created'),
(2, 'user_registered', 'Staff user account created'),
(1, 'order_placed', 'Order #1 placed for delivery'),
(1, 'payment_completed', 'Payment completed for order #1'),
(1, 'order_status_changed', 'Order #1 status changed to completed');

COMMIT;
