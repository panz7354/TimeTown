<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    const UPGRADE_MAP = [
        '學習'     => [0=>'基礎房子', 1=>'書店', 2=>'學校',    3=>'圖書館'],
        '工作'     => [0=>'基礎房子', 1=>'便利商店', 2=>'工坊', 3=>'辦公大樓'],
        '運動'     => [0=>'基礎房子', 1=>'公園', 2=>'操場',    3=>'健身房'],
        '社交'     => [0=>'基礎房子', 1=>'咖啡廳', 2=>'餐廳',  3=>'廣場'],
        '休息'     => [0=>'基礎房子', 1=>'花園', 2=>'旅館',    3=>'溫泉'],
        '興趣創作' => [0=>'基礎房子', 1=>'畫室', 2=>'音樂教室', 3=>'數位工作室'],
    ];

    const SVG_MAP = [
        '學習'     => [0=>'01_基礎房子.svg', 1=>'02_書店.svg', 2=>'03_學校.svg',    3=>'04_圖書館.svg'],
        '工作'     => [0=>'01_基礎房子.svg', 1=>'05_便利商店.svg', 2=>'06_工坊.svg', 3=>'07_辦公大樓.svg'],
        '運動'     => [0=>'01_基礎房子.svg', 1=>'08_公園.svg', 2=>'09_操場.svg',    3=>'10_健身房.svg'],
        '社交'     => [0=>'01_基礎房子.svg', 1=>'11_咖啡廳.svg', 2=>'12_餐廳.svg',  3=>'13_廣場.svg'],
        '休息'     => [0=>'01_基礎房子.svg', 1=>'14_花園.svg', 2=>'15_旅館.svg',    3=>'16_溫泉.svg'],
        '興趣創作' => [0=>'01_基礎房子.svg', 1=>'17_畫室.svg', 2=>'18_音樂教室.svg', 3=>'19_數位工作室.svg'],
    ];

    // 升級門檻：每棟各自的 completed_count 要達到幾次
    const LEVEL_THRESHOLDS = [0=>0, 1=>1, 2=>2, 3=>4];

    // 新增任務時確保有基礎房子，若最新一棟已達 MAX 且不足 3 棟，新增下一棟
    public static function ensureBaseBuilding(int $userId, string $taskType): void
    {
        // 取得目前這個類型的所有棟，按 slot 排序
        $buildings = Building::where('user_id', $userId)
                             ->where('type', $taskType)
                             ->orderBy('slot')
                             ->get();

        // 完全沒有 → 建第 0 棟（slot=0）
        if ($buildings->isEmpty()) {
            Building::create([
                'user_id'         => $userId,
                'type'            => $taskType,
                'slot'            => 0,
                'level'           => 0,
                'name'            => self::UPGRADE_MAP[$taskType][0],
                'svg_file'        => self::SVG_MAP[$taskType][0],
                'completed_count' => 0,
                'grid_x'          => null,
                'grid_y'          => null,
            ]);
            return;
        }

        // 最新一棟（slot 最大的）
        $last = $buildings->last();

        // 最新一棟已到 MAX（level 3）且目前棟數 < 3 → 新增下一棟
        if ($last->level >= 3 && $buildings->count() < 3) {
            $nextSlot = $buildings->count();
            Building::create([
                'user_id'         => $userId,
                'type'            => $taskType,
                'slot'            => $nextSlot,
                'level'           => 0,
                'name'            => self::UPGRADE_MAP[$taskType][0],
                'svg_file'        => self::SVG_MAP[$taskType][0],
                'completed_count' => 0,
                'grid_x'          => null,
                'grid_y'          => null,
            ]);
        }
        // 其他情況（最新棟還沒 MAX，或已有 3 棟）→ 不動
    }

    // 完成任務時，升級「最新一棟尚未 MAX 的建築」
    public static function upgradeAfterComplete(int $userId, string $taskType): Building
    {
        // 找最新一棟還沒達到 MAX 的
        $building = Building::where('user_id', $userId)
                            ->where('type', $taskType)
                            ->where('level', '<', 3)
                            ->orderBy('slot')
                            ->first();

        // 全部都 MAX 了，回傳最後一棟（不變）
        if (!$building) {
            return Building::where('user_id', $userId)
                           ->where('type', $taskType)
                           ->orderByDesc('slot')
                           ->first();
        }

        $building->completed_count += 1;
        $newLevel = self::calculateLevel($building->completed_count);

        if ($newLevel !== $building->level) {
            $building->level    = $newLevel;
            $building->name     = self::UPGRADE_MAP[$taskType][$newLevel];
            $building->svg_file = self::SVG_MAP[$taskType][$newLevel];
        }

        $building->save();
        return $building;
    }

    private static function calculateLevel(int $count): int
    {
        return match(true) {
            $count >= 4 => 3,
            $count >= 2 => 2,
            $count >= 1 => 1,
            default     => 0,
        };
    }

    public function index()
    {
        $buildings = Building::where('user_id', Auth::id())
                             ->whereNotNull('grid_x')
                             ->orderBy('grid_y')
                             ->orderBy('grid_x')
                             ->get();

        $unplaced = Building::where('user_id', Auth::id())
                            ->whereNull('grid_x')
                            ->orderBy('type')
                            ->orderBy('slot')
                            ->get();

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
