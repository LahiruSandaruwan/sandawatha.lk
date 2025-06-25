<?php
session_start();
require_once '../config/database.php';

// SEO Meta Data
$pageTitle = "Contact Us - Sandawatha.lk";
$pageDescription = "Get in touch with Sandawatha.lk's support team. We're here to help you with any questions about our matrimony platform.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    
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
                    },
                    fontFamily: {
                        'display': ['Playfair Display', 'serif'],
                        'body': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-romantic-50/30">
    <!-- Include Navigation -->
    <?php require_once '../app/views/shared/navbar.php'; ?>

    <main class="pt-24 pb-16">
        <!-- Contact Header -->
        <div class="relative bg-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl tracking-tight font-display font-bold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Get in Touch</span>
                        <span class="block text-romantic-600 text-3xl mt-3">We're Here to Help</span>
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                        Have questions about finding your perfect match? Our team is ready to assist you.
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Form Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-white shadow-xl rounded-lg">
                <div class="grid grid-cols-1 lg:grid-cols-3">
                    <!-- Contact Information -->
                    <div class="relative overflow-hidden bg-romantic-600 py-10 px-6 sm:px-10 xl:p-12 rounded-t-lg lg:rounded-l-lg lg:rounded-tr-none">
                        <div class="absolute inset-0 pointer-events-none">
                            <svg class="absolute inset-0 h-full w-full" aria-hidden="true">
                                <pattern id="pattern-squares" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <path fill="rgba(255, 255, 255, 0.1)" d="M0 0h2v2H0z"/>
                                </pattern>
                                <rect width="100%" height="100%" fill="url(#pattern-squares)"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-white">Contact Information</h3>
                        <p class="mt-6 text-base text-romantic-50 max-w-3xl">
                            Our support team is available Monday through Friday, 9am to 6pm Sri Lanka time.
                        </p>
                        <dl class="mt-8 space-y-6">
                            <dt><span class="sr-only">Phone number</span></dt>
                            <dd class="flex text-base text-romantic-50">
                                <i class="fas fa-phone-alt flex-shrink-0 h-6 w-6 text-romantic-200"></i>
                                <span class="ml-3">+94 11 123 4567</span>
                            </dd>
                            <dt><span class="sr-only">Email</span></dt>
                            <dd class="flex text-base text-romantic-50">
                                <i class="fas fa-envelope flex-shrink-0 h-6 w-6 text-romantic-200"></i>
                                <span class="ml-3">support@sandawatha.lk</span>
                            </dd>
                            <dt><span class="sr-only">Address</span></dt>
                            <dd class="flex text-base text-romantic-50">
                                <i class="fas fa-location-dot flex-shrink-0 h-6 w-6 text-romantic-200"></i>
                                <span class="ml-3">Colombo, Sri Lanka</span>
                            </dd>
                        </dl>
                        <ul role="list" class="mt-8 flex space-x-12">
                            <li>
                                <a class="text-romantic-200 hover:text-romantic-100" href="#">
                                    <span class="sr-only">Facebook</span>
                                    <i class="fab fa-facebook text-2xl"></i>
                                </a>
                            </li>
                            <li>
                                <a class="text-romantic-200 hover:text-romantic-100" href="#">
                                    <span class="sr-only">Instagram</span>
                                    <i class="fab fa-instagram text-2xl"></i>
                                </a>
                            </li>
                            <li>
                                <a class="text-romantic-200 hover:text-romantic-100" href="#">
                                    <span class="sr-only">Twitter</span>
                                    <i class="fab fa-twitter text-2xl"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Form -->
                    <div class="py-10 px-6 sm:px-10 lg:col-span-2 xl:p-12">
                        <h3 class="text-2xl font-display font-medium text-gray-900">Send us a message</h3>
                        <form id="contactForm" class="mt-6 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-900">Name <span class="text-romantic-600">*</span></label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" class="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-romantic-500 focus:border-romantic-500 border-gray-300 rounded-md">
                                    <p class="mt-2 hidden text-sm text-red-600" id="name-error"></p>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-900">Email <span class="text-romantic-600">*</span></label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" class="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-romantic-500 focus:border-romantic-500 border-gray-300 rounded-md">
                                    <p class="mt-2 hidden text-sm text-red-600" id="email-error"></p>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="subject" class="block text-sm font-medium text-gray-900">Subject</label>
                                <div class="mt-1">
                                    <input type="text" name="subject" id="subject" class="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-romantic-500 focus:border-romantic-500 border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="message" class="block text-sm font-medium text-gray-900">Message <span class="text-romantic-600">*</span></label>
                                <div class="mt-1">
                                    <textarea id="message" name="message" rows="4" class="py-3 px-4 block w-full shadow-sm text-gray-900 focus:ring-romantic-500 focus:border-romantic-500 border border-gray-300 rounded-md"></textarea>
                                    <p class="mt-2 hidden text-sm text-red-600" id="message-error"></p>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-romantic-600 hover:bg-romantic-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500">
                                    <span id="submitText">Send Message</span>
                                    <span id="submitSpinner" class="hidden ml-2">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        </form>

                        <!-- Success Message (Hidden by default) -->
                        <div id="successMessage" class="hidden mt-6 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Message Sent Successfully</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Thank you for reaching out! We'll get back to you as soon as possible.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer -->
    <?php require_once '../app/views/shared/footer.php'; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Form Validation and Submission -->
    <script>
        $(document).ready(function() {
            const form = $('#contactForm');
            const successMessage = $('#successMessage');
            const submitText = $('#submitText');
            const submitSpinner = $('#submitSpinner');

            // Helper function to show error
            function showError(field, message) {
                const errorElement = $(`#${field}-error}`);
                errorElement.text(message).removeClass('hidden');
                $(`#${field}`).addClass('border-red-500');
            }

            // Helper function to clear error
            function clearError(field) {
                $(`#${field}-error`).addClass('hidden');
                $(`#${field}`).removeClass('border-red-500');
            }

            // Form validation
            form.on('submit', function(e) {
                e.preventDefault();
                let isValid = true;

                // Clear previous errors
                ['name', 'email', 'message'].forEach(field => clearError(field));

                // Validate name
                const name = $('#name').val().trim();
                if (!name) {
                    showError('name', 'Name is required');
                    isValid = false;
                }

                // Validate email
                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    showError('email', 'Email is required');
                    isValid = false;
                } else if (!emailRegex.test(email)) {
                    showError('email', 'Please enter a valid email address');
                    isValid = false;
                }

                // Validate message
                const message = $('#message').val().trim();
                if (!message) {
                    showError('message', 'Message is required');
                    isValid = false;
                }

                if (isValid) {
                    // Show loading state
                    submitText.text('Sending...');
                    submitSpinner.removeClass('hidden');

                    // Simulate form submission (replace with actual AJAX call)
                    setTimeout(function() {
                        // Hide form
                        form[0].reset();
                        form.addClass('hidden');
                        
                        // Show success message
                        successMessage.removeClass('hidden');
                        
                        // Reset button state
                        submitText.text('Send Message');
                        submitSpinner.addClass('hidden');
                    }, 1500);
                }
            });

            // Clear errors on input
            ['name', 'email', 'message'].forEach(field => {
                $(`#${field}`).on('input', function() {
                    clearError(field);
                });
            });
        });
    </script>
</body>
</html> 