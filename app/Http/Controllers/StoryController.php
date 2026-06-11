<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\WeeklyStory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class StoryController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $user   = Auth::user();
        $now    = Carbon::now();

        $week = (int) request('week', $now->isoWeek());
        $year = (int) request('year', $now->year);

        // 所有有故事或有任務的週清單
        $availableWeeks = WeeklyStory::where('user_id', $userId)
                                     ->orderByDesc('year')
                                     ->orderByDesc('week')
                                     ->get();

        // 找這週的故事
        $story = WeeklyStory::where('user_id', $userId)
                            ->where('week', $week)
                            ->where('year', $year)
                            ->first();

        // 這週有任務但還沒生成故事 → 可以手動觸發
        $weekTasks = Task::where('user_id', $userId)
                         ->where('week', $week)
                         ->where('year', $year)
                         ->get();

        $canGenerate = $weekTasks->isNotEmpty() && (!$story || !$story->story_text);

        return view('stories.index', compact(
            'week', 'year', 'story', 'availableWeeks',
            'weekTasks', 'canGenerate', 'user'
        ));
    }

    public function generate()
    {
        $userId = Auth::id();
        $user   = Auth::user();

        $week = (int) request('week');
        $year = (int) request('year');

        // 取得這週任務
        $tasks = Task::where('user_id', $userId)
                     ->where('week', $week)
                     ->where('year', $year)
                     ->get();

        if ($tasks->isEmpty()) {
            return redirect()->back()->with('error', '這週沒有任務，無法生成故事');
        }

        // 已有故事就不重新生成
        // $existing = WeeklyStory::where('user_id', $userId)
        //                        ->where('week', $week)
        //                        ->where('year', $year)
        //                        ->whereNotNull('story_text')
        //                        ->first();

        // if ($existing) {
        //     return redirect()->route('stories.index', ['week' => $week, 'year' => $year])
        //                      ->with('info', '這週的故事已經生成過了');
        // }

        // force=1 時強制重新生成，否則已有故事就跳過
        if (!request('force')) {
            $existing = WeeklyStory::where('user_id', $userId)
                                ->where('week', $week)
                                ->where('year', $year)
                                ->whereNotNull('story_text')
                                ->first();
            if ($existing) {
                return redirect()->route('stories.index', ['week' => $week, 'year' => $year])
                                ->with('info', '這週的故事已經生成過了');
            }
        }

        // 上週結尾
        $prevStory = WeeklyStory::where('user_id', $userId)
                                ->where('year', $year)
                                ->where('week', $week - 1)
                                ->whereNotNull('story_text')
                                ->first();
        $prevTail = $prevStory ? $prevStory->prev_story_tail : null;

        // 任務摘要
        $summary = $tasks->map(fn($t) => [
            'title'  => $t->title,
            'type'   => $t->type,
            'status' => $t->status,
        ])->toArray();

        // 組 prompt
        $completedItems = $tasks->where('status','completed')
            ->map(fn($t) => "・【{$t->type}】{$t->title}")
            ->join("\n");
        $pendingItems = $tasks->where('status','pending')
            ->map(fn($t) => "・【{$t->type}】{$t->title}")
            ->join("\n");

        $prevContext = $prevTail
            ? "上週故事的結尾是：「{$prevTail}」，請讓這週的故事與上週有所連結。"
            : "這是主人翁的第一週故事。";

        // 計算任務實際分布的日期
        $taskDates = $tasks->where('status','completed')
            ->pluck('date')
            ->unique()
            ->sort()
            ->values();
        $dateCount = $taskDates->count();
        $dateDesc  = $dateCount === 1
            ? "這週所有任務都發生在同一天（{$taskDates->first()}），請不要把任務分配到不同天"
            : "這週任務分布在 {$dateCount} 天：" . $taskDates->join('、');

        $prompt = <<<EOT
你是一位擅長寫溫馨生活故事的作家。請根據以下「實際任務清單」，為主人翁「{$user->name}」寫一篇本週生活故事。

{$prevContext}

== 本週實際完成的任務（只能根據這些內容發揮）==
{$completedItems}

== 本週尚未完成的任務 ==
{$pendingItems}

== 寫作規則（嚴格遵守）==
1. 故事中出現的所有活動，必須來自上面的任務清單，不可自行添加清單以外的活動
2. 任務名稱可以用比較生活化的方式描述，但不能改變任務的本質
3. 如果任務名稱很簡短（例如「123」），請照字面意思寫，不要擅自解讀成其他事情
4. 第三人稱敘述，主人翁名字叫「{$user->name}」
5. 長度約 250～350 字
6. 風格溫馨、生活化
7. 未完成的任務只需暗示「還有些事情尚未完成」即可，不要點名
8. 故事最後一段請以「【本週結語】」開頭，這段將作為下週故事的連結素材
9. 時間分配規則：{$dateDesc}。不可以自行捏造任務發生的星期幾或時間點

請直接輸出故事內容，不需要加標題。
EOT;

        // 呼叫 Groq API
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'      => 'llama-3.3-70b-versatile',
                'max_tokens' => 1000,
                'messages'   => [
                    ['role' => 'system', 'content' => '你是一位溫馨故事作家，請用繁體中文寫作。'],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

            $storyText = $response->json('choices.0.message.content');

            \Log::info('Groq response status: ' . $response->status());
            \Log::info('Story text length: ' . strlen($storyText ?? ''));
            \Log::info('Story text preview: ' . substr($storyText ?? '', 0, 100));

            // 擷取【結尾】段落
            $tail = null;
            if (preg_match('/【本週結語】(.+)$/s', $storyText, $matches)) {
                $tail = trim($matches[1]);
                $storyText = trim(preg_replace('/【本週結語】.+$/s', '', $storyText));
            }

            // 儲存
            WeeklyStory::updateOrCreate(
                ['user_id' => $userId, 'week' => $week, 'year' => $year],
                [
                    'task_summary'    => $summary,
                    'prev_story_tail' => $tail,
                    'story_text'      => $storyText,
                    'generated_at'    => now(),
                ]
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'AI 生成失敗，請稍後再試：' . $e->getMessage());
        }

        return redirect()->route('stories.index', ['week' => $week, 'year' => $year])
                         ->with('success', '故事生成完成！');
    }
}
