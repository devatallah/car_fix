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
    <div class="main-container" style="height: 100%; width: 100%;">

        <div class="card">
            <form id="logoutForm" action="{{ route('user_logout') }}" method="POST" style="display: none">
                {{ csrf_field() }}
            </form>
            <form onsubmit="event.preventDefault(); findSolution(this)">
                <section>
                    <h3 class="group-title">Solution Type</h3>
                    <br>
                    <div class="solutions-radio-group">
                        @foreach ($modules as $module)
                            <label class="label-container text-gray">{{ $module->name }}
                                <input type="radio" name="solution" value="{{ $module->uuid }}"
                                    data-module-name="{{ $module->name }}" onchange="getBrands(this)">
                                <span class="checkmark"></span>
                            </label>
                        @endforeach
                    </div>
                    <div class="lists-group"></div>
                </section>
                <section>
                    <div class="card-header">
                        <div class="row">
                            <div class="col-12 mb-2 d-flex justify-content-end align-items-center">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#create_modal"><span><i class="fa fa-plus"></i> @lang('Request New Solutions')</span>
                                </button>
                                <a class="btn btn-outline-primary mx-3" href="" type="button">
                                    <span><i class="fa fa-recycle"></i> @lang('Refresh')</span>
                                </a>
                                <button class="btn btn-primary" type="button"
                                    onclick="document.querySelector('form#logoutForm').submit();">
                                    <i class="mr-50" data-feather="power"></i>@lang('logout')
                                </button>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <img class="logo" src="{{ asset('portals/app-assets/images/logo.svg') }}" alt="logo">
                                <div class="user">
                                    <h3 class="text-gray">User:</h3>
                                    <p class="text-gray">{{ auth()->user()->name }}</p>
                                </div>
                                <div class="linces">
                                    <h3 class="text-gray">License EXP.</h3>
                                    <p class="text-gray">{{ auth()->user()->license_expire_date }}</p>
                                </div>
                                <div class="user">
                                    <h3 class="text-gray">Balance:</h3>
                                    <p class="text-gray">{{ auth()->user()->balance }}</p>
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-secondary" style="background-color :red;    box-shadow: none;
    font-weight: 500;" href="">DTC Coming Soon</a>
                        <br>
                        <div class="btn-groups d-flex">
                            <input type="file" id="originalFile" accept=".bin" onchange="uploadFile(this)" hidden>
                            <button type="button" onclick="fileExplorer()">Open</button>
                            <button type="submit">Solution</button>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-container">
                                <div class="progress-bg">45%</div>
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
        {{-- <div class="modal fade" id="requestSolution" tabindex="-1" role="dialog"
            aria-labelledby="requestSolutionModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Whould you want to request a new solution?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>We can't find a solution for your file.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Request</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
@section('js')
@endsection
@section('scripts')
    <script>
        $('#requestSolution').modal('show')
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
            document.getElementById("selectedModule").innerText = $(ev).attr('data-module-name');
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
                            <h3 class="tilte" onclick="showCategories(this.parentNode, this)" data-brand-id="${element['uuid']}" data-brand-name="${element['name']}">+${element['name']}</h3>
                            <ul>`;
                        let brandChidlren = element['ecus'];
                        for (let index = 0; index < brandChidlren.length; index++) {
                            const ecu = brandChidlren[index];
                            text += `<li class="menu-item">
                                    <label class="label-container sm-label text-gray">${ecu['name']}
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
    </script>
@endsection
