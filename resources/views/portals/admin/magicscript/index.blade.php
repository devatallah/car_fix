@extends('portals.admin.app')

@section('title') MagicScript Files @endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">MagicScript Files</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a></li>
                            <li class="breadcrumb-item active">MagicScript</li>
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
                                <h4 class="card-title">Upload .magicsscript Patch Files</h4>
                                <small class="text-muted">These files are applied directly to ECU binaries using byte-offset patching.</small>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#create_modal">
                                    <i class="fa fa-upload"></i> Upload Script
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
                                        <label>ECU</label>
                                        <select name="ecu_uuid" id="s_ecu_uuid" class="form-control">
                                            <option value="">All ECUs</option>
                                            @foreach($ecus as $ecu)
                                                <option value="{{ $ecu->uuid }}">{{ $ecu->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>Module</label>
                                        <select name="module_uuid" id="s_module_uuid" class="form-control">
                                            <option value="">All Modules</option>
                                            @foreach($modules as $m)
                                                <option value="{{ $m->uuid }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3" style="margin-top:20px;">
                                        <button class="btn btn-outline-info" type="submit"><i class="fa fa-search"></i> Search</button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()"><i class="fa fa-undo"></i> Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive card-datatable">
                            <table class="table" id="datatable">
                                <thead>
                                    <tr>
                                        <th style="width:35px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="table_ids custom-control-input dt-checkboxes" id="select_all">
                                                <label class="custom-control-label" for="select_all"></label>
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Brand / ECU</th>
                                        <th>Module</th>
                                        <th>Patches</th>
                                        <th style="width:180px;">@lang('actions')</th>
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

{{-- CREATE MODAL --}}
<div class="modal fade" id="create_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload .magicsscript File</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="create_form" class="ajax_form" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="form-group">
                        <label>ECU <span class="text-danger">*</span></label>
                        <select id="c_ecu_uuid" class="form-control" onchange="loadEcuFiles('c')">
                            <option value="">Select ECU first...</option>
                            @foreach($ecus as $ecu)
                                <option value="{{ $ecu->uuid }}">{{ $ecu->brand_name }} / {{ $ecu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ECU File <span class="text-danger">*</span></label>
                        <select name="ecu_file_uuid" id="c_ecu_file_uuid" class="form-control" required>
                            <option value="">Select ECU first</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Module (Fix Type) <span class="text-danger">*</span></label>
                        <select name="module_uuid" class="form-control" required>
                            <option value="">Select module</option>
                            @foreach($modules as $m)
                                <option value="{{ $m->uuid }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>.magicsscript File <span class="text-danger">*</span></label>
                        <input type="file" name="script_file" class="form-control" accept=".magicsscript,.txt" required>
                        <small class="text-muted">Only .magicsscript files are accepted</small>
                    </div>
                    <div id="c_validation_result" class="alert" style="display:none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="create_form" class="submit_btn btn btn-primary">
                    <i class="fa fa-spinner fa-spin" style="display:none;"></i> Upload & Save
                </button>
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')</button>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="edit_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Script Record</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="edit_form" class="ajax_form" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Module (Fix Type) <span class="text-danger">*</span></label>
                        <select name="module_uuid" id="e_module_uuid" class="form-control" required>
                            <option value="">Select module</option>
                            @foreach($modules as $m)
                                <option value="{{ $m->uuid }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Replace Script File (optional)</label>
                        <input type="file" name="script_file" class="form-control" accept=".magicsscript,.txt">
                        <small class="text-muted">Leave empty to keep existing script</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="edit_form" class="submit_btn btn btn-primary">
                    <i class="fa fa-spinner fa-spin" style="display:none;"></i> @lang('save')
                </button>
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var baseUrl = '{{ url("/admin/magicscript") }}';

    var oTable = $('#datatable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        processing: true, serverSide: true, searching: false,
        ajax: {
            url: baseUrl + '/indexTable',
            data: function(d) {
                d.ecu_uuid    = $('#s_ecu_uuid').val();
                d.module_uuid = $('#s_module_uuid').val();
            }
        },
        columns: [
            { render: function(d, t, full) {
                return `<div class="custom-control custom-checkbox"><input type="checkbox" class="table_ids custom-control-input dt-checkboxes" value="${full.uuid}" id="chk${full.uuid}"><label class="custom-control-label" for="chk${full.uuid}"></label></div>`;
            }},
            { data: 'id' },
            { render: function(d, t, full) { return (full.brand_name || '-') + ' / ' + (full.ecu_name || '-'); }},
            { data: 'module_name' },
            { render: function(d, t, full) {
                return full.patch_count !== 'N/A'
                    ? '<span class="badge badge-success">' + full.patch_count + ' patches</span>'
                    : '<span class="badge badge-warning">N/A</span>';
            }},
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']]
    });

    $('#search_form').submit(function(e) { e.preventDefault(); oTable.ajax.reload(); });
    function clearSearch() { $('#s_ecu_uuid, #s_module_uuid').val(''); oTable.ajax.reload(); }

    $(document).on('click', '.edit_btn', function() {
        var b = $(this);
        $('#edit_form').attr('action', baseUrl + '/' + b.data('uuid'));
        $('#e_module_uuid').val(b.data('module_uuid')).trigger('change');
    });

    $('#create_modal').on('show.bs.modal', function() {
        $('#create_form').attr('action', baseUrl);
        $('#c_ecu_file_uuid').html('<option value="">Select ECU first</option>');
    });

    function loadEcuFiles(prefix) {
        var ecuUuid = $('#' + prefix + '_ecu_uuid').val();
        if (!ecuUuid) return;
        $.get('{{ url("/admin/ecu_signatures/ecu-files") }}', { ecu_uuid: ecuUuid }, function(files) {
            var opts = '<option value="">Select ECU file</option>';
            files.forEach(function(f) { opts += `<option value="${f.uuid}">File ID: ${f.id}</option>`; });
            $('#' + prefix + '_ecu_file_uuid').html(opts);
        });
    }
</script>
@endsection
