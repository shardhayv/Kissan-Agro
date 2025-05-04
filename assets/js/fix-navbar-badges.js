/**
 * Fix Navbar Badges
 * 
 * This script ensures that product badges don't interfere with the navbar
 */
document.addEventListener('DOMContentLoaded', function() {
    // Remove any stray badges that might have been added to the navbar
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        // Skip the cart link which should have a badge
        if (link.textContent.trim().includes('Cart')) {
            return;
        }
        
        // Find any badges that aren't part of the intended structure
        const badges = link.querySelectorAll('.badge:not(.rounded-pill)');
        badges.forEach(badge => {
            badge.remove();
        });
    });
});
