<?php

use App\Http\Controllers\Api\CatController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user',[UserController::class,'show']);
    Route::post('user', [UserController::class,'profile']);
    Route::post('logout',[UserController::class,'logout']);

    // cat
    Route::prefix('cat/')->group(function(){
        Route::get('all',[CatController::class,'all']);
        Route::post('create',[CatController::class,'store']);
        Route::delete('delete',[CatController::class,'destroy']);
        Route::post('update',[CatController::class,'update']);
    });
});

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
