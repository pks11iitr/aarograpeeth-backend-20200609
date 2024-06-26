<?php

use Illuminate\Http\Request;
$api = app('Dingo\Api\Routing\Router');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api->post('login', 'Customer\Auth\LoginController@login');
$api->post('login-with-otp', 'Customer\Auth\LoginController@loginWithOtp');
$api->post('register', 'Customer\Auth\RegisterController@register');
$api->post('forgot', 'Customer\Auth\ForgotPasswordController@forgot');
$api->post('verify-otp', 'Customer\Auth\OtpController@verify');
$api->post('resend-otp', 'Customer\Auth\OtpController@resend');
$api->post('update-password', 'Customer\Auth\ForgotPasswordController@updatePassword');

//home page api
$api->get('home', 'Customer\Api\HomeController@home');

//clinic screen apis
$api->get('clinics', ['as'=>'clinics.list', 'uses'=>'Customer\Api\ClinicController@index']);
$api->get('therapies', ['as'=>'therapies.list', 'uses'=>'Customer\Api\TherapyController@index']);
$api->get('clinic/{id}', ['as'=>'clinics.details', 'uses'=>'Customer\Api\ClinicController@details']);
$api->get('clinic-therapy/{clinicid}/{therapyid}', ['as'=>'clinics.therapy.details', 'uses'=>'Customer\Api\ClinicController@clinicTherapyDetails']);

//$api->post('get-available-slots/{clinicid}/{therapy_id}', ['as'=>'clinics.available.slots', 'uses'=>'Customer\Api\ClinicController@getAvailableSlots']);



//therapy screen api
$api->get('therapy/{id}', ['as'=>'therapy.details', 'uses'=>'Customer\Api\TherapyController@details']);
$api->post('nearby-therapists', ['as'=>'therapy.nearby.therapistadmin', 'uses'=>'Customer\Api\TherapyController@nearbyTherapists']);


$api->get('products-home', ['as'=>'products.home', 'uses'=>'Customer\Api\ProductController@home']);
$api->get('product/{id}', ['as'=>'product.details', 'uses'=>'Customer\Api\ProductController@details']);
$api->get('topdeals-products', ['as'=>'product.deals', 'uses'=>'Customer\Api\ProductController@topdeals']);
$api->get('bestseller-products', ['as'=>'product.deals', 'uses'=>'Customer\Api\ProductController@bestseller']);

$api->get('reviews/{type}/{id}', ['as'=>'reviews', 'uses'=>'Customer\Api\ReviewController@index']);

$api->get('search', ['as'=>'search', 'uses'=>'Customer\Api\SearchController@index']);


$api->get('profile', ['as'=>'profile', 'uses'=>'Customer\Api\ProfileController@view']);
$api->post('profile', ['as'=>'profile', 'uses'=>'Customer\Api\ProfileController@update']);


//Order apis
$api->post('initiate-order', ['as'=>'initiate.order', 'uses'=>'Customer\Api\OrderController@initiateOrder']);

$api->get('order-details/{id}', ['as'=>'order.details', 'uses'=>'Customer\Api\OrderController@orderdetails']);

$api->get('order-history', ['as'=>'order.history', 'uses'=>'Customer\Api\OrderController@index']);

$api->get('cancel-booking/{order_id}', ['as'=>'order.cancel', 'uses'=>'Customer\Api\OrderController@cancelBooking']);

$api->post('reschedule-order/{id}', ['as'=>'order.reschedule', 'uses'=>'Customer\Api\OrderController@rescheduleOrder']);


// APis For New Therapy Bookings

$api->post('initiate-order', ['as'=>'initiate.order', 'uses'=>'Customer\Api\OrderController@initiateOrder']);

$api->post('create-schedule/{id}', ['as'=>'order.create.schedule', 'uses'=>'Customer\Api\OrderController@setSchedule']);

$api->get('display-schedule/{id}', ['as'=>'order.display.reschedule', 'uses'=>'Customer\Api\OrderController@displaySchedule']);

$api->get('delete-booking/{order_id}/{booking_id}', ['as'=>'order.delete.booking', 'uses'=>'Customer\Api\OrderController@deleteBooking']);






