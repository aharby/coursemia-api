@extends('layouts.school_admin_layout')
@section('title')
    {{ @$page_title }}
@endsection

@section('buttons')
    <a href="{{ route("school-admin.general-quizzes-reports.branch-reports.subject.levels.charts", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 5px;">{{ trans("general_quizzes.view chart") }}</a>
    @if(!empty($generalQuizzes))
        <a href="{{ route("school-admin.general-quizzes-reports.branch-reports.subject.levels.export", array_merge(["branch" => $branch ?? null], request()->all())) }}" class="btn btn-md btn-success align-right" style="margin: 5px;" target="_blank">{{ trans("app.Export") }}</a>
    @endif
@endsection

@section('content')
    <div class="row">

        @if(!empty($generalQuizzes))
            @include('school_admin.GeneralQuizzesReports.branch_reports.subject_level._filter')

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead-dark>
                                    <tr>
                                        <th class="text-center">{{ trans('quiz.Branch Name') }}</th>
                                        <th class="text-center">{{ trans('quiz.gradeClass') }}</th>
                                        <th class="text-center">{{ trans('quiz.subject') }}</th>
                                        <th class="text-center">{{ trans('quiz.type') }}</th>
                                        <th class="text-center">{{ trans('quiz.name') }}</th>
                                        <th class="text-center">{{ trans('quiz.time') }}</th>
                                        <th class="text-center">{{ trans('quiz.started_at') }}</th>
                                        <th class="text-center">{{ trans('quiz.ended_at') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Attend Students') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.highest grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.lower grade') }}</th>
                                        <th class="text-center">{{ trans('general_quizzes.Average Score') }}</th>
                                    </tr>
                                </thead-dark>
                                <tbody>
                                @foreach($generalQuizzes as $quiz)
                                    <tr class="text-center">
                                        <td>{{ $quiz->branch->name ??''}}</td>
                                        <td>{{ $quiz->gradeClass->title ??''}}</td>
                                        <td>{{ $quiz->subject->name ??''}}</td>
                                        <td>{{ trans('quiz.'.$quiz->quiz_type) ??''}}</td>
                                        <td>{{ $quiz->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format("Y/m/d") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->start_at)->format("h:i a") ??''}}</td>
                                        <td>{{ \Carbon\Carbon::parse($quiz->end_at)->format("h:i a") ??''}}</td>
                                        <td>{{ $quiz->attend_students ?? 0}}</td>
                                        <td>{{ $quiz->highest_grade ?? 0.00 }}</td>
                                        <td>{{ $quiz->lower_grade ?? 0.00 }}</td>
                                        <td>{{ $quiz->successful_percentage }}</td>

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

        $("#quizType").change(function () {
            const quizType = $(this).val()
        })

        $("#branchSelect").change(function () {
            $('#gradeClass_id').empty();
            $('#gradeClass_id').trigger('change')
            $('#gradeClass_id').append(`<option value="" selected> {{ __("quiz.gradeClass") }} </option>`);
            let branch_id = $(this).val();

            $.get('{{ route('school-branch-supervisor.classrooms.branches.gradeClass') }}' + `/${branch_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    $.each(response.gradeClasses, function (i, item) {
                        $('#gradeClass_id').append(`<option value="${i}">${item} </option>`);
                    });
                });
        });

        $("#gradeClass_id").change(function () {
            const subject = $('#subject_id')
            subject.empty()
            subject.append(`<option value="" selected> {{ trans("quiz.subject") }} </option>`)

            let gradeClass_id = $(this).val()
            let branch_id = $("#branchSelect").val();

            $.get('{{ route('school-account-manager.manager-reports.gradeClass.subjects') }}' +
                    `/${gradeClass_id}/${branch_id}`,
                {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                })
                .done(function (response) {
                    $.each(response.subjects, function(i, item) {
                        subject.append(`<option value="${i}">${item}</option>`)
                    })
                })
        })

        $(document).ready(function () {
            $('#from_date').datepicker()
            $('#to_date').datepicker()
        });

    </script>
@endpush
