<!DOCTYPE html>
<html class="loading dark-layout" lang="en" data-layout="dark-layout" data-textdirection="rtl">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>magicSolution</title>
    <link rel="apple-touch-icon" href="{{ asset('landing/assets/images/landpage/svg/logo3.svg') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('landing/assets/images/landpage/svg/icon.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
        rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/vendors' . rtl_assets() . '.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/extensions/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/fonts/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/extensions/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/colors.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/components.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/themes/dark-layout.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/themes/bordered-layout.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/themes/semi-dark-layout.min.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/core/menu/menu-types/vertical-menu.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('portals/app-assets/css' . rtl_assets() . '/plugins/extensions/ext-component-toastr.min.css') }}">
    <!-- END: Page CSS-->
    @yield('styles')

    <!-- BEGIN: Custom CSS-->
    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('portals/app-assets/css' . rtl_assets() . '/custom' . rtl_assets() . '.min.css') }}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{ asset('portals/assets/css/style' . rtl_assets() . '.css') }}">
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
                {{--            <li class="nav-item dropdown dropdown-language"> --}}
                {{--                <a class="nav-link dropdown-toggle" id="dropdown-flag" href="#" data-bs-toggle="dropdown" --}}
                {{--                   aria-haspopup="true" aria-expanded="false"><i --}}
                {{--                        class="flag-icon flag-icon-{{LaravelLocalization::getCurrentLocaleNative() == 'English' ? 'us' : 'ps'}}"></i><span --}}
                {{--                        class="selected-language">{{ LaravelLocalization::getCurrentLocaleNative() }}</span></a> --}}
                {{--                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-flag"> --}}
                {{--                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) --}}
                {{--                        <a class="dropdown-item" --}}
                {{--                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" --}}
                {{--                           data-language="{{ $localeCode }}"><i --}}
                {{--                                class="flag-icon flag-icon-{{$localeCode == 'en' ? 'us' : 'ps'}}"></i>{{ $properties['native'] }} --}}
                {{--                        </a> --}}
                {{--                    @endforeach --}}
                {{--                </div> --}}
                {{--            </li> --}}
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link"
                        id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span
                                class="user-name fw-bolder">{{ auth()->user()->name }}</span><span
                                class="user-status">Admin</span></div>
                        <span class="avatar"><img class="round"
                                src="{{ asset('portals/app-assets/images/portrait/small/avatar-s-11.jpg') }}"
                                alt="avatar" height="40" width="40"><span
                                class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="{{ url('/admin/profile') }}"><i class="mr-50"
                                data-feather="user"></i>
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
                    <div class="me-75"><img src="{{ asset('portals/app-assets/images/icons/xls.png') }}"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Two new item submitted</p><small
                            class="text-muted">Marketing
                            Manager</small>
                    </div>
                </div>
                <small class="search-data-size me-50 text-muted">&apos;17kb</small>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100"
                href="app-file-manager.html">
                <div class="d-flex">
                    <div class="me-75"><img src="{{ asset('portals/app-assets/images/icons/jpg.png') }}"
                            alt="png" height="32"></div>
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
                    <div class="me-75"><img src="{{ asset('portals/app-assets/images/icons/pdf.png') }}"
                            alt="png" height="32"></div>
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
                    <div class="me-75"><img src="{{ asset('portals/app-assets/images/icons/doc.png') }}"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web
                            Designer</small>
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
                            src="{{ asset('portals/app-assets/images/portrait/small/avatar-s-8.jpg') }}"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100"
                href="app-user-view-account.html">
                <div class="d-flex align-items-center">
                    <div class="avatar me-75"><img
                            src="{{ asset('portals/app-assets/images/portrait/small/avatar-s-1.jpg') }}"
                            alt="png" height="32"></div>
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
                            src="{{ asset('portals/app-assets/images/portrait/small/avatar-s-14.jpg') }}"
                            alt="png" height="32"></div>
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
                            src="{{ asset('portals/app-assets/images/portrait/small/avatar-s-6.jpg') }}"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web
                            Designer</small>
                    </div>
                </div>
            </a></li>
    </ul>
    <ul class="main-search-list-defaultlist-other-list d-none">
        <li class="auto-suggestion justify-content-between"><a
                class="d-flex align-items-center justify-content-between w-100 py-50">
                <div class="d-flex justify-content-start"><span class="me-75"
                        data-feather="alert-circle"></span><span>No results found.</span>
                </div>
            </a></li>
    </ul>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto"><a class="navbar-brand" href="{{ url('admin') }}"><span
                            class="brand-logo" style="width: 36px;">
                            <img src="{{ asset('landing/assets/images/landpage/svg/logo3.svg') }}" alt=""></span>
                        <h2 class="brand-text">magicSolution</h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0"
                        data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4"
                            data-feather="x"></i><i
                            class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary"
                            data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin') }}"><i
                            data-feather="home"></i><span class="menu-title text-truncate"
                            data-i18n="Dashboard">Dashboard</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin/modules') }}"><i
                            data-feather="grid"></i><span class="menu-title text-truncate"
                            data-i18n="Modules">@lang('modules')</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin/brands') }}"><i
                            data-feather="grid"></i><span class="menu-title text-truncate"
                            data-i18n="Brands">@lang('brands')</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin/ecus') }}"><i
                            data-feather="grid"></i><span class="menu-title text-truncate"
                            data-i18n="ECUs">@lang('ecus')</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin/ecu_requests') }}"><i
                            data-feather="grid"></i><span class="menu-title text-truncate"
                            data-i18n="ECURequests">@lang('ecu_requests')</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin/admins') }}"><i
                            data-feather="grid"></i><span class="menu-title text-truncate"
                            data-i18n="Admin">@lang('admins')</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="{{ url('admin/users') }}"><i
                            data-feather="grid"></i><span class="menu-title text-truncate"
                            data-i18n="User">@lang('users')</span></a>
                </li>
                {{--            <li class=" navigation-header"><span data-i18n="User Interface">User Interface</span><i data-feather="more-horizontal"></i> --}}
                {{--            </li> --}}
                {{--            <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="layout"></i><span class="menu-title text-truncate" data-i18n="Page Layouts">Page Layouts</span></a> --}}
                {{--                <ul class="menu-content"> --}}
                {{--                    <li><a class="d-flex align-items-center" href="layout-collapsed-menu.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Collapsed Menu">Collapsed Menu</span></a> --}}
                {{--                    </li> --}}
                {{--                    <li><a class="d-flex align-items-center" href="layout-full.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Layout Full">Layout Full</span></a> --}}
                {{--                    </li> --}}
                {{--                    <li><a class="d-flex align-items-center" href="layout-without-menu.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Without Menu">Without Menu</span></a> --}}
                {{--                    </li> --}}
                {{--                    <li class="active"><a class="d-flex align-items-center" href="layout-empty.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Layout Empty">Layout Empty</span></a> --}}
                {{--                    </li> --}}
                {{--                    <li><a class="d-flex align-items-center" href="layout-blank.html"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Layout Blank">Layout Blank</span></a> --}}
                {{--                    </li> --}}
                {{--                </ul> --}}
                {{--            </li> --}}
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
                    class="ms-25" href="https://1.envato.market/pixinvent_portfolio"
                    target="_blank">Pixinvent</a><span class="d-none d-sm-inline-block">, All rights
                    Reserved</span></span><span class="float-md-end d-none d-md-block">Hand-crafted & Made with<i
                    data-feather="heart"></i></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->


    <script></script>

    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('portals/app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('portals/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/tables/datatable/datatables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('portals/app-assets/js/core/app-menu.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/js/core/app.min.js') }}"></script>
    <script src="{{ asset('portals/app-assets/js/scripts/customizer.min.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    {{-- <script src="{{asset('portals/app-assets/js/scripts/tables/table-datatables-basic.min.js')}}"></script> --}}
    <script src="{{ asset('portals/app-assets/js/scripts/extensions/ext-component-toastr.min.js') }}"></script>
    <!-- END: Page JS-->
    @yield('js')

    <script>
        var isRtl = '{{ LaravelLocalization::getCurrentLocaleDirection() }}' === 'rtl';

        var selectedIds = function() {
            return $("input[name='table_ids[]']:checked").map(function() {
                return this.value;
            }).get();
        };
        $('select').select2({
            dir: '{{ LaravelLocalization::getCurrentLocaleDirection() }}',
            placeholder: "@lang('select')",
        });
        $(document).ready(function() {
            $(document).on('click', "#export_btn", function(e) {
                e.preventDefault();
                window.open(url + 'export?' + $('#search_form').serialize(), '_blank');
            });

            $(document).on('click', "#chart_btn", function(e) {
                e.preventDefault();
                window.open(url + 'chart?' + $('#search_form').serialize(), '_blank');
            });

            $("#advance_search_btn").click(function(e) {
                e.preventDefault();
                $('#advance_search_div').toggle(500);
            });

            $(document).on('change', "#select_all", function(e) {
                var delete_btn = $('#delete_btn'),
                    export_btn = $('#export_btn'),
                    chart_btn = $('#chart_btn'),
                    all_status_btn = $('.all_status_btn'),
                    table_ids = $('.table_ids');
                this.checked ? table_ids.each(function() {
                    this.checked = true
                }) : table_ids.each(function() {
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

            $(document).on('change', ".table_ids", function(e) {
                var delete_btn = $('#delete_btn'),
                    select_all = $('#select_all'),
                    all_status_btn = $('.all_status_btn');
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

            $('#search_btn').on('click', function(e) {
                oTable.draw();
                e.preventDefault();
            });

            $('#clear_btn').on('click', function(e) {
                e.preventDefault();
                $('.search_input').val("").trigger("change")
                oTable.draw();
            });

            $(document).on("click", ".delete-btn", function(e) {
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
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: urls,
                            method: 'DELETE',
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                        }).done(function(data) {
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

                        }).fail(function() {
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
            $(document).on("click", ".status_btn", function(e) {
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
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: urls,
                            method: 'PUT',
                            type: 'PUT',
                            data: {
                                ids: ids,
                                status: status,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(data) {
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

            $('#create_modal,#edit_modal').on('hide.bs.modal', function(event) {
                var form = $(this).find('form');
                form.find('select').val('').trigger("change")
                form[0].reset();
                $('.submit_btn').removeAttr('disabled');
                $('.fa-spinner.fa-spin').hide();
                $(".is-invalid").removeClass("is-invalid");
                $(".invalid-feedback").html("");
            })

            $(document).on('submit', '.ajax_form', function(e) {
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
                    beforeSend: function() {
                        $('.invalid-feedback').html('');
                        $('.is-invalid ').removeClass('is-invalid');
                        form.removeClass('was-validated');
                    },
                }).done(function(data) {
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
                }).fail(function(data) {
                    if (data.status === 422) {
                        var response = data.responseJSON;
                        $.each(response.errors, function(key, value) {
                            var str = (key.split("."));
                            if (str[1] === '0') {
                                key = str[0] + '[]';
                            }
                            if (str.length > 1) {
                                key = "";
                                str.forEach((element, index) => {
                                    if (index == 0) {
                                        key = element;
                                    } else {
                                        key += "[" + element + "]";
                                    }
                                });
                            }
                            $('[name="' + key + '"], [name="' + key + '[]"]').addClass(
                                'is-invalid');
                            $('[name="' + key + '"], [name="' + key + '[]"]').closest(
                                '.form-group').find('.invalid-feedback').html(value[0]);
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

            // $(document).on('click', '.status_btn', function(e) {
            //     e.preventDefault();
            //     var urls = url + 'update_status',
            //         status = $(this).val();
            //     $.ajax({
            //         url: urls,
            //         method: 'PUT',
            //         type: 'PUT',
            //         data: {
            //             ids: $(this).data('id'),
            //             status: status,
            //             _token: '{{ csrf_token() }}'
            //         },
            //         success: function(data) {
            //             if (data.status) {
            //                 toastr.success('@lang('done_successfully')');
            //                 oTable.draw();
            //             } else {
            //                 toastr.error('@lang('something_wrong')');
            //             }
            //         }
            //     });
            // });

            $('#datatable').on('draw', function() {
                $("#select_all").prop("checked", false)
                $('#delete_btn').prop('disabled', 'disabled');
                $('.status_btn').prop('disabled', 'disabled');
            });

        });
    </script>
    @yield('scripts')
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
