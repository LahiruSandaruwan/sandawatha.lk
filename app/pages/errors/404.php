<?php
// Set page title
$pageTitle = '404 - Page Not Found';

// Include header
require_once ROOT_PATH . '/app/views/shared/header.php';
?>

<div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <!-- Error Illustration -->
        <div class="mb-8">
            <img src="<?php echo asset('images/patterns/404.svg'); ?>" 
                 alt="404 Illustration" 
                 class="w-64 h-64 mx-auto">
        </div>
        
        <!-- Error Message -->
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Page Not Found
        </h1>
        <p class="text-lg text-gray-600 mb-8">
            The page you're looking for doesn't exist or has been moved.
        </p>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" 
               class="px-6 py-3 bg-romantic-600 text-white rounded-lg hover:bg-romantic-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500">
                Go Home
            </a>
            <button onclick="history.back()" 
                    class="px-6 py-3 bg-white text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Go Back
            </button>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/shared/footer.php'; ?> 