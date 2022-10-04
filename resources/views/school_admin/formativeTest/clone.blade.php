@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['method' => 'post','route'=>['school-admin.formative-test.clone', $formativeTest->id],'class'=>'form-vertical form-label-left' ,  "enctype"=>"multipart/form-data"]) }}

                    @include('school_admin.formativeTest.form')
                    <div class="form-group">
                        <div class="form-layout-footer">
                            <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('school_supervisor.classroomClasses.partials._script')
@endpush

