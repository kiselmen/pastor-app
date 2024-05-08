<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
    Route::get('/user', 'App\Http\Controllers\Api\UserController@index');
    Route::get('/peoples', 'App\Http\Controllers\Api\PeopleController@index');
    Route::post('/peoples', 'App\Http\Controllers\Api\PeopleController@store');
    Route::post('/peoples/{id}/update', 'App\Http\Controllers\Api\PeopleController@update');
    Route::get('/targets', 'App\Http\Controllers\Api\TargetController@index');
    Route::post('/targets', 'App\Http\Controllers\Api\TargetController@store');
    Route::get('/services', 'App\Http\Controllers\Api\ServiceController@index');
    Route::post('/services', 'App\Http\Controllers\Api\ServiceController@store');
    Route::post('/services/{id}/delete', 'App\Http\Controllers\Api\ServiceController@delete');
    Route::post('/services/delete', 'App\Http\Controllers\Api\ServiceController@delete');
    Route::get('/levels', 'App\Http\Controllers\Api\LevelController@index');
    Route::post('/levels', 'App\Http\Controllers\Api\LevelController@store');
});
Route::get('/statuses', 'App\Http\Controllers\Api\StatusController@index');


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     // return $request->user();
//     return $request;
// });
