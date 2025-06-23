<?php
// Features data
$features = [
    [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>',
        'title' => 'Verified Profiles',
        'description' => 'Every profile is manually verified with proper documentation for your safety and trust.'
    ],
    [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>',
        'title' => 'Horoscope Matching',
        'description' => 'Advanced astrological compatibility analysis based on traditional Sri Lankan horoscope matching.'
    ],
    [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
        'title' => 'AI-Powered Matches',
        'description' => 'Smart matching algorithm that learns your preferences to suggest the most compatible partners.'
    ],
    [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>',
        'title' => 'Private Messaging',
        'description' => 'Secure and private communication system to connect with potential matches safely.'
    ],
    [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'title' => 'Real-time Notifications',
        'description' => 'Stay updated with instant notifications about profile views, messages, and matches.'
    ],
    [
        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
        'title' => 'Family Profiles',
        'description' => 'Create and manage profiles for family members with proper verification and privacy.'
    ]
];
?>

<section id="features" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl font-display font-bold text-gray-900 sm:text-4xl">
                Why Choose Sandawatha.lk?
            </h2>
            <p class="mt-4 text-xl text-gray-600">
                We combine traditional values with modern technology to help you find your perfect life partner.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($features as $feature): ?>
                <div class="relative group">
                    <div class="h-full p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                        <div class="flex items-center justify-center w-12 h-12 bg-romantic-50 text-romantic-600 rounded-lg mb-4 group-hover:scale-110 transition-transform duration-300">
                            <?php echo $feature['icon']; ?>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($feature['title']); ?>
                        </h3>
                        <p class="text-gray-600">
                            <?php echo htmlspecialchars($feature['description']); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section> 