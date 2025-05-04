<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in and is admin
if (!check_access(['admin'], 'security log details')) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied';
    exit;
}

// Check if log ID is provided
if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Log ID is required';
    exit;
}

// Get log ID
$log_id = (int)$_GET['id'];

// Get log details
$query = "SELECT * FROM security_logs WHERE id = ?";
$log = db_select_one($query, 'i', [$log_id]);

if (!$log) {
    header('HTTP/1.1 404 Not Found');
    echo 'Log not found';
    exit;
}

// Format additional data
$additional_data = !empty($log['additional_data']) ? json_decode($log['additional_data'], true) : [];
?>

<div class="row">
    <div class="col-md-6">
        <h6>Basic Information</h6>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td><?php echo $log['id']; ?></td>
            </tr>
            <tr>
                <th>Event Type</th>
                <td>
                    <span class="badge bg-<?php echo get_event_type_color($log['event_type']); ?>">
                        <?php echo ucwords(str_replace('_', ' ', $log['event_type'])); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo $log['description']; ?></td>
            </tr>
            <tr>
                <th>Date/Time</th>
                <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6>User Information</h6>
        <table class="table table-bordered">
            <tr>
                <th>User ID</th>
                <td><?php echo $log['user_id'] ? $log['user_id'] : 'N/A'; ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo $log['username']; ?></td>
            </tr>
            <tr>
                <th>IP Address</th>
                <td><?php echo $log['ip_address']; ?></td>
            </tr>
            <tr>
                <th>Request URI</th>
                <td><?php echo $log['request_uri']; ?></td>
            </tr>
        </table>
    </div>
</div>

<?php if (!empty($additional_data)): ?>
<div class="row mt-3">
    <div class="col-12">
        <h6>Additional Data</h6>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($additional_data as $key => $value): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($key); ?></td>
                        <td>
                            <?php 
                            if (is_array($value) || is_object($value)) {
                                echo '<pre>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . '</pre>';
                            } else {
                                echo htmlspecialchars($value);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="row mt-3">
    <div class="col-12">
        <h6>User Agent</h6>
        <div class="p-3 bg-light rounded">
            <?php echo $log['user_agent']; ?>
        </div>
    </div>
</div>

<?php
// Function to get event type color
function get_event_type_color($event_type) {
    switch ($event_type) {
        case 'failed_login':
            return 'danger';
        case 'successful_login':
            return 'success';
        case 'logout':
            return 'info';
        case 'csrf_failure':
            return 'warning';
        case 'suspicious_request':
            return 'danger';
        case 'access_violation':
            return 'warning';
        default:
            return 'secondary';
    }
}
?>
