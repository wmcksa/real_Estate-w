<!-- navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(url('/')); ?>"> <img src="<?php echo e(getFile(config('location.logoIcon.path').'logo.png')); ?>" alt="<?php echo e(config('basic.site_title')); ?>" /></a>
        <button
            class="navbar-toggler p-0"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="far fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(Request::routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>"><?php echo app('translator')->get('Home'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(Request::routeIs('about') ? 'active' : ''); ?>" href="<?php echo e(route('about')); ?>"><?php echo app('translator')->get('About Us'); ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(Request::routeIs('property') ? 'active' : ''); ?>" href="<?php echo e(route('property')); ?>"><?php echo app('translator')->get('Property'); ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(Request::routeIs('blog') ? 'active' : ''); ?>" href="<?php echo e(route('blog')); ?>"><?php echo app('translator')->get('Blogs'); ?></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(Request::routeIs('faq') ? 'active' : ''); ?>" href="<?php echo e(route('faq')); ?>"><?php echo app('translator')->get('FAQ'); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(Request::routeIs('contact') ? 'active' : ''); ?>" href="<?php echo e(route('contact')); ?>"><?php echo app('translator')->get('Contact'); ?></a>
                </li>

                <?php if(auth()->guard()->guest()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Request::routeIs('login') ? 'active' : ''); ?>" href="<?php echo e(route('login')); ?>"><?php echo app('translator')->get('LOGIN'); ?></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('user.home')); ?>"><?php echo app('translator')->get('Dashboard'); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>
<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/partials/topbar.blade.php ENDPATH**/ ?>