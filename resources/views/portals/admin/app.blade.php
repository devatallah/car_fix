<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-layout="dark-layout" data-textdirection="rtl">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Car Fix</title>
    <link rel="apple-touch-icon" href="{{asset('portals/app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('portals/app-assets/images/ico/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
          rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/vendors'.rtl_assets().'.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/extensions/toastr.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/bootstrap-extended.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('portals/app-assets/css'.rtl_assets().'/colors.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/components.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/themes/dark-layout.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/themes/bordered-layout.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/themes/semi-dark-layout.min.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/core/menu/menu-types/vertical-menu.min.css')}}">
    <link rel="stylesheet" type="text/css"
          href="{{asset('portals/app-assets/css'.rtl_assets().'/plugins/extensions/ext-component-toastr.min.css')}}">
    <!-- END: Page CSS-->
    @yield('styles')

    <!-- BEGIN: Custom CSS-->
    @if(LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link rel="stylesheet" type="text/css"
              href="{{asset('portals/app-assets/css'.rtl_assets().'/custom'.rtl_assets().'.min.css')}}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{asset('portals/assets/css/style'.rtl_assets().'.css')}}">
    <!-- END: Custom CSS-->

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

    </style>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click"
      data-menu="vertical-menu-modern" data-col="">

<!-- BEGIN: Header-->
<nav
    class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-dark navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-language">
                <a class="nav-link dropdown-toggle" id="dropdown-flag" href="#" data-bs-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false"><i class="flag-icon flag-icon-{{LaravelLocalization::getCurrentLocaleNative() == 'English' ? 'us' : 'ps'}}"></i><span
                        class="selected-language">{{ LaravelLocalization::getCurrentLocaleNative() }}</span></a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-flag">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a class="dropdown-item"
                                                                                                href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                                                                                data-language="{{ $localeCode }}"><i
                            class="flag-icon flag-icon-{{$localeCode == 'en' ? 'us' : 'ps'}}"></i>{{ $properties['native'] }}</a>
                    @endforeach
</div>
            </li>
            <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link"
                                                           id="dropdown-user" href="#" data-bs-toggle="dropdown"
                                                           aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder">{{auth()->user()->name}}</span><span
                            class="user-status">Admin</span></div>
                    <span class="avatar"><img class="round"
                                              src="{{asset('portals/app-assets/images/portrait/small/avatar-s-11.jpg')}}"
                                              alt="avatar" height="40" width="40"><span
                            class="avatar-status-online"></span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{url('/admin/profile')}}"><i class="mr-50" data-feather="user"></i>
                        Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mr-50" data-feather="power"></i>Logout</a>
                    <form id="logout-form" action="{{ route('admin_logout') }}" method="POST"
                          style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center"><a href="#">
            <h6 class="section-label mt-75 mb-0">Files</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100"
                                   href="app-file-manager.html">
            <div class="d-flex">
                <div class="me-75"><img src="{{asset('portals/app-assets/images/icons/xls.png')}}" alt="png"
                                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing
                        Manager</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;17kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100"
                                   href="app-file-manager.html">
            <div class="d-flex">
                <div class="me-75"><img src="{{asset('portals/app-assets/images/icons/jpg.png')}}" alt="png"
                                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd
                        Developer</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;11kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100"
                                   href="app-file-manager.html">
            <div class="d-flex">
                <div class="me-75"><img src="{{asset('portals/app-assets/images/icons/pdf.png')}}" alt="png"
                                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital
                        Marketing Manager</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;150kb</small>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100"
                                   href="app-file-manager.html">
            <div class="d-flex">
                <div class="me-75"><img src="{{asset('portals/app-assets/images/icons/doc.png')}}" alt="png"
                                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                </div>
            </div>
            <small class="search-data-size me-50 text-muted">&apos;256kb</small>
        </a></li>
    <li class="d-flex align-items-center"><a href="#">
            <h6 class="section-label mt-75 mb-0">Members</h6>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100"
                                   href="app-user-view-account.html">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img
                        src="{{asset('portals/app-assets/images/portrait/small/avatar-s-8.jpg')}}" alt="png"
                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100"
                                   href="app-user-view-account.html">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img
                        src="{{asset('portals/app-assets/images/portrait/small/avatar-s-1.jpg')}}" alt="png"
                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd
                        Developer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100"
                                   href="app-user-view-account.html">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img
                        src="{{asset('portals/app-assets/images/portrait/small/avatar-s-14.jpg')}}" alt="png"
                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing
                        Manager</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100"
                                   href="app-user-view-account.html">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img
                        src="{{asset('portals/app-assets/images/portrait/small/avatar-s-6.jpg')}}" alt="png"
                        height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                </div>
            </div>
        </a></li>
