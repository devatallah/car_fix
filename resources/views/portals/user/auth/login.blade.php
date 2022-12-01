<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-layout="dark-layout" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>Login Page</title>
    <link rel="apple-touch-icon" href="{{asset('portals/app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('portals/app-assets/images/ico/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/vendors/css/vendors.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css/pages/authentication.css')}}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('portals/assets/css/style.css')}}">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="auth-wrapper auth-basic px-2">
                <div class="auth-inner my-2">
                    <!-- Login basic -->
                    <div class="card mb-0">
                        <div class="card-body">
                            <a href="index.html" class="brand-logo">
                                <img src="{{asset('portals/app-assets/images/logo.svg')}}" style="width: 120px" alt="">
                            </a>


                            <form class="auth-login-form mt-2" method="POST" action="{{ route('user_login') }}">
                                {{ csrf_field() }}
                                <div class="mb-1">
                                    <div class="form-group">
                                        <label for="login-email" class="form-label">@lang('email')</label>
                                        <input type="text" class="form-control" id="login-email" name="email"
                                               placeholder="@lang('email')" aria-describedby="login-email" tabindex="1" autofocus />
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="login-password">@lang('password')</label>
                                    </div>
                                    <div class="input-group input-group-merge form-password-toggle">
                                        <input type="password" class="form-control form-control-merge" id="login-password" name="password" tabindex="2" placeholder="@lang('password')" aria-describedby="login-password" />
                                        <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                    </div>
                                </div>

                                <div class="mb-1">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="remember-me" name="remember" tabindex="3" />
                                            <label class="custom-control-label" for="remember-me"> @lang('remember_me') </label>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-block" tabindex="4">@lang('sign_in')</button>
                            </form>



                        </div>
                    </div>
                    <!-- /Login basic -->
                </div>
            </div>

        </div>
    </div>
</div>
<!-- END: Content-->


<!-- BEGIN: Vendor JS-->
<script src="{{asset('portals/app-assets/vendors/js/vendors.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('portals/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('portals/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('portals/app-assets/js/core/app.js')}}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{asset('portals/app-assets/js/scripts/pages/auth-login.js')}}"></script>
<!-- END: Page JS-->

<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>
</body>
<!-- END: Body-->

</html>
