<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    // ──────────────────────────────────────────
    // 建築升級對照表
    // 結構：類型 => [ level => [建築名稱, svg檔名] ]
    // level 0 = 基礎房子（任何類型設定任務時出現）
    // level 1 = 完成 1 次
    // level 2 = 完成 3 次
    // level 3 = 完成 7 次
    // ──────────────────────────────────────────
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

    // 升級門檻：幾次完成 → 哪個 level
    const LEVEL_THRESHOLDS = [
        3 => 7,   // 7 次以上 → level 3
        2 => 3,   // 3 次以上 → level 2
        1 => 1,   // 1 次以上 → level 1
        0 => 0,   // 0 次     → level 0
    ];

    // 每個類型建築預設放在地圖上的格子位置
    const GRID_POSITIONS = [
        '學習'   => ['x' => 1, 'y' => 1],
        '工作'   => ['x' => 2, 'y' => 1],
        '運動'   => ['x' => 3, 'y' => 1],
        '社交'   => ['x' => 1, 'y' => 2],
        '休息'   => ['x' => 2, 'y' => 2],
        '興趣創作' => ['x' => 3, 'y' => 2],
    ];

    // ──────────────────────────────────────────
    // 核心方法：任務完成後呼叫此方法更新建築
    // 在 TaskController@complete 裡呼叫
    // ──────────────────────────────────────────
    public static function upgradeAfterComplete(int $userId, string $taskType): Building
    {
        // 找到或建立該類型的建築記錄
        $building = Building::firstOrCreate(
            ['user_id' => $userId, 'type' => $taskType],
            [
                'level'           => 0,
                'name'            => '基礎房子',
                'svg_file'        => '01_基礎房子.svg',
                'completed_count' => 0,
                'grid_x'          => self::GRID_POSITIONS[$taskType]['x'],
                'grid_y'          => self::GRID_POSITIONS[$taskType]['y'],
            ]
        );

        // 完成次數 +1
        $building->completed_count += 1;

        // 計算新 level
        $newLevel = self::calculateLevel($building->completed_count);

        // 有升級才更新名稱與圖示
        if ($newLevel !== $building->level) {
            $building->level    = $newLevel;
            $building->name     = self::UPGRADE_MAP[$taskType][$newLevel][0];
            $building->svg_file = self::UPGRADE_MAP[$taskType][$newLevel][1];
        }

        $building->save();

        return $building;
    }

    // ──────────────────────────────────────────
    // 新增任務時呼叫：確保基礎房子已存在
    // 在 TaskController@store 裡呼叫
    // ──────────────────────────────────────────
    public static function ensureBaseBuilding(int $userId, string $taskType): void
    {
        Building::firstOrCreate(
            ['user_id' => $userId, 'type' => $taskType],
            [
                'level'           => 0,
                'name'            => '基礎房子',
                'svg_file'        => '01_基礎房子.svg',
                'completed_count' => 0,
                'grid_x'          => self::GRID_POSITIONS[$taskType]['x'],
                'grid_y'          => self::GRID_POSITIONS[$taskType]['y'],
            ]
        );
    }

    // ──────────────────────────────────────────
    // 計算 level（私有輔助方法）
    // ──────────────────────────────────────────
    private static function calculateLevel(int $count): int
    {
        return match(true) {
            $count >= 7 => 3,
            $count >= 3 => 2,
            $count >= 1 => 1,
            default     => 0,
        };
    }

    // ──────────────────────────────────────────
    // 取得目前登入者所有建築（給城鎮地圖頁用）
    // ──────────────────────────────────────────
    public function index()
    {
        $buildings = Building::where('user_id', Auth::id())
                             ->orderBy('grid_y')
                             ->orderBy('grid_x')
                             ->get();

        return view('town.index', compact('buildings'));
    }
}
