@extends('portals.user.app')

@section('title')
    لوحة المستخدم
@endsection

@section('my_style')
    <style>
        .user-dashboard {
            min-height: 80vh;
        }

        .sidebar-card {
            border-radius: 22px;
            overflow: hidden;
        }

        .sidebar-card .card-body {
            padding: 2rem;
        }

        .sidebar-avatar {
            width: 88px;
            height: 88px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #5c7cfa 0%, #2dd4bf 100%);
            color: #fff;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .sidebar-nav .list-group-item {
            border: none;
            border-radius: 16px;
            margin-bottom: 0.75rem;
            padding: 1rem 1.25rem;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .sidebar-nav .list-group-item:hover {
            transform: translateY(-1px);
            background: rgba(35, 161, 242, 0.08);
        }

        .sidebar-nav .list-group-item.active {
            background: linear-gradient(135deg, #22c55e 0%, #14b8a6 100%);
            color: #fff;
            box-shadow: 0 18px 50px rgba(56, 189, 248, 0.18);
        }

        .content-card {
            border-radius: 24px;
            border: none;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
        }

        .tab-section {
            display: none;
        }

        .tab-section.active {
            display: block;
        }

        .info-row .label {
            font-weight: 700;
            color: #334155;
        }

        .status-pill {
            border-radius: 9999px;
            padding: 0.35rem 0.95rem;
            font-size: 0.85rem;
            font-weight: 700;
            background: rgba(34, 197, 94, 0.12);
            color: #166534;
        }

        .field-note {
            color: #64748b;
            font-size: 0.9rem;
        }

        .card-badge {
            border-radius: 16px;
            padding: 0.75rem 1rem;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
        }

        .solution-grid {
            gap: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="content-body user-dashboard px-3 py-4">
        <div class="row gy-4">
            <div class="col-xl-3 col-lg-4">
                <div class="card sidebar-card shadow-sm">
                    <div class="card-body text-center">
                        <div class="sidebar-avatar mx-auto">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <h4 class="mb-1 fw-bold">{{ auth()->user()->name }}</h4>
                        <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="card-badge">User</span>
                            <span class="card-badge">{{ auth()->user()->balance ?? 0 }} Credit</span>
                        </div>

                        <div class="list-group sidebar-nav text-start">
                            <a href="#" class="list-group-item active" data-tab="profileTab">
                                <i class="fa fa-user-circle me-2"></i> بروفايل
                            </a>
                            <a href="#" class="list-group-item" data-tab="solutionTab">
                                <i class="fa fa-cogs me-2"></i> ECU solution
                            </a>
                            <a href="{{ url('/user/file-processor') }}" class="list-group-item">
                                <i class="fa fa-upload me-2"></i> File Processor
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="card content-card p-4">
                    <div id="profileTab" class="tab-section active">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
                            <div>
                                <h3 class="mb-1 fw-bold">إعدادات البروفايل</h3>
                                <p class="text-muted mb-0">حرّك بياناتك الشخصية هنا، وغيّر اسمك أو بريدك أو كلمة المرور.</p>
                            </div>
                            <span class="status-pill bg-green/10 text-green-700">مرحّباً بك</span>
                        </div>

                        <form id="profileForm" method="POST" action="{{ url('/user/profile') }}">
                            @csrf
                            @method('PUT')
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">الاسم</label>
                                    <input type="text" name="name" class="form-control rounded-3" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control rounded-3" value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">رقم الهاتف</label>
                                    <input type="text" name="mobile" class="form-control rounded-3" value="{{ auth()->user()->mobile ?? '' }}">
                                    <div class="field-note">يمكنك تحديث رقم الهاتف هنا إذا كنت تريد.</div>
                                </div>
                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ التعديلات</button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-5">

                        <div>
                            <h5 class="fw-bold mb-3">تغيير كلمة المرور</h5>
                            <form id="passwordForm" method="POST" action="{{ url('/user/password') }}">
                                @csrf
                                @method('PUT')
                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">كلمة المرور الحالية</label>
                                        <input type="password" name="current_password" class="form-control rounded-3" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">كلمة المرور الجديدة</label>
                                        <input type="password" name="password" class="form-control rounded-3" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">تأكيد كلمة المرور</label>
                                        <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                                    </div>
                                    <div class="col-12 text-end mt-2">
                                        <button type="submit" class="btn btn-outline-primary rounded-pill px-4">تحديث كلمة المرور</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="solutionTab" class="tab-section">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
                            <div>
                                <h3 class="mb-1 fw-bold">ECU solution</h3>
                                <p class="text-muted mb-0">هنا يظهر برنامج الحل الخاص بك مع تفاصيل البراند والوحدة والملف.</p>
                            </div>
                            <a href="{{ url('/user/file-processor') }}" class="btn btn-success rounded-pill px-4">
                                فتح الملف
                            </a>
                        </div>

                        <div class="row row-cols-1 row-cols-md-2 solution-grid">
                            <div class="col">
                                <div class="card rounded-4 p-4 h-100 border-0 bg-slate-50">
                                    <p class="mb-2 text-muted">Brand</p>
                                    <h5 class="fw-bold">Hyundai</h5>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card rounded-4 p-4 h-100 border-0 bg-slate-50">
                                    <p class="mb-2 text-muted">Model</p>
                                    <h5 class="fw-bold">EDC17CP14</h5>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card rounded-4 p-4 h-100 border-0 bg-slate-50">
                                    <p class="mb-2 text-muted">Fixed</p>
                                    <h5 class="fw-bold text-success">Yes</h5>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card rounded-4 p-4 h-100 border-0 bg-slate-50">
                                    <p class="mb-2 text-muted">Uploaded file</p>
                                    <h5 class="fw-bold">MagicSolution.bin</h5>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 rounded-4 border border-dashed border-primary bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">ملخص البرنامج</h6>
                                    <p class="mb-0 text-muted">يمكنك عرض تفاصيل الملف المرفوع والحل هنا مع أي تحديث جديد.</p>
                                </div>
                                <span class="badge bg-primary me-2">ECU Solution</span>
                            </div>
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Brand</span>
                                        <strong>Hyundai</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Model</span>
                                        <strong>EDC17CP14</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Fixed</span>
                                        <strong class="text-success">Done</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Uploaded file</span>
                                        <strong>origin.bin</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.querySelectorAll('.sidebar-nav .list-group-item').forEach(function (item) {
            item.addEventListener('click', function (event) {
                event.preventDefault();
                document.querySelectorAll('.sidebar-nav .list-group-item').forEach(function (link) {
                    link.classList.remove('active');
                });
                item.classList.add('active');
                const target = item.getAttribute('data-tab');
                if (target) {
                    document.querySelectorAll('.tab-section').forEach(function (section) {
                        section.classList.remove('active');
                    });
                    document.getElementById(target).classList.add('active');
                }
            });
        });
    </script>
@endsection