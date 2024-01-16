<?php $__env->startSection('title', trans('Property Details')); ?>

<?php $__env->startPush('seo'); ?>
    <meta name="description" content="<?php echo e(optional($singlePropertyDetails->details)->property_title); ?>">
    <meta name="keywords" content="<?php echo e(config('seo')['meta_keywords']); ?>">
    <link rel="shortcut icon" href="<?php echo e(getFile(config('location.logoIcon.path').'favicon.png')); ?>" type="image/x-icon">
    <!-- Apple Stuff -->
    <link rel="apple-touch-icon" href="<?php echo e(getFile(config('location.propertyThumbnail.path').$singlePropertyDetails->thumbnail)); ?>">
    <title><?php echo app('translator')->get($basic->site_title); ?> | <?php echo e(optional($singlePropertyDetails->details)->property_title); ?> </title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(getFile(config('location.logoIcon.path').'favicon.png')); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="<?php echo app('translator')->get($basic->site_title); ?> | <?php echo e(optional($singlePropertyDetails->details)->property_title); ?>">
    <!-- Google / Search Engine Tags -->
    <meta itemprop="name" content="<?php echo app('translator')->get($basic->site_title); ?> | <?php echo e(optional($singlePropertyDetails->details)->property_title); ?>">
    <meta itemprop="description" content="<?php echo e(optional($singlePropertyDetails->details)->details); ?>">
    <meta itemprop="image" content="<?php echo e(getFile(config('location.propertyThumbnail.path').$singlePropertyDetails->thumbnail)); ?>">
    <!-- Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e(optional($singlePropertyDetails->details)->property_title); ?>">
    <meta property="og:description" content="<?php echo e(optional($singlePropertyDetails->details)->details); ?>">
    <meta property="og:image" content="<?php echo e(getFile(config('location.propertyThumbnail.path').$singlePropertyDetails->thumbnail)); ?>"/>
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="<?php echo e(getFile(config('location.propertyThumbnail.path').$singlePropertyDetails->thumbnail)); ?>">
<?php $__env->stopPush(); ?>


