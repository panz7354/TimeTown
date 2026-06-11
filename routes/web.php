<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\GuideController;

// 登入/註冊（不需要驗證）
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// 需要登入才能進入
Route::middleware('auth')->group(function () {
    Route::get('/',                         [BuildingController::class, 'index']);
    Route::get('/calendar',                 [TaskController::class, 'index']);
    Route::post('/tasks',                   [TaskController::class, 'store']);
    Route::patch('/tasks/{id}/complete',    [TaskController::class, 'complete']);
    Route::patch('/buildings/{id}/place',   [BuildingController::class, 'place']);
    Route::get('/review',  [ReviewController::class, 'index']);
    Route::get('/stories',          [StoryController::class, 'index'])->name('stories.index');
    Route::post('/stories/generate',[StoryController::class, 'generate'])->name('stories.generate');
    Route::get('/guide', [GuideController::class, 'index']);
});
