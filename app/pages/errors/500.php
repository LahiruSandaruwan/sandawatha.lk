<?php
// Set page title
$pageTitle = '500 Server Error - Sandawatha.lk';

// Include header
require_once ROOT_PATH . '/app/views/shared/header.php';
?>

<div class="min-h-screen bg-romantic-gradient flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="relative">
            <!-- Heart Icon -->
            <div class="absolute -top-16 left-1/2 transform -translate-x-1/2">
                <div class="bg-romantic-600 rounded-full p-4 shadow-lg animate-float">
                    <i class="fas fa-heart-broken text-white text-4xl"></i>
                </div>
            </div>

            <!-- Error Content -->
            <div class="mt-8 bg-white rounded-lg shadow-xl p-8">
                <h1 class="text-4xl font-display font-bold text-gray-900">
                    500 Server Error
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Our servers are experiencing a heartache. Please try again later.
                </p>
                <div class="mt-8 space-y-4">
                    <a href="/" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-romantic-600 hover:bg-romantic-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500 hover-lift">
                        <i class="fas fa-home mr-2"></i>
                        Return Home
                    </a>
                    <div class="flex flex-col">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">or</span>
                            </div>
                        </div>
                        <a href="/contact" class="mt-4 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-romantic-600 bg-romantic-50 hover:bg-romantic-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500 hover-lift">
                            <i class="fas fa-envelope mr-2"></i>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Loading Animation -->
            <div class="mt-8 flex justify-center">
                <div class="loading-heart"></div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/shared/footer.php'; ?> 