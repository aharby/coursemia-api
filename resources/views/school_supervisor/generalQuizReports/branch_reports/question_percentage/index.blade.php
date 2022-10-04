@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-branch-supervisor.general-quizzes-reports.question.percentage.report.index.charts", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right">{{ trans("general_quizzes.view chart") }}</a>
@endsection

@section('content')
    <div class="row">

        @if (!empty($generalQuizzes))
            @include('school_supervisor.generalQuizReports.branch_reports.question_percentage._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.start_date') }}</th>
                                        <th class="text-center">{{ trans('quiz.end_date') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Attend Students') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.highest grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.lower grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score Percentage') }}</th>
                                        <th class="text-center">{{ trans('quiz.actions') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                    @foreach ($generalQuizzes as $quiz)
                                        <tr class="text-center">
                                            <td>{{ $quiz->gradeClass->title ?? '' }}</td>
                                            <td>{{ $quiz->subject->name ?? '' }}</td>
                                            <td>{{ trans('general_quizzes.' . $quiz->quiz_type) }}</td>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('Y/m/d') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format('Y/m/d') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('h:i a') ?? '' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format('h:i a') ?? '' }}</td>
                                            <td>{{ $quiz->attend_students ?? 0 }}</td>
                                            <td>{{ $quiz->highest_grade ?? 0.0 }}</td>
                                            <td>{{ $quiz->lower_grade ?? 0.0 }}</td>
                                            <td>{{ round($quiz->studentsAnswered->average('score_percentage'), 2) }} %
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-xs"
                                                    href="{{ route('school-branch-supervisor.general-quizzes-reports.question.percentage.report.questions', $quiz->id) }}"
                                                    title="{{ trans('general_quizzes.View score percentage for questions') }}">
                                                    <i class="mdi mdi-eye"></i>
                                                    {{ trans('general_quizzes.View score percentage for questions') }}
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
        $("#gradeClass_id").change(function() {
            const subject = $('#subject_id')
            subject.empty()
            subject.append(`<option value="" selected> {{ trans('quiz.subject') }} </option>`)
            let gradeClass_id = $(this).val()
            let branch_id = "{{ $branch->id }}";

            $.get('{{ route('school-account-manager.manager-reports.gradeClass.subjects') }}' +
                    `/${gradeClass_id}/${branch_id}`, {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                    })
                .done(function(response) {
                    $.each(response.subjects, function(i, item) {
                        subject.append(`<option value="${i}">${item}</option>`)
                    })
                })
        })

        $(document).ready(function() {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
