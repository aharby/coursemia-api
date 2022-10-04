@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">
            {{ Form::open(['method' => 'post','route'=>['admin.users.post.add.student-teacher', 'studentId' => $student->id],'class'=>'form-vertical form-label-left']) }}
            @include('admin.users.student_student_teacher.form')
            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

