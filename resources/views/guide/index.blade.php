{{-- resources/views/guide/index.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TimeTown｜圖鑑與說明</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;700&display=swap');
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#F5EDD6;font-family:'Noto Serif TC',serif;min-height:100vh;}

.header{background:#3D2B1F;padding:14px 28px;display:flex;align-items:center;justify-content:space-between;}
.header-title{color:#F5EDD6;font-size:17px;font-weight:700;letter-spacing:0.08em;}
.header-deco{display:flex;align-items:center;gap:8px;}
.deco-line{width:60px;height:1px;background:linear-gradient(90deg,transparent,#C9A84C);}
.deco-diamond{width:6px;height:6px;background:#C9A84C;transform:rotate(45deg);}

.main{max-width:960px;margin:0 auto;padding:28px 20px 110px;}

/* Tab 切換 */
.tabs{display:flex;gap:0;margin-bottom:28px;border:1.5px solid #D4C089;border-radius:12px;overflow:hidden;}
.tab-btn{
    flex:1;padding:10px 0;text-align:center;font-size:13px;font-weight:700;
    font-family:'Noto Serif TC',serif;cursor:pointer;border:none;
    background:#FDF7EC;color:#9A7230;transition:background 0.15s,color 0.15s;
}
.tab-btn.active{background:#C9A84C;color:#3D2B1F;}
.tab-btn:not(:last-child){border-right:1.5px solid #D4C089;}

.tab-content{display:none;}
.tab-content.active{display:block;}

/* ── 說明頁 ── */
.about-hero{
    background:#3D2B1F;border-radius:16px;padding:32px;margin-bottom:24px;
    text-align:center;
}
.about-logo{font-size:48px;margin-bottom:12px;}
.about-title{color:#C9A84C;font-size:22px;font-weight:700;margin-bottom:8px;}
.about-subtitle{color:rgba(245,237,214,0.7);font-size:13px;line-height:1.8;}

.flow-section{margin-bottom:28px;}
.section-header{display:flex;align-items:center;gap:10px;margin-bottom:16px;}
.section-line{flex:1;height:1px;background:linear-gradient(90deg,#D4C089,transparent);}
.section-label{font-size:12px;font-weight:700;color:#8B6914;letter-spacing:0.08em;white-space:nowrap;}

.flow-steps{display:flex;flex-direction:column;gap:12px;}
.flow-step{
    background:#FDF7EC;border:1.5px solid #D4C089;border-radius:14px;
    padding:16px 20px;display:flex;align-items:flex-start;gap:16px;
}
.step-num{
    width:32px;height:32px;border-radius:50%;
    background:#C9A84C;color:#3D2B1F;
    font-size:14px;font-weight:700;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
}
.step-body{}
.step-title{font-size:14px;font-weight:700;color:#3D2B1F;margin-bottom:4px;}
.step-desc{font-size:12px;color:#6B4C35;line-height:1.75;}

.upgrade-intro{
    background:#FDF7EC;border:1.5px solid #D4C089;border-radius:14px;
    padding:18px 20px;margin-bottom:12px;
}
.upgrade-intro-title{font-size:13px;font-weight:700;color:#8B6914;margin-bottom:10px;}
.threshold-row{display:flex;gap:8px;flex-wrap:wrap;}
.threshold-chip{
    background:#FAEEDA;border:1px solid #C9A84C;border-radius:8px;
    padding:5px 12px;font-size:12px;color:#854F0B;
}
.threshold-chip strong{color:#3D2B1F;}

.multi-building-box{
    background:#FDF7EC;border:1.5px solid #D4C089;border-radius:14px;
    padding:18px 20px;
}
.mb-title{font-size:13px;font-weight:700;color:#8B6914;margin-bottom:10px;}
.mb-row{display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:12px;color:#6B4C35;}
.mb-arrow{color:#C9A84C;font-weight:700;}

/* ── 圖鑑頁 ── */
.type-filter{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:20px;}
.filter-btn{
    padding:5px 14px;border-radius:99px;font-size:12px;font-weight:700;
    border:1.5px solid #D4C089;background:#FDF7EC;color:#8B6914;
    cursor:pointer;font-family:'Noto Serif TC',serif;transition:all 0.15s;
}
.filter-btn.active,
.filter-btn:hover{background:#C9A84C;color:#3D2B1F;border-color:#C9A84C;}

.type-section{margin-bottom:28px;}
.type-header{
    display:flex;align-items:center;gap:10px;margin-bottom:12px;
    padding-bottom:8px;border-bottom:1.5px solid #D4C089;
}
.type-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
.type-name{font-size:14px;font-weight:700;color:#3D2B1F;}
.type-progress{font-size:11px;color:#9A7230;margin-left:auto;}

.building-chain{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.chain-item{
    display:flex;flex-direction:column;align-items:center;
    background:#FDF7EC;border:1.5px solid #D4C089;border-radius:14px;
    padding:14px 12px;width:130px;position:relative;transition:border-color 0.15s;
}
.chain-item.unlocked{border-color:#C9A84C;}
.chain-item.unlocked .chain-img{filter:none;}
.chain-item .chain-img{
    width:72px;height:72px;object-fit:contain;image-rendering:pixelated;
    filter:grayscale(1) opacity(0.4);transition:filter 0.2s;
}
.chain-name{font-size:12px;font-weight:700;color:#3D2B1F;margin-top:8px;text-align:center;}
.chain-lv{font-size:10px;color:#9A7230;margin-top:2px;}
.chain-req{font-size:10px;color:#C9A84C;margin-top:4px;text-align:center;}
.chain-lock{
    position:absolute;top:8px;right:8px;
    font-size:14px;opacity:0.4;
}
.chain-item.unlocked .chain-lock{display:none;}
.chain-item.current{
    border-color:#C9A84C;
    box-shadow:0 0 0 3px rgba(201,168,76,0.25);
}
.chain-item.current::after{
    content:'目前';position:absolute;top:-10px;left:50%;transform:translateX(-50%);
    background:#C9A84C;color:#3D2B1F;font-size:9px;font-weight:700;
    padding:2px 8px;border-radius:99px;white-space:nowrap;
}
.chain-arrow{color:#C9A84C;font-size:18px;font-weight:700;flex-shrink:0;}

.unlock-badge{
    position:absolute;top:8px;left:8px;
    background:#6A9E4E;color:white;font-size:9px;font-weight:700;
    padding:2px 6px;border-radius:99px;
}

.stats-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;}
.stat-card{
    flex:1;min-width:100px;
    background:#FDF7EC;border:1.5px solid #D4C089;border-radius:12px;
    padding:12px 14px;text-align:center;
}
.stat-num{font-size:22px;font-weight:700;color:#3D2B1F;}
.stat-label{font-size:11px;color:#9A7230;margin-top:3px;}

/* 導覽 */
.nav{position:fixed;bottom:18px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:80;}
.nav-btn{
    background:rgba(61,43,31,0.88);color:#F5EDD6;border:1px solid rgba(201,168,76,0.5);
    padding:8px 14px;border-radius:99px;font-family:'Noto Serif TC',serif;font-size:12px;
    cursor:pointer;text-decoration:none;transition:background 0.15s;
}
.nav-btn:hover{background:#3D2B1F;}
.nav-btn.active{background:#C9A84C;color:#3D2B1F;border-color:#C9A84C;}
</style>
</head>
<body>
<div class="header">
    <div class="header-title">TimeTown｜圖鑑與說明</div>
    <div class="header-deco">
        <div class="deco-line"></div>
        <div class="deco-diamond"></div>
    </div>
</div>

<div class="main">
    {{-- Tab 切換 --}}
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('about')">關於 TimeTown</button>
        <button class="tab-btn" onclick="switchTab('guide')">建築圖鑑</button>
    </div>

    {{-- ══ 關於頁 ══ --}}
    <div class="tab-content active" id="tab-about">
        <div class="about-hero">
            <div class="about-logo">🏘️</div>
            <div class="about-title">TimeTown</div>
            <div class="about-subtitle">
                把你的每一天，蓋成一座城鎮<br>
                每完成一個任務，城鎮就多一棟建築<br>
                堅持的時間越久，城鎮就越繁榮
            </div>
        </div>

        <div class="flow-section">
            <div class="section-header">
                <span class="section-label">整體運作流程</span>
                <div class="section-line"></div>
            </div>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">新增任務</div>
                        <div class="step-desc">在行事曆頁輸入任務名稱、選擇類型（學習、工作、運動、社交、休息、興趣創作）和日期。新增任務後，對應類別的第一棟基礎房子會自動出現在「待放置」清單中。</div>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">放置建築</div>
                        <div class="step-desc">回到城鎮頁，頂部會出現「新建築等待放置」的提示。點擊建築名稱後，地圖上的草地格子會亮起，點選喜歡的位置即可放置。放置後無法移動，請慎重選擇！</div>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">完成任務，建築升級</div>
                        <div class="step-desc">在行事曆按下「完成」，對應類別的建築會自動累積完成次數並升級外觀。每種類別的建築都有 4 個等級，從基礎房子一路升級到最高等級建築。</div>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-num">4</div>
                    <div class="step-body">
                        <div class="step-title">城鎮持續成長</div>
                        <div class="step-desc">當同一類別的建築升到最高等級（Lv.3）後，繼續新增該類別的任務，就會再出現一棟新的基礎房子。每個類別最多可以擁有 3 棟建築，六個類別共最多 18 棟。</div>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-num">5</div>
                    <div class="step-body">
                        <div class="step-title">週回顧</div>
                        <div class="step-desc">每週結束後，在回顧頁查看本週的任務統計、類型分析圖表、建築升級紀錄，以及城鎮的即時快照，回顧自己這週的成長軌跡。</div>
                    </div>
                </div>
                <div class="flow-step">
                    <div class="step-num">6</div>
                    <div class="step-body">
                        <div class="step-title">AI 週故事生成</div>
                        <div class="step-desc">在故事頁，AI 會根據你這週完成的任務，為你的城鎮主人翁撰寫一篇生活故事。故事之間具有連貫性，上週的結尾會影響這週的開頭，讓你的城鎮故事像一本連載小說。</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flow-section">
            <div class="section-header">
                <span class="section-label">建築升級規則</span>
                <div class="section-line"></div>
            </div>
            <div class="upgrade-intro">
                <div class="upgrade-intro-title">每棟建築各自累積完成次數，達到門檻自動升級</div>
                <div class="threshold-row">
                    <div class="threshold-chip">完成 <strong>1 次</strong> → Lv.1</div>
                    <div class="threshold-chip">完成 <strong>2 次</strong> → Lv.2</div>
                    <div class="threshold-chip">完成 <strong>4 次</strong> → Lv.3（MAX）</div>
                </div>
            </div>
            <div class="multi-building-box">
                <div class="mb-title">同類別多棟觸發條件</div>
                <div class="mb-row">🏠 <span>新增第一個任務</span> <span class="mb-arrow">→</span> <span>第 1 棟基礎房子出現</span></div>
                <div class="mb-row">🏆 <span>第 1 棟升到 Lv.3（MAX）後，繼續新增或完成任務</span> <span class="mb-arrow">→</span> <span>第 2 棟出現</span></div>
                <div class="mb-row">🏆 <span>第 2 棟升到 Lv.3（MAX）後，繼續新增或完成任務</span> <span class="mb-arrow">→</span> <span>第 3 棟出現</span></div>
                <div class="mb-row" style="margin-top:8px;color:#9A7230;">每個類別最多 3 棟，六種類別共最多 18 棟建築</div>
            </div>
        </div>
    </div>

    {{-- ══ 圖鑑頁 ══ --}}
    <div class="tab-content" id="tab-guide">
        @php
            $upgradeMap = \App\Http\Controllers\BuildingController::UPGRADE_MAP;
            $svgMap     = \App\Http\Controllers\BuildingController::SVG_MAP;
            $thresholds = [0=>0, 1=>1, 2=>2, 3=>4];
            $typeColors = [
                '學習'     => '#7EB8D4',
                '工作'     => '#B87BCF',
                '運動'     => '#6C9A54',
                '社交'     => '#D88A6A',
                '休息'     => '#C9A84C',
                '興趣創作' => '#EFB0A4',
            ];
            // 使用者目前的建築狀態
            $userBuildings = $buildings ?? collect();
            // 統計
            $totalUnlocked = 0;
            $totalMax = 0;
            foreach($upgradeMap as $type => $levels){
                $typeBlds = $userBuildings->where('type', $type);
                foreach($typeBlds as $b){
                    for($lv=0; $lv<=$b->level; $lv++) $totalUnlocked++;
                    if($b->level >= 3) $totalMax++;
                }
            }
        @endphp

        {{-- 統計 --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-num">{{ $totalUnlocked }}</div>
                <div class="stat-label">已解鎖建築</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ $totalMax }}</div>
                <div class="stat-label">已達最高等級</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ $userBuildings->count() }}</div>
                <div class="stat-label">城鎮建築棟數</div>
            </div>
            <div class="stat-card">
                <div class="stat-num">{{ 24 - $userBuildings->count() }}</div>
                <div class="stat-label">剩餘建設空間</div>
            </div>
        </div>

        {{-- 類型篩選 --}}
        <div class="type-filter">
            <button class="filter-btn active" onclick="filterType('all', this)">全部</button>
            @foreach(array_keys($upgradeMap) as $type)
            <button class="filter-btn" onclick="filterType('{{ $type }}', this)">{{ $type }}</button>
            @endforeach
        </div>

        {{-- 各類別圖鑑 --}}
        @foreach($upgradeMap as $type => $levels)
        @php
            $typeBlds    = $userBuildings->where('type', $type)->sortBy('slot');
            $maxLevel    = $typeBlds->max('level') ?? -1;
            $hasAny      = $typeBlds->isNotEmpty();
            $color       = $typeColors[$type];
            // 這個類別目前最高解鎖到哪個等級
            $unlockedLv  = $hasAny ? $maxLevel : -1;
        @endphp
        <div class="type-section" data-type="{{ $type }}">
            <div class="type-header">
                <div class="type-dot" style="background:{{ $color }}"></div>
                <div class="type-name">{{ $type }}</div>
                <div class="type-progress">
                    @if(!$hasAny)
                        尚未解鎖
                    @elseif($maxLevel >= 3)
                        已達最高等級 🌟
                    @else
                        Lv.{{ $maxLevel }} · 還差 {{ $thresholds[$maxLevel+1] - $typeBlds->first()->completed_count }} 次升級
                    @endif
                </div>
            </div>
            <div class="building-chain">
                @foreach($levels as $lv => $name)
                @php
                    $isUnlocked = $hasAny && $unlockedLv >= $lv;
                    $isCurrent  = $hasAny && $unlockedLv === $lv;
                    $req        = $lv === 0 ? '新增任務即解鎖' : "完成 {$thresholds[$lv]} 次任務";
                @endphp
                <div class="chain-item {{ $isUnlocked ? 'unlocked' : '' }} {{ $isCurrent ? 'current' : '' }}">
                    @if($isUnlocked)
                        <span class="unlock-badge">✓</span>
                    @else
                        <span class="chain-lock">🔒</span>
                    @endif
                    <img class="chain-img" src="/svg/{{ $svgMap[$type][$lv] }}" alt="{{ $name }}">
                    <div class="chain-name">{{ $name }}</div>
                    <div class="chain-lv">Lv.{{ $lv }}</div>
                    <div class="chain-req">{{ $req }}</div>
                </div>
                @if($lv < 3)
                    <div class="chain-arrow">→</div>
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<nav class="nav">
    <a href="{{ url('/') }}"         class="nav-btn">城鎮</a>
    <a href="{{ url('/calendar') }}" class="nav-btn">行事曆</a>
    <a href="{{ url('/review') }}"   class="nav-btn">回顧</a>
    <a href="{{ url('/stories') }}"  class="nav-btn">故事</a>
    <a href="{{ url('/guide') }}"    class="nav-btn active">圖鑑</a>
</nav>

<script>
function switchTab(tab){
    document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('tab-'+tab).classList.add('active');
    event.target.classList.add('active');
}
function filterType(type, btn){
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.type-section').forEach(s=>{
        s.style.display = (type==='all' || s.dataset.type===type) ? '' : 'none';
    });
}
</script>
</body>
</html>