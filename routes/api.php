<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Route not found'], 404);
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:delete users');

    // Route::prefix('v1')->group(function () {
        Route::apiResource('articles', ArticleController::class);

        Route::post('/messages/send', [MessageController::class, 'send']);
        Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation']);
        Route::get('/messages/inbox', [MessageController::class, 'inbox']);
        Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    // });
});
