<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    const UPGRADE_MAP = [
        '學習' => [
            0 => ['基礎房子',   '01_基礎房子.svg'],
            1 => ['書店',       '02_書店.svg'],
            2 => ['學校',       '03_學校.svg'],
            3 => ['圖書館',     '04_圖書館.svg'],
        ],
        '工作' => [
            0 => ['基礎房子',   '01_基礎房子.svg'],
            1 => ['便利商店',   '05_便利商店.svg'],
            2 => ['工坊',       '06_工坊.svg'],
            3 => ['辦公大樓',   '07_辦公大樓.svg'],
        ],
        '運動' => [
            0 => ['基礎房子',   '01_基礎房子.svg'],
            1 => ['公園',       '08_公園.svg'],
            2 => ['操場',       '09_操場.svg'],
            3 => ['健身房',     '10_健身房.svg'],
        ],
        '社交' => [
            0 => ['基礎房子',   '01_基礎房子.svg'],
            1 => ['咖啡廳',     '11_咖啡廳.svg'],
            2 => ['餐廳',       '12_餐廳.svg'],
            3 => ['廣場',       '13_廣場.svg'],
        ],
        '休息' => [
            0 => ['基礎房子',   '01_基礎房子.svg'],
            1 => ['花園',       '14_花園.svg'],
            2 => ['旅館',       '15_旅館.svg'],
            3 => ['溫泉',       '16_溫泉.svg'],
        ],
        '興趣創作' => [
            0 => ['基礎房子',   '01_基礎房子.svg'],
            1 => ['畫室',       '17_畫室.svg'],
            2 => ['音樂教室',   '18_音樂教室.svg'],
            3 => ['數位工作室', '19_數位工作室.svg'],
        ],
    ];

    // ── 任務完成後升級建築 ──────────────────────
    public static function upgradeAfterComplete(int $userId, string $taskType): Building
    {
        $building = Building::where('user_id', $userId)
                            ->where('type', $taskType)
                            ->firstOrFail();

        $building->completed_count += 1;

        $newLevel = self::calculateLevel($building->completed_count);

        if ($newLevel !== $building->level) {
            $building->level    = $newLevel;
            $building->name     = self::UPGRADE_MAP[$taskType][$newLevel][0];
            $building->svg_file = self::UPGRADE_MAP[$taskType][$newLevel][1];
        }

        $building->save();

        return $building;
    }

    // ── 新增任務時建立建築（grid 為 null，等待使用者選位置）──
    public static function ensureBaseBuilding(int $userId, string $taskType): Building
    {
        return Building::firstOrCreate(
            ['user_id' => $userId, 'type' => $taskType],
            [
                'level'           => 0,
                'name'            => '基礎房子',
                'svg_file'        => '01_基礎房子.svg',
                'completed_count' => 0,
                'grid_x'          => null,  // 等使用者選位置
                'grid_y'          => null,
            ]
        );
    }

    // ── 使用者選好格子後存入位置 ────────────────
    public function placeBuilding(Request $request, int $buildingId): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'grid_x' => 'required|integer|min:0|max:7',
            'grid_y' => 'required|integer|min:0|max:7',
        ]);

        $building = Building::where('id', $buildingId)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

        // 位置一旦設定就不能再改
        if ($building->grid_x !== null) {
            return response()->json(['error' => '此建築已有位置，無法再移動。'], 422);
        }

        // 檢查同一個 user 的同一格是否已被佔用
        $occupied = Building::where('user_id', Auth::id())
                            ->where('grid_x', $request->grid_x)
                            ->where('grid_y', $request->grid_y)
                            ->exists();

        if ($occupied) {
            return response()->json(['error' => '這個格子已有建築了！'], 422);
        }

        $building->update([
            'grid_x' => $request->grid_x,
            'grid_y' => $request->grid_y,
        ]);

        return response()->json(['success' => true, 'building' => $building]);
    }

    // ── 城鎮地圖頁 ───────────────────────────────
    public function index()
    {
        $buildings = Building::where('user_id', Auth::id())
                             ->orderBy('grid_y')
                             ->orderBy('grid_x')
                             ->get();

        // 尚未選位置的建築（需要提示使用者去選）
        $unplaced = $buildings->whereNull('grid_x');

        return view('town.index', compact('buildings', 'unplaced'));
    }

    private static function calculateLevel(int $count): int
    {
        return match(true) {
            $count >= 7 => 3,
            $count >= 3 => 2,
            $count >= 1 => 1,
            default     => 0,
        };
    }
}