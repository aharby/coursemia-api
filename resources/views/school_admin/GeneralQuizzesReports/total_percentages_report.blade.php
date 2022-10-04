@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-admin.general-quizzes-reports.total-percentages-reports.report.charts", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 5px;">{{ trans("general_quizzes.view chart") }}</a>
    <a href="{{ route("school-admin.general-quizzes-reports.total-percentages-reports.report.export", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 5px;" target="_blank">{{ trans("app.Export") }}</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('general_quizzes.Quizzes Count') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Students Count') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            <tr class="text-center">
                                <td>{{ $quizzes_count }}</td>
                                <td>{{ $school_students }}</td>
                                <td>{{ $percentage_average_scores }}%</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>





        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead-dark>
                                <tr>
                                    <th class="text-center">{{ trans('general_quizzes.branch name') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Students Count') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Quizzes Count') }}</th>
                                    <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                </tr>
                            </thead-dark>
                            <tbody>
                            @foreach($branches as $branch)
                                <tr class="text-center">
                                    <td>{{ $branch->name }}</td>
                                    <td>{{ $branch->students_count }}</td>
                                    <td>{{ $branch->general_quizzes_count }}</td>
                                    <td>{{ $branch->general_quizzes_score_average }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

