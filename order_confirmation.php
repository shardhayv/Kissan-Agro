<?php
// Include required files
include 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_error_message('Invalid order ID. Please check your order ID and try again.');
    redirect('index.php');
    exit;
}

$order_id = (int)$_GET['id'];

// ENHANCED FIX: Verify order token if available
$token_valid = false;
if (isset($_GET['token']) && isset($_SESSION['order_token']) && $_GET['token'] === $_SESSION['order_token']) {
    $token_valid = true;
}

// Get order details from database
$query = "SELECT o.*, GROUP_CONCAT(p.name SEPARATOR ', ') as product_names 
          FROM orders o 
          LEFT JOIN order_items oi ON o.id = oi.order_id 
          LEFT JOIN products p ON oi.product_id = p.id 
          WHERE o.id = $order_id 
          GROUP BY o.id";

$result = mysqli_query($conn, $query);

// Check if order exists
if (!$result || mysqli_num_rows($result) === 0) {
    // Only show error if we don't have a valid token (prevents showing error during redirects)
    if (!$token_valid && !isset($_SESSION['order_completed']) && !isset($_SESSION['order_id_completed'])) {
        set_error_message('Order not found. Please check your order ID and try again.');
        redirect('track-order.php');
        exit;
    }
}

// Get order data
$order = mysqli_fetch_assoc($result);

// Get order items
$query = "SELECT oi.*, p.name, p.image FROM order_items oi
          JOIN products p ON oi.product_id = p.id
          WHERE oi.order_id = $order_id";

$items_result = mysqli_query($conn, $query);
$order_items = [];

while ($item = mysqli_fetch_assoc($items_result)) {
    $order_items[] = $item;
}

// ENHANCED FIX: Clear all the order completion flags after successful loading
if (isset($_SESSION['order_completed'])) unset($_SESSION['order_completed']);
if (isset($_SESSION['order_completing'])) unset($_SESSION['order_completing']);
if (isset($_SESSION['order_id_completed'])) unset($_SESSION['order_id_completed']);
if (isset($_SESSION['order_token'])) unset($_SESSION['order_token']);

// Only keep the timestamp for a bit longer to prevent issues with refreshes
if (isset($_SESSION['order_completed_time'])) {
    // Keep this for 30 seconds then remove
    if (time() - $_SESSION['order_completed_time'] > 30) {
        unset($_SESSION['order_completed_time']);
    }
}
?>

