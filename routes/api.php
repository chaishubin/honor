<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    //test
//   Route::any('testHospital','DoctorController@testHospital');

Route::group(['middleware' => ['checkUserLogin'],'prefix' => '/doctor'], function () {
   Route::post('signUp','DoctorController@signUp');
   Route::post('signUpInfoEdit','DoctorController@signUpInfoEdit');
   Route::post('signUpInfoDetail','DoctorController@signUpInfoDetail');
   Route::post('signUpList','DoctorController@signUpList');
   Route::post('userLogout','ManagerController@userLogout');

   Route::post('configAward','DoctorController@configAward');
   Route::post('configJobTitle','DoctorController@configJobTitle');
   Route::post('userAwardList','DoctorController@userAwardList');
   Route::post('hospitalList','DoctorController@hospitalList');
});

Route::group(['middleware' => ['session'], 'prefix' => '/doctor'], function () {
    Route::post('showCaptcha','Common@showCaptcha');
    Route::post('userLogin','DoctorController@userLogin');
});

Route::group(['middleware' => ['session'], 'prefix' => '/manager'], function () {
    Route::post('managerLogin','ManagerController@managerLogin');
});

Route::group(['middleware' => ['checkManagerLogin'], 'prefix' => '/manager'], function () {
    Route::post('managerList','ManagerController@managerList')->middleware('checkManagerRoleLogin');
    Route::post('managerAdd','ManagerController@managerAdd')->middleware('checkManagerRoleLogin');
    Route::post('managerDelete','ManagerController@managerDelete')->middleware('checkManagerRoleLogin');

    Route::post('managerLogout','ManagerController@managerLogout');


    Route::post('configAward','DoctorController@configAward');
    Route::post('configJobTitle','DoctorController@configJobTitle');

    Route::post('signUpInfoDetail','DoctorController@signUpInfoDetail');
    Route::post('signUpList','DoctorController@signUpList');
    Route::post('signUpInfoReview','DoctorController@signUpInfoReview');
    Route::post('signUpInfoReviewList','DoctorController@signUpInfoReviewList');
});


