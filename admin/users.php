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

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_membership':
                $cust_id = intval($_POST['cust_id']);
                $membership = $_POST['membership'];
                
                $stmt = $conn->prepare("UPDATE customer SET MEMBERSHIP = ? WHERE CUST_ID = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $membership, $cust_id);
                    if ($stmt->execute()) {
                        $message = "Customer membership updated successfully!";
                    } else {
                        $error = "Failed to update customer membership.";
                    }
                    $stmt->close();
                }
                break;
                
            case 'add_staff':
                $name = $_POST['name'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $phone = $_POST['phone'];
                
                // Generate staff number
                $staff_number = getNextCustomId($conn, 'staff', 'S#', 'STAFF_NUMBER');
                
                $stmt = $conn->prepare("INSERT INTO staff (STAFF_NUMBER, STAFF_NAME, STAFF_EMAIL, STAFF_PASSWORD, STAFF_PNUMBER) VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssss", $staff_number, $name, $email, $password, $phone);
                    if ($stmt->execute()) {
                        $message = "Staff member added successfully!";
                    } else {
                        $error = "Failed to add staff member.";
                    }
                    $stmt->close();
                }
                break;
        }
    }
}

// Get all customers
$customers = [];
$result = $conn->query("SELECT * FROM customer ORDER BY created_at DESC");
if ($result) {
    $customers = $result->fetch_all(MYSQLI_ASSOC);
}

// Get all staff
$staff = [];
$result = $conn->query("SELECT * FROM staff ORDER BY created_at DESC");
if ($result) {
    $staff = $result->fetch_all(MYSQLI_ASSOC);
}

// Get user statistics
$stats = [];

// Total customers
$result = $conn->query("SELECT COUNT(*) as total FROM customer");
$stats['total_customers'] = $result ? $result->fetch_assoc()['total'] : 0;

// Total staff
$result = $conn->query("SELECT COUNT(*) as total FROM staff");
$stats['total_staff'] = $result ? $result->fetch_assoc()['total'] : 0;

// Premium members
$result = $conn->query("SELECT COUNT(*) as total FROM customer WHERE MEMBERSHIP = 'premium'");
$stats['premium_members'] = $result ? $result->fetch_assoc()['total'] : 0;

