@extends('portals.user.app')

@section('title')
    @lang('Solutions')
@endsection

@section('my_style')
    <link rel="stylesheet" href="{{ asset('portals/assets/css/user-solutions.css') }}">
    <style>
        html .content.app-content {
            padding: 0;
        }
    </style>
@endsection

@section('content')
    <div class="main-container">

        <div class="card">
            <form id="logoutForm" action="{{ route('user_logout') }}" method="POST" style="display: none">
                {{ csrf_field() }}
            </form>
            <form onsubmit="event.preventDefault(); findSolution(this)">
                <section>
                    <div class="row">
                        <div class="col-md-12 mb-2 d-flex justify-content-start align-items-center">
                            <img class="logo" src="{{ asset('portals/app-assets/images/logo.svg') }}" alt="logo"
                                width="100" height="100">
                        </div>
                        <div
                            class="col-md-12 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 modules-list">
                            <select class="form-control" name="solution" id="solution" onchange="getBrands(this)">
                                @foreach ($modules as $module)
                                    <option value="{{ $module->uuid }}" data-module-name="{{ $module->name }}">
                                        {{ $module->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="group-title btn btn-danger" disabled>DTC Comming Soon</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="lists-group"></div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-sm-12 col-md-9 d-flex flex-column align-items-center gap-1 mb-2">
                            <div class="user d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <h3 class="text-gray">User:</h3>
                                <p class="text-gray text-left">{{ auth()->user()->name }}</p>
                            </div>
                            <div class="linces d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <h3 class="text-gray">License EXP.</h3>
                                <p class="text-gray text-left">{{ auth()->user()->license_expire_date }}</p>
                            </div>
                            <div class="email d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <h3 class="text-gray">Email:</h3>
                                <p class="text-gray text-left">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="balance d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <h3 class="text-gray">Balance:</h3>
                                <p class="text-gray text-left">{{ auth()->user()->balance }}</p>
                            </div>
                        </div>
                        <div
                            class="col-sm-12 col-md-3 mb-2 d-flex flex-sm-row flex-md-column justify-content-center align-items-end gap-1">
                            <button class="btn btn-primary" type="button" title="Logout"
                                onclick="document.querySelector('form#logoutForm').submit();">
                                <i class="mr-50" data-feather="power"></i>
                            </button>
                            <button class="btn btn-outline-primary" type="button" title="Add New Request"
                                data-bs-toggle="modal" data-bs-target="#create_modal"><span><i
                                        class="fa fa-plus"></i></span>
                            </button>
                            <a class="btn btn-outline-primary" href="" title="Refresh Page">
                                <i class="fa fa-recycle"></i>
                            </a>
                        </div>
                    </div>
                    <div class="btn-groups d-flex justify-content-between gap-3">
                        <input type="file" id="originalFile" accept=".bin" onchange="uploadFile(this)" hidden>
                        <button type="button" onclick="fileExplorer()">Open</button>
                        <button type="submit">Solution</button>
                    </div>
                    <br>
                    <div class="progress-bar">
                        <div class="progress-container">
                            <div class="progress-bg"></div>
                        </div>
                    </div>
                    <div class="file-details">
                        <p><b>Selected Module:</b> <span id="selectedModule"></span></p>
                        <p><b>Selected Brand:</b> <span id="selectedBrand"></span></p>
                        <p><b>Selected ECU:</b> <span id="selectedECU"></span></p>
                        <p><b>Selected File:</b> <span id="selectedFile"></span></p>
                        <p><b>File Size:</b> <span id="selectedSize"></span></p>
                    </div>
                </section>
            </form>
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
                        <form action="{{ url('/user/ecu_requests') }}" id="create_request_form" method="POST"
                            data-reset="true" class="ajax_form form-horizontal" enctype="multipart/form-data" novalidate>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 mb-1">
                                    <div class="form-group">
                                        <label for="module">fix type</label>
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
                                <div class="col-12 mb-1">
                                    <div class="form-group">
                                        <label for="file">File</label>
                                        <input type="file" class="form-control" id="file" name="file"
                                        accept=".bin" required>
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
        var fix_type_uuid, brand_uuid, ecu_uuid = "";

        function showCategories(parentItem, ev) {
            brand_uuid = $(ev).attr('data-brand-id');
            document.getElementById("selectedECU").innerText = '';
            document.getElementById("selectedBrand").innerText = $(ev).attr('data-brand-name');
            parentItem.classList.toggle("show");
        }

        function getECU(ev) {
            ecu_uuid = ev.value;
            document.getElementById("selectedECU").innerText = $(ev).attr('data-ecu-name');
        }

        function getBrands(ev) {
            $('.lists-group').empty();
            let url = "/user/solutions/brands/list";
            let id = ev.value;
            fix_type_uuid = id;
            document.getElementById("selectedBrand").innerText = '';
            document.getElementById("selectedECU").innerText = '';
            document.getElementById("selectedModule").innerText = $('option:selected', ev).attr('data-module-name');
            const data = {
                'module_uuid': id
            };
            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                dataType: 'json',
            }).done(function(data) {
                if (data.status) {
                    toastr.success(data.message, '');
                    let text = '';
                    let brand = data.data;
                    brand.forEach((element) => {
                        text += `<div class="dropdown-container">
                            <h3 class="tilte" onclick="showCategories(this.parentNode, this)" data-brand-id="${element['uuid']}" data-brand-name="${element['name']}">
                                <i class="fa fa-angle-right"></i> ${element['name']}
                            </h3>
                            <ul>`;
                        let brandChidlren = element['ecus'];
                        for (let index = 0; index < brandChidlren.length; index++) {
                            const ecu = brandChidlren[index];
                            text += `<li class="menu-item">
                                    <label class="label-container sm-label text-dark">${ecu['name']}
                                        <input type="radio" name="category" data-ecu-name="${ecu['name']}" value="${ecu['uuid']}" onchange="getECU(this)">
                                        <span class="checkmark"></span>
                                    </label>
                                </li>`
                        }
                        text += `</ul>
                        </div>`
                    });
                    $('.lists-group').append(text);
                } else {
                    if (data.message) {
                        toastr.error(data.message, '');
                    } else {
                        toastr.error('@lang('something_wrong')', '');
                    }
                }
            }).fail(function(data) {
                toastr.error(data.responseJSON.message, '');
            });
        }

        function fileExplorer() {
            $('#originalFile').click();
        }

        function uploadFile(ev) {
            const file = event.target.files[0];
            document.getElementById("selectedFile").innerText = file.name;
            document.getElementById("selectedSize").innerText = formatBytes(file.size);
        }

        function formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 Bytes'

            const k = 1024
            const dm = decimals < 0 ? 0 : decimals
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

            const i = Math.floor(Math.log(bytes) / Math.log(k))

            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
        }

        function findSolution(ev) {

            let url = "/user/solutions/find/solution";
            var progress = $(".progress-bg");

            if (fix_type_uuid == "" || brand_uuid == "" || ecu_uuid == "") {
                toastr.error('Fix Type, Brand, ECU data should not be empty');
            } else if (document.getElementById("originalFile").files.length == 0) {
                toastr.error('No original file selected');
            } else {
                $.ajaxSetup({
                    url: url,
                    global: false,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });
                var formData = new FormData()
                formData.append('module_uuid', fix_type_uuid)
                formData.append('brand_uuid', brand_uuid)
                formData.append('ecu_uuid', ecu_uuid)
                formData.append('file', document.getElementById("originalFile").files[0])
                $.ajax({
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        var percentVal = '0%';
                        progress.width(percentVal)
                        progress.html(percentVal);
                    },
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(event) {
                            if (event.lengthComputable) {
                                var percentComplete = Math.round((event.loaded * 100) / event.total);
                                var percentVal = percentComplete + '%';
                                progress.width(percentVal);
                                progress.html(percentVal);
                            }
                        }, false);
                        return xhr;
                    },
                    complete: function(xhr) {
                        toastr.success('Request Done!');
                    }
                }).done(function(response) {
                    if (response.status) {
                        toastr.success(response.message, '');
                        const link = document.createElement('a');
                        link.setAttribute('href', response.data.url);
                        link.setAttribute('target', '_blank');
                        link.click();
                        location.reload();
                    } else {
                        if (response.message) {
                            toastr.error(response.message, '');
                        } else {
                            toastr.error('@lang('something_wrong')', '');
                        }

                        // $('#myModal').modal('show')
                    }
                    var percentVal = '100%';
                    progress.width(percentVal)
                    progress.html(percentVal);
                }).fail(function(data) {
                    toastr.error(data.responseJSON.message, '');
                });
            }
        }

        $(document).ready(function() {
            if ($('#solution option:first')) {
                let url = "/user/solutions/brands/list";
                let id = $("#solution").prop("selectedIndex", 0).val();
                fix_type_uuid = id;
                document.getElementById("selectedBrand").innerText = '';
                document.getElementById("selectedECU").innerText = '';
                document.getElementById("selectedModule").innerText = $("#solution option:first").attr(
                    'data-module-name');
                const data = {
                    'module_uuid': id
                };
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                }).done(function(data) {
                    if (data.status) {
                        toastr.success(data.message, '');
                        let text = '';
                        let brand = data.data;
                        brand.forEach((element) => {
                            text += `<div class="dropdown-container">
                            <h3 class="tilte" onclick="showCategories(this.parentNode, this)" data-brand-id="${element['uuid']}" data-brand-name="${element['name']}">
                                <i class="fa fa-angle-right"></i> ${element['name']}
                            </h3>
                            <ul>`;
                            let brandChidlren = element['ecus'];
                            for (let index = 0; index < brandChidlren.length; index++) {
                                const ecu = brandChidlren[index];
                                text += `<li class="menu-item">
                                    <label class="label-container sm-label text-dark">${ecu['name']}
                                        <input type="radio" name="category" data-ecu-name="${ecu['name']}" value="${ecu['uuid']}" onchange="getECU(this)">
                                        <span class="checkmark"></span>
                                    </label>
                                </li>`
                            }
                            text += `</ul>
                        </div>`
                        });
                        $('.lists-group').append(text);
                    } else {
                        if (data.message) {
                            toastr.error(data.message, '');
                        } else {
                            toastr.error('@lang('something_wrong')', '');
                        }
                    }
                }).fail(function(data) {
                    toastr.error(data.responseJSON.message, '');
                });
            }
        });
    </script>
@endsection

@push('master_script')
    <script>
        $('#solution').select2({
            placeholder: "Select Solutions",
        });
    </script>
@endpush