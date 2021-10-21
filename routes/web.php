<?php

use Illuminate\Support\Facades\Route;

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

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

Route::get('/websocket', function () {
    Artisan::call('websockets:serve');
});

Route::get('/clear-cache-all', function() {
    Artisan::call('cache:clear');  
    dd("Cache Clear All");
});

Route::get('/make-model', function() {
    Artisan::call('make:model photo -m -r');  
    dd("model create All");
});




Route::group(['middleware' => ['auth']], function () {
    Route::get('/getcurl', 'App\Http\Controllers\AgoraVideoController@getcurl');

    Route::get('/socketio', 'App\Http\Controllers\AgoraVideoController@socketio');
    Route::get('/read-socketio', 'App\Http\Controllers\AgoraVideoController@ReadSocketio');
    Route::post('/save-socketid', 'App\Http\Controllers\AgoraVideoController@SaveSocketId');
    Route::post('/private-message', 'App\Http\Controllers\AgoraVideoController@PrivateMessage');
    

    Route::get('/profile/{user}', 'App\Http\Controllers\AgoraVideoController@userprofile');
    Route::get('/alluser', 'App\Http\Controllers\AgoraVideoController@alluser');

    Route::get('/getcurl', 'App\Http\Controllers\AgoraVideoController@getcurl');
    Route::get('/refreshuserlist', 'App\Http\Controllers\AgoraVideoController@refreshUserlist');
    
    Route::get('/agora-chat', 'App\Http\Controllers\AgoraVideoController@index');
    Route::get('/chat', 'App\Http\Controllers\AgoraVideoController@chat')->name('chat');
    Route::get('/chat/{id}', 'App\Http\Controllers\AgoraVideoController@chat')->name('chat');
    Route::post('/agora/token', 'App\Http\Controllers\AgoraVideoController@token');
    Route::post('/agora/call-user', 'App\Http\Controllers\AgoraVideoController@callUser');

    Route::post('/save-chat', 'App\Http\Controllers\AgoraVideoController@SaveChat');
    Route::get('/getChat/{from_id}/{to_id}', 'App\Http\Controllers\AgoraVideoController@GetChat');
    
    Route::post('/getChathistory', 'App\Http\Controllers\AgoraVideoController@getChathistory');
    Route::post('/getContactlist', 'App\Http\Controllers\AgoraVideoController@getContactlist');

    Route::post('/save-chat-file','App\Http\Controllers\AgoraVideoController@SaveChatFile');
    Route::post('/pauth','App\Http\Controllers\AgoraVideoController@auth');
    //Route::get('/agora-chat', 'App\Http\Controllers\AgoraVideoController@index');

    /**
     * Make messages as seen
     */
    Route::post('/makeseen', 'App\Http\Controllers\AgoraVideoController@makeSeen')->name('messages.seen');

    Route::post('/delete-chats', 'App\Http\Controllers\AgoraVideoController@deleteChats')->name('delete-chats');

    Route::post('/expert-rating', 'App\Http\Controllers\AgoraVideoController@rating')->name('expert-rating');


});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// admin protected routes
Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin','as' => 'admin.'], function () {
    
    Route::get('/', 'App\Http\Controllers\Admin\HomeadminController@index')->name('admin_dashboard');
    
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class);
});

