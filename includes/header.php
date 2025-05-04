<?php
// Include environment configuration first
require_once __DIR__ . '/../config/environment.php';

// Include database connection, common functions, and session security
require_once 'functions.php';
require_once __DIR__ . '/../config/database.php';
require_once 'session_security.php';

// Get site title from settings
$site_title = get_setting('site_title', 'Kissan Agro Foods');
$site_description = get_setting('site_description', 'Quality wheat flour and puffed rice products');

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Track visitor
track_visitor();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    // Get current page for dynamic meta tags
    $current_page_name = ucwords(str_replace('_', ' ', basename($_SERVER['PHP_SELF'], '.php')));
    $page_title = $site_title . ' | ' . $current_page_name;

    // Set page-specific descriptions
    $page_description = $site_description;
    if ($current_page === 'about.php') {
        $page_description = "Learn about Kissan Agro Foods, a leading wheat flour and puffed rice mill in Khairba, Mahottari, Nepal. Discover our story, mission, and commitment to quality.";
    } elseif ($current_page === 'products.php') {
        $page_description = "Explore our range of high-quality wheat flour and puffed rice products. Premium quality products delivered across Mahottari and Dhanusha districts in Nepal.";
    } elseif ($current_page === 'contact.php') {
        $page_description = "Contact Kissan Agro Foods for inquiries about our wheat flour and puffed rice products. Located in Khairba, Mahottari, Nepal.";
    } elseif ($current_page === 'cart.php') {
        $page_description = "View your shopping cart at Kissan Agro Foods. Order premium wheat flour and puffed rice products for delivery in Mahottari and Dhanusha districts.";
    } elseif ($current_page === 'track-order.php') {
        $page_description = "Track your order from Kissan Agro Foods. Monitor the delivery status of your wheat flour and puffed rice products.";
    }

    // Get canonical URL using our environment-aware function
    $request_uri = $_SERVER['REQUEST_URI'];
    // Remove /mill prefix if in development
    if (is_development() && strpos($request_uri, PATH_PREFIX) === 0) {
        $request_uri = substr($request_uri, strlen(PATH_PREFIX));
    }
    $canonical_url = BASE_URL . $request_uri;
    $canonical_url = strtok($canonical_url, '?'); // Remove query parameters for canonical URL
    ?>

    <!-- Primary Meta Tags -->
    <title><?php echo $page_title; ?></title>
    <meta name="title" content="<?php echo $page_title; ?>">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="Kissan Agro Foods, wheat flour, puffed rice, flour mill, Nepal, Mahottari, Dhanusha, food products, khairba mill, anubhav aata, batohi sir, Khairba">
    <meta name="author" content="Kissan Agro Foods">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $canonical_url; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo asset_url('images/og-image.jpg'); ?>">
    <meta property="og:site_name" content="<?php echo $site_title; ?>">
    <meta property="og:locale" content="en_US">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $canonical_url; ?>">
    <meta property="twitter:title" content="<?php echo $page_title; ?>">
    <meta property="twitter:description" content="<?php echo $page_description; ?>">
    <meta property="twitter:image" content="<?php echo asset_url('images/og-image.jpg'); ?>">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo asset_url('favicon/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo asset_url('favicon/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo asset_url('favicon/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?php echo asset_url('favicon/site.webmanifest'); ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/single-product.css'); ?>">

    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    <link rel="preload" href="<?php echo asset_url('css/style.css'); ?>" as="style">
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="<?php echo site_url('index.php'); ?>">
                    <?php if (get_site_image('logo', '')): ?>
                        <img src="<?php echo get_site_image('logo', 'logo.png'); ?>" alt="<?php echo $site_title; ?>" height="40" class="me-2">
                    <?php endif; ?>
                    <strong><?php echo $site_title; ?></strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="<?php echo site_url('index.php'); ?>">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>" href="<?php echo site_url('about.php'); ?>">
                                <i class="fas fa-info-circle"></i> About Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'products.php' ? 'active' : ''; ?>" href="<?php echo site_url('products.php'); ?>">
                                <i class="fas fa-box"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'contact.php' ? 'active' : ''; ?>" href="<?php echo site_url('contact.php'); ?>">
                                <i class="fas fa-envelope"></i> Contact
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'cart.php' ? 'active' : ''; ?>" href="<?php echo site_url('cart.php'); ?>">
                                <i class="fas fa-shopping-cart"></i> Cart
                                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                    <span class="badge bg-danger rounded-pill">
                                        <?php
                                        $cart_count = 0;
                                        foreach ($_SESSION['cart'] as $item) {
                                            $cart_count += $item['quantity'];
                                        }
                                        echo $cart_count;
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'track-order.php' ? 'active' : ''; ?>" href="<?php echo site_url('track-order.php'); ?>">
                                <i class="fas fa-truck"></i> Track Order
                            </a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo site_url('admin/dashboard.php'); ?>">
                                    <i class="fas fa-user-shield"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container py-4">
        <?php display_messages(); ?>