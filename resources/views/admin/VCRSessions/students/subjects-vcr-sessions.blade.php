@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@push('button')
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2" data-step="1" data-intro="{{trans('introJs.You Can Create subject Packages!')}}"  data-position='right' >
            <a href="{{ route('admin.vcr-sessions.vcr-sessions.subjects.export', request()->query->all()) }}" class="btn btn-success">{{ trans('VCRSessions.export') }}</a>
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('VCRSessions.Subject Name') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.grade class') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.sessions count') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $subject)
                    <tr class="text-center">
                        <td>{{ $subject->name }}</td>
                        <td>{{ $subject->gradeClass->title }}</td>
                        {{-- Count how many days vcr would be --}}
                        @php
                          $daysCount = 0;
                          foreach ($subject->VCRSchedules as $schedule)
                              foreach ($schedule->workingDays as $day) 
                                  $daysCount += count(dayRepeated($day->day,$schedule->from_date,$schedule->to_date)); 
                        @endphp
                        <td>{{ $subject->v_c_r_sessions_count + $daysCount }}</td>
                        <td>
                            <div class="row">
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.vcr-sessions.vcr-sessions.subjects.courses',$subject->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Courses') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.vcr-sessions.vcr-sessions.subjects.live-sessions',$subject->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.Live Sessions') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a class="btn btn-xs btn-primary" href="{{  route('admin.vcr-sessions.vcr-sessions.subjects.vcr-schedule',$subject->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('app.vcr_schedule') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
