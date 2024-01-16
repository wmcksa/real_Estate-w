<?php $__env->startSection('title','404'); ?>

<?php $__env->startSection('content'); ?>
    <section class="error-page wow fadeInUp mb-5 pb-5">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-md-12 text-center my-5 text-box">
                    <img src="<?php echo e(asset($themeTrue.'img/error-404.png')); ?>" alt="<?php echo app('translator')->get('not found'); ?>" />
                    <h1 class="golden-text opps d-block"><?php echo e(trans('Opps!')); ?></h1>
                    <div class="sub_title golden-text mb-4 lead"><?php echo e(trans("We can't seem to find the page you are looking for")); ?></div>
                    <a class="gold-btn btn-custom" href="<?php echo e(url('/')); ?>" ><?php echo app('translator')->get('Back To Home'); ?></a>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($theme.'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/errors/404.blade.php ENDPATH**/ ?>