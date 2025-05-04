<?php
// Include header
include 'includes/header.php';

// Get featured products
$featured_products = get_products(null, 6, true);
?>

<!-- Hero Section -->
<section class="hero" data-bg-image="<?php echo get_site_image('hero_bg', 'hero-bg.jpg'); ?>">
    <div class="container">
        <div class="hero-content">
            <h1 class="animate-text">Welcome to Kissan Agro Foods</h1>
            <p class="lead animate-text-delay-1">Quality wheat flour and puffed rice products for your everyday needs</p>
            <p class="text-white location-text animate-text-delay-2"><i class="fas fa-map-marker-alt me-2"></i>Located at MV37+9JJ, Pipra 45700, Khairba, Mahottari, Nepal | Delivering across Mahottari and Dhanusha districts</p>
            <div class="hero-buttons animate-text-delay-3">
                <a href="products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-basket me-2"></i>Explore Our Products
                </a>
                <a href="about.php" class="btn btn-outline-light btn-lg ms-2">
                    <i class="fas fa-info-circle me-2"></i>Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-content">
                    <div class="section-title-wrapper">
                        <h2 class="section-title">About Kissan Agro Foods</h2>
                    </div>
                    <div class="about-text">
                        <p class="lead">Kissan Agro Foods is a leading manufacturer of high-quality wheat flour and puffed rice products. With two state-of-the-art mills, we are committed to delivering the finest products to our customers.</p>
                        <p>Our modern facilities and strict quality control ensure that every product that leaves our mills meets the highest standards of quality and taste.</p>
                        <div class="about-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-primary"></i>
                                <span>Premium Quality Products</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-primary"></i>
                                <span>Modern Manufacturing Facilities</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-primary"></i>
                                <span>Timely Delivery Across Districts</span>
                            </div>
                        </div>
                        <a href="about.php" class="btn btn-primary mt-4">
                            <i class="fas fa-arrow-right me-2"></i>Learn More About Us
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-img-wrapper">
                    <div class="about-img">
                        <img src="<?php echo get_site_image('about_image', 'about.jpg'); ?>" alt="About Kissan Agro Foods" class="img-fluid">
                    </div>
                    <div class="experience-badge">
                        <span class="years">10+</span>
                        <span class="text">Years of Excellence</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mills Section -->
<section class="mills-section py-5">
    <div class="container">
        <div class="section-title-wrapper text-center">
            <h2 class="section-title">Our State-of-the-Art Mills</h2>
            <p class="section-subtitle">Combining traditional expertise with modern technology</p>
        </div>
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <div class="mill-card">
                    <div class="mill-card-icon">
                        <i class="fas fa-wheat-awn"></i>
                    </div>
                    <div class="mill-card-content">
                        <h3>Wheat Flour Mill</h3>
                        <p>Our state-of-the-art wheat flour mill produces high-quality flour for all your baking and cooking needs. Using the latest technology, we ensure that our flour is of the highest quality.</p>
                        <ul class="mill-features">
                            <li><i class="fas fa-check me-2"></i>Premium quality wheat sourcing</li>
                            <li><i class="fas fa-check me-2"></i>Modern milling technology</li>
                            <li><i class="fas fa-check me-2"></i>Strict quality control measures</li>
                        </ul>
                    </div>
                    <div class="mill-card-image">
                        <img src="<?php echo get_site_image('wheat_mill_image', 'wheat-mill.jpg'); ?>" alt="Wheat Flour Mill" class="img-fluid">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mill-card">
                    <div class="mill-card-icon">
                        <i class="fas fa-bowl-rice"></i>
                    </div>
                    <div class="mill-card-content">
                        <h3>Puffed Rice Mill</h3>
                        <p>Our puffed rice mill produces light, crispy puffed rice that is perfect for snacks and breakfast cereals. We use traditional methods combined with modern technology to create the perfect texture.</p>
                        <ul class="mill-features">
                            <li><i class="fas fa-check me-2"></i>Traditional puffing techniques</li>
                            <li><i class="fas fa-check me-2"></i>No artificial additives</li>
                            <li><i class="fas fa-check me-2"></i>Consistent quality and taste</li>
                        </ul>
                    </div>
                    <div class="mill-card-image">
                        <img src="<?php echo get_site_image('rice_mill_image', 'rice-mill.jpg'); ?>" alt="Puffed Rice Mill" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products-section py-5">
    <div class="container">
        <div class="section-title-wrapper text-center">
            <h2 class="section-title">Our Featured Products</h2>
            <p class="section-subtitle">Discover our most popular high-quality products</p>
        </div>

        <div class="row g-4 mt-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
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
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo site_url('products.php'); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us-section py-5">
    <div class="container">
        <div class="section-title-wrapper text-center">
            <h2 class="section-title">Why Choose Us</h2>
            <p class="section-subtitle">Discover the advantages of partnering with Kissan Agro Foods</p>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Premium Quality Products</h4>
                        <p>We are committed to delivering the highest quality products to our customers. Our strict quality control ensures that every product meets our high standards.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle me-2"></i>Finest raw materials</li>
                            <li><i class="fas fa-check-circle me-2"></i>Rigorous quality testing</li>
                            <li><i class="fas fa-check-circle me-2"></i>Consistent product quality</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Reliable Delivery Service</h4>
                        <p>We understand the importance of timely delivery. Our efficient logistics ensure that your orders are delivered on time, every time.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle me-2"></i>On-time delivery</li>
                            <li><i class="fas fa-check-circle me-2"></i>Safe transportation</li>
                            <li><i class="fas fa-check-circle me-2"></i>Order tracking available</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Exceptional Customer Support</h4>
                        <p>Our dedicated customer support team is always ready to assist you with any queries or concerns you may have about our products or services.</p>
                        <ul class="feature-list">
                            <li><i class="fas fa-check-circle me-2"></i>Responsive support team</li>
                            <li><i class="fas fa-check-circle me-2"></i>Quick issue resolution</li>
                            <li><i class="fas fa-check-circle me-2"></i>Customer satisfaction focus</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo site_url('about.php'); ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-info-circle me-2"></i>Learn More About Our Values
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <div class="section-title-wrapper text-center mb-5">
            <h2 class="section-title">Get in Touch</h2>
            <p class="section-subtitle">We'd love to hear from you</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="contact-info-wrapper">
                    <div class="contact-intro">
                        <p class="lead">Have questions about our products or services? Contact us today and our team will be happy to assist you.</p>
                    </div>

                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Our Location</h5>
                                <p><?php echo get_setting('address', 'MV37+9JJ, Pipra 45700, Khairba, Mahottari, Nepal'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Phone Number</h5>
                                <p><?php echo get_setting('contact_phone', '+977 9800000000'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Email Address</h5>
                                <p><?php echo get_setting('contact_email', 'info@kissanagrofoods.com'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="icon-box">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Business Hours</h5>
                                <p>Monday - Saturday: 9:00 AM - 6:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-button mt-4">
                        <a href="<?php echo site_url('contact.php'); ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="map-wrapper">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3565.853065130027!2d85.86466606698231!3d26.65318659474385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ec410074cfa351%3A0x556ec86698c09a97!2sSHREE%20SHALHES%20TEMPLE!5e0!3m2!1sen!2snp!4v1746182885974!5m2!1sen!2snp" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>