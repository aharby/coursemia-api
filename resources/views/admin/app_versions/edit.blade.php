@extends('layouts.admin_layout')

@push('title')
    {{ @$page_title }}
@endpush

@section('title',@$page_title)

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ Form::model($rows,['method' => 'put','class'=>'form-vertical form-label-left']) }}
            @include('admin.app_versions.form')
            <div class="form-group">
                <div class="form-layout-footer">
                    <button type="submit" class="btn btn-success"> {{ trans('app.Save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

