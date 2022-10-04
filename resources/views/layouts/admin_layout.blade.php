@extends('layouts.app')

@section('body_class','nav-md')
@include('flash::message')

@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('admin.partials.navigation')
                @include('admin.partials.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
                <div class="page-title">
                    <div class="title_left">
                        <h1 class="h3">@yield('title')</h1>
                        @stack('button')
                    </div>
                    @if(isset($breadcrumb))
                    @include('admin.partials.breadcrumb')
                    @endif
                </div>
                @if(isset($filters))
                    @include('formFilters.build' , $filters)
                @endif
                @yield('content')
            </div>

            <footer>
                @include('admin.partials.footer')
            </footer>
        </div>
    </div>
@endsection

@section('styles')
    @if(app()->getLocale() == 'ar')
        {{ Html::style(mix('assets/admin-rtl/css/admin.css')) }}
    @else
        {{ Html::style(mix('assets/admin/css/admin.css')) }}
    @endif
    @stack('css')
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
    integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
{{ Html::style('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css') }}
{{ Html::style('https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css') }}

@endsection

@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

    @if(app()->getLocale() == 'ar')
        {{ Html::script(mix('assets/admin-rtl/js/admin.js')) }}
    @else
        {{ Html::script(mix('assets/admin/js/admin.js')) }}
    @endif
    <script>
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            singleClasses: "picker_2",
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format) );
        });
        $('div.alert').not('.alert-important').delay(3000).fadeOut(350);


        var nowDate = new Date();
        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);

        // limited date picker
        $('.nowdatepicker').daterangepicker({
                minDate: today,
                singleDatePicker: true,
                singleClasses: "picker_2",
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false
            }).on("apply.daterangepicker", function (e, picker) {
                picker.element.val(picker.startDate.format(picker.locale.format) );
            });

            // time only picker
        $('.timepicker').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 1,
            timePickerSeconds: true,
            singleDatePicker: true,
            locale: {
                format: 'HH:mm:ss'
            }
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

    </script>

    <script>
        $(document).ready(function () {
            $('form').submit(function (e) {
                $(this).find("button[type='submit']").attr("disabled", true);
                return true;
            });

            $('#sortSelector').val("{{request()->input('sortby')}}")
        });
    </script>

    @stack('js')
@endsection

