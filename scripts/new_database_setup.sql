-- Drop existing database and create new one based on ERD
DROP DATABASE IF EXISTS cafe_ordering_system;
CREATE DATABASE cafe_ordering_system;
USE cafe_ordering_system;

-- Create CUSTOMER table
CREATE TABLE CUSTOMER (
    CUST_ID INT AUTO_INCREMENT PRIMARY KEY,
    CUST_NAME VARCHAR(100) NOT NULL,
    CUST_NPHONE VARCHAR(20) NOT NULL,
    CUST_EMAIL VARCHAR(100) UNIQUE NOT NULL,
    CUST_PASSWORD VARCHAR(255) NOT NULL,
    MEMBERSHIP ENUM('basic', 'premium', 'vip') DEFAULT 'basic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create STAFF table
CREATE TABLE STAFF (
    STAFF_ID INT AUTO_INCREMENT PRIMARY KEY,
    STAFF_NAME VARCHAR(100) NOT NULL,
    STAFF_PNUMBER VARCHAR(20) NOT NULL,
    STAFF_EMAIL VARCHAR(100) UNIQUE NOT NULL,
    STAFF_PASSWORD VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create ADMIN table
CREATE TABLE ADMIN (
    ADM_ID INT AUTO_INCREMENT PRIMARY KEY,
    ADM_USERNAME VARCHAR(50) UNIQUE NOT NULL,
    ADM_PASSWORD VARCHAR(255) NOT NULL,
    ADM_EMAIL VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create MENU_ITEM table
CREATE TABLE MENU_ITEM (
    ITEM_ID INT AUTO_INCREMENT PRIMARY KEY,
    ITEM_NAME VARCHAR(100) NOT NULL,
    ITEM_PRICE DECIMAL(10, 2) NOT NULL,
    ITEM_DESCRIPTION TEXT,
    ITEM_CATEGORY VARCHAR(50) NOT NULL,
    STOCK_LEVEL INT DEFAULT 0,
    ADMIN_ID INT,
    image VARCHAR(255),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ADMIN_ID) REFERENCES ADMIN(ADM_ID) ON DELETE SET NULL
);

-- Create ORDER table
CREATE TABLE `ORDER` (
    ORDER_ID INT AUTO_INCREMENT PRIMARY KEY,
    ORDER_TIME TIME NOT NULL,
    ORDER_DATE DATE NOT NULL,
    ORDER_TYPE ENUM('delivery', 'takeaway', 'dine-in') NOT NULL,
    ORDER_STATUS ENUM('pending', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled') DEFAULT 'pending',
    TOT_AMOUNT DECIMAL(10, 2) NOT NULL,
    DELIVERY_ADDRESS TEXT,
    PAYMENT_METHOD VARCHAR(50),
    PAYMENT_STATUS ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    CUST_ID INT NOT NULL,
    STAFF_ID INT,
    special_instructions TEXT,
    pickup_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (CUST_ID) REFERENCES CUSTOMER(CUST_ID) ON DELETE CASCADE,
    FOREIGN KEY (STAFF_ID) REFERENCES STAFF(STAFF_ID) ON DELETE SET NULL
);

-- Create ORDER_LISTING table
CREATE TABLE ORDER_LISTING (
    LISTING_ID INT AUTO_INCREMENT PRIMARY KEY,
    ORDER_QUANTITY INT NOT NULL,
    ORDER_ID INT NOT NULL,
    ITEM_ID INT NOT NULL,
    item_price DECIMAL(10, 2) NOT NULL,
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ORDER_ID) REFERENCES `ORDER`(ORDER_ID) ON DELETE CASCADE,
    FOREIGN KEY (ITEM_ID) REFERENCES MENU_ITEM(ITEM_ID) ON DELETE CASCADE
);

-- Create activity logs table for tracking
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('customer', 'staff', 'admin') NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin users with hashed passwords
INSERT INTO ADMIN (ADM_USERNAME, ADM_PASSWORD, ADM_EMAIL) VALUES 
('admin_sara', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin.sara@email.com'),
('sofeaJane_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin.sofeaJanee@gmail.com'),
('irfanIzzany_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin.irfanIzzaneyy16@gmail.com'),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@cafedelights.com');

-- Insert default staff users
INSERT INTO STAFF (STAFF_NAME, STAFF_PNUMBER, STAFF_EMAIL, STAFF_PASSWORD) VALUES 
('Staff User', '0987654321', 'staff@cafedelights.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('John Staff', '0123456789', 'john.staff@cafedelights.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample customers
INSERT INTO CUSTOMER (CUST_NAME, CUST_NPHONE, CUST_EMAIL, CUST_PASSWORD, MEMBERSHIP) VALUES 
('John Customer', '0111234567', 'john.customer@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'basic'),
('Jane Premium', '0119876543', 'jane.premium@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'premium');

-- Insert sample menu items
INSERT INTO MENU_ITEM (ITEM_NAME, ITEM_PRICE, ITEM_DESCRIPTION, ITEM_CATEGORY, STOCK_LEVEL, ADMIN_ID, image) VALUES
-- Coffee & Beverages
('Cappuccino', 5.50, 'Rich espresso with steamed milk and foam', 'Coffee', 50, 1, 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400'),
('Latte', 5.00, 'Smooth espresso with steamed milk', 'Coffee', 45, 1, 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400'),
('Americano', 4.50, 'Bold espresso with hot water', 'Coffee', 40, 1, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400'),
('Mocha', 6.00, 'Espresso with chocolate and steamed milk', 'Coffee', 35, 1, 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400'),
('Iced Coffee', 4.90, 'Refreshing iced coffee with milk and sugar', 'Beverages', 30, 1, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400'),
('Fresh Orange Juice', 3.90, 'Freshly squeezed orange juice', 'Beverages', 25, 1, 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400'),

-- Main Courses
('Grilled Chicken Burger', 12.90, 'Juicy grilled chicken breast with fresh lettuce, tomato, and our special sauce', 'Main Course', 20, 1, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400'),
('Beef Steak', 24.90, 'Premium beef steak cooked to perfection with mashed potatoes and vegetables', 'Main Course', 15, 1, 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400'),
('Fish and Chips', 14.90, 'Crispy battered fish with golden fries and tartar sauce', 'Main Course', 18, 1, 'https://images.unsplash.com/photo-1544982503-9f984c14501a?w=400'),
('Margherita Pizza', 16.90, 'Classic pizza with tomato sauce, mozzarella cheese, and fresh basil', 'Pizza', 12, 1, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400'),

-- Appetizers & Salads
('Caesar Salad', 8.90, 'Fresh romaine lettuce with parmesan cheese, croutons, and caesar dressing', 'Salads', 22, 1, 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400'),
('Chicken Wings', 9.90, 'Spicy buffalo chicken wings with blue cheese dip', 'Appetizers', 25, 1, 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=400'),

-- Pastries & Desserts
('Croissant', 3.50, 'Buttery, flaky French pastry', 'Pastry', 30, 1, 'https://images.unsplash.com/photo-1555507036-ab794f4afe5b?w=400'),
('Chocolate Cake', 6.90, 'Rich chocolate cake with chocolate ganache and fresh berries', 'Desserts', 10, 1, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400'),
('Tiramisu', 7.90, 'Classic Italian dessert with coffee-soaked ladyfingers and mascarpone', 'Desserts', 8, 1, 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=400'),
('Cheesecake', 7.50, 'Creamy New York style cheesecake with berry compote', 'Desserts', 12, 1, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400');

-- Insert sample orders
INSERT INTO `ORDER` (ORDER_TIME, ORDER_DATE, ORDER_TYPE, ORDER_STATUS, TOT_AMOUNT, DELIVERY_ADDRESS, PAYMENT_METHOD, PAYMENT_STATUS, CUST_ID, STAFF_ID, special_instructions) VALUES
('12:30:00', '2024-01-15', 'delivery', 'completed', 25.80, '123 Main Street, City', 'fpx', 'completed', 1, 1, 'Please ring the doorbell'),
('14:15:00', '2024-01-15', 'takeaway', 'ready', 18.90, NULL, 'credit_card', 'completed', 1, 1, 'Extra sauce please'),
('19:45:00', '2024-01-15', 'dine-in', 'preparing', 32.70, NULL, 'ewallet', 'completed', 2, 2, 'Table for 2');

-- Insert sample order listings
INSERT INTO ORDER_LISTING (ORDER_QUANTITY, ORDER_ID, ITEM_ID, item_price) VALUES
(1, 1, 1, 5.50),
(1, 1, 6, 3.90),
(1, 1, 3, 4.50),
(1, 2, 10, 16.90),
(1, 3, 8, 24.90),
(1, 3, 14, 6.90);

-- Create indexes for better performance
CREATE INDEX idx_order_cust_id ON `ORDER`(CUST_ID);
CREATE INDEX idx_order_staff_id ON `ORDER`(STAFF_ID);
CREATE INDEX idx_order_status ON `ORDER`(ORDER_STATUS);
CREATE INDEX idx_order_date ON `ORDER`(ORDER_DATE);
CREATE INDEX idx_order_listing_order_id ON ORDER_LISTING(ORDER_ID);
CREATE INDEX idx_order_listing_item_id ON ORDER_LISTING(ITEM_ID);
CREATE INDEX idx_menu_item_category ON MENU_ITEM(ITEM_CATEGORY);
CREATE INDEX idx_menu_item_active ON MENU_ITEM(active);
CREATE INDEX idx_customer_email ON CUSTOMER(CUST_EMAIL);
CREATE INDEX idx_staff_email ON STAFF(STAFF_EMAIL);
CREATE INDEX idx_admin_username ON ADMIN(ADM_USERNAME);

-- Create views for easier data access
CREATE VIEW order_summary AS
SELECT 
    o.ORDER_ID,
    o.ORDER_TIME,
    o.ORDER_DATE,
    o.ORDER_TYPE,
    o.ORDER_STATUS,
    o.TOT_AMOUNT,
    o.DELIVERY_ADDRESS,
    o.PAYMENT_METHOD,
    o.PAYMENT_STATUS,
    c.CUST_NAME as customer_name,
    c.CUST_EMAIL as customer_email,
    c.CUST_NPHONE as customer_phone,
    s.STAFF_NAME as staff_name,
    COUNT(ol.LISTING_ID) as item_count
FROM `ORDER` o
JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
LEFT JOIN STAFF s ON o.STAFF_ID = s.STAFF_ID
LEFT JOIN ORDER_LISTING ol ON o.ORDER_ID = ol.ORDER_ID
GROUP BY o.ORDER_ID;

-- Create view for popular items
CREATE VIEW popular_items AS
SELECT 
    mi.ITEM_ID,
    mi.ITEM_NAME,
    mi.ITEM_DESCRIPTION,
    mi.ITEM_PRICE,
    mi.ITEM_CATEGORY,
    mi.image,
    mi.STOCK_LEVEL,
    COUNT(ol.LISTING_ID) as order_count,
    SUM(ol.ORDER_QUANTITY) as total_quantity
FROM MENU_ITEM mi
LEFT JOIN ORDER_LISTING ol ON mi.ITEM_ID = ol.ITEM_ID
WHERE mi.active = TRUE
GROUP BY mi.ITEM_ID
ORDER BY order_count DESC, total_quantity DESC;

COMMIT;
