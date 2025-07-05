<div class="dashboard-sidebar">
    <div class="sidebar-header">
        <h2 class="cafe-name">Cofeology</h2>
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
                    <span>Menu</span>
                </a>
            </li>
            <?php if (isAdmin()): ?>
            <li>
                <a href="../admin/dashboard.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin Panel</span>
                </a>
            </li>
            <?php endif; ?>
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
