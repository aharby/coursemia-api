@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">
        @if(!empty($studentQuizzes))

            @foreach($studentQuizzes as $studentQuiz)
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <table class="table">
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.time') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center"></th>
                                    </tr>
                                    <tr class="text-center">
                                        <td>{{ $studentQuiz->subject->name ??''}}</td>
                                        <td>{{ $studentQuiz->generalQuiz->quiz_type ? trans("general_quizzes." . $studentQuiz->generalQuiz->quiz_type) :'' }}</td>
                                        <td>{{ $studentQuiz->generalQuiz->title ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($studentQuiz->generalQuiz->start_at)->format("Y/m/d") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($studentQuiz->generalQuiz->start_at)->format("H:m a") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($studentQuiz->generalQuiz->end_at)->format("H:m a") ??''}}</td>
                                        <td>
                                            <a class="btn btn-xs btn-info"
                                               href="{{ route('school-admin.general-quizzes-reports.student.level.student.section.performance', ["generalQuizStudent" => $studentQuiz->id]) }}"
                                               data-toggle="tooltip" data-placement="top"
                                               data-title="{{ trans('general_quizzes.details') }}">
                                                {{ trans('general_quizzes.details') }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>


                            <div class="card-body">
                                <canvas id="bar-chart_{{$studentQuiz->id}}" width="800" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        <div class="pull-right">
            {{ $studentQuizzes->links() }}
        </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection

@push("scripts")
    <script>
        @foreach($studentQuizzes as $studentQuiz)
        // Bar chart
        new Chart(document.getElementById("bar-chart_{{$studentQuiz->id}}"), {
            type: 'bar',
            data: {
                labels: ["{{ trans("general_quizzes.Average student performance") }}", "{{ trans("general_quizzes.The overall average of the students") }}"],
                datasets: [
                    {
                        // label: "Population (millions)",
                        backgroundColor: ["#3e95cd", "#8e5ea2"],
                        data: [
                            "@if( $studentQuiz->generalQuiz && $studentQuiz->generalQuiz->mark >0 ){{ number_format($studentQuiz->score / $studentQuiz->generalQuiz->mark, 2) * 100 }}@else 0 @endif",
                            "@if( $studentQuiz->generalQuiz && $studentQuiz->generalQuiz->mark >0 ){{ number_format($studentQuiz->generalQuiz->average_scores / $studentQuiz->generalQuiz->mark, 2) * 100 }}@else 0 @endif",
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
