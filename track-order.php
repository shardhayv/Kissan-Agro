<?php
// Include header
include 'includes/header.php';

// Initialize variables
$order = null;
$order_items = [];
$error = '';
$success = false;

// Check if order_id and email are provided in the URL (from order confirmation page)
$prefill_order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : '';
$prefill_email = isset($_GET['email']) ? urldecode($_GET['email']) : '';

// Function to check order items table existence
function check_order_items_table($conn)
{
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'order_items'");
    return ($result && mysqli_num_rows($result) > 0);
}

// Function to get order by ID and email
function get_order($conn, $order_id, $email)
{
    // Try with exact match first (standard case)
    $query = "SELECT * FROM orders WHERE id = ? AND customer_email = ?";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, 'is', $order_id, $email);

    if (!mysqli_stmt_execute($stmt)) {
        return null;
    }

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        return null;
    }

    // If we found an order, return it
    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);

        // Fix for the status field - ensure it matches expected values
        if (isset($order['status']) && strpos($order['status'], 'delivere') === 0 && $order['status'] !== 'delivered') {
            $order['status'] = 'delivered';
        }

        return $order;
    }

    // If no exact match, try case-insensitive match
    $query = "SELECT * FROM orders WHERE id = ? AND LOWER(customer_email) = LOWER(?)";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, 'is', $order_id, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);

        // Fix for the status field
        if (isset($order['status']) && strpos($order['status'], 'delivere') === 0 && $order['status'] !== 'delivered') {
            $order['status'] = 'delivered';
        }

        return $order;
    }

    return null;
}

// Function to get order items
function get_order_items($conn, $order_id)
{
    $items = [];

    // First check if order_items table exists
    if (!check_order_items_table($conn)) {
        return $items;
    }

    // Try different queries based on table structure
    $queries = [
        // First try with a LEFT JOIN to products
        "SELECT oi.*, p.name as product_name, p.image 
         FROM order_items oi 
         LEFT JOIN products p ON oi.product_id = p.id 
         WHERE oi.order_id = ?",

        // If that fails, try without linking to products
        "SELECT * FROM order_items WHERE order_id = ?"
    ];

    foreach ($queries as $query) {
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            continue;
        }

        mysqli_stmt_bind_param($stmt, 'i', $order_id);

        if (!mysqli_stmt_execute($stmt)) {
            continue;
        }

        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            continue;
        }

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Add product name if missing but we have the product ID
                if (!isset($row['product_name']) && isset($row['product_id'])) {
                    $row['product_name'] = 'Product #' . $row['product_id'];
                }

                // Add default product name if missing entirely
                if (!isset($row['product_name'])) {
                    $row['product_name'] = 'Product';
                }

                // Ensure proper structure for display
                if (!isset($row['price'])) {
                    $row['price'] = 0;
                }

                if (!isset($row['quantity'])) {
                    $row['quantity'] = 1;
                }

                $items[] = $row;
            }

            // If we found items, break out of the loop
            break;
        }
    }

    return $items;
}

