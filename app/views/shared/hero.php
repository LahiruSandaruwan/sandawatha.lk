<?php
// Get total user counts from parent
$totalUsers = isset($totalUsers) ? number_format($totalUsers) : '10,000+';
$verifiedUsers = isset($verifiedUsers) ? number_format($verifiedUsers) : '8,000+';
$totalConnections = isset($totalConnections) ? number_format($totalConnections) : '5,000+';
?>

<section class="relative min-h-screen flex items-center justify-center py-32 overflow-hidden bg-gray-900">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/60 z-10"></div>
        <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1920&q=80" 
             alt="Background" 
             class="w-full h-full object-cover"
             loading="eager">
    </div>

    <!-- Decorative Pattern -->
    <div class="absolute inset-0 bg-repeat opacity-5 z-20 hero-pattern"></div>

    <!-- Content -->
    <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="space-y-8">
            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-bold text-white leading-tight">
                Find Your Perfect Match<br>
                <span class="text-romantic-400">in Sri Lanka</span>
            </h1>

            <!-- Subheading -->
            <p class="max-w-2xl mx-auto text-xl text-gray-300">
                Join the most trusted matrimonial platform connecting Sri Lankan singles worldwide. 
                Start your journey to find true love today.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6">
                <a href="/register.php" 
                   class="inline-flex items-center justify-center px-8 py-4 text-lg font-medium text-white bg-romantic-600 rounded-full hover:bg-romantic-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500 transform hover:scale-105 transition-all">
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

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-16">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white"><?php echo $totalUsers; ?></div>
                    <div class="text-gray-400 mt-1">Active Members</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white"><?php echo $verifiedUsers; ?></div>
                    <div class="text-gray-400 mt-1">Verified Profiles</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white"><?php echo $totalConnections; ?></div>
                    <div class="text-gray-400 mt-1">Successful Matches</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-30 animate-bounce">
        <a href="#features" class="text-white/60 hover:text-white transition-colors">
            <span class="sr-only">Scroll down</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </a>
    </div>
</section> 