<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

$errors = [];

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, attempt to login
    if (empty($errors)) {
        if (loginUser($conn, $email, $password)) {
            // Log the activity
            logActivity($conn, $_SESSION['user_id'], 'user_login', 'User logged in successfully');
            
            // Redirect based on user role
            if ($_SESSION['user_role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($_SESSION['user_role'] === 'staff') {
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
            $errors[] = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Caféee Delights</title>
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
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
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
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
