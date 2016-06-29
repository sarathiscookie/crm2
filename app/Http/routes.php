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

/* Create customers */
Route::get('/customer/create', 'CustomerController@create');

/* Store customers */
Route::post('/customer/save', 'CustomerController@save');

/*Search */
Route::get('/search', 'SearchController@search');

/*Cutomer Details */
Route::get('/customer/details/{id}', 'CustomerController@showDetails')->where(['id' => '[0-9]+']);

/* Create event */
Route::get('/event/create/{customer}/{car}', 'EventController@create')->where('customer', '[0-9]+')->where('car', '[0-9]+');

/* Store event */
Route::post('/event/save', 'EventController@save'); 
    
/* Store Vehicle */
Route::post('/vehicle/save', 'VehicleController@saveVehicle');

/* Check vehicle already added */
Route::get('/vehicle/check/{customer}/{exe_id}', 'VehicleController@getStatus')->where('customer', '[0-9]+')->where('exe_id', '[0-9]+');



/*
 |--------------------------------------------------------------------------
 | Routes for customers
 |--------------------------------------------------------------------------
 |
 | Create form, List, Store
*/

/* List customers */
Route::get('/', ['as' => 'listCustomersPage', 'uses' => 'CustomerController@index']);

/* Hardware tag Listout */
Route::get('/tag/hardware', ['as' => 'listHardware', 'uses' => 'CustomerController@getHardwareTag']);

/* Search vehicles */
Route::post('/search/vehicle', ['as' => 'searchVehicle', 'uses' => 'CustomerController@searchVehicle']);


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






/*Route::auth();

Route::get('/home', 'HomeController@index');*/
