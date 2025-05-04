<?php
// Include header
include 'includes/header.php';
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('terms_banner', 'terms-banner.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="text-white">Terms & Conditions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Terms & Conditions</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Content -->
<section class="terms-section py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-title-wrapper">
                <h2 class="section-title">Our Terms and Conditions</h2>
                <div class="section-subtitle">Last updated: <?php echo date('F j, Y'); ?></div>
            </div>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">Please read these terms and conditions carefully before using our services. By accessing or using our website and services, you agree to be bound by these terms.</p>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="terms-content p-4 p-md-5 rounded shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3>1. Acceptance of Terms</h3>
                        <p>By accessing or using the Kissan Agro Foods website and services, you agree to be bound by these Terms and Conditions. If you do not agree to all the terms and conditions, you may not access or use our services.</p>
                        <p>These terms apply to all visitors, users, and others who access or use our website and services. By accessing or using our website, you agree to be bound by these terms.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>2. Order Placement</h3>
                        <p>Orders can be placed through our website or by contacting our customer service. All orders are subject to acceptance and availability.</p>
                        <p>When you place an order, you are making an offer to purchase products. We reserve the right to accept or decline your order for any reason, including but not limited to product availability, errors in product or pricing information, or problems identified by our verification procedures.</p>
                        <p>We will confirm acceptance of your order by sending you an order confirmation email. The contract between you and Kissan Agro Foods will only be formed when we send you this confirmation.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3>3. Pricing and Payment</h3>
                        <p>All prices are in Nepalese Rupees (NPR) and include applicable taxes. Payment is accepted via cash on delivery only.</p>
                        <p>We reserve the right to change prices for products displayed on our website at any time, and to correct pricing errors that may inadvertently occur.</p>
                        <p>Payment must be made in full upon delivery of the products. Failure to make payment may result in the return of the products and cancellation of the order.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>4. Delivery</h3>
                        <p>We deliver to all areas in Mahottari and Dhanusha districts. Delivery times may vary based on your location and other factors.</p>
                        <p>We aim to deliver products within 1-3 business days of order confirmation, but delivery times are estimates and not guaranteed. We are not responsible for delays beyond our control.</p>
                        <p>You are responsible for providing accurate delivery information. If you provide incorrect or incomplete delivery information, we may not be able to deliver your order, and additional delivery charges may apply for redelivery.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h3>5. Returns and Refunds</h3>
                        <p>If you are not satisfied with your purchase, please contact our customer service within 24 hours of receiving your order.</p>
                        <p>To be eligible for a return, your item must be unused and in the same condition that you received it. It must also be in the original packaging.</p>
                        <p>We reserve the right to refuse returns if the product has been opened, used, or damaged after delivery, or if the return is not reported within the specified timeframe.</p>
                        <p>Refunds will be processed once we have received and inspected the returned item. Refunds will be issued in the same form as the original payment.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>6. Privacy Policy</h3>
                        <p>We respect your privacy and will only use your personal information to process your order and improve our services.</p>
                        <p>The personal information we collect may include your name, address, email address, phone number, and payment information. We use this information to process and fulfill your orders, communicate with you about your orders, and provide customer support.</p>
                        <p>We do not sell, trade, or otherwise transfer your personal information to outside parties except as necessary to fulfill your orders or as required by law.</p>
                        <p>For complete details about how we collect, use, and protect your information, please review our <a href="<?php echo site_url('privacy.php'); ?>">Privacy Policy</a>.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h3>7. Product Information</h3>
                        <p>We make every effort to display as accurately as possible the colors, features, specifications, and details of the products available on our website. However, we cannot guarantee that your computer's display of any color will be accurate, and we do not guarantee that product descriptions or other content on the website is accurate, complete, reliable, current, or error-free.</p>
                        <p>All products are subject to availability. We reserve the right to discontinue any product at any time.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <h3>8. Website Usage and Tracking</h3>
                        <p>When you visit our website, we automatically collect certain information about your device and your interaction with our website. This may include your IP address, browser type, operating system, device type, pages visited, and time spent on those pages.</p>
                        <p>We use this information to analyze website usage, improve our website and services, protect against fraudulent activities, and enhance user experience. This information is collected through cookies and similar tracking technologies.</p>
                        <p>By using our website, you consent to such processing and you warrant that all data provided by you is accurate and up to date.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h3>9. Limitation of Liability</h3>
                        <p>Kissan Agro Foods shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the website or services.</p>
                        <p>In no event shall our total liability to you for all claims exceed the amount paid by you for the products giving rise to such liability.</p>
                    </div>

                    <div class="terms-section-item mb-5">
                        <div class="terms-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h3>10. Changes to Terms</h3>
                        <p>We reserve the right to modify these terms at any time. We will provide notice of significant changes by updating the date at the top of these terms and by maintaining a current version of the terms on our website.</p>
                        <p>Your continued use of our website and services after such modifications will constitute your acknowledgment of the modified terms and agreement to abide and be bound by the modified terms.</p>
                    </div>

                    <div class="terms-section-item">
                        <div class="terms-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3>11. Contact Us</h3>
                        <p>If you have any questions about these Terms and Conditions, please contact us:</p>
                        <ul class="contact-list">
                            <li><i class="fas fa-map-marker-alt"></i> <?php echo get_setting('address', 'Khairba, Mahottari, Nepal'); ?></li>
                            <li><i class="fas fa-phone"></i> <?php echo get_setting('contact_phone', '+977 9800000000'); ?></li>
                            <li><i class="fas fa-envelope"></i> <?php echo get_setting('contact_email', 'info@kissanagrofoods.com'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="200">
            <a href="<?php echo site_url('contact.php'); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-envelope me-2"></i> Contact Us for Questions
            </a>
            <a href="<?php echo site_url('index.php'); ?>" class="btn btn-outline-primary btn-lg ms-2">
                <i class="fas fa-home me-2"></i> Return to Homepage
            </a>
        </div>
    </div>
</section>

<!-- Custom CSS for Terms Page -->
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

    /* Terms Section */
    .terms-section {
        background-color: #f8f9fa;
        padding: 80px 0;
    }

    .terms-content {
        background-color: #fff;
        border-radius: 10px;
    }

    .terms-section-item {
        position: relative;
        padding-left: 70px;
    }

    .terms-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 50px;
        height: 50px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    .terms-section-item h3 {
        margin-bottom: 15px;
        color: var(--dark-color);
        font-weight: 700;
    }

    .terms-section-item p {
        color: var(--medium-color);
        margin-bottom: 15px;
        line-height: 1.7;
    }

    .terms-section-item p:last-child {
        margin-bottom: 0;
    }

    .contact-list {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
    }

    .contact-list li {
        margin-bottom: 10px;
        color: var(--medium-color);
    }

    .contact-list li i {
        width: 25px;
        color: var(--primary-color);
    }

    /* Responsive Styles */
    @media (max-width: 767.98px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .terms-section {
            padding: 40px 0;
        }

        .terms-section-item {
            padding-left: 0;
            padding-top: 60px;
        }

        .terms-icon {
            left: 50%;
            transform: translateX(-50%);
            top: 0;
        }
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>