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



Route::group(['prefix' => '/doctor'], function () {
   Route::post('signUp','DoctorController@signUp');
   Route::post('signUpInfoEdit','DoctorController@signUpInfoEdit');
   Route::post('signUpInfoDetail','DoctorController@signUpInfoDetail');
   Route::post('signUpList','DoctorController@signUpList');
   Route::post('signUpInfoReview','DoctorController@signUpInfoReview');
   Route::post('signUpInfoReviewList','DoctorController@signUpInfoReviewList');

   Route::post('configAward','DoctorController@configAward');
   Route::post('configJobTitle','DoctorController@configJobTitle');

//    Route::post('showCaptcha','Common@showCaptcha')->middleware('web');
//    Route::post('userLogin','DoctorController@userLogin');


   Route::post('userAwardList','DoctorController@userAwardList');

   //test
   Route::any('testHospital','DoctorController@testHospital');
});


//->middleware('web')


