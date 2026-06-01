@extends('portals.admin.app')

@section('title') ECU Signatures @endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ECU Signatures</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a></li>
                            <li class="breadcrumb-item active">ECU Signatures</li>
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
                                <h4 class="card-title">ECU Auto-Detection Signatures</h4>
                                <small class="text-muted">Each signature tells the system how to identify an ECU file automatically by its size and byte pattern.</small>
                            </div>
                            <div class="text-right">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#create_modal">
                                    <i class="fa fa-plus"></i> Add Signature
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
                                            <label>ECU</label>
                                            <select name="ecu_uuid" id="s_ecu_uuid" class="form-control">
                                                <option value="">All ECUs</option>
                                                @foreach($ecus as $ecu)
                                                    <option value="{{ $ecu->uuid }}">{{ $ecu->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3" style="margin-top:20px">
                                        <button id="search_btn" class="btn btn-outline-info" type="submit">
                                            <i class="fa fa-search"></i> @lang('search')
                                        </button>
                                        <button id="clear_btn" class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
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
                                        <th style="width:35px;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="table_ids custom-control-input dt-checkboxes" id="select_all">
                                                <label class="custom-control-label" for="select_all"></label>
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Brand / ECU</th>
                                        <th>Car Info</th>
                                        <th>File Size</th>
                                        <th>ECU Type</th>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add ECU Signature</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="create_form" class="ajax_form" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>ECU <span class="text-danger">*</span></label>
                                <select name="ecu_uuid" id="c_ecu_uuid" class="form-control" required>
                                    <option value="">Select ECU</option>
                                    @foreach($ecus as $ecu)
                                        <option value="{{ $ecu->uuid }}">{{ $ecu->brand_name }} / {{ $ecu->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>File Size (bytes) <span class="text-danger">*</span></label>
                                <input type="number" name="file_size" class="form-control" placeholder="e.g. 2097152" required>
                                <small class="text-muted">2 MB = 2097152 &nbsp;|&nbsp; 4 MB = 4194304</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Signature Offset (hex)</label>
                                <input type="text" name="signature_offset" class="form-control" placeholder="e.g. 0x40000">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Signature Bytes (hex)</label>
                                <input type="text" name="signature_bytes" class="form-control" placeholder="e.g. 454443313743343600">
                                <small class="text-muted">Hex bytes at the above offset</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Car Make</label>
                                <input type="text" name="car_make" class="form-control" placeholder="BMW, VW, Mercedes">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Car Model</label>
                                <input type="text" name="car_model" class="form-control" placeholder="Golf 6, E90">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Year Range</label>
                                <input type="text" name="year_range" class="form-control" placeholder="2009-2013">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>ECU Type</label>
                                <input type="text" name="ecu_type" class="form-control" placeholder="Bosch EDC17C46">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>HW/SW Number</label>
                                <input type="text" name="hw_sw_number" class="form-control" placeholder="1037511027">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="create_form" class="submit_btn btn btn-primary">
                    <i class="fa fa-spinner fa-spin" style="display:none;"></i> @lang('save')
                </button>
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')</button>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="edit_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit ECU Signature</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="edit_form" class="ajax_form" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>ECU <span class="text-danger">*</span></label>
                                <select name="ecu_uuid" id="e_ecu_uuid" class="form-control" required>
                                    <option value="">Select ECU</option>
                                    @foreach($ecus as $ecu)
                                        <option value="{{ $ecu->uuid }}">{{ $ecu->brand_name }} / {{ $ecu->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>File Size (bytes) <span class="text-danger">*</span></label>
                                <input type="number" name="file_size" id="e_file_size" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Signature Offset (hex)</label>
                                <input type="text" name="signature_offset" id="e_sig_offset" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Signature Bytes (hex)</label>
                                <input type="text" name="signature_bytes" id="e_sig_bytes" class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Car Make</label>
                                <input type="text" name="car_make" id="e_car_make" class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Car Model</label>
                                <input type="text" name="car_model" id="e_car_model" class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Year Range</label>
                                <input type="text" name="year_range" id="e_year_range" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>ECU Type</label>
                                <input type="text" name="ecu_type" id="e_ecu_type" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>HW/SW Number</label>
                                <input type="text" name="hw_sw_number" id="e_hw_sw" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="e_description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
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
    var baseUrl  = '{{ url("/admin/ecu_signatures") }}';

    var oTable = $('#datatable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        processing: true, serverSide: true, searching: false,
        ajax: {
            url: baseUrl + '/indexTable',
            data: function(d) { d.ecu_uuid = $('#s_ecu_uuid').val(); }
        },
        columns: [
            { render: function(d, t, full) {
                return `<div class="custom-control custom-checkbox"><input type="checkbox" class="table_ids custom-control-input dt-checkboxes" value="${full.uuid}" id="chk${full.uuid}"><label class="custom-control-label" for="chk${full.uuid}"></label></div>`;
            }},
            { data: 'id' },
            { render: function(d, t, full) { return (full.brand_name || '') + ' / ' + (full.ecu_name || ''); }},
            { render: function(d, t, full) {
                var parts = [];
                if (full.car_make)   parts.push(full.car_make);
                if (full.car_model)  parts.push(full.car_model);
                if (full.year_range) parts.push(full.year_range);
                return parts.join(' ') || '<span class="text-muted">-</span>';
            }},
            { render: function(d, t, full) {
                var mb = full.file_size ? (full.file_size / 1048576).toFixed(1) + ' MB' : '-';
                return full.file_size + ' <small class="text-muted">(' + mb + ')</small>';
            }},
            { render: function(d, t, full) { return full.ecu_type || '<span class="text-muted">-</span>'; }},
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']]
    });

    $('#search_form').submit(function(e) { e.preventDefault(); oTable.ajax.reload(); });
    function clearSearch() { $('#s_ecu_uuid').val(''); oTable.ajax.reload(); }

    $(document).on('click', '.edit_btn', function() {
        var b = $(this);
        $('#edit_form').attr('action', baseUrl + '/' + b.data('uuid'));
        $('#e_ecu_uuid').val(b.data('ecu_uuid')).trigger('change');
        $('#e_file_size').val(b.data('file_size'));
        $('#e_sig_offset').val(b.data('signature_offset'));
        $('#e_sig_bytes').val(b.data('signature_bytes'));
        $('#e_car_make').val(b.data('car_make'));
        $('#e_car_model').val(b.data('car_model'));
        $('#e_year_range').val(b.data('year_range'));
        $('#e_ecu_type').val(b.data('ecu_type'));
        $('#e_hw_sw').val(b.data('hw_sw_number'));
        $('#e_description').val(b.data('description'));
    });

    $('#create_modal').on('show.bs.modal', function() {
        $('#create_form').attr('action', baseUrl);
    });
</script>
@endsection
