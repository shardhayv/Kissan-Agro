<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    set_error_message('You must be logged in to access this page');
    redirect(site_url('admin/index.php'));
}

// Get counts for dashboard
$query = "SELECT COUNT(*) as product_count FROM products";
$result = mysqli_query($conn, $query);
$product_count = mysqli_fetch_assoc($result)['product_count'];

$query = "SELECT COUNT(*) as inquiry_count FROM inquiries WHERE status = 'new'";
$result = mysqli_query($conn, $query);
$inquiry_count = mysqli_fetch_assoc($result)['inquiry_count'];

$query = "SELECT COUNT(*) as user_count FROM users";
$result = mysqli_query($conn, $query);
$user_count = mysqli_fetch_assoc($result)['user_count'];

$query = "SELECT COUNT(*) as order_count FROM orders WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
$order_count = mysqli_fetch_assoc($result)['order_count'];

// Get visitor stats
$visitor_stats = [
    'today' => 0,
    'yesterday' => 0,
    'this_week' => 0,
    'this_month' => 0,
    'total' => 0,
    'unique' => 0
];

// Check if visitor_logs table exists
$table_exists = false;
$query = "SHOW TABLES LIKE 'visitor_logs'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    $table_exists = true;

    // Today's visits
    $query = "SELECT COUNT(*) as count FROM visitor_logs WHERE DATE(visit_time) = CURDATE()";
    $result = mysqli_query($conn, $query);
    $visitor_stats['today'] = mysqli_fetch_assoc($result)['count'];

    // Yesterday's visits
    $query = "SELECT COUNT(*) as count FROM visitor_logs WHERE DATE(visit_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    $result = mysqli_query($conn, $query);
    $visitor_stats['yesterday'] = mysqli_fetch_assoc($result)['count'];

    // This week's visits
    $query = "SELECT COUNT(*) as count FROM visitor_logs WHERE YEARWEEK(visit_time) = YEARWEEK(NOW())";
    $result = mysqli_query($conn, $query);
    $visitor_stats['this_week'] = mysqli_fetch_assoc($result)['count'];

    // This month's visits
    $query = "SELECT COUNT(*) as count FROM visitor_logs WHERE MONTH(visit_time) = MONTH(NOW()) AND YEAR(visit_time) = YEAR(NOW())";
    $result = mysqli_query($conn, $query);
    $visitor_stats['this_month'] = mysqli_fetch_assoc($result)['count'];

    // Total visits
    $query = "SELECT COUNT(*) as count FROM visitor_logs";
    $result = mysqli_query($conn, $query);
    $visitor_stats['total'] = mysqli_fetch_assoc($result)['count'];

    // Unique visitors
    $query = "SELECT COUNT(DISTINCT ip_address) as count FROM visitor_logs";
    $result = mysqli_query($conn, $query);
    $visitor_stats['unique'] = mysqli_fetch_assoc($result)['count'];
}

// Get recent inquiries
$query = "SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$recent_inquiries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recent_inquiries[] = $row;
}

// Get recent orders
$query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$recent_orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recent_orders[] = $row;
}

// Include admin header
include 'includes/header.php';
?>

<!-- Dashboard Content -->
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Dashboard Cards -->
    <div class="row">
        <!-- Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $product_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="products.php" class="text-primary">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- New Inquiries Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">New Inquiries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $inquiry_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="inquiries.php" class="text-success">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $user_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="users.php" class="text-info">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $order_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="orders.php?status=pending" class="text-warning">View Pending Orders <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                    <a href="orders.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_orders)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent orders.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th class="d-none d-md-table-cell">Total</th>
                                        <th class="d-none d-md-table-cell">Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo $order['customer_name']; ?></td>
                                            <td class="d-none d-md-table-cell"><?php echo format_price($order['total_amount']); ?></td>
                                            <td class="d-none d-md-table-cell"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch ($order['status']) {
                                                    case 'pending':
                                                        $status_class = 'bg-warning';
                                                        break;
                                                    case 'processing':
                                                        $status_class = 'bg-info';
                                                        break;
                                                    case 'shipped':
                                                        $status_class = 'bg-primary';
                                                        break;
                                                    case 'delivered':
                                                        $status_class = 'bg-success';
                                                        break;
                                                    case 'cancelled':
                                                        $status_class = 'bg-danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                                            </td>
                                            <td>
                                                <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye d-md-none"></i><span class="d-none d-md-inline"><i class="fas fa-eye me-1"></i> View</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Inquiries -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Inquiries</h6>
                    <a href="inquiries.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_inquiries)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No recent inquiries.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th class="d-none d-md-table-cell">Email</th>
                                        <th class="d-none d-md-table-cell">Subject</th>
                                        <th class="d-none d-md-table-cell">Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_inquiries as $inquiry): ?>
                                        <tr>
                                            <td><?php echo $inquiry['name']; ?></td>
                                            <td class="d-none d-md-table-cell"><?php echo $inquiry['email']; ?></td>
                                            <td class="d-none d-md-table-cell"><?php echo $inquiry['subject']; ?></td>
                                            <td class="d-none d-md-table-cell"><?php echo date('d M Y', strtotime($inquiry['created_at'])); ?></td>
                                            <td>
                                                <?php if ($inquiry['status'] === 'new'): ?>
                                                    <span class="badge bg-danger">New</span>
                                                <?php elseif ($inquiry['status'] === 'in_progress'): ?>
                                                    <span class="badge bg-warning">In Progress</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Resolved</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="inquiries.php?view=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye d-md-none"></i><span class="d-none d-md-inline"><i class="fas fa-eye me-1"></i> View</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Visitor Stats -->
    <?php if ($table_exists): ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Visitor Statistics</h6>
                        <a href="visitor_logs.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-chart-line me-1"></i> View Detailed Stats
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Visits</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $visitor_stats['today']; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">This Week</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $visitor_stats['this_week']; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $visitor_stats['this_month']; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Visits</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $visitor_stats['total']; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-danger shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Unique Visitors</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $visitor_stats['unique']; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-dark shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Yesterday</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $visitor_stats['yesterday']; ?></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-history fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Links</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="products.php?action=add" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-plus me-2"></i> <span class="d-none d-sm-inline">Add New</span> Product
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="categories.php" class="btn btn-secondary btn-block w-100">
                                <i class="fas fa-tags me-2"></i> <span class="d-none d-sm-inline">Manage</span> Categories
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="orders.php" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-shopping-cart me-2"></i> <span class="d-none d-sm-inline">Manage</span> Orders
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="users.php?action=add" class="btn btn-info btn-block w-100">
                                <i class="fas fa-user-plus me-2"></i> <span class="d-none d-sm-inline">Add New</span> User
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="settings.php" class="btn btn-dark btn-block w-100">
                                <i class="fas fa-cog me-2"></i> <span class="d-none d-sm-inline">Site</span> Settings
                            </a>
                        </div>
                        <?php if ($table_exists): ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <a href="visitor_logs.php" class="btn btn-danger btn-block w-100">
                                    <i class="fas fa-chart-line me-2"></i> <span class="d-none d-sm-inline">Visitor</span> Logs
                                </a>
                            </div>
                        <?php endif; ?>
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