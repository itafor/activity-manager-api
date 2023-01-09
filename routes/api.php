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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('v1')->group(
    function () {
        
        Route::prefix('user')->group(
            function () {
                Route::prefix('auth')->group(
                    function () {

                        Route::post('signup', [App\Http\Controllers\UserAuthController::class, 'userSignup']);
                        
                        Route::post('login', [App\Http\Controllers\UserAuthController::class, 'userlogin']);


                    });

                Route::middleware(['auth:user'])->group(
                    function () {

                        Route::get('logout', [App\Http\Controllers\UserAuthController::class, 'userLogout']);
                       
                        Route::get('profile', [App\Http\Controllers\UserAuthController::class, 'userProfile']);
                        
                        Route::get('my-activities', [App\Http\Controllers\ActivityController::class, 'myActivities']);

                                
                            });

                    });


           Route::prefix('admin')->group(
            function () {
                Route::prefix('auth')->group(
                    function () {
                        Route::post('signup', [App\Http\Controllers\AdminAuthController::class, 'adminSignup']);
                        Route::post('login', [App\Http\Controllers\AdminAuthController::class, 'adminLogin']);
                    });

                Route::middleware(['auth:admin'])->group(
                    function () {

                        Route::get('profile', [App\Http\Controllers\AdminAuthController::class, 'adminProfile']);
                        Route::post('update-profile', [App\Http\Controllers\AdminAuthController::class, 'updateAdminProfile']);
                        Route::post('change/password', [App\Http\Controllers\AdminAuthController::class, 'changeAdminPassword']);

                        Route::get('logout', [App\Http\Controllers\AdminAuthController::class, 'adminLogout']);

                         Route::prefix('user')->group(function () {
                            Route::get('/list', [App\Http\Controllers\UserController::class, 'getUsers']);
                            Route::get('/show/{userId}', [App\Http\Controllers\UserController::class, 'getOneUser']);
                        });

                      
                    });

                 });
            }); 