// New users this month
$thisMonth = date('Y-m');
$result = $conn->query("SELECT COUNT(*) as total FROM customer WHERE DATE_FORMAT(created_at, '%Y-%m') = '$thisMonth'");
$stats['new_users'] = $result ? $result->fetch_assoc()['total'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Cofeology</title>
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
                    <h1>Users Management</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddStaffModal()">
                        <i class="fas fa-user-plus"></i> Add Staff
                    </button>
                </div>
            </header>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-stats">
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
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Staff Members</h3>
                        <p><?php echo number_format($stats['total_staff']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Premium Members</h3>
                        <p><?php echo number_format($stats['premium_members']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3>New This Month</h3>
                        <p><?php echo number_format($stats['new_users']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-header">
                        <h2>Customers</h2>
                        <div class="card-filters">
                            <select id="membership-filter" class="form-control">
                                <option value="">All Memberships</option>
                                <option value="basic">Basic</option>
                                <option value="premium">Premium</option>
                                <option value="vip">VIP</option>
                            </select>
                            <input type="text" id="customer-search" class="form-control" placeholder="Search customers...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Membership</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customers-table">
                                    <?php foreach ($customers as $customer): ?>
                                        <tr data-membership="<?php echo $customer['MEMBERSHIP']; ?>" 
                                            data-search="<?php echo strtolower($customer['CUST_NAME'] . ' ' . $customer['CUST_EMAIL']); ?>">
                                            <td><?php echo $customer['CUSTOMER_ID']; ?></td>
                                            <td><?php echo htmlspecialchars($customer['CUST_NAME']); ?></td>
                                            <td><?php echo htmlspecialchars($customer['CUST_EMAIL']); ?></td>
                                            <td><?php echo htmlspecialchars($customer['CUST_NPHONE']); ?></td>
                                            <td>
                                                <span class="membership-badge <?php echo $customer['MEMBERSHIP']; ?>">
                                                    <?php echo ucfirst($customer['MEMBERSHIP']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                                            <td>
                                                <div class="table-actions">
                                                    <button class="btn-icon" onclick="updateMembership(<?php echo $customer['CUST_ID']; ?>, '<?php echo $customer['MEMBERSHIP']; ?>')" title="Update Membership">
                                                        <i class="fas fa-crown"></i>
                                                    </button>
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Staff Members</h2>
                        <div class="card-filters">
                            <input type="text" id="staff-search" class="form-control" placeholder="Search staff...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="staff-table">
                                    <?php foreach ($staff as $member): ?>
                                        <tr data-search="<?php echo strtolower($member['STAFF_NAME'] . ' ' . $member['STAFF_EMAIL']); ?>">
                                            <td><?php echo $member['STAFF_NUMBER']; ?></td>
                                            <td><?php echo htmlspecialchars($member['STAFF_NAME']); ?></td>
                                            <td><?php echo htmlspecialchars($member['STAFF_EMAIL']); ?></td>
                                            <td><?php echo htmlspecialchars($member['STAFF_PNUMBER']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                                            <td>
                                                <div class="table-actions">
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Membership Update Modal -->
    <div class="modal" id="membership-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Customer Membership</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="membership-form" method="post">
                    <input type="hidden" name="action" value="update_membership">
                    <input type="hidden" id="membership-cust-id" name="cust_id">
                    
                    <div class="form-group">
                        <label for="membership" class="form-label">Membership Level</label>
                        <select id="membership" name="membership" class="form-control">
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('membership-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Membership</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Staff Modal -->
    <div class="modal" id="staff-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Staff Member</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="staff-form" method="post">
                    <input type="hidden" name="action" value="add_staff">
                    
                    <div class="form-group">
                        <label for="staff-name" class="form-label">Full Name</label>
                        <input type="text" id="staff-name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="staff-email" class="form-label">Email</label>
                        <input type="email" id="staff-email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="staff-password" class="form-label">Password</label>
                        <input type="password" id="staff-password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="staff-phone" class="form-label">Phone Number</label>
                        <input type="tel" id="staff-phone" name="phone" class="form-control" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('staff-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Staff</button>
                    </div>
                </form>
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
        
        .membership-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .membership-badge.basic {
            background-color: #e2e3e5;
            color: #495057;
        }
        
        .membership-badge.premium {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .membership-badge.vip {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .card-filters {
            display: flex;
            gap: 10px;
        }
        
        .card-filters .form-control {
            width: auto;
            min-width: 150px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #333;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality for customers
            const membershipFilter = document.getElementById('membership-filter');
            const customerSearch = document.getElementById('customer-search');
            const customersTable = document.getElementById('customers-table');
            
            function filterCustomers() {
                const membershipValue = membershipFilter.value;
                const searchValue = customerSearch.value.toLowerCase();
                const rows = customersTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowMembership = row.getAttribute('data-membership');
                    const rowSearch = row.getAttribute('data-search');
                    
                    let showRow = true;
                    
                    if (membershipValue && rowMembership !== membershipValue) {
                        showRow = false;
                    }
                    
                    if (searchValue && !rowSearch.includes(searchValue)) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            membershipFilter.addEventListener('change', filterCustomers);
            customerSearch.addEventListener('input', filterCustomers);
            
            // Filter functionality for staff
            const staffSearch = document.getElementById('staff-search');
            const staffTable = document.getElementById('staff-table');
            
            staffSearch.addEventListener('input', function() {
                const searchValue = this.value.toLowerCase();
                const rows = staffTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowSearch = row.getAttribute('data-search');
                    const showRow = !searchValue || rowSearch.includes(searchValue);
                    row.style.display = showRow ? '' : 'none';
                });
            });
            
            // Modal functionality
            const modals = document.querySelectorAll('.modal');
            const closeButtons = document.querySelectorAll('.close-modal');
            
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    modal.style.display = 'none';
                });
            });
            
            window.addEventListener('click', function(event) {
                modals.forEach(modal => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });
        });
        
        function updateMembership(custId, currentMembership) {
            document.getElementById('membership-cust-id').value = custId;
            document.getElementById('membership').value = currentMembership;
            document.getElementById('membership-modal').style.display = 'flex';
        }
        
        function openAddStaffModal() {
            document.getElementById('staff-form').reset();
            document.getElementById('staff-modal').style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function exportUsers() {
            window.open('../api/export-users.php', '_blank');
        }
    </script>
</body>
</html>
