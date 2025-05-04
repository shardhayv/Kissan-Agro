<?php
// Include header
include 'includes/header.php';

// Get categories
$categories = get_categories();

// Check if product ID is provided for single product view
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $product = get_product($product_id);

    // If product not found, redirect to products page
    if (!$product) {
        set_error_message('Product not found');
        redirect('products.php');
    }
} else {
    // Get all products or filter by category
    $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
    $products = get_products($category_id);
}
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header enhanced-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('products_header', 'products-header.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown">
                <?php echo isset($product) ? $product['name'] : 'Our Products'; ?>
            </h1>
            <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">
                <?php echo isset($product) ? 'Premium quality product for your needs' : 'Discover our range of high-quality wheat flour and puffed rice products'; ?>
            </p>
            <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp animate__delay-1s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>">Home</a></li>
                    <?php if (isset($product)): ?>
                        <li class="breadcrumb-item"><a href="<?php echo site_url('products.php'); ?>">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php if (isset($product)): ?>
    <!-- Enhanced Single Product View -->
    <section class="single-product-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                    <div class="product-image-container">
                        <img src="<?php echo !empty($product['image']) ? upload_url($product['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $product['name']; ?>" class="img-fluid rounded-lg shadow-lg">
                        <?php if ($product['is_featured']): ?>
                            <div class="product-badge featured">
                                <i class="fas fa-star me-1"></i> Featured
                            </div>
                        <?php endif; ?>
                        <?php if ($product['stock'] > 0): ?>
                            <div class="product-badge stock in-stock">
                                <i class="fas fa-check-circle me-1"></i> In Stock
                            </div>
                        <?php else: ?>
                            <div class="product-badge stock out-of-stock">
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 product-details" data-aos="fade-left" data-aos-delay="200">
                    <div class="category-badge mb-3">
                        <span><?php echo $product['category_name']; ?></span>
                    </div>
                    <h2 class="product-title mb-3"><?php echo $product['name']; ?></h2>
                    <div class="product-price mb-4"><?php echo format_price($product['price']); ?></div>

                    <div class="product-description mb-4">
                        <h5 class="description-title">Product Description</h5>
                        <p><?php echo $product['description']; ?></p>
                    </div>

                    <div class="product-features mb-4" data-aos="fade-up" data-aos-delay="300">
                        <h5 class="features-title">Product Features</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="features-list">
                                    <li><i class="fas fa-check-circle"></i> Premium Quality</li>
                                    <li><i class="fas fa-check-circle"></i> Carefully Processed</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="features-list">
                                    <li><i class="fas fa-check-circle"></i> Nutritional Value</li>
                                    <li><i class="fas fa-check-circle"></i> Consistent Taste</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="delivery-info mb-4">
                        <div class="delivery-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="delivery-content">
                            <h5>Delivery Information</h5>
                            <p>We deliver to all areas in Mahottari and Dhanusha districts. Free delivery on orders above Rs. 1000.</p>
                        </div>
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                        <div class="product-actions mt-4" data-aos="fade-up" data-aos-delay="400">
                            <form action="<?php echo site_url('cart.php'); ?>" method="get" class="add-to-cart-form">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="quantity" class="form-label fw-bold">Quantity</label>
                                        <div class="quantity-control">
                                            <button type="button" class="quantity-btn minus" onclick="decrementQuantity()">-</button>
                                            <input type="number" name="quantity" id="quantity" class="form-control quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                            <button type="button" class="quantity-btn plus" onclick="incrementQuantity(<?php echo $product['stock']; ?>)">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-lg d-block w-100">
                                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="additional-actions mt-4">
                        <a href="<?php echo site_url('contact.php?subject=Inquiry about ' . urlencode($product['name'])); ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-envelope me-2"></i> Inquire Now
                        </a>
                        <a href="<?php echo site_url('products.php'); ?>" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-arrow-left me-2"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function incrementQuantity(max) {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value, 10);
            if (currentValue < max) {
                input.value = currentValue + 1;
            }
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value, 10);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }
    </script>

    <!-- Related Products -->
    <section class="related-products-section py-5 bg-light">
        <div class="container">
            <div class="section-title-wrapper text-center mb-5">
                <h2 class="section-title">Related Products</h2>
                <p class="section-subtitle">You might also be interested in these products</p>
            </div>
            <div class="row g-4">
                <?php
                $related_products = get_products($product['category_id'], 3);
                foreach ($related_products as $related_product):
                    if ($related_product['id'] != $product_id):
                ?>
                        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="product-card enhanced" data-category="<?php echo $related_product['category_id']; ?>" data-featured="<?php echo $related_product['is_featured']; ?>">
                                <div class="image-container">
                                    <img src="<?php echo !empty($related_product['image']) ? upload_url($related_product['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $related_product['name']; ?>" class="card-img-top">
                                    <?php if ($related_product['is_featured']): ?>
                                        <div class="featured-tag">
                                            <i class="fas fa-star me-1"></i> Featured
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($related_product['stock'] > 0): ?>
                                        <div class="stock-tag in-stock">
                                            <i class="fas fa-check-circle me-1"></i> In Stock
                                        </div>
                                    <?php else: ?>
                                        <div class="stock-tag out-of-stock">
                                            <i class="fas fa-times-circle me-1"></i> Out of Stock
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="category-badge">
                                        <span><?php echo $related_product['category_name']; ?></span>
                                    </div>
                                    <h5 class="card-title"><?php echo $related_product['name']; ?></h5>
                                    <p class="card-text"><?php echo substr($related_product['description'], 0, 60) . '...'; ?></p>
                                    <div class="price-and-actions">
                                        <p class="price"><?php echo format_price($related_product['price']); ?></p>
                                        <div class="product-actions">
                                            <a href="<?php echo site_url('products.php?id=' . $related_product['id']); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                            <?php if ($related_product['stock'] > 0): ?>
                                                <a href="<?php echo site_url('cart.php?action=add&id=' . $related_product['id']); ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- Products Listing -->
    <section class="products-listing-section py-5">
        <div class="container">
            <div class="section-title-wrapper text-center mb-5">
                <h2 class="section-title">Our Products</h2>
                <p class="section-subtitle">Discover our range of high-quality products</p>
            </div>

            <!-- Enhanced Category Filter -->
            <div class="category-filter-container mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <form action="<?php echo site_url('products.php'); ?>" method="get" class="category-filter-form">
                            <div class="custom-select-wrapper">
                                <div class="select-icon">
                                    <i class="fas fa-th-large"></i>
                                </div>
                                <select name="category" id="categoryFilter" class="custom-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="select-arrow">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-4">
                <?php if (empty($products)): ?>
                    <div class="col-12">
                        <div class="alert alert-info p-4 text-center shadow-sm">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h4>No Products Found</h4>
                            <p>We couldn't find any products matching your criteria. Please try a different category or check back later.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                            <div class="product-card enhanced" data-category="<?php echo $product['category_id']; ?>" data-featured="<?php echo $product['is_featured']; ?>">
                                <div class="image-container">
                                    <img src="<?php echo !empty($product['image']) ? upload_url($product['image']) : asset_url('images/product-placeholder.jpg'); ?>" alt="<?php echo $product['name']; ?>" class="card-img-top">
                                    <?php if ($product['is_featured']): ?>
                                        <div class="featured-tag">
                                            <i class="fas fa-star me-1"></i> Featured
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($product['stock'] > 0): ?>
                                        <div class="stock-tag in-stock">
                                            <i class="fas fa-check-circle me-1"></i> In Stock
                                        </div>
                                    <?php else: ?>
                                        <div class="stock-tag out-of-stock">
                                            <i class="fas fa-times-circle me-1"></i> Out of Stock
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="category-badge">
                                        <span><?php echo $product['category_name']; ?></span>
                                    </div>
                                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                    <p class="card-text"><?php echo substr($product['description'], 0, 60) . '...'; ?></p>
                                    <div class="price-and-actions">
                                        <p class="price"><?php echo format_price($product['price']); ?></p>
                                        <div class="product-actions">
                                            <a href="<?php echo site_url('products.php?id=' . $product['id']); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                            <?php if ($product['stock'] > 0): ?>
                                                <a href="<?php echo site_url('cart.php?action=add&id=' . $product['id']); ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Add Animation Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            easing: 'ease',
            once: true
        });
    });
