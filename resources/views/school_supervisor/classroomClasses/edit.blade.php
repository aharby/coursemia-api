@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
            {!! Form::model($classRoomClass, ['method' => 'post']) !!}
                @include('school_supervisor.classroomClasses.partials._form')
            {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('school_supervisor.classroomClasses.partials._script')
    <script>
        $(document).ready(function () {
            $('#from_time').timepicker('setTime', '{{ $classRoomClass->from_time }}');
            $('#to_time').timepicker('setTime', '{{ $classRoomClass->to_time }}');
        });
    </script>
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
    </style>
@endpush
