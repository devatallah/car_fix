@extends('portals.user.app')

@section('title')
    File Processor
@endsection

@section('my_style')
    <style>
        .file-processor-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .processor-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .processor-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1rem;
        }

        .processor-header p {
            font-size: 1.1rem;
            color: #555;
            max-width: 600px;
            margin: 0 auto;
        }

        .main-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 24px 24px 0 0;
        }

        .card-header-custom h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .card-body-custom {
            padding: 2.5rem;
        }

        .form-group-custom {
            margin-bottom: 2rem;
        }

        .form-label-custom {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1rem;
        }

        .form-select-custom,
        .form-control-custom {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid #e0e7ff;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Cairo', sans-serif;
        }

        .form-select-custom:focus,
        .form-control-custom:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-select-custom:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .file-drop-zone {
            border: 2px dashed #667eea;
            border-radius: 16px;
            padding: 2.5rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .file-drop-zone:hover {
            border-color: #764ba2;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
        }

        .file-drop-zone.dragging {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
        }

        .upload-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .upload-text {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .upload-hint {
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .btn-submit-custom {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .btn-submit-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .btn-submit-custom:active {
            transform: translateY(0);
        }

        .btn-submit-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .helper-text {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .alert-box {
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: none;
        }

        .alert-success-custom {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(56, 142, 60, 0.1) 100%);
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, rgba(244, 67, 54, 0.1) 0%, rgba(211, 47, 47, 0.1) 100%);
            border-left: 4px solid #f44336;
            color: #c62828;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.1) 0%, rgba(25, 103, 210, 0.1) 100%);
            border-left: 4px solid #2196f3;
            color: #1565c0;
        }

        .spinner {
            display: none;
            margin-top: 0.5rem;
        }

        .spinner.show {
            display: inline-block;
        }

        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .step-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .step-card:hover {
            border-color: #667eea;
            background: #f0f3ff;
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .step-card h6 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .step-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin: 0;
        }

        .file-info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            margin-top: 1rem;
        }

        .file-info-box strong {
            color: #2c3e50;
        }

        .file-info-box span {
            color: #667eea;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
<div class="file-processor-page">
    <div class="container">
        <div class="processor-header">
            <h1>معالجة ملفات ECU</h1>
            <p>اختر البراند والوحدة، ثم ارفع ملفك لمعالجته تلقائياً بأعلى جودة</p>
        </div>

        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h6>اختر البراند</h6>
                <p>حدد العلامة التجارية للسيارة</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h6>اختر الوحدة</h6>
                <p>حدد ECU المناسب</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h6>اختر الحل</h6>
                <p>حدد الحل المطلوب</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h6>ارفع الملف</h6>
                <p>ارفع ملفك الأصلي</p>
            </div>
        </div>

        <div class="main-card">
            <div class="card-header-custom">
                <h4><i class="fa fa-cloud-upload me-2"></i> معالجة ملف ECU</h4>
            </div>
            <div class="card-body-custom">
                <form id="processFileForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group-custom">
                                <label for="brand" class="form-label-custom">البراند</label>
                                <select id="brand" class="form-select-custom" required>
                                    <option value="">-- اختر البراند --</option>
                                </select>
                                <div class="spinner" id="brandLoader">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </div>
                                <p class="helper-text">اختر العلامة التجارية الخاصة بسيارتك</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-custom">
                                <label for="ecu" class="form-label-custom">وحدة التحكم (ECU)</label>
                                <select id="ecu" class="form-select-custom" required disabled>
                                    <option value="">-- اختر ECU --</option>
                                </select>
                                <div class="spinner" id="ecuLoader">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </div>
                                <p class="helper-text">حدد وحدة التحكم الخاصة بسيارتك</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-custom">
                                <label for="solution" class="form-label-custom">الحل المتاح</label>
                                <select id="solution" class="form-select-custom" required disabled>
                                    <option value="">-- اختر الحل --</option>
                                </select>
                                <div class="spinner" id="solutionLoader">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </div>
                                <p class="helper-text">اختر الحل المناسب للمعالجة</p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group-custom">
                                <label for="file" class="form-label-custom">ملف ECU الأصلي</label>
                                <div class="file-drop-zone" id="fileDropZone">
                                    <div class="upload-icon"><i class="fa fa-file-arrow-down"></i></div>
                                    <div class="upload-text">اسحب الملف هنا أو اختر من جهازك</div>
                                    <div class="upload-hint">ملفات مدعومة: .bin، .hex، .img | الحد الأقصى: 10 MB</div>
                                </div>
                                <input type="file" id="file" class="d-none" required accept=".bin,.hex,.img">
                                <div id="fileInfo" class="file-info-box d-none">
                                    <strong>الملف:</strong> <span id="fileName"></span> <br>
                                    <strong>الحجم:</strong> <span id="fileSize"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-custom" id="submitBtn">
                        <i class="fa fa-play me-2"></i> معالجة الملف الآن
                    </button>
                </form>

                <div id="resultBox" class="alert-box alert-success-custom d-none">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-check-circle me-2 mt-1" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1">
                            <h5 class="mb-2"><i class="fa fa-check me-2"></i> تمت المعالجة بنجاح</h5>
                            <p class="mb-2" id="resultMessage"></p>
                            <a href="#" id="downloadLink" class="btn btn-sm" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); color: white; border-radius: 8px; padding: 0.5rem 1rem; text-decoration: none;">
                                <i class="fa fa-download me-1"></i> تحميل الآن
                            </a>
                        </div>
                    </div>
                </div>

                <div id="errorBox" class="alert-box alert-danger-custom d-none">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-exclamation-circle me-2 mt-1" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1">
                            <h5 class="mb-2"><i class="fa fa-exclamation me-2"></i> حدث خطأ</h5>
                            <p class="mb-0" id="errorMessage"></p>
                        </div>
                    </div>
                </div>

                <div id="infoBox" class="alert-box alert-info-custom d-none">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-info-circle me-2 mt-1" style="font-size: 1.5rem;"></i>
                        <div class="flex-grow-1">
                            <h5 class="mb-2"><i class="fa fa-lightbulb me-2"></i> معلومات المعالجة</h5>
                            <p class="mb-0">سيتم تطبيق الحل المختار على الملف الأصلي مباشرة، وستحصل على النسخة المعدلة جاهزة للتحميل.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 bg-gradient-success text-white rounded-3 p-3">
                            <i class="fas fa-rocket fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">خطوات سريعة</h5>
                            <p class="text-muted mb-0">واجهة مرنة لتسريع عملية التعديل.</p>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-transparent px-0 py-3 border-0">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-primary rounded-circle me-3">1</span>
                                <div>
                                    <h6 class="mb-1">اختر العلامة التجارية</h6>
                                    <small class="text-muted">ابدأ باختيار البراند المناسب.</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent px-0 py-3 border-0">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-info rounded-circle me-3">2</span>
                                <div>
                                    <h6 class="mb-1">اختر وحدة التحكم (ECU)</h6>
                                    <small class="text-muted">حدد ECU الخاص بسيارتك.</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent px-0 py-3 border-0">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-warning text-dark rounded-circle me-3">3</span>
                                <div>
                                    <h6 class="mb-1">اختر الحل و ارفع الملف</h6>
                                    <small class="text-muted">الآن قم برفع ملف ECU ليتم معالجته.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-4 border border-dashed border-secondary bg-white">
                        <h6 class="fw-bold mb-2">مميزات المعالجة</h6>
                        <ul class="list-unstyled mb-0 text-muted">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>حفظ جودة الملف الأصلي</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>تحميل مباشر بعد الإكمال</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>دعم تنسيقات .bin و .hex و .img</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header p-4 bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-1"><i class="fas fa-cloud-upload-alt me-2"></i> معالجة ملف ECU</h4>
                            <p class="mb-0 opacity-75">واجهة محسّنة بألوان جديدة وتجربة استخدام أسرع.</p>
                        </div>
                        <span class="badge bg-white text-primary py-2 px-3 shadow-sm">جاهز الآن</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="processFileForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="brand" class="form-label fw-bold text-dark">اختر العلامة التجارية</label>
                                <select id="brand" class="form-select form-control rounded-4 shadow-sm" required>
                                    <option value="">-- اختر البراند --</option>
                                </select>
                                <div class="text-muted small mt-2">ماكينة التعديل تعمل بدقة على حسب العلامة.</div>
                                <div class="spinner-border spinner-border-sm d-none mt-2" id="brandLoader" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="ecu" class="form-label fw-bold text-dark">اختر ECU</label>
                                <select id="ecu" class="form-select form-control rounded-4 shadow-sm" required disabled>
                                    <option value="">-- اختر ECU أولاً --</option>
                                </select>
                                <div class="spinner-border spinner-border-sm d-none mt-2" id="ecuLoader" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="solution" class="form-label fw-bold text-dark">اختر الحل المتاح</label>
                                <select id="solution" class="form-select form-control rounded-4 shadow-sm" required disabled>
                                    <option value="">-- اختر ECU أولاً --</option>
                                </select>
                                <div class="text-muted small mt-2">الحل المناسب سيسهل المعالجة بدون أخطاء.</div>
                                <div class="spinner-border spinner-border-sm d-none mt-2" id="solutionLoader" role="status">
                                    <span class="visually-hidden">جاري التحميل...</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="file" class="form-label fw-bold text-dark">رفع ملف ECU الأصلي</label>
                                <div class="border rounded-4 p-3 bg-white d-flex align-items-center justify-content-between shadow-sm file-drop-zone">
                                    <div>
                                        <div class="fw-semibold mb-1">اسحب الملف هنا أو اختر من جهازك</div>
                                        <div class="text-muted">مدعوم: .bin, .hex, .img | حتى 10 MB</div>
                                    </div>
                                    <div class="btn btn-outline-primary rounded-pill">
                                        <i class="fas fa-file-upload me-2"></i> اختر ملف
                                    </div>
                                </div>
                                <input type="file" id="file" class="form-control d-none" required accept=".bin,.hex,.img">
                                <div id="fileInfo" class="alert alert-info mt-3 d-none rounded-4">
                                    <strong>الملف:</strong> <span id="fileName"></span> <br>
                                    <strong>الحجم:</strong> <span id="fileSize"></span>
                                </div>
                            </div>
                        </div>

                        <div id="infoBox" class="alert alert-primary mt-4 rounded-4 shadow-sm" role="alert" style="display: none;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lightbulb fa-2x me-3 mt-1"></i>
                                <div>
                                    <h5 class="mb-2 fw-bold">كيف يعمل النظام</h5>
                                    <p class="mb-0 text-muted">سيتم تطبيق الحل المختار على الملف الأصلي، ثم ستحصل على نسخة معدلة جاهزة للتحميل.</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient-success btn-lg w-100 mt-3 rounded-pill" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i> معالجة الملف
                        </button>
                    </form>

                    <div id="resultBox" class="alert alert-success mt-4 rounded-4 shadow-sm" role="alert" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="alert-heading mb-1"><i class="fas fa-check-circle me-2"></i> تمت المعالجة بنجاح</h5>
                                <p class="mb-1 text-muted" id="resultMessage"></p>
                            </div>
                            <a href="#" id="downloadLink" class="btn btn-primary btn-sm rounded-pill" download>
                                <i class="fas fa-download me-1"></i> تحميل الآن
                            </a>
                        </div>
                    </div>

                    <div id="errorBox" class="alert alert-danger mt-4 rounded-4 shadow-sm" role="alert" style="display: none;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-circle fa-2x me-3 mt-1"></i>
                            <div>
                                <h5 class="alert-heading mb-1">حدث خطأ</h5>
                                <p class="mb-0" id="errorMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0066ff 0%, #00d1ff 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #8ddc86 100%);
    }
    .btn-gradient-success {
        background: linear-gradient(135deg, #198754 0%, #47d17a 100%);
        border: none;
        color: #fff;
    }
    .btn-gradient-success:hover {
        background: linear-gradient(135deg, #145c38 0%, #36a964 100%);
    }
    .file-drop-zone {
        cursor: pointer;
        transition: all 0.25s ease;
        min-height: 120px;
    }
    .file-drop-zone:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 45px rgba(13, 110, 253, 0.1);
    }
    .form-select,
    .form-control {
        min-height: 52px;
        border-radius: 18px;
    }
    .form-select:focus,
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.18);
    }
    .badge.bg-gradient-primary {
        letter-spacing: 0.5px;
    }
    .border-dashed {
        border-style: dashed !important;
    }
