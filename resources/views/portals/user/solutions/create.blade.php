@extends('portals.user.app')
@section('title')
    @lang('Solutions')
@endsection
@section('styles')
    <style>
        .pac-container {
            z-index: 1051 !important;
        }

    </style>
@endsection
@section('content')
    <div class="content-wrapper" style="height: 200px; width: 700px;margin-left: 260px">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12 d-inline-flex">
                        <img style="width: 100px" src="{{asset('portals/app-assets/images/logo.svg')}}" alt="">
                        <div style="padding-top: 20px;padding-left: 20px">
                        <h2 class="content-header-title float-left mb-0">@lang('MAGICSOLUTION')</h2>
                        </div></div>
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
                            <div class="card-body" style="padding:0px">
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
                                            <h4 class="mb-0">Balance</h4>
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
                                                data-bs-target="#create_modal"><span><i class="fa fa-plus"></i> @lang('Request New Solutions')</span>
                                        </button>

                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="form-gruop">
                                        <a class="btn btn-outline-primary" href="" type="button"><span><i
                                                    class="fa fa-recycle"></i> @lang('Refresh')</span>
                                        </a>

                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="" id="create_form" method="POST"
                                      data-reset="true" class="form-horizontal" enctype="multipart/form-data"
                                      novalidate>
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-1">
                                                <label for="module_uuid">@lang('Solution Type')</label>
                                                <select class="module_uuid form-control" id="module_uuid"
                                                        name="module_uuid"
                                                        required>
                                                    <option value="">@lang('select')</option>
                                                    @foreach($modules as $module)
                                                        <option
                                                            value="{{$module->uuid}}" {{ $loop->first ? 'selected' : '' }}>{{$module->module_name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('module_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('module_uuid') }}</strong>
                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div id="ecus" class="form-group ps-1 pt-1 mb-1"
                                                 style="background-color: #2B344D; height: 500px; overflow:auto;">
                                                @foreach($brands as $brand)
                                                    <div class="mb-1">
                                                        <h5 class="brand">{{$brand['text']}}</h5>
                                                        <div class="ms-1 demo-vertical-spacing brand_ecus"
                                                             style="display: none">
                                                            @foreach($brand['children'] as $item)
                                                                <div class="form-check form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                           name="ecu_uuid" id="{{$item['id']}}"
                                                                           value="{{$item['id']}}" style="width: 0.8rem;
    height: 0.8rem;
    margin-top: 0.45rem; margin-left: -1.15rem;">
                                                                    <label class="form-check-label"
                                                                           for="{{$item['id']}}"><small>{{$item['text']}}</small></label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-6 mt-1">
                                            <div class="ms-4 col-6">
                                                <div class="d-inline-flex">
                                                    <div class="demo-vertical-spacing">
                                                        <label for="module_uuid">@lang('Original File')</label>
                                                        <div class="form-group">
                                                <span class="btn btn-secondary btn-file">
                                                            <span style="width:50px;" class="fileinput-new">@lang('Upload File')</span>
                                                            <input type="file" name="Original File">
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
                                                            <button type="submit" form="create_form"
                                                                    class="ms-1 submit_btn btn btn-primary">
                                                                <i class="fa fa-spinner fa-spin"
                                                                   style="display: none;"></i>
                                                                @lang('Solution')
                                                            </button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style=" height: 200px; weight:200px; margin-left:45px; margin-top:16px;">
                                            <div id="results"
                                                 style="background-color: #2B344D;">
                                                <p><b style="font-size:small; padding:8px;">Selected Module: </b> <span id="module_result"></span></p>
                                                <p><b style="font-size:small;padding:8px;">Selected Brand: </b> <span id="brand_result"></span></p>
                                                <p><b style="font-size:small;padding:8px;">Selected ECU: </b> <span id="ecu_result"></span></p>
                                                <p><b style="font-size:small;padding:8px;">Selected File: </b> <span id="file_result"></span></p>
                                                <p><b style="font-size:small;padding:8px;">File Size: </b> <span id="file_size_result"></span></p>
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
                                        <label for="module">@lang('module')</label>
                                        <input type="text" class="module form-control" id="module" name="module"
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

        var url = '{{url("/user/solutions")}}/';
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
            $(document).on('change', '#module_uuid', function (e) {
                e.preventDefault();
                $('#ecus').html('')
                var urls = '{{url("/get_module_brands")}}' + '?module_uuid=' + $(this).val();
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


            $(document).on('submit', '#create_form', function (e) {
                // $('.submit_btn').prop('disabled', true);
                e.preventDefault();
                var form = $(this);
                var url = $(this).attr('action');
                var method = $(this).attr('method');
                var reset = $(this).data('reset');
                var Data = new FormData(this);
                $('.submit_btn').attr('disabled', 'disabled');
                $('.fa-spinner.fa-spin').show();
                $.ajax({
                    url: "{{url("/user/solutions")}}",
                    type: method,
                    data: Data,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('.invalid-feedback').html('');
                        $('.is-invalid ').removeClass('is-invalid');
                        form.removeClass('was-validated');
                    }
                }).done(function (data) {
                    if (data.status) {
                        toastr.success('@lang('done_successfully')', '', {
                            rtl: isRtl
                        });
                        if (reset === true) {
                            console.log(isRtl)
                            form[0].reset();
                            $('.submit_btn').removeAttr('disabled');
                            $('.fa-spinner.fa-spin').hide();
                            $('.modal').modal('hide');
                            // oTable.draw();
                        } else {
                            var url = $('#cancel_btn').attr('href');
                            window.location.replace(url);
                        }
                        $('#module_result').html(data.module_name)
                        $('#brand_result').html(data.brand_name)
                        $('#ecu_result').html(data.ecu_name)
                        $('#file_result').html(data.file_name)
                        $('#file_size_result').html(data.file_size)
                        window.open(data.url, '_blank');
                    } else {
                        if (data.message) {
                            toastr.error(data.message, '', {
                                rtl: isRtl
                            });
                        } else {
                            toastr.error('@lang('something_wrong')', '', {
                                rtl: isRtl
                            });
                        }
                        $('.submit_btn').removeAttr('disabled');
                        $('.fa-spinner.fa-spin').hide();
                    }
                }).fail(function (data) {
                    if (data.status === 422) {
                        var response = data.responseJSON;
                        $.each(response.errors, function (key, value) {
                            var str = (key.split("."));
                            if (str[1] === '0') {
                                key = str[0] + '[]';
                            }
                            $('[name="' + key + '"], [name="' + key + '[]"]').addClass('is-invalid');
                            $('[name="' + key + '"], [name="' + key + '[]"]').closest('.form-group').find('.invalid-feedback').html(value[0]);
                        });
                    } else {
                        toastr.error('@lang('something_wrong')', '', {
                            rtl: isRtl
                        });
                    }
                    $('.submit_btn').removeAttr('disabled');
                    $('.fa-spinner.fa-spin').hide();

                });
            });
        });

    </script>
@endsection
