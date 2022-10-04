
@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('content')
    <div class="row">

        @if(!empty($questions))
            @php
                $serial = (request("page") ?? 0 ) * env("PAGE_LIMIT")  + 0;
            @endphp
            @foreach($questions as $question)

                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <table class="table">
                                    <tr>
                                        <th class="text-center">{{ __('reports.id') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.question') }}</th>
                                        <th class="text-center">{{ trans('quiz.section') }}</th>

                                    </tr>
                                    <tr class="text-center">
                                        <td>{{ ++$serial }}</td>
                                        @if($question->slug == "drag_drop_text" or $question->slug == "drag_drop_image")
                                            <td>{!! $question->questions->description ?? "" !!}</td>
                                        @else
                                            <td>{!! $question->questions->text ??  $question->questions->question ?? "" !!}</td>
                                        @endif
                                        <td>{{ $question->section->title ??''}}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="card-body">
                                <canvas id="bar-chart_{{$question->id}}" width="800" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="pull-right">
                {{ $questions->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection

@push("scripts")
    <script>
        @foreach($questions as $question)
        // Bar chart
        new Chart(document.getElementById("bar-chart_{{$question->id}}"), {
            type: 'bar',
            data: {
                labels: ["{{ trans('quiz.question grade') }}", "{{ trans('general_quizzes.Average Score') }}"],
                datasets: [
                    {
                        // label: "Population (millions)",
                        backgroundColor: ["#8e5ea2","#3cba9f"],
                        data: [
{{--                            "{{ count($question->groupStudentAnswersByStudent ?? []) }}",--}}
                            "{{ $question->grade }}",
                            "{{ ( $question->grade > 0 and isset($generalQuiz->attend_students) and $generalQuiz->attend_students > 0) ? number_format((($question->groupStudentAnswersByQuestion[0]->total_score ?? 0)/($question->grade*$generalQuiz->attend_students)) * 100, 2) : 0}}",
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
