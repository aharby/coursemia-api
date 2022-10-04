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

            {{ Form::open(['route'=>'school-account-manager.school-account-branches.post-update-password','method' => 'post','class'=>'form-vertical form-label-left' ]) }}

            {!! Form::hidden('user_id',$row->id)!!}

            @include('form.input',['type'=>'text','name'=>'name','value'=> $row->name ?? null,
            'attributes'=>['class'=>'form-control', 'disabled', 'label'=>trans('users.name'),'placeholder'=>trans('users.name'),'required'=>'required']])

            @include('form.input',['type'=>'text','name'=>'email','value'=> $row->email ?? null,
            'attributes'=>['class'=>'form-control', 'disabled', 'label'=>trans('users.email'),'placeholder'=>trans('users.email'),'required'=>'required']])

            @include('form.input',['type'=>'text','name'=>'type','value'=> $row->type ?? null,
            'attributes'=>['class'=>'form-control', 'disabled', 'label'=>trans('users.type'),'placeholder'=>trans('users.type'),'required'=>'required']])

            <div class="row mg-t-20 form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12" for="name">
                    @lang('users.password')
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="password" class="form-control" name="password" value="">
                </div>
            </div>

            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

