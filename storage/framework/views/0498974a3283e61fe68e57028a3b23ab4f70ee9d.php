<?php if(isset($templates['latest-property'][0]) && $latestProperty = $templates['latest-property'][0]): ?>
    <!-- latest property -->
    <?php if(count($latestProperties) > 0): ?>
        <section class="latest-property">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center">
                            <h5><?php echo app('translator')->get(optional($latestProperty->description)->title); ?></h5>
                            <h2><?php echo app('translator')->get(optional($latestProperty->description)->sub_title); ?></h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php $__currentLoopData = $latestProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-6 mb-4">
                                <?php echo $__env->make($theme.'partials.propertyBox', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/sections/latest-property.blade.php ENDPATH**/ ?>