<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app_local" content="{{ app()->getLocale() }}">
    <title>{{ AppName() }} @yield('title')</title>

    <link rel="stylesheet" type="text/css" href="//www.fontstatic.com/f=DroidKufi-Regular"/>
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('assets/purple/css/app_rtl.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('assets/purple/css/app.css') }}" rel="stylesheet">
    @endif
    <link href="{{ asset('assets/purple/css/customStyle.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    {{ Html::style('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css') }}
    {{ Html::style('https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css') }}

    <link rel="shortcut icon" href="/favicon.png"/>
    <style>
        .footer {
            position: fixed;
            padding: 10px 10px 0px 10px;
            bottom: 0;
            width: 100%;
            /* Height of the footer*/
            height: 40px;
        }
    </style>
    @yield('head')
</head>
<body class="{{ app()->getLocale()  == 'ar' ? 'rtl' : ''}}">
<div class="container-scroller">
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
            <a class="navbar-brand brand-logo" href="javascript:void(0)"><img src="{!! getSchoolLogo() !!}"
                                                                              alt="logo"/></a>
            <a class="navbar-brand brand-logo-mini" href="javascript:void(0)"><img src="/img/blue.png" alt="logo"/></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="language" href="#"
                       data-toggle="dropdown">
                        <i class="mdi mdi-border-color"></i>
                        <span>{{auth()->user()->schoolAdmin->currentSchool->name ?? ''}}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                         aria-labelledby="language">
                        <nav>
                            <ul class="list-unstyled" style="overflow-y: scroll;  max-height:500px;">
                                @forelse(auth()->user()->schoolAdminAssignedSchools as $school)
                                    <li class="nav-item">
                                    <a class="btn" href="{{route('school-admin.profile.updateCurrentSchool',$school->id)}}" class="nav-link">{{$school->name}}</a>
                                </li>
                           @empty
                           @endforelse
                            </ul>
                        </nav>
                    </div>
                </li>
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link" href="#">
                        <div class="nav-profile-img">
                        </div>
                        <div class="nav-profile-text">
                            @if(auth()->user() &&
                                auth()->user()->type == \App\OurEdu\Users\UserEnums::SCHOOL_ADMIN &&
                                is_null(auth()->user()->schoolAdmin->currentSchool))
                                    <p class="mb-1 text-black">{{auth()->user()->name}}</p>
                            @else
                                <p class="mb-1" style="color: #b66dff;">{{auth()->user()->name}} - {{auth()->user()->schoolAdmin->currentSchool->name}}
                                </p>
                            @endif
                        </div>
                    </a>
                </li>
                @if(auth()->user()->type == \App\OurEdu\Users\UserEnums::SCHOOL_SUPERVISOR)
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                           onclick="getNotification()"
                           data-toggle="dropdown">
                            <i class="mdi mdi-bell-outline"></i>
                            <span class="count-symbol bg-danger"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                             aria-labelledby="notificationDropdown">
                            <h6 class="p-3 mb-0 text-center">{{ trans('notification.Notification') }}</h6>
                            <div id="notifications_list">
                            </div>

                            {{--                        <h6 class="p-3 mb-0 text-center">See all notifications</h6>--}}
                        </div>
                    </li>
                @endif
                <li class="nav-item d-none d-lg-block full-screen-link">
                    <a class="nav-link">
                        <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                    </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                    <a class="nav-link" href="{{ route('profile.get.logout') }}">
                        <i class="mdi mdi-power"></i>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link count-indicator dropdown-toggle" id="language" href="#"
                       data-toggle="dropdown">
                        <i class="mdi mdi-border-color"></i>
                        <span>{{ucfirst(lang())}}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                         aria-labelledby="language">
                        <nav class="nav">
                @foreach(languages() as $key=>$lang)
                    <li>
                        <a href="{{urlLang(url()->full(),lang(),$key)}}" class="nav-link">{{$lang}}</a>
                    </li>
        @endforeach
    </nav>
</div>
</li>
</ul>
<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
        data-toggle="offcanvas">
    <span class="mdi mdi-menu"></span>
</button>
</div>
</nav>
<div class="container-fluid page-body-wrapper">
    @include('school_admin.partials.navigation')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-assistant"></i>
              </span>
                    @yield('title')
                </h3>
                <nav aria-label="breadcrumb">
                    <div class="breadcrumb">
                        @yield('buttons')
                    </div>
                </nav>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i>{{ trans('app.Something went wrong') }}</h4>
                    @foreach ($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </div>
            @endif
            @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>{{ Session::get('success') }}</p>
                </div>
            @endif
            @if(Session::has('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>{{ Session::get('warning') }}</p>
                </div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>{{ Session::get('error') }}</p>
                </div>
            @endif
            @yield('content')
        </div>
        <!-- content-wrapper ends -->
    </div>
    <input type="hidden" id="socketToken"
           value="{{(app(\App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface::class))->createAuthToken(\App\OurEdu\Users\Auth\Enum\TokenNameEnum::DYNAMIC_lINKS_Token)}}"/>
    <input type="hidden" id="auth_user_id" value="{{auth()->id()}}"/>
    <input type="hidden" id="branch_id" value="{{auth()->user()->schoolAccountBranchType->id ?? null}}"/>
    <input type="hidden" id="sdk_host" value="{{env('VIDEO_PORTAL_URL')}}"/>
    <input type="hidden" id="call_request_id"/>

    <!-- main-panel ends -->
</div>
<div class="footer">

        <div class="col-lg-12 text-center mx-auto"><a style="color: #00f;" href="https://admin.ta3lom.com/app/Ta3lom-1.7.4.zip" role="button" class="market-btn windows-btn">

                <span class="market-button-title" style="color: #00f;"><span class="market-button-subtitle">{{trans('app.download_ta3lom_windows_app')}} Windows </span><i class="fab fa-windows"></i> </span></span></a></div>

    </div>

<!-- page-body-wrapper ends -->
</div>
<div class="video_call_modal">
    <div class="content">
        <div class="modal" tabindex="-1" role="dialog" id="video_call_request" data-backdrop="static"
             data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ trans('video.Video call request') }}</h5>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                                onclick=handleVideoCallStatus("accepted")>{{ trans('video.accept') }}
                        </button>
                        <button type="button" class="btn btn-danger"
                                onclick=handleVideoCallStatus("rejected")>{{ trans('video.reject') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- container-scroller -->
<script>
    window.laravel_echo_port = '{{env("LARAVEL_ECHO_PORT")}}';
    window.csrf = '{{ csrf_token() }}';
    window.laravel_echo_host = '{{ env('ECHO_HOST','https://echo.ta3lom.com') }}';
</script>
<script src="//{{ env('ECHO_HOST','echo.ta3lom.com') }}{{ env('LARAVEL_ECHO_PORT') ?? ''  }}/socket.io/socket.io.js"></script>

{{--<script src="{{ asset('js/laravel-echo-setup.js') }}"></script>--}}
<script src="{{ asset('assets/purple/script.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>--}}
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.14.4//firebase.js"></script>
<script src="{{ url('/js/laravel-echo-setup.js') }}" type="text/javascript"></script>
<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.14.4/firebase-analytics.js"></script>
<script src="//cdn.jsdelivr.net/npm/fingerprintjs2@2.1.0/dist/fingerprint2.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script>

    function getNotification() {
        $.ajax({
            url: '{{ route('notifications.get.index') }}',
            type: 'GET',
            data: {},
            dataType: 'JSON',
            success: function (data) {
                if (data) {
                    let ul = '';
                    $('#notifications_list').html('');
                    $.each(data.notifications.notificationsData, function (i, val) {
                        ul += '<div class="dropdown-divider"></div>' +
                            '<a href="' + val.data.url + '" class="dropdown-item preview-item">' +
                            '<div class="preview-thumbnail">' +
                            '<div class="preview-icon bg-success">' +
                            '<i class="mdi mdi-calendar"></i>' +
                            '</div>' +
                            '</div>' +
                            '<div class="preview-item-content d-flex align-items-start flex-column justify-content-center">' +
                            '<h6 class="preview-subject font-weight-normal mb-1">' + val.title + '</h6>' +
                            '<p class="text-gray ellipsis mb-0"> ' + val.body + ' </p>' +
                            '</div>' +
                            '</a>';
                    });
                    $('#notifications_list').html(ul);
                }
            }
        });

    }
</script>

<script>
    @if(auth()->user()->type == 'school_supervisor')
    var token = document.getElementById('socketToken').value;
    Echo.connector.options.auth.headers['Authorization'] = 'Bearer ' + token
    Echo.join(`Branch.${document.getElementById('branch_id').value}`)
        .listen('.VideoCallEvent', (response) => {
            console.log(response);
            $("#video_call_request").modal({backdrop: 'static', keyboard: false});
            document.getElementById('call_request_id').setAttribute('value', response.videoCallRequest.id)
            $(".modal-body").html(`<p>${response.message}</p>`)
        })
        .listen('.VideoCallCancelEvent', (response) => {
            $("#video_call_request").modal('toggle');
        });
    @endif
    function handleVideoCallStatus(status) {
        $("#video_call_request").modal('toggle');
        $.ajax({
            headers: {
                Authorization: 'Bearer ' + token
            },
            url: '/video-call/status/update',
            method: 'post',
            data: {
                "call_request_id": document.getElementById('call_request_id').value,
                status
            }
        }).then(res => {
            if (res.token) {
                // console.log(res)
                ///call?call=${e.video_call_request.id}&channel_token=${e.token}&authId=${this.$auth.user.id}&channel=${e.channel}
                window.open(`${document.getElementById('sdk_host').value}call?call=${document.getElementById('call_request_id').value}&channel_token=${res.token}&authId={{auth()->id()}}&channel=${res.channel}&locale={{app()->getLocale()}}`, 'superWindow',
                    `toolbar=no,
                    location=no,
                    status=no,
                    menubar=no,
                    scrollbars=yes,
                    resizable=yes,
                    width=830,
                    height=630`)
            }
        });
    }
</script>

<script>
    // Your web app's Firebase configuration


    $(document).ready(function () {
        $('.select2').select2();

        var firebaseConfig = {
            apiKey: "AIzaSyD9dLPRH_TC2ow99u4fx66yIevHscUZOwY",
            authDomain: "ouredu-240bf.firebaseapp.com",
            databaseURL: "https://ouredu-240bf.firebaseio.com",
            projectId: "ouredu-240bf",
            storageBucket: "ouredu-240bf.appspot.com",
            messagingSenderId: "500852686542",
            appId: "1:500852686542:web:274d2d19e129a35a581a9f",
            measurementId: "G-B6YMYS2HC6",
            fcmPublicVapidKey:
                "BMCOGvwhWD0UGiCxN4X_ZKFvLqrAxpKONkduTzHLFJAwEJ6HT60AZPh8iJylW6MsgfroN8PX20NjmHPuRcETG1s"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();
        var messaging = firebase.messaging();

        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (token) {

                if (window.requestIdleCallback) {
                    requestIdleCallback(function () {
                        Fingerprint2.get(function (components) {

                            finger = Fingerprint2.x64hash128(
                                components
                                    .map(pair => {
                                        return pair.value;
                                    })
                                    .join(),
                                31)

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: '{{ route('notifications.updateUserToken')}}',
                                type: 'post',
                                headers: {"Authorization": "Bearer  {{(app(\App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface::class))->createAuthToken(\App\OurEdu\Users\Auth\Enum\TokenNameEnum::DYNAMIC_lINKS_Token)}}"},

                                data: JSON.stringify({
                                    "data": {
                                        "type": "notification",
                                        "id": "null",
                                        "attributes": {
                                            "fingerprint": finger,
                                            "device_token": token,
                                            "device_type": "web"
                                        }
                                    }


                                }),
                                dataType: 'JSON',
                                success: function (response) {
                                    console.log(response)
                                },
                                error: function (err) {
                                    console.log(" Can't do because: " + err);
                                },
                            });
                            console.log(finger) // an array of components: {key: ..., value: ...}
                        })
                    })
                }

            })
            .catch(function (err) {
                console.log("Unable to get permission to notify.", err);
            });

        messaging.onMessage(function (payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(noteTitle, noteOptions);
        });

        self.addEventListener('notificationclick', function (e) {
            const notification = e.notification
            // MARK 1 -> always takes first item
            const clickAction = notification.click_action
            const action = e.action
            if (action === 'close') {
                notification.close()
            } else {
                clients.openWindow(clickAction)
                notification.close()
            }
        })

    });
</script>
@stack('scripts')
</body>
</html>
