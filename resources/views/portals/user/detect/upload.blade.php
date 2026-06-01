@extends('portals.user.layouts.carfix')

@section('title', 'معالج ملفات ECU')

@section('nav-steps')
<div class="cf-nav-steps">
  <div class="cf-nav-step active" id="step1Btn">
    <div class="cf-nav-step-num">١</div>رفع الملف
  </div>
  <div class="cf-nav-step-divider"></div>
  <div class="cf-nav-step" id="step2Btn">
    <div class="cf-nav-step-num">٢</div>الحلول
  </div>
  <div class="cf-nav-step-divider"></div>
  <div class="cf-nav-step" id="step3Btn">
    <div class="cf-nav-step-num">٣</div>التحميل
  </div>
</div>
@endsection

@section('styles')
<style>
  :root{
    --bg-card:rgba(255,255,255,.04);--bg-card-hover:rgba(255,255,255,.07);
    --accent:#00e5b0;--accent-glow:rgba(0,229,176,.35);--accent-dim:rgba(0,229,176,.12);
    --accent2:#00c9f5;--text-primary:#e8eaf0;--text-secondary:#8a90a2;--text-muted:#4a5068;
    --border:rgba(0,229,176,.15);--border-strong:rgba(0,229,176,.45);
    --danger:#ff4d6d;--warning:#ffb830;
    --radius-sm:8px;--radius-md:14px;--radius-lg:20px;
    --shadow-glow:0 0 24px rgba(0,229,176,.18),0 4px 32px rgba(0,0,0,.6);
    --transition:all .3s cubic-bezier(.4,0,.2,1);
  }

  /* ── Page Header ── */
  .page-header{margin-bottom:32px;text-align:center}
  .page-title{font-family:'Orbitron',sans-serif;font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:6px}
  .page-title span{color:var(--accent)}
  .page-subtitle{font-size:13px;color:var(--text-secondary)}

  /* ── Upload Zone ── */
  .upload-zone{
    position:relative;border:2px dashed var(--border-strong);border-radius:var(--radius-lg);
    background:var(--bg-card);backdrop-filter:blur(12px);
    padding:44px 24px;display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:14px;cursor:pointer;transition:var(--transition);overflow:hidden;margin-bottom:28px;
  }
  .upload-zone::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse 60% 40% at 50% 50%,rgba(0,229,176,.04) 0%,transparent 70%);
    pointer-events:none;
  }
  .upload-zone:hover,.upload-zone.drag-over{
    border-color:var(--accent);background:var(--bg-card-hover);
    box-shadow:0 0 40px var(--accent-glow),inset 0 0 30px rgba(0,229,176,.04);
  }
  .upload-zone:hover .upload-icon-wrap,.upload-zone.drag-over .upload-icon-wrap{
    transform:translateY(-4px);box-shadow:0 0 30px var(--accent-glow);
  }
  .upload-icon-wrap{
    width:72px;height:72px;border-radius:18px;background:var(--accent-dim);
    border:1px solid var(--border-strong);display:flex;align-items:center;justify-content:center;
    transition:var(--transition);
  }
  .upload-icon-wrap svg{width:36px;height:36px;color:var(--accent)}
  .upload-title{font-size:17px;font-weight:700;color:var(--text-primary)}
  .upload-subtitle{font-size:12px;color:var(--text-secondary);text-align:center}
  .upload-types{display:flex;gap:8px;flex-wrap:wrap;justify-content:center}
  .upload-type-tag{
    padding:3px 10px;background:rgba(0,229,176,.08);border:1px solid var(--border);
    border-radius:20px;font-size:11px;color:var(--accent);font-weight:600;font-family:'Orbitron',sans-serif;
  }
  .upload-btn{
    padding:10px 24px;background:transparent;border:1px solid var(--accent);
    border-radius:var(--radius-sm);color:var(--accent);font-family:'Cairo',sans-serif;
    font-size:13px;font-weight:700;cursor:pointer;transition:var(--transition);
  }
  .upload-btn:hover{background:var(--accent);color:var(--bg-primary);box-shadow:0 0 20px var(--accent-glow)}
  #ecuFile{display:none}

  /* Progress */
  .upload-progress{width:100%;max-width:380px}
  .progress-bar-wrap{height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden;margin-bottom:6px}
  .progress-bar-fill{
    height:100%;width:0%;background:linear-gradient(90deg,var(--accent2),var(--accent));
    border-radius:2px;transition:width .4s ease;box-shadow:0 0 8px var(--accent-glow);
  }
  .progress-text{display:flex;justify-content:space-between;font-size:11px;color:var(--text-secondary)}
  .progress-filename{color:var(--accent);font-weight:600}

  /* ── Grid ── */
  .content-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
  @media(max-width:820px){.content-grid{grid-template-columns:1fr}}

  /* ── Glass Card ── */
  .glass-card{
    background:var(--bg-card);backdrop-filter:blur(16px);
    border:1px solid var(--border);border-radius:var(--radius-lg);
    padding:24px;transition:var(--transition);position:relative;overflow:hidden;
  }
  .glass-card::before{
    content:'';position:absolute;top:0;right:0;width:120px;height:120px;
    background:radial-gradient(circle,rgba(0,229,176,.06) 0%,transparent 70%);pointer-events:none;
  }
  .glass-card:hover{border-color:var(--border-strong);box-shadow:var(--shadow-glow)}

  .card-header-row{
    display:flex;align-items:center;gap:10px;margin-bottom:20px;
    padding-bottom:14px;border-bottom:1px solid var(--border);
  }
  .card-header-icon{
    width:34px;height:34px;border-radius:var(--radius-sm);background:var(--accent-dim);
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
  }
  .card-header-icon svg{width:18px;height:18px;color:var(--accent)}
  .card-header-title{font-size:14px;font-weight:700;color:var(--text-primary)}
  .card-header-sub{font-size:11px;color:var(--text-secondary);margin-top:1px}
  .card-header-badge{
    margin-right:auto;padding:3px 10px;background:rgba(0,229,176,.1);
    border:1px solid var(--border-strong);border-radius:20px;
    font-size:10px;color:var(--accent);font-weight:700;letter-spacing:.5px;
  }

  /* ── Car Info ── */
  .car-info-list{display:flex;flex-direction:column;gap:10px}
  .info-row{
    display:flex;align-items:center;justify-content:space-between;padding:10px 14px;
    background:rgba(255,255,255,.025);border-radius:var(--radius-sm);
    border:1px solid transparent;transition:var(--transition);
  }
  .info-row:hover{background:rgba(0,229,176,.05);border-color:var(--border)}
  .info-label{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:7px}
  .info-label-dot{width:6px;height:6px;border-radius:50%;background:var(--accent);opacity:.6;flex-shrink:0}
  .info-value{font-size:12px;font-weight:700;color:var(--text-primary);font-family:'Orbitron',sans-serif}
  .info-value.highlight{color:var(--accent)}
  .ecu-chip{
    display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
    background:rgba(0,201,245,.1);border:1px solid rgba(0,201,245,.3);
    border-radius:20px;font-size:10px;color:var(--accent2);font-weight:700;font-family:'Orbitron',sans-serif;
  }
  .confidence-wrap{
    display:flex;flex-direction:column;gap:4px;padding:12px 14px;
    background:rgba(0,229,176,.04);border:1px solid var(--border);
    border-radius:var(--radius-sm);margin-top:4px;
  }
  .confidence-header{display:flex;justify-content:space-between;font-size:11px;color:var(--text-secondary)}
  .confidence-header span:last-child{color:var(--accent);font-weight:700}
  .confidence-bar{height:5px;background:rgba(255,255,255,.06);border-radius:3px;overflow:hidden}
  .confidence-fill{height:100%;background:linear-gradient(90deg,var(--accent2),var(--accent));border-radius:3px;box-shadow:0 0 8px var(--accent-glow);transition:width 1.2s ease;width:0}

  /* ── Solutions ── */
  .solutions-list{display:flex;flex-direction:column;gap:9px;margin-bottom:20px}
  .solution-item{
    display:flex;align-items:center;gap:12px;padding:12px 14px;
    background:rgba(255,255,255,.025);border-radius:var(--radius-sm);
    border:1px solid transparent;cursor:pointer;transition:var(--transition);
    position:relative;overflow:hidden;user-select:none;
  }
  .solution-item::after{
    content:'';position:absolute;right:0;top:0;bottom:0;width:0;
    background:linear-gradient(90deg,transparent,rgba(0,229,176,.06));transition:width .3s ease;
  }
  .solution-item:hover{background:rgba(0,229,176,.05);border-color:var(--border)}
  .solution-item:hover::after{width:60px}
  .solution-item.checked{
    background:rgba(0,229,176,.07);border-color:rgba(0,229,176,.3);
    box-shadow:0 0 14px rgba(0,229,176,.08);
  }
  .custom-checkbox{
    width:20px;height:20px;border-radius:5px;border:2px solid var(--text-muted);
    background:transparent;display:flex;align-items:center;justify-content:center;
    flex-shrink:0;transition:var(--transition);
  }
  .solution-item.checked .custom-checkbox{background:var(--accent);border-color:var(--accent);box-shadow:0 0 10px var(--accent-glow)}
  .custom-checkbox svg{width:11px;height:11px;color:var(--bg-primary);opacity:0;transition:opacity .2s}
  .solution-item.checked .custom-checkbox svg{opacity:1}
  .solution-info{flex:1}
  .solution-name{font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:2px}
  .solution-desc{font-size:11px;color:var(--text-secondary)}
  .solution-meta{display:flex;flex-direction:column;align-items:flex-end;gap:4px}
  .solution-credits{font-family:'Orbitron',sans-serif;font-size:12px;font-weight:700;color:var(--accent)}
  .solution-credits span{font-size:9px;color:var(--text-secondary);font-family:'Cairo',sans-serif}
  .solution-tag{font-size:9px;padding:2px 7px;border-radius:10px;font-weight:700}
  .solution-tag.popular{background:rgba(255,184,48,.12);color:var(--warning);border:1px solid rgba(255,184,48,.25)}
  .solution-tag.new{background:rgba(0,229,176,.1);color:var(--accent);border:1px solid var(--border-strong)}
  .solution-tag.hot{background:rgba(255,77,109,.1);color:var(--danger);border:1px solid rgba(255,77,109,.25)}

  /* ── Cost & Button ── */
  .cost-summary{
    padding:14px;background:rgba(0,229,176,.05);
    border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:16px;
  }
  .cost-row{display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--text-secondary);margin-bottom:6px}
  .cost-divider{height:1px;background:var(--border);margin:8px 0}
  .cost-total-row{display:flex;justify-content:space-between;align-items:center}
  .cost-total-label{font-size:13px;font-weight:700;color:var(--text-primary)}
  .cost-total-value{font-family:'Orbitron',sans-serif;font-size:20px;font-weight:900;color:var(--accent);text-shadow:0 0 20px var(--accent-glow)}
  .cost-total-value span{font-size:11px;color:var(--text-secondary);font-family:'Cairo',sans-serif}

  .fix-btn{
    width:100%;padding:16px;background:linear-gradient(135deg,var(--accent),var(--accent2));
    border:none;border-radius:var(--radius-md);color:#0d0f14;
    font-family:'Cairo',sans-serif;font-size:15px;font-weight:900;cursor:pointer;
    transition:var(--transition);position:relative;overflow:hidden;
    box-shadow:0 0 30px var(--accent-glow),0 4px 20px rgba(0,0,0,.4);letter-spacing:.5px;
  }
  .fix-btn::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.15),transparent);opacity:0;transition:opacity .3s}
  .fix-btn:hover{transform:translateY(-2px);box-shadow:0 0 50px var(--accent-glow),0 8px 30px rgba(0,0,0,.5)}
  .fix-btn:hover::before{opacity:1}
  .fix-btn:active{transform:translateY(0)}
  .fix-btn:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none}
  .fix-btn-inner{display:flex;align-items:center;justify-content:center;gap:10px}
  .fix-btn-divider{width:1px;height:18px;background:rgba(0,0,0,.25)}

  .warning-note{
    display:flex;align-items:center;gap:8px;padding:10px 14px;margin-top:12px;
    background:rgba(255,184,48,.06);border:1px solid rgba(255,184,48,.2);
    border-radius:var(--radius-sm);font-size:11px;color:var(--text-secondary);
  }
  .warning-note svg{width:14px;height:14px;color:var(--warning);flex-shrink:0}

  /* Empty state */
  .empty-state{
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:36px 20px;gap:12px;opacity:.45;
  }
  .empty-state svg{width:44px;height:44px;color:var(--text-muted)}
  .empty-state p{font-size:13px;color:var(--text-secondary);text-align:center;line-height:1.6}

  /* Spinner */
  @keyframes cfSpin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  .cf-spin{animation:cfSpin 1s linear infinite}
