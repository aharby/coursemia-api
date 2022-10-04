<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/v4-shims.css">

        {{--CSRF Token--}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ trans('app.ta3lom') }} @stack('title')</title>


        {{--Common App Styles--}}
        @if(app()->getLocale() == 'ar')
            {{ Html::style('assets/app-ar/css/app.css') }}
        @else
            {{ Html::style('assets/app/css/app.css') }}
        @endif

        {{--Styles--}}
        @yield('styles')
        {{--Head--}}
        @yield('head')

    </head>
    <body class="@yield('body_class')">

        {{--Page--}}
        @yield('page')

        {{--Common Scripts--}}
        @if(app()->getLocale() == 'ar')
            {{ Html::script('assets/app-ar/js/app.js') }}
        @else
            {{ Html::script('assets/app/js/app.js') }}
        @endif




        {{--Scripts--}}
        @yield('scripts')
    </body>
</html>