$api->post('update-contact/{id}', ['as'=>'order.contact.update', 'uses'=>'Customer\Api\OrderController@addContactDetails']);

$api->post('update-cart', ['as'=>'cart.update', 'uses'=>'Customer\Api\CartController@updateCartItems']);
$api->get('cart-details', ['as'=>'cart.details', 'uses'=>'Customer\Api\CartController@index']);


//payment apis
$api->post('initiate-payment/{id}', ['as'=>'order.payment', 'uses'=>'Customer\Api\PaymentController@initiatePayment']);
$api->post('verify-payment', ['as'=>'order.payment.verify', 'uses'=>'Customer\Api\PaymentController@verifyPayment']);


//wallet apis
$api->get('wallet-balance', ['as'=>'wallet.balance', 'uses'=>'Customer\Api\WalletController@getWalletBalance']);
$api->get('wallet-history', ['as'=>'wallet.history', 'uses'=>'Customer\Api\WalletController@history']);

$api->post('recharge', ['as'=>'wallet.add.money', 'uses'=>'Customer\Api\WalletController@addMoney']);
$api->post('verify-recharge', ['as'=>'wallet.add.money', 'uses'=>'Customer\Api\WalletController@verifyRecharge']);

//complaints api
$api->get('complaints', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@index']);
$api->post('complaints', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@create']);
$api->get('complaint/{id}', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@messages']);
$api->post('complaint/{id}', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@postMessage']);

//news api
$api->get('news', ['as'=>'new.list', 'uses'=>'Customer\Api\NewsController@index']);
$api->get('news/{id}', ['as'=>'new.list', 'uses'=>'Customer\Api\NewsController@details']);

//notifications api
$api->get('notifications', ['as'=>'notifications.list', 'uses'=>'Customer\Api\NotificationController@index']);

$api->group(['middleware' => ['customer-auth']], function ($api) {
    $api->post('get-available-slots/{order_id}', ['as'=>'clinics.available.slots', 'uses'=>'Customer\Api\OrderController@getAvailableSlots']);

    $api->get('cancel-complete-order/{order_id}', ['as'=>'cancel.all.slots', 'uses'=>'Customer\Api\OrderController@cancelAll']);

    $api->get('get-reschedule-slots/{order_id}/{booking_id}', ['as'=>'cancel.all.slots', 'uses'=>'Customer\Api\OrderController@getRescheduleSlots']);

    $api->post('reschedule-booking/{order_id}/{booking_id}', ['as'=>'reschedule.booking', 'uses'=>'Customer\Api\OrderController@rescheduleBooking']);

    $api->post('initiate-reschedule-payment/{order_id}/{booking_id}', ['as'=>'reschedule.payment.initiate', 'uses'=>'Customer\Api\PaymentController@initiateReschedulePayment']);

    $api->post('verify-reschedule-payment', ['as'=>'reschedule.payment.verify', 'uses'=>'Customer\Api\PaymentController@verifyReschedulePayment']);

    $api->post('reviews/{order_id}/{item_id}', ['as'=>'reviews.post.item', 'uses'=>'Customer\Api\ReviewController@post']);

    $api->post('review-session/{order_id}/{session_id}', ['as'=>'reviews.post.session', 'uses'=>'Customer\Api\ReviewController@postSessionReview']);

    $api->get('booking-details/{order_id}/{session_id}', ['as'=>'booking.details', 'uses'=>'Customer\Api\OrderController@bookingDetails']);

    $api->get('get-therapist-location/{order_id}/{session_id}', ['as'=>'booking.therapist.location', 'uses'=>'Customer\Api\OrderController@getTherapistLocation']);

});


$api->get('download-invoice/{refid}', ['as'=>'download.invoice', 'uses'=>'Customer\Api\OrderController@downloadInvoice']);

/*
 * Therapist Apis Starts Here
 */

$api->group(['prefix' => 'therapist'], function ($api) {
    $api->post('login', 'Therapist\Auth\LoginController@login');
    $api->post('login-with-otp', 'Therapist\Auth\LoginController@loginWithOtp');
    $api->post('register', 'Therapist\Auth\RegisterController@register');
    $api->post('forgot', 'Therapist\Auth\ForgotPasswordController@forgot');
    $api->post('verify-otp', 'Therapist\Auth\OtpController@verify');
    $api->post('resend-otp', 'Therapist\Auth\OtpController@resend');
    $api->post('update-password', 'Therapist\Auth\ForgotPasswordController@updatePassword');

    $api->group(['middleware' => ['therapistadmin-auth']], function ($api) {
        $api->post('update-location', 'Therapist\Api\LocationController@updateLocation');
        $api->post('upload-image', 'Therapist\Api\ProfileController@updateImage');

        $api->get('get-services', 'Therapist\Api\ProfileController@getServices');

        $api->post('add-services', 'Therapist\Api\ProfileController@addServices');

        $api->get('delete-therapy/{id}', 'Therapist\Api\ProfileController@deleteService');

        $api->get('my-profile', 'Therapist\Api\ProfileController@myProfile');
        $api->post('update-availability', 'Therapist\Api\ProfileController@updateavalibility');
        $api->get('my-availability', 'Therapist\Api\ProfileController@myapdateavalibility');
        $api->get('open-booking', 'Therapist\Api\TherapiestOrderController@openbooking');

        $api->get('completed-booking', 'Therapist\Api\TherapiestOrderController@completedbookings');

        $api->get('open-booking-details/{id}', 'Therapist\Api\TherapiestOrderController@openbookingdetails');
        $api->get('completed-booking-details/{id}', 'Therapist\Api\TherapiestOrderController@completedbookingdetails2');
        $api->get('journey-started/{id}', 'Therapist\Api\TherapiestOrderController@journey_started');
        //$api->get('disease-point', 'Therapist\Api\TherapiestOrderController@diseasepoint');
        //$api->get('treatment-list', 'Therapist\Api\TherapiestOrderController@treatmentlist');
        //$api->post('send-disease-point/{id}', 'Therapist\Api\TherapiestOrderController@send_diesase_point');
//        $api->post('treatment-suggestation/{id}', 'Therapist\Api\TherapiestOrderController@treatmentsuggestation');
        $api->get('painpoint-relif/{id}', 'Therapist\Api\TherapiestOrderController@pain_point_relif');
        $api->post('pain-relief-rating/{id}', 'Therapist\Api\TherapiestOrderController@pain_relief_update_rating');

        $api->get('my-finances', 'Therapist\Api\FinanceController@index');

        $api->post('therapist-feedback/{id}', 'Therapist\Api\TherapiestOrderController@postCustomerReview');

        $api->get('diseases-list/{id}', 'Therapist\Api\TherapiestOrderController@mainDiseaseList');
        $api->post('add-main-diseases/{id}', 'Therapist\Api\TherapiestOrderController@addMainDiseases');

        $api->get('pain-points/{id}', 'Therapist\Api\TherapiestOrderController@painPointList');
        $api->post('update-pain-points/{id}', 'Therapist\Api\TherapiestOrderController@updatePainPointList');

//        $api->get('diagnose-list-before-treatment/{id}', 'Therapist\Api\TherapiestOrderController@diagnoseListBeforeTreatment');
//        $api->post('add-diagnose-before-treatment/{id}', 'Therapist\Api\TherapiestOrderController@addDiagnoseBeforeTreatment');

        $api->get('treatments-suggested/{id}', 'Therapist\Api\TherapiestOrderController@suggestedTreatments');
        $api->post('treatments-given/{id}', 'Therapist\Api\TherapiestOrderController@chooseTreatments');

        $api->get('other-diseases/{id}', 'Therapist\Api\TherapiestOrderController@otherDiseasesList');
        $api->post('update-other-diseases/{id}', 'Therapist\Api\TherapiestOrderController@updateOtherDiseases');

        $api->get('get-patient-details/{id}', 'Therapist\Api\TherapiestOrderController@getPatientDetails');
        $api->post('update-patient-details/{id}', 'Therapist\Api\TherapiestOrderController@updatePatientDetails');



    });

});

$api->get('therapist-instructions', 'Therapist\Api\InstructionController@instructions');
$api->get('customer-instructions', 'Customer\Api\InstructionController@instructions');


$api->get('about-us','CommonController@about');
$api->get('terms-n-conditions','CommonController@term');
$api->get('privacy-policy','CommonController@privacy');
