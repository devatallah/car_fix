@extends('portals.user.app')

@section('title')
    ECU Detected - Select Modifications
@endsection

@section('my_style')
    <style>
        html .content.app-content { padding: 0; }

        .results-container {
            max-width: 750px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .ecu-info-card .badge-confidence {
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
        }

        .ecu-info-table td:first-child {
            color: #888;
            width: 160px;
            font-size: 0.85rem;
        }

        .ecu-info-table td:last-child {
            font-weight: 600;
            color: #ddd;
        }

        .mod-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid #333;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .mod-item:hover { border-color: #5fdd9a; background: rgba(95,221,154,0.05); }

        .mod-item.selected {
            border-color: #5fdd9a;
            background: rgba(95,221,154,0.1);
        }

        .mod-item input[type="checkbox"] {
            width: 1.2rem;
            height: 1.2rem;
            accent-color: #5fdd9a;
        }

        .mod-label { font-weight: 600; color: #ddd; }

        .apply-btn {
            width: 100%;
            padding: 0.8rem;
            font-size: 1.1rem;
            background: #5fdd9a;
            border: none;
            color: #1a1a2e;
            font-weight: 700;
            border-radius: 8px;
            margin-top: 1.5rem;
        }

        .apply-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .apply-btn:hover:not(:disabled) { background: #4acf8a; }

        .no-mods { color: #888; text-align: center; padding: 2rem; }

        .progress-wrap {
            display: none;
            margin-top: 1rem;
        }
    </style>
@endsection

@section('content')
<div class="results-container">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <img src="{{ asset('landing/assets/images/landpage/svg/logo3.svg') }}" alt="logo" height="50">
        <a href="{{ url('/user/detect') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> Upload New File
        </a>
    </div>

    {{-- ECU Info Card --}}
    <div class="card ecu-info-card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-check-circle text-success mr-1"></i> ECU Identified</h5>
            @if(($detection['confidence'] ?? 'high') === 'high')
                <span class="badge badge-success badge-confidence">High Confidence</span>
            @else
                <span class="badge badge-warning badge-confidence">Low Confidence (size match only)</span>
            @endif
        </div>
        <div class="card-body">
            <table class="ecu-info-table" style="width:100%">
                <tr>
                    <td>File</td>
                    <td>{{ $sessionData['file_name'] }} ({{ number_format($sessionData['file_size'] / 1024 / 1024, 2) }} MB)</td>
                </tr>
                @if($detection['car_make'])
                <tr>
                    <td>Make</td>
                    <td>{{ $detection['car_make'] }}</td>
                </tr>
                @endif
                @if($detection['car_model'])
                <tr>
                    <td>Model</td>
                    <td>{{ $detection['car_model'] }}</td>
                </tr>
                @endif
                @if($detection['year_range'])
                <tr>
                    <td>Year</td>
                    <td>{{ $detection['year_range'] }}</td>
                </tr>
                @endif
                @if($detection['ecu_type'])
                <tr>
                    <td>ECU Type</td>
                    <td>{{ $detection['ecu_type'] }}</td>
                </tr>
                @endif
                @if($detection['hw_sw_number'])
                <tr>
                    <td>HW/SW</td>
                    <td>{{ $detection['hw_sw_number'] }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Modifications --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Select Modifications</h5>
        </div>
        <div class="card-body">
            @if($modifications->isEmpty())
                <div class="no-mods">
                    <i class="fa fa-info-circle" style="font-size:2rem;color:#555;"></i>
                    <p class="mt-2">No script-based modifications available for this ECU yet.</p>
                    <small class="text-muted">Ask your admin to upload .magicsscript files for this ECU.</small>
                </div>
            @else
                @foreach($modifications as $mod)
                <div class="mod-item" onclick="toggleMod(this, '{{ $mod->uuid }}')">
                    <input type="checkbox" value="{{ $mod->uuid }}" id="mod_{{ $mod->uuid }}">
                    <label class="mod-label mb-0" for="mod_{{ $mod->uuid }}">
                        {{ $mod->module_name ?? $mod->module_uuid }}
                    </label>
                </div>
                @endforeach

                <div class="progress-wrap" id="progressWrap">
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                             style="width:100%"></div>
                    </div>
                    <small class="text-muted mt-1 d-block text-center">Applying patches, please wait...</small>
                </div>

                <button class="apply-btn" id="applyBtn" onclick="applyMods()" disabled>
                    Apply Selected Modifications
                </button>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    var selectedMods = [];

    function toggleMod(el, uuid) {
        var checkbox = el.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        el.classList.toggle('selected', checkbox.checked);

        if (checkbox.checked) {
            selectedMods.push(uuid);
        } else {
            selectedMods = selectedMods.filter(function(u) { return u !== uuid; });
        }

        document.getElementById('applyBtn').disabled = selectedMods.length === 0;
    }

    function applyMods() {
        if (selectedMods.length === 0) return;

        document.getElementById('applyBtn').disabled = true;
        document.getElementById('progressWrap').style.display = 'block';

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        selectedMods.forEach(function(uuid) {
            formData.append('record_uuids[]', uuid);
        });

        $.ajax({
            url: '{{ url("/user/detect/" . $sessionKey . "/apply") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: { responseType: 'blob' },
        }).done(function (blob, status, xhr) {
            var filename = 'MagicSolution_patched.bin';
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition) {
                var match = disposition.match(/filename="(.+?)"/);
                if (match) filename = match[1];
            }

            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

            toastr.success('File patched and downloaded successfully!');
            document.getElementById('progressWrap').style.display = 'none';
            document.getElementById('applyBtn').disabled = false;

        }).fail(function (xhr) {
            var msg = 'Processing failed.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else {
                // Try to read blob as text for error
                var reader = new FileReader();
                reader.onload = function() {
                    try {
                        var json = JSON.parse(reader.result);
                        toastr.error(json.message || msg);
                    } catch(e) { toastr.error(msg); }
                };
                if (xhr.response instanceof Blob) reader.readAsText(xhr.response);
                else toastr.error(msg);
            }
            document.getElementById('progressWrap').style.display = 'none';
            document.getElementById('applyBtn').disabled = false;
        });
    }
</script>
@endsection
