@extends('layouts.school_manager_layout')

@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
            {{  Form::open(['route'=>'school-account-manager.branch-grade-classes.assign-grade-classes', 'method'=>'post', 'class'=>'form-vertical form-label-left', 'files' => true])  }}
            @include('school_account_manager.assign_branch_data.form')
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
    </div>
@endsection

