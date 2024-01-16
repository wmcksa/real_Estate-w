<?php if(isset($templates['hero'][0]) && $hero = $templates['hero'][0]): ?>
    <!-- home section -->
    <section class="home-section">
        <div class="overlay h-100">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-xl-7">
                        <div class="text-box">
                            <h4> <?php echo app('translator')->get($hero['description']->title); ?> </h4>
                            <h2><?php echo app('translator')->get($hero['description']->sub_title); ?> </h2>
                            <h2 class="primary_color"><?php echo app('translator')->get($hero['description']->another_sub_title); ?></h2>
                            <p><?php echo app('translator')->get($hero['description']->short_description); ?></p>
                            <a class="btn-custom mt-4 btn text-white" href="<?php echo e(route('property')); ?>"><?php echo app('translator')->get($hero['description']->button_name); ?></a>
                        </div>
                    </div>
                    <div class="col-xl-5 d-none d-xl-block">
                        <div class="img-main-wrapper">
                            <div class="img-wrapper">
                                <div class="img-box img-1">
                                    <img src="<?php echo e(getFile(config('location.content.path').$hero->templateMedia()->image1)); ?>" alt="" />
                                </div>
                                <div class="img-box img-2">
                                    <img src="<?php echo e(getFile(config('location.content.path').$hero->templateMedia()->image2)); ?>" alt="" />
                                </div>
                                <div class="img-box img-3">
                                    <img src="<?php echo e(getFile(config('location.content.path').$hero->templateMedia()->image3)); ?>" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/partials/heroBanner.blade.php ENDPATH**/ ?>