{{-- resources/views/review/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimeTown｜週回顧</title>
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

        .header-deco {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 20px 100px;
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
            cursor: pointer;
        }

        .week-selector select:focus {
            border-color: #C9A84C;
        }

        .week-range {
            font-size: 12px;
            color: #9A7230;
            margin-left: auto;
        }

        /* 區段標題 */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 24px 0 14px;
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

        /* 摘要卡片 */
        .summary-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .summary-card {
            flex: 1;
            min-width: 140px;
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 12px;
            padding: 16px 18px;
            text-align: center;
        }

        .summary-num {
            font-size: 28px;
            font-weight: 700;
            color: #3D2B1F;
            line-height: 1;
        }

        .summary-label {
            font-size: 11px;
            color: #9A7230;
            margin-top: 5px;
        }

        /* 圖表容器 */
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 8px;
        }

        @media(max-width:600px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 14px;
            padding: 18px;
        }

        .chart-title {
            font-size: 12px;
            font-weight: 700;
            color: #8B6914;
            margin-bottom: 14px;
            letter-spacing: 0.06em;
        }

        canvas {
            width: 100% !important;
        }

        /* 升級紀錄 */
        .upgrade-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .upgrade-card {
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 12px;
            padding: 11px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .upgrade-icon {
            font-size: 20px;
        }

        .upgrade-info {
            flex: 1;
        }

        .upgrade-name {
            font-size: 14px;
            font-weight: 700;
            color: #3D2B1F;
        }

        .upgrade-meta {
            font-size: 11px;
            color: #9A7230;
            margin-top: 2px;
        }

        .upgrade-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 99px;
            background: #FAEEDA;
            color: #854F0B;
            font-weight: 700;
        }

        /* 城鎮快照 */
        .snapshot-wrap {
            background: #FDF7EC;
            border: 1.5px solid #D4C089;
            border-radius: 14px;
            padding: 18px;
            overflow: hidden;
        }

        .snapshot-map {
            position: relative;
            width: 100%;
            padding-top: 55%;
            background: #8BAF66;
            border-radius: 10px;
            overflow: hidden;
        }

        .snapshot-inner {
            position: absolute;
            inset: 0;
        }

        .snap-building {
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: translate(-50%, -50%);
        }

        .snap-building img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            image-rendering: pixelated;
        }

        .snap-label {
            font-size: 8px;
            color: #3D2B1F;
            background: rgba(245, 237, 214, 0.85);
            padding: 1px 5px;
            border-radius: 99px;
            margin-top: 2px;
            white-space: nowrap;
        }

        /* 空狀態 */
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

    <div class="header">
        <div class="header-title">TimeTown｜週回顧</div>
        <div class="header-deco">
            <div class="deco-line"></div>
            <div class="deco-diamond"></div>
        </div>
    </div>

    <div class="main">

        {{-- 週選擇器 --}}
        <div class="week-selector">
            <label>選擇週次</label>
            <select onchange="
            const [y,w] = this.value.split('-');
            location.href='{{ url('/review') }}?year='+y+'&week='+w;
        ">
                @foreach($availableWeeks as $aw)
                    <option value="{{ $aw->year }}-{{ $aw->week }}" {{ $aw->year == $year && $aw->week == $week ? 'selected' : '' }}>
                        {{ $aw->year }} 年 第 {{ $aw->week }} 週
                    </option>
                @endforeach
                @if($availableWeeks->isEmpty())
                    <option>尚無資料</option>
                @endif
            </select>
            <span class="week-range">
                {{ $weekStart->format('m/d') }}（一）～ {{ $weekEnd->format('m/d') }}（日）
            </span>
        </div>

        @if($tasks->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <div>這週還沒有任務紀錄</div>
            </div>
        @else

            {{-- 摘要 --}}
            <div class="section-header">
                <span class="section-label">本週摘要</span>
                <div class="section-line"></div>
            </div>
            <div class="summary-row">
                <div class="summary-card">
                    <div class="summary-num">{{ $tasks->count() }}</div>
                    <div class="summary-label">總任務數</div>
                </div>
                <div class="summary-card">
                    <div class="summary-num" style="color:#6A9E4E">{{ $completed->count() }}</div>
                    <div class="summary-label">已完成</div>
                </div>
                <div class="summary-card">
                    <div class="summary-num" style="color:#C9A84C">{{ $pending->count() }}</div>
                    <div class="summary-label">未完成</div>
                </div>
                <div class="summary-card">
                    <div class="summary-num" style="color:#7EB8D4">
                        {{ $tasks->count() > 0 ? round($completed->count() / $tasks->count() * 100) : 0 }}%
                    </div>
                    <div class="summary-label">完成率</div>
                </div>
            </div>

            {{-- 圖表 --}}
            <div class="section-header">
                <span class="section-label">類型分析</span>
                <div class="section-line"></div>
            </div>
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-title">各類型完成 / 未完成</div>
                    <canvas id="barChart" height="200"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">完成任務類型佔比</div>
                    <canvas id="pieChart" height="200"></canvas>
                </div>
            </div>

            {{-- 升級紀錄 --}}
            <div class="section-header">
                <span class="section-label">本週建築升級</span>
                <div class="section-line"></div>
            </div>
            @if($upgradedBuildings->isEmpty())
                <div class="empty-state" style="padding:20px;">
                    <div>這週沒有建築升級紀錄</div>
                </div>
            @else
                <div class="upgrade-list">
                    @foreach($upgradedBuildings as $b)
                        <div class="upgrade-card">
                            <div class="upgrade-icon">🏗️</div>
                            <div class="upgrade-info">
                                <div class="upgrade-name">{{ $b->name }}</div>
                                <div class="upgrade-meta">{{ $b->type }}・第 {{ $b->slot + 1 }} 棟</div>
                            </div>
                            <span class="upgrade-badge">Lv.{{ $b->level }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 城鎮快照 --}}
            <div class="section-header">
                <span class="section-label">城鎮快照</span>
                <div class="section-line"></div>
            </div>
            <div class="snapshot-wrap">
                @if($snapshot->isEmpty())
                    <div class="empty-state" style="padding:20px;">
                        <div>還沒有放置任何建築</div>
                    </div>
                @else
                    <div class="snapshot-map">
                        <div class="snapshot-inner">
                            {{-- 簡易地形背景 --}}
                            <svg width="100%" height="100%" viewBox="0 0 1684 1056" preserveAspectRatio="xMidYMid slice"
                                style="position:absolute;inset:0;">
                                <rect width="1684" height="1056" fill="#8BAF66" />
                                {{-- 橫向道路 row=2後,row=6後 --}}
                                <rect x="0" y="228" width="1684" height="28" fill="#D2BD85" />
                                <rect x="0" y="628" width="1684" height="28" fill="#D2BD85" />
                                {{-- 縱向道路 col=3後,col=8後,col=13後 --}}
                                <rect x="328" y="0" width="28" height="1056" fill="#D2BD85" />
                                <rect x="828" y="0" width="28" height="1056" fill="#D2BD85" />
                                <rect x="1328" y="0" width="28" height="1056" fill="#D2BD85" />
                                {{-- 河流 col=14-15 --}}
                                <rect x="1384" y="0" width="300" height="1056" fill="#6AAEC8" />
                            </svg>
                            {{-- 建築點 --}}
                            @foreach($snapshot as $b)
                                @php
                                    $roadCols = [3, 8, 13];
                                    $roadRows = [2, 6];
                                    $cellSz   = 100;
                                    $roadW    = 28;
                                    $mapW     = $cellSz * 16 + $roadW * 3;  // 1684
                                    $mapH     = $cellSz * 10 + $roadW * 2;  // 1056
                                @endphp

                                @foreach($snapshot as $b)
                                    @php
                                        // 計算 col 像素
                                        $bx = 0;
                                        for($i = 0; $i < $b->grid_x; $i++){
                                            $bx += $cellSz;
                                            if(in_array($i, $roadCols)) $bx += $roadW;
                                        }
                                        $bx += $cellSz / 2;

                                        // 計算 row 像素
                                        $by = 0;
                                        for($i = 0; $i < $b->grid_y; $i++){
                                            $by += $cellSz;
                                            if(in_array($i, $roadRows)) $by += $roadW;
                                        }
                                        $by += $cellSz * 0.85;

                                        $px = $bx / $mapW * 100;
                                        $py = $by / $mapH * 100;
                                    @endphp
                                    <div class="snap-building" style="left:{{ $px }}%;top:{{ $py }}%;">
                                        <img src="/svg/{{ $b->svg_file }}" alt="{{ $b->name }}">
                                        <div class="snap-label">{{ $b->name }}</div>
                                    </div>
                                @endforeach
                                <div class="snap-building" style="left:{{ $px }}%;top:{{ $py }}%;">
                                    <img src="/svg/{{ $b->svg_file }}" alt="{{ $b->name }}">
                                    <div class="snap-label">{{ $b->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        @endif {{-- end tasks not empty --}}

    </div>

    <nav class="nav">
        <a href="{{ url('/') }}" class="nav-btn">城鎮</a>
        <a href="{{ url('/calendar') }}" class="nav-btn">行事曆</a>
        <a href="{{ url('/review') }}" class="nav-btn active">回顧</a>
        <a href="{{ url('/stories') }}" class="nav-btn">故事</a>
    </nav>

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const types = @json($types);
        const stats = @json($stats);

        const typeColors = {
            '學習': '#7EB8D4',
            '工作': '#B87BCF',
            '運動': '#6C9A54',
            '社交': '#D88A6A',
            '休息': '#C9A84C',
            '興趣創作': '#EFB0A4',
        };

        // 長條圖
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: types,
                datasets: [
                    {
                        label: '已完成',
                        data: types.map(t => stats[t].completed),
                        backgroundColor: types.map(t => typeColors[t]),
                        borderRadius: 6,
                    },
                    {
                        label: '未完成',
                        data: types.map(t => stats[t].pending),
                        backgroundColor: types.map(t => typeColors[t] + '55'),
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { font: { family: 'Noto Serif TC' }, boxWidth: 12 } } },
                scales: {
                    x: { ticks: { font: { family: 'Noto Serif TC', size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Noto Serif TC' } } }
                }
            }
        });

        // 圓餅圖
        const pieData = types.map(t => stats[t].completed).filter(v => v > 0);
        const pieLabels = types.filter(t => stats[t].completed > 0);
        const pieColors = pieLabels.map(t => typeColors[t]);

        if (pieData.length > 0) {
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: pieLabels,
                    datasets: [{ data: pieData, backgroundColor: pieColors, borderWidth: 2, borderColor: '#FDF7EC' }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Noto Serif TC' }, boxWidth: 12, padding: 10 } }
                    }
                }
            });
        } else {
            document.getElementById('pieChart').parentElement.innerHTML =
                '<div class="chart-title">完成任務類型佔比</div><div style="text-align:center;padding:40px;color:#9A7230;font-size:13px;">本週尚無完成任務</div>';
        }
    </script>
</body>

</html>
