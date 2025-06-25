<?php
// Updated functions for the new database structure

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function isStaff() {
    return isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'staff' || $_SESSION['user_type'] === 'admin');
}

function isCustomer() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer';
}

function formatCurrency($amount) {
    return 'RM ' . number_format($amount, 2);
}

function getPopularItems($conn, $limit = 4) {
    $items = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'MENU_ITEM'");
    if($result->num_rows == 0) {
        return $items;
    }
    
    $sql = "SELECT ITEM_ID as id, ITEM_NAME as name, ITEM_DESCRIPTION as description, ITEM_PRICE as price, ITEM_CATEGORY as category, STOCK_LEVEL, image FROM MENU_ITEM WHERE active = 1 ORDER BY STOCK_LEVEL DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    
    if($stmt) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Ensure all required fields have default values
            $item = [
                'id' => $row['id'] ?? 0,
                'name' => $row['name'] ?? 'Unknown Item',
                'description' => $row['description'] ?? 'No description available',
                'price' => $row['price'] ?? 0.00,
                'category' => $row['category'] ?? 'general',
                'STOCK_LEVEL' => $row['STOCK_LEVEL'] ?? 0,
                'image' => $row['image'] ?? '/placeholder.svg?height=200&width=280'
            ];
            $items[] = $item;
        }
        $stmt->close();
    }
    
    return $items;
}

function getMenuItems($conn, $category = null) {
    $items = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'MENU_ITEM'");
    if($result->num_rows == 0) {
        return $items;
    }
    
    $sql = "SELECT ITEM_ID as id, ITEM_NAME as name, ITEM_DESCRIPTION as description, ITEM_PRICE as price, ITEM_CATEGORY as category, STOCK_LEVEL, image FROM MENU_ITEM WHERE active = 1";
    
    if ($category) {
        $sql .= " AND ITEM_CATEGORY = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
    } else {
        $stmt = $conn->prepare($sql);
    }
    
    if($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Ensure all required fields have default values
            $item = [
                'id' => $row['id'] ?? 0,
                'name' => $row['name'] ?? 'Unknown Item',
                'description' => $row['description'] ?? 'No description available',
                'price' => $row['price'] ?? 0.00,
                'category' => $row['category'] ?? 'general',
                'STOCK_LEVEL' => $row['STOCK_LEVEL'] ?? 0,
                'image' => $row['image'] ?? '/placeholder.svg?height=200&width=280'
            ];
            $items[] = $item;
        }
        $stmt->close();
    }
    
    return $items;
}

// Customer Authentication Functions
function registerCustomer($conn, $name, $email, $password, $phone, $membership = 'basic') {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO CUSTOMER (CUST_NAME, CUST_EMAIL, CUST_PASSWORD, CUST_NPHONE, MEMBERSHIP) VALUES (?, ?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $membership);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt->close();
            return $user_id;
        }
        $stmt->close();
    }
    
    return false;
}

function loginUser($conn, $email, $password, $user_type = null) {
    // Try to login as customer first, then staff, then admin
    $tables = [
        'customer' => ['table' => 'CUSTOMER', 'id' => 'CUST_ID', 'name' => 'CUST_NAME', 'email' => 'CUST_EMAIL', 'password' => 'CUST_PASSWORD'],
        'staff' => ['table' => 'STAFF', 'id' => 'STAFF_ID', 'name' => 'STAFF_NAME', 'email' => 'STAFF_EMAIL', 'password' => 'STAFF_PASSWORD'],
        'admin' => ['table' => 'ADMIN', 'id' => 'ADM_ID', 'name' => 'ADM_USERNAME', 'email' => 'ADM_EMAIL', 'password' => 'ADM_PASSWORD']
    ];
    
    // If user_type is specified, only check that table
    if ($user_type && isset($tables[$user_type])) {
        $tables = [$user_type => $tables[$user_type]];
    }
    
    foreach ($tables as $type => $config) {
        $stmt = $conn->prepare("SELECT {$config['id']}, {$config['name']}, {$config['email']}, {$config['password']} FROM {$config['table']} WHERE {$config['email']} = ?");
        if($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user[$config['password']])) {
                    // Password is correct, start a new session
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // Store data in session variables
                    $_SESSION["user_id"] = $user[$config['id']];
                    $_SESSION["user_name"] = $user[$config['name']];
                    $_SESSION["user_email"] = $user[$config['email']];
                    $_SESSION["user_type"] = $type;
                    
                    $stmt->close();
                    return true;
                }
            }
            $stmt->close();
        }
    }
    
    return false;
}

