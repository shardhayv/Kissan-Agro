<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!check_csrf_token()) {
        // Error message already set by check_csrf_token()
    } else {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        // Check if login_attempts table exists
        $table_exists = false;
        $check_query = "SHOW TABLES LIKE 'login_attempts'";
        $check_result = mysqli_query($conn, $check_query);
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $table_exists = true;
        }

        // Create login_attempts table if it doesn't exist
        if (!$table_exists) {
            $create_query = "CREATE TABLE IF NOT EXISTS login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                locked_until TIMESTAMP NULL DEFAULT NULL,
                INDEX (ip_address),
                INDEX (attempt_time),
                INDEX (locked_until)
            )";
            mysqli_query($conn, $create_query);
        }

        // Check for login attempts limit
        $max_attempts = 5;
        $lockout_time = 15; // minutes

        // Get client IP address
        $ip = $_SERVER['REMOTE_ADDR'];

        try {
            // Attempt login first - if successful, no need to check lockouts
            if (login($username, $password)) {
                redirect('dashboard.php');
                exit;
            }

            // If login failed and table exists, handle lockout logic
            if ($table_exists) {
                // Check if IP is locked out
                $query = "SELECT * FROM login_attempts WHERE ip_address = ? AND locked_until > NOW()";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 's', $ip);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($result && mysqli_num_rows($result) > 0) {
                    $lockout = mysqli_fetch_assoc($result);
                    $time_left = ceil((strtotime($lockout['locked_until']) - time()) / 60);
                    set_error_message("Too many failed login attempts. Please try again in $time_left minutes.");
                } else {
                    // Record failed attempt
                    $query = "INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW())";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 's', $ip);
                    mysqli_stmt_execute($stmt);

                    // Check if should lock out
                    $query = "SELECT COUNT(*) as attempts FROM login_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 'si', $ip, $lockout_time);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $attempts = mysqli_fetch_assoc($result)['attempts'];

                    if ($attempts >= $max_attempts) {
                        // Lock out the IP
                        $query = "INSERT INTO login_attempts (ip_address, attempt_time, locked_until) VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL ? MINUTE))";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, 'si', $ip, $lockout_time);
                        mysqli_stmt_execute($stmt);
                        set_error_message("Too many failed login attempts. Your IP has been temporarily locked.");
                    } else {
                        set_error_message('Invalid username or password');
                    }
                }
            } else {
                // If table doesn't exist, just show generic error
                set_error_message('Invalid username or password');
            }
        } catch (Exception $e) {
            // Log error and show generic message
            error_log("Login error: " . $e->getMessage());
            set_error_message('An error occurred during login. Please try again.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kissan Agro Foods | Admin Login</title>
    <meta name="description" content="Admin login page for Kissan Agro Foods">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>">
</head>

<body>
    <div class="container login-container">
        <div class="login-card">
            <div class="login-logo">
                <h1>Kissan Agro Foods</h1>
                <p class="text-muted">Admin Panel</p>
            </div>

            <?php display_messages(); ?>

            <form action="index.php" method="post">
                <?php csrf_token_field(); ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </div>
            </form>

            <div class="alert alert-info mt-4">
                <p class="mb-2"><strong>Admin Access:</strong></p>
                <p class="mb-0 small">Please contact the system administrator if you need access to the admin panel.</p>
                <p class="mb-0 small">If you're setting up for the first time, run create_admin.php to create the admin user.</p>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo site_url('index.php'); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-home me-2"></i> Back to Website
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="<?php echo site_url('assets/js/admin.js'); ?>"></script>
</body>

</html>