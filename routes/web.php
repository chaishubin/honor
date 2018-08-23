<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//接口文档路由
Route::get('/webapi', function () {
    return view('webapi');
});

//H5 接口文档路由
Route::get('/h5api', function () {
    return view('h5api');
});

Route::get('/testhospital', function () {
    return view('test-table');
});


Route::any('/sendMessage','SmsController@sendMessage');


Route::group(['middleware' => ['web'], 'prefix' => '/api/doctor'], function () {
//    Route::post('showCaptcha','Common@showCaptcha');
    Route::post('userLogin','DoctorController@userLogin');
});

Route::group(['middleware' => ['web'], 'prefix' => '/api/manager'], function () {
    Route::post('managerLogin','ManagerController@managerLogin');
});