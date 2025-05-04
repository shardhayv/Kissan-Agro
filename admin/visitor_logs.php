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
    // Check CSRF token
    if (!check_csrf_token()) {
        // Error message already set by check_csrf_token()
        redirect('visitor_logs.php');
    }

    // Clear logs
    if (isset($_POST['clear_logs'])) {
        $query = "DELETE FROM visitor_logs";

        if (mysqli_query($conn, $query)) {
            set_success_message('Visitor logs cleared successfully');
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('visitor_logs.php');
    }
}

// Check if viewing a specific IP
$view_ip = isset($_GET['view_ip']) ? sanitize($_GET['view_ip']) : '';
$ip_details = [];

if (!empty($view_ip)) {
    // Get IP details
    $query = "SELECT * FROM visitor_logs WHERE ip_address = '$view_ip' ORDER BY visit_time DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $ip_details = mysqli_fetch_assoc($result);

        // Get visit count
        $query = "SELECT COUNT(*) as visit_count FROM visitor_logs WHERE ip_address = '$view_ip'";
        $result = mysqli_query($conn, $query);
        $ip_details['visit_count'] = mysqli_fetch_assoc($result)['visit_count'];

        // Get first visit
        $query = "SELECT MIN(visit_time) as first_visit FROM visitor_logs WHERE ip_address = '$view_ip'";
        $result = mysqli_query($conn, $query);
        $ip_details['first_visit'] = mysqli_fetch_assoc($result)['first_visit'];

        // Get last visit
        $query = "SELECT MAX(visit_time) as last_visit FROM visitor_logs WHERE ip_address = '$view_ip'";
        $result = mysqli_query($conn, $query);
        $ip_details['last_visit'] = mysqli_fetch_assoc($result)['last_visit'];

        // Get most visited pages
        $query = "SELECT page_url, COUNT(*) as count FROM visitor_logs WHERE ip_address = '$view_ip' GROUP BY page_url ORDER BY count DESC LIMIT 5";
        $result = mysqli_query($conn, $query);
        $ip_details['top_pages'] = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $ip_details['top_pages'][] = $row;
        }

        // Check if this IP has placed orders
        $query = "SELECT COUNT(*) as order_count FROM orders WHERE customer_ip = '$view_ip'";
        $result = mysqli_query($conn, $query);
        $ip_details['order_count'] = mysqli_fetch_assoc($result)['order_count'];

        if ($ip_details['order_count'] > 0) {
            // Get orders
            $query = "SELECT * FROM orders WHERE customer_ip = '$view_ip' ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);
            $ip_details['orders'] = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $ip_details['orders'][] = $row;
            }
        }
    } else {
        set_error_message('IP address not found');
        redirect('visitor_logs.php');
    }
}

// Get filters
$ip_filter = isset($_GET['ip']) ? sanitize($_GET['ip']) : '';
$page_filter = isset($_GET['page']) ? sanitize($_GET['page']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';
$device_filter = isset($_GET['device']) ? sanitize($_GET['device']) : '';

// Build query
$query = "SELECT * FROM visitor_logs";

$where_clauses = [];

if (!empty($ip_filter)) {
    $where_clauses[] = "ip_address LIKE '%$ip_filter%'";
}

if (!empty($page_filter)) {
    $where_clauses[] = "page_url LIKE '%$page_filter%'";
}

if (!empty($date_from)) {
    $where_clauses[] = "visit_time >= '$date_from 00:00:00'";
}

if (!empty($date_to)) {
    $where_clauses[] = "visit_time <= '$date_to 23:59:59'";
}

if (!empty($device_filter)) {
    $where_clauses[] = "device_type = '$device_filter'";
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

// Add order by
$query .= " ORDER BY visit_time DESC";

// Add limit
$limit = 100;
$query .= " LIMIT $limit";

// Execute query
$result = mysqli_query($conn, $query);
$logs = [];

while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = $row;
}

// Get stats
$stats = [
    'total_visits' => 0,
    'unique_visitors' => 0,
    'mobile_visits' => 0,
    'desktop_visits' => 0,
    'tablet_visits' => 0,
    'top_pages' => [],
    'top_browsers' => [],
    'top_ips' => []
];

// Get total visits
$query = "SELECT COUNT(*) as total FROM visitor_logs";
$result = mysqli_query($conn, $query);
$stats['total_visits'] = mysqli_fetch_assoc($result)['total'];

// Get unique visitors
$query = "SELECT COUNT(DISTINCT ip_address) as total FROM visitor_logs";
$result = mysqli_query($conn, $query);
$stats['unique_visitors'] = mysqli_fetch_assoc($result)['total'];

// Get device stats
$query = "SELECT device_type, COUNT(*) as total FROM visitor_logs GROUP BY device_type";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['device_type'] === 'Mobile') {
        $stats['mobile_visits'] = $row['total'];
    } elseif ($row['device_type'] === 'Desktop') {
        $stats['desktop_visits'] = $row['total'];
    } elseif ($row['device_type'] === 'Tablet') {
        $stats['tablet_visits'] = $row['total'];
    }
}

