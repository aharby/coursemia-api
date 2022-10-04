@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">

        @if (!empty($generalQuizzes))
            @include('school_supervisor.generalQuizReports.branch_reports.question_percentage._filter')

            @foreach ($generalQuizzes as $quiz)
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <table class="table">
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.start_date') }}</th>
                                        <th class="text-center">{{ trans('quiz.end_date') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.actions') }}</th>
                                    </tr>
                                    <tr class="text-center">
                                        <td>{{ $quiz->gradeClass->title ?? '' }}</td>
                                        <td>{{ $quiz->subject->name ?? '' }}</td>
                                        <td>{{ trans('general_quizzes.' . $quiz->quiz_type) }}</td>
                                        <td>{{ $quiz->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('Y/m/d') ?? '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format('Y/m/d') ?? '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format('h:i a') ?? '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format('h:i a') ?? '' }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-xs"
                                               href="{{ route('school-account-manager.general-quizzes-reports.question.percentage.report.questions', $quiz->id) }}"
                                               title="{{ trans('general_quizzes.View score percentage for questions') }}">
                                                <i class="mdi mdi-eye"></i>
                                                {{ trans('general_quizzes.View score percentage for questions') }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>


                            <div class="card-body">
                                <canvas id="bar-chart_{{$quiz->id}}" width="800" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

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
        @foreach($generalQuizzes as $quiz)
        // Bar chart
        new Chart(document.getElementById("bar-chart_{{$quiz->id}}"), {
            type: 'bar',
            data: {
                labels: ["{{ trans('general_quizzes.Attend Students') }}", "{{ trans('general_quizzes.highest grade') }}", "{{ trans('general_quizzes.lower grade') }}", "{{ trans('general_quizzes.Average Score') }}"],
                datasets: [
                    {
                        // label: "Population (millions)",
                        backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f", "#3c5a9f"],
                        data: [
                            "{{ $quiz->attend_students ?? 0}}",
                            "{{ $quiz->highest_grade ?? 0.00 }}",
                            "{{ $quiz->lower_grade ?? 0.00 }}",
                            "{{ round($quiz->studentsAnswered->average('score_percentage'),2) }}"
                        ]
                    }
                ]
            },
            options: {
                legend: { display: false },
                title: {
                    display: false,
                    text: 'Predicted world population (millions) in 2050'
                }
            }
        });
        @endforeach
    </script>
@endpush
