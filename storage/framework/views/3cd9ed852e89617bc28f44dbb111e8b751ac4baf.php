<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <?php if(adminAccessRoute(config('role.dashboard.access.view'))): ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.dashboard')); ?>" aria-expanded="false">
                            <i data-feather="home" class="feather-icon text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Dashboard'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.manage_role.access.view'))): ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.staff')); ?>" aria-expanded="false">
                            <i data-feather="users" class="feather-icon text-cyan"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Role Permission'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(adminAccessRoute(config('role.identify_form.access.view'))): ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.identify-form')); ?>" aria-expanded="false">
                            <i data-feather="file-text" class="feather-icon text-danger"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('KYC / Identity Form'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.manage_property.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Manage Property'); ?></span></li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.scheduleManage')); ?>" aria-expanded="false">
                            <i class="fas fa-clock text-warning"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Profit Schedule'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.amenities')); ?>" aria-expanded="false">
                            <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Amenities List'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.addressList')); ?>" aria-expanded="false">
                            <i class="fa fa-check-circle text-primary" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Address List'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fa fa-building text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Property List'); ?></span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo e(route('admin.propertyList',['all'])); ?>" aria-expanded="false">
                                    <span class="hide-menu"><?php echo app('translator')->get('All Properties'); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo e(route('admin.propertyList',['upcoming'])); ?>" aria-expanded="false">
                                    <span class="hide-menu"><?php echo app('translator')->get('Upcoming Properties'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo e(route('admin.propertyList',['running'])); ?>" aria-expanded="false">
                                    <span class="hide-menu"><?php echo app('translator')->get('Running Properties'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo e(route('admin.propertyList',['expired'])); ?>" aria-expanded="false">
                                    <span class="hide-menu"><?php echo app('translator')->get('Expired Properties'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.wishListProperty')); ?>" aria-expanded="false">
                            <i class="fa fa-heart text-info" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('WishList Property'); ?></span>
                        </a>
                    </li>


                    <li class="sidebar-item <?php echo e(menuActive(['admin.shareInvestment'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.shareInvestment')); ?>" aria-expanded="false">
                            <i class="fas fa-share-alt text-cyan"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Can share investment?'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.propertyAnalytics')); ?>" aria-expanded="false">
                            <i class="fas fa-chart-line text-warning"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Analytics'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.investments.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Investments'); ?></span></li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.investments'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.investments',['all'])); ?>" aria-expanded="false">
                            <i class="fas fa-shopping-cart text-purple"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('All Investments'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.investedProperty','admin.seeInvestedUser'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.investments',['running'])); ?>" aria-expanded="false">
                            <i class="fas fa-running text-primary"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Running Investments'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.investedProperty','admin.seeInvestedUser'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.investments',['due'])); ?>" aria-expanded="false">
                            <i class="fa fa-info-circle text-warning" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Due Investments'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.expiredInvestment'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.investments',['expired'])); ?>" aria-expanded="false">
                            <i class="fas fa-times-circle text-danger"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Expired Investments'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.completedInvestment'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.investments',['completed'])); ?>" aria-expanded="false">
                            <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Completed Investments'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.manage_badge.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Manage Badge'); ?></span></li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.badgeSettings'])); ?>">
                        <a class="sidebar-link <?php echo e(menuActive(['admin.badgeSettings'])); ?>" href="<?php echo e(route('admin.badgeSettings')); ?>" aria-expanded="false">
                            <i class="fas fa-cog text-pink"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Badge Bonus'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.badgeList'])); ?>">
                        <a class="sidebar-link <?php echo e(menuActive(['admin.badgeList'])); ?>" href="<?php echo e(route('admin.badgeList')); ?>" aria-expanded="false">
                            <i class="fa fa-certificate text-yellow" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Badge List'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.manage_user.access.view'))): ?>
                    
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Manage User'); ?></span></li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.users','admin.users.search','admin.user-edit*','admin.send-email*','admin.user*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.users')); ?>" aria-expanded="false">
                            <i class="fas fa-users text-info"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('All User'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.kyc.users.pending')); ?>"
                           aria-expanded="false">
                            <i class="fas fa-spinner text-cyan"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Pending KYC'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.kyc.users')); ?>"
                           aria-expanded="false">
                            <i class="fas fa-file text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('KYC Log'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.email-send')); ?>"
                           aria-expanded="false">
                            <i class="fas fa-envelope-open text-blue"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Send Email'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.commission_setting.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Commission Setting'); ?></span></li>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.referral-commission'])); ?>">
                        <a class="sidebar-link <?php echo e(menuActive(['admin.referral-commission'])); ?>" href="<?php echo e(route('admin.referral-commission')); ?>" aria-expanded="false">
                            <i class="fas fa-cogs text-info"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Referral'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.payment_settings.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Payment Settings'); ?></span></li>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.payment.methods','admin.edit.payment.methods'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payment.methods')); ?>"
                           aria-expanded="false">
                            <i class="fas fa-credit-card text-primary"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Payment Methods'); ?></span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.deposit.manual.index','admin.deposit.manual.create','admin.deposit.manual.edit'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.deposit.manual.index')); ?>"
                           aria-expanded="false">
                            <i class="fa fa-university text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Manual Gateway'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.payment.pending'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payment.pending')); ?>" aria-expanded="false">
                            <i class="fas fa-spinner text-info"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Deposit Request'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.payment.log','admin.payment.search'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payment.log')); ?>" aria-expanded="false">
                            <i class="fas fa-history text-cyan"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Payment Log'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.payout_settings.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Payout Settings'); ?></span></li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.payout.settings'])); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payout.settings')); ?>" aria-expanded="false">
                            <i class="fas fa-hand-holding-usd text-blue"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Payout Settings'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.payout-method*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payout-method')); ?>"
                           aria-expanded="false">
                            <i class="fas fa-credit-card text-warning"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Payout Methods'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.payout-request'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payout-request')); ?>" aria-expanded="false">
                            <i class="fas fa-hand-holding-usd text-cyan"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Payout Request'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.payout-log*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.payout-log')); ?>" aria-expanded="false">
                            <i class="fas fa-history text-indigo"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Payout Log'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.all_transaction.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('All Transaction '); ?></span></li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.transaction*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.transaction')); ?>" aria-expanded="false">
                            <i class="fas fa-exchange-alt text-warning"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Transaction'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item <?php echo e(menuActive(['admin.commissions*'],3)); ?>">
                        <a class="sidebar-link" href="<?php echo e(route('admin.commissions')); ?>" aria-expanded="false">
                            <i class="fas fa-money-bill-alt text-indigo"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Commission'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.support_ticket.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Support Tickets'); ?></span></li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.ticket')); ?>" aria-expanded="false">
                            <i class="fas fa-ticket-alt text-cyan"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('All Tickets'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.ticket',['open'])); ?>"
                           aria-expanded="false">
                            <i class="fas fa-spinner text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Open Ticket'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.ticket',['closed'])); ?>"
                           aria-expanded="false">
                            <i class="fas fa-times-circle text-danger"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Closed Ticket'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.ticket',['answered'])); ?>"
                           aria-expanded="false">
                            <i class="fas fa-reply text-primary"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Answered Ticket'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.subscriber.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Subscriber'); ?></span></li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.subscriber.index')); ?>" aria-expanded="false">
                            <i class="fas fa-envelope-open text-pink"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Subscriber List'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.website_controls.access.view'))): ?>)
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Website Controls'); ?></span></li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.basic-controls')); ?>" aria-expanded="false">
                            <i class="fas fa-cogs text-purple"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Basic Controls'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.plugin.config')); ?>" aria-expanded="false">
                            <i class="fa fa-plug text-yellow" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Plugin Configuration'); ?></span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-envelope text-success"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Email Settings'); ?></span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.email-controls')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('Email Controls'); ?></span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.email-template.show')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('Email Template'); ?> </span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-mobile-alt text-purple"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('SMS Settings'); ?></span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.sms.config')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('SMS Controls'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.sms-template')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('SMS Template'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-bell text-pink"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Push Notification'); ?></span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.notify-config')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('Configuration'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.notify-template.show')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('Template'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.currency.exchange.api.config')); ?>" aria-expanded="false">
                            <i class="fa fa-dollar-sign text-yellow" aria-hidden="true"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Currency Exchange'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.language_settings.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Language Settings'); ?></span></li>
                        <?php if(adminAccessRoute(config('role.language_settings.access.view'))): ?>
                            <li class="sidebar-item <?php echo e(menuActive(['admin.language.create','admin.language.edit*','admin.language.keywordEdit*'],3)); ?>">
                                <a class="sidebar-link" href="<?php echo e(route('admin.language.index')); ?>"
                                   aria-expanded="false">
                                    <i class="fas fa-language text-cyan"></i>
                                    <span class="hide-menu"><?php echo app('translator')->get('Manage Language'); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                <?php endif; ?>


                <?php if(adminAccessRoute(config('role.theme_settings.access.view'))): ?>
                    <li class="list-divider"></li>
                    <li class="nav-small-cap"><span class="hide-menu"><?php echo app('translator')->get('Theme Settings'); ?></span></li>



                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.logo-seo')); ?>" aria-expanded="false">
                            <i class="fas fa-image text-success"></i><span
                                class="hide-menu"><?php echo app('translator')->get('Manage Logo & SEO'); ?></span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="<?php echo e(route('admin.breadcrumb')); ?>" aria-expanded="false">
                            <i class="fas fa-file-image text-cyan"></i><span
                                class="hide-menu"><?php echo app('translator')->get('Manage Breadcrumb'); ?></span>
                        </a>
                    </li>


                    <li class="sidebar-item <?php echo e(menuActive(['admin.template.show*'],3)); ?>">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-clipboard-list text-primary"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Section Heading'); ?></span>
                        </a>
                        <ul aria-expanded="false"
                            class="collapse first-level base-level-line <?php echo e(menuActive(['admin.template.show*'],1)); ?>">

                            <?php $__currentLoopData = array_diff(array_keys(config('templates')),['message','template_media']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="sidebar-item <?php echo e(menuActive(['admin.template.show'.$name])); ?>">
                                    <a class="sidebar-link <?php echo e(menuActive(['admin.template.show'.$name])); ?>"
                                       href="<?php echo e(route('admin.template.show',$name)); ?>">
                                        <span class="hide-menu"><?php echo app('translator')->get(ucfirst(kebab2Title($name))); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>


                    <?php
                        $segments = request()->segments();
                        $last  = end($segments);
                    ?>
                    <li class="sidebar-item <?php echo e(menuActive(['admin.content.create','admin.content.show*'],3)); ?>">
                        <a class="sidebar-link has-arrow <?php echo e(Request::routeIs('admin.content.show',$last) ? 'active' : ''); ?>"
                           href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-clipboard-list text-pink"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('Content Settings'); ?></span>
                        </a>
                        <ul aria-expanded="false"
                            class="collapse first-level base-level-line <?php echo e(menuActive(['admin.content.create','admin.content.show*'],1)); ?>">
                            <?php $__currentLoopData = array_diff(array_keys(config('contents')),['message','content_media']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="sidebar-item <?php echo e(($last == $name) ? 'active' : ''); ?> ">
                                    <a class="sidebar-link <?php echo e(($last == $name) ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.content.index',$name)); ?>">
                                        <span class="hide-menu"><?php echo app('translator')->get(ucfirst(kebab2Title($name))); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if(adminAccessRoute(config('role.manage_blogs.access.view'))): ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <i class="fas fa-newspaper"></i>
                            <span class="hide-menu"><?php echo app('translator')->get('All Blogs'); ?></span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.blogCategory')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('Category List'); ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a href="<?php echo e(route('admin.blogList')); ?>" class="sidebar-link">
                                    <span class="hide-menu"><?php echo app('translator')->get('Blog List'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>


                    <li class="list-divider"></li>
                    <li class="nav-small-cap text-center"><span class="hide-menu"><?php echo app('translator')->get('Version 1.0'); ?></span></li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>