<?php
// Get total user counts from parent
$totalUsers = isset($totalUsers) ? number_format($totalUsers) : '10,000+';
?>

<section class="relative py-24 bg-romantic-600 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute inset-0 h-full w-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hearts" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <path d="M10 3.22l-.61-.6a5.5 5.5 0 0 0-7.78 7.77L10 18.78l8.39-8.4a5.5 5.5 0 0 0-7.78-7.77l-.61.61z" fill="currentColor"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hearts)"/>
        </svg>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Main Content -->
            <h2 class="text-3xl font-display font-bold text-white sm:text-4xl">
                Join <?php echo $totalUsers; ?> Singles Looking for Love
            </h2>
            <p class="mt-4 text-xl text-romantic-100">
                Start your journey to finding your perfect match today. Create your profile in minutes.
            </p>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="/register.php" 
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-romantic-600 bg-white rounded-full hover:bg-romantic-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transform hover:scale-105 transition-all">
                    Create Free Profile
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <a href="/horoscope/match" 
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white border-2 border-white/20 rounded-full hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/50 transition-all">
                    Try Horoscope Match
                </a>
            </div>

            <!-- Trust Badges -->
            <div class="mt-12 grid grid-cols-2 gap-8 md:grid-cols-4">
                <div class="flex flex-col items-center">
                    <div class="text-white/90">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-romantic-100">Verified Profiles</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="text-white/90">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-romantic-100">Privacy Protected</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="text-white/90">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-romantic-100">Quality Matches</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="text-white/90">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-romantic-100">24/7 Support</p>
                </div>
            </div>
        </div>
    </div>
</section> 