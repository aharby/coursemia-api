@extends('layouts.school_manager_layout')
@section('title', @$page_title)

@section('buttons')
    {{--    <div class="row">--}}
    <a href="{{ route('school-branch-supervisor.classrooms.classroomClasses.get.create' , request()->route('classroom')) }}" class="btn btn-success">{{ trans('classroomClass.Create') }}</a>
    {{--    </div>--}}
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
                                    <th class="text-center">{{ trans('classroomClass.Start Time') }}</th>
                                    <th class="text-center">{{ trans('classroomClass.End Time') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rows as $row)

                                    <tr class="text-center">
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                                    <a class="btn btn-xs btn-primary"
                                                       href="{{  route('school-branch-supervisor.classrooms.classroomClasses.view',[ 'classroom' => request()->route('classroom') , 'classroomClass' => $row->id]) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('classroomClass.view') }}">
                                                        {{ trans('app.View') }}
                                                    </a>
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                                    <a class="btn btn-xs btn-info"
                                                       href="{{  route('school-branch-supervisor.classrooms.classroomClasses.get.edit',[ 'classroom' => request()->route('classroom') , 'classroomClass' => $row->id]) }}"
                                                       data-toggle="tooltip" data-placement="top"
                                                       data-title="{{ trans('classroomClass.edit') }}">
                                                        {{ trans('app.edit') }}
                                                    </a>
                                                </div>
                                            </div>
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
