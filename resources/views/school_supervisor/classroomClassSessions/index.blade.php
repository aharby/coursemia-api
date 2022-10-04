@extends('layouts.school_manager_layout')
@section('title', @$page_title)



@section('content')
    <div class="row">
        @if(!empty($rows))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('classroomClassSession.Class Room Name') }}</th>
                                    <th class="text-center">{{ trans('classroomClassSession.Subject') }}</th>
                                    <th class="text-center">{{ trans('classroomClassSession.Instructor') }}</th>
                                    <th class="text-center">{{ trans('classroomClassSession.Ended by Instructor') }}</th>
                                    <th class="text-center">{{ trans('classroomClassSession.Day') }}</th>
                                    <th class="text-center">{{ trans('classroomClassSession.from') }}</th>
                                    <th class="text-center">{{ trans('classroomClassSession.to') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)

                                    <tr class="text-center">
                                        <td>{{ $row->classroom->name ?? '' }}</td>
                                        <td>{{ $row->subject->name ?? '' }}</td>
                                        <td>{{ $row->instructor ? $row->instructor->name : ''}}</td>
                                        <td>

                                            @if($row->vcrSession->is_ended_by_instructor ?? false)
                                                <span style="color: #00ff00">✔</span>
                                            @elseif($row->to > \Carbon\Carbon::now())
                                                <span>Not started</span>
                                            @else
                                                <span style="color: #ff0000">X</span>
                                            @endif

                                        </td>
                                        <td>{{ $row->from ?  $row->from->format('Y-m-d') : ''}}</td>
                                        <td>{{ $row->from->format("H:i") ?? ''}}</td>
                                        <td>{{ $row->to->format("H:i") ?? ''}}</td>
                                        <td>
                                        @if(can('update-sessions') and $row->to >= \Carbon\Carbon::now())
                                            <a class="btn btn-xs btn-info"
                                                       href="{{  route('school-branch-supervisor.sessions.class.get.edit',[ 'classroomClassSession' => $row->id]) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('classroomClass.edit') }}">
                                                        {{ trans('app.edit') }}
                                            </a>
                                        @endif
                                        @if(can('delete-sessions') and $row->from > \Carbon\Carbon::now())
                                            <a class="btn btn-xs btn-danger"
                                                   href="{{  route('school-branch-supervisor.sessions.class.get.delete',[ 'classroomClassSession' => $row->id]) }}"
                                                   data-toggle="tooltip" data-placement="top">
                                                        {{ trans('app.Delete') }}
                                                </a>
                                        @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div></div></div></div>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
