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

Route::group(['middleware' => 'web'], function () {

    /*
     |--------------------------------------------------------------------------
     | Routes for customers
     |--------------------------------------------------------------------------
     |
     | Create form, List, Store
    */

    /* Create customers */
    Route::get('/customer/create', 'CustomerController@create');

    /* List customer page */
    Route::get('/', ['as' => 'listCustomersPage', 'uses' => 'CustomerController@index']);

    /* List customers */
    Route::get('/customer', ['as' => 'listCustomers', 'uses' => 'CustomerController@show']);

    /* Store customers */
    Route::post('/customer/save', 'CustomerController@save');
   
});
/*Route::auth();

Route::get('/home', 'HomeController@index');*/
