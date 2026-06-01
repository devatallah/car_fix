<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>CAR.FIX PRO – @yield('title', 'Dashboard')</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('landing/assets/images/landpage/svg/icon.svg') }}">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --bg-primary:    #0d0f14;
      --bg-secondary:  #141820;
      --bg-card:       rgba(255,255,255,0.04);
      --bg-card-hover: rgba(255,255,255,0.07);
      --accent:        #00e5b0;
      --accent-glow:   rgba(0,229,176,0.35);
      --accent-dim:    rgba(0,229,176,0.12);
      --accent2:       #00c9f5;
      --text-primary:  #e8eaf0;
      --text-secondary:#8a90a2;
      --text-muted:    #4a5068;
      --border:        rgba(0,229,176,0.15);
      --border-strong: rgba(0,229,176,0.45);
      --danger:        #ff4d6d;
      --warning:       #ffb830;
      --radius-sm:     8px;
      --radius-md:     14px;
      --radius-lg:     20px;
      --shadow-glow:   0 0 24px rgba(0,229,176,0.18), 0 4px 32px rgba(0,0,0,0.6);
      --transition:    all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Cairo', sans-serif;
      background: var(--bg-primary);
      color: var(--text-primary);
      min-height: 100vh;
      overflow-x: hidden;
      background-image:
        radial-gradient(ellipse 80% 50% at 20% -10%, rgba(0,229,176,0.06) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 85% 90%, rgba(0,201,245,0.05) 0%, transparent 60%);
    }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg-primary); }
    ::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 3px; }

    /* ── NAVBAR ── */
    .cf-navbar {
      position: sticky; top: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 32px; height: 68px;
      background: rgba(13,15,20,0.88);
      backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
      box-shadow: 0 2px 30px rgba(0,0,0,0.5);
    }
    .cf-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .cf-logo-icon {
      width: 38px; height: 38px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 0 16px var(--accent-glow); flex-shrink: 0;
    }
    .cf-logo-icon svg { width: 22px; height: 22px; }
    .cf-logo-text {
      font-family: 'Orbitron', sans-serif; font-size: 16px;
      font-weight: 900; letter-spacing: 2px; color: var(--text-primary);
    }
    .cf-logo-text span { color: var(--accent); }

    .cf-nav-steps { display: flex; align-items: center; gap: 6px; }
    .cf-nav-step {
      display: flex; align-items: center; gap: 6px;
      padding: 6px 14px; border-radius: 20px; font-size: 12px;
      font-weight: 600; color: var(--text-muted); cursor: pointer;
      transition: var(--transition); border: 1px solid transparent;
    }
    .cf-nav-step.active {
      background: var(--accent-dim); color: var(--accent);
      border-color: var(--border-strong);
    }
    .cf-nav-step-num {
      width: 20px; height: 20px; border-radius: 50%;
      background: var(--text-muted); color: var(--bg-primary);
      font-size: 10px; font-weight: 900;
      display: flex; align-items: center; justify-content: center;
    }
    .cf-nav-step.active .cf-nav-step-num {
      background: var(--accent); box-shadow: 0 0 8px var(--accent-glow);
    }
    .cf-nav-step-divider { width: 24px; height: 1px; background: var(--border); }

    .cf-user-section { display: flex; align-items: center; gap: 14px; }
    .cf-credits-badge {
      display: flex; align-items: center; gap: 7px;
      padding: 6px 14px;
      background: var(--accent-dim); border: 1px solid var(--border-strong);
      border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .cf-credit-icon {
      width: 16px; height: 16px; background: var(--accent);
      border-radius: 50%; display: flex; align-items: center; justify-content: center;
      font-size: 9px; color: var(--bg-primary); font-weight: 900;
    }
    .cf-credit-value { color: var(--accent); }
    .cf-credit-label { color: var(--text-secondary); }

    .cf-expiry-badge { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text-secondary); }
    .cf-expiry-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: var(--accent); box-shadow: 0 0 6px var(--accent);
      animation: cfPulse 2s infinite;
    }
    @keyframes cfPulse {
      0%,100% { opacity: 1; transform: scale(1); }
      50%      { opacity: 0.5; transform: scale(0.8); }
    }

    .cf-user-menu { position: relative; }
    .cf-user-avatar {
      width: 38px; height: 38px; border-radius: 50%;
      background: linear-gradient(135deg, #1e2535, #2a3045);
      border: 2px solid var(--border-strong);
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 700; color: var(--accent);
      cursor: pointer; transition: var(--transition);
      box-shadow: 0 0 12px var(--accent-glow);
    }
    .cf-user-avatar:hover { transform: scale(1.05); box-shadow: 0 0 20px var(--accent-glow); }
    .cf-dropdown {
      position: absolute; top: calc(100% + 10px); left: 0;
      min-width: 180px;
      background: rgba(20,24,32,0.97); border: 1px solid var(--border-strong);
      border-radius: var(--radius-md); padding: 8px;
      backdrop-filter: blur(20px); box-shadow: var(--shadow-glow);
      opacity: 0; pointer-events: none; transform: translateY(-8px);
      transition: var(--transition); z-index: 200;
    }
    .cf-user-menu:hover .cf-dropdown,
    .cf-user-menu:focus-within .cf-dropdown {
      opacity: 1; pointer-events: all; transform: translateY(0);
    }
    .cf-dropdown a, .cf-dropdown button {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 12px; border-radius: var(--radius-sm);
      font-family: 'Cairo', sans-serif; font-size: 12px; font-weight: 600;
      color: var(--text-secondary); text-decoration: none;
      background: none; border: none; cursor: pointer; width: 100%;
      transition: var(--transition);
    }
    .cf-dropdown a:hover, .cf-dropdown button:hover {
      background: var(--accent-dim); color: var(--accent);
    }
    .cf-dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

    /* ── PAGE ── */
    .cf-main { max-width: 1200px; margin: 0 auto; padding: 36px 24px 60px; }

    /* ── TOAST ── */
    .cf-toast {
      position: fixed; bottom: 28px; left: 50%;
      transform: translateX(-50%) translateY(20px);
      background: rgba(20,24,32,0.97); border: 1px solid var(--border-strong);
      border-radius: var(--radius-md); padding: 12px 22px;
      font-size: 13px; color: var(--accent); font-weight: 600;
      backdrop-filter: blur(20px); box-shadow: 0 0 30px var(--accent-glow);
      opacity: 0; transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
      pointer-events: none; z-index: 999; white-space: nowrap;
    }
    .cf-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    @media (max-width: 820px) {
      .cf-nav-steps { display: none; }
      .cf-expiry-badge { display: none; }
      .cf-navbar { padding: 0 16px; }
    }

    @yield('layout-styles')
  </style>
  @yield('styles')
</head>
<body>

  <!-- NAVBAR -->
  <nav class="cf-navbar">
    <a href="{{ url('/user/solutions') }}" class="cf-logo">
      <div class="cf-logo-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:#0d0f14">
          <path d="M3 18v-6a9 9 0 0118 0v6"/>
          <path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/>
        </svg>
      </div>
      <span class="cf-logo-text">CAR.<span>FIX</span> PRO</span>
    </a>

    @yield('nav-steps')

    <div class="cf-user-section">
      <div class="cf-credits-badge">
        <div class="cf-credit-icon">C</div>
        <span class="cf-credit-value">{{ auth()->user()->balance ?? 0 }}</span>
        <span class="cf-credit-label">كريدت</span>
      </div>
      <div class="cf-expiry-badge">
        <div class="cf-expiry-dot"></div>
        <span>{{ auth()->user()->license_expire_date ?? 'نشط' }}</span>
      </div>
      <div class="cf-user-menu" tabindex="0">
        <div class="cf-user-avatar">
          {{ mb_substr(auth()->user()->name ?? 'U', 0, 2) }}
        </div>
        <div class="cf-dropdown">
          <a href="{{ url('/user/profile') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            الملف الشخصي
          </a>
          <div class="cf-dropdown-divider"></div>
          <form action="{{ route('user_logout') }}" method="POST" style="display:contents">
            @csrf
            <button type="submit">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              تسجيل الخروج
            </button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <!-- PAGE CONTENT -->
  <main class="cf-main">
    @yield('content')
  </main>

  <!-- Toast -->
  <div class="cf-toast" id="cfToast"></div>

  <script>
    function cfShowToast(msg, type) {
      const t = document.getElementById('cfToast');
      t.textContent = msg;
      t.style.color = type === 'error' ? '#ff4d6d' : 'var(--accent)';
      t.style.borderColor = type === 'error' ? 'rgba(255,77,109,0.45)' : 'var(--border-strong)';
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 3500);
    }
  </script>

  @yield('scripts')
</body>
</html>
