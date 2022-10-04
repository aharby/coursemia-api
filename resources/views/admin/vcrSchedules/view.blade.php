@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <a href="{{ route('admin.vcr_schedules.get.edit',$row->id) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endpush
@section('content')
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap">
            <tbody>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.Subject Name') }}</th>
                <td width="75%" class="text-center">{{ $row->subject->name }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.Instructor Name') }}</th>
                <td width="75%" class="text-center">{{ $row->instructor->name }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.From Date') }}</th>
                <td width="75%" class="text-center">{!!  $row->from_date !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.To Date') }}</th>
                <td width="75%" class="text-center">{!! $row->to_date !!}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.Price') }}</th>
                <td width="75%" class="text-center">{!! $row->price !!} {{ trans('subject_packages.riyal') }}</td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.Working Days') }}</th>
                <td width="75%" class="text-center">
                    @foreach($row->workingDays as $workingDay)
                        <table class="table table-bordered">
                            <tr>
                                <th><strong>{{$workingDay->day }}</strong></th>
                                <td>{{ $workingDay->from_time ?  Carbon\Carbon::parse($workingDay->from_time)->format('g:i A') : '--' }}</td>
                                <td>{{  $workingDay->to_time ?  Carbon\Carbon::parse($workingDay->to_time)->format('g:i A') : '--' }}</td>
                            </tr>
                        </table>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th width="25%" class="text-center">{{ trans('vcr_schedule.Is active') }}</th>
                <td width="75%" class="text-center">{!!  $row->is_active ? '<span class="label label-primary">'.trans('vcr_schedule.active') : '<span class="label label-danger">'.trans('vcr_schedule.not active') !!}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
