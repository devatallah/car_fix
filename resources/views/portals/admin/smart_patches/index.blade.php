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
                    <div class="card">
                        <div class="card-header">
                            <div class="head-label">
                                <h4 class="card-title">Smart Patches</h4>
                                <small class="text-muted">Upload 3 binary files → system auto-generates the patch map</small>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-outline-primary" type="button"
                                    data-bs-toggle="modal" data-bs-target="#create_modal">
                                    <i class="fa fa-plus"></i> Add Smart Patch
                                </button>
                                <button disabled id="delete_btn" class="delete-btn btn btn-outline-danger">
                                    <i class="fa fa-trash-alt"></i> @lang('delete')
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
                            <table class="table" id="datatable">
                                <thead>
                                    <tr>
                                        <th style="width:35px">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="table_ids custom-control-input dt-checkboxes" id="select_all">
                                                <label class="custom-control-label" for="select_all"></label>
                                            </div>
                                        </th>
                                        <th>Brand</th>
                                        <th>ECU</th>
                                        <th>Fix Type</th>
                                        <th>File Size</th>
                                        <th>Patches</th>
                                        <th>Wildcards (??)</th>
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

{{-- ─── Create Modal ─── --}}
<div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Smart Patch</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>How it works:</strong>
                    Upload 3 binary files from the <strong>same ECU model</strong>. The system will automatically
                    extract the fix and mark variable bytes (VIN/Immo) as wildcards (<code>??</code>).
                </div>

                <form id="create_form" method="POST" enctype="multipart/form-data" novalidate>
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>@lang('ecu') <span class="text-danger">*</span></label>
                                <select class="form-control" name="ecu_uuid" id="ecu_uuid" required>
                                    <option value="">@lang('select')</option>
                                    @foreach ($ecus as $ecu)
                                        <option value="{{ $ecu->uuid }}">{{ $ecu->brand->name }} - {{ $ecu->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Fix Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="module_uuid" id="module_uuid" required>
                                    <option value="">@lang('select')</option>
                                    @foreach ($modules as $module)
                                        <option value="{{ $module->uuid }}">{{ $module->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <p class="font-weight-bold text-primary mb-1"><i class="fa fa-file"></i> Upload 3 Binary Files</p>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <span class="badge badge-secondary">Ori 1</span>
                                    Original file — from the car you tuned
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="ori1" id="ori1" accept=".bin" required>
                                    <label class="custom-file-label" for="ori1">Choose ori1.bin...</label>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <span class="badge badge-success">Mod</span>
                                    Fixed/tuned file — same car after applying the fix
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="mod" id="mod" accept=".bin" required>
                                    <label class="custom-file-label" for="mod">Choose mod.bin...</label>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <span class="badge badge-warning">Ori 2</span>
                                    Original file — from a different car (same ECU model, different VIN)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="ori2" id="ori2" accept=".bin" required>
                                    <label class="custom-file-label" for="ori2">Choose ori2.bin...</label>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Result Preview --}}
                    <div id="result_box" class="alert alert-success mt-2" style="display:none">
                        <strong>✅ Patch generated successfully!</strong><br>
                        <span id="result_details"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_patch_btn" class="btn btn-primary">
                    <i class="fa fa-spinner fa-spin d-none" id="submit_spinner"></i>
                    <i class="fa fa-cog" id="submit_icon"></i>
                    Extract &amp; Save Patch
                </button>
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var url = '{{ url('/admin/smart_patches') }}/';

var oTable = $('#datatable').DataTable({
    dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    processing: true,
    serverSide: true,
    searching: false,
    ajax: {
        url: '{{ url('/admin/smart_patches/indexTable') }}',
        data: function(d) {
            d.ecu_uuid    = $('#s_ecu_uuid').val();
            d.module_uuid = $('#s_module_uuid').val();
        }
    },
    columns: [
        {
            render: function(data, type, full) {
                return `<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="table_ids custom-control-input dt-checkboxes"
                        name="table_ids[]" value="${full.uuid}" id="cb_${full.uuid}">
                    <label class="custom-control-label" for="cb_${full.uuid}"></label>
                </div>`;
            },
            orderable: false, searchable: false
        },
        { data: 'brand_name',   name: 'brand_name' },
        { data: 'ecu_name',     name: 'ecu_name' },
        { data: 'module_name',  name: 'module_name' },
        {
            data: 'file_size', name: 'file_size',
            render: function(d) { return d ? d.toLocaleString() + ' B' : '—'; }
        },
        { data: 'patches_count',  name: 'patches_count' },
        { data: 'wildcard_count', name: 'wildcard_count',
          render: function(d) { return '<span class="badge badge-warning">' + d + '</span>'; }
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
['ori1','mod','ori2'].forEach(function(name) {
    $('#' + name).on('change', function() {
        var label = this.files[0] ? this.files[0].name : 'Choose ' + name + '.bin...';
        $(this).next('.custom-file-label').text(label);
    });
});

// ─── Submit ───────────────────────────────────────────────────────────────────
$('#submit_patch_btn').on('click', function() {
    var $btn = $(this);
    var formData = new FormData($('#create_form')[0]);

    // Basic validation
    if (!$('#ecu_uuid').val() || !$('#module_uuid').val() ||
        !$('#ori1')[0].files.length || !$('#mod')[0].files.length || !$('#ori2')[0].files.length) {
        toastr.error('Please fill all fields and upload all 3 files.');
        return;
    }

    $('#submit_spinner').removeClass('d-none');
    $('#submit_icon').addClass('d-none');
    $btn.prop('disabled', true);
    $('#result_box').hide();

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status) {
                $('#result_box').show();
                $('#result_details').html(
                    'File Size: <strong>' + res.file_size.toLocaleString() + ' bytes</strong> | ' +
                    'Changed Bytes: <strong>' + res.patches_count + '</strong> | ' +
                    'Clusters: <strong>' + res.clusters_count + '</strong> | ' +
                    'Wildcards (??): <strong>' + res.wildcard_count + '</strong>'
                );
                oTable.ajax.reload();
                toastr.success('Smart patch saved successfully!');
            } else {
                toastr.error(res.message || 'Unknown error');
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Server error';
            toastr.error(msg);
        },
        complete: function() {
            $('#submit_spinner').addClass('d-none');
            $('#submit_icon').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });
});

// ─── Delete ───────────────────────────────────────────────────────────────────
$(document).on('click', '.delete-btn', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Delete?',
        text: 'This smart patch will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: url + id,
                method: 'POST',
                data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if (res.status) { oTable.ajax.reload(); toastr.success('Deleted.'); }
                }
            });
        }
    });
});

// ─── Bulk delete ──────────────────────────────────────────────────────────────
$('#delete_btn').on('click', function() {
    var ids = selectedIds();
    if (!ids.length) return;
    Swal.fire({
        title: 'Delete selected?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: url + ids.join(','),
                method: 'POST',
                data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if (res.status) { oTable.ajax.reload(); toastr.success('Deleted.'); }
                }
            });
        }
    });
});

$(document).on('change', 'input[name="table_ids[]"]', function() {
    $('#delete_btn').prop('disabled', !$('input[name="table_ids[]"]:checked').length);
});
$('#select_all').on('change', function() {
    $('input[name="table_ids[]"]').prop('checked', this.checked);
    $('#delete_btn').prop('disabled', !this.checked);
});
</script>
@endsection
