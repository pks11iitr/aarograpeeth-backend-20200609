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
   Route::group(['prefix'=>'therapy'], function(){
        Route::get('/','SuperAdmin\TherapistController@index')->name('therapy.list');
        Route::get('create','SuperAdmin\TherapistController@create')->name('therapy.create');
        Route::post('store','SuperAdmin\TherapistController@store')->name('therapy.store');
        Route::get('edit/{id}','SuperAdmin\TherapistController@edit')->name('therapy.edit');
        Route::post('update/{id}','SuperAdmin\TherapistController@update')->name('therapy.update');
        Route::post('document/{id}','SuperAdmin\TherapistController@document')->name('therapy.document');
        Route::get('delete/{id}','SuperAdmin\TherapistController@delete')->name('therapy.delete');

    });
  Route::group(['prefix'=>'clinic'], function(){
        Route::get('/','SuperAdmin\ClinicController@index')->name('clinic.list');
        Route::get('create','SuperAdmin\ClinicController@create')->name('clinic.create');
        Route::post('store','SuperAdmin\ClinicController@store')->name('clinic.store');
        Route::get('edit/{id}','SuperAdmin\ClinicController@edit')->name('clinic.edit');
        Route::post('update/{id}','SuperAdmin\ClinicController@update')->name('clinic.update');
        Route::post('document/{id}','SuperAdmin\ClinicController@document')->name('clinic.document');
        Route::get('delete/{id}','SuperAdmin\ClinicController@delete')->name('clinic.delete');
        Route::post('therapystore/{id}','SuperAdmin\ClinicController@therapystore')->name('clinic.therapystore');
        Route::get('therapyeedit/{id}','SuperAdmin\ClinicController@therapyedit')->name('clinic.therapyedit');
        Route::post('therapyeedit/{id}','SuperAdmin\ClinicController@therapyupdate');
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

    });

});


Route::group(['prefix'=>'partners', 'middleware'=>['auth', 'acl'], 'is'=>'clinic-admin'], function() {
    Route::get('/dashboard', 'ClinicAdmin\DashboardController@index')->name('clinic.home');

    Route::group(['prefix'=>'order'], function(){
        Route::get('/','ClinicAdmin\OrderController@index')->name('order.list');
        Route::get('details/{id}','ClinicAdmin\OrderController@details')->name('order.details');
        Route::get('edit/{id}','ClinicAdmin\OrderController@edit')->name('order.edit');
        Route::post('store','ClinicAdmin\OrderController@store')->name('order.store');

    });

    Route::group(['prefix'=>'therapistadmin'], function(){
        Route::get('/','ClinicAdmin\TherapistController@index')->name('therapistadmin.list');
        Route::get('create','ClinicAdmin\TherapistController@create')->name('therapistadmin.create');
        Route::post('store','ClinicAdmin\TherapistController@store')->name('therapistadmin.store');
        Route::get('edit/{id}','ClinicAdmin\TherapistController@edit')->name('therapistadmin.edit');
        Route::post('update/{id}','ClinicAdmin\TherapistController@update')->name('therapistadmin.update');

    });

});


Route::group(['prefix'=>'therapistadmin', 'middleware'=>['auth', 'acl'], 'is'=>'clinic-therapist'], function() {

    Route::get('/dashboard', 'TherapistAdmin\DashboardController@index')->name('clinic.therapist.home');

    Route::group(['prefix'=>'therapistwork'], function(){
        Route::get('/','TherapistAdmin\TherapistWorkController@index')->name('therapistwork.list');
        Route::get('past','TherapistAdmin\TherapistWorkController@past')->name('therapistwork.past');
        Route::get('details/{id}','TherapistAdmin\TherapistWorkController@details')->name('therapistwork.details');
        Route::post('detailstore/{id}','TherapistAdmin\TherapistWorkController@detailstore')->name('therapistwork.detailstore');

    });

});
