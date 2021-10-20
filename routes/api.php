<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;


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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::group([
    'namespace'=>'App\Http\Controllers\Api',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        //Route::get('logout', 'AuthController@logout');
        //Route::get('user', 'AuthController@user');
    });

    Route::group([
      'middleware' => 'auth:api',
      'prefix' => 'post'
    ], function() {
        Route::get('/', 'PostController@index');
        Route::get('/{id}', 'PostController@show');
        Route::post('/create', 'PostController@store');
        Route::post('/update/{id}', 'PostController@update');
    });

});

