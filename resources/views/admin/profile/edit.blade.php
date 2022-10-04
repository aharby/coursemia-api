@extends('layouts.admin_layout')

@section('title',trans('app.Edit').' '.@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::model($row,['method' => 'put','class'=>'form-vertical form-label-left','files'=>true]) }}
            @include('admin.profile.form')
            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

