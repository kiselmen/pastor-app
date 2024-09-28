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
    Route::post('/user', 'App\Http\Controllers\Api\UserController@store'); // с правами доступа
    Route::post('/user/change', 'App\Http\Controllers\Api\UserController@change'); // с правами доступа
    Route::get('/user/{id}/permitions', 'App\Http\Controllers\Api\UserController@permitions'); // с правами доступа
        
    Route::post('/permition', 'App\Http\Controllers\Api\PermitionController@store'); // с правами доступа
    Route::post('/permition/delete', 'App\Http\Controllers\Api\PermitionController@delete'); // с правами доступа

    Route::get('/peoples', 'App\Http\Controllers\Api\PeopleController@index'); // с правами доступа
    Route::get('/peoples/{id}', 'App\Http\Controllers\Api\PeopleController@show'); // с правами доступа
    Route::post('/peoples', 'App\Http\Controllers\Api\PeopleController@store'); // с правами доступа
    Route::post('/peoples/{id}/update', 'App\Http\Controllers\Api\PeopleController@update'); // с правами доступа
    Route::get('/birthday', 'App\Http\Controllers\Api\PeopleController@birthday'); // с правами доступа
    Route::post('/born_peoples', 'App\Http\Controllers\Api\PeopleController@born_report'); // с правами доступа
    
    Route::get('/targets', 'App\Http\Controllers\Api\TargetController@index'); // с правами доступа
    Route::post('/targets', 'App\Http\Controllers\Api\TargetController@store'); // с правами доступа

    Route::get('/services', 'App\Http\Controllers\Api\ServiceController@index'); // с правами доступа
    Route::post('/services', 'App\Http\Controllers\Api\ServiceController@store'); // с правами доступа
    Route::post('/services/{id}/update', 'App\Http\Controllers\Api\ServiceController@update'); // с правами доступа
    Route::post('/services/delete', 'App\Http\Controllers\Api\ServiceController@delete'); // с правами доступа

    Route::post('/pservices', 'App\Http\Controllers\Api\PserviceController@store');  // с правами доступа
    Route::post('/pservices/delete', 'App\Http\Controllers\Api\PserviceController@delete'); // с правами доступа

    Route::post('/ptargets', 'App\Http\Controllers\Api\PtargetController@store');  // с правами доступа
    Route::post('/ptargets/delete', 'App\Http\Controllers\Api\PtargetController@delete'); // с правами доступа

    Route::get('/levels', 'App\Http\Controllers\Api\LevelController@index'); // с правами доступа
    Route::post('/levels', 'App\Http\Controllers\Api\LevelController@store'); // с правами доступа
    Route::post('/levels/{id}/update', 'App\Http\Controllers\Api\LevelController@update'); // с правами доступа

    Route::post('/plevels', 'App\Http\Controllers\Api\PlevelController@store'); // с правами доступа
    Route::post('/plevels/delete', 'App\Http\Controllers\Api\PlevelController@delete'); // с правами доступа
    Route::post('/plevels/{id}/update', 'App\Http\Controllers\Api\PlevelController@update'); // с правами доступа

    Route::get('/prihods', 'App\Http\Controllers\Api\PrihodController@index'); // с правами доступа
    Route::post('/prihods', 'App\Http\Controllers\Api\PrihodController@store'); // с правами доступа
    Route::post('/prihods/{id}/update', 'App\Http\Controllers\Api\PrihodController@update'); // с правами доступа

    Route::get('/families', 'App\Http\Controllers\Api\FamilyController@index'); // с правами доступа
    Route::get('/families/{id}', 'App\Http\Controllers\Api\FamilyController@show'); // с правами доступа
    Route::post('/families', 'App\Http\Controllers\Api\FamilyController@store'); // с правами доступа
    Route::post('/families/{id}/update', 'App\Http\Controllers\Api\FamilyController@update'); // с правами доступа
});
Route::get('/statuses', 'App\Http\Controllers\Api\StatusController@index');
Route::get('/sexes', 'App\Http\Controllers\Api\SexController@index'); // с правами доступа

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     // return $request->user();
//     return $request;
// });
