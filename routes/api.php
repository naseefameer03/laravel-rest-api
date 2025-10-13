<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ArticleVersionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Route not found'], 404);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user', [AuthController::class, 'user']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::prefix('v1')->group(function () {
        Route::apiResource('articles', ArticleController::class);
        Route::post('articles/{article}/publish', [ArticleController::class, 'publish']);
        Route::post('articles/{article}/images', [ArticleController::class, 'uploadImages']);
        Route::post('articles/{article}/cover', [ArticleController::class, 'replaceCover']);

        Route::get('articles/{article}/versions', [ArticleVersionController::class, 'index']);
        Route::get('articles/{article}/versions/{version}', [ArticleVersionController::class, 'show']);
        Route::post('articles/{article}/versions/{version}/revert', [ArticleVersionController::class, 'revert']);

        Route::get('articles/{article}/comments', [CommentController::class, 'index']);
        Route::post('articles/{article}/comments', [CommentController::class, 'store']);
        Route::put('comments/{comment}', [CommentController::class, 'update']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

        Route::post('/messages/send', [MessageController::class, 'send']);
        Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation']);
        Route::get('/messages/inbox', [MessageController::class, 'inbox']);
        Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    });
});
