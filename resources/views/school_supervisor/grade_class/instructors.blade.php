<style>

    #container {
        color: white;
        background: white;
        border: black;
        width: 100px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #content {
        background: #3e4b5b;
        /*height:30px;*/
        border-radius: 30px;
        width: 250px;


    }

</style>
@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('content')
    <div class="row">
        @if(!empty($rows))
            @section('buttons')
                @if(!request()->has("deactivated"))
                    <div class="col-md-2 col-sm-2 col-xs-2" data-step="2" data-intro="Export Students" data-position='right'>
                        <a href="{{ route('school-branch-supervisor.subject-instructors.get.view', ['branch'=>$branch?? null]) }}"
                           class="btn btn-success">{{ trans('instructors.view') }}</a>
                    </div>
                @endif
            @endsection
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ trans('grade-class.instructor') }}</th>
                                    <th class="text-center">{{ trans('users.id') }}</th>
                                    <th class="text-center">{{ trans('grade-class.Subjects') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    <tr class="text-center">
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->username }}</td>
                                        <td class="text-center">
                                            <div style="height: 100px;overflow: auto;overflow-x: hidden;">
                                                @foreach($row->schoolInstructorSubjects as $subject)
                                                    <div id="content" style="background:#f67b95;  width: 175px; color: white;">
                                                        <b>{{$subject->name}}</b>
                                                    </div>
                                                    <div id="content" style="color: white">
                                                        {{  $subject->gradeClass->title  }}
                                                    </div>
                                                    <br>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if(can('update-subjectInstructors') and !request()->has("deactivated"))
                                                <a class="btn btn-xs btn-info"
                                                   href="{{  route('school-branch-supervisor.subject-instructors.get-edit-instructor',["instructorUserId" => $row->id, "branch" => $branch ?? null]) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('course.edit') }}">
                                                    {{ trans('app.edit') }}
                                                </a>
                                            @endif

                                            @if(can('update-subjectInstructors'))
                                                @if($row->is_active)
                                                    <a class="btn btn-xs btn-danger"
                                                       href="{{  route('school-branch-supervisor.subject-instructors.get-active-instructor',$row->id) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('course.Deactivate') }}">
                                                        {{ trans('app.Deactivate') }}
                                                    </a>
                                                @else
                                                    <a class="btn btn-xs btn-success"
                                                       href="{{  route('school-branch-supervisor.subject-instructors.get-active-instructor',$row->id) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('course.Activate') }}">
                                                        {{ trans('app.Activate') }}
                                                    </a>
                                        @endif
                                        @endif
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
