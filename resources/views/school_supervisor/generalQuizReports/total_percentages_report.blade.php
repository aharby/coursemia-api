@extends('layouts.school_manager_layout')
@section('title')
    {{ @$page_title }}
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
                                <tr class="text-center">
                                    <td>{{ $BranchReportDetails->name }}</td>
                                    <td>{{ $BranchReportDetails->students_count }}</td>
                                    <td>{{ $BranchReportDetails->general_quizzes_count }}</td>
                                    <td>{{ $BranchReportDetails->general_quizzes_score_average }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

