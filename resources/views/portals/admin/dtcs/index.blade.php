@extends('portals.admin.app')

@section('title')
    @lang('dtc')
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
                        <h2 class="content-header-title float-left mb-0">@lang('dtc')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dtcs') }}">@lang('dtc')</a>
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
                                    <h4 class="card-title">@lang('dtc')</h4>
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
                                                <label for="s_brand_uuid">@lang('brands')</label>
                                                <select name="s_brand_uuid" id="s_brand_uuid" class="form-control">
                                                    <option value="">@lang('select')</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->uuid }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                            <th>@lang('name')</th>
                                            <th>@lang('brand')</th>
                                            <th>@lang('ecu')</th>
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
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="note">@lang('name')</label>
                                    <input type="text" class="form-control" placeholder="@lang('name')"
                                        name="name" id="name">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="brand_uuid">@lang('brands')</label>
                                    <select name="brand_uuid" id="brand_uuid" class="brand_uuid form-control">
                                        <option value="">@lang('select')</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->uuid }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="ecu_uuid">@lang('ecu')</label>
                                    <select class="ecu_uuid form-control" id="ecu_uuid" name="ecu_uuid" required>
                                        <option value="">@lang('select')</option>
                                    </select>
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
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label for="edit_name">@lang('name')</label>
                                    <input type="text" class="form-control" placeholder="@lang('name')"
                                        name="name" id="edit_name">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_brand_uuid">@lang('brands')</label>
                                    <select name="brand_uuid" id="edit_brand_uuid" class="brand_uuid form-control">
                                        <option value="">@lang('select')</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->uuid }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_ecu_uuid">@lang('ecu')</label>
                                    <select class="ecu_uuid form-control" id="edit_ecu_uuid" name="ecu_uuid" required>
                                        <option value="">@lang('select')</option>
                                    </select>
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


@section('scripts')
    <script>
        var url = '{{ url('/admin/dtcs') }}/';

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
                url: '{{ url('/admin/dtcs/indexTable') }}',
                data: function(d) {
                    d.brand_uuid = $('#s_brand_uuid').val();
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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'brand_name',
                    name: 'brand_name'
                },
                {
                    data: 'ecu_name',
                    name: 'ecu_name'
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

            $(document).on('change', '#create_form #brand_uuid', function(event) {
                let brand = event.target.value;

                $.get('{{ url('/admin/ecus-by-brand') }}', {
                    brand_uuid: brand
                }, function(result) {

                    $("#create_form #ecu_uuid").find("option").remove().end().append(
                        '<option value="">@lang('select')</option>');

                    if (result.status) {
                        console.log(result);
                        let ecus = result.data;

                        ecus.forEach(element => {
                            $("#create_form #ecu_uuid").append(
                                `<option value="${element.uuid}">${element.name}</option>`
                            );
                        });
                    }
                });
            });

            $(document).on('change', '#edit_form #edit_brand_uuid', function(event) {
                let brand = event.target.value;

                $.get('{{ url('/admin/ecus-by-brand') }}', {
                    brand_uuid: brand
                }, function(result) {

                    $("#edit_form #edit_ecu_uuid").find("option").remove().end().append(
                        '<option value="">@lang('select')</option>');

                    if (result.status) {
                        console.log(result);
                        let ecus = result.data;

                        ecus.forEach(element => {
                            $("#edit_form #edit_ecu_uuid").append(
                                `<option value="${element.uuid}">${element.name}</option>`
                            );
                        });
                    }
                });
            });

            $(document).on('click', '.edit_btn', function(event) {
                var button = $(this)
                var uuid = button.data('uuid')
                $('#edit_form').attr('action', url + uuid)
                $('#edit_name').val(button.data('name'))
                $('#edit_brand_uuid').val(button.data('brand_uuid')).trigger('change')

                let brand = button.data('brand_uuid');

                $.get('{{ url('/admin/ecus-by-brand') }}', {
                    brand_uuid: brand
                }, function(result) {

                    $("#edit_form #edit_ecu_uuid").find("option").remove().end().append(
                        '<option value="">@lang('select')</option>');

                    if (result.status) {
                        console.log(result);
                        let ecus = result.data;

                        ecus.forEach(element => {
                            $("#edit_form #edit_ecu_uuid").append(
                                `<option value="${element.uuid}">${element.name}</option>`
                            );
                        });

                        $('#edit_ecu_uuid').val(button.data('ecu_uuid')).trigger('change')
                    }
                });
            });
            $(document).on('click', '#create_btn', function(event) {
                $('#create_form').attr('action', url);
            });
        });
    </script>
@endsection