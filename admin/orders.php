<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    set_error_message('You must be logged in to access this page');
    redirect(site_url('admin/index.php'));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    if (!check_csrf_token()) {
        // Error message already set by check_csrf_token()
        redirect('orders.php');
    }

    // Update order status
    if (isset($_POST['update_status'])) {
        $order_id = (int)$_POST['order_id'];
        $status = sanitize($_POST['status']);
        $payment_status = sanitize($_POST['payment_status']);

        $query = "UPDATE orders SET status = '$status', payment_status = '$payment_status', updated_at = NOW() WHERE id = $order_id";

        if (mysqli_query($conn, $query)) {
            set_success_message('Order status updated successfully');

            // Log the status change
            $admin_name = $_SESSION['full_name'];
            $log_query = "INSERT INTO order_logs (order_id, admin_id, admin_name, action, details)
                          VALUES ($order_id, {$_SESSION['user_id']}, '{$admin_name}', 'status_update',
                          'Status changed to \"$status\", Payment status changed to \"$payment_status\"')";
            mysqli_query($conn, $log_query);
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('orders.php?view=' . $order_id);
    }

    // Delete order
    if (isset($_POST['delete_order'])) {
        $order_id = (int)$_POST['order_id'];

        // Delete order items first
        $query = "DELETE FROM order_items WHERE order_id = $order_id";
        mysqli_query($conn, $query);

        // Delete order
        $query = "DELETE FROM orders WHERE id = $order_id";

        if (mysqli_query($conn, $query)) {
            set_success_message('Order deleted successfully');

            // Log the deletion
            $admin_name = $_SESSION['full_name'];
            $log_query = "INSERT INTO order_logs (order_id, admin_id, admin_name, action, details)
                          VALUES ($order_id, {$_SESSION['user_id']}, '{$admin_name}', 'delete',
                          'Order deleted')";
            mysqli_query($conn, $log_query);
        } else {
            set_error_message('Error: ' . mysqli_error($conn));
        }

        redirect('orders.php');
    }
}

// Get order for view
$order = null;
$order_items = [];
$order_logs = [];
if (isset($_GET['view'])) {
    $order_id = (int)$_GET['view'];

    $query = "SELECT * FROM orders WHERE id = $order_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);

        // Get order items
        $query = "SELECT oi.*, p.name as product_name, p.image, p.description, p.category_id, c.name as category_name
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE oi.order_id = $order_id";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $order_items[] = $row;
        }

        // Check if order_logs table exists
        $table_exists = false;
        $query = "SHOW TABLES LIKE 'order_logs'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $table_exists = true;

            // Get order logs
            $query = "SELECT * FROM order_logs WHERE order_id = $order_id ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $order_logs[] = $row;
            }
        }
    } else {
        set_error_message('Order not found');
        redirect('orders.php');
    }
}

// Get all orders with filtering and search
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search_term = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

$query = "SELECT * FROM orders";

$where_clauses = [];

if (!empty($status_filter)) {
    $where_clauses[] = "status = '$status_filter'";
}

if (!empty($search_term)) {
    $where_clauses[] = "(customer_name LIKE '%$search_term%' OR customer_email LIKE '%$search_term%' OR customer_phone LIKE '%$search_term%' OR id LIKE '%$search_term%')";
}

if (!empty($date_from)) {
    $where_clauses[] = "created_at >= '$date_from 00:00:00'";
}

