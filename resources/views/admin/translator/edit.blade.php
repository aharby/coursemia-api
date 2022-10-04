@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
    @section('title',@$page_title)

@section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 x_panel">
        {!! Form::open(['method' => 'post','files' => true] ) !!} {{ csrf_field() }} {{method_field('put')}}
        @include('admin.translator.form')
        <!-- custom-file -->
        <div class="form-layout-footer">
            <button class="btn btn-primary bd-0">{{ trans('app.Save') }}</button>
        </div>
        {!! Form::close() !!}
        <!-- form-layout-footer -->
    </div>
    <!-- form-layout -->
</div>
@endsection
