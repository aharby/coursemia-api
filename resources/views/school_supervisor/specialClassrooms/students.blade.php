@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($rows))
{{--        @section('buttons')
            <div class="col-md-2 col-sm-2 col-xs-2" data-step="2" data-intro="Export Students" data-position='right'>
                <a href="{{ route('school-branch-supervisor.students.get.export').'?'.request()->getQueryString() }}"
                   class="btn btn-primary">{{ trans('students.Export') }}</a>
            </div>
        @endsection--}}
        @include('school_supervisor.students._filter',['username'=>true,'mobile' => false,'classroom' => false])
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('students.student name') }}</th>
                                    <th class="text-center">{{ trans('students.ID') }}</th>
                                    <th class="text-center">{{ trans('students.classroom') }}</th>
                                    <th class="text-center">{{ trans('students.educational system') }}</th>
                                    <th class="text-center">{{ trans('students.grade class') }}</th>
                                    <th class="text-center">{{ trans('students.created on') }}</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($rows as $row)
                                <tr class="text-center">
                                    @if($row->user)
                                        <td>{{ $row->user->name ??''}}</td>
                                        <td>{{ $row->user->username ?? '' }}</td>
                                        <td>{{ $row->classroom->name ??'' }}</td>
                                        <td>{{ $row->educationalSystem->name??'' }}</td>
                                        <td>{{ $row->classroom->branchEducationalSystemGradeClass->gradeClass->title ??'' }}</td>
                                        <td>{{ $row->created_at }}</td>
                                        <td>
                                            @if(can('view-students'))
                                                <a class="btn btn-primary btn-xs" href="{{$module}}/view/{{$row->id}}"
                                                   title="{{trans('students.View')}}">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            @endif

                                            <a class="btn btn-xs btn-info"
                                               href="{{ route('school-branch-supervisor.students.get.edit', $row) }}"
                                               data-toggle="tooltip" data-placement="top"
                                               data-title="{{ trans('classrooms.edit') }}">
                                                {{ trans('app.edit') }}
                                            </a>

                                            @if(can('update-students'))
                                                {{--                                        {{dd($row->user->is_active)}}--}}
                                                @if($row->user->is_active)
                                                    <a class="btn btn-xs btn-danger"
                                                       href="{{  route('school-branch-supervisor.students.get.active-student',$row->id) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('course.Deactivate') }}">
                                                        {{ trans('app.Deactivate') }}
                                                    </a>
                                                @else
                                                    <a class="btn btn-xs btn-success"
                                                       href="{{  route('school-branch-supervisor.students.get.active-student',$row->id) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('course.Activate') }}">
                                                        {{ trans('app.Activate') }}
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                </tr>
                                @endif
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
