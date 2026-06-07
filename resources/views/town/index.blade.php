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
body{background:#F5EDD6;font-family:'Noto Serif TC',serif;min-height:100vh;overflow:hidden;}

/* ── 頂部提示橫幅 ── */
.unplaced-banner{
    background:#3D2B1F;color:#F5EDD6;
    padding:10px 20px;
    display:flex;align-items:center;justify-content:center;gap:12px;
    flex-wrap:wrap;font-size:14px;
}
.unplaced-list{display:flex;gap:8px;flex-wrap:wrap;justify-content:center;}
.unplaced-chip{
    background:#C9A84C;color:#3D2B1F;font-size:12px;font-weight:700;
    padding:5px 14px;border-radius:99px;cursor:pointer;
    border:2px solid transparent;font-family:'Noto Serif TC',serif;
    transition:transform 0.1s,border-color 0.1s;
}
.unplaced-chip:hover{transform:scale(1.06);}
.unplaced-chip.active{border-color:#F4D48E;}

/* ── 選位模式說明 ── */
.placing-hint{
    background:#8B6914;color:#F5EDD6;
    padding:7px 20px;text-align:center;font-size:13px;
    display:none;align-items:center;justify-content:center;gap:12px;
}
.placing-hint.show{display:flex;}
.cancel-btn{
    background:none;border:1px solid rgba(245,237,214,0.6);color:#F5EDD6;
    padding:3px 12px;border-radius:99px;cursor:pointer;
    font-family:'Noto Serif TC',serif;font-size:12px;
}
.cancel-btn:hover{background:rgba(245,237,214,0.15);}

/* ── Viewport ── */
.banner-area{flex-shrink:0;}
.viewport{
    width:100%;
    height:calc(100vh - var(--banner-h, 0px));
    overflow:hidden;cursor:grab;position:relative;
    background:#8BAF66;
}
.viewport.dragging{cursor:grabbing;}
.viewport.placing-mode{cursor:crosshair;}
.world{position:absolute;width:1080px;height:820px;top:0;left:0;will-change:transform;}
.terrain-svg{position:absolute;inset:0;pointer-events:none;}
.objects-layer{position:absolute;inset:0;}

/* ── 格子選位覆蓋層（8×8，每格 135×102.5px）── */
.grid-overlay{
    position:absolute;inset:0;
    display:grid;
    grid-template-columns:repeat(8,135px);
    grid-template-rows:repeat(8,102.5px);
    pointer-events:none;opacity:0;transition:opacity 0.2s;
    z-index:20;
}
.grid-overlay.show{pointer-events:all;opacity:1;}
.grid-cell{border:1.5px dashed rgba(201,168,76,0.0);transition:background 0.15s,border-color 0.15s;}
.grid-cell.available{border-color:rgba(201,168,76,0.5);cursor:pointer;}
.grid-cell.available:hover{background:rgba(201,168,76,0.28);border-color:rgba(201,168,76,0.95);}
.grid-cell.occupied{border-color:rgba(61,43,31,0.18);cursor:not-allowed;background:rgba(61,43,31,0.06);}

/* ── 建築 ── */
.building{
    position:absolute;cursor:pointer;
    display:flex;flex-direction:column;align-items:center;
    transition:transform 0.12s;z-index:10;
}
.building:hover{transform:translateY(-5px) scale(1.06);}
.building:hover .bld-tag{opacity:1;transform:translateY(0);}
.bld-tag{
    position:absolute;bottom:calc(100% + 7px);
    background:#3D2B1F;color:#F5EDD6;
    font-size:11px;font-weight:700;
    padding:3px 10px;border-radius:20px;white-space:nowrap;
    opacity:0;transform:translateY(4px);transition:all 0.13s;pointer-events:none;
    box-shadow:0 2px 8px rgba(0,0,0,0.3);
}
.bld-tag::after{
    content:'';position:absolute;top:100%;left:50%;transform:translateX(-50%);
    border:5px solid transparent;border-top-color:#3D2B1F;
}
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
.panel-close{
    position:absolute;top:12px;right:14px;
    background:none;border:none;cursor:pointer;
    font-size:18px;color:#8B6914;line-height:1;
}
.panel-close:hover{color:#3D2B1F;}
.p-title{font-size:16px;font-weight:700;color:#3D2B1F;margin-bottom:3px;}
.p-sub{font-size:11px;color:#9A7230;letter-spacing:0.06em;margin-bottom:14px;}
.p-divider{height:1px;background:linear-gradient(90deg,transparent,#D4C089,transparent);margin:10px 0;}
.stat-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;}
.stat-label{font-size:12px;color:#6B4C35;}
.stat-val{font-size:13px;font-weight:700;color:#3D2B1F;}
.prog-meta{display:flex;justify-content:space-between;font-size:11px;color:#9A7230;margin-bottom:5px;margin-top:10px;}
.prog-bar{height:9px;background:#E8D9B0;border-radius:99px;overflow:hidden;}
.prog-fill{height:100%;background:#C9A84C;border-radius:99px;transition:width 0.5s;}
.next-label{font-size:11px;color:#9A7230;margin:12px 0 8px;}
.next-bld{
    display:flex;align-items:center;gap:10px;
    background:rgba(180,178,169,0.2);
    border:1.5px dashed #C4B99A;border-radius:12px;padding:10px;
    opacity:0.6;
}
.next-bld-img{width:52px;height:52px;object-fit:contain;image-rendering:pixelated;filter:grayscale(1) opacity(0.7);}
.next-name{font-size:13px;font-weight:700;color:#7A6048;}
.next-req{font-size:11px;color:#9A7230;margin-top:2px;}
.next-badge{
    display:inline-block;margin-top:5px;
    background:#D3D1C7;color:#5F5E5A;
    font-size:10px;padding:2px 8px;border-radius:99px;
}
.max-hint{font-size:12px;color:#C9A84C;text-align:center;margin-top:12px;font-weight:700;}

/* ── Toast ── */
.toast{
    position:absolute;bottom:24px;left:50%;transform:translateX(-50%);
    background:#3D2B1F;color:#F5EDD6;
    font-size:13px;padding:10px 20px;border-radius:99px;
    z-index:999;opacity:0;transition:opacity 0.3s;pointer-events:none;
    font-family:'Noto Serif TC',serif;white-space:nowrap;
}
.toast.show{opacity:1;}

/* ── 導覽列 ── */
.nav{
    position:absolute;bottom:20px;left:50%;transform:translateX(-50%);
    display:flex;gap:10px;z-index:50;
}
.nav-btn{
    background:rgba(61,43,31,0.85);color:#F5EDD6;
    border:1px solid rgba(201,168,76,0.5);
    padding:8px 20px;border-radius:99px;
    font-family:'Noto Serif TC',serif;font-size:13px;
    cursor:pointer;text-decoration:none;
    transition:background 0.15s;
}
.nav-btn:hover{background:#3D2B1F;}
.nav-btn.active{background:#C9A84C;color:#3D2B1F;border-color:#C9A84C;}
</style>
</head>
<body>

<div class="banner-area" id="bannerArea">
    @if(isset($unplaced) && $unplaced->count() > 0)
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

    <div class="placing-hint" id="placingHint">
        <span>點擊空格子放置建築</span>
        <button class="cancel-btn" onclick="cancelPlacing()">取消</button>
    </div>
</div>

<div class="viewport" id="vp">
  <div class="world" id="world">

    {{-- ══ 地形 SVG ══ --}}
    <svg class="terrain-svg" viewBox="0 0 1080 820" width="1080" height="820" xmlns="http://www.w3.org/2000/svg">
      <rect width="1080" height="820" fill="#8BAF66"/>
      <rect x="0" y="0" width="340" height="280" fill="#82A85E" opacity="0.6"/>
      <rect x="700" y="500" width="380" height="320" fill="#7FA05A" opacity="0.5"/>
      <rect x="0" y="550" width="300" height="270" fill="#85AB62" opacity="0.4"/>
      <rect x="450" y="0" width="260" height="200" fill="#93B86E" opacity="0.4"/>
      <!-- 道路 -->
      <rect x="0" y="350" width="1080" height="40" fill="#D2BD85"/>
      <rect x="0" y="368" width="1080" height="4" fill="rgba(255,248,200,0.28)"/>
      <rect x="370" y="0" width="40" height="820" fill="#D2BD85"/>
      <rect x="388" y="0" width="4" height="820" fill="rgba(255,248,200,0.28)"/>
      <rect x="740" y="0" width="36" height="820" fill="#CDB87E"/>
      <rect x="757" y="0" width="3" height="820" fill="rgba(255,248,200,0.22)"/>
      <rect x="0" y="162" width="740" height="32" fill="#CDB87E"/>
      <rect x="0" y="177" width="740" height="3" fill="rgba(255,248,200,0.22)"/>
      <rect x="370" y="582" width="710" height="32" fill="#CDB87E"/>
      <rect x="370" y="597" width="710" height="3" fill="rgba(255,248,200,0.22)"/>
      <!-- 路口廣場 -->
      <rect x="370" y="350" width="40" height="40" fill="#C8B472"/>
      <rect x="740" y="350" width="36" height="40" fill="#C8B472"/>
      <rect x="370" y="162" width="40" height="32" fill="#C8B472"/>
      <rect x="740" y="162" width="36" height="32" fill="#C8B472"/>
      <rect x="370" y="582" width="40" height="32" fill="#C8B472"/>
      <rect x="740" y="582" width="36" height="32" fill="#C8B472"/>
      <!-- 河流：右側垂直向下 L 形 -->
      <rect x="855" y="0" width="52" height="600" fill="#6AAEC8"/>
      <rect x="855" y="560" width="225" height="52" fill="#6AAEC8"/>
      <rect x="848" y="0" width="8" height="600" fill="#5EA0B8" opacity="0.45"/>
      <rect x="905" y="0" width="8" height="600" fill="#5EA0B8" opacity="0.4"/>
      <rect x="855" y="553" width="225" height="8" fill="#5EA0B8" opacity="0.45"/>
      <rect x="855" y="610" width="225" height="8" fill="#5EA0B8" opacity="0.4"/>
      <ellipse cx="881" cy="80"  rx="12" ry="4" fill="white" opacity="0.15"/>
      <ellipse cx="881" cy="200" rx="16" ry="5" fill="white" opacity="0.12"/>
      <ellipse cx="881" cy="340" rx="10" ry="3" fill="white" opacity="0.13"/>
      <ellipse cx="881" cy="470" rx="14" ry="4" fill="white" opacity="0.11"/>
      <ellipse cx="960" cy="586" rx="14" ry="4" fill="white" opacity="0.13"/>
      <ellipse cx="1040" cy="586" rx="10" ry="3" fill="white" opacity="0.11"/>
      <!-- 橋（y=350 橫向） -->
      <rect x="855" y="350" width="52" height="40" fill="#C8A96A"/>
      <rect x="857" y="352" width="4" height="36" fill="rgba(180,140,60,0.5)"/>
      <rect x="901" y="352" width="4" height="36" fill="rgba(180,140,60,0.5)"/>
      <rect x="855" y="350" width="52" height="4" fill="#B89050" opacity="0.7"/>
      <rect x="855" y="386" width="52" height="4" fill="#B89050" opacity="0.7"/>
      <!-- 橋（y=582 橫向） -->
      <rect x="855" y="582" width="52" height="32" fill="#C8A96A"/>
      <rect x="855" y="582" width="52" height="3" fill="#B89050" opacity="0.7"/>
      <rect x="855" y="611" width="52" height="3" fill="#B89050" opacity="0.7"/>
      <!-- 公園區（左上） -->
      <rect x="20" y="20" width="330" height="118" fill="#6A9E4E" rx="14"/>
      <ellipse cx="180" cy="75" rx="52" ry="28" fill="#7ABCD8" opacity="0.7"/>
      <ellipse cx="180" cy="75" rx="38" ry="18" fill="#8ECFE8" opacity="0.5"/>
      <path d="M80 138 Q130 75 180 75 Q230 75 280 138" stroke="#D2BD85" stroke-width="5" fill="none" opacity="0.45"/>
      <!-- 小廣場（中） -->
      <rect x="420" y="198" width="140" height="140" fill="#C8B472" rx="8" opacity="0.5"/>
      <circle cx="490" cy="268" r="20" fill="#B89A50" opacity="0.35"/>
      <circle cx="490" cy="268" r="10" fill="#D4AA5A" opacity="0.45"/>
      <!-- 圍欄 -->
      <g stroke="#A07840" stroke-width="2" opacity="0.55">
        <line x1="20" y1="140" x2="350" y2="140"/>
        <line x1="30"  y1="138" x2="30"  y2="145"/><line x1="60"  y1="138" x2="60"  y2="145"/>
        <line x1="90"  y1="138" x2="90"  y2="145"/><line x1="120" y1="138" x2="120" y2="145"/>
        <line x1="150" y1="138" x2="150" y2="145"/><line x1="180" y1="138" x2="180" y2="145"/>
        <line x1="210" y1="138" x2="210" y2="145"/><line x1="240" y1="138" x2="240" y2="145"/>
        <line x1="270" y1="138" x2="270" y2="145"/><line x1="300" y1="138" x2="300" y2="145"/>
        <line x1="330" y1="138" x2="330" y2="145"/>
      </g>
      <!-- 石板小徑 -->
      <rect x="420" y="230" width="8" height="118" fill="#C8B070" rx="2" opacity="0.45"/>
      <rect x="150" y="198" width="8" height="152" fill="#C8B070" rx="2" opacity="0.38"/>
      <rect x="580" y="198" width="8" height="152" fill="#C8B070" rx="2" opacity="0.38"/>
      <!-- 樹木群 -->
      <g transform="translate(58,28)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#4A8038"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#5A9048"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#68A056"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(98,22)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#4A8038"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#5A9048"/><ellipse cx="14" cy="31" rx="11" ry="7" fill="#68A056"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(58,76)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#508840"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#609858"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#6EA864"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(278,28)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#4A8038"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#5A9048"/><ellipse cx="14" cy="31" rx="11" ry="7" fill="#68A056"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(308,58)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#508840"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(18,418)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#4A8038"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#5A9048"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#68A056"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(58,458)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#508840"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(18,508)"><ellipse cx="14" cy="22" rx="11" ry="9" fill="#4A8038"/><ellipse cx="14" cy="27" rx="13" ry="8" fill="#5A9048"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(98,398)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#508840"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#609850"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#6EA860"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(798,28)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#4A8038"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#5A9048"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#68A056"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(818,68)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#508840"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(798,198)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#4A8038"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#5A9048"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(808,268)"><ellipse cx="14" cy="22" rx="11" ry="9" fill="#508840"/><ellipse cx="14" cy="27" rx="13" ry="8" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(928,618)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#4A8038"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#5A9048"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#68A056"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(978,648)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#508840"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(1018,618)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#4A8038"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#5A9048"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(158,238)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#508840"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(218,398)"><ellipse cx="14" cy="22" rx="11" ry="9" fill="#4A8038"/><ellipse cx="14" cy="27" rx="13" ry="8" fill="#5A9048"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(538,418)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#508840"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(598,458)"><ellipse cx="14" cy="22" rx="11" ry="9" fill="#4A8038"/><ellipse cx="14" cy="27" rx="13" ry="8" fill="#5A9048"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(198,658)"><ellipse cx="14" cy="22" rx="13" ry="11" fill="#508840"/><ellipse cx="14" cy="27" rx="15" ry="10" fill="#609850"/><ellipse cx="14" cy="31" rx="12" ry="8" fill="#6EA860"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(548,698)"><ellipse cx="14" cy="22" rx="12" ry="10" fill="#4A8038"/><ellipse cx="14" cy="27" rx="14" ry="9" fill="#5A9048"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <g transform="translate(618,728)"><ellipse cx="14" cy="22" rx="11" ry="9" fill="#508840"/><ellipse cx="14" cy="27" rx="13" ry="8" fill="#609850"/><rect x="11" y="37" width="6" height="10" rx="2" fill="#7A5A28" opacity="0.8"/></g>
      <!-- 花朵 -->
      <g transform="translate(138,143)"><circle cx="7" cy="7" r="3" fill="#F4D48E"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/></g>
      <g transform="translate(308,143)"><circle cx="7" cy="7" r="3" fill="#F4D48E"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/></g>
      <g transform="translate(168,448)"><circle cx="7" cy="7" r="3" fill="#EFB0A4"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#EFB0A4" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#EFB0A4" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#EFB0A4" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#EFB0A4" opacity="0.7"/></g>
      <g transform="translate(78,538)"><circle cx="7" cy="7" r="3" fill="#EFB0A4"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#EFB0A4" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#EFB0A4" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#EFB0A4" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#EFB0A4" opacity="0.7"/></g>
      <g transform="translate(558,378)"><circle cx="7" cy="7" r="3" fill="#C18ACF"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#C18ACF" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#C18ACF" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#C18ACF" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#C18ACF" opacity="0.7"/></g>
      <g transform="translate(668,658)"><circle cx="7" cy="7" r="3" fill="#C18ACF"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#C18ACF" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#C18ACF" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#C18ACF" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#C18ACF" opacity="0.7"/></g>
      <g transform="translate(798,408)"><circle cx="7" cy="7" r="3" fill="#F4D48E"/><ellipse cx="7" cy="2" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/><ellipse cx="7" cy="12" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/><ellipse cx="2" cy="7" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/><ellipse cx="12" cy="7" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/></g>
    </svg>

    {{-- ══ 物件層 ══ --}}
    <div class="objects-layer" id="objectsLayer">

      {{-- 格子選位覆蓋層 --}}
      <div class="grid-overlay" id="gridOverlay">
        @for($gy = 0; $gy < 8; $gy++)
          @for($gx = 0; $gx < 8; $gx++)
            @php
              $isOccupied = $buildings->whereNotNull('grid_x')
                                      ->where('grid_x', $gx)
                                      ->where('grid_y', $gy)
                                      ->count() > 0;
            @endphp
            <div class="grid-cell {{ $isOccupied ? 'occupied' : 'available' }}"
                 data-gx="{{ $gx }}"
                 data-gy="{{ $gy }}"
                 @if(!$isOccupied) onclick="selectCell(this)" @endif>
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
          $levels     = $upgradeMap[$b->type];
          $nextLevel  = $b->level + 1;
          $hasNext    = isset($levels[$nextLevel]);
          $thresholds = [0=>0, 1=>1, 2=>3, 3=>7];
          $curT       = $thresholds[$b->level] ?? 0;
          $nextT      = $hasNext ? ($thresholds[$nextLevel] ?? $curT) : $curT;
          $pct        = $hasNext && ($nextT > $curT)
                          ? min(100, (int)round(($b->completed_count - $curT) / ($nextT - $curT) * 100))
                          : 100;
          $need       = $hasNext ? max(0, $nextT - $b->completed_count) : 0;
          $nextName   = $hasNext ? $levels[$nextLevel][0] : '';
          $nextSvg    = $hasNext ? $levels[$nextLevel][1] : '';
        @endphp
        <div class="building"
             style="left:{{ $px }}px;top:{{ $py }}px;"
             onclick="openPanel(
               '{{ addslashes($b->name) }}',
               '{{ $b->type }}',
               {{ $b->level }},
               {{ $b->completed_count }},
               {{ $hasNext ? 1 : 0 }},
               '{{ addslashes($nextName) }}',
               '{{ $nextSvg }}',
               {{ $need }},
               {{ $pct }}
             )">
          <div class="bld-tag">{{ $b->name }}・{{ $b->type }}</div>
          <img class="bld-img" src="/svg/{{ $b->svg_file }}" alt="{{ $b->name }}">
        </div>
      @endforeach

    </div>{{-- /objects-layer --}}
  </div>{{-- /world --}}

  {{-- ══ 資訊面板 ══ --}}
  <div class="panel-overlay" id="overlay">
    <div class="panel">
      <button class="panel-close" onclick="closePanel()">✕</button>
      <div class="p-title"  id="p-title"></div>
      <div class="p-sub"    id="p-sub"></div>
      <div class="p-divider"></div>
      <div class="stat-row"><span class="stat-label">已完成任務</span><span class="stat-val" id="p-done"></span></div>
      <div class="stat-row"><span class="stat-label">目前等級</span><span class="stat-val" id="p-lv"></span></div>
      <div class="prog-meta"><span>升級進度</span><span id="p-pct"></span></div>
      <div class="prog-bar"><div class="prog-fill" id="p-fill"></div></div>
      <div class="p-divider"></div>
      <div id="next-section">
        <div class="next-label">下一棟建築（解鎖條件）</div>
        <div class="next-bld">
          <img class="next-bld-img" id="p-next-img" src="" alt="">
          <div>
            <div class="next-name" id="p-next-name"></div>
            <div class="next-req"  id="p-next-req"></div>
            <span class="next-badge">尚未解鎖</span>
          </div>
        </div>
      </div>
      <div class="max-hint" id="p-max" style="display:none">🌟 已達最高等級，城鎮之星！</div>
    </div>
  </div>

  {{-- ══ 導覽列 ══ --}}
  <nav class="nav">
    <a href="{{ url('/') }}"        class="nav-btn active">城鎮</a>
    <a href="{{ url('/calendar') }}" class="nav-btn">行事曆</a>
  </nav>

  <div class="toast" id="toast"></div>
</div>{{-- /viewport --}}

<script>
// ── 動態計算 banner 高度，讓 viewport 填滿剩餘空間 ──
(function(){
  const ba = document.getElementById('bannerArea');
  if(ba){
    document.documentElement.style.setProperty('--banner-h', ba.offsetHeight + 'px');
  }
})();

// ── 拖曳（加 requestAnimationFrame 防卡頓）──
const vp    = document.getElementById('vp');
const world = document.getElementById('world');
let ox=0, oy=0, sx=0, sy=0, drag=false, rafPending=false;
const WW=1080, WH=820;

function clamp(v,mn,mx){ return Math.max(mn,Math.min(mx,v)); }

function applyTransform(){
    world.style.transform = `translate(${ox}px,${oy}px)`;
    rafPending = false;
}

vp.addEventListener('mousedown', e => {
    if(e.target.closest('.panel') || e.target.closest('.grid-cell.available')) return;
    drag=true; sx=e.clientX-ox; sy=e.clientY-oy;
    vp.classList.add('dragging');
});
window.addEventListener('mousemove', e => {
    if(!drag) return;
    ox = clamp(e.clientX-sx, -(WW - vp.offsetWidth),  0);
    oy = clamp(e.clientY-sy, -(WH - vp.offsetHeight), 0);
    if(!rafPending){ rafPending=true; requestAnimationFrame(applyTransform); }
});
window.addEventListener('mouseup', () => { drag=false; vp.classList.remove('dragging'); });

let tSx=0, tSy=0;
vp.addEventListener('touchstart', e => {
    tSx=e.touches[0].clientX-ox;
    tSy=e.touches[0].clientY-oy;
}, {passive:true});
vp.addEventListener('touchmove', e => {
    ox = clamp(e.touches[0].clientX-tSx, -(WW - vp.offsetWidth),  0);
    oy = clamp(e.touches[0].clientY-tSy, -(WH - vp.offsetHeight), 0);
    if(!rafPending){ rafPending=true; requestAnimationFrame(applyTransform); }
}, {passive:true});

// ── 選位模式 ─────────────────────────────────
let placingId = null;

document.querySelectorAll('.unplaced-chip').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.unplaced-chip').forEach(b => b.classList.remove('active'));
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
  document.querySelectorAll('.unplaced-chip').forEach(b => b.classList.remove('active'));
}

function selectCell(cell){
  if(!placingId) return;
  const gx = parseInt(cell.dataset.gx);
  const gy = parseInt(cell.dataset.gy);

  fetch(`/buildings/${placingId}/place`, {
    method: 'PATCH',
    headers: {
      'Content-Type':  'application/json',
      'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
      'Accept':        'application/json',
    },
    body: JSON.stringify({ grid_x: gx, grid_y: gy })
  })
  .then(r => r.json())
  .then(data => {
    if(data.success){
      showToast('建築已放置！');
      setTimeout(() => location.reload(), 900);
    } else {
      showToast(data.error || '發生錯誤，請再試一次');
    }
  })
  .catch(() => showToast('網路錯誤，請再試一次'));
}

// ── 資訊面板 ─────────────────────────────────
function openPanel(name, type, lv, done, hasNext, nextName, nextSvg, need, pct){
  document.getElementById('p-title').textContent = name + '・' + type;
  document.getElementById('p-sub').textContent   = '目前建築：' + name + '（Lv.' + lv + '）';
  document.getElementById('p-done').textContent  = done + ' 次';
  document.getElementById('p-lv').textContent    = 'Lv.' + lv;
  document.getElementById('p-pct').textContent   = pct + '%';
  document.getElementById('p-fill').style.width  = pct + '%';

  const ns = document.getElementById('next-section');
  const pm = document.getElementById('p-max');
  if(hasNext){
    ns.style.display = '';
    pm.style.display = 'none';
    document.getElementById('p-next-img').src          = '/svg/' + nextSvg;
    document.getElementById('p-next-name').textContent = nextName + '（Lv.' + (lv+1) + '）';
    document.getElementById('p-next-req').textContent  = '再完成 ' + need + ' 次任務可解鎖';
  } else {
    ns.style.display = 'none';
    pm.style.display = 'block';
  }
  document.getElementById('overlay').classList.add('active');
}

function closePanel(){
  document.getElementById('overlay').classList.remove('active');
}
document.getElementById('overlay').addEventListener('click', e => {
  if(e.target === document.getElementById('overlay')) closePanel();
});

// ── Toast ─────────────────────────────────────
function showToast(msg){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}
</script>
</body>
</html>
