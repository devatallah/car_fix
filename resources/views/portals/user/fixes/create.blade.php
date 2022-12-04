@extends('portals.user.app')
@section('title')
    @lang('fixes')
@endsection
@section('styles')
    <style>
        #myProgress {
            width: 100%;
            background-color: #ddd;
        }

        #myBar {
            width: 1%;
            height: 30px;
            background-color: #04AA6D;
        }
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
                        <h2 class="content-header-title float-left mb-0">@lang('Solutions')</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section class="app-user-view-account">
                <div class="row">
                    <!-- User Sidebar -->
                    <div class="col-12 order-1 order-md-0">
                        <!-- User Card -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-around my-2 pt-75">
                                    <div class="d-flex align-items-start me-2">
                                        <div class="ms-75">
                                            <h4 class="mb-0">Name</h4>
                                            <span>{{auth()->user()->name}}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="ms-75">
                                            <h4 class="mb-0">Email</h4>
                                            <span>{{auth()->user()->email}}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="ms-75">
                                            <h4 class="mb-0">License EXP.</h4>
                                            <span>{{auth()->user()->license_expire_date}}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="ms-75">
                                            <h4 class="mb-0">Credit</h4>
                                            <span>{{auth()->user()->balance}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /User Card -->
                        <!-- Plan Card -->
                        <!-- /Plan Card -->
                    </div>
                    <!--/ User Sidebar -->

                    <!-- User Content -->
                    <!--/ User Content -->
                </div>
            </section>
            <section id="">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="head-label">
                                    <div class="form-gruop">
                                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                                data-bs-target="#create_modal"><span><i class="fa fa-plus"></i> @lang('Request New Solution')</span>
                                        </button>

                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">
                                        <a class="btn btn-outline-primary" href="" type="button" ><span><i class="fa fa-recycle"></i> @lang('Refresh')</span>
                                        </a>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{url("/user/fixes")}}" id="create_form" method="POST"
                                      data-reset="true" class="form-horizontal" enctype="multipart/form-data"
                                      novalidate>
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-1">
                                                <label for="solution_uuid">@lang('Solution Type')</label>
                                                <select class="solution_uuid form-control" id="solution_uuid"
                                                        name="solution_uuid"
                                                        required>
                                                    <option value="">@lang('select')</option>
                                                    @foreach($solutions as $solution)
                                                        <option
                                                            value="{{$solution->uuid}}">{{$solution->solution_name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('solution_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('solution_uuid') }}</strong>
                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div id="ecus" class="form-group ps-1 pt-1 mb-1"
                                                 style="background-color: #2B344D; height: 500px; overflow:auto;">
                                            </div>
                                        </div>
                                        <div class="col-6 mt-1">
                                        <div class="ms-5 col-6">
                                            <div class="d-inline-flex">
                                            <div class="demo-vertical-spacing">
                                                <label for="solution_uuid">@lang('Origin File')</label>
                                                <div class="form-group">
                                                <span class="btn btn-secondary btn-file">
                                                            <span class="fileinput-new"> @lang('select file')</span>
                                                            <input type="file" name="broken_file">
                                                        </span>
                                                @if ($errors->has('broken_file'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('broken_file') }}</strong>
                                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                                <div class="demo-vertical-spacing">
                                                    <label for="">&nbsp</label>
                                                    <div class="form-group">
                                                    <button type="submit" form="create_form" class="ms-1 btn btn-primary">
                                                    @lang('solution')
                                                </button>

                                            </div>
                                            </div>
                                        </div>
                                        </div>
{{--                                            <div class=" col-12">--}}
{{--                                            <div id="myProgress">--}}
{{--                                                    <div id="myBar">--}}

{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">@lang('create')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{url("/user/ecu_requests")}}" id="create_request_form" method="POST"
                              data-reset="true" class="ajax_form form-horizontal" enctype="multipart/form-data"
                              novalidate>
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-12 mb-1">
                                    <div class="form-group">
                                        <label for="solution">@lang('solution')</label>
                                        <input type="text" class="solution form-control" id="solution" name="solution"
                                               required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 mb-1">
                                    <div class="form-group">
                                        <label for="brand">@lang('brand')</label>
                                        <input type="text" class="brand form-control" id="brand" name="brand"
                                               required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12 mb-1">
                                    <div class="form-group">
                                        <label for="ecu">@lang('ecu')</label>
                                        <input type="text" class="ecu form-control" id="ecu" name="ecu"
                                               required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="create_request_form" class="submit_btn btn btn-primary">
                            <i class="fa fa-spinner fa-spin" style="display: none;"></i>
                            @lang('save')
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">@lang('close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')

@endsection
@section('scripts')
    <script>

        var url = '{{url("/user/fixes")}}/';
        // var i = 0;
        // function move(e) {
        //     e.preventDefault()
        //     console.log(444)
        //     if (i == 0) {
        //         i = 1;
        //         var elem = document.getElementById("myBar");
        //         var width = 1;
        //         var id = setInterval(frame, 10);
        //         function frame() {
        //             if (width >= 100) {
        //                 clearInterval(id);
        //                 i = 0;
        //             } else {
        //                 width++;
        //                 elem.style.width = width + "%";
        //             }
        //         }
        //     }
        // }
        $(document).ready(function () {
            $(document).on('change', '#solution_uuid', function (e) {
                e.preventDefault();
                $('#ecus').html('')
                var urls = '{{url("/get_solution_brands")}}' + '?solution_uuid=' + $(this).val();
                $.ajax({
                    url: urls,
                    method: 'GET',
                    type: 'GET',
                    success: function (data) {
                        text = ``
                        $.each(data, function (index, value) {
                            text += `                                                <div class="mb-1">
                                                    <h5 class="brand">` + value.text + `</h5>
                                                    <div class="ms-1 demo-vertical-spacing brand_ecus" style="display: none">`
                            $.each(value.children, function (index, value) {

                                text += `<div class="form-check form-check">
                                                            <input class="form-check-input" type="radio"
                                                                   name="ecu_uuid" id="` + value.id + `"
                                                                   value="` + value.id + `" style="width: 0.8rem;
    height: 0.8rem;
    margin-top: 0.45rem; margin-left: -1.15rem;">
                                                            <label class="form-check-label"
                                                                   for="` + value.id + `"><small>` + value.text + `</small></label>
                                                        </div>`
                            });
                            text += `                  </div>
                                                </div>`
                        });
                        $('#ecus').append(text)

                    }

                });
            });
            $(document).on('click', '.brand', function (e) {
                console.log(333)
                $(this).parent().find('.brand_ecus').toggle();
            });
        });

    </script>
@endsection