</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion justify-content-between"><a
            class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="me-75" data-feather="alert-circle"></span><span>No results found.</span>
            </div>
        </a></li>
</ul>
<!-- END: Header-->


<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto"><a class="navbar-brand"
                                            href="{{asset('portals/html/rtl/vertical-menu-template-dark/index.html')}}"><span
                        class="brand-logo">
                            <svg viewbox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" height="24">
                                <defs>
                                    <lineargradient id="linearGradient-1" x1="100%" y1="10.5120544%" x2="50%"
                                                    y2="89.4879456%">
                                        <stop stop-color="#000000" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </lineargradient>
                                    <lineargradient id="linearGradient-2" x1="64.0437835%" y1="46.3276743%"
                                                    x2="37.373316%" y2="100%">
                                        <stop stop-color="#EEEEEE" stop-opacity="0" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </lineargradient>
                                </defs>
                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Artboard" transform="translate(-400.000000, -178.000000)">
                                        <g id="Group" transform="translate(400.000000, 178.000000)">
                                            <path class="text-primary" id="Path"
                                                  d="M-5.68434189e-14,2.84217094e-14 L39.1816085,2.84217094e-14 L69.3453773,32.2519224 L101.428699,2.84217094e-14 L138.784583,2.84217094e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L6.71554594,44.4188507 C2.46876683,39.9813776 0.345377275,35.1089553 0.345377275,29.8015838 C0.345377275,24.4942122 0.230251516,14.560351 -5.68434189e-14,2.84217094e-14 Z"
                                                  style="fill:currentColor"></path>
                                            <path id="Path1"
                                                  d="M69.3453773,32.2519224 L101.428699,1.42108547e-14 L138.784583,1.42108547e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L32.8435758,70.5039241 L69.3453773,32.2519224 Z"
                                                  fill="url(#linearGradient-1)" opacity="0.2"></path>
                                            <polygon id="Path-2" fill="#000000" opacity="0.049999997"
                                                     points="69.3922914 32.4202615 32.8435758 70.5039241 54.0490008 16.1851325"></polygon>
                                            <polygon id="Path-21" fill="#000000" opacity="0.099999994"
                                                     points="69.3922914 32.4202615 32.8435758 70.5039241 58.3683556 20.7402338"></polygon>
                                            <polygon id="Path-3" fill="url(#linearGradient-2)" opacity="0.099999994"
                                                     points="101.428699 0 83.0667527 94.1480575 130.378721 47.0740288"></polygon>
                                        </g>
                                    </g>
                                </g>
                            </svg></span>
                    <h2 class="brand-text">Car Fix</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i
                        class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i
                        class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin')}}"><i
                        data-feather="home"></i><span class="menu-title text-truncate"
                                                      data-i18n="Dashboard">Dashboard</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/categories')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="Categories">@lang('categories')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/manufacturers')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="Manufacturers">@lang('manufacturers')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/car_models')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="CarModel">@lang('car_models')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/files')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="File">@lang('files')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/fixes/create')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="Fixe">@lang('create_fix')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/fixes')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="Fixe">@lang('fixes')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/admins')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="Admin">@lang('admins')</span></a>
            </li>
            <li class=" nav-item"><a class="d-flex align-items-center" href="{{url('admin/users')}}"><i
                        data-feather="grid"></i><span class="menu-title text-truncate"
                                                      data-i18n="User">@lang('users')</span></a>
            </li>
            {{--            <li class=" navigation-header"><span data-i18n="User Interface">User Interface</span><i data-feather="more-horizontal"></i>--}}
            {{--            </li>--}}
            {{--            <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="layout"></i><span class="menu-title text-truncate" data-i18n="Page Layouts">Page Layouts</span></a>--}}
            {{--                <ul class="menu-content">--}}
            {{--                    <li><a class="d-flex align-items-center" href="layout-collapsed-menu.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Collapsed Menu">Collapsed Menu</span></a>--}}
            {{--                    </li>--}}
            {{--                    <li><a class="d-flex align-items-center" href="layout-full.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Layout Full">Layout Full</span></a>--}}
            {{--                    </li>--}}
            {{--                    <li><a class="d-flex align-items-center" href="layout-without-menu.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Without Menu">Without Menu</span></a>--}}
            {{--                    </li>--}}
            {{--                    <li class="active"><a class="d-flex align-items-center" href="layout-empty.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Layout Empty">Layout Empty</span></a>--}}
            {{--                    </li>--}}
            {{--                    <li><a class="d-flex align-items-center" href="layout-blank.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Layout Blank">Layout Blank</span></a>--}}
            {{--                    </li>--}}
            {{--                </ul>--}}
            {{--            </li>--}}
        </ul>
    </div>
