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

    Route::get('/tag/submit', ['as' => 'tagsubmit', 'uses' => 'CustomerController@tagsubmit']);



});
/*Route::auth();

Route::get('/home', 'HomeController@index');*/
