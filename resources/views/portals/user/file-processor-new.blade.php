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

        .detection-box {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 16px;
            margin-top: 2rem;
            display: none;
            border-left: 4px solid #667eea;
        }

        .detection-box.show {
            display: block;
        }

        .detection-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px solid #e0e7ff;
            text-align: center;
        }

        .info-label {
            font-size: 0.85rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #667eea;
        }

        .solutions-section {
            margin-top: 2rem;
        }

        .solutions-section h6 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .solution-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .solution-btn {
            padding: 1rem;
            border: 2px solid #e0e7ff;
            background: white;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
        }

        .solution-btn:hover {
            border-color: #667eea;
            background: #f0f3ff;
            transform: translateY(-2px);
        }

        .solution-btn.selected {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
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

        .btn-submit-custom:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .btn-submit-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert-box {
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: none;
            display: none;
        }

        .alert-box.show {
            display: block;
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

        .spinner-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .spinner-overlay.show {
            display: flex;
        }

        .file-info-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            margin-top: 1rem;
            display: none;
        }

        .file-info-box.show {
            display: block;
        }
    </style>
@endsection

@section('content')
<div class="file-processor-page">
    <div class="container">
        <div class="processor-header">
            <h1>معالجة ملفات ECU</h1>
            <p>ارفع ملفك فقط، وسيتم الكشف التلقائي عن البراند والحل المناسب</p>
        </div>

        <div class="main-card">
            <div class="card-header-custom">
                <h4><i class="fa fa-cloud-upload me-2"></i> معالجة ملف ECU</h4>
            </div>
            <div class="card-body-custom">
                <form id="processFileForm" enctype="multipart/form-data">
                    @csrf

                    <!-- File Upload -->
                    <div class="form-group-custom">
                        <label for="file" class="form-label-custom">ملف ECU الأصلي</label>
                        <div class="file-drop-zone" id="fileDropZone">
                            <div class="upload-icon"><i class="fa fa-file-arrow-down"></i></div>
                            <div class="upload-text">اسحب الملف هنا أو اختر من جهازك</div>
                            <div class="upload-hint">ملفات مدعومة: .bin، .hex، .img | الحد الأقصى: 10 MB</div>
                        </div>
                        <input type="file" id="file" class="d-none" required accept=".bin,.hex,.img">
                        <div id="fileInfo" class="file-info-box">
                            <strong>الملف:</strong> <span id="fileName"></span> <br>
                            <strong>الحجم:</strong> <span id="fileSize"></span>
                        </div>
                    </div>

                    <!-- Detection Results -->
                    <div id="detectionBox" class="detection-box">
                        <h6 style="margin-bottom: 1.5rem; font-weight: 700; color: #2c3e50;">
                            <i class="fa fa-check-circle me-2" style="color: #4caf50;"></i> تم الكشف التلقائي
                        </h6>

                        <div class="detection-info">
                            <div class="info-card">
                                <div class="info-label">البراند</div>
                                <div class="info-value" id="detectedBrand">-</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">ECU</div>
                                <div class="info-value" id="detectedEcu">-</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">حجم الملف</div>
                                <div class="info-value" id="detectedSize">-</div>
                            </div>
                        </div>

                        <div class="solutions-section">
                            <h6>اختر الحل المطلوب</h6>
                            <div class="solution-grid" id="solutionsGrid">
                                <!-- Solutions will be populated here -->
                            </div>
                        </div>

                        <input type="hidden" id="selectedModule" name="module_uuid">
                        <input type="hidden" id="selectedEcu" name="ecu_uuid">
                        <input type="hidden" id="selectedBrand" name="brand_uuid">

                        <button type="submit" class="btn-submit-custom" id="submitBtn">
                            <i class="fa fa-play me-2"></i> معالجة الملف الآن
                        </button>
                    </div>

                    <!-- Success Alert -->
                    <div id="resultBox" class="alert-box alert-success-custom">
                        <div class="d-flex align-items-start">
                            <i class="fa fa-check-circle me-2 mt-1" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <h5 class="mb-2"><i class="fa fa-check me-2"></i> تمت المعالجة بنجاح</h5>
                                <p class="mb-2" id="resultMessage"></p>
                                <a href="#" id="downloadLink" class="btn btn-sm" style="background: #4caf50; color: white; border-radius: 8px; padding: 0.5rem 1rem; text-decoration: none;">
                                    <i class="fa fa-download me-1"></i> تحميل الآن
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div id="errorBox" class="alert-box alert-danger-custom">
                        <div class="d-flex align-items-start">
                            <i class="fa fa-exclamation-circle me-2 mt-1" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <h5 class="mb-2"><i class="fa fa-exclamation me-2"></i> حدث خطأ</h5>
                                <p class="mb-0" id="errorMessage"></p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="spinnerOverlay" class="spinner-overlay">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">جاري المعالجة...</span>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('fileDropZone');
    const fileInput = document.getElementById('file');
    let detectedData = null;
    let currentSession = null;

    // File drop zone
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragging');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragging');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragging');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (!file) return;

        // Show file info
        document.getElementById('fileInfo').classList.add('show');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

        // Send to detection API
        detectFile(file);
    }

    function detectFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        showSpinner();

        fetch('{{ url("/user/detect") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            hideSpinner();
            console.log('Detection response:', data);
            if (data.status && data.data) {
                currentSession = data.session;
                const modifications = data.modifications || [];
                const detectionData = {
                    car_make: data.data.car_make,
                    car_model: data.data.car_model,
                    ecu_type: data.data.ecu_type,
                    ecu_uuid: data.data.ecu_uuid,
                    brand_uuid: data.data.brand_uuid,
                    file_size: data.data.file_size,
                };
                displayDetectionResults(detectionData, modifications);
            } else {
                showError(data.message || 'لم يتم التعرف على الملف. يرجى المحاولة مرة أخرى.');
            }
        })
        .catch(err => {
            hideSpinner();
            console.error('Detection error:', err);
            showError('خطأ في الاتصال: ' + err.message);
        });
    }

    function displayDetectionResults(detection, solutions) {
        detectedData = detection;

        // Display detection info
        document.getElementById('detectedBrand').textContent = detection.car_make || '-';
        document.getElementById('detectedEcu').textContent = detection.car_model || detection.ecu_type || '-';
        document.getElementById('detectedSize').textContent = detection.file_size ? (detection.file_size / 1024 / 1024).toFixed(2) + ' MB' : '-';

        // Set hidden inputs
        document.getElementById('selectedEcu').value = detection.ecu_uuid || '';
        document.getElementById('selectedBrand').value = detection.brand_uuid || '';

        // Display available solutions
        const grid = document.getElementById('solutionsGrid');
        grid.innerHTML = '';

        if (solutions && solutions.length > 0) {
            solutions.forEach((solution, index) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'solution-btn';
                btn.innerHTML = `<i class="fa fa-wrench me-1"></i> ${solution.module_name || solution.name || 'Solution ' + (index + 1)}`;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    selectSolution(btn, solution);
                });
                grid.appendChild(btn);
            });
        } else {
            grid.innerHTML = '<p class="text-muted col-12 text-center">لا توجد حلول متاحة لهذا الملف</p>';
        }

        document.getElementById('detectionBox').classList.add('show');
    }

    function selectSolution(btn, solution) {
        document.querySelectorAll('.solution-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('selectedModule').value = solution.uuid;
    }

    // Form submission
    document.getElementById('processFileForm').addEventListener('submit', (e) => {
        e.preventDefault();

        if (!document.getElementById('selectedModule').value) {
            showError('يرجى اختيار حل من الحلول المتاحة');
            return;
        }

        if (!currentSession) {
            showError('انتهت الجلسة، يرجى رفع الملف مرة أخرى');
            return;
        }

        const formData = new FormData();
        formData.append('record_uuids[]', document.getElementById('selectedModule').value);

        showSpinner();

        fetch(`{{ url("/user/detect") }}/${currentSession}/apply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        })
        .then(response => {
            const contentType = response.headers.get('Content-Type') || '';
            if (contentType.includes('application/octet-stream')) {
                const disposition = response.headers.get('Content-Disposition') || '';
                let filename = 'patched.bin';
                const match = disposition.match(/filename="(.+?)"/);
                if (match) filename = match[1];
                return response.blob().then(blob => ({ type: 'file', blob, filename }));
            }
            return response.json().then(data => ({ type: 'json', data }));
        })
        .then(result => {
            hideSpinner();
            if (result.type === 'file') {
                const url = URL.createObjectURL(result.blob);
                document.getElementById('downloadLink').href = url;
                document.getElementById('downloadLink').download = result.filename;
                showSuccess(result.filename);
            } else {
                showError(result.data.message || 'حدث خطأ في المعالجة');
            }
        })
        .catch(err => {
            hideSpinner();
            showError('خطأ: ' + err.message);
        });
    });

    function showSuccess(filename) {
        hideAlerts();
        document.getElementById('resultMessage').textContent = 'الملف جاهز للتحميل: ' + filename;
        document.getElementById('resultBox').classList.add('show');
    }

    function showError(message) {
        hideAlerts();
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorBox').classList.add('show');
    }

    function hideAlerts() {
        document.getElementById('resultBox').classList.remove('show');
        document.getElementById('errorBox').classList.remove('show');
    }

    function showSpinner() {
        document.getElementById('spinnerOverlay').classList.add('show');
    }

    function hideSpinner() {
        document.getElementById('spinnerOverlay').classList.remove('show');
    }
});
</script>
@endsection