</script>

<!-- Custom CSS for Products Page -->
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

    /* Single Product Section */
    .single-product-section {
        background-color: white;
        padding: 80px 0;
    }

    .product-image-container {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .product-badge {
        position: absolute;
        padding: 8px 15px;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .product-badge.featured {
        top: 20px;
        left: 20px;
        background-color: var(--secondary-color);
        color: var(--dark-color);
    }

    .product-badge.stock {
        top: 20px;
        right: 20px;
    }

    .product-badge.in-stock {
        background-color: rgba(46, 204, 113, 0.9);
        color: white;
    }

    .product-badge.out-of-stock {
        background-color: rgba(231, 76, 60, 0.9);
        color: white;
    }

    .category-badge {
        display: inline-block;
    }

    .category-badge span {
        background-color: rgba(78, 125, 52, 0.1);
        color: var(--primary-color);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .product-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-color);
        position: relative;
        padding-left: 15px;
        display: inline-block;
    }

    .product-price:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 5px;
        height: 70%;
        background-color: var(--primary-color);
        border-radius: 2px;
    }

    .product-description {
        margin-bottom: 30px;
    }

    .description-title,
    .features-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--dark-color);
        position: relative;
        padding-bottom: 10px;
    }

    .description-title:after,
    .features-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background-color: var(--primary-color);
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .features-list li {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .features-list li i {
        color: var(--primary-color);
        margin-right: 10px;
    }

    .delivery-info {
        display: flex;
        background-color: rgba(78, 125, 52, 0.05);
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid var(--primary-color);
    }

    .delivery-icon {
        width: 50px;
        height: 50px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        color: var(--primary-color);
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .delivery-content h5 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: var(--dark-color);
    }

    .delivery-content p {
        margin-bottom: 0;
        color: var(--medium-color);
    }

    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .quantity-btn {
        width: 40px;
        height: 38px;
        background-color: #f8f9fa;
        border: none;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .quantity-btn:hover {
        background-color: #e9ecef;
    }

    .quantity-input {
        width: calc(100% - 80px);
        text-align: center;
        border: none;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-radius: 0;
    }

    .add-to-cart-form .btn {
        border-radius: 4px;
        font-weight: 600;
    }

    .additional-actions .btn {
        border-radius: 4px;
        font-weight: 600;
    }

    /* Related Products Section */
    .related-products-section {
        background-color: #f9f9f9;
        padding: 80px 0;
    }

    /* Products Listing Section */
    .products-listing-section {
        background-color: white;
        padding: 80px 0;
    }

    /* Category Filter Styles */
    .category-filter-container {
        margin-bottom: 40px;
    }

    .custom-select-wrapper {
        position: relative;
        width: 100%;
        height: 55px;
        background-color: #fff;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .custom-select-wrapper:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .select-icon {
        position: absolute;
        left: 20px;
        color: var(--primary-color);
        font-size: 1.2rem;
        z-index: 2;
    }

    .custom-select {
        width: 100%;
        height: 100%;
        padding: 0 60px 0 55px;
        border: none;
        background-color: transparent;
        font-size: 1rem;
        font-weight: 500;
        color: var(--dark-color);
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        outline: none;
        z-index: 1;
    }

    .custom-select:focus {
        outline: none;
    }

    .custom-select option {
        padding: 10px;
        font-size: 1rem;
    }

    .select-arrow {
        position: absolute;
        right: 20px;
        color: var(--primary-color);
        font-size: 0.9rem;
        pointer-events: none;
        transition: transform 0.3s ease;
    }

    .custom-select:focus+.select-arrow {
        transform: rotate(180deg);
    }

    /* Active state for the dropdown */
    .custom-select-wrapper.active {
        border: 2px solid var(--primary-color);
        box-shadow: 0 8px 25px rgba(78, 125, 52, 0.15);
        transform: translateY(-2px);
    }

    /* Animation for the dropdown */
    @keyframes selectPulse {
        0% {
            border-color: #e0e0e0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        50% {
            border-color: var(--primary-color);
            box-shadow: 0 8px 25px rgba(78, 125, 52, 0.15);
        }

        100% {
            border-color: #e0e0e0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
    }

    .category-filter-container:hover .custom-select-wrapper {
        animation: selectPulse 2s infinite;
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .enhanced-header {
            padding: 80px 0;
        }

        .product-title {
            font-size: 2rem;
        }

        .product-price {
            font-size: 1.8rem;
        }

        .single-product-section,
        .related-products-section,
        .products-listing-section {
            padding: 60px 0;
        }
    }

    @media (max-width: 767px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .product-title {
            font-size: 1.8rem;
        }

        .product-price {
            font-size: 1.5rem;
        }

        .delivery-info {
            flex-direction: column;
        }

        .delivery-icon {
            margin-bottom: 15px;
            margin-right: 0;
        }

        .additional-actions {
            display: flex;
            flex-direction: column;
        }

        .additional-actions .btn {
            margin-bottom: 10px;
            margin-left: 0 !important;
        }

        .custom-select-wrapper {
            height: 50px;
            border-width: 1px;
        }

        .custom-select-wrapper.active {
            border-width: 2px;
        }

        .select-icon {
            left: 15px;
            font-size: 1rem;
        }

        .custom-select {
            padding: 0 50px 0 45px;
            font-size: 0.95rem;
        }

        .select-arrow {
            right: 15px;
        }
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>