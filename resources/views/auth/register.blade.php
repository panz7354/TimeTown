{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TimeTown｜加入城鎮</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@400;700&display=swap');
*{box-sizing:border-box;margin:0;padding:0;}
body{
  font-family:'Noto Serif TC',serif;
  min-height:100vh;
  display:flex;align-items:center;justify-content:center;
  background:#8BAF66;
  position:relative;overflow:hidden;
}

/* ── 背景地圖草地 ── */
body::before{
  content:'';position:absolute;inset:0;
  background-image:
    repeating-linear-gradient(0deg,transparent,transparent 39px,rgba(100,120,60,0.12) 40px),
    repeating-linear-gradient(90deg,transparent,transparent 39px,rgba(100,120,60,0.12) 40px);
}
body::after{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(ellipse at 0% 0%,   rgba(120,160,60,0.3)  0%,transparent 55%),
    radial-gradient(ellipse at 100% 0%, rgba(80,130,50,0.25)  0%,transparent 55%),
    radial-gradient(ellipse at 0% 100%, rgba(60,110,40,0.3)   0%,transparent 55%),
    radial-gradient(ellipse at 100% 100%,rgba(90,140,55,0.25) 0%,transparent 55%);
}

/* ── 卡片 ── */
.card{
  position:relative;z-index:1;
  background:#F5EDD6;
  border-radius:20px;
  border:2px solid #C9A84C;
  padding:40px 36px 36px;
  width:100%;max-width:420px;
  box-shadow:0 12px 48px rgba(40,26,16,0.35);
}

/* 羊皮紙紋路 */
.card::before{
  content:'';position:absolute;inset:0;border-radius:18px;
  background-image:
    repeating-linear-gradient(0deg,transparent,transparent 19px,rgba(139,105,20,0.06) 20px),
    repeating-linear-gradient(90deg,transparent,transparent 19px,rgba(139,105,20,0.06) 20px);
  pointer-events:none;
}

/* 四角裝飾 */
.card::after{
  content:'';position:absolute;inset:6px;border-radius:14px;
  border:1px solid rgba(201,168,76,0.3);
  pointer-events:none;
}

/* ── Logo / 標題 ── */
.logo{text-align:center;margin-bottom:28px;position:relative;}
.logo-icon{
  font-size:36px;display:block;margin-bottom:8px;
  filter:drop-shadow(0 2px 4px rgba(0,0,0,0.15));
}
.logo-title{
  font-size:22px;font-weight:700;color:#3D2B1F;
  letter-spacing:0.1em;
}
.logo-sub{
  font-size:12px;color:#9A7230;margin-top:4px;letter-spacing:0.12em;
}
.logo-deco{
  display:flex;align-items:center;justify-content:center;gap:8px;margin-top:10px;
}
.deco-line{width:60px;height:1px;background:linear-gradient(90deg,transparent,#C9A84C);}
.deco-line.r{background:linear-gradient(90deg,#C9A84C,transparent);}
.deco-diamond{width:6px;height:6px;background:#C9A84C;transform:rotate(45deg);}

/* ── 表單 ── */
.form-group{margin-bottom:16px;position:relative;}
.form-group label{
  display:block;font-size:12px;color:#8B6914;
  letter-spacing:0.06em;margin-bottom:6px;font-weight:700;
}
.form-group input{
  width:100%;
  background:#FDF7EC;
  border:1.5px solid #D4C089;
  border-radius:10px;
  padding:10px 14px;
  font-family:'Noto Serif TC',serif;
  font-size:14px;color:#3D2B1F;
  outline:none;
  transition:border-color 0.15s,box-shadow 0.15s;
}
.form-group input:focus{
  border-color:#C9A84C;
  box-shadow:0 0 0 3px rgba(201,168,76,0.15);
}
.form-group input.error{border-color:#C0392B;}

.field-error{
  font-size:11px;color:#C0392B;margin-top:4px;
  display:flex;align-items:center;gap:4px;
}
.field-error::before{content:'⚠';font-size:10px;}

/* ── 送出按鈕 ── */
.submit-btn{
  width:100%;
  background:#C9A84C;color:#3D2B1F;
  border:none;border-radius:10px;
  padding:12px;margin-top:6px;
  font-family:'Noto Serif TC',serif;
  font-size:15px;font-weight:700;
  cursor:pointer;letter-spacing:0.06em;
  transition:background 0.15s,transform 0.1s;
}
.submit-btn:hover{background:#B89040;transform:translateY(-1px);}
.submit-btn:active{transform:translateY(0);}

/* ── 切換連結 ── */
.switch{
  text-align:center;margin-top:20px;
  font-size:13px;color:#6B4C35;
}
.switch a{
  color:#8B6914;font-weight:700;text-decoration:none;
  border-bottom:1px solid rgba(139,105,20,0.3);
  transition:color 0.15s;
}
.switch a:hover{color:#C9A84C;}

/* 像素裝飾小星 */
.px-star{
  position:absolute;pointer-events:none;opacity:0.7;
}
</style>
</head>
<body>

<!-- 背景像素樹裝飾 -->
<svg style="position:absolute;top:30px;left:30px;opacity:0.6;pointer-events:none;" width="56" height="70" viewBox="0 0 56 70">
  <ellipse cx="28" cy="28" rx="24" ry="20" fill="#4A8038"/>
  <ellipse cx="28" cy="34" rx="26" ry="18" fill="#5A9048"/>
  <ellipse cx="28" cy="40" rx="22" ry="15" fill="#68A056"/>
  <rect x="22" y="52" width="12" height="18" rx="3" fill="#7A5A28" opacity="0.8"/>
</svg>
<svg style="position:absolute;bottom:40px;right:40px;opacity:0.5;pointer-events:none;" width="44" height="56" viewBox="0 0 44 56">
  <ellipse cx="22" cy="22" rx="19" ry="16" fill="#508840"/>
  <ellipse cx="22" cy="27" rx="21" ry="14" fill="#609850"/>
  <rect x="17" y="40" width="10" height="16" rx="3" fill="#7A5A28" opacity="0.8"/>
</svg>
<svg style="position:absolute;top:60px;right:60px;opacity:0.45;pointer-events:none;" width="36" height="46" viewBox="0 0 36 46">
  <ellipse cx="18" cy="18" rx="16" ry="14" fill="#4A8038"/>
  <ellipse cx="18" cy="22" rx="17" ry="12" fill="#5A9048"/>
  <rect x="14" y="32" width="8" height="14" rx="2" fill="#7A5A28" opacity="0.8"/>
</svg>
<!-- 花朵 -->
<svg style="position:absolute;bottom:80px;left:60px;opacity:0.6;pointer-events:none;" width="20" height="20" viewBox="0 0 20 20">
  <circle cx="10" cy="10" r="4" fill="#F4D48E"/>
  <ellipse cx="10" cy="3"  rx="3" ry="4" fill="#F4D48E" opacity="0.7"/>
  <ellipse cx="10" cy="17" rx="3" ry="4" fill="#F4D48E" opacity="0.7"/>
  <ellipse cx="3"  cy="10" rx="4" ry="3" fill="#F4D48E" opacity="0.7"/>
  <ellipse cx="17" cy="10" rx="4" ry="3" fill="#F4D48E" opacity="0.7"/>
</svg>
<svg style="position:absolute;top:120px;left:80px;opacity:0.5;pointer-events:none;" width="16" height="16" viewBox="0 0 16 16">
  <circle cx="8" cy="8" r="3" fill="#EFB0A4"/>
  <ellipse cx="8" cy="2" rx="2" ry="3" fill="#EFB0A4" opacity="0.7"/>
  <ellipse cx="8" cy="14" rx="2" ry="3" fill="#EFB0A4" opacity="0.7"/>
  <ellipse cx="2" cy="8" rx="3" ry="2" fill="#EFB0A4" opacity="0.7"/>
  <ellipse cx="14" cy="8" rx="3" ry="2" fill="#EFB0A4" opacity="0.7"/>
</svg>

<div class="card">
  <div class="logo">
    <span class="logo-icon">🏰</span>
    <div class="logo-title">TimeTown</div>
    <div class="logo-sub">建立你的城鎮帳號</div>
    <div class="logo-deco">
      <div class="deco-line"></div>
      <div class="deco-diamond"></div>
      <div class="deco-line r"></div>
    </div>
  </div>

  <form action="{{ url('/register') }}" method="POST" novalidate>
    @csrf

    <div class="form-group">
      <label>暱稱</label>
      <input type="text" name="name" placeholder="你的城鎮名稱"
             value="{{ old('name') }}"
             class="{{ $errors->has('name') ? 'error' : '' }}"
             maxlength="20" required>
      @error('name')
        <div class="field-error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="your@email.com"
             value="{{ old('email') }}"
             class="{{ $errors->has('email') ? 'error' : '' }}"
             required>
      @error('email')
        <div class="field-error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label>密碼（至少 8 個字元）</label>
      <input type="password" name="password" placeholder="••••••••"
             class="{{ $errors->has('password') ? 'error' : '' }}"
             required>
      @error('password')
        <div class="field-error">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label>確認密碼</label>
      <input type="password" name="password_confirmation" placeholder="••••••••" required>
    </div>

    <button type="submit" class="submit-btn">建立帳號，進入城鎮 🏡</button>
  </form>

  <div class="switch">
    已有帳號？<a href="{{ url('/login') }}">登入城鎮</a>
  </div>
</div>

</body>
</html>