<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Include database connection
    if(!file_exists('../includes/db_connect.php')) {
        throw new Exception("Database connection file not found");
    }
    
    include '../includes/db_connect.php';
    
    if(!file_exists('../includes/functions.php')) {
        throw new Exception("Functions file not found");
    }
    
    include '../includes/functions.php';
    
    if(!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Test database connection
    if($conn->connect_error) {
        throw new Exception("Database connection error: " . $conn->connect_error);
    }
    
    $popularItems = getPopularItems($conn, 4);
    
    // Add placeholder images for items without images
    foreach($popularItems as &$item) {
        if(empty($item['image']) || !file_exists('../' . $item['image'])) {
            $item['image'] = '/placeholder.svg?height=200&width=280';
        }
    }
    
    echo json_encode([
        'success' => true,
        'items' => $popularItems,
        'count' => count($popularItems)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'items' => []
    ]);
}
?>