<?php $__env->startSection('content'); ?>
    <!-- property details -->
    <section class="property-details">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="row info-box">
                            <div class="col-lg-8">
                                <h3 class="title">
                                    <?php echo app('translator')->get(optional($singlePropertyDetails->details)->property_title); ?>
                                </h3>

                                <p class="address mb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo app('translator')->get(optional($singlePropertyDetails->getAddress->details)->title); ?>
                                </p>
                                <div class="review">
                                    <?php echo $__env->make($theme.'partials.review', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <a href="#reviews">
                                        (<?php echo e($totalReview <= 1 ? ($totalReview. trans(' review')) : ($totalReview. trans(' reviews'))); ?>

                                        ) </a>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="right-side">
                                    <h3 class="price"> <?php echo e($singlePropertyDetails->investmentAmount); ?>

                                        <span><?php echo e($singlePropertyDetails->is_invest_type == 0 ? trans('(Fixed Invest)') : trans('(Invest Range)')); ?></span>
                                    </h3>
                                    <?php if($singlePropertyDetails->available_funding == 0 && $singlePropertyDetails->expire_date > now() && $singlePropertyDetails->is_available_funding == 1): ?>
                                        <span class="invest-completed-details"><i class="fad fa-check-circle"></i> <?php echo app('translator')->get('Investment Completed'); ?></span>
                                    <?php elseif($singlePropertyDetails->expire_date < now()): ?>
                                        <span class="invest-completed-details bg-danger"><i
                                                class="fad fa-times-circle"></i> <?php echo app('translator')->get('Investment Time Expired'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-lg-5">
                    <div class="col-lg-8">
                        <div class="gallery-box">
                            <div id="mainCarousel" class="carousel mx-auto main_carousel">
                                <?php $__currentLoopData = $singlePropertyDetails->image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div
                                        class="carousel__slide"
                                        data-src="<?php echo e(getFile(config('location.property.path').$img->image)); ?>"
                                        data-fancybox="gallery"
                                        data-caption="">
                                        <img class="img-fluid"
                                             src="<?php echo e(getFile(config('location.property.path').$img->image)); ?>"/>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div id="thumbCarousel" class="carousel max-w-xl mx-auto thumb_carousel">
                                <?php if(count($singlePropertyDetails->image) > 0): ?>
                                    <?php $__currentLoopData = $singlePropertyDetails->image; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="carousel__slide">
                                            <img class="panzoom__content img-fluid"
                                                 src="<?php echo e(getFile(config('location.property.path').$img->image)); ?>"/>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="">
                                        <img class="panzoom__content img-fluid"
                                             src="<?php echo e(getFile(config('location.propertyThumbnail.path').$singlePropertyDetails->thumbnail)); ?>"/>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>

                        <div id="description" class="description-box">
                            <h4><?php echo app('translator')->get('Description'); ?></h4>
                            <p class="property__description">
                                <?php echo optional($singlePropertyDetails->details)->details; ?>

                            </p>
                        </div>
                        <div id="amenities" class="amenities-box">
                            <h4 class="mb-4"><?php echo app('translator')->get('Amenities'); ?></h4>
                            <div class="row gy-4">
                                <?php $__currentLoopData = $singlePropertyDetails->allamenity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-3 col-md-2">
                                        <div class="amenity-box">
                                            <i class="<?php echo e(@$amenity->icon); ?>"></i>
                                            <h6><?php echo app('translator')->get(optional($amenity->details)->title); ?></h6>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>


                        <div class="map-box">
                            <h4><?php echo app('translator')->get('Location'); ?></h4>
                            <iframe
                                src="<?php echo e($singlePropertyDetails->location); ?>"
                                width="100%"
                                height="400"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>

                        <?php if($singlePropertyDetails->details->faq != null): ?>
                            <div class="faq-box" class="accordion" id="accordionExample">
                                <?php
                                    $faq_key = 0;
                                ?>
                                <?php $__currentLoopData = $singlePropertyDetails->details->faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $faq_key++;
                                    ?>
                                    <div class="accordion-item">
                                        <h5 class="accordion-header" id="headingOne<?php echo e(@$faq_key); ?>">
                                            <button
                                                class="accordion-button <?php echo e($faq_key == 1 ? '' : 'collapsed'); ?>"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne<?php echo e(@$faq_key); ?>"
                                                aria-expanded="false"
                                                aria-controls="collapseOne"
                                            >
                                                <?php echo app('translator')->get(@$faq->field_name); ?>
                                            </button>
                                        </h5>
                                        <div
                                            id="collapseOne<?php echo e(@$faq_key); ?>"
                                            class="accordion-collapse collapse <?php echo e(@$faq_key == 1 ? 'show' : ''); ?>"
                                            aria-labelledby="headingOne<?php echo e(@$faq_key); ?>"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <?php echo app('translator')->get(@$faq->field_value); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        <div id="review-app">
                            <div id="reviews" class="reviews">
                                <div class="customer-review">
                                    <h4><?php echo app('translator')->get('Reviews'); ?></h4>

                                    <div class="review-box" v-for="(obj, index) in item.feedArr">
                                        <div class="text">
                                            <img :src="obj.review_user_info.imgPath"/>
                                            <span class="name" v-cloak="">{{obj.review_user_info.fullname}}</span>
                                            <p class="mt-3" v-cloak="">
                                                {{ obj.review }}
                                            </p>
                                        </div>

                                        <div class="review-date">
                                         <span class="review rating-group">
                                              <div id="half-stars-example">
                                                  <i class="fas fa-star" v-for="i in obj.rating2" :key="i"
                                                     v-cloak=""></i>
                                              </div>
                                          </span>
                                            <br/>
                                            <span class="date" v-cloak="">{{obj.date_formatted}}</span>
                                        </div>
                                    </div>

                                    <div class="frontend-not-data-found" v-if="item.feedArr.length<1" v-cloak="">
                                        <p class="text-center not-found-times" v-cloak="">
                                            <i class="fad fa-file-times not-found-times" v-cloak=""></i>
                                        </p>
                                        <h5 class="text-center m-0 " v-cloak=""><?php echo app('translator')->get("No Review Found"); ?></h5>

                                    </div>

                                    <div class="row mt-5">
                                        <div class="col d-flex justify-content-center" v-cloak="">
                                            <?php echo $__env->make('partials.vuePaginate', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if(auth()->guard()->check()): ?>
                                    <?php if($reviewDone <= 0 && in_array(\Auth::user()->id, $investor)): ?>
                                        <div class="add-review mb-5" v-if="item.reviewDone < 1">
                                            <div>
                                                <h4><?php echo app('translator')->get('Add Review'); ?></h4>
                                            </div>
                                            <form>
                                                <div class="mb-3">
                                                    <div id="half-stars-example">
                                                        <div class="rating-group ms-1">

                                                            <label
                                                                aria-label="1 star"
                                                                class="rating__label"
                                                                for="rating2-10"
                                                            ><i
                                                                    class="rating__icon rating__icon--star fa fa-star"
                                                                    aria-hidden="true"
                                                                ></i
                                                                ></label>
                                                            <input
                                                                class="rating__input"
                                                                name="rating2"
                                                                id="rating2-10"
                                                                value="1"
                                                                @click="rate(1)"
                                                                type="radio"
                                                            />
                                                            <label
                                                                aria-label="2 stars"
                                                                class="rating__label"
                                                                for="rating2-20"
                                                            ><i
                                                                    class="rating__icon rating__icon--star fa fa-star"
                                                                    aria-hidden="true"
                                                                ></i
                                                                ></label>
                                                            <input
                                                                class="rating__input"
                                                                name="rating2"
                                                                id="rating2-20"
                                                                value="2"
                                                                @click="rate(2)"
                                                                type="radio"
                                                            />
                                                            <label
                                                                aria-label="3 stars"
                                                                class="rating__label"
                                                                for="rating2-30"
                                                            ><i
                                                                    class="rating__icon rating__icon--star fa fa-star"
                                                                    aria-hidden="true"
                                                                ></i
                                                                ></label>
                                                            <input
                                                                class="rating__input"
                                                                name="rating2"
                                                                id="rating2-30"
                                                                value="3"
                                                                @click="rate(3)"
                                                                type="radio"
                                                            />
                                                            <label
                                                                aria-label="4 stars"
                                                                class="rating__label"
                                                                for="rating2-40"
                                                            ><i
                                                                    class="rating__icon rating__icon--star fa fa-star"
                                                                    aria-hidden="true"
                                                                ></i
                                                                ></label>
                                                            <input
                                                                class="rating__input"
                                                                name="rating2"
                                                                id="rating2-40"
                                                                value="4"
                                                                @click="rate(4)"
                                                                type="radio"
                                                            />
                                                            <label
                                                                aria-label="5 stars"
                                                                class="rating__label"
                                                                for="rating2-50"
                                                            ><i
                                                                    class="rating__icon rating__icon--star fa fa-star"
                                                                    aria-hidden="true"
                                                                ></i
                                                                ></label>
                                                            <input
                                                                class="rating__input"
                                                                name="rating2"
                                                                id="rating2-50"
                                                                value="5"
                                                                checked=""
                                                                type="radio"
                                                                @click="rate(5)"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        for="exampleFormControlTextarea1"
                                                        class="form-label"
                                                    ><?php echo app('translator')->get('Your message'); ?></label>
                                                    <textarea
                                                        class="form-control text-dark"
                                                        id="exampleFormControlTextarea1"
                                                        name="review"
                                                        v-model="item.feedback"
                                                        rows="5"></textarea>
                                                    <span class="text-danger"
                                                          v-cloak="">{{ error.feedbackError }}</span>
                                                </div>
                                                <button class="btn-custom mt-3"
                                                        @click.prevent="addFeedback"><?php echo app('translator')->get('Submit now'); ?></button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="add-review mb-5 add__review__login" v-if="item.reviewDone < 1">
                                        <div class="d-flex justify-content-between">
                                            <h4><?php echo app('translator')->get('Add Review'); ?></h4>
                                        </div>
                                        <a href="<?php echo e(route('login')); ?>"
                                           class="btn btn-review-custom btn-sm h-25 text-white"><?php echo app('translator')->get('Login to review'); ?></a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>

                        <div id="amenities" class="amenities-box">
                            <div id="shareBlock"><h4><?php echo app('translator')->get('Share now'); ?></h4></div>
                        </div>
                    </div>

                    <!-- sidebar start -->

                    <div class="col-lg-4">
                        <div class="side-bar">
                            <form action="<?php echo e(route('user.invest-property', $singlePropertyDetails->id)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="side-box">
                                    <div class="d-flex justify-content-between">
                                        <h4><?php echo app('translator')->get('Invest Amount'); ?></h4>
                                    </div>
                                    <?php if($singlePropertyDetails->is_available_funding == 1): ?>
                                    <p class="primary_color"><?php echo app('translator')->get('Available for funding'); ?>:
                                        <?php if($singlePropertyDetails->available_funding < $singlePropertyDetails->minimum_amount && $singlePropertyDetails->available_funding !=0): ?>
                                            <?php echo e(config('basic.currency_symbol')); ?><?php echo e($singlePropertyDetails->minimum_amount); ?>

                                        <?php else: ?>
                                            <span><?php echo e(config('basic.currency_symbol')); ?><?php echo e($singlePropertyDetails->available_funding); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <?php endif; ?>

                                    <ul class="profit-calculation">
                                        <li><?php echo app('translator')->get('Invest Amount'); ?>:
                                            <span>
                                                <?php if($singlePropertyDetails->fixed_amount > $singlePropertyDetails->available_funding && $singlePropertyDetails->available_funding > 0): ?>
                                                    <?php echo e(config('basic.currency_symbol')); ?><?php echo e($singlePropertyDetails->available_funding); ?>

                                                <?php else: ?>
                                                    <?php if($singlePropertyDetails->available_funding < $singlePropertyDetails->minimum_amount && $singlePropertyDetails->available_funding !=0): ?>
                                                        <?php echo e(config('basic.currency_symbol')); ?><?php echo e($singlePropertyDetails->minimum_amount); ?>

                                                    <?php else: ?>
                                                        <?php echo e($singlePropertyDetails->investmentAmount); ?>

                                                    <?php endif; ?>
                                                <?php endif; ?>

                                            </span></li>
                                        <li><?php echo app('translator')->get('Profit'); ?>:
                                            <span><?php echo e($singlePropertyDetails->profit_type == 1 ? (int)$singlePropertyDetails->profit.'%' : config('basic.currency_symbol').$singlePropertyDetails->profit); ?></span>
                                        </li>

                                        <li><?php echo app('translator')->get('Return Interval'); ?>:
                                            <span><?php echo e($singlePropertyDetails->how_many_times == null ? optional($singlePropertyDetails->managetime)->time.' '.optional($singlePropertyDetails->managetime)->time_type.' '.'(Lifetime)' :  optional($singlePropertyDetails->managetime)->time.' '.optional($singlePropertyDetails->managetime)->time_type.' '.'('.$singlePropertyDetails->how_many_times. ' '. 'times'. ')'); ?></span>
                                        </li>
                                        <?php if($singlePropertyDetails->fixed_amount < $singlePropertyDetails->available_funding && $singlePropertyDetails->available_funding > 0): ?>
                                            <?php if($singlePropertyDetails->is_installment == 1): ?>
                                                <li><?php echo app('translator')->get('Total Installments'); ?>
                                                    <span><?php echo e($singlePropertyDetails->total_installments); ?></span></li>
                                                <li><?php echo app('translator')->get('Installment Duration'); ?>
                                                    <span><?php echo e($singlePropertyDetails->installment_duration); ?> <?php echo app('translator')->get($singlePropertyDetails->installment_duration_type); ?></span>
                                                </li>
                                                <li><?php echo app('translator')->get('Installment Late Fee'); ?>
                                                    <span> <?php echo e($basic->currency_symbol); ?><?php echo e($singlePropertyDetails->installment_late_fee); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <li><?php echo app('translator')->get('Capital Back'); ?>:
                                            <span><?php echo e($singlePropertyDetails->is_capital_back == 1 ? 'Yes' : 'No'); ?></span>
                                        </li>

                                        <li><?php echo app('translator')->get('Expire'); ?>:
                                            <span
                                                class="primary_color"><?php echo e(dateTime($singlePropertyDetails->expire_date)); ?></span>
                                        </li>
                                        <?php if($singlePropertyDetails->available_funding != 0 && $singlePropertyDetails->expire_date > now()): ?>
                                            <hr/>
                                        <?php endif; ?>
                                    </ul>

                                    <?php if(auth()->guard()->check()): ?>
                                        <?php if($singlePropertyDetails->available_funding != 0 && $singlePropertyDetails->expire_date > now()): ?>
                                            <div class="input-box col-12">
                                                <label for=""><?php echo app('translator')->get('Select Wallet'); ?></label>
                                                <select class="form-control form-select" id="exampleFormControlSelect1"
                                                        name="balance_type">
                                                    <?php if(auth()->guard()->check()): ?>
                                                        <option
                                                            value="balance"><?php echo app('translator')->get('Deposit Balance - '.$basic->currency_symbol.getAmount(auth()->user()->balance)); ?></option>
                                                        <option
                                                            value="interest_balance"><?php echo app('translator')->get('Interest Balance -'.$basic->currency_symbol.getAmount(auth()->user()->interest_balance)); ?></option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <?php if($singlePropertyDetails->fixed_amount < $singlePropertyDetails->available_funding && $singlePropertyDetails->available_funding > 0 && $singlePropertyDetails->is_installment==1): ?>
                                                <div class="input-box col-12 payInstallment mt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="0"
                                                               name="pay_installment" id="pay_installment"
                                                               data-installmentamount="<?php echo e($singlePropertyDetails->installment_amount); ?>"
                                                               data-fixedamount="<?php echo e($singlePropertyDetails->fixed_amount); ?>"/>
                                                        <label class="form-check-label"
                                                               for="pay_installment"><?php echo app('translator')->get('Pay Installment'); ?></label>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="input-box col-12 mt-2 mb-2">
                                                <label for="<?php echo app('translator')->get('Amount'); ?>"><?php echo app('translator')->get('Amount'); ?></label>
                                                <input class="form-control invest-amount" type="text"
                                                       value="<?php echo e($singlePropertyDetails->investableAmount()); ?>"
                                                       <?php echo e($singlePropertyDetails->is_invest_type == 0 ? 'readonly' : ''); ?> placeholder="<?php echo app('translator')->get('Enter amount'); ?>"
                                                       onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                       name="amount" id="amount"/>
                                            </div>
                                            <div>
                                                <button type="submit"
                                                        class="btn-custom w-100"><?php echo e(trans('Invest Now')); ?></button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6 class="text-center font-weight-bold"><?php echo app('translator')->get('First Log In To Your Account For Invest'); ?></h6>
                                                    <div class="tree">
                                                        <div class="d-flex justify-content-center">
                                                            <div
                                                                class="branch branch-1"><?php echo app('translator')->get('Sign In / Sign Up'); ?></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="branch branch-2"><a href="<?php echo e(route('login')); ?>"
                                                                                            class="text-decoration-underline"><?php echo app('translator')->get('Login'); ?></a>
                                                            </div>
                                                            <div class="branch branch-3"><a
                                                                    href="<?php echo e(route('register')); ?>"
                                                                    class="text-decoration-underline"><?php echo app('translator')->get('Register'); ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                </div>
                            </form>

                            <?php if(count($singlePropertyDetails->getInvestment) > 0): ?>
                                <div class="side-box">
                                    <h4><?php echo app('translator')->get('Investor'); ?></h4>
                                    <div class="owl-carousel property-agents">
                                        <?php $__currentLoopData = $singlePropertyDetails->getInvestment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $investor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="agent-box-wrapper">
                                                <div class="agent-box">
                                                    <div class="img-box">
                                                        <img
                                                            src="<?php echo e(getFile(config('location.user.path').optional($investor->user)->image)); ?>"
                                                            class="img-fluid profile" alt="<?php echo app('translator')->get('not found'); ?>"/>
                                                    </div>
                                                    <div class="text-box">
                                                        <a href="<?php echo e(route('investorProfile', [@slug(optional($investor->user)->username), optional($investor->user)->id])); ?>"
                                                           class="agent-name"><?php echo app('translator')->get(optional($investor->user)->fullname); ?></a>
                                                        <span><?php echo app('translator')->get('Agent of Property'); ?></span>
                                                    </div>
                                                </div>
                                                <ul>
                                                    <li>
                                                        <i class="fal fa-building"></i>
                                                        <span>
                                                            <?php echo e(optional($investor->user)->countTotalInvestment()); ?>

                                                            <?php if(optional($investor->user)->countTotalInvestment() == 1): ?>
                                                                <?php echo app('translator')->get('Property'); ?>
                                                            <?php else: ?>
                                                                <?php echo app('translator')->get('Propertys'); ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </li>

                                                    <?php if(optional($investor->user)->address): ?>
                                                        <li>
                                                            <i class="fal fa-map-marker-alt" aria-hidden="true"></i>
                                                            <span><?php echo app('translator')->get(optional($investor->user)->address); ?></span>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="side-box">
                                <h4><?php echo app('translator')->get('Latest Properties'); ?></h4>
                                <?php $__currentLoopData = $latestProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="property-side-box">
                                        <div class="img-box">
                                            <img class="img-fluid"
                                                 src="<?php echo e(getFile(config('location.propertyThumbnail.path').$property->thumbnail)); ?>"
                                                 alt=""/>
                                        </div>
                                        <div class="text-box">
                                            <a href="<?php echo e(route('propertyDetails',[@slug(optional($property->details)->property_title), $property->id])); ?>"
                                               class="title"><?php echo e(\Illuminate\Support\Str::limit(optional($property->details)->property_title, 20)); ?></a>
                                            <p class="address"><i
                                                    class="fal fa-map-marker-alt"></i> <?php echo app('translator')->get(optional($property->getAddress->details)->title); ?>
                                            </p>
                                            <h5 class="price"><?php echo e(@$property->investmentAmount); ?></h5>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css-lib'); ?>
    <link rel="stylesheet" href="<?php echo e(asset($themeTrue.'css/owl.carousel.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset($themeTrue.'css/owl.theme.default.min.css')); ?>"/>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('extra-js'); ?>
    <!-- fancybox slider -->
    <script src="<?php echo e(asset($themeTrue.'js/fancybox.umd.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset($themeTrue.'js/carousel.js')); ?>"></script>
    <script>
        'use strict'
        var newApp = new Vue({
            el: "#review-app",
            data: {
                item: {
                    feedback: "",
                    propertyId: '',
                    feedArr: [],
                    reviewDone: "",
                    rating: "",
                },

                pagination: [],
                links: [],
                error: {
                    feedbackError: ''
                }
            },
            beforeMount() {
                let _this = this;
                _this.getReviews()
            },
            mounted() {
                let _this = this;
                _this.item.propertyId = "<?php echo e($singlePropertyDetails->id); ?>"
                _this.item.reviewDone = "<?php echo e($reviewDone); ?>"
                _this.item.rating = "5";
            },
            methods: {
                rate(rate) {
                    this.item.rating = rate;
                },
                addFeedback() {
                    let item = this.item;
                    this.makeError();
                    axios.post("<?php echo e(route('user.review.push')); ?>", this.item)
                        .then(function (response) {
                            console.log(response)
                            if (response.data.status == 'success') {

                                item.feedArr.unshift({
                                    review: response.data.data.review,
                                    review_user_info: response.data.data.review_user_info,
                                    rating2: parseInt(response.data.data.rating2),
                                    date_formatted: response.data.data.date_formatted,
                                });
                                item.reviewDone = 5;
                                item.feedback = "";
                                Notiflix.Notify.Success("Review done");
                            }
                        })
                        .catch(function (error) {
                            console.log(error)
                        });
                },
                makeError() {
                    if (!this.item.feedback) {
                        this.error.feedbackError = "Your review message field is required"
                    }
                },

                getReviews() {
                    var app = this;
                    axios.get("<?php echo e(route('api-propertyReviews',[$singlePropertyDetails->id])); ?>")
                        .then(function (res) {
                            app.item.feedArr = res.data.data.data;
                            app.pagination = res.data.data;
                            app.links = res.data.data.links;
                            app.links = app.links.slice(1, -1);
                            console.log(app.links);
                        })

                },
                updateItems(page) {
                    var app = this;
                    if (page == 'back') {
                        var url = this.pagination.prev_page_url;
                    } else if (page == 'next') {
                        var url = this.pagination.next_page_url;
                    } else {
                        var url = page.url;
                    }
                    axios.get(url)
                        .then(function (res) {
                            app.item.feedArr = res.data.data.data;
                            app.pagination = res.data.data;
                            app.links = res.data.data.links;
                        })
                },
            }
        })

        $(document).ready(function () {
            $(document).on('click', '#pay_installment', function () {
                if ($(this).prop("checked") == true) {
                    $(this).val(1);
                    let installmentAmount = $(this).data('installmentamount');
                    console.log(installmentAmount);
                    $('.invest-amount').val(installmentAmount);
                    $('#amount').attr('readonly', true);
                } else {
                    let fixedAmount = $(this).data('fixedamount');
                    console.log(fixedAmount);
                    $('.invest-amount').val(fixedAmount);
                    $('#amount').attr('readonly', true);
                    $(this).val(0);
                }

            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make($theme.'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/propertyDetails.blade.php ENDPATH**/ ?>