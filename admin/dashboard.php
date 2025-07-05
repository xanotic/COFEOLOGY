<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Get dashboard statistics
$stats = [];

// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM `ORDER`");
$stats['total_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Total users (customers)
$result = $conn->query("SELECT COUNT(*) as total FROM CUSTOMER");
$stats['total_customers'] = $result ? $result->fetch_assoc()['total'] : 0;

// Total revenue
$result = $conn->query("SELECT SUM(TOT_AMOUNT) as total FROM `ORDER` WHERE ORDER_STATUS = 'completed'");
$stats['total_revenue'] = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;

// Today's orders
$today = date('Y-m-d');
$result = $conn->query("SELECT COUNT(*) as total FROM `ORDER` WHERE DATE(ORDER_DATE) = '$today'");
$stats['today_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Recent orders
$stmt = $conn->prepare("
    SELECT o.ORDER_ID, o.ORDER_NUMBER, o.ORDER_DATE, o.ORDER_TIME, o.ORDER_TYPE, o.ORDER_STATUS, o.TOT_AMOUNT, 
           c.CUST_NAME as customer_name, c.CUST_EMAIL as customer_email
    FROM `ORDER` o
    JOIN CUSTOMER c ON o.CUST_ID = c.CUST_ID
    ORDER BY o.ORDER_DATE DESC, o.ORDER_TIME DESC
    LIMIT 10
");
$stmt->execute();
$recentOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Recent users (customers)
$stmt = $conn->prepare("
    SELECT CUST_ID, CUST_NAME, CUST_EMAIL, MEMBERSHIP, created_at
    FROM CUSTOMER
    ORDER BY created_at DESC
    LIMIT 5
");
$stmt->execute();
$recentUsers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cofeology</title>
    <link rel="stylesheet" href="../css/style.css?v=1.1">
    <link rel="stylesheet" href="../css/dashboard.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="header-actions">
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="../logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </header>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <p><?php echo number_format($stats['total_orders']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Customers</h3>
                        <p><?php echo number_format($stats['total_customers']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <p><?php echo formatCurrency($stats['total_revenue']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Today's Orders</h3>
                        <p><?php echo number_format($stats['today_orders']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-main">
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Orders</h2>
                            <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentOrders)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No orders found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recentOrders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo $order['ORDER_ID']; ?></td>
                                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                    <td><?php echo ucfirst($order['ORDER_TYPE']); ?></td>
                                                    <td>
                                                        <span class="status-badge <?php echo $order['ORDER_STATUS']; ?>">
                                                            <?php echo ucfirst($order['ORDER_STATUS']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo formatCurrency($order['TOT_AMOUNT']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($order['ORDER_DATE'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Recent Users</h2>
                            <a href="users.php" class="btn btn-outline btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentUsers)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No users found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recentUsers as $user): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($user['CUST_NAME']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['CUST_EMAIL']); ?></td>
                                                    <td>
                                                        <span class="role-badge customer">
                                                            Customer
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="menu.php" class="quick-action-btn">
                                <i class="fas fa-utensils"></i>
                                <span>Manage Menu</span>
                            </a>
                            <a href="orders.php" class="quick-action-btn">
                                <i class="fas fa-shopping-bag"></i>
                                <span>View Orders</span>
                            </a>
                            <a href="users.php" class="quick-action-btn">
                                <i class="fas fa-users"></i>
                                <span>Manage Users</span>
                            </a>
                            <a href="reports.php" class="quick-action-btn">
                                <i class="fas fa-chart-bar"></i>
                                <span>View Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh dashboard every 30 seconds
        setInterval(function() {
            // You can add AJAX calls here to update stats without full page reload
        }, 30000);
    </script>
</body>
</html>
