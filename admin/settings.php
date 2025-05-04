<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    set_error_message('You do not have permission to access this page');
    redirect(site_url('admin/dashboard.php'));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!check_csrf_token()) {
        // Error message already set by check_csrf_token()
        redirect('settings.php');
    }

    if (isset($_POST['save_settings'])) {
        $settings = [
            'site_title' => sanitize($_POST['site_title']),
            'site_description' => sanitize($_POST['site_description']),
            'contact_email' => sanitize($_POST['contact_email']),
            'contact_phone' => sanitize($_POST['contact_phone']),
            'address' => sanitize($_POST['address']),
            'facebook_url' => sanitize($_POST['facebook_url']),
            'instagram_url' => sanitize($_POST['instagram_url']),
            'twitter_url' => sanitize($_POST['twitter_url']),
            'delivery_areas' => sanitize($_POST['delivery_areas'])
        ];

        // Update each setting
        foreach ($settings as $key => $value) {
            $query = "UPDATE settings SET setting_value = '$value' WHERE setting_key = '$key'";
            mysqli_query($conn, $query);
        }

        set_success_message('Settings updated successfully');
        redirect('settings.php');
    }
}

// Get current settings
$settings = [];
$query = "SELECT * FROM settings";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Site Settings</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">General Settings</h6>
        </div>
        <div class="card-body">
            <form action="settings.php" method="post">
                <?php csrf_token_field(); ?>
                <div class="row">
                    <div class="col-lg-6">
                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 stacked-form-group">
                                    <label for="site_title" class="form-label">Site Title</label>
                                    <input type="text" class="form-control" id="site_title" name="site_title" value="<?php echo isset($settings['site_title']) ? $settings['site_title'] : 'Kissan Agro Foods'; ?>" required>
                                </div>

                                <div class="mb-3 stacked-form-group">
                                    <label for="site_description" class="form-label">Site Description</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo isset($settings['site_description']) ? $settings['site_description'] : 'Quality wheat flour and puffed rice products'; ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-3 stacked-form-group">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo isset($settings['contact_email']) ? $settings['contact_email'] : 'info@kissanagrofoods.com'; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3 stacked-form-group">
                                            <label for="contact_phone" class="form-label">Contact Phone</label>
                                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo isset($settings['contact_phone']) ? $settings['contact_phone'] : '+977 9800000000'; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 stacked-form-group">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo isset($settings['address']) ? $settings['address'] : 'MV37+9JJ, Pipra 45700, Khairba, Mahottari, Nepal'; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <!-- Social Media & Delivery -->
                        <div class="card mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Social Media & Delivery</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 stacked-form-group">
                                    <label for="facebook_url" class="form-label">
                                        <i class="fab fa-facebook text-primary me-2"></i> Facebook URL
                                    </label>
                                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo isset($settings['facebook_url']) ? $settings['facebook_url'] : 'https://facebook.com/kissanagrofoods'; ?>">
                                </div>

                                <div class="mb-3 stacked-form-group">
                                    <label for="instagram_url" class="form-label">
                                        <i class="fab fa-instagram text-danger me-2"></i> Instagram URL
                                    </label>
                                    <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?php echo isset($settings['instagram_url']) ? $settings['instagram_url'] : 'https://instagram.com/kissanagrofoods'; ?>">
                                </div>

                                <div class="mb-3 stacked-form-group">
                                    <label for="twitter_url" class="form-label">
                                        <i class="fab fa-twitter text-info me-2"></i> Twitter URL
                                    </label>
                                    <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?php echo isset($settings['twitter_url']) ? $settings['twitter_url'] : 'https://twitter.com/kissanagrofoods'; ?>">
                                </div>

                                <div class="mb-3 stacked-form-group">
                                    <label for="delivery_areas" class="form-label">
                                        <i class="fas fa-truck text-success me-2"></i> Delivery Areas
                                    </label>
                                    <input type="text" class="form-control" id="delivery_areas" name="delivery_areas" value="<?php echo isset($settings['delivery_areas']) ? $settings['delivery_areas'] : 'Mahottari, Dhanusha'; ?>">
                                    <small class="form-text text-muted">Comma-separated list of areas where you deliver.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 form-actions">
                    <button type="submit" name="save_settings" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>