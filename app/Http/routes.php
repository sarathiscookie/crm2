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

/*Search on navbar*/
Route::get('/search', 'SearchController@search');

/*Cutomer Details */
Route::get('/customer/details/{id}', 'CustomerController@showDetails')->where(['id' => '[0-9]+']);

/* Create event form */
Route::get('/event/create/{customer}/{car}', 'EventController@create')->where('customer', '[0-9]+')->where('car', '[0-9]+');

/* Store event */
Route::post('/event/save', 'EventController@save'); 
    
/* Store Vehicle */
Route::post('/vehicle/save', 'VehicleController@saveVehicle');

/* Check vehicle already added for customer */
Route::post('/vehicle/check', 'VehicleController@checkVehicle');



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

/*Route::auth();

Route::get('/home', 'HomeController@index');*/
