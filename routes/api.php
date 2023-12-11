<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\LawyerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout',          [ UserController::class, 'logout']);
    Route::delete('/lawyers',       [ LawyerController::class,      'destroy']);
    Route::delete('/posts',         [ PostsController::class,       'destroy']);
    Route::delete('/categories',    [ CategoryController::class,    'destroy']);
    Route::delete('/photos',        [ PhotoController::class,       'destroy']);
    Route::post('/info',            [ UserController::class, 'info']);
    
});


Route::apiResources([
    'users'         => UserController::class,
    'photos'        => PhotoController::class,
    'categories'    => CategoryController::class,
    'posts'         => PostsController::class,
    'messages'      => MessageController::class,
    'lawyers'       => LawyerController::class,
    'photos'        => PhotoController::class,
]);

Route::post('/login', [ UserController::class, 'login']);


