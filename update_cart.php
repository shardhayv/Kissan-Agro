<?php
// Prevent PHP errors from being displayed in the output
ini_set('display_errors', 0);
error_reporting(0);

// Set content type to JSON
header('Content-Type: application/json');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // Include database configuration
    require_once 'config/database.php';

    // Include functions
    require_once 'includes/functions.php';
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error initializing: ' . $e->getMessage()
    ]);
    exit;
}

// Get product ID and quantity
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Debug information
$debug_info = [
    'post_data' => $_POST,
    'product_id' => $product_id,
    'quantity' => $quantity,
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_cart' => isset($_SESSION['cart']) ? 'exists' : 'not set'
];

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'debug' => $debug_info
];

// Validate product ID
if ($product_id <= 0) {
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit;
}

// Initialize or get the cart from session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product exists in the database
try {
    $product = null;

    // Use prepared statement to prevent SQL injection
    $query = "SELECT p.*, c.name as category_name FROM products p
              JOIN categories c ON p.category_id = c.id
              WHERE p.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    }

    // Add product info to debug
    $response['debug']['product'] = $product ? 'found' : 'not found';
    $response['debug']['product_id_used'] = $product_id;

    if (!$product) {
        $response['message'] = 'Product not found';
        echo json_encode($response);
        exit;
    }
} catch (Exception $e) {
    $response['message'] = 'Error retrieving product: ' . $e->getMessage();
    $response['debug']['error'] = $e->getMessage();
    echo json_encode($response);
    exit;
}

// Update cart
if ($quantity > 0) {
    // Check if product already in cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Update quantity
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
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

    $response['success'] = true;
    $response['message'] = 'Cart updated successfully';
} else {
    // Remove from cart if quantity is 0
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $response['success'] = true;
        $response['message'] = 'Product removed from cart';
    } else {
        $response['message'] = 'Product not in cart';
    }
}

// Recalculate cart totals
$cart_total = 0;
$cart_items = 0;

foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
    $cart_items += $item['quantity'];
}

// Add cart totals to response
$response['cart_total'] = $cart_total;
$response['cart_items'] = $cart_items;
$response['formatted_total'] = format_price($cart_total);

// Return response
echo json_encode($response);
exit;
