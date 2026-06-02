<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [TownController::class, 'index']);        // 城鎮主頁
Route::get('/calendar', [TaskController::class, 'index']); // 行事曆
Route::post('/tasks', [TaskController::class, 'store']);   // 新增任務
Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']); // 完成任務
Route::get('/stories', [StoryController::class, 'index']); // 故事頁
Route::get('/review', [ReviewController::class, 'index']); // 回顧頁
