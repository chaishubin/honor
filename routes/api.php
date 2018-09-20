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
//   Route::any('test-table',function(){
//       return view('test-table');
//   });
//   Route::any('testHospital','DoctorController@testHospital');

Route::group(['middleware' => ['checkUserLogin'],'prefix' => '/doctor'], function () {
   Route::post('signUp','DoctorController@signUp');
   Route::post('teamSignUp','DoctorController@teamSignUp');
   Route::post('signUpInfoEdit','DoctorController@signUpInfoEdit');
   Route::post('signUpInfoDetail','DoctorController@signUpInfoDetail');
   Route::post('userSignUpInfoDetail','DoctorController@userSignUpInfoDetail');
   Route::post('signUpList','DoctorController@signUpList');
   Route::post('userLogout','DoctorController@userLogout');

   Route::post('configJobTitle','DoctorController@configJobTitle');
   Route::post('userAwardList','DoctorController@userAwardList');
   Route::post('hospitalList','DoctorController@hospitalList');
});

Route::group(['middleware' => ['session'], 'prefix' => '/doctor'], function () {
    Route::post('showCaptcha','Common@showCaptcha');
    Route::post('userLogin','DoctorController@userLogin');
    Route::post('testSetCookie','DoctorController@testSetCookie');
});

Route::group(['middleware' => ['session'], 'prefix' => '/manager'], function () {
    Route::post('managerLogin','ManagerController@managerLogin');
    Route::post('testSetCookie','ManagerController@testSetCookie');
});

Route::group(['middleware' => ['checkManagerLogin'], 'prefix' => '/manager'], function () {
    Route::post('managerList','ManagerController@managerList')->middleware('checkManagerRoleLogin');
    Route::post('managerAdd','ManagerController@managerAdd')->middleware('checkManagerRoleLogin');
    Route::post('managerDelete','ManagerController@managerDelete')->middleware('checkManagerRoleLogin');
    Route::post('managerEdit','ManagerController@managerEdit');
    Route::post('managerDetail','ManagerController@managerDetail');

    Route::post('managerLogout','ManagerController@managerLogout');


    Route::post('configAward','DoctorController@configAward');
    Route::post('configJobTitle','DoctorController@configJobTitle');

    Route::post('signUpInfoDetail','DoctorController@signUpInfoDetail');
    Route::post('signUpList','DoctorController@signUpList');
    Route::post('signUpInfoReview','DoctorController@signUpInfoReview');
    Route::post('signUpInfoReviewList','DoctorController@signUpInfoReviewList');

    Route::post('timeSetting','ManagerController@timeSetting');
});

Route::post('/doctor/configAward','DoctorController@configAward');
Route::post('timeSettingList','ManagerController@timeSettingList');

Route::any('getWechatConfig','Common@getWechatConfig');

Route::group(['prefix' => '/vote'], function () {
   Route::post('userVote', 'VoteController@userVote');
});

Route::post('/manager/expertList','ManagerController@expertList');
Route::post('/manager/expertAdd','ManagerController@expertAdd');
Route::post('/manager/exportEdit','ManagerController@exportEdit');
Route::post('/manager/expertDelete','ManagerController@expertDelete');
Route::post('/manager/expertDetail','ManagerController@expertDetail');


