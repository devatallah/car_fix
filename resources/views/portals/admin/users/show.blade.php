@extends('admin.app')
@section('title')
    @lang('users')
@endsection
@section('styles')
@endsection
@section('content')

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('users')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{url('/admin')}}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{url('/admin/users')}}">@lang('users')</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">

            <section id="">
                <div class="row" id="user_div">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="head-label">
                                    <h4 class="card-title">@lang('User Details')</h4>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                    <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>@lang('name')</label>
                                            <input type="text" class="search_input form-control"
                                                   disabled
                                                   value="{{$user->name}}" min="1"
                                                   placeholder="@lang('name')">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>@lang('email')</label>
                                            <input type="text" class="search_input form-control"
                                                   disabled
                                                   value="{{$user->email}}" min="1"
                                                   placeholder="@lang('email')">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label>@lang('mobile')</label>
                                            <input type="text" class="search_input form-control"
                                                   disabled
                                                   value="{{$user->mobile}}" min="1"
                                                   placeholder="@lang('mobile')">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="head-label">
                                    <h4 class="card-title">@lang('User Subscriptions')</h4>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="search_form">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="s_plan_uuid">@lang('plan')</label>
                                                <select name="s_plan_uuid" id="s_plan_uuid"
                                                        class="search_input form-control">
                                                    <option value="">@lang('select')</option>
                                                    @foreach($plans as $plan)
                                                        <option value="{{$plan->uuid}}">{{$plan->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-3" style="margin-top: 20px">
                                            <div class="form-group">
                                                <button id="search_btn" class="btn btn-outline-info"
                                                        type="submit">
                                                    <span><i class="fa fa-search"></i> @lang('search')</span>
                                                </button>
                                                <button id="clear_btn" class="btn btn-outline-secondary"
                                                        type="submit">
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
                                        <th>@lang('number')</th>
                                        <th>@lang('user')</th>
                                        <th>@lang('plan')</th>
                                        <th>@lang('quantity')</th>
                                        <th>@lang('price')</th>
                                        <th>@lang('total_price')</th>
                                        <th>@lang('start_date')</th>
                                        <th>@lang('days')</th>
                                        <th>@lang('status')</th>
                                        <th>@lang('create_date')</th>
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


@endsection
@section('js')

@endsection
@section('scripts')
    <script>
        var url = '{{url(app()->getLocale()."/admin/subscriptions")}}/';

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
                url: '{{ url(app()->getLocale().'/admin/subscriptions/indexTable')}}',
                data: function (d) {
                    d.user_uuid = '{{$user->uuid}}';
                    d.restaurant_uuid = $('#s_restaurant_uuid').val();
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
                {data: 'subscription_no', name: 'subscription_no'},
                {data: 'user_name', name: 'user_uuid'},
                {data: 'plan_title', name: 'plan_uuid'},
                {data: 'quantity', name: 'quantity'},
                {data: 'price', name: 'price'},
                {data: 'total_price', name: 'total_price'},
                {data: 'start_date', name: 'start_date'},
                {data: 'days', name: 'days'},
                {data: 'status', name: 'status'},
                {data: 'create_date', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $(document).ready(function () {
            oTable.on('draw', function () {
                $("#select_all").prop("checked", false)
                $('.all_status_btn').prop('disabled', 'disabled');
            });
            $('#s_user_uuid').select2({
                dir: 'ltr',
                placeholder: "@lang('select')",
                minimumInputLength: 3,
                ajax: {
                    url: '{{url('/admin/get_users')}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: $.trim(params.term)
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true,
                }
            });
            $(document).on('click', '.edit_btn', function (event) {
                var button = $(this)
                var uuid = button.data('uuid')
                var image = button.data('image')
                $('#edit_form').attr('action', url + uuid)
                @foreach(locales() as $key => $value)
                $('#edit_name_{{$key}}').val(button.data('name_{{$key}}'))
                @endforeach
                $('#edit_image').attr('src', image)
            });
            $(document).on('click', '#create_btn', function (event) {
                $('#create_form').attr('action', url);
            });
        });

    </script>

@endsection
