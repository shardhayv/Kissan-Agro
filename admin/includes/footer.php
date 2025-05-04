    </div>
    <!-- End of Main Content -->

    <!-- Admin Footer -->
    <footer class="admin-footer mt-auto py-3 bg-light">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">&copy; <?php echo date('Y'); ?> Kissan Agro Foods</span>
                </div>
                <div>
                    <a href="<?php echo site_url('index.php'); ?>" class="text-decoration-none me-3" target="_blank">
                        <i class="fas fa-globe me-1"></i> View Website
                    </a>
                    <a href="<?php echo site_url('admin/settings.php'); ?>" class="text-decoration-none">
                        <i class="fas fa-cog me-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin JS -->
    <script src="<?php echo site_url('assets/js/admin.js'); ?>"></script>

    <!-- Image preview script -->
    <script>
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>

    <!-- CSRF Protection Script -->
    <script>
        // Add CSRF token to all forms
        document.addEventListener('DOMContentLoaded', function() {
            // Get CSRF token
            const csrfToken = '<?php echo generate_csrf_token(); ?>';

            // Add to all forms
            document.querySelectorAll('form').forEach(form => {
                // Skip forms that already have a CSRF token
                if (!form.querySelector('input[name="csrf_token"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'csrf_token';
                    input.value = csrfToken;
                    form.appendChild(input);
                }
            });

            // Add confirmation to delete actions
            document.querySelectorAll('form button[name*="delete"], form input[name*="delete"]').forEach(button => {
                if (!button.hasAttribute('data-confirm-set')) {
                    button.setAttribute('data-confirm-set', 'true');
                    button.addEventListener('click', function(e) {
                        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
        });
    </script>
    </body>

    </html>