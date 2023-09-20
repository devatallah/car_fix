@extends('portals.admin.app')

@section('title')
    @lang('settings')
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">@lang('settings')</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin') }}">@lang('home')</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/admin/settings') }}">@lang('settings')</a>
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
                                    <h4 class="card-title">@lang('settings')</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                @if (\Session::has('success'))
                                    <div class="alert alert-success" role="alert">
                                        <p class="text-white">{{ \Session::get('success') }}</p>
                                    </div>
                                @endif

                                <form id="search_form" action="/admin/settings" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="api_key">@lang('api_key')</label>
                                                <input id="api-key" name="api_key" type="text"
                                                    class="search_input form-control" placeholder="@lang('api_key')"
                                                    value="{{ $setting ? $setting->api_key : '' }}">
                                                @error('api_key')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3" style="margin-top: 20px">
                                            <div class="form-group">
                                                <button class="btn btn-outline-info" type="submit">
                                                    <span><i class="fa fa-paper-plane"></i> @lang('send')</span>
                                                </button>
                                                <button class="btn btn-outline-primary" id="generate" type="button">
                                                    <span><i class="fas fa-magic"></i> @lang('generate')</span>
                                                </button>
                                            </div>
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

@section('scripts')
    <script>
        $('#generate').on('click', function(ev) {
            ev.preventDefault();

            let token = generateAlphanumericToken(32);

            $('#api-key').val(token);
        })

        function generateAlphanumericToken(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let token = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                token += characters[randomIndex];
            }

            return token;
        }
    </script>
@endsection
