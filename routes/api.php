<?php

use App\Http\Controllers\Api\CatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\LoveController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessagesController;
use App\Http\Controllers\Api\RoomMessageController;
use App\Http\Controllers\Api\VeterinaryController;
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
    Route::get('user',[UserController::class,'all']);
    Route::post('user', [UserController::class,'profile']);
    Route::post('user/email', [UserController::class,'changeEmail']);
    Route::post('user/password', [UserController::class,'changePassword']);
    Route::delete('user/delete', [UserController::class,'destroy']);
    Route::post('change',[UserController::class,'changePhoto']);
    Route::post('logout',[UserController::class,'logout']);

    Route::get('veterinary', [VeterinaryController::class,'all']);

    // cat
    Route::prefix('cat/')->group(function(){
        Route::get('all',[CatController::class,'all']);
        Route::get('search',[CatController::class,'search']);
        Route::post('create',[CatController::class,'store']);
        Route::delete('delete',[CatController::class,'destroy']);
        Route::post('update',[CatController::class,'update']);
    });

    // Follow
    Route::prefix('follow/')->group(function(){
        Route::get('all',[FollowController::class,'all']);
        Route::get('follower',[FollowController::class,'follower']);
        Route::get('following',[FollowController::class,'following']);
        Route::post('create',[FollowController::class,'store']);
        Route::delete('delete',[FollowController::class,'destroy']);
    });

    // Posts
    Route::prefix('post/')->group(function(){
        Route::get('all',[PostController::class,'all']);
        Route::post('create',[PostController::class,'store']);
        Route::delete('delete',[PostController::class,'destroy']);
        Route::post('update',[PostController::class,'update']);
    });

    // Loves
    Route::prefix('love/')->group(function(){
        Route::get('all',[LoveController::class,'all']);
        Route::post('create',[LoveController::class,'store']);
        Route::delete('delete',[LoveController::class,'destroy']);
    });

    //Comment
    Route::prefix('comment/')->group(function () {
        Route::get('all', [CommentController::class, 'all']);
        Route::post('create', [CommentController::class, 'store']);
        Route::post('update', [CommentController::class, 'update']);
        Route::delete('delete', [CommentController::class, 'destroy']);
    });
});

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);
