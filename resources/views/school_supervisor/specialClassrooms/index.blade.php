@extends('layouts.school_manager_layout')
@section('title', @$page_title)

@section('buttons')
    @if(can('create-classrooms'))
        <a href="{{ route('school-branch-supervisor.specialClassrooms.get.create') }}"
           class="btn btn-success">{{ trans('classrooms.Create') }}</a>
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
                                    <th class="text-center">{{ trans('classrooms.Name') }}</th>
                                    <th class="text-center">{{ trans('classrooms.Grade Class') }}</th>
                                    <th class="text-center">{{ trans('classrooms.Educational System') }}</th>
                                    <th class="text-center">{{ trans('classrooms.Academic Year') }}</th>
                                    <th class="text-center">{{ trans('classrooms.Educational Term') }}</th>
                                    <th class="text-center">{{ trans('classrooms.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)
                                    @php
                                        $branchEducationalSystemGradeClass = $row->branchEducationalSystemGradeClass;
                                        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $branchEducationalSystemGradeClass->gradeClass->title }}</td>
                                        <td>{{ $branchEducationalSystem->educationalSystem->name ?? '' }}</td>
                                        <td>{{ $branchEducationalSystem->academicYear->title?? '' }}</td>
                                        <td>{{ $branchEducationalSystem->educationalTerm->title?? '' }}</td>
                                        {{--                        <td>{{ $row->created_at }}</td>--}}
                                        <td>
                                            @if(can('view-classrooms'))
                                                <a class="btn btn-xs btn-primary"
                                                   href="{{  route('school-branch-supervisor.classrooms.get.view',$row->id) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('classrooms.view') }}">
                                                    {{ trans('app.View') }}
                                                </a>
                                            @endif

                                            @if(can('update-classrooms'))
                                                <a class="btn btn-xs btn-info"
                                                   href="{{  route('school-branch-supervisor.specialClassrooms.get.edit',$row->id) }}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('classrooms.edit') }}">
                                                    {{ trans('app.edit') }}
                                                </a>
                                            @endif

                                            @if(can('view-classrooms'))
                                                <a class="btn btn-xs btn-dark"
                                                   href="{{  route('school-branch-supervisor.classrooms.classroomClasses.index',$row->id)}}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('classrooms.Classes') }}">
                                                    {{ trans('app.Classes') }}
                                                </a>
                                            @endif

                                            @if(can('view-classrooms'))
                                                <a class="btn btn-xs btn-dark"
                                                   href="{{route('school-branch-supervisor.specialClassrooms.students',['classroom'=>$row->id])}}"
                                                   data-toggle="tooltip" data-placement="top"
                                                   data-title="{{ trans('app.Students') }}">
                                                    {{ trans('app.Students') }}
                                                </a>
                                            @endif

                                            @if(can('delete-classrooms'))
                                                <a href="{{route('school-branch-supervisor.classrooms.delete',['id'=>$row->id])}}"
                                                   class="btn btn-xs btn-danger delete-confirm">
                                                    {{ trans('app.Delete') }}
                                                </a>
                                                @include('partials.sweetalert',['title'=> trans('app.delete_classroom_message'), 'text' => trans('app.delete_classroom_note')])
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
