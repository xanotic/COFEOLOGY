<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

// Get date range from request or default to last 30 days
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));

// Sales Summary
$sales_query = "SELECT 
    DATE(ORDER_DATE) as date,
    COUNT(*) as total_orders,
    SUM(TOT_AMOUNT) as total_sales,
    AVG(TOT_AMOUNT) as avg_order_value
    FROM `order` 
    WHERE ORDER_DATE BETWEEN ? AND ? AND PAYMENT_STATUS = 'completed'
    GROUP BY DATE(ORDER_DATE)
    ORDER BY DATE(ORDER_DATE)";

$stmt = $conn->prepare($sales_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$sales_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Top Selling Items
$top_items_query = "SELECT 
    mi.ITEM_NAME,
    mi.ITEM_CATEGORY,
    mi.ITEM_PRICE,
    SUM(ol.ORDER_QUANTITY) as total_quantity,
    SUM(ol.ORDER_QUANTITY * ol.item_price) as total_revenue
    FROM order_listing ol
    JOIN menu_item mi ON ol.ITEM_ID = mi.ITEM_ID
    JOIN `order` o ON ol.ORDER_ID = o.ORDER_ID
    WHERE o.ORDER_DATE BETWEEN ? AND ? AND o.PAYMENT_STATUS = 'completed'
    GROUP BY ol.ITEM_ID
    ORDER BY total_quantity DESC
    LIMIT 5";

$stmt = $conn->prepare($top_items_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$top_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Top Customers
$top_customers_query = "SELECT 
    c.CUST_NAME,
    c.CUST_EMAIL,
    c.MEMBERSHIP,
    COUNT(o.ORDER_ID) as total_orders,
    SUM(o.TOT_AMOUNT) as total_spent
    FROM customer c
    JOIN `order` o ON c.CUST_ID = o.CUST_ID
    WHERE o.ORDER_DATE BETWEEN ? AND ? AND o.PAYMENT_STATUS = 'completed'
    GROUP BY c.CUST_ID
    ORDER BY total_spent DESC
    LIMIT 5";

$stmt = $conn->prepare($top_customers_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$top_customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Order Type Distribution
$order_type_query = "SELECT 
    ORDER_TYPE,
    COUNT(*) as count,
    SUM(TOT_AMOUNT) as revenue
    FROM `order`
    WHERE ORDER_DATE BETWEEN ? AND ? AND PAYMENT_STATUS = 'completed'
    GROUP BY ORDER_TYPE";

$stmt = $conn->prepare($order_type_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$order_types = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Hourly Sales Distribution
$hourly_query = "SELECT 
    HOUR(ORDER_TIME) as hour,
    COUNT(*) as orders,
    SUM(TOT_AMOUNT) as revenue
    FROM `order`
    WHERE ORDER_DATE BETWEEN ? AND ? AND PAYMENT_STATUS = 'completed'
    GROUP BY HOUR(ORDER_TIME)
    ORDER BY hour";

$stmt = $conn->prepare($hourly_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$hourly_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Category Performance
$category_query = "SELECT 
    mi.ITEM_CATEGORY as category,
    COUNT(ol.ORDER_ID) as orders,
    SUM(ol.ORDER_QUANTITY) as quantity,
    SUM(ol.ORDER_QUANTITY * ol.item_price) as revenue
    FROM order_listing ol
    JOIN menu_item mi ON ol.ITEM_ID = mi.ITEM_ID
    JOIN `order` o ON ol.ORDER_ID = o.ORDER_ID
    WHERE o.ORDER_DATE BETWEEN ? AND ? AND o.PAYMENT_STATUS = 'completed'
    GROUP BY mi.ITEM_CATEGORY
    ORDER BY revenue DESC";

$stmt = $conn->prepare($category_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$category_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Overall Statistics
$total_revenue = array_sum(array_column($sales_data, 'total_sales'));
$total_orders = array_sum(array_column($sales_data, 'total_orders'));
$avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;
$total_customers = count($top_customers);

// Calculate growth (compare with previous period)
$period_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
$prev_start = date('Y-m-d', strtotime($start_date . " -{$period_days} days"));
$prev_end = date('Y-m-d', strtotime($start_date . " -1 day"));

$prev_query = "SELECT SUM(TOT_AMOUNT) as revenue, COUNT(*) as orders FROM `order` WHERE ORDER_DATE BETWEEN ? AND ? AND PAYMENT_STATUS = 'completed'";
$stmt = $conn->prepare($prev_query);
$stmt->bind_param("ss", $prev_start, $prev_end);
$stmt->execute();
$prev_data = $stmt->get_result()->fetch_assoc();

$revenue_growth = $prev_data['revenue'] > 0 ? (($total_revenue - $prev_data['revenue']) / $prev_data['revenue']) * 100 : 0;
$orders_growth = $prev_data['orders'] > 0 ? (($total_orders - $prev_data['orders']) / $prev_data['orders']) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Cofeology</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="modern-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1><i class="fas fa-chart-bar"></i> Analytics Dashboard</h1>
                        <p>Track your café's performance and insights</p>
                    </div>
                    <div class="header-actions">
                        <div class="date-picker-container">
                            <form method="GET" class="date-form">
                                <div class="date-inputs">
                                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="date-input">
                                    <span class="date-separator">to</span>
                                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="date-input">
                                    <button type="submit" class="btn-update">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="action-buttons">
                            </button>
                            <button class="btn-icon" onclick="printReport()" title="Print Report">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- KPI Cards -->
            <div class="kpi-grid">
                <div class="kpi-card revenue">
                    <div class="kpi-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">RM <?php echo number_format($total_revenue, 0); ?></div>
                        <div class="kpi-label">Total Revenue</div>
                        <div class="kpi-change <?php echo $revenue_growth >= 0 ? 'positive' : 'negative'; ?>">
                            <i class="fas fa-arrow-<?php echo $revenue_growth >= 0 ? 'up' : 'down'; ?>"></i>
                            <?php echo abs(round($revenue_growth, 1)); ?>%
                        </div>
                    </div>
                </div>
                
                <div class="kpi-card orders">
                    <div class="kpi-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value"><?php echo number_format($total_orders); ?></div>
                        <div class="kpi-label">Total Orders</div>
                        <div class="kpi-change <?php echo $orders_growth >= 0 ? 'positive' : 'negative'; ?>">
                            <i class="fas fa-arrow-<?php echo $orders_growth >= 0 ? 'up' : 'down'; ?>"></i>
                            <?php echo abs(round($orders_growth, 1)); ?>%
                        </div>
                    </div>
                </div>
                
                <div class="kpi-card avg-order">
                    <div class="kpi-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value">RM <?php echo number_format($avg_order_value, 0); ?></div>
                        <div class="kpi-label">Avg Order Value</div>
                        <div class="kpi-change neutral">
                            <i class="fas fa-minus"></i>
                            Stable
                        </div>
                    </div>
                </div>
                
                <div class="kpi-card customers">
                    <div class="kpi-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value"><?php echo number_format($total_customers); ?></div>
                        <div class="kpi-label">Active Customers</div>
                        <div class="kpi-change positive">
                            <i class="fas fa-arrow-up"></i>
                            Growing
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Grid -->
            <div class="charts-grid">
                <div class="chart-card large">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-area"></i> Sales Trend</h3>
                        <div class="chart-controls">
                            <button class="chart-btn active" data-period="daily">Daily</button>
                            <button class="chart-btn" data-period="weekly">Weekly</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Order Types</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="orderTypeChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> Peak Hours</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-layer-group"></i> Categories</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Data Tables -->
            <div class="tables-grid">
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fas fa-fire"></i> Top Items</h3>
                        <span class="table-count"><?php echo count($top_items); ?> items</span>
                    </div>
                    <div class="table-content">
                        <?php foreach ($top_items as $index => $item): ?>
                            <div class="table-row">
                                <div class="row-rank"><?php echo $index + 1; ?></div>
                                <div class="row-content">
                                    <div class="row-title"><?php echo htmlspecialchars($item['ITEM_NAME']); ?></div>
                                    <div class="row-subtitle"><?php echo htmlspecialchars($item['ITEM_CATEGORY']); ?></div>
                                </div>
                                <div class="row-stats">
                                    <div class="stat-value"><?php echo number_format($item['total_quantity']); ?></div>
                                    <div class="stat-label">sold</div>
                                </div>
                                <div class="row-revenue">RM <?php echo number_format($item['total_revenue'], 0); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fas fa-crown"></i> Top Customers</h3>
                        <span class="table-count"><?php echo count($top_customers); ?> customers</span>
                    </div>
                    <div class="table-content">
                        <?php foreach ($top_customers as $index => $customer): ?>
                            <div class="table-row">
                                <div class="row-rank"><?php echo $index + 1; ?></div>
                                <div class="row-content">
                                    <div class="row-title"><?php echo htmlspecialchars($customer['CUST_NAME']); ?></div>
                                    <div class="row-subtitle">
                                        <span class="membership-badge <?php echo $customer['MEMBERSHIP']; ?>">
                                            <?php echo ucfirst($customer['MEMBERSHIP']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row-stats">
                                    <div class="stat-value"><?php echo number_format($customer['total_orders']); ?></div>
                                    <div class="stat-label">orders</div>
                                </div>
                                <div class="row-revenue">RM <?php echo number_format($customer['total_spent'], 0); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .modern-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .header-title p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .date-picker-container {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 0.5rem;
            backdrop-filter: blur(10px);
        }
        
        .date-form .date-inputs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .date-input {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .date-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        
        .date-separator {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
        
        .btn-update {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-update:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-icon:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .kpi-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .kpi-card.revenue { border-left-color: #10b981; }
        .kpi-card.orders { border-left-color: #3b82f6; }
        .kpi-card.avg-order { border-left-color: #f59e0b; }
        .kpi-card.customers { border-left-color: #8b5cf6; }
        
        .kpi-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .revenue .kpi-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .orders .kpi-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .avg-order .kpi-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .customers .kpi-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        
        .kpi-content {
            flex: 1;
        }
        
        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .kpi-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .kpi-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .kpi-change.positive { color: #10b981; }
        .kpi-change.negative { color: #ef4444; }
        .kpi-change.neutral { color: #6b7280; }
        
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-rows: auto auto;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .chart-card.large {
            grid-row: span 2;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .chart-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .chart-controls {
            display: flex;
            gap: 0.5rem;
        }
        
        .chart-btn {
            background: #f3f4f6;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .chart-btn.active {
            background: #3b82f6;
            color: white;
        }
        
        .chart-container {
            height: 200px;
            position: relative;
        }
        
        .chart-card.large .chart-container {
            height: 300px;
        }
        
        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .table-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .table-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table-count {
            background: #f3f4f6;
            color: #6b7280;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .table-content {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .table-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .table-row:hover {
            background: #f9fafb;
        }
        
        .row-rank {
            width: 24px;
            height: 24px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
        }
        
        .row-content {
            flex: 1;
        }
        
        .row-title {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }
        
        .row-subtitle {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .row-stats {
            text-align: center;
        }
        
        .stat-value {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.7rem;
        }
        
        .row-revenue {
            font-weight: 700;
            color: #10b981;
            font-size: 0.9rem;
        }
        
        .membership-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .membership-badge.basic {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .membership-badge.premium {
            background: #fef3c7;
            color: #d97706;
        }
        
        .membership-badge.vip {
            background: #fce7f3;
            color: #be185d;
        }
        
        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .tables-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .kpi-grid {
                grid-template-columns: 1fr;
            }
            
            .date-form .date-inputs {
                flex-wrap: wrap;
            }
        }
        
        @media print {
            .header-actions,
            .chart-controls {
                display: none;
            }
        }
    </style>
    
    <script>
        // Modern color palette
        const colors = {
            primary: '#3b82f6',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            purple: '#8b5cf6',
            gradient: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe']
        };
        
        // Chart.js default configuration
        Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6b7280';
        
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($d) { return date('M j', strtotime($d['date'])); }, $sales_data)); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_column($sales_data, 'total_sales')); ?>,
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: colors.primary,
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: RM ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return 'RM ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Order Type Chart
        const orderTypeCtx = document.getElementById('orderTypeChart').getContext('2d');
        const orderTypeChart = new Chart(orderTypeCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_map('ucfirst', array_column($order_types, 'ORDER_TYPE'))); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($order_types, 'count')); ?>,
                    backgroundColor: colors.gradient.slice(0, <?php echo count($order_types); ?>),
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Hourly Chart
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        const hourlyChart = new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function($h) { return $h['hour'] . ':00'; }, $hourly_data)); ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?php echo json_encode(array_column($hourly_data, 'orders')); ?>,
                    backgroundColor: colors.success + '80',
                    borderColor: colors.success,
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'polarArea',
            data: {
                labels: <?php echo json_encode(array_column($category_data, 'category')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($category_data, 'revenue')); ?>,
                    backgroundColor: colors.gradient.map(color => color + '60'),
                    borderColor: colors.gradient,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': RM ' + context.parsed.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Chart controls
        document.querySelectorAll('.chart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Add period switching logic here
            });
        });
        
        function exportReport() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
            window.open(`../api/export-report.php?start_date=${startDate}&end_date=${endDate}`, '_blank');
        }
        
        function printReport() {
            window.print();
        }
        
        // Auto-refresh data every 5 minutes
        setInterval(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
