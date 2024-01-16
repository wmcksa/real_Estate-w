<?php if(isset($templates['testimonial'][0]) && $testimonial = $templates['testimonial'][0]): ?>
    <!-- testimonial section -->
    <section class="testimonial-section">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-6">
                    <div class="testimonial-wrapper">
                        <div class="header-text">
                            <h5><?php echo app('translator')->get(optional($testimonial->description)->title); ?></h5>
                            <h3><?php echo app('translator')->get(optional($testimonial->description)->sub_title); ?></h3>
                            <div class="quote">
                                <img src="<?php echo e(asset($themeTrue.'img/quote-2.png')); ?> " alt=""/>
                            </div>
                        </div>
                        <?php if(isset($contentDetails['testimonial'])): ?>
                            <div class="<?php echo e((session()->get('rtl') == 1) ? 'testimonials-rtl': 'testimonials'); ?> owl-carousel">
                                <?php $__currentLoopData = $contentDetails['testimonial']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="review-box">
                                    <div class="text">
                                        <p>
                                            <?php echo app('translator')->get(optional($data->description)->description); ?>
                                        </p>

                                        <div class="top">
                                            <h4><?php echo app('translator')->get(optional($data->description)->name); ?></h4>
                                            <span class="title"
                                            ><a class="organization" href=""><?php echo app('translator')->get(optional($data->description)->designation); ?></a></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="client-img">
                        <img src="<?php echo e(getFile(config('location.content.path').$testimonial->templateMedia()->image)); ?>" alt="" class="img-fluid"/>

                        <img class="shape" src="<?php echo e(asset($themeTrue.'img/dot-square.png')); ?>" alt="<?php echo app('translator')->get('not found'); ?>"/>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php $__env->startPush('css-lib'); ?>
    <link rel="stylesheet" href="<?php echo e(asset($themeTrue.'css/owl.carousel.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset($themeTrue.'css/owl.theme.default.min.css')); ?>"/>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('extra-js'); ?>
    <!-- fancybox slider -->
    <script src="<?php echo e(asset($themeTrue.'js/fancybox.umd.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset($themeTrue.'js/carousel.js')); ?>"></script>
    <script>
        'use strict'
        $(document).ready(function () {
            // Customize Fancybox
            Fancybox.bind('[data-fancybox="gallery"]', {
                Carousel: {
                    on: {
                        change: (that) => {
                            mainCarousel.slideTo(mainCarousel.findPageForSlide(that.page), {
                                friction: 0,
                            });
                        },
                    },
                },
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\real\resources\views/themes/original/sections/testimonial.blade.php ENDPATH**/ ?>