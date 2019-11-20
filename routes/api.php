<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('/search/get', 'SearchController@show');
// Route::get('/search/history/get/{id}', 'SearchController@showHistory');
Route::get('/search/history/get/{id}', 'SearchController@showHistory2');
Route::post('/search/history/put', 'SearchController@putHistory');
Route::post('/bookmark/put', 'SearchController@putFavorite');
Route::get('/bookmark/get/{id}', 'SearchController@getFavorite');

Route::post('/route/walk/get', 'RouteController@show');
Route::post('/route/bus/get', 'RouteController@showBus');
Route::post('/route/direction', 'RouteController@direction');
