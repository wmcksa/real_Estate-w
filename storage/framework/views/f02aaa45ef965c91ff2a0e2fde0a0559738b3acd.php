<?php if(isset($templates['about-us'][0]) && $aboutUs = $templates['about-us'][0]): ?>
    <!-- about section -->
    <section class="about-section">
        <div class="container">
            <div class="row gy-5 g-lg-5">
                <div class="col-md-6">
                    <div class="img-wrapper">
                        <div class="img-box img-1">
                            <img src="<?php echo e(getFile(config('location.content.path').$aboutUs->templateMedia()->image2)); ?>" alt=""/>
                        </div>
                        <div class="img-box img-2">
                            <img src="<?php echo e(getFile(config('location.content.path').$aboutUs->templateMedia()->image1)); ?>" alt=""/>
                        </div>
                        <img class="shape" src="<?php echo e(asset($themeTrue.'img/dot-square.png')); ?>" alt=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="header-text mb-4">
                        <h5><?php echo app('translator')->get(optional($aboutUs->description)->title); ?></h5>
                        <h2><?php echo app('translator')->get(optional($aboutUs->description)->sub_title); ?></h2>
                    </div>
                    <div class="text-box">
                        <h4><?php echo app('translator')->get(optional($aboutUs->description)->short_title); ?></h4>
                        <p>
                            <?php echo app('translator')->get(optional($aboutUs->description)->short_description); ?>
                        </p>
                        <a class="btn-custom mt-4 btn text-white" href="<?php echo e(route('about')); ?>"><?php echo app('translator')->get('Discover More'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\real\resources\views/themes/original/sections/about-us.blade.php ENDPATH**/ ?>