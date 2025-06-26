-- Drop existing database and create new one based on ERD
DROP DATABASE IF EXISTS cafe_ordering_system;
CREATE DATABASE cafe_ordering_system;
USE cafe_ordering_system;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 09:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafe_ordering_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_type` enum('customer','staff','admin') NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_type`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 'customer', 3, 'user_registered', 'Customer registered successfully', '2025-06-25 07:57:44'),
(2, 'customer', 3, 'order_placed', 'Order #4 placed', '2025-06-25 08:24:55'),
(3, 'customer', 4, 'user_registered', 'Customer registered successfully', '2025-06-25 16:00:16'),
(4, 'customer', 4, 'user_login', 'User logged in successfully', '2025-06-25 16:00:35'),
(5, 'customer', 4, 'user_login', 'User logged in successfully', '2025-06-25 16:00:54'),
(6, 'customer', 4, 'order_placed', 'Order #5 placed', '2025-06-25 16:17:02'),
(7, 'customer', 4, 'payment_completed', 'Payment completed for order #5', '2025-06-25 16:27:07'),
(8, 'customer', 4, 'order_placed', 'Order #6 placed', '2025-06-25 18:26:27'),
(9, 'customer', 4, 'payment_completed', 'Payment completed for order #6', '2025-06-25 18:26:32'),
(10, 'customer', 4, 'user_login', 'User logged in successfully', '2025-06-25 19:00:50'),
(11, 'customer', 4, 'order_placed', 'Order #7 placed', '2025-06-25 19:01:00'),
(12, 'customer', 4, 'payment_completed', 'Payment completed for order #7', '2025-06-25 19:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ADM_ID` int(11) NOT NULL,
  `ADMIN_ID` varchar(20) NOT NULL,
  `ADM_USERNAME` varchar(50) NOT NULL,
  `ADM_PASSWORD` varchar(255) NOT NULL,
  `ADM_EMAIL` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ADM_ID`, `ADMIN_ID`, `ADM_USERNAME`, `ADM_PASSWORD`, `ADM_EMAIL`, `created_at`, `updated_at`) VALUES
(1, 'A#1', 'admin_sara', 'SaraAdmin@123', 'admin.sara@email.com', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(2, 'A#2', 'sofeaJane_admin', 'sof3aJ4ne32#', 'admin.sofeaJanee@gmail.com', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(3, 'A#3', 'irfanIzzany_admin', 'irfanzz4nY16_19', 'admin.irfanIzzaneyy16@gmail.com', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(4, 'A#4', 'admin', 'password', 'admin@cafedelights.com', '2025-06-25 07:53:34', '2025-06-25 07:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CUST_ID` int(11) NOT NULL,
  `CUSTOMER_ID` varchar(20) NOT NULL,
  `CUST_NAME` varchar(100) NOT NULL,
  `CUST_NPHONE` varchar(20) NOT NULL,
  `CUST_EMAIL` varchar(100) NOT NULL,
  `CUST_PASSWORD` varchar(255) NOT NULL,
  `MEMBERSHIP` enum('basic','premium','vip') DEFAULT 'basic',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CUST_ID`, `CUSTOMER_ID`, `CUST_NAME`, `CUST_NPHONE`, `CUST_EMAIL`, `CUST_PASSWORD`, `MEMBERSHIP`, `created_at`, `updated_at`) VALUES
(1, 'C#1', 'Aina Zulaikha', '0114455667', 'aina@gmail.com', 'abc123Aina', 'premium', '2025-06-24 16:42:58', '2025-06-24 16:42:58'),
(2, 'C#2', 'Dahlia Darwisyah', '014-3751301', 'dahlia.d_01@gmail.com', 'Darwisyah#301', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(3, 'C#3', 'Nabil Fauzi', '018-9912391', 'nabilfz_98@gmail.com', 'Fauzi!9912', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(4, 'C#4', 'Hazim Syahmi', '010-9221001', 'hazim.sy10my@gmail.com', 'Hazim_S1001', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(5, 'C#5', 'Hana Lut', '016-4423245', 'hana.lutx16@gmail.com', 'hanaLT@234', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(6, 'C#6', 'Batrisyia Balqis', '011-3922887', 'batbalq_92@gmail.com', 'Btrsy@2887', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(7, 'C#7', 'Haein Sarah', '012-8655643', 'hae.sarah12x@gmail.com', 'haeS#5643', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(8, 'C#8', 'Aiman Zafran', '017-8912290', 'aimanzf_2290@gmail.com', 'Zafran!1790', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(9, 'C#9', 'Syafif Tay', '013-0998792', 'syaf.tay13x@gmail.com', 'sTay*987', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(10, 'C#10', 'Eliana Kalogeras', '018-1127223', 'eli.kalo18@gmail.com', 'Eliana#7223', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(11, 'C#11', 'Syuhada Winalda', '011-9871190', 'syuwin_011@gmail.com', 'SyuWin#1190', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(12, 'C#12', 'Diana Daniella', '019-1120988', 'd.daniella19@gmail.com', 'Dani1120@', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(13, 'C#13', 'Rayyan Rizqy', '010-8322489', 'ray.rizqyx10@gmail.com', 'Ray8322$', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(14, 'C#14', 'Tasya Qistina', '015-4435433', 'tasya.q_5433@gmail.com', 'TQistina_443', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(15, 'C#15', 'Zahirah Urasya', '014-2227656', 'zahurah_14x@gmail.com', 'ZahiR@2765', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(16, 'C#16', 'Intan Hannah', '017-4425995', 'int.hannahx17@gmail.com', 'Hannah#4599', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(17, 'C#17', 'Kamal Park', '013-6744338', 'kamalprk_13@gmail.com', 'KamPark#433', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(18, 'C#18', 'Syed Aqil', '011-3317726', 'syed.aqilx11@gmail.com', 'Aqil726!', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(19, 'C#19', 'Yusra Khayrina', '015-5853780', 'yus.kh_015@gmail.com', 'Yusra#5780', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(20, 'C#20', 'Mimi Amalina', '019-9091220', 'mimi.ama_919@gmail.com', 'mimA@1220', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(21, 'C#21', 'Zikry Hasnul', '017-2412842', 'zik.hasnul17x@gmail.com', 'Zikry#2842', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(22, 'C#22', 'Hazwani Uqasya', '010-2519195', 'haz.uqasya10x@gmail.com', 'Hzwani_5195', 'premium', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(23, 'C#23', 'Lunara Imane', '018-3987453', 'luna.imane18@gmail.com', 'Luna7453@', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(24, 'C#24', 'Muhammad Muaz', '013-8856083', 'muaz.md_8856@gmail.com', 'Muaz!6083', 'basic', '2025-06-24 16:44:43', '2025-06-24 16:44:43'),
(25, 'C#25', 'Andra Iqmal', '012-9676610', 'andra.iq_967@gmail.com', 'IqmalX610@', 'premium', '2025-06-24 16:44:44', '2025-06-24 16:44:44'),
(26, 'C#26', 'Tiara Kesuma', '019-7958645', 'tiarak_79@gmail.com', 'Tiara#8645', 'premium', '2025-06-24 16:44:44', '2025-06-24 16:44:44'),
(27, 'C#27', 'Patricia Wong', '013-8334200', 'patwong_13x@gmail.com', 'Ptw4200!', 'basic', '2025-06-24 16:44:44', '2025-06-24 16:44:44'),
(28, 'C#28', 'Jennie Ruby', '019-0092122', 'jennie.rby19@gmail.com', 'JRuby2122#', 'premium', '2025-06-24 16:44:44', '2025-06-24 16:44:44');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE `menu_item` (
  `ITEM_ID` int(11) NOT NULL,
  `MENU_ITEM_ID` varchar(20) NOT NULL,
  `ITEM_NAME` varchar(100) NOT NULL,
  `ITEM_PRICE` decimal(10,2) NOT NULL,
  `ITEM_DESCRIPTION` text DEFAULT NULL,
  `ITEM_CATEGORY` varchar(50) NOT NULL,
  `STOCK_LEVEL` int(11) DEFAULT 0,
  `ADMIN_ID` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`ITEM_ID`, `MENU_ITEM_ID`, `ITEM_NAME`, `ITEM_PRICE`, `ITEM_DESCRIPTION`, `ITEM_CATEGORY`, `STOCK_LEVEL`, `ADMIN_ID`, `image`, `active`, `created_at`, `updated_at`) VALUES
(1, 'MI#1', 'Curry Mee Set', 23.10, 'A flavorful Malaysian noodle dish featuring yellow noodles in a rich and spicy coconut curry broth', 'Local Courses', 23, 1, 'https://woonheng.com/wp-content/uploads/2020/10/Curry-Laksa-Step11-1024x576.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 19:01:00'),
(2, 'MI#2', 'Spaghetti Spicy Buttermilk Set', 23.10, 'Spaghetti tossed in a creamy, spicy buttermilk sauce infused with chili, curry leaves, and aromatic spices', 'Italian Cuisines', 40, 1, 'https://i.pinimg.com/736x/0f/1b/df/0f1bdf26296527c19f45ec854dcf423e.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(3, 'MI#3', 'Caesar Salad', 19.60, 'Crisp romaine lettuce tossed with Caesar dressing, croutons, and grated parmesan cheese. Often topped with grilled chicken', 'Salads', 28, 1, 'https://i.pinimg.com/736x/00/38/9d/00389ddab812fdc051465e0d21b83ee8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(4, 'MI#4', 'Mushroom Soup', 14.00, 'A creamy, savory soup made from blended mushrooms, garlic, and herbs. Smooth in texture and rich in flavor', 'Side Dishes', 15, 1, 'https://i.pinimg.com/736x/fc/3d/d8/fc3dd8e101ee74115f0377a58e88ff6b.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(5, 'MI#5', 'Tomato Soup with Sourdough Grilled Cheese Toast', 19.60, 'Tangy tomato soup served with crispy, golden-brown sourdough grilled cheese. A comforting classic', 'Side Dishes', 25, 1, 'https://i.pinimg.com/736x/36/fa/39/36fa39e65e5e0866b4a7e160e534db94.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(6, 'MI#6', 'Spaghetti Aglio Olio Mushroom', 18.90, 'Spaghetti sautéed in garlic-infused olive oil, chili flakes, and mushrooms. Simple, flavorful, and fresh', 'Italian Cuisines', 43, 1, 'https://i.pinimg.com/736x/fb/e4/97/fbe497bd211a352ad26b22512dd7a0e8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(7, 'MI#7', 'Spaghetti Carbonara Chicken and Mushroom', 23.10, 'Creamy egg-based carbonara sauce with chicken and mushrooms, topped with cheese and black pepper. Rich and indulgent', 'Italian Cuisines', 55, 1, 'https://i.pinimg.com/736x/b1/3c/0f/b13c0f98b4d581a1be1cda07ae6d4ad8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(8, 'MI#8', 'Spaghetti Spicy Buttermilk Chicken', 20.30, 'Spaghetti coated in spicy buttermilk sauce and topped with crispy chicken pieces. Creamy, spicy, and satisfying', 'Italian Cuisines', 44, 2, 'https://www.shutterstock.com/image-photo/creamy-buttermilk-chicken-spaghetti-herbs-600nw-2624595051.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(9, 'MI#9', 'Spaghetti Bolognese', 22.40, 'Spaghetti smothered in slow-cooked tomato and minced meat sauce, seasoned with herbs and spices', 'Italian Cuisines', 16, 2, 'https://i.pinimg.com/736x/94/ed/51/94ed516e1d1ed82e6a1da3c86ea0877a.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(10, 'MI#10', 'Spaghetti Meatballs', 23.10, 'Spaghetti with juicy homemade meatballs in savory marinara sauce, topped with parmesan cheese', 'Italian Cuisines', 22, 2, 'https://i.pinimg.com/736x/4c/3b/b7/4c3bb70def222f23285f3b66ba559103.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(11, 'MI#11', 'Beef Lasagna', 23.10, 'Layers of pasta, seasoned beef, béchamel, and tomato sauce baked to perfection. Cheesy and hearty', 'Italian Cuisines', 29, 2, 'https://i.pinimg.com/736x/6e/04/05/6e0405bc7c4b4430077603049773440f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(12, 'MI#12', 'Grilled Chicken Chop', 24.50, 'Juicy grilled chicken thigh served with brown or black pepper sauce and classic sides like mashed potatoes', 'Western Favorites', 22, 2, 'https://i.pinimg.com/736x/b2/08/7a/b2087a33c0b5945e33807a2fafcc3753.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(13, 'MI#13', 'Shrimp and Fries', 25.90, 'Crispy golden shrimp served with seasoned fries and dipping sauce. A light yet satisfying combo', 'Side Dishes', 26, 2, 'https://i.pinimg.com/736x/b2/3e/5d/b23e5d783580a4311c615329d52901cb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(14, 'MI#14', 'Hainanese Chicken Chop', 24.50, 'Breaded chicken chop in sweet tomato-based Hainanese gravy with peas and fries. A local twist on Western cuisine', 'Western Favorites', 22, 2, 'https://i.pinimg.com/736x/5e/01/e1/5e01e155fc1edec49e04d650d4e7a941.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(15, 'MI#15', 'Swedish Meatballs', 26.60, 'Juicy meatballs in creamy brown gravy with mashed potatoes and lingonberry sauce. Comforting and hearty', 'Western Favorites', 30, 2, 'https://i.pinimg.com/736x/2b/6a/eb/2b6aeb48f0120a72486fc9164f75b0f8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(16, 'MI#16', 'Breaded Chicken Chop', 22.40, 'Crispy chicken chop coated in golden breadcrumbs and served with mushroom or pepper sauce', 'Western Favorites', 30, 2, 'https://i.pinimg.com/736x/70/d7/d7/70d7d7de24e3711c09b3a6775e66dabf.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(17, 'MI#17', 'Fish and Chips', 27.30, 'Battered fish fillet fried to golden perfection with fries and tartar sauce. A British classic', 'Western Favorites', 39, 2, 'https://i.pinimg.com/736x/31/61/e8/3161e808b41a7d2b91335b449616c298.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(18, 'MI#18', 'Deep Fried Salted Egg Squid', 20.30, 'Crispy fried squid in savory salted egg yolk sauce with curry leaves. Rich and crunchy', 'Side Dishes', 24, 3, 'https://i.pinimg.com/736x/20/47/11/204711faf52e4882057170a647f94701.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(19, 'MI#19', 'Tuna Melt Sandwich', 20.30, 'Creamy tuna filling with melted cheese toasted between golden-brown bread slices. Warm and savory', 'Side Dishes', 41, 3, 'https://images.themodernproper.com/production/posts/2023/TunaMelt_8.jpg?w=1200&q=82&auto=format&fit=crop&dm=1682896760&s=4bf022874e817675156a954c02455df9', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(20, 'MI#20', 'Kuey Teow Goreng', 21.00, 'Flat rice noodles stir-fried with soy sauce, egg, vegetables, and protein. Smoky and flavorful', 'Local Courses', 27, 3, 'https://homecookingwithsomjit.com/media/2022/12/Vegetarian-Fried-Kuey-Teow.webp', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(21, 'MI#21', 'Kampung Fried Rice', 23.10, 'Spicy fried rice with anchovies, egg, and vegetables. Served with sambal and crackers', 'Local Courses', 33, 3, 'https://www.elmundoeats.com/wp-content/uploads/2024/04/Nasi-goreng-kampung-or-village-style-fried-rice.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(22, 'MI#22', 'Mashed Potato', 9.10, 'Creamy mashed potatoes whipped smooth with butter. Comforting and classic', 'Side Dishes', 20, 3, 'https://cdn.apartmenttherapy.info/image/upload/f_jpg,q_auto:eco,c_fill,g_auto,w_1500,ar_4:3/k%2FPhoto%2FRecipes%2F2024-11-loaded-mashed-potatoes%2Floaded-mashed-potatoes-4', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(23, 'MI#23', 'Mantao (6 pcs)', 4.90, 'Soft, fluffy steamed buns lightly fried. Great with rich sauces', 'Side Dishes', 20, 3, 'https://i0.wp.com/www.angsarap.net/wp-content/uploads/2023/11/Fried-Mantou-with-Condensed-Milk-Wide.jpg?ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(24, 'MI#24', 'Classic Beef Burger', 22.40, 'Grilled beef patty with lettuce, tomato, cheese in a toasted bun. A satisfying classic', 'Western Favorites', 10, 3, 'https://searafoodsme.com/wp-content/uploads/2022/04/Beef-Burger1080x720px.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(25, 'MI#25', 'Salted Egg Yolk Chicken Burger', 29.40, 'Crispy chicken thigh glazed in salted egg yolk sauce in a bun with creamy dressing', 'Western Favorites', 16, 3, 'https://staging.myburgerlab.com/static/img/menu/products/v3/img_chicken_salted-egg-yolk-burger.webp', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(26, 'MI#26', 'Crispy Fish Burger', 26.60, 'Fried fish fillet with lettuce, cheese, and tartar sauce in a soft bun', 'Western Favorites', 26, 3, 'https://www.kitchensanctuary.com/wp-content/uploads/2014/01/Crispy-Fish-Burger-with-Shoestring-Fries-Recipe-square-FS.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(27, 'MI#27', 'French Fries', 11.20, 'Golden, crispy fries seasoned and perfect as a snack or side', 'Side Dishes', 10, 3, 'https://sausagemaker.com/wp-content/uploads/Homemade-French-Fries_8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(28, 'MI#28', 'Fried Mushrooms', 13.30, 'Crispy fried mushroom bites with juicy centers. Perfect for dipping', 'Side Dishes', 36, 3, 'https://northeastnosh.com/wp-content/uploads/2024/11/Crispy-Fried-Mushrooms.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(29, 'MI#29', 'Panna Cotta', 9.80, 'Silky Italian cream dessert with a melt-in-mouth texture, often served with fruit or sauce', 'Desserts', 15, 3, 'https://www.cucinabyelena.com/wp-content/uploads/2023/06/Panna-Cotta-28-1.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(30, 'MI#30', 'Apple Juice', 11.90, 'Smooth, sweet juice from fresh apples. Crisp and refreshing', 'Beverages', 12, 3, 'https://i.pinimg.com/736x/21/ff/7e/21ff7ef76f32d75208abd60ad024201b.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(31, 'MI#31', 'Orange Juice', 11.90, 'Tangy, bright citrus juice from ripe oranges. Full of vitamin C', 'Beverages', 12, 3, 'https://i.pinimg.com/736x/84/20/82/8420829bd36d33eb912543f97174b5cb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(32, 'MI#32', 'Watermelon Juice', 11.90, 'Cool, sweet juice made from juicy watermelon. Very refreshing', 'Beverages', 10, 3, 'https://i.pinimg.com/736x/7c/ff/dd/7cffdd161e9db0b4fac50b60e348c735.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(33, 'MI#33', 'Pineapple Juice', 11.90, 'Tropical, tangy-sweet juice with vibrant flavor and acidity', 'Beverages', 18, 3, 'https://i.pinimg.com/736x/b9/e4/e8/b9e4e87b2ca8129b803987b6e9be77e9.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(34, 'MI#34', 'Lemonade', 9.80, 'Zesty drink balancing lemon juice and sweetness. Classic and thirst-quenching', 'Beverages', 18, 3, 'https://i.pinimg.com/736x/2a/80/3d/2a803db9c63dd34cae5013a94b048111.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(35, 'MI#35', 'Avocado Juice', 11.90, 'Creamy and smooth juice blended with avocado and milk. Rich and energizing', 'Beverages', 21, 3, 'https://i.pinimg.com/736x/f8/16/82/f81682ee5177f4782de3bfc506ab4f03.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(36, 'MI#36', 'Strawberry Choux Pastry', 5.60, 'Choux puff with vanilla cream and strawberry glaze. Fruity and fluffy', 'Pastries', 30, 3, 'https://youthsweets.com/wp-content/uploads/2022/10/StrawberryChouxauCraquelin_Feature.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(37, 'MI#37', 'Milky Chocolate Choux Pastry', 5.60, 'Choux pastry filled with milky chocolate ganache. Rich and soft', 'Pastries', 30, 3, 'https://i.pinimg.com/736x/83/40/33/834033773bbae20054410bb34183c0e7.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(38, 'MI#38', 'Pistachio Cromboloni', 12.60, 'Croissant-bomboloni hybrid filled with pistachio cream. Buttery and nutty', 'Pastries', 30, 3, 'https://statik.tempo.co/data/2023/12/11/id_1262189/1262189_720.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(39, 'MI#39', 'Chocolate Hazelnut Flat Croissant', 9.10, 'Flaky croissant with chocolate hazelnut filling. Crispy and indulgent', 'Pastries', 13, 3, 'https://strictlydowntowndubai.com/wp-content/uploads/2024/04/flat-croissant-in-dubai-mall.png', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(40, 'MI#40', 'Mixed Berries Pavlova Nest', 12.60, 'Crispy meringue nest with cream and fresh berries. Sweet and airy', 'Pastries', 10, 2, 'https://paarman.co.za/cdn/shop/articles/meringue-nests-berries-cream_8fef8791-53cd-4918-802a-82247cf9d1d2.jpg?v=1748610410', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(41, 'MI#41', 'Milky Chocolate Cromboloni', 12.60, 'Flaky pastry filled with rich milky chocolate cream', 'Pastries', 17, 1, 'https://www.shutterstock.com/image-photo/cromboloni-crombolini-croissant-bomboloni-round-600nw-2407278015.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(42, 'MI#42', 'Berry Pavlova Cromboloni', 12.60, 'Cromboloni filled with berry cream and topped with fresh fruits', 'Pastries', 18, 1, 'https://driscolls.imgix.net/-/media/assets/recipes/mixed-berry-pavlova.ashx?w=1074&h=806&fit=crop&crop=entropy&q=50&auto=format,compress&cs=srgb&ixlib=imgixjs-3.4.2', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(43, 'MI#43', 'Berry Pavlova Slice', 12.60, 'Slice with meringue, whipped cream, and fresh berries', 'Desserts', 10, 1, 'https://i0.wp.com/bryonysbakes.com/wp-content/uploads/2021/09/DSC02033.jpg?ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(44, 'MI#44', 'Carrot Cake Slice', 12.60, 'Moist spiced cake with cream cheese frosting and shredded carrots', 'Desserts', 10, 1, 'https://static01.nyt.com/images/2020/11/01/dining/Carrot-Cake-textless/Carrot-Cake-textless-mediumThreeByTwo440.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(45, 'MI#45', 'Chocolate Pavlova Slice', 12.60, 'Chocolate meringue slice with cream and cocoa flavor', 'Desserts', 10, 1, 'https://i0.wp.com/espressoandlime.com/wp-content/uploads/2025/02/Dark-Chocolate-Pavlova-02-scaled.jpg?resize=700%2C1003&ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(46, 'MI#46', 'Chocolate Rocher Cheesecake Slice', 12.60, 'Chocolate cheesecake with hazelnut crunch, inspired by Ferrero Rocher', 'Desserts', 10, 2, 'https://www.littlesugarsnaps.com/wp-content/uploads/2021/12/ferrero-rocher-cheesecake-square.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(47, 'MI#47', 'Key Lime Cheesecake Slice', 11.90, 'Creamy cheesecake with tangy lime flavor. Light and zesty', 'Desserts', 10, 2, 'https://bakerbynature.com/wp-content/uploads/2019/04/keylimecheesecake-1-of-1-2-500x500.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(48, 'MI#48', 'Mango Cheesecake Slice', 11.90, 'Tropical mango-topped creamy cheesecake. Fruity and smooth', 'Desserts', 19, 2, 'https://assets.epicurious.com/photos/57b208990e4be0011c1bf088/master/pass/mango-cheesecake.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(49, 'MI#49', 'Strawberry Momofuku Slice', 13.30, 'Layered cake with strawberry cream and crunch, Momofuku-style', 'Desserts', 11, 2, 'https://i.pinimg.com/736x/b8/fa/c6/b8fac65230e940ac4ddc773f0d308c2b.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(50, 'MI#50', 'Raspberry Pistachio Cake Slice', 12.60, 'Moist cake with tart raspberries and nutty pistachio', 'Desserts', 10, 2, 'https://juliemarieeats.com/wp-content/uploads/2024/02/Raspberry-Pistachio-Cake-9-1-scaled.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(51, 'MI#51', 'Speculoos Cheesecake Slice', 12.60, 'Cheesecake with spiced speculoos (Biscoff) flavor and crunch', 'Desserts', 10, 2, 'https://www.abakingjourney.com/wp-content/uploads/2020/11/Speculoos-Biscoff-Cheesecake-1.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(52, 'MI#52', 'Salted Caramel Almond Brittle Slice', 14.40, 'Cake with gooey caramel and crunchy almond brittle topping', 'Desserts', 21, 2, 'https://minimalistbaker.com/wp-content/uploads/2021/09/Easy-Almond-Brittle-SQUARE.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(53, 'MI#53', 'Tiramisu Cheesecake Slice', 12.60, 'Tiramisu-flavored cheesecake with coffee and cocoa notes', 'Desserts', 29, 2, 'https://amyinthekitchen.com/wp-content/uploads/2020/09/tiramisu-cheesecake-slice-on-a-plate.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(54, 'MI#54', 'Macadamia Baked Cheesecake Slice', 13.30, 'Baked cheesecake with buttery macadamia nuts', 'Desserts', 33, 2, 'https://www.thecookierookie.com/wp-content/uploads/2019/08/Macadamia-Caramel-Cheesecake-800px-Cookie-Rookie-1.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(55, 'MI#55', 'Chocolate Rocher Tart Slice', 11.90, 'Chocolate ganache tart with hazelnut crunch. Inspired by Rocher', 'Desserts', 38, 2, 'https://www.hungrypinner.com/wp-content/uploads/2022/07/ferrero-rocher-nutella-tart-process-3.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(56, 'MI#56', 'Dark Chocolate Tart', 11.20, 'Bittersweet dark chocolate in a crisp tart shell', 'Desserts', 40, 3, 'https://i.pinimg.com/736x/a2/ef/94/a2ef9410c31232087e898bffee7b37b9.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(57, 'MI#57', 'Loaded Blueberry Tart', 11.20, 'Tart filled with cream and topped with fresh blueberries', 'Desserts', 32, 3, 'https://www.tasteofhome.com/wp-content/uploads/2023/07/Blueberry-Tart-PPP18_4701_C04_25_2b_KSedit.jpg?fit=700%2C467', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(58, 'MI#58', 'Strawberry Bliss Tart', 11.90, 'Vanilla cream tart topped with fresh strawberries', 'Desserts', 39, 3, 'https://i.pinimg.com/736x/8b/39/33/8b39336ca6b4709087ef86d19b692a7d.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(59, 'MI#59', 'Lemon Cream Tart', 11.20, 'Zesty lemon curd and lemon cream in a buttery shell', 'Desserts', 13, 3, 'https://i0.wp.com/www.growingupcali.com/wp-content/uploads/2022/03/Growing-Up-Cali-Lemon-Tartlets-45.jpg?fit=800%2C1000&ssl=1', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(60, 'MI#60', 'Strawberry Tartlet', 9.10, 'Mini tart with cream and glossy strawberry topping', 'Desserts', 21, 3, 'https://sundaytable.co/wp-content/uploads/2025/04/mini-strawberry-tartlets-with-custard.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(61, 'MI#61', 'Chocolate Hazelnut Tartlet', 10.50, 'Mini tart filled with rich chocolate and hazelnuts', 'Desserts', 38, 3, 'https://theskinnyfoodco.com/cdn/shop/articles/the-best-chocolate-hazelnut-tartlets-786581.jpg?v=1666894052', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(62, 'MI#62', 'Red Velvet Cream Cheese Soft Cookies', 7.70, 'Red velvet cookies with cream cheese center. Soft and rich', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/54/20/c0/5420c01fce2e00a2ac59a753e51572aa.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(63, 'MI#63', 'Original Brown Butter Soft Cookies', 7.00, 'Chewy cookies made with nutty browned butter', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/c1/a0/95/c1a095dd5cc80edf81495638855e4877.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(64, 'MI#64', 'Dark Chocolate Soft Cookies', 7.70, 'Fudgy cookies with intense dark chocolate flavor', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/be/7a/67/be7a67deeeda73554433d3bb283b44d3.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(65, 'MI#65', 'Matcha White Chocolate Soft Cookies', 7.70, 'Matcha cookies with sweet white chocolate chunks', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/11/dc/a3/11dca3785d212aa284bbb87ff37e49e7.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(66, 'MI#66', 'Chocolate Chip Soft Cookies', 7.70, 'Classic soft cookies with melty chocolate chips', 'Pastries', 41, 3, 'https://i.pinimg.com/736x/c2/73/fa/c273fa0c99c56497b1c49ef49ce5746f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(67, 'MI#67', 'Quesadilla', 21.00, 'Grilled tortilla filled with cheese and your choice of meat or veggies', 'Mexican Cuisines', 10, 3, 'https://i.pinimg.com/736x/aa/a8/83/aaa8833a0a9f338c64d5658586d66a79.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(68, 'MI#68', 'Fajitas', 23.80, 'Grilled marinated meat strips with peppers and onions. Served with tortillas', 'Mexican Cuisines', 15, 3, 'https://www.eatingbirdfood.com/wp-content/uploads/2024/04/healthy-chicken-fajitas-hero-new.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(69, 'MI#69', 'Peruvian Chicken', 19.60, 'Juicy grilled chicken with Peruvian spices and green sauce', 'International Cuisines', 19, 2, 'https://i.pinimg.com/736x/47/ab/5e/47ab5e8d6c47151899fe57ab1fc6790f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(70, 'MI#70', 'Crunchy Tacos (3 pieces)', 22.40, 'Three crispy taco shells filled with seasoned meat, cheese, and salsa', 'Mexican Cuisines', 16, 2, 'https://i.pinimg.com/736x/78/25/02/7825024db57999f02f082331b9e83d22.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(71, 'MI#71', 'Soft Tacos (3 pieces)', 21.70, 'Soft tortillas filled with meat, greens, cheese, and salsa', 'Mexican Cuisines', 29, 2, 'https://i.pinimg.com/736x/87/7f/1b/877f1b1402ae12382b27d175512eab56.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(72, 'MI#72', 'Smash Mac Tacos', 9.80, 'Tacos with smashed beef patties and mac & cheese. Cheesy and bold', 'Mexican Cuisines', 35, 2, 'https://i.pinimg.com/736x/16/ed/d2/16edd2dc0209ea6c0427d0a36ccd7e0a.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(73, 'MI#73', 'Prawn Gambas', 26.60, 'Sautéed prawns in garlic olive oil with herbs and chili', 'International Cuisines', 38, 2, 'https://i.pinimg.com/736x/01/56/12/015612451d03dda6899bb2ee296de787.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(74, 'MI#74', 'Seafood Chowder', 17.50, 'Creamy soup with seafood chunks, potatoes, and vegetables', 'Side Dishes', 26, 2, 'https://i.pinimg.com/736x/ef/8b/b0/ef8bb01f657451a69aba77370ce2b5eb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(75, 'MI#75', 'Corn Salad', 12.60, 'Sweet corn salad with lime, herbs, and light chili', 'Salads', 24, 2, 'https://i.pinimg.com/736x/40/60/0b/40600bbecfd56538c92d409d5a56adaf.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(76, 'MI#76', 'Chicken Tenders', 12.60, 'Golden fried chicken strips, crispy and juicy', 'Side Dishes', 36, 2, 'https://i.pinimg.com/736x/07/45/12/074512e2f6c5ee3f62cb64b807c074b9.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(77, 'MI#77', 'Buffalo Wings', 15.40, 'Fried chicken wings tossed in spicy buffalo sauce', 'Side Dishes', 36, 2, 'https://i.pinimg.com/736x/4d/e6/7f/4de67f34255f781311fd5662b188e5b8.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(78, 'MI#78', 'Cheesy Nachos', 10.50, 'Tortilla chips with melted cheese, jalapeños, and toppings', 'Mexican Cuisines', 19, 3, 'https://i.pinimg.com/736x/f3/46/2d/f3462daf5a1ca157cecaa2fd4c11877e.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(79, 'MI#79', 'Guacamole', 15.40, 'Creamy mashed avocado dip with lime, tomato, and onion', 'Mexican Cuisines', 19, 2, 'https://i.pinimg.com/736x/bd/22/e3/bd22e3a9c6982bc2e32f8159e3faf29f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(80, 'MI#80', 'Cheesy Fries', 15.40, 'Fries loaded with melted cheese and sauces', 'Side Dishes', 40, 1, 'https://i.pinimg.com/736x/38/9a/5b/389a5b577dfef793b3405ba995b73bab.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(81, 'MI#81', 'Cheese Platter', 38.50, 'Selection of cheeses, crackers, fruits, and nuts', 'Mexican Cuisines', 10, 1, 'https://i.pinimg.com/736x/5e/1b/d3/5e1bd3e109628aa8fa6dd7a2f53181f6.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(82, 'MI#82', 'Surf & Turf Burger', 69.30, 'Burger with beef patty and grilled prawns in a toasted bun', 'Western Favorites', 13, 1, 'https://i.pinimg.com/736x/19/b5/7b/19b57bf02451c21faf8745f913d1111f.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(83, 'MI#83', 'Mexicana Avocado Beef Burger', 26.60, 'Beef burger with avocado, salsa, and cheese', 'Western Favorites', 13, 1, 'https://d31qjkbvvkyanm.cloudfront.net/images/recipe-images/carne-asada-guacamole-burgers-detail-b270aeb5.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(84, 'MI#84', 'Signature Penne Taco Pasta', 21.00, 'Penne pasta in cheesy taco-style sauce with meat', 'International Cuisines', 10, 1, 'https://i.pinimg.com/736x/9a/db/e3/9adbe3eeb2a17f89ef01c5d30b762c88.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(85, 'MI#85', 'Cheesy Mac and Cheese', 20.30, 'Baked macaroni in rich, gooey cheese sauce', 'Western Favorites', 18, 1, 'https://i.pinimg.com/736x/29/11/b2/2911b213e6d0986e5a975c3ec5177f17.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(86, 'MI#86', 'Prawn Roll', 31.50, 'Toasted roll filled with prawns, lettuce, and creamy dressing', 'Side Dishes', 20, 2, 'https://i.pinimg.com/736x/0c/86/12/0c8612b388f240ee54f171b14b286277.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(87, 'MI#87', 'Margherita Pizza', 17.50, 'Classic pizza with tomato, mozzarella, and basil', 'Italian Cuisines', 12, 3, 'https://i.pinimg.com/736x/57/e7/a1/57e7a19334571946391a7430fcb86202.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(88, 'MI#88', 'Seafood Fiesta Pizza', 26.60, 'Pizza topped with shrimp, squid, and seafood', 'Italian Cuisines', 12, 3, 'https://i.pinimg.com/736x/07/24/4c/07244c38f7df4939dc59d20eb206e3eb.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(89, 'MI#89', 'Peruvian Grilled Chicken Pizza', 23.10, 'Pizza with Peruvian-spiced grilled chicken', 'Italian Cuisines', 10, 2, 'https://i.pinimg.com/736x/d8/a1/17/d8a1175918aa45ab95ab97e1220f1195.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(90, 'MI#90', 'Grilled Prawn Pizza', 25.20, 'Pizza topped with smoky grilled prawns', 'Italian Cuisines', 10, 1, 'https://i.pinimg.com/736x/aa/c2/0a/aac20ab55ef3a925e91b15c4ebdb5bc6.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(91, 'MI#91', 'Sizzling Brownies', 16.80, 'Warm brownie on a sizzling plate with ice cream and chocolate sauce', 'Desserts', 30, 3, 'https://i.pinimg.com/736x/10/c7/0e/10c70e6903e07575174ef5247b7b7d8d.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(92, 'MI#92', 'Churros', 12.60, 'Fried dough sticks coated in cinnamon sugar, served with dipping sauce', 'Desserts', 30, 1, 'https://cdn.jwplayer.com/v2/media/sFx0klK3/thumbnails/mbVGQSpX.jpg?width=1280', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(93, 'MI#93', 'Cappuccino', 5.50, 'Rich espresso with steamed milk and foam', 'Coffee', 50, 1, 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(94, 'MI#94', 'Latte', 5.00, 'Smooth espresso with steamed milk', 'Coffee', 45, 1, 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(95, 'MI#95', 'Americano', 4.50, 'Bold espresso with hot water', 'Coffee', 40, 1, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(96, 'MI#96', 'Mocha', 6.00, 'Espresso with chocolate and steamed milk', 'Coffee', 35, 1, 'https://ichef.bbc.co.uk/ace/standard/1600/food/recipes/the_perfect_mocha_coffee_29100_16x9.jpg.webp', 1, '2025-06-25 17:30:00', '2025-06-25 18:12:59'),
(97, 'MI#97', 'Iced Coffee', 4.90, 'Refreshing iced coffee with milk and sugar', 'Beverages', 30, 1, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(98, 'MI#98', 'Espresso', 3.50, 'Strong concentrated coffee shot', 'Coffee', 50, 1, 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(99, 'MI#99', 'Macchiato', 4.80, 'Espresso with a dollop of steamed milk foam', 'Coffee', 35, 1, 'https://roastercoffees.com/wp-content/uploads/2021/05/Espresso-Macchiato-Recipe.webp', 1, '2025-06-25 17:30:00', '2025-06-25 18:12:37'),
(100, 'MI#100', 'Flat White', 5.20, 'Double shot espresso with steamed milk', 'Coffee', 40, 1, 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(101, 'MI#101', 'Tom Yum Soup', 16.50, 'Spicy and sour Thai soup with shrimp and mushrooms', 'International Cuisines', 20, 2, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(102, 'MI#102', 'Green Curry Chicken', 22.40, 'Thai green curry with chicken and vegetables', 'International Cuisines', 18, 2, 'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(103, 'MI#103', 'Nasi Lemak', 19.60, 'Malaysian coconut rice with sambal and accompaniments', 'Local Courses', 22, 3, 'https://delishglobe.com/wp-content/uploads/2025/04/Nasi-Lemak-Coconut-Rice-with-Sambal.png', 1, '2025-06-25 17:30:00', '2025-06-25 18:09:24'),
(104, 'MI#104', 'Char Kway Teow', 21.70, 'Stir-fried flat rice noodles with prawns and Chinese sausage', 'Local Courses', 20, 3, 'https://www.feastingathome.com/wp-content/uploads/2025/01/Char-Kway-Teow-12.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:09:53'),
(105, 'MI#105', 'Laksa', 20.30, 'Spicy noodle soup with coconut milk and seafood', 'Local Courses', 18, 3, 'https://www.elmundoeats.com/wp-content/uploads/2024/03/Penang-asam-laksa-in-a-bowl.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:10:22'),
(106, 'MI#106', 'Mozzarella Sticks', 13.30, 'Breaded mozzarella with marinara dipping sauce', 'Appetizers', 28, 2, 'https://easyweeknightrecipes.com/wp-content/uploads/2024/04/Mozzarella-Sticks_0013-500x375.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:20:07'),
(107, 'MI#107', 'Garlic Bread', 8.40, 'Toasted bread with garlic butter and herbs', 'Appetizers', 40, 2, 'https://spicecravings.com/wp-content/uploads/2021/09/Air-Fryer-Garlic-Bread-Featured.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:22:05'),
(108, 'MI#108', 'Clam Chowder', 15.40, 'Creamy soup with clams and potatoes', 'Side Dishes', 20, 2, 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(109, 'MI#109', 'New York Cheesecake', 13.30, 'Classic dense and creamy cheesecake', 'Desserts', 15, 1, 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(110, 'MI#110', 'Chocolate Lava Cake', 14.70, 'Warm chocolate cake with molten center', 'Desserts', 12, 1, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(111, 'MI#111', 'Earl Grey Tea', 4.20, 'Classic English breakfast tea', 'Beverages', 50, 3, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(112, 'MI#112', 'Hot Chocolate', 5.60, 'Rich hot chocolate with whipped cream', 'Beverages', 40, 3, 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400', 1, '2025-06-25 17:30:00', '2025-06-25 17:30:00'),
(113, 'MI#113', 'Smoothie Bowl', 16.80, 'Acai smoothie bowl with fresh fruits and granola', 'Beverages', 20, 3, 'https://www.veggiesdontbite.com/wp-content/uploads/2020/05/vegan-smoothie-bowl-FI.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:14:00'),
(114, 'MI#114', 'Mango Smoothie', 12.60, 'Fresh mango smoothie with yogurt', 'Beverages', 25, 3, 'https://www.cubesnjuliennes.com/wp-content/uploads/2021/04/Mango-Smoothie-Recipe.jpg', 1, '2025-06-25 17:30:00', '2025-06-25 18:14:19'),
(115, 'MI#115', 'Loaded Potato Skins', 14.70, 'Crispy potato skins with cheese and bacon', 'Appetizers', 25, 2, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRz6UTP7HkNBr6p4Ur5BrwUxkF6QONRDYhfJg&s', 1, '2025-06-25 17:30:00', '2025-06-25 18:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `ORDER_ID` int(11) NOT NULL,
  `ORDER_NUMBER` varchar(20) NOT NULL,
  `ORDER_TIME` time NOT NULL,
  `ORDER_DATE` date NOT NULL,
  `ORDER_TYPE` enum('delivery','takeaway','dine-in') NOT NULL,
  `ORDER_STATUS` enum('pending','preparing','ready','out_for_delivery','completed','cancelled') DEFAULT 'pending',
  `TOT_AMOUNT` decimal(10,2) NOT NULL,
  `DELIVERY_ADDRESS` text DEFAULT NULL,
  `PAYMENT_METHOD` varchar(50) DEFAULT NULL,
  `PAYMENT_STATUS` enum('pending','completed','failed') DEFAULT 'pending',
  `CUST_ID` int(11) NOT NULL,
  `STAFF_ID` int(11) DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `pickup_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`ORDER_ID`, `ORDER_NUMBER`, `ORDER_TIME`, `ORDER_DATE`, `ORDER_TYPE`, `ORDER_STATUS`, `TOT_AMOUNT`, `DELIVERY_ADDRESS`, `PAYMENT_METHOD`, `PAYMENT_STATUS`, `CUST_ID`, `STAFF_ID`, `special_instructions`, `pickup_time`, `created_at`, `updated_at`) VALUES
