@extends('layouts.school_admin_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['url'=> route('school-admin.profile.update'), 'method' => 'post', 'class'=>'form-vertical form-label-left', "enctype"=>"multipart/form-data"]) }}
                    @method("put")
                    @include('school_supervisor.profile.form')
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


    <div class="page-header">
        <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-assistant"></i>
              </span>
            {{trans('students.Change Password')}}
        </h3>
        <nav aria-label="breadcrumb">
            <div class="breadcrumb">
            </div>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['url'=> route('school-admin.profile.change.password'), 'method' => 'post', 'class'=>'form-vertical form-label-left', "enctype"=>"multipart/form-data"]) }}
                    @method("put")
                    @include('school_supervisor.profile.change_password_form')
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

