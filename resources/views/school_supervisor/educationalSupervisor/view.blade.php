@extends('layouts.school_manager_layout')
@push('title')
    {{ @$page_title }}
@endpush


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
                                <th width="25%" class="text-center">{{ trans('educationalSupervisor.name') }} </th>
                                <td width="75%" class="text-center">{{ $row->name ?? ''}}</td>
                            </tr>

                            

                            <tr>
                                <th width="25%" class="text-center">{{ trans('educationalSupervisor.ID') }} </th>
                                <td width="75%" class="text-center">{{ $row->username ?? ''}}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('educationalSupervisor.Mobile') }} </th>
                                <td width="75%" class="text-center">{{ $row->mobile ??''}}</td>
                            </tr>

                            <tr>
                                <th width="25%" class="text-center">{{ trans('educationalSupervisor.Email') }} </th>
                                <td width="75%" class="text-center">{{ $row->email ??''}}</td>
                            </tr>
                            
                            <tr>
                                <th width="25%" class="text-center">{{ trans('educationalSupervisor.grade class') }} </th>
                                <td width="75%"
                                    class="text-center">{{ $gradeClasses ?? '' }}</td>
                            </tr>
                            <tr>
                                <th width="25%" class="text-center">{{ trans('educationalSupervisor.Assigned Subjects') }} </th>
                                <td width="75%"
                                    class="text-center">
                                    @if(count($assigned_subjects)>0)
                                        @foreach($assigned_subjects as $subject)
                                           ({{ $subject->name }} - {{$subject->gradeClass->title}}),
                                        @endforeach
                                    @endif
                                
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
