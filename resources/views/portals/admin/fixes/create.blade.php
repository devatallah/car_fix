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
                            </div>
                            <div class="card-body">
                                <form action="{{url("/admin/fixes")}}" id="create_form" method="POST"
                                      data-reset="true" class="form-horizontal" enctype="multipart/form-data"
                                      novalidate>
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="solution_uuid">@lang('solution')</label>
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
                                            <div class="form-group">
                                                <label for="brand_uuid">@lang('brand')</label>
                                                <select class="brand_uuid form-control" id="brand_uuid"
                                                        name="brand_uuid" required>
                                                    <option value="">@lang('select')</option>
                                                </select>
                                                @if ($errors->has('brand_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('brand_uuid') }}</strong>
                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="ecu_uuid">@lang('ecu')</label>
                                                <select name="ecu_uuid" id="ecu_uuid"
                                                        class="js-example-data-array form-control">
                                                    <option value="">@lang('select')</option>
                                                </select>
                                                @if ($errors->has('ecu_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('ecu_uuid') }}</strong>
                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="file">@lang('broken_file')</label>
                                            <div class="form-group">
                                                <div class="fileinput fileinput-exists"
                                                     data-provides="fileinput">
                                                    <div class="fileinput-preview thumbnail"
                                                         data-trigger="fileinput"
                                                         style="width: 200px; height: 150px;border-style: solid;">
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
                                                    @if ($errors->has('broken_file'))
                                                        <span class="help-block">
                                        <strong>{{ $errors->first('broken_file') }}</strong>
                                    </span>
                                                    @endif
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" form="create_form" class="btn btn-primary">
                                                @lang('fix')
                                            </button>
                                        </div>
                                    </div>
                                </form>
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

        var url = '{{url("/admin/fixes")}}/';
        $(document).ready(function () {
            $(document).on('change', '#solution_uuid', function (e) {
                e.preventDefault();
                var urls = '{{url("/get_solution_brands")}}' + '?solution_uuid='+$(this).val();
                $.ajax({
                    url: urls,
                    method: 'GET',
                    type: 'GET',
                    success: function (data) {
                        console.log(data.status)
                        $("#brand_uuid").select2({
                            data: data,
                            width: 'auto'
                        });
                    }
                });
            });
            $(document).on('change', '#brand_uuid', function (e) {
                e.preventDefault();
                var solution_uuid = $('#solution_uuid').val();
                var urls = '{{url("/get_solution_brand_ecus")}}' + '?solution_uuid=' + solution_uuid + '&brand_uuid='+$(this).val();
                $.ajax({
                    url: urls,
                    method: 'GET',
                    type: 'GET',
                    success: function (data) {
                        console.log(data.status)
                        $("#ecu_uuid").select2({
                            data: data,
                            width: 'auto'
                        });
                    }
                });
            });
        });


    </script>
@endsection
