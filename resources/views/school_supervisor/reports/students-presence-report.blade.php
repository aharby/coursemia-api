@extends('layouts.school_manager_layout')
@section('title')
{{ @$page_title }}
@endsection

@section('buttons')
    @if(request()->filled("classroom") and count($rows))
        <a href="{{ route("school-account-manager.reports.students.class.presence.export", array_merge(["branch" => $branch ?? null], request()->all())) }}" target="_blank" class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
<div class="row">
    @if(!request()->filled("classroom"))
        <div class="col-md-12 grid-margin stretch-card">
            <p class="h3">
                {{ trans('reports.please select the classroom')  }}
            </p>
        </div>
    @endif

    @include('school_supervisor.reports._filter')

    @if(request()->filled("classroom"))

    @if(count($rows))
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
                                <th class="text-center">{{ trans('reports.attends') }}</th>
                                <th class="text-center">{{ trans('reports.absents') }}</th>
                            </tr>
                        </thead-dark>
                        <tbody>
                        @foreach($rows as $classroom)
                            @foreach($classroom->students as $student)
                                <tr class="text-center">
                                    <td>{{ $student->user->name ??''}}</td>
                                    <td>{{ $student->user->username ?? '' }}</td>
                                    <td>{{ $classroom->name ??'' }}</td>
                                    <td>{{ $student->user->v_c_r_sessions_presence_count }}</td>
                                    <td>{{ ($classroom->sessions_count ?? 0) - ($student->user->v_c_r_sessions_presence_count ?? 0) }}</td>
                                </tr>
                            @endforeach
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
