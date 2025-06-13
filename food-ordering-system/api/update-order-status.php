<?php
header('Content-Type: application/json');
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Include database connection and functions
    include '../includes/db_connect.php';
    include '../includes/functions.php';
    
    // Check if user is staff
    if (!isStaff()) {
        throw new Exception("Unauthorized access");
    }
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['order_id']) || !isset($input['status'])) {
        throw new Exception("Missing required parameters");
    }
    
    $order_id = intval($input['order_id']);
    $status = $input['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'preparing', 'ready', 'out_for_delivery', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        throw new Exception("Invalid status");
    }
    
    // Update order status
    $result = updateOrderStatus($conn, $order_id, $status);
    
    if ($result) {
        // Log activity
        logActivity($conn, $_SESSION['user_id'], 'order_status_updated', "Order #$order_id status updated to $status");
        
        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    } else {
        throw new Exception("Failed to update order status");
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
