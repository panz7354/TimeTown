{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TimeTown｜登入城鎮</title>
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

.card{
  position:relative;z-index:1;
  background:#F5EDD6;
  border-radius:20px;
  border:2px solid #C9A84C;
  padding:40px 36px 36px;
  width:100%;max-width:400px;
  box-shadow:0 12px 48px rgba(40,26,16,0.35);
}
.card::before{
  content:'';position:absolute;inset:0;border-radius:18px;
  background-image:
    repeating-linear-gradient(0deg,transparent,transparent 19px,rgba(139,105,20,0.06) 20px),
    repeating-linear-gradient(90deg,transparent,transparent 19px,rgba(139,105,20,0.06) 20px);
  pointer-events:none;
}
.card::after{
  content:'';position:absolute;inset:6px;border-radius:14px;
  border:1px solid rgba(201,168,76,0.3);
  pointer-events:none;
}

.logo{text-align:center;margin-bottom:28px;position:relative;}
.logo-icon{font-size:36px;display:block;margin-bottom:8px;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.15));}
.logo-title{font-size:22px;font-weight:700;color:#3D2B1F;letter-spacing:0.1em;}
.logo-sub{font-size:12px;color:#9A7230;margin-top:4px;letter-spacing:0.12em;}
.logo-deco{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:10px;}
.deco-line{width:60px;height:1px;background:linear-gradient(90deg,transparent,#C9A84C);}
.deco-line.r{background:linear-gradient(90deg,#C9A84C,transparent);}
.deco-diamond{width:6px;height:6px;background:#C9A84C;transform:rotate(45deg);}

/* ── 全域錯誤（帳密錯誤）── */
.alert-error{
  background:#FCEBEB;border:1px solid #E8A0A0;border-radius:10px;
  padding:10px 14px;margin-bottom:16px;
  font-size:13px;color:#A32D2D;
  display:flex;align-items:center;gap:6px;
}
.alert-error::before{content:'⚠';font-size:12px;}

/* ── Flash success ── */
.alert-success{
  background:#EAF3DE;border:1px solid #C0DD97;border-radius:10px;
  padding:10px 14px;margin-bottom:16px;
  font-size:13px;color:#3B6D11;
}

.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:12px;color:#8B6914;letter-spacing:0.06em;margin-bottom:6px;font-weight:700;}
.form-group input{
  width:100%;background:#FDF7EC;
  border:1.5px solid #D4C089;border-radius:10px;
  padding:10px 14px;
  font-family:'Noto Serif TC',serif;font-size:14px;color:#3D2B1F;
  outline:none;transition:border-color 0.15s,box-shadow 0.15s;
}
.form-group input:focus{border-color:#C9A84C;box-shadow:0 0 0 3px rgba(201,168,76,0.15);}
.form-group input.error{border-color:#C0392B;}
.field-error{font-size:11px;color:#C0392B;margin-top:4px;display:flex;align-items:center;gap:4px;}
.field-error::before{content:'⚠';font-size:10px;}

/* 記住我 */
.remember-row{display:flex;align-items:center;gap:8px;margin-bottom:18px;}
.remember-row input[type=checkbox]{
  width:16px;height:16px;accent-color:#C9A84C;cursor:pointer;
}
.remember-row label{font-size:13px;color:#6B4C35;cursor:pointer;}

.submit-btn{
  width:100%;background:#C9A84C;color:#3D2B1F;
  border:none;border-radius:10px;padding:12px;
  font-family:'Noto Serif TC',serif;font-size:15px;font-weight:700;
  cursor:pointer;letter-spacing:0.06em;
  transition:background 0.15s,transform 0.1s;
}
.submit-btn:hover{background:#B89040;transform:translateY(-1px);}
.submit-btn:active{transform:translateY(0);}

.switch{text-align:center;margin-top:20px;font-size:13px;color:#6B4C35;}
.switch a{color:#8B6914;font-weight:700;text-decoration:none;border-bottom:1px solid rgba(139,105,20,0.3);transition:color 0.15s;}
.switch a:hover{color:#C9A84C;}
</style>
</head>
<body>

<!-- 背景裝飾 -->
<svg style="position:absolute;top:40px;left:40px;opacity:0.55;pointer-events:none;" width="56" height="70" viewBox="0 0 56 70">
  <ellipse cx="28" cy="28" rx="24" ry="20" fill="#4A8038"/>
  <ellipse cx="28" cy="34" rx="26" ry="18" fill="#5A9048"/>
  <ellipse cx="28" cy="40" rx="22" ry="15" fill="#68A056"/>
  <rect x="22" y="52" width="12" height="18" rx="3" fill="#7A5A28" opacity="0.8"/>
</svg>
<svg style="position:absolute;bottom:50px;right:50px;opacity:0.5;pointer-events:none;" width="48" height="62" viewBox="0 0 48 62">
  <ellipse cx="24" cy="24" rx="21" ry="18" fill="#508840"/>
  <ellipse cx="24" cy="30" rx="23" ry="16" fill="#609850"/>
  <ellipse cx="24" cy="36" rx="19" ry="13" fill="#6EA860"/>
  <rect x="19" y="46" width="10" height="16" rx="3" fill="#7A5A28" opacity="0.8"/>
</svg>
<svg style="position:absolute;top:80px;right:50px;opacity:0.4;pointer-events:none;" width="32" height="42" viewBox="0 0 32 42">
  <ellipse cx="16" cy="16" rx="14" ry="12" fill="#4A8038"/>
  <ellipse cx="16" cy="20" rx="15" ry="11" fill="#5A9048"/>
  <rect x="12" y="30" width="8" height="12" rx="2" fill="#7A5A28" opacity="0.8"/>
</svg>
<svg style="position:absolute;bottom:90px;left:50px;opacity:0.55;pointer-events:none;" width="20" height="20" viewBox="0 0 20 20">
  <circle cx="10" cy="10" r="4" fill="#C18ACF"/>
  <ellipse cx="10" cy="3" rx="3" ry="4" fill="#C18ACF" opacity="0.7"/>
  <ellipse cx="10" cy="17" rx="3" ry="4" fill="#C18ACF" opacity="0.7"/>
  <ellipse cx="3" cy="10" rx="4" ry="3" fill="#C18ACF" opacity="0.7"/>
  <ellipse cx="17" cy="10" rx="4" ry="3" fill="#C18ACF" opacity="0.7"/>
</svg>
<svg style="position:absolute;top:140px;right:90px;opacity:0.5;pointer-events:none;" width="16" height="16" viewBox="0 0 16 16">
  <circle cx="8" cy="8" r="3" fill="#F4D48E"/>
  <ellipse cx="8" cy="2" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/>
  <ellipse cx="8" cy="14" rx="2" ry="3" fill="#F4D48E" opacity="0.7"/>
  <ellipse cx="2" cy="8" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/>
  <ellipse cx="14" cy="8" rx="3" ry="2" fill="#F4D48E" opacity="0.7"/>
</svg>

<div class="card">
  <div class="logo">
    <span class="logo-icon">🏰</span>
    <div class="logo-title">TimeTown</div>
    <div class="logo-sub">回到你的城鎮</div>
    <div class="logo-deco">
      <div class="deco-line"></div>
      <div class="deco-diamond"></div>
      <div class="deco-line r"></div>
    </div>
  </div>

  {{-- Flash success（例如剛登出）--}}
  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- 帳密錯誤 --}}
  @if($errors->has('email') && !$errors->has('password'))
    <div class="alert-error">{{ $errors->first('email') }}</div>
  @endif

  <form action="{{ url('/login') }}" method="POST" novalidate>
    @csrf

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="your@email.com"
             value="{{ old('email') }}"
             class="{{ $errors->has('email') ? 'error' : '' }}"
             required autofocus>
      @error('email')
        @if($errors->has('password'))
          <div class="field-error">{{ $message }}</div>
        @endif
      @enderror
    </div>

    <div class="form-group">
      <label>密碼</label>
      <input type="password" name="password" placeholder="••••••••"
             class="{{ $errors->has('password') ? 'error' : '' }}"
             required>
      @error('password')
        <div class="field-error">{{ $message }}</div>
      @enderror
    </div>

    <div class="remember-row">
      <input type="checkbox" name="remember" id="remember"
             {{ old('remember') ? 'checked' : '' }}>
      <label for="remember">記住我</label>
    </div>

    <button type="submit" class="submit-btn">登入城鎮 ✨</button>
  </form>

  <div class="switch">
    還沒有帳號？<a href="{{ url('/register') }}">建立城鎮</a>
  </div>
</div>

</body>
</html>