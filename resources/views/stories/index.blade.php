{{-- resources/views/stories/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TimeTown｜週故事</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #F5EDD6;
            font-family: 'Noto Serif TC', serif;
            min-height: 100vh;
        }

        .header {
            background: #3D2B1F;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-title {
            color: #F5EDD6;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .header-deco {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .deco-line {
            width: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #C9A84C);
        }

        .deco-diamond {
            width: 6px;
            height: 6px;
            background: #C9A84C;
            transform: rotate(45deg);
        }

        .main {
            max-width: 760px;
            margin: 0 auto;
            padding: 28px 20px 100px;
        }

        /* Flash */
        .flash {
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .flash-success {
            background: #EAF3DE;
            color: #3B6D11;
            border: 1px solid #C0DD97;
        }

        .flash-info {
            background: #FAEEDA;
            color: #854F0B;
            border: 1px solid #FAC775;
        }

        .flash-error {
            background: #FAEDED;
            color: #A32D2D;
            border: 1px solid #F5A5A5;
        }

        /* 週選擇器 */
        .week-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }

        .week-selector label {
            font-size: 12px;
            color: #8B6914;
            font-weight: 700;
        }

        .week-selector select {
            background: #F5EDD6;
            border: 1.5px solid #D4C089;
            border-radius: 8px;
            padding: 6px 12px;
            font-family: 'Noto Serif TC', serif;
            font-size: 13px;
            color: #3D2B1F;
            outline: none;
        }

        /* 生成按鈕區 */
        .generate-box {
            background: #FDF7EC;
            border: 1.5px dashed #C9A84C;
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            margin-bottom: 24px;
        }

        .generate-hint {
            font-size: 13px;
            color: #8B6914;
            margin-bottom: 16px;
            line-height: 1.7;
        }

        .generate-btn {
            background: #C9A84C;
            color: #3D2B1F;
            border: none;
            border-radius: 10px;
            padding: 11px 28px;
            font-family: 'Noto Serif TC', serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }

        .generate-btn:hover {
            background: #B89040;
        }

        .generate-btn:disabled {
            background: #D4C089;
            cursor: not-allowed;
        }

        /* 故事卡片 */
        .story-card {
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 18px;
            padding: 32px 36px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(61, 43, 31, 0.07);
        }

        .story-week-label {
            font-size: 11px;
            color: #9A7230;
            letter-spacing: 0.1em;
            margin-bottom: 6px;
        }

        .story-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #D4C089, transparent);
            margin: 18px 0;
        }

        .story-text {
            font-size: 15px;
            line-height: 2.1;
            color: #3D2B1F;
            white-space: pre-wrap;
        }

        .story-footer {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .story-generated {
            font-size: 11px;
            color: #B8A880;
        }

        .story-tasks-toggle {
            font-size: 11px;
            color: #8B6914;
            cursor: pointer;
            border: 1px solid #D4C089;
            border-radius: 99px;
            padding: 3px 12px;
            background: none;
            font-family: 'Noto Serif TC', serif;
        }

        .story-tasks-toggle:hover {
            background: #F5EDD6;
        }

        /* 任務摘要展開 */
        .task-summary-list {
            margin-top: 14px;
            padding: 14px 16px;
            background: rgba(245, 237, 214, 0.6);
            border-radius: 10px;
            display: none;
        }

        .task-summary-list.show {
            display: block;
        }

        .tsm-item {
            font-size: 12px;
            color: #6B4C35;
            padding: 3px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tsm-done {
            color: #6A9E4E;
        }

        .tsm-pending {
            color: #C9A84C;
        }

        /* 空狀態 */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9A7230;
        }

        .empty-icon {
            font-size: 40px;
            margin-bottom: 14px;
            opacity: 0.5;
        }

        .empty-text {
            font-size: 14px;
            line-height: 1.8;
        }

        /* loading */
        .loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(61, 43, 31, 0.55);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-text {
            color: #F5EDD6;
            font-size: 15px;
            letter-spacing: 0.08em;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(245, 237, 214, 0.3);
            border-top-color: #C9A84C;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* 導覽 */
        .nav {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 50;
        }

        .nav-btn {
            background: rgba(61, 43, 31, 0.85);
            color: #F5EDD6;
            border: 1px solid rgba(201, 168, 76, 0.5);
            padding: 8px 20px;
            border-radius: 99px;
            font-family: 'Noto Serif TC', serif;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .nav-btn:hover {
            background: #3D2B1F;
        }

        .nav-btn.active {
            background: #C9A84C;
            color: #3D2B1F;
            border-color: #C9A84C;
        }
    </style>
</head>

<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">AI 正在為你的城鎮撰寫故事⋯⋯</div>
    </div>

    <div class="header">
        <div class="header-title">TimeTown｜週故事</div>
        <div class="header-deco">
            <div class="deco-line"></div>
            <div class="deco-diamond"></div>
        </div>
    </div>

    <div class="main">

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="flash flash-info">{{ session('info') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        {{-- 週選擇器 --}}
        <div class="week-selector">
            <label>選擇週次</label>
            <select onchange="
            const [y,w] = this.value.split('-');
            location.href='{{ url('/stories') }}?year='+y+'&week='+w;
        ">
                @forelse($availableWeeks as $aw)
                    <option value="{{ $aw->year }}-{{ $aw->week }}" {{ $aw->year == $year && $aw->week == $week ? 'selected' : '' }}>
                        {{ $aw->year }} 年 第 {{ $aw->week }} 週
                    </option>
                @empty
                    <option>尚無故事</option>
                @endforelse
                {{-- 如果當週有任務但還沒故事，補上當週選項 --}}
                @if($availableWeeks->where('week', $week)->where('year', $year)->isEmpty() && $weekTasks->isNotEmpty())
                    <option value="{{ $year }}-{{ $week }}" selected>
                        {{ $year }} 年 第 {{ $week }} 週（尚未生成）
                    </option>
                @endif
            </select>
        </div>

        @if($canGenerate)
            {{-- 可以生成 --}}
            <div class="generate-box">
                <div class="generate-hint">
                    這週共有 <strong>{{ $weekTasks->count() }}</strong> 個任務紀錄<br>
                    完成了 <strong>{{ $weekTasks->where('status', 'completed')->count() }}</strong> 個<br><br>
                    讓 AI 根據這週的生活，為 <strong>{{ $user->name }}</strong> 的城鎮寫一篇故事吧！
                </div>
                <form action="{{ url('/stories/generate') }}" method="POST" onsubmit="document.getElementById('loadingOverlay').classList.add('show');
                                document.querySelector('.generate-btn').disabled=true;">
                    @csrf
                    <input type="hidden" name="week" value="{{ $week }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="generate-btn">✨ 生成本週故事</button>
                </form>
            </div>

        @elseif($story && $story->story_text)
            {{-- 故事已生成 --}}
            <div class="story-card">
                <div class="story-week-label">
                    {{ $year }} 年 第 {{ $week }} 週・{{ $user->name }} 的城鎮故事
                </div>
                <div class="story-divider"></div>
                <div class="story-text">{{ $story->story_text }}</div>
                <div class="story-divider"></div>
                <div class="story-footer">
                    <span class="story-generated">
                        生成於 {{ \Carbon\Carbon::parse($story->generated_at)->format('Y/m/d H:i') }}
                    </span>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <button class="story-tasks-toggle" onclick="toggleTasks(this)">查看本週任務摘要</button>
                        <form action="{{ url('/stories/generate') }}" method="POST"
                            onsubmit="document.getElementById('loadingOverlay').classList.add('show');">
                            @csrf
                            <input type="hidden" name="week" value="{{ $week }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="force" value="1">
                            <button type="submit" class="story-tasks-toggle" style="color:#8B6914;">重新生成</button>
                        </form>
                    </div>
                </div>
                <div class="task-summary-list" id="taskSummary">
                    @foreach($story->task_summary as $t)
                        <div class="tsm-item">
                            <span class="{{ $t['status'] === 'completed' ? 'tsm-done' : 'tsm-pending' }}">
                                {{ $t['status'] === 'completed' ? '✓' : '○' }}
                            </span>
                            <span>{{ $t['title'] }}（{{ $t['type'] }}）</span>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            {{-- 這週沒有任務 --}}
            <div class="empty-state">
                <div class="empty-icon">📖</div>
                <div class="empty-text">
                    這週還沒有任務紀錄<br>
                    去行事曆新增任務，累積你的城鎮故事吧！
                </div>
            </div>
        @endif

    </div>

    <nav class="nav">
        <a href="{{ url('/') }}" class="nav-btn">城鎮</a>
        <a href="{{ url('/calendar') }}" class="nav-btn">行事曆</a>
        <a href="{{ url('/review') }}" class="nav-btn">回顧</a>
        <a href="{{ url('/stories') }}" class="nav-btn active">故事</a>
        <a href="{{ url('/guide') }}" class="nav-btn">圖鑑</a>
    </nav>

    <script>
        function toggleTasks(btn) {
            const list = document.getElementById('taskSummary');
            list.classList.toggle('show');
            btn.textContent = list.classList.contains('show') ? '收起任務摘要' : '查看本週任務摘要';
        }
    </script>
</body>

</html>
