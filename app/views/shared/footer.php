<?php
// Social media links
$socialLinks = [
    'facebook' => 'https://facebook.com/sandawatha',
    'instagram' => 'https://instagram.com/sandawatha',
    'twitter' => 'https://twitter.com/sandawatha',
    'youtube' => 'https://youtube.com/sandawatha'
];

// Quick links
$quickLinks = [
    'About Us' => '/about',
    'Blog' => '/blog',
    'Contact' => '/contact',
    'FAQ' => '/faq'
];

// Legal links
$legalLinks = [
    'Privacy Policy' => '/privacy',
    'Terms of Service' => '/terms',
    'Refund Policy' => '/refund',
    'Safety Tips' => '/safety'
];
?>

<footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Company Info -->
            <div>
                <div class="flex items-center mb-6">
                    <img src="<?php echo asset('images/logo.svg'); ?>" alt="Sandawatha.lk" class="h-8 w-auto" onerror="this.style.display='none';">
                    <span class="ml-2 text-xl font-semibold">Sandawatha.lk</span>
                </div>
                <p class="text-gray-400 mb-6">
                    Sri Lanka's premier matrimonial platform combining tradition with technology.
                </p>
                <!-- Social Links -->
                <div class="flex space-x-4">
                    <?php foreach ($socialLinks as $platform => $url): ?>
                    <a href="<?php echo htmlspecialchars($url); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="text-gray-400 hover:text-white transition-colors duration-300"
                       aria-label="Follow us on <?php echo ucfirst($platform); ?>">
                        <i class="fab fa-<?php echo $platform; ?> text-xl"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                <ul class="space-y-4">
                    <?php foreach ($quickLinks as $label => $url): ?>
                    <li>
                        <a href="<?php echo $url; ?>" 
                           class="text-gray-400 hover:text-white transition-colors duration-300">
                            <?php echo $label; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-lg font-semibold mb-6">Legal</h3>
                <ul class="space-y-4">
                    <?php foreach ($legalLinks as $label => $url): ?>
                    <li>
                        <a href="<?php echo $url; ?>" 
                           class="text-gray-400 hover:text-white transition-colors duration-300">
                            <?php echo $label; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h3 class="text-lg font-semibold mb-6">Stay Updated</h3>
                <p class="text-gray-400 mb-4">
                    Subscribe to our newsletter for updates and success stories.
                </p>
                <form action="/subscribe" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               placeholder="Enter your email"
                               required
                               class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-romantic-500">
                    </div>
                    <button type="submit" 
                            class="w-full px-4 py-3 bg-romantic-600 text-white rounded-lg hover:bg-romantic-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-romantic-500">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-12 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> Sandawatha.lk. All rights reserved.
                </p>
                <div class="mt-4 md:mt-0">
                    <img src="<?php echo asset('images/payment/visa.svg'); ?>" alt="Visa" class="h-8 inline-block" onerror="this.style.display='none';">
                    <img src="<?php echo asset('images/payment/mastercard.svg'); ?>" alt="Mastercard" class="h-8 inline-block ml-2" onerror="this.style.display='none';">
                    <img src="<?php echo asset('images/payment/paypal.svg'); ?>" alt="PayPal" class="h-8 inline-block ml-2" onerror="this.style.display='none';">
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Newsletter form handling
document.querySelector('form[action="/subscribe"]')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const response = await fetch('/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.querySelector('[name="csrf_token"]').value
            },
            body: JSON.stringify({
                email: this.querySelector('[name="email"]').value
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Show success message
            const input = this.querySelector('[name="email"]');
            input.value = '';
            input.classList.add('bg-green-800', 'text-white');
            input.placeholder = 'Successfully subscribed!';
            
            // Reset after 3 seconds
            setTimeout(() => {
                input.classList.remove('bg-green-800', 'text-white');
                input.placeholder = 'Enter your email';
            }, 3000);
        } else {
            throw new Error(data.message || 'Failed to subscribe');
        }
    } catch (error) {
        console.error('Newsletter subscription error:', error);
        alert(error.message || 'Failed to subscribe. Please try again later.');
    }
});
</script> 