<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    set_error_message('You do not have permission to access this page');
    redirect(site_url('admin/dashboard.php'));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update user
    if (isset($_POST['save_user'])) {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $full_name = sanitize($_POST['full_name']);
        $role = sanitize($_POST['role']);
        $password = isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : '';

        // Add new user
        if (!isset($_POST['user_id'])) {
            if (empty($password)) {
                set_error_message('Password is required for new users');
            } else {
                $user_data = [
                    'username' => $username,
                    'email' => $email,
                    'full_name' => $full_name,
                    'role' => $role,
                    'password' => $password
                ];

                if (register_user($user_data)) {
                    redirect('users.php');
                }
            }
        }
        // Update existing user
        else {
            $user_id = (int)$_POST['user_id'];

            $user_data = [
                'id' => $user_id,
                'email' => $email,
                'full_name' => $full_name,
                'role' => $role
            ];

            if (!empty($password)) {
                $user_data['password'] = $password;
            }

            if (update_user($user_data)) {
                redirect('users.php');
            }
        }
    }

    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];

        if (delete_user($user_id)) {
            redirect('users.php');
        }
    }
}

// Get user for edit
$user = null;
if (isset($_GET['edit'])) {
    $user_id = (int)$_GET['edit'];
    $user = get_user($user_id);

    if (!$user) {
        set_error_message('User not found');
        redirect('users.php');
    }
}

// Get all users
$users = get_users();

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo isset($_GET['action']) && $_GET['action'] === 'add' ? 'Add New User' : (isset($_GET['edit']) ? 'Edit User' : 'Manage Users'); ?>
        </h1>
        <?php if (!isset($_GET['action']) && !isset($_GET['edit'])): ?>
            <a href="users.php?action=add" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || isset($_GET['edit'])): ?>
        <!-- Add/Edit User Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <?php echo isset($_GET['edit']) ? 'Edit User' : 'Add New User'; ?>
                </h6>
            </div>
            <div class="card-body">
                <form action="users.php" method="post">
                    <?php if (isset($_GET['edit'])): ?>
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($user) ? $user['username'] : ''; ?>" <?php echo isset($user) ? 'readonly' : 'required'; ?>>
                                <?php if (isset($user)): ?>
                                    <small class="form-text text-muted">Username cannot be changed.</small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user) ? $user['email'] : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($user) ? $user['full_name'] : ''; ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <?php echo isset($user) ? '' : '*'; ?></label>
                                <input type="password" class="form-control" id="password" name="password" <?php echo isset($user) ? '' : 'required'; ?>>
                                <?php if (isset($user)): ?>
                                    <small class="form-text text-muted">Leave empty to keep current password.</small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="admin" <?php echo isset($user) && $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="manager" <?php echo isset($user) && $user['role'] === 'manager' ? 'selected' : ''; ?>>Manager</option>
                                    <option value="staff" <?php echo isset($user) && $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" name="save_user" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save User
                        </button>
                        <a href="users.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Users List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
            </div>
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <p class="text-center">No users found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?php echo $u['id']; ?></td>
                                        <td><?php echo $u['username']; ?></td>
                                        <td><?php echo $u['email']; ?></td>
                                        <td><?php echo $u['full_name']; ?></td>
                                        <td>
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php elseif ($u['role'] === 'manager'): ?>
                                                <span class="badge bg-warning">Manager</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Staff</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                                        <td>
                                            <a href="users.php?edit=<?php echo $u['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                                <form action="users.php" method="post" class="d-inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>