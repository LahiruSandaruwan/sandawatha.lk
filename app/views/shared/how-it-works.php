<?php
// Steps data
$steps = [
    [
        'number' => '01',
        'title' => 'Create Your Profile',
        'description' => 'Sign up and create your detailed profile with photos, personal information, and preferences.',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'
    ],
    [
        'number' => '02',
        'title' => 'Verify Your Identity',
        'description' => 'Complete our secure verification process to ensure trust and safety in our community.',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>'
    ],
    [
        'number' => '03',
        'title' => 'Find Matches',
        'description' => 'Browse profiles, use our AI matching system, or try horoscope matching to find compatible partners.',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>'
    ],
    [
        'number' => '04',
        'title' => 'Connect Safely',
        'description' => 'Start conversations through our secure messaging system and get to know your matches better.',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>'
    ]
];
?>

<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl font-display font-bold text-gray-900 sm:text-4xl">
                How Sandawatha.lk Works
            </h2>
            <p class="mt-4 text-xl text-gray-600">
                Follow these simple steps to begin your journey to finding your perfect match.
            </p>
        </div>

        <!-- Steps -->
        <div class="relative">
            <!-- Connecting Line -->
            <div class="hidden lg:block absolute top-1/2 left-12 right-12 h-0.5 bg-romantic-100 -translate-y-1/2"></div>

            <!-- Steps Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 relative">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="relative flex flex-col items-center text-center group">
                        <!-- Step Number -->
                        <div class="w-12 h-12 rounded-full bg-romantic-600 text-white flex items-center justify-center text-xl font-bold mb-6 relative z-10 group-hover:scale-110 transition-transform duration-300">
                            <?php echo htmlspecialchars($step['number']); ?>
                        </div>

                        <!-- Icon -->
                        <div class="w-12 h-12 rounded-lg bg-romantic-50 text-romantic-600 flex items-center justify-center mb-6">
                            <?php echo $step['icon']; ?>
                        </div>

                        <!-- Content -->
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($step['title']); ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo htmlspecialchars($step['description']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action Button -->
        <div class="text-center mt-16">
            <a href="/register.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-romantic-600 hover:bg-romantic-700 md:py-4 md:text-lg md:px-10 transition-colors">
                Get Started Now
            </a>
        </div>
    </div>
</section> 