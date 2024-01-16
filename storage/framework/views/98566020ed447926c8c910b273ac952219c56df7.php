<!-- footer section -->
<footer class="footer-section">
    <div class="overlay">
        <div class="container">
            <div class="row gy-5 gy-lg-0">
                <div class="col-lg-3 col-md-6 pe-lg-5">
                    <div class="footer-box">
                        <a class="navbar-brand" href="<?php echo e(url('/')); ?>"> <img
                                src="<?php echo e(getFile(config('location.logoIcon.path').'logo.png')); ?>"
                                alt="<?php echo e(config('basic.site_title')); ?>"/></a>
                        <?php if(isset($contactUs['contact-us'][0]) && $contact = $contactUs['contact-us'][0]): ?>
                            <p class="company-bio">
                                <?php echo app('translator')->get(strip_tags(@$contact->description->footer_short_details)); ?>
                            </p>
                        <?php endif; ?>
                        <?php if(isset($contentDetails['social'])): ?>
                            <div class="social-links">
                                <?php $__currentLoopData = $contentDetails['social']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(@$data->content->contentMedia->description->link); ?>" target="_blank" class="facebook">
                                        <i class="<?php echo e(@$data->content->contentMedia->description->icon); ?>"></i>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box">
                        <h4><?php echo e(trans('Useful Links')); ?></h4>
                        <ul>
                            <li>
                                <a href="<?php echo e(route('home')); ?>"><?php echo app('translator')->get('Home'); ?></a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('about')); ?>"><?php echo app('translator')->get('About'); ?></a>
                            </li>

                            <li>
                                <a href="<?php echo e(route('contact')); ?>"><?php echo app('translator')->get('Contact'); ?></a>
                            </li>

                            <?php if(isset($contentDetails['support'])): ?>
                                <?php $__currentLoopData = $contentDetails['support']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('getLink', [slug($data->description->title), $data->content_id])); ?>"><?php echo app('translator')->get(optional($data->description)->title); ?></a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box">
                        <h4><?php echo app('translator')->get('Contact us'); ?></h4>
                        <ul>
                            <li><i class="fal fa-map-marker-alt"></i> <span><?php echo app('translator')->get(@$contact->description->address); ?></span></li>
                            <li><i class="fal fa-envelope"></i> <span><?php echo app('translator')->get(@$contact->description->email); ?></span></li>
                            <li><i class="fal fa-phone-alt"></i> <span><?php echo app('translator')->get(@$contact->description->phone); ?></span></li>
                        </ul>
                    </div>
                </div>
                <?php if(isset($templates['news-letter'][0]) && $newsLetter = $templates['news-letter'][0]): ?>
                    <div class="col-lg-3 col-md-6">
                    <div class="footer-box">
                        <h4><?php echo app('translator')->get(@$newsLetter->description->title); ?></h4>
                        <form action="<?php echo e(route('subscribe')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <div class="input-group">
                                <input type="email" name="email" class="form-control" placeholder="<?php echo app('translator')->get('Email Address'); ?>">
                                <button type="submit"><i class="fal fa-paper-plane" aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="d-flex flex-wrap copyright justify-content-between">
                <div>
                    <span> <?php echo app('translator')->get('Copyright'); ?> &copy; <?php echo e(date('Y')); ?> <a href="<?php echo e(url('/')); ?>"><?php echo app('translator')->get($basic->site_title); ?></a> <?php echo app('translator')->get('All Rights Reserved'); ?> </span>
                </div>
                <?php
                    $languageArray = json_decode($languages, true);
                ?>
                <div class="language <?php echo e((session()->get('rtl') == 1) ? 'text-md-start': 'text-md-end'); ?>">
                    <?php $__currentLoopData = $languageArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('language',$key)); ?>" class="language"><?php echo e($lang); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\laragon\www\real\resources\views/themes/original/partials/footer.blade.php ENDPATH**/ ?>