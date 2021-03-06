<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/* Create customer form */
Route::get('/customer/create', 'CustomerController@create');

/* Store customers */
Route::post('/customer/save', 'CustomerController@save');

/*Edit form - Customer*/
Route::get('/customer/edit/{id}', 'CustomerController@showEdit')->where(['id' => '[0-9]+']);

/* Update customer */
Route::post('/customer/update/{id}', 'CustomerController@update')->where(['id' => '[0-9]+']);

/*Search on navbar*/
Route::get('/search', 'SearchController@search');

/*Cutomer Details */
Route::get('/customer/details/{id}', 'CustomerController@showDetails')->where(['id' => '[0-9]+']);

/* Create event form */
Route::get('/event/create/{customer}/{car}', 'EventController@create')->where('customer', '[0-9]+')->where('car', '[0-9]+');

/* Store event */
Route::post('/event/save', 'EventController@save');

/* Edit event */
Route::get('/event/edit/{id}', 'EventController@showEdit')->where(['id' => '[0-9]+']);

/* Update event */
Route::post('/event/update/{id}', 'EventController@update')->where(['id' => '[0-9]+']);    
    
/* Store Vehicle */
Route::post('/vehicle/save', 'VehicleController@saveVehicle');

/* Store Vehicle */
Route::get('/vehicle/edit/{id}', 'VehicleController@showEdit')->where(['id' => '[0-9]+']);

/*Update vehicle details*/
Route::post('/vehicle/update/{id}', 'VehicleController@update')->where(['id' => '[0-9]+']);

/*Delete vehicle - set status*/
Route::post('/vehicle/delete', 'VehicleController@delete');

/* Check vehicle already added for customer */
Route::post('/vehicle/check', 'VehicleController@checkVehicle');
    
/*Vehicle documents upload*/    
Route::post('/vehicle/upload', 'VehicleController@uploadDocuments');

/*Document download*/
Route::get('/document/download/{id}/{path}', 'VehicleController@getDocumentDownload')
    ->where('id', '[0-9]+');
    
/*Get Hidden info of an event*/    
Route::get('/event/info', 'EventController@getInfo');

/*Create notice - vehicle histories*/
Route::get('/notice/create/{id}', 'VehicleHistoryController@create')->where('id', '[0-9]+');

/* Save notice*/
Route::post('/notice/save', 'VehicleHistoryController@save');

/*Create notice - customer histories*/
Route::get('/customer/notice/create/{id}', 'CustomerHistoryController@create')->where('id', '[0-9]+');

/* Save notice*/
Route::post('/customer/notice/save', 'CustomerHistoryController@save');


/*Testing*/
Route::get('/temp_reminder','CronjobController@sendEventReminder');
Route::get('/temp_feedback','CronjobController@sendFeedbackLink');
Route::get('/temp_sync','CronjobController@syncCustomer');


/*
 |--------------------------------------------------------------------------
 | Routes for customers
 |--------------------------------------------------------------------------
 |
 | Create form, List, Store
*/

/* List customers page*/
Route::get('/customers', ['as' => 'listCustomersPage', 'uses' => 'CustomerController@index']);

/* List customers*/
Route::get('/list/customers', ['as' => 'listCustomers', 'uses' => 'CustomerController@listCustomers']);

/* Hardware tag Listout */
Route::get('/tag/hardware', ['as' => 'listHardware', 'uses' => 'CustomerController@getHardwareTag']);

/* Search vehicles */
Route::post('/search/vehicle', ['as' => 'searchVehicle', 'uses' => 'CustomerController@searchVehicle']);

/* Search Advertiser*/
Route::post('/search/advertiser', ['as' => 'searchCustomer', 'uses' => 'CustomerController@searchAdvertiser']);    
     

/*
 |--------------------------------------------------------------------------
 | Routes for services
 |--------------------------------------------------------------------------
 |
 | Create form, List, Store
*/
/* List services */
Route::get('/services', ['as' => 'listServices', 'uses' => 'ServicesController@index']);

/* Store services */
Route::post('/services/save', ['as' => 'storeServices', 'uses' => 'ServicesController@store']);

/* Update services */
Route::post('/services/{id}', ['as' => 'updateServices', 'uses' => 'ServicesController@update'])->where(['id' => '[0-9]+']);

/*
 |--------------------------------------------------------------------------
 | Routes for events
 |--------------------------------------------------------------------------
 |
 | Show page, List events
*/
/* Show events page*/
Route::get('/events', ['as' => 'showEventsPage', 'uses' => 'EventController@index']);

/* Show events page*/
Route::get('/events/list', ['as' => 'listEvents', 'uses' => 'EventController@view']);

/* Show events page*/
Route::get('/events/list/{id}', ['as' => 'listEachEvent', 'uses' => 'EventController@show']);
/*
 |--------------------------------------------------------------------------
 | Routes for dashboard
 |--------------------------------------------------------------------------
 |
 | List
*/

/* List customers on dashboard*/
Route::get('/', ['as' => 'listLimitedCustomers', 'uses' => 'DashboardController@index']);

/*Route::auth();

Route::get('/home', 'HomeController@index');*/
