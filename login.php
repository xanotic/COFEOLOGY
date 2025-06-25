<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

$errors = [];

// Check if user is already logged in
if (isLoggedIn()) {
    // Redirect based on user type
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } elseif (isStaff()) {
        header("Location: staff/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'] ?? null; // Optional user type selection
    
    // Validation
    if (empty($email)) {
        $errors[] = "Email/Username is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, attempt to login
    if (empty($errors)) {
        // For admin, try username first, then email
        if ($user_type === 'admin' || (!$user_type && filter_var($email, FILTER_VALIDATE_EMAIL) === false)) {
            // Try admin login with username
            $admin = getAdminByUsername($conn, $email);
            if ($admin && password_verify($password, $admin['ADM_PASSWORD'])) {
                $_SESSION["user_id"] = $admin['ADM_ID'];
                $_SESSION["user_name"] = $admin['ADM_USERNAME'];
                $_SESSION["user_email"] = $admin['ADM_EMAIL'];
                $_SESSION["user_type"] = 'admin';
                
                logActivity($conn, $admin['ADM_ID'], 'admin', 'user_login', 'Admin logged in successfully');
                header("Location: admin/dashboard.php");
                exit();
            }
        }
        
        // Try regular email login
        if (loginUser($conn, $email, $password, $user_type)) {
            // Log the activity
            logActivity($conn, $_SESSION['user_id'], $_SESSION['user_type'], 'user_login', 'User logged in successfully');
            
            // Redirect based on user type
            if ($_SESSION['user_type'] === 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($_SESSION['user_type'] === 'staff') {
                header("Location: staff/dashboard.php");
            } else {
                // Redirect to intended page if set, otherwise to home
                if (isset($_SESSION['redirect_url'])) {
                    $redirect = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    header("Location: $redirect");
                } else {
                    header("Location: index.php");
                }
            }
            exit();
        } else {
            $errors[] = "Invalid email/username or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Café Delights</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Login to Your Account</h1>
            </div>
        </section>
        
        <section class="login-section">
            <div class="container">
                <div class="form-container">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label for="user_type" class="form-label">Login As</label>
                            <select id="user_type" name="user_type" class="form-control">
                                <option value="">Auto Detect</option>
                                <option value="customer">Customer</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address / Username</label>
                            <input type="text" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            <div class="form-text">For admin login, you can use username or email</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        
                        <div class="form-footer">
                            <a href="forgot-password.php">Forgot Password?</a>
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                    </form>
                    
                    <div class="demo-accounts">
                        <h3>Demo Accounts</h3>
                        <div class="demo-grid">
                            <div class="demo-account">
                                <h4>Admin</h4>
                                <p>Username: admin</p>
                                <p>Password: password</p>
                            </div>
                            <div class="demo-account">
                                <h4>Staff</h4>
                                <p>Email: staff@cafedelights.com</p>
                                <p>Password: password</p>
                            </div>
                            <div class="demo-account">
                                <h4>Customer</h4>
                                <p>Email: john.customer@email.com</p>
                                <p>Password: password</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <style>
        .demo-accounts {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .demo-accounts h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }
        
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .demo-account {
            background: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #ddd;
        }
        
        .demo-account h4 {
            margin: 0 0 10px 0;
            color: #ff6b6b;
        }
        
        .demo-account p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</body>
</html>
