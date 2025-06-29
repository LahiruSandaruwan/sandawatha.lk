/**
 * Main JavaScript File
 * Contains common functionality used across the site
 */

// Main JavaScript for Sandawatha.lk

document.addEventListener('DOMContentLoaded', function() {
    console.log('Sandawatha.lk - Main JS Loaded');
    
    // Initialize any components that need setup
    initializeComponents();
    
    // Handle flash messages
    setupFlashMessages();
    
    // Setup CSRF token for AJAX requests
    setupCSRFToken();
});

// Initialize UI components
function initializeComponents() {
    // Initialize AOS (Animate on Scroll) if it exists
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    }
    
    // Hide loading overlay when page is ready
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.style.opacity = '0';
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 300);
    }
}

// Setup flash messages
function setupFlashMessages() {
    // Close flash message when close button is clicked
    document.querySelectorAll('.close-flash').forEach(button => {
        button.addEventListener('click', function() {
            const flashMessage = this.closest('.flash-message');
            flashMessage.style.opacity = '0';
            setTimeout(() => {
                flashMessage.remove();
            }, 300);
        });
    });
    
    // Auto-hide flash messages after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.flash-message').forEach(message => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        });
    }, 5000);
}

// CSRF Token Setup for AJAX Requests
function setupCSRFToken() {
    // For jQuery AJAX requests
    if (typeof $ !== 'undefined') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
    }
    
    // For fetch API requests
    window.fetchWithCSRF = function(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!options.headers) {
            options.headers = {};
        }
        
        options.headers['X-CSRF-TOKEN'] = csrfToken;
        
        return fetch(url, options);
    };
}