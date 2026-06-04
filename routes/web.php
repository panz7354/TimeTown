<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TownController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BuildingController;

Route::get('/', [TownController::class, 'index']);
Route::get('/calendar', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']);

// 使用者選擇建築格子位置（只能設定一次）
Route::patch('/buildings/{id}/place', [BuildingController::class, 'placeBuilding']);