@extends('portals.user.app')
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
                                <li class="breadcrumb-item"><a href="{{url('/user')}}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{url('/user/fixes')}}">@lang('fixes')</a>
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
                                <form action="{{url(app()->getLocale()."/user/fixes")}}" id="create_form" method="POST"
                                      data-reset="true" class="form-horizontal" enctype="multipart/form-data"
                                      novalidate>
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="category_uuid">@lang('category')</label>
                                                <select class="category_uuid form-control" id="category_uuid"
                                                        name="category_uuid"
                                                        required>
                                                    <option value="">@lang('select')</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->uuid}}">{{$category->category_name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('category_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('category_uuid') }}</strong>
                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="manufacturer_uuid">@lang('manufacturer')</label>
                                                <select class="manufacturer_uuid form-control" id="manufacturer_uuid"
                                                        name="manufacturer_uuid" required>
                                                    <option value="">@lang('select')</option>
                                                    @foreach($manufacturers as $manufacturer)
                                                        <option
                                                            value="{{$manufacturer->uuid}}">{{$manufacturer->name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('manufacturer_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('manufacturer_uuid') }}</strong>
                                    </span>
                                                @endif
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="form-group">
                                                <label for="car_model_uuid">@lang('car_model')</label>
                                                <select name="car_model_uuid" id="car_model_uuid" class="form-control">
                                                    <option value="">@lang('select')</option>
                                                </select>
                                                @if ($errors->has('car_model_uuid'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('car_model_uuid') }}</strong>
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
                                                @lang('save')
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

        });


    </script>
@endsection
