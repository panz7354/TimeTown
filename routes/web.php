<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TownController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BuildingController;

// ── 訪客路由（未登入才能進）──────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
});

// ── 登入後路由 ───────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/',  [TownController::class, 'index']);
    Route::get('/calendar', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::patch('/buildings/{id}/place', [BuildingController::class, 'placeBuilding']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});