@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-branch-supervisor.general-quizzes-reports.student.level.student.section.performance.charts", ["generalQuizStudent" => $generalQuizStudent->id]) }}" class="btn btn-md btn-success align-right" style="margin: 5px;">{{ trans("general_quizzes.view chart") }}</a>
    <a href="{{ route("school-branch-supervisor.general-quizzes-reports.student.level.student.section.performance.export", ["generalQuizStudent" => $generalQuizStudent->id]) }}" class="btn btn-md btn-success align-right" style="margin: 5px;" target="_blank">{{ trans("app.Export") }}</a>
@endsection

@section('content')
    <div class="row">
        @if(!empty($studentSectionsGrade))

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('quiz.student_number') }}</th>
                                    <th class="text-center">{{ trans('quiz.section') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Student score percentage (per section)') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.General  Average Score percentage (per section)') }}</th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @php  $key = 1; @endphp
                            @foreach($studentSectionsGrade as $section => $studentAnswer)
                                <tr class="text-center">
                                    <td>{{ $key }}</td>
                                    <td>{{ $section ??''}}</td>

                                    <td>@if(isset($sectionGrades[$section]) && $sectionGrades[$section]->sum('grade') > 0 ) {{round(($studentAnswer->sum('total_score')/$sectionGrades[$section]->sum('grade')) * 100 ,2)}} @else 0 @endif %
                                    <td>@if(isset($sectionGrades[$section]) && $sectionGrades[$section]->sum('grade') > 0 && $generalQuizStudent->generalQuiz->studentsAnswered->count() >0) {{round((($sectionsStudentsGrade[$section]->sum('total_score')/$generalQuizStudent->generalQuiz->studentsAnswered->count())/$sectionGrades[$section]->sum('grade')) * 100 ,2)}} @else 0 @endif %

                                </tr>
                                @php $key +=1; @endphp
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
