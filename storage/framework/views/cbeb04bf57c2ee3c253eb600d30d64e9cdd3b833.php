<div class="property-box">
    <div class="img-box">
        <img class="img-fluid"
             src="<?php echo e(getFile(config('location.propertyThumbnail.path').$property->thumbnail)); ?>"
             alt="<?php echo app('translator')->get('property thumbnail'); ?>"/>
        <div class="content">
            <?php if($property->is_invest_type == 0): ?>
                <div class="tag"><?php echo app('translator')->get('Fixed Invest'); ?></div>
                <?php if($property->is_installment == 1): ?>
                    <div class="tag2"><?php echo app('translator')->get('Installment'); ?></div>
                <?php else: ?>
                    <div class="tag2"><?php echo app('translator')->get('No Installment'); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="tag"><?php echo app('translator')->get('Invest Range'); ?></div>
                <?php if($property->is_installment == 1): ?>
                    <div class="tag2"><?php echo app('translator')->get('Installment'); ?></div>
                <?php else: ?>
                    <div class="tag2"><?php echo app('translator')->get('No Installment'); ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="badges">
                <button class="save wishList" type="button" id="<?php echo e($key); ?>" data-property="<?php echo e($property->id); ?>">
                    <?php if($property->get_favourite_count > 0): ?>
                        <i class="fas fa-heart save<?php echo e($key); ?>"></i>
                    <?php else: ?>
                        <i class="fal fa-heart save<?php echo e($key); ?>"></i>
                    <?php endif; ?>
                </button>
            </div>


            <h4 class="price"><?php echo e($property->investmentAmount); ?></h4>
            <?php if($property->is_available_funding == 1 && $property->available_funding == 0): ?>
                <span class="invest-completed"><i class="fad fa-check-circle"></i> <?php echo app('translator')->get(' Completed'); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="text-box">
        <div class="review">
            <?php echo $__env->make($theme.'partials.propertyReview', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <a class="title"
           href="<?php echo e(route('propertyDetails',[slug(optional($property->details)->property_title), $property->id])); ?>"><?php echo e(\Str::limit(optional($property->details)->property_title, 30)); ?></a>
        <p class="address">
            <i class="fas fa-map-marker-alt"></i>
            <?php echo app('translator')->get(optional($property->getAddress->details)->title); ?>
        </p>

        <div class="aminities">
            <?php $__currentLoopData = $property->limitamenity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span><i class="<?php echo e($amenity->icon); ?>"></i><?php echo e(optional($amenity->details)->title); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="invest-btns d-flex justify-content-between">
            <button type="button" class="investNow"
                    <?php echo e($property->rud()['upcomingProperties'] ? 'disabled' : ''); ?>

                    data-route="<?php echo e(route('user.invest-property', $property->id)); ?>"
                    data-property="<?php echo e($property); ?>"
                    data-expired="<?php echo e(dateTime($property->expire_date)); ?>"
                    data-symbol="<?php echo e($basic->currency_symbol); ?>"
                    data-currency="<?php echo e($basic->currency); ?>">
                <?php if($property->rud()['upcomingProperties']): ?>
                    <span class="text-info"><?php echo app('translator')->get('Coming in '); ?> <span
                            class="text-success"><?php echo app('translator')->get($property->rud()['difference']->d.'D '. $property->rud()['difference']->h.'H '. $property->rud()['difference']->i.'M '); ?></span></span>
                <?php else: ?>
                    <?php echo app('translator')->get('Invest Now'); ?>
                <?php endif; ?>
            </button>

            <a href="<?php echo e(route('contact')); ?>">
                <?php echo app('translator')->get('Contact Us'); ?>
            </a>
        </div>

        <div class="plan d-flex justify-content-between">
            <div>
                <?php if($property->profit_type == 1): ?>
                    <h5><?php echo e((int)@$property->profit); ?>% (<?php echo app('translator')->get('Fixed'); ?>)</h5>
                <?php else: ?>
                    <h5><?php echo e(config('basic.currency_symbol')); ?><?php echo e((int)@$property->profit); ?>

                        (<?php echo app('translator')->get('Fixed'); ?>)</h5>
                <?php endif; ?>
                <span><?php echo app('translator')->get('Profit Range'); ?></span>
            </div>

            <div>
                <?php if($property->is_return_type == 1): ?>
                    <h5><?php echo app('translator')->get('Lifetime'); ?></h5>
                <?php else: ?>
                    <h5><?php echo e(optional($property->managetime)->time); ?> <?php echo app('translator')->get(optional($property->managetime)->time_type); ?></h5>
                <?php endif; ?>
                <span><?php echo app('translator')->get('Return Interval'); ?></span>
            </div>
            <div>
                <h5><?php echo e(@$property->is_capital_back == 1 ? 'Yes' : 'No'); ?></h5>
                <span><?php echo app('translator')->get('Capital back'); ?></span>
            </div>
        </div>
    </div>
</div>





<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/partials/propertyBox.blade.php ENDPATH**/ ?>