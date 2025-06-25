<?php
/**
 * Hero Section
 * Main landing section for the home page
 */

// Define image paths
$heroImage = '/assets/images/hero-image.jpg';
$placeholderImage = '/assets/images/placeholder.svg';

// Check if hero image exists, otherwise use placeholder
$imagePath = file_exists(PUBLIC_PATH . $heroImage) ? $heroImage : $placeholderImage;
?>
<section class="relative min-h-screen bg-gradient-to-b from-romantic-50 to-white overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-romantic-100/50"></div>
        <div class="absolute inset-y-0 right-0 w-1/2 bg-romantic-600/5 transform skew-x-12"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-32 lg:pt-32">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Left column: Text content -->
            <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                <div class="mt-24">
                    <!-- Trust badge -->
                    <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-romantic-100 text-romantic-800 mb-8">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">Sri Lanka's Most Trusted Platform</span>
                    </div>

                    <!-- Main heading -->
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
                        <span class="block xl:inline">Find Your</span>
                        <span class="block text-romantic-600 xl:inline"> Perfect Match</span>
                    </h1>

                    <!-- Subheading -->
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Join Sri Lanka's most trusted matrimonial platform. Connect with verified profiles, match horoscopes, and find your soulmate.
                    </p>

                    <!-- Feature badges -->
                    <div class="mt-8 space-y-4 sm:space-y-0 sm:flex sm:space-x-4 lg:justify-start sm:justify-center">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-2 h-5 w-5 text-romantic-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified Profiles
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-2 h-5 w-5 text-romantic-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            Horoscope Matching
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-2 h-5 w-5 text-romantic-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                            Privacy Focused
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="mt-8 sm:flex sm:justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="/register" class="flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-romantic-600 hover:bg-romantic-700 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out transform hover:scale-105">
                            Get Started Free
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                        <a href="/about" class="flex items-center justify-center px-8 py-3 border-2 border-romantic-200 text-base font-medium rounded-xl text-romantic-600 bg-white hover:bg-romantic-50 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right column: Image -->
            <div class="mt-16 sm:mt-24 relative lg:mt-0 lg:col-span-6">
                <div class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md">
                    <div class="relative block w-full bg-white rounded-lg overflow-hidden">
                        <img class="w-full" src="<?php echo asset($imagePath); ?>" 
                             alt="Happy couple finding love"
                             onerror="this.onerror=null; this.src='<?php echo asset($placeholderImage); ?>';">
                        <div class="absolute inset-0 w-full h-full flex items-center justify-center">
                            <div class="absolute inset-0 bg-romantic-600 mix-blend-multiply opacity-10"></div>
                        </div>
                    </div>
                </div>

                <!-- Floating stats card -->
                <div class="absolute bottom-0 right-0 transform translate-y-1/2 translate-x-1/4">
                    <div class="bg-white rounded-2xl shadow-xl p-6 backdrop-blur-sm bg-white/90">
                        <div class="flex items-center space-x-4">
                            <div class="flex -space-x-2">
                                <img class="w-10 h-10 rounded-full border-2 border-white" src="<?php echo asset('/assets/images/testimonials/couple1.jpg'); ?>" alt="">
                                <img class="w-10 h-10 rounded-full border-2 border-white" src="<?php echo asset('/assets/images/testimonials/couple2.jpg'); ?>" alt="">
                                <img class="w-10 h-10 rounded-full border-2 border-white" src="<?php echo asset('/assets/images/testimonials/couple3.jpg'); ?>" alt="">
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">10,000+ Happy Couples</div>
                                <div class="text-sm text-gray-500">Found their match here</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave decoration -->
    <div class="absolute bottom-0 inset-x-0">
        <svg class="w-full text-white" viewBox="0 0 1440 100" fill="currentColor" preserveAspectRatio="none">
            <path d="M0,50 C280,84 760,84 1440,50 L1440,100 L0,100 Z"></path>
        </svg>
    </div>
</section> 