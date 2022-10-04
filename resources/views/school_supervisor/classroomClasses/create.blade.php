@extends('layouts.school_manager_layout')
@section('title', @$page_title)
@section('content')
    <div class="alert alert-danger alert-dismissible validation-errors-containers" style="display: none">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i>{{ trans('app.Something went wrong') }}</h4>
        <ul class="validation-errors">
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        {!! Form::model($classRoomClass, ['method' => 'post', 'id' => 'classroom_class_form', 'name' => 'createClassroomClass']) !!}
                            @include('school_supervisor.classroomClasses.partials._form')
                        {!! Form::close() !!}

                        <div class="alert alert-danger alert-dismissible validation-errors-containers" style="display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-ban"></i>{{ trans('app.Something went wrong') }}</h4>
                            <ul class="validation-errors">
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loader-wrapper" style="display: none">
        <div id="loader" style="right: 50%;left: 50%"></div>
        <div id="loader-content">
            {{ trans("classroomClass.the table is being added") }} <a href="{{ route('school-branch-supervisor.classrooms.classroomClasses.get.create' , request()->route('classroom')) }}" target="_blank" > {{ trans("classroomClass.here") }}</a>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $("#repeat_by").val("0");
    </script>
    @include('school_supervisor.classroomClasses.partials._script')
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
