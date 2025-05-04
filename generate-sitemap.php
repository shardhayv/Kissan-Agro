<?php
// Include database connection
require_once 'config/database.php';
require_once 'includes/functions.php';

// Set content type to XML
header('Content-Type: application/xml; charset=utf-8');

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Get base URL from environment settings
$base_url = rtrim(BASE_URL, '/');

// Static pages with their priorities and change frequencies
$static_pages = [
    'index.php' => ['priority' => '1.0', 'changefreq' => 'weekly'],
    'about.php' => ['priority' => '0.9', 'changefreq' => 'monthly'], // Higher priority for about page with Khairba Mill and Batohi Sir info
    'products.php' => ['priority' => '0.9', 'changefreq' => 'weekly'], // High priority for products including Anubhav Aata
    'contact.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'cart.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    'track-order.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    'terms.php' => ['priority' => '0.4', 'changefreq' => 'yearly'],
    'privacy.php' => ['priority' => '0.4', 'changefreq' => 'yearly'],
];

// Add static pages to sitemap
foreach ($static_pages as $page => $meta) {
    echo "\t<url>\n";
    echo "\t\t<loc>$base_url/$page</loc>\n";
    echo "\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "\t\t<changefreq>{$meta['changefreq']}</changefreq>\n";
    echo "\t\t<priority>{$meta['priority']}</priority>\n";
    echo "\t</url>\n";
}

// Get all products from database
$query = "SELECT id, updated_at FROM products ORDER BY id";
$result = mysqli_query($conn, $query);

// Add product pages to sitemap
while ($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['id'];
    $lastmod = date('Y-m-d', strtotime($row['updated_at']));

    echo "\t<url>\n";
    echo "\t\t<loc>$base_url/products.php?id=$product_id</loc>\n";
    echo "\t\t<lastmod>$lastmod</lastmod>\n";
    echo "\t\t<changefreq>weekly</changefreq>\n";
    echo "\t\t<priority>0.7</priority>\n";
    echo "\t</url>\n";
}

// Get all categories from database
$query = "SELECT id, updated_at FROM categories ORDER BY id";
$result = mysqli_query($conn, $query);

// Add category pages to sitemap
while ($row = mysqli_fetch_assoc($result)) {
    $category_id = $row['id'];
    $lastmod = date('Y-m-d', strtotime($row['updated_at']));

    echo "\t<url>\n";
    echo "\t\t<loc>$base_url/products.php?category=$category_id</loc>\n";
    echo "\t\t<lastmod>$lastmod</lastmod>\n";
    echo "\t\t<changefreq>weekly</changefreq>\n";
    echo "\t\t<priority>0.6</priority>\n";
    echo "\t</url>\n";
}

// End XML output
echo '</urlset>';
