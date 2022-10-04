
@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-account-manager.general-quizzes-reports.question.percentage.report.questions.charts", $generalQuiz->id) }}" class="btn btn-md btn-success align-right">{{ trans("general_quizzes.view chart") }}</a>
@endsection

@section('content')
<div class="row">
   <div class="col">
    <a  class="btn btn-md btn-success float-right" href="{{$generalQuiz->id}}/export?{{ request()->getQueryString()}}">{{ trans('app.Export')}}</a>
   </div>

  </div>
    <div class="row">

        @if(!empty($questions))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ __('reports.id') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.question') }}</th>
                                        <th class="text-center">{{ trans('quiz.question_percentage_section') }}</th>
{{--                                        <th class="text-center">{{ trans('quiz.students count') }}</th>--}}
                                        <th class="text-center">{{ trans('quiz.question grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score Percentage') }}</th>

                                    </tr>
                                </thead-dark>
                                <tbody>
                                @php
                                    $serial = (request("page") ?? 0 ) * env("PAGE_LIMIT")  + 0;
                                @endphp
                                @foreach($questions as $question)

                                    <tr class="text-center">
                                        <td>{{ ++$serial }}</td>
                                        @if($question->slug == "drag_drop_text" or $question->slug == "drag_drop_image")
                                            <td>{!! $question->questions->description ?? "" !!}</td>
                                        @else
                                            <td>{!! $question->questions->text ??  $question->questions->question ?? "" !!}</td>
                                        @endif
                                        <td>{{ $question->section->title ??''}}</td>
{{--                                        <td>{{ count($question->groupStudentAnswersByStudent ?? []) }}</td>--}}
                                        <td> {{ $question->grade }}</td>
                                        <td>{{ ( $question->grade > 0 and isset($generalQuiz->attend_students)and $generalQuiz->attend_students > 0)  ? number_format((($question->groupStudentAnswersByQuestion[0]->total_score ?? 0)/($question->grade*$generalQuiz->attend_students)) * 100, 2) : 0}}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                {{ $questions->links() }}
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
