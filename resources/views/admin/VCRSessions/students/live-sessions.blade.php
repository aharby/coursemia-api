@extends('layouts.admin_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)

@section('content')
    <div class="row">
        @if(!$rows->isEmpty())
            <table class="table table-striped table-bordered dt-responsive nowrap">
                <thead>
                <tr>
                    <th class="text-center">{{ trans('live_sessions.Name') }}</th>
                    <th class="text-center">{{ trans('courses.Instructor') }}</th>
                    <th class="text-center">{{ trans('live_sessions.created on') }}</th>
                    <th class="text-center">{{ trans('VCRSessions.attendance number') }}</th>
                    <th class="text-center">{{ trans('live_sessions.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->name }}</td>
                        <td>{{ "{$row->instructor->first_name} {$row->instructor->last_name}" }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>{{ $row->v_c_r_session_presence_count }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-md-2 col-md-2" data-step="2" data-intro="{{trans('introJs.You Can View Live sessions!')}}"  data-position='right' >
                                    <div class="form-group">
                                        <a class="btn btn-xs btn-primary" href="{{  route('admin.liveSessions.get.view',$row->id) }}" data-toggle="tooltip" data-placement="top" data-title="{{ trans('course.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
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