if (!empty($date_to)) {
    $where_clauses[] = "created_at <= '$date_to 23:59:59'";
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo isset($_GET['view']) ? 'Order Details' : 'Manage Orders'; ?>
        </h1>
    </div>

    <?php if (isset($_GET['view'])): ?>
        <!-- View Order -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Order #<?php echo $order['id']; ?></h6>
                <a href="orders.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                        <p><strong>Address:</strong> <?php echo $order['customer_address']; ?></p>
                        <p><strong>IP Address:</strong> <?php echo $order['customer_ip'] ?? 'Not recorded'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <p><strong>Order Date:</strong> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Total Amount:</strong> <?php echo format_price($order['total_amount']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
                        <p>
                            <strong>Order Status:</strong>
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
                        </p>
                        <p>
                            <strong>Payment Status:</strong>
                            <?php
                            $payment_status_class = match ($order['payment_status']) {
                                'pending' => 'bg-warning',
                                'completed' => 'bg-success',
                                'failed' => 'bg-danger',
                                default => ''
                            };
                            ?>
                            <span class="badge <?php echo $payment_status_class; ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="../uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['product_name']; ?>" class="img-thumbnail me-2" style="width: 50px;">
                                                <?php else: ?>
                                                    <img src="../assets/images/product-placeholder.jpg" alt="No Image" class="img-thumbnail me-2" style="width: 50px;">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo $item['product_name']; ?></strong>
                                                    <?php if (!empty($item['description'])): ?>
                                                        <div class="small text-muted"><?php echo substr($item['description'], 0, 50); ?><?php echo strlen($item['description']) > 50 ? '...' : ''; ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $item['category_name'] ?? 'Uncategorized'; ?></td>
                                        <td><?php echo format_price($item['price']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                                        <td>
                                            <a href="products.php?view=<?php echo $item['product_id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View Product
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td colspan="2"><strong><?php echo format_price($order['total_amount']); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Update Order Status</h5>
                    <form action="orders.php" method="post" class="row g-3">
                        <?php csrf_token_field(); ?>
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="col-md-5">
                            <label for="status" class="form-label">Order Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <select class="form-select" id="payment_status" name="payment_status">
                                <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="completed" <?php echo $order['payment_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($order_logs)): ?>
                    <div class="mt-4">
                        <h5>Order History</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Admin</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_logs as $log): ?>
                                        <tr>
                                            <td><?php echo date('d M Y H:i:s', strtotime($log['created_at'])); ?></td>
                                            <td><?php echo $log['admin_name']; ?></td>
                                            <td>
                                                <?php
                                                [$action_class, $action_text] = match ($log['action']) {
                                                    'status_update' => ['bg-info', 'Status Update'],
                                                    'delete' => ['bg-danger', 'Delete'],
                                                    default => ['bg-secondary', ucfirst($log['action'])]
                                                };
                                                ?>
                                                <span class="badge <?php echo $action_class; ?>"><?php echo $action_text; ?></span>
                                            </td>
                                            <td><?php echo $log['details']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <form action="orders.php" method="post" class="d-inline">
                            <?php csrf_token_field(); ?>
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="delete_order" class="btn btn-danger delete-btn">
                                <i class="fas fa-trash"></i> Delete Order
                            </button>
                        </form>

                        <?php if (!empty($order['customer_ip'])): ?>
                            <a href="visitor_logs.php?ip=<?php echo $order['customer_ip']; ?>" class="btn btn-info">
                                <i class="fas fa-search"></i> View Customer Activity
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
            </div>
            <div class="card-body">
                <!-- Enhanced Filter and Search -->
                <div class="mb-4">
                    <form action="orders.php" method="get" class="filter-section">
                        <div class="card mb-3">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">Search & Filter Orders</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Search Term -->
                                    <div class="col-md-6">
                                        <label for="search" class="form-label">Search</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" id="search" name="search" placeholder="Order ID, customer name, email, phone" value="<?php echo $search_term; ?>">
                                        </div>
                                        <small class="form-text text-muted">Search by order ID, customer name, email or phone</small>
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="col-md-6">
                                        <label for="status-filter" class="form-label">Order Status</label>
                                        <select name="status" id="status-filter" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="col-md-6">
                                        <label for="date-from" class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="date-from" name="date_from" value="<?php echo $date_from; ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="date-to" class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="date-to" name="date_to" value="<?php echo $date_to; ?>">
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="col-12 mt-3">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-filter me-1"></i> Apply Filters
                                            </button>
                                            <?php if (!empty($status_filter) || !empty($search_term) || !empty($date_from) || !empty($date_to)): ?>
                                                <a href="orders.php" class="btn btn-secondary">
                                                    <i class="fas fa-undo me-1"></i> Reset Filters
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if (empty($orders)): ?>
                    <p class="text-center">No orders found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered mobile-optimized-table">
                            <thead>
                                <tr>
                                    <th class="priority-1">Order ID</th>
                                    <th class="priority-1">Customer</th>
                                    <th class="priority-2">Total</th>
                                    <th class="priority-3">Date</th>
                                    <th class="priority-2">Status</th>
                                    <th class="priority-3">Payment</th>
                                    <th class="priority-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $ord): ?>
                                    <tr>
                                        <td class="priority-1">#<?php echo $ord['id']; ?></td>
                                        <td class="priority-1"><?php echo $ord['customer_name']; ?></td>
                                        <td class="priority-2"><?php echo format_price($ord['total_amount']); ?></td>
                                        <td class="priority-3"><?php echo date('d M Y', strtotime($ord['created_at'])); ?></td>
                                        <td class="priority-2">
                                            <?php
                                            $status_class = match ($ord['status']) {
                                                'pending' => 'bg-warning',
                                                'processing' => 'bg-info',
                                                'shipped' => 'bg-primary',
                                                'delivered' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                default => ''
                                            };
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($ord['status']); ?></span>
                                        </td>
                                        <td class="priority-3">
                                            <?php
                                            $payment_status_class = match ($ord['payment_status']) {
                                                'pending' => 'bg-warning',
                                                'completed' => 'bg-success',
                                                'failed' => 'bg-danger',
                                                default => ''
                                            };
                                            ?>
                                            <span class="badge <?php echo $payment_status_class; ?>"><?php echo ucfirst($ord['payment_status']); ?></span>
                                        </td>
                                        <td class="priority-1">
                                            <div class="table-actions">
                                                <a href="orders.php?view=<?php echo $ord['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye d-md-none"></i><span class="d-none d-md-inline"><i class="fas fa-eye me-1"></i> View</span>
                                                </a>
                                                <form action="orders.php" method="post" class="d-inline">
                                                    <?php csrf_token_field(); ?>
                                                    <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                                    <button type="submit" name="delete_order" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="fas fa-trash d-md-none"></i><span class="d-none d-md-inline"><i class="fas fa-trash me-1"></i> Delete</span>
                                                    </button>
                                                </form>
                                            </div>
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