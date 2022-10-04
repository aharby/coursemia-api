@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title . ' [' .$parent->name??'' . ']')
@push('button')
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2" data-position='right' >
            @if (get_class($parent) == 'App\OurEdu\Courses\Models\Course')
            <a href="{{  route('admin.vcr-sessions.vcr-sessions.courses.attendance.export', array_merge(["course" => $parent->id], request()->query->all())) }}" class="btn btn-success">{{ trans('VCRSessions.export') }}</a>  
            @endif
        </div>
    </div>
@endpush
@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">{{ trans('VCRSessions.date') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.Instructor Name') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.started at') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.ended at') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.attendance number') }}</th>
                </tr>
                </thead>
                <tbody>
                @php
                $pageNumber = request()->query("page") ? request()->query("page")-1 : 0;
                $serial = $pageNumber * env("PAGE_LIMIT", 15) + 1;
                @endphp
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $serial++ }}</td>
                        <td>{{ $row->time_to_start ? date("d-m-Y", strtotime($row->time_to_start)) : "" }}</td>
                        <td>{{ $row->instructor->name }}</td>
                        <td>{{ $row->time_to_start ? date("H:i:s", strtotime($row->time_to_start)) : "" }}</td>
                        <td>{{ $row->time_to_end ? date("H:i:s", strtotime($row->time_to_end)) : ""}}</td>
                        <td>{{ $row->v_c_r_session_presence_count }}</td>
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
