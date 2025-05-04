/**
 * Prevent Double Form Submission
 * 
 * This script prevents users from submitting a form multiple times.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all forms on the page
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Skip forms with the 'no-double-submit' class
        if (form.classList.contains('no-double-submit')) {
            return;
        }
        
        // Add submit event listener
        form.addEventListener('submit', function(e) {
            // Check if form is already submitting
            if (form.classList.contains('is-submitting')) {
                // Prevent the submission
                e.preventDefault();
                return false;
            }
            
            // Mark form as submitting
            form.classList.add('is-submitting');
            
            // Disable all submit buttons
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(button => {
                // Save original text
                if (button.tagName === 'BUTTON') {
                    button.dataset.originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                }
                
                // Disable button
                button.disabled = true;
            });
            
            // Allow form to submit
            return true;
        });
    });
});
