<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and has permission
if (!isLoggedIn() || (!isAdmin() && !isStaff())) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['order_id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$order_id = intval($input['order_id']);
$status = $input['status'];

// Validate status
$valid_statuses = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

try {
    // Update order status
    $stmt = $conn->prepare("UPDATE `ORDER` SET ORDER_STATUS = ? WHERE ORDER_ID = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        // Log the activity
        logActivity($conn, $_SESSION['user_id'], $_SESSION['user_type'], 'order_status_updated', "Order #$order_id status changed to $status");
        
        // If staff is updating, assign them to the order
        if ($_SESSION['user_type'] === 'staff') {
            $assign_stmt = $conn->prepare("UPDATE `ORDER` SET STAFF_ID = ? WHERE ORDER_ID = ?");
            $assign_stmt->bind_param("ii", $_SESSION['user_id'], $order_id);
            $assign_stmt->execute();
            $assign_stmt->close();
        }
        
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
