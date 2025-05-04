<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try/catch block to catch any errors in includes
try {
    // Include header and form resubmission prevention
    include 'includes/header.php';
    include 'includes/prevent_resubmission.php';

    // ENHANCED FIX: Much more robust check for skipping empty cart validation
    $skip_empty_cart_check = false;

    // Check for ANY order completion indicators
    if (
        (isset($_SESSION['order_completed']) && $_SESSION['order_completed'] === true) ||
        (isset($_SESSION['order_completing']) && $_SESSION['order_completing'] === true) ||
        (isset($_SESSION['order_id_completed'])) ||
        (isset($_GET['token']) && isset($_SESSION['order_token']) && $_GET['token'] === $_SESSION['order_token']) ||
        (strpos($_SERVER['REQUEST_URI'], 'order_confirmation.php') !== false)
    ) {
        $skip_empty_cart_check = true;

        // Log for debugging if needed
        error_log('Skipping empty cart check due to order completion indicators');
    }

    // Additional safety check - if we were recently on order_confirmation.php page
    if (isset($_SESSION['order_completed_time'])) {
        // If within the last 30 seconds, don't check cart
        if (time() - $_SESSION['order_completed_time'] < 30) {
            $skip_empty_cart_check = true;
        }
    }

    // Now perform the empty cart check only if not skipping it
    if (!$skip_empty_cart_check && (!isset($_SESSION['cart']) || empty($_SESSION['cart']))) {
        set_error_message('Your cart is empty. Please add products to your cart before checkout.');
        redirect('products.php');
        exit; // Add explicit exit to prevent further execution
    }

    // Calculate cart totals
    $cart_total = 0;
    $cart_items = 0;

    foreach ($_SESSION['cart'] as $item) {
        $cart_total += $item['price'] * $item['quantity'];
        $cart_items += $item['quantity'];
    }

    // Function to process the order
    function process_order_form()
    {
        global $cart_total, $conn;

        // Check if database connection is valid
        if (!$conn) {
            error_log("Database connection failed in order form");
            set_error_message("Unable to process your order due to a database connection issue. Please try again later.");
            return false;
        }

        // Validate form data
        $required_fields = ['customer_name', 'customer_email', 'customer_phone', 'customer_address', 'payment_method'];
        $errors = validate_required_fields($required_fields, $_POST);

        // Additional validation
        if (empty($errors)) {
            // Sanitize input
            $customer_name = sanitize($_POST['customer_name']);
            $customer_email = sanitize($_POST['customer_email']);
            $customer_phone = sanitize($_POST['customer_phone']);
            $customer_address = sanitize($_POST['customer_address']);
            $payment_method = sanitize($_POST['payment_method']);
            $delivery_notes = isset($_POST['delivery_notes']) ? sanitize($_POST['delivery_notes']) : '';

            // Additional email validation
            if (!validate_email($customer_email)) {
                $errors[] = 'Please enter a valid email address';
            }

            // Process order with transaction
            try {
                // Start transaction
                mysqli_begin_transaction($conn);

                // Get client IP
                $client_ip = $_SERVER['REMOTE_ADDR'];

                // Insert order into database
                $query = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, payment_method, total_amount, customer_ip)
                        VALUES ('$customer_name', '$customer_email', '$customer_phone', '$customer_address', '$payment_method', $cart_total, '$client_ip')";

                if (mysqli_query($conn, $query)) {
                    $order_id = mysqli_insert_id($conn);

                    // Insert order items
                    foreach ($_SESSION['cart'] as $item) {
                        $product_id = (int)$item['id'];
                        $quantity = (int)$item['quantity'];
                        $price = (float)$item['price'];

                        $query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                                VALUES ($order_id, $product_id, $quantity, $price)";

                        if (!mysqli_query($conn, $query)) {
                            throw new Exception('Failed to insert order item: ' . mysqli_error($conn));
                        }
                    }

                    // Commit transaction
                    mysqli_commit($conn);

                    // ENHANCED FIX: Set flags BEFORE clearing the cart
                    // Set a flag to indicate we're completing an order
                    $_SESSION['order_completing'] = true;
                    $_SESSION['order_id_completed'] = $order_id;

                    // Generate order token for verification
                    $order_token = md5('order_' . $order_id . '_' . time());
                    $_SESSION['order_token'] = $order_token;

                    // Clear the cart
                    $_SESSION['cart'] = [];

                    // Set success message
                    set_success_message("Your order has been placed successfully! Order #{$order_id}");

                    // Return order ID for redirect
                    return ['id' => $order_id, 'token' => $order_token];
                } else {
                    throw new Exception('Failed to insert order: ' . mysqli_error($conn));
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($conn);
                error_log("Order processing error: " . $e->getMessage());
                set_error_message('Error processing order: ' . $e->getMessage());
                return false;
            }
        } else {
            // Display errors
            foreach ($errors as $error) {
                set_error_message($error);
            }
            return false;
        }
    }

    // Process the form with resubmission prevention
    try {
        // Store a temporary copy of the cart for confirmation page
        if (!empty($_SESSION['cart'])) {
            $_SESSION['temp_cart'] = $_SESSION['cart'];
        }

        // Process the order form
        $order_result = process_form_once('order_form', 'process_order_form', site_url('order.php'));

        // ENHANCED FIX: Improved handling of order result
        if (is_array($order_result) && isset($order_result['id']) && isset($order_result['token'])) {
            $order_id = $order_result['id'];
            $order_token = $order_result['token'];

            // Store order ID in session for confirmation page
            $_SESSION['last_order_id'] = $order_id;
            $_SESSION['order_completed'] = true;

            // Add a timestamp to track when the order was completed
            $_SESSION['order_completed_time'] = time();

            // Direct redirect to confirmation page with token
            redirect("order_confirmation.php?id={$order_id}&token={$order_token}");
            exit; // Ensure script execution stops here
        }
    } catch (Exception $e) {
        error_log("Error in process_form_once for order: " . $e->getMessage());
        set_error_message("An error occurred while processing your order. Please try again.");
    }
} catch (Exception $e) {
    // Log any errors from includes
    error_log("Fatal error in order.php: " . $e->getMessage());
    echo "<div class='alert alert-danger'>The order page encountered an error. Please try again later or contact the administrator.</div>";
    // Try to include a minimal footer if header was loaded
    if (function_exists('site_url')) {
        echo "<p><a href='" . site_url('index.php') . "'>Return to Home</a></p>";
    }
    exit;
}
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header enhanced-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('checkout_header', 'checkout-header.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown">Checkout</h1>
            <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">Complete your order with our secure checkout process</p>
            <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp animate__delay-1s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>" class="text-white">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('cart.php'); ?>" class="text-white">Cart</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Enhanced Checkout Content -->
<section class="checkout-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-title-wrapper">
                <h2 class="section-title">Complete Your Order</h2>
                <div class="section-subtitle">Fill in your details to place your order</div>
            </div>
        </div>

        <div class="row">
            <!-- Order Summary for Mobile (shown at top on small screens) -->
            <div class="col-12 d-md-none mb-4">
                <div class="order-summary-card mb-4">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <h4>Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <span class="item-label">Items (<?php echo $cart_items; ?>):</span>
                            <span class="item-value"><?php echo format_price($cart_total); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="item-label">Shipping:</span>
                            <span class="item-value">Free</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-item total">
                            <span class="item-label">Total:</span>
                            <span class="item-value"><?php echo format_price($cart_total); ?></span>
                        </div>
                    </div>
                </div>

                <div class="d-grid mb-4">
                    <a href="<?php echo site_url('cart.php'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i> Edit Cart
                    </a>
                </div>
            </div>

            <!-- Customer Information Form -->
            <div class="col-lg-8 order-md-1">
                <div class="customer-info-card mb-4">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h4>Customer Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo site_url('order.php'); ?>" method="post" id="checkout-form" autocomplete="on">
                            <input type="hidden" name="form_name" value="order_form">
                            <?php form_token_field('order_form'); ?>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="customer_name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" placeholder="Enter your full name" autocomplete="name" required>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="customer_phone" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control form-control-lg" id="customer_phone" name="customer_phone" placeholder="Enter your phone number" autocomplete="tel" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="customer_email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control form-control-lg" id="customer_email" name="customer_email" placeholder="Enter your email address" autocomplete="email" required>
                                </div>
                                <small class="text-muted mt-1 d-block">We'll send your order confirmation to this email.</small>
                            </div>

                            <div class="mb-4">
                                <label for="customer_address" class="form-label fw-bold">Delivery Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea class="form-control form-control-lg" id="customer_address" name="customer_address" rows="3" placeholder="Enter your complete delivery address" autocomplete="street-address" required></textarea>
                                </div>
                                <small class="text-muted mt-1 d-block">We deliver to all areas in Mahottari and Dhanusha districts.</small>
                            </div>

                            <div class="payment-methods-section mb-4">
                                <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                                <div class="payment-method-option">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash_on_delivery" checked>
                                    <label class="form-check-label payment-label" for="payment_cash">
                                        <div class="payment-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="payment-info">
                                            <span class="payment-title">Cash on Delivery</span>
                                            <span class="payment-description">Pay when you receive your order</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="terms-section mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="<?php echo site_url('terms.php'); ?>" target="_blank">terms and conditions</a>
                                    </label>
                                </div>
                            </div>

                            <div class="checkout-actions">
                                <button type="submit" name="place_order" class="btn btn-primary btn-lg" id="place-order-btn">
                                    <i class="fas fa-check-circle me-2"></i> Place Order
                                </button>
                                <a href="<?php echo site_url('cart.php'); ?>" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Cart
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary for Desktop (shown on right on medium and larger screens) -->
            <div class="col-md-4 order-md-2 d-none d-md-block">
                <div class="order-summary-card mb-4">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <h4>Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <span class="item-label">Items (<?php echo $cart_items; ?>):</span>
                            <span class="item-value"><?php echo format_price($cart_total); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="item-label">Shipping:</span>
                            <span class="item-value">Free</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-item total">
                            <span class="item-label">Total Amount:</span>
                            <span class="item-value"><?php echo format_price($cart_total); ?></span>
                        </div>
                    </div>
                </div>

                <div class="order-items-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h4>Your Order Items</h4>
                    </div>
                    <div class="card-body p-0">
                        <!-- Order Items Table Header -->
                        <div class="order-items-header">
                            <div class="order-header-item product-col">Product</div>
                            <div class="order-header-item price-col">Unit Price</div>
                            <div class="order-header-item qty-col">Quantity</div>
                            <div class="order-header-item subtotal-col">Subtotal</div>
                        </div>
                        <ul class="order-items-list">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <li class="order-item">
                                    <div class="product-info">
                                        <div class="product-image">
                                            <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['name']; ?>">
                                        </div>
                                        <div class="product-details">
                                            <h6 class="product-name"><?php echo $item['name']; ?></h6>
                                            <div class="product-description">
                                                <small class="text-muted">Product ID: <?php echo $item['id']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-price">
                                        <?php echo format_price($item['price']); ?>
                                    </div>
                                    <div class="product-quantity">
                                        <span class="quantity-badge"><?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="product-total">
                                        <?php echo format_price($item['price'] * $item['quantity']); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo site_url('cart.php'); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i> Edit Cart
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Items for Mobile (shown at bottom on small screens) -->
            <div class="col-12 d-md-none mt-4">
                <div class="order-items-mobile-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h4>Your Order Items</h4>
                    </div>
                    <div class="card-body">
                        <div class="order-items-mobile">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="order-item-card">
                                    <div class="item-image">
                                        <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['name']; ?>">
                                    </div>
                                    <div class="item-details">
                                        <h5 class="item-name"><?php echo $item['name']; ?></h5>
                                        <div class="item-id">
                                            <small class="text-muted">Product ID: <?php echo $item['id']; ?></small>
                                        </div>
                                        <div class="item-meta">
                                            <div class="meta-group">
                                                <span class="meta-label">Unit Price:</span>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>



<!-- Custom CSS for Order Page -->
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

    /* Checkout Section */
    .checkout-section {
        background-color: #f8f9fa;
        padding: 80px 0;
    }

    /* Card Styles */
    .customer-info-card,
    .order-summary-card,
    .order-items-card,
    .order-items-mobile-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        align-items: center;
        padding: 20px 25px;
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .header-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-right: 15px;
    }

    .card-header h4 {
        margin-bottom: 0;
        font-weight: 700;
        color: var(--dark-color);
    }

    .card-body {
        padding: 25px;
    }

    .card-footer {
        padding: 15px 25px;
        background-color: #f8f9fa;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Order Summary Styles */
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .item-label {
        color: var(--medium-color);
        font-weight: 500;
    }

    .item-value {
        font-weight: 600;
        color: var(--dark-color);
    }

    .summary-divider {
        height: 1px;
        background-color: rgba(0, 0, 0, 0.1);
        margin: 15px 0;
    }

    .summary-item.total {
        margin-top: 15px;
    }

    .summary-item.total .item-label,
    .summary-item.total .item-value {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    /* Form Styles */
    .form-label {
        margin-bottom: 8px;
        color: var(--dark-color);
    }

    .input-group-text {
        border: none;
        color: var(--primary-color);
    }

    .form-control {
        border: 1px solid rgba(0, 0, 0, 0.1);
        padding: 12px 15px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 125, 52, 0.25);
    }

    /* Payment Method Styles */
    .payment-methods-section {
        margin-top: 20px;
    }

    .payment-method-option {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method-option:hover {
        border-color: var(--primary-color);
        background-color: rgba(78, 125, 52, 0.05);
    }

    .payment-label {
        display: flex;
        align-items: center;
        margin-left: 10px;
        cursor: pointer;
        width: 100%;
    }

    .payment-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        margin-right: 15px;
    }

    .payment-info {
        flex: 1;
    }

    .payment-title {
        display: block;
        font-weight: 600;
        color: var(--dark-color);
    }

    .payment-description {
        display: block;
        font-size: 0.85rem;
        color: var (--medium-color);
    }

    /* Terms Section */
    .terms-section {
        margin-top: 20px;
    }

    /* Checkout Actions */
    .checkout-actions {
        margin-top: 30px;
        display: flex;
        align-items: center;
    }

    /* Order Items Styles */
    .order-items-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 25px;
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        font-weight: 600;
        color: var(--dark-color);
    }

    .order-header-item {
        text-align: center;
    }

    .order-header-item.product-col {
        flex: 2;
        text-align: left;
    }

    .order-header-item.price-col,
    .order-header-item.qty-col,
    .order-header-item.subtotal-col {
        flex: 1;
    }

    .order-items-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .product-info {
        display: flex;
        align-items: center;
        flex: 2;
    }

    .product-price,
    .product-quantity,
    .product-total {
        flex: 1;
        text-align: center;
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
        margin-bottom: 5px;
        font-weight: 600;
    }

    .product-description {
        margin-top: 5px;
        font-size: 0.85rem;
    }

    .product-meta {
        display: flex;
        align-items: center;
    }

    .quantity-badge {
        display: inline-block;
        background-color: rgba(78, 125, 52, 0.1);
        color: var(--primary-color);
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        margin-right: 10px;
    }

    .price {
        color: var(--medium-color);
        font-size: 0.9rem;
    }

    .product-price {
        font-weight: 600;
        color: var (--medium-color);
    }

    .product-total {
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Mobile Order Items */
    .order-items-mobile {
        margin-top: 10px;
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

    .item-id {
        margin-bottom: 10px;
        font-size: 0.85rem;
    }

    .item-meta {
        font-size: 0.9rem;
        margin-top: 10px;
        padding: 10px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .meta-group {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px dashed rgba(0, 0, 0, 0.05);
    }

    .meta-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .meta-label {
        color: var(--medium-color);
        font-weight: 500;
    }

    .meta-value {
        font-weight: 600;
    }

    .meta-group.total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed rgba(0, 0, 0, 0.1);
        border-bottom: none;
    }

    .meta-group.total .meta-label,
    .meta-group.total .meta-value {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.05rem;
    }

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .order-items-header {
            display: none;
            /* Hide the header on smaller screens */
        }

        .order-item {
            flex-wrap: wrap;
            padding: 15px;
        }

        .product-info {
            flex: 0 0 100%;
            margin-bottom: 15px;
        }

        .product-price,
        .product-quantity,
        .product-total {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product-price:before {
            content: 'Price';
            font-size: 0.8rem;
            color: var(--medium-color);
            margin-bottom: 5px;
        }

        .product-quantity:before {
            content: 'Qty';
            font-size: 0.8rem;
            color: var(--medium-color);
            margin-bottom: 5px;
        }

        .product-total:before {
            content: 'Subtotal';
            font-size: 0.8rem;
            color: var(--medium-color);
            margin-bottom: 5px;
        }
    }

    @media (max-width: 767.98px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .checkout-section {
            padding: 40px 0;
        }

        .card-header {
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .checkout-actions {
            flex-direction: column;
        }

        .checkout-actions .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .checkout-actions .btn:last-child {
            margin-left: 0 !important;
        }
    }
</style>

<!-- Add a simple script to handle the place order button -->
<script>
    // Check if we're coming from a completed order to prevent flicker
    if (sessionStorage.getItem('orderJustCompleted') === 'true' ||
        window.location.href.indexOf('order_confirmation.php') !== -1) {

        // Prevent any error messages from displaying temporarily
        document.addEventListener('DOMContentLoaded', function() {
            // Hide any error messages that might flash
            const errorMessages = document.querySelectorAll('.alert-danger, .error-message');
            errorMessages.forEach(function(msg) {
                msg.style.display = 'none';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const orderForm = document.getElementById('checkout-form');
        const orderButton = document.getElementById('place-order-btn');

        if (orderForm && orderButton) {
            orderForm.addEventListener('submit', function(e) {
                // Prevent double-clicking
                orderButton.disabled = true;
                orderButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing Order...';

                // Set flag in sessionStorage (persists across page loads)
                sessionStorage.setItem('orderJustCompleted', 'true');

                // Don't prevent the form from submitting
                // Let the form submission and server-side handling occur naturally
            });

            // Check if we're returning from a failed submission
            if (sessionStorage.getItem('orderAttempted') === 'true') {
                // Clear the flag
                sessionStorage.removeItem('orderAttempted');
            }
        }
    });
</script>

<?php
// Include footer
include 'includes/footer.php';
?>