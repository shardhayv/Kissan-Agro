<?php
// Include header
include 'includes/header.php';

// Initialize or get the cart from session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Add to cart
    if ($action === 'add' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

        // Get product details
        $product = get_product($product_id);

        if ($product) {
            // Check if product already in cart
            if (isset($_SESSION['cart'][$product_id])) {
                // Update quantity
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                // Add new product to cart
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image' => $product['image']
                ];
            }

            set_success_message('Product added to cart');

            // Prevent redirect loop and ensure cart is displayed properly
            if (isset($_GET['redirect']) && $_GET['redirect'] === 'false') {
                // Handle AJAX requests that don't want a redirect
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
                    exit;
                }
            } else {
                // Direct URL access needs proper redirect to show the cart
                $redirect = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'cart.php') === false ?
                    $_SERVER['HTTP_REFERER'] : 'cart.php';
                redirect($redirect);
                exit;
            }
        } else {
            set_error_message('Product not found');

            // Redirect back to products if product not found
            redirect('products.php');
            exit;
        }
    }

    // Update cart
    if ($action === 'update' && isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;

            if ($quantity > 0) {
                // Update quantity
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                // Remove from cart if quantity is 0
                unset($_SESSION['cart'][$product_id]);
            }
        }

        set_success_message('Cart updated successfully');
        redirect('cart.php');
    }

    // Update single item (for non-JavaScript fallback)
    if ($action === 'update' && isset($_POST['update_single'])) {
        $product_id = (int)$_POST['update_single'];

        if (isset($_POST['quantity'][$product_id])) {
            $quantity = (int)$_POST['quantity'][$product_id];

            if ($quantity > 0) {
                // Update quantity
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                // Remove from cart if quantity is 0
                unset($_SESSION['cart'][$product_id]);
            }

            set_success_message('Item updated successfully');
        }

        redirect('cart.php');
    }

    // Remove from cart
    if ($action === 'remove' && isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];

        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            set_success_message('Product removed from cart');
        }

        redirect('cart.php');
    }

    // Clear cart
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        set_success_message('Cart cleared');
        redirect('cart.php');
    }
}

// Calculate cart totals
$cart_total = 0;
$cart_items = 0;

foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_items += $item['quantity'];
}
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header enhanced-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('cart_header', 'cart-header.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown">Shopping Cart</h1>
            <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">Review and manage your selected products</p>
            <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp animate__delay-1s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>" class="text-white">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Cart</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Cart Update Indicator -->
<div id="cart-update-indicator" class="cart-update-indicator" style="display: none;"></div>

<!-- Enhanced Cart Content -->
<section class="cart-section py-5 my-5">
    <div class="container">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart-container text-center py-5" data-aos="fade-up">
                <div class="empty-cart-icon mb-4">
                    <i class="fas fa-shopping-cart fa-5x text-muted"></i>
                </div>
                <h2 class="mb-3">Your Cart is Empty</h2>
                <p class="lead text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="<?php echo site_url('products.php'); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-box me-2"></i>Browse Our Products
                </a>
            </div>
        <?php else: ?>
            <div class="text-center mb-5" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Your Shopping Cart</h2>
                    <div class="section-subtitle">Review and manage your items</div>
                </div>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">You have <?php echo $cart_items; ?> item<?php echo $cart_items > 1 ? 's' : ''; ?> in your cart. Review your items below before proceeding to checkout.</p>
            </div>
        <?php endif; ?>
        <form action="<?php echo site_url('cart.php?action=update'); ?>" method="post">
            <!-- Desktop Cart View (visible on md and larger screens) -->
            <div class="row">
                <div class="col-lg-8 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                    <div class="cart-table-container bg-white p-4 rounded shadow-sm">
                        <div class="table-responsive">
                            <table class="table cart-table">
                                <thead>
                                    <tr>
                                        <th scope="col" width="50%">Product</th>
                                        <th scope="col" class="text-center">Price</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-center">Subtotal</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <tr class="cart-item" data-aos="fade-up" data-aos-delay="150">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="cart-item-image-container me-3">
                                                        <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['name']; ?>" class="cart-item-image">
                                                    </div>
                                                    <div class="cart-item-details">
                                                        <h5 class="mb-1"><?php echo $item['name']; ?></h5>
                                                        <span class="text-muted small">Product ID: <?php echo $item['id']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="price-amount"><?php echo format_price($item['price']); ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="quantity-control mx-auto">
                                                    <button type="button" class="quantity-btn minus" onclick="decrementQuantity(this)">-</button>
                                                    <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="form-control quantity-input" data-price="<?php echo $item['price']; ?>" data-id="<?php echo $item['id']; ?>" onchange="updateSubtotal(this, <?php echo $item['price']; ?>)">
                                                    <button type="button" class="quantity-btn plus" onclick="incrementQuantity(this)">+</button>
                                                    <noscript>
                                                        <!-- Fallback for when JavaScript is disabled -->
                                                        <button type="submit" name="update_single" value="<?php echo $item['id']; ?>" class="btn btn-sm btn-primary ms-2">Update</button>
                                                    </noscript>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="subtotal-amount fw-bold"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="<?php echo site_url('cart.php?action=remove&id=' . $item['id']); ?>" class="btn btn-sm btn-outline-danger remove-item-btn" data-bs-toggle="tooltip" title="Remove Item">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Desktop Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <a href="<?php echo site_url('products.php'); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                </a>
                            </div>
                            <div>
                                <a href="<?php echo site_url('cart.php?action=clear'); ?>" class="btn btn-outline-danger me-2">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </a>
                                <button type="submit" name="update_cart" class="btn btn-primary">
                                    <i class="fas fa-sync me-2"></i>Update Cart
                                </button>
                                <div id="js-disabled-note" class="mt-2 text-muted small" style="display: none;">
                                    If the plus/minus buttons don't work, please use this Update Cart button.
                                </div>
                                <script>
                                    // Hide the note if JavaScript is enabled
                                    document.getElementById('js-disabled-note').style.display = 'block';
                                </script>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4" data-aos="fade-left" data-aos-delay="200">
                    <div class="cart-summary bg-white p-4 rounded shadow-sm sticky-top" style="top: 20px;">
                        <h3 class="border-bottom pb-3 mb-3">Order Summary</h3>

                        <div class="summary-item d-flex justify-content-between mb-2">
                            <span>Items (<?php echo $cart_items; ?>):</span>
                            <span><?php echo format_price($cart_total); ?></span>
                        </div>

                        <div class="summary-item d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">Free in Mahottari & Dhanusha</span>
                        </div>

                        <div class="summary-item d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span>Estimated Tax:</span>
                            <span>Calculated at checkout</span>
                        </div>

                        <div class="summary-total d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold fs-5 text-primary"><?php echo format_price($cart_total); ?></span>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="<?php echo site_url('order.php'); ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Proceed to Order (Cash on Delivery)
                            </a>
                        </div>

                        <div class="payment-note text-center mt-4">
                            <p class="text-muted small mb-2">Cash on Delivery Only</p>
                            <i class="fas fa-money-bill-wave mx-1 text-muted fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Cart View (visible on smaller screens) -->
            <div class="d-md-none">
                <div class="mobile-cart-items mb-4">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="mobile-cart-item bg-white rounded shadow-sm mb-3" data-aos="fade-up">
                            <div class="cart-item-header p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?php echo $item['name']; ?></h5>
                                <a href="<?php echo site_url('cart.php?action=remove&id=' . $item['id']); ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                            <div class="cart-item-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-4">
                                        <div class="mobile-cart-image-container">
                                            <img src="<?php echo !empty($item['image']) ? upload_url($item['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $item['name']; ?>" class="mobile-cart-image">
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="mobile-cart-details">
                                            <div class="price-info mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Price:</span>
                                                    <span class="fw-bold"><?php echo format_price($item['price']); ?></span>
                                                </div>
                                            </div>

                                            <div class="quantity-info mb-2">
                                                <label for="mobile-quantity-<?php echo $item['id']; ?>" class="form-label text-muted mb-1">Quantity:</label>
                                                <div class="mobile-quantity-control">
                                                    <button type="button" class="quantity-btn minus" onclick="decrementQuantity(this)">-</button>
                                                    <input type="number" id="mobile-quantity-<?php echo $item['id']; ?>" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="form-control quantity-input" data-price="<?php echo $item['price']; ?>" data-id="<?php echo $item['id']; ?>" data-view="mobile" onchange="updateMobileSubtotal(this, <?php echo $item['price']; ?>, <?php echo $item['id']; ?>)">
                                                    <button type="button" class="quantity-btn plus" onclick="incrementQuantity(this)">+</button>
                                                    <noscript>
                                                        <!-- Fallback for when JavaScript is disabled -->
                                                        <button type="submit" name="update_single" value="<?php echo $item['id']; ?>" class="btn btn-sm btn-primary ms-2">Update</button>
                                                    </noscript>
                                                </div>
                                            </div>

                                            <div class="subtotal-info">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted">Subtotal:</span>
                                                    <span class="fw-bold text-primary" id="mobile-subtotal-<?php echo $item['id']; ?>"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Mobile Cart Summary -->
                <div class="mobile-cart-summary bg-white rounded shadow-sm p-3 mb-4" data-aos="fade-up">
                    <h4 class="border-bottom pb-2 mb-3">Order Summary</h4>

                    <div class="summary-item d-flex justify-content-between mb-2">
                        <span>Items (<?php echo $cart_items; ?>):</span>
                        <span><?php echo format_price($cart_total); ?></span>
                    </div>

                    <div class="summary-item d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">Free in Mahottari & Dhanusha</span>
                    </div>

                    <div class="summary-item d-flex justify-content-between mb-3 pb-2 border-bottom">
                        <span>Estimated Tax:</span>
                        <span>At checkout</span>
                    </div>

                    <div class="summary-total d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold fs-5 text-primary"><?php echo format_price($cart_total); ?></span>
                    </div>
                </div>

                <!-- Mobile Action Buttons -->
                <div class="mobile-cart-actions" data-aos="fade-up">
                    <!-- Primary Actions -->
                    <div class="d-flex justify-content-between mb-3">
                        <button type="submit" name="update_cart" class="btn btn-success flex-grow-1 me-2">
                            <i class="fas fa-sync me-2"></i>Update Cart
                        </button>
                        <div id="mobile-js-disabled-note" class="mt-2 text-muted small" style="display: none;">
                            If the plus/minus buttons don't work, please use this Update Cart button.
                        </div>
                        <script>
                            // Show the note if JavaScript is enabled
                            document.getElementById('mobile-js-disabled-note').style.display = 'block';
                        </script>
                        <a href="<?php echo site_url('cart.php?action=clear'); ?>" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>

                    <!-- Secondary Actions -->
                    <div class="d-grid gap-2 mb-3">
                        <a href="<?php echo site_url('order.php'); ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Proceed to Order (COD)
                        </a>
                        <a href="<?php echo site_url('products.php'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>

                    <div class="payment-note text-center mt-4">
                        <p class="text-muted small mb-2">Cash on Delivery Only</p>
                        <i class="fas fa-money-bill-wave mx-1 text-muted fs-4"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Add Animation Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease',
            once: true
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Quantity control functions
    function decrementQuantity(button) {
        const input = button.nextElementSibling;
        let value = parseInt(input.value);
        if (value > 0) {
            input.value = value - 1;

            // Get product ID and price from data attributes
            const price = parseFloat(input.getAttribute('data-price'));
            const productId = input.getAttribute('data-id');

            // Update subtotal
            updateSubtotalFromButton(input, price);

            // Sync with other view (mobile/desktop)
            syncQuantityChange(productId, value - 1, input);

            // Update session data via AJAX
            updateCartSession(productId, value - 1);
        }
    }

    function incrementQuantity(button) {
        const input = button.previousElementSibling;
        let value = parseInt(input.value);
        input.value = value + 1;

        // Get product ID and price from data attributes
        const price = parseFloat(input.getAttribute('data-price'));
        const productId = input.getAttribute('data-id');

        // Update subtotal
        updateSubtotalFromButton(input, price);

        // Sync with other view (mobile/desktop)
        syncQuantityChange(productId, value + 1, input);

        // Update session data via AJAX
        updateCartSession(productId, value + 1);
    }

    // Function to update cart session data via AJAX
    function updateCartSession(productId, quantity) {
        // Create form data
        const formData = new FormData();

        // Ensure productId is a number
        productId = parseInt(productId);
        if (isNaN(productId) || productId <= 0) {
            console.error('Invalid product ID:', productId);
            const updateIndicator = document.getElementById('cart-update-indicator');
            if (updateIndicator) {
                updateIndicator.textContent = 'Error: Invalid product ID';
                updateIndicator.style.display = 'block';
                updateIndicator.className = 'cart-update-indicator error';
                setTimeout(() => {
                    updateIndicator.style.display = 'none';
                }, 3000);
            }
            return;
        }

        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('ajax_update', 'true');

        console.log('Updating cart with product ID:', productId, 'quantity:', quantity);

        // Show loading indicator
        const updateIndicator = document.getElementById('cart-update-indicator');
        if (updateIndicator) {
            updateIndicator.textContent = 'Updating cart...';
            updateIndicator.style.display = 'block';
            updateIndicator.className = 'cart-update-indicator updating';
        }

        // Send AJAX request
        fetch('<?php echo site_url('update_cart.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                console.log('Response data:', data);

                if (data.success) {
                    console.log('Cart updated successfully');
                    if (updateIndicator) {
                        updateIndicator.textContent = 'Cart updated successfully';
                        updateIndicator.className = 'cart-update-indicator success';
                        // Hide after 2 seconds
                        setTimeout(() => {
                            updateIndicator.style.display = 'none';
                        }, 2000);
                    }

                    // Update the cart display without page reload
                    if (data.cart_items !== undefined && data.formatted_total !== undefined) {
                        updateCartDisplay(data.cart_items, data.formatted_total);
                    }
                } else {
                    console.error('Error updating cart:', data.message);
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                    }
                    if (updateIndicator) {
                        updateIndicator.textContent = 'Error updating cart: ' + data.message;
                        updateIndicator.className = 'cart-update-indicator error';
                        // Hide after 3 seconds
                        setTimeout(() => {
                            updateIndicator.style.display = 'none';
                        }, 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart:', error);
                if (updateIndicator) {
                    updateIndicator.textContent = 'Error updating cart. Please try again.';
                    updateIndicator.className = 'cart-update-indicator error';
                    // Hide after 3 seconds
                    setTimeout(() => {
                        updateIndicator.style.display = 'none';
                    }, 3000);
                }
            });

        // Function to update cart display with new totals
        function updateCartDisplay(itemCount, formattedTotal) {
            // Update item count in summary
            const itemCountElements = document.querySelectorAll('.summary-item:first-child .item-label, .summary-item:first-child span:first-child');
            itemCountElements.forEach(el => {
                el.textContent = `Items (${itemCount}):`;
            });

            // Update total amount
            const totalAmountElements = document.querySelectorAll('.summary-total .item-value, .summary-total span:last-child, .summary-item.total .item-value');
            totalAmountElements.forEach(el => {
                el.textContent = formattedTotal;
            });
        }
    }

    // Sync quantity between mobile and desktop views
    function syncQuantityChange(productId, newValue, sourceInput) {
        // Determine if source is mobile or desktop
        const isMobile = sourceInput.hasAttribute('data-view') && sourceInput.getAttribute('data-view') === 'mobile';

        // Find the corresponding input in the other view
        if (isMobile) {
            // Update desktop view
            const desktopInput = document.querySelector(`input[name="quantity[${productId}]"]:not([data-view="mobile"])`);
            if (desktopInput) {
                desktopInput.value = newValue;
                const price = parseFloat(desktopInput.getAttribute('data-price'));
                updateSubtotal(desktopInput, price);
            }
        } else {
            // Update mobile view
            const mobileInput = document.getElementById(`mobile-quantity-${productId}`);
            if (mobileInput) {
                mobileInput.value = newValue;
                const price = parseFloat(mobileInput.getAttribute('data-price'));
                updateMobileSubtotal(mobileInput, price, productId);
            }
        }

        // Update cart summary totals
        updateCartTotals();
    }

    // Update cart totals (items count and total price)
    function updateCartTotals() {
        let totalItems = 0;
        let totalAmount = 0;

        // Get all quantity inputs (use desktop view to avoid counting twice)
        const quantityInputs = document.querySelectorAll('input[name^="quantity["]:not([data-view="mobile"])');

        quantityInputs.forEach(input => {
            const quantity = parseInt(input.value);
            const price = parseFloat(input.getAttribute('data-price'));
            const subtotal = price * quantity;

            totalItems += quantity;
            totalAmount += subtotal;
        });

        // Update summary displays
        const itemCountElements = document.querySelectorAll('.summary-item:first-child .item-label, .summary-item:first-child span:first-child');
        itemCountElements.forEach(el => {
            el.textContent = `Items (${totalItems}):`;
        });

        const totalAmountElements = document.querySelectorAll('.summary-total .item-value, .summary-total span:last-child, .summary-item.total .item-value');
        totalAmountElements.forEach(el => {
            el.textContent = formatPrice(totalAmount);
        });
    }

    function updateSubtotalFromButton(input, price) {
        const quantity = parseInt(input.value);
        const subtotal = price * quantity;
        const productId = input.getAttribute('data-id');

        // For desktop view
        const subtotalElement = input.closest('tr')?.querySelector('.subtotal-amount');
        if (subtotalElement) {
            subtotalElement.textContent = formatPrice(subtotal);
        }

        // For mobile view
        const mobileSubtotalElement = document.getElementById(`mobile-subtotal-${productId}`);
        if (mobileSubtotalElement) {
            mobileSubtotalElement.textContent = formatPrice(subtotal);
        }
    }

    function updateSubtotal(input, price) {
        const quantity = parseInt(input.value);
        const subtotal = price * quantity;
        const productId = input.getAttribute('data-id');
        const subtotalElement = input.closest('tr').querySelector('.subtotal-amount');
        subtotalElement.textContent = formatPrice(subtotal);

        // Update cart totals
        updateCartTotals();

        // Update session data via AJAX
        updateCartSession(productId, quantity);
    }

    function updateMobileSubtotal(input, price, itemId) {
        const quantity = parseInt(input.value);
        const subtotal = price * quantity;
        const subtotalElement = document.getElementById(`mobile-subtotal-${itemId}`);
        subtotalElement.textContent = formatPrice(subtotal);

        // Update cart totals
        updateCartTotals();

        // Update session data via AJAX
        updateCartSession(itemId, quantity);
    }

    function formatPrice(price) {
        return 'Rs. ' + price.toFixed(2);
    }
</script>

<!-- Custom CSS for Cart Page -->
<style>
    /* Cart Update Indicator */
    .cart-update-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .cart-update-indicator.updating {
        background-color: #007bff;
    }

    .cart-update-indicator.success {
        background-color: var(--primary-color);
    }

    .cart-update-indicator.error {
        background-color: var(--danger-color);
    }

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
        margin-bottom: 20px;
        position: relative;
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
        color: var(--primary-color);
        font-weight: 500;
        margin-bottom: 10px;
    }

    /* Empty Cart Styles */
    .empty-cart-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 50px 20px;
    }

    .empty-cart-icon {
        color: var(--medium-color);
        opacity: 0.5;
    }

    /* Cart Table Styles */
    .cart-table-container {
        border: 1px solid rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .cart-table {
        margin-bottom: 0;
    }

    .cart-table thead th {
        background-color: rgba(78, 125, 52, 0.05);
        border-bottom: 2px solid var(--primary-color);
        padding: 15px;
        font-weight: 600;
        color: var(--dark-color);
    }

    .cart-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .cart-table tbody tr:hover {
        background-color: rgba(78, 125, 52, 0.02);
    }

    .cart-table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    .cart-item-image-container {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .cart-item-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .cart-item-image:hover {
        transform: scale(1.05);
    }

    .cart-item-details h5 {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .price-amount {
        font-weight: 600;
        color: var(--dark-color);
    }

    .subtotal-amount {
        color: var(--primary-color);
    }

    .remove-item-btn {
        border-radius: 50%;
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .remove-item-btn:hover {
        background-color: var(--danger-color);
        color: white;
        transform: rotate(90deg);
    }

    /* Quantity Control Styles */
    .quantity-control {
        display: flex;
        align-items: center;
        max-width: 120px;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #ced4da;
    }

    .quantity-btn {
        width: 30px;
        height: 38px;
        background-color: #f8f9fa;
        border: none;
        color: var(--dark-color);
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quantity-btn:hover {
        background-color: var(--primary-light);
        color: white;
    }

    .quantity-input {
        width: 60px;
        text-align: center;
        border: none;
        border-left: 1px solid #ced4da;
        border-right: 1px solid #ced4da;
        border-radius: 0;
        padding: 0.375rem 0.5rem;
    }

    .quantity-input:focus {
        box-shadow: none;
        border-color: #ced4da;
    }

    /* Cart Summary Styles */
    .cart-summary {
        border: 1px solid rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .cart-summary:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .cart-summary h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
    }

    .summary-item {
        font-size: 0.95rem;
    }

    .summary-total {
        font-size: 1.1rem;
    }

    /* Mobile Cart Styles */
    .mobile-cart-item {
        border: 1px solid rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .mobile-cart-item:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .cart-item-header {
        background-color: rgba(78, 125, 52, 0.05);
    }

    .mobile-cart-image-container {
        width: 100%;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .mobile-cart-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .mobile-cart-image:hover {
        transform: scale(1.05);
    }

    .mobile-quantity-control {
        display: flex;
        align-items: center;
        max-width: 100%;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #ced4da;
    }

    .mobile-cart-summary {
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .mobile-cart-summary h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--dark-color);
    }

    /* Mobile Action Buttons */
    .mobile-cart-actions {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .mobile-cart-actions .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .mobile-cart-actions .btn-success {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .mobile-cart-actions .btn-success:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
    }

    .mobile-cart-actions .btn-outline-danger {
        width: 46px;
        height: 46px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-cart-actions .btn-outline-danger:hover {
        transform: rotate(15deg);
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .enhanced-header {
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.2rem;
        }

        .cart-table-container,
        .cart-summary {
            padding: 20px !important;
        }
    }

    @media (max-width: 767px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .section-subtitle {
            font-size: 1rem;
        }

        .mobile-cart-item {
            margin-bottom: 15px;
        }

        .mobile-cart-image-container {
            height: 80px;
        }
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>