<div class="page-header enhanced-header success-header">
    <div class="header-overlay">
        <div class="container">
            <div class="text-center">
                <div class="success-checkmark">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">Order Confirmed!</h1>
                <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s">Thank you for your purchase. Your order has been successfully placed.</p>
                <div class="order-number animate__animated animate__fadeInUp animate__delay-1s">
                    <span>Order #</span>
                    <strong><?php echo $order_id; ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="order-confirmation-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-title-wrapper">
                <h2 class="section-title">Your Order Details</h2>
                <div class="section-subtitle">We've sent a confirmation to your email</div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="confirmation-card mb-4">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h4>Order Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="order-info-grid">
                            <div class="info-item">
                                <span class="info-label">Order Number:</span>
                                <span class="info-value">#<?php echo $order_id; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Order Date:</span>
                                <span class="info-value"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Payment Method:</span>
                                <span class="info-value"><i class="fas fa-money-bill-wave text-success me-2"></i> <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Order Status:</span>
                                <span class="info-value">
                                    <span class="status-badge status-processing">Processing</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="confirmation-card mb-4">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4>Customer Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info-block">
                                    <h5><i class="fas fa-user-circle me-2"></i> Account Details</h5>
                                    <p class="customer-name"><?php echo $order['customer_name']; ?></p>
                                    <p class="customer-email"><i class="fas fa-envelope text-muted me-2"></i> <?php echo $order['customer_email']; ?></p>
                                    <p class="customer-phone"><i class="fas fa-phone text-muted me-2"></i> <?php echo $order['customer_phone']; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="customer-info-block">
                                    <h5><i class="fas fa-shipping-fast me-2"></i> Delivery Information</h5>
                                    <p class="delivery-address">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        <?php echo nl2br($order['customer_address']); ?>
                                    </p>
                                    <p class="delivery-expectation">
                                        <i class="fas fa-calendar-alt text-muted me-2"></i>
                                        Expected delivery: <?php echo date('F j, Y', strtotime('+3 days', strtotime($order['created_at']))); ?> - <?php echo date('F j, Y', strtotime('+5 days', strtotime($order['created_at']))); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="confirmation-card mb-4">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h4>Order Summary</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="order-items-list">
                            <div class="order-items-header">
                                <div class="item-col product-col">Product</div>
                                <div class="item-col price-col">Price</div>
                                <div class="item-col qty-col">Qty</div>
                                <div class="item-col total-col">Total</div>
                            </div>
                            <div class="order-items-body">
                                <?php foreach ($order_items as $item): ?>
                                    <div class="order-item">
                                        <div class="item-data product-data">
                                            <div class="product-image">
                                                <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['name']; ?>">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name"><?php echo $item['name']; ?></h6>
                                                <div class="product-meta">
                                                    <span class="product-id">ID: <?php echo $item['product_id']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-data price-data">
                                            <?php echo format_price($item['price']); ?>
                                        </div>
                                        <div class="item-data qty-data">
                                            <span class="qty-badge"><?php echo $item['quantity']; ?></span>
                                        </div>
                                        <div class="item-data total-data">
                                            <?php echo format_price($item['price'] * $item['quantity']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="order-items-footer">
                                <div class="order-summary">
                                    <div class="summary-row">
                                        <span class="summary-label">Subtotal:</span>
                                        <span class="summary-value"><?php echo format_price($order['total_amount']); ?></span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Shipping:</span>
                                        <span class="summary-value">Free</span>
                                    </div>
                                    <div class="summary-row total">
                                        <span class="summary-label">Total:</span>
                                        <span class="summary-value"><?php echo format_price($order['total_amount']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="confirmation-actions">
                    <a href="<?php echo site_url('index.php'); ?>" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i> Back to Homepage
                    </a>
                    <a href="<?php echo site_url('products.php'); ?>" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-shopping-cart me-2"></i> Continue Shopping
                    </a>
                </div>

                <div class="text-center mt-5">
                    <div class="track-order-cta">
                        <p>You can track your order status anytime using our order tracking feature.</p>
                        <a href="<?php echo site_url('track-order.php?id=' . $order_id); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-truck me-2"></i> Track Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Success Header Styles */
    .success-header {
        background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('confirmation_header', 'confirmation-header.jpg'); ?>') center/cover no-repeat;
        padding: 100px 0 120px;
        margin-bottom: -50px;
        position: relative;
    }

    .success-checkmark {
        width: 100px;
        height: 100px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        color: #4CAF50;
        font-size: 50px;
        box-shadow: 0 0 0 10px rgba(76, 175, 80, 0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(76, 175, 80, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
        }
    }

    .order-number {
        display: inline-block;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        padding: 8px 20px;
        color: #fff;
    }

    .order-number span {
        font-size: 1.1rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.8);
        margin-right: 5px;
    }

    .order-number strong {
        font-size: 1.3rem;
        font-weight: 700;
    }

    /* Section Styles */
    .order-confirmation-section {
        background-color: #f8f9fa;
        padding: 80px 0;
        position: relative;
        z-index: 1;
    }

    .section-title-wrapper {
        margin-bottom: 30px;
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

    /* Card Styles */
    .confirmation-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 25px;
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

    /* Order Information Grid */
    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.9rem;
        color: var(--medium-color);
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-processing {
        background-color: rgba(255, 193, 7, 0.1);
        color: #FFC107;
    }

    /* Customer Information Styles */
    .customer-info-block {
        margin-bottom: 20px;
    }

    .customer-info-block h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--dark-color);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding-bottom: 10px;
    }

    .customer-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--dark-color);
    }

    .customer-email,
    .customer-phone,
    .delivery-address,
    .delivery-expectation {
        margin-bottom: 10px;
        color: var(--medium-color);
    }

    /* Order Items Styles */
    .order-items-header {
        display: grid;
        grid-template-columns: 3fr 1fr 1fr 1fr;
        padding: 12px 25px;
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        font-weight: 600;
        color: var(--dark-color);
    }

    .order-item {
        display: grid;
        grid-template-columns: 3fr 1fr 1fr 1fr;
        padding: 15px 25px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        align-items: center;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .product-data {
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
        margin-bottom: 5px;
        font-weight: 600;
    }

    .product-meta {
        font-size: 0.85rem;
        color: var(--medium-color);
    }

    .price-data,
    .qty-data,
    .total-data {
        text-align: center;
    }

    .price-data,
    .qty-data {
        color: var(--medium-color);
    }

    .total-data {
        font-weight: 700;
        color: var(--dark-color);
    }

    .qty-badge {
        display: inline-block;
        background-color: rgba(78, 125, 52, 0.1);
        color: var(--primary-color);
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
    }

    /* Order Summary */
    .order-items-footer {
        padding: 20px 25px;
        background-color: #f8f9fa;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .order-summary {
        margin-left: auto;
        width: 300px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
    }

    .summary-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .summary-label {
        color: var(--medium-color);
    }

    .summary-value {
        font-weight: 600;
        color: var(--dark-color);
    }

    .summary-row.total {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        border-bottom: none;
    }

    .summary-row.total .summary-label,
    .summary-row.total .summary-value {
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    /* Confirmation Actions */
    .confirmation-actions {
        text-align: center;
        margin-top: 40px;
    }

    .confirmation-actions .btn {
        padding: 12px 25px;
        font-weight: 600;
    }

    /* Track Order CTA */
    .track-order-cta {
        margin-top: 60px;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .track-order-cta p {
        margin-bottom: 15px;
        color: var(--medium-color);
    }

    /* Responsive Styles */
    @media (max-width: 991.98px) {
        .success-header {
            padding: 80px 0 100px;
            margin-bottom: -40px;
        }

        .order-confirmation-section {
            padding: 60px 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .order-info-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }

    @media (max-width: 767.98px) {
        .success-header {
            padding: 60px 0 80px;
        }

        .success-checkmark {
            width: 80px;
            height: 80px;
            font-size: 40px;
            margin-bottom: 20px;
        }

        .order-confirmation-section {
            padding: 40px 0;
        }

        .card-header {
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .order-items-header {
            display: none;
        }

        .order-item {
            grid-template-columns: 1fr;
            gap: 15px;
            padding: 15px;
        }

        .price-data,
        .qty-data,
        .total-data {
            display: flex;
            justify-content: space-between;
            text-align: left;
            padding-left: 75px;
        }

        .price-data:before {
            content: 'Price:';
            font-weight: 600;
            color: var(--dark-color);
        }

        .qty-data:before {
            content: 'Quantity:';
            font-weight: 600;
            color: var(--dark-color);
        }

        .total-data:before {
            content: 'Subtotal:';
            font-weight: 600;
            color: var(--dark-color);
        }

        .order-summary {
            width: 100%;
        }

        .confirmation-actions {
            flex-direction: column;
        }

        .confirmation-actions .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .confirmation-actions .btn:last-child {
            margin-left: 0 !important;
        }
    }
</style>

<!-- Client-side session handling script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clear the orderJustCompleted flag after successful load of confirmation page
        sessionStorage.removeItem('orderJustCompleted');
    });
</script>

<?php include 'includes/footer.php'; ?>