</style>
@endsection

@section('content')

  <div class="page-header">
    <h1 class="page-title">معالج ملفات <span>ECU</span></h1>
    <p class="page-subtitle">ارفع ملف الـ ECU الخاص بك للكشف التلقائي عن الحلول المناسبة</p>
  </div>

  {{-- ═══ UPLOAD ZONE ═══ --}}
  <div class="upload-zone" id="uploadZone">
    <input type="file" id="ecuFile" accept=".bin"/>

    <div class="upload-icon-wrap" id="uploadIconWrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/>
        <path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/>
      </svg>
    </div>

    <p class="upload-title">اسحب وأفلت الـ ECU هنا</p>
    <p class="upload-subtitle">أو اضغط لاختيار الملف من جهازك — يدعم ملفات .bin</p>

    <div class="upload-types">
      <span class="upload-type-tag">.BIN</span>
      <span class="upload-type-tag">.HEX</span>
      <span class="upload-type-tag">.ORI</span>
      <span class="upload-type-tag">.MOD</span>
    </div>

    <button type="button" class="upload-btn" onclick="document.getElementById('ecuFile').click()">
      اختر ملف ECU
    </button>

    <div class="upload-progress" id="uploadProgress" style="display:none">
      <div class="progress-bar-wrap">
        <div class="progress-bar-fill" id="progressFill"></div>
      </div>
      <div class="progress-text">
        <span class="progress-filename" id="progressFilename">—</span>
        <span id="progressPercent">0%</span>
      </div>
    </div>
  </div>

  {{-- ═══ TWO-COLUMN GRID ═══ --}}
  <div class="content-grid">

    {{-- ── Car Info Card ── --}}
    <div class="glass-card">
      <div class="card-header-row">
        <div class="card-header-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="3" width="15" height="13" rx="2"/>
            <path d="M16 8h4l3 3v5h-7V8z"/>
            <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
          </svg>
        </div>
        <div>
          <div class="card-header-title">بيانات السيارة</div>
          <div class="card-header-sub">مستخرجة تلقائيًا من الملف</div>
        </div>
        <div class="card-header-badge" id="detectionBadge">⏳ بانتظار الملف</div>
      </div>

      <div class="empty-state" id="emptyCarInfo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/>
          <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
        <p>ارفع ملف ECU أولًا<br/>لتظهر هنا تفاصيل السيارة</p>
      </div>

      <div class="car-info-list" id="carInfoList" style="display:none">
        <div class="info-row">
          <span class="info-label"><span class="info-label-dot"></span>الماركة</span>
          <span class="info-value highlight" id="info-make">—</span>
        </div>
        <div class="info-row">
          <span class="info-label"><span class="info-label-dot"></span>الموديل</span>
          <span class="info-value" id="info-model">—</span>
        </div>
        <div class="info-row">
          <span class="info-label"><span class="info-label-dot"></span>نوع المحرك</span>
          <span class="info-value" id="info-ecu-type">—</span>
        </div>
        <div class="info-row">
          <span class="info-label"><span class="info-label-dot"></span>وحدة التحكم ECU</span>
          <span class="ecu-chip" id="info-hw">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/>
              <line x1="9" y1="2" x2="9" y2="4"/><line x1="15" y1="2" x2="15" y2="4"/>
              <line x1="9" y1="20" x2="9" y2="22"/><line x1="15" y1="20" x2="15" y2="22"/>
              <line x1="20" y1="9" x2="22" y2="9"/><line x1="20" y1="14" x2="22" y2="14"/>
              <line x1="2" y1="9" x2="4" y2="9"/><line x1="2" y1="14" x2="4" y2="14"/>
            </svg>
            <span id="info-hw-text">—</span>
          </span>
        </div>
        <div class="info-row">
          <span class="info-label"><span class="info-label-dot"></span>حجم الملف</span>
          <span class="info-value" id="info-size">—</span>
        </div>
        <div class="info-row">
          <span class="info-label"><span class="info-label-dot"></span>اسم الملف</span>
          <span class="info-value" id="info-filename" style="font-size:10px;letter-spacing:0">—</span>
        </div>
        <div class="confidence-wrap">
          <div class="confidence-header">
            <span>مستوى الثقة بالتطابق</span>
            <span id="confidenceVal">—</span>
          </div>
          <div class="confidence-bar">
            <div class="confidence-fill" id="confidenceFill"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Solutions Card ── --}}
    <div class="glass-card">
      <div class="card-header-row">
        <div class="card-header-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01z"/>
          </svg>
        </div>
        <div>
          <div class="card-header-title">الحلول المتاحة</div>
          <div class="card-header-sub">اختر التعديلات المطلوبة</div>
        </div>
        <div class="card-header-badge" id="selectedCount">0 محدد</div>
      </div>

      <div class="solutions-list" id="solutionsList">
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01z"/>
          </svg>
          <p>ارفع ملف ECU أولًا<br/>لتظهر الحلول المتاحة</p>
        </div>
      </div>

      <div class="cost-summary">
        <div class="cost-row">
          <span>الحلول المختارة</span>
          <span id="selectedSolutionsCount">—</span>
        </div>
        <div class="cost-row">
          <span>رصيدك الحالي</span>
          <span style="color:var(--accent)" id="currentCreditsDisplay">{{ auth()->user()->balance ?? 0 }} كريدت</span>
        </div>
        <div class="cost-divider"></div>
        <div class="cost-total-row">
          <span class="cost-total-label">إجمالي التكلفة</span>
          <span class="cost-total-value"><span id="totalCost">0</span> <span>كريدت</span></span>
        </div>
      </div>

      <button class="fix-btn" id="fixBtn" disabled>
        <div class="fix-btn-inner" id="fixBtnInner">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
          </svg>
          FIX FILE
          <div class="fix-btn-divider"></div>
          <span>إصلاح الملف</span>
        </div>
      </button>

      <div class="warning-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        سيتم خصم الكريدتات عند الضغط على إصلاح الملف. العملية غير قابلة للاسترداد.
      </div>
    </div>

  </div>{{-- end content-grid --}}