// Order Functions
function createOrder($conn, $cust_id, $order_type, $total_amount, $delivery_address = null, $pickup_time = null, $special_instructions = null, $payment_method = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'ORDER'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $order_time = date('H:i:s');
    $order_date = date('Y-m-d');
    
    $stmt = $conn->prepare("INSERT INTO `ORDER` (ORDER_TIME, ORDER_DATE, ORDER_TYPE, TOT_AMOUNT, DELIVERY_ADDRESS, PAYMENT_METHOD, CUST_ID, special_instructions, pickup_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("sssdssiss", $order_time, $order_date, $order_type, $total_amount, $delivery_address, $payment_method, $cust_id, $special_instructions, $pickup_time);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            $stmt->close();
            return $order_id;
        }
        $stmt->close();
    }
    
    return false;
}

function addOrderListing($conn, $order_id, $item_id, $quantity, $price, $special_requests = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'ORDER_LISTING'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO ORDER_LISTING (ORDER_ID, ITEM_ID, ORDER_QUANTITY, item_price, special_requests) VALUES (?, ?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("iiids", $order_id, $item_id, $quantity, $price, $special_requests);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function getCustomerOrders($conn, $cust_id) {
    $orders = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'ORDER'");
    if($result->num_rows == 0) {
        return $orders;
    }
    
    $stmt = $conn->prepare("SELECT * FROM `ORDER` WHERE CUST_ID = ? ORDER BY ORDER_DATE DESC, ORDER_TIME DESC");
    if($stmt) {
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
    }
    
    return $orders;
}

function getOrderListings($conn, $order_id) {
    $listings = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'ORDER_LISTING'");
    if($result->num_rows == 0) {
        return $listings;
    }
    
    $stmt = $conn->prepare("
        SELECT ol.*, mi.ITEM_NAME, mi.image 
        FROM ORDER_LISTING ol
        JOIN MENU_ITEM mi ON ol.ITEM_ID = mi.ITEM_ID
        WHERE ol.ORDER_ID = ?
    ");
    
    if($stmt) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $listings[] = $row;
        }
        $stmt->close();
    }
    
    return $listings;
}

function updateOrderStatus($conn, $order_id, $status) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'ORDER'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE `ORDER` SET ORDER_STATUS = ? WHERE ORDER_ID = ?");
    if($stmt) {
        $stmt->bind_param("si", $status, $order_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function assignStaffToOrder($conn, $order_id, $staff_id) {
    $stmt = $conn->prepare("UPDATE `ORDER` SET STAFF_ID = ? WHERE ORDER_ID = ?");
    if($stmt) {
        $stmt->bind_param("ii", $staff_id, $order_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

// Admin Functions
function getAllCustomers($conn) {
    $customers = [];
    
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'CUSTOMER'");
    if($result->num_rows == 0) {
        return $customers;
    }
    
    $stmt = $conn->prepare("SELECT CUST_ID, CUST_NAME, CUST_EMAIL, CUST_NPHONE, MEMBERSHIP, created_at FROM CUSTOMER ORDER BY created_at DESC");
    if($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
        $stmt->close();
    }
    
    return $customers;
}

function getAllStaff($conn) {
    $staff = [];
    
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'STAFF'");
    if($result->num_rows == 0) {
        return $staff;
    }
    
    $stmt = $conn->prepare("SELECT STAFF_ID, STAFF_NAME, STAFF_EMAIL, STAFF_PNUMBER, created_at FROM STAFF ORDER BY created_at DESC");
    if($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $staff[] = $row;
        }
        $stmt->close();
    }
    
    return $staff;
}

function updateCustomerMembership($conn, $cust_id, $membership) {
    $stmt = $conn->prepare("UPDATE CUSTOMER SET MEMBERSHIP = ? WHERE CUST_ID = ?");
    if($stmt) {
        $stmt->bind_param("si", $membership, $cust_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function addMenuItem($conn, $name, $description, $price, $category, $stock_level, $admin_id, $image = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'MENU_ITEM'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO MENU_ITEM (ITEM_NAME, ITEM_DESCRIPTION, ITEM_PRICE, ITEM_CATEGORY, STOCK_LEVEL, ADMIN_ID, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("ssdsiis", $name, $description, $price, $category, $stock_level, $admin_id, $image);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function updateMenuItem($conn, $item_id, $name, $description, $price, $category, $stock_level, $active, $image = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'MENU_ITEM'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $sql = "UPDATE MENU_ITEM SET ITEM_NAME = ?, ITEM_DESCRIPTION = ?, ITEM_PRICE = ?, ITEM_CATEGORY = ?, STOCK_LEVEL = ?, active = ?";
    
    if ($image) {
        $sql .= ", image = ? WHERE ITEM_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsiiisi", $name, $description, $price, $category, $stock_level, $active, $image, $item_id);
    } else {
        $sql .= " WHERE ITEM_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsiii", $name, $description, $price, $category, $stock_level, $active, $item_id);
    }
    
    if($stmt) {
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function getSalesReport($conn, $start_date, $end_date) {
    $report = [];
    
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'ORDER'");
    if($result->num_rows == 0) {
        return $report;
    }
    
    $stmt = $conn->prepare("
        SELECT 
            ORDER_DATE as order_date,
            COUNT(ORDER_ID) as total_orders,
            SUM(TOT_AMOUNT) as total_sales
        FROM 
            `ORDER`
        WHERE 
            ORDER_DATE BETWEEN ? AND ?
            AND ORDER_STATUS = 'completed'
        GROUP BY 
            ORDER_DATE
        ORDER BY 
            ORDER_DATE
    ");
    
    if($stmt) {
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $report[] = $row;
        }
        $stmt->close();
    }
    
    return $report;
}

// Helper Functions
function generateOrderNumber() {
    return 'ORD-' . strtoupper(substr(uniqid(), -6));
}

function logActivity($conn, $user_id, $user_type, $action, $details = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'activity_logs'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, user_type, action, details) VALUES (?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("isss", $user_id, $user_type, $action, $details);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function getCustomerByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM CUSTOMER WHERE CUST_EMAIL = ?");
    if($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $customer = $result->fetch_assoc();
            $stmt->close();
            return $customer;
        }
        $stmt->close();
    }
    
    return false;
}

function getStaffByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM STAFF WHERE STAFF_EMAIL = ?");
    if($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $staff = $result->fetch_assoc();
            $stmt->close();
            return $staff;
        }
        $stmt->close();
    }
    
    return false;
}

function getAdminByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM ADMIN WHERE ADM_USERNAME = ?");
    if($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            $stmt->close();
            return $admin;
        }
        $stmt->close();
    }
    
    return false;
}

function updateStockLevel($conn, $item_id, $quantity_sold) {
    $stmt = $conn->prepare("UPDATE MENU_ITEM SET STOCK_LEVEL = STOCK_LEVEL - ? WHERE ITEM_ID = ? AND STOCK_LEVEL >= ?");
    if($stmt) {
        $stmt->bind_param("iii", $quantity_sold, $item_id, $quantity_sold);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}
?>