// Auto-submit if both order_id and email are provided in the URL
if (!empty($prefill_order_id) && !empty($prefill_email) && filter_var($prefill_email, FILTER_VALIDATE_EMAIL)) {
    $order = get_order($conn, $prefill_order_id, $prefill_email);

    if ($order) {
        $order_items = get_order_items($conn, $prefill_order_id);
        $success = true;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track_order'])) {
    // Check if all required form fields exist
    if (!isset($_POST['order_id']) || !isset($_POST['email'])) {
        $error = 'Missing required form fields. Please try again.';
    } else {
        $order_id = (int)sanitize($_POST['order_id']);
        $email = sanitize($_POST['email']);

        // Validate input
        if (empty($order_id)) {
            $error = 'Please enter a valid order ID';
        } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address';
        } else {
            // Now attempt to get the order
            $order = get_order($conn, $order_id, $email);

            if ($order) {
                $order_items = get_order_items($conn, $order_id);
                $success = true;
            } else {
                $error = 'No order found with the provided ID and email. Please check your information and try again.';
            }
        }
    }
}
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header enhanced-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('track_order_header', 'track-order-header.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown">Track Your Order</h1>
            <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">Check the status of your order with our easy tracking system</p>
            <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp animate__delay-1s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>" class="text-white">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Track Order</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Track Order Content -->
<section class="track-order-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!$success): ?>
                    <div class="section-title-wrapper text-center mb-5">
                        <h2 class="section-title">Track Your Order</h2>
                        <div class="section-subtitle">Check the status of your recent purchase</div>
                    </div>

                    <!-- Track Order Form -->
                    <div class="card shadow-lg mb-5 border-0 rounded-lg overflow-hidden">
                        <div class="card-header bg-primary text-white py-3">
                            <h4 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-search-location me-2"></i> Order Tracking
                            </h4>
                        </div>
                        <div class="card-body p-4 p-lg-5">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                                    <div><?php echo $error; ?></div>
                                </div>
                            <?php endif; ?>

                            <p class="mb-4 text-muted">Please enter your order ID and the email address you used during checkout to track your order status.</p>

                            <form action="<?php echo site_url('track-order.php'); ?>" method="post" autocomplete="on" id="track-order-form">
                                <div class="mb-4">
                                    <label for="order_id" class="form-label fw-bold">Order ID <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                                        <input type="number" class="form-control form-control-lg" id="order_id" name="order_id" value="<?php echo $prefill_order_id; ?>" placeholder="Enter your order number" autocomplete="off" required>
                                    </div>
                                    <small class="text-muted mt-1 d-block">The order ID was provided in your order confirmation.</small>
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?php echo htmlspecialchars($prefill_email); ?>" placeholder="Enter your email address" autocomplete="email" required>
                                    </div>
                                    <small class="text-muted mt-1 d-block">The email address you used when placing your order.</small>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" name="track_order" value="1" class="btn btn-primary btn-lg" id="track-order-button">
                                        <i class="fas fa-search me-2"></i> Track My Order
                                    </button>
                                </div>
                            </form>

                            <!-- Add simple JavaScript to ensure form submission works -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var form = document.getElementById('track-order-form');
                                    var button = document.getElementById('track-order-button');

                                    // Add click handler to ensure the button click is registered
                                    button.addEventListener('click', function(e) {
                                        console.log('Track order button clicked');
                                        // Make sure form is valid before submitting
                                        if (form.checkValidity()) {
                                            console.log('Form is valid, submitting...');
                                            // Manually add a hidden field to ensure track_order is included
                                            var hiddenField = document.createElement('input');
                                            hiddenField.type = 'hidden';
                                            hiddenField.name = 'track_order';
                                            hiddenField.value = '1';
                                            form.appendChild(hiddenField);

                                            // Submit the form
                                            form.submit();
                                        } else {
                                            console.log('Form validation failed');
                                            // Let the browser handle the validation error display
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card shadow border-0 rounded-lg overflow-hidden">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-question-circle me-2 text-primary"></i> Need Help?
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-3">If you're having trouble tracking your order or have any questions, our customer support team is here to help:</p>
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Phone Support</h6>
                                            <p class="mb-0"><?php echo get_setting('contact_phone', '+977 9800000000'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-icon me-3">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Email Support</h6>
                                            <p class="mb-0"><?php echo get_setting('contact_email', 'info@kissanagrofoods.com'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="section-title-wrapper text-center mb-5">
                        <h2 class="section-title">Order Details</h2>
                        <div class="section-subtitle">Tracking information for your purchase</div>
                    </div>

                    <!-- Order Details -->
                    <div class="card shadow-lg border-0 rounded-lg overflow-hidden mb-5">
                        <div class="card-header bg-success text-white py-3">
                            <h4 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i> Order #<?php echo $order['id']; ?> Found
                            </h4>
                        </div>
                        <div class="card-body p-4 p-lg-5">
                            <div class="order-success-message mb-5">
                                <div class="success-icon mb-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h5 class="mb-2">Order Located Successfully</h5>
                                <p class="text-muted mb-0">We found your order #<?php echo $order['id']; ?>. Here are the details:</p>
                            </div>

                            <div class="row mb-5">
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                    <div class="order-info-card h-100">
                                        <div class="card-icon">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                        <h5 class="card-title">Order Information</h5>
                                        <ul class="order-info-list">
                                            <li>
                                                <div class="info-label">Order ID</div>
                                                <div class="info-value">#<?php echo $order['id']; ?></div>
                                            </li>
                                            <li>
                                                <div class="info-label">Order Date</div>
                                                <div class="info-value"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
                                            </li>
                                            <li>
                                                <div class="info-label">Payment Method</div>
                                                <div class="info-value"><?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></div>
                                            </li>
                                            <li>
                                                <div class="info-label">Payment Status</div>
                                                <div class="info-value">
                                                    <?php
                                                    $payment_status_class = '';
                                                    $payment_status_icon = '';
                                                    switch ($order['payment_status']) {
                                                        case 'pending':
                                                            $payment_status_class = 'status-pending';
                                                            $payment_status_icon = 'fa-clock';
                                                            break;
                                                        case 'completed':
                                                            $payment_status_class = 'status-completed';
                                                            $payment_status_icon = 'fa-check-circle';
                                                            break;
                                                        case 'failed':
                                                            $payment_status_class = 'status-failed';
                                                            $payment_status_icon = 'fa-times-circle';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="status-badge <?php echo $payment_status_class; ?>">
                                                        <i class="fas <?php echo $payment_status_icon; ?> me-1"></i>
                                                        <?php echo ucfirst($order['payment_status']); ?>
                                                    </span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="info-label">Total Amount</div>
                                                <div class="info-value total-amount"><?php echo format_price($order['total_amount']); ?></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="order-info-card h-100">
                                        <div class="card-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <h5 class="card-title">Shipping Information</h5>
                                        <ul class="order-info-list">
                                            <li>
                                                <div class="info-label">Customer Name</div>
                                                <div class="info-value"><?php echo $order['customer_name']; ?></div>
                                            </li>
                                            <li>
                                                <div class="info-label">Email Address</div>
                                                <div class="info-value text-break"><?php echo $order['customer_email']; ?></div>
                                            </li>
                                            <li>
                                                <div class="info-label">Phone Number</div>
                                                <div class="info-value"><?php echo $order['customer_phone']; ?></div>
                                            </li>
                                            <li>
                                                <div class="info-label">Delivery Address</div>
                                                <div class="info-value address">
                                                    <?php echo nl2br($order['customer_address']); ?>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Tracking Status -->
                            <div class="order-tracking-section mb-5">
                                <h4 class="section-subtitle mb-4"><i class="fas fa-truck me-2"></i> Order Status Tracking</h4>
                                <div class="order-tracking-card">
                                    <div class="order-tracking">
                                        <?php
                                        // Define the order statuses and their icons
                                        $statuses = [
                                            'pending' => ['icon' => 'fa-check-circle', 'label' => 'Order Placed', 'description' => 'Your order has been received'],
                                            'processing' => ['icon' => 'fa-box', 'label' => 'Processing', 'description' => 'We are preparing your order'],
                                            'shipped' => ['icon' => 'fa-shipping-fast', 'label' => 'Shipped', 'description' => 'Your order is on the way'],
                                            'delivered' => ['icon' => 'fa-home', 'label' => 'Delivered', 'description' => 'Order has been delivered'],
                                            'cancelled' => ['icon' => 'fa-times-circle', 'label' => 'Cancelled', 'description' => 'Order has been cancelled']
                                        ];

                                        // Determine the current status index
                                        $current_status = $order['status'];
                                        $status_index = array_search($current_status, array_keys($statuses));

                                        // If order is cancelled, handle differently
                                        $is_cancelled = $current_status === 'cancelled';
                                        ?>

                                        <div class="tracking-progress">
                                            <?php foreach ($statuses as $status => $info): ?>
                                                <?php if ($status !== 'cancelled' || $is_cancelled): ?>
                                                    <?php
                                                    // Determine if this status is active, completed, or pending
                                                    $status_class = '';
                                                    $status_date = '';

                                                    if ($is_cancelled && $status === 'cancelled') {
                                                        $status_class = 'active';
                                                        $status_date = date('F j, Y', strtotime($order['updated_at']));
                                                    } elseif (!$is_cancelled) {
                                                        $status_index = array_search($current_status, array_keys($statuses));
                                                        $this_index = array_search($status, array_keys($statuses));

                                                        if ($this_index < $status_index) {
                                                            $status_class = 'completed';
                                                            $status_date = ($status === 'pending') ? date('F j, Y', strtotime($order['created_at'])) : 'Completed';
                                                        } elseif ($this_index === $status_index) {
                                                            $status_class = 'active';
                                                            $status_date = ($status === 'pending') ? date('F j, Y', strtotime($order['created_at'])) : 'In Progress';
                                                        } else {
                                                            $status_class = '';
                                                            $status_date = 'Pending';
                                                        }
                                                    }
                                                    ?>

                                                    <div class="tracking-step <?php echo $status_class; ?>">
                                                        <div class="step-icon">
                                                            <i class="fas <?php echo $info['icon']; ?>"></i>
                                                        </div>
                                                        <div class="step-text">
                                                            <h5><?php echo $info['label']; ?></h5>
                                                            <p class="step-description"><?php echo $info['description']; ?></p>
                                                            <p class="step-date"><?php echo $status_date; ?></p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if ($is_cancelled): ?>
                                            <div class="order-status-message cancelled">
                                                <div class="status-icon">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </div>
                                                <div class="status-text">
                                                    <h5>Order Cancelled</h5>
                                                    <p>This order has been cancelled. If you have any questions, please contact our customer support.</p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="order-status-message <?php echo $current_status; ?>">
                                                <div class="status-icon">
                                                    <i class="fas <?php echo $statuses[$current_status]['icon']; ?>"></i>
                                                </div>
                                                <div class="status-text">
                                                    <h5>Current Status: <?php echo ucfirst($current_status); ?></h5>
                                                    <p><?php echo $statuses[$current_status]['description']; ?>. We'll update the status as it progresses.</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="order-items-section mb-5">
                                <h4 class="section-subtitle mb-4"><i class="fas fa-box-open me-2"></i> Order Items</h4>

                                <?php if (empty($order_items)): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No order items found for this order. Please contact customer support.
                                    </div>
                                <?php else: ?>
                                    <!-- Display order items -->
                                    <!-- Desktop Order Items Table -->
                                    <div class="order-items-table d-none d-md-block">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" width="50%">Product</th>
                                                        <th scope="col" class="text-center">Price</th>
                                                        <th scope="col" class="text-center">Quantity</th>
                                                        <th scope="col" class="text-end">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($order_items as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="product-info d-flex align-items-center">
                                                                    <div class="product-image">
                                                                        <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['product_name']; ?>">
                                                                    </div>
                                                                    <div class="product-details">
                                                                        <h6 class="product-name"><?php echo $item['product_name']; ?></h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center"><?php echo format_price($item['price']); ?></td>
                                                            <td class="text-center">
                                                                <span class="quantity-badge"><?php echo $item['quantity']; ?></span>
                                                            </td>
                                                            <td class="text-end fw-bold"><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="order-total">
                                                        <td colspan="3" class="text-end">Total Amount:</td>
                                                        <td class="text-end total-price"><?php echo format_price($order['total_amount']); ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Mobile Order Items Cards -->
                                    <div class="order-items-mobile d-md-none">
                                        <?php foreach ($order_items as $item): ?>
                                            <div class="order-item-card">
                                                <div class="item-image">
                                                    <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['product_name']; ?>">
                                                </div>
                                                <div class="item-details">
                                                    <h5 class="item-name"><?php echo $item['product_name']; ?></h5>
                                                    <div class="item-meta">
                                                        <div class="meta-group">
                                                            <span class="meta-label">Price:</span>
                                                            <span class="meta-value"><?php echo format_price($item['price']); ?></span>
                                                        </div>
                                                        <div class="meta-group">
                                                            <span class="meta-label">Quantity:</span>
                                                            <span class="meta-value quantity-badge"><?php echo $item['quantity']; ?></span>
                                                        </div>
                                                        <div class="meta-group total">
                                                            <span class="meta-label">Subtotal:</span>
                                                            <span class="meta-value"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <div class="order-total-mobile">
                                            <span class="total-label">Total Amount:</span>
                                            <span class="total-value"><?php echo format_price($order['total_amount']); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="order-actions">
                                <a href="<?php echo site_url('track-order.php'); ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-search me-2"></i> Track Another Order
                                </a>
                                <a href="<?php echo site_url('products.php'); ?>" class="btn btn-primary btn-lg ms-3">
                                    <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS for Track Order Page -->
<style>
    /* Enhanced Header Styles */
    .enhanced-header {
        padding: 100px 0;
        margin-bottom: 0;
        position: relative;
    }

    .enhanced-header:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 70px;
        background: linear-gradient(to right bottom, transparent 49%, #fff 50%);
    }

    /* Section Title Styles */
    .section-title-wrapper {
        margin-bottom: 30px;
        position: relative;
        text-align: center;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background-color: var(--primary-color);
    }

    .section-subtitle {
        font-size: 1.2rem;
        color: var(--medium-color);
        margin-bottom: 0;
    }

    /* Track Order Section */
    .track-order-section {
        background-color: #f8f9fa;
        padding: 80px 0;
    }

    /* Contact Icon */
    .contact-icon {
        width: 50px;
        height: 50px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.2rem;
    }

    /* Order Success Message */
    .order-success-message {
        text-align: center;
        padding: 20px;
        background-color: rgba(46, 204, 113, 0.1);
        border-radius: 10px;
    }

    .success-icon {
        font-size: 3rem;
        color: var(--success-color);
    }

    /* Order Info Cards */
    .order-info-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .order-info-card .card-icon {
        width: 60px;
        height: 60px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .order-info-card .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: var(--dark-color);
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 15px;
    }

    .order-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order-info-list li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .order-info-list li:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--medium-color);
    }

    .info-value {
        font-weight: 500;
        color: var(--dark-color);
        text-align: right;
    }

    .info-value.address {
        margin-top: 5px;
        line-height: 1.6;
    }

    .total-amount {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    /* Status Badges */
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-pending {
        background-color: rgba(243, 156, 18, 0.2);
        color: #f39c12;
    }

    .status-completed {
        background-color: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
    }

    .status-failed {
        background-color: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }

    /* Order Tracking Section */
    .order-tracking-section {
        margin-top: 40px;
    }

    .order-tracking-card {
        background-color: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Tracking Progress */
    .tracking-progress {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 40px 0;
    }

    .tracking-progress:before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .tracking-step {
        position: relative;
        z-index: 2;
        flex: 1;
        text-align: center;
        padding: 0 10px;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: #6c757d;
        font-size: 20px;
        position: relative;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .tracking-step.active .step-icon,
    .tracking-step.completed .step-icon {
        background-color: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .tracking-step.active .step-text h5,
    .tracking-step.completed .step-text h5 {
        color: var(--primary-color);
        font-weight: bold;
    }

    .step-description {
        font-size: 0.85rem;
        color: var(--medium-color);
        margin-bottom: 5px;
    }

    .step-date {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--dark-color);
    }

    /* Order Status Message */
    .order-status-message {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        background-color: rgba(52, 152, 219, 0.1);
    }

    .order-status-message.pending {
        background-color: rgba(243, 156, 18, 0.1);
    }

    .order-status-message.processing {
        background-color: rgba(52, 152, 219, 0.1);
    }

    .order-status-message.shipped {
        background-color: rgba(155, 89, 182, 0.1);
    }

    .order-status-message.delivered {
        background-color: rgba(46, 204, 113, 0.1);
    }

    .order-status-message.cancelled {
        background-color: rgba(231, 76, 60, 0.1);
    }

    .status-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 1.5rem;
    }

    .order-status-message.pending .status-icon {
        background-color: rgba(243, 156, 18, 0.2);
        color: #f39c12;
    }

    .order-status-message.processing .status-icon {
        background-color: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }

    .order-status-message.shipped .status-icon {
        background-color: rgba(155, 89, 182, 0.2);
        color: #9b59b6;
    }

    .order-status-message.delivered .status-icon {
        background-color: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
    }

    .order-status-message.cancelled .status-icon {
        background-color: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }

    .status-text {
        flex: 1;
    }

    .status-text h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .status-text p {
        margin-bottom: 0;
        color: var(--medium-color);
    }

    /* Order Items Section */
    .order-items-section {
        margin-top: 40px;
    }

    /* Product Info in Table */
    .product-info {
        display: flex;
        align-items: center;
    }

    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        margin-right: 15px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-name {
        margin-bottom: 0;
        font-weight: 600;
    }

    /* Quantity Badge */
    .quantity-badge {
        display: inline-block;
        background-color: rgba(78, 125, 52, 0.1);
        color: var(--primary-color);
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        min-width: 40px;
        text-align: center;
    }

    /* Order Total Row */
    .order-total {
        background-color: rgba(0, 0, 0, 0.02);
        font-weight: 700;
    }

    .total-price {
        color: var(--primary-color);
        font-size: 1.1rem;
    }

    /* Mobile Order Items */
    .order-items-mobile {
        margin-top: 20px;
    }

    .order-item-card {
        display: flex;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 15px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .item-image {
        width: 100px;
        height: 100px;
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-details {
        flex: 1;
        padding: 15px;
    }

    .item-name {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .item-meta {
        font-size: 0.9rem;
    }

    .meta-group {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .meta-label {
        color: var(--medium-color);
    }

    .meta-value {
        font-weight: 600;
    }

    .meta-group.total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed rgba(0, 0, 0, 0.1);
    }

    .meta-group.total .meta-label,
    .meta-group.total .meta-value {
        font-weight: 700;
        color: var(--primary-color);
    }

    .order-total-mobile {
        display: flex;
        justify-content: space-between;
        background-color: #fff;
        padding: 15px 20px;
        border-radius: 10px;
        margin-top: 15px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .total-label {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .total-value {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    /* Action Buttons */
    .order-actions {
        margin-top: 40px;
        text-align: center;
    }

    /* For mobile screens */
    @media (max-width: 767.98px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .tracking-progress {
            flex-direction: column;
            margin-left: 20px;
        }

        .tracking-progress:before {
            top: 0;
            left: 25px;
            width: 2px;
            height: 100%;
        }

        .tracking-step {
            display: flex;
            align-items: center;
            text-align: left;
            margin-bottom: 20px;
            padding: 0;
        }

        .step-icon {
            margin: 0 20px 0 0;
        }

        .step-text {
            flex: 1;
        }

        .order-status-message {
            flex-direction: column;
            text-align: center;
        }

        .status-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .order-actions .btn {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        .order-actions .btn:last-child {
            margin-left: 0 !important;
        }
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>