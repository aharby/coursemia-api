@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush

@section('buttons')
        <a href="{{ route('school-branch-supervisor.students.parents.create', $row) }}"
           class="btn btn-success">{{ trans('students.Add Parent') }}</a>
@endsection


@section('title', @$page_title)
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive nowrap">
                            <tbody>
                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.student name') }} </th>
                                <td width="75%" class="text-center">{{ $row->user->name ??''}}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.grade class') }} </th>
                                <td width="75%"
                                    class="text-center">{{ $row->classroom->branchEducationalSystemGradeClass->gradeClass->title ??'' }}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.classroom') }} </th>
                                <td width="75%" class="text-center">{{ $row->classroom->name ??'' }}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.Student ID Number') }} </th>
                                <td width="75%" class="text-center">{{ $row->user->username}}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.Another Mobile Number') }} </th>
                                <td width="75%" class="text-center">{{ $row->user->mobile ??''}}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.Email') }} </th>
                                <td width="75%" class="text-center">{{ $row->user->email ??''}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header">
        <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-assistant"></i>
              </span>
            {{trans('students.Parents Data')}}
        </h3>
        <nav aria-label="breadcrumb">
            <div class="breadcrumb">
            </div>
        </nav>
    </div>

    <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead-dark>
                            <tr>
                                <th width="25%" class="text-center">{{ trans('students.Parent Name') }} </th>
                                <th width="25%" class="text-center">{{ trans('students.Parent ID Number') }} </th>
                                <th width="25%" class="text-center">{{ trans('students.Parent Mobile Number') }} </th>
                                <th width="25%" class="text-center"> </th>
                            </tr>
                        </thead-dark>
                        <tbody>
                        @foreach( $parentsStudent as $num=>$parent)
                            <tr class="text-center">
                                <td class="text-center">
                                    {{$parent->name ?? ''}}
                                </td>
                                <td class="text-center">
                                    {{$parent->username ?? ''}}
                                </td>
                                <td class="text-center">
                                    {{$parent->mobile ?? ''}}
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-xs btn-danger"
                                       href="{{  route('school-branch-supervisor.students.parents.delete',["student"=>$row->id, "parent" => $parent->id]) }}"
                                       data-toggle="tooltip" data-placement="top"
                                       data-title="{{ trans('app.Delete') }}">
                                        {{ trans('app.Delete') }}
                                    </a>

                                    <a class="btn btn-xs btn-info"
                                       href="{{  route('school-branch-supervisor.students.parents.edit',["student"=>$row, "parent" => $parent]) }}"
                                       data-toggle="tooltip" data-placement="top"
                                       data-title="{{ trans('classrooms.edit') }}">
                                        {{ trans('app.edit') }}
                                    </a>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
