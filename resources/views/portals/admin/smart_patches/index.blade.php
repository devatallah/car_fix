@extends('portals.admin.app')

@section('title')
    Smart Patches
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Smart Patches</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a></li>
                            <li class="breadcrumb-item active">Smart Patches</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section>
            <div class="row">
                <div class="col-12">
                    {{-- ─── Groups Table ─── --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="head-label">
                                <h4 class="card-title">Smart Patch Groups</h4>
                                <small class="text-muted">Each group = one ECU + Fix Type. Add calibration variants per group.</small>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-outline-primary" type="button"
                                    data-bs-toggle="modal" data-bs-target="#create_group_modal">
                                    <i class="fa fa-plus"></i> New Group
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <form id="search_form">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>@lang('ecu')</label>
                                            <select name="s_ecu_uuid" id="s_ecu_uuid" class="form-control">
                                                <option value="">@lang('select')</option>
                                                @foreach ($ecus as $ecu)
                                                    <option value="{{ $ecu->uuid }}">{{ $ecu->brand->name }} - {{ $ecu->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label>Fix Type</label>
                                            <select name="s_module_uuid" id="s_module_uuid" class="form-control">
                                                <option value="">@lang('select')</option>
                                                @foreach ($modules as $module)
                                                    <option value="{{ $module->uuid }}">{{ $module->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3" style="margin-top: 20px">
                                        <button id="search_btn" class="btn btn-outline-info" type="submit">
                                            <i class="fa fa-search"></i> @lang('search')
                                        </button>
                                        <button id="clear_btn" class="btn btn-outline-secondary" type="button">
                                            <i class="fa fa-undo"></i> @lang('reset')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive card-datatable">
                            <table class="table" id="groups_table">
                                <thead>
                                    <tr>
                                        <th>Brand</th>
                                        <th>ECU</th>
                                        <th>Fix Type</th>
                                        <th>Calibrations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

{{-- ─── Create Group Modal ─── --}}
<div class="modal fade" id="create_group_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Smart Patch Group</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Step 1:</strong> Define the ECU and the fix type. Then add calibration variants to the group.
                </div>
                <form id="create_group_form" novalidate>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>@lang('ecu') <span class="text-danger">*</span></label>
                        <select class="form-control" name="ecu_uuid" id="g_ecu_uuid" required>
                            <option value="">@lang('select')</option>
                            @foreach ($ecus as $ecu)
                                <option value="{{ $ecu->uuid }}">{{ $ecu->brand->name }} - {{ $ecu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fix Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="module_uuid" id="g_module_uuid" required>
                            <option value="">@lang('select')</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->uuid }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_group_btn" class="btn btn-primary">
                    <i class="fa fa-spinner fa-spin d-none" id="group_spinner"></i>
                    <i class="fa fa-save" id="group_icon"></i>
                    Create Group
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('close')</button>
            </div>
        </div>
    </div>
</div>

{{-- ─── Add Calibration Modal ─── --}}
<div class="modal fade" id="calibration_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Calibration</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Step 2:</strong> Upload 3 binary files from the <strong>same ECU variant</strong>.
                    The system extracts the fix and wildcards variable bytes (VIN/Immo) automatically.
                </div>
                <form id="calibration_form" enctype="multipart/form-data" novalidate>
                    {{ csrf_field() }}
                    <input type="hidden" id="cal_group_uuid" value="">

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label><span class="badge badge-secondary">Ori 1</span> Original file — from the car you tuned <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input bin-input" name="ori1" id="c_ori1" accept=".bin" required>
                                    <label class="custom-file-label" for="c_ori1">Choose ori1.bin...</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label><span class="badge badge-success">Mod</span> Fixed/tuned file — same car after applying the fix <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input bin-input" name="mod" id="c_mod" accept=".bin" required>
                                    <label class="custom-file-label" for="c_mod">Choose mod.bin...</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label><span class="badge badge-warning">Ori 2</span> Original — different car, same ECU model <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input bin-input" name="ori2" id="c_ori2" accept=".bin" required>
                                    <label class="custom-file-label" for="c_ori2">Choose ori2.bin...</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="cal_result_box" class="alert alert-success mt-2" style="display:none">
                        <strong>✅ Calibration saved!</strong><br>
                        <span id="cal_result_details"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_calibration_btn" class="btn btn-primary">
                    <i class="fa fa-spinner fa-spin d-none" id="cal_spinner"></i>
                    <i class="fa fa-cog" id="cal_icon"></i>
                    Extract &amp; Save Calibration
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var baseUrl   = '{{ url('/admin/smart_patches') }}';
var csrfToken = $('meta[name="csrf-token"]').attr('content');

// ─── Groups DataTable ─────────────────────────────────────────────────────────
var oTable = $('#groups_table').DataTable({
    dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    processing: true,
    serverSide: true,
    searching: false,
    ajax: {
        url: baseUrl + '/groups/indexTable',
        data: function(d) {
            d.ecu_uuid    = $('#s_ecu_uuid').val();
            d.module_uuid = $('#s_module_uuid').val();
        }
    },
    columns: [
        { data: 'brand_name',          name: 'brand_name' },
        { data: 'ecu_name',            name: 'ecu_name' },
        { data: 'module_name',         name: 'module_name' },
        { data: 'calibrations_count',  name: 'calibrations_count',
          render: function(d) { return '<span class="badge badge-info">' + d + ' calibration(s)</span>'; }
        },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
});

// ─── Search ──────────────────────────────────────────────────────────────────
$('#search_form').on('submit', function(e) { e.preventDefault(); oTable.ajax.reload(); });
$('#clear_btn').on('click', function() {
    $('#s_ecu_uuid, #s_module_uuid').val('').trigger('change');
    oTable.ajax.reload();
});

// ─── File input labels ────────────────────────────────────────────────────────
$(document).on('change', '.bin-input', function() {
    var label = this.files[0] ? this.files[0].name : 'Choose file...';
    $(this).next('.custom-file-label').text(label);
});

// ─── Create Group ─────────────────────────────────────────────────────────────
$('#submit_group_btn').on('click', function() {
    var ecuUuid    = $('#g_ecu_uuid').val();
    var moduleUuid = $('#g_module_uuid').val();

    if (!ecuUuid || !moduleUuid) {
        toastr.error('Please select an ECU and a Fix Type.');
        return;
    }

    var $btn = $(this);
    $('#group_spinner').removeClass('d-none');
    $('#group_icon').addClass('d-none');
    $btn.prop('disabled', true);

    $.ajax({
        url: baseUrl + '/groups',
        method: 'POST',
        data: { _token: csrfToken, ecu_uuid: ecuUuid, module_uuid: moduleUuid },
        success: function(res) {
            if (res.status) {
                $('#create_group_modal').modal('hide');
                $('#g_ecu_uuid, #g_module_uuid').val('');
                oTable.ajax.reload();
                toastr.success('Group created. Now add calibrations to it.');
            } else {
                toastr.error(res.message || 'Unknown error');
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error';
            toastr.error(msg);
        },
        complete: function() {
            $('#group_spinner').addClass('d-none');
            $('#group_icon').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });
});

// ─── Open Calibration Modal (from DataTable action button) ────────────────────
$(document).on('click', '.add-calibration-btn', function() {
    var groupUuid = $(this).data('group');
    $('#cal_group_uuid').val(groupUuid);
    $('#calibration_form')[0].reset();
    $('.custom-file-label').text('Choose file...');
    $('#cal_result_box').hide();
    $('#calibration_modal').modal('show');
});

// ─── Submit Calibration ───────────────────────────────────────────────────────
$('#submit_calibration_btn').on('click', function() {
    var groupUuid = $('#cal_group_uuid').val();

    if (!$('#c_ori1')[0].files.length || !$('#c_mod')[0].files.length || !$('#c_ori2')[0].files.length) {
        toastr.error('Please upload all 3 binary files.');
        return;
    }

    var formData = new FormData($('#calibration_form')[0]);
    var $btn = $(this);
    $('#cal_spinner').removeClass('d-none');
    $('#cal_icon').addClass('d-none');
    $btn.prop('disabled', true);
    $('#cal_result_box').hide();

    $.ajax({
        url: baseUrl + '/groups/' + groupUuid + '/calibrations',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status) {
                $('#cal_result_box').show();
                $('#cal_result_details').html(
                    'ECU#: <strong>' + (res.ecu_software_number || '—') + '</strong> | ' +
                    'Size: <strong>' + res.file_size.toLocaleString() + ' B</strong> | ' +
                    'Patches: <strong>' + res.patches_count + '</strong> | ' +
                    'Wildcards: <strong>' + res.wildcard_count + '</strong>'
                );
                oTable.ajax.reload();
                toastr.success('Calibration saved!');
            } else {
                toastr.error(res.message || 'Unknown error');
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error';
            toastr.error(msg);
        },
        complete: function() {
            $('#cal_spinner').addClass('d-none');
            $('#cal_icon').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });
});

// ─── Delete Group ─────────────────────────────────────────────────────────────
$(document).on('click', '.delete-group-btn', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Delete Group?',
        text: 'This will delete the group and ALL its calibrations.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl + '/groups/' + id,
                method: 'POST',
                data: { _method: 'DELETE', _token: csrfToken },
                success: function(res) {
                    if (res.status) { oTable.ajax.reload(); toastr.success('Group deleted.'); }
                }
            });
        }
    });
});
</script>
@endsection
