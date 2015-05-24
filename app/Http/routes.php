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

Route::get('/', 'WelcomeController@index');
Route::get('/ride/search', 'RideController@search');
Route::resource('passenger', 'PassengerController', array('only' => array('destroy', 'store')));
Route::resource('ride', 'RideController', array('only' => array('destroy', 'index', 'show', 'store')));