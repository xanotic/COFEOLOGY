<?php
// Determine user type for header display
$is_admin = isset($_SESSION['admin_id']) || isset($_SESSION['admin_logged_in']) || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$is_staff = isset($_SESSION['staff_id']) || isset($_SESSION['staff_logged_in']) || (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'staff') || (isset($_SESSION['role']) && $_SESSION['role'] === 'staff');
?>
<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <h1>COFEOLOGY </h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <?php if(isLoggedIn()): ?>
                        <li><a href="my-orders.php">My Orders</a></li>
                        <?php if(isStaff()): ?>
                            <li><a href="staff/dashboard.php">Staff Dashboard</a></li>
                        <?php endif; ?>
                        <?php if(isAdmin()): ?>
                            <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="header-actions">
                <?php if(isLoggedIn()): ?>
                    <div class="user-menu">
                        <button class="user-menu-btn">
                            <i class="fas fa-user"></i>
                            <span><?php echo $_SESSION['user_name']; ?></span>
                        </button>
                        <div class="user-dropdown">
                            <a href="logout.php">Log Out</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary">Log In</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
                
                <!-- Only show cart for customers, not admin or staff -->
                <?php if (!$is_admin && !$is_staff): ?>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count" id="cart-count">0</span>
                    </a>
                <?php endif; ?>
                
                <button class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>
