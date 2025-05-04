<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try/catch block to catch any errors in includes
try {
    // Include header and form resubmission prevention
    include 'includes/header.php';
    include 'includes/prevent_resubmission.php';

    // Pre-fill subject if provided in URL
    $subject = isset($_GET['subject']) ? urldecode($_GET['subject']) : '';

    // Form processing function
    function process_contact_form()
    {
        global $conn;

        // Check if database connection is valid
        if (!$conn) {
            error_log("Database connection failed in contact form");
            return false;
        }

        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $subject = sanitize($_POST['subject']);
        $message = sanitize($_POST['message']);

        // Validate form data
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($subject)) {
            $errors[] = 'Subject is required';
        }

        if (empty($message)) {
            $errors[] = 'Message is required';
        }

        // If no errors, save inquiry to database
        if (empty($errors)) {
            $query = "INSERT INTO inquiries (name, email, phone, subject, message)
                    VALUES ('$name', '$email', '$phone', '$subject', '$message')";

            if (mysqli_query($conn, $query)) {
                set_success_message('Your message has been sent. We will get back to you soon!');
                return true;
            } else {
                error_log("MySQL Error in contact form: " . mysqli_error($conn));
                set_error_message('Error: ' . mysqli_error($conn));
                return false;
            }
        } else {
            // Display errors
            foreach ($errors as $error) {
                set_error_message($error);
            }
            return false;
        }
    }

    // Process the form with resubmission prevention
    try {
        process_form_once('contact_form', 'process_contact_form', site_url('contact.php'));
    } catch (Exception $e) {
        error_log("Error in process_form_once: " . $e->getMessage());
        set_error_message("An error occurred while processing your request. Please try again.");
    }
} catch (Exception $e) {
    // Log any errors from includes
    error_log("Fatal error in contact.php: " . $e->getMessage());
    echo "<div class='alert alert-danger'>The contact page encountered an error. Please try again later or contact the administrator.</div>";
    // Try to include a minimal footer if header was loaded
    if (function_exists('site_url')) {
        echo "<p><a href='" . site_url('index.php') . "'>Return to Home</a></p>";
    }
    exit;
}
?>

<!-- Enhanced Page Header with Background Image -->
<div class="page-header enhanced-header" style="background: linear-gradient(rgba(46, 62, 80, 0.7), rgba(46, 62, 80, 0.7)), url('<?php echo get_site_image('contact_header', 'contact-header.jpg'); ?>') center/cover no-repeat;">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 fw-bold text-white mb-3 animate__animated animate__fadeInDown">Contact Us</h1>
            <p class="lead text-white mb-4 animate__animated animate__fadeIn animate__delay-1s">We're here to help with any questions you may have</p>
            <nav aria-label="breadcrumb" class="animate__animated animate__fadeInUp animate__delay-1s">
                <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="<?php echo site_url('index.php'); ?>" class="text-white">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Contact Us</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Enhanced Contact Section -->
