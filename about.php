<?php
// Include header
include 'includes/header.php';

// Add structured data for About page
$structured_data = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => "Kissan Agro Foods",
    "description" => "Kissan Agro Foods (also known as Khairba Mill) is a leading manufacturer of high-quality wheat flour (Anubhav Aata) and puffed rice products in Nepal, founded by Batohi Sir.",
    "url" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/mill/",
    "logo" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/mill/assets/images/logo.png",
    "foundingDate" => "2020",
    "address" => [
        "@type" => "PostalAddress",
        "streetAddress" => "MV37+9JJ, Pipra 45700",
        "addressLocality" => "Khairba",
        "addressRegion" => "Mahottari",
        "postalCode" => "45700",
        "addressCountry" => "Nepal"
    ],
    "contactPoint" => [
        "@type" => "ContactPoint",
        "telephone" => get_setting('contact_phone', '+977 9800000000'),
        "contactType" => "customer service",
        "email" => get_setting('contact_email', 'info@kissanagrofoods.com'),
        "areaServed" => ["Mahottari", "Dhanusha"],
        "availableLanguage" => ["English", "Nepali"]
    ],
    "sameAs" => [
        get_setting('facebook_url', '#'),
        get_setting('instagram_url', '#'),
        get_setting('twitter_url', '#')
    ]
];
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header enhanced-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('about_header', 'about-header.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown">About Us</h1>
            <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">Discover our journey, mission, and commitment to quality</p>
            <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp animate__delay-1s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Us</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Enhanced About Section -->
<section class="container my-5 py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0 position-relative" data-aos="fade-right" data-aos-delay="100">
            <div class="about-image-container">
                <img src="<?php echo get_site_image('about_image', 'about.jpg'); ?>" alt="Kissan Agro Foods - Premium Wheat Flour and Puffed Rice Mill in Mahottari, Nepal" class="img-fluid rounded-lg shadow-lg" loading="lazy">
                <div class="about-experience-badge">
                    <span class="years">3+</span>
                    <span class="text">Years of Excellence</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
            <div class="section-title-wrapper mb-4">
                <h2 class="section-title">Our Story</h2>
                <div class="section-subtitle">A journey of quality and excellence</div>
            </div>
            <p class="lead text-dark mb-4">Kissan Agro Foods, locally known as "Khairba Mill", was established in 2020 by Batohi Sir with a vision to provide high-quality wheat flour (our premium "Anubhav Aata") and puffed rice products to customers. What started as a small mill at MV37+9JJ, Pipra 45700, Khairba, Mahottari, Nepal has now grown into a leading manufacturer in the region.</p>
            <p class="mb-4">Over the years, we have expanded our operations and invested in state-of-the-art machinery to ensure that our products meet the highest standards of quality. Our commitment to excellence has earned us the trust of our customers and made us a preferred choice in the market.</p>
            <div class="about-features" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Premium Quality Products</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Modern Manufacturing Facilities</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Serving Mahottari and Dhanusha Districts</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission & Vision -->
<section class="mission-vision-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Mission & Vision</h2>
            <p class="section-subtitle">Guiding principles that drive our business forward</p>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="mission-card h-100">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="mission-content">
                        <h3>Our Mission</h3>
                        <p>To produce and deliver high-quality wheat flour and puffed rice products that exceed customer expectations while maintaining the highest standards of food safety and hygiene.</p>
                        <ul class="mission-features">
                            <li><i class="fas fa-check"></i> Quality Products</li>
                            <li><i class="fas fa-check"></i> Customer Satisfaction</li>
                            <li><i class="fas fa-check"></i> Food Safety Standards</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="vision-card h-100">
                    <div class="vision-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="vision-content">
                        <h3>Our Vision</h3>
                        <p>To be the leading manufacturer of wheat flour and puffed rice products, recognized for our quality, innovation, and customer satisfaction.</p>
                        <ul class="vision-features">
                            <li><i class="fas fa-check"></i> Market Leadership</li>
                            <li><i class="fas fa-check"></i> Product Innovation</li>
                            <li><i class="fas fa-check"></i> Regional Expansion</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mills -->
<section class="mills-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our State-of-the-Art Mills</h2>
            <p class="section-subtitle">Modern facilities producing premium quality products</p>
        </div>

        <!-- Wheat Flour Mill -->
        <div class="mill-showcase mb-5">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0" data-aos="fade-left" data-aos-delay="100">
                    <div class="mill-image-container">
                        <img src="<?php echo get_site_image('wheat_mill', 'wheat-mill.jpg'); ?>" alt="Kissan Agro Foods - Modern Wheat Flour Mill with Advanced Technology" class="img-fluid rounded-lg shadow-lg" loading="lazy">
                        <div class="mill-badge">
                            <span>Premium Quality</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1" data-aos="fade-right" data-aos-delay="200">
                    <div class="mill-content">
                        <div class="mill-icon">
                            <i class="fas fa-wheat-awn"></i>
                        </div>
                        <h3 class="mill-title">Wheat Flour Mill</h3>
                        <p class="lead mb-4">Our wheat flour mill is equipped with modern machinery that ensures the production of high-quality flour, including our premium "Anubhav Aata" brand. The mill, popularly known as "Khairba Mill", has a capacity of processing several tons of wheat per day, making it one of the largest in the region.</p>
                        <div class="mill-features" data-aos="fade-up" data-aos-delay="300">
                            <div class="feature-row">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Modern Machinery</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>High Capacity</span>
                                </div>
                            </div>
                            <div class="feature-row">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Quality Control</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Nutritional Value</span>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3">We source the finest wheat grains from trusted farmers and subject them to rigorous quality checks before processing. Our milling process preserves the nutritional value of the wheat while ensuring that the flour meets the highest standards of quality.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Puffed Rice Mill -->
        <div class="mill-showcase">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                    <div class="mill-image-container">
                        <img src="<?php echo get_site_image('rice_mill', 'rice-mill.jpg'); ?>" alt="Kissan Agro Foods - Traditional & Modern Puffed Rice Mill in Nepal" class="img-fluid rounded-lg shadow-lg" loading="lazy">
                        <div class="mill-badge">
                            <span>Traditional & Modern</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="mill-content">
                        <div class="mill-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h3 class="mill-title">Puffed Rice Mill</h3>
                        <p class="lead mb-4">Our puffed rice mill combines traditional methods with modern technology to produce light, crispy puffed rice. The mill has been designed to ensure that the rice retains its natural flavor and nutritional value during the puffing process.</p>
                        <div class="mill-features" data-aos="fade-up" data-aos-delay="300">
                            <div class="feature-row">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Traditional Methods</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Modern Technology</span>
                                </div>
                            </div>
                            <div class="feature-row">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Natural Flavor</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Consistent Quality</span>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3">We use high-quality rice grains that are carefully selected and processed to create puffed rice that is perfect for snacks and breakfast cereals. Our puffed rice is known for its consistent quality and taste, available in both plain and flavored varieties.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="values-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Core Values</h2>
            <p class="section-subtitle">Principles that guide our business practices</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="value-card h-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="value-content">
                        <h4>Quality</h4>
                        <p>We are committed to maintaining the highest standards of quality in all our products and processes.</p>
                        <ul class="value-features">
                            <li><i class="fas fa-check"></i> Premium Ingredients</li>
                            <li><i class="fas fa-check"></i> Rigorous Testing</li>
                            <li><i class="fas fa-check"></i> Consistent Standards</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="value-card h-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="value-content">
                        <h4>Integrity</h4>
                        <p>We conduct our business with honesty, transparency, and ethical practices.</p>
                        <ul class="value-features">
                            <li><i class="fas fa-check"></i> Ethical Business</li>
                            <li><i class="fas fa-check"></i> Transparent Practices</li>
                            <li><i class="fas fa-check"></i> Honest Communication</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="value-card h-100" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="value-content">
                        <h4>Customer Focus</h4>
                        <p>We prioritize customer satisfaction and strive to exceed their expectations.</p>
                        <ul class="value-features">
                            <li><i class="fas fa-check"></i> Responsive Service</li>
                            <li><i class="fas fa-check"></i> Customer Feedback</li>
                            <li><i class="fas fa-check"></i> Continuous Improvement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Leadership Team</h2>
            <p class="section-subtitle">Meet the experts behind our success</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="team-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-image-container">
                        <img src="<?php echo get_site_image('team1', 'team1.jpg'); ?>" alt="Sandeep Pandey - CEO & Founder of Kissan Agro Foods" loading="lazy">
                        <div class="team-overlay">
                            <div class="team-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>Sandeep Pandey (Batohi Sir)</h4>
                        <span class="team-position">CEO & Founder</span>
                        <p>With over 20 years of experience in the industry, Sandeep Pandey, affectionately known as "Batohi Sir" in the local community, leads our company with vision and dedication.</p>
                        <div class="team-expertise">
                            <span>Business Strategy</span>
                            <span>Industry Expert</span>
                            <span>Leadership</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="team-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-image-container">
                        <img src="<?php echo get_site_image('team2', 'team2.jpg'); ?>" alt="Abhishek Pandey - Operations Manager at Kissan Agro Foods" loading="lazy">
                        <div class="team-overlay">
                            <div class="team-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>Abhishek Pandey</h4>
                        <span class="team-position">Operations Manager</span>
                        <p>Abhishek oversees our day-to-day operations, ensuring that our mills run efficiently and produce high-quality products.</p>
                        <div class="team-expertise">
                            <span>Operations</span>
                            <span>Process Optimization</span>
                            <span>Team Management</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="team-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-image-container">
                        <img src="<?php echo get_site_image('team3', 'team3.jpg'); ?>" alt="Abhishek Pandey - Quality Control Manager at Kissan Agro Foods" loading="lazy">
                        <div class="team-overlay">
                            <div class="team-social">
                                <a href="#"><i class="fab fa-linkedin"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>Abhishek Pandey</h4>
                        <span class="team-position">Quality Control Manager</span>
                        <p>Abhishek ensures that all our products meet the highest standards of quality and safety before they reach our customers.</p>
                        <div class="team-expertise">
                            <span>Quality Assurance</span>
                            <span>Food Safety</span>
                            <span>Product Testing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

<!-- Structured Data for SEO -->
<script type="application/ld+json">
    <?php echo json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
</script>

<!-- Custom CSS for About Page -->
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

    /* About Image Container */
    .about-image-container {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .about-experience-badge {
        position: absolute;
        bottom: 30px;
        right: -20px;
        background-color: var(--primary-color);
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        text-align: center;
        z-index: 2;
    }

    .about-experience-badge .years {
        font-size: 2.5rem;
        font-weight: 800;
        display: block;
        line-height: 1;
    }

    .about-experience-badge .text {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Mission & Vision Cards */
    .mission-vision-section {
        background-color: #f9f9f9;
        position: relative;
    }

    .mission-card,
    .vision-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        padding: 30px;
        transition: all 0.4s ease;
        position: relative;
        border-top: 5px solid var(--primary-color);
    }

    .vision-card {
        border-top: 5px solid var(--secondary-color);
    }

    .mission-card:hover,
    .vision-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .mission-icon,
    .vision-icon {
        width: 70px;
        height: 70px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        color: var(--primary-color);
        font-size: 2rem;
        transition: all 0.4s ease;
    }

    .vision-icon {
        background-color: rgba(249, 178, 51, 0.1);
        color: var(--secondary-color);
    }

    .mission-card:hover .mission-icon,
    .vision-card:hover .vision-icon {
        transform: rotateY(180deg);
    }

    .mission-content h3,
    .vision-content h3 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: var(--dark-color);
    }

    .mission-features,
    .vision-features {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
    }

    .mission-features li,
    .vision-features li {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }

    .mission-features li i,
    .vision-features li i {
        color: var(--primary-color);
        margin-right: 10px;
    }

    .vision-features li i {
        color: var(--secondary-color);
    }

    /* Mills Section */
    .mills-section {
        background-color: white;
    }

    .mill-showcase {
        margin-bottom: 80px;
    }

    .mill-showcase:last-child {
        margin-bottom: 0;
    }

    .mill-image-container {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .mill-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: var(--primary-color);
        color: white;
        padding: 8px 15px;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .mill-content {
        padding: 20px;
    }

    .mill-icon {
        width: 60px;
        height: 60px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        color: var(--primary-color);
        font-size: 1.8rem;
    }

    .mill-title {
        font-size: 2rem;
        margin-bottom: 15px;
        color: var(--dark-color);
        position: relative;
        padding-bottom: 15px;
    }

    .mill-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--primary-color);
    }

    .mill-features {
        margin: 20px 0;
    }

    .feature-row {
        display: flex;
        margin-bottom: 10px;
    }

    .feature-row .feature-item {
        margin-right: 30px;
    }

    /* Values Section */
    .values-section {
        background-color: #f9f9f9;
    }

    .value-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        padding: 30px;
        transition: all 0.4s ease;
        position: relative;
    }

    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .value-icon {
        width: 70px;
        height: 70px;
        background-color: rgba(78, 125, 52, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        color: var(--primary-color);
        font-size: 2rem;
        transition: all 0.4s ease;
    }

    .value-card:hover .value-icon {
        background-color: var(--primary-color);
        color: white;
        transform: rotateY(180deg);
    }

    .value-content h4 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: var(--dark-color);
    }

    .value-features {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
    }

    .value-features li {
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .value-features li i {
        color: var(--primary-color);
        margin-right: 10px;
    }

    /* Team Section */
    .team-section {
        background-color: white;
    }

    .team-card {
        background-color: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .team-image-container {
        position: relative;
        height: 300px;
        overflow: hidden;
    }

    .team-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.5s ease;
    }

    .team-card:hover .team-image-container img {
        transform: scale(1.1);
    }

    .team-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 100%);
        opacity: 0;
        transition: all 0.4s ease;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 20px;
    }

    .team-card:hover .team-overlay {
        opacity: 1;
    }

    .team-social {
        display: flex;
    }

    .team-social a {
        width: 40px;
        height: 40px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 5px;
        color: var(--primary-color);
        font-size: 1.2rem;
        transition: all 0.3s ease;
        transform: translateY(20px);
        opacity: 0;
    }

    .team-card:hover .team-social a {
        transform: translateY(0);
        opacity: 1;
    }

    .team-card:hover .team-social a:nth-child(1) {
        transition-delay: 0.1s;
    }

    .team-card:hover .team-social a:nth-child(2) {
        transition-delay: 0.2s;
    }

    .team-card:hover .team-social a:nth-child(3) {
        transition-delay: 0.3s;
    }

    .team-social a:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-5px);
    }

    .team-content {
        padding: 25px;
    }

    .team-content h4 {
        font-size: 1.5rem;
        margin-bottom: 5px;
        color: var(--dark-color);
    }

    .team-position {
        display: block;
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 15px;
    }

    .team-expertise {
        display: flex;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .team-expertise span {
        background-color: rgba(78, 125, 52, 0.1);
        color: var(--primary-color);
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .enhanced-header {
            padding: 80px 0;
        }

        .about-experience-badge {
            bottom: 20px;
            right: 20px;
            padding: 15px;
        }

        .about-experience-badge .years {
            font-size: 2rem;
        }

        .mill-title {
            font-size: 1.8rem;
        }

        .team-image-container {
            height: 250px;
        }
    }

    @media (max-width: 767px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .about-experience-badge {
            bottom: 10px;
            right: 10px;
            padding: 10px;
        }

        .about-experience-badge .years {
            font-size: 1.8rem;
        }

        .feature-row {
            flex-direction: column;
        }

        .feature-row .feature-item {
            margin-right: 0;
            margin-bottom: 10px;
        }

        .team-image-container {
            height: 220px;
        }
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>