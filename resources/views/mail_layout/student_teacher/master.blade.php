<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ AppName() }}</title>
    <style>
        body{
            margin: 0;
        }
        .container{
            display:flex;
            justify-content: center;
        }
        .main {
            background: #fff;
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            min-height: 100vh;
            width:600px;
        }
        .header {
            background: url("{{asset('assets/email/images/Group 806@2x.png')}}") bottom center no-repeat;
            background-size: cover;
            min-height: 250px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        .header .logo {
            margin-left: 30px;
        }
        .header .logo img {
            width: 150px;
        }
        .message{
            padding-left: 25px;
        }
        .main-footer {
            background: #36b1c3;
            width: 100%;
        }
        .main-footer .top {
            display: flex;
            justify-content: space-between;
            padding: 30px 30px 0;
        }
        .main-footer ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .main-footer a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
            font-family: "Courier New", Courier, monospace;
        }
        .main-footer li {
            margin-bottom: 10px;
        }
        .main-footer .top img {
            width: 100px;
        }
        .main-footer .bottom {
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
        }

        .footer_bottom_logo img {
            width: 70px;
        }
        .social a {
            margin: 0 5px;
        }
        .social img {
            height: 24px;
            width: 24px;
        }
        .social img.facebook {
            width: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="main">
        {{--   header    --}}
            @include('mail_layout.parent.header')

        {{--   body     --}}
        <div class="body">
            @yield('content')

        </div>

        {{--  footer   --}}
            @include('mail_layout.parent.footer')
    </div>
</div>
</body>
</html>
