@extends('portals.admin.app')
@section('title')
    @lang('Profile')
@endsection
@section('styles')
@endsection
@section('content')

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('Profile')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{url('/admin')}}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{url('/admin/profile')}}">@lang('Profile')</a>
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
                                    <h4 class="card-title">@lang('Profile')</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile"
                                           role="tab" aria-controls="profile" aria-selected="true">@lang('profile')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password"
                                           role="tab" aria-controls="password" aria-selected="false">@lang('password')</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                        <form action="{{url('/admin/profile')}}"
                                              id="profile-form"
                                              data-reset="false" method="post"
                                              class="ajax_form form-horizontal" enctype="multipart/form-data"
                                              novalidate>
                                            {{csrf_field()}}
                                            {{method_field('put')}}
                                            <div class="form-body offset-2">
                                                <div class="form-group row">
                                                    <label for="name"
                                                           class="col-md-3 col-form-label">@lang('name')
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input type="text" name="name" id="name"
                                                               class="form-control"
                                                               value="{{auth()->user()->name}}"
                                                               required/>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="email"
                                                           class="col-md-3 col-form-label">@lang('email')
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input type="email" name="email" id="email"

                                                               class="form-control"
                                                               value="{{auth()->user()->email}}" required/>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="mobile"
                                                           class="col-md-3 col-form-label">@lang('mobile')
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input type="text" name="mobile" id="mobile"

                                                               class="form-control"
                                                               value="{{auth()->user()->mobile}}" required/>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="submit_btn btn btn-primary">
                                                    <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                                                    @lang('save')
                                                </button>
                                                <a href="{{url('/admin/profile')}}" id="cancel_btn"
                                                   class="btn btn-outline-danger">
                                                    @lang('cancel')
                                                </a>
                                            </div>


                                        </form>
                                    </div>
                                    <div class="tab-pane" id="password" aria-labelledby="password-tab" role="tabpanel">
                                        <form action="{{url('/admin/password')}}"
                                              id="password-form"
                                              method="post" data-reset="true"
                                              class="ajax_form form-horizontal" enctype="multipart/form-data"
                                              novalidate>
                                            {{csrf_field()}}
                                            {{method_field('put')}}
                                            <div class="form-body offset-2">
                                                <div class="form-group row">
                                                    <label for="current_password"
                                                           class="col-md-3 col-form-label">@lang('current_password')
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input type="password" name="current_password"
                                                               id="current_password" placeholder="@lang('current_password')"
                                                               class="form-control" value="" required/>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password"
                                                           class="col-md-3 col-form-label">@lang('password')
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input type="password" name="password" id="password"
                                                               placeholder="@lang('password')"
                                                               class="form-control" value="" required/>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password_confirmation"
                                                           class="col-md-3 col-form-label">@lang('password_confirmation')
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input type="password" name="password_confirmation"
                                                               id="password_confirmation" placeholder="@lang('password_confirmation')"
                                                               class="form-control" value="" required/>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="submit_btn btn btn-primary">
                                                    <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                                                    @lang('save')
                                                </button>
                                                <a href="{{url('/admin/profile')}}" id="cancel_btn"
                                                   class="btn btn-outline-danger">
                                                    @lang('cancel')
                                                </a>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
    <div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">@lang('name')</label>
                                    <input type="text" class="form-control" placeholder="@lang('name')" name="name"
                                           id="name">
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
                    </button>{{--                            <button type="button" form="create_form" class="btn btn-primary">Send message</button>--}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                        <div class="form-group">
                            <label for="edit_name" class="col-form-label">@lang('name')</label>
                            <input type="text" class="form-control" placeholder="@lang('name')" name="name"
                                   id="edit_name">
                            <div class="invalid-feedback"></div>
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
        var url = '{{url("/admin/profile")}}/';

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
                url: '{{ url('/admin/profile/indexTable')}}',
                data: function (d) {
                    d.name = $('#s_name').val();
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
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $(document).ready(function () {
            $(document).on('click', '.edit_btn', function (event) {
                var button = $(this)
                var uuid = button.data('uuid')
                var name = button.data('name')
                $('#edit_form').attr('action', url + uuid)
                $('#edit_name').val(name)
                $('#edit_image').attr('src', image)
            });
            $(document).on('click', '#create_btn', function (event) {
                $('#create_form').attr('action', url);
            });
        });

    </script>

@endsection
