<?php if(isset($templates['statistics'][0]) && $statistic = $templates['statistics'][0]): ?>
    <!-- commission section -->
    <section class="commission-section">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="header-text text-center">
                        <h5><?php echo app('translator')->get(optional($statistic->description)->title); ?></h5>
                        <h2><?php echo app('translator')->get(optional($statistic->description)->sub_title); ?></h2>
                    </div>
                </div>
            </div>

            <?php if(isset($contentDetails['statistics'])): ?>
                <?php if(count($contentDetails['statistics']) > 0): ?>
                    <div class="row g-4 g-lg-5">
                        <?php $__currentLoopData = $contentDetails['statistics']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-3 box">
                            <div
                                class="commission-box <?php echo e((session()->get('rtl') == 1) ? 'isRtl': 'noRtl'); ?>"
                                data-aos="zoom-in"
                                data-aos-duration="800"
                                data-aos-anchor-placement="center-bottom">
                                <h3><?php echo app('translator')->get(optional($data->description)->title); ?></h3>
                                <h5><?php echo app('translator')->get(optional($data->description)->description); ?></h5>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
        <div class="shapes">
            <img src="<?php echo e(asset($themeTrue.'img/dot-square.png')); ?>" alt="" class="shape-1"/>
            <img src="<?php echo e(asset($themeTrue.'img/dot-square.png')); ?>" alt="" class="shape-2"/>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\real\resources\views/themes/original/sections/statistics.blade.php ENDPATH**/ ?>