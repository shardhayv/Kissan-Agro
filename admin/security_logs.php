<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in and is admin
if (!check_access(['admin'], 'security logs')) {
    set_error_message('You do not have permission to access this page');
    redirect(site_url('admin/dashboard.php'));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!check_csrf_token()) {
        // Error message already set by check_csrf_token()
        redirect('security_logs.php');
    }

    // Clear logs
    if (isset($_POST['clear_logs'])) {
        $query = "DELETE FROM security_logs";

        if (db_execute($query)) {
            set_success_message('Security logs cleared successfully');
        } else {
            set_error_message('Error clearing security logs');
        }

        redirect('security_logs.php');
    }
}

// Get filter parameters
$event_type = isset($_GET['event_type']) ? sanitize($_GET['event_type']) : '';
$username = isset($_GET['username']) ? sanitize($_GET['username']) : '';
$ip_address = isset($_GET['ip_address']) ? sanitize($_GET['ip_address']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

// Build query
$query = "SELECT * FROM security_logs WHERE 1=1";
$params = [];
$types = '';

if (!empty($event_type)) {
    $query .= " AND event_type = ?";
    $params[] = $event_type;
    $types .= 's';
}

if (!empty($username)) {
    $query .= " AND username LIKE ?";
    $params[] = "%$username%";
    $types .= 's';
}

if (!empty($ip_address)) {
    $query .= " AND ip_address LIKE ?";
    $params[] = "%$ip_address%";
    $types .= 's';
}

if (!empty($date_from)) {
    $query .= " AND created_at >= ?";
    $params[] = $date_from . ' 00:00:00';
    $types .= 's';
}

if (!empty($date_to)) {
    $query .= " AND created_at <= ?";
    $params[] = $date_to . ' 23:59:59';
    $types .= 's';
}

// Add order by
$query .= " ORDER BY created_at DESC";

// Add limit
$limit = 100;
$query .= " LIMIT $limit";

// Get logs
$logs = !empty($types) ? db_select($query, $types, $params) : db_select($query);

// Get distinct event types for filter
$event_types_query = "SELECT DISTINCT event_type FROM security_logs ORDER BY event_type";
$event_types = db_select($event_types_query);

// Include admin header
include 'includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Security Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Security Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Logs</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-bs-toggle="collapse" data-bs-target="#filter-collapse" aria-expanded="false">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
            <div class="card-body collapse" id="filter-collapse">
                <form action="security_logs.php" method="get" class="mb-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="event_type" class="form-label">Event Type</label>
                            <select class="form-select" id="event_type" name="event_type">
                                <option value="">All Events</option>
                                <?php foreach ($event_types as $type): ?>
                                    <option value="<?php echo $type['event_type']; ?>" <?php echo $event_type === $type['event_type'] ? 'selected' : ''; ?>>
                                        <?php echo ucwords(str_replace('_', ' ', $type['event_type'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" placeholder="Filter by username">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control" id="ip_address" name="ip_address" value="<?php echo $ip_address; ?>" placeholder="Filter by IP address">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                        </div>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i> Filter
                        </button>
                        <a href="security_logs.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Security Logs</h3>
                <div class="card-tools">
                    <form action="security_logs.php" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all security logs? This action cannot be undone.');">
                        <?php csrf_token_field(); ?>
                        <button type="submit" name="clear_logs" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-2"></i> Clear Logs
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Event Type</th>
                            <th>Description</th>
                            <th>Username</th>
                            <th>IP Address</th>
                            <th>Date/Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No security logs found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo $log['id']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo get_event_type_color($log['event_type']); ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $log['event_type'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $log['description']; ?></td>
                                    <td><?php echo $log['username']; ?></td>
                                    <td><?php echo $log['ip_address']; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-details" data-bs-toggle="modal" data-bs-target="#logDetailsModal" data-log-id="<?php echo $log['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="text-muted">
                    Showing up to <?php echo $limit; ?> most recent logs
                    <?php if (!empty($event_type) || !empty($username) || !empty($ip_address) || !empty($date_from) || !empty($date_to)): ?>
                        with applied filters
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailsModalLabel">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="logDetails">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to get log details
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-details');

        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const logId = this.getAttribute('data-log-id');
                const logDetails = document.getElementById('logDetails');

                // Show loading spinner
                logDetails.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                // Fetch log details
                fetch(`get_log_details.php?id=${logId}`)
                    .then(response => response.text())
                    .then(data => {
                        logDetails.innerHTML = data;
                    })
                    .catch(error => {
                        logDetails.innerHTML = `<div class="alert alert-danger">Error loading log details: ${error}</div>`;
                    });
            });
        });
    });
</script>

<?php
// Function to get event type color
function get_event_type_color($event_type)
{
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

// Include admin footer
include 'includes/footer.php';
?>