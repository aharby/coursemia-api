@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.liveSessions.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">@lang('live_sessions.Name')</th>
                <td width="75%" class="text-center">{{ $row->name }}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Subject') }}</th>
                <td width="75%" class="text-center">{!!  $row->subject->name ??''  !!}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Instructor') }}</th>
                <td width="75%" class="text-center">{!!  $row->instructor->first_name ?? '' !!} {!!  $row->instructor->last_name ?? '' !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Subscription Cost') }}</th>
                <td width="75%" class="text-center">{!!  $row->subscription_cost ?? ''  !!}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Content') }}</th>
                <td width="75%" class="text-center">{!!  $row->session->content ?? '' !!}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Date') }}</th>
                <td width="75%" class="text-center">{!!  $row->session->date ?? '' !!}</td>
            </tr>

            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Start time') }}</th>
                <td width="75%" class="text-center">{!!  $row->session->start_time ?? '' !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.End time') }}</th>
                <td width="75%" class="text-center">{!!  $row->session->end_time ?? '' !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('live_sessions.Picture') }}</th>
                <td class="text-center">{!! viewImage($row->picture, 'large') !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('live_sessions.Is active') }}</th>
                <td width="75%" class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('live_sessions.active') : '<span class="label label-danger">'.trans('live_sessions.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
