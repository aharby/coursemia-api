@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@push('button')
    <div class="row">
    </div>
@endpush


@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Default form</h4>
                     @include('school_supervisor.classrooms.schedule.partials._form')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $("#repeat_by").val("0");
    </script>
    @include('school_supervisor.classrooms.schedule.partials._script')
@endpush

@push('styles')
    <style>
        .time-control-label {
            height: 40px !important;
        }

        .input-group .form-control {
            border-radius: 0px !important;
        }

        .input-group .input-group-text {
            width: 120px;
            border-radius: 0px !important;
        }

        .ui-state-default {
            padding: 0 !important;
            min-width: 20px !important;
        }
        #repetition_days > .form-group label {
            margin-bottom: 0;
            padding: 0 2px;
        }
    </style>
@endpush
