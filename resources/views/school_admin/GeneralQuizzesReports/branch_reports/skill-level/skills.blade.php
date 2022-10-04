@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-admin.general-quizzes-reports.branch-reports.sectionPercentageReport.charts", $generalQuiz->id) }}" class="btn btn-md btn-success align-right" style="margin: 5px;">{{ trans("general_quizzes.view chart") }}</a>
    @if(count($sections))
        <a href="{{ route("school-admin.general-quizzes-reports.branch-reports.sectionPercentageReport.export", array_merge(request()->query->all(), ["generalQuiz" => $generalQuiz->id])) }}" class="btn btn-md btn-success align-right" style="margin: 5px;" target="_blank">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">
        @if(!empty($sections))
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{trans('quiz.section') }}</th>
                                        <th class="text-center">{{trans('quiz.students count')}}</th>
                                        <th class="text-center">{{trans('general_quizzes.Average Score')}}</th>
                                        <th class="text-center">{{trans('quiz.min score')}}</th>
                                        <th class="text-center">{{trans('quiz.max score')}}</th>

                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($sections as  $key => $section)
                                    <tr class="text-center">
                                        <td>{{  $key ??''}}</td>
                                        <td>{{ $generalQuiz->studentsAnswered->count() ?? 0 }}</td>
                                        <td>@if(isset($sectionGrades[$key]) && $sectionGrades[$key]->sum('grade') > 0 && $generalQuiz->studentsAnswered->count() >0) {{round((($section->sum('total_score')/$generalQuiz->studentsAnswered->count())/$sectionGrades[$key]->sum('grade')) * 100 ,2)}} @else 0 @endif %
                                        <td>{{ $section->min('total_score') ?? 0}}</td>
                                        <td>{{ $section->max('total_score') ?? 0}}</td>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
            </div>
        @else
            @include('partials.noData')
        @endif
    </div>
@endsection
