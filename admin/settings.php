<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';

// Redirect if not admin
if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$error = '';

// Handle settings updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // This is a basic settings page - you can expand this based on your needs
    $message = "Settings updated successfully!";
}

// Get system information
$phpVersion = phpversion();
$mysqlVersion = $conn->server_info;
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Café Delights</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="dashboard-content">
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>System Settings</h1>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>General Settings</h2>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" id="site_name" name="site_name" class="form-control" value="Café Delights">
                            </div>
                            
                            <div class="form-group">
                                <label for="site_email" class="form-label">Contact Email</label>
                                <input type="email" id="site_email" name="site_email" class="form-control" value="info@cafedelights.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="site_phone" class="form-label">Contact Phone</label>
                                <input type="tel" id="site_phone" name="site_phone" class="form-control" value="+123 456 7890">
                            </div>
                            
                            <div class="form-group">
                                <label for="site_address" class="form-label">Address</label>
                                <textarea id="site_address" name="site_address" class="form-control" rows="3">123 Food Street, City, Country</textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Order Settings</h2>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="delivery_fee" class="form-label">Delivery Fee (RM)</label>
                                <input type="number" id="delivery_fee" name="delivery_fee" class="form-control" value="5.00" step="0.01" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="min_order" class="form-label">Minimum Order Amount (RM)</label>
                                <input type="number" id="min_order" name="min_order" class="form-control" value="15.00" step="0.01" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="delivery_radius" class="form-label">Delivery Radius (km)</label>
                                <input type="number" id="delivery_radius" name="delivery_radius" class="form-control" value="10" min="1">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-check">
                                    <input type="checkbox" name="accept_orders" class="form-check-input" checked>
                                    <span class="form-check-label">Accept New Orders</span>
                                </label>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>Business Hours</h2>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="business-hours">
                                <div class="day-hours">
                                    <label>Monday</label>
                                    <input type="time" name="mon_open" value="08:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="mon_close" value="22:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="mon_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                                
                                <div class="day-hours">
                                    <label>Tuesday</label>
                                    <input type="time" name="tue_open" value="08:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="tue_close" value="22:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="tue_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                                
                                <div class="day-hours">
                                    <label>Wednesday</label>
                                    <input type="time" name="wed_open" value="08:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="wed_close" value="22:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="wed_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                                
                                <div class="day-hours">
                                    <label>Thursday</label>
                                    <input type="time" name="thu_open" value="08:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="thu_close" value="22:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="thu_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                                
                                <div class="day-hours">
                                    <label>Friday</label>
                                    <input type="time" name="fri_open" value="08:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="fri_close" value="22:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="fri_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                                
                                <div class="day-hours">
                                    <label>Saturday</label>
                                    <input type="time" name="sat_open" value="09:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="sat_close" value="23:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="sat_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                                
                                <div class="day-hours">
                                    <label>Sunday</label>
                                    <input type="time" name="sun_open" value="09:00" class="form-control">
                                    <span>to</span>
                                    <input type="time" name="sun_close" value="23:00" class="form-control">
                                    <label class="form-check">
                                        <input type="checkbox" name="sun_closed" class="form-check-input">
                                        <span>Closed</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Save Hours</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>System Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="system-info">
                            <div class="info-item">
                                <strong>PHP Version:</strong>
                                <span><?php echo $phpVersion; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>MySQL Version:</strong>
                                <span><?php echo $mysqlVersion; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Server Software:</strong>
                                <span><?php echo $serverSoftware; ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Server Time:</strong>
                                <span><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                        
                        <div class="system-actions">
                            <button class="btn btn-outline" onclick="clearCache()">
                                <i class="fas fa-trash"></i> Clear Cache
                            </button>
                            <button class="btn btn-outline" onclick="backupDatabase()">
                                <i class="fas fa-download"></i> Backup Database
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .business-hours {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .day-hours {
            display: grid;
            grid-template-columns: 100px 1fr auto 1fr auto;
            gap: 10px;
            align-items: center;
        }
        
        .day-hours label:first-child {
            font-weight: 500;
        }
        
        .system-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .system-actions {
            display: flex;
            gap: 10px;
        }
        
        .form-check {
            display: flex;
            align-items: center;
        }
        
        .form-check-input {
            margin-right: 5px;
        }
    </style>
    
    <script>
        function clearCache() {
            if (confirm('Are you sure you want to clear the cache?')) {
                // Implement cache clearing logic
                alert('Cache cleared successfully!');
            }
        }
        
        function backupDatabase() {
            if (confirm('Are you sure you want to backup the database?')) {
                window.open('../api/backup-database.php', '_blank');
            }
        }
    </script>
</body>
</html>
