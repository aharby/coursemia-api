@extends('layouts.app')

@section('body_class','nav-md')
@include('flash::message')
@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('sme.partials.navigation')
                @include('sme.partials.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
                <div class="page-title">
                    <div class="title_left">
                        <h1 class="h3">@yield('title')</h1>
                        @stack('button')
                    </div>
                    @if(isset($breadcrumb))
                        @include('sme.partials.breadcrumb')
                    @endif
                </div>
                @yield('content')
            </div>

            <footer>
                @include('sme.partials.footer')
            </footer>
        </div>
    </div>
@endsection

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}
    <script>
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            singleClasses: "picker_2" ,
            locale : {
                format : 'YYYY-MM-DD'
            }
        });

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
        });;
    </script>
    @stack('js')
@endsection

