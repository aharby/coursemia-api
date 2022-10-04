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
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">
            {{ Form::model($row,['method' => 'put','class'=>'form-vertical form-label-left', 'files' => true]) }}
            @include('school_account_manager.school_account_branches.form')
            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
                    </div></div></div></div>
    </div>
@endsection

