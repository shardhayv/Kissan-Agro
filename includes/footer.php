    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5>Kissan Agro Foods</h5>
                    <p><?php echo get_setting('site_description', 'Quality wheat flour and puffed rice products'); ?></p>
                    <p>We are committed to providing high-quality wheat flour and puffed rice products to our customers throughout Mahottari and Dhanusha districts.</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo site_url('index.php'); ?>"><i class="fas fa-home me-2"></i>Home</a></li>
                        <li><a href="<?php echo site_url('about.php'); ?>"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                        <li><a href="<?php echo site_url('products.php'); ?>"><i class="fas fa-box me-2"></i>Products</a></li>
                        <li><a href="<?php echo site_url('cart.php'); ?>"><i class="fas fa-shopping-cart me-2"></i>Cart</a></li>
                        <li><a href="<?php echo site_url('track-order.php'); ?>"><i class="fas fa-truck me-2"></i>Track Order</a></li>
                        <li><a href="<?php echo site_url('contact.php'); ?>"><i class="fas fa-envelope me-2"></i>Contact Us</a></li>
                        <li><a href="<?php echo site_url('terms.php'); ?>"><i class="fas fa-file-contract me-2"></i>Terms & Conditions</a></li>
                        <li><a href="<?php echo site_url('privacy.php'); ?>"><i class="fas fa-user-shield me-2"></i>Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Contact Us</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo get_setting('address', 'Khairba, Mahottari, Nepal'); ?></p>
                        <p><i class="fas fa-phone me-2"></i> <?php echo get_setting('contact_phone', '+977 9800000000'); ?></p>
                        <p><i class="fas fa-envelope me-2"></i> <?php echo get_setting('contact_email', 'info@kissanagrofoods.com'); ?></p>
                    </address>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Follow Us</h5>
                    <p>Stay connected with us on social media for updates, promotions, and more.</p>
                    <div class="social-links">
                        <a href="<?php echo get_setting('facebook_url', '#'); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo get_setting('instagram_url', '#'); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo get_setting('twitter_url', '#'); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="copyright">&copy; <?php echo date('Y'); ?> Kissan Agro Foods. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="<?php echo asset_url('js/main.js'); ?>"></script>
    <script src="<?php echo asset_url('js/dynamic-hero-bg.js'); ?>"></script>
    <script src="<?php echo asset_url('js/fix-navbar-badges.js'); ?>"></script>
    <script src="<?php echo asset_url('js/enhanced-effects.js'); ?>"></script>
    <script src="<?php echo asset_url('js/prevent-double-submit.js'); ?>"></script>
    </body>

    </html>