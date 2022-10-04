@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush
@section('title', @$page_title)
@section('buttons')
    <div class="row">
        <a href="{{ route('school-branch-supervisor.classrooms.get.edit',["id"=>$row->id, "branch"=>$branch ?? null]) }}"
           class="btn btn-success">{{ trans('app.Edit') }}</a>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                <table class="table table-striped table-bordered dt-responsive nowrap">
                    <tbody>

                    @php
                        $branchEducationalSystemGradeClass = $row->branchEducationalSystemGradeClass;
                        $branchEducationalSystem = $branchEducationalSystemGradeClass->branchEducationalSystem;
                    @endphp

                    <tr>
                        <th width="25%" class="text-center">{{ trans('classrooms.Name') }} </th>
                        <td width="75%" class="text-center">{{ $row->name }}</td>
                    </tr>

                    <tr>
                        <th width="25%" class="text-center">{{  trans('classrooms.Grade Class') }}</th>
                        <td width="75%" class="text-center">{{ $branchEducationalSystemGradeClass->gradeClass->title }}</td>
                    </tr>

                    <tr>
                        <th width="25%" class="text-center">{{ trans('classrooms.Educational System') }}</th>
                        <td width="75%" class="text-center">{{ $branchEducationalSystem->educationalSystem->name?? '' }}</td>
                    </tr>
                    <tr>
                        <th width="25%" class="text-center">{{ trans('classrooms.Academic Year') }}</th>
                        <td width="75%" class="text-center">{{ $branchEducationalSystem->academicYear->title?? '' }}</td>
                    </tr>
                    <tr>
                        <th width="25%" class="text-center">{{ trans('classrooms.Educational Term') }}</th>
                        <td width="75%" class="text-center">{{ $branchEducationalSystem->educationalTerm->title?? '' }}</td>
                    </tr>

                    <tr>
                        <th width="25%" class="text-center">{{ trans('classrooms.Created at') }}</th>
                        <td width="75%" class="text-center">{{ $row->created_at ?? '' }}</td>
                    </tr>

                    </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