// Get top pages
$query = "SELECT page_url, COUNT(*) as total FROM visitor_logs GROUP BY page_url ORDER BY total DESC LIMIT 5";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $stats['top_pages'][] = $row;
}

// Get top browsers
$query = "SELECT browser, COUNT(*) as total FROM visitor_logs GROUP BY browser ORDER BY total DESC LIMIT 5";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $stats['top_browsers'][] = $row;
}

// Get top IPs
$query = "SELECT ip_address, COUNT(*) as total FROM visitor_logs GROUP BY ip_address ORDER BY total DESC LIMIT 5";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $stats['top_ips'][] = $row;
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo !empty($view_ip) ? "IP Details: $view_ip" : 'Visitor Logs'; ?>
        </h1>
        <?php if (empty($view_ip)): ?>
            <form action="visitor_logs.php" method="post" class="d-inline">
                <?php csrf_token_field(); ?>
                <button type="submit" name="clear_logs" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all visitor logs? This action cannot be undone.');">
                    <i class="fas fa-trash me-2"></i> Clear All Logs
                </button>
            </form>
        <?php else: ?>
            <a href="visitor_logs.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Logs
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($view_ip)): ?>
        <!-- IP Details View -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Visitor Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">IP Address</th>
                                    <td><?php echo $ip_details['ip_address']; ?></td>
                                </tr>
                                <tr>
                                    <th>Browser</th>
                                    <td><?php echo $ip_details['browser']; ?></td>
                                </tr>
                                <tr>
                                    <th>Operating System</th>
                                    <td><?php echo $ip_details['os']; ?></td>
                                </tr>
                                <tr>
                                    <th>Device Type</th>
                                    <td><?php echo $ip_details['device_type']; ?></td>
                                </tr>
                                <tr>
                                    <th>Total Visits</th>
                                    <td><?php echo $ip_details['visit_count']; ?></td>
                                </tr>
                                <tr>
                                    <th>First Visit</th>
                                    <td><?php echo date('d M Y H:i:s', strtotime($ip_details['first_visit'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Last Visit</th>
                                    <td><?php echo date('d M Y H:i:s', strtotime($ip_details['last_visit'])); ?></td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td class="small"><?php echo $ip_details['user_agent']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Most Visited Pages</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Page URL</th>
                                        <th>Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ip_details['top_pages'])): ?>
                                        <tr>
                                            <td colspan="2" class="text-center">No page visits recorded</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ip_details['top_pages'] as $page): ?>
                                            <tr>
                                                <td><?php echo $page['page_url']; ?></td>
                                                <td><?php echo $page['count']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($ip_details['order_count'] > 0): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Orders from this IP (<?php echo $ip_details['order_count']; ?>)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ip_details['orders'] as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo $order['customer_name']; ?></td>
                                        <td><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td><?php echo format_price($order['total_amount']); ?></td>
                                        <td>
                                            <?php
                                            $status_class = match ($order['status']) {
                                                'pending' => 'bg-warning',
                                                'processing' => 'bg-info',
                                                'shipped' => 'bg-primary',
                                                'delivered' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                default => ''
                                            };
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Visit History</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Page URL</th>
                                <th>Referrer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM visitor_logs WHERE ip_address = '$view_ip' ORDER BY visit_time DESC LIMIT 100";
                            $result = mysqli_query($conn, $query);
                            $visits = [];

                            while ($row = mysqli_fetch_assoc($result)) {
                                $visits[] = $row;
                            }

                            if (empty($visits)):
                            ?>
                                <tr>
                                    <td colspan="3" class="text-center">No visit history found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($visits as $visit): ?>
                                    <tr>
                                        <td><?php echo date('d M Y H:i:s', strtotime($visit['visit_time'])); ?></td>
                                        <td><?php echo $visit['page_url']; ?></td>
                                        <td><?php echo $visit['referrer_url'] ?: 'Direct'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php else: ?>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Visits</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_visits']; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Unique Visitors</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['unique_visitors']; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Mobile Visits</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['mobile_visits']; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Desktop Visits</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['desktop_visits']; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-desktop fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Logs</h6>
            </div>
            <div class="card-body">
                <form action="visitor_logs.php" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="ip" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="ip" name="ip" value="<?php echo $ip_filter; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="page" class="form-label">Page URL</label>
                        <input type="text" class="form-control" id="page" name="page" value="<?php echo $page_filter; ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="device" class="form-label">Device Type</label>
                        <select class="form-select" id="device" name="device">
                            <option value="">All Devices</option>
                            <option value="Desktop" <?php echo $device_filter === 'Desktop' ? 'selected' : ''; ?>>Desktop</option>
                            <option value="Mobile" <?php echo $device_filter === 'Mobile' ? 'selected' : ''; ?>>Mobile</option>
                            <option value="Tablet" <?php echo $device_filter === 'Tablet' ? 'selected' : ''; ?>>Tablet</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                        <?php if (!empty($ip_filter) || !empty($page_filter) || !empty($date_from) || !empty($date_to) || !empty($device_filter)): ?>
                            <a href="visitor_logs.php" class="btn btn-secondary">
                                <i class="fas fa-undo me-2"></i> Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row">
            <!-- Top Pages -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Pages</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th>Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['top_pages'] as $page): ?>
                                        <tr>
                                            <td><?php echo $page['page_url']; ?></td>
                                            <td><?php echo $page['total']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Browsers -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Browsers</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Browser</th>
                                        <th>Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['top_browsers'] as $browser): ?>
                                        <tr>
                                            <td><?php echo $browser['browser']; ?></td>
                                            <td><?php echo $browser['total']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top IPs -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top IP Addresses</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>Visits</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['top_ips'] as $ip): ?>
                                        <tr>
                                            <td><?php echo $ip['ip_address']; ?></td>
                                            <td><?php echo $ip['total']; ?></td>
                                            <td>
                                                <a href="visitor_logs.php?view_ip=<?php echo $ip['ip_address']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-search"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Visitor Logs (Last <?php echo $limit; ?> entries)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mobile-optimized-table">
                        <thead>
                            <tr>
                                <th class="priority-1">IP Address</th>
                                <th class="priority-2">Page URL</th>
                                <th class="priority-3">Referrer</th>
                                <th class="priority-2">Browser</th>
                                <th class="priority-3">OS</th>
                                <th class="priority-2">Device</th>
                                <th class="priority-1">Visit Time</th>
                                <th class="priority-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No logs found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="priority-1"><?php echo $log['ip_address']; ?></td>
                                        <td class="priority-2"><?php echo $log['page_url']; ?></td>
                                        <td class="priority-3"><?php echo $log['referrer_url'] ?: 'Direct'; ?></td>
                                        <td class="priority-2"><?php echo $log['browser']; ?></td>
                                        <td class="priority-3"><?php echo $log['os']; ?></td>
                                        <td class="priority-2"><?php echo $log['device_type']; ?></td>
                                        <td class="priority-1"><?php echo date('d M Y H:i:s', strtotime($log['visit_time'])); ?></td>
                                        <td class="priority-1">
                                            <a href="visitor_logs.php?view_ip=<?php echo $log['ip_address']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-search"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>