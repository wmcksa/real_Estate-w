<?php if(count($popularBlogs) > 0): ?>
    <section class="blog-section">
        <div class="container">
            <?php if(isset($templates['blog'][0]) && $blog = $templates['blog'][0]): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center">
                            <h5><?php echo app('translator')->get(optional($blog->description)->title); ?></h5>
                            <h2><?php echo app('translator')->get(optional($blog->description)->sub_title); ?></h2>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row g-4 g-lg-5">
                <?php $__currentLoopData = $popularBlogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 col-md-6">
                        <div
                            class="blog-box"
                            data-aos="fade-up"
                            data-aos-duration="1000"
                            data-aos-anchor-placement="center-bottom"
                        >
                            <div class="img-box">
                                <img class="img-fluid"
                                     src="<?php echo e(getFile(config('location.blog.path'). $blog->image)); ?>"
                                     alt="<?php echo app('translator')->get('not found'); ?>"/>
                            </div>
                            <div class="text-box">
                                <div class="date">
                                    <span><?php echo e(dateTime($blog->created_at, 'M d, Y')); ?></span>
                                </div>
                                <div class="author">
                                    <span><i class="fal fa-user-circle"></i> <?php echo app('translator')->get(optional($blog->details)->author); ?> </span>
                                </div>
                                <a href="<?php echo e(route('blogDetails',[slug(optional($blog->details)->title), $blog->id])); ?>" class="title"><?php echo e(\Illuminate\Support\Str::limit(optional($blog->details)->title, 80)); ?></a>
                                <p><?php echo e(\Illuminate\Support\Str::limit(optional($blog->details)->description, 80)); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\real\resources\views/themes/original/sections/blog.blade.php ENDPATH**/ ?>