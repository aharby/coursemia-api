@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.vcr_schedules.get.create') }}" class="btn btn-success">{{ trans('vcr_schedule.Create') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <iframe src="{{$url}}" frameborder="0" style="
    width: 100%;
    height: 500px;
"></iframe>
    </div>
@endsection
