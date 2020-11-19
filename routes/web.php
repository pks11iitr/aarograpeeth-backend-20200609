<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware'=>['auth', 'acl'], 'is'=>'admin|clinic-admin|clinic-therapist'], function(){

    Route::get('/role-check', 'SuperAdmin\HomeController@check_n_redirect')->name('user.role.check');

});


Route::group(['middleware'=>['auth', 'acl'], 'is'=>'admin'], function(){

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::get('/dashboard', 'SuperAdmin\DashboardController@index')->name('home');

   Route::group(['prefix'=>'banners'], function(){
        Route::get('/','SuperAdmin\BannerController@index')->name('banners.list');
        Route::get('create','SuperAdmin\BannerController@create')->name('banners.create');
        Route::post('store','SuperAdmin\BannerController@store')->name('banners.store');
        Route::get('edit/{id}','SuperAdmin\BannerController@edit')->name('banners.edit');
        Route::post('update/{id}','SuperAdmin\BannerController@update')->name('banners.update');
        Route::get('delete/{id}','SuperAdmin\BannerController@delete')->name('banners.delete');
    });

    Route::group(['prefix'=>'disease'], function(){
        Route::get('/','SuperAdmin\DiseaseController@index')->name('disease.list');
        Route::get('create','SuperAdmin\DiseaseController@create')->name('disease.create');
        Route::post('store','SuperAdmin\DiseaseController@store')->name('disease.store');
        Route::get('edit/{id}','SuperAdmin\DiseaseController@edit')->name('disease.edit');
        Route::post('update/{id}','SuperAdmin\DiseaseController@update')->name('disease.update');
        Route::get('delete/{id}','SuperAdmin\DiseaseController@delete')->name('disease.delete');
    });

    Route::group(['prefix'=>'painpoint'], function(){
        Route::get('/','SuperAdmin\PainPointController@index')->name('painpoint.list');
        Route::get('create','SuperAdmin\PainPointController@create')->name('painpoint.create');
        Route::post('store','SuperAdmin\PainPointController@store')->name('painpoint.store');
        Route::get('edit/{id}','SuperAdmin\PainPointController@edit')->name('painpoint.edit');
        Route::post('update/{id}','SuperAdmin\PainPointController@update')->name('painpoint.update');
        Route::get('delete/{id}','SuperAdmin\PainPointController@delete')->name('painpoint.delete');
    });

    Route::group(['prefix'=>'treatment'], function(){
        Route::get('/','SuperAdmin\TreatmentController@index')->name('treatment.list');
        Route::get('create','SuperAdmin\TreatmentController@create')->name('treatment.create');
        Route::post('store','SuperAdmin\TreatmentController@store')->name('treatment.store');
        Route::get('edit/{id}','SuperAdmin\TreatmentController@edit')->name('treatment.edit');
        Route::post('update/{id}','SuperAdmin\TreatmentController@update')->name('treatment.update');
        Route::get('delete/{id}','SuperAdmin\TreatmentController@delete')->name('treatment.delete');
    });

    Route::group(['prefix'=>'therapy'], function(){
        Route::get('/','SuperAdmin\TherapistController@index')->name('therapy.list');
        Route::get('create','SuperAdmin\TherapistController@create')->name('therapy.create');
        Route::post('store','SuperAdmin\TherapistController@store')->name('therapy.store');
        Route::get('edit/{id}','SuperAdmin\TherapistController@edit')->name('therapy.edit');
        Route::post('update/{id}','SuperAdmin\TherapistController@update')->name('therapy.update');
        Route::post('document/{id}','SuperAdmin\TherapistController@document')->name('therapy.document');
        Route::get('delete/{id}','SuperAdmin\TherapistController@delete')->name('therapy.delete');

        // routes for admin search available therapist
        Route::get('available-therapists','SuperAdmin\TherapistController@getAvailableHomeTherapist')->name('therapy.available.therapist');

        // routes for admin search available slots
        Route::get('available-slots','SuperAdmin\TherapistController@getAvailableTimeSlots')->name('therapy.available.slots');


    });

  Route::group(['prefix'=>'clinic'], function(){
        Route::get('/','SuperAdmin\ClinicController@index')->name('clinic.list');
        Route::get('create','SuperAdmin\ClinicController@create')->name('clinic.create');
        Route::post('store','SuperAdmin\ClinicController@store')->name('clinic.store');
        Route::get('edit/{id}','SuperAdmin\ClinicController@edit')->name('clinic.edit');
        Route::post('update/{id}','SuperAdmin\ClinicController@update')->name('clinic.update');
        Route::post('therapystore/{id}','SuperAdmin\ClinicController@therapystore')->name('clinic.therapystore');
        Route::get('therapyeedit/{id}','SuperAdmin\ClinicController@therapyedit')->name('clinic.therapyedit');
        Route::post('therapyeedit/{id}','SuperAdmin\ClinicController@therapyupdate');

        Route::get('available-therapists','SuperAdmin\ClinicController@getAvailableTherapistInClinic')->name('clinic.available.therapist');
        Route::get('available-slots','SuperAdmin\ClinicController@getAvailableTimeSlots')->name('clinic.available.slots');

    });

    Route::group(['prefix'=>'customer'], function(){
        Route::get('/','SuperAdmin\CustomerController@index')->name('customer.list');
        Route::get('edit/{id}','SuperAdmin\CustomerController@edit')->name('customer.edit');
        Route::post('update/{id}','SuperAdmin\CustomerController@update')->name('customer.update');
        Route::post('send_message','SuperAdmin\CustomerController@send_message')->name('customer.send_message');
    });

   Route::group(['prefix'=>'product'], function(){
        Route::get('/','SuperAdmin\ProductController@index')->name('product.list');
        Route::get('create','SuperAdmin\ProductController@create')->name('product.create');
        Route::post('store','SuperAdmin\ProductController@store')->name('product.store');
        Route::get('edit/{id}','SuperAdmin\ProductController@edit')->name('product.edit');
        Route::post('update/{id}','SuperAdmin\ProductController@update')->name('product.update');
        Route::post('document/{id}','SuperAdmin\ProductController@document')->name('product.document');
        Route::get('delete/{id}','SuperAdmin\ProductController@delete')->name('product.delete');
    });

    Route::group(['prefix'=>'orders'], function(){
        Route::get('/','SuperAdmin\OrderController@index')->name('orders.list');
        Route::get('view/{id}','SuperAdmin\OrderController@details')->name('order.view');
        Route::get('product','SuperAdmin\OrderController@product')->name('orders.product');
        Route::get('productdetails/{id}','SuperAdmin\OrderController@productdetails')->name('order.productdetails');
        Route::get('change-status/{id}','SuperAdmin\OrderController@changeStatus')->name('orders.status.change');
//        Route::get('booking-edit','SuperAdmin\OrderController@editTherapySession')->name('order.booking.edit');
//        Route::post('booking-edit','SuperAdmin\OrderController@updateTherapySession');

    });

    Route::group(['prefix'=>'sessions'], function(){
        Route::get('list/{type}','SuperAdmin\SessionController@index')->name('sessions.list');
        Route::get('details/{type}/{id}','SuperAdmin\SessionController@details')->name('session.details');
        Route::get('therapist/{type}/{id}','SuperAdmin\SessionController@index')->name('therapist.sessions');
    });


    Route::group(['prefix'=>'complain'], function(){
        Route::get('/','SuperAdmin\ComplainController@index')->name('complain.list');
        Route::get('view/{id}','SuperAdmin\ComplainController@details')->name('complain.view');
        Route::post('message','SuperAdmin\ComplainController@send_message')->name('complain.message');

    });

    Route::group(['prefix'=>'news'], function(){
        Route::get('/','SuperAdmin\NewsUpdateController@index')->name('news.list');
        Route::get('create','SuperAdmin\NewsUpdateController@create')->name('news.create');
        Route::post('store','SuperAdmin\NewsUpdateController@store')->name('news.store');
        Route::get('edit/{id}','SuperAdmin\NewsUpdateController@edit')->name('news.edit');
        Route::post('update/{id}','SuperAdmin\NewsUpdateController@update')->name('news.update');

    });

    Route::group(['prefix'=>'notification'], function(){
        Route::get('create','SuperAdmin\NotificationController@create')->name('notification.create');
        Route::post('store','SuperAdmin\NotificationController@store')->name('notification.store');

    });

    Route::group(['prefix'=>'video'], function(){
        Route::get('/','SuperAdmin\VideoController@index')->name('video.list');
        Route::get('create','SuperAdmin\VideoController@create')->name('video.create');
        Route::post('store','SuperAdmin\VideoController@store')->name('video.store');
        Route::get('edit/{id}','SuperAdmin\VideoController@edit')->name('video.edit');
        Route::post('update/{id}','SuperAdmin\VideoController@update')->name('video.update');
        Route::get('delete/{id}','SuperAdmin\VideoController@delete')->name('video.delete');

    });

    Route::group(['prefix'=>'therapists'], function(){
        Route::get('/','SuperAdmin\NewTherapistController@index')->name('therapists.list');
        Route::get('create','SuperAdmin\NewTherapistController@create')->name('therapists.create');
        Route::post('store','SuperAdmin\NewTherapistController@store')->name('therapists.store');
        Route::get('edit/{id}','SuperAdmin\NewTherapistController@edit')->name('therapists.edit');
        Route::post('update/{id}','SuperAdmin\NewTherapistController@update')->name('therapists.update');
        Route::post('therapystore/{id}','SuperAdmin\NewTherapistController@therapystore')->name('therapists.therapystore');
        Route::get('therapyedit/{id}','SuperAdmin\NewTherapistController@therapyedit')->name('therapists.therapyedit');
        Route::post('therapyupdate/{id}','SuperAdmin\NewTherapistController@therapyupdate')->name('therapists.therapyupdate');
        Route::get('available-therapists','SuperAdmin\TherapistController@getAvailableHomeTherapist')->name('therapy.available.therapist');
        Route::get('available-slots','SuperAdmin\TherapistController@getAvailableTimeSlots')->name('therapy.available.slots');

    });

    Route::group(['prefix'=>'review'], function(){
        Route::get('/','SuperAdmin\ReviewController@index')->name('review.list');
        Route::get('status/{id}/{isactive}','SuperAdmin\ReviewController@status')->name('review.status');

    });

});

