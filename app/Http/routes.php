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


/*Route::auth();

Route::get('/home', 'HomeController@index');*/
