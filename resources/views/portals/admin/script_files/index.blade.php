@extends('portals.admin.app')

@section('title')
    @lang('script_files')
@endsection

@section('styles')
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('script_files')</h2>
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
                                    <h4 class="card-title">Script Files ID: - {{ $script->id }}</h4>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#create_modal" id="create_btn"><span><i class="fa fa-plus"></i>
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
                            </div>
                            <div class="table-responsive card-datatable">
                                <table class="table" id="datatable">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-column sorting_disabled" style="width: 35px;"
                                                aria-label=" Record Id ">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                        class="table_ids custom-control-input dt-checkboxes"
                                                        id="select_all">
                                                    <label class="custom-control-label" for="select_all"></label>
                                                </div>
                                            </th>
                                            <th>ID</th>
                                            <th>@lang('file')</th>
                                            <th style="width: 225px;">@lang('actions')</th>
                                        </tr>
                                    </thead>
                                </table>
                                <tbody></tbody>
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
                        <input type="hidden" name="script_uuid" value="{{ $script->uuid }}">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                        <div>
                                            <label for="file">@lang('select_file')</label>
                                            <input type="file" class="form-control" name="file[0]"
                                                id="file" accept="text/bin">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info" onclick="appendRecord()">Add record</button>
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
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                                <label for="file">@lang('file')</label>
                                <div class="form-group">
                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                        <div>
                                            <span class="btn btn-secondary btn-file">
                                                <label for="file">@lang('select_file')</label>
                                                <input type="file" class="form-control" name="file"
                                                    id="file">
                                            </span>
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
        var url = '{{ url('/admin/script_files') }}/';
        var url2 = '{{ url('/admin/script_files') }}';

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
                    // remove previous & next text from pagination
                    "sPrevious": '&nbsp;',
                    "sNext": '&nbsp;'
                }
            },
            'columnDefs': [{
                'targets': 0,
                "searchable": false,
                "orderable": false
            }, ],
            // dom: 'lrtip',
            "order": [
                [1, 'asc']
            ],
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '{{ url('/admin/script_files/indexTable/'.$script->uuid) }}',
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
                    data: 'id',
                    name: 'id'
                },
                {
                    "render": function(data, type, full, meta) {
                        return `<a href="` + full.file + `" target="_blank">@lang('download_file')</a>`;
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
                $('#edit_form').attr('action', url2 + '/' + uuid)
            });
            $(document).on('click', '#create_btn', function(event) {
                $("#create_form .new_record").remove();
                $('#create_form').attr('action', url2);
            });
        });
    </script>

    <script>
        var index = 0;
        const template = function() {
            index++;
            return `<div class="row new_record">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="fileinput fileinput-exists" data-provides="fileinput">
                                        <div>
                                            <label for="file">@lang('select_file')</label>
                                            <input type="file" class="form-control" name="file[${index}]"
                                                id="file">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
        };
        function appendRecord() {
            $("#create_form").append(template());
        }
    </script>
@endsection