Route::group(['middleware'=>['auth', 'acl'], 'is'=>'admin|clinic-admin'], function(){

    Route::group(['prefix'=>'clinic'], function(){
        Route::post('document/{id}','SuperAdmin\ClinicController@document')->name('clinic.document');
        Route::get('delete/{id}','SuperAdmin\ClinicController@delete')->name('clinic.delete');
    });

    Route::group(['prefix'=>'session'], function(){

        Route::get('booking-edit','SuperAdmin\SessionController@editTherapistSession')->name('session.booking.edit');
        Route::post('booking-edit','SuperAdmin\SessionController@updateTherapistSession');

    });

});



Route::group(['prefix'=>'partners', 'middleware'=>['auth', 'acl'], 'is'=>'clinic-admin'], function() {
    Route::get('/dashboard', 'ClinicAdmin\DashboardController@index')->name('clinicadmin.home');

    Route::get('/profile', 'ClinicAdmin\ProfileController@view')->name('clinicadmin.profile');
    Route::post('/profile', 'ClinicAdmin\ProfileController@update');
    Route::post('/add-therapy', 'ClinicAdmin\ProfileController@therapystore')->name('clinicadmin.therapy.add');

    Route::group(['prefix'=>'order'], function(){
        Route::get('/','ClinicAdmin\OrderController@index')->name('clinicadmin.order.list');
        Route::get('details/{id}','ClinicAdmin\OrderController@details')->name('clinicadmin.order.details');
        Route::get('edit/{id}','ClinicAdmin\OrderController@edit')->name('clinicadmin.order.edit');
        Route::get('booking-edit','ClinicAdmin\OrderController@editClinicSession')->name('clinicadmin.booking.edit');
        Route::post('booking-edit','ClinicAdmin\OrderController@updateClinicSession');

        Route::get('available-therapists','ClinicAdmin\OrderController@getAvailableTherapistInClinic')->name('clinicadmin.available.therapist');

        Route::get('available-slots','ClinicAdmin\OrderController@getAvailableTimeSlots')->name('clinicadmin.available.slots');

    });

    Route::group(['prefix'=>'therapist'], function(){
        Route::get('/','ClinicAdmin\TherapistController@index')->name('clinicadmin.therapist.list');
        Route::get('create','ClinicAdmin\TherapistController@create')->name('clinicadmin.therapist.create');
        Route::post('store','ClinicAdmin\TherapistController@store')->name('clinicadmin.therapist.store');
        Route::get('edit/{id}','ClinicAdmin\TherapistController@edit')->name('clinicadmin.therapist.edit');
        Route::post('update/{id}','ClinicAdmin\TherapistController@update')->name('clinicadmin.therapist.update');
    });

    Route::group(['prefix'=>'session'], function(){

        Route::get('list/{type}/{id?}','ClinicAdmin\SessionController@index_session')->name('therapist.sessions.list');
        Route::get('details/{type}/{id}','ClinicAdmin\SessionController@details')->name('therapist.session.details');

    });

});


