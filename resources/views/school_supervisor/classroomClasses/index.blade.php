@extends('layouts.school_manager_layout')
@section('title', @$page_title)

@section('buttons')
    <div  class="col">
        <a href="{{ route('school-branch-supervisor.classrooms.classroomClasses.get.import' , request()->route('classroom')) }}"
           class="btn btn-success">{{ trans('classroomClass.import') }}</a>
    </div>
    @if(can('create-classroomClasses'))
        <div  class="col">
            <a href="{{ route('school-branch-supervisor.classrooms.classroomClasses.get.create' , request()->route('classroom')) }}"
               class="btn btn-success">{{ trans('classroomClass.Create') }}</a>
        </div>
    @endif
    @if(can('view-classroomClasses'))
        <div class="col">
            <a href="{{ route('school-branch-supervisor.getVue')}}/class/{{request()->route('classroom') }}/timetable"
               class="btn btn-success">{{ trans('classroomClass.timetable') }}</a>
        </div>
    @endif
@endsection

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
                                    <th class="text-center">{{ trans('classroomClass.Class Room Name') }}</th>
                                    <th class="text-center">{{ trans('classroomClass.Subject') }}</th>
                                    <th class="text-center">{{ trans('classroomClass.Instructor') }}</th>
                                    <th class="text-center">{{ trans('classroomClass.Start Date') }}</th>
                                    <th class="text-center">{{ trans('classroomClass.End Date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)

                                    <tr class="text-center">
                                        <td>{{ $row->classroom->name ?? '' }}</td>
                                        <td>{{ $row->subject->name ?? '' }}</td>
                                        <td>{{ $row->instructor ? $row->instructor->name : ''}}</td>
                                        <td>{{ $row->from ?  $row->from->format('Y-m-d') : ''}}</td>
                                        <td>{{ $row->until_date ? $row->until_date->format('Y-m-d') : ''}}</td>
                                        <td>
                                            @if(can('delete-classroomClasses'))
                                                <a class="btn btn-xs btn-danger delete-confirm"
                                                   href="{{  route('school-branch-supervisor.classrooms.classroomClasses.delete',[ 'classroom' => request()->route('classroom') , 'classroomClass' => $row->id]) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('app.Delete') }}">
                                                    {{ trans('app.Delete') }}
                                                </a>
                                                @include('partials.sweetalert',['title'=> trans('app.delete_classroom_class_message'), 'text' => trans('app.delete_classroom_class_note')])

                                            @endif

                                            @if(can('view-sessions'))
                                                    <a class="btn btn-xs btn-info"
                                                       href="{{  route('school-branch-supervisor.sessions.class.get.index',['classroomClass' => $row->id]) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('classroomClass.Sessions') }}">
                                                        {{ trans('classroomClass.Sessions') }}
                                                    </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $rows->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
