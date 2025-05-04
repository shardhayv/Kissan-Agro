<?php
// Include authentication functions
require_once '../includes/auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    set_error_message('You must be logged in to access this page');
    redirect('index.php');
}

// Include admin header
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Admin Documentation</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Getting Started</h6>
        </div>
        <div class="card-body">
            <p>Welcome to the Kissan Agro Foods Admin Panel. This documentation will help you understand how to use the various features of the admin panel.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> The admin panel allows you to manage all aspects of your website, including products, categories, orders, inquiries, users, and site settings.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <!-- Dashboard -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dashboard</h6>
                </div>
                <div class="card-body">
                    <p>The dashboard provides an overview of your website's key metrics:</p>
                    <ul>
                        <li><strong>Products Count:</strong> Total number of products in your catalog</li>
                        <li><strong>New Inquiries:</strong> Number of unresolved customer inquiries</li>
                        <li><strong>Users:</strong> Total number of admin users</li>
                        <li><strong>Pending Orders:</strong> Number of orders awaiting processing</li>
                    </ul>
                    <p>The dashboard also displays recent inquiries and orders for quick access.</p>
                </div>
            </div>

            <!-- Products Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Products Management</h6>
                </div>
                <div class="card-body">
                    <p>The Products page allows you to manage your product catalog:</p>
                    <ul>
                        <li><strong>Add New Products:</strong> Create new products with details like name, description, price, category, and image</li>
                        <li><strong>Edit Products:</strong> Update existing product information</li>
                        <li><strong>Delete Products:</strong> Remove products from your catalog</li>
                        <li><strong>Featured Products:</strong> Mark products as featured to display them on the homepage</li>
                    </ul>
                    <p>You can also manage product categories from the Categories page.</p>
                </div>
            </div>

            <!-- Orders Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Orders Management</h6>
                </div>
                <div class="card-body">
                    <p>The Orders page allows you to manage customer orders:</p>
                    <ul>
                        <li><strong>View Orders:</strong> See detailed information about each order</li>
                        <li><strong>Update Status:</strong> Change order status (pending, processing, shipped, delivered, cancelled)</li>
                        <li><strong>Update Payment Status:</strong> Change payment status (pending, completed, failed)</li>
                        <li><strong>Search & Filter:</strong> Find orders by customer name, email, phone, date range, or status</li>
                        <li><strong>Delete Orders:</strong> Remove orders from the system</li>
                    </ul>
                    <p>Order status updates are reflected in the customer's order tracking page.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Inquiries Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Inquiries Management</h6>
                </div>
                <div class="card-body">
                    <p>The Inquiries page allows you to manage customer inquiries:</p>
                    <ul>
                        <li><strong>View Inquiries:</strong> See detailed information about each inquiry</li>
                        <li><strong>Update Status:</strong> Change inquiry status (new, in progress, resolved)</li>
                        <li><strong>Delete Inquiries:</strong> Remove inquiries from the system</li>
                    </ul>
                </div>
            </div>

            <!-- Users Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Users Management</h6>
                </div>
                <div class="card-body">
                    <p>The Users page allows administrators to manage admin users:</p>
                    <ul>
                        <li><strong>Add New Users:</strong> Create new admin users with different roles (admin, manager, staff)</li>
                        <li><strong>Edit Users:</strong> Update user information and roles</li>
                        <li><strong>Delete Users:</strong> Remove users from the system</li>
                    </ul>
                    <p>Different roles have different permissions:</p>
                    <ul>
                        <li><strong>Admin:</strong> Full access to all features</li>
                        <li><strong>Manager:</strong> Access to most features except user management</li>
                        <li><strong>Staff:</strong> Limited access to basic features</li>
                    </ul>
                </div>
            </div>

            <!-- Site Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Site Settings</h6>
                </div>
                <div class="card-body">
                    <p>The Settings page allows administrators to configure site-wide settings:</p>
                    <ul>
                        <li><strong>Site Title & Description:</strong> Update your website's title and description</li>
                        <li><strong>Contact Information:</strong> Update email, phone, and address</li>
                        <li><strong>Social Media Links:</strong> Update Facebook, Instagram, and Twitter URLs</li>
                        <li><strong>Delivery Areas:</strong> Update the list of areas where you deliver</li>
                    </ul>
                    <p>These settings affect the entire website and are used in various places like the header, footer, and contact page.</p>
                </div>
            </div>

            <!-- Website Images -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Website Images</h6>
                </div>
                <div class="card-body">
                    <p>The Website Images page allows administrators to manage site-wide images:</p>
                    <ul>
                        <li><strong>Add New Images:</strong> Upload images with a unique key for reference</li>
                        <li><strong>Edit Images:</strong> Update existing images</li>
                        <li><strong>Delete Images:</strong> Remove images from the system</li>
                    </ul>
                    <p>These images are used throughout the website in various sections like the homepage, about page, and contact page.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Security Best Practices</h6>
        </div>
        <div class="card-body">
            <p>To ensure the security of your admin panel, please follow these best practices:</p>
            <ul>
                <li><strong>Strong Passwords:</strong> Use strong, unique passwords for your admin account</li>
                <li><strong>Regular Updates:</strong> Keep your system updated with the latest security patches</li>
                <li><strong>Limited Access:</strong> Only give admin access to trusted individuals</li>
                <li><strong>Logout:</strong> Always logout when you're done using the admin panel</li>
                <li><strong>Secure Connection:</strong> Ensure you're using a secure connection (HTTPS) when accessing the admin panel</li>
            </ul>
        </div>
    </div>
</div>

<?php
// Include admin footer
include 'includes/footer.php';
?>
