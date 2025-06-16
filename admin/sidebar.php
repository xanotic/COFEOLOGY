<div class="dashboard-sidebar">
    <div class="sidebar-header">
        <h2>Café Delights</h2>
        <span class="admin-badge">Admin Panel</span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'menu.php' ? 'active' : ''; ?>">
                    <i class="fas fa-utensils"></i>
                    <span>Menu Management</span>
                </a>
            </li>
            <li>
                <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li>
                <a href="../staff/dashboard.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Staff Panel</span>
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="fas fa-home"></i>
                    <span>Back to Site</span>
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
