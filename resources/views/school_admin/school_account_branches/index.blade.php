@extends('layouts.school_admin_layout')
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
                    <th class="text-center">{{ trans('school-account-branches.School') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Branch') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Active') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.created on') }}</th>
                    <th class="text-center">{{ trans('school-account-branches.Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    <tr class="text-center">
                        <td>{{ $row->schoolAccount->name }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{!!  $row->is_active ? '<span class="btn btn-xs btn-primary">'.trans('school-account-manager.active') : '<span class="btn btn-xs btn-danger">'.trans('school-account-manager.not active') !!}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="2" data-intro="{{trans('introJs.You Can View Courses!')}}"  data-position='right' >
                                        <a class="btn btn-xs btn-primary"
                                               href="{{  route('school-admin.school-account-branches.getView',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-account-branches.view') }}">
                                            {{ trans('app.View') }}
                                        </a>
                                </div>

                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit Courses!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <!--   -->
                                        <a class="btn btn-xs btn-info"
                                           href="{{ route('school-admin.school-account-branches.get.edit',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-account-branches.edit') }}">
                                            {{ trans('app.Edit') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit Courses!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                        <div class="dropdown">
                                            <button class="btn btn-xs btn-primary " type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Details
                                            </button>
                                            <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                                                <li class="dropdown-item"><a href="{{ route('school-branch-supervisor.grade-classes.get.index', ["branch" => $row]) }}">{{ trans('navigation.Grade Classes') }}</a></li>
                                                <li class="dropdown-item"><a href="{{ route('school-branch-supervisor.subject-instructors.get.school-instructor', ["branch" => $row]) }}">{{ trans('navigation.School Instructors') }}</a></li>
                                                <li class="dropdown-item"><a href="{{ route('school-branch-supervisor.classrooms.get.index', ["branch" => $row]) }}">{{ trans('navigation.Classrooms') }}</a></li>
                                                <li class="dropdown-item"><a href="{{ route('school-branch-supervisor.students.get.index', ["branch" => $row]) }}">{{ trans('navigation.Students') }}</a></li>
                                                <li class="dropdown-item"><a href="{{ route('school-branch-supervisor.students.get.parents', ["branch" => $row]) }}">{{ trans('navigation.Parents') }}</a></li>
                                                <li class="dropdown-item">
                                                <!--      -->
                                                    <a href='{{ route("school-admin.reports.students.class.presence", ["branch" => $row]) }}'>{{ trans('navigation.Students class Presence') }}</a></li>
                                                <li class="dropdown-item"><a href="{{ route('school-admin.reports.students.subjects.presence',['branch' =>$row]) }}">{{ trans('navigation.Students Subjects Presence') }}</a></li>
                                                <li class="dropdown-divider"></li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-2 col-xs-2 " data-step="4" data-intro="{{trans('introJs.You Can Edit Courses!')}}"  data-position='right' >
                                    <div class="col-md-3 col-sm-3 col-xs-3 form-group">
                                    <!--  -->
                                        <a class="btn btn-xs btn-info"
                                           href="{{ route('school-admin.school-account-branches.branch.subjects.index',$row->id) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('school-account-branches.Subjects Permissions') }}">
                                            {{ trans('school-account-branches.Subjects Permissions') }}
                                        </a>
                                    </div>
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

@push("scripts")
    <script>
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })

    </script>
@endpush
