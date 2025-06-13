<?php
// Basic functions for the food ordering system

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isStaff() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'staff' || $_SESSION['user_role'] === 'admin');
}

function formatCurrency($amount) {
    return 'RM ' . number_format($amount, 2);
}

function getPopularItems($conn, $limit = 4) {
    $items = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'menu_items'");
    if($result->num_rows == 0) {
        return $items; // Return empty array if table doesn't exist
    }
    
    $sql = "SELECT * FROM menu_items WHERE active = 1 LIMIT ?";
    $stmt = $conn->prepare($sql);
    
    if($stmt) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
    }
    
    return $items;
}

function getMenuItems($conn, $category = null) {
    $items = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'menu_items'");
    if($result->num_rows == 0) {
        return $items;
    }
    
    $sql = "SELECT * FROM menu_items WHERE active = 1";
    
    if ($category) {
        $sql .= " AND category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
    } else {
        $stmt = $conn->prepare($sql);
    }
    
    if($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
    }
    
    return $items;
}

// User Authentication Functions
function registerUser($conn, $name, $email, $password, $phone, $address) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
    if($stmt) {
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $address);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt->close();
            return $user_id;
        }
        $stmt->close();
    }
    
    return false;
}

function loginUser($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    if($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                
                // Store data in session variables
                $_SESSION["user_id"] = $user['id'];
                $_SESSION["user_name"] = $user['name'];
                $_SESSION["user_email"] = $user['email'];
                $_SESSION["user_role"] = $user['role'];
                
                $stmt->close();
                return true;
            }
        }
        $stmt->close();
    }
    
    return false;
}

// Order Functions - FIXED parameter order
function createOrder($conn, $user_id, $order_type, $total_amount, $delivery_address = null, $pickup_time = null, $special_instructions = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, delivery_address, pickup_time, special_instructions, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    if($stmt) {
        $stmt->bind_param("issssd", $user_id, $order_type, $delivery_address, $pickup_time, $special_instructions, $total_amount);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            $stmt->close();
            return $order_id;
        }
        $stmt->close();
    }
    
    return false;
}

function addOrderDetail($conn, $order_id, $item_id, $quantity, $price, $special_requests = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'order_details'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO order_details (order_id, item_id, quantity, price, special_requests) VALUES (?, ?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("iiids", $order_id, $item_id, $quantity, $price, $special_requests);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function getUserOrders($conn, $user_id) {
    $orders = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if($result->num_rows == 0) {
        return $orders;
    }
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    if($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
    }
    
    return $orders;
}

function getOrderDetails($conn, $order_id) {
    $details = [];
    
    // Check if tables exist first
    $result = $conn->query("SHOW TABLES LIKE 'order_details'");
    if($result->num_rows == 0) {
        return $details;
    }
    
    $stmt = $conn->prepare("
        SELECT od.*, m.name, m.image 
        FROM order_details od
        JOIN menu_items m ON od.item_id = m.id
        WHERE od.order_id = ?
    ");
    
    if($stmt) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $details[] = $row;
        }
        $stmt->close();
    }
    
    return $details;
}

function updateOrderStatus($conn, $order_id, $status) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if($stmt) {
        $stmt->bind_param("si", $status, $order_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

// Admin Functions
function getAllUsers($conn) {
    $users = [];
    
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if($result->num_rows == 0) {
        return $users;
    }
    
    $stmt = $conn->prepare("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
    if($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
    }
    
    return $users;
}

function updateUserRole($conn, $user_id, $role) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    if($stmt) {
        $stmt->bind_param("si", $role, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function addMenuItem($conn, $name, $description, $price, $category, $image = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'menu_items'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("ssdss", $name, $description, $price, $category, $image);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}

function updateMenuItem($conn, $id, $name, $description, $price, $category, $active, $image = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'menu_items'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $sql = "UPDATE menu_items SET name = ?, description = ?, price = ?, category = ?, active = ?";
    
    if ($image) {
        $sql .= ", image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsisi", $name, $description, $price, $category, $active, $image, $id);
    } else {
        $sql .= " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsii", $name, $description, $price, $category, $active, $id);
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
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if($result->num_rows == 0) {
        return $report;
    }
    
    $stmt = $conn->prepare("
        SELECT 
            DATE(o.created_at) as order_date,
            COUNT(o.id) as total_orders,
            SUM(o.total_amount) as total_sales
        FROM 
            orders o
        WHERE 
            o.created_at BETWEEN ? AND ?
            AND o.status = 'completed'
        GROUP BY 
            DATE(o.created_at)
        ORDER BY 
            order_date
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

function logActivity($conn, $user_id, $action, $details = null) {
    // Check if table exists first
    $result = $conn->query("SHOW TABLES LIKE 'activity_logs'");
    if($result->num_rows == 0) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    if($stmt) {
        $stmt->bind_param("iss", $user_id, $action, $details);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    return false;
}
?>
