/**
 * Dynamic Hero Background
 * 
 * This script dynamically sets the hero background image from the database.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the hero element
    const heroElement = document.querySelector('.hero');
    
    // If hero element exists
    if (heroElement) {
        // Get the data attribute with the image URL
        const bgImage = heroElement.getAttribute('data-bg-image');
        
        // If the data attribute exists, set the background image
        if (bgImage) {
            heroElement.style.background = `linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('${bgImage}')`;
            heroElement.style.backgroundSize = 'cover';
            heroElement.style.backgroundPosition = 'center';
        }
    }
});
