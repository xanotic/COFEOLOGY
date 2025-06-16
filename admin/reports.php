<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Get date range from query parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Get sales report
$salesReport = getSalesReport($conn, $start_date, $end_date);

// Get total statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as avg_order_value
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ? 
    AND status = 'completed'
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$totalStats = $stmt->get_result()->fetch_assoc();

// Get order type breakdown
$stmt = $conn->prepare("
    SELECT 
        order_type,
        COUNT(*) as count,
        SUM(total_amount) as revenue
    FROM orders 
    WHERE DATE(created_at) BETWEEN ? AND ? 
    AND status = 'completed'
    GROUP BY order_type
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$orderTypeStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get top selling items
$stmt = $conn->prepare("
    SELECT 
        mi.name,
        mi.category,
        SUM(od.quantity) as total_quantity,
        SUM(od.quantity * od.price) as total_revenue
    FROM order_details od
    JOIN menu_items mi ON od.item_id = mi.id
    JOIN orders o ON od.order_id = o.id
    WHERE DATE(o.created_at) BETWEEN ? AND ?
    AND o.status = 'completed'
    GROUP BY mi.id, mi.name, mi.category
    ORDER BY total_quantity DESC
    LIMIT 10
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$topItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Café Delights</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Reports & Analytics</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export PDF
                    </button>
                </div>
            </header>
            
            <div class="card">
                <div class="card-header">
                    <h2>Date Range</h2>
                </div>
                <div class="card-body">
                    <form method="get" class="date-range-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <p><?php echo number_format($totalStats['total_orders'] ?? 0); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <p><?php echo formatCurrency($totalStats['total_revenue'] ?? 0); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Average Order Value</h3>
                        <p><?php echo formatCurrency($totalStats['avg_order_value'] ?? 0); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Days in Period</h3>
                        <p><?php echo (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>Sales Trend</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Order Types</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="orderTypeChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>Top Selling Items</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Category</th>
                                        <th>Quantity Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($topItems)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($topItems as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                                <td><?php echo number_format($item['total_quantity']); ?></td>
                                                <td><?php echo formatCurrency($item['total_revenue']); ?></td>
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
                        <h2>Daily Sales</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($salesReport)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No data available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($salesReport as $day): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($day['order_date'])); ?></td>
                                                <td><?php echo number_format($day['total_orders']); ?></td>
                                                <td><?php echo formatCurrency($day['total_sales']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .date-range-form {
            display: flex;
            gap: 20px;
            align-items: end;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 5px;
            font-weight: 500;
        }
    </style>
    
    <script>
        // Sales trend chart
        const salesData = <?php echo json_encode($salesReport); ?>;
        const salesLabels = salesData.map(item => new Date(item.order_date).toLocaleDateString());
        const salesValues = salesData.map(item => parseFloat(item.total_sales));
        
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Daily Sales (RM)',
                    data: salesValues,
                    borderColor: '#ff6b6b',
                    backgroundColor: 'rgba(255, 107, 107, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Order type chart
        const orderTypeData = <?php echo json_encode($orderTypeStats); ?>;
        const orderTypeLabels = orderTypeData.map(item => item.order_type.charAt(0).toUpperCase() + item.order_type.slice(1));
        const orderTypeCounts = orderTypeData.map(item => parseInt(item.count));
        
        const orderTypeCtx = document.getElementById('orderTypeChart').getContext('2d');
        new Chart(orderTypeCtx, {
            type: 'doughnut',
            data: {
                labels: orderTypeLabels,
                datasets: [{
                    data: orderTypeCounts,
                    backgroundColor: [
                        '#ff6b6b',
                        '#4ecdc4',
                        '#ffd166'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
        
        function exportReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            window.open(`../api/export-report.php?start_date=${startDate}&end_date=${endDate}`, '_blank');
        }
    </script>
</body>
</html>
