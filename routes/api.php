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

Route::post('/user/put', 'userController@put');
Route::post('/user/get', 'userController@get');
Route::get('/user/get/{id}', 'userController@getNickname');

Route::get('/interest/get/{uid}', 'InterestController@get');
Route::post('/interest/delete', 'InterestController@delete');
Route::post('/interest/put', 'InterestController@put');


Route::post('/todo/put', 'todo@put');
Route::post('/todo/check', 'todo@check');
Route::post('/todo/delete', 'todo@delete');
Route::get('/todo/get/{uid}', 'todo@get');

Route::get('/bus/1', 'bus@b1');
Route::get('/bus/2', 'bus@b2');
