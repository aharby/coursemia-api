@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    @if($row->status == \App\OurEdu\Courses\Enums\CourseSessionEnums::ACTIVE)
        <div class="row">
        <a href="{{ route('admin.courseSessions.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
    @endif
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="20%">@lang('course_sessions.Content')</th>
                <td>{{ $row->content }}</td>
            </tr>
            <tr>
                <th width="20%">{{ trans('course_sessions.Status') }}</th>
                <td>{!! $row->country->status ?? '' !!}</td>
            </tr>
            
            <tr>
                <th width="20%">{{ trans('course_sessions.Date') }}</th>
                <td>{!! $row->date ?? '' !!}</td>
            </tr>

            <tr>
                <th width="20%">{{ trans('course_sessions.Start time') }}</th>
                <td>{!!  $row->start_time !!}</td>
            </tr>

            <tr>
                <th width="20%">{{ trans('course_sessions.End time') }}</th>
                <td>{!!  $row->end_time !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
