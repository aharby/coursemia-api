@extends('layouts.school_manager_layout')
@section('title')
{{ @$page_title }}
@endsection

@section('buttons')
        @if(request()->filled("classroom") and  request()->filled("date") and count($students))
            <a href="{{ route("school-account-manager.reports.students.subjects.presence.export", array_merge(["branch" => $branch ?? null], request()->all())) }}" target="_blank" class="btn btn-md btn-success align-right">{{ trans("app.Export") }}</a>
        @endif
@endsection

@section('content')
<div class="row">
    @if (!request()->filled("classroom") or !request()->filled("date"))
        <div class="col-md-12 grid-margin stretch-card">
        <p class="h3">
            {{ trans('reports.please select date and class to firstly')  }}
        </p>
        </div>
    @endif
    @include('school_supervisor.reports.subject-presence-filter')
        <div class="col-md-12 grid-margin stretch-card">
        @if (request()->filled("classroom") or request()->filled("date"))
        @if(count($classSessions) and count($students))
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead-dark>
                            <tr>
                                <th class="text-center">{{ trans('students.student name') }}</th>
                                <th class="text-center">{{ trans('students.username') }}</th>

                                @foreach($classSessions as $session)
                                    <th class="text-center">{{ $session->subject->name }} <br> {{ $session->from->format("h:i") . " - " . $session->to->format("h:i") }}</th>
                                @endforeach

                            </tr>
                        </thead-dark>
                        <tbody>
                            @foreach($students as $student)
                                <tr class="text-center">
                                    <td>{{ $student->name ??''}}</td>
                                    <td>{{ $student->username ?? '' }}</td>

                                    @foreach($classSessions as $session)
                                        @if(in_array($session->id, $student->attendSessions))
                                            <td style="color: #00ff00">✔</td>
                                        @else
                                            <td style="color: #ff0000">X</td>
                                        @endif
                                    @endforeach

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="pull-right">
{{--        {{ $rows->links() }}--}}
    </div>
    @else
        @include('partials.noData')
        @endif
        @endif
</div>
</div>
@endsection

@push('scripts')
    @include('school_supervisor.classroomClasses.partials._script')
    <script>
        $(document).ready(function () {

            $("#from_time").datepicker({
                maxDate: 0
            });
        });
    </script>
@endpush