@endsection

@section('scripts')
<script>
/* ─────────────────────────────────
   State
───────────────────────────────── */
var userBalance  = {{ auth()->user()->balance ?? 0 }};
var sessionKey   = null;
var uploadedFile = null;

/* ─────────────────────────────────
   Upload Zone
───────────────────────────────── */
var zone  = document.getElementById('uploadZone');
var input = document.getElementById('ecuFile');

zone.addEventListener('click', function(e){
  if(e.target.classList.contains('upload-btn') || e.target === input) return;
  input.click();
});
zone.addEventListener('dragover',  function(e){ e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', function(){ zone.classList.remove('drag-over'); });
zone.addEventListener('drop', function(e){
  e.preventDefault(); zone.classList.remove('drag-over');
  if(e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]);
});
input.addEventListener('change', function(){ if(input.files[0]) handleFile(input.files[0]); });

function handleFile(file){
  uploadedFile = file;
  var progress = document.getElementById('uploadProgress');
  var fill     = document.getElementById('progressFill');
  var fname    = document.getElementById('progressFilename');
  var pct      = document.getElementById('progressPercent');

  progress.style.display = 'block';
  fname.textContent = file.name;
  document.getElementById('info-filename').textContent = file.name;
  document.getElementById('info-size').textContent     = formatBytes(file.size);

  var p = 0;
  var iv = setInterval(function(){
    p += Math.random() * 12;
    if(p >= 80){ p = 80; clearInterval(iv); sendToServer(file, fill, pct); }
    fill.style.width = p + '%';
    pct.textContent  = Math.round(p) + '%';
  }, 100);
}

/* ─────────────────────────────────
   Send to /user/detect  (AJAX)
───────────────────────────────── */
function sendToServer(file, fill, pct){
  var formData = new FormData();
  formData.append('file', file);
  formData.append('_token', '{{ csrf_token() }}');

  var xhr = new XMLHttpRequest();
  xhr.upload.addEventListener('progress', function(e){
    if(e.lengthComputable){
      var p2 = 80 + Math.round((e.loaded/e.total)*20);
      fill.style.width = p2 + '%';
      pct.textContent  = p2 + '%';
    }
  });
  xhr.open('POST', '{{ url("/user/detect") }}', true);
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xhr.setRequestHeader('Accept', 'application/json');
  xhr.onload = function(){
    fill.style.width = '100%'; pct.textContent = '100%';
    try {
      var res = JSON.parse(xhr.responseText);
      if(res.status){
        sessionKey = res.session;
        showDetectionResult(res.data, res.modifications, file);
      } else {
        cfShowToast('❌ ' + (res.message || 'تعذّر التعرف على الملف'), 'error');
      }
    } catch(ex){
      cfShowToast('❌ خطأ في الاستجابة من الخادم', 'error');
    }
  };
  xhr.onerror = function(){ cfShowToast('❌ تعذّر الاتصال بالخادم', 'error'); };
  xhr.send(formData);
}

/* ─────────────────────────────────
   Show detection result
───────────────────────────────── */
function showDetectionResult(data, modifications, file){
  document.getElementById('emptyCarInfo').style.display = 'none';
  document.getElementById('carInfoList').style.display  = 'flex';
  document.getElementById('detectionBadge').textContent  = '✓ تم التعرف';

  document.getElementById('info-make').textContent     = data.car_make   || '—';
  document.getElementById('info-model').textContent    = data.car_model  || '—';
  document.getElementById('info-ecu-type').textContent = data.ecu_type   || '—';
  document.getElementById('info-hw-text').textContent  = data.hw_sw_number || data.ecu_type || 'ECU';
  document.getElementById('info-size').textContent     = formatBytes(data.file_size || file.size);

  // Confidence
  var confMap = { exact_signature: 95, size_proximity: 65, low: 30 };
  var confVal = confMap[data.confidence] || 50;
  setTimeout(function(){
    document.getElementById('confidenceFill').style.width = confVal + '%';
    document.getElementById('confidenceVal').textContent  = confVal + '%';
  }, 300);

  // Populate solutions
  populateSolutions(modifications);

  cfShowToast('✅ تم التعرف على الملف بنجاح!');
  setStep(2);
}

/* ─────────────────────────────────
   Populate solutions list
───────────────────────────────── */
function populateSolutions(mods){
  var list = document.getElementById('solutionsList');

  if(!mods || mods.length === 0){
    list.innerHTML = '<div class="empty-state"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:44px;height:44px;color:var(--text-muted)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01z"/></svg><p>لا توجد حلول متاحة لهذا الـ ECU حاليًا</p></div>';
    updateCostSummary();
    return;
  }

  list.innerHTML = mods.map(function(m){
    return '<div class="solution-item" data-uuid="' + m.uuid + '" data-cost="1" onclick="toggleSolution(this)">' +
      '<div class="custom-checkbox"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="2 6 5 9 10 3"/></svg></div>' +
      '<div class="solution-info"><div class="solution-name">' + escHtml(m.module_name) + '</div><div class="solution-desc">تعديل تلقائي على ملف الـ ECU</div></div>' +
      '<div class="solution-meta"><div class="solution-credits">1 <span>كريدت</span></div></div>' +
    '</div>';
  }).join('');

  updateCostSummary();
}

/* ─────────────────────────────────
   Checkboxes & Cost
───────────────────────────────── */
function toggleSolution(el){
  el.classList.toggle('checked');
  updateCostSummary();
}

function updateCostSummary(){
  var checked = document.querySelectorAll('.solution-item.checked');
  var total = 0;
  checked.forEach(function(item){ total += parseInt(item.dataset.cost || 1); });

  document.getElementById('totalCost').textContent = total;
  document.getElementById('selectedCount').textContent = checked.length + ' محدد';
  document.getElementById('selectedSolutionsCount').textContent = checked.length > 0 ? checked.length + ' حل' : '—';

  document.getElementById('fixBtn').disabled = (checked.length === 0 || total > userBalance || !sessionKey);
}

/* ─────────────────────────────────
   FIX FILE  → POST /user/detect/{session}/apply
───────────────────────────────── */
document.getElementById('fixBtn').addEventListener('click', function(){
  var checked = document.querySelectorAll('.solution-item.checked');
  if(!sessionKey || checked.length === 0) return;

  var uuids = [];
  checked.forEach(function(el){ uuids.push(el.dataset.uuid); });
  var total = parseInt(document.getElementById('totalCost').textContent);

  var btn = document.getElementById('fixBtn');
  btn.disabled = true;
  document.getElementById('fixBtnInner').innerHTML =
    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="cf-spin"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> جاري المعالجة...';

  setStep(3);

  // Build form and submit to trigger file download
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ url("/user/detect") }}/' + sessionKey + '/apply';

  var tokenInput = document.createElement('input');
  tokenInput.type = 'hidden'; tokenInput.name = '_token'; tokenInput.value = '{{ csrf_token() }}';
  form.appendChild(tokenInput);

  uuids.forEach(function(uuid){
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'record_uuids[]'; inp.value = uuid;
    form.appendChild(inp);
  });

  document.body.appendChild(form);
  form.submit();

  setTimeout(function(){
    userBalance -= total;
    document.getElementById('currentCreditsDisplay').textContent = userBalance + ' كريدت';
    document.querySelector('.cf-credit-value').textContent = userBalance;
    cfShowToast('✅ تم تطبيق التعديلات! جاري التحميل...');
    resetFixBtn();
  }, 1500);
});

function resetFixBtn(){
  document.getElementById('fixBtnInner').innerHTML =
    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>FIX FILE<div class="fix-btn-divider"></div><span>إصلاح الملف</span>';
  updateCostSummary();
}

/* ─────────────────────────────────
   Steps
───────────────────────────────── */
function setStep(n){
  document.querySelectorAll('.cf-nav-step').forEach(function(el, i){
    el.classList.toggle('active', i + 1 === n);
  });
}

/* ─────────────────────────────────
   Helpers
───────────────────────────────── */
function formatBytes(bytes){
  if(!+bytes) return '0 Bytes';
  var k = 1024, sizes = ['Bytes','KB','MB','GB'];
  var i = Math.floor(Math.log(bytes)/Math.log(k));
  return parseFloat((bytes/Math.pow(k,i)).toFixed(2)) + ' ' + sizes[i];
}
function escHtml(s){
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

updateCostSummary();
</script>
@endsection