Route::group(['prefix'=>'therapistadmin', 'middleware'=>['auth', 'acl'], 'is'=>'clinic-therapist'], function() {

    Route::get('/dashboard', 'TherapistAdmin\DashboardController@index')->name('clinic.therapist.home');

    Route::group(['prefix'=>'therapistwork'], function(){
        Route::get('/','TherapistAdmin\TherapistWorkController@index')->name('therapistwork.list');
        Route::get('past','TherapistAdmin\TherapistWorkController@past')->name('therapistwork.past');
        Route::get('details/{id}','TherapistAdmin\TherapistWorkController@details')->name('therapistwork.details');

        Route::post('update-diagnose/{id}','TherapistAdmin\TherapistWorkController@updateDiagnose')->name('therapistwork.diagnose');
        Route::post('select-treatment/{id}','TherapistAdmin\TherapistWorkController@startTherapy')->name('therapistwork.start');

        Route::post('update-feedback/{id}','TherapistAdmin\TherapistWorkController@completeTherapy')->name('therapistwork.feedback');



    });

});


Route::get('about-us','StaticPagesController@aboutus')->name('about.us');
Route::get('terms-n-conditions','StaticPagesController@terms')->name('terms.cond');
Route::get('privacy-policy','StaticPagesController@privacy')->name('privacy.policy');
