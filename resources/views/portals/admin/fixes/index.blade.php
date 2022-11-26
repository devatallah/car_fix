@extends('portals.admin.app')
@section('title')
    @lang('fixes')
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
                        <h2 class="content-header-title float-left mb-0">@lang('fixes')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{url('/admin')}}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{url('/admin/fixes')}}">@lang('fixes')</a>
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
                                    <h4 class="card-title">@lang('fixes')</h4>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                                data-bs-target="#create_modal"><span><i class="fa fa-plus"></i> @lang('add_new_record')</span>
                                        </button>
                                        <button disabled="" id="delete_btn"
                                                class="delete-btn btn btn-outline-danger">
                                            <span><i class="fa fa-lg fa-trash-alt" aria-hidden="true"></i> @lang('delete')</span>
                                        </button>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_name">@lang('name')</label>
                                                <input id="s_name" type="text" class="search_input form-control"
                                                       placeholder="@lang('name')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_category_uuid">@lang('category')</label>
                                                <select name="s_category_uuid" id="s_category_uuid"
                                                        class="form-control">
                                                    <option value="">@lang('select')</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->uuid}}">{{$category->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_manufacturer_uuid">@lang('manufacturer')</label>
                                                <select name="s_manufacturer_uuid" id="s_manufacturer_uuid"
                                                        class="form-control">
                                                    <option value="">@lang('select')</option>
                                                    @foreach($manufacturers as $manufacturer)
                                                        <option
                                                            value="{{$manufacturer->uuid}}">{{$manufacturer->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_car_model_uuid">@lang('car_model')</label>
                                                <select name="s_car_model_uuid" id="s_car_model_uuid"
                                                        class="form-control">
                                                    <option value="">@lang('select')</option>
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
                                        <th>@lang('broken_file')</th>
                                        <th>@lang('fixed_file')</th>
                                        <th>@lang('category')</th>
                                        <th>@lang('manufacturer')</th>
                                        <th>@lang('car_model')</th>
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
                    <form action="" id="create_form" method="POST"
                          data-reset="true" class="ajax_form form-horizontal" enctype="multipart/form-data"
                          novalidate>
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="category_uuid">@lang('category')</label>
                                    <select class="category_uuid form-control" id="category_uuid" name="category_uuid"
                                            required>
                                        <option value="">@lang('select')</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->uuid}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="manufacturer_uuid">@lang('manufacturer')</label>
                                    <select class="manufacturer_uuid form-control" id="manufacturer_uuid"
                                            name="manufacturer_uuid" required>
                                        <option value="">@lang('select')</option>
                                        @foreach($manufacturers as $manufacturer)
                                            <option value="{{$manufacturer->uuid}}">{{$manufacturer->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="car_model_uuid">@lang('car_model')</label>
                                    <select name="car_model_uuid" id="car_model_uuid" class="form-control">
                                        <option value="">@lang('select')</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="file">@lang('broken_file')</label>
                                <div class="form-group">
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="" src="" alt=""/>
                                        </div>
                                        <div>
                                                    <span class="btn btn-secondary btn-file">
                                                                <span
                                                                    class="fileinput-new"> @lang('select_file')</span>
                                                                <span
                                                                    class="fileinput-exists"> @lang('select_file')</span>
                                                        <input type="file" name="broken_file"></span>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
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
                    </button>{{--                            <button type="button" form="create_form" class="btn btn-primary">Send message</button>--}}
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
                    <form action="" id="edit_form" method="POST"
                          data-reset="true" class="ajax_form form-horizontal" enctype="multipart/form-data"
                          novalidate>
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_category_uuid">@lang('category')</label>
                                    <select class="category_uuid form-control" id="edit_category_uuid"
                                            name="category_uuid" required>
                                        <option value="">@lang('select')</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->uuid}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_manufacturer_uuid">@lang('manufacturer')</label>
                                    <select class="manufacturer_uuid form-control" id="edit_manufacturer_uuid"
                                            name="manufacturer_uuid" required>
                                        <option value="">@lang('select')</option>
                                        @foreach($manufacturers as $manufacturer)
                                            <option value="{{$manufacturer->uuid}}">{{$manufacturer->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="edit_car_model_uuid">@lang('car_model')</label>
                                    <select name="car_model_uuid" id="edit_car_model_uuid" class="form-control">
                                        <option value="">@lang('select')</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="edit_file">@lang('broken_file')</label>
                                <div class="form-group">
                                    <div class="fileinput fileinput-exists"
                                         data-provides="fileinput">
                                        <div class="fileinput-preview thumbnail"
                                             data-trigger="fileinput"
                                             style="width: 200px; height: 150px;">
                                            <img id="edit_src_file" src="" alt=""/>
                                        </div>
                                        <div>
                                                    <span class="btn btn-secondary btn-file">
                                                                <span
                                                                    class="fileinput-new"> @lang('select_file')</span>
                                                                <span
                                                                    class="fileinput-exists"> @lang('select_file')</span>
                                                        <input type="file" name="broken_file"></span>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
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
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')

@endsection
@section('scripts')
    <script>
        var url = '{{url(app()->getLocale()."/admin/fixes")}}/';

        var oTable = $('#datatable').DataTable({
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "oLanguage": {
                @if(app()->isLocale('ar'))
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

                @endif// "oPaginate": {"sPrevious": '<-', "sNext": '->'},
                "oPaginate": {
                    // remove previous & next text from pagination
                    "sPrevious": '&nbsp;',
                    "sNext": '&nbsp;'
                }
            },
            'columnDefs': [
                {
                    "targets": 1,
                    "visible": false
                },
                {
                    'targets': 0,
                    "searchable": false,
                    "orderable": false
                },
            ],
            // dom: 'lrtip',
            "order": [[1, 'asc']],
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '{{ url(app()->getLocale().'/admin/fixes/indexTable')}}',
                data: function (d) {
                    d.name = $('#s_name').val();
                    d.category_uuid = $('#s_category_uuid').val();
                    d.manufacturer_uuid = $('#s_manufacturer_uuid').val();
                    d.car_model_uuid = $('#s_car_model_uuid').val();
                }
            },
            columns: [
                {
                    "render": function (data, type, full, meta) {
                        return `<td class="checkbox-column sorting_1">
                                       <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="table_ids custom-control-input dt-checkboxes"
                                         name="table_ids[]" value="` + full.uuid + `" id="checkbox` + full.uuid + `" >
                                    <label class="custom-control-label" for="checkbox` + full.uuid + `"></label>
                                </div></td>`;
                    }
                },

                {data: 'uuid', name: 'uuid'},
                {
                    "render": function (data, type, full, meta) {
                        return `<a href="` + full.broken_file + `" target="_blank">@lang('download_file')</a>`;
                    }
                },
                {
                    "render": function (data, type, full, meta) {
                        return `<a href="` + full.fixed_file + `" target="_blank">@lang('download_file')</a>`;
                    }
                },
                {data: 'category_name', name: 'category_uuid'},
                {data: 'manufacturer_name', name: 'manufacturer_uuid'},
                {data: 'car_model_name', name: 'car_model_uuid'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $(document).ready(function () {
            var car_models_list = {
                @foreach($manufacturers as $manufacturer)
                'manufacturer_{{$manufacturer->uuid}}': [
                        @foreach($manufacturer->car_models as $car_model)
                    {
                        id: '{{$car_model->uuid}}',
                        text: '{{$car_model->name}}',
                    },

                    @endforeach
                ],
                @endforeach
            };

            $(document).on("change", "#manufacturer_uuid", function (e) {
                var value = $(this).val();
                $("#car_model_uuid").html('<option selected value="">@lang('select')</option>')
                $("#car_model_uuid").select2({
                    data: car_models_list['manufacturer_' + value]
                }).trigger("change");
            });
            $(document).on("change", "#s_manufacturer_uuid", function (e) {
                var value = $(this).val();
                $("#s_car_model_uuid").html('<option selected value="">@lang('select')</option>')
                $("#s_car_model_uuid").select2({
                    data: car_models_list['manufacturer_' + value]
                }).trigger("change");
            });
            $(document).on("change", "#edit_manufacturer_uuid", function (e) {
                var value = $(this).val();
                $("#edit_car_model_uuid").html('<option selected value="">@lang('select')</option>')
                $("#edit_car_model_uuid").select2({
                    data: car_models_list['manufacturer_' + value]
                }).trigger("change");
            });

            $(document).on('click', '.edit_btn', function (event) {
                var button = $(this)
                var uuid = button.data('uuid')
                $('#edit_form').attr('action', url + uuid)
                @foreach(locales() as $key => $value)
                $('#edit_name_{{$key}}').val(button.data('name_{{$key}}'))
                @endforeach
                var user_uuid = button.data('user_uuid')
                $('#edit_category_uuid').val(button.data('category_uuid')).trigger('change')
                $('#edit_manufacturer_uuid').val(button.data('manufacturer_uuid')).trigger('change')
                $('#edit_car_model_uuid').val(button.data('car_model_uuid')).trigger('change')
                $('#edit_src_file').attr('src', button.data('file'))
            });
            $(document).on('click', '#create_btn', function (event) {
                $('#create_form').attr('action', url);
            });
        });

    </script>
@endsection
