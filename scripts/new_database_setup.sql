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

INSERT INTO `menu_item` (`ITEM_ID`, `ITEM_NAME`, `ITEM_PRICE`, `ITEM_DESCRIPTION`, `ITEM_CATEGORY`, `STOCK_LEVEL`, `ADMIN_ID`, `image`, `active`, `created_at`, `updated_at`) VALUES
(657, 'Curry Mee Set', 23.10, 'A flavorful Malaysian noodle dish featuring yellow noodles in a rich and spicy coconut curry broth', 'Local Courses', 25, 1, 'https://woonheng.com/wp-content/uploads/2020/10/Curry-Laksa-Step11-1024x576.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(658, 'Spaghetti Spicy Buttermilk Set', 23.10, 'Spaghetti tossed in a creamy, spicy buttermilk sauce infused with chili, curry leaves, and aromatic spices', 'Italian Cuisines', 40, 1, 'https://i.pinimg.com/736x/0f/1b/df/0f1bdf26296527c19f45ec854dcf423e.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(659, 'Caesar Salad', 19.60, 'Crisp romaine lettuce tossed with Caesar dressing, croutons, and grated parmesan cheese. Often topped with grilled chicken', 'Salads', 28, 1, 'https://i.pinimg.com/736x/00/38/9d/00389ddab812fdc051465e0d21b83ee8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(660, 'Mushroom Soup', 14.00, 'A creamy, savory soup made from blended mushrooms, garlic, and herbs. Smooth in texture and rich in flavor', 'Side Dishes', 15, 1, 'https://i.pinimg.com/736x/fc/3d/d8/fc3dd8e101ee74115f0377a58e88ff6b.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(661, 'Tomato Soup with Sourdough Grilled Cheese Toast', 19.60, 'Tangy tomato soup served with crispy, golden-brown sourdough grilled cheese. A comforting classic', 'Side Dishes', 25, 1, 'https://i.pinimg.com/736x/36/fa/39/36fa39e65e5e0866b4a7e160e534db94.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(662, 'Spaghetti Aglio Olio Mushroom', 18.90, 'Spaghetti sautéed in garlic-infused olive oil, chili flakes, and mushrooms. Simple, flavorful, and fresh', 'Italian Cuisines', 43, 1, 'https://i.pinimg.com/736x/fb/e4/97/fbe497bd211a352ad26b22512dd7a0e8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(663, 'Spaghetti Carbonara Chicken and Mushroom', 23.10, 'Creamy egg-based carbonara sauce with chicken and mushrooms, topped with cheese and black pepper. Rich and indulgent', 'Italian Cuisines', 55, 1, 'https://i.pinimg.com/736x/b1/3c/0f/b13c0f98b4d581a1be1cda07ae6d4ad8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(664, 'Spaghetti Spicy Buttermilk Chicken', 20.30, 'Spaghetti coated in spicy buttermilk sauce and topped with crispy chicken pieces. Creamy, spicy, and satisfying', 'Italian Cuisines', 44, 2, 'https://www.shutterstock.com/image-photo/creamy-buttermilk-chicken-spaghetti-herbs-600nw-2624595051.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(665, 'Spaghetti Bolognese', 22.40, 'Spaghetti smothered in slow-cooked tomato and minced meat sauce, seasoned with herbs and spices', 'Italian Cuisines', 16, 2, 'https://i.pinimg.com/736x/94/ed/51/94ed516e1d1ed82e6a1da3c86ea0877a.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(666, 'Spaghetti Meatballs', 23.10, 'Spaghetti with juicy homemade meatballs in savory marinara sauce, topped with parmesan cheese', 'Italian Cuisines', 22, 2, 'https://i.pinimg.com/736x/4c/3b/b7/4c3bb70def222f23285f3b66ba559103.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(667, 'Beef Lasagna', 23.10, 'Layers of pasta, seasoned beef, béchamel, and tomato sauce baked to perfection. Cheesy and hearty', 'Italian Cuisines', 29, 2, 'https://i.pinimg.com/736x/6e/04/05/6e0405bc7c4b4430077603049773440f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(668, 'Grilled Chicken Chop', 24.50, 'Juicy grilled chicken thigh served with brown or black pepper sauce and classic sides like mashed potatoes', 'Western Favorites', 22, 2, 'https://i.pinimg.com/736x/b2/08/7a/b2087a33c0b5945e33807a2fafcc3753.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(669, 'Shrimp and Fries', 25.90, 'Crispy golden shrimp served with seasoned fries and dipping sauce. A light yet satisfying combo', 'Side Dishes', 26, 2, 'https://i.pinimg.com/736x/b2/3e/5d/b23e5d783580a4311c615329d52901cb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(670, 'Hainanese Chicken Chop', 24.50, 'Breaded chicken chop in sweet tomato-based Hainanese gravy with peas and fries. A local twist on Western cuisine', 'Western Favorites', 22, 2, 'https://i.pinimg.com/736x/5e/01/e1/5e01e155fc1edec49e04d650d4e7a941.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(671, 'Swedish Meatballs', 26.60, 'Juicy meatballs in creamy brown gravy with mashed potatoes and lingonberry sauce. Comforting and hearty', 'Western Favorites', 30, 2, 'https://i.pinimg.com/736x/2b/6a/eb/2b6aeb48f0120a72486fc9164f75b0f8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(672, 'Breaded Chicken Chop', 22.40, 'Crispy chicken chop coated in golden breadcrumbs and served with mushroom or pepper sauce', 'Western Favorites', 30, 2, 'https://i.pinimg.com/736x/70/d7/d7/70d7d7de24e3711c09b3a6775e66dabf.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(673, 'Fish and Chips', 27.30, 'Battered fish fillet fried to golden perfection with fries and tartar sauce. A British classic', 'Western Favorites', 39, 2, 'https://i.pinimg.com/736x/31/61/e8/3161e808b41a7d2b91335b449616c298.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(674, 'Deep Fried Salted Egg Squid', 20.30, 'Crispy fried squid in savory salted egg yolk sauce with curry leaves. Rich and crunchy', 'Side Dishes', 24, 3, 'https://i.pinimg.com/736x/20/47/11/204711faf52e4882057170a647f94701.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(675, 'Tuna Melt Sandwich', 20.30, 'Creamy tuna filling with melted cheese toasted between golden-brown bread slices. Warm and savory', 'Side Dishes', 41, 3, 'https://images.themodernproper.com/production/posts/2023/TunaMelt_8.jpg?w=1200&q=82&auto=format&fit=crop&dm=1682896760&s=4bf022874e817675156a954c02455df9', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(676, 'Kuey Teow Goreng', 21.00, 'Flat rice noodles stir-fried with soy sauce, egg, vegetables, and protein. Smoky and flavorful', 'Local Courses', 27, 3, 'https://homecookingwithsomjit.com/media/2022/12/Vegetarian-Fried-Kuey-Teow.webp', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(677, 'Kampung Fried Rice', 23.10, 'Spicy fried rice with anchovies, egg, and vegetables. Served with sambal and crackers', 'Local Courses', 33, 3, 'https://www.elmundoeats.com/wp-content/uploads/2024/04/Nasi-goreng-kampung-or-village-style-fried-rice.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(678, 'Mashed Potato', 9.10, 'Creamy mashed potatoes whipped smooth with butter. Comforting and classic', 'Side Dishes', 20, 3, 'https://cdn.apartmenttherapy.info/image/upload/f_jpg,q_auto:eco,c_fill,g_auto,w_1500,ar_4:3/k%2FPhoto%2FRecipes%2F2024-11-loaded-mashed-potatoes%2Floaded-mashed-potatoes-4', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(679, 'Mantao (6 pcs)', 4.90, 'Soft, fluffy steamed buns lightly fried. Great with rich sauces', 'Side Dishes', 20, 3, 'https://i0.wp.com/www.angsarap.net/wp-content/uploads/2023/11/Fried-Mantou-with-Condensed-Milk-Wide.jpg?ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(680, 'Classic Beef Burger', 22.40, 'Grilled beef patty with lettuce, tomato, cheese in a toasted bun. A satisfying classic', 'Western Favorites', 10, 3, 'https://searafoodsme.com/wp-content/uploads/2022/04/Beef-Burger1080x720px.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(681, 'Salted Egg Yolk Chicken Burger', 29.40, 'Crispy chicken thigh glazed in salted egg yolk sauce in a bun with creamy dressing', 'Western Favorites', 16, 3, 'https://staging.myburgerlab.com/static/img/menu/products/v3/img_chicken_salted-egg-yolk-burger.webp', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(682, 'Crispy Fish Burger', 26.60, 'Fried fish fillet with lettuce, cheese, and tartar sauce in a soft bun', 'Western Favorites', 26, 3, 'https://www.kitchensanctuary.com/wp-content/uploads/2014/01/Crispy-Fish-Burger-with-Shoestring-Fries-Recipe-square-FS.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(683, 'French Fries', 11.20, 'Golden, crispy fries seasoned and perfect as a snack or side', 'Side Dishes', 10, 3, 'https://sausagemaker.com/wp-content/uploads/Homemade-French-Fries_8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(684, 'Fried Mushrooms', 13.30, 'Crispy fried mushroom bites with juicy centers. Perfect for dipping', 'Side Dishes', 36, 3, 'https://northeastnosh.com/wp-content/uploads/2024/11/Crispy-Fried-Mushrooms.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(685, 'Panna Cotta', 9.80, 'Silky Italian cream dessert with a melt-in-mouth texture, often served with fruit or sauce', 'Desserts', 15, 3, 'https://www.cucinabyelena.com/wp-content/uploads/2023/06/Panna-Cotta-28-1.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(686, 'Apple Juice', 11.90, 'Smooth, sweet juice from fresh apples. Crisp and refreshing', 'Beverages', 12, 3, 'https://i.pinimg.com/736x/21/ff/7e/21ff7ef76f32d75208abd60ad024201b.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(687, 'Orange Juice', 11.90, 'Tangy, bright citrus juice from ripe oranges. Full of vitamin C', 'Beverages', 12, 3, 'https://i.pinimg.com/736x/84/20/82/8420829bd36d33eb912543f97174b5cb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(688, 'Watermelon Juice', 11.90, 'Cool, sweet juice made from juicy watermelon. Very refreshing', 'Beverages', 10, 3, 'https://i.pinimg.com/736x/7c/ff/dd/7cffdd161e9db0b4fac50b60e348c735.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(689, 'Pineapple Juice', 11.90, 'Tropical, tangy-sweet juice with vibrant flavor and acidity', 'Beverages', 18, 3, 'https://i.pinimg.com/736x/b9/e4/e8/b9e4e87b2ca8129b803987b6e9be77e9.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(690, 'Lemonade', 9.80, 'Zesty drink balancing lemon juice and sweetness. Classic and thirst-quenching', 'Beverages', 18, 3, 'https://i.pinimg.com/736x/2a/80/3d/2a803db9c63dd34cae5013a94b048111.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(691, 'Avocado Juice', 11.90, 'Creamy and smooth juice blended with avocado and milk. Rich and energizing', 'Beverages', 21, 3, 'https://i.pinimg.com/736x/f8/16/82/f81682ee5177f4782de3bfc506ab4f03.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(692, 'Strawberry Choux Pastry', 5.60, 'Choux puff with vanilla cream and strawberry glaze. Fruity and fluffy', 'Pastries', 30, 3, 'https://youthsweets.com/wp-content/uploads/2022/10/StrawberryChouxauCraquelin_Feature.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(693, 'Milky Chocolate Choux Pastry', 5.60, 'Choux pastry filled with milky chocolate ganache. Rich and soft', 'Pastries', 30, 3, 'https://i.pinimg.com/736x/83/40/33/834033773bbae20054410bb34183c0e7.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(694, 'Pistachio Cromboloni', 12.60, 'Croissant-bomboloni hybrid filled with pistachio cream. Buttery and nutty', 'Pastries', 30, 3, 'https://statik.tempo.co/data/2023/12/11/id_1262189/1262189_720.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(695, 'Chocolate Hazelnut Flat Croissant', 9.10, 'Flaky croissant with chocolate hazelnut filling. Crispy and indulgent', 'Pastries', 13, 3, 'https://strictlydowntowndubai.com/wp-content/uploads/2024/04/flat-croissant-in-dubai-mall.png', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(696, 'Mixed Berries Pavlova Nest', 12.60, 'Crispy meringue nest with cream and fresh berries. Sweet and airy', 'Pastries', 10, 2, 'https://paarman.co.za/cdn/shop/articles/meringue-nests-berries-cream_8fef8791-53cd-4918-802a-82247cf9d1d2.jpg?v=1748610410', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(697, 'Milky Chocolate Cromboloni', 12.60, 'Flaky pastry filled with rich milky chocolate cream', 'Pastries', 17, 1, 'https://www.shutterstock.com/image-photo/cromboloni-crombolini-croissant-bomboloni-round-600nw-2407278015.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(698, 'Berry Pavlova Cromboloni', 12.60, 'Cromboloni filled with berry cream and topped with fresh fruits', 'Pastries', 18, 1, 'https://driscolls.imgix.net/-/media/assets/recipes/mixed-berry-pavlova.ashx?w=1074&h=806&fit=crop&crop=entropy&q=50&auto=format,compress&cs=srgb&ixlib=imgixjs-3.4.2', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(699, 'Berry Pavlova Slice', 12.60, 'Slice with meringue, whipped cream, and fresh berries', 'Desserts', 10, 1, 'https://i0.wp.com/bryonysbakes.com/wp-content/uploads/2021/09/DSC02033.jpg?ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(701, 'Carrot Cake Slice', 12.60, 'Moist spiced cake with cream cheese frosting and shredded carrots', 'Desserts', 10, 1, 'https://static01.nyt.com/images/2020/11/01/dining/Carrot-Cake-textless/Carrot-Cake-textless-mediumThreeByTwo440.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(702, 'Chocolate Pavlova Slice', 12.60, 'Chocolate meringue slice with cream and cocoa flavor', 'Desserts', 10, 1, 'https://i0.wp.com/espressoandlime.com/wp-content/uploads/2025/02/Dark-Chocolate-Pavlova-02-scaled.jpg?resize=700%2C1003&ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(703, 'Chocolate Rocher Cheesecake Slice', 12.60, 'Chocolate cheesecake with hazelnut crunch, inspired by Ferrero Rocher', 'Desserts', 10, 2, 'https://www.littlesugarsnaps.com/wp-content/uploads/2021/12/ferrero-rocher-cheesecake-square.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(704, 'Key Lime Cheesecake Slice', 11.90, 'Creamy cheesecake with tangy lime flavor. Light and zesty', 'Desserts', 10, 2, 'https://bakerbynature.com/wp-content/uploads/2019/04/keylimecheesecake-1-of-1-2-500x500.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(705, 'Mango Cheesecake Slice', 11.90, 'Tropical mango-topped creamy cheesecake. Fruity and smooth', 'Desserts', 19, 2, 'https://assets.epicurious.com/photos/57b208990e4be0011c1bf088/master/pass/mango-cheesecake.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(706, 'Strawberry Momofuku Slice', 13.30, 'Layered cake with strawberry cream and crunch, Momofuku-style', 'Desserts', 11, 2, 'https://i.pinimg.com/736x/b8/fa/c6/b8fac65230e940ac4ddc773f0d308c2b.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(707, 'Raspberry Pistachio Cake Slice', 12.60, 'Moist cake with tart raspberries and nutty pistachio', 'Desserts', 10, 2, 'https://juliemarieeats.com/wp-content/uploads/2024/02/Raspberry-Pistachio-Cake-9-1-scaled.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(708, 'Speculoos Cheesecake Slice', 12.60, 'Cheesecake with spiced speculoos (Biscoff) flavor and crunch', 'Desserts', 10, 2, 'https://www.abakingjourney.com/wp-content/uploads/2020/11/Speculoos-Biscoff-Cheesecake-1.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(709, 'Salted Caramel Almond Brittle Slice', 14.40, 'Cake with gooey caramel and crunchy almond brittle topping', 'Desserts', 21, 2, 'https://minimalistbaker.com/wp-content/uploads/2021/09/Easy-Almond-Brittle-SQUARE.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(710, 'Tiramisu Cheesecake Slice', 12.60, 'Tiramisu-flavored cheesecake with coffee and cocoa notes', 'Desserts', 29, 2, 'https://amyinthekitchen.com/wp-content/uploads/2020/09/tiramisu-cheesecake-slice-on-a-plate.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(711, 'Macadamia Baked Cheesecake Slice', 13.30, 'Baked cheesecake with buttery macadamia nuts', 'Desserts', 33, 2, 'https://www.thecookierookie.com/wp-content/uploads/2019/08/Macadamia-Caramel-Cheesecake-800px-Cookie-Rookie-1.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(712, 'Chocolate Rocher Tart Slice', 11.90, 'Chocolate ganache tart with hazelnut crunch. Inspired by Rocher', 'Desserts', 38, 2, 'https://www.hungrypinner.com/wp-content/uploads/2022/07/ferrero-rocher-nutella-tart-process-3.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(713, 'Dark Chocolate Tart', 11.20, 'Bittersweet dark chocolate in a crisp tart shell', 'Desserts', 40, 3, 'https://i.pinimg.com/736x/a2/ef/94/a2ef9410c31232087e898bffee7b37b9.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(714, 'Loaded Blueberry Tart', 11.20, 'Tart filled with cream and topped with fresh blueberries', 'Desserts', 32, 3, 'https://www.tasteofhome.com/wp-content/uploads/2023/07/Blueberry-Tart-PPP18_4701_C04_25_2b_KSedit.jpg?fit=700%2C467', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(715, 'Strawberry Bliss Tart', 11.90, 'Vanilla cream tart topped with fresh strawberries', 'Desserts', 39, 3, 'https://i.pinimg.com/736x/8b/39/33/8b39336ca6b4709087ef86d19b692a7d.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(716, 'Lemon Cream Tart', 11.20, 'Zesty lemon curd and lemon cream in a buttery shell', 'Desserts', 13, 3, 'https://i0.wp.com/www.growingupcali.com/wp-content/uploads/2022/03/Growing-Up-Cali-Lemon-Tartlets-45.jpg?fit=800%2C1000&ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(717, 'Strawberry Tartlet', 9.10, 'Mini tart with cream and glossy strawberry topping', 'Desserts', 21, 3, 'https://sundaytable.co/wp-content/uploads/2025/04/mini-strawberry-tartlets-with-custard.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(718, 'Chocolate Hazelnut Tartlet', 10.50, 'Mini tart filled with rich chocolate and hazelnuts', 'Desserts', 38, 3, 'https://theskinnyfoodco.com/cdn/shop/articles/the-best-chocolate-hazelnut-tartlets-786581.jpg?v=1666894052', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(719, 'Red Velvet Cream Cheese Soft Cookies', 7.70, 'Red velvet cookies with cream cheese center. Soft and rich', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/54/20/c0/5420c01fce2e00a2ac59a753e51572aa.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(720, 'Original Brown Butter Soft Cookies', 7.00, 'Chewy cookies made with nutty browned butter', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/c1/a0/95/c1a095dd5cc80edf81495638855e4877.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(721, 'Dark Chocolate Soft Cookies', 7.70, 'Fudgy cookies with intense dark chocolate flavor', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/be/7a/67/be7a67deeeda73554433d3bb283b44d3.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(722, 'Matcha White Chocolate Soft Cookies', 7.70, 'Matcha cookies with sweet white chocolate chunks', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/11/dc/a3/11dca3785d212aa284bbb87ff37e49e7.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(723, 'Chocolate Chip Soft Cookies', 7.70, 'Classic soft cookies with melty chocolate chips', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/c2/73/fa/c273fa0c99c56497b1c49ef49ce5746f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(724, 'Quesadilla', 21.00, 'Grilled tortilla filled with cheese and your choice of meat or veggies', 'Mexican Cuisines', 10, 3, 'https://i.pinimg.com/736x/aa/a8/83/aaa8833a0a9f338c64d5658586d66a79.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(725, 'Fajitas', 23.80, 'Grilled marinated meat strips with peppers and onions. Served with tortillas', 'Mexican Cuisines', 15, 3, 'https://www.eatingbirdfood.com/wp-content/uploads/2024/04/healthy-chicken-fajitas-hero-new.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(726, 'Peruvian Chicken', 19.60, 'Juicy grilled chicken with Peruvian spices and green sauce', 'International Cuisines', 19, 2, 'https://i.pinimg.com/736x/47/ab/5e/47ab5e8d6c47151899fe57ab1fc6790f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(727, 'Crunchy Tacos (3 pieces)', 22.40, 'Three crispy taco shells filled with seasoned meat, cheese, and salsa', 'Mexican Cuisines', 16, 2, 'https://i.pinimg.com/736x/78/25/02/7825024db57999f02f082331b9e83d22.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(728, 'Soft Tacos (3 pieces)', 21.70, 'Soft tortillas filled with meat, greens, cheese, and salsa', 'Mexican Cuisines', 29, 2, 'https://i.pinimg.com/736x/87/7f/1b/877f1b1402ae12382b27d175512eab56.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(729, 'Smash Mac Tacos', 9.80, 'Tacos with smashed beef patties and mac & cheese. Cheesy and bold', 'Mexican Cuisines', 35, 2, 'https://i.pinimg.com/736x/16/ed/d2/16edd2dc0209ea6c0427d0a36ccd7e0a.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(730, 'Prawn Gambas', 26.60, 'Sautéed prawns in garlic olive oil with herbs and chili', 'International Cuisines', 38, 2, 'https://i.pinimg.com/736x/01/56/12/015612451d03dda6899bb2ee296de787.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(731, 'Seafood Chowder', 17.50, 'Creamy soup with seafood chunks, potatoes, and vegetables', 'Side Dishes', 26, 2, 'https://i.pinimg.com/736x/ef/8b/b0/ef8bb01f657451a69aba77370ce2b5eb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(732, 'Corn Salad', 12.60, 'Sweet corn salad with lime, herbs, and light chili', 'Salads', 24, 2, 'https://i.pinimg.com/736x/40/60/0b/40600bbecfd56538c92d409d5a56adaf.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(733, 'Chicken Tenders', 12.60, 'Golden fried chicken strips, crispy and juicy', 'Side Dishes', 36, 2, 'https://i.pinimg.com/736x/07/45/12/074512e2f6c5ee3f62cb64b807c074b9.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(734, 'Buffalo Wings', 15.40, 'Fried chicken wings tossed in spicy buffalo sauce', 'Side Dishes', 36, 2, 'https://i.pinimg.com/736x/4d/e6/7f/4de67f34255f781311fd5662b188e5b8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(735, 'Cheesy Nachos', 10.50, 'Tortilla chips with melted cheese, jalapeños, and toppings', 'Mexican Cuisines', 19, 3, 'https://i.pinimg.com/736x/f3/46/2d/f3462daf5a1ca157cecaa2fd4c11877e.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(736, 'Guacamole', 15.40, 'Creamy mashed avocado dip with lime, tomato, and onion', 'Mexican Cuisines', 19, 2, 'https://i.pinimg.com/736x/bd/22/e3/bd22e3a9c6982bc2e32f8159e3faf29f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(737, 'Cheesy Fries', 15.40, 'Fries loaded with melted cheese and sauces', 'Side Dishes', 40, 1, 'https://i.pinimg.com/736x/38/9a/5b/389a5b577dfef793b3405ba995b73bab.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(738, 'Cheese Platter', 38.50, 'Selection of cheeses, crackers, fruits, and nuts', 'Mexican Cuisines', 10, 1, 'https://i.pinimg.com/736x/5e/1b/d3/5e1bd3e109628aa8fa6dd7a2f53181f6.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(739, 'Surf & Turf Burger', 69.30, 'Burger with beef patty and grilled prawns in a toasted bun', 'Western Favorites', 13, 1, 'https://i.pinimg.com/736x/19/b5/7b/19b57bf02451c21faf8745f913d1111f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(740, 'Mexicana Avocado Beef Burger', 26.60, 'Beef burger with avocado, salsa, and cheese', 'Western Favorites', 13, 1, 'https://d31qjkbvvkyanm.cloudfront.net/images/recipe-images/carne-asada-guacamole-burgers-detail-b270aeb5.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(741, 'Signature Penne Taco Pasta', 21.00, 'Penne pasta in cheesy taco-style sauce with meat', 'International Cuisines', 10, 1, 'https://i.pinimg.com/736x/9a/db/e3/9adbe3eeb2a17f89ef01c5d30b762c88.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(742, 'Cheesy Mac and Cheese', 20.30, 'Baked macaroni in rich, gooey cheese sauce', 'Western Favorites', 18, 1, 'https://i.pinimg.com/736x/29/11/b2/2911b213e6d0986e5a975c3ec5177f17.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(743, 'Prawn Roll', 31.50, 'Toasted roll filled with prawns, lettuce, and creamy dressing', 'Side Dishes', 20, 2, 'https://i.pinimg.com/736x/0c/86/12/0c8612b388f240ee54f171b14b286277.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(744, 'Margherita Pizza', 17.50, 'Classic pizza with tomato, mozzarella, and basil', 'Italian Cuisines', 12, 3, 'https://i.pinimg.com/736x/57/e7/a1/57e7a19334571946391a7430fcb86202.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(745, 'Seafood Fiesta Pizza', 26.60, 'Pizza topped with shrimp, squid, and seafood', 'Italian Cuisines', 12, 3, 'https://i.pinimg.com/736x/07/24/4c/07244c38f7df4939dc59d20eb206e3eb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(746, 'Peruvian Grilled Chicken Pizza', 23.10, 'Pizza with Peruvian-spiced grilled chicken', 'Italian Cuisines', 10, 2, 'https://i.pinimg.com/736x/d8/a1/17/d8a1175918aa45ab95ab97e1220f1195.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(747, 'Grilled Prawn Pizza', 25.20, 'Pizza topped with smoky grilled prawns', 'Italian Cuisines', 10, 1, 'https://i.pinimg.com/736x/aa/c2/0a/aac20ab55ef3a925e91b15c4ebdb5bc6.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(748, 'Sizzling Brownies', 16.80, 'Warm brownie on a sizzling plate with ice cream and chocolate sauce', 'Desserts', 30, 3, 'https://i.pinimg.com/736x/10/c7/0e/10c70e6903e07575174ef5247b7b7d8d.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(749, 'Churros', 12.60, 'Fried dough sticks coated in cinnamon sugar, served with dipping sauce', 'Desserts', 30, 1, 'https://i.pinimg.com/736x/b5/b6/a2/b5b6a248382ecd09bfcc3366cb072966.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(750, 'Cappuccino', 5.50, 'Rich espresso with steamed milk and foam', 'Coffee', 50, 1, 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(751, 'Latte', 5.00, 'Smooth espresso with steamed milk', 'Coffee', 45, 1, 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(752, 'Americano', 4.50, 'Bold espresso with hot water', 'Coffee', 40, 1, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(753, 'Mocha', 6.00, 'Espresso with chocolate and steamed milk', 'Coffee', 35, 1, 'https://ichef.bbc.co.uk/ace/standard/1600/food/recipes/the_perfect_mocha_coffee_29100_16x9.jpg.webp', 1, '2025-06-25 17:30:00', '2025-06-25 18:12:59'),
(754, 'Iced Coffee', 4.90, 'Refreshing iced coffee with milk and sugar', 'Beverages', 30, 1, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(755, 'Espresso', 3.50, 'Strong concentrated coffee shot', 'Coffee', 50, 1, 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(756, 'Macchiato', 4.80, 'Espresso with a dollop of steamed milk foam', 'Coffee', 35, 1, 'https://roastercoffees.com/wp-content/uploads/2021/05/Espresso-Macchiato-Recipe.webp', 1, '2025-06-25 17:30:00', '2025-06-25 18:12:37'),
(757, 'Flat White', 5.20, 'Double shot espresso with steamed milk', 'Coffee', 40, 1, 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(759, 'Tom Yum Soup', 16.50, 'Spicy and sour Thai soup with shrimp and mushrooms', 'International Cuisines', 20, 2, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(760, 'Green Curry Chicken', 22.40, 'Thai green curry with chicken and vegetables', 'International Cuisines', 18, 2, 'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(762, 'Nasi Lemak', 19.60, 'Malaysian coconut rice with sambal and accompaniments', 'Local Courses', 22, 3, 'https://delishglobe.com/wp-content/uploads/2025/04/Nasi-Lemak-Coconut-Rice-with-Sambal.png', 1, '2025-06-25 17:30:00', '2025-06-25 18:09:24'),
(763, 'Char Kway Teow', 21.70, 'Stir-fried flat rice noodles with prawns and Chinese sausage', 'Local Courses', 20, 3, 'https://www.feastingathome.com/wp-content/uploads/2025/01/Char-Kway-Teow-12.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:09:53'),
(764, 'Laksa', 20.30, 'Spicy noodle soup with coconut milk and seafood', 'Local Courses', 18, 3, 'https://www.elmundoeats.com/wp-content/uploads/2024/03/Penang-asam-laksa-in-a-bowl.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:10:22'),
(777, 'Mozzarella Sticks', 13.30, 'Breaded mozzarella with marinara dipping sauce', 'Appetizers', 28, 2, 'https://easyweeknightrecipes.com/wp-content/uploads/2024/04/Mozzarella-Sticks_0013-500x375.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:20:07'),
(779, 'Garlic Bread', 8.40, 'Toasted bread with garlic butter and herbs', 'Appetizers', 40, 2, 'https://spicecravings.com/wp-content/uploads/2021/09/Air-Fryer-Garlic-Bread-Featured.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:22:05'),
(780, 'Clam Chowder', 15.40, 'Creamy soup with clams and potatoes', 'Side Dishes', 20, 2, 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(788, 'New York Cheesecake', 13.30, 'Classic dense and creamy cheesecake', 'Desserts', 15, 1, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(789, 'Chocolate Lava Cake', 14.70, 'Warm chocolate cake with molten center', 'Desserts', 12, 1, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(796, 'Earl Grey Tea', 4.20, 'Classic English breakfast tea', 'Beverages', 50, 3, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(798, 'Hot Chocolate', 5.60, 'Rich hot chocolate with whipped cream', 'Beverages', 40, 3, 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(800, 'Smoothie Bowl', 16.80, 'Acai smoothie bowl with fresh fruits and granola', 'Beverages', 20, 3, 'https://www.veggiesdontbite.com/wp-content/uploads/2020/05/vegan-smoothie-bowl-FI.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:14:00'),
(801, 'Mango Smoothie', 12.60, 'Fresh mango smoothie with yogurt', 'Beverages', 25, 3, 'https://www.cubesnjuliennes.com/wp-content/uploads/2021/04/Mango-Smoothie-Recipe.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:14:19'),
(814, 'Loaded Potato Skins', 14.70, 'Crispy potato skins with cheese and bacon', 'Appetizers', 25, 2, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRz6UTP7HkNBr6p4Ur5BrwUxkF6QONRDYhfJg&s', 1, '2025-06-25 17:30:00', '2025-06-25 18:19:42');

-- Update stock levels for better inventory management
UPDATE MENU_ITEM SET STOCK_LEVEL = STOCK_LEVEL + 5 WHERE STOCK_LEVEL < 10;

-- Insert sample order listings
INSERT INTO ORDER_LISTING (ORDER_QUANTITY, ORDER_ID, ITEM_ID, item_price) VALUES
(1, 1, 657, 23.10),  
(1, 1, 658, 23.10),  
(1, 1, 659, 19.60),  
(1, 2, 660, 14.00),  
(1, 3, 661, 19.60),  
(1, 3, 662, 18.90);  

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
