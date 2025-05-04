<?php
// Include authentication functions
require_once '../includes/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kissan Agro Foods | Admin - <?php echo ucwords(str_replace('_', ' ', basename($_SERVER['PHP_SELF'], '.php'))); ?></title>
    <meta name="description" content="Admin panel for Kissan Agro Foods">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css'); ?>">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <a href="<?php echo site_url('admin/dashboard.php'); ?>" style="text-decoration: none; color: inherit;">
                <h2>Kissan Agro Foods</h2>
                <p class="mb-0">Admin Panel</p>
            </a>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo site_url('admin/dashboard.php'); ?>" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('admin/products.php'); ?>" class="<?php echo $current_page === 'products.php' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Products
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('admin/categories.php'); ?>" class="<?php echo $current_page === 'categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('admin/inquiries.php'); ?>" class="<?php echo $current_page === 'inquiries.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Inquiries
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('admin/orders.php'); ?>" class="<?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
            </li>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li>
                    <a href="<?php echo site_url('admin/users.php'); ?>" class="<?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('admin/settings.php'); ?>" class="<?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('admin/images.php'); ?>" class="<?php echo $current_page === 'images.php' ? 'active' : ''; ?>">
                        <i class="fas fa-images"></i> Website Images
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('admin/seo-analysis.php'); ?>" class="<?php echo $current_page === 'seo-analysis.php' ? 'active' : ''; ?>">
                        <i class="fas fa-search"></i> SEO Analysis
                    </a>
                </li>
                <?php
                // Check if visitor_logs table exists
                $query = "SHOW TABLES LIKE 'visitor_logs'";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0):
                ?>
                    <li>
                        <a href="<?php echo site_url('admin/visitor_logs.php'); ?>" class="<?php echo $current_page === 'visitor_logs.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i> Visitor Logs
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                // Check if security_logs table exists
                $query = "SHOW TABLES LIKE 'security_logs'";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0):
                ?>
                    <li>
                        <a href="<?php echo site_url('admin/security_logs.php'); ?>" class="<?php echo $current_page === 'security_logs.php' ? 'active' : ''; ?>">
                            <i class="fas fa-shield-alt"></i> Security Logs
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <li>
                <a href="<?php echo site_url('admin/admin_documentation.php'); ?>" class="<?php echo $current_page === 'admin_documentation.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Documentation
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('index.php'); ?>" target="_blank">
                    <i class="fas fa-globe"></i> View Website
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('admin/logout.php'); ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light">
        <div class="container-fluid">
            <!-- Sidebar Toggle Button -->
            <button id="sidebarToggle" class="sidebar-toggle" type="button" aria-label="Toggle Sidebar" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Page Title for Mobile -->
            <div class="d-block d-lg-none">
                <h5 class="mb-0"><?php echo ucwords(str_replace('_', ' ', basename($_SERVER['PHP_SELF'], '.php'))); ?></h5>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['full_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo site_url('admin/profile.php'); ?>"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?php echo site_url('admin/logout.php'); ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <?php
        // Display messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['success_message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo $_SESSION['error_message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['error_message']);
        }
        ?>