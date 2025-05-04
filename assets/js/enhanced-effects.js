/**
 * Enhanced Effects for Kissan Agro Foods
 * This script adds additional animations and effects to the website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add parallax effect to hero section
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        window.addEventListener('scroll', function() {
            const scrollPosition = window.pageYOffset;
            // Only apply parallax effect if not on mobile
            if (window.innerWidth > 768) {
                heroSection.style.backgroundPosition = `center ${scrollPosition * 0.4}px`;
            }
        });
    }

    // Add scroll reveal animations
    const revealElements = document.querySelectorAll('.section-title, .about-img, .mill-card, .product-card, .feature-box, .contact-info-wrapper, .map-wrapper');
    
    if (revealElements.length > 0) {
        // Simple reveal animation on scroll
        const revealOnScroll = function() {
            for (let i = 0; i < revealElements.length; i++) {
                const elementTop = revealElements[i].getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    revealElements[i].classList.add('revealed');
                }
            }
        };
        
        // Add revealed class for CSS animations
        window.addEventListener('scroll', revealOnScroll);
        
        // Add CSS class for initial state
        revealElements.forEach(element => {
            element.classList.add('reveal-element');
        });
        
        // Trigger on initial load
        revealOnScroll();
    }

    // Add hover effects to product cards
    const productCards = document.querySelectorAll('.product-card.enhanced');
    if (productCards.length > 0) {
        productCards.forEach(card => {
            // Create shine effect element
            const shineEffect = document.createElement('div');
            shineEffect.classList.add('shine-effect');
            card.appendChild(shineEffect);
            
            // Add mousemove event for shine effect
            card.addEventListener('mousemove', function(e) {
                const x = e.clientX - card.getBoundingClientRect().left;
                const y = e.clientY - card.getBoundingClientRect().top;
                
                const posX = (x / card.offsetWidth) * 100;
                const posY = (y / card.offsetHeight) * 100;
                
                shineEffect.style.background = `radial-gradient(circle at ${posX}% ${posY}%, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 60%)`;
            });
            
            // Remove shine effect on mouse leave
            card.addEventListener('mouseleave', function() {
                shineEffect.style.background = 'none';
            });
        });
    }
});

// Add CSS for reveal animations
document.head.insertAdjacentHTML('beforeend', `
<style>
    .reveal-element {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .revealed {
        opacity: 1;
        transform: translateY(0);
    }
    
    .shine-effect {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 3;
        pointer-events: none;
        border-radius: inherit;
    }
</style>
`);