<section class="contact-section py-5 my-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-title-wrapper">
                <h2 class="section-title">Get In Touch</h2>
                <div class="section-subtitle">We'd love to hear from you</div>
            </div>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Have questions about our products or services? Fill out the form below and we'll get back to you as soon as possible.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                <div class="contact-form-wrapper">
                    <div class="contact-form p-4 p-md-5 rounded shadow-lg">
                        <form id="contactForm" action="<?php echo site_url('contact.php'); ?>" method="post" autocomplete="on">
                            <!-- Form name and token for resubmission prevention -->
                            <input type="hidden" name="form_name" value="contact_form">
                            <?php form_token_field('contact_form'); ?>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Your name" autocomplete="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Your email" autocomplete="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your phone number (optional)" autocomplete="tel">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" placeholder="Message subject" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label">Message *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your message" autocomplete="off" required></textarea>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="contact-info-container h-100">
                    <div class="contact-info p-4 p-md-5 rounded shadow-lg h-100">
                        <h3 class="mb-4 position-relative">Contact Information</h3>

                        <div class="contact-item mb-4">
                            <div class="icon-box">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Our Location</h5>
                                <p><?php echo get_setting('address', 'Khairba, Mahottari, Nepal'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item mb-4">
                            <div class="icon-box">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Phone Number</h5>
                                <p><?php echo get_setting('contact_phone', '+977 9800000000'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item mb-4">
                            <div class="icon-box">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Email Address</h5>
                                <p><?php echo get_setting('contact_email', 'info@kissanagrofoods.com'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item mb-4">
                            <div class="icon-box">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-text">
                                <h5>Business Hours</h5>
                                <p class="mb-1">Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p class="mb-1">Saturday: 9:00 AM - 1:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h5 class="mb-3">Follow Us</h5>
                            <div class="social-links-enhanced">
                                <a href="<?php echo get_setting('facebook_url', '#'); ?>" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="<?php echo get_setting('instagram_url', '#'); ?>" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="<?php echo get_setting('twitter_url', '#'); ?>" class="social-icon" target="_blank"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Map Section -->
<section class="map-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-title-wrapper">
                <h2 class="section-title">Our Location</h2>
                <div class="section-subtitle">Find us here</div>
            </div>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Visit our mill located near SHREE SHALHES TEMPLE in Khairba, Mahottari, Nepal.</p>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto" data-aos="zoom-in" data-aos-delay="100">
                <div class="map-wrapper shadow-lg rounded overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3565.853065130027!2d85.86466606698231!3d26.65318659474385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ec410074cfa351%3A0x556ec86698c09a97!2sSHREE%20SHALHES%20TEMPLE!5e0!3m2!1sen!2snp!4v1746182885974!5m2!1sen!2snp" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="map-info-box bg-white p-4 shadow-sm rounded mt-4 text-center" data-aos="fade-up" data-aos-delay="200">
                    <p class="mb-0"><i class="fas fa-map-marker-alt text-primary me-2"></i> <strong>Exact Location:</strong> MV37+9JJ, Pipra 45700, Nepal, near SHREE SHALHES TEMPLE</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced FAQ Section -->
<section class="faq-section py-5 my-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-title-wrapper">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <div class="section-subtitle">Common questions about our products and services</div>
            </div>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Find answers to the most common questions about our flour mill and products.</p>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="faq-container" data-aos="fade-up" data-aos-delay="100">
                    <div class="accordion custom-accordion" id="faqAccordion">
                        <div class="accordion-item shadow-sm mb-3 border-0 rounded">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <i class="fas fa-question-circle me-2 text-primary"></i>
                                    What types of wheat flour do you offer?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">We offer a variety of wheat flour products, including all-purpose flour, whole wheat flour, and semolina. Each type is carefully processed to ensure the highest quality and is suitable for different culinary needs.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item shadow-sm mb-3 border-0 rounded">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <i class="fas fa-question-circle me-2 text-primary"></i>
                                    Do you offer bulk orders for businesses?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">Yes, we offer bulk orders for businesses such as bakeries, restaurants, and retailers. Please contact us directly to discuss your specific requirements and to get a customized quote.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item shadow-sm mb-3 border-0 rounded">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <i class="fas fa-question-circle me-2 text-primary"></i>
                                    What is the shelf life of your products?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">The shelf life of our products varies depending on the type. Generally, our wheat flour products have a shelf life of 6-8 months, while our puffed rice products can last for 3-4 months when stored in a cool, dry place in an airtight container.</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item shadow-sm mb-3 border-0 rounded">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <i class="fas fa-question-circle me-2 text-primary"></i>
                                    Do you deliver to all areas?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">We currently deliver to all areas within Mahottari and Dhanusha districts. For specific delivery inquiries outside these areas, please contact our customer service team with your location details.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="200">
                    <p class="mb-4">Still have questions? Feel free to contact us directly.</p>
                    <a href="#contactForm" class="btn btn-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </a>
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

        // Smooth scroll for contact button
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>

<!-- Custom CSS for Contact Page -->
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
        margin-bottom: 20px;
        position: relative;
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
        color: var(--primary-color);
        font-weight: 500;
        margin-bottom: 10px;
    }

    /* Contact Form Styles */
    .contact-form {
        background-color: white;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .contact-form:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .contact-form .form-label {
        font-weight: 600;
        color: var(--dark-color);
    }

    .contact-form .form-control {
        padding: 12px 15px;
        border: 1px solid #e1e1e1;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .contact-form .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(78, 125, 52, 0.25);
    }

    .contact-form .input-group-text {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    /* Contact Info Styles */
    .contact-info {
        background-color: var(--primary-color);
        color: white;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .contact-info:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .contact-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .contact-info h3:after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--secondary-color);
    }

    .contact-item {
        display: flex;
        margin-bottom: 25px;
    }

    .contact-item .icon-box {
        width: 50px;
        height: 50px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .contact-item:hover .icon-box {
        background-color: var(--secondary-color);
        transform: rotateY(180deg);
    }

    .contact-text h5 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: white;
    }

    .contact-text p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0;
    }

    /* Social Links */
    .social-links-enhanced {
        display: flex;
        gap: 15px;
    }

    .social-links-enhanced .social-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .social-links-enhanced .social-icon:hover {
        background-color: var(--secondary-color);
        transform: translateY(-5px);
    }

    /* Map Styles */
    .map-wrapper {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .map-wrapper:hover {
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2) !important;
    }

    .map-info-box {
        border-left: 4px solid var(--primary-color);
    }

    /* FAQ Styles */
    .custom-accordion .accordion-item {
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .custom-accordion .accordion-item:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .custom-accordion .accordion-button {
        padding: 20px;
        font-weight: 600;
        color: var(--dark-color);
        background-color: white;
    }

    .custom-accordion .accordion-button:not(.collapsed) {
        color: var(--primary-color);
        background-color: rgba(78, 125, 52, 0.05);
    }

    .custom-accordion .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(78, 125, 52, 0.1);
    }

    .custom-accordion .accordion-button::after {
        background-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .custom-accordion .accordion-body {
        padding: 20px;
        background-color: white;
    }

    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .enhanced-header {
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.2rem;
        }

        .contact-form,
        .contact-info {
            padding: 30px !important;
        }
    }

    @media (max-width: 767px) {
        .enhanced-header {
            padding: 60px 0;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .section-subtitle {
            font-size: 1rem;
        }

        .contact-form,
        .contact-info {
            padding: 25px !important;
        }

        .contact-item .icon-box {
            width: 40px;
            height: 40px;
        }

        .map-wrapper iframe {
            height: 350px;
        }

        .custom-accordion .accordion-button {
            padding: 15px;
        }
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>