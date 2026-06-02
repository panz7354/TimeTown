<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TownController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BuildingController;

Route::get('/', [TownController::class, 'index']);                          // 城鎮主頁
Route::get('/calendar', [TaskController::class, 'index']);                  // 行事曆
Route::post('/tasks', [TaskController::class, 'store']);                    // 新增任務
Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']);  // 完成任務

// 以下等對應 Controller 建好後再開啟
// Route::get('/stories', [StoryController::class, 'index']);
// Route::get('/review', [ReviewController::class, 'index']);