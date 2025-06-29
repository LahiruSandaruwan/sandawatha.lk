<?php
/**
 * Testimonials Section
 * Success stories from happy couples
 */
?>
<section class="py-24 bg-white relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-romantic-50/50 to-transparent"></div>
        <div class="absolute top-0 left-0 transform -translate-x-1/2 -translate-y-1/2">
            <div class="w-96 h-96 bg-romantic-100 rounded-full opacity-20 blur-3xl"></div>
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section header -->
        <div class="text-center max-w-3xl mx-auto">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Success Stories
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Real couples who found their perfect match on Sandawatha.lk
            </p>
        </div>

        <!-- Testimonials grid -->
        <div class="mt-20 grid gap-8 lg:grid-cols-3 sm:grid-cols-2">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1" data-aos="fade-up">
                <!-- Couple photo -->
                <div class="aspect-w-16 aspect-h-9 relative">
                    <img class="object-cover w-full h-full" 
                         src="<?php echo asset(str_replace('/assets/', '', $testimonial['photo'])); ?>" 
                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                         onerror="this.onerror=null; this.src='<?php echo asset('images/placeholder.svg'); ?>';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($testimonial['name']); ?></h3>
                        <p class="text-sm text-romantic-100"><?php echo htmlspecialchars($testimonial['location']); ?></p>
                    </div>
                </div>

                <!-- Testimonial content -->
                <div class="p-6">
                    <!-- Rating -->
                    <div class="flex items-center mb-4">
                        <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                        <svg class="w-5 h-5 text-romantic-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <?php endfor; ?>
                    </div>

                    <!-- Quote -->
                    <div class="relative">
                        <svg class="absolute top-0 left-0 transform -translate-x-6 -translate-y-8 h-16 w-16 text-romantic-100" fill="currentColor" viewBox="0 0 32 32" aria-hidden="true">
                            <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/>
                        </svg>
                        <p class="relative text-base text-gray-500">
                            <?php echo htmlspecialchars($testimonial['message']); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Stats -->
        <div class="mt-20">
            <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">Happy Couples</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">10,000+</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">Success Rate</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">89%</dd>
                </div>
                <div class="mx-auto flex max-w-xs flex-col gap-y-4">
                    <dt class="text-base leading-7 text-gray-600">Years of Service</dt>
                    <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">15+</dd>
                </div>
            </dl>
        </div>

        <!-- CTA -->
        <div class="mt-16 text-center">
            <a href="/success-stories" class="inline-flex items-center text-romantic-600 font-medium hover:text-romantic-700">
                Read More Success Stories
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section> 