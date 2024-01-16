<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear', function () {
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    Artisan::call('optimize:clear', array(), $output);
    return $output->fetch();
})->name('/clear');

Route::get('/user', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/loginModal', 'Auth\LoginController@loginModal')->name('loginModal');

Route::get('queue-work', function () {
    return Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
})->name('queue.work');

Route::get('cron', function () {
	return Illuminate\Support\Facades\Artisan::call('schedule:run');
})->name('cron');

Auth::routes(['verify' => true]);

Route::group(['middleware' => ['guest']], function () {
    Route::get('register/{sponsor?}', 'Auth\RegisterController@sponsor')->name('register.sponsor');
});

Route::group(['middleware' => ['auth', 'Maintenance'], 'prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('/check', 'User\VerificationController@check')->name('check');
    Route::get('/resend_code', 'User\VerificationController@resendCode')->name('resendCode');
    Route::post('/mail-verify', 'User\VerificationController@mailVerify')->name('mailVerify');
    Route::post('/sms-verify', 'User\VerificationController@smsVerify')->name('smsVerify');
    Route::post('twoFA-Verify', 'User\VerificationController@twoFAverify')->name('twoFA-Verify');
    Route::middleware('userCheck')->group(function () {
        Route::middleware('kyc')->group(function () {

            Route::post('/khalti/payment/verify/{trx}', 'khaltiPaymentController@verifyPayment')->name('khalti.verifyPayment');
            Route::post('/khalti/payment/store','khaltiPaymentController@storePayment')->name('khalti.storePayment');

            // review
            Route::post('/property-details/review', 'FrontendController@reviewPush')->name('review.push');
            Route::get('/propertyReviews/{id?}', 'FrontendController@getReview')->name('propertyReviews');

            Route::post('/send-message-to-property-investor', 'user\SendMailController@sendMessageToPropertyInvestor')->name('sendMessageToPropertyInvestor');

            Route::get('/dashboard', 'User\HomeController@index')->name('home');
            Route::get('payment', 'User\HomeController@payment')->name('payment');
            Route::get('add-fund', 'User\HomeController@addFund')->name('addFund');
            Route::post('add-fund', 'PaymentController@addFundRequest')->name('addFund.request');
            Route::get('addFundConfirm', 'PaymentController@depositConfirm')->name('addFund.confirm');
            Route::post('addFundConfirm', 'PaymentController@fromSubmit')->name('addFund.fromSubmit');

            // Invest History
            Route::get('invest-history', 'User\HomeController@investHistory')->name('invest-history');
            Route::get('invest-history-details/{id}', 'User\HomeController@investHistoryDetails')->name('invest-history-details');
            Route::put('/complete-due-payment/{id}', 'User\HomeController@completeDuePayment')->name('completeDuePayment');
            Route::post('/invest-property/{id}', 'User\HomeController@investProperty')->name('invest-property');
            Route::post('/property-make-offer/{id}', 'User\HomeController@propertyMakeOfferStore')->name('propertyMakeOfferStore');

            // user Property Market
            Route::get('/property-market/{type?}', 'User\HomeController@propertyMarket')->name('propertyMarket');
            Route::post('/property-share-store/{id}', 'User\HomeController@propertyShareStore')->name('propertyShareStore');
            Route::post('/property-share-update/{id}', 'User\HomeController@propertyShareUpdate')->name('propertyShareUpdate');
            Route::delete('/property-share-remove/{id}', 'User\HomeController@propertyShareRemove')->name('propertyShareRemove');
            Route::post('/property-offer-update/{id}', 'User\HomeController@propertyOfferUpdate')->name('propertyOfferUpdate');
            Route::delete('/property-offer-remove/{id}', 'User\HomeController@propertyOfferRemove')->name('propertyOfferRemove');
            Route::get('/offer-list/{id}', 'User\HomeController@offerList')->name('offerList');
            Route::get('/offer-accept/{id}', 'User\HomeController@offerAccept')->name('offerAccept');
            Route::get('/offer-reject/{id}', 'User\HomeController@offerReject')->name('offerReject');
            Route::delete('/offer-remove/{id}', 'User\HomeController@offerRemove')->name('offerRemove');
            Route::get('/offer-conversation/{id}', 'User\HomeController@offerConversation')->name('offerConversation');
            Route::post('/offer/reply/message/render/', 'User\HomeController@offerReplyMessageRender')->name('offerReplyMessageRender');
            Route::post('/offer/reply/message', 'User\HomeController@offerReplyMessage')->name('offerReplyMessage');
            Route::post('/offer/payment/lock/{id}', 'User\HomeController@paymentLock')->name('paymentLock');
            Route::post('/offer/payment/lock/update/{id}', 'User\HomeController@paymentLockUpdate')->name('paymentLockUpdate');
            Route::get('/payment/lock/cancel/{id}', 'User\HomeController@paymentLockCancel')->name('paymentLockCancel');
            Route::post('/payment/lock/confirm/{id}', 'User\HomeController@paymentLockConfirm')->name('paymentLockConfirm');

            Route::post('/buy/share/{id}', 'User\HomeController@BuyShare')->name('directBuyShare');


            // wishlist section
            Route::post('/wish-list', 'User\FavouriteController@wishList')->name('wishList');

            Route::get('/wish-list-property', 'User\FavouriteController@wishListProperty')->name('wishListProperty');
            Route::delete('/wish-list-delete/{id?}', 'User\FavouriteController@favouriteListingDelete')->name('favouriteListingDelete');


            // comment section
            Route::post('/send-comment/{id?}', 'User\HomeController@sendComment')->name('sendComment');
            Route::post('/send-reply/{id?}', 'User\HomeController@sendReply')->name('sendReply');

            //transaction
            Route::get('/transaction', 'User\HomeController@transaction')->name('transaction');
            Route::get('/transaction-search', 'User\HomeController@transactionSearch')->name('transaction.search');
            Route::get('fund-history', 'User\HomeController@fundHistory')->name('fund-history');
            Route::get('fund-history-search', 'User\HomeController@fundHistorySearch')->name('fund-history.search');

            // TWO-FACTOR SECURITY
            Route::get('/twostep-security', 'User\HomeController@twoStepSecurity')->name('twostep.security');
            Route::post('twoStep-enable', 'User\HomeController@twoStepEnable')->name('twoStepEnable');
            Route::post('twoStep-disable', 'User\HomeController@twoStepDisable')->name('twoStepDisable');


            Route::get('push-notification-show', 'SiteNotificationController@show')->name('push.notification.show');
            Route::get('push.notification.readAll', 'SiteNotificationController@readAll')->name('push.notification.readAll');
            Route::get('push-notification-readAt/{id}', 'SiteNotificationController@readAt')->name('push.notification.readAt');

            Route::get('/payout', 'User\HomeController@payoutMoney')->name('payout.money');
            Route::post('/payout', 'User\HomeController@payoutMoneyRequest')->name('payout.moneyRequest');
            Route::get('/payout/preview', 'User\HomeController@payoutPreview')->name('payout.preview');
            Route::post('/payout/preview', 'User\HomeController@payoutRequestSubmit')->name('payout.submit');
            Route::post('/withdraw/paystack/{trx_id}', 'User\HomeController@paystackPayout')->name('payout.submit.paystack');
            Route::post('/withdraw/flutterwave/{trx_id}', 'User\HomeController@flutterwavePayout')->name('payout.submit.flutterwave');
            Route::post('withdraw-bank-list', 'User\HomeController@getBankList')->name('payout.getBankList');
            Route::post('withdraw-bank-from', 'User\HomeController@getBankForm')->name('payout.getBankFrom');

            Route::get('payout-history', 'User\HomeController@payoutHistory')->name('payout.history');
            Route::get('payout-history-search', 'User\HomeController@payoutHistorySearch')->name('payout.history.search');

            Route::get('/referral', 'User\HomeController@referral')->name('referral');
            Route::get('/referral-bonus', 'User\HomeController@referralBonus')->name('referral.bonus');
            Route::get('/referral-bonus-search', 'User\HomeController@referralBonusSearch')->name('referral.bonus.search');
            Route::get('/badges', 'User\HomeController@badges')->name('badges');

            // money-transfer
            Route::get('/money-transfer', 'User\HomeController@moneyTransfer')->name('money-transfer');
            Route::post('/money-transfer', 'User\HomeController@moneyTransferConfirm')->name('money.transfer');
        });


        Route::get('/profile', 'User\HomeController@profile')->name('profile');
        Route::post('/updateProfile', 'User\HomeController@updateProfile')->name('updateProfile');
        Route::post('/profileImageUpdate', 'User\HomeController@profileImageUpdate')->name('profileImageUpdate');
        Route::put('/updateInformation', 'User\HomeController@updateInformation')->name('updateInformation');
        Route::post('/updatePassword', 'User\HomeController@updatePassword')->name('updatePassword');

        Route::post('/verificationSubmit', 'User\HomeController@verificationSubmit')->name('verificationSubmit');
        Route::post('/addressVerification', 'User\HomeController@addressVerification')->name('addressVerification');


        Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
            Route::get('/', 'User\SupportController@index')->name('list');
            Route::get('/create', 'User\SupportController@create')->name('create');
            Route::post('/create', 'User\SupportController@store')->name('store');
            Route::get('/view/{ticket}', 'User\SupportController@ticketView')->name('view');
            Route::put('/reply/{ticket}', 'User\SupportController@reply')->name('reply');
            Route::get('/download/{ticket}', 'User\SupportController@download')->name('download');
        });
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', 'Admin\LoginController@showLoginForm')->name('login');
    Route::post('/', 'Admin\LoginController@login')->name('login');
    Route::post('/logout', 'Admin\LoginController@logout')->name('logout');

    Route::get('/password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset', 'Admin\Auth\ResetPasswordController@reset')->name('password.update');

    Route::get('/403', 'Admin\DashboardController@forbidden')->name('403');

    Route::group(['middleware' => ['auth:admin','permission']], function () {
        Route::get('/dashboard', 'Admin\DashboardController@dashboard')->name('dashboard');

        Route::get('/profile', 'Admin\DashboardController@profile')->name('profile');
        Route::put('/profile', 'Admin\DashboardController@profileUpdate')->name('profileUpdate');
        Route::get('/password', 'Admin\DashboardController@password')->name('password');
        Route::put('/password', 'Admin\DashboardController@passwordUpdate')->name('passwordUpdate');


        Route::get('/staff', 'Admin\ManageRolePermissionController@staff')->name('staff');
        Route::post('/staff', 'Admin\ManageRolePermissionController@storeStaff')->name('storeStaff');
        Route::put('/staff/{id}', 'Admin\ManageRolePermissionController@updateStaff')->name('updateStaff');

        Route::get('/identity-form', 'Admin\IdentyVerifyFromController@index')->name('identify-form');
        Route::post('/identity-form', 'Admin\IdentyVerifyFromController@store')->name('identify-form.store');
        Route::post('/identity-form/action', 'Admin\IdentyVerifyFromController@action')->name('identify-form.action');

        /*====== Refferal Commission =======*/
        Route::get('/referral-commission', 'Admin\ReferralCommissionController@referralCommission')->name('referral-commission');
        Route::post('/referral-commission', 'Admin\ReferralCommissionController@referralCommissionStore')->name('referral-commission.store');
        Route::post('/referral-commission/action', 'Admin\ReferralCommissionController@referralCommissionAction')->name('referral-commission.action');


        // Badge Settings
        Route::get('/badge-bonus', 'Admin\BadgeController@badgeSettings')->name('badgeSettings');
        Route::post('badge/settings/action', 'Admin\BadgeController@badgeSettingsAction')->name('badge.settings.action');
        Route::post('/sort-badges', 'Admin\BadgeController@sortBadges')->name('sort.badges');

        // Badge Ranking
        Route::get('/badge-list', 'Admin\BadgeController@badgeList')->name('badgeList');
        Route::get('/badge-create', 'Admin\BadgeController@badgeCreate')->name('badgeCreate');
        Route::post('/badge-store/{language?}', 'Admin\BadgeController@badgeStore')->name('badgeStore');
        Route::get('badge-edit/{id}', 'Admin\BadgeController@badgeEdit')->name('badgeEdit');
        Route::post('/badge-update/{id}/{language?}', 'Admin\BadgeController@badgeUpdate')->name('badgeUpdate');

        /* ===== ADMIN Amenities Manage ===== */
        Route::get('amenities', 'Admin\AmenitiesController@amenities')->name('amenities');
        Route::get('create-amenities', 'Admin\AmenitiesController@amenitiesCreate')->name('amenitiesCreate');
        Route::post('amenities-store/{language?}', 'Admin\AmenitiesController@amenitiesStore')->name('amenitiesStore');
        Route::get('amenities-edit/{id}', 'Admin\AmenitiesController@amenitiesEdit')->name('amenitiesEdit');
        Route::put('/amenities-update/{id}/{language?}', 'Admin\AmenitiesController@amenitiesUpdate')->name('amenitiesUpdate');

        /* ===== ADMIN Address Manage ===== */
        Route::get('address-list', 'Admin\AddressController@addressList')->name('addressList');
        Route::get('create-address', 'Admin\AddressController@addressCreate')->name('addressCreate');
        Route::post('address-store/{language?}', 'Admin\AddressController@addressStore')->name('addressStore');
        Route::get('address-edit/{id}', 'Admin\AddressController@addressEdit')->name('addressEdit');
        Route::put('/address-update/{id}/{language?}', 'Admin\AddressController@addressUpdate')->name('addressUpdate');

        // manage property Return Schedule
        Route::get('property-analytics/{id?}', 'Admin\ManagePropertyController@propertyAnalytics')->name('propertyAnalytics');
        Route::get('/show/property/analytics/{id}', 'admin\ManagePropertyController@showPropertyAnalytics')->name('showPropertyAnalytics');
        Route::get('/schedule-manage', 'Admin\ManagePropertyController@scheduleManage')->name('scheduleManage');
        Route::post('/schedule-create', 'Admin\ManagePropertyController@storeSchedule')->name('store.schedule');
        Route::put('/schedule-update/{id}', 'Admin\ManagePropertyController@updateSchedule')->name('update.schedule');

        // manage property list
        Route::get('/property-list/{type?}', 'Admin\ManagePropertyController@propertyList')->name('propertyList');
        Route::get('/property-create', 'Admin\ManagePropertyController@propertyCreate')->name('propertyCreate');
        Route::post('/property-store/{language?}', 'Admin\ManagePropertyController@propertyStore')->name('propertyStore');
        Route::get('/property-edit/{id}', 'Admin\ManagePropertyController@propertyEdit')->name('propertyEdit');
        Route::put('/property-update/{id}/{language?}', 'Admin\ManagePropertyController@propertyUpdate')->name('propertyUpdate');
        Route::post('/property-active', 'Admin\ManagePropertyController@activeMultiple')->name('property-active');
        Route::post('/property-inactive', 'Admin\ManagePropertyController@inActiveMultiple')->name('property-inactive');
        Route::delete('property-delete/{id}', 'Admin\ManagePropertyController@propertyDelete')->name('propertyDelete');
        Route::get('/property-share-investment', 'Admin\ManagePropertyController@shareInvestment')->name('shareInvestment');
        Route::post('/property-share-investment-action', 'Admin\ManagePropertyController@shareInvestmentAction')->name('share.investment.action');

        /* ====== WishList =====*/
        Route::get('wish-list-property', 'Admin\ManagePropertyController@wishListProperty')->name('wishListProperty');
        Route::delete('wish-list-property-delete/{id?}', 'Admin\ManagePropertyController@wishListDelete')->name('wishListDelete');




        /* ====== Plugin =====*/
        Route::get('/plugin-config', 'Admin\BasicController@pluginConfig')->name('plugin.config');
        Route::match(['get', 'post'], 'tawk-config', 'Admin\BasicController@tawkConfig')->name('tawk.control');
        Route::match(['get', 'post'], 'fb-messenger-config', 'Admin\BasicController@fbMessengerConfig')->name('fb.messenger.control');
        Route::match(['get', 'post'], 'google-recaptcha', 'Admin\BasicController@googleRecaptchaConfig')->name('google.recaptcha.control');
        Route::match(['get', 'post'], 'google-analytics', 'Admin\BasicController@googleAnalyticsConfig')->name('google.analytics.control');


        Route::match(['get', 'post'], 'currency-exchange-api-config', 'Admin\BasicController@currencyExchangeApiConfig')->name('currency.exchange.api.config');


        /* ====== Transaction Log =====*/
        Route::get('/transaction', 'Admin\LogController@transaction')->name('transaction');
        Route::get('/transaction-search', 'Admin\LogController@transactionSearch')->name('transaction.search');

        /* ====== Admin Investment Manage =====*/
        Route::get('/investments/{type?}', 'Admin\InvestmentController@investments')->name('investments');
        Route::get('/invested-user/{property_id}/type/{type?}', 'Admin\InvestmentController@seeInvestedUser')->name('seeInvestedUser');
        Route::get('/due-invested-user/{id}', 'Admin\InvestmentController@seeDueInvestedUser')->name('seeDueInvestedUser');
        Route::post('/invested-payment/single/user/{id}', 'Admin\InvestmentController@investPaymentSingleUser')->name('investPaymentSingleUser');
        Route::post('/invest-payment/all/user/{id}', 'Admin\InvestmentController@investPaymentAllUser')->name('investPaymentAllUser');

        Route::post('/disbursement-type', 'Admin\InvestmentController@disbursementType')->name('disbursementType');


        Route::get('/investments-search', 'Admin\InvestmentController@investmentsSearch')->name('investments.search');
        Route::get('/investments-details/{id?}', 'Admin\InvestmentController@investmentDetails')->name('investmentDetails');
        Route::post('investment-active', 'Admin\InvestmentController@investActive')->name('investActive');
        Route::post('investment-deactive', 'Admin\InvestmentController@investDeactive')->name('investDeactive');
        /* ====== Multiple Investment Action =====*/
        Route::post('/multiple-invest-active', 'Admin\InvestmentController@activeMultiple')->name('multiple.invest.active');
        Route::post('/multiple-invest-deactive', 'Admin\InvestmentController@deactiveMultiple')->name('multiple.invest.deactive');


        Route::get('/commissions', 'Admin\LogController@commissions')->name('commissions');
        Route::get('/commissions-search', 'Admin\LogController@commissionsSearch')->name('commissions.search');



        /*====Manage Users ====*/
        Route::get('/users', 'Admin\UsersController@index')->name('users');
        Route::get('/premium-user', 'Admin\UsersController@premiumUser')->name('premiumUser');
        Route::get('/create-user', 'Admin\UsersController@createUser')->name('createUser');
        Route::post('/users-store', 'Admin\UsersController@userStore')->name('userStore');

        Route::get('/users/search', 'Admin\UsersController@search')->name('users.search');
        Route::post('/users-active', 'Admin\UsersController@activeMultiple')->name('user-multiple-active');
        Route::post('/users-inactive', 'Admin\UsersController@inactiveMultiple')->name('user-multiple-inactive');
        Route::get('/user/edit/{id}', 'Admin\UsersController@userEdit')->name('user-edit');
        Route::post('/user/update/{id}', 'Admin\UsersController@userUpdate')->name('user-update');
        Route::post('/user/password/{id}', 'Admin\UsersController@passwordUpdate')->name('userPasswordUpdate');
        Route::post('/user/balance-update/{id}', 'Admin\UsersController@userBalanceUpdate')->name('user-balance-update');


        Route::get('/user/send-email/{id}', 'Admin\UsersController@sendEmail')->name('send-email');
        Route::post('/user/send-email/{id}', 'Admin\UsersController@sendMailUser')->name('user.email-send');
        Route::get('/user/transaction/{id}', 'Admin\UsersController@transaction')->name('user.transaction');
        Route::get('/user/fundLog/{id}', 'Admin\UsersController@funds')->name('user.fundLog');
        Route::get('/user/investmentLog/{id}', 'Admin\UsersController@investments')->name('user.plan-purchaseLog');
        Route::get('/user/payoutLog/{id}', 'Admin\UsersController@payoutLog')->name('user.withdrawal');
        Route::get('/user/commissionLog/{id}', 'Admin\UsersController@commissionLog')->name('user.commissionLog');
        Route::get('/user/referralMember/{id}', 'Admin\UsersController@referralMember')->name('user.referralMember');
        Route::post('/admin/login/as/user/{id}', 'Admin\UsersController@loginAsUser')->name('login-as-user');

        Route::get('users/kyc/pending', 'Admin\UsersController@kycPendingList')->name('kyc.users.pending');
        Route::get('users/kyc', 'Admin\UsersController@kycList')->name('kyc.users');
        Route::put('users/kycAction/{id}', 'Admin\UsersController@kycAction')->name('users.Kyc.action');
        Route::get('user/{user}/kyc', 'Admin\UsersController@userKycHistory')->name('user.userKycHistory');

        Route::get('/email-send', 'Admin\UsersController@emailToUsers')->name('email-send');
        Route::post('/email-send', 'Admin\UsersController@sendEmailToUsers')->name('email-send.store');



        /*=====Payment Log=====*/
        Route::get('payment-methods', 'Admin\PaymentMethodController@index')->name('payment.methods');
        Route::post('payment-methods/deactivate', 'Admin\PaymentMethodController@deactivate')->name('payment.methods.deactivate');
        Route::get('payment-methods/deactivate', 'Admin\PaymentMethodController@deactivate')->name('payment.methods.deactivate');
        Route::post('sort-payment-methods', 'Admin\PaymentMethodController@sortPaymentMethods')->name('sort.payment.methods');
        Route::get('payment-methods/edit/{id}', 'Admin\PaymentMethodController@edit')->name('edit.payment.methods');
        Route::put('payment-methods/update/{id}', 'Admin\PaymentMethodController@update')->name('update.payment.methods');


        // Manual Methods
        Route::get('payment-methods/manual', 'Admin\ManualGatewayController@index')->name('deposit.manual.index');
        Route::get('payment-methods/manual/new', 'Admin\ManualGatewayController@create')->name('deposit.manual.create');
        Route::post('payment-methods/manual/new', 'Admin\ManualGatewayController@store')->name('deposit.manual.store');
        Route::get('payment-methods/manual/edit/{id}', 'Admin\ManualGatewayController@edit')->name('deposit.manual.edit');
        Route::put('payment-methods/manual/update/{id}', 'Admin\ManualGatewayController@update')->name('deposit.manual.update');


        Route::get('payment/pending', 'Admin\PaymentLogController@pending')->name('payment.pending');
        Route::put('payment/action/{id}', 'Admin\PaymentLogController@action')->name('payment.action');
        Route::get('payment/log', 'Admin\PaymentLogController@index')->name('payment.log');
        Route::get('payment/search', 'Admin\PaymentLogController@search')->name('payment.search');

        Route::get('payout/settings', 'Admin\PayoutSettingsController@settings')->name('payout.settings');
        Route::post('payout/settings/action', 'Admin\PayoutSettingsController@settingsAction')->name('payout.settings.action');


        /*==========Payout Settings============*/
        Route::get('/payout-method', 'Admin\PayoutGatewayController@index')->name('payout-method');
        Route::get('/payout-method/create', 'Admin\PayoutGatewayController@create')->name('payout-method.create');
        Route::post('/payout-method/create', 'Admin\PayoutGatewayController@store')->name('payout-method.store');
        Route::get('/payout-method/{id}', 'Admin\PayoutGatewayController@edit')->name('payout-method.edit');
        Route::put('/payout-method/{id}', 'Admin\PayoutGatewayController@update')->name('payout-method.update');


        Route::get('/payout-log', 'Admin\PayoutRecordController@index')->name('payout-log');
        Route::get('/payout-log/search', 'Admin\PayoutRecordController@search')->name('payout-log.search');
        Route::get('/payout-request', 'Admin\PayoutRecordController@request')->name('payout-request');
        Route::get('/withdraw-view/{id}', 'Admin\PayoutRecordController@view')->name('payout-view');
        Route::post('/withdraw-confirm/{id}', 'Admin\PayoutRecordController@payoutConfirm')->name('payout-confirm');
        Route::post('/withdraw-cancel/{id}', 'Admin\PayoutRecordController@payoutCancel')->name('payout-cancel');
        Route::put('/payout-action/{id}', 'Admin\PayoutRecordController@action')->name('payout-action');



        /* ===== Support Ticket ====*/
        Route::get('tickets/{type?}', 'Admin\TicketController@tickets')->name('ticket');
        Route::get('tickets/view/{id}', 'Admin\TicketController@ticketReply')->name('ticket.view');
        Route::put('ticket/reply/{id}', 'Admin\TicketController@ticketReplySend')->name('ticket.reply');
        Route::get('ticket/download/{ticket}', 'Admin\TicketController@ticketDownload')->name('ticket.download');
        Route::delete('ticket/delete/{id}', 'Admin\TicketController@ticketDelete')->name('ticket.delete');

        /* ===== Subscriber =====*/
        Route::get('subscriber', 'Admin\SubscriberController@index')->name('subscriber.index');
        Route::post('subscriber/remove', 'Admin\SubscriberController@remove')->name('subscriber.remove');
        Route::get('subscriber/send-email', 'Admin\SubscriberController@sendEmailForm')->name('subscriber.sendEmail');
        Route::post('subscriber/send-email', 'Admin\SubscriberController@sendEmail')->name('subscriber.mail');


        /* ===== website controls =====*/
        Route::any('/basic-controls', 'Admin\BasicController@index')->name('basic-controls');
        Route::post('/basic-controls', 'Admin\BasicController@updateConfigure')->name('basic-controls.update');

        Route::any('/email-controls', 'Admin\EmailTemplateController@emailControl')->name('email-controls');
        Route::post('/email-controls', 'Admin\EmailTemplateController@emailConfigure')->name('email-controls.update');
        Route::post('/email-controls/action', 'Admin\EmailTemplateController@emailControlAction')->name('email-controls.action');
        Route::post('/email/test','Admin\EmailTemplateController@testEmail')->name('testEmail');

        Route::get('/email-template', 'Admin\EmailTemplateController@show')->name('email-template.show');
        Route::get('/email-template/edit/{id}', 'Admin\EmailTemplateController@edit')->name('email-template.edit');
        Route::post('/email-template/update/{id}', 'Admin\EmailTemplateController@update')->name('email-template.update');

        /*========Sms control ========*/
        Route::match(['get', 'post'], '/sms-controls', 'Admin\SmsTemplateController@smsConfig')->name('sms.config');
        Route::post('/sms-controls/action', 'Admin\SmsTemplateController@smsControlAction')->name('sms-controls.action');
        Route::get('/sms-template', 'Admin\SmsTemplateController@show')->name('sms-template');
        Route::get('/sms-template/edit/{id}', 'Admin\SmsTemplateController@edit')->name('sms-template.edit');
        Route::post('/sms-template/update/{id}', 'Admin\SmsTemplateController@update')->name('sms-template.update');

        Route::get('/notify-config', 'Admin\NotifyController@notifyConfig')->name('notify-config');
        Route::post('/notify-config', 'Admin\NotifyController@notifyConfigUpdate')->name('notify-config.update');
        Route::get('/notify-template', 'Admin\NotifyController@show')->name('notify-template.show');
        Route::get('/notify-template/edit/{id}', 'Admin\NotifyController@edit')->name('notify-template.edit');
        Route::post('/notify-template/update/{id}', 'Admin\NotifyController@update')->name('notify-template.update');


        /* ===== ADMIN Language SETTINGS ===== */
        Route::get('language', 'Admin\LanguageController@index')->name('language.index');
        Route::get('language/create', 'Admin\LanguageController@create')->name('language.create');
        Route::post('language/create', 'Admin\LanguageController@store')->name('language.store');
        Route::get('language/{language}', 'Admin\LanguageController@edit')->name('language.edit');
        Route::put('language/{language}', 'Admin\LanguageController@update')->name('language.update');
        Route::delete('language/{language}', 'Admin\LanguageController@delete')->name('language.delete');
        Route::get('/language/keyword/{id}', 'Admin\LanguageController@keywordEdit')->name('language.keywordEdit');
        Route::put('/language/keyword/{id}', 'Admin\LanguageController@keywordUpdate')->name('language.keywordUpdate');
        Route::post('/language/importJson', 'Admin\LanguageController@importJson')->name('language.importJson');
        Route::post('store-key/{id}', 'Admin\LanguageController@storeKey')->name('language.storeKey');
        Route::put('update-key/{id}', 'Admin\LanguageController@updateKey')->name('language.updateKey');
        Route::delete('delete-key/{id}', 'Admin\LanguageController@deleteKey')->name('language.deleteKey');

        Route::get('/manage/theme', 'Admin\BasicController@manageTheme')->name('manage.theme');
        Route::put('/activate/theme/{name}', 'Admin\BasicController@activateTheme')->name('activate.themeUpdate');
        Route::get('/logo-seo', 'Admin\BasicController@logoSeo')->name('logo-seo');
        Route::put('/logoUpdate', 'Admin\BasicController@logoUpdate')->name('logoUpdate');
        Route::put('/seoUpdate', 'Admin\BasicController@seoUpdate')->name('seoUpdate');
        Route::get('/breadcrumb', 'Admin\BasicController@breadcrumb')->name('breadcrumb');
        Route::put('/breadcrumb', 'Admin\BasicController@breadcrumbUpdate')->name('breadcrumbUpdate');

        /* ===== ADMIN TEMPLATE SETTINGS ===== */
        Route::get('template/{section}', 'Admin\TemplateController@show')->name('template.show');
        Route::put('template/{section}/{language}', 'Admin\TemplateController@update')->name('template.update');
        Route::get('contents/{content}', 'Admin\ContentController@index')->name('content.index');
        Route::get('content-create/{content}', 'Admin\ContentController@create')->name('content.create');
        Route::put('content-create/{content}/{language?}', 'Admin\ContentController@store')->name('content.store');
        Route::get('content-show/{content}/{name?}', 'Admin\ContentController@show')->name('content.show');
        Route::put('content-update/{content}/{language?}', 'Admin\ContentController@update')->name('content.update');
        Route::delete('contents/{id}', 'Admin\ContentController@contentDelete')->name('content.delete');

        Route::get('push-notification-show', 'SiteNotificationController@showByAdmin')->name('push.notification.show');
        Route::get('push.notification.readAll', 'SiteNotificationController@readAllByAdmin')->name('push.notification.readAll');
        Route::get('push-notification-readAt/{id}', 'SiteNotificationController@readAt')->name('push.notification.readAt');
        Route::match(['get', 'post'], 'pusher-config', 'SiteNotificationController@pusherConfig')->name('pusher.config');

        /* ===== ADMIN Blog Manage ===== */
        Route::get('blog-category', 'Admin\BlogController@categoryList')->name('blogCategory');
        Route::get('blog-category-create', 'Admin\BlogController@blogCategoryCreate')->name('blogCategoryCreate');
        Route::post('blog-category-store/{language?}', 'Admin\BlogController@blogCategoryStore')->name('blogCategoryStore');
        Route::get('blog-category-edit/{id}', 'Admin\BlogController@blogCategoryEdit')->name('blogCategoryEdit');
        Route::put('/blog-category-update/{id}/{language?}', 'Admin\BlogController@blogCategoryUpdate')->name('blogCategoryUpdate');
        Route::delete('/blog-category-delete/{id}', 'Admin\BlogController@blogCategoryDelete')->name('blogCategoryDelete');

        Route::get('blog-list', 'Admin\BlogController@blogList')->name('blogList');
        Route::get('blog-create', 'Admin\BlogController@blogCreate')->name('blogCreate');
        Route::post('blog-store/{language?}', 'Admin\BlogController@blogStore')->name('blogStore');
        Route::get('blog-edit/{id}', 'Admin\BlogController@blogEdit')->name('blogEdit');
        Route::put('blog-update/{id}/{language?}', 'Admin\BlogController@blogUpdate')->name('blogUpdate');
        Route::delete('blog-delete/{id}', 'Admin\BlogController@blogDelete')->name('blogDelete');
    });
});


Route::group(['middleware' => ['Maintenance']], function () {
    Route::match(['get', 'post'], 'success', 'PaymentController@success')->name('success');
    Route::match(['get', 'post'], 'failed', 'PaymentController@failed')->name('failed');
    Route::match(['get', 'post'], 'payment/{code}/{trx?}/{type?}', 'PaymentController@gatewayIpn')->name('ipn');

    Route::get('/language/{code?}', 'FrontendController@language')->name('language');

    Route::get('/', 'FrontendController@index')->name('home');
    Route::get('/about', 'FrontendController@about')->name('about');
    Route::get('/plan', 'FrontendController@planList')->name('plan');

    Route::get('/blog', 'FrontendController@blog')->name('blog');
    Route::get('/blog-details/{slug}/{id}', 'FrontendController@blogDetails')->name('blogDetails');
    Route::get('/blog-category/{slug}/{id}', 'FrontendController@CategoryWiseBlog')->name('CategoryWiseBlog');
    Route::get('blog-search', 'FrontendController@blogSearch')->name('blogSearch');

    Route::get('/property', 'FrontendController@property')->name('property');
    Route::get('/property-details/{title?}/{id}', 'FrontendController@propertyDetails')->name('propertyDetails');
    Route::get('/investor-profile/{username?}/{id?}', 'FrontendController@investorProfile')->name('investorProfile');
    Route::get('/faq', 'FrontendController@faq')->name('faq');


    Route::get('/contact', 'FrontendController@contact')->name('contact');
    Route::post('/contact', 'FrontendController@contactSend')->name('contact.send');

    Route::post('/subscribe', 'FrontendController@subscribe')->name('subscribe');

    Route::get('/{getLink}/{content_id}', 'FrontendController@getLink')->name('getLink');
});

