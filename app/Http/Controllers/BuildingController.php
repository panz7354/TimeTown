<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Task;
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

    const LEVEL_THRESHOLDS = [3 => 7, 2 => 3, 1 => 1, 0 => 0];

    const GRID_POSITIONS = [
        '學習'     => ['x' => 1, 'y' => 1],
        '工作'     => ['x' => 2, 'y' => 1],
        '運動'     => ['x' => 3, 'y' => 1],
        '社交'     => ['x' => 1, 'y' => 2],
        '休息'     => ['x' => 2, 'y' => 2],
        '興趣創作' => ['x' => 3, 'y' => 2],
    ];

    public static function upgradeAfterComplete(int $userId, string $taskType): Building
    {
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

    private static function calculateLevel(int $count): int
    {
        return match(true) {
            $count >= 7 => 3,
            $count >= 3 => 2,
            $count >= 1 => 1,
            default     => 0,
        };
    }

    public function index()
    {
        $buildings = Building::where('user_id', Auth::id())
                             ->orderBy('grid_y')
                             ->orderBy('grid_x')
                             ->get();

        $unplaced = $buildings->filter(fn($b) => is_null($b->grid_x));

        return view('town.index', compact('buildings', 'unplaced'));
    }

    public function place(Request $request, int $id)
    {
        $building = Building::where('id', $id)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

        $building->update([
            'grid_x' => $request->grid_x,
            'grid_y' => $request->grid_y,
        ]);

        return response()->json(['success' => true]);
    }
}
