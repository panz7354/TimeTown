<?php

use App\Models\Task;
use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// 新增任務
function store(Request $request){
    $request->validate([
        'title' => 'required|string|max:100',
        'type'  => 'required|in:學習,工作,運動,社交,休息,興趣創作',
        'date'  => 'required|date',
    ]);

    $date = Carbon::parse($request->date);

    $task = Task::create([
        'user_id' => Auth::id(),
        'title'   => $request->title,
        'type'    => $request->type,
        'status'  => 'pending',
        'date'    => $date->toDateString(),
        'week'    => $date->isoWeek(),
        'month'   => $date->month,
        'year'    => $date->year,
    ]);

    // 建築：確保基礎房子已存在
    BuildingController::ensureBaseBuilding(Auth::id(), $request->type);

    return redirect()->back()->with('success', '任務已新增！');
}

// 完成任務
function complete(int $id)
{
    $task = Task::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

    if ($task->status === 'completed') {
        return redirect()->back()->with('info', '這個任務已經完成了！');
    }

    $task->update([
        'status'       => 'completed',
        'completed_at' => now(),
    ]);

    // 建築升級
    $building = BuildingController::upgradeAfterComplete(Auth::id(), $task->type);

    $msg = $building->wasChanged()
        ? "任務完成！{$task->type} 升級為【{$building->name}】🎉"
        : "任務完成！繼續累積可以讓建築升級喔 🏠";

    return redirect()->back()->with('success', $msg);
}
