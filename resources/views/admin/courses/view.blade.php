@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.courses.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">@lang('courses.Name')</th>
                <td width="75%" class="text-center">{{ $row->name }}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('courses.Type') }}</th>
                <td class="text-center">{{ \App\OurEdu\Courses\Enums\CourseEnums::getFormattedTypes($row->type) }}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('courses.Subject') }}</th>
                <td class="text-center">{!!  $row->subject->name ??''  !!}</td>
            </tr>

            <tr>
                <th class="text-center">{{ trans('courses.Instructor') }}</th>
                <td class="text-center">{!!  $row->instructor->first_name ?? '' !!} {!!  $row->instructor->last_name ?? '' !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.Subscription Cost') }}</th>
                <td class="text-center">{!!  $row->subscription_cost ?? ''  !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.Start Date') }}</th>
                <td class="text-center">{!!  $row->start_date !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.End Date') }}</th>
                <td class="text-center">{!!  $row->end_date !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.Picture') }}</th>
                <td class="text-center">{!! viewImage($row->picture, 'large') !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.medium_picture') }}</th>
                <td class="text-center">{!! viewImage($row->medium_picture, 'small') !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.small_picture') }}</th>
                <td class="text-center">{!! viewImage($row->small_picture, 'small') !!}</td>
            </tr>
            <tr>
                <th class="text-center">{{ trans('courses.Is active') }}</th>
                <td class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('courses.active') : '<span class="label label-danger">'.trans('courses.not active') !!}</td>
            </tr>
{{--            <tr>--}}
{{--                <th class="text-center">{{ trans('courses.Is Top Qudrat') }}</th>--}}
{{--                <td class="text-center">{!!  $row->is_top_qudrat ? '<span class="label label-primary">'.trans('courses.yes') : '<span class="label label-danger">'.trans('courses.no') !!}</td>--}}
{{--            </tr>--}}
            </tbody>
        </table>
    </div>
@endsection
