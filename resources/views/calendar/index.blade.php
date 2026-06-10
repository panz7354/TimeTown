{{-- resources/views/calendar/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TimeTown｜行事曆</title>
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

        /* ── 頂部 Header ── */
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

        /* ── 主要容器 ── */
        .main {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 20px 100px;
        }

        /* ── 新增任務表單 ── */
        .add-form {
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 28px;
        }

        .form-title {
            font-size: 14px;
            font-weight: 700;
            color: #8B6914;
            letter-spacing: 0.06em;
            margin-bottom: 14px;
        }

        .form-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-size: 11px;
            color: #8B6914;
            letter-spacing: 0.05em;
        }

        .form-group input,
        .form-group select {
            background: #F5EDD6;
            border: 1.5px solid #D4C089;
            border-radius: 8px;
            padding: 7px 12px;
            font-family: 'Noto Serif TC', serif;
            font-size: 13px;
            color: #3D2B1F;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #C9A84C;
        }

        .form-group.grow {
            flex: 1;
            min-width: 160px;
        }

        .submit-btn {
            background: #C9A84C;
            color: #3D2B1F;
            border: none;
            border-radius: 8px;
            padding: 9px 22px;
            font-family: 'Noto Serif TC', serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
            white-space: nowrap;
            align-self: flex-end;
        }

        .submit-btn:hover {
            background: #B89040;
        }

        /* ── Flash 訊息 ── */
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

        /* ── 區段標題 ── */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            margin-top: 24px;
        }

        .section-header:first-of-type {
            margin-top: 0;
        }

        .section-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, #D4C089, transparent);
        }

        .section-label {
            font-size: 12px;
            font-weight: 700;
            color: #8B6914;
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        /* ── 任務卡片列表 ── */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .task-card {
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: border-color 0.15s;
        }

        .task-card:hover {
            border-color: #C9A84C;
        }

        .task-card.completed {
            opacity: 0.55;
        }

        /* 類型色條 */
        .type-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .task-info {
            flex: 1;
        }

        .task-title {
            font-size: 14px;
            color: #3D2B1F;
            margin-bottom: 3px;
        }

        .task-title.done {
            text-decoration: line-through;
            color: #8B7A6A;
        }

        .task-meta {
            font-size: 11px;
            color: #9A7230;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .task-type-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 99px;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* 完成按鈕 */
        .complete-form {
            flex-shrink: 0;
        }

        .complete-btn {
            background: none;
            border: 1.5px solid #C9A84C;
            border-radius: 8px;
            padding: 5px 14px;
            font-family: 'Noto Serif TC', serif;
            font-size: 12px;
            color: #8B6914;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .complete-btn:hover {
            background: #C9A84C;
            color: #3D2B1F;
        }

        .done-mark {
            font-size: 12px;
            color: #6A9E4E;
            font-weight: 700;
            padding: 5px 10px;
        }

        /* ── 空狀態 ── */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9A7230;
            font-size: 14px;
        }

        .empty-icon {
            font-size: 32px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        /* ── 底部導覽 ── */
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

        .page-btn{
            background:#FDF7EC;border:1.5px solid #D4C089;border-radius:8px;
            padding:6px 14px;font-size:13px;color:#8B6914;text-decoration:none;cursor:pointer;
        }
        .page-btn.disabled{opacity:0.4;cursor:default;pointer-events:none;}
        .page-btn:not(.disabled):hover{background:#F5EDD6;}
    </style>
</head>

<body>

    <div class="header">
        <div class="header-title">TimeTown｜行事曆</div>
        <div class="header-deco">
            <div class="deco-line"></div>
            <div class="deco-diamond"></div>
        </div>
    </div>

    <div class="main">

        {{-- Flash 訊息 --}}
        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="flash flash-info">{{ session('info') }}</div>
        @endif

        {{-- ══ 新增任務表單 ══ --}}
        <div class="add-form">
            <div class="form-title">新增任務</div>
            <form action="{{ url('/tasks') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group grow">
                        <label>任務名稱</label>
                        <input type="text" name="title" placeholder="今天要做什麼？" required maxlength="100"
                            value="{{ old('title') }}">
                    </div>
                    <div class="form-group">
                        <label>類型</label>
                        <select name="type" required>
                            <option value="" disabled {{ old('type') ? '' : 'selected' }}>選擇類型</option>
                            @foreach(['學習', '工作', '運動', '社交', '休息', '興趣創作'] as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>日期</label>
                        <input type="date" name="date" required value="{{ old('date', now()->toDateString()) }}">
                    </div>
                    <button type="submit" class="submit-btn">新增任務</button>
                </div>
                @error('title')<div style="font-size:12px;color:#A32D2D;margin-top:6px;">{{ $message }}</div>@enderror
                @error('type') <div style="font-size:12px;color:#A32D2D;margin-top:6px;">{{ $message }}</div>@enderror
                @error('date') <div style="font-size:12px;color:#A32D2D;margin-top:6px;">{{ $message }}</div>@enderror
            </form>
        </div>

        @php
            $typeColors = [
                '學習' => ['dot' => '#7EB8D4', 'bg' => '#E6F1FB', 'text' => '#185FA5'],
                '工作' => ['dot' => '#B87BCF', 'bg' => '#EEEDFE', 'text' => '#534AB7'],
                '運動' => ['dot' => '#6C9A54', 'bg' => '#EAF3DE', 'text' => '#3B6D11'],
                '社交' => ['dot' => '#D88A6A', 'bg' => '#FAECE7', 'text' => '#993C1D'],
                '休息' => ['dot' => '#C9A84C', 'bg' => '#FAEEDA', 'text' => '#854F0B'],
                '興趣創作' => ['dot' => '#EFB0A4', 'bg' => '#FBEAF0', 'text' => '#993556'],
            ];
            $pending = $pending->sortByDesc('date');
        @endphp

        {{-- ══ 待完成任務 ══ --}}
        <div class="section-header">
            <span class="section-label">待完成（{{ $pending->count() }}）</span>
            <div class="section-line"></div>
        </div>

        @if($pending->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏡</div>
                <div>目前沒有待完成的任務，去新增一個吧！</div>
            </div>
        @else
            <div class="task-list">
                @foreach($pending as $task)
                    @php $c = $typeColors[$task->type] ?? ['dot' => '#888', 'bg' => '#eee', 'text' => '#555']; @endphp
                    <div class="task-card">
                        <div class="type-dot" style="background:{{ $c['dot'] }}"></div>
                        <div class="task-info">
                            <div class="task-title">{{ $task->title }}</div>
                            <div class="task-meta">
                                <span>{{ \Carbon\Carbon::parse($task->date)->format('Y/m/d') }}</span>
                                <span>第 {{ $task->week }} 週</span>
                            </div>
                        </div>
                        <span class="task-type-badge" style="background:{{ $c['bg'] }};color:{{ $c['text'] }}">
                            {{ $task->type }}
                        </span>
                        <div class="complete-form">
                            <form action="{{ url('/tasks/' . $task->id . '/complete') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="complete-btn">完成</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ══ 最近完成 ══ --}}
        @if($completed->isNotEmpty())
            <div class="section-header">
                <span class="section-label">已完成（{{ $completed->total() }} 筆）</span>
                <div class="section-line"></div>
            </div>
            <div class="task-list">
                @foreach($completed as $task)
                    @php $c = $typeColors[$task->type] ?? ['dot' => '#888', 'bg' => '#eee', 'text' => '#555']; @endphp
                    <div class="task-card completed">
                        <div class="type-dot" style="background:{{ $c['dot'] }};opacity:0.5"></div>
                        <div class="task-info">
                            <div class="task-title done">{{ $task->title }}</div>
                            <div class="task-meta">
                                <span>{{ \Carbon\Carbon::parse($task->date)->format('Y/m/d') }}</span>
                                <span>完成於 {{ \Carbon\Carbon::parse($task->completed_at)->format('m/d H:i') }}</span>
                            </div>
                        </div>
                        <span class="task-type-badge" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};opacity:0.7">
                            {{ $task->type }}
                        </span>
                        <div class="done-mark">✓ 完成</div>
                    </div>
                @endforeach
                @if($completed->hasPages())
                    <div style="display:flex;justify-content:center;gap:8px;margin-top:16px;align-items:center;">
                        @if($completed->onFirstPage())
                            <span class="page-btn disabled">←</span>
                        @else
                            <a href="{{ $completed->previousPageUrl() }}" class="page-btn">←</a>
                        @endif
                        <span style="font-size:13px;color:#9A7230;padding:6px 12px;">
                            第 {{ $completed->currentPage() }} 頁，共 {{ $completed->lastPage() }} 頁
                        </span>
                        @if($completed->hasMorePages())
                            <a href="{{ $completed->nextPageUrl() }}" class="page-btn">→</a>
                        @else
                            <span class="page-btn disabled">→</span>
                        @endif
                    </div>
                @endif
            </div>
        @endif

    </div>{{-- /main --}}

    {{-- 底部導覽 --}}
    <nav class="nav">
        <a href="{{ url('/') }}" class="nav-btn">城鎮</a>
        <a href="{{ url('/calendar') }}" class="nav-btn active">行事曆</a>
        <a href="{{ url('/review') }}" class="nav-btn">回顧</a>
        <a href="{{ url('/stories') }}" class="nav-btn">故事</a>
    </nav>

</body>

</html>
