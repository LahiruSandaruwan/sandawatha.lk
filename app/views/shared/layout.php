<?php
/**
 * Main Layout Template
 * This file serves as the base template for all pages
 */

// Start output buffering for the layout
ob_start();
?>
<!DOCTYPE html>
<html lang="<?php echo isset($currentLang) ? $currentLang : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Sandawatha.lk' : 'Sandawatha.lk - Find Your Perfect Match'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'romantic': {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AOS Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo asset('css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/custom.css'); ?>">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
</head>
<body class="antialiased bg-gray-50">
    <!-- Loading Overlay -->
    <div class="loading-overlay fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-300">
        <div class="loading-spinner w-10 h-10 border-4 border-romantic-200 border-t-romantic-600 rounded-full animate-spin"></div>
    </div>

    <!-- Include Navigation -->
    <?php require_once __DIR__ . '/navbar.php'; ?>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash-message fixed top-4 right-4 z-50 max-w-md" role="alert">
            <div class="bg-white rounded-lg shadow-lg border-l-4 <?php
                echo match ($_SESSION['flash_type'] ?? 'info') {
                    'success' => 'border-green-500',
                    'error' => 'border-red-500',
                    'warning' => 'border-yellow-500',
                    default => 'border-blue-500'
                };
            ?> p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <?php
                        $icon = match ($_SESSION['flash_type'] ?? 'info') {
                            'success' => '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                            'error' => '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                            'warning' => '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                            default => '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
                        };
                        echo $icon;
                        ?>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" 
                                    class="close-flash inline-flex rounded-md p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-gray-600">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main id="main" class="min-h-screen">
        <?php
        // Output the content
        if (isset($content) && is_string($content)) {
            echo $content;
        } else {
            error_log('Content variable is not set or is not a string in layout.php');
            echo '<div class="p-8 text-center text-gray-600">Content not available.</div>';
        }
        ?>
    </main>

    <!-- Include Footer -->
    <?php require_once __DIR__ . '/footer.php'; ?>

    <!-- Core JavaScript -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
    
    <script>
        // Hide loading overlay when page is ready
        window.addEventListener('load', function() {
            document.querySelector('.loading-overlay').style.opacity = '0';
            setTimeout(() => {
                document.querySelector('.loading-overlay').style.display = 'none';
            }, 300);
        });
        
        // Flash message handling
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
        
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    </script>
</body>
</html>
<?php
// Flush the output buffer
ob_end_flush();
?> 