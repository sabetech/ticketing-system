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

// creates Routes in v2
Route::group(['prefix' => 'v2'], function() {
    Route::get('/health', function(Request $req) {
        return 'I\'m Alive and Well!!!';
    });

    Route::post('/login', 'Auth\LoginController@login')->name('login.api');;
    Route::post('/register', 'Auth\RegisterController@register')->name('register.api');;

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/logout', 'Auth\LoginController@logout');
        Route::get('/user', function(Request $req) {
            return $req->user();
        });
    });

});

