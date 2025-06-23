<?php
// Testimonials data (passed from parent)
$testimonials = isset($testimonials) ? $testimonials : [
    [
        'name' => 'Priya & Ashan',
        'photo' => 'https://images.unsplash.com/photo-1621784563330-caee0b138a00?auto=format&fit=crop&w=400&q=80',
        'placeholder' => 'https://images.unsplash.com/photo-1621784563330-caee0b138a00?auto=format&fit=crop&w=50&q=20&blur=10',
        'message' => 'We found each other on Sandawatha.lk and our horoscopes matched perfectly! Now happily married for 2 years.',
        'rating' => 5,
        'location' => 'Colombo'
    ],
    [
        'name' => 'Malini & Dinesh',
        'photo' => 'https://images.unsplash.com/photo-1623069923731-45e5d19f6f0f?auto=format&fit=crop&w=400&q=80',
        'placeholder' => 'https://images.unsplash.com/photo-1623069923731-45e5d19f6f0f?auto=format&fit=crop&w=50&q=20&blur=10',
        'message' => 'The AI matching system introduced us, and it was like magic from our first meeting. Getting married next month!',
        'rating' => 5,
        'location' => 'Kandy'
    ],
    [
        'name' => 'Kumari & Rajitha',
        'photo' => 'https://images.unsplash.com/photo-1621784562877-01a79135c0c5?auto=format&fit=crop&w=400&q=80',
        'placeholder' => 'https://images.unsplash.com/photo-1621784562877-01a79135c0c5?auto=format&fit=crop&w=50&q=20&blur=10',
        'message' => 'Thank you Sandawatha for helping us find true love. The verification process gave us peace of mind.',
        'rating' => 5,
        'location' => 'Galle'
    ]
];
?>

<section class="py-24 bg-romantic-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl font-display font-bold text-gray-900 sm:text-4xl">
                Success Stories
            </h2>
            <p class="mt-4 text-xl text-gray-600">
                Hear from couples who found their perfect match on Sandawatha.lk
            </p>
        </div>

        <!-- Testimonials Slider -->
        <div class="swiper testimonials-slider">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide p-4">
                        <div class="bg-white rounded-2xl shadow-sm p-8 h-full">
                            <!-- Testimonial Header -->
                            <div class="flex items-center mb-6">
                                <div class="relative w-16 h-16 rounded-full overflow-hidden mr-4">
                                    <!-- Placeholder image while loading -->
                                    <img src="<?php echo htmlspecialchars($testimonial['placeholder']); ?>" 
                                         class="absolute inset-0 w-full h-full object-cover blur-lg" 
                                         alt="">
                                    <!-- Main image -->
                                    <img src="<?php echo htmlspecialchars($testimonial['photo']); ?>" 
                                         class="absolute inset-0 w-full h-full object-cover" 
                                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                                         loading="lazy">
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($testimonial['name']); ?>
                                    </h3>
                                    <p class="text-gray-600">
                                        <?php echo htmlspecialchars($testimonial['location']); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Rating Stars -->
                            <div class="flex items-center mb-4">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                <?php endfor; ?>
                            </div>

                            <!-- Testimonial Message -->
                            <blockquote class="text-gray-600 italic">
                                "<?php echo htmlspecialchars($testimonial['message']); ?>"
                            </blockquote>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    new Swiper('.testimonials-slider', {
        slidesPerView: 1,
        spaceBetween: 32,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
});</script> 