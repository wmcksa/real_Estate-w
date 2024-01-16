<?php $__env->startSection('title', trans('property')); ?>

<?php $__env->startSection('content'); ?>
    <!-- shop section -->
    <section class="shop-section">
        <div class="container">
            <div class="row g-lg-5">
                <div class="col-lg-3">
                    <form action="<?php echo e(route('property')); ?>" method="get">
                        <div class="filter-area">
                            <div class="filter-box">
                                <h5><?php echo app('translator')->get('Search Property'); ?></h5>
                                <div class="row g-3">
                                    <div class="input-box col-12">
                                        <input type="text" class="form-control" name="name"
                                               value="<?php echo e(old('name', request()->name)); ?>" autocomplete="off"
                                               placeholder="<?php echo app('translator')->get('What are you looking for?'); ?>"/>
                                    </div>

                                    <div class="input-box col-12">
                                        <select class="js-example-basic-single form-control" name="location">
                                            <option selected disabled><?php echo app('translator')->get('Select Location'); ?></option>
                                            <?php $__currentLoopData = $allAddress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($address->id); ?>"
                                                        <?php if(request()->location == $address->id): ?> selected <?php endif; ?>><?php echo app('translator')->get(optional($address->details)->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- PRICE RANGE -->
                            <div class="filter-box">
                                <h5><?php echo app('translator')->get('Filter By Available Funding'); ?></h5>
                                <div class="input-box">
                                    <input type="text" class="js-range-slider" name="my_range" value=""/>
                                    <label for="customRange1" class="form-label mt-3"> <span class="highlight"><?php echo e(config('basic.currency_symbol') . $min); ?> - <?php echo e(config('basic.currency_symbol') . $max); ?></span>
                                    </label>
                                </div>
                            </div>

                            <!-- SEARCH BY Amenities -->
                            <div class="filter-box">
                                <h5><?php echo app('translator')->get('Amenities'); ?></h5>
                                <div class="check-box searchAmenities">
                                    <?php $__currentLoopData = $allAmenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="amenity_id[]"
                                                   <?php if(isset(request()->amenity_id)): ?>
                                                   <?php $__currentLoopData = request()->amenity_id; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                   <?php if($data == $amenity->id): ?> checked <?php endif; ?>
                                                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                   <?php endif; ?>
                                                   value="<?php echo e($amenity->id); ?>" id="amenity<?php echo e($amenity->id); ?>"/>
                                            <label class="form-check-label" for="amenity<?php echo e($amenity->id); ?>"><i
                                                    class="<?php echo e($amenity->icon); ?>"
                                                    aria-hidden="true"></i> <?php echo app('translator')->get(optional($amenity->details)->title); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </div>
                            </div>
                            <div class="filter-box">
                                <h5><?php echo app('translator')->get('Filter By Ratings'); ?></h5>
                                <div class="check-box">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               <?php if(isset(request()->rating)): ?>
                                               <?php $__currentLoopData = request()->rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               <?php if($data == 5): ?> checked <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                               <?php endif; ?>
                                               value="5" name="rating[]" id="rating1"/>

                                        <label class="form-check-label" for="rating1">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               <?php if(isset(request()->rating)): ?>
                                               <?php $__currentLoopData = request()->rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               <?php if($data == 4): ?> checked <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                               <?php endif; ?>
                                               name="rating[]" value="4" id="rating2"/>
                                        <label class="form-check-label" for="rating2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               <?php if(isset(request()->rating)): ?>
                                               <?php $__currentLoopData = request()->rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               <?php if($data == 3): ?> checked <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                               <?php endif; ?>
                                               value="3" name="rating[]" id="rating3"/>
                                        <label class="form-check-label" for="rating3">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               <?php if(isset(request()->rating)): ?>
                                               <?php $__currentLoopData = request()->rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               <?php if($data == 2): ?> checked <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                               <?php endif; ?>
                                               value="2" name="rating[]" id="rating4"/>
                                        <label class="form-check-label" for="rating4">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               <?php if(isset(request()->rating)): ?>
                                               <?php $__currentLoopData = request()->rating; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               <?php if($data == 1): ?> checked <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                               <?php endif; ?>
                                               value="1" name="rating[]" id="rating5"/>
                                        <label class="form-check-label" for="rating5">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="filter-box">
                                <button class="btn-custom w-100"><?php echo app('translator')->get('filter now'); ?></button>
                            </div>



                        </div>
                    </form>
                </div>

                <?php if(count($properties) > 0): ?>
                    <div class="col-lg-9">
                        <div class="row g-4 mb-5">
                            <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!$property->rud()['runningProperties']): ?>
                                    <div class="col-md-6 col-lg-6">
                                        <?php echo $__env->make($theme.'partials.propertyBox', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <?php echo e($properties->appends($_GET)->links()); ?>

                            </ul>
                        </nav>
                    </div>
                <?php else: ?>
                    <div class="custom-not-found">
                        <img src="<?php echo e(asset($themeTrue.'img/no_data_found.png')); ?>" alt="not found" class="img-fluid">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <?php $__env->startPush('frontendModal'); ?>
        <?php echo $__env->make($theme.'partials.investNowModal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset($themeTrue.'js/investNow.js')); ?>"></script>
    <script>
        "use strict";
        var min = '<?php echo e($min); ?>'
        var max = '<?php echo e($max); ?>'
        var minRange = '<?php echo e($minRange); ?>'
        var maxRange = '<?php echo e($maxRange); ?>'

        var isAuthenticate = '<?php echo e(Auth::check()); ?>';

        $(document).ready(function () {
            $('.wishList').on('click', function () {
                var _this = this.id;
                let property_id = $(this).data('property');
                if (isAuthenticate == 1) {
                    wishList(property_id, _this);
                } else {
                    window.location.href = '<?php echo e(route('login')); ?>';
                }
            });

            $(".js-range-slider").ionRangeSlider({
                type: "double",
                min: min,
                max: max,
                from: minRange,
                to: maxRange,
                grid: true,
            });
        });

        function wishList(property_id = null, id = null) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "<?php echo e(route('user.wishList')); ?>",
                type: "POST",
                data: {
                    property_id: property_id,
                },
                success: function (data) {
                    if (data.data == 'added') {
                        $(`.save${id}`).removeClass("fal fa-heart");
                        $(`.save${id}`).addClass("fas fa-heart");
                        Notiflix.Notify.Success("Wishlist added");
                    }
                    if (data.data == 'remove') {
                        $(`.save${id}`).removeClass("fas fa-heart");
                        $(`.save${id}`).addClass("fal fa-heart");
                        Notiflix.Notify.Success("Wishlist removed");
                    }
                },
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make($theme.'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/property.blade.php ENDPATH**/ ?>