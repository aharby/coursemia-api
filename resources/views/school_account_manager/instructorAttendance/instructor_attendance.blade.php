@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    @if(count($instructors))
        <a href="{{ route("school-account-manager.reports.instructor.sessions.attendance.export", request()->all()) }}" target="_blank" class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">
        @include('school_account_manager.instructorAttendance._filter')

        @if(!empty($instructors))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('instructors.Instructor Name') }}</th>
                                        <th class="text-center">{{ trans('instructors.Instructor ID') }}</th>
                                        <th class="text-center">{{ trans('instructors.Branch Name') }}</th>
                                        <th class="text-center">{{ trans('instructors.Total of Sessions') }}</th>
                                        <th class="text-center">{{ trans('instructors.Session Attend') }}</th>
                                        <th class="text-center">{{ trans('instructors.Session Absence') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($instructors as $instructor)
                                    <tr class="text-center">
                                        <td>{{ $instructor->name ??''}}</td>
                                        <td>{{ $instructor->username ?? '' }}</td>
                                        <td>{{ $instructor->branch->name ??'' }}</td>
                                        <td>{{ $instructor->school_instructor_sessions_count }}</td>
                                        <td>{{ $instructor->v_c_r_sessions_presence_count }}</td>
                                        <td>{{ ($instructor->school_instructor_sessions_count ?? 0) - ($instructor->v_c_r_sessions_presence_count ?? 0) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $instructors->withQueryString()->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection


@push('scripts')
    @include('school_supervisor.classroomClasses.partials._script')
    <script>
        $(document).ready(function () {
            $('#from_date').datepicker({
                maxDate: 0
            });
            $('#to_date').datepicker({
                maxDate: 0
            });
        });
    </script>
@endpush
