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
    <title>Login - Cofeology</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=1.1">
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
                            <label class="form-label">Log In As</label>
                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <label class="user-type-method">
                                    <input type="radio" id="user_type_customer" name="user_type" value="customer" class="form-check-input" <?php if(isset($_POST['user_type']) && $_POST['user_type'] === 'customer') echo 'checked'; ?>>
                                    <div class="user-type-method-icon"><i class="fas fa-user"></i></div>
                                    <span>Customer</span>
                                </label>
                                <label class="user-type-method">
                                    <input type="radio" id="user_type_staff" name="user_type" value="staff" class="form-check-input" <?php if(isset($_POST['user_type']) && $_POST['user_type'] === 'staff') echo 'checked'; ?>>
                                    <div class="user-type-method-icon"><i class="fas fa-user-tie"></i></div>
                                    <span>Staff</span>
                                </label>
                                <label class="user-type-method">
                                    <input type="radio" id="user_type_admin" name="user_type" value="admin" class="form-check-input" <?php if(isset($_POST['user_type']) && $_POST['user_type'] === 'admin') echo 'checked'; ?>>
                                    <div class="user-type-method-icon"><i class="fas fa-user-shield"></i></div>
                                    <span>Admin</span>
                                </label>
                            </div>
                            <style>
                                .user-type-method {
                                    display: flex;
                                    align-items: center;
                                    gap: 8px;
                                    padding: 6px 12px;
                                    border-radius: 6px;
                                    border: 1px solid #ddd;
                                    background: #fff;
                                    cursor: pointer;
                                    transition: border 0.2s;
                                }
                                .user-type-method:hover, .user-type-method input:checked ~ .user-type-method-icon {
                                    border-color: #ff6b6b;
                                }
                                .user-type-method-icon {
                                    width: 24px;
                                    height: 24px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 1.2em;
                                    color: #ff6b6b;
                                }
                                .user-type-method input[type="radio"] {
                                    margin-right: 4px;
                                }
                            </style>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="text" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            <div class="form-text">For admin log in, you can use username or email</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-input-container">
                                <input type="password" id="password" name="password" class="form-control" required>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Log In</button>
                        </div>
                        
                        <div class="form-footer">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1 0 auto;
            padding-bottom: 32px;
        }
        .site-footer, footer.site-footer {
            background: #2d3436;
            color: #fff;
            padding: 40px 0 20px;
            margin-top: 0;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .site-footer .footer-bottom, footer.site-footer .footer-bottom {
            text-align: center;
            padding-top: 16px;
            font-size: 0.95rem;
            color: #ccc;
        }
        .site-footer .footer-content, footer.site-footer .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 0;
        }
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
        
        .password-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 5px;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: #ff6b6b;
        }
        
        .password-input-container .form-control {
            padding-right: 40px;
        }
    </style>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
