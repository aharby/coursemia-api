@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-admin.general-quizzes-reports.student.level.student.quizzes.charts", array_merge(["student" => $user->id], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 5px;">{{ trans("general_quizzes.view chart") }}</a>

    @if(count($studentQuizzes))
        <a href="{{ route('school-admin.general-quizzes-reports.student.level.student.quizzes.export', array_merge(["student" => $user->id], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 5px;" target="_blank">{{ trans("app.Export") }}</a>
    @endif
@endsection


@section('content')
    <div class="row">
        @if(count($studentQuizzes))

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('quiz.subject') }}</th>
                                    <th class="text-center">{{ trans('quiz.type') }}</th>
                                    <th class="text-center">{{ trans('quiz.name') }}</th>
                                    <th class="text-center">{{ trans('quiz.time') }}</th>
                                    <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                    <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                    <th class="text-center">{{ trans("general_quizzes.Average student performance") }}</th>
                                    <th class="text-center">{{ trans("general_quizzes.The overall average of the students") }}</th>
                                    <th class="text-center">{{trans('students.action')}}</th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($studentQuizzes as $studentQuiz)
                                <tr class="text-center">
                                    <td>{{ $studentQuiz->subject->name ??''}}</td>
                                    <td>{{ $studentQuiz->generalQuiz->quiz_type ? trans("general_quizzes." . $studentQuiz->generalQuiz->quiz_type) :'' }}</td>
                                    <td>{{ $studentQuiz->generalQuiz->title ??''}}</td>
                                    <td>{{ \Carbon\Carbon::parse($studentQuiz->generalQuiz->start_at)->format("Y/m/d") ??''}}</td>
                                    <td>{{ \Carbon\Carbon::parse($studentQuiz->generalQuiz->start_at)->format("h:i a") ??''}}</td>
                                    <td>{{ \Carbon\Carbon::parse($studentQuiz->generalQuiz->end_at)->format("h:i a") ??''}}</td>

                                    <td>@if( $studentQuiz->generalQuiz && $studentQuiz->generalQuiz->mark >0 ){{ number_format(($studentQuiz->score / $studentQuiz->generalQuiz->mark)* 100, 2) }}@else 0 @endif %</td>
                                    <td>@if( $studentQuiz->generalQuiz && $studentQuiz->generalQuiz->mark >0 ){{ number_format(($studentQuiz->generalQuiz->average_scores / $studentQuiz->generalQuiz->mark)* 100,2)  }}@else 0 @endif %</td>

                                    <td>
                                        <a class="btn btn-xs btn-info"
                                           href="{{ route('school-admin.general-quizzes-reports.student.level.student.section.performance', ["generalQuizStudent" => $studentQuiz->id]) }}"
                                           data-toggle="tooltip" data-placement="top"
                                           data-title="{{ trans('general_quizzes.details') }}">
                                            {{ trans('general_quizzes.details') }}
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
            {{ $studentQuizzes->links() }}
        </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
