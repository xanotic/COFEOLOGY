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

// Handle role updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_role') {
        $user_id = intval($_POST['user_id']);
        $new_role = $_POST['role'];
        
        if (updateUserRole($conn, $user_id, $new_role)) {
            $message = "User role updated successfully!";
        } else {
            $error = "Failed to update user role.";
        }
    }
}

// Get all users
$users = getAllUsers($conn);

// Get user statistics
$stmt = $conn->prepare("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$stmt->execute();
$roleStats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stats = [];
foreach ($roleStats as $stat) {
    $stats[$stat['role']] = $stat['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Café Delights</title>
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
                    <h1>Users Management</h1>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="exportUsers()">
                        <i class="fas fa-download"></i> Export
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
                        <p><?php echo $stats['customer'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Staff Members</h3>
                        <p><?php echo $stats['staff'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Administrators</h3>
                        <p><?php echo $stats['admin'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3>New This Month</h3>
                        <p>
                            <?php
                            $thisMonth = date('Y-m');
                            $newUsers = array_filter($users, function($user) use ($thisMonth) {
                                return strpos($user['created_at'], $thisMonth) === 0;
                            });
                            echo count($newUsers);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>All Users</h2>
                    <div class="card-filters">
                        <select id="role-filter" class="form-control">
                            <option value="">All Roles</option>
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                        <input type="text" id="search-filter" class="form-control" placeholder="Search users...">
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
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="users-table">
                                <?php foreach ($users as $user): ?>
                                    <tr data-role="<?php echo $user['role']; ?>" data-search="<?php echo strtolower($user['name'] . ' ' . $user['email']); ?>">
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                        <td>
                                            <span class="role-badge <?php echo $user['role']; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <button class="btn-icon" onclick="changeRole(<?php echo $user['id']; ?>, '<?php echo $user['role']; ?>')" title="Change Role">
                                                    <i class="fas fa-user-cog"></i>
                                                </button>
                                                <button class="btn-icon" onclick="viewUserDetails(<?php echo $user['id']; ?>)" title="View Details">
                                                    <i class="fas fa-eye"></i>
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
        </div>
    </div>
    
    <!-- Role Change Modal -->
    <div class="modal" id="role-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change User Role</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="role-form" method="post">
                    <input type="hidden" name="action" value="update_role">
                    <input type="hidden" id="user-id" name="user_id">
                    
                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-control">
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Role</button>
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
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const roleFilter = document.getElementById('role-filter');
            const searchFilter = document.getElementById('search-filter');
            const usersTable = document.getElementById('users-table');
            
            function filterUsers() {
                const roleValue = roleFilter.value;
                const searchValue = searchFilter.value.toLowerCase();
                const rows = usersTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const rowRole = row.getAttribute('data-role');
                    const rowSearch = row.getAttribute('data-search');
                    
                    let showRow = true;
                    
                    if (roleValue && rowRole !== roleValue) {
                        showRow = false;
                    }
                    
                    if (searchValue && !rowSearch.includes(searchValue)) {
                        showRow = false;
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            roleFilter.addEventListener('change', filterUsers);
            searchFilter.addEventListener('input', filterUsers);
            
            // Modal functionality
            const modal = document.getElementById('role-modal');
            const closeModal = document.querySelector('.close-modal');
            
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
        
        function changeRole(userId, currentRole) {
            document.getElementById('user-id').value = userId;
            document.getElementById('role').value = currentRole;
            document.getElementById('role-modal').style.display = 'flex';
        }
        
        function viewUserDetails(userId) {
            // Redirect to user details page or show modal with user details
            window.location.href = 'user-details.php?id=' + userId;
        }
        
        function exportUsers() {
            window.open('../api/export-users.php', '_blank');
        }
    </script>
</body>
</html>
