<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ AppName() }}</title>


        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .footer {
                position:fixed;
                width:100%;
                height:20px;
                padding:5px;
                bottom:0px;
                font-size: smaller;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
        @stack('style')

    </head>
    <body>
        <div class="flex-center position-ref full-height">

                <div class="top-right links">
                    @if(!auth()->check())
                    <a href="{{ route('auth.get.login') }}">{{ trans('auth.Admin Login') }}</a>
                    <a href="{{ route('auth.get.schoolLogin') }}">{{ trans('auth.School Login') }}</a>
                    @else
                        <a href="{{ route('profile.get.logout') }}">{{ trans('header.Logout') }}</a>
                        @php
                            if (auth()->user()->type == \App\OurEdu\Users\UserEnums::ADMIN_TYPE || auth()->user()->type == \App\OurEdu\Users\UserEnums::SUPER_ADMIN_TYPE){
                                $type = \App\OurEdu\Users\UserEnums::ADMIN_TYPE;
                            @endphp
                        <a href="{{ route($type.'.dashboard') }}">{{ trans('app.Dashboard') }}</a>
                            @php
                            }else{
                                $type = auth()->user()->type;
                            }
                        @endphp

                    @endif
                </div>

            <div class="content">
                @yield('content')
                <div class="footer">

                </div>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
