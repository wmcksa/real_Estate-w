<?php $__env->startSection('title',__('Login')); ?>

<?php $__env->startSection('content'); ?>
    <!-- login section -->
    <section class="login-section">
        <div class="container h-100">
            <div class="row h-100 justify-content-center">
                <div class="col-lg-7">
                    <div class="img-box">
                        <img src="<?php echo e(asset($themeTrue.'img/login.png')); ?>" alt="<?php echo app('translator')->get('login-image'); ?>" class="img-fluid" />
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-wrapper d-flex align-items-center h-100">
                        <div class="form-box">
                            <form action="<?php echo e(route('login')); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h4><?php echo app('translator')->get('Login To Your Account'); ?></h4>
                                    </div>
                                    <div class="input-box col-12">
                                        <input type="text"
                                               name="username"
                                               class="form-control"
                                               placeholder="<?php echo app('translator')->get('Email Or Username'); ?>"/>
                                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger float-left"><?php echo app('translator')->get($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger float-left"><?php echo app('translator')->get($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="input-box col-12">
                                        <input type="hidden"
                                               name="timezone"
                                               class="form-control timezone"
                                               placeholder="<?php echo app('translator')->get('timezone'); ?>"/>
                                    </div>

                                    <div class="input-box col-12">
                                        <input type="password"
                                               name="password"
                                               class="form-control"
                                               placeholder="<?php echo app('translator')->get('Password'); ?>"/>
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger mt-1"><?php echo app('translator')->get($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <?php if(basicControl()->reCaptcha_status_login): ?>
                                        <div class="box mb-4 form-group">
                                            <?php echo NoCaptcha::renderJs(session()->get('trans')); ?>

                                            <?php echo NoCaptcha::display($basic->theme == 'original' ? ['data-theme' => 'dark'] : []); ?>

                                            <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger mt-1"><?php echo app('translator')->get($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    <?php endif; ?>


                                    <div class="col-12">
                                        <div class="links">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="remember"
                                                       <?php echo e(old('remember') ? 'checked' : ''); ?>

                                                       id="flexCheckDefault"/>
                                                <label class="form-check-label" for="flexCheckDefault"> <?php echo app('translator')->get('Remember me'); ?> </label>
                                            </div>
                                            <a href="<?php echo e(route('password.request')); ?>"><?php echo app('translator')->get('Forget password?'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn-custom" type="submit"><?php echo app('translator')->get('Sign in'); ?></button>
                                <div class="bottom">
                                    <?php echo app('translator')->get("Don't have an account?"); ?>

                                    <a href="<?php echo e(route('register')); ?>"><?php echo app('translator')->get('Create account'); ?></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        'use strict'
        $(document).ready(function (){
            $('.timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make($theme.'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/auth/login.blade.php ENDPATH**/ ?>