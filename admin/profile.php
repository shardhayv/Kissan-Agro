<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    set_error_message('You must be logged in to access this page');
    redirect(site_url('admin/index.php'));
}

// Get current user
$user_id = $_SESSION['user_id'];
$user = get_user($user_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $email = sanitize($_POST['email']);
        $full_name = sanitize($_POST['full_name']);
        $password = !empty($_POST['password']) ? $_POST['password'] : '';

        $user_data = [
            'id' => $user_id,
            'email' => $email,
            'full_name' => $full_name
        ];

        if (!empty($password)) {
            $user_data['password'] = $password;
        }

        if (update_user($user_data)) {
            // Update session data
            $_SESSION['full_name'] = $full_name;

            set_success_message('Profile updated successfully');
            redirect('profile.php');
        }
    }
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body">
                    <form action="profile.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" readonly>
                            <small class="form-text text-muted">Username cannot be changed.</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" value="<?php echo ucfirst($user['role']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Leave empty to keep current password.</small>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Account Created:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Last Login:</strong> <?php echo date('d M Y H:i'); ?></p>

                    <div class="alert alert-info mt-4">
                        <h5><i class="fas fa-info-circle"></i> Security Tips</h5>
                        <ul class="mb-0">
                            <li>Use a strong password with a mix of letters, numbers, and special characters.</li>
                            <li>Change your password regularly.</li>
                            <li>Do not share your login credentials with others.</li>
                            <li>Always log out when you're done using the admin panel.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>