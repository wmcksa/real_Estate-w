<?php $__env->startSection('title','500'); ?>


<?php $__env->startSection('content'); ?>
<section class="error-page wow fadeInUp mb-5 pb-5">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-md-12 text-center my-5">
                <span class="golden-text opps d-block"><?php echo app('translator')->get('Internal Server Error'); ?></span>
                <div class="sub_title golden-text mb-4 lead"><?php echo app('translator')->get("The server encountered an internal error misconfiguration and was unable to complate your request. Please contact the server administrator."); ?></div>
                <a class="gold-btn" href="<?php echo e(url('/')); ?>" ><?php echo app('translator')->get('Back To Home'); ?></a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($theme.'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/errors/500.blade.php ENDPATH**/ ?>