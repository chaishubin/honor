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
    $host = \App\Http\Controllers\Common::environmentUrl();
    if ($host != 'production'){
        return view('webapi');
    }else{
        return view('welcome');
    }
});

//H5 接口文档路由
Route::get('/h5api', function () {
    $host = \App\Http\Controllers\Common::environmentUrl();
    if ($host != 'production'){
        return view('h5api');
    }else{
        return view('welcome');
    }
});

Route::get('/testhospital', function () {
    $host = \App\Http\Controllers\Common::environmentUrl();
    if ($host != 'production'){
        return view('test-table');
    }else{
        return view('welcome');
    }
});

