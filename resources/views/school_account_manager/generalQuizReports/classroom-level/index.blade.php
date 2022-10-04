@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-account-manager.general-quizzes-reports.branch.reports.class.levels.charts", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right">{{ trans("general_quizzes.view chart") }}</a>
    @if(count($generalQuizzes))
        <a href="{{ route("school-account-manager.general-quizzes-reports.student.reports.class.levels.export", request()->query->all()) }}" target="_blank" class="btn btn-md btn-success align-right" style="margin: 10px;">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">

        @if(!empty($generalQuizzes))
            @include('school_account_manager.generalQuizReports.classroom-level._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.Branch Name') }}</th>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.classroom') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.quiz date') }}</th>

                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.count of quiz students') }}</th>
                                        <th class="text-center">{{ trans('quiz.highest grade') }}</th>
                                        <th class="text-center">{{ trans('quiz.lowest grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($generalQuizzes as $quiz)
                                    <tr class="text-center">
                                        <td>{{ $quiz->branch->name ??''}}</td>
                                        <td>{{ $quiz->gradeClass->title ??''}}</td>
                                        <td>
                                        @foreach($quiz->classrooms as $classroom)
                                            ( {{ $classroom->name ??'' }} )
                                        @endforeach
                                        </td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>{{ trans('quiz.'.$quiz->quiz_type) ??''}}</td>
                                        <td>{{ $quiz->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format("Y/m/d") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format("h:i a") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format("h:i a") ??''}}</td>
                                        <td>{{ $quiz->studentsAnswered->count() ?? 0 }}</td>
                                        <td>{{ $quiz->highest_grade ?? 0 }}</td>
                                        <td>{{ $quiz->lower_grade ?? 0 }}</td>

                                        <td>{{ round($quiz->studentsAnswered->average('score_percentage'),2) ?? 0 }} %</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $generalQuizzes->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
@push('scripts')
    <script>

        function hideFields () {
            const IDs = ["classroom_id"];

            IDs.forEach(function (item) {
                let selector = "#" + item;
                $(selector).closest(".col-4").hide();
                $(selector).empty();
            })
        }

        function showFields () {
            const IDs = ["classroom_id"];

            IDs.forEach(function (item) {
                $("#" + item).closest(".col-4").show();
            })
        }

        $("#branchSelect").change(function () {
            $('#gradeClass_id').empty();
            $('#gradeClass_id').trigger('change')
            $('#gradeClass_id').append(`<option value="" selected> {{ __("quiz.gradeClass") }} </option>`);


            let branch_id = $(this).val();

            $.get('{{ route('school-branch-supervisor.classrooms.branches.gradeClass') }}' + `/${branch_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    $.each(response.gradeClasses, function (i, item) {
                        $('#gradeClass_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });

        $("#gradeClass_id").change(function () {
            $('#classroom_id').empty();
            $('#classroom_id').trigger("change");
            $('#classroom_id').append(`<option value="" selected> {{ __("quiz.classroom") }} </option>`);


            let gradeClass_id = $(this).val();
            let branch_id = $("#branchSelect").val();

            $.get('{{ route('school-branch-supervisor.classrooms.gradeClass.Classroom') }}' + `/${gradeClass_id}/${branch_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'gradeClass': $(this).val(),
                })
                .done(function (response) {
                    $.each(response.classrooms, function (i, item) {
                        $('#classroom_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });


        @if(request()->old('classroom_id'))
        $('#classroom_id').trigger('change');
        @endif


        @if(request()->old('classroom_id'))
        $('#classroom_id').trigger('change');
        @endif

        $(document).ready(function () {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
