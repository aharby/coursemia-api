<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ appName() }} @yield('title')</title>
    <link rel="stylesheet" type="text/css" href="//www.fontstatic.com/f=DroidKufi-Regular" />
    @if(app()->getLocale() == 'ar')
        <link href="{{ asset('assets/purple/css/app_rtl.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('assets/purple/css/app.css') }}" rel="stylesheet">
    @endif
    <link rel="shortcut icon" href="/favicon.png" />
</head>

<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="row w-100">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light p-5">
                        <div class="brand-logo">
                            <img height="200" src="/img/Blue_Pixel.png">
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<script src="/assets/js/app.js"></script>
</body>

</html>
