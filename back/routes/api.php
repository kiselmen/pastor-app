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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/peoples', 'App\Http\Controllers\Api\PeopleController@index');
    Route::post('/peoples', 'App\Http\Controllers\Api\PeopleController@store');
    Route::post('/peoples/{id}/update', 'App\Http\Controllers\Api\PeopleController@update');
});


// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     // return $request->user();
//     return $request;
// });
