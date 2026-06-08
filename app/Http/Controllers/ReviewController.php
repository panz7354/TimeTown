<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Building;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now    = Carbon::now();

        // 預設顯示當週
        $week = (int) request('week', $now->isoWeek());
        $year = (int) request('year', $now->year);

        // 計算該週的起訖日
        $weekStart = Carbon::now()->setISODate($year, $week)->startOfDay();
        $weekEnd   = (clone $weekStart)->addDays(6)->endOfDay();

        // 當週任務
        $tasks = Task::where('user_id', $userId)
                     ->where('week', $week)
                     ->where('year', $year)
                     ->get();

        $completed = $tasks->where('status', 'completed');
        $pending   = $tasks->where('status', 'pending');

        // 各類型統計
        $types = ['學習','工作','運動','社交','休息','興趣創作'];
        $stats = [];
        foreach ($types as $type) {
            $stats[$type] = [
                'completed' => $completed->where('type', $type)->count(),
                'pending'   => $pending->where('type',   $type)->count(),
            ];
        }

        // 當週升級紀錄（用 updated_at 判斷）
        $upgradedBuildings = Building::where('user_id', $userId)
                                     ->where('level', '>', 0)
                                     ->whereBetween('updated_at', [$weekStart, $weekEnd])
                                     ->get();

        // 城鎮快照：當週結束時所有建築狀態
        $snapshot = Building::where('user_id', $userId)
                            ->whereNotNull('grid_x')
                            ->get();

        // 可選週清單（有任務的週）
        $availableWeeks = Task::where('user_id', $userId)
                              ->selectRaw('year, week, MIN(date) as week_start')
                              ->groupBy('year', 'week')
                              ->orderByDesc('year')
                              ->orderByDesc('week')
                              ->get();

        return view('review.index', compact(
            'week', 'year', 'weekStart', 'weekEnd',
            'tasks', 'completed', 'pending',
            'stats', 'types', 'upgradedBuildings',
            'snapshot', 'availableWeeks'
        ));
    }
}