(1, 'O#1', '12:30:00', '2024-01-15', 'delivery', 'completed', 25.80, '123 Main Street, City', 'fpx', 'completed', 1, 1, 'Please ring the doorbell', NULL, '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(2, 'O#2', '14:15:00', '2024-01-15', 'takeaway', 'ready', 18.90, NULL, 'credit_card', 'completed', 1, 1, 'Extra sauce please', NULL, '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(3, 'O#3', '19:45:00', '2024-01-15', 'dine-in', 'preparing', 32.70, NULL, 'ewallet', 'completed', 2, 2, 'Table for 2', NULL, '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(4, 'O#4', '10:24:55', '2025-06-25', 'delivery', 'pending', 15.32, 'ads', NULL, 'pending', 3, NULL, 'asd', NULL, '2025-06-25 08:24:55', '2025-06-25 08:24:55'),
(5, 'O#5', '18:17:02', '2025-06-25', 'delivery', 'pending', 17.90, 'ad', 'fpx', 'completed', 4, NULL, 'da', NULL, '2025-06-25 16:17:02', '2025-06-25 16:27:07'),
(6, 'O#6', '20:26:27', '2025-06-25', 'dine-in', 'pending', 23.10, '', 'fpx', 'completed', 4, NULL, 's', '2025-07-04 02:28:00', '2025-06-25 18:26:27', '2025-06-25 18:26:32'),
(7, 'O#7', '21:01:00', '2025-06-25', 'delivery', 'pending', 28.10, 'asd', 'fpx', 'completed', 4, NULL, '', NULL, '2025-06-25 19:01:00', '2025-06-25 19:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_listing`
--

CREATE TABLE `order_listing` (
  `LISTING_ID` int(11) NOT NULL,
  `ORDER_LISTING_ID` varchar(20) NOT NULL,
  `ORDER_QUANTITY` int(11) NOT NULL,
  `ORDER_ID` int(11) NOT NULL,
  `ITEM_ID` int(11) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_listing`
--

INSERT INTO `order_listing` (`LISTING_ID`, `ORDER_LISTING_ID`, `ORDER_QUANTITY`, `ORDER_ID`, `ITEM_ID`, `item_price`, `special_requests`, `created_at`) VALUES
(9, 'OL#1', 1, 6, 1, 23.10, NULL, '2025-06-25 18:26:27'),
(16, 'OL#2', 1, 7, 1, 23.10, NULL, '2025-06-25 19:01:00'),
(17, 'OL#3', 1, 1, 1, 23.10, NULL, '2025-06-25 19:04:03'),
(18, 'OL#4', 1, 1, 2, 23.10, NULL, '2025-06-25 19:04:03'),
(19, 'OL#5', 1, 1, 3, 19.60, NULL, '2025-06-25 19:04:03'),
(20, 'OL#6', 1, 2, 4, 14.00, NULL, '2025-06-25 19:04:03'),
(21, 'OL#7', 1, 3, 5, 19.60, NULL, '2025-06-25 19:04:03'),
(22, 'OL#8', 1, 3, 6, 18.90, NULL, '2025-06-25 19:04:03');

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_summary`
-- (See below for the actual view)
--
CREATE TABLE `order_summary` (
`ORDER_ID` int(11)
,`ORDER_NUMBER` varchar(20)
,`ORDER_TIME` time
,`ORDER_DATE` date
,`ORDER_TYPE` enum('delivery','takeaway','dine-in')
,`ORDER_STATUS` enum('pending','preparing','ready','out_for_delivery','completed','cancelled')
,`TOT_AMOUNT` decimal(10,2)
,`DELIVERY_ADDRESS` text
,`PAYMENT_METHOD` varchar(50)
,`PAYMENT_STATUS` enum('pending','completed','failed')
,`customer_name` varchar(100)
,`customer_email` varchar(100)
,`customer_phone` varchar(20)
,`staff_name` varchar(100)
,`item_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `popular_items`
-- (See below for the actual view)
--
CREATE TABLE `popular_items` (
`ITEM_ID` int(11)
,`MENU_ITEM_ID` varchar(20)
,`ITEM_NAME` varchar(100)
,`ITEM_DESCRIPTION` text
,`ITEM_PRICE` decimal(10,2)
,`ITEM_CATEGORY` varchar(50)
,`image` varchar(255)
,`STOCK_LEVEL` int(11)
,`order_count` bigint(21)
,`total_quantity` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `STAFF_ID` int(11) NOT NULL,
  `STAFF_NUMBER` varchar(20) NOT NULL,
  `STAFF_NAME` varchar(100) NOT NULL,
  `STAFF_PNUMBER` varchar(20) NOT NULL,
  `STAFF_EMAIL` varchar(100) NOT NULL,
  `STAFF_PASSWORD` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`STAFF_ID`, `STAFF_NUMBER`, `STAFF_NAME`, `STAFF_PNUMBER`, `STAFF_EMAIL`, `STAFF_PASSWORD`, `created_at`, `updated_at`) VALUES
(1, 'S#1', 'Sarah Yusra', '013-7771234', 'sarahYuss@gmail.com', 'Srah#1234', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(2, 'S#2', 'Raiqal Hilmi', '013-7946623', 'hilmiQal@gmail.com', 'Raiq@2025!hm', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(3, 'S#3', 'Yasmin Batrisya', '019-5010197', 'yasBatty77@gmail.com', 'YBtrsy#77mint', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(4, 'S#4', 'Fatien Najwa', '013-2831565', 'najwawa89@gmail.com', 'FatieN_94!na', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(5, 'S#5', 'Patricia Adriane', '017-8708340', 'adrianeGrand3@gmail.com', 'PatAdri@456!ne', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(6, 'S#6', 'Rosie Brown', '011-2157783', 'rorosieBb@gmail.com', 'RosieBr0wn$23', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(7, 'S#7', 'Farhan Qimie', '019-9698707', 'qimi3Farhan@gmail.com', 'FarQ!88hanQ', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(8, 'S#8', 'Asyraf Tan', '010-8852265', 'AsyrafTan09@gmail.com', 'AsTan#2024rf', '2025-06-25 07:53:34', '2025-06-25 07:53:34'),
(9, 'S#9', 'Olivia Aileen', '013-3404500', 'oliviviLeen@gmail.com', 'OliAil_07$en', '2025-06-25 07:53:34', '2025-06-25 07:53:34');


-- --------------------------------------------------------

--
-- Structure for view `order_summary`
--
DROP TABLE IF EXISTS `order_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_summary`  AS SELECT `o`.`ORDER_ID` AS `ORDER_ID`, `o`.`ORDER_NUMBER` AS `ORDER_NUMBER`, `o`.`ORDER_TIME` AS `ORDER_TIME`, `o`.`ORDER_DATE` AS `ORDER_DATE`, `o`.`ORDER_TYPE` AS `ORDER_TYPE`, `o`.`ORDER_STATUS` AS `ORDER_STATUS`, `o`.`TOT_AMOUNT` AS `TOT_AMOUNT`, `o`.`DELIVERY_ADDRESS` AS `DELIVERY_ADDRESS`, `o`.`PAYMENT_METHOD` AS `PAYMENT_METHOD`, `o`.`PAYMENT_STATUS` AS `PAYMENT_STATUS`, `c`.`CUST_NAME` AS `customer_name`, `c`.`CUST_EMAIL` AS `customer_email`, `c`.`CUST_NPHONE` AS `customer_phone`, `s`.`STAFF_NAME` AS `staff_name`, count(`ol`.`LISTING_ID`) AS `item_count` FROM (((`order` `o` join `customer` `c` on(`o`.`CUST_ID` = `c`.`CUST_ID`)) left join `staff` `s` on(`o`.`STAFF_ID` = `s`.`STAFF_ID`)) left join `order_listing` `ol` on(`o`.`ORDER_ID` = `ol`.`ORDER_ID`)) GROUP BY `o`.`ORDER_ID` ;

-- --------------------------------------------------------

--
-- Structure for view `popular_items`
--
DROP TABLE IF EXISTS `popular_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `popular_items`  AS SELECT `mi`.`ITEM_ID` AS `ITEM_ID`, `mi`.`MENU_ITEM_ID` AS `MENU_ITEM_ID`, `mi`.`ITEM_NAME` AS `ITEM_NAME`, `mi`.`ITEM_DESCRIPTION` AS `ITEM_DESCRIPTION`, `mi`.`ITEM_PRICE` AS `ITEM_PRICE`, `mi`.`ITEM_CATEGORY` AS `ITEM_CATEGORY`, `mi`.`image` AS `image`, `mi`.`STOCK_LEVEL` AS `STOCK_LEVEL`, count(`ol`.`LISTING_ID`) AS `order_count`, sum(`ol`.`ORDER_QUANTITY`) AS `total_quantity` FROM (`menu_item` `mi` left join `order_listing` `ol` on(`mi`.`ITEM_ID` = `ol`.`ITEM_ID`)) WHERE `mi`.`active` = 1 GROUP BY `mi`.`ITEM_ID` ORDER BY count(`ol`.`LISTING_ID`) DESC, sum(`ol`.`ORDER_QUANTITY`) DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ADM_ID`),
  ADD UNIQUE KEY `ADMIN_ID` (`ADMIN_ID`),
  ADD UNIQUE KEY `ADM_USERNAME` (`ADM_USERNAME`),
  ADD UNIQUE KEY `ADM_EMAIL` (`ADM_EMAIL`),
  ADD KEY `idx_admin_username` (`ADM_USERNAME`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CUST_ID`),
  ADD UNIQUE KEY `CUSTOMER_ID` (`CUSTOMER_ID`),
  ADD UNIQUE KEY `CUST_EMAIL` (`CUST_EMAIL`),
  ADD KEY `idx_customer_email` (`CUST_EMAIL`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`ITEM_ID`),
  ADD UNIQUE KEY `MENU_ITEM_ID` (`MENU_ITEM_ID`),
  ADD KEY `ADMIN_ID` (`ADMIN_ID`),
  ADD KEY `idx_menu_item_category` (`ITEM_CATEGORY`),
  ADD KEY `idx_menu_item_active` (`active`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`ORDER_ID`),
  ADD UNIQUE KEY `ORDER_NUMBER` (`ORDER_NUMBER`),
  ADD KEY `idx_order_cust_id` (`CUST_ID`),
  ADD KEY `idx_order_staff_id` (`STAFF_ID`),
  ADD KEY `idx_order_status` (`ORDER_STATUS`),
  ADD KEY `idx_order_date` (`ORDER_DATE`);

--
-- Indexes for table `order_listing`
--
ALTER TABLE `order_listing`
  ADD PRIMARY KEY (`LISTING_ID`),
  ADD UNIQUE KEY `ORDER_LISTING_ID` (`ORDER_LISTING_ID`),
  ADD KEY `idx_order_listing_order_id` (`ORDER_ID`),
  ADD KEY `idx_order_listing_item_id` (`ITEM_ID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`STAFF_ID`),
  ADD UNIQUE KEY `STAFF_NUMBER` (`STAFF_NUMBER`),
  ADD UNIQUE KEY `STAFF_EMAIL` (`STAFF_EMAIL`),
  ADD KEY `idx_staff_email` (`STAFF_EMAIL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ADM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `CUST_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `ITEM_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `ORDER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_listing`
--
ALTER TABLE `order_listing`
  MODIFY `LISTING_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `STAFF_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`ADMIN_ID`) REFERENCES `admin` (`ADM_ID`) ON DELETE SET NULL;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`CUST_ID`) REFERENCES `customer` (`CUST_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`STAFF_ID`) REFERENCES `staff` (`STAFF_ID`) ON DELETE SET NULL;

--
-- Constraints for table `order_listing`
--
ALTER TABLE `order_listing`
  ADD CONSTRAINT `order_listing_ibfk_1` FOREIGN KEY (`ORDER_ID`) REFERENCES `order` (`ORDER_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_listing_ibfk_2` FOREIGN KEY (`ITEM_ID`) REFERENCES `menu_item` (`ITEM_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