</style>

<script>
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const apiUrl = '{{ config("app.url") }}/api';

    document.addEventListener('DOMContentLoaded', function() {
        loadBrands();

        const dropZone = document.querySelector('.file-drop-zone');
        const fileInput = document.getElementById('file');

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-primary');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    });

    function loadBrands() {
        showLoader('brandLoader');
        fetch(`${apiUrl}/user/portal/brands`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            hideLoader('brandLoader');
            if (data.success) {
                const select = document.getElementById('brand');
                select.innerHTML = '<option value="">-- اختر البراند --</option>';
                data.data.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.uuid;
                    option.textContent = brand.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(err => {
            hideLoader('brandLoader');
            showError('خطأ في جلب البراندات');
        });
    }

    document.getElementById('brand').addEventListener('change', function() {
        const brandUuid = this.value;
        if (!brandUuid) {
            document.getElementById('ecu').disabled = true;
            document.getElementById('ecu').innerHTML = '<option value="">-- اختر البراند أولاً --</option>';
            return;
        }

        showLoader('ecuLoader');
        fetch(`${apiUrl}/user/portal/ecus`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ brand_uuid: brandUuid })
        })
        .then(r => r.json())
        .then(data => {
            hideLoader('ecuLoader');
            if (data.success) {
                const select = document.getElementById('ecu');
                select.innerHTML = '<option value="">-- اختر ECU --</option>';
                select.disabled = false;
                data.data.forEach(ecu => {
                    const option = document.createElement('option');
                    option.value = ecu.uuid;
                    option.textContent = ecu.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(err => {
            hideLoader('ecuLoader');
            showError('خطأ في جلب ECU');
        });
    });

    document.getElementById('ecu').addEventListener('change', function() {
        const ecuUuid = this.value;
        if (!ecuUuid) {
            document.getElementById('solution').disabled = true;
            document.getElementById('solution').innerHTML = '<option value="">-- اختر ECU أولاً --</option>';
            return;
        }

        showLoader('solutionLoader');
        fetch(`${apiUrl}/user/portal/solutions`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ecu_uuid: ecuUuid })
        })
        .then(r => r.json())
        .then(data => {
            hideLoader('solutionLoader');
            if (data.success) {
                const select = document.getElementById('solution');
                select.innerHTML = '<option value="">-- اختر الحل --</option>';
                select.disabled = false;
                data.data.forEach(solution => {
                    const option = document.createElement('option');
                    option.value = solution.uuid;
                    option.textContent = solution.name + (solution.description ? ` - ${solution.description}` : '');
                    select.appendChild(option);
                });
            }
        })
        .catch(err => {
            hideLoader('solutionLoader');
            showError('خطأ في جلب الحلول');
        });
    });

    document.getElementById('file').addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            document.getElementById('fileInfo').classList.remove('d-none');
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
        }
    });

    document.getElementById('processFileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const file = document.getElementById('file').files[0];
        const solutionUuid = document.getElementById('solution').value;

        if (!file || !solutionUuid) {
            showError('يرجى اختيار الملف والحل');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('solution_template_uuid', solutionUuid);

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

        fetch(`${apiUrl}/user/portal/process-file`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> معالجة الملف';

            if (data.success) {
                document.getElementById('resultMessage').textContent = data.message;
                document.getElementById('downloadLink').href = data.download_url;
                document.getElementById('resultBox').style.display = 'block';
                document.getElementById('errorBox').style.display = 'none';
            } else {
                showError(data.message);
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i> معالجة الملف';
            showError('خطأ في معالجة الملف: ' + err.message);
        });
    });

    function showLoader(id) {
        document.getElementById(id).classList.remove('d-none');
    }

    function hideLoader(id) {
        document.getElementById(id).classList.add('d-none');
    }

    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorBox').style.display = 'block';
        document.getElementById('resultBox').style.display = 'none';
    }
</script>
@endsection