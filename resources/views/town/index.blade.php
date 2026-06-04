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
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#F5EDD6;font-family:'Noto Serif TC',serif;min-height:100vh;}

/* ── 頂部提示橫幅 ── */
.unplaced-banner{
    background:#3D2B1F;color:#F5EDD6;
    padding:10px 20px;text-align:center;font-size:14px;
    display:flex;align-items:center;justify-content:center;gap:12px;
}
.unplaced-banner.hidden{display:none;}
.unplaced-list{display:flex;gap:8px;flex-wrap:wrap;justify-content:center;}
.unplaced-chip{
    background:#C9A84C;color:#3D2B1F;font-size:12px;font-weight:700;
    padding:4px 12px;border-radius:99px;cursor:pointer;
    border:none;font-family:'Noto Serif TC',serif;
    transition:transform 0.1s;
}
.unplaced-chip:hover{transform:scale(1.06);}
.unplaced-chip.active{background:#F4D48E;outline:2px solid #C9A84C;}

/* ── 選位模式說明 ── */
.placing-hint{
    background:#8B6914;color:#F5EDD6;
    padding:8px 20px;text-align:center;font-size:13px;
    display:none;
}
.placing-hint.show{display:block;}

/* ── Viewport ── */
.viewport{
    width:100%;height:calc(100vh - 44px);
    overflow:hidden;cursor:grab;position:relative;
    background:#8BAF66;
}
.viewport.dragging{cursor:grabbing;}
.viewport.placing-mode{cursor:crosshair;}
.world{position:absolute;width:1080px;height:820px;top:0;left:0;will-change:transform;}
.terrain-svg{position:absolute;inset:0;pointer-events:none;}
.objects-layer{position:absolute;inset:0;}

/* ── 格子選位覆蓋層 ── */
.grid-overlay{
    position:absolute;inset:0;
    display:grid;
    grid-template-columns:repeat(8,135px);
    grid-template-rows:repeat(8,102.5px);
    pointer-events:none;opacity:0;transition:opacity 0.2s;
}
.grid-overlay.show{pointer-events:all;opacity:1;}
.grid-cell{
    border:1.5px dashed rgba(201,168,76,0.0);
    transition:background 0.15s,border-color 0.15s;
    cursor:default;
}
.grid-cell.available{
    border-color:rgba(201,168,76,0.5);
    cursor:pointer;
}
.grid-cell.available:hover{
    background:rgba(201,168,76,0.25);
    border-color:rgba(201,168,76,0.9);
}
.grid-cell.occupied{
    border-color:rgba(61,43,31,0.2);
    cursor:not-allowed;
}

/* ── 建築 ── */
.building{
    position:absolute;cursor:pointer;
    display:flex;flex-direction:column;align-items:center;
    transition:transform 0.12s;
}
.building:hover{transform:translateY(-5px) scale(1.06);}
.building:hover .bld-tag{opacity:1;transform:translateY(0);}
.bld-tag{
    position:absolute;bottom:calc(100% + 7px);
    background:#3D2B1F;color:#F5EDD6;
    font-size:11px;font-weight:700;
    padding:3px 10px;border-radius:20px;white-space:nowrap;
    opacity:0;transform:translateY(4px);transition:all 0.13s;pointer-events:none;
}
.bld-tag::after{content:'';position:absolute;top:100%;left:50%;transform:translateX(-50%);border:5px solid transparent;border-top-color:#3D2B1F;}
.bld-img{width:90px;height:90px;object-fit:contain;image-rendering:pixelated;}

/* ── 資訊面板 ── */
.panel-overlay{
    position:absolute;inset:0;z-index:100;
    display:flex;align-items:center;justify-content:center;
    background:rgba(40,26,16,0.48);
    opacity:0;pointer-events:none;transition:opacity 0.2s;
}
.panel-overlay.active{opacity:1;pointer-events:all;}
.panel{
    background:#F5EDD6;border-radius:18px;border:2px solid #C9A84C;
    width:310px;padding:22px;position:relative;
    box-shadow:0 8px 32px rgba(40,26,16,0.3);
}
.panel-close{position:absolute;top:12px;right:14px;background:none;border:none;cursor:pointer;font-size:18px;color:#8B6914;}
.p-title{font-size:16px;font-weight:700;color:#3D2B1F;margin-bottom:3px;}
.p-sub{font-size:11px;color:#9A7230;letter-spacing:0.06em;margin-bottom:14px;}
.p-divider{height:1px;background:linear-gradient(90deg,transparent,#D4C089,transparent);margin:10px 0;}
.stat-row{display:flex;justify-content:space-between;margin-bottom:7px;}
.stat-label{font-size:12px;color:#6B4C35;}
.stat-val{font-size:13px;font-weight:700;color:#3D2B1F;}
.prog-meta{display:flex;justify-content:space-between;font-size:11px;color:#9A7230;margin-bottom:5px;margin-top:10px;}
.prog-bar{height:9px;background:#E8D9B0;border-radius:99px;overflow:hidden;}
.prog-fill{height:100%;background:#C9A84C;border-radius:99px;transition:width 0.5s;}
.next-label{font-size:11px;color:#9A7230;margin-bottom:8px;}
.next-bld{display:flex;align-items:center;gap:10px;background:rgba(180,178,169,0.2);border:1.5px dashed #C4B99A;border-radius:12px;padding:10px;opacity:0.6;}
.next-bld img{width:52px;height:52px;object-fit:contain;image-rendering:pixelated;filter:grayscale(1);}
.next-name{font-size:13px;font-weight:700;color:#7A6048;}
.next-req{font-size:11px;color:#9A7230;margin-top:2px;}
.next-badge{display:inline-block;margin-top:5px;background:#D3D1C7;color:#5F5E5A;font-size:10px;padding:2px 8px;border-radius:99px;}
.max-hint{font-size:12px;color:#C9A84C;text-align:center;margin-top:10px;font-weight:700;}

/* ── Toast ── */
.toast{
    position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
    background:#3D2B1F;color:#F5EDD6;
    font-size:13px;padding:10px 20px;border-radius:99px;
    z-index:999;opacity:0;transition:opacity 0.3s;pointer-events:none;
    font-family:'Noto Serif TC',serif;
}
.toast.show{opacity:1;}
</style>
</head>
<body>

{{-- 頂部：待選位置的建築提示 --}}
@if($unplaced->count() > 0)
<div class="unplaced-banner" id="unplacedBanner">
    <span>新建築等待放置：</span>
    <div class="unplaced-list">
        @foreach($unplaced as $b)
        <button class="unplaced-chip"
                data-id="{{ $b->id }}"
                data-name="{{ $b->name }}"
                data-type="{{ $b->type }}">
            {{ $b->name }}（{{ $b->type }}）
        </button>
        @endforeach
    </div>
</div>
@endif

{{-- 選位模式提示 --}}
<div class="placing-hint" id="placingHint">
    點擊地圖上的空格子來放置建築　<button onclick="cancelPlacing()" style="background:none;border:1px solid #F5EDD6;color:#F5EDD6;padding:2px 10px;border-radius:99px;cursor:pointer;font-family:'Noto Serif TC',serif;font-size:12px;">取消</button>
</div>

<div class="viewport" id="vp">
  <div class="world" id="world">

    {{-- 地形 SVG（與 mockup 相同）--}}
    <svg class="terrain-svg" viewBox="0 0 1080 820" width="1080" height="820" xmlns="http://www.w3.org/2000/svg">
      {{-- 貼上前一版的地形 SVG 內容 --}}
    </svg>

    <div class="objects-layer" id="objectsLayer">

      {{-- 格子選位覆蓋層 --}}
      <div class="grid-overlay" id="gridOverlay">
        @for($gy = 0; $gy < 8; $gy++)
          @for($gx = 0; $gx < 8; $gx++)
            @php
              $isOccupied = $buildings->where('grid_x', $gx)->where('grid_y', $gy)->count() > 0;
            @endphp
            <div class="grid-cell {{ $isOccupied ? 'occupied' : 'available' }}"
                 data-gx="{{ $gx }}" data-gy="{{ $gy }}"
                 {{ !$isOccupied ? 'onclick="selectCell(this)"' : '' }}>
            </div>
          @endfor
        @endfor
      </div>

      {{-- 已放置的建築 --}}
      @foreach($buildings->whereNotNull('grid_x') as $b)
      @php
        $px = $b->grid_x * 135 + 22;
        $py = $b->grid_y * 102.5 + 6;
        $upgradeMap = \App\Http\Controllers\BuildingController::UPGRADE_MAP;
        $levels = $upgradeMap[$b->type];
        $nextLevel = $b->level + 1;
        $hasNext = isset($levels[$nextLevel]);
        $curThreshold = match($b->level) { 1=>1, 2=>3, 3=>7, default=>0 };
        $nextThreshold = $hasNext ? match($nextLevel) { 1=>1, 2=>3, 3=>7, default=>0 } : $curThreshold;
        $pct = $hasNext ? min(100, round((($b->completed_count - $curThreshold) / ($nextThreshold - $curThreshold)) * 100)) : 100;
        $need = $hasNext ? max(0, $nextThreshold - $b->completed_count) : 0;
      @endphp
      <div class="building"
           style="left:{{ $px }}px;top:{{ $py }}px;"
           onclick="openPanel({{ $b->id }}, '{{ $b->name }}', '{{ $b->type }}', {{ $b->level }}, {{ $b->completed_count }}, {{ (int)$hasNext }}, '{{ $hasNext ? $levels[$nextLevel][0] : '' }}', '{{ $hasNext ? $levels[$nextLevel][1] : '' }}', {{ $need }}, {{ $pct }})">
        <div class="bld-tag">{{ $b->name }}・{{ $b->type }}</div>
        <img class="bld-img" src="/svg/{{ $b->svg_file }}" alt="{{ $b->name }}">
      </div>
      @endforeach

    </div>
  </div>

  {{-- 資訊面板 --}}
  <div class="panel-overlay" id="overlay">
    <div class="panel">
      <button class="panel-close" onclick="closePanel()">✕</button>
      <div class="p-title" id="p-title"></div>
      <div class="p-sub" id="p-sub"></div>
      <div class="p-divider"></div>
      <div class="stat-row"><span class="stat-label">已完成任務</span><span class="stat-val" id="p-done"></span></div>
      <div class="stat-row"><span class="stat-label">目前等級</span><span class="stat-val" id="p-lv"></span></div>
      <div class="prog-meta"><span>升級進度</span><span id="p-pct"></span></div>
      <div class="prog-bar"><div class="prog-fill" id="p-fill"></div></div>
      <div class="p-divider"></div>
      <div id="next-section">
        <div class="next-label">下一棟建築（解鎖條件）</div>
        <div class="next-bld">
          <img id="p-next-img" src="" alt="">
          <div>
            <div class="next-name" id="p-next-name"></div>
            <div class="next-req" id="p-next-req"></div>
            <span class="next-badge">尚未解鎖</span>
          </div>
        </div>
      </div>
      <div class="max-hint" id="p-max" style="display:none">🌟 已達最高等級，城鎮之星！</div>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
// ── 拖曳地圖 ────────────────────────────────
const vp = document.getElementById('vp');
const world = document.getElementById('world');
let ox=0,oy=0,sx=0,sy=0,drag=false,moved=false;
const WW=1080,WH=820;

function clamp(v,mn,mx){return Math.max(mn,Math.min(mx,v));}

vp.addEventListener('mousedown',e=>{
    if(e.target.closest('.panel')||e.target.closest('.grid-cell.available'))return;
    drag=true;moved=false;sx=e.clientX-ox;sy=e.clientY-oy;
    vp.classList.add('dragging');
});
window.addEventListener('mousemove',e=>{
    if(!drag)return;
    moved=true;
    ox=clamp(e.clientX-sx,-(WW-vp.offsetWidth),0);
    oy=clamp(e.clientY-sy,-(WH-vp.offsetHeight),0);
    world.style.transform=`translate(${ox}px,${oy}px)`;
});
window.addEventListener('mouseup',()=>{drag=false;vp.classList.remove('dragging');});

// ── 選位模式 ────────────────────────────────
let placingId = null;

document.querySelectorAll('.unplaced-chip').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.unplaced-chip').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        placingId = btn.dataset.id;
        document.getElementById('gridOverlay').classList.add('show');
        document.getElementById('placingHint').classList.add('show');
        vp.classList.add('placing-mode');
    });
});

function cancelPlacing(){
    placingId = null;
    document.getElementById('gridOverlay').classList.remove('show');
    document.getElementById('placingHint').classList.remove('show');
    vp.classList.remove('placing-mode');
    document.querySelectorAll('.unplaced-chip').forEach(b=>b.classList.remove('active'));
}

function selectCell(cell){
    if(!placingId) return;
    const gx = cell.dataset.gx;
    const gy = cell.dataset.gy;

    fetch(`/buildings/${placingId}/place`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ grid_x: parseInt(gx), grid_y: parseInt(gy) })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success){
            showToast('建築已放置！頁面重新整理中…');
            setTimeout(()=>location.reload(), 1000);
        } else {
            showToast(data.error || '發生錯誤，請再試一次');
        }
    })
    .catch(()=>showToast('網路錯誤，請再試一次'));
}

// ── 資訊面板 ────────────────────────────────
function openPanel(id,name,type,lv,done,hasNext,nextName,nextSvg,need,pct){
    document.getElementById('p-title').textContent = name + '・' + type;
    document.getElementById('p-sub').textContent   = '目前等級：Lv.' + lv;
    document.getElementById('p-done').textContent  = done + ' 次';
    document.getElementById('p-lv').textContent    = 'Lv.' + lv;
    document.getElementById('p-pct').textContent   = pct + '%';
    document.getElementById('p-fill').style.width  = pct + '%';

    if(hasNext){
        document.getElementById('next-section').style.display = '';
        document.getElementById('p-max').style.display = 'none';
        document.getElementById('p-next-img').src  = '/svg/' + nextSvg;
        document.getElementById('p-next-name').textContent = nextName + '（Lv.' + (lv+1) + '）';
        document.getElementById('p-next-req').textContent  = '再完成 ' + need + ' 次任務可解鎖';
    } else {
        document.getElementById('next-section').style.display = 'none';
        document.getElementById('p-max').style.display = 'block';
    }
    document.getElementById('overlay').classList.add('active');
}

function closePanel(){
    document.getElementById('overlay').classList.remove('active');
}
document.getElementById('overlay').addEventListener('click',e=>{
    if(e.target===document.getElementById('overlay')) closePanel();
});

// ── Toast ────────────────────────────────────
function showToast(msg){
    const t=document.getElementById('toast');
    t.textContent=msg;t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),2500);
}
</script>
</body>
</html>