</div>
<!-- END: Main Menu-->

<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    @yield('content')
</div>
<!-- END: Content-->

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2021<a
                class="ms-25" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent</a><span
                class="d-none d-sm-inline-block">, All rights Reserved</span></span><span
            class="float-md-end d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span></p>
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->


<script>

</script>

<!-- BEGIN: Vendor JS-->
<script src="{{asset('portals/app-assets/vendors/js/vendors.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('portals/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/tables/datatable/datatables.bootstrap5.min.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
<script src="{{asset('portals/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('portals/app-assets/js/core/app-menu.min.js')}}"></script>
<script src="{{asset('portals/app-assets/js/core/app.min.js')}}"></script>
<script src="{{asset('portals/app-assets/js/scripts/customizer.min.js')}}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
{{--<script src="{{asset('portals/app-assets/js/scripts/tables/table-datatables-basic.min.js')}}"></script>--}}
<script src="{{asset('portals/app-assets/js/scripts/extensions/ext-component-toastr.min.js')}}"></script>
<!-- END: Page JS-->
@yield('js')

<script>
    var isRtl = '{{LaravelLocalization::getCurrentLocaleDirection()}}' === 'rtl';

    var selectedIds = function () {
        return $("input[name='table_ids[]']:checked").map(function () {
            return this.value;
        }).get();
    };
    $('select').select2({
        dir: '{{LaravelLocalization::getCurrentLocaleDirection()}}',
        placeholder: "@lang('select')",
    });
    $(document).ready(function () {
        $(document).on('click', "#export_btn", function (e) {
            e.preventDefault();
            window.open(url + 'export?' + $('#search_form').serialize(), '_blank');
        });

        $(document).on('click', "#chart_btn", function (e) {
            e.preventDefault();
            window.open(url + 'chart?' + $('#search_form').serialize(), '_blank');
        });

        $("#advance_search_btn").click(function (e) {
            e.preventDefault();
            $('#advance_search_div').toggle(500);
        });

        $(document).on('change', "#select_all", function (e) {
            var delete_btn = $('#delete_btn'), export_btn = $('#export_btn'),
                chart_btn = $('#chart_btn'), all_status_btn = $('.all_status_btn'), table_ids = $('.table_ids');
            this.checked ? table_ids.each(function () {
                this.checked = true
            }) : table_ids.each(function () {
                this.checked = false
            })
            delete_btn.attr('data-id', selectedIds().join());
            export_btn.attr('data-id', selectedIds().join());
            chart_btn.attr('data-id', selectedIds().join());
            all_status_btn.attr('data-id', selectedIds().join());
            if (selectedIds().join().length) {
                delete_btn.prop('disabled', '');
                all_status_btn.prop('disabled', '');
            } else {
                delete_btn.prop('disabled', 'disabled');
                all_status_btn.prop('disabled', 'disabled');
            }
        });

        $(document).on('change', ".table_ids", function (e) {
            var delete_btn = $('#delete_btn'), select_all = $('#select_all'), all_status_btn = $('.all_status_btn');
            if ($(".table_ids:checked").length === $(".table_ids").length) {
                select_all.prop("checked", true)
            } else {
                select_all.prop("checked", false)
            }
            delete_btn.attr('data-id', selectedIds().join());
            all_status_btn.attr('data-id', selectedIds().join());
            console.log(selectedIds().join().length)
            if (selectedIds().join().length) {
                delete_btn.prop('disabled', '');
                all_status_btn.prop('disabled', '');
            } else {
                delete_btn.prop('disabled', 'disabled');
                all_status_btn.prop('disabled', 'disabled');
            }
        });

        $('#search_btn').on('click', function (e) {
            oTable.draw();
            e.preventDefault();
        });

        $('#clear_btn').on('click', function (e) {
            e.preventDefault();
            $('.search_input').val("").trigger("change")
            oTable.draw();
        });

        $(document).on("click", ".delete-btn", function (e) {
            e.preventDefault();
            var urls = url;
            if (selectedIds().join().length) {
                urls += selectedIds().join();
            } else {
                urls += $(this).data('id');
            }
            Swal.fire({
                title: '@lang('delete_confirmation')',
                text: '@lang('confirm_delete')',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang('yes')',
                cancelButtonText: '@lang('cancel')',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger'
                },
                buttonsStyling: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: urls,
                        method: 'DELETE',
                        type: 'DELETE',
                        data: {
                            _token: '{{csrf_token()}}'
                        },
                    }).done(function (data) {
                        if (data.status) {
                            toastr.success('@lang('deleted')', '', {
                                rtl: isRtl
                            });
                            oTable.draw();
                            $('#select_all').prop('checked', false).trigger('change')
                        } else {
                            toastr.warning('@lang('not_deleted')', '', {
                                rtl: isRtl
                            });
                        }

                    }).fail(function () {
                        toastr.error('@lang('something_wrong')', '', {
                            rtl: isRtl
                        });
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    toastr.info('@lang('delete_canceled')', '', {
                        rtl: isRtl
                    })
                }
            });
        });
        $(document).on("click", ".status_btn", function (e) {
            e.preventDefault();
            var ids = $(this).data('id');
            var status = $(this).val();
            var urls = url + 'update_status';
            Swal.fire({
                title: '@lang('update_confirmation')',
                text: '@lang('confirm_update')',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '@lang('yes')',
                cancelButtonText: '@lang('cancel')',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger'
                },
                buttonsStyling: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: urls,
                        method: 'PUT',
                        type: 'PUT',
                        data: {
                            ids: ids,
                            status: status,
                            _token: '{{csrf_token()}}'
                        },
                        success: function (data) {
                            if (data.status) {
                                toastr.success('@lang('done_successfully')');
                                oTable.draw();
                            } else {
                                toastr.error('@lang('something_wrong')');
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    toastr.info('@lang('update_canceled')', '', {
                        rtl: isRtl
                    })
                }
            });
        });

        $('#create_modal,#edit_modal').on('hide.bs.modal', function (event) {
            var form = $(this).find('form');
            form.find('select').val('').trigger("change")
            form[0].reset();
            $('.submit_btn').removeAttr('disabled');
            $('.fa-spinner.fa-spin').hide();
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").html("");
        })

        $(document).on('submit', '.ajax_form', function (e) {
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
                url: url,
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
                        oTable.draw();
                    } else {
                        var url = $('#cancel_btn').attr('href');
                        window.location.replace(url);
                    }
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

        {{--$(document).on('click', '.status_btn', function (e) {--}}
        {{--    e.preventDefault();--}}
        {{--    var urls = url + 'update_status', status = $(this).val();--}}
        {{--    $.ajax({--}}
        {{--        url: urls,--}}
        {{--        method: 'PUT',--}}
        {{--        type: 'PUT',--}}
        {{--        data: {--}}
        {{--            ids: $(this).data('id'),--}}
        {{--            status: status,--}}
        {{--            _token: '{{csrf_token()}}'--}}
        {{--        },--}}
        {{--        success: function (data) {--}}
        {{--            if (data.status) {--}}
        {{--                toastr.success('@lang('done_successfully')');--}}
        {{--                oTable.draw();--}}
        {{--            } else {--}}
        {{--                toastr.error('@lang('something_wrong')');--}}
        {{--            }--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

        $('#datatable').on('draw', function () {
            $("#select_all").prop("checked", false)
            $('#delete_btn').prop('disabled', 'disabled');
            $('.status_btn').prop('disabled', 'disabled');
        });

    });


</script>
@yield('scripts')
<!-- END: Page JS-->

<script>
    $(window).on('load', function () {
        if (feather) {
            feather.replace({width: 14, height: 14});
        }
    })
</script>
</body>
<!-- END: Body-->

</html>
