<?php
/**
 * Call to Action Section
 * Final section to encourage user registration
 */
?>
<section class="relative py-24 bg-romantic-600">
    <!-- Background pattern -->
    <div class="absolute inset-0 z-0">
        <svg class="absolute inset-0 h-full w-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hearts" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path fill="rgba(255,255,255,0.1)" d="M20 35.6c-1.2 0-2.4-.4-3.4-1.2l-12.7-9.4c-2.4-1.8-3.9-4.7-3.9-7.8 0-5.4 4.4-9.8 9.8-9.8 3.2 0 6.2 1.6 8 4.1 1.8-2.5 4.8-4.1 8-4.1 5.4 0 9.8 4.4 9.8 9.8 0 3.1-1.5 6-3.9 7.8l-12.7 9.4c-1 .8-2.2 1.2-3.4 1.2z"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hearts)"/>
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
            <!-- Left column: Content -->
            <div class="max-w-md mx-auto lg:max-w-none lg:mx-0 text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    Ready to Find Your Soulmate?
                </h2>
                <p class="mt-4 text-xl text-romantic-100">
                    Join thousands of happy couples who found their perfect match on Sandawatha.lk. Start your journey today!
                </p>
                <div class="mt-8 space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                    <a href="/register" class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-romantic-600 bg-white hover:bg-romantic-50 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out transform hover:scale-105">
                        Create Free Account
                    </a>
                    <a href="/about" class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 border-2 border-white text-base font-medium rounded-xl text-white hover:bg-romantic-500 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Right column: Features -->
            <div class="mt-12 lg:mt-0">
                <dl class="space-y-10">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-romantic-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-lg leading-6 font-medium text-white">100% Verified Profiles</dt>
                            <dd class="mt-2 text-base text-romantic-100">All profiles are manually verified with proper documentation.</dd>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-romantic-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-lg leading-6 font-medium text-white">Horoscope Matching</dt>
                            <dd class="mt-2 text-base text-romantic-100">Advanced astrological compatibility analysis.</dd>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-xl bg-romantic-500 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <dt class="text-lg leading-6 font-medium text-white">Privacy First</dt>
                            <dd class="mt-2 text-base text-romantic-100">Your privacy and security are our top priority.</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section> 