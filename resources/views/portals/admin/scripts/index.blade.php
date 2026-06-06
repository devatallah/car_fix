@extends('portals.admin.app')

@section('title')
    @lang('scripts')
@endsection

@section('styles')
    <style>
        .pac-container {
            z-index: 1051 !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('scripts')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/admin/scripts') }}">@lang('scripts')</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="head-label">
                                    <h4 class="card-title">@lang('scripts')</h4>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#create_modal"><span><i class="fa fa-plus"></i>
                                                @lang('add_new_record')</span>
                                        </button>
                                        <button disabled="" id="delete_btn" class="delete-btn btn btn-outline-danger">
                                            <span><i class="fa fa-lg fa-trash-alt" aria-hidden="true"></i>
                                                @lang('delete')</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_ecu_uuid">@lang('ecu')</label>
                                                <select name="s_ecu_uuid" id="s_ecu_uuid" class="form-control">
                                                    <option value="">@lang('select')</option>
                                                    @foreach ($ecus as $ecu)
                                                        <option value="{{ $ecu->uuid }}">{{ $ecu->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_module_uuid">@lang('script_type')</label>
                                                <select name="s_module_uuid" id="s_module_uuid" class="form-control">
                                                    <option value="">@lang('select')</option>
                                                    @foreach ($modules as $module)
                                                        <option value="{{ $module->uuid }}">{{ $module->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3" style="margin-top: 20px">
                                            <div class="form-group">
                                                <button id="search_btn" class="btn btn-outline-info" type="submit">
                                                    <span><i class="fa fa-search"></i> @lang('search')</span>
                                                </button>
                                                <button id="clear_btn" class="btn btn-outline-secondary" type="submit">
                                                    <span><i class="fa fa-undo"></i> @lang('reset')</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive card-datatable">
                                <table class="table" id="datatable">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-column sorting_disabled" rowspan="1" colspan="1"
                                                style="width: 35px;" aria-label=" Record Id ">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                        class="table_ids custom-control-input dt-checkboxes"
                                                        id="select_all">
                                                    <label class="custom-control-label" for="select_all"></label>
                                                </div>
                                            </th>
                                            <th>@lang('uuid')</th>
                                            <th>@lang('ecu')</th>
                                            <th>@lang('brand')</th>
                                            <th>@lang('script_type')</th>
                                            <th>File Size (bytes)</th>
                                            <th style="width: 225px;">@lang('actions')</th>
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
    <div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('create')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="create_form" method="POST" data-reset="true" class="ajax_form form-horizontal"
                        enctype="multipart/form-data" novalidate>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="ecu_uuid">@lang('ecu')</label>
                                    <select class="ecu_uuid form-control" id="ecu_uuid" name="ecu_uuid" required>
                                        <option value="">@lang('select')</option>
                                        @foreach ($ecus as $ecu)
                                            <option value="{{ $ecu->uuid }}">
                                                {{ $ecu->brand->name . '-' . $ecu->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="module_uuid">@lang('script_type')</label>
                                    <select class="module_uuid form-control" id="module_uuid" name="module_uuid"
                                        required>
                                        <option value="">@lang('select')</option>
                                        @foreach ($modules as $module)
                                            <option value="{{ $module->uuid }}">{{ $module->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="expected_file_size">حجم الملف المتوقع (bytes)</label>
                                    <input type="number" class="form-control" id="expected_file_size"
                                        name="expected_file_size" placeholder="مثال: 2097152" min="1" required>
                                    <small class="text-muted">حجم ملف الـ ECU بالـ bytes — يُستخدم لمطابقة الملفات المرفوعة من المستخدمين</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12"><hr><p class="text-muted small mb-1">حقول التحقق الدقيق — اختيارية، تُستخدم لتمييز هذا السكريبت عن غيره لنفس الـ ECU</p></div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="part_number">Part Number</label>
                                    <input type="text" class="form-control" id="part_number" name="part_number" placeholder="مثال: 0261S12345">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="calibration_id">Calibration ID</label>
                                    <input type="text" class="form-control" id="calibration_id" name="calibration_id" placeholder="مثال: 8V0906259B">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="sw_version">SW Version</label>
                                    <input type="text" class="form-control" id="sw_version" name="sw_version" placeholder="مثال: 1.0.4">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="hw_version">HW Version</label>
                                    <input type="text" class="form-control" id="hw_version" name="hw_version" placeholder="مثال: H05">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="create_form" class="submit_btn btn btn-primary">
                        <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                        @lang('save')
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('edit')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="edit_form" method="POST" data-reset="true"
                        class="ajax_form form-horizontal" enctype="multipart/form-data" novalidate>
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_ecu_uuid">@lang('ecu')</label>
                                    <select class="ecu_uuid form-control" id="edit_ecu_uuid" name="ecu_uuid" required>
                                        <option value="">@lang('select')</option>
                                        @foreach ($ecus as $ecu)
                                            <option value="{{ $ecu->uuid }}">
                                                {{ $ecu->brand->name . '-' . $ecu->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_module_uuid">@lang('script_type')</label>
                                    <select class="module_uuid form-control" id="edit_module_uuid" name="module_uuid"
                                        required>
                                        <option value="">@lang('select')</option>
                                        @foreach ($modules as $module)
                                            <option value="{{ $module->uuid }}">{{ $module->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="edit_expected_file_size">حجم الملف المتوقع (bytes)</label>
                                    <input type="number" class="form-control" id="edit_expected_file_size"
                                        name="expected_file_size" placeholder="مثال: 2097152" min="1">
                                    <small class="text-muted">حجم ملف الـ ECU بالـ bytes</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12"><hr><p class="text-muted small mb-1">حقول التحقق الدقيق — اختيارية</p></div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_part_number">Part Number</label>
                                    <input type="text" class="form-control" id="edit_part_number" name="part_number" placeholder="مثال: 0261S12345">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_calibration_id">Calibration ID</label>
                                    <input type="text" class="form-control" id="edit_calibration_id" name="calibration_id" placeholder="مثال: 8V0906259B">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_sw_version">SW Version</label>
                                    <input type="text" class="form-control" id="edit_sw_version" name="sw_version" placeholder="مثال: 1.0.4">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_hw_version">HW Version</label>
                                    <input type="text" class="form-control" id="edit_hw_version" name="hw_version" placeholder="مثال: H05">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="edit_form" class="submit_btn btn btn-primary">
                        <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                        @lang('save')
                    </button>
                    <button type="button" class="btn btn-outline-danger"
                        data-bs-dismiss="modal">@lang('close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
@section('scripts')
    <script>
        var url = '{{ url('/admin/scripts') }}/';

        var oTable = $('#datatable').DataTable({
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "oLanguage": {
                @if (app()->isLocale('ar'))
                    "sEmptyTable": "ليست هناك بيانات متاحة في الجدول",
                    "sLoadingRecords": "جارٍ التحميل...",
                    "sProcessing": "جارٍ التحميل...",
                    "sLengthMenu": "أظهر _MENU_ مدخلات",
                    "sZeroRecords": "لم يعثر على أية سجلات",
                    "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
                    "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                    "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
                    "sInfoPostFix": "",
                    "sSearch": "ابحث:",
                    "oAria": {
                        "sSortAscending": ": تفعيل لترتيب العمود تصاعدياً",
                        "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
                    },
                @endif
                "oPaginate": {
                    "sPrevious": '&nbsp;',
                    "sNext": '&nbsp;'
                }
            },
            'columnDefs': [{
                    "targets": 1,
                    "visible": false
                },
                {
                    'targets': 0,
                    "searchable": false,
                    "orderable": false
                },
            ],
            "order": [
                [1, 'asc']
            ],
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '{{ url('/admin/scripts/indexTable') }}',
                data: function(d) {
                    d.module_uuid = $('#s_module_uuid').val();
                    d.ecu_uuid = $('#s_ecu_uuid').val();
                }
            },
            columns: [{
                    "render": function(data, type, full, meta) {
                        return `<td class="checkbox-column sorting_1">
                                       <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="table_ids custom-control-input dt-checkboxes"
                                         name="table_ids[]" value="` + full.uuid + `" id="checkbox` + full.uuid + `" >
                                    <label class="custom-control-label" for="checkbox` + full.uuid + `"></label>
                                </div></td>`;
                    }
                },

                {
                    data: 'uuid',
                    name: 'uuid'
                },
                {
                    data: 'ecu_name',
                    name: 'ecu_name'
                },
                {
                    data: 'brand_name',
                    name: 'brand_name'
                },
                {
                    data: 'module_name',
                    name: 'module_name'
                },
                {
                    data: 'expected_file_size',
                    name: 'expected_file_size',
                    render: function(data) {
                        return data ? data.toLocaleString() + ' B' : '—';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).ready(function() {

            $(document).on('click', '.edit_btn', function(event) {
                var button = $(this)
                var uuid = button.data('uuid')
                $('#edit_form').attr('action', url + uuid)
                $('#edit_ecu_uuid').val(button.data('ecu_uuid')).trigger('change')
                $('#edit_module_uuid').val(button.data('module_uuid')).trigger('change')
                $('#edit_expected_file_size').val(button.data('expected_file_size'))
                $('#edit_part_number').val(button.data('part_number'))
                $('#edit_calibration_id').val(button.data('calibration_id'))
                $('#edit_sw_version').val(button.data('sw_version'))
                $('#edit_hw_version').val(button.data('hw_version'))
            });
            $(document).on('click', '#create_btn', function(event) {
                $('#create_form').attr('action', url);
            });
        });
    </script>
@endsection