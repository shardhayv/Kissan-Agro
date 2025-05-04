<?php
// Include header
include 'includes/header.php';
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('privacy_banner', 'privacy-banner.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="text-white">Privacy Policy</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Privacy Policy</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Policy Content -->
<section class="privacy-section py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-title-wrapper">
                <h2 class="section-title">Our Privacy Policy</h2>
                <div class="section-subtitle">Last updated: <?php echo date('F j, Y'); ?></div>
            </div>
            <p class="lead text-muted mx-auto" style="max-width: 800px;">At Kissan Agro Foods, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, and safeguard your data when you use our website and services.</p>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="privacy-content p-4 p-md-5 rounded shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>1. Introduction</h3>
                        <p>Kissan Agro Foods ("we," "our," or "us") operates the website located at www.kissanagrofoods.com. This Privacy Policy describes how we collect, use, and disclose information when you use our website and services.</p>
                        <p>By accessing or using our website, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our website.</p>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3>2. Information We Collect</h3>
                        <p>We collect several types of information from and about users of our website, including:</p>
                        <h4>2.1 Personal Information</h4>
                        <p>When you place an order or contact us, we may collect personal information such as:</p>
                        <ul>
                            <li>Name</li>
                            <li>Email address</li>
                            <li>Phone number</li>
                            <li>Delivery address</li>
                            <li>Payment information (for cash on delivery verification only)</li>
                        </ul>

                        <h4>2.2 Usage Information</h4>
                        <p>We automatically collect certain information about your equipment, browsing actions, and patterns when you visit our website, including:</p>
                        <ul>
                            <li>IP address</li>
                            <li>Browser type and version</li>
                            <li>Operating system</li>
                            <li>Device type (desktop, mobile, tablet)</li>
                            <li>Pages visited and time spent on those pages</li>
                            <li>Referring website or source</li>
                        </ul>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>3. How We Use Your Information</h3>
                        <p>We use the information we collect for various purposes, including to:</p>
                        <ul>
                            <li>Process and fulfill your orders</li>
                            <li>Communicate with you about your orders and provide customer support</li>
                            <li>Improve our website and services</li>
                            <li>Analyze usage patterns and trends</li>
                            <li>Protect against fraudulent transactions and other misuses of our website</li>
                            <li>Comply with legal obligations</li>
                        </ul>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3>4. Information Sharing and Disclosure</h3>
                        <p>We do not sell, trade, or otherwise transfer your personal information to outside parties except in the following circumstances:</p>
                        <ul>
                            <li><strong>Service Providers:</strong> We may share your information with trusted third parties who assist us in operating our website, conducting our business, or servicing you (such as delivery partners).</li>
                            <li><strong>Legal Requirements:</strong> We may disclose your information when we believe release is appropriate to comply with the law, enforce our site policies, or protect our or others' rights, property, or safety.</li>
                        </ul>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>5. Data Security</h3>
                        <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                        <p>We limit access to personal information to employees, contractors, and service providers who need to know that information to process it for us and who are subject to contractual confidentiality obligations.</p>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-cookie"></i>
                        </div>
                        <h3>6. Cookies and Similar Technologies</h3>
                        <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier.</p>
                        <p>We use cookies for the following purposes:</p>
                        <ul>
                            <li>To maintain your shopping cart during your visit</li>
                            <li>To remember your preferences and settings</li>
                            <li>To analyze how our website is used so we can improve it</li>
                        </ul>
                        <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.</p>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>7. Your Rights and Choices</h3>
                        <p>You have certain rights regarding your personal information, including:</p>
                        <ul>
                            <li><strong>Access:</strong> You can request access to your personal information we hold.</li>
                            <li><strong>Correction:</strong> You can request that we correct inaccurate or incomplete information.</li>
                            <li><strong>Deletion:</strong> You can request that we delete your personal information, subject to certain exceptions.</li>
                            <li><strong>Opt-out:</strong> You can opt out of receiving promotional communications from us.</li>
                        </ul>
                        <p>To exercise these rights, please contact us using the information provided in the "Contact Us" section below.</p>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-child"></i>
                        </div>
                        <h3>8. Children's Privacy</h3>
                        <p>Our website is not intended for children under 16 years of age. We do not knowingly collect personal information from children under 16. If you are a parent or guardian and believe that your child has provided us with personal information, please contact us, and we will delete such information from our records.</p>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3>9. International Data Transfers</h3>
                        <p>We primarily operate in Nepal and process data within Nepal. However, some of our service providers may be located in different countries. By using our website, you consent to the transfer of your information to Nepal and other countries which may have different data protection laws than those in your country of residence.</p>
                    </div>

                    <div class="privacy-section-item mb-5">
                        <div class="privacy-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h3>10. Changes to This Privacy Policy</h3>
                        <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date at the top of this Privacy Policy.</p>
                        <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
                    </div>

                    <div class="privacy-section-item">
                        <div class="privacy-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h3>11. Contact Us</h3>
                        <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
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

<!-- Custom CSS for Privacy Page -->
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

    /* Privacy Section */
    .privacy-section {
        background-color: #f8f9fa;
        padding: 80px 0;
    }

    .privacy-content {
        background-color: #fff;
        border-radius: 10px;
    }

    .privacy-section-item {
        position: relative;
        padding-left: 70px;
    }

    .privacy-icon {
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

    .privacy-section-item h3 {
        margin-bottom: 15px;
        color: var(--dark-color);
        font-weight: 700;
    }

    .privacy-section-item h4 {
        margin-top: 20px;
        margin-bottom: 10px;
        color: var(--dark-color);
        font-weight: 600;
        font-size: 1.2rem;
    }

    .privacy-section-item p {
        color: var(--medium-color);
        margin-bottom: 15px;
        line-height: 1.7;
    }

    .privacy-section-item p:last-child {
        margin-bottom: 0;
    }

    .privacy-section-item ul {
        color: var(--medium-color);
        margin-bottom: 15px;
        line-height: 1.7;
    }

    .privacy-section-item ul li {
        margin-bottom: 8px;
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

        .privacy-section {
            padding: 40px 0;
        }

        .privacy-section-item {
            padding-left: 0;
            padding-top: 60px;
        }

        .privacy-icon {
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