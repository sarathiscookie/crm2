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

Route::group(['middleware' => 'web'], function () {

    /*
     |--------------------------------------------------------------------------
     | Routes for customers
     |--------------------------------------------------------------------------
     |
     | Create form, List, Store
    */

    /* List customers */
    Route::get('/', ['as' => 'listCustomersPage', 'uses' => 'CustomerController@index']);


});
/*Route::auth();

Route::get('/home', 'HomeController@index');*/
