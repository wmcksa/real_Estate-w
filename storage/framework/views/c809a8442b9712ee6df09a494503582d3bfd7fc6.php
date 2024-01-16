
<style>
    .banner-section {
        background: linear-gradient( 170deg, rgb(255,255,255, .93), rgb(255, 255, 255, .2)), url(<?php echo e(getFile(config('location.logo.path').'banner.jpg')); ?>);
    }
</style>

<?php if(!request()->routeIs('home')): ?>
    <!-- banner section -->
    <section class="banner-section">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3><?php echo $__env->yieldContent('title'); ?></h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><?php echo app('translator')->get('Home'); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo $__env->yieldContent('title'); ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\real\resources\views/themes/original/partials/banner.blade.php ENDPATH**/ ?>