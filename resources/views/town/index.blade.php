{{-- resources/views/town/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TimeTown｜我的城鎮</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Noto Serif TC', serif;
        }

        #bannerArea {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 60;
        }

        .unplaced-banner {
            background: #3D2B1F;
            color: #F5EDD6;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 13px;
        }

        .unplaced-list {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .unplaced-chip {
            background: #C9A84C;
            color: #3D2B1F;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 99px;
            cursor: pointer;
            border: 2px solid transparent;
            font-family: 'Noto Serif TC', serif;
            transition: transform 0.1s, border-color 0.1s;
        }

        .unplaced-chip:hover {
            transform: scale(1.06);
        }

        .unplaced-chip.active {
            border-color: #F4D48E;
        }

        .placing-hint {
            background: #8B6914;
            color: #F5EDD6;
            padding: 6px 16px;
            text-align: center;
            font-size: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .placing-hint.show {
            display: flex;
        }

        .cancel-btn {
            background: none;
            border: 1px solid rgba(245, 237, 214, 0.6);
            color: #F5EDD6;
            padding: 2px 10px;
            border-radius: 99px;
            cursor: pointer;
            font-family: 'Noto Serif TC', serif;
            font-size: 11px;
        }

        #vp {
            position: fixed;
            inset: 0;
            overflow: hidden;
            cursor: grab;
            background: #8BAF66;
            touch-action: none;
        }

        #vp.dragging {
            cursor: grabbing;
        }

        #world {
            position: absolute;
            top: 0;
            left: 0;
            will-change: transform;
        }

        #mapWrap {
            position: relative;
        }

        #terrainSvg {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        #gridLayer {
            position: absolute;
            inset: 0;
            display: grid;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s;
        }

        #gridLayer.show {
            pointer-events: all;
            opacity: 1;
        }

        .cell {
            transition: background 0.1s;
        }

        .placeable {
            cursor: pointer;
        }

        .placeable:hover {
            background: rgba(201, 168, 76, 0.25) !important;
            outline: 2px dashed rgba(201, 168, 76, 0.8);
            outline-offset: -3px;
        }

        .building {
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            z-index: 10;
            transform: translate(-50%, -80%);
        }

        .building:hover .bld-tag {
            opacity: 1;
            transform: translateY(0);
        }

        .bld-tag {
            position: absolute;
            bottom: calc(100% + 4px);
            background: #3D2B1F;
            color: #F5EDD6;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            white-space: nowrap;
            opacity: 0;
            transform: translateY(4px);
            transition: all 0.13s;
            pointer-events: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .bld-tag::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 4px solid transparent;
            border-top-color: #3D2B1F;
        }

        .bld-img {
            object-fit: contain;
            image-rendering: pixelated;
        }

        .panel-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(40, 26, 16, 0.48);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .panel-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .panel {
            background: #F5EDD6;
            border-radius: 18px;
            border: 2px solid #C9A84C;
            width: min(310px, 90vw);
            padding: 22px;
            position: relative;
            box-shadow: 0 8px 32px rgba(40, 26, 16, 0.3);
        }

        .panel-close {
            position: absolute;
            top: 12px;
            right: 14px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #8B6914;
        }

        .p-title {
            font-size: 16px;
            font-weight: 700;
            color: #3D2B1F;
            margin-bottom: 3px;
        }

        .p-sub {
            font-size: 11px;
            color: #9A7230;
            margin-bottom: 14px;
        }

        .p-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #D4C089, transparent);
            margin: 10px 0;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 7px;
        }

        .stat-label {
            font-size: 12px;
            color: #6B4C35;
        }

        .stat-val {
            font-size: 13px;
            font-weight: 700;
            color: #3D2B1F;
        }

        .prog-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #9A7230;
            margin-bottom: 5px;
            margin-top: 10px;
        }

        .prog-bar {
            height: 9px;
            background: #E8D9B0;
            border-radius: 99px;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            background: #C9A84C;
            border-radius: 99px;
            transition: width 0.5s;
        }

        .next-label {
            font-size: 11px;
            color: #9A7230;
            margin: 12px 0 8px;
        }

        .next-bld {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(180, 178, 169, 0.2);
            border: 1.5px dashed #C4B99A;
            border-radius: 12px;
            padding: 10px;
            opacity: 0.65;
        }

        .next-bld-img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            image-rendering: pixelated;
            filter: grayscale(1) opacity(0.7);
        }

        .next-name {
            font-size: 13px;
            font-weight: 700;
            color: #7A6048;
        }

        .next-req {
            font-size: 11px;
            color: #9A7230;
            margin-top: 2px;
        }

        .next-badge {
            display: inline-block;
            margin-top: 5px;
            background: #D3D1C7;
            color: #5F5E5A;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 99px;
        }

        .max-hint {
            font-size: 12px;
            color: #C9A84C;
            text-align: center;
            margin-top: 12px;
            font-weight: 700;
        }

        .toast {
            position: fixed;
            bottom: 72px;
            left: 50%;
            transform: translateX(-50%);
            background: #3D2B1F;
            color: #F5EDD6;
            font-size: 13px;
            padding: 10px 20px;
            border-radius: 99px;
            z-index: 200;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            white-space: nowrap;
        }

        .toast.show {
            opacity: 1;
        }

        .nav {
            position: fixed;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 80;
        }

        .nav-btn {
            background: rgba(61, 43, 31, 0.88);
            color: #F5EDD6;
            border: 1px solid rgba(201, 168, 76, 0.5);
            padding: 8px 18px;
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
    <div id="bannerArea">
        @if(isset($unplaced) && $unplaced->count() > 0)
            <div class="unplaced-banner">
                <span>新建築等待放置：</span>
                <div class="unplaced-list">
                    @foreach($unplaced as $b)
                        <button class="unplaced-chip" data-id="{{ $b->id }}" data-name="{{ $b->name }}"
                            data-type="{{ $b->type }}">
                            {{ $b->name }}（{{ $b->type }}）
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="placing-hint" id="placingHint">
            <span>點擊草地放置建築</span>
            <button class="cancel-btn" onclick="cancelPlacing()">取消</button>
        </div>
    </div>

    <div id="vp">
        <div id="world">
            <div id="mapWrap">
                <svg id="terrainSvg" xmlns="http://www.w3.org/2000/svg"></svg>
                <div id="gridLayer">
                    @php
                        /*
                         * 地圖格局：16欄 × 10列，格子固定 100px
                         * 道路用 SVG 畫，不佔格子
                         * 道路位置（格子之間）：
                         *   縱向：col 3後、col 8後、col 13後（把地圖分4塊）
                         *   橫向：row 2後、row 6後（把地圖分3層）
                         * 特殊區域（不可放置）：
                         *   公園：col 0-2, row 0-1（3×2格）
                         *   廣場：col 5, row 4（1×1格）
                         *   河流：col 14-15, 所有row（2欄，最右側）
                         */
                        $COLS = 16;
                        $ROWS = 10;
                        // 道路在哪幾格「後面」
                        $roadCols = [3, 8, 13];
                        $roadRows = [2, 6];
                        $roadW = 28; // px
                        $cellSz = 100; // px

                        // 不可放置的格子
                        $blocked = [];
                        for ($r = 0; $r <= 1; $r++)
                            for ($c = 0; $c <= 2; $c++)
                                $blocked[] = "$r-$c"; // 公園
                        $blocked[] = "4-5"; // 廣場
                        for ($r = 0; $r < $ROWS; $r++) {
                            $blocked[] = "$r-14";
                            $blocked[] = "$r-15";
                        } // 河流

                        $occupiedCells = [];
                        foreach ($buildings->whereNotNull('grid_x') as $b)
                            $occupiedCells[$b->grid_y . '-' . $b->grid_x] = true;

                        // 計算每格的實際像素位置（含道路偏移）
                        function colPx($c, $roadCols, $roadW, $cellSz)
                        {
                            $x = 0;
                            for ($i = 0; $i < $c; $i++) {
                                $x += $cellSz;
                                if (in_array($i, $roadCols))
                                    $x += $roadW;
                            }
                            return $x;
                        }
                        function rowPx($r, $roadRows, $roadW, $cellSz)
                        {
                            $y = 0;
                            for ($i = 0; $i < $r; $i++) {
                                $y += $cellSz;
                                if (in_array($i, $roadRows))
                                    $y += $roadW;
                            }
                            return $y;
                        }
                    @endphp
                    @for($r = 0; $r < $ROWS; $r++)
                        @for($c = 0; $c < $COLS; $c++)
                            @php
                                $key = "$r-$c";
                                $isBlocked = in_array($key, $blocked);
                                $isOccupied = isset($occupiedCells[$key]);
                                $px = colPx($c, $roadCols, $roadW, $cellSz);
                                $py = rowPx($r, $roadRows, $roadW, $cellSz);
                            @endphp
                            @if(!$isBlocked)
                                <div class="cell{{ $isOccupied ? '' : ' placeable' }}" data-r="{{ $r }}" data-c="{{ $c }}"
                                    style="position:absolute;left:{{ $px }}px;top:{{ $py }}px;width:{{ $cellSz }}px;height:{{ $cellSz }}px;"
                                    @if(!$isOccupied) onclick="selectCell(this)" @endif></div>
                            @endif
                        @endfor
                    @endfor
                </div>

                @foreach($buildings->whereNotNull('grid_x') as $b)
                    @php
                        $upgradeMap = \App\Http\Controllers\BuildingController::UPGRADE_MAP;
                        $svgMapArr = \App\Http\Controllers\BuildingController::SVG_MAP;
                        $levels = $upgradeMap[$b->type];
                        $nextLevel = $b->level + 1;
                        $hasNext = isset($levels[$nextLevel]);
                        $thresholds = [0 => 0, 1 => 1, 2 => 2, 3 => 4];
                        $curT = $thresholds[$b->level] ?? 0;
                        $nextT = $hasNext ? ($thresholds[$nextLevel] ?? $curT) : $curT;
                        $pct = $hasNext && ($nextT > $curT) ? min(100, (int) round(($b->completed_count - $curT) / ($nextT - $curT) * 100)) : 100;
                        $need = $hasNext ? max(0, $nextT - $b->completed_count) : 0;
                        $nextName = $hasNext ? $levels[$nextLevel] : '';
                        $nextSvg = $hasNext ? $svgMapArr[$b->type][$nextLevel] : '';
                        // 建築像素位置
                        $bpx = colPx($b->grid_x, $roadCols, $roadW, $cellSz) + $cellSz / 2;
                        $bpy = rowPx($b->grid_y, $roadRows, $roadW, $cellSz) + $cellSz * 0.85;
                    @endphp
                    <div class="building" id="bld-{{ $b->id }}" data-gx="{{ $b->grid_x }}" data-gy="{{ $b->grid_y }}"
                        style="left:{{ $bpx }}px;top:{{ $bpy }}px;"
                        onclick="openPanel('{{ addslashes($b->name) }}','{{ $b->type }}',{{ $b->level }},{{ $b->completed_count }},{{ $hasNext ? 1 : 0 }},'{{ addslashes($nextName) }}','{{ $nextSvg }}',{{ $need }},{{ $pct }})">
                        <div class="bld-tag">{{ $b->name }}・{{ $b->type }}</div>
                        <img class="bld-img" src="/svg/{{ $b->svg_file }}" alt="{{ $b->name }}"
                            style="width:110px;height:110px;">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel-overlay" id="overlay">
            <div class="panel">
                <button class="panel-close" onclick="closePanel()">✕</button>
                <div class="p-title" id="p-title"></div>
                <div class="p-sub" id="p-sub"></div>
                <div class="p-divider"></div>
                <div class="stat-row"><span class="stat-label">已完成任務</span><span class="stat-val" id="p-done"></span>
                </div>
                <div class="stat-row"><span class="stat-label">目前等級</span><span class="stat-val" id="p-lv"></span></div>
                <div class="prog-meta"><span>升級進度</span><span id="p-pct"></span></div>
                <div class="prog-bar">
                    <div class="prog-fill" id="p-fill"></div>
                </div>
                <div class="p-divider"></div>
                <div id="next-section">
                    <div class="next-label">下一棟建築</div>
                    <div class="next-bld">
                        <img class="next-bld-img" id="p-next-img" src="" alt="">
                        <div>
                            <div class="next-name" id="p-next-name"></div>
                            <div class="next-req" id="p-next-req"></div><span class="next-badge">尚未解鎖</span>
                        </div>
                    </div>
                </div>
                <div class="max-hint" id="p-max" style="display:none">🌟 已達最高等級！</div>
            </div>
        </div>
        <nav class="nav">
            <a href="{{ url('/') }}" class="nav-btn active">城鎮</a>
            <a href="{{ url('/calendar') }}" class="nav-btn">行事曆</a>
            <a href="{{ url('/review') }}" class="nav-btn">回顧</a>
            <a href="{{ url('/stories') }}" class="nav-btn">故事</a>
        </nav>
        <div class="toast" id="toast"></div>
    </div>

    <script>
        // ── 地圖常數（與 PHP 同步）──
        const COLS = 16, ROWS = 10, CELL = 100, ROAD_W = 28;
        const ROAD_COLS = [3, 8, 13], ROAD_ROWS = [2, 6];

        function colPx(c) { let x = 0; for (let i = 0; i < c; i++) { x += CELL; if (ROAD_COLS.includes(i)) x += ROAD_W; } return x; }
        function rowPx(r) { let y = 0; for (let i = 0; i < r; i++) { y += CELL; if (ROAD_ROWS.includes(i)) y += ROAD_W; } return y; }

        const W = colPx(COLS);   // 總寬
        const H = rowPx(ROWS);   // 總高

        // ── 地形 SVG ──
        function drawTerrain() {
            const svg = document.getElementById('terrainSvg');
            svg.setAttribute('width', W); svg.setAttribute('height', H);
            svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
            let s = '';

            // 草地底色
            s += `<rect width="${W}" height="${H}" fill="#8BAF66"/>`;

            // 橫向道路
            ROAD_ROWS.forEach(rr => {
                const y = rowPx(rr) + CELL;
                s += `<rect x="0" y="${y}" width="${W}" height="${ROAD_W}" fill="#D2BD85"/>`;
                s += `<rect x="0" y="${y + ROAD_W * 0.38}" width="${W}" height="${ROAD_W * 0.1}" fill="rgba(255,248,200,0.3)"/>`;
            });
            // 縱向道路
            ROAD_COLS.forEach(rc => {
                const x = colPx(rc) + CELL;
                s += `<rect x="${x}" y="0" width="${ROAD_W}" height="${H}" fill="#D2BD85"/>`;
                s += `<rect x="${x + ROAD_W * 0.38}" y="0" width="${ROAD_W * 0.1}" height="${H}" fill="rgba(255,248,200,0.3)"/>`;
            });
            // 路口
            ROAD_ROWS.forEach(rr => {
                ROAD_COLS.forEach(rc => {
                    s += `<rect x="${colPx(rc) + CELL}" y="${rowPx(rr) + CELL}" width="${ROAD_W}" height="${ROAD_W}" fill="#C8B472"/>`;
                });
            });

            // 河流（col 14-15）
            const rx = colPx(14), rw = W - rx;
            s += `<rect x="${rx}" y="0" width="${rw}" height="${H}" fill="#6AAEC8"/>`;
            s += `<rect x="${rx}" y="0" width="${ROAD_W * 0.4}" height="${H}" fill="#5EA0B8" opacity="0.35"/>`;
            // 水波
            for (let r = 0; r < ROWS; r++) {
                if (ROAD_ROWS.includes(r)) continue;
                const wy = rowPx(r) + CELL * 0.35;
                s += `<ellipse cx="${rx + rw * 0.35}" cy="${wy}" rx="${CELL * 0.22}" ry="${CELL * 0.055}" fill="white" opacity="0.18"/>`;
                s += `<ellipse cx="${rx + rw * 0.75}" cy="${wy + CELL * 0.45}" rx="${CELL * 0.16}" ry="${CELL * 0.045}" fill="white" opacity="0.13"/>`;
            }
            // 橋
            ROAD_ROWS.forEach(rr => {
                const by = rowPx(rr) + CELL;
                s += `<rect x="${rx}" y="${by}" width="${rw}" height="${ROAD_W}" fill="#C8A96A"/>`;
                s += `<rect x="${rx}" y="${by}" width="${rw}" height="${ROAD_W * 0.12}" fill="#B89050" opacity="0.7"/>`;
                s += `<rect x="${rx}" y="${by + ROAD_W * 0.88}" width="${rw}" height="${ROAD_W * 0.12}" fill="#B89050" opacity="0.7"/>`;
            });

            // 公園（col 0-2, row 0-1）
            const pkx = colPx(0), pky = rowPx(0);
            const pkw = colPx(3) - pkx, pkh = rowPx(2) - pky;  // 到 col3 道路前
            s += `<rect x="${pkx}" y="${pky}" width="${pkw}" height="${pkh}" fill="#6A9E4E" rx="8"/>`;
            s += `<ellipse cx="${pkx + pkw * 0.5}" cy="${pky + pkh * 0.5}" rx="${pkw * 0.28}" ry="${pkh * 0.24}" fill="#7ABCD8" opacity="0.7"/>`;
            s += `<ellipse cx="${pkx + pkw * 0.5}" cy="${pky + pkh * 0.5}" rx="${pkw * 0.19}" ry="${pkh * 0.16}" fill="#8ECFE8" opacity="0.5"/>`;
            s += `<path d="M${pkx + pkw * 0.12} ${pky + pkh * 0.94} Q${pkx + pkw * 0.35} ${pky + pkh * 0.5} ${pkx + pkw * 0.5} ${pky + pkh * 0.5} Q${pkx + pkw * 0.65} ${pky + pkh * 0.5} ${pkx + pkw * 0.88} ${pky + pkh * 0.94}" stroke="#D2BD85" stroke-width="3" fill="none" opacity="0.5"/>`;
            [[0.1, 0.12], [0.28, 0.06], [0.75, 0.08], [0.9, 0.15], [0.06, 0.7], [0.93, 0.65]].forEach(([fx, fy]) => {
                s += tree(pkx + pkw * fx, pky + pkh * fy, CELL * 0.1);
            });

            // 廣場（col 5, row 4）
            const plx = colPx(5) + CELL * 0.5, ply = rowPx(4) + CELL * 0.5;
            s += `<circle cx="${plx}" cy="${ply}" r="${CELL * 0.32}" fill="#B89A50" opacity="0.38"/>`;
            s += `<circle cx="${plx}" cy="${ply}" r="${CELL * 0.16}" fill="#D4AA5A" opacity="0.5"/>`;

            // 草地樹木（分布在草地格的角落）
            [
                [3.5, 0.4], [4.6, 0.3], [5.5, 0.5], [6.4, 0.4], [7.5, 0.3],
                [9.3, 0.4], [10.5, 0.5], [11.4, 0.3], [12.4, 0.4],
                [0.4, 2.5], [1.6, 3.4], [2.5, 2.6], [0.3, 3.6], [2.4, 3.5],
                [3.4, 2.5], [4.5, 3.4], [6.3, 2.6], [7.4, 3.5],
                [9.4, 2.4], [10.3, 3.6], [11.5, 2.5], [12.5, 3.4],
                [0.4, 7.4], [1.5, 7.6], [2.4, 7.3], [0.3, 8.5], [2.5, 8.4],
                [3.3, 7.5], [4.6, 8.4], [5.5, 7.3], [6.4, 8.5], [7.5, 7.4],
                [9.3, 7.5], [10.5, 8.3], [11.4, 7.6], [12.3, 8.4],
                [0.5, 4.5], [2.4, 5.6], [0.4, 5.5], [2.5, 4.4],
                [3.5, 4.4], [4.4, 5.5], [6.5, 4.5], [7.3, 5.4],
                [9.5, 4.3], [10.4, 5.6], [11.5, 4.4], [12.4, 5.5],
            ].forEach(([tc, tr]) => { s += tree(colPx(tc), rowPx(tr), CELL * 0.09); });

            // 花朵
            [
                [1.5, 2.02, '#F4D48E'], [5.5, 2.02, '#EFB0A4'], [10.5, 2.02, '#C18ACF'],
                [1.5, 6.02, '#F4D48E'], [5.5, 6.02, '#EFB0A4'], [10.5, 6.02, '#C18ACF'],
                [3.5, 0.02, '#F4D48E'], [9.5, 0.02, '#EFB0A4'],
            ].forEach(([fc, fr, col]) => {
                const fx = colPx(fc), fy = rowPx(fr), r2 = CELL * 0.055;
                s += `<circle cx="${fx}" cy="${fy}" r="${r2}" fill="${col}"/>`;
                [`${fx} ${fy - r2 * 1.6}`, `${fx} ${fy + r2 * 1.6}`, `${fx - r2 * 1.6} ${fy}`, `${fx + r2 * 1.6} ${fy}`].forEach(pt => {
                    const [ex, ey] = pt.split(' ');
                    const isV = pt.includes(fy - r2 * 1.6) || pt.includes(fy + r2 * 1.6);
                    s += `<ellipse cx="${ex}" cy="${ey}" rx="${isV ? r2 * 0.65 : r2 * 1.1}" ry="${isV ? r2 * 1.1 : r2 * 0.65}" fill="${col}" opacity="0.7"/>`;
                });
            });

            svg.innerHTML = s;
            // 地圖尺寸同步
            const wrap = document.getElementById('mapWrap');
            wrap.style.width = W + 'px'; wrap.style.height = H + 'px';
            svg.style.width = W + 'px'; svg.style.height = H + 'px';
            document.getElementById('gridLayer').style.width = W + 'px';
            document.getElementById('gridLayer').style.height = H + 'px';
        }

        function tree(cx, cy, r) {
            return `<ellipse cx="${cx}" cy="${cy}" rx="${r}" ry="${r * 0.85}" fill="#4A8038"/>
  <ellipse cx="${cx}" cy="${cy + r * 0.4}" rx="${r * 1.1}" ry="${r * 0.75}" fill="#5A9048"/>
  <ellipse cx="${cx}" cy="${cy + r * 0.75}" rx="${r * 0.85}" ry="${r * 0.6}" fill="#68A056"/>
  <rect x="${cx - r * 0.25}" y="${cy + r}" width="${r * 0.5}" height="${r * 0.85}" rx="2" fill="#7A5A28" opacity="0.8"/>`;
        }

        // ── 拖曳 ──
        const vp = document.getElementById('vp'), world = document.getElementById('world');
        let ox = 0, oy = 0, sx = 0, sy = 0, drag = false, raf = false, banH = 0;
        function clamp(v, a, b) { return Math.max(a, Math.min(b, v)); }
        function applyT() { world.style.transform = `translate(${ox}px,${oy}px)`; raf = false; }
        function clampOff() {
            ox = clamp(ox, Math.min(0, -(W - window.innerWidth)), 0);
            oy = clamp(oy, Math.min(0, -(H - (window.innerHeight - banH))), 0);
        }
        vp.addEventListener('mousedown', e => { if (e.target.closest('.panel')) return; drag = true; sx = e.clientX - ox; sy = e.clientY - oy; vp.classList.add('dragging'); });
        window.addEventListener('mousemove', e => { if (!drag) return; ox = e.clientX - sx; oy = e.clientY - sy; clampOff(); if (!raf) { raf = true; requestAnimationFrame(applyT); } });
        window.addEventListener('mouseup', () => { drag = false; vp.classList.remove('dragging'); });
        let tSx = 0, tSy = 0;
        vp.addEventListener('touchstart', e => { tSx = e.touches[0].clientX - ox; tSy = e.touches[0].clientY - oy; }, { passive: true });
        vp.addEventListener('touchmove', e => { ox = e.touches[0].clientX - tSx; oy = e.touches[0].clientY - tSy; clampOff(); if (!raf) { raf = true; requestAnimationFrame(applyT); } }, { passive: true });

        // ── 選位 ──
        let placingId = null;
        document.querySelectorAll('.unplaced-chip').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.unplaced-chip').forEach(b => b.classList.remove('active'));
                btn.classList.add('active'); placingId = btn.dataset.id;
                document.getElementById('placingHint').classList.add('show');
                document.getElementById('gridLayer').classList.add('show');
            });
        });
        function cancelPlacing() {
            placingId = null;
            document.getElementById('placingHint').classList.remove('show');
            document.getElementById('gridLayer').classList.remove('show');
            document.querySelectorAll('.unplaced-chip').forEach(b => b.classList.remove('active'));
        }
        function selectCell(cell) {
            if (!placingId) return;
            const gc = parseInt(cell.dataset.c), gr = parseInt(cell.dataset.r);
            fetch(`/buildings/${placingId}/place`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ grid_x: gc, grid_y: gr })
            }).then(r => r.json()).then(d => {
                if (d.success) { showToast('建築已放置！'); setTimeout(() => location.reload(), 900); }
                else showToast(d.error || '發生錯誤');
            }).catch(() => showToast('網路錯誤'));
        }

        // ── 資訊面板 ──
        function openPanel(name, type, lv, done, hasNext, nextName, nextSvg, need, pct) {
            document.getElementById('p-title').textContent = name + '・' + type;
            document.getElementById('p-sub').textContent = '目前建築：' + name + '（Lv.' + lv + '）';
            document.getElementById('p-done').textContent = done + ' 次';
            document.getElementById('p-lv').textContent = 'Lv.' + lv;
            document.getElementById('p-pct').textContent = pct + '%';
            document.getElementById('p-fill').style.width = pct + '%';
            const ns = document.getElementById('next-section'), pm = document.getElementById('p-max');
            if (hasNext) {
                ns.style.display = ''; pm.style.display = 'none';
                document.getElementById('p-next-img').src = '/svg/' + nextSvg;
                document.getElementById('p-next-name').textContent = nextName + '（Lv.' + (lv + 1) + '）';
                document.getElementById('p-next-req').textContent = '再完成 ' + need + ' 次任務可解鎖';
            } else { ns.style.display = 'none'; pm.style.display = 'block'; }
            document.getElementById('overlay').classList.add('active');
        }
        function closePanel() { document.getElementById('overlay').classList.remove('active'); }
        document.getElementById('overlay').addEventListener('click', e => { if (e.target === document.getElementById('overlay')) closePanel(); });
        function showToast(msg) { const t = document.getElementById('toast'); t.textContent = msg; t.classList.add('show'); setTimeout(() => t.classList.remove('show'), 2500); }

        // ── 初始化 ──
        function init() {
            banH = document.getElementById('bannerArea').offsetHeight;
            world.style.top = banH + 'px';
            drawTerrain();
            // 初始位置：地圖置中偏左上
            ox = Math.min(0, -(W - window.innerWidth) * 0.2);
            oy = 0; clampOff();
            world.style.transform = `translate(${ox}px,${oy}px)`;
        }
        window.addEventListener('resize', () => { banH = document.getElementById('bannerArea').offsetHeight; world.style.top = banH + 'px'; clampOff(); if (!raf) { raf = true; requestAnimationFrame(applyT); } });
        init();
    </script>
</body>

</html>
